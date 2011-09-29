<?php
namespace AIOSystem\Module\Xml;

class XmlNode {
	const XML_NODE_TYPE_STRUCTURE = 1;
	const XML_NODE_TYPE_CONTENT = 2;
	const XML_NODE_TYPE_CDATA = 3;
	const XML_NODE_TYPE_COMMENT = 4;

	private $NodeType = XmlNode::XML_NODE_TYPE_CONTENT;

	private $NodeName = null;
	private $NodeContent = null;
	private $NodeAttributeList = array();

	private $NodePosition = null;
	private $NodeParent = null;
	private $NodeChildList = array();

	/**
	 * @static
	 * @param XmlToken|string $NameOrToken
	 * @return XmlNode
	 */
	public static function Instance( &$NameOrToken ) {
		$Node = new XmlNode();
		if( is_object( $NameOrToken ) && $NameOrToken instanceof XmlToken ) {
			$Node->NodeName = $NameOrToken->Name();
			$Node->NodeAttributeList = $NameOrToken->Attributes();
			$Node->NodePosition = $NameOrToken->Position();
			unset( $NameOrToken );
		} else {
			$Node->NodeName = $NameOrToken;
		}
		return $Node;
	}

	public function Name( $Value = null ) {
		if( $Value !== null ) {
			$this->NodeName = $Value;
		} return $this->NodeName;
	}
	public function Content( $Value = null ) {
		if( $Value !== null ) {
			if( preg_match( '![<>&]!is', $Value ) ) {
				$this->NodeType = $this::XML_NODE_TYPE_CDATA;
			}
			$this->NodeContent = $Value;
		} return $this->NodeContent;
	}

	public function Parent( XmlNode $Value = null ) {
		if( $Value !== null ) {
			$this->NodeParent = $Value;
		} return $this->NodeParent;
	}

	public function AddChild( XmlNode $Value ) {
		$Value->Parent( $this );
		array_push( $this->NodeChildList, $Value );
	}
	public function GetChildList() {
		return $this->NodeChildList;
	}

	public function SetAttribute( $Name, $Value = null ) {
		if( $Value === null ) {
			unset( $this->NodeAttributeList[$Name] );
		} else {
			$this->NodeAttributeList[$Name] = $Value;
		}
	}
	public function GetAttribute( $Name ) {
		if( isset( $this->NodeAttributeList[$Name] ) ) {
			return $this->NodeAttributeList[$Name];
		} else {
			return null;
		}
	}
	public function GetAttributeList() {
		return $this->NodeAttributeList;
	}
	public function GetAttributeString() {
		$AttributeList = $this->NodeAttributeList;
		array_walk( $AttributeList, create_function( '&$Value,$Key', '$Value = $Key.\'="\'.$Value.\'"\';' ) );
		return implode(' ',$AttributeList);
	}

	public function Search( $Name, $AttributeList = null, $Index = null, $Recursive = true ) {
		/** @var XmlNode $Node */
		foreach( $this->NodeChildList as $Node ) {
			if( $Node->Name() == $Name ) {
				if( $AttributeList === null && $Index === null ) {
					return $Node;
				} else if( $Index === null ) {
					if( $Node->GetAttributeList() == $AttributeList ) {
						return $Node;
					}
				} else if( $AttributeList === null ) {
					if( $Index === 0 ) {
						return $Node;
					} else {
						$Index--;
					}
				} else {
					if( $Node->GetAttributeList() == $AttributeList && $Index === 0 ) {
						return $Node;
					} else if( $Node->GetAttributeList() == $AttributeList ) {
						$Index--;
					}
				}
			}
			if( true === $Recursive && !empty( $Node->NodeChildList ) ) {
				if( false !== ( $Result = $Node->Search( $Name, $AttributeList, $Index, $Recursive ) ) ) {
					if( !is_object( $Result ) ) {
						$Index = $Result;
					} else {
						return $Result;
					}
				}
			}
		}
		if( $Index !== null ) {
			return $Index;
		} else {
			return false;
		}
	}
	public function Like( $Name, $AttributeList = null, $Index = null, $Recursive = true ) {
		/** @var XmlNode $Node */
		foreach( $this->NodeChildList as $Node ) {
			if( preg_match( $Name, $Node->Name() ) ) {
				if( $AttributeList === null && $Index === null ) {
					return $Node;
				} else if( $Index === null ) {
					if( $Node->GetAttributeList() == $AttributeList ) {
						return $Node;
					}
				} else if( $AttributeList === null ) {
					if( $Index === 0 ) {
						return $Node;
					} else {
						$Index--;
					}
				} else {
					if( $Node->GetAttributeList() == $AttributeList && $Index === 0 ) {
						return $Node;
					} else if( $Node->GetAttributeList() == $AttributeList ) {
						$Index--;
					}
				}
			}
			if( true === $Recursive && !empty( $Node->NodeChildList ) ) {
				if( false !== ( $Result = $Node->Like( $Name, $AttributeList, $Index, $Recursive ) ) ) {
					if( !is_object( $Result ) ) {
						$Index = $Result;
					} else {
						return $Result;
					}
				}
			}
		}
		if( $Index !== null ) {
			return $Index;
		} else {
			return false;
		}
	}

	public function Code() {
		$FuncArgs = func_get_args();
		if( empty( $FuncArgs) ) {
			$FuncArgs[0] = false;
			$FuncArgs[1] = 0;
		}
		// BUILD STRUCTURE STRING
		$Result = ''
			.( !$FuncArgs[0]?'<?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n":"\n" )
			.str_repeat( "\t", $FuncArgs[1] );
		if( $this->NodeType == $this::XML_NODE_TYPE_COMMENT ) {
			$Result .= '<!-- '.$this->NodeContent.' //-->';
		} else {
			$Result .= '<'.trim($this->NodeName.' '.$this->GetAttributeString());
		}
		if( $this->NodeContent === null && empty( $this->NodeChildList ) ) {
			$Result .= ' />';
		}
		else if( $this->NodeType == $this::XML_NODE_TYPE_CONTENT ) {
			$Result .= '>'.$this->NodeContent.'</'.$this->NodeName.'>';
		}
		else if( $this->NodeType == $this::XML_NODE_TYPE_CDATA ) {
			$Result .= '><![CDATA['.$this->NodeContent.']]></'.$this->NodeName.'>';
		}
		else if( $this->NodeType == $this::XML_NODE_TYPE_STRUCTURE ) {
			$Result .= '>';
			/** @var XmlNode $Node */
			foreach( $this->NodeChildList as $Node ) {
				$Result .= $Node->Code(true, $FuncArgs[1] + 1 );
			}
			$Result .= "\n".str_repeat( "\t", $FuncArgs[1] ).'</'.$this->NodeName.'>';
		}
		// RETURN STRUCTURE
		return $Result;
	}

	public function Position() {
		return $this->NodePosition;
	}
	public function Type( $XML_NODE_TYPE = null ) {
		if( null !== $XML_NODE_TYPE ) {
			$this->NodeType = $XML_NODE_TYPE;
		} return $this->NodeType;
	}

	public function __destruct() {
		/** @var XmlNode $Node */
		unset( $this->NodeParent );
		foreach( (array)$this->NodeChildList as $Node ) {
			$Node->__destruct();
		}
		unset( $this );
	}
}
