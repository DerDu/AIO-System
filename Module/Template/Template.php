<?php
/**
 * Template
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
 * @package AioSystem\Module
 * @subpackage Template
 */
namespace AioSystem\Module\Template;
use \AioSystem\Api\System as System;
use \AioSystem\Api\Stack as Stack;
/**
 * @package AioSystem\Module
 * @subpackage Template
 */
interface InterfaceTemplate {
	public static function Instance( $File );
	public function Parse( $ParsePhp = true );
	public function Assign( $Template, $Value = null );
	public function Repeat( $Template, $Array );
}
/**
 * @package AioSystem\Module
 * @subpackage Template
 */
class ClassTemplate implements InterfaceTemplate {
	/** @var \AioSystem\Core\ClassSystemFile $propertyTemplateFile */
	public $propertyTemplateFile = null;
	/** @var \AioSystem\Core\ClassStackQueue $propertyAssignContent */
	private $propertyAssignContent = array();
	/** @var \AioSystem\Core\ClassStackQueue $propertyAssignRepeat */
	private $propertyAssignRepeat = array();

	/**
	 * @static
	 * @param string $File
	 * @return ClassTemplate
	 */
	public static function Instance( $File ) {
		return new ClassTemplate( $File );
	}
	public function __construct( $File ) {
		if( file_exists( $File) ) {
			$this->propertyTemplateFile = System::File( $File );
		} else trigger_error( 'Template not available!' );

		$this->propertyAssignContent = Stack::Queue();
		$this->propertyAssignRepeat = Stack::Queue();
	}
	/**
	 * @return string
	 */
	public function Parse( $ParsePhp = true ) {
		while( $this->propertyAssignRepeat->peekData() !== null ) {
			$Repeat = $this->propertyAssignRepeat->popData();
			$Content = $this->propertyTemplateFile->readFile( $ParsePhp );
			preg_match_all( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $Content , $Matches );
			foreach( (array)$Matches[1] as $TemplateIndex => $TemplateContent ) {
				$TemplateRepeat = '';
				foreach( (array)$Repeat[1] as $RowIndex => $ValueList ) {
					$TemplateContentRow = $TemplateContent;
					foreach( (array)$ValueList as $Key => $Value ) {
						$TemplateContentRow = preg_replace( '!{'.$Key.'}!is', $Value, $TemplateContentRow );
					}
					$TemplateRepeat .= $TemplateContentRow;
				}
				$Content = preg_replace( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $TemplateRepeat, $Content, 1 );
			}
			$this->propertyTemplateFile->propertyFileContent( $Content );
		}
		$Content = $this->propertyTemplateFile->propertyFileContent();
		while( $this->propertyAssignContent->peekData() !== null ) {
			$Replace = $this->propertyAssignContent->popData();
			$Content = preg_replace( '!{'.$Replace[0].'}!is', $Replace[1], $Content );
		}
		$this->propertyTemplateFile->propertyFileContent( $Content );
		return $this->propertyTemplateFile->propertyFileContent();
	}
	/**
	 * @param string|array $Template
	 * @param string|null $Value
	 * @return void
	 */
	public function Assign( $Template, $Value = null ) {
		if( is_array( $Template ) ) {
			$this->_assignArray( $Template );
		} else {
			$this->propertyAssignContent->pushData( array( $Template, $Value ) );
		}
	}
	/**
	 * @param string $Template
	 * @param array $Array array( array( 'Template'=>'Value', ... ), ... )
	 * @return void
	 */
	public function Repeat( $Template, $Array ) {
		$this->propertyAssignRepeat->pushData( array( $Template, $Array ) );
	}
	/**
	 * @param array $Array
	 * @return void
	 */
	private function _AssignArray( $Array ) {
		foreach( (array)$Array as $Template => $Value ) {
			$this->Assign( $Template, $Value );
		}
	}
}
?>