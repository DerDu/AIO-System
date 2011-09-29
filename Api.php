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
 * @package AIOSystem
 */
namespace AIOSystem;
use \AIOSystem\Api\Session;
use \AIOSystem\Api\Seo;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem
 */
class Api {
	const API_PREFIX_CLASS = 'Class';
	const API_PREFIX_INTERFACE = 'Interface';
	const API_PREFIX_WIDGET = 'Widget';
	private static $propertySetup = true;
	/**
	 * Setup API
	 *
	 * This registers the \AIOSystem class auto load function
	 *
	 * @static
	 * @return void
	 */
	public static function Setup(){
		self::$propertySetup = true;
		spl_autoload_register( array(__CLASS__,'AutoLoader') );
		require_once( __DIR__. '/Library/PhpFunction.php');
		Session::Start();
		Event::RegisterHandler( E_ALL, true );
		Seo::Request();
		//self::WidgetStyle();
		self::$propertySetup = false;
		ini_set( "docref_root", "http://www.php.net/" );
	}
	/**
	 * Load class files
	 *
	 * @static
	 * @param  string $propertyClassName
	 * @return bool
	 */
	private static function AutoLoader( $propertyClassName ) {
		$propertyClassName = str_replace( __NAMESPACE__, '', $propertyClassName );
		$propertyClassName = explode( '\\', $propertyClassName );
		$ClassName = array_pop( $propertyClassName );
		$ClassName = preg_replace(
						'!(^'.self::API_PREFIX_CLASS.'|'
						.'^'.self::API_PREFIX_INTERFACE.'|'
						.'^'.self::API_PREFIX_WIDGET.')!is'
						, '', $ClassName );
		array_push( $propertyClassName, $ClassName );
		$propertyClassLocation = __DIR__.implode( '\\', $propertyClassName ).'.php';
		//var_dump( $propertyClassLocation );
		if( file_exists( $propertyClassLocation ) ) {
			require_once( $propertyClassLocation );
			//\AIOSystem\Api\Event::Debug( 'Load: '.$propertyClassLocation );
			if( self::$propertySetup === false || session_id() != '' ) {
				//\AIOSystem\Api\Event::Message( 'Load: '.$propertyClassLocation );
				//var_dump( 'AIO Load: '.$propertyClassLocation );
			}
			return true;
		} else {
			return false;
		}
	}
	/**
	 * @static
	 * @return string
	 */
	/*
	public static function WidgetStyle() {
		$WidgetStyleList = Session::Read( __METHOD__ );
		if( $WidgetStyleList === null ) {
			$WidgetList = System::DirectoryList( 'Widget' );
			$WidgetStyleList = array();
			foreach( (array)$WidgetList as $Directory ) {
				if( is_dir( $Directory.'Style' ) ) {
					$WidgetStyle = System::FileList( $Directory.'Style', 'css' );
					/** @var \AIOSystem\Core\ClassSystemFile $WidgetStyleFile */
	/*				foreach( (array)$WidgetStyle as $WidgetStyleFile ) {
						$StyleFile = Seo::Path( $WidgetStyleFile->propertyFileLocation() );
						if( !in_array( $StyleFile, $WidgetStyleList ) ) {
							array_push( $WidgetStyleList, $StyleFile );
						}
					}
				}
			}
			Session::Write( __METHOD__, $WidgetStyleList );
		}
		$Return = '';
		foreach( (array)$WidgetStyleList as $StyleLocation ) {
			$Return .= '<link rel="stylesheet" href="'.$StyleLocation.'"/>'."\n";
		}
		return $Return;
	}*/
}
/**
 * Setup API (auto)
 */
Api::Setup();


// ---------------------------------------------------------------------------------------
/**
 * Information
 *
 * Class::Install()
 * - Create database structure
 * - Unzip files
 * - ...
 *
 * Class::Instance()
 * - Create class instance object
 * - Initialize instance object
 *
 * Class::Config()
 * -
 *
 */
?>
