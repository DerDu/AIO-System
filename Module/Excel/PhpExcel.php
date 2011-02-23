<?php
/**
 * PHP-Excel
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
 * @package AioSystem\Module
 * @subpackage Excel
 */
namespace AioSystem\Module\Excel;
/**
 * @package AioSystem\Module
 * @subpackage Excel
 */
interface InterfacePhpExcel {
// PHPEXCEL : IO -------------------------------------------------------------------------
	public static function openFile( $propertyWorksheet = 'Worksheet', $propertyFileName = null );
	public static function closeFile( $propertyFileName = null );
// PHPEXCEL : WORKSHEET ------------------------------------------------------------------
	public static function createWorksheet( $propertyWorksheetName );
	public static function activeWorksheet( $propertyWorksheetName = null );
	public static function propertyWorksheetName( $propertyWorksheetName = null );
// PHPEXCEL : CELL -----------------------------------------------------------------------
	public static function cellValue( $propertyCellName, $propertyCellValue, $propertyCellType = null );
	public static function cellImage( $propertyCellName, $propertyFileName, $propertyWidth, $propertyHeight );
// PHPEXCEL : STYLE ----------------------------------------------------------------------
	public static function cellStyle( $propertyCellName, $propertyCssList = array() );
// PHPEXCEL : COMMON ---------------------------------------------------------------------
	public static function convertCellNameToIndex( $propertyCellName );
	public static function convertCellIndexToName( $propertyIndexX, $propertyIndexY );
}
/**
 * @package AioSystem\Module
 * @subpackage Excel
 */
