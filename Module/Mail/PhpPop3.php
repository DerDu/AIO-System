<?php
/**
 * PhpPop3
 *
 * Based on http://de3.php.net/manual/de/book.imap.php#96414
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
 * @package AIOSystem\Module
 * @subpackage Mail
 */
namespace AIOSystem\Module\Mail;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Cache;
/**
 * @package AIOSystem\Module
 * @subpackage Mail
 */
interface InterfacePhpPop3 {
	public static function phppop3_open( $string_hostname, $string_username, $string_password, $string_pop3folder = 'INBOX', $bool_pop3ssl = false, $integer_pop3port = 110);
	public static function phppop3_close();

	public static function phppop3_count();
	public static function phppop3_search( $array_search );
	public static function phppop3_remove( $array_message_number );

	public static function phppop3_header( $integer_message_number );
	public static function phppop3_read( $integer_message_number, $bool_parse_header = false );
	public static function phppop3_attachment( $integer_message_number );
}
/**
 * @package AIOSystem\Module
 * @subpackage Mail
 */
class ClassPhpPop3 implements InterfacePhpPop3
{
	/** @var \resource $phppop3_connection */
	private static $phppop3_connection = null;

	public static function phppop3_open( $string_hostname, $string_username, $string_password, $string_pop3folder = 'INBOX', $bool_pop3ssl = false, $integer_pop3port = 110) {
		$bool_pop3ssl = ( $bool_pop3ssl == false )?'/novalidate-cert':'';
	    self::phppop3_connection( imap_open( '{'.$string_hostname.':'.$integer_pop3port.'/pop3'.$bool_pop3ssl.'}'.$string_pop3folder, $string_username, $string_password ) );
		return self::phppop3_connection();
	}
	public static function phppop3_close() {
		imap_expunge( self::phppop3_connection() );
		imap_close( self::phppop3_connection() );
		self::$phppop3_connection = null;
	}

	public static function phppop3_count() {
		return imap_num_msg( self::phppop3_connection() );
	}
	public static function phppop3_search( $array_search ) {
		$array_allowed = array( 'BODY', 'FROM', 'SUBJECT' );
		$array_search = array_intersect_key( $array_search, array_flip($array_allowed) );
		$string_search = '';
		foreach( (array)$array_search as $string_criteria => $mixed_value ) {
			$string_search = $string_criteria.' '.$mixed_value.' ';
		}
		return imap_search ( self::phppop3_connection() , trim($string_search) );
	}
	public static function phppop3_remove( $array_message_number ) {
		$array_message_number = (array)$array_message_number;
		foreach( (array)$array_message_number as $integer_message_index => $integer_message_number ) {
			imap_delete( self::phppop3_connection(), $integer_message_number );
		}
	}
	public static function phppop3_header( $integer_message_number ) {
		return ( imap_fetchheader( self::phppop3_connection(), $integer_message_number, FT_PREFETCHTEXT ) );
	}
	public static function phppop3_read( $integer_message_number, $bool_parse_header = false ) {
		$object_mail = imap_fetchstructure( self::phppop3_connection(), $integer_message_number );
		$object_mail = self::phppop3_part_get( $integer_message_number, $object_mail, 0 );
		if($bool_parse_header) $object_mail[0]["parsed"] = self::phppop3_header_decode( $object_mail[0]["data"] );
		return ( $object_mail );
	}
	public static function phppop3_attachment( $integer_message_number ) {
		$array_mail = self::phppop3_read( $integer_message_number, true );
		$array_attachment = array();
		foreach( (array)$array_mail as $integer_part => $array_part ){
			if( isset($array_part['is_attachment']) && $array_part['is_attachment'] == true ) {
				$ClassSystemFile = System::File(
					Cache::Location(__CLASS__).'ATTACHMENT.'.$integer_message_number.'.'.$integer_part.'-'.$array_part['filename']
				);
				$ClassSystemFile->propertyFileContent( $array_part['data'] );
				$ClassSystemFile->writeFile();
				array_push( $array_attachment, $ClassSystemFile );
			}
		}
		return $array_attachment;
	}
// ---------------------------------------------------------------------------------------
	private static function phppop3_part_get( $integer_message_number, $object_message_part, $string_prefix )
	{
		$array_attachment = array();
		$array_attachment[$string_prefix] = self::phppop3_part_decode( $integer_message_number, $object_message_part, $string_prefix );
		if( isset( $object_message_part->parts ) ) // multipart
		{
			$string_prefix = ($string_prefix == '0')?'':$string_prefix.'.';
			foreach( $object_message_part->parts as $integer_number => $object_part )
				$array_attachment = array_merge( $array_attachment, self::phppop3_part_get( $integer_message_number, $object_part, $string_prefix.($integer_number+1) ));
		}
		return $array_attachment;
	}
	private static function phppop3_part_decode( $integer_message_number, $object_message_part, $string_prefix )
	{
		$array_attachment = array();
		if( $object_message_part->ifdparameters) {
			foreach( $object_message_part->dparameters as $object_parameter ) {
				$array_attachment[strtolower($object_parameter->attribute)] = $object_parameter->value;
				if(strtolower($object_parameter->attribute) == 'filename') {
					$array_attachment['is_attachment'] = true;
					$array_attachment['filename'] = $object_parameter->value;
				}
			}
		}
		if( $object_message_part->ifparameters) {
			foreach( $object_message_part->parameters as $object_parameter ) {
				$array_attachment[strtolower($object_parameter->attribute)] = $object_parameter->value;
				if(strtolower($object_parameter->attribute) == 'name') {
					$array_attachment['is_attachment'] = true;
					$array_attachment['name'] = $object_parameter->value;
				}
			}
		}
		$array_attachment['data'] = imap_fetchbody( self::phppop3_connection(), $integer_message_number, $string_prefix );
		if( $object_message_part->encoding == 3 ) { // 3 = BASE64
			$array_attachment['data'] = base64_decode( $array_attachment['data'] );
		}
		elseif( $object_message_part->encoding == 4 ) { // 4 = QUOTED-PRINTABLE
			$array_attachment['data'] = quoted_printable_decode( $array_attachment['data'] );
		}
		return( $array_attachment );
	}
	private static function phppop3_header_decode( $string_headers )
	{
		$string_headers = preg_replace( '/\r\n\s+/m', '', $string_headers );
		preg_match_all( '/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $string_headers, $array_matches );
		$array_result = array();
		foreach ( (array)$array_matches[1] as $string_key =>$string_value) {
			$array_result[$string_value] = $array_matches[2][$string_key];
		}
		return($array_result);
	}
	private static function phppop3_connection( $resource_connection = null ) {
		if( $resource_connection !== null ) self::$phppop3_connection = $resource_connection;
		if( !is_resource( self::$phppop3_connection ) ) throw new \Exception('Connection not available!');
		return self::$phppop3_connection;
	}
}
?>