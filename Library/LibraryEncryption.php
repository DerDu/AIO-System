<?php
require_once( dirname(__FILE__).'/../Core/CoreSession.php' );
// ---------------------------------------------------------------------------------------
// InterfaceLibraryEncryption, ClassLibraryEncryption
// ---------------------------------------------------------------------------------------
interface InterfaceLibraryEncryption
{
	public static function encodeSessionEncryption( $string_plaintext );
	public static function decodeSessionEncryption( $string_encrypted );
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
class ClassLibraryEncryption implements InterfaceLibraryEncryption
{
	public static function encodeSessionEncryption( $propertyPlainText ){
		return base64_encode( self::_runSessionEncryption( $propertyPlainText ) );
	}
	public static function decodeSessionEncryption( $propertyEncryptedText ){
		return self::_runSessionEncryption( base64_decode( $propertyEncryptedText ) );
	}
// ---------------------------------------------------------------------------------------
	private static function _runSessionEncryption( $propertyContent ){
		$_runSessionEncryption = '';
		$_codeSessionEncryption = self::_codeSessionEncryption( $propertyContent );
		$countContentLength = strlen( $propertyContent );
		for( $runContentLength = 0; $runContentLength < $countContentLength; $runContentLength++ ){
			$_runSessionEncryption .= chr( ord( $propertyContent[$runContentLength] ) ^ ord( $_codeSessionEncryption[$runContentLength] ) );
		}
		return $_runSessionEncryption;
	}
	private static function _codeSessionEncryption( $propertyContent ){
		$getSessionId = ClassCoreSession::getSessionId();
		return str_repeat( $getSessionId, ceil(strlen($propertyContent)/strlen($getSessionId)) );
	}
}
?>