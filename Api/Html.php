<?php
/**
 * This file contains the API:Html
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
use \AIOSystem\Api\Event;
use \AIOSystem\Module\Video\ClassVideo as Video;
use \AIOSystem\Module\Excel\DataTable as DataTable;

/**
 * @package AIOSystem\Api
 */
class Html {
	const STYLE_MEDIA_ALL = 'all';
	const STYLE_MEDIA_SCREEN = 'screen';
	const STYLE_MEDIA_HANDHELD = 'handheld';
	const STYLE_MEDIA_PROJECTION = 'projection';
	const STYLE_MEDIA_PRINT = 'print';
	/**
	 * @static
	 * @param string $Directory
	 * @param bool $Recursive
	 * @return string
	 */
	public static function Style( $Directory, $Recursive = false, $Media = self::STYLE_MEDIA_ALL ) {
		$FileList = array();
		if( is_dir( realpath($Directory) ) ) {
			$FileList = System::FileList( realpath($Directory), array('css'), $Recursive );
		} else if( is_file( realpath($Directory) ) ) {
			//return '<link rel="stylesheet" href="'.Seo::Path( $Directory ).'"/>';
			return '<link rel="stylesheet" type="text/css" href="'.Seo::Path(
					System::RelativeDirectory(
						System::DirectorySyntax( realpath($Directory), DIRECTORY_SEPARATOR ),
						System::DirectorySyntax( System::DirectoryRoot(), DIRECTORY_SEPARATOR )
					)
				,-1 ).'" media="'.$Media.'"/>';
		} else {
			trigger_error('Style not available! '.$Directory );
		}
		$Return = '';
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$FileList as $File ) {
			//$Return .= '<link rel="stylesheet" href="'.Seo::Path( $Directory.System::DirectorySyntax( DIRECTORY_SEPARATOR.System::RelativeDirectory( $File->propertyFileLocation(), realpath($Directory) ),false) ).'"/>';
			$Return .= '<link rel="stylesheet" type="text/css" href="'.Seo::Path(
					System::RelativeDirectory(
						System::DirectorySyntax( $File->propertyFileLocation(), DIRECTORY_SEPARATOR ),
						System::DirectorySyntax( System::DirectoryRoot(), DIRECTORY_SEPARATOR )
					)
				,-1).'" media="'.$Media.'"/>';
		}
		return $Return;
	}
	/**
	 * @static
	 * @param string $Directory
	 * @param bool $Recursive
	 * @return string
	 */
	public static function Javascript( $Directory, $Recursive = false ) {
		$FileList = array();
		if( is_dir( realpath($Directory) ) ) {
			$FileList = System::FileList( realpath($Directory), array('js'), $Recursive );
		} else if( is_file( realpath($Directory) ) ) {
			//return '<script type="text/javascript" src="'.Seo::Path( $Directory ).'"></script>';
			return '<script type="text/javascript" src="'.Seo::Path(
					System::RelativeDirectory(
						System::DirectorySyntax( realpath($Directory), DIRECTORY_SEPARATOR ),
						System::DirectorySyntax( System::DirectoryRoot(), DIRECTORY_SEPARATOR )
					)
				,-1 ).'"></script>';
		} else {
			trigger_error('JavaScript not available! '.$Directory );
		}
		$Return = '';
		/** @var \AIOSystem\Core\ClassSystemFile $File */
		foreach( (array)$FileList as $File ) {
			//$Return .= '<script type="text/javascript" src="'.Seo::Path( $Directory.System::DirectorySyntax( DIRECTORY_SEPARATOR.System::RelativeDirectory( $File->propertyFileLocation(), realpath($Directory) ),false) ).'"></script>';
			$Return .= '<script type="text/javascript" src="'.Seo::Path(
					System::RelativeDirectory(
						System::DirectorySyntax( $File->propertyFileLocation(), DIRECTORY_SEPARATOR ),
						System::DirectorySyntax( System::DirectoryRoot(), DIRECTORY_SEPARATOR )
					)
				,-1).'"></script>';
		}
		return $Return;
	}
	public static function Video( $File, $Option = array() ) {
		return Video::Load( $File, $Option );
	}
	public static function Favicon( $File ) {
		return '<link rel="shortcut icon" type="image/x-icon" href="'.Seo::Path( $File ).'"/>';
	}
	public static function DataTable( $Data = array(), $Hidden = false ) {
		return DataTable::Instance( $Data, $Hidden );
	}
}
?>
