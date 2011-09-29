<?php
/**
 * Debug Timer
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
 * @package AIOSystem\Core
 * @subpackage Debug
 */
namespace AIOSystem\Core;
use \AIOSystem\Api\Session;
/**
 * @package AIOSystem\Core
 * @subpackage Debug
 */
interface InterfaceDebugTimer {
	public static function Timer( $string_name = 'CoreWBTimer', $bool_average = false );
}
/**
 * @package AIOSystem\Core
 * @subpackage Debug
 */
class ClassDebugTimer implements InterfaceDebugTimer {

	private $TimerStart;
	private $TimerName;
	private $TimerMemory;
	private $TimerAverage = false;

	public static function Timer( $Name = 'CoreWBTimer', $Average = false ) {
		return new ClassDebugTimer( $Name, $Average );
	}

	public function __construct( $Name = 'CoreWBTimer', $Average = false ) {
		$this->Start();
		$this->TimerName = $Name;
		$this->TimerMemory = memory_get_usage(true);
		$this->TimerAverage = $Average;
	}
	private function Start() {
		$this->TimerStart = microtime(true);
	}
	private function Stop() {
		return (microtime(true) - $this->TimerStart);
	}
	public function Lap() {
		$Timer = (array)Session::Read( 'AIODebugTimer-'.$this->TimerName );
		array_push( $Timer, $this->Stop() );
		Session::Write( 'AIODebugTimer-'.$this->TimerName, $Timer );
	}
	public function __toString()
	{
		$Return = (string)str_pad_right($this->TimerName,25)
				.str_pad_right($this->TimerAverage?$this->Average():$this->Stop(),25)
				."Mem:".($this->TimerMemory/1024/1024).'MB/'.(memory_get_usage(true)/1024/1024)."MB"
				."\t^".((memory_get_usage(true)-$this->TimerMemory)/1024/1024)."MB"
				."\t(Peak:".(memory_get_peak_usage(true) / 1024 / 1024)."MB)\n";
		//$string_return = (string)str_pad_right($this->TimerName,20)." ".$this->average()."\tMem:".(memory_get_usage(true) / 1024 / 1024)."MB\t(Peak:".(memory_get_peak_usage(true) / 1024 / 1024)."MB)\n";
		$this->TimerMemory = memory_get_usage(true);
		return $Return;
	}
	private function Average()
	{
		$Timer = (array)Session::Read( 'AIODebugTimer-'.$this->TimerName );
		array_push( $Timer, $this->Stop() );
		Session::Write( 'AIODebugTimer-'.$this->TimerName, $Timer );
		return '~'.str_pad_right((array_sum($Timer) / count( $Timer )),25).'#'.count( $Timer ).' ';
	}
}
?>
