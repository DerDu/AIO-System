<?php
/**
 * Pdf
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
 * @subpackage EzPdf
 */
namespace AIOSystem\Module\Pdf;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Font;
/**
 * @package AIOSystem\Module
 * @subpackage Pdf
 */
interface InterfaceEzpdf {
	public static function ezpdf_open( $string_filename );
	public static function ezpdf_close();

	public static function ezpdf_font_family( $string_filename );
	public static function ezpdf_font_size( $integer_size = null );
	public static function ezpdf_font_align( $string_align = null );
	public static function ezpdf_font_color( $string_hexcolor = null );
	public static function ezpdf_font_lineheight( $float_lineheight = null );
	public static function ezpdf_font_margin( $float_marginleft = null, $float_marginright = null );

	public static function ezpdf_text( $string_text );

	public static function ezpdf_table( $array_content );
	public static function ezpdf_table_showlines( $integer_showlines = null );
	public static function ezpdf_table_showheader( $integer_showheader = null );
	public static function ezpdf_table_showshade( $integer_showshade = null );

	public static function ezpdf_image( $string_filename );

	public static function ezpdf_line();
	public static function ezpdf_line_color( $string_hexcolor = null );

	public static function ezpdf_utf8( $string_utf8_encode );
	public static function ezpdf_table_map( $array_content, $array_columns, $string_callback );
}
/**
 * @package AIOSystem\Module
 * @subpackage Pdf
 */
class ClassEzpdf implements InterfaceEzpdf {
	/** @var $ezpdf_instance \Cezpdf */
	private static $ezpdf_instance = null;
	private static $ezpdf_filename = null;

	private static $ezpdf_option_font_size = 12;
	private static $ezpdf_option_font_align = 'left';
	private static $ezpdf_option_font_color = '000000';
	private static $ezpdf_option_font_lineheight = 1;
	private static $ezpdf_option_font_margintop = 0;
	private static $ezpdf_option_font_marginright = 0;
	private static $ezpdf_option_font_marginbottom = 0;
	private static $ezpdf_option_font_marginleft = 0;

	private static $ezpdf_option_table_align = 'center';
	private static $ezpdf_option_table_showlines = 1;
	private static $ezpdf_option_table_showheader = 1;
	private static $ezpdf_option_table_showshade = 1;
	private static $ezpdf_option_table_padding_row = 2;
	private static $ezpdf_option_table_padding_column = 5;
	private static $ezpdf_option_table_width = 0;
	private static $ezpdf_option_table_width_max = 0;

