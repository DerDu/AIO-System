<?php
/**
 * SeoUri
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
 * @subpackage Seo
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Core
 * @subpackage Seo
 */
interface InterfaceSeoUri {
	public static function UriPath( $string_path_relative, $integer_path_level = 0 );
	public static function UriCarrier( $string_path_carrier = null );
	public static function UriSeparator( $string_separator_char = null );
	public static function UriRequest();
}
/**
 * @package AIOSystem\Core
 * @subpackage Seo
 */
class ClassSeoUri implements InterfaceSeoUri {
	const DEBUG = false;

	private static $string_separator_char = '~';
	private static $string_path_carrier = 'URI-PATH';

	public static function UriPath( $PathRelative, $PathLevel = 0 ) {
		if( !isset($_SERVER['SERVER_PORT']) ) $_SERVER['SERVER_PORT'] = '';
		switch( $_SERVER['SERVER_PORT'] )
		{
			case '80': $PathPort = 'http://'; break;
			case '21': $PathPort = 'ftp://'; break;
			default: $PathPort = 'http://';
		}
		// Define "../"
		$PathRelative = ($PathLevel!=-1?str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])):'')
			.'/'
			.str_repeat( '../', ($PathLevel!=-1?$PathLevel:0) )
			.$PathRelative;
		// Resolve "../"
		$PathRelativeCount = substr_count( $PathRelative, '../' );
		for( $PathRelativeRun = 0; $PathRelativeRun < $PathRelativeCount; $PathRelativeRun++ )
		$PathRelative = preg_replace( '!\/[^\/]*?\/\.\.!is', '', $PathRelative );
		// Build correct path
		return $PathPort . str_replace( '//', '/', (isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'')
			.(is_numeric($_SERVER['SERVER_PORT'])?':'.$_SERVER['SERVER_PORT']:'')
			.(isset($_SERVER['SERVER_NAME'])?'/':'')
			.$PathRelative
		);
	}
	public static function UriRequest( $UriString = null, &$Parameter = null ) {
		if( $UriString === null ) {
			$array_uri_filter = self::UriFilter( self::UriString() );
		} else {
			$array_uri_filter = self::UriFilter( $UriString );
		}
		//Event::Debug( $UriString );
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		if(self::DEBUG){Event::Debug(self::UriString(),__FILE__,__LINE__);}
		if(self::DEBUG){Event::Debug($array_uri_filter,__FILE__,__LINE__);}
		foreach( (array)$array_uri_filter as $string_uri_content ) {
			$array_uri_content = explode( self::UriSeparator(), $string_uri_content );
			if( preg_match( '!^[a-zA-Z_\x7f-\xff\-\:][a-zA-ZöäüÖÄÜ0-9_\>\|\=\x7f-\xff\-\:]*$!', urldecode( $array_uri_content[0] ) ) ) {
				if( $UriString === null ) {
					//Event::Message('UriString UnSet',__METHOD__);
					//$_REQUEST[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
					if( $Parameter !== null ) {
						//Event::Message('Parameter Set',__METHOD__);
						$Parameter[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
					} else {
						//Event::Message('Parameter UnSet',__METHOD__);
						$_REQUEST[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
					}
				} else {
					//Event::Message('UriString Set',__METHOD__);
					if( $Parameter !== null ) {
						//Event::Message('Parameter Set',__METHOD__);
						$Parameter[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
					} else {
						//Event::Message('Parameter UnSet',__METHOD__);
						$_REQUEST[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
					}
				}
			}
		}
		// KILL PARAMETER, KILL QUESTION-STRING, KILL TRAILING-SLASH
		if( $UriString === null ) {
			return $_REQUEST[self::UriCarrier()] = preg_replace(
				array('![\w\d\s\.\-\:]*?'.self::UriSeparator().'[\w\d\s\.\-\:\>\|\=öäüÖÄÜ]*?(/|(?=\?)|$)!is','!\?.*$!is','!\/$!is'),
				'', urldecode( self::UriString() )
			);
		} else {
			return preg_replace(
				array('![\w\d\s\.\-\:]*?'.self::UriSeparator().'[\w\d\s\.\-\:\>\|\=öäüÖÄÜ]*?(/|(?=\?)|$)!is','!\?.*$!is','!\/$!is'),
				'', urldecode( $UriString )
			);
		}
		//if(self::DEBUG){Event::Debug($_REQUEST,__FILE__,__LINE__);}
	}
	public static function UriCarrier( $PathCarrier = null ) {
		if( $PathCarrier !== null ) self::$string_path_carrier = $PathCarrier; return self::$string_path_carrier;
	}
	public static function UriSeparator( $SeparatorChar = null ) {
		if( $SeparatorChar !== null ) self::$string_separator_char = $SeparatorChar; return self::$string_separator_char;
	}

	public static function UriString()
	{
		self::FixIISRequestUri();
		return substr( $_SERVER['REQUEST_URI'], strlen( $_SERVER['SCRIPT_NAME'] ) );
	}
	private static function UriFilter( $Uri )
	{
		// CUT QUESTION-STRING
		$Uri = explode( '?', $Uri );
		$Uri = explode( '/', $Uri[0] );
		$Filter = create_function( '$filter_seo_uri',
		'return preg_match("!^[^'
		.self::UriSeparator().']+?'
		.self::UriSeparator().'[^'
		.self::UriSeparator().']*$!is", $filter_seo_uri );' );
		return array_filter( $Uri, $Filter );
	}

	/**
	 * Problem to fix: The $_SERVER["REQUEST_URI"] is empty in IIS.
	 *
	 * Based on: http://www.dokeos.com/forum/viewtopic.php?t=8335#p36966
	 * Added by Ivan Tcholakov, 28-JUN-2006.
	 *
	 * @static
	 * @return void
	 */
	private static function FixIISRequestUri() {
		if (empty($_SERVER['REQUEST_URI'])) {
			$URI = $_SERVER['SCRIPT_NAME'];
			if (!empty($_SERVER['PATH_INFO'])) {
				$URI .= $_SERVER['PATH_INFO'];
			}
			if (!empty($_SERVER['QUERY_STRING'])) {
				$URI .= '?'.$_SERVER['QUERY_STRING'];
			}
			$_SERVER['REQUEST_URI'] = $URI;
		}
	}
}
?>
