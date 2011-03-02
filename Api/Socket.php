<?php
/**
 * This file contains the API:Socket
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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Core\ClassSocket as AioSocket;
/**
 * ClassSocket
 *  
 * @package AioSystem\Api
 */
class ClassSocket {
	/**
	 * @static
	 * @param string $Host
	 * @param null|int $Port
	 * @return string
	 */
	public static function Open( $Host, $Port = null ) {
		return AioSocket::openSocket( $Host, $Port );
	}
	/**
	 * @static
	 * @param null|int $Length
	 * @return string
	 */
	public static function Read( $Length = null ) {
		return AioSocket::readSocket( $Length );
	}
	/**
	 * @static
	 * @param string $Data
	 * @return mixed
	 */
	public static function Write( $Data ) {
		return AioSocket::writeSocket( $Data );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Close() {
		return AioSocket::closeSocket();
	}
	/**
	 * @static
	 * @param null|string $Socket
	 * @return string
	 */
	public static function Socket( $Socket = null ) {
		return AioSocket::propertySocketIdentifier( $Socket );
	}
	public static function HttpGet( $File ) {
		$GET = "GET /".$File." HTTP/1.1\r\n";
		$GET .= "Host: ".AioSocket::propertySocketDeviceHost()."\r\n";
        $GET .= "Connection: Close\r\n\r\n";
		self::Write( $GET );
		return preg_replace( "/^.*?\r\n\r\n/is", '', self::Read() );
	}
}
?>