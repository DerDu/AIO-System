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
 * @package AioSystem\Api
 */
namespace AioSystem\Api;
use \AioSystem\Module\Pathfinder\ClassAStarNode as AioAStarNode;
use \AioSystem\Module\Pathfinder\ClassAStar as AioAStar;
use \AioSystem\Module\Pathfinder\ClassAStarMap as AioAStarMap;
/**
 * @package AioSystem\Api
 */
class Pathfinder {
	/**
	 * @return \AioSystem\Module\Pathfinder\ClassAStarMap
	 */
	public static function AStarMap() {
		return AioAStarMap::Instance();
	}
	/**
	 * @static
	 * @param int $Expense
	 * @param int $PositionX
	 * @param int $PositionY
	 * @return \AioSystem\Module\Pathfinder\ClassAStarNode
	 */
	public static function AStarNode( $Expense, $PositionX, $PositionY ) {
		return AioAStarNode::Instance( $Expense, $PositionX, $PositionY );
	}
	/**
	 * @static
	 * @param \AioSystem\Module\Pathfinder\ClassAStarNode $Start
	 * @param \AioSystem\Module\Pathfinder\ClassAStarNode $Target
	 * @param int $Mode
	 * @return \AioSystem\Module\Pathfinder\ClassAStarNode|bool
	 */
	public static function AStarPath(
		\AioSystem\Module\Pathfinder\ClassAStarNode $Start,
		\AioSystem\Module\Pathfinder\ClassAStarNode $Target,
		$Mode = \AioSystem\Module\Pathfinder\ClassAStarNode::EXPENSE_ABSOLUTE
	) {
		return AioAStar::Run( $Start, $Target, $Mode );
	}
	/**
	 * @static
	 * @param \AioSystem\Module\Pathfinder\ClassAStarNode $From
	 * @param \AioSystem\Module\Pathfinder\ClassAStarNode $To
	 * @return void
	 */
	public static function AStarTransition(
		\AioSystem\Module\Pathfinder\ClassAStarNode $From,
		\AioSystem\Module\Pathfinder\ClassAStarNode $To
	) {
		$From->Proximity( $To );
	}
}
?>