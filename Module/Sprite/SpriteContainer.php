<?php
/**
 * SpriteContainer
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
 * @subpackage Sprite
 */
namespace AIOSystem\Module\Sprite;
/**
 * @package AIOSystem\Module
 * @subpackage Sprite
 */
interface InterfaceSpriteContainer {
	public function getArea();
	public function getCut();
	public function propertyWidth( $Value = null );
	public function propertyHeight( $Value = null );
	public function propertyPositionX( $Value = null );
	public function propertyPositionY( $Value = null );
	public function propertyItem( ClassSpriteItem $Item = null );
}
/**
 * @package AIOSystem\Module
 * @subpackage Sprite
 */
class ClassSpriteContainer implements InterfaceSpriteContainer {
	const CUT_HORIZONTAL = 1;
	const CUT_VERTICAL = 2;
	/** @var null|int $_propertyWidth */
	private $_propertyWidth = null;
	/** @var null|int $_propertyHeight */
	private $_propertyHeight = null;
	/** @var int $_propertyPositionX */
	private $_propertyPositionX = 0;
	/** @var int $_propertyPositionY */
	private $_propertyPositionY = 0;
	public $Combine = 0;

	/** @var ClassSpriteItem $_propertyItem */
	private $_propertyItem = null;

	function __construct( $PositionX = 0, $PositionY = 0, $Width = null, $Height = null ) {
		$this->propertyPositionX( $PositionX );
		$this->propertyPositionY( $PositionY );
		$this->propertyWidth( $Width );
		$this->propertyHeight( $Height );
	}
	/**
	 * @return null|int
	 */
	public function getArea() {
		return $this->propertyWidth() * $this->propertyHeight();
	}
	/**
	 * @return int
	 */
	public function getCut() {
		$ItemWidth = $this->propertyItem()->propertyWidth();
		$ItemHeight = $this->propertyItem()->propertyHeight();
		$Horizontal = ($this->propertyWidth() - $ItemWidth) * $ItemHeight;
		$Vertical = ($this->propertyHeight() - $ItemHeight) * $ItemWidth;
		$Leftover = ($this->propertyWidth() - $ItemWidth) * ($this->propertyHeight() - $ItemHeight);
		if( $Horizontal + $Leftover <= $Vertical + $Leftover ) {
			return self::CUT_HORIZONTAL;
		} else {
			return self::CUT_VERTICAL;
		}
	}
	/**
	 * @param null|int $Value
	 * @return null|int
	 */
	public function propertyWidth( $Value = null ) {
		if( $Value !== null ) {
			$this->_propertyWidth = $Value;
		} return $this->_propertyWidth;
	}
	/**
	 * @param null|int $Value
	 * @return null|int
	 */
	public function propertyHeight( $Value = null ) {
		if( $Value !== null ) {
			$this->_propertyHeight = $Value;
		} return $this->_propertyHeight;
	}
	/**
	 * @param null|int $Value
	 * @return int
	 */
	public function propertyPositionX( $Value = null ) {
		if( $Value !== null ) {
			$this->_propertyPositionX = $Value;
		} return $this->_propertyPositionX;
	}
	/**
	 * @param null|int $Value
	 * @return int
	 */
	public function propertyPositionY( $Value = null ) {
		if( $Value !== null ) {
			$this->_propertyPositionY = $Value;
		} return $this->_propertyPositionY;
	}
	/**
	 * @param ClassSpriteItem|null $Item
	 * @return ClassSpriteItem|null
	 */
	public function propertyItem( ClassSpriteItem $Item = null ) {
		if( $Item !== null ) {
			$this->_propertyItem = $Item;
		} return $this->_propertyItem;
	}
}
?>