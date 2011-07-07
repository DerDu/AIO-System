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
use \AIOSystem\Api\Json;
/**
 * @package AIOSystem\Module
 * @subpackage jQPlot
 */
class JQPlotOptionGrid extends JQPlotOptionCommon {
	// TODO: grid:renderer: $.jqplot.CanvasGridRenderer
	// TODO: grid:rendererOptions: {}
	/**
	 * @param boolean $Switch
	 * @return void
	 */
	public function ShowGrid( $Switch = true ) {
		$this->PlotOption['grid']['drawGridLines'] = $Switch;
		$this->PlotOption['axesDefaults']['tickOptions']['showGridline'] = $Switch;
	}

	public function SetGridColor( $CssColor = '#CCCCCC' ) {
		$this->PlotOption['grid']['gridLineColor'] = $CssColor;
	}
	public function SetGridBackground( $CssColor = '#FFFDF6' ) {
		$this->PlotOption['grid']['background'] = $CssColor;
	}

	public function ShowGridBorder( $Switch = true ) {
		$this->PlotOption['grid']['borderWidth'] = ($Switch?2:0);
	}
	public function SetGridBorderWidth( $Width = 2 ) {
		$this->PlotOption['grid']['borderWidth'] = $Width;
	}
	public function SetGridBorderColor( $CssColor = '#999999' ) {
		$this->PlotOption['grid']['borderColor'] = $CssColor;
	}

	public function ShowGridShadow( $Switch = true ) {
		$this->PlotOption['grid']['shadow'] = $Switch;
	}
	public function SetGridShadowAngle( $Angle = 45 ) {
		$this->PlotOption['grid']['shadowAngle'] = $Angle;
	}
	public function SetGridShadowOffset( $Offset = 1.5 ) {
		$this->PlotOption['grid']['shadowOffset'] = $Offset;
	}
	public function SetGridShadowWidth( $Width = 3 ) {
		$this->PlotOption['grid']['shadowWidth'] = $Width;
	}
	public function SetGridShadowDepth( $Depth = 3 ) {
		$this->PlotOption['grid']['shadowDepth'] = $Depth;
	}
	public function SetGridShadowAlpha( $Alpha = 0.07 ) {
		$this->PlotOption['grid']['shadowAlpha'] = $Alpha;
	}
}
