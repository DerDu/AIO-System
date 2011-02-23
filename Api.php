<?php
/**
 * This file contains the API-Setup
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
 *  * Neither the name of the Gerd Christian Kunze nor the names of its
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
 * @package AioSystem
 */
namespace AioSystem;
/**
 * @package AioSystem
 */
class ClassApi {
	const API_PREFIX_NAMESPACE = __NAMESPACE__;
	const API_PREFIX_CLASS = 'Class';
	private static $propertySetup = true;
	/**
	 * Setup API
	 *
	 * This registers the \AioSystem class files auto load function
	 *
	 * @static
	 * @return void
	 */
	public static function Setup(){
		self::$propertySetup = true;
		$splAutoLoad = spl_autoload_functions();
		if( $splAutoLoad === false || empty( $splAutoLoad ) || !in_array( array(__CLASS__,'Load'), $splAutoLoad, true ) ) {
			spl_autoload_register( array(__CLASS__,'Load') );
		}
		require_once('Library\PhpFunction.php');
		Core\ClassSession::startSession();
		Core\ClassEventHandler::registerEventHandler(E_ALL,true);
		self::$propertySetup = false;
	}
	/**
	 * Load Class files
	 *
	 * @static
	 * @param  string $propertyClassName
	 * @return bool
	 */
	public static function Load( $propertyClassName ) {
		$propertyClassLocation = __DIR__.(
			preg_replace(
				array(
					'!'.self::API_PREFIX_NAMESPACE.'!is',
					'!'.self::API_PREFIX_CLASS.'!is'
				),
				'',
				$propertyClassName
			).'.php'
		);
		if( file_exists( $propertyClassLocation ) ) {
			if( self::$propertySetup === false || session_id() != '' ) {
				var_dump( 'Load: '.$propertyClassLocation );
			}
			require_once( $propertyClassLocation );
			return true;
		} else {
			return false;
		}
	}
}
/**
 * Setup API (auto)
 */
ClassApi::Setup();
?>