<?php
/**
 * SpriteItem
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
 *  * Neither the name of the Gerd Christian Kunze nor the names of its
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
 * @subpackage Sprite
 */
namespace AioSystem\Module\Sprite;
/**
 * @package AioSystem\Module
 * @subpackage Sprite
 */
interface InterfaceSpriteItem {
	public function getArea();
	public function propertyWidth( $Value = null );
	public function propertyHeight( $Value = null );
	public function propertyContent( $Content = null );
}
/**
 * @package AioSystem\Module
 * @subpackage Sprite
 */
class ClassSpriteItem implements InterfaceSpriteItem {
	private $_propertyWidth = null;
	private $_propertyHeight = null;
	private $_propertyContent = null;
	/**
	 * @return null|int
	 */
	public function getArea() {
		return $this->propertyWidth() * $this->propertyHeight();
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
	 * @param null|mixed $Content
	 * @return null|mixed
	 */
	public function propertyContent( $Content = null ) {
		if( $Content !== null ) {
			$this->_propertyContent = $Content;
		} return $this->_propertyContent;
	}
}
?>