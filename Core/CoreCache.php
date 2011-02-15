<?php
require_once( dirname(__FILE__).'/CoreSystemDirectory.php' );
require_once( dirname(__FILE__).'/CoreSystemFile.php' );
require_once( dirname(__FILE__).'/CoreSession.php' );
// ---------------------------------------------------------------------------------------
// InterfaceCoreCache, ClassCoreCache
// ---------------------------------------------------------------------------------------
interface InterfaceCoreCache
{
	public static function isCached( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false );
	public static function getCache( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false );
	public static function setCache( $propertyCacheParameter, $propertyCacheContent,
	                                 $propertyCacheName = 'DefaultCache', $isGlobal = false, $isForcedRefresh = false
	);
	public static function getCacheLocation( $propertyCacheName = 'DefaultCache', $isGlobal = false );
}
// ---------------------------------------------------------------------------------------
// LICENSE (BSD)
//
//	Copyright (c) 2011, Gerd Christian Kunze
//	All rights reserved.
//
//	Redistribution and use in source and binary forms, with or without
//	modification, are permitted provided that the following conditions are
//	met:
//
//		* Redistributions of source code must retain the above copyright
//		  notice, this list of conditions and the following disclaimer.
//		* Redistributions in binary form must reproduce the above copyright
//		  notice, this list of conditions and the following disclaimer in the
//		  documentation and/or other materials provided with the distribution.
//		* Neither the name of the Gerd Christian Kunze nor the names of its
//		  contributors may be used to endorse or promote products derived from
//		  this software without specific prior written permission.
//
//	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
//	IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
//	THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
//	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
//	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
//	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
//	PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
//	PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
//	LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
//	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
//	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
class ClassCoreCache implements InterfaceCoreCache
{
	private static $_propertyDirectoryName = '../Cache';
	private static $_propertyCacheTimeout = 3600;

	public static function isCached( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false ){
		self::_runCacheTimeout();
		$propertyCacheParameter = sha1( serialize( $propertyCacheParameter ) );
		if( file_exists( self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter ) ){
			return $propertyCacheParameter;
		} else {
			return false;
		}
	}
	public static function getCache( $propertyCacheParameter, $propertyCacheName = 'DefaultCache', $isGlobal = false ){
		if( false !== ( $propertyCacheParameter = self::isCached( $propertyCacheParameter ) ) ){
			return file_get_contents( self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter );
		} else {
			return false;
		}
	}
	public static function setCache(
		$propertyCacheParameter, $propertyCacheContent,
		$propertyCacheName = 'DefaultCache', $isGlobal = false, $isForcedRefresh = false
	){
		if( false !== ( $propertyCacheParameter = self::isCached( $propertyCacheParameter, $propertyCacheName, $isGlobal ) ) && !$isForcedRefresh ){
			return false;
		}
		$propertyCacheParameter = sha1( serialize( $propertyCacheParameter ) );
		$propertyCacheLocation = self::getCacheLocation( $propertyCacheName, $isGlobal ).$propertyCacheParameter;
		$ClassCoreSystemFile = ClassCoreSystemFile::Instance( $propertyCacheLocation );
		$ClassCoreSystemFile->writeFile();
		return true;
	}
	public static function getCacheLocation( $propertyCacheName = 'DefaultCache', $isGlobal = false ){
		// Create Cache-Location
		$propertyDirectoryName = ClassCoreSystemDirectory::createDirectory(
			ClassCoreSystemDirectory::adjustDirectorySyntax(
				self::_getCacheDirectory().'/'.$propertyCacheName
				.(!$isGlobal?'/'.ClassCoreSession::getSessionId():'')
			)
		);
		chmod( $propertyDirectoryName, 0777 );
		return $propertyDirectoryName;
	}
// ---------------------------------------------------------------------------------------
	private static function _runCacheTimeout(){
		$getFileList = ClassCoreSystemDirectory::getFileList( self::_getCacheDirectory(), array(), true );
		foreach( (array)$getFileList as $ClassCoreSystemFile ){
			if( $ClassCoreSystemFile->propertyFileTime() < time() - self::$_propertyCacheTimeout ){
				$ClassCoreSystemFile->removeFile();
			}
		}
	}
	private static function _getCacheDirectory( $propertyDirectoryName = null ){
		if( $propertyDirectoryName !== null ){
			self::$_propertyDirectoryName = $propertyDirectoryName;
		}
		$propertyDirectoryName = ClassCoreSystemDirectory::adjustDirectorySyntax( dirname(__FILE__).'/'.self::$_propertyDirectoryName );
		if( !is_dir( $propertyDirectoryName ) ){
			ClassCoreSystemDirectory::createDirectory( $propertyDirectoryName );
		}
		return $propertyDirectoryName;
	}
}
?>