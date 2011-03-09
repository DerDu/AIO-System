<?php
/**
 * This file contains the API:Mail
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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Module\Mail\ClassPhpPop3 as AioPhpPop3;
use \AioSystem\Module\Mail\ClassPhpMailer as AioPhpMailer;
/**
 * @package AioSystem\Api
 */
class Mail {
	/**
	 * @static
	 * @param string $Host
	 * @param string $User
	 * @param string $Password
	 * @param string $Folder
	 * @param bool $Ssl
	 * @param int $Port
	 * @return resource
	 */
	public static function Pop3Open( $Host, $User, $Password, $Folder = 'INBOX', $Ssl = false, $Port = 110 ) {
		return AioPhpPop3::phppop3_open( $Host, $User, $Password, $Folder, $Ssl, $Port );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Pop3Close() {
		return AioPhpPop3::phppop3_close();
	}
	/**
	 * @static
	 * @return int
	 */
	public static function Pop3Count() {
		return AioPhpPop3::phppop3_count();
	}
	/**
	 * @static
	 * @param  array $Criteria
	 * @return array
	 */
	public static function Pop3Search( $Criteria ) {
		return AioPhpPop3::phppop3_search( $Criteria );
	}
	/**
	 * @static
	 * @param  int $Message
	 * @return void
	 */
	public static function Pop3Remove( $Message ) {
		return AioPhpPop3::phppop3_remove( $Message );
	}
	/**
	 * @static
	 * @param  int $Message
	 * @return string
	 */
	public static function Pop3Header( $Message ) {
		return AioPhpPop3::phppop3_header( $Message );
	}
	/**
	 * @static
	 * @param  int $Message
	 * @return array
	 */
	public static function Pop3Read( $Message, $Parse = false ) {
		return AioPhpPop3::phppop3_read( $Message, $Parse );
	}
	/**
	 * @static
	 * @param  int $Message
	 * @return array
	 */
	public static function Pop3Attachment( $Message ) {
		return AioPhpPop3::phppop3_attachment( $Message );
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $Host
	 * @param string $User
	 * @param string $Password
	 * @param int $Port
	 * @return void
	 */
	public static function Smtp( $Host, $User, $Password, $Port = 25 ) {
		return AioPhpMailer::phpmailer_smtp( $Host, $User, $Password, $Port );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function Send() {
		return AioPhpMailer::phpmailer_send();
	}
	/**
	 * @static
	 * @param string $Text
	 * @return void
	 */
	public static function Subject( $Text ) {
		return AioPhpMailer::phpmailer_subject( $Text );
	}
	/**
	 * @static
	 * @param string $Text
	 * @return void
	 */
	public static function Body( $Text ) {
		return AioPhpMailer::phpmailer_body( $Text );
	}
	/**
	 * @static
	 * @param string $File
	 * @return void
	 */
	public static function Attachment( $File ) {
		return AioPhpMailer::phpmailer_attachment( $File );
	}
	/**
	 * @static
	 * @param string $Mail
	 * @param string $Name
	 * @return void
	 */
	public static function From( $Mail, $Name = '' ) {
		return AioPhpMailer::phpmailer_from( $Mail, $Name );
	}
	/**
	 * @static
	 * @param string $Mail
	 * @param string $Name
	 * @return void
	 */
	public static function To( $Mail, $Name = '' ) {
		return AioPhpMailer::phpmailer_to(  $Mail, $Name );
	}
	/**
	 * @static
	 * @param string $Mail
	 * @param string $Name
	 * @return void
	 */
	public static function Reply( $Mail, $Name = '' ) {
		return AioPhpMailer::phpmailer_reply( $Mail, $Name );
	}
}
?>