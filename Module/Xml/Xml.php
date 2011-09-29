<?php
namespace AIOSystem\Module\Xml;

class Xml {

	public static function Parse( $Content ) {
		return XmlParser::Instance( XmlTokenizer::Instance( $Content ) )->Result();
	}
}
