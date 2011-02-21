<?php
/**
 * StackRegister
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
 * @package AioSystem\Core
 * @subpackage Stack
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Stack
 */
interface InterfaceStackRegister {
	public static function Instance();
// ---------------------------------------------------------------------------------------
	public function getRegister( $propertyRegisterIndex = 0 );
	public function setRegister( $propertyRegisterIndex, $propertyRegisterData );
	public function listRegister();
}
/**
 * @package AioSystem\Core
 * @subpackage Stack
 */
class ClassStackRegister implements InterfaceStackRegister {
	private $_propertyStackRegister = array();
// ---------------------------------------------------------------------------------------
	public static function Instance() {
		return new ClassStackRegister();
	}
// ---------------------------------------------------------------------------------------
	public function getRegister( $propertyRegisterIndex = null ) {
		if( ! isset( $this->_propertyStackRegister[$propertyRegisterIndex] ) ) return null;
		return $this->_propertyStackRegister[$propertyRegisterIndex];
	}
	public function setRegister( $propertyRegisterIndex, $propertyRegisterData ) {
		$this->_propertyStackRegister[$propertyRegisterIndex] = $propertyRegisterData;
		return $this->getRegister( $propertyRegisterIndex );
	}
	public function listRegister() {
		return $this->_propertyStackRegister;
	}
}
?>