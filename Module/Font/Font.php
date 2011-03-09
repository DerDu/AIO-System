<?php
/**
 * Font
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
 * @package AioSystem\Module
 * @subpackage Font
 */
namespace AioSystem\Module\Font;
use \AioSystem\Api\Image as AioImage;
use \AioSystem\Api\Cache as AioCache;
use \AioSystem\Library\ClassColor as AioColor;
use \AioSystem\Core\ClassSystemDirectory as AioDirectory;
/**
 * @package AioSystem\Module
 * @subpackage Font
 */
interface InterfaceFont
{
	public static function Create( $string_text, $float_text_size = null, $string_text_color = null, $string_font_filename = null );
	public static function font_text_color( $string_text_color = null );
	public static function font_text_size( $float_text_size = null );
	public static function font_file_name( $string_font_filename = null );
	public static function font_utf8( $string_text );
}
/**
 * @package AioSystem\Module
 * @subpackage Font
 */
class ClassFont implements InterfaceFont
{
	private static $font_file_name = null;
	private static $font_text_color = '#000000';
	private static $font_text_size = 12;

	public static function font_image( $string_text, $string_font_filename = null, $float_text_size = null, $string_text_color = null  )
	{
		// Find Path BASE ( = File@LastEntry )
		$array_trace = debug_backtrace();
		$array_trace = array_pop($array_trace);
		// TODO: [FIX BUG] AIOSeoPath for AJAX Call -> wrong RelativePath e.g. aio::seo_path( '../'.aio::directory_relative(... or something blabla :o(
		return '<img alt="'.str_replace('"','\"',$string_text).'" src="'.AioDirectory::relativeDirectory( self::Create( $string_text, $float_text_size, $string_text_color, $string_font_filename ), dirname($array_trace['file']) ).'" />';
	}
	public static function Create( $string_text, $float_text_size = null, $string_text_color = null, $string_font_filename = null )
	{
		// Set Options
		self::font_text_size( $float_text_size );
		self::font_text_color( $string_text_color );
		self::font_file_name( $string_font_filename );
		// Fetch Cache
		$string_image = AioCache::Location('Font',true).self::font_hash( $string_text ).'.png';
		if( file_exists( $string_image ) ) return $string_image;
		// Check Font
		if( !file_exists( self::font_file_name() ) ){
			trigger_error('File not found!');
			return false;
		}
		// Fetch Font-Box
		$array_font_box = imagettfbbox( self::font_text_size(), 0, self::font_file_name(), self::font_ncr( $string_text ) );
		// Fetch Dimensions ( + 2 px = FIX: FontStyle )
		$integer_image_x = abs($array_font_box[2]) + abs($array_font_box[0]) + 2;
		$integer_image_y = abs($array_font_box[7]) + abs($array_font_box[1]);

		$AioImage = AioImage::Instance( $string_image, $integer_image_x, $integer_image_y );
		$array_color = AioColor::convertHex2Rgb( self::font_text_color() );

		imagettftext(
			$AioImage->Resource(),
			self::font_text_size(),
			0, 0 , abs( $array_font_box[5] ),
			imagecolorallocate(
				$AioImage->Resource(),
				$array_color[0],
				$array_color[1],
				$array_color[2]
			),
			self::font_file_name(),
			self::font_ncr( $string_text )
		);
		$AioImage->Save();
		return $string_image;
	}

	public static function font_text_color( $string_text_color = null )
	{
		if( $string_text_color !== null ) self::$font_text_color = $string_text_color;
		return self::$font_text_color;
	}
	public static function font_text_size( $float_text_size = null )
	{
		if( $float_text_size !== null ) self::$font_text_size = $float_text_size;
		return self::$font_text_size;
	}
	public static function font_file_name( $string_font_filename = null )
	{
		if( $string_font_filename !== null ) self::$font_file_name = $string_font_filename;
		return self::$font_file_name;
	}
	public static function font_utf8( $string_text )
	{
		return utf8_decode( str_replace( '€', chr(128), $string_text ) );
	}
	private static function font_ncr( $string_text )
	{
		$array_text = str_split( $string_text, 1 ); $string_text = '';
		foreach ( (array) $array_text as $value_char ){
			$string_text .= "&#".ord($value_char).";";
		}
		return $string_text;
	}
	private static function font_hash( $string_text )
	{
		return sha1(
			self::font_file_name()
			.self::font_text_size()
			.self::font_ncr( $string_text )
		);
	}
}
?>