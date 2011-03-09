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
 * @package AioSystem\Core
 * @subpackage Seo
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Seo
 */
interface InterfaceSeoUri {
	public static function uri_path( $string_path_relative, $integer_path_level = 0 );
	public static function uri_carrier( $string_path_carrier = null );
	public static function uri_separator( $string_separator_char = null );
	public static function uri_request();
}
/**
 * @package AioSystem\Core
 * @subpackage Seo
 */
class ClassSeoUri implements InterfaceSeoUri
{
	private static $string_separator_char = '~';
	private static $string_path_carrier = 'URI-PATH';
	
	public static function uri_path( $string_path_relative, $integer_path_level = 0 )
	{
		if( !isset($_SERVER['SERVER_PORT']) ) $_SERVER['SERVER_PORT'] = '';

		switch( $_SERVER['SERVER_PORT'] )
		{
			case '80': $integer_path_port = 'http://'; break;
			case '21': $integer_path_port = 'ftp://'; break;
			default: $integer_path_port = '';
		}
		$string_path_relative = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])).'/'.str_repeat( '../', $integer_path_level ).$string_path_relative;
		// Resolve "../"
		$integer_path_relative_count = substr_count( $string_path_relative, '../' );
		for( $integer_path_relative_run = 0; $integer_path_relative_run < $integer_path_relative_count; $integer_path_relative_run++ )
		$string_path_relative = preg_replace( '!\/[^\/]*?\/\.\.!is', '', $string_path_relative );
		// Build correct path
		return $integer_path_port .= str_replace( '//', '/', (isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME'].'/':'')
			//.':'.$_SERVER['SERVER_PORT']
			.$string_path_relative 
		);
	}
	public static function uri_request()
	{
		$array_uri_filter = self::uri_filter( self::uri_string() );
		foreach( (array)$array_uri_filter as $string_uri_content )
		{
			$array_uri_content = explode( self::uri_separator(), $string_uri_content );
			if( preg_match( '!^[a-zA-Z_\x7f-\xff\-\:][a-zA-Z0-9_\x7f-\xff\-\:]*$!', urldecode( $array_uri_content[0] ) ) )
			{
				$_REQUEST[urldecode( $array_uri_content[0] )] = urldecode( $array_uri_content[1] );
			}
		}
		// KILL PARAMETER, KILL QUESTION-STRING, KILL TRAILING-SLASH
		$_REQUEST[self::uri_carrier()] = preg_replace(
			array('![\w\d\s\.\-\:]*?'.self::uri_separator().'[\w\d\s\.\-\:]*?(/|(?=\?)|$)!is','!\?.*$!is','!\/$!is'),
			'', urldecode( self::uri_string() )
		);
	}
	public static function uri_carrier( $string_path_carrier = null ) {
		if( $string_path_carrier !== null ) self::$string_path_carrier = $string_path_carrier; return self::$string_path_carrier;
	}
	public static function uri_separator( $string_separator_char = null ) {
		if( $string_separator_char !== null ) self::$string_separator_char = $string_separator_char; return self::$string_separator_char;
	}

	private static function uri_string()
	{
		return substr( $_SERVER['REQUEST_URI'], strlen( $_SERVER['SCRIPT_NAME'] ) );
	}
	private static function uri_filter( $filter_seo_uri )
	{
		// CUT QUESTION-STRING 
		$filter_seo_uri = explode( '?', $filter_seo_uri );
		$filter_seo_uri = explode( '/', $filter_seo_uri[0] );
		$filter_seo = create_function( '$filter_seo_uri',
		'return preg_match("!^[^'
		.self::uri_separator().']+?'
		.self::uri_separator().'[^'
		.self::uri_separator().']*$!is", $filter_seo_uri );' );
		return array_filter( $filter_seo_uri, $filter_seo );
	}
}
?>