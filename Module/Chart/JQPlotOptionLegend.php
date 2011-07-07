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
class JQPlotOptionLegend extends JQPlotOptionAxes {
	public function ShowLegend( $Switch ) {
		$this->PlotOption['legend']['show'] = $Switch;
	}
	const LEGEND_LOCATION_NW = 'nw';
	const LEGEND_LOCATION_N = 'n';
	const LEGEND_LOCATION_NE = 'ne';
	const LEGEND_LOCATION_E = 'e';
	const LEGEND_LOCATION_SE = 'se';
	const LEGEND_LOCATION_S = 's';
	const LEGEND_LOCATION_SW = 'sw';
	const LEGEND_LOCATION_W = 'w';
	public function SetLegendLocation( $Direction = self::LEGEND_LOCATION_NE ) {
		$this->PlotOption['legend']['location'] = $Direction;
	}

	public function SetLegendOffset( $OffsetX = 12, $OffsetY = 12 ) {
		$this->PlotOption['legend']['xoffset'] = $OffsetX;
		$this->PlotOption['legend']['yoffset'] = $OffsetY;
	}
}
