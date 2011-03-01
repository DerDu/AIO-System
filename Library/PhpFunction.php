<?php
/**
 * PhpFunction Library
 *
 * Extends (existing/missing) PHP-Functionality
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
 * @package AioSystem\Library
 * @subpackage Php
 */
// ---------------------------------------------------------------------------------------
// PHP CHECK : IS_?
if( ! function_exists('is_num') ) {
	/**
	 * @param  $propertyValue
	 * @return boolean
	 */
	function is_num( $propertyValue ) {
		return ( is_int( $propertyValue ) || is_double( $propertyValue ) );
	}
}
if( ! function_exists('is_box') ) {
	/**
	 * @param  $propertyValue
	 * @return boolean
	 */
	function is_box( $propertyValue ) {
		return ( is_array( $propertyValue ) || is_object( $propertyValue ) );
	}
}

// ---------------------------------------------------------------------------------------
// PHP CHECK : IN_?
if( ! function_exists('in_string') ) {
	/**
	 * @param  $propertyHaystack
	 * @param  $propertyNeedle
	 * @return boolean
	 */
	function in_string( $propertyHaystack, $propertyNeedle ) {
		if( strpos( $propertyHaystack, $propertyNeedle ) === false ) return false;
		return true;
	}
}

// ---------------------------------------------------------------------------------------
// PHP CONVERT : ?2?
if( ! function_exists('bin2str') ) {
	function bin2str( $propertyBinary ) {
		$propertyBinary = explode(' ',trim(chunk_split($propertyBinary,8,' ')));
		$propertyBinary = array_map( 'bindec', $propertyBinary );
		$propertyBinary = array_map( 'chr', $propertyBinary );
		return implode($propertyBinary);
	}
}
if( ! function_exists('str2bin') ) {
	function str2bin( $propertyString ) {
		$propertyString = str_split( $propertyString, 1 );
		$propertyString = array_map( 'ord', $propertyString );
		$propertyString = array_map( 'decbin', $propertyString );
		return implode($propertyString);
	}
}

// ---------------------------------------------------------------------------------------
// PHP ARRAY : ARRAY_?
if( ! function_exists('array_peek') ) {
	function array_peek( array &$referenceArray ) {
		$peekArray = array_pop( $referenceArray );
		array_push( $referenceArray, $peekArray );
		return $peekArray;
	}
}
if (!function_exists('array_intersect_key')) {
	function array_intersect_key() {
		$array_args = func_get_args();
		$array_result = array_shift( $array_args );
		foreach( (array)$array_args as $array_check ) {
			foreach( (array)$array_result as $mixed_key => $mixed_value) {
				if( !array_key_exists( $mixed_key, $array_check ) ) {
					unset($array_result[$mixed_key]);
				}
			}
		}
		return $array_result;
	}
}

// ---------------------------------------------------------------------------------------
// PHP STRING : STR_PAD_?
if( ! function_exists('str_pad_right') ) {
	function str_pad_right( $propertyInput, $propertyLength, $propertyPad = " " ) {
		return str_pad( $propertyInput, $propertyLength, $propertyPad, STR_PAD_RIGHT );
	}
}
if( ! function_exists('str_pad_left') ) {
	function str_pad_left( $propertyInput, $propertyLength, $propertyPad = " " ) {
		return str_pad( $propertyInput, $propertyLength, $propertyPad, STR_PAD_LEFT );
	}
}
if( ! function_exists('str_pad_both') ) {
	function str_pad_both( $propertyInput, $propertyLength, $propertyPad = " " ) {
		return str_pad( $propertyInput, $propertyLength, $propertyPad, STR_PAD_BOTH );
	}
}

// ---------------------------------------------------------------------------------------
// PHP STRING : CHR_?
if( ! function_exists('chr_unicode') ) {
	function chr_unicode( $propertyAsciiValue ) {
		if ($propertyAsciiValue <= 0x7F)
			{ return chr($propertyAsciiValue); }
		else if ($propertyAsciiValue <= 0x7FF)
			{ return chr(0xC0 | $propertyAsciiValue >> 6) .chr(0x80 | $propertyAsciiValue & 0x3F); }
		else if ($propertyAsciiValue <= 0xFFFF)
			{ return chr(0xE0 | $propertyAsciiValue >> 12).chr(0x80 | $propertyAsciiValue >> 6 & 0x3F) .chr(0x80 | $propertyAsciiValue & 0x3F); }
		else if ($propertyAsciiValue <= 0x10FFFF)
			{ return chr(0xF0 | $propertyAsciiValue >> 18).chr(0x80 | $propertyAsciiValue >> 12 & 0x3F).chr(0x80 | $propertyAsciiValue >> 6 & 0x3F).chr(0x80 | $propertyAsciiValue & 0x3F); }
		else { return false; }
	}
}

// ---------------------------------------------------------------------------------------
// PHP STRING : STR_
// Based on: http://cogo.wordpress.com/2008/01/08/string-permutation-in-php/
if( ! function_exists('str_perm') ) {
	function str_perm( $String ) {
		if ( strlen( $String ) < 2 ) {
			return array( $String );
		}
		$Permutations = array();
		$TailList = str_perm( substr( $String, 1 ) );
		foreach ( $TailList as $Permutation ) {
			$Length = strlen( $Permutation );
			for ( $Run = 0; $Run <= $Length; $Run++ ) {
				$Permutations[] = substr( $Permutation, 0, $Run ).$String[0].substr( $Permutation, $Run );
			}
		}
		return array_unique( $Permutations );
	}
}
?>