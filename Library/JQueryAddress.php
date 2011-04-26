<?php
/**
 * JQueryAddress
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
 * @package AIOSystem\Library
 * @subpackage JQueryAddress
 */
namespace AIOSystem\Library;
use \AIOSystem\Api\Json;
use \AIOSystem\Api\Seo;
/**
 * @package AIOSystem\Library
 * @subpackage JQueryAddress
 */
interface InterfaceJQueryAddress {
	public static function jQueryAddressDeeplink(
		$string_target, $string_file, $string_value, $array_parameter = array(), $string_base = ''
	);
	public static function jQueryAddressDeeplinkAttributes(
		$string_target, $string_file, $string_value, $array_parameter = array(), $string_base = ''
	);
	public static function jQueryAddressLink(
		$string_href, $string_name
	);
}
/**
 * @package AIOSystem\Library
 * @subpackage JQueryAddress
 */
class ClassJQueryAddress implements InterfaceJQueryAddress {
	public static function jQueryAddressDeeplink( $mixed_target, $mixed_file, $string_value, $array_parameter = array(), $integer_level = 0 ){
		$array_deeplink = self::jQueryAddressDeeplinkAttributes( $mixed_target, $mixed_file, $string_value, $array_parameter, $integer_level );
		return '<a id="'.$array_deeplink['id'].'" '
			.'href="'.$array_deeplink['href'].'" '
			.'rel="'.$array_deeplink['rel'].'" '
			.'>'.$array_deeplink['value'].'</a>';
	}
	public static function jQueryAddressDeeplinkAttributes( $mixed_target, $mixed_file, $string_value, $array_parameter = array(), $integer_level = 0 ){
//		var_dump($array_parameter);
		$mixed_file = array_map( '\AIOSystem\Api\Seo::Path', (array)$mixed_file );
//		var_dump( $mixed_file );
		$string_file = '?f='.Json::Encode( (array)$mixed_file );
		$string_target = '&t='.Json::Encode( (array)$mixed_target );
		$string_parameter = '&p='.Json::Encode( (array)$array_parameter );
		$string_level = '&l='.Json::Encode( (array)$integer_level );
//		var_dump($string_parameter);
		return array(
			'id'    => 'AIODeepLink-'.strtoupper(sha1($string_file.$string_target.$string_level.$string_value)),
			'href'  => Seo::Path('/').'?AIOLink='.ClassEncryption::encodeSessionEncryption( $string_file.$string_target.$string_parameter.$string_level ),
			'rel'   => 'address:?AIOLink='.ClassEncryption::encodeSessionEncryption( $string_file.$string_target.$string_parameter.$string_level ),
			'value'  => $string_value
		);
	}
	public static function jQueryAddressLink( $string_href, $string_value ){
		return '<a href="'.$string_href.'" rel="address:/'.$string_href.'">'.$string_value.'</a>';
	}
}
?>