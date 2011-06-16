<?php
/**
 * This file contains the API:Seo
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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Core\ClassSeoUri as AIOSeoUri;
use \AIOSystem\Library\ClassJQueryAddress as AIOSeoDeeplink;
/**
 * @package AIOSystem\Api
 */
class Seo {
	/**
	 * Build absolute Uri for SEO usage
	 *
	 * @static
	 * @param string $Path
	 * @param int $Level
	 * @return string
	 */
	public static function Path( $Path, $Level = 0 ) {
		return AIOSeoUri::UriPath( $Path, $Level );
	}
	/**
	 * Add SEO-Url-Parameter to $_REQUEST
	 *
	 * @static
	 * @param null|string $UriString
	 * @param null|array $Parameter
	 * @return string
	 */
	public static function Request( $UriString = null, &$Parameter = null ) {
		return AIOSeoUri::UriRequest( $UriString, $Parameter );
	}
	/**
	 * @static
	 * @param string|array $Target
	 * @param string|array $File
	 * @param string $Name
	 * @param array $Parameter
	 * @param int $Level
	 * @return string
	 */
	public static function DeepLink( $Target, $File, $Name, $Parameter = array(), $Level = 0 ) {
		return AIOSeoDeeplink::jQueryAddressDeeplink( $Target, $File, $Name, $Parameter, $Level );
	}
	public static function NameConvention( $Name ) {
		$Convert = array(
			'Ä'=>'Ae','ä'=>'ae',
			'Ö'=>'Oe','ö'=>'oe',
			'Ü'=>'Ue','ü'=>'ue',
			'ß'=>'ss',
			'â'=>'ae','ô'=>'oe',
			'é'=>'ee','ó'=>'oe',
			'è'=>'e','ò'=>'o',
			'´'=>'-','_'=>'-','§'=>' Paragraph ',
			'&'=>' und '
		);
		$Name = str_replace( array_keys( $Convert ), array_values( $Convert ), $Name );
		$Convert = array(
			'a' => array( 230,229,227,226,225,224 ),
			'A' => array( 197,195,194,193,192 ),
			'e' => array( 235,234,233,232 ),
			'f' => array( 131 ),
			'c' => array( 231,162 ),
			'C' => array( 199 ),
			'D' => array( 208 ),
			'E' => array( 203,202,201,200 ),
			'i' => array( 239,238,237,236,161 ),
			'I' => array( 207,206,205,204 ),
			'L' => array( 163 ),
			'n' => array( 241 ),
			'N' => array( 209 ),
			'o' => array( 240,245,244,243,242 ),
			'O' => array( 213,212,211,210 ),
			'S' => array( 138 ),
			'u' => array( 251,250,249 ),
			'U' => array( 219,218,217 ),
			'y' => array( 255,253 ),
			'Y' => array( 221,165,159 ),
		);
		for( $Run = (strlen( $Name )-1); $Run > 0; $Run-- ) {
			$Ord = ord( $Name[$Run] );
			if( $Ord < 32 ) {
				$Name[$Run] = ' ';
			}
			if( $Ord > 127 ) {
				Event::Message($Ord.':'.chr($Ord).'->'.array_search_recursive( $Ord, $Convert ));
				$Name[$Run] = array_search_recursive( $Ord, $Convert );
			}
		}
		$Name = preg_replace(
			array( '![^\w\d\s\-\.]!is', '!\s{2,}!is', '!(^\s+|\s+$)!is', '!\s!is', '!-{2,}!is', '!\.{2,}!is' ),
			array( '', ' ', '', '-', '-', '.' ), $Name
		);
		return strtoupper($Name[0]).substr($Name,1);
	}
}
?>
