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
 * @package AIOSystem\Module
 * @subpackage Template
 */
namespace AIOSystem\Module\Template;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Template
 */
interface InterfaceTemplate {
	public static function Instance( $File, $ParsePhp = true );
	public function Content();
	public function Parse();
	public function Assign( $Template, $Value = null );
	public function Repeat( $Template, $Array );
}
/**
 * @package AIOSystem\Module
 * @subpackage Template
 */
class ClassTemplate implements InterfaceTemplate {
	/** @var \AIOSystem\Core\ClassSystemFile $propertyTemplateFile */
	private $propertyTemplateFile = null;
	/** @var null|string $propertyTemplateContent */
	private $propertyTemplateContent = null;
	/** @var \AIOSystem\Core\ClassStackQueue $propertyAssignContent */
	private $propertyAssignContent = array();
	/** @var \AIOSystem\Core\ClassStackQueue $propertyAssignRepeat */
	private $propertyAssignRepeat = array();

	/**
	 * @static
	 * @throws \Exception
	 * @param string $File
	 * @return ClassTemplate
	 */
	public static function Instance( $File, $ParsePhp = true ) {
		return new ClassTemplate( $File, $ParsePhp = true );
	}
	public function __construct( $File, $ParsePhp = true ) {
		$this->propertyAssignContent = Stack::Queue();
		$this->propertyAssignRepeat = Stack::Queue();
		if( file_exists( $File ) ) {
			$this->propertyTemplateFile = System::File( $File );
			//var_dump( 'Load: '.$File );
		} else {
			Event::Message('Load Template: '.$File);
			throw new \Exception( 'Template not available!' );
		}
		$this->propertyTemplateContent = $this->propertyTemplateFile->readFile( $ParsePhp );
	}
	/**
	 * @param null|string $Content
	 * @return string
	 */
	public function Content( $Content = null ) {
		if( $Content !== null ) {
			$this->propertyTemplateContent = $Content;
		} return $this->propertyTemplateContent;
	}
	/**
	 * @return array
	 */
	public function MapAssign() {
		return $this->propertyAssignContent->listData();
	}
	/**
	 * @return array
	 */
	public function MapRepeat() {
		return $this->propertyAssignRepeat->listData();
	}
	/**
	 * @param string $RepeatKey
	 * @param array $DataArray
	 * @param string $Content
	 * @return string
	 */
	private function ParseRepeat( $RepeatKey, $DataArray, $Content ) {
		preg_match_all( '!{'.$RepeatKey.'}(.*?){\/'.$RepeatKey.'}!is', $Content , $Matches );
		foreach( (array)$Matches[1] as $TemplateIndex => $TemplateContent ) {
			$TemplateRepeat = '';
			foreach( (array)$DataArray as $RowIndex => $ValueList ) {
				$TemplateContentRow = $TemplateContent;
				foreach( (array)$ValueList as $Key => $Value ) {
					if( is_array( $Value ) ) {
						$TemplateContentRow = $this->ParseRepeat( $Key, $Value, $TemplateContentRow );
					} else {
						$TemplateContentRow = preg_replace( '!{'.$Key.'}!is', $Value, $TemplateContentRow );
					}
				}
				$TemplateRepeat .= $TemplateContentRow;
			}
			$Content = preg_replace( '!{'.$RepeatKey.'}(.*?){\/'.$RepeatKey.'}!is', $TemplateRepeat, $Content, 1 );
		}
		return $Content;
	}
	/**
	 * @return string
	 */
	public function Parse() {
		while( $this->propertyAssignRepeat->peekData() !== null ) {
			$Repeat = $this->propertyAssignRepeat->popData();
			$Content = $this->propertyTemplateContent;
			preg_match_all( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $Content , $Matches );
			foreach( (array)$Matches[1] as $TemplateIndex => $TemplateContent ) {
				$TemplateRepeat = '';
				foreach( (array)$Repeat[1] as $RowIndex => $ValueList ) {
					$TemplateContentRow = $TemplateContent;
					foreach( (array)$ValueList as $Key => $Value ) {
						if( is_array( $Value ) ) {
							$TemplateContentRow = $this->ParseRepeat( $Key, $Value, $TemplateContentRow );
						} else {
							$TemplateContentRow = preg_replace( '!{'.$Key.'}!is', $Value, $TemplateContentRow );
						}
					}
					$TemplateRepeat .= $TemplateContentRow;
				}
				$Content = preg_replace( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $TemplateRepeat, $Content, 1 );
			}
			$this->propertyTemplateContent = $Content;
		}
		$Content = $this->propertyTemplateContent;
		while( $this->propertyAssignContent->peekData() !== null ) {
			$Replace = $this->propertyAssignContent->popData();
			$Content = preg_replace( '!{'.$Replace[0].'}!is', $Replace[1], $Content );
		}
		$this->propertyTemplateContent = $Content;
		return $this->propertyTemplateContent;
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