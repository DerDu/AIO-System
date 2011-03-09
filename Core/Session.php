<?php
/**
 * Session
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
 * @package AioSystem\Core
 * @subpackage Session
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Session
 */
interface InterfaceSession
{
	public static function getSessionId();
	/**
	 * @static
	 * @abstract
	 * @return string Session ID
	 */
	public static function startSession();
	public static function writeSession( $propertyName, $propertyValue );
	public static function readSession( $propertyName = null );
}
/**
 * Session handling
 * 
 * @package AioSystem\Core
 * @subpackage Session
 */
class ClassSession implements InterfaceSession {
	private static $_propertyName = __CLASS__;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @return string Session ID
	 */
	public static function startSession() {
		if( !strlen( session_id() ) > 0 ) {
			session_start();
			if( !isset( $_SESSION[self::propertyName()] ) ) {
				$_SESSION[self::propertyName()] = array();
			}
		} return session_id();
	}
	/**
	 * @static
	 * @return string Session ID
	 */
	public static function getSessionId() {
		self::startSession();
		return session_id();
	}
	/**
	 * @static
	 * @param string $propertyName
	 * @param mixed $propertyValue
	 * @return mixed
	 */
	public static function writeSession( $propertyName, $propertyValue ) {
		self::startSession();
		return $_SESSION[self::propertyName()][$propertyName] = $propertyValue;
	}
	/**
	 * @static
	 * @param null $propertyName
	 * @return null|array|mixed
	 */
	public static function readSession( $propertyName = null ) {
		self::startSession();
		if( $propertyName !== null ) {
			if( isset( $_SESSION[self::propertyName()][$propertyName] ) ) {
				return $_SESSION[self::propertyName()][$propertyName];
			} else {
				return null;
			}
		} return $_SESSION[self::propertyName()];
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param null $propertyName
	 * @return string
	 */
	private static function propertyName( $propertyName = null ) {
		if( $propertyName !== null ) {
			self::$_propertyName = $propertyName;
		} return self::$_propertyName;
	}
}
?>