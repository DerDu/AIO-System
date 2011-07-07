<?php
/**
 * jQPlot
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
 * @subpackage jQPlot
 */
namespace AIOSystem\Module\Chart;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage jQPlot
 */
class JQPlotOptionCommon {
	/** @var array $PlotOption */
	protected $PlotOption = array();
	/**
	 * @return string
	 */
	protected function createOptions( $PlotOption = null ) {
		// Special "TypeCast"
		$defineTypeNumberObject = array(
			'seriesColors',
			'renderer',
			'labelRenderer',
			'tickRenderer'
		);
		if( $PlotOption === null ) {
			$PlotOption = $this->PlotOption;
			$Root = true;
		} else {
			$Root = false;
		}
		$OptionString = '';
		foreach( (array)$PlotOption as $Key => $Value ) {
			if( !empty( $OptionString ) ) {
				$OptionString .= ',';
			}
			if( is_array( $Value ) ) {
				$OptionString .= $Key.':{'.$this->createOptions( $Value )."}";
			} else {
				if( is_num( $Value ) || in_array( $Key, $defineTypeNumberObject ) ) {
					$OptionString .= $Key.":".$Value."";
				} else if( $Value === null ) {
					$OptionString .= $Key.":null";
				} else if( $Value === true ) {
					$OptionString .= $Key.":true";
				} else if( $Value === false ) {
					$OptionString .= $Key.":false";
				} else {
					$OptionString .= $Key.":'".$Value."'";
				}
			}
		}

		if( $Root ) {
			return '{'.$OptionString.'}';
		} else {
			return $OptionString;
		}
	}
}