class ClassPhpExcel implements InterfacePhpExcel
{
	/**
	 * @var \PHPExcel $_propertySingleton
	 */
	private static $_propertySingleton = null;
	private static $_propertyFileName = null;
	private static $_propertyTimeout = true;
// PHPEXCEL : IO -------------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyWorksheet
	 * @param null $propertyFileName
	 * @return void
	 */
	public static function openFile( $propertyWorksheet = 'Worksheet', $propertyFileName = null ) {
		if( !class_exists( 'PHPExcel' ) ) require_once(__DIR__ . '/PhpExcel/Classes/PHPExcel.php');
		self::propertyFileName( $propertyFileName );
		if( file_exists( self::propertyFileName() ) ){
			self::propertySingleton( \PHPExcel_IOFactory::load( self::propertyFileName() ) );
			self::activeWorksheet( $propertyWorksheet );
		} else {
			self::propertySingleton( new \PHPExcel() );
			self::pageSetup('PAPERSIZE_A4');
			self::pageSetup('ORIENTATION_DEFAULT');
			self::propertyWorksheetName( $propertyWorksheet );
		}
		self::propertyTimeout( true );
	}
	/**
	 * @static
	 * @param string|null $propertyFileName
	 * @return string|null
	 */
	public static function closeFile( $propertyFileName = null ) {
		switch( strtoupper( pathinfo( self::propertyFileName( $propertyFileName ), PATHINFO_EXTENSION ) ) ){
			case 'XLS': $object_phpexcel = \PHPExcel_IOFactory::createWriter( self::propertySingleton(), 'Excel5' ); break;
			case 'XLSX': $object_phpexcel = \PHPExcel_IOFactory::createWriter( self::propertySingleton(), 'Excel2007' ); break;
			default: $object_phpexcel = \PHPExcel_IOFactory::createWriter( self::propertySingleton(), 'Excel5' ); break;
		}
		if( $propertyFileName !== null ){
			$ClassSystemFile = \AioSystem\Core\ClassSystemFile::Instance( self::propertyFileName() );
			$ClassSystemFile->removeFile();
		}
		$object_phpexcel->save( self::propertyFileName() );
		self::propertyFileName('');
		self::propertySingleton('');
		self::propertyTimeout( false );
		return $propertyFileName;
	}
// PHPEXCEL : PAGE -----------------------------------------------------------------------
	/**
	 * @static
	 * @return void
	 */
	public static function pageFit() {
		$ActiveSheet = self::activeWorksheet()->getPageSetup();
		$ActiveSheet->setHorizontalCentered( true );
		$ActiveSheet->setVerticalCentered( false );
	}
	/**
	 * @static
	 * @throws \Exception
	 * @param string $propertyValue
	 * @return void
	 */
	public static function pageSetup( $propertyValue ) {
		$propertyValue = strtoupper( $propertyValue );
		$ActiveSheet = self::activeWorksheet()->getPageSetup();
		if( preg_match( '!^PAPERSIZE_!is', $propertyValue ) ) return $ActiveSheet->setPaperSize( self::_constant( $propertyValue ) );
		if( preg_match( '!^ORIENTATION_!is', $propertyValue ) ) return $ActiveSheet->setOrientation( self::_constant( $propertyValue ) );
		throw new \Exception('Value not available!');
	}
// PHPEXCEL : WORKSHEET ------------------------------------------------------------------
	/**
	 * @static
	 * @param  string $propertyWorksheetName
	 * @return \PHPExcel_Worksheet
	 */
	public static function createWorksheet( $propertyWorksheetName ) {
		self::propertySingleton()->createSheet()->setTitle( $propertyWorksheetName );
		return self::activeWorksheet( $propertyWorksheetName );
	}
	/**
	 * @static
	 * @param string|null $propertyWorksheetName
	 * @return \PHPExcel_Worksheet
	 */
	public static function activeWorksheet( $propertyWorksheetName = null ) {
		if( $propertyWorksheetName !== null ){
			$array_worksheet_names = self::propertyWorksheetNameList();
			if( ! in_array( $propertyWorksheetName, (array)$array_worksheet_names ) )
			{
				self::createWorksheet( $propertyWorksheetName );
				$array_worksheet_names = self::propertyWorksheetNameList();
			}
			$array_worksheet_names = array_flip( $array_worksheet_names );
			self::propertySingleton()->setActiveSheetIndex( $array_worksheet_names[$propertyWorksheetName] );
		}
		return self::propertySingleton()->getActiveSheet();
	}
	/**
	 * @static
	 * @param string|null $propertyWorksheetName
	 * @return \string[]
	 */
	public static function propertyWorksheetName( $propertyWorksheetName = null ) {
		if( $propertyWorksheetName !== null ){
			self::activeWorksheet()->setTitle( $propertyWorksheetName );
		}
		return self::propertyWorksheetNameList( self::propertySingleton()->getActiveSheetIndex() );
	}
	/**
	 * @static
	 * @param integer|null $propertyWorksheetIndex
	 * @return \string[]
	 */
	public static function propertyWorksheetNameList( $propertyWorksheetIndex = null ) {
		$propertyWorksheetNameList = self::propertySingleton()->getSheetNames();
		if( $propertyWorksheetIndex !== null ){
			return $propertyWorksheetNameList[$propertyWorksheetIndex];
		} else {
			return $propertyWorksheetNameList;
		}
	}
// PHPEXCEL : CELL -----------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyCellName
	 * @param mixed $propertyCellValue
	 * @param null $propertyCellType
	 * @return void
	 */
	public static function cellValue( $propertyCellName, $propertyCellValue, $propertyCellType = null ) {
		if( $propertyCellType === null ) {
			self::activeWorksheet()->setCellValue( $propertyCellName, $propertyCellValue );
		} else {
			self::activeWorksheet()->setCellValueExplicit( $propertyCellName, $propertyCellValue, self::_constant( $propertyCellType ) );
		}
	}
	/**
	 * @static
	 * @param  $propertyCellName
	 * @param  $propertyFileName
	 * @param  $propertyWidth
	 * @param  $propertyHeight
	 * @return void
	 */
	public static function cellImage( $propertyCellName, $propertyFileName, $propertyWidth, $propertyHeight ) {
	/*	$object_image = api_factory_shell::shell_image_load( $propertyFileName );
		api_factory_shell::shell_image_resize_pixel( $object_image, $propertyWidth, $propertyHeight );
		$MemoryDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
		$MemoryDrawing->setCoordinates( $propertyCellName );
		$MemoryDrawing->setImageResource( api_factory_shell::shell_image_resource( $object_image ) );
		$MemoryDrawing->setRenderingFunction( \PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT );
		$MemoryDrawing->setMimeType( \PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT );
		$MemoryDrawing->setWorksheet( self::activeWorksheet() );
		$MemoryDrawing->setResizeProportional( true );
		$MemoryDrawing->setWidthAndHeight( $propertyWidth, $propertyHeight );
*/	}
// PHPEXCEL : STYLE ----------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyCellName
	 * @param array $propertyCssList
	 * @return void
	 */
	public static function cellStyle( $propertyCellName, $propertyCssList = array() ) {
		self::activeWorksheet()->getStyle( $propertyCellName )->applyFromArray( self::_style( $propertyCssList ) );
	}
// PHPEXCEL : COMMON ---------------------------------------------------------------------
	/**
	 * @static
	 * @param string $propertyCellName
	 * @return array
	 */
	public static function convertCellNameToIndex( $propertyCellName ) {
		$coordinateFromString = \PHPExcel_Cell::coordinateFromString( $propertyCellName );
		$coordinateFromString[0] = \PHPExcel_Cell::columnIndexFromString( $coordinateFromString[0] );
		return $coordinateFromString;
	}
	/**
	 * @static
	 * @param int $propertyIndexX
	 * @param int $propertyIndexY
	 * @return string
	 */
	public static function convertCellIndexToName( $propertyIndexX, $propertyIndexY ) {
		return \PHPExcel_Cell::stringFromColumnIndex( ($propertyIndexX) ).( $propertyIndexY +1 );
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @static
	 * @param string|null $propertyFileName
	 * @return string|null
	 */
	private static function propertyFileName( $propertyFileName = null ) {
		if( $propertyFileName !== null ) self::$_propertyFileName = $propertyFileName;
		return self::$_propertyFileName;
	}
	/**
	 * @static
	 * @param null\PHPExcel $propertySingleton
	 * @return null|\PHPExcel
	 */
	private static function propertySingleton( $propertySingleton = null ) {
		if( $propertySingleton !== null ) self::$_propertySingleton = $propertySingleton;
		return self::$_propertySingleton;
	}
	/**
	 * @static
	 * @param bool $propertyTimeout
	 * @return void
	 */
	private static function propertyTimeout( $propertyTimeout = true ) {
		if( $propertyTimeout ){
			self::$_propertyTimeout = ini_get('max_execution_time');
			ini_set('max_execution_time', 0 );
		} else {
			ini_set('max_execution_time', self::$_propertyTimeout );
		}
	}
	/**
	 * @static
	 * @param array $propertyCssList
	 * @return array
	 */
	private static function _style( $propertyCssList = array() ) {
		$_style = array();
		foreach( (array)$propertyCssList as $propertyCssAttribute => $propertyCssValue ){
			$propertyCssValue = trim( strtolower( $propertyCssValue ) );
			$propertyCssAttribute = trim( strtolower( $propertyCssAttribute ) );
			switch( $propertyCssAttribute ) {
				case 'color': {
					$_style['font']['color']['rgb'] = substr( $propertyCssValue, -6 );
					break;
				}
				case 'font-weight': {
					if( $propertyCssValue == 'bold' ) $_style['font']['bold'] = true;
					break;
				}
				case 'border': {
					$propertyCssValueList = explode( ' ', $propertyCssValue );
					// THIN / THICK - Decision
					if( intval( $propertyCssValueList[0] ) > 1 ) {
						$_style['borders']['top']['style'] = self::_constant( 'BORDER_THICK' );
						$_style['borders']['right']['style'] = self::_constant( 'BORDER_THICK' );
						$_style['borders']['bottom']['style'] = self::_constant( 'BORDER_THICK' );
						$_style['borders']['left']['style'] = self::_constant( 'BORDER_THICK' );
					} else {
						$_style['borders']['top']['style'] = self::_constant( 'BORDER_THIN' );
						$_style['borders']['right']['style'] = self::_constant( 'BORDER_THIN' );
						$_style['borders']['bottom']['style'] = self::_constant( 'BORDER_THIN' );
						$_style['borders']['left']['style'] = self::_constant( 'BORDER_THIN' );
					}
					// COLOR
					$_style['borders']['top']['color']['rgb'] = substr( trim($propertyCssValueList[2]), -6 );
					$_style['borders']['right']['color']['rgb'] = substr( trim($propertyCssValueList[2]), -6 );
					$_style['borders']['bottom']['color']['rgb'] = substr( trim($propertyCssValueList[2]), -6 );
					$_style['borders']['left']['color']['rgb'] = substr( trim($propertyCssValueList[2]), -6 );
					break;
				}
				case 'number-format': {
					$_style['numberformat']['code'] = $propertyCssValue;
				}
			}
		}
		//var_dump( $propertyCssList );
		//var_dump( $_style );
		return $_style;
	}
	/**
	 * @static
	 * @throws \Exception
	 * @param  string $propertyConstant
	 * @return int|string
	 */
	private static function _constant( $propertyConstant ) {
		switch( strtoupper( $propertyConstant ) ) {
			// PAPER-ORIENTATION
			case 'ORIENTATION_DEFAULT': return \PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT;
			case 'ORIENTATION_LANDSCAPE': return \PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE;
			case 'ORIENTATION_PORTRAIT': return \PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT;
			// PAPER-SIZE
			case 'PAPERSIZE_A4': return \PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4;
			case 'PAPERSIZE_A3': return \PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3;
			// DATA-TYPE
			case 'TYPE_STRING': return \PHPExcel_Cell_DataType::TYPE_STRING;
			case 'TYPE_NUMERIC': return \PHPExcel_Cell_DataType::TYPE_NUMERIC;
			case 'TYPE_BOOL': return \PHPExcel_Cell_DataType::TYPE_BOOL;
			// STYLE-BORDER
			case 'BORDER_THIN': return \PHPExcel_Style_Border::BORDER_THIN;
			case 'BORDER_THICK': return \PHPExcel_Style_Border::BORDER_THICK;
			case 'BORDER_DOTTED': return \PHPExcel_Style_Border::BORDER_DOTTED;
		}
		throw new \Exception('Constant not available!');
	}
}
?>