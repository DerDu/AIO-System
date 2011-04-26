<?php
/**
 * This file contains the API:Seo
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
use \AIOSystem\Core\ClassSeoUri as AIOSeoUri;
use \AIOSystem\Library\ClassJQueryAddress as AIOSeoDeeplink;
/**
 * @package AIOSystem\Api
 */
class Seo {
	/**
	 * Build absolute Uri for SEO usage
	 *
	 * @static
	 * @param string $Path
	 * @param int $Level
	 * @return string
	 */
	public static function Path( $Path, $Level = 0 ) {
		return AIOSeoUri::UriPath( $Path, $Level );
	}
	/**
	 * Add SEO-Url-Parameter to $_REQUEST
	 *
	 * @static
	 * @return void
	 */
	public static function Request() {
		return AIOSeoUri::UriRequest();
	}
	/**
	 * @static
	 * @param string|array $Target
	 * @param string|array $File
	 * @param string $Name
	 * @param array $Parameter
	 * @param int $Level
	 * @return string
	 */
	public static function DeepLink( $Target, $File, $Name, $Parameter = array(), $Level = 0 ) {
		return AIOSeoDeeplink::jQueryAddressDeeplink( $Target, $File, $Name, $Parameter, $Level );
	}
}
?>