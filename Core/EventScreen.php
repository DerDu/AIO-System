<?php
namespace AioSystem\Core;
// ---------------------------------------------------------------------------------------
// InterfaceEventScreen, ClassEventScreen
// ---------------------------------------------------------------------------------------
interface InterfaceEventScreen {
	public static function addEvent(
		$propertyNumber, $propertyContent, $propertyLocation = '', $propertyPosition = '',
		$propertyEventScreenType = ClassEventScreen::SCREEN_ERROR
	);
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
class ClassEventScreen implements InterfaceEventScreen {
	const SCREEN_INFO = 0;
	const SCREEN_ERROR = 1;
	const SCREEN_EXCEPTION = 2;
// ---------------------------------------------------------------------------------------
	public static function addEvent(
		$propertyNumber, $propertyContent, $propertyLocation = '', $propertyPosition = '',
		$propertyEventScreenType = ClassEventScreen::SCREEN_ERROR
	) {
		if( ! ini_get('display_errors') ) return false;

		switch( $propertyEventScreenType ) {
			case self::SCREEN_INFO:{
				print self::_screenError( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );
				return true;
			}
			case self::SCREEN_ERROR:{
				print self::_screenError( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );
				return true;
			}
			case self::SCREEN_EXCEPTION:{
				print self::_screenException( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );
				return true;
			}
		}
	}
// ---------------------------------------------------------------------------------------
	private static function _screenInfo( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto auto 1px auto; background-color: #702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; overflow:auto;">'
		."\n".$propertyContent.'<br />'
			.'<span style="font-family: monospace; font-size: 10px;color:#DDA0A0;">'
				.'Code ['.$propertyNumber.']'
				.' thrown in '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
	private static function _screenError( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto auto 1px auto; background-color: #702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; overflow:auto;">'
		."\n".$propertyContent.'<br />'
			.'<span style="font-family: monospace; font-size: 10px;color:#DDA0A0;">'
				.'Code ['.$propertyNumber.']'
				.' thrown in '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
	private static function _screenException( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto; margin-bottom: 1px; background-color:#702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; overflow: auto;">'
		."\n".'<strong style="color:#DDA0A0;">Unexpected Error:</strong><br /><br />'
		.preg_replace_callback( '!\#([1-9]{1}|[0-9]{2,}) !is', create_function('$exception_replace','return str_replace("#","<br/>#",$exception_replace[0]);'), $propertyContent )
		.'<br />'
			.'<span style="font-family: monospace; font-size: 10px;color:#DDA0A0;">'
				.'Code ['.$propertyNumber.']'
				.' thrown in '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
}
?>