<?php
/**
 * This file contains the API:Pathfinder
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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Module\Pathfinder\ClassAStarNode as AIOAStarNode;
use \AIOSystem\Module\Pathfinder\ClassAStar as AIOAStar;
use \AIOSystem\Module\Pathfinder\ClassAStarMap as AIOAStarMap;
/**
 * @package AIOSystem\Api
 */
class Pathfinder {
	/**
	 * @return \AIOSystem\Module\Pathfinder\ClassAStarMap
	 */
	public static function AStarMap() {
		return AIOAStarMap::Instance();
	}
	/**
	 * @static
	 * @param int $Expense
	 * @param int $PositionX
	 * @param int $PositionY
	 * @return \AIOSystem\Module\Pathfinder\ClassAStarNode
	 */
	public static function AStarNode( $Expense, $PositionX, $PositionY ) {
		return AIOAStarNode::Instance( $Expense, $PositionX, $PositionY );
	}
	/**
	 * @static
	 * @param \AIOSystem\Module\Pathfinder\ClassAStarNode $Start
	 * @param \AIOSystem\Module\Pathfinder\ClassAStarNode $Target
	 * @param int $Mode
	 * @return \AIOSystem\Module\Pathfinder\ClassAStarNode|bool
	 */
	public static function AStarPath( AIOAStarNode $Start, AIOAStarNode $Target, $Mode = AIOAStarNode::EXPENSE_ABSOLUTE ) {
		return AIOAStar::Run( $Start, $Target, $Mode );
	}
	/**
	 * @static
	 * @param \AIOSystem\Module\Pathfinder\ClassAStarNode $From
	 * @param \AIOSystem\Module\Pathfinder\ClassAStarNode $To
	 * @return void
	 */
	public static function AStarTransition( AIOAStarNode $From, AIOAStarNode $To ) {
		$From->Proximity( $To );
	}
}
?>