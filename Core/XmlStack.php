<?php
/**
 * ClassXmlStack - File
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
 * @subpackage Xml
 */
namespace AIOSystem\Core;
/**
 * ClassXmlStack - Interface
 *
 * @package AIOSystem\Core
 * @subpackage Xml
 */
interface InterfaceXmlStack
{
	public static function pushXmlNode( ClassXmlNode $XmlNode );
	public static function peekXmlNode();
	public static function popXmlNode();
	public static function listXmlNode();
}
/**
 * ClassXmlStack - Class
 *
 * @package AIOSystem\Core
 * @subpackage Xml
 */
class ClassXmlStack implements InterfaceXmlStack {
	/** @var ClassXmlNode[] $propertyStack */
	private static $propertyStack = array();
// ---------------------------------------------------------------------------------------
	/**
	 * @param ClassXmlNode $XmlNode
	 * @return ClassXmlNode
	 */
	public static function pushXmlNode( ClassXmlNode $XmlNode ) {
		array_push( self::$propertyStack, $XmlNode );
		return self::peekXmlNode();
	}
	/**
	 * @return ClassXmlNode
	 */
	public static function peekXmlNode() {
		return end( self::$propertyStack );
	}
	/**
	 * @return ClassXmlNode
	 */
	public static function popXmlNode() {
		return array_pop( self::$propertyStack );
	}
	/**
	 * @static
	 * @return array
	 */
	public static function listXmlNode() {
		return self::$propertyStack;
	}
}
?>