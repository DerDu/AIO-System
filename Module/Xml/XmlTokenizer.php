<?php
namespace AIOSystem\Module\Xml;

class XmlTokenizer {
	private $Content = '';
	private $PatternTokenizer = '!(?<=<)[^\?<>]*?(?=>)!is';
	private $PatternComment = '!(?<=<\!--).*?(?=//-->)!is';
	private $PatternCDATA = '!(?<=<\!\[CDATA\[).*?(?=\]\]>)!is';
	private $Result = array();

	public static function Instance( $Content ) {
		$Tokenizer = new XmlTokenizer();
		$Tokenizer->Content = $Content;
		$Tokenizer->Tokenize();
		return $Tokenizer;
	}

	private function Tokenize() {
		$this->EncodeComment();
		$this->EncodeCDATA();
		preg_match_all( $this->PatternTokenizer, $this->Content, $this->Result, PREG_OFFSET_CAPTURE );
		$this->Result = array_map( array( __NAMESPACE__.'\XmlToken', 'Instance' ), $this->Result[0] );
	}
	public function Result() {
		return $this->Result;
	}
	public function Content() {
		return $this->Content;
	}
	private function EncodeComment() {
		$this->Content = preg_replace_callback(
			$this->PatternComment,
			array( $this, 'EncodeBase64' ),
			$this->Content
		);
	}
	private function EncodeCDATA() {
		$this->Content = preg_replace_callback(
			$this->PatternCDATA,
			array( $this, 'EncodeBase64' ),
			$this->Content
		);
	}
	private function EncodeBase64( $Content ) {
		return base64_encode( $Content[0] );
	}
}
