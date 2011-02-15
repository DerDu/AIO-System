<?php
// ---------------------------------------------------------------------------------------
// InterfaceCoreSession, ClassCoreSession
// ---------------------------------------------------------------------------------------
interface InterfaceCoreSession
{
	public static function propertySessionName( $string_name = null );
// ---------------------------------------------------------------------------------------
	public static function getSessionId();
	public static function startSession();
	public static function writeSession( $string_key, $mixed_value );
	public static function readSession( $string_key = null );
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
class ClassCoreSession implements InterfaceCoreSession
{
	private static $_propertySessionName = 'AIOSystemSession';
// ---------------------------------------------------------------------------------------
	public static function propertySessionName( $propertySessionName = null ){
		if( $propertySessionName !== null ) self::$_propertySessionName = $propertySessionName;
		return self::$_propertySessionName;
	}
// ---------------------------------------------------------------------------------------
	public static function startSession(){
		if( !strlen( session_id() ) > 0 ) {
			session_start();
			if( !isset( $_SESSION[self::propertySessionName()] ) ){
				$_SESSION[self::propertySessionName()] = array();
			}
		}
		return session_id();
	}
	public static function getSessionId(){
		self::startSession();
		return session_id();
	}
	public static function writeSession( $propertyName, $propertyValue ){
		self::startSession();
		return $_SESSION[self::propertySessionName()][$propertyName] = $propertyValue;
	}
	public static function readSession( $propertyName = null ){
		self::startSession();
		if( $propertyName !== null ){
			if( isset( $_SESSION[self::propertySessionName()][$propertyName] ) )
			return $_SESSION[self::propertySessionName()][$propertyName];
			else
			return null;
		}
		return $_SESSION[self::propertySessionName()];
	}
}
?>