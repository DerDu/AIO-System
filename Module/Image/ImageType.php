<?php
/**
 * Image-Type
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
interface InterfaceImageType {
	public static function MimeTypeToExtension( $MimeType );
}
/**
 * @package AIOSystem\Module
 * @subpackage Image
 */
class ClassImageType implements InterfaceImageType {
	public static function MimeTypeToExtension( $MimeType ) {
		switch( $MimeType ) {
			case image_type_to_mime_type( IMAGETYPE_GIF ): return image_type_to_extension( IMAGETYPE_GIF );
			case image_type_to_mime_type( IMAGETYPE_JPEG ): return str_replace(array('E','e'),'',image_type_to_extension( IMAGETYPE_JPEG ));
			case image_type_to_mime_type( IMAGETYPE_PNG ): return image_type_to_extension( IMAGETYPE_PNG );
			case image_type_to_mime_type( IMAGETYPE_SWF ): return image_type_to_extension( IMAGETYPE_SWF );
			case image_type_to_mime_type( IMAGETYPE_PSD ): return image_type_to_extension( IMAGETYPE_PSD );
			case image_type_to_mime_type( IMAGETYPE_BMP ): return image_type_to_extension( IMAGETYPE_BMP );
			case image_type_to_mime_type( IMAGETYPE_TIFF_II ): return image_type_to_extension( IMAGETYPE_TIFF_II );
			case image_type_to_mime_type( IMAGETYPE_TIFF_MM ): return image_type_to_extension( IMAGETYPE_TIFF_MM );
			case image_type_to_mime_type( IMAGETYPE_JPC ): return image_type_to_extension( IMAGETYPE_JPC );
			case image_type_to_mime_type( IMAGETYPE_JP2 ): return image_type_to_extension( IMAGETYPE_JP2 );
			case image_type_to_mime_type( IMAGETYPE_JPX ): return image_type_to_extension( IMAGETYPE_JPX );
			case image_type_to_mime_type( IMAGETYPE_JB2 ): return image_type_to_extension( IMAGETYPE_JB2 );
			case image_type_to_mime_type( IMAGETYPE_SWC ): return image_type_to_extension( IMAGETYPE_SWC );
			case image_type_to_mime_type( IMAGETYPE_IFF ): return image_type_to_extension( IMAGETYPE_IFF );
			case image_type_to_mime_type( IMAGETYPE_WBMP ): return image_type_to_extension( IMAGETYPE_WBMP );
			case image_type_to_mime_type( IMAGETYPE_XBM ): return image_type_to_extension( IMAGETYPE_XBM );
			case image_type_to_mime_type( IMAGETYPE_ICO ): return image_type_to_extension( IMAGETYPE_ICO );
			default: return false;
		}
	}
}
?>
