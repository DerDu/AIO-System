<?php
/**
 * Color
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
 * @package AIOSystem\Library
 * @subpackage Color
 */
namespace AIOSystem\Library;
/**
 * @package AIOSystem\Library
 * @subpackage Color
 */
interface InterfaceColor {
}
/**
 * @package AIOSystem\Library
 * @subpackage Color
 */
class ClassColor implements InterfaceColor {

	public static function convertHex2Rgb( $colorHex ) {
		$array_hexcolor = str_split(
			substr(
				strtoupper( trim( $colorHex ) ),
				(strlen($colorHex)>4?-6:-3)
			),
			(strlen($colorHex)>4?2:1)
		);
		foreach( (array)$array_hexcolor as $integer_rgbcolor => $string_rgbcolor ){
			$array_hexcolor[$integer_rgbcolor] = hexdec( str_pad_left( $string_rgbcolor, 2, $string_rgbcolor ) );
		} return $array_hexcolor;
	}

	public static function convertHex2RgbFloat( $colorHex ) {
		$array_hexcolor = self::convertHex2Rgb( $colorHex );
		foreach( (array)$array_hexcolor as $integer_rgbcolor => $string_rgbcolor ){
			$array_hexcolor[$integer_rgbcolor] = (100 / 255 * $string_rgbcolor) / 100;
		} return $array_hexcolor;
	}
}
?>