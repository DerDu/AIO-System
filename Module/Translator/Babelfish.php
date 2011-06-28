<?php
/**
 * Babelfish
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
 * @package AIOSystem\Module
 * @subpackage Translator
 */
namespace AIOSystem\Module\Translator;
use \AIOSystem\Api\Session;
use \AIOSystem\Api\Proxy;
/**
 * @package AIOSystem\Module
 * @subpackage Translator
 */
interface InterfaceBabelfish {
	public static function Translate( $string_text, $string_from = 'en', $string_to = 'de' );
	public static function Container( $string_text, $string_from = 'en', $string_to = 'de' );
}
/**
 * @package AIOSystem\Module
 * @subpackage Translator
 */
class Babelfish implements InterfaceBabelfish {
	private static $babelfish_service = 'http://babelfish.yahoo.com/translate_txt';
	private static $babelfish_fields = array('ei'=>'UTF-8','doit'=>'done','fr'=>'bf-home','intl'=>'1','tt'=>'urltext','btnTrTxt'=>'Translate');

	public static function Translate( $string_text, $string_from = 'en', $string_to = 'de' ){
		// Something to do ?
		if( empty( $string_text ) ) return '';
		if( $string_from == $string_to ) return $string_text;
		// Check-Cache
		$array_hash = Session::Read(__METHOD__);
		if( isset( $array_hash[sha1($string_text.$string_from.$string_to)] ) ){
			if( ($string_cache = $array_hash[sha1($string_text.$string_from.$string_to)]) !== null )
			return $string_cache;
		}
		// Build Query
		$babelfish_query = '';
		foreach( (array)self::$babelfish_fields as $string_key => $string_value ){
			$babelfish_query .= $string_key.'='.$string_value.'&';
		}
		$babelfish_query .= 'lp='.$string_from.'_'.$string_to.'&trtext='.urlencode($string_text);
		// Fetch Data
		if( Proxy::IsUsed() ) {
			$string_result = Proxy::HttpGet( self::$babelfish_service.'?'.$babelfish_query );
		} else {
			$resource_query = fopen( self::$babelfish_service.'?'.$babelfish_query, "r" );
			if( is_resource( $resource_query ) ){
				$string_result = '';
				while( !feof( $resource_query ) ){
					$string_result .= fread( $resource_query, 1024 );
				}
				fclose( $resource_query );
			} else $string_result = '';
		}
		preg_match('!(?<=id\=\"result\">).*?(?=\<\/div\>)!sm', $string_result, $array_result );
		// Fill-Cache
		if( !isset( $array_result[0] ) ) return false;
		$array_hash = Session::Read(__METHOD__);
		$array_hash[sha1($string_text.$string_from.$string_to)] = trim(strip_tags($array_result[0]));
		Session::Write(__METHOD__,$array_hash);
		// Return
		return trim(strip_tags($array_result[0]));
	}
	public static function Container( $string_text, $string_from = 'en', $string_to = 'de' ){
		// Something to do ?
		if( empty( $string_text ) ) return '';
		if( $string_from == $string_to ) return $string_text;
		$string_babelfish = self::Translate( $string_text, $string_from, $string_to );
		if( str_replace(' ','',$string_babelfish) == str_replace(' ','',$string_text) ) return $string_text;
		// Build Container
		return '<span style="display:block; color: #303000; border: 1px dotted #C0C0C0; padding: 5px; margin: 3px 0 3px 0;">'
					.$string_text.'<br/>'
					.'<span style="display:block; font-family:monospace; font-size: 11px; font-weight:normal; color: #909090; border-top: 1px dotted #DDD; margin: 5px 0 5px 0;">'
						.'Babelfish '.strtoupper($string_from.'->'.$string_to).':<br/>'
					.'</span>'
					.self::Translate( $string_text, $string_from, $string_to )
				.'</span>';
	}
}
