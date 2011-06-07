<?php
/**
 * UploadFile
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
 * @package AIOSystem\Module
 * @subpackage UploadFile
 */
namespace AIOSystem\Module\Upload;
/**
 * @package AIOSystem\Module
 * @subpackage UploadFile
 */
interface InterfaceUploadFile {
	public function SaveTo( $Destination, $Overwrite = false );
	public function Field();
	public function Name();
	public function Size();
	public function Type();
	public function Location();
	public function Error();
	public function ErrorMessage();
}
/**
 * @package AIOSystem\Module
 * @subpackage UploadFile
 */
class UploadFile implements InterfaceUploadFile {
	private $Field = null;
	private $Name = null;
	private $Size = null;
	private $Type = null;
	private $Location = null;
	private $Error = 0;

	public function __construct( $Field, $Name, $Size, $Type, $Location, $Error ) {
		$this->Field = $Field;
		$this->Name = $Name;
		$this->Size = $Size;
		$this->Type = $Type;
		$this->Location = $Location;
		$this->Error = $Error;
	}

	/**
	 * @param string $Destination
	 * @return bool|int
	 */
	public function SaveTo( $Destination, $Overwrite = false ) {
		if( $this->Error() != 0 ) {
			return $this->Error();
		} else {
			if( $Overwrite === false && file_exists( $Destination.DIRECTORY_SEPARATOR.$this->Name() ) ) {
				return false;
			} else {
				return move_uploaded_file( $this->Location(), $Destination.DIRECTORY_SEPARATOR.$this->Name() );
			}
		}
	}

	public function Field() {
		return $this->Field;
	}
	public function Name() {
		return $this->Name;
	}
	public function Size() {
		return $this->Size;
	}
	public function Type() {
		return $this->Type;
	}
	public function Location() {
		return $this->Location;
	}
	public function Error() {
		return $this->Error;
	}

	public function ErrorMessage() {
		$Message = parse_ini_file( __DIR__.DIRECTORY_SEPARATOR.'Message.ini', false );
		switch( $this->Error() ) {
			case UPLOAD_ERR_OK: {
				return $Message['UPLOAD_ERR_OK']; break;
			}
			case UPLOAD_ERR_INI_SIZE: {
				return $Message['UPLOAD_ERR_INI_SIZE']; break;
			}
			case UPLOAD_ERR_FORM_SIZE: {
				return $Message['UPLOAD_ERR_FORM_SIZE']; break;
			}
			case UPLOAD_ERR_PARTIAL: {
				return $Message['UPLOAD_ERR_PARTIAL']; break;
			}
			case (UPLOAD_ERR_NO_FILE || 4): {
				return $Message['UPLOAD_ERR_NO_FILE']; break;
			}
			case UPLOAD_ERR_NO_TMP_DIR: {
				return $Message['UPLOAD_ERR_NO_TMP_DIR']; break;
			}
			case UPLOAD_ERR_CANT_WRITE: {
				return $Message['UPLOAD_ERR_CANT_WRITE']; break;
			}
			case UPLOAD_ERR_EXTENSION: {
				return $Message['UPLOAD_ERR_EXTENSION']; break;
			}
			default: {
				return false; break;
			}
		}
	}
}
?>
