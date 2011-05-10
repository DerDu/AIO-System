<?php
/**
 * This file contains the API:Sprite
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
use \AIOSystem\Api\System;
use \AIOSystem\Module\Sprite\ClassSprite as AIOSprite;
use \AIOSystem\Module\Sprite\ClassSpriteItem as AIOSpriteItem;
/**
 * Sprite
 *
 * @package AIOSystem\Api
 */
class Sprite {
	public static function AddImage( $ImageToAdd ) {
		$Image = Image::Instance( $ImageToAdd );
		$SpriteItem = new AIOSpriteItem();
		$SpriteItem->propertyContent( $Image );
		$SpriteItem->propertyHeight( $Image->Height() );
		$SpriteItem->propertyWidth( $Image->Width() );
		AIOSprite::addItem( $SpriteItem );
	}
	public static function Create( $SpriteImageName, $CssPrefix = '.AIOSI-' ) {
		$Stack = AIOSprite::Sprite();
		//AIOSprite::debugSpriteStructure();
		//var_dump( $Stack );
		$Sprite = Image::Instance( $SpriteImageName, AIOSprite::$_SpriteWidth, AIOSprite::$_SpriteHeight );
		$Css = '';
		while( $Stack->peekData() !== null ) {
			/** @var \AIOSystem\Module\Sprite\ClassSpriteContainer $Container */
			$Container = $Stack->popData();
			$Item = $Container->propertyItem();
			/** @var Image $Image */
			$Image = $Item->propertyContent();
			$Sprite->Layer( $Image->Resource(), $Container->propertyPositionX(), $Container->propertyPositionY() );
			$Css .= $CssPrefix.
				strtoupper(
					preg_replace('!([^\w]|[\_])!is', '', substr(basename($Image->File()),0,-3))
					//.'-ID'.$Image->Hash()
				)
				.' {'
					." ".'background: transparent '.'url("'.$SpriteImageName.'") -'.$Container->propertyPositionX().'px -'.$Container->propertyPositionY().'px no-repeat;'
					." ".'width: '.$Container->propertyWidth().'px; height: '.$Container->propertyHeight().'px;'
					." ".'display: block;'
				." ".'} '."\n";
		}
		$Sprite->Save();
		$CssFile = System::File( pathinfo( $Sprite->File(), PATHINFO_DIRNAME ).DIRECTORY_SEPARATOR.pathinfo( $Sprite->File(), PATHINFO_FILENAME ).'.css' );
		$CssFile->propertyFileContent( $Css );
		$CssFile->writeFile();
	}
}
?>
