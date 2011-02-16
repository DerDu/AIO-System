<?php
namespace AioSystem\Module;
// ---------------------------------------------------------------------------------------
// InterfaceSocketDevice, ClassSocketDevice
// ---------------------------------------------------------------------------------------
interface InterfaceSocketDevice {
	public function openSocketDevice();
	public function readSocketDevice( $propertyLength = null );
	public function writeSocketDevice( $propertyData );
	public function closeSocketDevice();
}
// ---------------------------------------------------------------------------------------
// LICENSE (BSD)
//
//	Copyright (c) 2011, Gerd Christian Kunze
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
//		* Neither the name of the Gerd Christian Kunze nor the names of its
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
class ClassSocketDevice implements InterfaceSocketDevice {
	/** @var resource $_propertySocketDevice */
	private $_propertySocketDevice = null;
	private $_propertySocketDeviceHost = null;
	private $_propertySocketDevicePort = null;
// ---------------------------------------------------------------------------------------
	public function __construct( $propertySocketDevice, $propertySocketDeviceHost, $propertySocketDevicePort = null ) {
		$this->_propertySocketDevice( $propertySocketDevice );
		$this->_propertySocketDeviceHost( $propertySocketDeviceHost );
		$this->_propertySocketDevicePort( $propertySocketDevicePort );
	}
// ---------------------------------------------------------------------------------------
	public function openSocketDevice() {
		return socket_connect(
			$this->_propertySocketDevice(),
			$this->_propertySocketDeviceHost(),
			$this->_propertySocketDevicePort()
		);
	}
	public function readSocketDevice( $propertyLength = null ) {
		$string_result = '';
		if( $propertyLength !== null ) {
			$string_result .= socket_read( $this->_propertySocketDevice(), (integer)$propertyLength );
		} else {
			while( ( $string_data = socket_read( $this->_propertySocketDevice(), 1024 ) ) ) {
				$string_result .= $string_data;
			}
		}
		return $string_result;
	}
	public function writeSocketDevice( $propertyData ) {
		return socket_write( $this->_propertySocketDevice(), $propertyData, strlen( $propertyData ) );
	}
	public function closeSocketDevice() {
		socket_close( $this->_propertySocketDevice() );
		unset( $this );
	}
// ---------------------------------------------------------------------------------------
	private function _propertySocketDevice( $propertySocketDevice = null ) {
		if( $propertySocketDevice !== null ) {
			$this->_propertySocketDevice = $propertySocketDevice;
		} return $this->_propertySocketDevice;
	}
	private function _propertySocketDeviceHost( $propertySocketDeviceHost = null ) {
		if( $propertySocketDeviceHost !== null ) {
			$this->_propertySocketDeviceHost = $propertySocketDeviceHost;
		} return $this->_propertySocketDeviceHost;
	}
	private function _propertySocketDevicePort( $propertySocketDevicePort = null ) {
		if( $propertySocketDevicePort !== null ) {
			$this->_propertySocketDevicePort = $propertySocketDevicePort;
		} return $this->_propertySocketDevicePort;
	}
}
?>