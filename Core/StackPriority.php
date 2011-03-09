<?php
/**
 * StackPriority
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
 * @package AioSystem\Core
 * @subpackage Stack
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Stack
 */
interface InterfaceStackPriority {
	public static function Instance( $Callback );
// ---------------------------------------------------------------------------------------
	public function pushData( $propertyPriorityData );
	public function popData();
	public function peekData();
	public function getData( $propertyPriorityIndex = 0 );
	public function listData();
}
/**
 * @package AioSystem\Core
 * @subpackage Stack
 */
class ClassStackPriority implements InterfaceStackPriority {
	private $_propertyStackCallback;
	private $_propertyStackPriority = array();
// ---------------------------------------------------------------------------------------
	/**
	 * @param function|string $Callback
	 * @return ClassStackPriority
	 */
	public static function Instance( $Callback ) {
		return new ClassStackPriority( $Callback );
	}
	/**
	 * @param function|string $Callback
	 */
	function __construct( $Callback ) {
		$this->_propertyStackCallback = $Callback;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param mixed $propertyPriorityData
	 * @return int
	 */
	public function pushData( $propertyPriorityData ) {
		array_push( $this->_propertyStackPriority, $propertyPriorityData );
		$this->sortData();
		return ( count( $this->_propertyStackPriority ) - 1 );
	}
	/**
	 * @return mixed
	 */
	public function popData() {
		return array_shift( $this->_propertyStackPriority );
	}
	/**
	 * @param null|int $propertyPriorityIndex
	 * @return null|mixed
	 */
	public function getData( $propertyPriorityIndex = null ) {
		if( ! isset( $this->_propertyStackPriority[$propertyPriorityIndex] ) ) return null;
		return $this->_propertyStackPriority[$propertyPriorityIndex];
	}
	/**
	 * @param null|int
	 * @return void
	 */
	public function removeData( $propertyPriorityIndex = null ) {
		if( isset( $this->_propertyStackPriority[$propertyPriorityIndex] ) ) {
			unset( $this->_propertyStackPriority[$propertyPriorityIndex] );
			$this->sortData();
		}
	}
	/**
	 * @return void
	 */
	public function sortData() {
		usort( $this->_propertyStackPriority, $this->_propertyStackCallback );
	}
	/**
	 * @return null|mixed
	 */
	public function peekData() {
		if( ! isset( $this->_propertyStackPriority[0] ) ) return null;
		return $this->_propertyStackPriority[0];
	}
	/**
	 * @return array
	 */
	public function listData() {
		return $this->_propertyStackPriority;
	}
}
?>