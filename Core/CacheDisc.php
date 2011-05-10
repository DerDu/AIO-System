<?php
/**
 * CacheDisc
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
 * @package AIOSystem\Core
 * @subpackage Cache
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\System;
/**
 * @package AIOSystem\Core
 * @subpackage Cache
 */
interface InterfaceCacheDisc {
	public static function isCached( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false );
	public static function getCache( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false );
	public static function setCache( $propertyCacheParameter, $propertyCacheContent,
	                                 $propertyCacheName = 'DefaultCache', $isGlobal = false, $isForcedRefresh = false
	);
	public static function getCacheLocation( $propertyCacheName = 'DefaultCache', $isGlobal = false );
}
/**
 * @package AIOSystem\Core
 * @subpackage Cache
 */
class ClassCacheDisc implements InterfaceCacheDisc {
	private static $_propertyDirectoryName = '../Cache';
	private static $_propertyCacheTimeout = 3600;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param mixed $Parameter
	 * @param string $Name
	 * @param bool $isGlobal
	 * @return bool|string
	 */
	public static function isCached( $Parameter, $Name = 'DefaultCache', $isGlobal = false, $setTimeoutSeconds = null ) {
		self::_runCacheTimeout( $setTimeoutSeconds );
		$Parameter = sha1( serialize( $Parameter ) );
		if( file_exists( self::getCacheLocation( $Name, $isGlobal ).$Parameter ) ) {
			return $Parameter;
		} else {
			return false;
		}
	}
	/**
	 * @static
	 * @param mixed $propertyCacheParameter
	 * @param string $propertyCacheName
	 * @param bool $isGlobal
	 * @return bool|string
	 */
	public static function getCache( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false, $Location = false ) {
		if( false !== ( $propertyCacheParameter = self::isCached( $propertyCacheParameter, $propertyCacheName, $isGlobal ) ) ) {
			if( $Location ) {
				return self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter;
			} else {
				return file_get_contents( self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter );
			}
		} else {
			return false;
		}
	}
	/**
	 * @static
	 * @param mixed $propertyCacheParameter
	 * @param string $propertyCacheContent
	 * @param string $propertyCacheName
	 * @param bool $isGlobal
	 * @param bool $isForcedRefresh
	 * @return bool
	 */
	public static function setCache(
		$propertyCacheParameter, $propertyCacheContent,
		$propertyCacheName = 'DefaultCache', $isGlobal = false, $isForcedRefresh = false
	) {
		if( false !== ( self::isCached( $propertyCacheParameter, $propertyCacheName, $isGlobal ) ) && !$isForcedRefresh ) {
			return false;
		} else {
			$propertyCacheParameter = sha1( serialize( $propertyCacheParameter ) );
			$propertyCacheLocation = self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter;
			$ClassCoreSystemFile = System::File( $propertyCacheLocation );
			$ClassCoreSystemFile->propertyFileContent( $propertyCacheContent );
			$ClassCoreSystemFile->writeFile();
			return true;
		}
	}
	/**
	 * @static
	 * @param string $propertyCacheName
	 * @param bool $isGlobal
	 * @return mixed|string
	 */
	public static function getCacheLocation( $propertyCacheName = 'DefaultCache', $isGlobal = false ) {
		self::_runCacheTimeout();
		// Create Cache-Location
		$propertyDirectoryName = ClassSystemDirectory::createDirectory(
			ClassSystemDirectory::adjustDirectorySyntax(
				self::_getCacheDirectory().'/'.$propertyCacheName
				.(!$isGlobal?'/'.ClassSession::getSessionId():'')
			)
		);
		chmod( $propertyDirectoryName, 0777 );
		return $propertyDirectoryName;
	}
	/**
	 * @static
	 * @param string $propertyCacheFileName
	 * @return string
	 */
	public static function getCacheFilename( $propertyCacheFileName ) {
		return  date('ymd')
				.'_'.pathinfo($propertyCacheFileName,PATHINFO_FILENAME)
				.'_'.time()
				.'.'.pathinfo($propertyCacheFileName,PATHINFO_EXTENSION);
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @return void
	 */
	private static function _runCacheTimeout( $Seconds = null ) {
		if( $Seconds !== null ) {
			self::$_propertyCacheTimeout = $Seconds;
		}
		$getFileList = ClassSystemDirectory::getFileList( self::_getCacheDirectory(), array(), true );
		/** @var ClassSystemFile $ClassSystemFile */
		foreach( (array)$getFileList as $ClassSystemFile ) {
			if( $ClassSystemFile->propertyFileTime() < time() - self::$_propertyCacheTimeout ) {
				$ClassSystemFile->removeFile();
			}
		}
	}
	/**
	 * @static
	 * @param null $propertyDirectoryName
	 * @return mixed|null
	 */
	private static function _getCacheDirectory( $propertyDirectoryName = null ) {
		if( $propertyDirectoryName !== null ) {
			self::$_propertyDirectoryName = $propertyDirectoryName;
		}
		$propertyDirectoryName = ClassSystemDirectory::adjustDirectorySyntax( __DIR__.'/'.self::$_propertyDirectoryName );
		if( !is_dir( $propertyDirectoryName ) ) {
			ClassSystemDirectory::createDirectory( $propertyDirectoryName );
		} return $propertyDirectoryName;
	}
}
?>
