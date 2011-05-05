<?php
/**
 * SystemDirectory
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
 * @subpackage System
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
interface InterfaceSystemDirectory {
	public static function getFileList( $propertyDirectoryName, $propertyFileTypeList = array(), $isRecursive = false );
	public static function applyFileListFilter( $propertyFileList, $propertyFilterList = array() );
// ---------------------------------------------------------------------------------------
	public static function adjustDirectorySyntax( $Directory, $TrailingSeparator = true, $DirectorySeparator = ClassSystemDirectory::DIRECTORY_SEPARATOR_SLASH  );
	public static function createDirectory( $propertyDirectoryName );
	public static function relativeDirectory(  $propertyDirectoryName, $propertyDirectoryLocation );
}
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
class ClassSystemDirectory implements InterfaceSystemDirectory {
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @param array $propertyFileTypeList
	 * @param bool $isRecursive
	 * @return \AIOSystem\Core\ClassSystemFile[]|bool
	 */
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
						$getFileList[] = System::File( $propertyDirectoryName.'/'.$directoryEntryName );
					}
				}
			}
		}
		$directoryHandler->close();
		return $getFileList;
	}
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @param bool $isRecursive
	 * @return array|bool
	 * @todo getDirectoryList
	 */
	public static function getDirectoryList( $propertyDirectoryName, $isRecursive = false ) {
		$getDirectoryList = array();
		if( !is_object( $directoryHandler = dir( $propertyDirectoryName ) ) ) {
			return false;
		}
		while ( false !== ( $directoryEntryName = $directoryHandler->read() ) ) {
			if( $directoryEntryName != '.' && $directoryEntryName != '..' ) {
				if( is_dir( $propertyDirectoryName.'/'.$directoryEntryName ) ) {
					if( $isRecursive ) {
						$getDirectoryList = array_merge( $getDirectoryList, (array)self::getDirectoryList( $propertyDirectoryName.$directoryEntryName, $isRecursive ) );
					}
						$getDirectoryList[] = self::adjustDirectorySyntax( $propertyDirectoryName.'/'.$directoryEntryName );
				}
			}
		}
		$directoryHandler->close();
		return $getDirectoryList;
	}
	/**
	 * @static
	 * @param ClassSystemFile[] $propertyFileList
	 * @param array $propertyFilterList
	 * @return ClassSystemFile[]
	 */
	public static function applyFileListFilter( $propertyFileList, $propertyFilterList = array() ) {
		foreach( (array)$propertyFileList as $indexFileList => $CoreSystemFile ) {
			foreach( (array)$propertyFilterList as $CoreSystemFileMethod => $filterRegExp ) {
				if( !preg_match( '/'.$filterRegExp.'/is', $CoreSystemFile->$CoreSystemFileMethod() ) ) {
					unset( $propertyFileList[$indexFileList] );
					break;
				}
			}
		}
		return $propertyFileList;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @return string
	 *
	 * Convert e.g Path1\Path2//Path3/../Path4 -> Path1/Path2/Path4/
	 */
	const DIRECTORY_SEPARATOR_SLASH = '/';
	const DIRECTORY_SEPARATOR_BACKSLASH = '\\';
	public static function adjustDirectorySyntax( $Directory, $TrailingSeparator = true, $DirectorySeparator = ClassSystemDirectory::DIRECTORY_SEPARATOR_SLASH ) {
		switch( $DirectorySeparator ) {
			case self::DIRECTORY_SEPARATOR_SLASH: {
				// CONVERT \ TO /
				$Directory = str_replace( array('\\','//'), '/', $Directory );
				// RESOLVE "../"
				$countRelativeLevel = substr_count( $Directory, '../' );
				for( $runRelativeLevel = 0; $runRelativeLevel < $countRelativeLevel; $runRelativeLevel++ ) {
					$Directory = preg_replace( '!\/?[^\/]*?\/\.\.!is', '', $Directory, 1 );
				}
				// HANDLE TRAILING /
				if( $TrailingSeparator == true )
				if( substr( $Directory, -1, 1 ) != '/' && !is_file( $Directory ) ) {
					$Directory .= '/';
				}
				// BUILD CORRECT PATH
				return str_replace( '//', '/', $Directory );
			}
			case self::DIRECTORY_SEPARATOR_BACKSLASH: {
				// CONVERT / TO \
				$Directory = str_replace( array('//','/','\\\\'), '\\', $Directory );
				// RESOLVE "..\"
				$countRelativeLevel = substr_count( $Directory, '..\\' );
				for( $runRelativeLevel = 0; $runRelativeLevel < $countRelativeLevel; $runRelativeLevel++ ) {
					$Directory = preg_replace( '!\\\\?[^\\\\]*?\\\\\.\.!is', '', $Directory, 1 );
				}
				// HANDLE TRAILING \
				if( $TrailingSeparator == true )
				if( substr( $Directory, -1, 1 ) != '\\' && !is_file( $Directory ) ) {
					$Directory .= '\\';
				}
				// BUILD CORRECT PATH
				return str_replace( '\\\\', '\\', $Directory );
			}
		}
	}
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @return string
	 */
	public static function createDirectory( $propertyDirectoryName ) {
		$directoryList = explode( "/", self::adjustDirectorySyntax( $propertyDirectoryName ) );
		$directoryLocation = '';
		foreach( (array)$directoryList as $directoryName ) {
			$directoryLocation .= $directoryName;
			if( !empty( $directoryLocation ) ) {
				if( substr( $_SERVER['DOCUMENT_ROOT'], 0, strlen( $directoryLocation ) ) != $directoryLocation ) {
					$directoryLocation = self::adjustDirectorySyntax( $directoryLocation );
					if( !is_dir( $directoryLocation ) ) {
						if( false == @mkdir( $directoryLocation ) ) {
							trigger_error('Could not create directory! '.$directoryLocation );
						}
					}
				} else {
					$directoryLocation .= '/';
				}
			} else {
				$directoryLocation .= '/';
			}
		}
		return $directoryLocation;
	}
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @param string $propertyDirectoryLocation
	 * @return string
	 */
	public static function relativeDirectory( $propertyDirectoryName, $propertyDirectoryLocation ) {
		// Adjust Path2Relative
		if( is_file( $propertyDirectoryName ) ) {
			$propertyDirectoryFileName = basename( $propertyDirectoryName );
			$propertyDirectoryName = self::adjustDirectorySyntax( dirname( $propertyDirectoryName ) );
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
