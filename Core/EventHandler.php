<?php
/**
 * EventHandler
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
 * @subpackage Event
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Event
 */
interface InterfaceEventHandler {
	public static function registerEventHandler( $propertyErrorReporting = E_ALL, $isDisplayError = true );
	public static function _executeEventShutdown();
}
/**
 * @package AioSystem\Core
 * @subpackage Event
 */
class ClassEventHandler implements InterfaceEventHandler {
	public static function registerEventHandler( $propertyErrorReporting = E_ALL, $isDisplayError = true ){
		if( $isDisplayError === true ) {
			ini_set('display_errors',1);
		} else {
			ini_set('display_errors',0);
		}
		error_reporting( $propertyErrorReporting );
		self::_registerEventError();
		self::_registerEventException();
		self::_registerEventShutdown();
	}
// ---------------------------------------------------------------------------------------
	public static function _executeEventShutdown() {
		if( ($getLastError = error_get_last() ) !== null ) {
			ClassEventJournal::addEvent(
				'ShutDown Error: '."\n\n"
				.strip_tags(str_replace(array('<br />','<br/>','<br>'),"\n",$getLastError['message']))."\n"
				.'Code ['.$getLastError['type'].' ERROR]'
				.' thrown in '.$getLastError['file']
				.' at line '.$getLastError['line']
				,'ClassEventShutdown'
			);
			ClassEventScreen::addEvent( $getLastError['type'], $getLastError['message'], $getLastError['file'], $getLastError['line'], ClassEventScreen::SCREEN_EXCEPTION );
		}
	}
// ---------------------------------------------------------------------------------------
	private static function _registerEventError() {
		set_error_handler(
			create_function(
				'$propertyNumber, $propertyContent, $propertyLocation, $propertyPosition',
				'if( \AioSystem\Core\ClassEventTypehint::eventHandler( $propertyNumber, $propertyContent ) ) return true;'
				.'\AioSystem\Core\ClassEventError::eventHandler( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );'
			)
		);
	}
	private static function _registerEventException() {
		set_exception_handler(
			create_function(
				'$EventException',
				'\AioSystem\Core\ClassEventException::eventHandler( $EventException->getCode(), $EventException->getMessage().\'<br /><br /><span style="font-size: 12px;">\'.$EventException->getTraceAsString()."</span><br />", $EventException->getFile(), $EventException->getLine() );'
			)
		);
	}
	private static function _registerEventShutdown() {
		register_shutdown_function(
			create_function(
				'',
				'\AioSystem\Core\ClassEventHandler::_executeEventShutdown();'
			)
		);
	}
}
?>