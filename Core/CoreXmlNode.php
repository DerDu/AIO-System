<?php
// ---------------------------------------------------------------------------------------
// InterfaceCoreXmlNode, ClassCoreXmlNode
// ---------------------------------------------------------------------------------------
interface InterfaceCoreXmlNode{
	public static function Instance();
// ---------------------------------------------------------------------------------------
	public function propertyParent( ClassCoreXmlNode $propertyParent = null );
// ---------------------------------------------------------------------------------------
	public function appendCoreXmlNode( ClassCoreXmlNode $CoreXmlNode );
	public function removeCoreXmlNode( $propertyName, $propertyAttributeList = array(), $indexCoreXmlNode = null, $isSearchRoot = false );
	public function groupCoreXmlNode( $propertyName, $propertyAttributeList = array(), $isDescendant = null );
	public function searchCoreXmlNode( $propertyName, $propertyAttributeList = array(), $indexCoreXmlNode = null, $isSearchRoot = false );
// ---------------------------------------------------------------------------------------
	public function codeCoreXmlNode( $isSubStructure = false, $indentLevel = 0 );
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
class ClassCoreXmlNode extends ClassCoreXmlContent implements InterfaceCoreXmlNode
{
	private $_propertyParent = null;
	private $_propertyHashCount = array();
// ---------------------------------------------------------------------------------------
	public static function Instance(){
		return new ClassCoreXmlNode();
	}
// ---------------------------------------------------------------------------------------
	public function propertyParent( ClassCoreXmlNode $propertyParent = null ){
		if( $propertyParent !== null ) $this->_propertyParent = $propertyParent;
		return $this->_propertyParent;
	}
// ---------------------------------------------------------------------------------------
	public function appendCoreXmlNode( ClassCoreXmlNode $CoreXmlNode ){
		$CoreXmlNode->propertyParent( $this );
		$propertyChildList = (array)$this->propertyChildList();
		$propertyChildList[] = $CoreXmlNode;
		$this->propertyChildList( $propertyChildList );
		// REBUILD OBJECT-CHAIN
		return $CoreXmlNode;
	}
	public function removeCoreXmlNode( $propertyName, $propertyAttributeList = array(), $indexCoreXmlNode = null, $isSearchRoot = false ){
		if( ( $CoreXmlNode = $this->searchCoreXmlNode( $propertyName, $propertyAttributeList, $indexCoreXmlNode, $isSearchRoot ) ) != false ) {
			$CoreXmlNodeChildList = $CoreXmlNode->propertyParent()->propertyChildList();
			foreach( (array)$CoreXmlNodeChildList as $indexCoreXmlNodeChild => $CoreXmlNodeChild ) {
				if( $CoreXmlNodeChild === $CoreXmlNode ) {
					$CoreXmlNodeChild->__destruct();
					unset( $CoreXmlNodeChildList[$indexCoreXmlNodeChild] );
				}
			}
			$CoreXmlNode->propertyParent()->propertyChildList( $CoreXmlNodeChildList );
			return $CoreXmlNode->propertyParent();
		} return false;
	}
	public function groupCoreXmlNode( $propertyName, $propertyAttributeList = array(), $isDescendant = null ){
		$groupCoreXmlNode = array();
		// FIX: Respect Depth-Level for Group -> 1st Check Childs, 2nd Search Node4Root
		$CoreXmlNodeChildList = $this->propertyChildList();
		foreach( (array)$CoreXmlNodeChildList as $CoreXmlNodeChild ){
			if( ( $CoreXmlNode = $CoreXmlNodeChild->searchCoreXmlNode( $propertyName, $propertyAttributeList, null, true ) ) !== false )
			$groupCoreXmlNode[] = $CoreXmlNode;
		}
		if( !empty($groupCoreXmlNode) || $isDescendant === true ) return $groupCoreXmlNode;
		// Search Node4Root
		$rootCoreXmlNode = $this->searchCoreXmlNode( $propertyName, $propertyAttributeList );
		if( !is_object( $rootCoreXmlNode ) ) return array();

		$CoreXmlNodeChildList = $rootCoreXmlNode->propertyParent()->propertyChildList();
		foreach( (array)$CoreXmlNodeChildList as $CoreXmlNodeChild ){
			if( ( $CoreXmlNode = $CoreXmlNodeChild->searchCoreXmlNode( $propertyName, $propertyAttributeList, null, true ) ) !== false )
			$groupCoreXmlNode[] = $CoreXmlNode;
		}
		return $groupCoreXmlNode;
	}
	public function searchCoreXmlNode( $propertyName, $propertyAttributeList = array(), $indexCoreXmlNode = null, $isSearchRoot = false ){
		$isSearchMatch = true;
		// CHECK NODE NAME
		if( $isSearchMatch == true
		&& $this->propertyName() != $propertyName )
			$isSearchMatch = false;
		// CHECK NODE ATTRIBUTES
		if( $isSearchMatch == true
		&& array_intersect_assoc( $propertyAttributeList, $this->propertyAttributeList() ) != $propertyAttributeList )
			$isSearchMatch = false;
		// CHECK NODE INDEX (LAST STEP!!)
		// TODO: [Fix] Index not working
		if( $isSearchMatch == true && $indexCoreXmlNode !== null ){
			$hashCoreXmlNode = sha1(serialize($propertyName).serialize($propertyAttributeList).serialize($indexCoreXmlNode));
			if( !isset( $this->_propertyHashCount[$hashCoreXmlNode] ) ){
				$this->_propertyHashCount[$hashCoreXmlNode] = 0;
			} else {
				$this->_propertyHashCount[$hashCoreXmlNode]++;
			}
			if( $this->_propertyHashCount[$hashCoreXmlNode] != $indexCoreXmlNode ){
				$isSearchMatch = false;
			}
		}
		// RETURN NODE
		if( $isSearchMatch == true ){
			return $this;
		}
		if( $isSearchRoot == true ){
			return false;
		}
		// TRY CHILDS
		$CoreXmlNodeChildList = $this->propertyChildList();
		foreach( (array)$CoreXmlNodeChildList as $CoreXmlNodeChild ){
			if( ( $CoreXmlNode = $CoreXmlNodeChild->searchCoreXmlNode( $propertyName, $propertyAttributeList, $indexCoreXmlNode )) != false )
			return $CoreXmlNode;
		}
		// RETURN FALSE
		return false;
	}
	public function codeCoreXmlNode( $isSubStructure = false, $indentLevel = 0 ){
		// BUILD ATTRIBUTES STRING
		$propertyAttributeList = $this->propertyAttributeList();
		$propertyAttributeString = ' ';
		foreach( (array)$propertyAttributeList as $propertyAttributeName => $propertyAttributeValue ) {
			if( strpos( (string)$propertyAttributeValue, '"' ) === false  )
				$propertyAttributeString .= $propertyAttributeName.'="'.$propertyAttributeValue.'" ';
			else
			if( strpos( (string)$propertyAttributeValue, "'" ) === false  )
				$propertyAttributeString .= $propertyAttributeName."='".$propertyAttributeValue."' ";
			else
			// ERROR
			throw new Exception(
				'Structure malformed!<br />XML-Attribute-Name: '.$propertyAttributeName.'<br />XML-Attribute-Value: '.$propertyAttributeValue
			);
		}
		// BUILD STRUCTURE STRING
		$codeCoreXmlNode = ''
			.( !$isSubStructure?'<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n":"\n" )
			.str_repeat( "\t", $indentLevel ).trim( '<'.$this->propertyName().' '.trim($propertyAttributeString) );
		// FIX === NULL NOT CORRECT => STRLEN == 0
		//if( $this->xml_node_content() === null && count( $this->xml_node_list_child() ) == 0 )
		if( strlen( $this->propertyContent() ) == 0 && count( $this->propertyChildList() ) == 0 ) {
			$codeCoreXmlNode .= ' />';
		}
		// FIX !== NULL NOT CORRECT => STRLEN != 0
		//else if( $this->xml_node_content() !== null )
		else if( strlen( $this->propertyContent() ) != 0 ) {
			$codeCoreXmlNode .= '>'.$this->propertyContent().'</'.$this->propertyName().'>';
		}
		else if( count( $this->propertyChildList() ) != 0 ) {
			$codeCoreXmlNode .= '>';
			$CoreXmlNodeChildList = $this->propertyChildList();
			foreach( $CoreXmlNodeChildList as $CoreXmlNodeChild ) {
				$codeCoreXmlNode .= $CoreXmlNodeChild->codeCoreXmlNode(true, $indentLevel + 1 );
			}
			$codeCoreXmlNode .= "\n".str_repeat( "\t", $indentLevel ).'</'.$this->propertyName().'>';
		}
		// RETURN STRUCTURE
		return $codeCoreXmlNode;
	}
// ---------------------------------------------------------------------------------------
	function __destruct(){
		unset( $this->_propertyParent );
		$propertyChildList = (array)$this->propertyChildList();
		foreach( $propertyChildList as $CoreXmlNode ){
			$CoreXmlNode->__destruct();
		}
		$this->propertyChildList(false);
	}
}
?>