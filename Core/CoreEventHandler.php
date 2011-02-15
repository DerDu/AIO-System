<?php
require_once( dirname(__FILE__).'/CoreEventJournal.php' );
require_once( dirname(__FILE__).'/CoreEventException.php' );
require_once( dirname(__FILE__).'/CoreEventError.php' );
require_once( dirname(__FILE__).'/CoreEventTypehint.php' );
// ---------------------------------------------------------------------------------------
// InterfaceCoreEventHandler, ClassCoreEventHandler
// ---------------------------------------------------------------------------------------
interface InterfaceCoreEventHandler{
	public static function registerCoreEventHandler( $propertyErrorReporting = E_ALL, $isDisplayError = true );
	public static function _executeCoreEventShutdown();
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
class ClassCoreEventHandler implements InterfaceCoreEventHandler
{
	public static function registerCoreEventHandler( $propertyErrorReporting = E_ALL, $isDisplayError = true ){
		if( $isDisplayError === true ) {
			ini_set('display_errors',1);
		} else {
			ini_set('display_errors',0);
		}
		error_reporting( $propertyErrorReporting );
		self::_registerCoreEventError();
		self::_registerCoreEventException();
		self::_registerCoreEventShutdown();
	}
// ---------------------------------------------------------------------------------------
	public static function _executeCoreEventShutdown(){
		if( ($getLastError = error_get_last() ) !== null ){
			ClassCoreEventJournal::addEvent(
				'ShutDown Error: '."\n\n"
				.strip_tags(str_replace(array('<br />','<br/>','<br>'),"\n",$getLastError['message']))."\n"
				.'Code ['.$getLastError['type'].' ERROR]'
				.' thrown in '.$getLastError['file']
				.' at line '.$getLastError['line']
				,'CoreEventShutdown'
			);
		}
	}
// ---------------------------------------------------------------------------------------
	private static function _registerCoreEventError(){
		set_error_handler(
			create_function(
				'$propertyNumber, $propertyContent, $propertyLocation, $propertyPosition',
				'if( ClassCoreEventTypehint::eventHandler( $propertyNumber, $propertyContent ) ) return true;'
				.'ClassCoreEventError::eventHandler( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );'
			)
		);
	}
	private static function _registerCoreEventException(){
		set_exception_handler(
			create_function(
				'$CoreEventException',
				'ClassCoreEventException::eventHandler( $CoreEventException->getCode(), $CoreEventException->getMessage().\'<br /><br /><span style="font-size: 12px;">\'.$CoreEventException->getTraceAsString()."</span><br />", $CoreEventException->getFile(), $CoreEventException->getLine() );'
			)
		);
	}
	private static function _registerCoreEventShutdown(){
		register_shutdown_function(
			create_function(
				'',
				'ClassCoreEventHandler::_executeCoreEventShutdown();'
			)
		);
	}
}
?>