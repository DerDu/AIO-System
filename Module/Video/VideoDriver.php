<?php
/**
 * VideoDriver
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
 * @subpackage Video
 */
namespace AIOSystem\Module\Video;
/**
 * @package AIOSystem\Module
 * @subpackage Video
 */
class ClassVideoDriver {
// Windows Media Player ------------------------------------------------------------------
	public static function driver_wmv( $string_filename, $array_option = array() )
	{
		return '<object type="video/x-ms-wmv" data="'.$string_filename.'" width="'.$array_option['width'].'" height="'.$array_option['height'].'">
				<param name="src" value="'.$string_filename.'" />
				<param name="autostart" value="'.$array_option['autoplay'].'" />
				<param name="controller" value="'.$array_option['controls'].'" />
				</object>';
	}
	public static function driver_asx( $string_filename, $array_option = array() )
	{
		return self::driver_wmv( $string_filename, $array_option );
	}
	public static function driver_asf( $string_filename, $array_option = array() )
	{
		return self::driver_wmv( $string_filename, $array_option );
	}
	public static function driver_avi( $string_filename, $array_option = array() )
	{
		return self::driver_wmv( $string_filename, $array_option );
	}
// Real Player ---------------------------------------------------------------------------
	public static function driver_rmv( $string_filename, $array_option = array() )
	{
		return '<object id="myMovie" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="'.$array_option['width'].'" height="'.$array_option['height'].'">
					<param name="src" value="'.$string_filename.'" />
					<param name="console" value="video1" />
					<param name="controls" value="'.($array_option['controls']?'all':'false').'" />
					<param name="autostart" value="'.$array_option['autoplay'].'" />
					<param name="loop" value="'.$array_option['loop'].'" />
					<embed name="myMovie" src="'.$string_filename.'" width="'.$array_option['width'].'" height="'.$array_option['height'].'"
					autostart="'.$array_option['autoplay'].'" loop="false" nojava="false" console="video1" controls="'.($array_option['controls']?'controlpanel':'false').'">
					</embed>
					<noembed><a href="'.$string_filename.'">Play first clip</a></noembed>
				</object>';
	}
	public static function driver_rmvb( $string_filename, $array_option = array() )
	{
		return self::driver_rmv( $string_filename, $array_option );
	}
// Quicktime -----------------------------------------------------------------------------
	public static function driver_mov( $string_filename, $array_option = array() )
	{
		return '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$array_option['width'].'" height="'.$array_option['height'].'">
					<param name="src" value="'.$string_filename.'" />
					<param name="controller" value="'.$array_option['controls'].'" />
					<param name="autoplay" value="'.$array_option['autoplay'].'" />
					<!--[if !IE]>-->
						<object type="video/quicktime"
						data="'.$string_filename.'"
						width="'.$array_option['width'].'" height="'.$array_option['height'].'">
						<param name="autoplay" value="'.$array_option['autoplay'].'" />
						<param name="controller" value="'.$array_option['controls'].'" />
						</object>
					<!--<![endif]-->
				</object>';
	}
// MPEG ----------------------------------------------------------------------------------
	public static function driver_mpg( $string_filename, $array_option = array() )
	{
		return '<embed src="'.$string_filename.'" autostart='.$array_option['autoplay'].' loop='.($array_option['loop']?'true':'false').' controller='.$array_option['controls'].' width="'.$array_option['width'].'" height="'.$array_option['height'].'" />';
	}
	public static function driver_mpeg( $string_filename, $array_option = array() )
	{
		return self::driver_mpg( $string_filename, $array_option );
	}
// Flash Video Player --------------------------------------------------------------------
	public static function driver_flv( $string_filename, $array_option = array() )
	{
		return '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>'
				.'<div id="MyMovie"><a href="http://www.macromedia.com/go/getflashplayer">Baixe o flash player</a> para visualizar este.</div>
				<script type="text/javascript">
					var s1 = new SWFObject("http://ianitsky.com/files/flvplayer.swf", "single","'.$array_option['width'].'","'.$array_option['height'].'","7");
					s1.addParam("allowfullscreen","true");
					s1.addVariable("file","'.$string_filename.'");
					s1.addVariable("showdigits", "'.$array_option['controls'].'");
					s1.addVariable("autostart", "'.$array_option['autoplay'].'");
					s1.write("MyMovie");
				</script>';
	}
// Flash Player --------------------------------------------------------------------------
	public static function driver_swf( $string_filename, $array_option = array() )
	{
		return '<object type="application/x-shockwave-flash" data="'.$string_filename.'" id="swf'.$array_option['player-id'].'" width="'.$array_option['width'].'" height="'.$array_option['height'].'">
					<param name="movie" value="'.$string_filename.'" />
					<param name="quality" value ="high" />
					<param name="loop" value="'.($array_option['loop']?'true':'false').'" />
					<param name="menu" value="'.$array_option['controls'].'" />
					<param name="swliveconnect" value="true" />
				</object>';
	}
}
?>