<?php
/**
 * This file contains the API:Event
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
use \AIOSystem\Module\Journal\ClassJournalViewer as AIOJournalViewer;
use \AIOSystem\Core\ClassEventJournal as AIOEventJournal;
use \AIOSystem\Core\ClassEventScreen as AIOEventScreen;
use \AIOSystem\Core\ClassEventHandler as AIOEventHandler;
use \AIOSystem\Core\ClassEventError as AIOEventError;
use \AIOSystem\Core\ClassEventException as AIOEventException;
/**
 * @package AIOSystem\Api
 */
class Event {
	/** @var \AIOSystem\Core\ClassStackRegister $EventRegister */
	private static $EventRegister = null;
	public static function RegisterHandler( $ERROR_REPORTING = E_ALL, $Display = true ) {
		AIOEventHandler::registerEventHandler( $ERROR_REPORTING, $Display );
	}
	/**
	 * @static
	 * @return string
	 */
	public static function Viewer() {
		return AIOJournalViewer::GetJournalViewer();
	}
	/**
	 * @static
	 * @param  string $Content
	 * @param  string $Name
	 * @return void
	 */
	public static function Journal( $Content, $Name = 'DefaultEventJournal' ) {
		AIOEventJournal::addEvent( $Content, $Name );
	}
	/**
	 * @static
	 * @param  string $Content
	 */
	public static function Debug( $Content, $Location = '', $Position = '' ) {
		AIOEventJournal::addEvent( $Content, 'Debug', $Location, $Position );
		AIOEventScreen::addEvent( 0, $Content, $Location, $Position, AIOEventScreen::SCREEN_DEBUG );
	}
	/**
	 * @static
	 * @param  string $Content
	 */
	public static function Message( $Content, $Location = '', $Position = '' ) {
		AIOEventScreen::addEvent( 0, $Content, $Location, $Position, AIOEventScreen::SCREEN_INFO );
	}
	/**
	 * @static
	 * @param  int $Id
	 * @param  string $Content
	 * @param  string $Location
	 * @param  int $Position
	 * @return void
	 */
	public static function Error( $Id, $Content, $Location, $Position ) {
		AIOEventError::eventHandler( $Id, $Content, $Location, $Position );
		//AIOEventScreen::addEvent( $Id, $Content, $Location, $Position, AIOEventScreen::SCREEN_ERROR );
	}
	/**
	 * @static
	 * @param  int $Id
	 * @param  string $Content
	 * @param  string $Location
	 * @param  int $Position
	 * @return void
	 */
	public static function Exception( $Id, $Content, $Location, $Position ) {
		AIOEventException::eventHandler( $Id, $Content, $Location, $Position );
		//AIOEventScreen::addEvent( $Id, $Content, $Location, $Position, AIOEventScreen::SCREEN_EXCEPTION );
	}
	public static function Result( $Key, $Content = null ) {
		if( self::$EventRegister === null ) {
			self::$EventRegister = Stack::Register( true );
		}
		if( $Key === null ) {
			return self::$EventRegister->listRegister();
		}
		if( $Content === null ) {
			$Result = self::$EventRegister->getRegister( $Key );
			self::$EventRegister->setRegister( $Key, null );
		} else {
			self::$EventRegister->setRegister( $Key, $Content );
			$Result = self::$EventRegister->getRegister( $Key );
		}
		return $Result;
	}
}
?>
