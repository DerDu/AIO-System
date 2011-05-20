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
 * @package AIOSystem\Core
 * @subpackage Stack
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\Session;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Core
 * @subpackage Stack
 */
interface InterfaceStackRegister {
	public static function Instance();
// ---------------------------------------------------------------------------------------
	public function getRegister( $propertyRegisterIndex );
	public function setRegister( $propertyRegisterIndex, $propertyRegisterData );
	public function listRegister();
}
/**
 * @package AIOSystem\Core
 * @subpackage Stack
 */
class ClassStackRegister implements InterfaceStackRegister {
	private $_propertyStackRegister = array();
	/**
	 * @var bool|string $_propertyStackPersistent
	 */
	private $_propertyStackPersistent = false;
// ---------------------------------------------------------------------------------------
	/**
	 * @param bool|string $Persistent
	 * @return ClassStackRegister
	 */
	public static function Instance( $Persistent = false ) {
		return new ClassStackRegister( $Persistent );
	}
	/**
	 * @param bool|string $Persistent
	 */
	function __construct( $Persistent = false ) {
		$this->_propertyStackPersistent = $Persistent;
		$this->_loadRegister();
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param int|string $propertyRegisterIndex
	 * @return mixed
	 */
	public function getRegister( $propertyRegisterIndex ) {
		$this->_loadRegister();
		if( ! isset( $this->_propertyStackRegister[$propertyRegisterIndex] ) ) return null;
		return $this->_propertyStackRegister[$propertyRegisterIndex];
	}
	/**
	 * @param int $propertyRegisterIndex
	 * @param mixed $propertyRegisterData
	 * @return mixed
	 */
	public function setRegister( $propertyRegisterIndex, $propertyRegisterData ) {
		$this->_loadRegister();
		$this->_propertyStackRegister[$propertyRegisterIndex] = $propertyRegisterData;
		$this->_saveRegister();
		return $this->getRegister( $propertyRegisterIndex );
	}
	/**
	 * @return array
	 */
	public function listRegister() {
		$this->_loadRegister();
		return $this->_propertyStackRegister;
	}
	private function _loadRegister() {
		if( $this->_propertyStackPersistent !== false && empty( $this->_propertyStackRegister ) ) {
			if( null !== ( $Register = Session::Read( $this->_propertyStackPersistent ) ) ) {
				$this->_propertyStackRegister = unserialize( Session::Decode( base64_decode( $Register ) ) );
			}
		}
	}
	private function _saveRegister() {
		if( $this->_propertyStackPersistent !== false ) {
			Session::Write( $this->_propertyStackPersistent, base64_encode( Session::Encode( serialize( $this->_propertyStackRegister ) ) ) );
		}
	}
}
?>
