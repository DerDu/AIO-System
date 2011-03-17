<?php
/**
 * Image-Effect
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
 * @subpackage Image
 */
namespace AioSystem\Module\Image;
/**
 * @package AioSystem\Module
 * @subpackage Image
 */
interface InterfaceImageEffect {
}
/**
 * @package AioSystem\Module
 * @subpackage Image
 */
class ClassImageEffect implements InterfaceImageEffect {
	public static function Negative( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_NEGATE );
	}
	public static function GrayScale( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_GRAYSCALE );
	}
	public static function EdgeDetect( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_EDGEDETECT );
	}
	public static function Emboss( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_EMBOSS );
	}
	public static function GaussianBlur( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_GAUSSIAN_BLUR );
	}
	public static function SelectiveBlur( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_SELECTIVE_BLUR );
	}
	public static function MeanRemoval( \AioSystem\Api\Image $Image ) {
		imagefilter( $Image->Resource(), IMG_FILTER_MEAN_REMOVAL );
	}
	public static function Brightness( \AioSystem\Api\Image $Image, $Level ) {
		imagefilter( $Image->Resource(), IMG_FILTER_BRIGHTNESS, $Level );
	}
	public static function Contrast( \AioSystem\Api\Image $Image, $Level ) {
		imagefilter( $Image->Resource(), IMG_FILTER_CONTRAST, $Level );
	}
	public static function Smooth( \AioSystem\Api\Image $Image, $Level ) {
		imagefilter( $Image->Resource(), IMG_FILTER_SMOOTH, $Level );
	}
}
?>