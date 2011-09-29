<?php
namespace AIOSystem\Module\Xml;

class XmlParser {
	/** @var null|XmlTokenizer $Tokenizer */
	private $Tokenizer = null;
	/** @var array $ParserStack */
	private $ParserStack = array();
	/** @var null|XmlNode $Result */
	private $Result = null;

	private $PatternComment = '!(?<=\!--).*?(?=//--)!is';
	private $PatternCDATA = '!(?<=\!\[CDATA\[).*?(?=\]\])!is';

	public static function Instance( XmlTokenizer &$Tokenizer ) {
		$Parser = new XmlParser();
		$Parser->ParserStack = array();
		$Parser->Tokenizer = $Tokenizer;
		$Parser->Parse();
		return $Parser;
	}

	private function Parse() {
		/** @var XmlToken $Token */
		foreach( (array)$this->Tokenizer->Result() as $Token ) {
			// Convert Token to Node
			$Node = XmlNode::Instance( $Token );
			// Handle Token by Type
			if( $Token->isOpenTag() ) {
				// Set Parent Type to Structure
				if( !empty( $this->ParserStack ) ) {
					$Parent = array_pop( $this->ParserStack );
					$Parent->Type( $Parent::XML_NODE_TYPE_STRUCTURE );
					array_push( $this->ParserStack, $Parent );
				}
				// Add Node to Stack
				array_push( $this->ParserStack, $Node );
			} elseif( $Token->isCloseTag() ) {
				// Get Parent (OpenTag)
				/** @var XmlNode $Parent */
				$Parent = array_pop( $this->ParserStack );
				// Handle Close by Type
				switch( $Parent->Type() ) {
					case $Parent::XML_NODE_TYPE_CONTENT : {
						// Get Content
						$LengthName = strlen( $Parent->Name() ) +1;
						$LengthAttribute = strlen( $Parent->GetAttributeString() ) +1;
						$LengthAttribute = ( $LengthAttribute == 1 ? 0 : $LengthAttribute );
						$Parent->Content(
							substr(
								$this->Tokenizer->Content(),

								$Parent->Position()
									+ $LengthName
									+ $LengthAttribute,

								( $Token->Position() - $Parent->Position() )
									- ( $LengthName +1 )
									- ( $LengthAttribute )
							)
						);
						// Do Parent Close
						$Ancestor = array_pop( $this->ParserStack );
						$Ancestor->AddChild( $Parent );
						array_push( $this->ParserStack, $Ancestor );
						break;
					}
					case $Parent::XML_NODE_TYPE_STRUCTURE : {
						// Set Ancestor <-> Parent Relation
						/** @var XmlNode $Ancestor */
						$Ancestor = array_pop( $this->ParserStack );
						if( is_object( $Ancestor ) ) {
							// Do Parent Close
							$Ancestor->AddChild( $Parent );
							array_push( $this->ParserStack, $Ancestor );
						} else {
							// No Ancestor -> Parent = Root
							array_push( $this->ParserStack, $Parent );
						}
						break;
					}
					case $Parent::XML_NODE_TYPE_CDATA : {
						// Set Ancestor <-> Parent Relation
						/** @var XmlNode $Ancestor */
						$Ancestor = array_pop( $this->ParserStack );
						// Do Parent Close
						$Ancestor->AddChild( $Parent );
						array_push( $this->ParserStack, $Ancestor );
						break;
					}
				}
			} elseif( $Token->isShortTag() ) {
				// Set Ancestor <-> Node Relation
				/** @var XmlNode $Parent */
				$Ancestor = array_pop( $this->ParserStack );
				$Ancestor->Type( $Parent::XML_NODE_TYPE_STRUCTURE );
				// Do Node Close
				$Ancestor->AddChild( $Node );
				array_push( $this->ParserStack, $Ancestor );
			} elseif( $Token->isCDATATag() ) {
				// Set Parent Type/Content
				/** @var XmlNode $Parent */
				$Parent = array_pop( $this->ParserStack );
				$Parent->Type( $Parent::XML_NODE_TYPE_CDATA );
				$Parent->Content( $Node->Name() );
				$this->DecodeCDATA( $Parent );
				// Do Node Close
				array_push( $this->ParserStack, $Parent );
			} elseif( $Token->isCommentTag() ) {
				// Set Parent Type/Content
				/** @var XmlNode $Parent */
				$Parent = array_pop( $this->ParserStack );
				$Node->Type( $Node::XML_NODE_TYPE_COMMENT );
				$Node->Content( $Node->Name() );
				$Node->Name( '__COMMENT__' );
				$this->DecodeComment( $Node );
				// Do Node Close
				$Parent->AddChild( $Node );
				array_push( $this->ParserStack, $Parent );
			}
		}
		// Set parsed Stack as Result
		$this->Result = array_pop( $this->ParserStack );
	}

	/**
	 * @return XmlNode|null
	 */
	public function Result() {
		return $this->Result;
	}

	private function DecodeCDATA( XmlNode &$Node ) {
		$Match = array();
		preg_match( $this->PatternCDATA, $Node->Content(), $Match );
		$Node->Content( $this->DecodeBase64( $Match[0] ) );
	}
	private function DecodeComment( XmlNode &$Node ) {
		$Match = array();
		preg_match( $this->PatternComment, $Node->Content(), $Match );
		$Node->Content( trim( $this->DecodeBase64( $Match[0] ) ) );
	}
	private function DecodeBase64( $Content ) {
		return base64_decode( $Content );
	}
}
