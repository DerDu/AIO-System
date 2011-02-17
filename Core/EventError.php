<?php
namespace AioSystem\Core;
// ---------------------------------------------------------------------------------------
// InterfaceEventError, ClassEventError
// ---------------------------------------------------------------------------------------
interface InterfaceEventError {
	public static function eventHandler( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );
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
class ClassEventError implements InterfaceEventError {
	private static $_propertyNumberList = array(
			0=>'SYSTEM',1=>'ERROR',2=>'WARNING',4=>'PARSER',
			8=>'NOTICE',16=>'CORE ERROR',32=>'CORE WARNING',64=>'COMPILE ERROR',
			128=>'COMPILE WARNING',256=>'USER ERROR',512=>'USER WARNING',1024=>'USER NOTICE',
			2047=>'ALL',2048=>'STRICT',4096=>'RECOVERABLE ERROR',8192=>'DEPRECATED',
			16384=>'USER DEPRECATED',30719=>'ALL'
	);
// ---------------------------------------------------------------------------------------
	public static function eventHandler( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		self::_eventToScreen(
			$propertyNumber.(isset(self::$_propertyNumberList[$propertyNumber])?' '.self::$_propertyNumberList[$propertyNumber]:''),
			$propertyContent, $propertyLocation, $propertyPosition
		);
		self::_eventToJournal(
			$propertyNumber.(isset(self::$_propertyNumberList[$propertyNumber])?' '.self::$_propertyNumberList[$propertyNumber]:''),
			$propertyContent, $propertyLocation, $propertyPosition
		);
	}
// ---------------------------------------------------------------------------------------
	private static function _eventToScreen( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		ClassEventScreen::addEvent(
			$propertyNumber, $propertyContent, $propertyLocation, $propertyPosition,
			ClassEventScreen::SCREEN_ERROR
		);
	}
	private static function _eventToJournal( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ){
		ClassEventJournal::addEvent(
			trim(strip_tags(str_replace(array('<br />','<br/>','<br>'),"\n",$propertyContent)))."\n"
			.'Code ['.$propertyNumber.'] thrown in '.$propertyLocation.' at line '.$propertyPosition
			,'ClassEventError'
		);
	}
}
?>