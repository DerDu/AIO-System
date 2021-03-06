<?php
/**
 * This file contains the API:System
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
use \AIOSystem\Core\ClassSystemFile as AIOSystemFile;
use \AIOSystem\Core\ClassSystemDirectory as AIOSystemDirectory;
use \AIOSystem\Core\ClassDebugTimer as AIODebugTimer;
use \AIOSystem\Module\System\SysInfo;
/**
 * @package AIOSystem\Api
 */
class System {
	/**
	 * @param string $FileLocation
	 * @return \AIOSystem\Core\ClassSystemFile
	 */
	public static function File( $FileLocation ) {
		return AIOSystemFile::Instance( $FileLocation );
	}
	/**
	 * @param string $Directory
	 * @param array $Type
	 * @param bool|int $Recursive - integer = max. level of depth, bool = toggle full depth
	 * @return \AIOSystem\Core\ClassSystemFile[]
	 */
	public static function FileList( $Directory, $Type = array(), $Recursive = false, $useFileObject = true ) {
		return AIOSystemDirectory::getFileList( $Directory, $Type, $Recursive, $useFileObject );
	}
	/**
	 * @param \AIOSystem\Core\ClassSystemFile[] $FileList
	 * @param array $Filter
	 * @return \AIOSystem\Core\ClassSystemFile[]
	 */
	public static function FileListFilter( $FileList, $Filter = array() ) {
		return AIOSystemDirectory::applyFileListFilter( $FileList, $Filter );
	}
	/**
	 * @static
	 * @param string $Directory
	 * @param bool $Recursive
	 */
	public static function DirectoryList( $Directory, $Recursive = false ) {
		return AIOSystemDirectory::getDirectoryList( $Directory, $Recursive );
	}
	/**
	 * @static
	 * @param string $Directory
	 * @return string
	 */
	public static function CreateDirectory( $Directory ) {
		return AIOSystemDirectory::createDirectory( $Directory );
	}
	/**
	 * @static
	 * @param string $Directory
	 * @return string
	 */
	public static function RemoveDirectory( $Directory, $Recursive = false ) {
		return AIOSystemDirectory::removeDirectory( $Directory, $Recursive );
	}

	const DIRECTORY_SEPARATOR_SLASH = AIOSystemDirectory::DIRECTORY_SEPARATOR_SLASH;
	const DIRECTORY_SEPARATOR_BACKSLASH = AIOSystemDirectory::DIRECTORY_SEPARATOR_BACKSLASH;
	/**
	 * @static
	 * @param string $Directory
	 * @param boolean $Trailing
	 * @param string $Separator
	 * @return string
	 */
	public static function DirectorySyntax( $Directory, $Trailing = true, $Separator = self::DIRECTORY_SEPARATOR_SLASH ) {
		return AIOSystemDirectory::adjustDirectorySyntax( $Directory, $Trailing, $Separator );
	}
	/**
	 * @static
	 * @param string $Directory
	 * @param string $Location
	 * @return string
	 */
	public static function RelativeDirectory( $Directory, $Location ) {
		return AIOSystemDirectory::relativeDirectory( $Directory, $Location );
	}
	/**
	 * @return string
	 */
	public static function DirectoryRoot() {
		return AIOSystemDirectory::DocumentRoot();
	}
	/**
	 * @return string
	 */
	public static function DirectoryCurrent() {
		return AIOSystemDirectory::CurrentDirectory();
	}
	/**
	 * @return \AIOSystem\Module\Upload\UploadFile[]
	 */
	public static function FileUpload() {
		return \AIOSystem\Module\Upload\Upload::Process();
	}

	/**
	 * @static
	 * @param string $Name
	 * @param bool $Average
	 * @return \AIOSystem\Core\ClassDebugTimer
	 */
	public static function DebugTimer( $Name = 'DebugTimer', $Average = false ) {
		return AIODebugTimer::Timer( $Name, $Average );
	}
	/**
	 * @static
	 * @return string
	 */
	public static function SysInfo() {
		return SysInfo::Instance();
	}
}
?>
