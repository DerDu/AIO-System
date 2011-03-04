<?php
/**
 * This file contains the API:Excel
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
 *  * Neither the name of the Gerd Christian Kunze nor the names of its
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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Module\Excel\ClassPhpExcel as AioExcel;
/**
 * @package AioSystem\Api
 */
class ClassExcel {
	/**
	 * @static
	 * @param string $Worksheet
	 * @param null|string $File
	 * @return void
	 */
	public static function Open( $Worksheet = 'Worksheet', $File = null ) {
		return AioExcel::openFile( $Worksheet, $File );
	}
	/**
	 * @static
	 * @param null|string $File
	 * @return string Filename
	 */
	public static function Close( $File = null ) {
		return AioExcel::closeFile( $File );
	}
	/**
	 * @static
	 * @param null|string $Name
	 * @return string
	 */
	public static function WorksheetName( $Name = null ) {
		return AioExcel::propertyWorksheetName( $Name );
	}
	/**
	 * @static
	 * @param null|string $Name
	 * @return \PHPExcel_Worksheet
	 */
	public static function ActiveWorksheet( $Name = null ) {
		return AioExcel::activeWorksheet( $Name );
	}
	/**
	 * @static
	 * @param  string $Name
	 * @return \PHPExcel_Worksheet
	 */
	public static function CreateWorksheet( $Name ) {
		return AioExcel::createWorksheet( $Name );
	}
	/**
	 * @static
	 * @param string $Name
	 * @param mixed $Value
	 * @param null|string $Type
	 * @return void
	 */
	public static function CellValue( $Name, $Value, $Type = null ) {
		return AioExcel::cellValue( $Name, $Value, $Type );
	}
	/**
	 * @static
	 * @param string $Name
	 * @param string $File
	 * @param int $Width
	 * @param int $Height
	 * @return void
	 */
	public static function CellImage( $Name, $File, $Width, $Height ) {

	}
	/**
	 * @static
	 * @param string $Name
	 * @param array $Css
	 * @return void
	 */
	public static function CellStyle( $Name, $Css = array() ) {
		return AioExcel::cellStyle( $Name, $Css );
	}
	/**
	 * @static
	 * @param  string $Name
	 * @return array
	 */
	public static function CellName2Index( $Name ) {
		return AioExcel::convertCellNameToIndex( $Name );
	}
	/**
	 * @static
	 * @param  int $IndexX
	 * @param  int $IndexY
	 * @return string
	 */
	public static function CellIndex2Name( $IndexX, $IndexY ) {
		return AioExcel::convertCellIndexToName( $IndexX, $IndexY );
	}
}
?>