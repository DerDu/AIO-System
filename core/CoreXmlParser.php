<?php
// ---------------------------------------------------------------------------------------
//	InterfaceCoreXmlParser, ClassCoreXmlParser
// ---------------------------------------------------------------------------------------
interface InterfaceCoreXmlParser
{
	public static function parseXml( $string_file_name );
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
class ClassCoreXmlParser implements InterfaceCoreXmlParser
{
	public static function parseXml( $propertyXml )
	{
		// Try File
		if( is_string( $propertyXml ) && file_exists( $propertyXml ) ) {
			$propertyXmlContent = file_get_contents( $propertyXml );
		}
		// Try String
		elseif( is_string( $propertyXml ) && preg_match( '!\<\?xml.*?\?\>!is', $propertyXml ) ){
			$propertyXmlContent = $propertyXml;
		}
		// Try Object
		elseif( is_object( $propertyXml ) ){
			return $propertyXml;
		}
		// Default Error
		else return false;
		// RESPECT COMMENT <!-- .* //-->
		preg_replace( '!\<\!\-\-.*?\/\/\-\-\>!is', '', $propertyXmlContent );
		// RESPECT CDATA
		$propertyXmlContent = preg_replace_callback(
			'!(?<=\<\!\[CDATA\[).*?(?=\]\]\>)!is',
			create_function('$a','return base64_encode($a[0]);'),
			$propertyXmlContent
		)."\n";
		$isRoot = true;
		while( $propertyXmlContent !== false )
		{
			$_searchTag = self::_searchTag( $propertyXmlContent );
			// Handle Tag-Type
			switch( $_searchTag['TYPE'] ) {
				case "OPEN": {
					// Reset Timeout
					set_time_limit( 30 );
					self::_openTag( $_searchTag );
					break;
				}
				case "CLOSE": {
					if( ( $CoreXmlNodeList = self::_closeTag( $_searchTag ) ) !== null )
					return $CoreXmlNodeList;
					break;
				}
				case "SHORT": {
					self::_openTag( $_searchTag );
					// FIX: Root = Short
					if( $isRoot ) return self::_closeTag( $_searchTag );
					self::_closeTag( $_searchTag );
					break;
				}
				default: {
					throw new Exception('['.__METHOD__.'] XML-Document malformed!');
				}
			}
			$propertyXmlContent = substr( $propertyXmlContent, $_searchTag['POS#']+$_searchTag['LEN#'] );
			$isRoot = false;
		}
		return false;
	}
	private static function _closeTag( $_readTag )
	{
		// Done ?
		$CoreXmlNodeChild = ClassCoreXmlStack::popCoreXmlNode();
		$CoreXmlNodeChild->propertyContent( $_readTag['TEXT'] );
		$CoreXmlNodeParent = ClassCoreXmlStack::peekCoreXmlNode();
		if( ! is_object( $CoreXmlNodeParent ) ) return $CoreXmlNodeChild;
		// No, not yet..
		$CoreXmlNodeChild->propertyParent( $CoreXmlNodeParent );
		$CoreXmlNodeChildList = $CoreXmlNodeParent->propertyChildList();
		array_push( $CoreXmlNodeChildList, $CoreXmlNodeChild );
		$CoreXmlNodeParent->propertyChildList( $CoreXmlNodeChildList );
		return null;
	}
	private static function _openTag( $_readTag )
	{
		$_readTag = self::_readTag( $_readTag['ITEM'] );
		$CoreXmlNode = ClassCoreXmlStack::pushCoreXmlNode( new ClassCoreXmlNode() );
		$CoreXmlNode->propertyName( $_readTag['NAME'] );
		$CoreXmlNode->propertyAttributeList( $_readTag['ATTR'] );
	}
	private static function _readTag( $_readTag )
	{
		// Fetch Name
		preg_match( '![a-z0-9_\.\-]+?(?=( |$))!is', $_readTag, $_readTagName );
		// Fetch Attributes
		preg_match_all( '!(?<=( |"))[a-z0-9_:]+?(?=\=)!is', $_readTag, $_readTagAttributeName );
		preg_match_all( '!".*?"!is', $_readTag, $_readTagAttributeValue );
		// Process Attributes
		if( sizeof( $_readTagAttributeName[0] ) > 0 ) {
			array_walk( $_readTagAttributeValue[0], create_function('&$value','$value = html_entity_decode( substr( $value, 1, -1 ) );') );
			$_readTagAttribute = array_combine( $_readTagAttributeName[0], $_readTagAttributeValue[0] );
		} else $_readTagAttribute = array();
		return array( 'NAME' => $_readTagName[0], 'ATTR' => $_readTagAttribute );
	}
	private static function _searchTag( $propertyXmlContent )
	{
		$_searchTag = array( 'TEXT'=>NULL, 'TYPE'=>NULL );
		if( preg_match( '!(?<=\<)(\/|\w).+?(?=\>)!is', $propertyXmlContent, $_searchTagResult, PREG_OFFSET_CAPTURE ) )
		{
			if( strpos( $_searchTagResult[0][0], '/' ) === 0 ) {
				$_searchTag['ITEM'] = str_replace( '/','',$_searchTagResult[0][0] );
				$_searchTag['TYPE'] = 'CLOSE';
				$_searchTag['POS#'] = $_searchTagResult[0][1] - 1;
				$_searchTag['LEN#'] = strlen( $_searchTag['ITEM'] ) + 3;
			}
			elseif( substr( trim( $_searchTagResult[0][0] ), -1 ) === '/' ) {
				$_searchTag['ITEM'] = substr( trim( $_searchTagResult[0][0] ), 0, -1 );
				//str_replace( '/','',$array_search[0][0] );
				$_searchTag['TYPE'] = 'SHORT';
				$_searchTag['POS#'] = $_searchTagResult[0][1] - 1;
				$_searchTag['LEN#'] = strlen( $_searchTag['ITEM'] ) + 3;
			}
			else {
				$_searchTag['ITEM'] = $_searchTagResult[0][0];
				$_searchTag['TYPE'] = 'OPEN';
				$_searchTag['POS#'] = $_searchTagResult[0][1] - 1;
				$_searchTag['LEN#'] = strlen( $_searchTag['ITEM'] ) + 2;
			}
		}
		// Content
		if( $_searchTag['TYPE'] && $_searchTag['TYPE'] != 'SHORT' ) {
			$_searchTag['TEXT'] = trim( substr( $propertyXmlContent, 0, $_searchTag['POS#'] ) );
			// RESPECT CDATA
			preg_match_all( '!(?<=\<\!\[CDATA\[).*?(?=\]\]\>)!is', $_searchTag['TEXT'], $propertyContent );
			foreach( (array)$propertyContent[0] as $propertyContentLine ) {
				$_searchTag['TEXT'] = str_replace( '<![CDATA['.$propertyContentLine.']]>', base64_decode($propertyContentLine), $_searchTag['TEXT'] );
			}
		}
		if( strlen( $_searchTag['TEXT'] ) == 0 ) $_searchTag['TEXT'] = null;
		// -----------------------------------------
		return $_searchTag;
	}
}
?>