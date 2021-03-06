<?php
/**
 * EventScreen
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
 * @subpackage Event
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\Font;
/**
 * @package AIOSystem\Core
 * @subpackage Event
 */
interface InterfaceEventScreen {
	public static function addEvent(
		$propertyNumber, $propertyContent, $propertyLocation = '', $propertyPosition = '',
		$propertyEventScreenType = ClassEventScreen::SCREEN_ERROR
	);
}
/**
 * @package AIOSystem\Core
 * @subpackage Event
 */
class ClassEventScreen implements InterfaceEventScreen {
	const SCREEN_DEBUG = 0;
	const SCREEN_INFO = 1;
	const SCREEN_ERROR = 2;
	const SCREEN_EXCEPTION = 3;
	const SCREEN_SHUTDOWN = 4;
// ---------------------------------------------------------------------------------------
	public static function addEvent(
		$propertyNumber, $propertyContent, $propertyLocation = '', $propertyPosition = '',
		$propertyEventScreenType = ClassEventScreen::SCREEN_ERROR
	) {
		if( ! ini_get('display_errors') ) return false;

		//var_dump( $propertyContent, gettype( $propertyContent ) );
		if( is_bool( $propertyContent ) ) {
			if( $propertyContent === true ) {
				$propertyContent = '(true)';
			}
			if( $propertyContent === false ) {
				$propertyContent = '(false)';
			}
		}
		if( is_null( $propertyContent ) ) {
			$propertyContent = '(NULL)';
		}

		switch( $propertyEventScreenType ) {
			case self::SCREEN_DEBUG:{
				print self::_screenDebug( $propertyContent, $propertyLocation, $propertyPosition );
				return true;
			}
			case self::SCREEN_INFO:{
				print self::_screenInfo( $propertyContent, $propertyLocation, $propertyPosition );
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
			case self::SCREEN_SHUTDOWN:{
				print self::_screenShutdown( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition );
				return true;
			}
		}
		return false;
	}
// ---------------------------------------------------------------------------------------
	private static function _screenDebug( $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto auto 1px auto; background-color: #306030; color:#A0DDA0; border: 1px solid #55B73B; border-top: 1px solid #58C03E; border-bottom: 1px solid #57A83A; font-family: monospace; font-size:14px; text-align: left; overflow:auto;">'
		."\n[Debug] <pre>".htmlspecialchars(Font::MixedToUtf8(print_r($propertyContent,true)))."</pre>"
				.'<br/>'
				.'<span style="font-family: monospace; font-size: 10px;color:#60C060;">'
					.'In '.$propertyLocation
					.' at line '.$propertyPosition
				.'</span>'
		.'</div>'."\n";
	}
	private static function _screenInfo( $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto auto 1px auto; background-color: #303080; color:#A0A0DD; border: 1px solid #553BB7; border-top: 1px solid #583EC0; border-bottom: 1px solid #573AA8; font-family: monospace; font-size:14px; text-align: left; overflow:auto;">'
		."\n[Info] ".Font::MixedToUtf8(print_r($propertyContent,true)).'<br/>'
			.'<span style="font-family: monospace; font-size: 10px;color:#6060C0;">'
				.'In '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
	private static function _screenError( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto auto 1px auto; background-color: #702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; text-align: left; overflow:auto;">'
		."\n".Font::MixedToUtf8(print_r($propertyContent,true)).'<br/>'
			.'<span style="font-family: monospace; font-size: 10px;color:#DDA0A0;">'
				.'Code ['.$propertyNumber.']'
				.' thrown in '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
	private static function _screenException( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto; margin-bottom: 1px; background-color:#702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; text-align: left; overflow: auto;">'
		."\n".'<strong style="color:#DDA0A0;">Unexpected Error:</strong><br /><br />'
		.preg_replace_callback( '!\#([1-9]{1}|[0-9]{2,}) !is', create_function('$exception_replace','return str_replace("#","<br/>#",$exception_replace[0]);'), Font::MixedToUtf8(print_r($propertyContent,true)) )
		.'<br />'
			.'<span style="font-family: monospace; font-size: 10px;color:#DDA0A0;">'
				.'Code ['.$propertyNumber.']'
				.' thrown in '.$propertyLocation
				.' at line '.$propertyPosition
			.'</span>'
		.'</div>'."\n";
	}
	private static function _screenShutdown( $propertyNumber, $propertyContent, $propertyLocation, $propertyPosition ) {
		print "\n".'<div style="position:relative; top: 0; z-index: 20; padding: 5px; margin: auto; margin-bottom: 1px; background-color:#702020; color:#DDA0A0; border: 1px solid #B73B55; border-top: 1px solid #C03E58; border-bottom: 1px solid #A83A57; font-family: monospace; font-size:14px; text-align: left; overflow: auto;">'
		."\n".'<strong style="color:#DDA0A0;">Shutdown Error:</strong><br /><br />'
		.preg_replace_callback( '!\#([1-9]{1}|[0-9]{2,}) !is', create_function('$exception_replace','return str_replace("#","<br/>#",$exception_replace[0]);'), Font::MixedToUtf8(print_r($propertyContent,true)) )
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
