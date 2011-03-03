<?php
/**
 * Pathfinder: AStarHeuristic
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
/**
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
interface InterfaceAStarHeuristic {
	public static function HeuristicManhattan( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode );
	public static function HeuristicManhattanX2( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode );
	public static function HeuristicEuclidean( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode );
	public static function HeuristicEuclideanX2( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode );
}
/**
 * @package AioSystem\Module
 * @subpackage Pathfinder
 */
class ClassAStarHeuristic implements InterfaceAStarHeuristic {
	public static function HeuristicManhattan( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode ) {
		$CurrentNode->propertyValueH(
			abs( $TargetNode->propertyPositionX() - $CurrentNode->propertyPositionX() )
			+ abs( $TargetNode->propertyPositionY() - $CurrentNode->propertyPositionY() )
		);
	}
	public static function HeuristicManhattanX2( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode ) {
		$CurrentNode->propertyValueH(
			pow(
				abs( $TargetNode->propertyPositionX() - $CurrentNode->propertyPositionX() )
				+ abs( $TargetNode->propertyPositionY() - $CurrentNode->propertyPositionY() )
			, 2 )
		);
	}
	public static function HeuristicManhattanTransition( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode ) {
		$X = abs( abs( $TargetNode->propertyPositionX() - $CurrentNode->propertyPositionX() ) );
		$Y = abs( abs( $TargetNode->propertyPositionY() - $CurrentNode->propertyPositionY() ) );
		// Horizontal / Vertical
		if( $X + $Y == 1 ) {
			return true;
		}
		// Diagonal
		if( $X == 1 && $Y == 1 ) {
			return true;
		}
		return false;
	}
	public static function HeuristicEuclidean( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode ) {
		$CurrentNode->propertyValueH(
			sqrt((
				pow( $TargetNode->propertyPositionX() - $CurrentNode->propertyPositionX(), 2 )
				+ pow( $TargetNode->propertyPositionY() - $CurrentNode->propertyPositionY(), 2 )
			))
		);
	}
	public static function HeuristicEuclideanX2( ClassAStarNode $CurrentNode, ClassAStarNode $TargetNode ) {
		$CurrentNode->propertyValueH(
			pow(
				sqrt((
						pow( $TargetNode->propertyPositionX() - $CurrentNode->propertyPositionX(), 2 )
						+ pow( $TargetNode->propertyPositionY() - $CurrentNode->propertyPositionY(), 2 )
				))
			, 2 )
		);
	}
}
?>