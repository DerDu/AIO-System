<?php
/**
 * StackObject
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
interface InterfaceStackObject {
	public static function Instance( $Persistent = true );
// ---------------------------------------------------------------------------------------
	public function LoadObject( $Identifier, $Callback = null );
	public function SaveObject( $Identifier, $Object );
	public function ListObject();
}
/**
 * @package AIOSystem\Core
 * @subpackage Stack
 */
class ClassStackObject implements InterfaceStackObject {
	private $StackRegister = array();
	private $StackPersistent = true;

	public static function Instance( $Persistent = true ) {
		return new ClassStackObject( $Persistent );
	}
	private function __construct( $Persistent = true ) {
		$this->StackPersistent = $Persistent;
	}

	public function ListObject() {
		$this->_load();
		return array_keys( $this->StackRegister );
	}

	public function LoadObject( $Identifier, $NameToFileCallback = null ) {
		$this->_load();
		$Name = $this->StackRegister[$Identifier][0];
		$String = $this->StackRegister[$Identifier][1];

		Event::Debug( $this->StackRegister );
		Event::Debug( $Name );
		Event::Debug( $String );

		if( !class_exists( $Name ) ) {
			if( $NameToFileCallback !== null && is_callable( $NameToFileCallback ) ) {
				$File = $NameToFileCallback( $Name );
			} else {
				$File = $Name;
			}
			require_once( $File );
		}

		return $this->_unserialize( $String );
	}

	public function SaveObject( $Identifier, $Object ) {
		$this->_load();
		$Name = get_class( $Object );
		$String = $this->_serialize( $Object );
		$this->StackRegister[$Identifier] = array( $Name, $String );
		$this->_save();
	}

	private function _serialize( $Object ) {
		$String = serialize( $Object );
		$String = base64_encode( $String );
		return Session::Encode( $String );
	}
	private function _unserialize( $String ) {
		$String = Session::Decode( $String );
		$String = base64_decode( $String );
		return unserialize( $String );
	}

	private function _load() {
		if( empty( $this->StackRegister ) ) {
			//Event::Debug( $_SESSION,__METHOD__,__LINE__ );
			$this->StackRegister = Session::Read( __CLASS__ );
			//Event::Debug( $this->StackRegister,__METHOD__,__LINE__ );
		}
	}
	private function _save() {
		if( $this->StackPersistent ) {
			//Event::Debug( $this->StackRegister,__METHOD__,__LINE__ );
			Session::Write( __CLASS__, $this->StackRegister );
			//Event::Debug( $_SESSION,__METHOD__,__LINE__ );
		}
	}
}
