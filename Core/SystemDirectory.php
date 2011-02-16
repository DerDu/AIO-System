<?php
namespace AioSystem\Core;
// ---------------------------------------------------------------------------------------
require_once(dirname(__FILE__) . '/SystemFile.php');
// ---------------------------------------------------------------------------------------
// InterfaceSystemDirectory, ClassSystemDirectory
// ---------------------------------------------------------------------------------------
interface InterfaceSystemDirectory {
	public static function getFileList( $propertyDirectoryName, $propertyFileTypeList = array(), $isRecursive = false );
	public static function applyFileListFilter( $propertyFileList, $propertyFilterList = array() );
// ---------------------------------------------------------------------------------------
	public static function adjustDirectorySyntax( $propertyDirectoryName );
	public static function createDirectory( $propertyDirectoryName );
	public static function relativeDirectory(  $propertyDirectoryName, $propertyDirectoryLocation );
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
class ClassSystemDirectory implements InterfaceSystemDirectory {
	public static function getFileList( $propertyDirectoryName, $propertyFileTypeList = array(), $isRecursive = false ) {
		$getFileList = array();
		if( !is_object( $directoryHandler = dir( $propertyDirectoryName ) ) ) {
			return false;
		}
		while ( false !== ( $directoryEntryName = $directoryHandler->read() ) ) {
			if( $directoryEntryName != '.' && $directoryEntryName != '..' ) {
				if( is_dir( $propertyDirectoryName.'/'.$directoryEntryName ) ) {
					if( $isRecursive ) {
						$getFileList = array_merge( $getFileList, (array)self::getFileList( $propertyDirectoryName.'/'.$directoryEntryName, $propertyFileTypeList, $isRecursive ) );
					}
				} else {
					if( ! empty( $propertyFileTypeList ) ) {
						$directoryEntryFileType = explode( '.', $directoryEntryName );
						if( in_array( array_pop( $directoryEntryFileType ), (array)$propertyFileTypeList ) ) {
							$isMatch = true;
						} else {
							$isMatch = false;
						}
					} else {
						$isMatch = true;
					}
					if( $isMatch ) {
						$getFileList[] = new ClassSystemFile( $propertyDirectoryName.'/'.$directoryEntryName );
					}
				}
			}
		}
		$directoryHandler->close();
		return $getFileList;
	}
	public static function applyFileListFilter( $propertyFileList, $propertyFilterList = array() ) {
		foreach( (array)$propertyFileList as $indexFileList => $CoreSystemFile ) {
			foreach( (array)$propertyFilterList as $CoreSystemFileMethod => $filterRegExp ) {
				if( !preg_match( '!'.$filterRegExp.'!is', $CoreSystemFile->$CoreSystemFileMethod() ) ) {
					unset( $propertyFileList[$indexFileList] );
					break;
				}
			}
		}
		return $propertyFileList;
	}
// ---------------------------------------------------------------------------------------
	// Convert e.g Path1\Path2//Path3/../Path4 -> Path1/Path2/Path4/
	public static function adjustDirectorySyntax( $propertyDirectoryName ) {
		// CONVERT \ TO /
		$propertyDirectoryName = str_replace( array('\\','//'), '/', $propertyDirectoryName );
		// RESOLVE "../"
		$countRelativeLevel = substr_count( $propertyDirectoryName, '../' );
		for( $runRelativeLevel = 0; $runRelativeLevel < $countRelativeLevel; $runRelativeLevel++ ) {
			$propertyDirectoryName = preg_replace( '!\/?[^\/]*?\/\.\.!is', '', $propertyDirectoryName );
		}
		// HANDLE TRAILING /
		if( substr( $propertyDirectoryName, -1, 1 ) != '/' ) {
			$propertyDirectoryName .= '/';
		}
		// BUILD CORRECT PATH
		return str_replace( '//', '/', $propertyDirectoryName );
	}
	public static function createDirectory( $propertyDirectoryName ) {
		$directoryList = explode( "/", self::adjustDirectorySyntax( $propertyDirectoryName ) );
		$directoryLocation = '';
		foreach( (array)$directoryList as $directoryName ) {
			$directoryLocation .= $directoryName;
			if( !empty( $directoryLocation ) ) {
				$directoryLocation = self::adjustDirectorySyntax( $directoryLocation );
				if( !is_dir( $directoryLocation ) ) {
					@mkdir( $directoryLocation );
				}
			}
		}
		return $directoryLocation;
	}
	public static function relativeDirectory( $propertyDirectoryName, $propertyDirectoryLocation ) {
		// Adjust Path2Relative
		if( is_file( $propertyDirectoryName ) ) {
			$propertyDirectoryName = self::adjustDirectorySyntax( dirname( $propertyDirectoryName ) );
			$propertyDirectoryFileName = basename( $propertyDirectoryName );
		} else {
			$propertyDirectoryName = self::adjustDirectorySyntax( $propertyDirectoryName );
			$propertyDirectoryFileName = '';
		}
		// Adjust PathBase
		if( is_file( $propertyDirectoryLocation ) ) {
			$propertyDirectoryLocation = self::adjustDirectorySyntax( dirname( $propertyDirectoryLocation ) );
		} else {
			$propertyDirectoryLocation = self::adjustDirectorySyntax( $propertyDirectoryLocation );
		}

		$propertyDirectoryList = explode('/',$propertyDirectoryName);
		$propertyDirectoryLocationList = explode('/',$propertyDirectoryLocation);

		$relativeDirectory = '';
		foreach( (array)$propertyDirectoryList as $indexDirectory => $propertyDirectory ) {
			if( isset($propertyDirectoryLocationList[$indexDirectory]) && $propertyDirectoryLocationList[$indexDirectory] == $propertyDirectory ) {
				$relativeDirectory .= $propertyDirectory.'/../';
			} else {
				$relativeDirectory .= $propertyDirectory.'/';
			}
		}
		return substr( preg_replace('!/{1}$!is', '', self::adjustDirectorySyntax( $relativeDirectory ).$propertyDirectoryFileName ), 1);
	}
}
?>