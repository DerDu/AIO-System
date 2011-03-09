<?php
/**
 * This file contains the API:Image
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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Module\Image\ClassImageResource as ImageResource;
use \AioSystem\Module\Image\ClassImageResize as ImageResize;
use \AioSystem\Module\Image\ClassImageLayer as ImageLayer;
/**
 * @package AioSystem\Api
 */
class Image {

	private $_propertyFile = null;
	private $_propertyResource = null;
	private $_propertyWidth = null;
	private $_propertyHeight = null;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $File
	 * @param null|int $Width
	 * @param null|int $Height
	 * @return Image
	 */
	public static function Instance( $File, $Width = null, $Height = null ) {
		$ClassImage = new Image();
		if( file_exists( $File ) ) {
			$ClassImage->Load( $File );
			$ClassImage->_propertyResource( \AioSystem\Module\Image\ClassImageResource::Load( $File ) );
		} else if( $Width !== null && $Height !== null ) {
			$ClassImage->_propertyFile( $File );
			$ClassImage->Create( $Width, $Height );
		} else {
			trigger_error( 'File not found' );
		}
		return $ClassImage;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param string $File
	 */
	public function Load( $File ) {
		$this->_propertyFile( $File );
		$this->_propertyResource(false);
		$this->_propertyWidth(false);
		$this->_propertyHeight(false);
	}
	/**
	 * @param null|string $File
	 */
	public function Save( $File = null ) {
		ImageResource::Save( $this->_propertyResource(), $this->_propertyFile( $File ), 100 );
	}
	/**
	 * @param int $Width
	 * @param int $Height
	 */
	public function Create( $Width, $Height ) {
		$this->_propertyResource( ImageResource::Create( $Width, $Height ) );
	}
	/**
	 * @return string
	 */
	public function Hash() {
		return sha1_file( $this->_propertyFile() );
	}
	/**
	 * @return null|string
	 */
	public function File() {
		return $this->_propertyFile();
	}
// ---------------------------------------------------------------------------------------
	public function ResizePixel( $Width = null, $Height = null ) {
		ImageResize::RelativePixel( $this->_propertyResource(), $Width, $Height );
	}
	public function ResizePixelAbsolute( $Width = null, $Height = null ) {
		ImageResize::AbsolutePixel( $this->_propertyResource(), $Width, $Height );
	}
	public function ResizePercent( $Width = null, $Height = null ) {
		ImageResize::RelativePercent( $this->_propertyResource(), $Width, $Height );
	}
	public function ResizePercentAbsolute( $Width = null, $Height = null ) {
		ImageResize::AbsolutePercent( $this->_propertyResource(), $Width, $Height );
	}
// ---------------------------------------------------------------------------------------
	public function Layer( $File, $OffsetX = 0, $OffsetY = 0 ) {
		$this->_propertyResource(
			ImageLayer::Copy( $this->_propertyResource(), $File, $OffsetY, $OffsetX )
		);
	}
// ---------------------------------------------------------------------------------------
	public function Width() {
		if( $this->_propertyWidth() === null ) {
			$this->_propertyWidth( imagesx( $this->_propertyResource() ) );
		} return $this->_propertyWidth();
	}
	public function Height() {
		if( $this->_propertyHeight() === null ) {
			$this->_propertyHeight( imagesy( $this->_propertyResource() ) );
		} return $this->_propertyHeight();
	}
	/**
	 * @return \resource
	 */
	public function Resource() {
		return $this->_propertyResource();
	}
// ---------------------------------------------------------------------------------------
	private function _propertyFile( $File = null ) {
		if( $File !== null ) {
			$this->_propertyFile = $File;
		} return $this->_propertyFile;
	}
	private function _propertyResource( $Resource = null ) {
		if( $Resource !== null ) {
			if( $Resource === false ) {
				$this->_propertyResource = null;
			} else {
				$this->_propertyResource = $Resource;
			}
		} return $this->_propertyResource;
	}
	private function _propertyWidth( $Width = null ) {
		if( $Width !== null ) {
			if( $Width === false ) {
				$this->_propertyWidth = null;
			} else {
				$this->_propertyWidth = $Width;
			}
		} return $this->_propertyWidth;
	}
	private function _propertyHeight( $Height = null ) {
		if( $Height !== null ) {
			if( $Height === false ) {
				$this->_propertyHeight = null;
			} else {
				$this->_propertyHeight = $Height;
			}
		} return $this->_propertyHeight;
	}
}
?>