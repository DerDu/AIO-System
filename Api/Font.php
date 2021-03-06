<?php
/**
 * This file contains the API:Font
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
use \AIOSystem\Core\ClassSystemDirectory as AIODirectory;
use \AIOSystem\Module\Font\ClassFont as AIOFont;
use \AIOSystem\Module\Font\ClassUtf8Encoding as AIOUtf8Encoding;
use \AIOSystem\Library\ClassFont as AIOLibraryFont;
use \AIOSystem\Api\Image;
/**
 * @package AIOSystem\Api
 */
class Font {
	public static function Image( $Text, $Size = null, $Color = null, $Font = null, $Level = 0 ) {
		$Image = self::Create( $Text, $Size, $Color, $Font );
		$AIOImage = Image::Instance( $Image );
		return '<img src="'.Seo::Path( AIODirectory::relativeDirectory( $Image, __DIR__.'/../../' ), $Level ).'" alt="'.$Text.'" width="'.$AIOImage->Width().'" height="'.$AIOImage->Height().'"/>';
	}
	/**
	 * @static
	 * @param string $Text
	 * @param null|int $Size
	 * @param null|string $Color
	 * @param null|string $Font
	 * @return bool|string
	 */
	public static function Create( $Text, $Size = null, $Color = null, $Font = null ) {
		return AIOFont::Create( $Text, $Size, $Color, $Font );
	}
	/**
	 * @static
	 * @param string $File
	 * @return string
	 */
	public static function ConvertTTF2AFM( $File ) {
		return AIOLibraryFont::convertTtf2Afm( $File );
	}
	public static function Utf8( $Content ) {
		return AIOFont::font_utf8( $Content );
	}

	public static function MixedToUtf8( $Text ) {
		return AIOUtf8Encoding::MixedToUtf8( $Text );
	}
	public static function MixedToLatin1( $Text ) {
		return AIOUtf8Encoding::MixedToLatin1( $Text );
	}
}
?>
