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
	public static function getFileList( $propertyDirectoryName, $propertyFileTypeList = array(), $isRecursive = false, $useFileObject = true );
	public static function applyFileListFilter( $propertyFileList, $propertyFilterList = array() );
// ---------------------------------------------------------------------------------------
	public static function adjustDirectorySyntax( $Directory, $TrailingSeparator = true, $DirectorySeparator = ClassSystemDirectory::DIRECTORY_SEPARATOR_SLASH  );
	public static function createDirectory( $propertyDirectoryName );
	public static function removeDirectory( $propertyDirectoryName );
	public static function relativeDirectory(  $propertyDirectoryName, $propertyDirectoryLocation );
}
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
class ClassSystemDirectory implements InterfaceSystemDirectory {
	const DEBUG = true;
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @param array $propertyFileTypeList
	 * @param bool|int $isRecursive - integer = max. level of depth, bool = toggle full depth
	 * @return \AIOSystem\Core\ClassSystemFile[]|bool
	 */
	public static function getFileList( $propertyDirectoryName, $propertyFileTypeList = array(), $isRecursive = false, $useFileObject = true ) {
		$getFileList = array();
		if( !is_object( $directoryHandler = @dir( $propertyDirectoryName ) ) ) {
			return false;
		}
		while ( false !== ( $directoryEntryName = $directoryHandler->read() ) ) {
			if( $directoryEntryName != '.' && $directoryEntryName != '..' ) {
				if( is_dir( $propertyDirectoryName.'/'.$directoryEntryName ) ) {
					if( $isRecursive ) {
						if( is_num( $isRecursive ) ) {
							$isRecursive--;
						}
						$getFileList = array_merge( $getFileList, (array)self::getFileList( $propertyDirectoryName.'/'.$directoryEntryName, $propertyFileTypeList, $isRecursive, $useFileObject ) );
					}
				} else {
					if( ! empty( $propertyFileTypeList ) ) {
						$directoryEntryFileType = explode( '.', $directoryEntryName );
						$directoryEntryFileType = array_pop( $directoryEntryFileType );
						if( is_array( $propertyFileTypeList ) ) {
							if( in_array( $directoryEntryFileType, (array)$propertyFileTypeList ) ) {
								$isMatch = true;
							} else {
								$isMatch = false;
							}
						} else {
							if( preg_match( '!'.$propertyFileTypeList.'!is', $directoryEntryFileType ) ) {
								$isMatch = true;
							} else {
								$isMatch = false;
							}
						}
					} else {
						$isMatch = true;
					}
					if( $isMatch ) {
						if( $useFileObject ) {
							$getFileList[] = System::File( $propertyDirectoryName.'/'.$directoryEntryName );
						} else {
							$getFileList[] = $propertyDirectoryName.'/'.$directoryEntryName;
						}
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
		if( !is_object( $directoryHandler = @dir( $propertyDirectoryName ) ) ) {
			return false;
		}
		while ( false !== ( $directoryEntryName = $directoryHandler->read() ) ) {
			if( $directoryEntryName != '.' && $directoryEntryName != '..' ) {
				if( is_dir( $propertyDirectoryName.DIRECTORY_SEPARATOR.$directoryEntryName ) ) {
					if( $isRecursive ) {
						$getDirectoryList = array_merge( $getDirectoryList, (array)self::getDirectoryList( $propertyDirectoryName.DIRECTORY_SEPARATOR.$directoryEntryName, $isRecursive ) );
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
		return false;
	}
	/**
	 * @static
	 * @param string $propertyDirectoryName
	 * @return string
	 */
	public static function createDirectory( $propertyDirectoryName ) {
		self::FixIISDocumentRoot();
		$directoryList = explode( "/", self::adjustDirectorySyntax( $propertyDirectoryName ) );
		$directoryLocation = '';
		foreach( (array)$directoryList as $directoryName ) {
			$directoryLocation .= $directoryName;
			if( !empty( $directoryLocation ) ) {
				if( substr( $_SERVER['DOCUMENT_ROOT'], 0, strlen( $directoryLocation ) ) != $directoryLocation ) {
					$directoryLocation = self::adjustDirectorySyntax( $directoryLocation );
					if( !is_dir( $directoryLocation ) ) {
						if( false == @mkdir( $directoryLocation ) ) {
							Event::Error(0,'Could not create directory! '.$directoryLocation,__FILE__,__LINE__);
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

	public static function removeDirectory( $propertyDirectoryName, $Recursive = false ) {
		self::FixIISDocumentRoot();
		$directoryLocation = self::adjustDirectorySyntax( $propertyDirectoryName, false, DIRECTORY_SEPARATOR );
		if( false === $Recursive ) {
			if( false == @rmdir( $directoryLocation ) ) {
				Event::Error(0,'Could not remove directory! '.$directoryLocation,__FILE__,__LINE__);
			}
		} else {
			/** @var \AIOSystem\Core\ClassSystemFile[] $FileList */
			$FileList = self::getFileList( $directoryLocation, array(), true );
			/** @var \AIOSystem\Core\ClassSystemFile $File */
			foreach( (array)$FileList as $File ) {
				$File->removeFile();
			}
			$DirectoryList = self::getDirectoryList( $directoryLocation, true );
			foreach( (array)$DirectoryList as $Directory ) {
				self::removeDirectory( $Directory, false );
			}
			self::removeDirectory( $directoryLocation, false );
		}
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

	public static function DocumentRoot() {
		self::FixIISDocumentRoot();
		return $_SERVER['DOCUMENT_ROOT'];
	}

	public static function CurrentDirectory() {
		return self::DocumentRoot().dirname($_SERVER['SCRIPT_NAME']);
	}

	/**
	 * Problem to fix: The $_SERVER["DOCUMENT_ROOT"] is empty in IIS.
	 *
	 * Based on: http://fyneworks.blogspot.com/2007/08/php-documentroot-in-iis-windows-servers.html
	 * Added by Diego, 13-AUG-2007.
	 *
	 * @static
	 * @return void
	 */
	private static function FixIISDocumentRoot() {
		// let's make sure the $_SERVER['DOCUMENT_ROOT'] variable is set
		if(!isset($_SERVER['DOCUMENT_ROOT'])){
			if(isset($_SERVER['SCRIPT_FILENAME'])){
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0-strlen($_SERVER['PHP_SELF'])));
			};
		};
		if(!isset($_SERVER['DOCUMENT_ROOT'])){
			if(isset($_SERVER['PATH_TRANSLATED'])){
				$_SERVER['DOCUMENT_ROOT'] = str_replace( '\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0-strlen($_SERVER['PHP_SELF'])));
			};
		};
		// $_SERVER['DOCUMENT_ROOT'] is now set - you can use it as usual...
	}
}
?>
