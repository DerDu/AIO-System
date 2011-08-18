<?php
/**
 * This file contains the API:Pdf
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
use \AIOSystem\Module\Pdf\ClassEzpdf as AIOPdf;
/**
 * @package AIOSystem\Api
 */
class Pdf {
	/**
	 * @static
	 * @param string $File
	 * @return void
	 */
	public static function Open( $File ) {
		AIOPdf::ezpdf_open( $File );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Close() {
		AIOPdf::ezpdf_close();
	}
	/**
	 * @static
	 * @param string $File
	 * @return void
	 */
	public static function FontFamily( $File ) {
		AIOPdf::ezpdf_font_family( $File );
	}
	/**
	 * @static
	 * @param null|int $Size
	 * @return int
	 */
	public static function FontSize( $Size = null ) {
		return AIOPdf::ezpdf_font_size( $Size );
	}
	/**
	 * @static
	 * @param null|string $Align
	 * @return string
	 */
	public static function FontAlign( $Align = null ) {
		return AIOPdf::ezpdf_font_align( $Align );
	}
	/**
	 * @static
	 * @param null|string $Color
	 * @return string
	 */
	public static function FontColor( $Color = null ) {
		return AIOPdf::ezpdf_font_color( $Color );
	}
	/**
	 * @static
	 * @param null|float $Height
	 * @return int
	 */
	public static function FontLineHeight( $Height = null ) {
		return AIOPdf::ezpdf_font_lineheight( $Height );
	}
	/**
	 * @static
	 * @param null|float $Top
	 * @param null|float $Right
	 * @param null|float $Bottom
	 * @param null|float $Left
	 * @return array
	 */
	public static function FontMargin( $Top = null, $Right = null, $Bottom = null, $Left = null ) {
		return AIOPdf::ezpdf_font_margin( $Top, $Right, $Bottom, $Left );
	}
	/**
	 * @static
	 * @param string $Text
	 * @return bool
	 */
	public static function Text( $Text ) {
		return AIOPdf::ezpdf_text( $Text );
	}
	/**
	 * @static
	 * @param string $Text
	 * @return string
	 */
	public static function TextEncode( $Text ) {
		return AIOPdf::ezpdf_utf8( $Text );
	}
	/**
	 * @static
	 * @param string $File
	 * @return bool
	 */
	public static function Image( $File ) {
		return AIOPdf::ezpdf_image( $File );
	}
	/**
	 * @static
	 * @param array $Content
	 * @param string|array $Column
	 * @param string $Title
	 * @param array $Extension
	 * @return float
	 */
	public static function Table( $Content, $Column = '', $Title = '', $Extension = array() ) {
		return AIOPdf::ezpdf_table( $Content, $Column, $Title, $Extension );
	}
	/**
	 * @static
	 * @param array $Content
	 * @param array $Column
	 * @param string|function $Callback
	 * @return array
	 */
	public static function TableMap( $Content, $Column, $Callback ) {
		return AIOPdf::ezpdf_table_map( $Content, $Column, $Callback );
	}
	public static function TableShowGrid( $Show ) {
		return AIOPdf::ezpdf_table_showlines( $Show = null );
	}
	public static function TableShowHeader( $Show = null ) {
		return AIOPdf::ezpdf_table_showheader( $Show );
	}
	public static function TableShowShade( $Show = null ) {
		return AIOPdf::ezpdf_table_showshade( $Show );
	}
	const TABLE_ALIGN_LEFT = 'left';
	const TABLE_ALIGN_RIGHT = 'right';
	const TABLE_ALIGN_CENTER = 'center';
	public static function TableAlign( $Align = self::TABLE_ALIGN_CENTER ) {
		return AIOPdf::ezpdf_table_align( $Align );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Line() {
		AIOPdf::ezpdf_line();
	}
	/**
	 * @static
	 * @param null|string $Color
	 * @return string
	 */
	public static function LineColor( $Color = null ) {
		return AIOPdf::ezpdf_line_color( $Color );
	}
}
?>
