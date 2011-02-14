<?php
// ---------------------------------------------------------------------------------------
// InterfaceCoreXmlContent, ClassCoreXmlContent
// ---------------------------------------------------------------------------------------
interface InterfaceCoreXmlContent
{
	public function propertyName( $propertyName = null );
	public function propertyAttribute( $propertyAttributeName, $propertyAttributeValue = null );
	public function propertyContent( $propertyContent = null );
	public function propertyAttributeList( $propertyAttributeList = null );
	public function propertyChildList( $propertyChildList = null );
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
class ClassCoreXmlContent implements InterfaceCoreXmlContent
{
	private $propertyName = null;
	private $propertyAttributeList = array();
	private $propertyContent = null;
	private $propertyChildList = array();
// ---------------------------------------------------------------------------------------
	public function __construct( $propertyName = null, $propertyAttributeList = array(), $propertyContent = null, $propertyChildList = array() )
	{
		$this->propertyName( $propertyName );
		$this->propertyAttributeList( $propertyAttributeList );
		$this->propertyContent( $propertyContent );
		$this->propertyChildList( $propertyChildList );
	}
// ---------------------------------------------------------------------------------------
	public function propertyName( $propertyName = null ){
		if( $propertyName !== null ) $this->propertyName = $propertyName;
		return $this->propertyName;
	}
	public function propertyAttribute( $propertyAttributeName, $propertyAttributeValue = null )
	{
		$propertyAttributeList = $this->propertyAttributeList();
		if( $propertyAttributeValue !== null ){
			$propertyAttributeList[$propertyAttributeName] = $propertyAttributeValue;
			$propertyAttributeList = $this->propertyAttributeList( $propertyAttributeList );
		}
		if( array_key_exists( $propertyAttributeName, (array)$propertyAttributeList ) )
		return $propertyAttributeList[$propertyAttributeName];
		return null;
	}
	public function propertyContent( $propertyContent = null ){
		if( $propertyContent !== null ) {
			$this->propertyContent = $propertyContent;
		}
		return $this->propertyContent;
	}
// ---------------------------------------------------------------------------------------
	public function propertyAttributeList( $propertyAttributeList = null ){
		if( $propertyAttributeList !== null ) $this->propertyAttributeList = $propertyAttributeList;
		return $this->propertyAttributeList;
	}
	public function propertyChildList( $propertyChildList = null ){
		if( $propertyChildList !== null ) $this->propertyChildList = $propertyChildList;
		return $this->propertyChildList;
	}
}
?> :)