	private static $ezpdf_option_line_color = '000000';
	private static $ezpdf_option_line_margintop = 0;
	private static $ezpdf_option_line_marginbottom = 0;
// ---------------------------------------------------------------------------------------
	public function __construct() {
	}
	public static function ezpdf_utf8( $string_utf8_encode ) {
		return utf8_decode( str_replace( '€', chr(128), $string_utf8_encode ) );
	}
	public static function ezpdf_table_map( $array_content, $array_columns, $string_callback ) {
		foreach( (array)$array_content as $index_row => $array_row ){
			foreach( (array)$array_row as $string_column => $mixed_column ){
				if( in_array( $string_column, (array)$array_columns ) ){
					$array_content[$index_row][$string_column] = call_user_func_array( $string_callback, array($mixed_column) );
				}
			}
		}
		return $array_content;
	}
// ezPDF : Open/Close --------------------------------------------------------------------
	public static function ezpdf_open( $string_filename ) {
		if( !class_exists( 'Cezpdf' ) ) require_once(__DIR__ . '/Ezpdf/class.ezpdf.php');
		self::ezpdf_instance( new \Cezpdf() );
		self::ezpdf_filename( $string_filename );
		// Set defaults
		self::ezpdf_font_color();
		self::ezpdf_line_color();
	}
	public static function ezpdf_close() {
		$ClassSystemFile = System::File( self::ezpdf_filename() );
		$ClassSystemFile->propertyFileContent( self::ezpdf_instance()->ezOutput() );
		$ClassSystemFile->writeFile();
	}
// ezPDF : FONT --------------------------------------------------------------------------
	public static function ezpdf_font_family( $string_filename ) {
		if( strtoupper( pathinfo( $string_filename, PATHINFO_EXTENSION ) ) == 'TTF' ) {
			$string_filename = Font::ConvertTTF2AFM( $string_filename );
		}
		// $string_filename e.g. path/fontname.afm
		if( strtoupper( pathinfo( $string_filename, PATHINFO_EXTENSION ) ) != 'AFM' )
			throw new \Exception(
				'Font not usable!<br/>'
						.$string_filename.'<br/>'
						.'Convert font online to .afm @ http://onlinefontconverter.com/<br/>'
						.'e.g. use "path/fontname.afm" as parameter.'
			);
		self::ezpdf_instance()->selectFont( $string_filename );
	}
	public static function ezpdf_font_size( $integer_size = null ) {
		// default: 12
		if( $integer_size !== null ) self::$ezpdf_option_font_size = $integer_size;
		return self::$ezpdf_option_font_size;
	}
	public static function ezpdf_font_align( $string_align = null ) {
		// default: left (right, center, centre, full)
		if( $string_align !== null ) self::$ezpdf_option_font_align = $string_align;
		return self::$ezpdf_option_font_align;
	}
	public static function ezpdf_font_color( $string_hexcolor = null ) {
		// default: 000000
		if( $string_hexcolor !== null ) self::$ezpdf_option_font_color = $string_hexcolor;
		return self::$ezpdf_option_font_color;
	}
	public static function ezpdf_font_lineheight( $float_lineheight = null ) {
		// default: 1
		if( $float_lineheight !== null ) self::$ezpdf_option_font_lineheight = $float_lineheight;
		return self::$ezpdf_option_font_lineheight;
	}
	public static function ezpdf_font_margin( $float_margintop = null, $float_marginright = null, $float_marginbottom = null, $float_marginleft = null ) {
		// default: 0
		if( $float_margintop !== null ) self::$ezpdf_option_font_margintop = $float_margintop;
		if( $float_marginright !== null ) self::$ezpdf_option_font_marginright = $float_marginright;
		if( $float_marginbottom !== null ) self::$ezpdf_option_font_marginbottom = $float_marginbottom;
		if( $float_marginleft !== null ) self::$ezpdf_option_font_marginleft = $float_marginleft;
		return array( self::$ezpdf_option_font_margintop, self::$ezpdf_option_font_marginright, self::$ezpdf_option_font_marginbottom, self::$ezpdf_option_font_marginleft );
	}
// ezPDF : PAGE --------------------------------------------------------------------------
	public static function ezpdf_page_margin( $float_cm_top, $float_cm_right, $float_cm_bottom, $float_cm_left ) {
		self::ezpdf_instance()->ezSetCmMargins( $float_cm_top, $float_cm_bottom, $float_cm_left, $float_cm_right );
	}
	public static function ezpdf_newpage() {
		self::ezpdf_instance()->ezNewPage();
	}
	public static function ezpdf_page_movecursor( $integer_units, $bool_force = false ) {
		// ( * - 1 ) -> Bottom Up Y ( e.g. 10 Units Down = -10 )
		self::ezpdf_instance()->ezSetDy( ($integer_units * -1), ($bool_force?'makeSpace':'') );
	}

// ezPDF : TABLE -------------------------------------------------------------------------
	public static function ezpdf_table( $array_content, $array_columns = '', $string_title = '', $debug_extension = array() ) {
		$array_align = self::ezpdf_table_align();
		$array_width = self::ezpdf_table_width();
		return self::ezpdf_instance()->ezTable( $array_content,
			$array_columns, $string_title,
			array_merge(
			array(
				'showLines'=>self::ezpdf_table_showlines(),
				'showHeadings'=>self::ezpdf_table_showheader(),
				'shaded'=>self::ezpdf_table_showshade(),
				'fontSize'=>self::ezpdf_font_size(),
				'textCol'=>\AIOSystem\Library\ClassColor::convertHex2RgbFloat( self::ezpdf_font_color() ),
				'lineCol'=>\AIOSystem\Library\ClassColor::convertHex2RgbFloat( self::ezpdf_line_color() ),
				'width'=>$array_width[0],
				'maxWidth'=>$array_width[1],
				'outerLineThickness'=>0.5,
				'innerLineThickness'=>0.5,
				'xPos'=>$array_align[0],
				'xOrientation'=>$array_align[1]
			), $debug_extension )
		);
	}
	public static function ezpdf_table_align( $string_align = null ) {
		// default: center (left, right, centre, full)
		if( $string_align !== null ) self::$ezpdf_option_table_align = $string_align;
		$array_padding = self::ezpdf_table_padding();
		switch( strtoupper( self::$ezpdf_option_table_align ) ){
			case 'LEFT': return array(
				self::ezpdf_attribute_getMarginLeft()
					+ (self::ezpdf_table_showlines()>0?$array_padding[1]:0),
				'right'
			);
			case 'RIGHT': return array(
				self::ezpdf_attribute_getPageWidth()
					- self::ezpdf_attribute_getMarginRight()
			        + $array_padding[1]
					+ (self::ezpdf_table_showlines()>0?0:$array_padding[1]),
				'left'
			);
			default: return array('center','center');
		}
	}
	public static function ezpdf_table_width( $float_width_percent = null, $float_max_percent = null ) {
		if( $float_width_percent !== null ) self::$ezpdf_option_table_width = $float_width_percent;
		if( $float_max_percent !== null ) self::$ezpdf_option_table_width_max = $float_max_percent;
		// default 'width'=>0, 'maxWidth'=>0
		return array(
			(self::$ezpdf_option_table_width>0)?((self::ezpdf_attribute_getPageWidth()-self::ezpdf_attribute_getMarginLeft()-self::ezpdf_attribute_getMarginRight())/100*self::$ezpdf_option_table_width):0,
			(self::$ezpdf_option_table_width_max>0)?((self::ezpdf_attribute_getPageWidth()-self::ezpdf_attribute_getMarginLeft()-self::ezpdf_attribute_getMarginRight())/100*self::$ezpdf_option_table_width_max):0
		);
	}
	public static function ezpdf_table_padding( $float_padding_row = null, $float_padding_column = null ) {
		// default: 2
		if( $float_padding_row !== null ) self::$ezpdf_option_table_padding_row = $float_padding_row;
		// default: 5
		if( $float_padding_column !== null ) self::$ezpdf_option_table_padding_column = $float_padding_column;
		return array( self::$ezpdf_option_table_padding_row, self::$ezpdf_option_table_padding_column );
	}
	public static function ezpdf_table_showlines( $integer_showlines = null ) {
		if( $integer_showlines !== null ) self::$ezpdf_option_table_showlines = $integer_showlines;
		return self::$ezpdf_option_table_showlines;
	}
	public static function ezpdf_table_showheader( $integer_showheader = null ) {
		if( $integer_showheader !== null ) self::$ezpdf_option_table_showheader = $integer_showheader;
		return self::$ezpdf_option_table_showheader;
	}
	public static function ezpdf_table_showshade( $integer_showshade = null ) {
		if( $integer_showshade !== null ) self::$ezpdf_option_table_showshade = $integer_showshade;
		return self::$ezpdf_option_table_showshade;
	}
// ezPDF : TEXT --------------------------------------------------------------------------
	public static function ezpdf_text( $string_text ) {
		$array_rgbcolor = \AIOSystem\Library\ClassColor::convertHex2RgbFloat( self::$ezpdf_option_font_color );
		self::ezpdf_instance()->setColor($array_rgbcolor[0],$array_rgbcolor[1],$array_rgbcolor[2]);

		self::ezpdf_page_movecursor( self::ezpdf_attribute_getFontMarginTop() );

		return self::ezpdf_instance()->ezText(
			$string_text,
			self::ezpdf_font_size(),
			array(
				'left'=>self::ezpdf_attribute_getFontMarginLeft(),
				'right'=>self::ezpdf_attribute_getFontMarginRight(),
				'justification'=>self::ezpdf_font_align(),
				'spacing'=>self::ezpdf_font_lineheight()
			)
		);
		self::ezpdf_page_movecursor( self::ezpdf_attribute_getFontMarginBottom() );
	}
// ezPDF : IMAGE -------------------------------------------------------------------------
	public static function ezpdf_image( $string_filename ) {
		return self::ezpdf_instance()->ezImage( $string_filename );
	}
// ---------------------------------------------------------------------------------------
	public static function ezpdf_line() {
		self::ezpdf_page_movecursor( self::ezpdf_attribute_getLineMarginTop() );

		self::ezpdf_line_color();
		self::ezpdf_instance()->setLineStyle( 0.5 );

		self::ezpdf_instance()->line(
			self::ezpdf_attribute_getMarginLeft() +
					self::ezpdf_attribute_getFontMarginLeft(),
			self::ezpdf_attribute_getCursor(),
			(
					self::ezpdf_attribute_getPageWidth() -
							self::ezpdf_attribute_getMarginRight() -
							self::ezpdf_attribute_getFontMarginRight()
			),
			self::ezpdf_attribute_getCursor()
		);

		self::ezpdf_page_movecursor( self::ezpdf_attribute_getLineMarginBottom() );
	}
	public static function ezpdf_line_color( $string_hexcolor = null ) {
		// default: 000000
		if( $string_hexcolor !== null ) self::$ezpdf_option_line_color = $string_hexcolor;
		$array_rgbcolor = \AIOSystem\Library\ClassColor::convertHex2RgbFloat( self::$ezpdf_option_line_color );
		self::ezpdf_instance()->setStrokeColor($array_rgbcolor[0],$array_rgbcolor[1],$array_rgbcolor[2]);
		return self::$ezpdf_option_line_color;
	}
	public static function ezpdf_line_margin( $float_margintop = null, $float_marginbottom = null ) {
		// default: 0
		if( $float_margintop !== null ) self::$ezpdf_option_line_margintop = $float_margintop;
		if( $float_marginbottom !== null ) self::$ezpdf_option_line_marginbottom = $float_marginbottom;
		return array( self::$ezpdf_option_line_margintop, self::$ezpdf_option_line_marginbottom );
	}
// ---------------------------------------------------------------------------------------
	private static function ezpdf_filename( $string_filename = null ) {
		if( $string_filename !== null ) self::$ezpdf_filename = $string_filename;
		return self::$ezpdf_filename;
	}
	public static function ezpdf_instance( $object_ezpdf = null ) {
		if( $object_ezpdf !== null ) self::$ezpdf_instance = $object_ezpdf;
		return self::$ezpdf_instance;
	}
// ---------------------------------------------------------------------------------------
	private static function ezpdf_attribute_getLineMarginTop() {
		$array_LineMargin = self::ezpdf_line_margin(); return $array_LineMargin[0];
	}
	private static function ezpdf_attribute_getLineMarginBottom() {
		$array_LineMargin = self::ezpdf_line_margin(); return $array_LineMargin[1];
	}
	private static function ezpdf_attribute_getFontMarginTop() {
		$array_TextMargin = self::ezpdf_font_margin(); return $array_TextMargin[0];
	}
	private static function ezpdf_attribute_getFontMarginRight() {
		$array_TextMargin = self::ezpdf_font_margin(); return $array_TextMargin[1];
	}
	private static function ezpdf_attribute_getFontMarginBottom() {
		$array_TextMargin = self::ezpdf_font_margin(); return $array_TextMargin[2];
	}
	private static function ezpdf_attribute_getFontMarginLeft() {
		$array_TextMargin = self::ezpdf_font_margin(); return $array_TextMargin[3];
	}
	private static function ezpdf_attribute_getMarginTop() {
		return self::ezpdf_instance()->ez['topMargin'];
	}
	private static function ezpdf_attribute_getMarginBottom() {
		return self::ezpdf_instance()->ez['bottomMargin'];
	}
	private static function ezpdf_attribute_getMarginLeft() {
		return self::ezpdf_instance()->ez['leftMargin'];
	}
	private static function ezpdf_attribute_getMarginRight() {
		return self::ezpdf_instance()->ez['rightMargin'];
	}
	private static function ezpdf_attribute_getPageWidth() {
		return self::ezpdf_instance()->ez['pageWidth'];
	}
	private static function ezpdf_attribute_getPageHeight() {
		return self::ezpdf_instance()->ez['pageWidth'];
	}
	private static function ezpdf_attribute_getCursor() {
		return self::ezpdf_instance()->y;
	}
}
?>