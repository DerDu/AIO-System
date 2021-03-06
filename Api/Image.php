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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Module\Image\ClassImageResource as AIOImageResource;
use \AIOSystem\Module\Image\ClassImageResize as AIOImageResize;
use \AIOSystem\Module\Image\ClassImageLayer as AIOImageLayer;
use \AIOSystem\Module\Image\ClassImageType as AIOImageType;
/**
 * @package AIOSystem\Api
 */
interface InterfaceImage {
	public static function Instance( $File, $Width = null, $Height = null );
	public function Load( $File );
	public function Save( $File = null );
	public function Create( $Width, $Height );
	public function Hash();
	public function File();
	public function ResizePixel( $Width = null, $Height = null );
	public function ResizePixelAbsolute( $Width = null, $Height = null );
	public function ResizePercent( $Width = null, $Height = null );
	public function ResizePercentAbsolute( $Width = null, $Height = null );
	public function Layer( $File, $OffsetX = 0, $OffsetY = 0 );
	public function Rotate( $Angle, $Background = 127 );
	public function Width();
	public function Height();
	public function Resource( \resource $Resource = null );
}
/**
 * @package AIOSystem\Api
 */
class Image implements InterfaceImage {

	private $_propertyFile = null;
	private $_propertyResource = null;
	private $_propertyWidth = null;
	private $_propertyHeight = null;
	private $_propertyExtension = null;
	private $_propertyMimeType = null;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $File
	 * @param null|int $Width
	 * @param null|int $Height
	 * @return \AIOSystem\Api\Image
	 */
	public static function Instance( $File, $Width = null, $Height = null ) {
		$ClassImage = new Image();
		if( file_exists( $File ) && ($Width === null && $Height === null) ) {
			$ClassImage->Load( $File );
			$ClassImage->_propertyResource( AIOImageResource::Load( $File ) );
		} else if( $Width !== null && $Height !== null ) {
			$ClassImage->_propertyFile( $File );
			$ClassImage->Create( $Width, $Height );
		} else {
			trigger_error( 'File not found' );
		}
		$ClassImage->_propertyExtension = AIOImageType::MimeTypeToExtension( $ClassImage->MimeType() );
		return $ClassImage;
	}
	/**
	 * @static
	 * @param string $MimeType
	 * @return string|false
	 */
	public static function MimeTypeToExtension( $MimeType, $SkipDot = false ) {
		return AIOImageType::MimeTypeToExtension( $MimeType, $SkipDot );
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
		$this->_propertyMimeType = null;
	}
	/**
	 * @param null|string $File
	 */
	public function Save( $File = null ) {
		AIOImageResource::Save( $this->_propertyResource(), $this->_propertyFile( $File ), 100 );
	}
	/**
	 * @param int $Width
	 * @param int $Height
	 */
	public function Create( $Width, $Height ) {
		$this->_propertyResource( AIOImageResource::Create( $Width, $Height ) );
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
	public function MimeType() {
		if( $this->_propertyMimeType === null ) {
			if( class_exists( '\finfo' ) ) {
				$Info = new \finfo( FILEINFO_MIME_TYPE );
				if( $this->_propertyMimeType != $Info->file( $this->File() ) ) {
					$this->_propertyMimeType = $Info->file( $this->File() );
				}
			} else {
				$InfoApp = array();
				$Info = getimagesize( $this->File(), $InfoApp );
				if( $Info === false ) {
					return false;
				}
				if( $this->_propertyMimeType != $Info['mime'] ) {
					$this->_propertyMimeType = $Info['mime'];
				}
			}
		}
		return $this->_propertyMimeType;
	}
/*
	public function Thumbnail( $Width = 100, $Height = 100, $Path = null, $Timeout = null ) {
		$CacheIdentifierObject = array_merge( array( $this->_propertyFile(), $Width, $Height, $Path, 'Object' ) );
		$CacheIdentifierImage = array_merge( array( $this->_propertyFile(), $Width, $Height, $Path, 'Image' ) );
		/** @var \AIOSystem\Module\Cache\Serializer $SerializerThumbnail */
/*		if( false === ( $SerializerThumbnail = Cache::Get( $CacheIdentifierObject, 'ImageThumbLink'.DIRECTORY_SEPARATOR.'x'.$Width.'y'.$Height, true ) ) ) {
			$this->ResizePixel( $Width, $Height );
			$this->Save( Cache::CacheFile( $CacheIdentifierImage, 'ImageThumbLink'.DIRECTORY_SEPARATOR.'x'.$Width.'y'.$Height, true, $Timeout ), $this->MimeType() );
			Cache::Set( $CacheIdentifierObject, Cache::SerializeObject( $this ), 'ImageThumbLink'.DIRECTORY_SEPARATOR.'x'.$Width.'y'.$Height, true );
			$this->Save( $this->File().$this->_propertyExtension, $this->MimeType() );
		} else {
			/** @var Image $Thumbnail */
/*			$Thumbnail = $SerializerThumbnail->Load();
			$this->Load( $Thumbnail->File() );
			$this->_propertyResource( AIOImageResource::Load( $Thumbnail->File() ) );
			$this->_propertyExtension = AIOImageType::MimeTypeToExtension( $this->MimeType() );
			$this->Save( $Thumbnail->File().$this->_propertyExtension, $this->MimeType() );
			Event::Debug( $Thumbnail );
			Event::Debug( $this );
		}
	}
*/

	public function Thumbnail( $Width = 100, $Height = 100, $Path = null, $Timeout = null ) {
		if( $Path === null ) {
			$Path = System::DirectorySyntax( pathinfo( $this->_propertyFile(), PATHINFO_DIRNAME ), true, DIRECTORY_SEPARATOR );
		} else {
			$Path = System::CreateDirectory( System::DirectorySyntax( $Path, true, DIRECTORY_SEPARATOR ) );
		}
		if( $Timeout === null ) {
			$Timeout = (60*60*24*7);
		}
		if( !preg_match('!-Thumbnail$!',pathinfo( $this->_propertyFile(), PATHINFO_FILENAME ) ) ) {
			$Thumbnail = $Path
				.pathinfo( $this->_propertyFile(), PATHINFO_FILENAME )
				.'-Thumbnail.'
				.pathinfo( $this->_propertyFile(), PATHINFO_EXTENSION );
			if( !file_exists( $Thumbnail ) || filemtime( $Thumbnail ) > ( time() + $Timeout ) ) {
				$this->ResizePixel( $Width, $Height );
				$this->Save( $Thumbnail );
			} else {
				$this->_propertyFile( $Thumbnail );
			}
		}
	}
// ---------------------------------------------------------------------------------------
	public function ResizePixel( $Width = null, $Height = null ) {
		$this->_propertyResource(
			AIOImageResize::RelativePixel( $this->_propertyResource(), $Width, $Height )
		);
	}
	public function ResizePixelAbsolute( $Width = null, $Height = null ) {
		$this->_propertyResource(
			AIOImageResize::AbsolutePixel( $this->_propertyResource(), $Width, $Height )
		);
	}
	public function ResizePercent( $Width = null, $Height = null ) {
		$this->_propertyResource(
			AIOImageResize::RelativePercent( $this->_propertyResource(), $Width, $Height )
		);
	}
	public function ResizePercentAbsolute( $Width = null, $Height = null ) {
		$this->_propertyResource(
			AIOImageResize::AbsolutePercent( $this->_propertyResource(), $Width, $Height )
		);
	}
// ---------------------------------------------------------------------------------------
	public function Layer( $File, $OffsetX = 0, $OffsetY = 0 ) {
		$this->_propertyResource(
			AIOImageLayer::Copy( $this->_propertyResource(), $File, $OffsetY, $OffsetX )
		);
	}
	public function Rotate( $Angle, $Background = 127 ) {
		$this->_propertyResource(
			AIOImageLayer::Rotate( $this->_propertyResource(), $Angle, $Background )
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
	 * @param null|\resource $Resource
	 * @return \resource
	 */
	public function Resource( \resource $Resource = null ) {
		return $this->_propertyResource( $Resource );
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
