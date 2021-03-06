<?php
/**
 * Image-Resource
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
 *  * Neither the name of Gerd Christian Kunze nor the names of the
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
 * @package AIOSystem\Module
 * @subpackage Image
 */
namespace AIOSystem\Module\Image;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
interface InterfaceImageResource {
	public static function Load( $image_file );
	public static function Save( $image_resource, $image_file, $image_quality = 100 );
	public static function Create( $image_size_width, $image_size_height );
}
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
class ClassImageResource implements InterfaceImageResource
{
	public static function Load( $image_file ) {
		// Not possible?
		if( empty( $image_file ) ) {
			return false;
		}
		// Is already loaded ?
		if( is_resource( $image_file ) ) {
			return $image_file;
		}
		$image_file = pathinfo( $image_file );
		if( file_exists( $image_file['dirname'].'/'.$image_file['basename'] ) ) {
			if( self::_gdlib( $image_file['extension'] ) ) {
				$image_load_function = 'imagecreatefrom'.str_replace( array('JPG','jpg'), 'jpeg', $image_file['extension'] );
				if( false !== ( $Resource = @$image_load_function( $image_file['dirname'].DIRECTORY_SEPARATOR.$image_file['basename'] ) ) ) {
					return $Resource;
				}
				else Event::Error(0,'Could not load '.$image_file['dirname'].DIRECTORY_SEPARATOR.$image_file['basename'],__FILE__,__LINE__);
			}
			else Event::Error(0,'File-Type ['.strtoupper($image_file['extension']).'] not supportet!',__FILE__,__LINE__);
		}
		else Event::Error(0,'File ['.$image_file['dirname'].DIRECTORY_SEPARATOR.$image_file['basename'].'] not found!',__FILE__,__LINE__);
		return false;
	}
	public static function Save( $image_resource, $image_file, $image_quality = 100 ) {
		if( !is_resource( $image_resource ) ) {
			Event::Error(0,'Could not save '.System::DirectorySyntax($image_file,false,DIRECTORY_SEPARATOR),__FILE__,__LINE__);
			return false;
		}
		$image_file = pathinfo( $image_file );
		$image_extension = str_replace( array('jpg'), array('jpeg'), $image_file['extension'] );
		switch( strtoupper($image_extension) ) {
			case 'JPEG': {
				$image_save_function = 'image'.$image_extension;
				$image_save_function( $image_resource, $image_file['dirname'].'/'.$image_file['basename'], $image_quality );
				return true;
				break;
			}
			default: {
				$image_save_function = 'image'.$image_extension;
				$image_save_function( $image_resource, $image_file['dirname'].'/'.$image_file['basename'] );
				return true;
				break;
			}
		}
	}
	public static function Create( $image_size_width, $image_size_height ) {
		$image_resource = imagecreatetruecolor( $image_size_width, $image_size_height );
		imageAlphaBlending( $image_resource, false );
		imageSaveAlpha( $image_resource, true );
		$image_alpha = imagecolorallocatealpha( $image_resource, 0, 0, 0, 127 );
		imagefill( $image_resource, 0, 0, $image_alpha );
		return $image_resource;
	}
// ---------------------------------------------------------------------------------------
	private static function _gdlib( $pathinfo_extension ) {
		$array_gd_info = array_values( gd_info() );
		switch( strtoupper($pathinfo_extension) ) {
			case 'GIF': { return $array_gd_info[4]*$array_gd_info[5]; break; }
			case 'JPEG': { return $array_gd_info[6]; break; }
			case 'JPG': { return $array_gd_info[6]; break; }
			case 'PNG': { return $array_gd_info[7]; break; }
			case 'WBMP': { return $array_gd_info[8]; break; }
			case 'BMP': { return $array_gd_info[8]; break; }
			case 'XPM': { return $array_gd_info[9]; break; }
			case 'XBM': { return $array_gd_info[10]; break; }
			case 'JIS': { return $array_gd_info[11]; break; }
			default: { return false; break; }
		}
	}
}
?>
