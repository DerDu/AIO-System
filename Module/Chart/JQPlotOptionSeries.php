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
class JQPlotOptionSeries extends JQPlotOptionLegend {

	/**
	 * @param array|string $CssHexColor
	 * @return void
	 */
	public function SetSeriesColors( $CssHexColor ) {
		if( !is_array( $CssHexColor ) ) {
			$CssHexColor = func_get_args();
		}
		$this->PlotOption['seriesColors'] = Json::Encode( $CssHexColor );
	}
	public function SetSeriesStacked( $Switch = false ) {
		$this->PlotOption['stackSeries'] = $Switch;
	}

	public function ShowSeriesDefaultRendererPieDataLabels( $Switch = false ) {
		$this->PlotOption['seriesDefaults']['rendererOptions']['showDataLabels'] = $Switch;
	}
	const SERIES_RENDERER_LINE = 'jQuery.jqplot.LineRenderer';
	const SERIES_RENDERER_BEZIER = 'jQuery.jqplot.BezierCurveRenderer';
	const SERIES_RENDERER_BAR = 'jQuery.jqplot.BarRenderer';
	const SERIES_RENDERER_PIE = 'jQuery.jqplot.PieRenderer';
	const SERIES_RENDERER_DONUT = 'jQuery.jqplot.DonutRenderer';
	const SERIES_RENDERER_FUNNEL = 'jQuery.jqplot.FunnelRenderer';
	public function SetSeriesDefaultRenderer( $Renderer = self::SERIES_RENDERER_LINE ) {
		$this->PlotOption['seriesDefaults']['renderer'] = $Renderer;
	}
	public function SetSeriesDefaultLineWidth( $Width = 2.5 ) {
		$this->PlotOption['seriesDefaults']['lineWidth'] = $Width;
	}

	public function ShowSeriesDefaultMarker( $Switch = true ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['show'] = $Switch;
	}
	const SERIES_MARKER_STYLE_CIRCLE = 'circle';
	const SERIES_MARKER_STYLE_FILLED_CIRCLE = 'filledCircle';
	const SERIES_MARKER_STYLE_DIAMOND = 'diamond';
	const SERIES_MARKER_STYLE_FILLED_DIAMOND = 'filledDiamond';
	const SERIES_MARKER_STYLE_SQUARE = 'square';
	const SERIES_MARKER_STYLE_FILLED_SQUARE = 'filledSquare';
	public function SetSeriesDefaultMarkerStyle( $Style = self::SERIES_MARKER_STYLE_FILLED_CIRCLE ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['style'] = $Style;
	}
	public function SetSeriesDefaultMarkerColor( $CssColor = '#666666' ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['color'] = $CssColor;
	}
	public function SetSeriesDefaultMarkerSize( $Size = 9 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['size'] = $Size;
	}
	public function SetSeriesDefaultMarkerWidth( $Width = 2 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['lineWidth'] = $Width;
	}

	public function ShowSeriesDefaultMarkerShadow( $Switch = true ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['shadow'] = $Switch;
	}
	public function SetSeriesDefaultMarkerShadowAngle( $Angle = 45 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['shadowAngle'] = $Angle;
	}
	public function SetSeriesDefaultMarkerShadowOffset( $Offset = 1 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['shadowOffset'] = $Offset;
	}
	public function SetSeriesDefaultMarkerShadowDepth( $Depth = 3 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['shadowDepth'] = $Depth;
	}
	public function SetSeriesDefaultMarkerShadowAlpha( $Alpha = 0.07 ) {
		$this->PlotOption['seriesDefaults']['markerOptions']['shadowAlpha'] = $Alpha;
	}

	public function ShowSeriesDefaultShadow( $Switch = true ) {
		$this->PlotOption['seriesDefaults']['shadow'] = $Switch;
	}
	public function SetSeriesDefaultShadowAngle( $Angle = 45 ) {
		$this->PlotOption['seriesDefaults']['shadowAngle'] = $Angle;
	}
	public function SetSeriesDefaultShadowOffset( $Offset = 1.25 ) {
		$this->PlotOption['seriesDefaults']['shadowOffset'] = $Offset;
	}
	public function SetSeriesDefaultShadowDepth( $Depth = 3 ) {
		$this->PlotOption['seriesDefaults']['shadowDepth'] = $Depth;
	}
	public function SetSeriesDefaultShadowAlpha( $Alpha = 0.1 ) {
		$this->PlotOption['seriesDefaults']['shadowAlpha'] = $Alpha;
	}
}
