<?php
/**
 * Font
 *
 * This product includes software developed by the TTF2PT1 Project and its contributors.
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
 * @subpackage Font
 */
namespace AIOSystem\Library;
/**
 * @package AIOSystem\Library
 * @subpackage Font
 */
interface InterfaceFont {
	public static function convertTtf2Afm( $string_filename_input );
}
/**
 * @package AIOSystem\Library
 * @subpackage Font
 */
class ClassFont implements InterfaceFont
{
	public static function convertTtf2Afm( $string_filename_input )
	{
		if( !file_exists( __DIR__.'/Font/ttf2pt1.exe' ) )
			throw new \Exception('Converter not available!');

		$string_filename_output = dirname( $string_filename_input ).'/'.pathinfo( $string_filename_input, PATHINFO_FILENAME ).'.afm';
		if( ! file_exists( $string_filename_output ) ) {
			system( escapeshellcmd( __DIR__.'/Font/ttf2pt1.exe -a '.$string_filename_input.' '.substr( $string_filename_output, 0, -4 ) ) );
			unlink( substr( $string_filename_output, 0, -4 ).'.t1a' );
		}

		return $string_filename_output;
	}
}
?>