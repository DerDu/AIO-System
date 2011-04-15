<?php
/**
 * Image-Resize
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
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
interface InterfaceImageResize {
	public static function RelativePercent( $image_file, $resize_width = null, $resize_height = null );
	public static function AbsolutePercent( $image_file, $resize_width = null, $resize_height = null );
	public static function RelativePixel( $image_file, $resize_width = null, $resize_height = null );
	public static function AbsolutePixel( $image_file, $resize_width = null, $resize_height = null );
}
// ---------------------------------------------------------------------------------------
// LICENSE (BSD)
//
//	Copyright (c) 2009, Gerd Christian Kunze
//	All rights reserved.
//
//	Redistribution and use in source and binary forms, with or without
//	modification, are permitted provided that the following conditions are
//	met:
//
//		* Redistributions of source code must retain the above copyright
//		  notice, this list of conditions and the following disclaimer.
//		* Redistributions in binary form must reproduce the above copyright
//		  notice, this list of conditions and the following disclaimer in the
//		  documentation and/or other materials provided with the distribution.
//		* Neither the name of Gerd Christian Kunze nor the names of the
//		  contributors may be used to endorse or promote products derived from
//		  this software without specific prior written permission.
//
//	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
//	IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
//	THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
//	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
//	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
//	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
//	PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
//	PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
//	LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
//	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
//	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
class ClassImageResize implements InterfaceImageResize {
	public static function RelativePercent( $image_file, $resize_width = null, $resize_height = null ) {
		if( ( $image_resource = ClassImageResource::Load( $image_file ) ) !== false )
		{
			if( $resize_width === null ) $resize_width = 1;
			if( $resize_height === null ) $resize_height = 1;
			// GET SIZE
			$image_original_width = imagesx( $image_resource ) * $resize_width;
			$image_original_height = imagesy( $image_resource ) * $resize_height;
			return self::RelativePixel( $image_resource, $image_original_width, $image_original_height );
		}
	}
	public static function AbsolutePercent( $image_file, $resize_width = null, $resize_height = null )
	{
		if( ( $image_resource = ClassImageResource::Load( $image_file ) ) !== false )
		{
			if( $resize_width === null ) $resize_width = 1;
			if( $resize_height === null ) $resize_height = 1;
			// GET SIZE
			$image_original_width = imagesx( $image_resource ) * $resize_width; 
			$image_original_height = imagesy( $image_resource ) * $resize_height; 
			return self::AbsolutePixel( $image_resource, $image_original_width, $image_original_height );
		}
	}
	public static function RelativePixel( $image_file, $resize_width = null, $resize_height = null )
	{
		if( ( $image_resource = ClassImageResource::Load( $image_file ) ) !== false )
		{
			// NOTHING TO DO ?
			if( $resize_width === null && $resize_height === null ) return $image_resource;
			// GET SIZE
			$image_original_width = imagesx( $image_resource ); 
			$image_original_height = imagesy( $image_resource ); 
			// ONLY ONE SIDE GIVEN ?
			if( ! ( $resize_width !== null && $resize_height !== null ) )
			{
				if( $resize_width === null ) $resize_width = $image_original_width;
				if( $resize_height === null ) $resize_height = $image_original_height;
			}
			// NOTHING TO DO EITHER ?
			if( $resize_width == $image_original_width && $resize_height == $image_original_height ) return $image_resource;
			// GET ASPECT RATIO
			$image_original_ratio = $image_original_width / $image_original_height;
			// CALC NEW SIZE
			if( ( $resize_width_result = ( $resize_height * $image_original_ratio ) ) > $resize_width )
			$resize_height = $resize_width / $image_original_ratio;
			else
			$resize_width = $resize_width_result;
			// RESIZE TO ABSOLUTE PIXEL
			return self::AbsolutePixel( $image_resource, $resize_width, $resize_height );
		}
	}
	public static function AbsolutePixel( $image_file, $resize_width = null, $resize_height = null )
	{
		if( ( $image_resource = ClassImageResource::Load( $image_file ) ) !== false )
		{
			// NOTHING TO DO ?
			if( $resize_width === null && $resize_height === null ) return $image_resource;
			// GET SIZE
			$image_original_width = imagesx( $image_resource ); 
			$image_original_height = imagesy( $image_resource ); 
			// ONLY ONE SIDE GIVEN ?
			if( ! ( $resize_width !== null && $resize_height !== null ) )
			{
				if( $resize_width === null ) $resize_width = $image_original_width;
				if( $resize_height === null ) $resize_height = $image_original_height;
			}
			$image_resource_result = ClassImageResource::Create( $resize_width, $resize_height );
			imagecopyresampled(
				$image_resource_result, $image_resource,
				0, 0,
				0, 0,
				$resize_width, $resize_height,
				$image_original_width, $image_original_height
			);
			return $image_resource_result;
		}
	}
}
?>