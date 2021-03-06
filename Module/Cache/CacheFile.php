<?php
/**
 * Cache-Driver: File
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
 * @subpackage Cache
 */
namespace AIOSystem\Module\Cache;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
interface InterfaceCacheFile {
	public static function Set( $Identifier, $Content, $Timeout = 3600, $Location = 'Common', $Global = false );
	public static function Get( $Identifier, $Location = 'Common', $Global = false );
	public static function Clean( $Identifier, $Location = 'Common', $Global = false );
}
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
class ClassCacheFile implements InterfaceCacheFile {
	public static function Set( $Identifier, $Content, $Timeout = 3600, $Location = 'Common', $Global = false ) {
		self::Clean( $Identifier, $Location, $Global );
		$File = System::File( self::GenerateCacheFileName( $Identifier, $Location, $Global, $Timeout ) );
		if( is_box( $Content ) ) {
			$File->FileContent( serialize($Content) );
		} else {
			$File->FileContent( $Content );
		}
		$File->writeFile();
	}
	public static function Get( $Identifier, $Location = 'Common', $Global = false ) {
		/** @var \AIOSystem\Core\ClassSystemFile[] $Directory */
		$Directory = System::FileList( ClassCache::Location( $Location, $Global ), self::Identifier( $Identifier ) );
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$Directory as $File ) {
			if( pathinfo( $File->propertyFileName(), PATHINFO_FILENAME ) >= time() ) {
			//if( $File->propertyFileName() >= time() ) {
				/**
				 * Detect serialize
				 */
				if( preg_match( '!^(a|o):[0-9]+:("|{)!is', $File->FileContent() ) ) {
					return unserialize( $File->FileContent() );
				} else {
					return $File->FileContent();
				}
			}
		}
		return false;
	}
	public static function Time( $Identifier, $Location = 'Common', $Global = false ) {
		/** @var \AIOSystem\Core\ClassSystemFile[] $Directory */
		$Directory = System::FileList( ClassCache::Location( $Location, $Global ), self::Identifier( $Identifier ) );
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$Directory as $File ) {
			if( pathinfo( $File->propertyFileName(), PATHINFO_FILENAME ) >= time() ) {
				return pathinfo( $File->propertyFileName(), PATHINFO_FILENAME );
			}
		}
		return time();
	}

	/**
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @return bool|null|string
	 */
	public static function Location( $Identifier, $Location = 'Common', $Global = false ) {
		/** @var \AIOSystem\Core\ClassSystemFile[] $Directory */
		$Directory = System::FileList( ClassCache::Location( $Location, $Global ), self::Identifier( $Identifier ) );
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$Directory as $File ) {
			if( file_exists( $File->propertyFileLocation() ) ) {
				return $File->propertyFileLocation();
			}
		}
		return false;
	}
	/**
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @param int $Timeout
	 * @return string
	 */
	public static function GenerateCacheFileName( $Identifier, $Location = 'Common', $Global = false, $Timeout = 3600 ) {
		return ClassCache::Location( $Location, $Global ).(time()+$Timeout).'.'.self::Identifier( $Identifier );
	}
	public static function Clean( $Identifier, $Location = 'Common', $Global = false ) {
		$Directory = System::FileList( ClassCache::Location( $Location, $Global ) );
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$Directory as $File ) {
			if( $File->propertyFileName() < time() ) {
				$File->removeFile();
			}
		}
	}
	private static function Identifier( $Identifier ) {
		return strtoupper(sha1(serialize($Identifier)));
	}
}
