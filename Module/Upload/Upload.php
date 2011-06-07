<?php
/**
 * Upload
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
 * @subpackage Upload
 */
namespace AIOSystem\Module\Upload;
use \AIOSystem\Api\Stack;
/**
 * @package AIOSystem\Module
 * @subpackage Upload
 */
interface InterfaceUpload {
	public static function Process();
	public static function isEnabled();
	public static function getMaxFileSize();
	public static function getMaxPostSize();
}
/**
 * @package AIOSystem\Module
 * @subpackage Upload
 */
class Upload implements InterfaceUpload {
	/** @var null|\AIOSystem\Core\ClassStackQueue $FileList */
	private static $FileList = null;
	/**
	 * @static
	 * @return UploadFile[]
	 */
	public static function Process() {
		self::$FileList = Stack::Queue();
		foreach( (array)$_FILES as $FieldName => $Property ) {
			if( is_array( $Property['name'] ) ) {
				$FieldNameCount = count( $Property['name'] );
				for( $Run = 0; $Run < $FieldNameCount; $Run++ ) {
					self::$FileList->pushData(
						new UploadFile(
							$FieldName,
							$Property['name'][$Run],
							$Property['size'][$Run],
							$Property['type'][$Run],
							$Property['tmp_name'][$Run],
							$Property['error'][$Run]
						)
					);
				}
			} else {
				self::$FileList->pushData(
					new UploadFile(
						$FieldName,
						$Property['name'],
						$Property['size'],
						$Property['type'],
						$Property['tmp_name'],
						$Property['error']
					)
				);
			}
		}
		return self::$FileList->listData();
	}
	public static function isEnabled() {
		return ini_get('file_uploads');
	}
	public static function getMaxFileSize() {
		return ini_get('upload_max_filesize');
	}
	public static function getMaxPostSize() {
		return ini_get('post_max_size');
	}
}
?>
