<?php
/**
 * Zip-Unpack
 *
 // ---------------------------------------------------------------------------------------
 * LICENSE (BSD)
 *
 * Copyright (c) 2011, Gerd Christian Kunze
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 *  * Neither the name of the Gerd Christian Kunze nor the names of its
 *    contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
 *
 * @package AioSystem\Module
 * @subpackage Zip
 */
namespace AioSystem\Module\Zip;
use \AioSystem\Api\System as System;
use \AioSystem\Api\Cache as Cache;
/**
 * @package AioSystem\Module
 * @subpackage Zip
 */
interface InterfaceZipUnpack {
	/**
	 * Unpack zip file
	 * 
	 * @static
	 * @abstract
	 * @param string $File
	 * @return \AioSystem\Core\ClassSystemFile[]
	 */
	public static function Open( $File );
}
/**
 * @package AioSystem\Module
 * @subpackage Zip
 */
class ClassZipUnpack implements InterfaceZipUnpack {
	/**
	 * @static
	 * @param string $File
	 * @return \AioSystem\Core\ClassSystemFile[]
	 */
	public static function Open( $File ) {
		// GET FILE CONTENTS
		$parse_zip_content = self::phpzip_file_get_content( $File );
		// GET ZIP SECTIONS
		$parse_zip_section = self::phpzip_get_sections( $parse_zip_content );
		// GET ZIP COMMENT
		$parse_zip_comment = self::phpzip_get_comment( $parse_zip_section );
		// GET CENTRAL DIRECTORY
		$parse_zip_directory = self::phpzip_get_directory( $parse_zip_content );
		// READ CONTENT
		$parse_zip_result = array();
		foreach( (array)$parse_zip_directory as $parse_zip_directory_entry ) {
			$parse_zip_decompressed = array();
			// SKIP IF ENCRYPTED
			if( self::phpzip_get_encrypted( $parse_zip_directory_entry ) ) {
				trigger_error('Encryption is not supported!'); continue;
			}
			// GET FILE-NAME
			$parse_zip_decompressed['file_basename'] = basename( self::phpzip_get_filename( $parse_zip_directory_entry ) );
			// GET FILE-DIRECTORY
			$parse_zip_decompressed['file_dirname'] = dirname( self::phpzip_get_filename( $parse_zip_directory_entry ) );
			// SKIP IF DIRECTORY
			if( substr( $parse_zip_decompressed['file_dirname'], -1) == "/" ) continue;
			// GET GENERAL INFORMATION
			$parse_zip_information = self::phpzip_get_information( $parse_zip_directory_entry );
			// GET FILE-CONTENT
			$parse_zip_decompressed['file_content'] = self::phpzip_get_content( $parse_zip_directory_entry );
			// SKIP IF MALFORMED COMPRESSION
			if( strlen( $parse_zip_decompressed['file_content'] ) != $parse_zip_information['info_size_compressed'] ) {
				trigger_error('Directory entry malformed!'); continue;
			}
			// DECOMPRESS FILE-CONTENT
			switch( self::phpzip_get_compress_method( $parse_zip_directory_entry ) ) {
				// NONE
				case 0: break;
				// DEFLATE
				case 8: $parse_zip_decompressed['file_content'] = gzinflate( $parse_zip_decompressed['file_content'] ); break;
				// BZIP2
				case 12: $parse_zip_decompressed['file_content'] = bzdecompress( $parse_zip_decompressed['file_content'] ); break;
				// NOT FOUND
				default: trigger_error('Compression method not supported!');
			}

			if( $parse_zip_decompressed['file_content'] === false ) {
				trigger_error('Decompression failed!'); continue;
			}
			if( strlen( $parse_zip_decompressed['file_content'] ) != $parse_zip_information['info_size_uncompressed'] ) {
				trigger_error('Decompression failed!'); continue;
			}
			if( crc32( $parse_zip_decompressed['file_content'] ) != $parse_zip_information['info_crc'] ) {
				trigger_error('CRC32 mismatch!');continue;
			}
			$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
			$parse_zip_decompressed['file_timestamp'] = self::phpzip_get_timestamp( $parse_zip_directory_entry );
			// ADD FILE TO RESULT
			//$parse_zip_result[] = $parse_zip_decompressed;
			$object_file = System::File(
				Cache::Location().$parse_zip_decompressed['file_basename']
			);
			$object_file->propertyFileContent( $parse_zip_decompressed['file_content'] );
			$object_file->writeFile();
			array_push( $parse_zip_result, $object_file );
		}
		return $parse_zip_result;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param string $parse_zip_directory_entry
	 * @return int
	 */
	private static function phpzip_get_timestamp( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
		return mktime(	($parse_zip_directory_entry_unpack['file_time']  & 0xf800) >> 11,
				($parse_zip_directory_entry_unpack['file_time']  & 0x07e0) >>  5,
				($parse_zip_directory_entry_unpack['file_time']  & 0x001f) <<  1,
				($parse_zip_directory_entry_unpack['file_date']  & 0x01e0) >>  5,
				($parse_zip_directory_entry_unpack['file_date']  & 0x001f),
				(($parse_zip_directory_entry_unpack['file_date'] & 0xfe00) >>  9) + 1980 );
	}
	/**
	 * @param string $parse_zip_directory_entry
	 * @return string
	 */
	private static function phpzip_get_compress_method( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
		return $parse_zip_directory_entry_unpack['compress_method'];
	}
	/**
	 * @param string $parse_zip_directory_entry
	 * @return string
	 */
	private static function phpzip_get_content( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
		return substr( $parse_zip_directory_entry, 26 + $parse_zip_directory_entry_unpack['filename_length'] );
	}
	/**
	 * @static
	 * @param string $parse_zip_directory_entry
	 * @return array
	 */
	private static function phpzip_get_information( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );

		if( $parse_zip_directory_entry_unpack['general_purpose'] & 0x0008 )
		{
			$parse_zip_directory_entry_unpack = unpack( "V1crc/V1size_compressed/V1size_uncompressed", substr( $parse_zip_directory_entry, -12 ) );
			return array(
				'info_crc'=>$parse_zip_directory_entry_unpack['crc'],
				'info_size_compressed'=>$parse_zip_directory_entry_unpack['size_uncompressed'],
				'info_size_uncompressed'=>$parse_zip_directory_entry_unpack['size_compressed'],
			);
		}
		return array(
			'info_crc'=>$parse_zip_directory_entry_unpack['crc'],
			'info_size_compressed'=>$parse_zip_directory_entry_unpack['size_compressed'],
			'info_size_uncompressed'=>$parse_zip_directory_entry_unpack['size_uncompressed'],
		);
	}
	/**
	 * @param string $parse_zip_directory_entry
	 * @return string
	 */
	private static function phpzip_get_filename( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
		return substr( $parse_zip_directory_entry, 26, $parse_zip_directory_entry_unpack['filename_length'] );
	}
	/**
	 * @param string $parse_zip_directory_entry
	 * @return bool
	 */
	private static function phpzip_get_encrypted( $parse_zip_directory_entry ) {
		$parse_zip_directory_entry_unpack = self::phpzip_get_directory_entry( $parse_zip_directory_entry );
		if( $parse_zip_directory_entry_unpack['general_purpose'] & 0x0001 )
		return true;
		return false;
	}
	/**
	 * @param string $parse_zip_directory_entry
	 * @return array
	 */
	private static function phpzip_get_directory_entry( $parse_zip_directory_entry ) {
		return unpack("v1version/v1general_purpose/v1compress_method/v1file_time/v1file_date/V1crc/V1size_compressed/V1size_uncompressed/v1filename_length", $parse_zip_directory_entry );
	}
	/**
	 * @param string $parse_zip_content
	 * @return array
	 */
	private static function phpzip_get_directory( $parse_zip_content ) {
		$parse_zip_section = explode("\x50\x4b\x01\x02", $parse_zip_content );
		$parse_zip_section = explode("\x50\x4b\x03\x04", $parse_zip_section[0]);
		array_shift( $parse_zip_section );
		return $parse_zip_section;
	}
	/**
	 * @param string $parse_zip_content
	 * @return array
	 */
	private static function phpzip_get_sections( $parse_zip_content ) {
		return explode( "\x50\x4b\x05\x06", $parse_zip_content );
	}
	/**
	 * @param string $parse_zip_section
	 * @return string
	 */
	private static function phpzip_get_comment( $parse_zip_section ) {
		$parse_zip_section_unpack = unpack( 'x16/v1length', $parse_zip_section[1] );
		return str_replace( array("\r\n", "\r"), "\n", substr( $parse_zip_section[1], 18, $parse_zip_section_unpack['length']) );
	}
	/**
	 * @static
	 * @param string $parse_zipfile
	 * @return string
	 */
	private static function phpzip_file_get_content( $parse_zipfile ) {
		if( file_exists( $parse_zipfile ) )
		return file_get_contents( $parse_zipfile );
		else
		return $parse_zipfile;
	}
}
?>