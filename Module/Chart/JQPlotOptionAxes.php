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
class JQPlotOptionAxes extends JQPlotOptionGrid {
	// TODO: axesDefaults:renderer: $.jqplot.LinearAxisRenderer
	// TODO: axesDefaults:rendererOptions: {}
	/**
	 * @param boolean $Switch
	 * @return void
	 */
	public function ShowAxesDefault( $Switch = false ) {
		$this->PlotOption['axesDefaults']['show'] = $Switch;
	}
	/**
	 * @param null|float|int $Value
	 * @return void
	 */
	public function SetAxesDefaultMin( $Value = null ) {
		$this->PlotOption['axesDefaults']['min'] = $Value;
	}
	/**
	 * @param null|float|int $Value
	 * @return void
	 */
	public function SetAxesDefaultMax( $Value = null ) {
		$this->PlotOption['axesDefaults']['max'] = $Value;
	}
	/**
	 * @param float $Value
	 * @return void
	 */
	public function SetAxesDefaultPadding( $Value = 1.2 ) {
		$this->PlotOption['axesDefaults']['pad'] = $Value;
	}

	const AXES_RENDERER_LABEL_CANVAS = 'jQuery.jqplot.CanvasAxisLabelRenderer';
	public function SetAxesDefaultLabelRenderer( $Renderer = self::AXES_RENDERER_LABEL_CANVAS ) {
		$this->PlotOption['axesDefaults']['labelRenderer'] = $Renderer;
	}
	const AXES_RENDERER_TICK_CANVAS = 'jQuery.jqplot.CanvasAxisTickRenderer';
	public function SetAxesDefaultTickRenderer( $Renderer = self::AXES_RENDERER_TICK_CANVAS ) {
		$this->PlotOption['axesDefaults']['tickRenderer'] = $Renderer;
	}


	/**
	 * @param boolean $Switch
	 * @return void
	 */
	public function ShowAxesDefaultTickMark( $Switch ) {
		$this->PlotOption['axesDefaults']['showTickMarks'] = $Switch;
		$this->PlotOption['axesDefaults']['tickOptions']['showMark'] = $Switch;
	}
	const AXES_TICK_MARK_OUTSIDE = 'outside';
	const AXES_TICK_MARK_INSIDE = 'inside';
	const AXES_TICK_MARK_CROSS = 'cross';
	public function SetAxesDefaultTickMark( $Position = self::AXES_TICK_MARK_OUTSIDE ) {
		$this->PlotOption['axesDefaults']['tickOptions']['mark'] = $Position;
	}
	public function SetAxesDefaultTickMarkLength( $Length = 4 ) {
		$this->PlotOption['axesDefaults']['tickOptions']['markSize'] = $Length;
	}
	public function SetAxesDefaultTickMarkFormat( $Format = '' ) {
		$this->PlotOption['axesDefaults']['tickOptions']['formatString'] = $Format;
	}

	/**
	 * @param boolean $Switch
	 * @return void
	 */
	public function ShowAxesDefaultTickLabel( $Switch ) {
		$this->PlotOption['axesDefaults']['showTicks'] = $Switch;
		$this->PlotOption['axesDefaults']['tickOptions']['showLabel'] = $Switch;
	}
}
