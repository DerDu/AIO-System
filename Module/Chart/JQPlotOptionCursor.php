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
/**
 * @package AIOSystem\Module
 * @subpackage jQPlot
 */
class JQPlotOptionCursor extends JQPlotOptionSeries {
	public function ShowCursor( $Switch = false ) {
		$this->PlotOption['cursor']['show'] = $Switch;
	}
	public function SetCursorStyle( $CssCursor = 'crosshair' ) {
		$this->PlotOption['cursor']['style'] = $CssCursor;
	}

	public function SetCursorZoom( $Switch = false ) {
		$this->PlotOption['cursor']['zoom'] = $Switch;
		$this->ShowCursor(true);
		// TODO: wont work correctly
		$this->SetCursorZoomResetClick(true);
	}
	public function SetCursorZoomResetClick( $Switch = true ) {
		$this->PlotOption['cursor']['clickReset'] = $Switch;
		$this->PlotOption['cursor']['dblClickReset'] = ($Switch?false:true);
	}

	public function ShowCursorTooltip( $Switch = true ) {
		$this->PlotOption['cursor']['showTooltip'] = $Switch;
	}
	const CURSOR_TOOLTIP_LOCATION_NW = 'nw';
	const CURSOR_TOOLTIP_LOCATION_N = 'n';
	const CURSOR_TOOLTIP_LOCATION_NE = 'ne';
	const CURSOR_TOOLTIP_LOCATION_E = 'e';
	const CURSOR_TOOLTIP_LOCATION_SE = 'se';
	const CURSOR_TOOLTIP_LOCATION_S = 's';
	const CURSOR_TOOLTIP_LOCATION_SW = 'sw';
	const CURSOR_TOOLTIP_LOCATION_W = 'w';
	public function SetCursorTooltipLocation( $Direction = self::CURSOR_TOOLTIP_LOCATION_SE ) {
		$this->PlotOption['cursor']['tooltipLocation'] = $Direction;
	}

	public function ShowCursorLine( $ShowVertical = false, $ShowHorizontal = false ) {
		$this->PlotOption['cursor']['showVerticalLine'] = $ShowVertical;
		$this->PlotOption['cursor']['showHorizontalLine'] = $ShowHorizontal;
	}
}
