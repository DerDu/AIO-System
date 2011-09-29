<?php
namespace AIOSystem\Module\Xml;

class XmlToken {
	const TOKEN_TYPE_OPEN = 1;
	const TOKEN_TYPE_CLOSE = 2;
	const TOKEN_TYPE_SHORT = 3;
	const TOKEN_TYPE_CDATA = 4;
	const TOKEN_TYPE_COMMENT = 5;

	private $TokenName = '';
	private $TokenAttributes = array();
	private $TokenPosition = 0;
	private $TokenType = 0;

	private $PatternTagOpen = '!^[^\!/].*?[^/]$!is';
	private $PatternTagClose = '!^/.*?!is';
	private $PatternTagShort = '!^[^\!].*?/$!is';
	private $PatternTagCDATA = '!^\!\[CDATA.*?\]\]$!is';
	private $PatternTagComment = '!^\![^\[].*?--$!is';

	public static function Instance( &$Content ) {
		$Token = new XmlToken();
		$Token->Read( $Content );
		return $Token;
	}
	private function Read( &$Content ) {
		$this->TokenPosition = $Content[1];
		$Token = explode(' ', $Content[0] );
		$this->TokenName = preg_replace( '!/$!is', '', array_shift( $Token ) );
		$Attribute = array();
		while( null !== ( $AttributeString = array_pop( $Token ) ) ) {
			if( $AttributeString != '/' ) {
				preg_match( '!(.*?)="(.*?)(?=")!is', $AttributeString, $Attribute );
				if( count( $Attribute ) == 3 ) {
					$this->TokenAttributes[$Attribute[1]] = $Attribute[2];
				}
			}
		}
		$this->ReadType( $Content[0] );
	}
	private function ReadType( &$Content ) {
		if( preg_match( $this->PatternTagOpen, $Content ) ) {
			$this->TokenType = self::TOKEN_TYPE_OPEN;
		} else
		if( preg_match( $this->PatternTagClose, $Content ) ) {
			$this->TokenType = self::TOKEN_TYPE_CLOSE;
		} else
		if( preg_match( $this->PatternTagShort, $Content ) ) {
			$this->TokenType = self::TOKEN_TYPE_SHORT;
		} else
		if( preg_match( $this->PatternTagCDATA, $Content ) ) {
			$this->TokenType = self::TOKEN_TYPE_CDATA;
		} else
		if( preg_match( $this->PatternTagComment, $Content ) ) {
			$this->TokenType = self::TOKEN_TYPE_COMMENT;
		}
	}

	public function Name() {
		return $this->TokenName;
	}
	public function Position() {
		return $this->TokenPosition;
	}
	public function Attributes() {
		return $this->TokenAttributes;
	}

	public function isOpenTag() {
		return $this->TokenType == self::TOKEN_TYPE_OPEN;
	}
	public function isCloseTag() {
		return $this->TokenType == self::TOKEN_TYPE_CLOSE;
	}
	public function isShortTag() {
		return $this->TokenType == self::TOKEN_TYPE_SHORT;
	}
	public function isCDATATag() {
		return $this->TokenType == self::TOKEN_TYPE_CDATA;
	}
	public function isCommentTag() {
		return $this->TokenType == self::TOKEN_TYPE_COMMENT;
	}
}
