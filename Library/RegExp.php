<?php
/**
 * Regular expression constructor
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
 * @package AioSystem\Library
 * @subpackage RegExp
 */
namespace AioSystem\Library;
/**
 * @package AioSystem\Library
 * @subpackage RegExp
 */
interface InterfaceRegExp {
	public static function integerBetween( $propertyFromInteger, $propertyToInteger );
}
/**
 * @package AioSystem\Library
 * @subpackage RegExp
 */
class ClassRegExp implements InterfaceRegExp {
	/**
	 * Create pattern, matching content between integer values
	 *
	 * <code>
	 * $pattern = AioSystem\Library\ClassRegExp::integerBetween( 2, 8 );
	 * preg_match( $pattern, 5 ); // true
	 * preg_match( $pattern, 0 ); // false
	 * preg_match( $pattern, 7 ); // true
	 * </code>
	 *
	 * @static
	 * @param  int $propertyFromInteger
	 * @param  int $propertyToInteger
	 * @return string
	 */
	public static function integerBetween( $propertyFromInteger, $propertyToInteger ) {
		$propertyFromList = str_split( str_pad_left( $propertyFromInteger, strlen( $propertyToInteger ), '0' ) );
		$propertyToList = str_split( str_pad_left( $propertyToInteger, strlen( $propertyFromInteger ), '0' ) );
		return str_replace( array("\n","\t",' '), '', self::_integerBetweenRule( $propertyFromList, $propertyToList ));
	}
// ---------------------------------------------------------------------------------------
	/**
	 * Rule to create pattern, matching content between integer values
	 *
	 * powered by Karl XD
	 *
	 * @static
	 * @param int $propertyFromList
	 * @param int $propertyToList
	 * @param int $propertyFromParent
	 * @param int $propertyToParent
	 * @param int $currentStep
	 * @param int $currentLevel
	 * @return string
	 */
	private static function _integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromParent = null, $propertyToParent = null, $currentStep = 0, $currentLevel = 0 ) {
		if( empty( $propertyFromList ) || empty( $propertyToList ) ) {
			return;
		}
		$propertyFromInteger = array_shift( $propertyFromList );
		$propertyToInteger = array_shift( $propertyToList );
		$_integerBetweenRule = '';

		if( ( $currentStep == 0 ) && $propertyFromInteger == $propertyToInteger ) {
			$_integerBetweenRule = $_integerBetweenRule
				.$propertyFromInteger
				.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 0 );
		} else if( $currentStep == 0 && $propertyFromInteger != $propertyToInteger ) {
			$currentStep = 1;
		}
		if( $currentStep == 1 ) {
			$_integerBetweenRule = $_integerBetweenRule
				.'['.$propertyFromInteger.'-'.$propertyToInteger.']'."\n"
				.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 2 );
		}
		if( $currentStep == 2 ) {
			$_integerBetweenRule .= '('."\n";
				// MIN
				$_integerBetweenRule .= str_repeat("\t",$currentLevel+1).'(?<='.$propertyFromParent.')['.$propertyFromInteger.'-9]'."\n"
					.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 3, ++$currentLevel );
				// MAX
				$_integerBetweenRule .= str_repeat("\t",$currentLevel-1)."|\n".str_repeat("\t",$currentLevel--)
					.'(?<='.$propertyToParent.')[0-'.$propertyToInteger.']'."\n"
					.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 4, ++$currentLevel );
				// INT
				if( abs( $propertyFromParent - $propertyToParent ) > 1 ) {
					$_integerBetweenRule .= str_repeat("\t",$currentLevel-1)."|\n".str_repeat("\t",$currentLevel--)
					.'(?<=['.($propertyFromParent+1).'-'.($propertyToParent-1).'])[0-9]*'."\n";
				}
			$_integerBetweenRule .= ')'."\n";
		}
		if( $currentStep == 3 ) {
			$_integerBetweenRule .= str_repeat("\t",$currentLevel).'('."\n";
				// |MIN|?[MIN]
				$_integerBetweenRule .= str_repeat("\t",$currentLevel+1).'(?<='.$propertyFromParent.')['.$propertyFromInteger.']'."\n"
					.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 3, ++$currentLevel );
				// [(MIN+1)-9]?[0-9]*
				if( $propertyFromParent + 1 <= 9 ) {
					$_integerBetweenRule .= str_repeat("\t",$currentLevel).'|(?<=['.($propertyFromParent+1).'-9])[0-9]*'."\n";
				}
				// (MIN)?[(MIN+1)-9][0-9]*
				$_integerBetweenRule .= str_repeat("\t",$currentLevel).'|(?<='.$propertyFromParent.')['.($propertyFromInteger==9?$propertyFromInteger:$propertyFromInteger+1).'-9][0-9]*'."\n";
			$_integerBetweenRule .= str_repeat("\t",$currentLevel-1).')'."\n";
		}
		if( $currentStep == 4 ) {
			$_integerBetweenRule .= str_repeat("\t",$currentLevel).'('."\n";
				// |MAX|?[MAX]
				$_integerBetweenRule .= str_repeat("\t",$currentLevel+1).'(?<='.$propertyToParent.')['.$propertyToInteger.']'."\n"
					.self::_integerBetweenRule( $propertyFromList, $propertyToList, $propertyFromInteger, $propertyToInteger, 4, ++$currentLevel );
				// (0-(MAX-1))?[0-9]*
				if( $propertyToParent - 1 >= 0 ) {
					$_integerBetweenRule .= str_repeat("\t",$currentLevel).'|(?<=[0-'.($propertyToParent-1).'])[0-9]*'."\n";
				}
				// (MAX)?[0-(MAX-1)][0-9]*
				$_integerBetweenRule .= str_repeat("\t",$currentLevel).'|(?<='.$propertyToParent.')[0-'.($propertyToInteger==0?$propertyToInteger:$propertyToInteger-1).'][0-9]*'."\n";
			$_integerBetweenRule .= str_repeat("\t",$currentLevel-1).')'."\n";
		}
		return $_integerBetweenRule;
	}
}
?>