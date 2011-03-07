<?php
/**
 * Pathfinder: AStar
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
 *  * Neither the name of the Gerd Christian Kunze nor the names of its
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
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
namespace AioSystem\Module\Pathfinder;
use AioSystem\Api\Stack as Stack;
/**
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
interface InterfaceAStar {

}
/**
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
class ClassAStar implements InterfaceAStar {
	/** @var \AioSystem\Core\ClassStackPriority $propertyOpenList */
	private static $propertyOpenList = null;
	/** @var \AioSystem\Core\ClassStackPriority $propertyClosedList */
	private static $propertyClosedList = null;

	public static function Run( ClassAStarNode $StartNode, ClassAStarNode $TargetNode, $ExpenseType = ClassAStarNode::EXPENSE_ABSOLUTE ) {
		// Check: Start == Target
		if( $StartNode == $TargetNode ) {
			return true;
		}
		self::$propertyOpenList = Stack::Priority('\AioSystem\Module\Pathfinder\ClassAStarNode::_sortProximityList');
		self::$propertyClosedList = Stack::Priority('\AioSystem\Module\Pathfinder\ClassAStarNode::_sortProximityList');
		self::pushOpenList( $StartNode );
		// Run: Target not in ClosedList
		while( self::$propertyOpenList->peekData() !== null ) {
			/** @var ClassAStarNode $CurrentNode */
			$CurrentNode = self::$propertyOpenList->popData();
			self::pushClosedList( $CurrentNode );

			$ProximityList = $CurrentNode->Proximity()->listData();
			/** @var ClassAStarNode $Proximity */
			foreach( $ProximityList as $Proximity ) {
				if( false === self::inList( self::$propertyClosedList, $Proximity ) ) {
					if( false === ( $IndexOpenList = self::inList( self::$propertyOpenList, $Proximity ) ) ) {
						$Proximity->Path( $CurrentNode );
						$Proximity->propertyValueG( $CurrentNode->Expense( $Proximity, $ExpenseType ) );
						ClassAStarHeuristic::HeuristicManhattan( $Proximity, $TargetNode );
						self::pushOpenList( $Proximity );
						
					} else {
						/** @var ClassAStarNode $OpenNode */
						$OpenNode = self::$propertyOpenList->getData( $IndexOpenList );
						if( $CurrentNode->Expense( $OpenNode, $ExpenseType ) < $OpenNode->propertyValueG() ) {
							$OpenNode->Path( $CurrentNode );
							$OpenNode->propertyValueG( $CurrentNode->Expense( $OpenNode, $ExpenseType ) );
							self::$propertyOpenList->sortData();
						}
					}
				}
			}
			// Path OK!
			if( $CurrentNode->Equal( $TargetNode ) ) {
				break;
			}
		}
		// Return Path(Node) / NoPath(False)
		if( $TargetNode->Path() !== null ) {
			return $TargetNode;
		} else {
			return false;
		}
	}

	public static function inList( \AioSystem\Core\ClassStackPriority $List, ClassAStarNode $Node ) {
		$List = $List->listData();
		/** @var ClassAStarNode $Item */
		foreach( $List as $Index => $Item ) {
			if( $Item->Equal( $Node ) ) {
				return $Index;
			}
		} return false;
	}

	public static function pushOpenList( ClassAStarNode $AStarNode ) {
		self::$propertyOpenList->pushData( $AStarNode );
	}
	public static function removeOpenList( ClassAStarNode $AStarNode ) {
		if( false !== ( $Index = self::inList( self::$propertyOpenList, $AStarNode ) ) ) {
			self::$propertyOpenList->removeData( $Index );
		}
	}
	public static function pushClosedList( ClassAStarNode $AStarNode ) {
		self::$propertyClosedList->pushData( $AStarNode );
	}

	public static function debugOpenList(){
		var_dump( self::$propertyOpenList );
	}
	public static function debugClosedList(){
		var_dump( self::$propertyClosedList );
	}
	public static function debugPath( ClassAStarNode $CurrentNode ) {
		$Stop = false;
		$Index = 0;
		while( !$Stop ) {
			var_dump( ($Index++).' X'.$CurrentNode->propertyPositionX().' Y'.$CurrentNode->propertyPositionY().' F'.$CurrentNode->propertyValueF() );
			if( $CurrentNode->Path() === null ) {
				$Stop = true;
				break;
			} else {
				$CurrentNode = $CurrentNode->Path();
			}
		}
	}
}
?>