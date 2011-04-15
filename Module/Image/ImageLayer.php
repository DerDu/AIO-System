<?php
/**
 * Image-Layer
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
 * @subpackage Image
 */
namespace AIOSystem\Module\Image;
use \AIOSystem\Api\Image;
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
interface InterfaceImageLayer {
	public static function Copy( $image_file, $image_layer, $position_top = 0, $position_left = 0 );
}
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
class ClassImageLayer implements InterfaceImageLayer
{
	public static function Copy( $image_file, $image_layer, $position_top = 0, $position_left = 0 ){

		if( ( $image_resource_file = ClassImageResource::Load( $image_file ) ) !== false ){
			if( ( $image_resource_layer = ClassImageResource::Load( $image_layer ) ) !== false ){
				imageSaveAlpha($image_resource_file, true);
				imagecopy( $image_resource_file, $image_resource_layer,
					$position_left, $position_top, 0, 0,
					imagesx( $image_resource_layer ), imagesy( $image_resource_layer )
				);
			}
		}
		return $image_resource_file;
	}
	public static function Rotate( Image $Image, $Angle, $Background ) {
		return imagerotate( $Image->Resource(), $Angle, $Background );
	}
}
?>