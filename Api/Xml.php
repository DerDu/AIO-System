<?php
/**
 * This file contains the API:Xml
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
use \AIOSystem\Core\ClassXmlParser as AIOXmlParser;
use \AIOSystem\Core\ClassXmlNode as AIOXmlNode;
/**
 * Xml
 *
 * @package AIOSystem\Api
 */
class Xml {
	/**
	 * Parse XML-Content
	 *
	 * Can be used to read XML code into Object
	 *
	 * @static
	 * @param string|Core\ClassXmlNode $Source XML to parse or Node-Instance to pass through
	 * @return \AIOSystem\Core\ClassXmlNode
	 */
	public static function Parser( $Source ) {
		return AIOXmlParser::parseXml( $Source );
	}
	/**
	 * Create XML-Node Instance
	 *
	 * @static
	 * @return \AIOSystem\Core\ClassXmlNode
	 */
	public static function Create() {
		return AIOXmlNode::Instance();
	}

	public static function Name( \AIOSystem\Core\ClassXmlNode &$Node, $Name = null ) {
		if( $Name !== null ) {
			$Node->propertyName( $Name );
		} return $Node->propertyName();
	}
	public static function Attribute( \AIOSystem\Core\ClassXmlNode &$Node, $Name, $Value = null ) {
		if( $Name === null ) {
			if( $Value !== null ) {
				$Node->propertyAttributeList( $Value );
			} return $Node->propertyAttributeList();
		} else {
			if( $Value !== null ) {
				$Node->propertyAttribute( $Name, $Value );
			} return $Node->propertyAttribute( $Name );
		}
	}
	public static function Content( \AIOSystem\Core\ClassXmlNode &$Node, $Content = null ) {
		if( $Content !== null ) {
			$Node->propertyContent( $Content );
		} return $Node->propertyContent();
	}

	public static function Search( \AIOSystem\Core\ClassXmlNode &$In, $Name, $AttributeList = array(), $Index = null, $isSearchRoot = false ) {
		return $In->searchXmlNode( $Name, $AttributeList, $Index, $isSearchRoot );
	}
	public static function Remove( \AIOSystem\Core\ClassXmlNode &$From, $Name, $AttributeList = array(), $Index = null, $isSearchRoot = false ) {
		return $From->removeXmlNode( $Name, $AttributeList, $Index, $isSearchRoot );
	}
	public static function Parent( \AIOSystem\Core\ClassXmlNode &$Node, \AIOSystem\Core\ClassXmlNode $Parent = null ) {
		if( $Parent !== null ) {
			$Node->propertyParent( $Parent );
		} return $Node->propertyParent();
	}

	public static function ListChild( \AIOSystem\Core\ClassXmlNode &$From, $Name, $AttributeList = array(), $isDescendant = null ) {
		return $From->groupXmlNode( $Name, $AttributeList, $isDescendant );
	}
	public static function AddChild( \AIOSystem\Core\ClassXmlNode &$ParentNode, \AIOSystem\Core\ClassXmlNode &$ChildNode ) {
		return $ParentNode->appendXmlNode( $ChildNode );
	}

	public static function Code( \AIOSystem\Core\ClassXmlNode &$Node ) {
		return $Node->codeXmlNode();
	}
}
?>
