<?php
/**
 * This file contains the API:Spam
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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Module\Spam\ClassSpam as AIOSpam;
/**
 * @package AIOSystem\Api
 */
class Spam {
	public static function Install() {
		return AIOSpam::Install();
	}
	/**
	 * @static
	 * @param string $Content
	 * @param bool $IsSpam
	 * @return void
	 */
	public static function Trainer( $Content, $IsSpam = true ) {
		return AIOSpam::Trainer( $Content, $IsSpam );
	}
	/**
	 * @static
	 * @param string $Content
	 * @return bool
	 */
	public static function Check( $Content ) {
		return AIOSpam::IsSpam( $Content );
	}
	/**
	 * @static
	 * @param string $Content
	 * @return int|float
	 */
	public static function Level( $Content ) {
		return AIOSpam::SpamLevel( $Content );
	}
}
?>