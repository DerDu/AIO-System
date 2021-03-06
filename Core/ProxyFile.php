<?php
/**
 * FileProxy
 *
 * Based on http://www.php.net/manual/de/function.fopen.php#47224
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
 * @subpackage Proxy
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\Session;
/**
 * @package AIOSystem\Core
 * @subpackage Proxy
 */
interface InterfaceProxyFile {
	public static function isFileProxy();
	public static function setFileProxy( $propertyHost, $propertyPort, $propertyUser = null, $propertyPass = null );
	public static function getFileProxy( $propertyUrl );
}
/**
 * @package AIOSystem\Core
 * @subpackage Proxy
 */
class ClassProxyFile implements InterfaceProxyFile {
	const PROXY_NONE = 0;
	const PROXY_RELAY = 1;
	const PROXY_BASIC = 2;
	private static $_propertyHost = null;
	private static $_propertyPort = null;
	private static $_propertyUser = null;
	private static $_propertyPass = null;
	private static $_propertyTimeout = 5;
	private static $_propertyErrorNumber = null;
	private static $_propertyErrorString = null;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @return bool
	 */
	public static function isFileProxy() {
		if( Session::Read(__CLASS__.'\Status') !== self::PROXY_NONE ) {
			return Session::Read(__CLASS__.'\Status');
		} else {
			return false;
		}
	}
	/**
	 * @static
	 * @return array
	 */
	public static function getCredentials() {
		return array(
			'proxy_host' => self::propertyHost(),
			'proxy_port' => self::propertyPort(),
			'proxy_login' => self::propertyUser(),
			'proxy_password' => self::propertyPass()
		);
	}
	/**
	 * @static
	 * @param string $propertyHost
	 * @param int $propertyPort
	 * @param null|string $propertyUser
	 * @param null|string $propertyPass
	 * @return void
	 */
	public static function setFileProxy( $propertyHost = null, $propertyPort = null, $propertyUser = null, $propertyPass = null ) {
		Session::Write(__CLASS__.'\Status',self::PROXY_NONE);
		if( $propertyUser !== null || $propertyPass !== null ) {
			self::propertyHost( $propertyHost );
			self::propertyPort( $propertyPort );
			Session::Write(__CLASS__.'\Status',self::PROXY_RELAY);
		}
		if( $propertyUser !== null || $propertyPass !== null ) {
			self::propertyUser( $propertyUser );
			self::propertyPass( $propertyPass );
			Session::Write(__CLASS__.'\Status',self::PROXY_BASIC);
		}
	}
	/**
	 * @static
	 * @param  string $propertyUrl
	 * @return null|string
	 */
	public static function getFileProxy( $propertyUrl ) {
		switch( self::isFileProxy() ) {
			case self::PROXY_NONE: return self::_proxyNone( $propertyUrl );
			case self::PROXY_RELAY: return self::_proxyRelay( $propertyUrl );
			case self::PROXY_BASIC: return self::_proxyBasic( $propertyUrl );
			default: throw new \Exception('Proxy not available!');
		}
	}
// ---------------------------------------------------------------------------------------
	private static function _proxyNone( $propertyUrl ) {
		self::propertyHost( parse_url( $propertyUrl, PHP_URL_HOST ) );
		if( parse_url( $propertyUrl, PHP_URL_PORT ) === null ) {
			switch( strtoupper( parse_url( $propertyUrl, PHP_URL_SCHEME ) ) ) {
				case 'HTTP': { self::propertyPort( '80' ); break; }
				case 'HTTPS': { self::propertyPort( '443' ); break; }
			}
		} else {
			self::propertyPort( parse_url( $propertyUrl, PHP_URL_PORT ) );
		}
		if( self::propertyPort() == '443' ) {
			return file_get_contents( $propertyUrl );
		}
		if( $socketFileProxy = @fsockopen( self::propertyHost(), self::propertyPort(), self::$_propertyErrorNumber, self::$_propertyErrorString, self::$_propertyTimeout ) ) {
			$getFileProxy = '';
			fputs( $socketFileProxy, "GET ".$propertyUrl." HTTP/1.0\r\nHost: ".parse_url( $propertyUrl, PHP_URL_HOST )."\r\n\r\n");
			while( !feof( $socketFileProxy ) ) {
				$getFileProxy .= fread( $socketFileProxy, 4096 );
			}
			fclose( $socketFileProxy );
			$getFileProxy = substr( $getFileProxy, strpos( $getFileProxy, "\r\n\r\n" ) +4 );
		} else {
			trigger_error( '['.self::$_propertyErrorNumber.'] '.self::$_propertyErrorString );
			$getFileProxy = null;
		}
		return $getFileProxy;
	}
	private static function _proxyRelay( $propertyUrl ) {
		if( $socketFileProxy = @fsockopen( self::propertyHost(), self::propertyPort(), self::$_propertyErrorNumber, self::$_propertyErrorString, self::$_propertyTimeout ) ) {
			$getFileProxy = '';
			fputs( $socketFileProxy, "GET ".$propertyUrl." HTTP/1.0\r\nHost: ".self::propertyHost()."\r\n\r\n");
			while( !feof( $socketFileProxy ) ) {
				$getFileProxy .= fread( $socketFileProxy, 4096 );
			}
			fclose( $socketFileProxy );
			$getFileProxy = substr( $getFileProxy, strpos( $getFileProxy, "\r\n\r\n" ) +4 );
		} else {
			trigger_error( '['.self::$_propertyErrorNumber.'] '.self::$_propertyErrorString );
			$getFileProxy = null;
		}
		return $getFileProxy;
	}
	private static function _proxyBasic( $propertyUrl ) {
		if( $socketFileProxy = @fsockopen( self::propertyHost(), self::propertyPort(), self::$_propertyErrorNumber, self::$_propertyErrorString, self::$_propertyTimeout ) ) {
			$getFileProxy = '';
			fputs( $socketFileProxy, "GET ".$propertyUrl." HTTP/1.0\r\nHost: ".self::propertyHost()."\r\n");
			fputs( $socketFileProxy, "Proxy-Authorization: Basic ".base64_encode( self::propertyUser().':'.self::propertyPass() ) . "\r\n\r\n");
			while( !feof( $socketFileProxy ) ) {
				$getFileProxy .= fread( $socketFileProxy, 4096 );
			}
			fclose( $socketFileProxy );
			$getFileProxy = substr( $getFileProxy, strpos( $getFileProxy, "\r\n\r\n" ) +4 );
		} else {
			trigger_error( '['.self::$_propertyErrorNumber.'] '.self::$_propertyErrorString );
			$getFileProxy = null;
		}
		return $getFileProxy;
	}
	/**
	 * @static
	 * @param null|string $propertyHost
	 * @return null|string
	 */
	private static function propertyHost( $propertyHost = null ) {
		if( $propertyHost !== null ) {
			self::$_propertyHost = $propertyHost;
		} return self::$_propertyHost;
	}
	/**
	 * @static
	 * @param null|int $propertyPort
	 * @return null|int
	 */
	private static function propertyPort( $propertyPort = null ) {
		if( $propertyPort !== null ) {
			self::$_propertyPort = $propertyPort;
		} return self::$_propertyPort;
	}
	/**
	 * @static
	 * @param null|string $propertyUser
	 * @return null|string
	 */
	private static function propertyUser( $propertyUser = null ) {
		if( $propertyUser !== null ) {
			self::$_propertyUser = $propertyUser;
		} return self::$_propertyUser;
	}
	/**
	 * @static
	 * @param null|string $propertyPass
	 * @return null|string
	 */
	private static function propertyPass( $propertyPass = null ) {
		if( $propertyPass !== null ) {
			self::$_propertyPass = $propertyPass;
		} return self::$_propertyPass;
	}
}
?>
