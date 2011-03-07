<?php
/**
 * Pathfinder: AStarNode
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
interface InterfaceAStarNode {

}
/**
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
class ClassAStarNode implements InterfaceAStarNode {
	const EXPENSE_ABSOLUTE = 1;
	const EXPENSE_AVERAGE = 2;
	const EXPENSE_DIFFERENCE = 3;

	private $propertyValueC = 0;
	private $propertyValueG = 0;
	private $propertyValueH = 0;

	private $propertyPositionX = 0;
	private $propertyPositionY = 0;

	private $propertyPathNode = null;
	private $propertyProximityList = null;

	public static function Instance( $Expense, $PositionX, $PositionY ) {
		return new ClassAStarNode( $Expense, $PositionX, $PositionY );
	}

	function __construct( $Expense, $PositionX, $PositionY ) {
		$this->propertyProximityList = Stack::Priority( '\AioSystem\Module\Pathfinder\ClassAStarNode::_sortProximityList' );
		$this->propertyValueC( $Expense );
		$this->propertyPositionX( $PositionX );
		$this->propertyPositionY( $PositionY );
	}

	public function propertyValueF() {
		return $this->propertyValueG() + $this->propertyValueH();
	}
	public function propertyValueH( $ValueH = null ) {
		if( $ValueH !== null ) {
			$this->propertyValueH = $ValueH;
		} return $this->propertyValueH;
	}
	public function propertyValueG( $ValueG = null ) {
		if( $ValueG !== null ) {
			$this->propertyValueG = $ValueG;
		} return $this->propertyValueG;
	}
	public function propertyValueC( $ValueC = null ) {
		if( $ValueC !== null ) {
			$this->propertyValueC = $ValueC;
		} return $this->propertyValueC;
	}

	public function propertyPositionX( $PositionX = null ) {
		if( $PositionX !== null ) {
			$this->propertyPositionX = $PositionX;
		} return $this->propertyPositionX;
	}
	public function propertyPositionY( $PositionY = null ) {
		if( $PositionY !== null ) {
			$this->propertyPositionY = $PositionY;
		} return $this->propertyPositionY;
	}

	public function Proximity( ClassAStarNode $Node = null ) {
		if( $Node !== null ) {
			$this->propertyProximityList->pushData( $Node );
		} return $this->propertyProximityList;
	}
	public function Path( ClassAStarNode $Node = null ) {
		if( $Node !== null ) {
			$this->propertyPathNode = $Node;
		} return $this->propertyPathNode;
	}
	public function Equal( ClassAStarNode $Node ) {
		if(
			($this->propertyPositionX() == $Node->propertyPositionX())
			&&  ($this->propertyPositionY() == $Node->propertyPositionY())
		) {
			return true;
		} return false;
	}
	public function Expense( ClassAStarNode $Node, $Type = self::EXPENSE_ABSOLUTE ) {
		switch( $Type ) {
			case self::EXPENSE_ABSOLUTE: {
				// C: 0 - Cp
				return $this->propertyValueG() + $Node->propertyValueC();
			}
			case self::EXPENSE_AVERAGE: {
				// C: (Ct+Cp)/2
				return $this->propertyValueG() + ( ($this->propertyValueC()+$Node->propertyValueC()) / 2);
			}
			case self::EXPENSE_DIFFERENCE: {
				// C: abs(Ct-Cp)
				return $this->propertyValueG() + ( abs( $this->propertyValueC() - $Node->propertyValueC() ) );
			}
		}
	}

	public static function _sortProximityList( ClassAStarNode $NodeA, ClassAStarNode $NodeB ) {
		if( $NodeA->propertyValueF() >= $NodeB->propertyValueF() ) {
			return 1;
		} else return -1;
	}
}
?>