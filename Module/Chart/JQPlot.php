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
use \AIOSystem\Api\Uuid;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage jQPlot
 */
interface InterfaceJQPlot {
	public static function Instance( $Width = 450, $Height = 300 );
	public function Render();
	public function AddPlotData( $Series );
}
/**
 * @package AIOSystem\Module
 * @subpackage jQPlot
 */
class JQPlot extends JQPlotOption implements InterfaceJQPlot {
	/** @var array $PlotData */
	private $PlotData = array();
	/** @var int $PlotWidth */
	private $PlotWidth = 450;
	/** @var int $PlotHeight */
	private $PlotHeight = 300;
	/** @var null|string $PlotUuid */
	private $PlotUuid = null;
	/** @var null|string $PlotJSData */
	private $PlotJSData = null;
	/** @var null|string $PlotJSOption */
	private $PlotJSOption = null;

	/**
	 * @param int $Width
	 * @param int $Height
	 * @return \AIOSystem\Module\Chart\JQPlot
	 */
	public static function Instance( $Width = 450, $Height = 300, $JSData = null, $JSOption = null ) {
		return new JQPlot( $Width, $Height, $JSData, $JSOption );
	}
	/**
	 * @param int $Width
	 * @param int $Height
	 * @return void
	 */
	private function __construct( $Width = 450, $Height = 300, $JSData = null, $JSOption = null ) {
		$this->PlotWidth = $Width;
		$this->PlotHeight = $Height;
		$this->PlotJSData = $JSData;
		$this->PlotJSOption = $JSOption;
		$this->PlotUuid = Uuid::V4();
	}
	/**
	 * @return string
	 */
	public function Render( $Name = null ) {
		$PlotOption = $this->getPlotOption();
		print '<div id="'.$this->getPlotUuid($Name).'" style="width:'.$this->PlotWidth.'px;height:'.$this->PlotHeight.'px;"></div>'
			.'<script type="text/javascript">'
			.($Name===null?'':'var oPlot'.$Name.' = null;')
			."jQuery('document').ready(function(){"
				.($Name===null?'':'oPlot'.$Name.' = ')
				."jQuery.jqplot('".$this->getPlotUuid($Name)."',".$this->getPlotData().(strlen($PlotOption)>2?','.$PlotOption:'').");"
			."});"
			.'</script>';
		//Event::Debug( $this->getPlotOption(), __FILE__,__LINE__ );
		//Event::Debug( $this->getPlotData(), __FILE__,__LINE__ );
	}
	/**
	 * @param array|int|string $Series
	 * @return void
	 */
	public function AddPlotData( $Series ) {
		if( !is_array( $Series ) ) {
			$Series = array();
			$Count = func_num_args();
			for( $Run = 0; $Run < $Count; $Run+=2 ) {
				array_push( $Series, array( func_get_arg( $Run ), func_get_arg( $Run+1 ) ) );
			}
		} else if( !is_array( current($Series) ) ) {
			$Series = func_get_args();
		}
		array_push( $this->PlotData, $Series );
	}
	public function getPlotUuid( $Name = null ){
		if($Name===null){
			return $this->PlotUuid;
		} else {
			return $Name;
		}
	}
	/**
	 * @return string
	 */
	private function getPlotOption(){
		if( $this->PlotJSOption !== null ) {
			return $this->PlotJSOption;
		} else {
			return $this->createOptions();
		}
	}
	/**
	 * @return string
	 */
	private function getPlotData(){
		if( $this->PlotJSData !== null ) {
			return $this->PlotJSData;
		} else {
			return Json::Encode( $this->PlotData );
		}
	}
}
