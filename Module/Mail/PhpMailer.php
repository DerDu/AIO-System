<?php
/**
 * PhpMail
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
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Mail
 */
interface InterfacePhpMailer
{
	public static function phpmailer_send();

	public static function phpmailer_subject( $string_subject_text );
	public static function phpmailer_body( $string_body_text );
	public static function phpmailer_attachment( $string_filename );

	public static function phpmailer_from( $string_from_mail, $string_from_name = '' );
	public static function phpmailer_to( $string_to_mail, $string_to_name = '' );
	public static function phpmailer_reply( $string_reply_mail, $string_reply_name = '' );

	public static function phpmailer_smtp( $string_hostname, $string_username, $string_password, $string_smtpport = 25 );
}
/**
 * @package AIOSystem\Module
 * @subpackage Mail
 */
class ClassPhpMailer implements InterfacePhpMailer
{
	/** @var \PHPMailer $phpmailer_instance */
	private static $phpmailer_instance = null;

	public function __construct() {
	}
// EXECUTION------------------------------------------------------------------------------------
	public static function phpmailer_send()
	{
		ob_start(); self::phpmailer_instance()->Send(); $Status = ob_get_contents();ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
		self::$phpmailer_instance = null;
		return $Status;
	}
// CONTENT -------------------------------------------------------------------------------
	public static function phpmailer_subject( $string_subject_text )
	{
		ob_start(); self::phpmailer_instance()->Subject = $string_subject_text; ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_body( $string_body_text, $isHtml = true ) {
		ob_start(); self::phpmailer_instance()->IsHTML( $isHtml ); ob_end_clean();
		ob_start(); self::phpmailer_instance()->MsgHTML( $string_body_text ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_attachment( $string_filename )
	{
		ob_start(); if( file_exists( $string_filename ) ) self::phpmailer_instance()->AddAttachment( $string_filename ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
// ADDRESS -------------------------------------------------------------------------------
	public static function phpmailer_from( $string_from_mail, $string_from_name = '' )
	{
		ob_start(); self::phpmailer_instance()->SetFrom( $string_from_mail, $string_from_name ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_check_to( $string_to_mail ) {
		ob_start(); self::phpmailer_instance()->ValidateAddress( $string_to_mail ); $Status = ob_get_contents();ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
		return $Status;
	}

	public static function phpmailer_to( $string_to_mail, $string_to_name = '' )
	{
		ob_start(); self::phpmailer_instance()->AddAddress( $string_to_mail, $string_to_name ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_to_cc( $string_to_mail, $string_to_name = '' )
	{
		ob_start(); self::phpmailer_instance()->AddCC( $string_to_mail, $string_to_name ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_to_bcc( $string_to_mail, $string_to_name = '' )
	{
		ob_start(); self::phpmailer_instance()->AddBCC( $string_to_mail, $string_to_name ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
	public static function phpmailer_reply( $string_reply_mail, $string_reply_name = '' )
	{
		ob_start(); self::phpmailer_instance()->AddReplyTo( $string_reply_mail, $string_reply_name ); ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
// AUTHENTICATION ------------------------------------------------------------------------
	public static function phpmailer_smtp( $string_hostname, $string_username, $string_password, $string_smtpport = 25 )
	{
		ob_start();
		self::phpmailer_instance()->IsSMTP();
		//self::phpmailer_instance()->SMTPDebug = 2;
		self::phpmailer_instance()->SMTPAuth = true;
		self::phpmailer_instance()->Host = $string_hostname;
		self::phpmailer_instance()->Username = $string_username;
		self::phpmailer_instance()->Password = $string_password;
		self::phpmailer_instance()->Port = $string_smtpport;
		ob_end_clean();
		if( self::phpmailer_instance()->IsError() ) {
			Event::Error( 0, self::phpmailer_instance()->ErrorInfo, __FILE__, __LINE__ );
			self::phpmailer_instance()->error_count = 0;
			self::phpmailer_instance()->ErrorInfo = null;
		}
	}
// ---------------------------------------------------------------------------------------
	private static function phpmailer_instance()
	{
		if( self::$phpmailer_instance === null ) {
			if( !class_exists('PHPMailer') ) require_once(dirname(__FILE__) . '/PhpMailer/class.phpmailer.php');
			self::$phpmailer_instance = new \PHPMailer();
		}
		return self::$phpmailer_instance;
	}
}
?>
