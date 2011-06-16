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
use \AIOSystem\Api\Cache;
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
	/** @var bool $ParseAfterContent */
	private $ParsePhpAfterContent = false;
	/** @var bool $isTemplateContent */
	private $isTemplateContent = false;
	/** @var array $EscapePattern */
	private $EscapePattern = array(
		'('=>'\(',')'=>'\)',
		'['=>'\[',']'=>'\]',
		'/'=>'\/',
	);

	/**
	 * @static
	 * @throws \Exception
	 * @param string $File
	 * @return ClassTemplate
	 */
	public static function Instance( $File, $ParsePhp = true, $ParsePhpAfterContent = false, $isTemplateContent = false ) {
		return new ClassTemplate( $File, $ParsePhp, $ParsePhpAfterContent, $isTemplateContent );
	}
	public function __construct( $File, $ParsePhp = true, $ParsePhpAfterContent = false, $isTemplateContent = false ) {
		$this->propertyAssignContent = Stack::Queue();
		$this->propertyAssignRepeat = Stack::Queue();
		if( !$isTemplateContent && file_exists( $File ) ) {
			$this->propertyTemplateFile = System::File( $File );
			//var_dump( 'Load: '.$File );
		} else if( $isTemplateContent ) {
			$this->isTemplateContent = $isTemplateContent;
		} else {
			Event::Message('Load Template: '.$File);
			throw new \Exception( 'Template not available!' );
		}
		if( $this->isTemplateContent ) {
			if( $ParsePhpAfterContent ) {
				// TODO: Parse PHP not available
				$this->Content( $File );
				$this->ParsePhpAfterContent = true;
			} else {
				$this->Content( $File );
			}
		} else {
			if( $ParsePhpAfterContent ) {
				$this->Content( $this->propertyTemplateFile->readFile( false ) );
				$this->ParsePhpAfterContent = true;
			} else {
				$this->Content( $this->propertyTemplateFile->readFile( $ParsePhp ) );
			}
		}
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
						$Key = str_replace( array_keys( $this->EscapePattern ), array_values( $this->EscapePattern ), $Key );
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
	 * Parse Template
	 *
	 * @param  boolean $doCleanup
	 * @return string
	 */
	public function Parse( $doCleanup = false ) {
		// LeftOver -> Empty String
		if( true === $doCleanup ) {
			$PlaceholderList = $this->Fetch('.*?');
			foreach( $PlaceholderList[0] as $Placeholder ) {
				$this->Assign( $Placeholder, '' );
			}
		}
		while( $this->propertyAssignRepeat->peekData() !== null ) {
			$Repeat = $this->propertyAssignRepeat->popData();
			$Content = $this->Content();
			preg_match_all( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $Content , $Matches );
			foreach( (array)$Matches[1] as $TemplateIndex => $TemplateContent ) {
				$TemplateRepeat = '';
				foreach( (array)$Repeat[1] as $RowIndex => $ValueList ) {
					$TemplateContentRow = $TemplateContent;
					foreach( (array)$ValueList as $Key => $Value ) {
						if( is_array( $Value ) ) {
							$TemplateContentRow = $this->ParseRepeat( $Key, $Value, $TemplateContentRow );
						} else {
							$Key = str_replace( array_keys( $this->EscapePattern ), array_values( $this->EscapePattern ), $Key );
							$TemplateContentRow = preg_replace( '!{'.$Key.'}!is', $Value, $TemplateContentRow );
						}
					}
					$TemplateRepeat .= $TemplateContentRow;
				}
				$Content = preg_replace( '!{'.$Repeat[0].'}(.*?){\/'.$Repeat[0].'}!is', $TemplateRepeat, $Content, 1 );
			}
			$this->Content( $Content );
		}
		$Content = $this->Content();
		while( $this->propertyAssignContent->peekData() !== null ) {
			$Replace = $this->propertyAssignContent->popData();
			$Content = preg_replace( '!{'.$Replace[0].'}!is', $Replace[1], $Content );
		}
		$this->Content( $Content );

		if( $this->ParsePhpAfterContent ) {
			Cache::Set( $this->Content(), $this->Content(), 'Template', false, 10 );
			ob_start(); include( Cache::GetLocation( $this->Content(), 'Template', false ) );
			$this->Content( ob_get_clean() );
		}
		return $this->Content();
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

	public function Fetch( $TemplateRegExp ) {
		$Matches = array();
		preg_match_all( '!(?<={)'.str_replace('!','\!',$TemplateRegExp).'(?=})!is', $this->Content(), $Matches );
		return $Matches;
	}
	/**
	 * @param string $Template
	 * @param array $Array array( array( 'Template'=>'Value or Sub-Repeat-Array', ... ), ... )
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
