<?php
/**
 * SystemFile
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
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
interface InterfaceSystemFile {
	public static function Instance( $propertyFileName );
// ---------------------------------------------------------------------------------------
	public function propertyFileName( $propertyFileName = null );
	public function propertyFilePath( $propertyFilePath = null );
	public function propertyFileLocation( $propertyFileLocation = null );
	public function propertyFileSize( $propertyFileSize = null );
	public function propertyFileTime( $propertyFileTime = null );
	public function FileContent( $propertyFileContent = null );
	public function Hash();
// ---------------------------------------------------------------------------------------
	public function readFile( $ParsePhp = false );
	public function writeFile( $_writeMode = 'wb' );
	public function writeFileAs( $propertyFileLocation, $_writeMode = 'wb' );
	public function moveFile( $propertyFileLocation );
	public function copyFile( $propertyFileLocation );
	public function removeFile();
	public function touchFile();
}
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
class ClassSystemFile implements InterfaceSystemFile {
	/** @var null|string $_propertyFileName */
	private $_propertyFileName = null;
	/** @var null|string $_propertyFilePath */
	private $_propertyFilePath = null;
	/** @var null|string $_propertyFileLocation */
	private $_propertyFileLocation = null;
	/** @var null|int $_propertyFileSize */
	private $_propertyFileSize = null;
	/** @var null|int $_propertyFileTime */
	private $_propertyFileTime = null;
	/** @var null|string $_propertyFileContent */
	private $_propertyFileContent = null;
	/** @var bool $_isChanged */
	private $_hasChanged = false;
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyFileName
	 * @return ClassSystemFile
	 */
	public static function Instance( $propertyFileName ) {
		return new ClassSystemFile( $propertyFileName );
	}
	public function __construct( $propertyFileLocation ) {
		$this->propertyFileLocation( $propertyFileLocation );
		$this->_loadFileAttributeList();
		if( ! file_exists( $this->propertyFileLocation() ) ) {
			$this->touchFile();
		}
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param string $_writeMode
	 * @return void
	 */
	public function writeFile( $_writeMode = 'wb' ) {
		if( is_array( $this->FileContent() ) ) {
			ClassSystemWrite::writeFile( $this->propertyFileLocation(), implode( PHP_EOL, $this->FileContent() ), $_writeMode );
		} else {
			ClassSystemWrite::writeFile( $this->propertyFileLocation(), $this->FileContent(), $_writeMode );
		}
		$this->_loadFileAttributeList();
		$this->_hasChanged = false;
	}
	/**
	 * @param string $propertyFileLocation
	 * @param string $_writeMode
	 * @return void
	 */
	public function writeFileAs( $propertyFileLocation, $_writeMode = 'wb' ) {
		if( is_array( $this->FileContent() ) ) {
			ClassSystemWrite::writeFile( $propertyFileLocation, implode( PHP_EOL, $this->FileContent() ), $_writeMode );
		} else {
			ClassSystemWrite::writeFile( $propertyFileLocation, $this->FileContent(), $_writeMode );
		}
		$this->propertyFileLocation( $propertyFileLocation );
		$this->_loadFileAttributeList();
		$this->_hasChanged = false;
	}
	/**
	 * @param bool $ParsePhp
	 * @return string
	 */
	public function readFile( $ParsePhp = false, $ParseArray = false ) {
		if( is_file( $this->propertyFileLocation() ) ) {
			if( $ParsePhp ) {
				ob_start(); include( $this->propertyFileLocation() );
				$this->FileContent( ob_get_clean() );
			} elseif( $ParseArray ) {
				$this->FileContent( file( $this->propertyFileLocation() ) );
			} else {
				$this->FileContent( file_get_contents( $this->propertyFileLocation() ) );
			}
		} else {
			if( $ParseArray ) {
				$this->FileContent( array() );
			} else {
				$this->FileContent('');
			}
		}
		$this->_hasChanged = false;
		return $this->FileContent();
	}
	/**
	 * @param string $propertyFileLocation
	 * @return boolean
	 */
	public function moveFile( $propertyFileLocation ) {
		if( file_exists( $this->propertyFileLocation() ) ) {
			if( $Return = ClassSystemWrite::renameFile( $this->propertyFileLocation(), $propertyFileLocation ) ) {
				$this->propertyFileLocation( $propertyFileLocation );
				$this->_loadFileAttributeList();
			}
			return $Return;
			/*
			if( rename( $this->propertyFileLocation(), $propertyFileLocation ) ) {
				$this->propertyFileLocation( $propertyFileLocation );
				$this->_loadFileAttributeList();
			}*/
		}
		return false;
	}
	/**
	 * @param string $propertyFileLocation
	 * @return void
	 */
	public function copyFile( $propertyFileLocation ) {
		if( file_exists( $this->propertyFileLocation() ) ) {
			if( copy( $this->propertyFileLocation(), $propertyFileLocation ) ) {
				$this->propertyFileLocation( $propertyFileLocation );
				$this->_loadFileAttributeList();
			}
		}
	}
	/**
	 * @return boolean
	 */
	public function removeFile() {
		if( file_exists( $this->propertyFileLocation() ) ) {
			$Return = ClassSystemWrite::removeFile( $this->propertyFileLocation() );
			//unlink( $this->propertyFileLocation() );
			unset($this);
			return $Return;
		}
		return false;
	}
	/**
	 * @return void
	 */
	public function touchFile() {
		if( strlen( $this->propertyFileLocation() ) > 0 ) {
			fclose( fopen( $this->propertyFileLocation(), 'a' ) );
			$this->_loadFileAttributeList();
		}
	}

	public function Hash() {
		if( file_exists( $this->propertyFileLocation() ) ) {
			return sha1_file( $this->propertyFileLocation() );
		} else {
			return null;
		}
	}

// ---------------------------------------------------------------------------------------
	/**
	 * @param null|string $propertyFileName
	 * @return null|string
	 */
	public function propertyFileName( $propertyFileName = null ) {
		if( $propertyFileName !== null ) {
			$this->_propertyFileName = $propertyFileName;
		} return $this->_propertyFileName;
	}
	/**
	 * @param null|string $propertyFilePath
	 * @return null|string
	 */
	public function propertyFilePath( $propertyFilePath = null ) {
		if( $propertyFilePath !== null ) {
			$this->_propertyFilePath = str_replace( '\\', '/', $propertyFilePath );
		} return $this->_propertyFilePath;
	}
	/**
	 * @param null|string $propertyFileLocation
	 * @return null|string
	 */
	public function propertyFileLocation( $propertyFileLocation = null ) {
		if( $propertyFileLocation !== null ) {
			$this->_propertyFileLocation = ClassSystemDirectory::adjustDirectorySyntax( $propertyFileLocation, false, DIRECTORY_SEPARATOR );
		} return $this->_propertyFileLocation;
	}
	/**
	 * @param null|int $propertyFileSize
	 * @return null|int
	 */
	public function propertyFileSize( $propertyFileSize = null ) {
		if( $propertyFileSize !== null ) {
			$this->_propertyFileSize = $propertyFileSize;
		} return $this->_propertyFileSize;
	}
	/**
	 * @param null|int $propertyFileTime
	 * @return null|int
	 */
	public function propertyFileTime( $propertyFileTime = null ) {
		if( $propertyFileTime !== null ) {
			$this->_propertyFileTime = $propertyFileTime;
		} return $this->_propertyFileTime;
	}
	/**
	 * @param null|string $propertyFileContent
	 * @return null|string
	 */
	public function FileContent( $propertyFileContent = null ) {
		if( $propertyFileContent !== null ) {
			if( $this->_propertyFileContent !== null ) $this->_hasChanged = true;
			$this->_propertyFileContent = $propertyFileContent;
		}
		if( $this->_propertyFileContent === null ) {
			$this->readFile();
		} return $this->_propertyFileContent;
	}
	public function FileContentArray( $propertyFileContent = null ) {
		if( $propertyFileContent !== null ) {
			if( $this->_propertyFileContent !== null ) $this->_hasChanged = true;
			$this->_propertyFileContent = $propertyFileContent;
		}
		if( $this->_propertyFileContent === null ) {
			$this->readFile( false, true );
		} return $this->_propertyFileContent;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @return void
	 */
	private function _loadFileAttributeList() {
		$this->propertyFileName( basename( $this->propertyFileLocation() ) );
		$this->propertyFilePath( dirname( $this->propertyFileLocation() ) );
		if( file_exists( $this->propertyFileLocation() ) ) {
			$this->propertyFileSize( filesize( $this->propertyFileLocation() ) );
			$this->propertyFileTime( filemtime( $this->propertyFileLocation() ) );
		}
	}
// ---------------------------------------------------------------------------------------
	function __toString() {
		return $this->propertyFileLocation()."\t".date('YmdHis',$this->propertyFileTime())."\t".($this->propertyFileSize()/1024).'KB';
	}
}
?>
