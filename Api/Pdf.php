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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Module\Pdf\ClassEzpdf as AioPdf;
/**
 * @package AioSystem\Api
 */
class ClassPdf {
	public static function Open( $File ) {
		return AioPdf::ezpdf_open( $File );
	}
	public static function Close() {
		return AioPdf::ezpdf_close();
	}

	public static function FontFamily( $File ) {
		return AioPdf::ezpdf_font_family( $File );
	}
	public static function FontSize( $Size = null ) {
		return AioPdf::ezpdf_font_size( $Size );
	}
	public static function FontAlign( $Align = null ) {
		return AioPdf::ezpdf_font_align( $Align );
	}
	public static function FontColor( $Color = null ) {
		return AioPdf::ezpdf_font_color( $Color );
	}
	public static function FontLineHeight( $Height = null ) {
		return AioPdf::ezpdf_font_lineheight( $Height );
	}
	public static function FontMargin( $Top = null, $Right = null, $Bottom = null, $Left = null ) {
		return AioPdf::ezpdf_font_margin( $Top, $Right, $Bottom, $Left );
	}

	public static function Text( $Text ) {
		return AioPdf::ezpdf_text( $Text );
	}
	public static function TextEncode( $Text ) {
		return AioPdf::ezpdf_utf8( $Text );
	}

	public static function Image( $File ) {
		return AioPdf::ezpdf_image( $File );
	}
	public static function Table( $Content, $Column = '', $Title = '', $Extension = array() ) {
		return AioPdf::ezpdf_table( $Content, $Column, $Title, $Extension );
	}
	/**
	 * @static
	 * @param  array $Content
	 * @param  array $Column
	 * @param  string|function $Callback
	 * @return array
	 */
	public static function TableMap( $Content, $Column, $Callback ) {
		return AioPdf::ezpdf_table_map( $Content, $Column, $Callback );
	}
	public static function TableShowGrid( $Show ) {
		return AioPdf::ezpdf_table_showlines( $Show = null );
	}
	public static function TableShowHeader( $Show = null ) {
		return AioPdf::ezpdf_table_showheader( $Show );
	}
	public static function TableShowShade( $Show = null ) {
		return AioPdf::ezpdf_table_showshade( $Show );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Line() {
		return AioPdf::ezpdf_line();
	}
	/**
	 * @static
	 * @param null|string $Color
	 * @return string
	 */
	public static function LineColor( $Color = null ) {
		return AioPdf::ezpdf_line_color( $Color );
	}
}
?>