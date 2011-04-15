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
		self::phpmailer_instance()->Send();
		self::$phpmailer_instance = null;
	}
// CONTENT -------------------------------------------------------------------------------
	public static function phpmailer_subject( $string_subject_text )
	{
		self::phpmailer_instance()->Subject = $string_subject_text;
	}
	public static function phpmailer_body( $string_body_text )
	{
		self::phpmailer_instance()->MsgHTML( $string_body_text );
	}
	public static function phpmailer_attachment( $string_filename )
	{
		if( file_exists( $string_filename ) ) self::phpmailer_instance()->AddAttachment( $string_filename );
	}
// ADDRESS -------------------------------------------------------------------------------
	public static function phpmailer_from( $string_from_mail, $string_from_name = '' )
	{
		self::phpmailer_instance()->SetFrom( $string_from_mail, $string_from_name );
	}
	public static function phpmailer_to( $string_to_mail, $string_to_name = '' )
	{
		self::phpmailer_instance()->AddAddress( $string_to_mail, $string_to_name );
	}
	public static function phpmailer_reply( $string_reply_mail, $string_reply_name = '' )
	{
		self::phpmailer_instance()->AddReplyTo( $string_reply_mail, $string_reply_name );
	}
// AUTHENTICATION ------------------------------------------------------------------------
	public static function phpmailer_smtp( $string_hostname, $string_username, $string_password, $string_smtpport = 25 )
	{
		self::phpmailer_instance()->IsSMTP();
		//self::phpmailer_instance()->SMTPDebug = 2;
		self::phpmailer_instance()->SMTPAuth = true;
		self::phpmailer_instance()->Host = $string_hostname;
		self::phpmailer_instance()->Username = $string_username;
		self::phpmailer_instance()->Password = $string_password;
		self::phpmailer_instance()->Port = $string_smtpport;
	}
// ---------------------------------------------------------------------------------------
	private static function phpmailer_instance()
	{
		if( self::$phpmailer_instance === null ) {
			if( !class_exists('PHPMailer') ) require_once(dirname(__FILE__) . '/PhpMailer/class.phpmailer.php');
			self::$phpmailer_instance = new \PHPMailer(true);
		}
		return self::$phpmailer_instance;
	}
}
?>