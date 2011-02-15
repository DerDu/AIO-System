<?php
require_once( dirname(__FILE__).'/CoreSystemDirectory.php' );
require_once( dirname(__FILE__).'/CoreSystemFile.php' );
require_once( dirname(__FILE__).'/../Library/LibraryRegExp.php' );
// ---------------------------------------------------------------------------------------
// InterfaceCoreEventJournal, ClassCoreEventJournal
// ---------------------------------------------------------------------------------------
interface InterfaceCoreEventJournal
{
	public static function addEvent( $propertyEventContent, $propertyJournalName = 'DefaultEventJournal' );
}
// ---------------------------------------------------------------------------------------
// LICENSE (BSD)
//
//	Copyright (c) 2011, Gerd Christian Kunze
//	All rights reserved.
//
//	Redistribution and use in source and binary forms, with or without
//	modification, are permitted provided that the following conditions are
//	met:
//
//		* Redistributions of source code must retain the above copyright
//		  notice, this list of conditions and the following disclaimer.
//		* Redistributions in binary form must reproduce the above copyright
//		  notice, this list of conditions and the following disclaimer in the
//		  documentation and/or other materials provided with the distribution.
//		* Neither the name of the Gerd Christian Kunze nor the names of its
//		  contributors may be used to endorse or promote products derived from
//		  this software without specific prior written permission.
//
//	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
//	IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
//	THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
//	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
//	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
//	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
//	PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
//	PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
//	LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
//	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
//	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
class ClassCoreEventJournal implements InterfaceCoreEventJournal
{
	public static function addEvent( $propertyEventContent, $propertyJournalName = 'DefaultEventJournal' )
	{
		$propertyJournalName = ClassCoreSystemDirectory::adjustDirectorySyntax( dirname( __FILE__ ).'/../Journal/' ).'Journal.'.$propertyJournalName.'.txt';
		if( !is_dir( dirname( $propertyJournalName ) ) ){
			ClassCoreSystemDirectory::createDirectory( dirname( $propertyJournalName ) );
		}
		$CoreSystemFile = new ClassCoreSystemFile( $propertyJournalName );
		if( date( 'Ymd', $CoreSystemFile->propertyFileTime() ) < date('Ymd') )
		{
			$CoreSystemFile->moveFile( substr($propertyJournalName,0,-4).'.'.date('YmdHis').'.txt' );
			$CoreSystemFile = new ClassCoreSystemFile( $propertyJournalName );
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
	public static function getJournalList( $propertyDayCountHistory = 15 ){
		$regexpIntegerBetween = ClassLibraryRegExp::integerBetween(
			mktime( 0,0,0, date('m'), (date('d')-abs($propertyDayCountHistory)), date('Y') ),
			time()
		);
		$directoryName = ClassCoreSystemDirectory::adjustDirectorySyntax( dirname( __FILE__ ).'/../Journal/' );
		$directoryFileList = ClassCoreSystemDirectory::getFileList( $directoryName );
		return ClassCoreSystemDirectory::applyFileListFilter( $directoryFileList,
			array(
				'propertyFileName'=>'^Journal\..*?\.txt',
				'propertyFileContent'=>'([0-9]{2}\.[0-9]{2}\.[0-9]{4}) ([0-9]{2}:[0-9]{2}:[0-9]{2}) SID:',
				'propertyFileTime'=>$regexpIntegerBetween
			)
		);
	}
}
?>