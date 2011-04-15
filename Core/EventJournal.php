<?php
/**
 * This file contains the Event-Journal
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
 * @package AIOSystem\Core
 * @subpackage Event
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Template;
use \AIOSystem\Library\ClassRegExp as AIORegExp;
/**
 * @package AIOSystem\Core
 * @subpackage Event
 */
interface InterfaceEventJournal {
	public static function addEvent( $propertyEventContent, $propertyJournalName = 'DefaultEventJournal' );
}
/**
 * @package AIOSystem\Core
 * @subpackage Event
 */
class ClassEventJournal implements InterfaceEventJournal {
	/**
	 * @static
	 * @param string $propertyEventContent
	 * @param string $propertyJournalName
	 * @return void
	 */
	public static function addEvent( $propertyEventContent, $propertyJournalName = 'DefaultEventJournal' ) {
		$propertyJournalName = ClassSystemDirectory::adjustDirectorySyntax( __DIR__.'/../Journal/' ).'Journal.'.$propertyJournalName.'.txt';
		if( !is_dir( dirname( $propertyJournalName ) ) ) {
			ClassSystemDirectory::createDirectory( dirname( $propertyJournalName ) );
		}
		$CoreSystemFile = System::File( $propertyJournalName );
		if( date( 'Ymd', $CoreSystemFile->propertyFileTime() ) < date('Ymd') ) {
			$CoreSystemFile->moveFile( substr($propertyJournalName,0,-4).'.'.date('YmdHis').'.txt' );
			$CoreSystemFile = System::File( $propertyJournalName );
		}
		$CoreSystemFile->propertyFileContent(
					( $CoreSystemFile->propertyFileSize() != 0 ? "\n" : '' )
					.str_repeat('-',50)."\n"
					.date("d.m.Y H:i:s",time())." SID:".strtoupper( session_id() )."\n"
					.str_repeat('-',50)."\n"
					.$propertyEventContent."\n"
		);
		$CoreSystemFile->writeFile('a');
	}
	/**
	 * @static
	 * @param int $propertyDayCountHistory
	 * @return \AIOSystem\Core\ClassSystemFile[] array
	 */
	public static function getJournalList( $propertyDayCountHistory = 15 ) {
		$regexpIntegerBetween = AIORegExp::integerBetween(
			mktime( 0,0,0, date('m'), (date('d')-abs($propertyDayCountHistory)), date('Y') ),
			time()
		);
		$directoryName = ClassSystemDirectory::adjustDirectorySyntax( __DIR__.'/../Journal/' );
		$directoryFileList = ClassSystemDirectory::getFileList( $directoryName );
		return ClassSystemDirectory::applyFileListFilter( $directoryFileList,
			array(
				'propertyFileName'=>'^Journal\..*?\.txt',
				'propertyFileContent'=>'([0-9]{2}\.[0-9]{2}\.[0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2}) SID:',
				'propertyFileTime'=>$regexpIntegerBetween
			)
		);
	}

	public static function getJournalContentList( $DayCountHistory = 15, $ShowCountHistory = 10, $ClassSystemFileList = null ) {
		if( $ClassSystemFileList === null ) {
			$ClassSystemFileList = self::getJournalList( $DayCountHistory );
		}

		usort( $ClassSystemFileList, create_function('$FileA,$FileB','return $FileA->propertyFileTime() < $FileB->propertyFileTime();') );
		/** @var ClassSystemFile $ClassSystemFile */
		$Content = '';
		foreach( (array)$ClassSystemFileList as $ClassSystemFile ) {
			$ClassSystemFile->propertyFileContent();
			$Content .= "\n\n".trim(
				preg_replace( '!\n\n(-{50})!is', "\n".$ClassSystemFile->propertyFileName().'${1}', $ClassSystemFile->propertyFileContent() )
			)."\n".$ClassSystemFile->propertyFileName();
		}
		$Match = array();
		preg_match_all(
			'!([0-9]{2}\.[0-9]{2}\.[0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2}) SID:(.*?)\n-{50}(.*?)(?=(-{50}|$))!is',
			$Content,
			$Match
		);
		$ErrorType = array(
			'Code [0 DEBUG]' => array('#D7A700','DEBUG','#FFF2E0','#973700'),
			'Code [2048 STRICT]' => array('#C0C0C0','STRICT','#FEFEFE','gray'),
			'Code [8 NOTICE]' => array('#C0C0C0','NOTICE','#FEFEFE'),
			'Code [2 WARNING]' => array('#903030','WARNING','#FFF3F3'),
			'Unexpected Error:' => array('#D30D0D','UNEXPECTED','#FFD0D0'),
			'ShutDown Error:' => array('#FF2323','SHUTDOWN','#FFADAD'),
		);
		$Length = count( $Match[0] );
		$TemplateData = array();
		for( $Run = 0; $Run < $Length && $Run < $ShowCountHistory; $Run++ ){
			$Attribute = array();
			foreach( (array)$ErrorType as $Type => $AttributeList ){
				if( strpos($Match[4][$Run], $Type ) !== false ){
					$Attribute = $AttributeList;
					break;
				}
			}
			array_push( $TemplateData, array(
				'CategoryStyle' =>
					'text-align:center;'
					.(isset($Attribute[0])?'color:'.$Attribute[0].';':'')
				,
				'EntrySort' => date('YmdHis',strtotime( $Match[1][$Run].' '.$Match[2][$Run] )),
				'EntryTimestamp' => trim($Match[1][$Run].' '.$Match[2][$Run]),
				'CategoryType' => trim(isset($Attribute[1])?'<br/>'.$Attribute[1]:''),
				'SessionId' => trim($Match[3][$Run]),
				'SessionUser' => '',
				'ContentStyle' =>
					(isset($Attribute[3])?'color:'.$Attribute[3].';':'')
					.(isset($Attribute[2])?'background-color:'.$Attribute[2].';':'')
				,
				'Content' =>trim(
							preg_replace(
								'!<br ?/?>\n([^<]*?)$!is',
								'<span style="color:silver;font-size:0.8em;"><br/>(${1})</span>',
							preg_replace(
								'!<br ?/?>[^<]*?(?=<br ?/?>[^<]*?$)!is',
								'<span style="color:gray;font-size:0.9em;">${0}</span>',
							nl2br(utf8_encode(trim($Match[4][$Run])))
							)
				)),
				'FileCount' => count( $ClassSystemFileList )
			) );
		}
		return $TemplateData;
	}

	public static function getJournalContent( $DayCountHistory = 15, $ShowCountHistory = 10, $ClassSystemFileList = null ) {
		$Template = Template::Load( __DIR__.'/EventJournal/JournalEntryTable.tpl' );
		$TemplateData = self::getJournalContentList( $DayCountHistory, $ShowCountHistory, $ClassSystemFileList );

		$Template->Repeat( 'JournalEntryList', $TemplateData );
		$Template->Assign( 'FileCount', $TemplateData[0]['FileCount'] );
		$Template->Assign( 'EntryCount', count( $TemplateData ) );
		return $Template->Parse();
	}
}
?>