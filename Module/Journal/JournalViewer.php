<?php
/**
 * Pathfinder: JournalViewer
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
 * @package AioSystem\Module
 * @subpackage JournalViewer
 */
namespace AioSystem\Module\Journal;
use AioSystem\Api\System;
use AioSystem\Api\Template;
use AioSystem\Core\ClassEventJournal as Journal;
/**
 * @package AioSystem\Module
 * @subpackage JournalViewer
 */
interface InterfaceJournalViewer {

}
/**
 * @package AioSystem\Module
 * @subpackage JournalViewer
 */
class ClassJournalViewer implements InterfaceJournalViewer {

	const CONTENT_ALL = 0;
	const CONTENT_DEBUG = 1;
	const CONTENT_ERROR = 2;
	const CONTENT_EXCEPTION = 3;
	const CONTENT_SHUTDOWN = 4;

	public static function GetJournalViewer() {
		return Template::Load( __DIR__.'/JournalViewer/'.'JournalViewer.tpl' )->Parse();
	}

	public static function GetJournalContent( $CONTENT_TYPE = self::CONTENT_ERROR, $ShowCountHistory = 20 ) {
		switch( $CONTENT_TYPE ) {
			case self::CONTENT_DEBUG: {
				$Content = self::GetJournalContentDebug( $ShowCountHistory );
				return self::GetContentTable( $Content, $CONTENT_TYPE, 10, isset($Content[0]['FileCount'])?$Content[0]['FileCount']:'N/A', count( $Content ) );
			}
			case self::CONTENT_ERROR: {
				$Content = self::GetJournalContentError( $ShowCountHistory );
				return self::GetContentTable( $Content, $CONTENT_TYPE, 10, isset($Content[0]['FileCount'])?$Content[0]['FileCount']:'N/A', count( $Content ) );
			}
			case self::CONTENT_EXCEPTION: {
				$Content = self::GetJournalContentException( $ShowCountHistory );
				return self::GetContentTable( $Content, $CONTENT_TYPE, 3, isset($Content[0]['FileCount'])?$Content[0]['FileCount']:'N/A', count( $Content ) );
			}
			case self::CONTENT_SHUTDOWN: {
				$Content = self::GetJournalContentShutdown( $ShowCountHistory );
				return self::GetContentTable( $Content, $CONTENT_TYPE, 3, isset($Content[0]['FileCount'])?$Content[0]['FileCount']:'N/A', count( $Content ) );
			}
			case self::CONTENT_ALL: {
				$Content = self::GetJournalContentAll( $ShowCountHistory );
				return self::GetContentTable( $Content, $CONTENT_TYPE, 15, isset($Content[0]['FileCount'])?$Content[0]['FileCount']:'N/A', count( $Content ) );
			}
		}
	}

	private static function GetContentTable( $ContentList, $ContentType, $ContentLength, $FileCount = 'N/A', $EntryCount = 'N/A' ) {
		$Template = Template::Load( __DIR__.'/JournalViewer/'.'JournalViewerTable.tpl' );
		$Template->Repeat( 'JournalEntryList', $ContentList );
		$Template->Assign( 'JournalType', $ContentType );
		$Template->Assign( 'JournalLength', $ContentLength );
		$Template->Assign( 'FileCount', $FileCount );
		$Template->Assign( 'EntryCount', $EntryCount );
		return $Template->Parse();
	}
	private static function GetJournalContentError( $ShowCountHistory = 20 ) {
		return Journal::getJournalContentList( 15, $ShowCountHistory,
			System::FileListFilter(
				Journal::getJournalList(), array('propertyFileName'=>'EventError')
			)
		);
	}
	private static function GetJournalContentException( $ShowCountHistory = 20 ) {
		return Journal::getJournalContentList( 15, $ShowCountHistory,
			System::FileListFilter(
				Journal::getJournalList(), array('propertyFileName'=>'EventException')
			)
		);
	}
	private static function GetJournalContentShutdown( $ShowCountHistory = 20 ) {
		return Journal::getJournalContentList( 15, $ShowCountHistory,
			System::FileListFilter(
				Journal::getJournalList(), array('propertyFileName'=>'EventShutdown')
			)
		);
	}
	private static function GetJournalContentDebug( $ShowCountHistory = 20 ) {
		return Journal::getJournalContentList( 15, $ShowCountHistory,
			System::FileListFilter(
				Journal::getJournalList(), array('propertyFileName'=>'EventDebug')
			)
		);
	}
	private static function GetJournalContentAll( $ShowCountHistory = 80 ) {
		return Journal::getJournalContentList( 15, $ShowCountHistory, Journal::getJournalList() );
	}
}