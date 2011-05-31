<?php
/**
 * Cache
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
 * @subpackage Cache
 */
namespace AIOSystem\Module\Cache;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Session;
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
interface InterfaceCache {
	public static function Set( $Identifier, $Content, $Timeout = 3600, $Location = 'Common', $Global = false );
	public static function Get( $Identifier, $Location = 'Common', $Global = false );
	public static function Clean( $Identifier, $Location = 'Common', $Global = false );
}
/**
 * @package AIOSystem\Module
 * @subpackage Cache
 */
class ClassCache {
	private static $CacheDirectory = '../../Cache';

	public static function Filename( $FileName ) {
		return  date('ymd')
				.'_'.pathinfo($FileName,PATHINFO_FILENAME)
				.'_'.time()
				.'.'.pathinfo($FileName,PATHINFO_EXTENSION);
	}

	public static function Location( $CacheName = 'Common', $isGlobal = false ) {
		$Directory = System::CreateDirectory(
			System::DirectorySyntax(
				self::_cacheDirectory().'/'.$CacheName.(!$isGlobal?'/'.strtoupper(Session::Id()):'')
			)
		);
		chmod( $Directory, 0777 );
		return $Directory;
	}

	private static function _cacheDirectory() {
		$CacheDirectory = System::DirectorySyntax( __DIR__.'/'.self::$CacheDirectory );
		if( !is_dir( $CacheDirectory ) ) {
			System::CreateDirectory( $CacheDirectory );
		} return $CacheDirectory;
	}
}
