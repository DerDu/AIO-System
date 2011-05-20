<?php
/**
 * Serializer
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
 * Based on: http://efiquest.org/2007-12-10/6/
 *
 * @package AIOSystem\Module
 * @subpackage Cache
 */
namespace AIOSystem\Module\Cache;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
interface InterfaceSerializer {
	public static function Instance( $Object );
	public function Load();
}
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
class Serializer implements InterfaceSerializer {
	const DEBUG = false;

	private $Object;
	private $Serialized;
	private $ClassList = array();

	public static function Instance( $Object ) {
		return new Serializer( $Object );
	}
	public function Load() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( $this->Serialized ) {
			$this->_loadClassDefinition();
			$this->Object = unserialize( $this->Serialized );
			unset( $this->Serialized );
		}
		return $this->Object;
	}

	private function _loadClassDefinition() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		foreach( $this->ClassList as $Location ) {
			require_once( $Location );
		}
	}
	private function _buildLocationList( $Serialized ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$ClassList = $this->_buildClassList( $Serialized );
		$this->ClassList = array();
		foreach( $ClassList as $ClassName ) {
			$ReflectClass = new \ReflectionClass( $ClassName );
			$this->ClassList[] = $ReflectClass->getFileName();
		}
	}
	private function _buildClassList( $Serialized ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$MatchedClasses = array();
		if( preg_match_all('~([||;]O|^O):d+:"([^"]+)":d+:{~', $Serialized, $MatchedClasses ) ) {
			return array_unique( $MatchedClasses[2] );
		} else {
			return array();
		}
	}

	private function __construct( $Object) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$this->Object = $Object;
	}
	public function __sleep() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$this->Serialized = serialize( $this->Object );
		$this->_buildLocationList( $this->Serialized );
		return array('Serialized', 'ClassList');
	}
}
