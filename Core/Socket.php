<?php
/**
 * Socket
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
 * @package AioSystem\Core
 * @subpackage Socket
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Socket
 */
interface InterfaceSocket {
	public static function openSocket( $propertyHost, $propertyPort = null );
	public static function readSocket( $propertyLength = null );
	public static function writeSocket( $propertyData );
	public static function closeSocket();
	public static function propertySocketIdentifier( $propertySocketIdentifier = null );
}
/**
 * @package AioSystem\Core
 * @subpackage Socket
 */
class ClassSocket implements InterfaceSocket {
	private static $_propertySocketList = array();
	private static $_propertySocketIdentifier = null;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @throws \Exception
	 * @param string $propertyHost
	 * @param null $propertyPort
	 * @return string
	 */
	public static function openSocket( $propertyHost, $propertyPort = null ) {
		if( !$socketHandler = socket_create( AF_INET, SOCK_STREAM, SOL_TCP ) ) {
			throw new \Exception( socket_strerror( socket_last_error() ) );
		}
		$ClassModuleSocketDevice = new ClassSocketDevice(
			$socketHandler,
			$propertyHost,
			$propertyPort
		);
		self::propertySocketIdentifier( sha1( serialize( $ClassModuleSocketDevice ) ) );
		self::$_propertySocketList[self::propertySocketIdentifier()] = $ClassModuleSocketDevice;
		self::connectSocket();
		return self::propertySocketIdentifier();
	}
	/**
	 * @return bool
	 */
	public static function connectSocket() {
		return self::$_propertySocketList[self::propertySocketIdentifier()]->openSocketDevice();
	}
	/**
	 * @static
	 * @param null|int $propertyLength
	 * @return string
	 */
	public static function readSocket( $propertyLength = null ) {
		return self::$_propertySocketList[self::propertySocketIdentifier()]->readSocketDevice( $propertyLength );
	}
	/**
	 * @static
	 * @param  string $propertyData
	 * @return int
	 */
	public static function writeSocket( $propertyData ) {
		return self::$_propertySocketList[self::propertySocketIdentifier()]->writeSocketDevice( $propertyData );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function closeSocket() {
		return self::$_propertySocketList[self::propertySocketIdentifier()]->closeSocketDevice();
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param null|string $propertySocketIdentifier
	 * @return string
	 */
	public static function propertySocketIdentifier( $propertySocketIdentifier = null ) {
		if( $propertySocketIdentifier !== null ) {
			self::$_propertySocketIdentifier = $propertySocketIdentifier;
		}
		return strtoupper(self::$_propertySocketIdentifier);
	}
}
?>