<?php
/**
 * Pathfinder: AStarMap
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
 * @subpackage Pathfinder
 */
namespace AIOSystem\Module\Pathfinder;
use \AIOSystem\Api\Stack;
/**
 * @package AIOSystem\Module
 * @subpackage Pathfinder
 */
interface InterfaceAStarMap {
}
/**
 * @package AIOSystem\Module
 * @subpackage Pathfinder
 */
class ClassAStarMap implements InterfaceAStarMap {
	/** @var \AIOSystem\Core\ClassStackRegister $WayPointList */
	private $WayPointList = null;
	/** @var \AIOSystem\Module\ClassAStarNode $DebugResult */
	private $DebugResult = null;
	/**
	 * @static
	 * @return ClassAStarMap
	 */
	public static function Instance() {
		$AStarMap = new ClassAStarMap;
		$AStarMap->WayPointList = Stack::Register();
		return $AStarMap;
	}
	/**
	 * @param int $Expense
	 * @param int $PositionX
	 * @param int $PositionY
	 */
	public function addPoint( $Expense, $PositionX, $PositionY ) {
		$this->WayPointList->setRegister(
			$PositionX.':'.$PositionY,
			ClassAStarNode::Instance( $Expense, $PositionX, $PositionY )
		);
	}
	/**
	 * @param int $fromPositionX
	 * @param int $fromPositionY
	 * @param int $toPositionX
	 * @param int $toPositionY
	 */
	public function addTransition( $fromPositionX, $fromPositionY, $toPositionX, $toPositionY ) {
		$this->WayPointList->getRegister( $fromPositionX.':'.$fromPositionY )->Proximity(
			$this->WayPointList->getRegister( $toPositionX.':'.$toPositionY )
		);
	}
	/**
	 * @param int $fromPositionX
	 * @param int $fromPositionY
	 * @param int $toPositionX
	 * @param int $toPositionY
	 * @return ClassAStarNode|bool
	 */
	public function getPath( $fromPositionX, $fromPositionY, $toPositionX, $toPositionY, $TransitionMode = ClassAStarNode::EXPENSE_ABSOLUTE ) {
		$this->DebugResult = $PathResult = ClassAStar::Run(
			$this->WayPointList->getRegister( $fromPositionX.':'.$fromPositionY ),
			$this->WayPointList->getRegister( $toPositionX.':'.$toPositionY ),
			$TransitionMode
		);
		if( $PathResult === false ) {
			return array();
		} else {
			$Return = array();
			while( $PathResult !== null ) {
				array_push( $Return, array( $PathResult->propertyPositionX(), $PathResult->propertyPositionY() ) );
				$PathResult = $PathResult->Path();
			}
			krsort( $Return );
			return $Return;
		}
	}

	public function debugMap( $ScaleX = 1, $ScaleY = 1 ) {

		$AStarNodeList = $this->WayPointList->listRegister();
		/** @var ClassAStarNode $AStarNode */
		foreach( $AStarNodeList as $AStarNode ) {
			print '<div style="position: absolute;'
					.' left: '.($AStarNode->propertyPositionX()*$ScaleX).'px;'
					.' top: '.($AStarNode->propertyPositionY()*$ScaleY).'px;'
					.' background-color: #'.str_repeat(dechex(200-( $AStarNode->propertyValueC() )),3).';'
				.' border: 1px solid silver; width: 50px; height: 50px;">'
				.$AStarNode->propertyValueF()
			.'</div>';
		}

		$AStarNode = $this->DebugResult;
		//if( $AStarNode === false ) return;
		$Index = 0;
		while( $AStarNode !== null ) {
			print '<div style="position: absolute;'
					.' left: '.($AStarNode->propertyPositionX()*$ScaleX).'px;'
					.' top: '.($AStarNode->propertyPositionY()*$ScaleY).'px;'
					.' background-color: #FF'.str_repeat(dechex( $AStarNode->propertyValueF() ),2).';'
				.' border: 1px solid red; width: 50px; height: 50px;">'
				//.($Index++)
				//.$AStarNode->propertyValueF()
			.'</div>';
			$AStarNode = $AStarNode->Path();
		}

	}
}