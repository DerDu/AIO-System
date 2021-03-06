<?php
/**
 * SystemWrite
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
interface InterfaceSystemWrite {
	public static function writeFile( $propertyFileName = null, $propertyFileContent = null, $_writeMode = 'wb' );
}
/**
 * @package AIOSystem\Core
 * @subpackage System
 */
class ClassSystemWrite implements InterfaceSystemWrite {
	private static $_writeCacheDirectory = null;
	private static $_writeModeList = array( 'A', 'W', 'WB' );
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @throws \Exception
	 * @param null|string $propertyFileName
	 * @param null|string $propertyFileContent
	 * @param string $_writeMode
	 * @return boolean
	 */
	public static function writeFile( $propertyFileName = null, $propertyFileContent = null, $_writeMode = 'wb' ) {
		if( self::_writeMode( strtoupper( $_writeMode ) ) === false ) {
			throw new \Exception( 'Write-Mode failed!' );
		}
		switch( strtoupper( $_writeMode ) ) {
			case 'A': {
				if( ( $writeFileHandler = @fopen( $propertyFileName, 'a' ) ) !== false	) {
					if( @fwrite( $writeFileHandler, $propertyFileContent ) === false ) {
						return false;
					}
					if( @fclose( $writeFileHandler ) === false ) {
						return false;
					}
					return true;
				}
				return false;
				break;
			}
			default: {
				// OPEN CACHE
				if( ( $writeCacheHandler = @fopen( ( $writeCacheFile = self::_writeCache() ), $_writeMode ) ) === false	) {
					throw new \Exception( 'Cache-Access failed!' );
				}
				// LOCK / WRITE TO CACHE
				$writeCacheFileTimeout = 15;
				while( @flock( $writeCacheHandler, LOCK_EX | LOCK_NB ) === false && $writeCacheFileTimeout > 0 ) {
					@usleep( round( rand( 1,1000 )*1000 ) );
					$writeCacheFileTimeout--;
				}
				if( ! $writeCacheFileTimeout > 0 ) {
					throw new \Exception( 'Cache-Lock failed!' );
				}
				if( @fwrite( $writeCacheHandler, $propertyFileContent ) === false ) {
					throw new \Exception( 'Cache-Write failed!' );
				}
				// UNLOCK / CLOSE CACHE
				if( @flock( $writeCacheHandler, LOCK_UN ) === false ) {
					throw new \Exception( 'Cache-UnLock failed!' );
				}
				if( @fclose( $writeCacheHandler ) === false ) {
					throw new \Exception( 'Cache-Close failed!' );
				}
				// WRITE CACHE TO FILE
				// Cause: Unlink not needed ?
				// [Fix] Windows XAMPP PHP 5 Error -> Unlink necessary
				/*
				if( self::removeFile( $propertyFileName ) === false ) {
					throw new \Exception( 'File-UnLink failed!' );
				}*/

				$Timeout = 15;
				while( ( $Check = self::removeFile( $propertyFileName ) ) === false && $Timeout > 0 ) {
					@usleep( round( rand( 1,1000 )*1000 ) );
					$Timeout--;
				}
				if( $Check === false ) {
					throw new \Exception( 'File-UnLink failed!' );
				}

				$Timeout = 15;
				while( ( $Check = self::renameFile( $writeCacheFile, $propertyFileName ) ) === false && $Timeout > 0 ) {
					@usleep( round( rand( 1,1000 )*1000 ) );
					$Timeout--;
				}
				if( $Check === false ) {
					throw new \Exception( 'File-Write failed!' );
				}
				/*
				if( is_file( $propertyFileName ) ) {
					if( @unlink( $propertyFileName ) === false ) {
						throw new \Exception( 'File-UnLink failed!' );
					}
				}*//*
				if( @rename( $writeCacheFile, $propertyFileName ) === false ) {
					throw new \Exception( 'File-Write failed!' );
				}*/
				return true;
			}
		}
	}
	public static function renameFile( $File, $NewFile ) {
		return self::_writeLockRename( $File, $NewFile );
	}
	private static function _writeLockRename( $File, $NewFile, $Timeout = 15 ) {
		if( is_file( $File ) ) {
			if(
				( false !== ( $HandlerA = @fopen( $File, "r" ) ) )
				&& ( false !== ( $HandlerB = @fopen( $NewFile, "w" ) ) )
			){
				$TimeoutA = $TimeoutB = $Timeout;
				while( @flock( $HandlerA, LOCK_EX | LOCK_NB ) === false && $TimeoutA > 0 ) {
					@usleep( round( rand( 1,1000 )*1000 ) );
					$TimeoutA--;
				}
				if( $TimeoutA > 0 ) {
					while( @flock( $HandlerB, LOCK_EX | LOCK_NB ) === false && $TimeoutB > 0 ) {
						@usleep( round( rand( 1,1000 )*1000 ) );
						$TimeoutB--;
					}
					if( $TimeoutB > 0 ) {
						@flock( $HandlerA, LOCK_UN );
						@fclose( $HandlerA );
						@flock( $HandlerB, LOCK_UN );
						@fclose( $HandlerB );
						return @rename( $File, $NewFile );
					}
				}
				@flock( $HandlerA, LOCK_UN );
				@fclose( $HandlerA );
				@fclose( $HandlerB );
			}
		}
		return false;
	}
	public static function removeFile( $File ) {
		return self::_writeLockUnlink( $File );
	}
	private static function _writeLockUnlink( $File, $Timeout = 15 ) {
		if( is_file( $File ) ) {
			if( false !== ( $Handler = @fopen( $File, "w" ) ) ) {
				while( @flock( $Handler, LOCK_EX | LOCK_NB ) === false && $Timeout > 0 ) {
					@usleep( round( rand( 1,1000 )*1000 ) );
					$Timeout--;
				}
				if( $Timeout > 0 ) {
					@flock( $Handler, LOCK_UN );
					@fclose( $Handler );
					return @unlink( $File );
				}
				@fclose( $Handler );
			}
		} else {
			return true;
		}
		return false;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $_writeMode
	 * @return bool
	 */
	private static function _writeMode( $_writeMode ) {
		if( count( array_intersect( (array)$_writeMode, self::$_writeModeList ) ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * @static
	 * @throws \Exception
	 * @return string
	 */
	private static function _writeCache() {
		if( self::$_writeCacheDirectory === null ) {
			self::$_writeCacheDirectory = ini_get('upload_tmp_dir');
		}
		if( ( $writeCacheFile = @tempnam( self::$_writeCacheDirectory, 'write' ) ) === false ) {
			throw new \Exception('Cache-Access failed!');
		}
		return $writeCacheFile;
	}
}
?>
