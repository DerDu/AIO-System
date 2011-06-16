<?php
/**
 * This file contains the API:Cache
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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Module\Cache\ClassCache as AIOCache;
use \AIOSystem\Module\Cache\ClassCacheFile as AIOCacheFile;
use \AIOSystem\Module\Cache\Serializer as AIOSerializer;
/**
 * @package AIOSystem\Api
 */
class Cache {
	/**
	 * Set content to cache
	 *
	 * @static
	 * @param mixed $Identifier
	 * @param string $Content
	 * @param string $Location - Default: 'DefaultCache'
	 * @param bool $Global - Default: false
	 * @param int $Timeout - Default: 3600
	 * @return bool
	 */
	public static function Set( $Identifier, $Content, $Location = 'DefaultCache', $Global = false, $Timeout = 3600 ) {
		return AIOCacheFile::Set( $Identifier, $Content, $Timeout, $Location, $Global );
	}
	/**
	 * Get content from cache
	 *
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @return bool|string
	 */
	public static function Get( $Identifier, $Location = 'DefaultCache', $Global = false ) {
		return AIOCacheFile::Get( $Identifier, $Location, $Global );
	}
	/**
	 * Get cache time
	 *
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @return int
	 */
	public static function GetTime( $Identifier, $Location = 'DefaultCache', $Global = false ) {
		return AIOCacheFile::Time( $Identifier, $Location, $Global );
	}
	/**
	 * Get location of cache file
	 *
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @return bool|string File location or false
	 */
	public static function GetLocation( $Identifier, $Location = 'DefaultCache', $Global = false ) {
		return AIOCacheFile::Location( $Identifier, $Location, $Global );
	}
	/**
	 * Returns the current cache location
	 *
	 * @static
	 * @param string $Cache
	 * @param bool $Global
	 * @return string
	 */
	public static function Location( $Cache = 'DefaultCache', $Global = false ) {
		return AIOCache::Location( $Cache, $Global );
	}
	/**
	 * Returns a cache file name
	 *
	 * @static
	 * @param string $File
	 * @return string
	 */
	public static function File( $File ) {
		return AIOCache::Filename( $File );
	}
	/**
	 * @param \Object $Object
	 * @return \AIOSystem\Module\Cache\Serializer
	 */
	public static function SerializeObject( $Object ) {
		return AIOSerializer::Instance( $Object );
	}
	/**
	 * Returns a generated cache file location
	 *
	 * @static
	 * @param mixed $Identifier
	 * @param string $Location
	 * @param bool $Global
	 * @param int $Timeout
	 * @return string
	 */
	public static function CacheFile( $Identifier, $Location = 'Common', $Global = false, $Timeout = 3600 ) {
		return AIOCacheFile::GenerateCacheFileName( $Identifier, $Location, $Global, $Timeout );
	}
}
?>
