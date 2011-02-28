<?php
/**
 * EventTypehint
 *
 * Based on http://www.php.net/manual/de/language.oop5.typehinting.php#83442
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
 * @package AioSystem\Core
 * @subpackage Event
 */
namespace AioSystem\Core;
/**
 * @package AioSystem\Core
 * @subpackage Event
 */
interface InterfaceEventTypehint {
	public static function eventHandler( $propertyNumber, $propertyContent );
}
/**
 * @package AioSystem\Core
 * @subpackage Event
 */
class ClassEventTypehint implements InterfaceEventTypehint {
	const TYPEHINT_REGEXP = '/Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (instance of )?(\w+) given/';
	private static $_propertyTypeList = array(
		'boolean'   => 'is_bool',
		'integer'   => 'is_int',
		'int'       => 'is_int',
		'float'     => 'is_float',
		'string'    => 'is_string',
		'resource'  => 'is_resource',
		// Special
		'number'    => 'is_num',
		'object'    => 'is_box'
	);
// ---------------------------------------------------------------------------------------
	public static function eventHandler( $propertyNumber, $propertyContent ) {
		if ( $propertyNumber == E_RECOVERABLE_ERROR ) {
			$matchTypehintList = array();
			if ( preg_match( self::TYPEHINT_REGEXP, $propertyContent, $matchTypehintList ) ) {
				list(	$string_argument_match,
						$propertyIndex,
						$propertyClass,
						$propertyType,
						$propertyHint,
						$string_argument_type
				) = $matchTypehintList;
				if ( isset( self::$_propertyTypeList[$propertyHint] ) ) {
					$debugBacktraceList = debug_backtrace();
					$propertyValue = null;
					if ( self::_checkArgument( $debugBacktraceList, $propertyType, $propertyIndex, $propertyValue ) ) {
						if ( call_user_func( self::$_propertyTypeList[$propertyHint], $propertyValue ) ) return true;
					}
				}
				throw new \Exception( $propertyContent );
			}
		}
		return false;
	}
// ---------------------------------------------------------------------------------------
	private static function _checkArgument( $debugBacktraceList, $propertyType, $propertyIndex, &$propertyValue ) {
		foreach ( $debugBacktraceList as $_propertyTypeList ) {
			// FIX: STRICT
			if( empty( $_propertyTypeList['args'] ) ) {
				$_propertyTypeList['args'] = array( 0 => null );
			}
			// Match the function; Note we could do more defensive error checking.
			if ( isset( $_propertyTypeList['function'] ) && $_propertyTypeList['function'] == $propertyType ) {
				$propertyValue = $_propertyTypeList['args'][$propertyIndex - 1];
				return true;
			}
		}
		return false;
	}
}
?>