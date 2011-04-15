<?php
/**
 * Video
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
use \AIOSystem\Api\System;
use \AIOSystem\Api\Seo;
/**
 * @package AIOSystem\Module
 * @subpackage Video
 */
interface InterfaceVideo {
	public static function Load( $string_filename, $array_option = array() );
}
/**
 * @package AIOSystem\Module
 * @subpackage Video
 */
class ClassVideo implements InterfaceVideo
{
	public static function Load( $string_filename, $array_option = array() )
	{
		$array_allowed_option = array(
			'width'=>320,'height'=>180,
			'autoplay'=>true,'loop'=>true,'controls'=>true,
			'slide_speed'=>6.0, 'fade_speed'=>3.0,
			'background'=>'#000000',
			'ajaxload'=>false
		);
		$array_option = array_merge( $array_allowed_option, $array_option );
		$array_option['player-id'] = sha1(microtime(true));

		if( !is_file( $string_filename ) && is_dir( $string_filename ) )
		return self::_imageSlide( $string_filename, $array_option );

		if( !class_exists( __NAMESPACE__.'\ClassVideoDriver') ) require_once(dirname(__FILE__) . '/VideoDriver.php');
		$string_extension = strtolower( pathinfo( trim( $string_filename ), PATHINFO_EXTENSION ) );
		if( method_exists( __NAMESPACE__.'\ClassVideoDriver', 'driver_'.$string_extension ) ){
			if( $array_option['ajaxload'] ) {
				$string_player_markup = '<div id="module-video-player-'.$array_option['player-id'].'" style="background:'.$array_option['background'].';width:'.$array_option['width'].'px;height:'.$array_option['height'].'px;"></div>';
				$string_player_markup .= '<script type="text/javascript">';
					$string_player_markup .= 'jQuery(document).ready(function() {';
						$string_player_markup .= 'jQuery(\'div#module-video-player-'.$array_option['player-id'].'\').html(\''.call_user_func_array( 'ClassVideoDriver::driver_'.$string_extension, array( $string_filename, $array_option ) ).'\');';
					$string_player_markup .= '});';
				$string_player_markup .= '</script>';
				return $string_player_markup;
			} else {
				return call_user_func_array( __NAMESPACE__.'\ClassVideoDriver::driver_'.$string_extension, array( $string_filename, $array_option ) );
			}
		} else {
			throw new \Exception( 'Driver not available!' );
		}
	}
	private static function _imageSlide( $string_directory, $array_option )
	{
		$array_files = System::FileList( $string_directory, array('jpg','png','jpeg','bmp','tif') );

		$str_return = '<div id="module-video-player-'.$array_option['player-id'].'" style="width: '.$array_option['width'].'px; height: '.$array_option['height'].'px; position: relative;">
		<img alt="" src="'.$array_files[0]->propertyFileLocation().'" style=" position: absolute; width: '.$array_option['width'].'px; height: '.$array_option['height'].'px; display: none;" />
		<img alt="" src="'.$array_files[0]->propertyFileLocation().'" style=" position: absolute; width: '.$array_option['width'].'px; height: '.$array_option['height'].'px; display: none;" />
		</div>';
		$array_slide['definition'] = '';
		$array_slide['preload'] = '';
		/** @var $object_file \AIOSystem\Core\ClassSystemFile */
		foreach ( (array) $array_files as $integer_file => $object_file ){
			$array_slide['definition'] .= ' arr_slide['.$integer_file.'] = "'.Seo::Path( $object_file->propertyFileLocation() ).'"; ';
			$array_slide['preload'] .= ' preload_slide.src = "'.Seo::Path( $object_file->propertyFileLocation() ).'"; ';
		}
		$array_option['fade_speed'] = ($array_option['fade_speed']*1000);
		$array_option['slide_speed'] = ($array_option['slide_speed']*1000);

		$str_return .= '<script type="text/javascript">
			jQuery(\'div#module-video-player-'.$array_option['player-id'].'\').hide();
			jQuery(\'div#module-video-player-'.$array_option['player-id'].'\').css({background:\''.$array_option['background'].'\'});
			jQuery(\'div#module-video-player-'.$array_option['player-id'].'\').fadeIn('.$array_option['fade_speed'].');
			function module_video_player_slide_'.$array_option['player-id'].'( int_slide, flg_img )
			{
				var object_player = jQuery(\'div#module-video-player-'.$array_option['player-id'].'\');
				var preload_slide = new Image(); '.$array_slide['preload'].'
				var arr_slide = new Array(); '.$array_slide['definition'].'

				if( flg_img == 0 ) { flg_img = 1; } else { flg_img = 0; }
				if( flg_img ) {
					object_player.find(\'img:eq(1)\').attr(\'src\',arr_slide[int_slide]).fadeIn('.$array_option['fade_speed'].');
					object_player.find(\'img:eq(0)\').fadeOut('.$array_option['fade_speed'].');
				} else {
					object_player.find(\'img:eq(0)\').attr(\'src\',arr_slide[int_slide]).fadeIn('.$array_option['fade_speed'].');
					object_player.find(\'img:eq(1)\').fadeOut('.$array_option['fade_speed'].');
				}
				if( arr_slide.length == 1 ) return;

				int_slide += 1; if( int_slide >= arr_slide.length ) int_slide = 0;
				window.setTimeout("module_video_player_slide_'.$array_option['player-id'].'( "+int_slide+", "+flg_img+" )",'.$array_option['slide_speed'].');
			}';
		if( $array_option['autoplay'] ) {
			is_bool( $array_option['autoplay'] ) ? $int_autoplay = 1000 : $int_autoplay = $array_option['autoplay'];
			$str_return .= 'window.setTimeout("module_video_player_slide_'.$array_option['player-id'].'(0,0);",'.$int_autoplay.');';
		}
		return $str_return.'</script>';
	}
	/*
	  *
	 * 	function module_video_img2swf( $val_parameter = "" )
	{
		// ----------------------
		// Fetch Parameter
			$val_parameter = system::system_parameter( $val_parameter, "file", array("file"), array(), __CLASS__.".".__FUNCTION__ );
		// ----------------------

			$arr_file = system::file_info( $val_parameter[file] );

			switch ( $arr_file[extension] )
			{
				case "png":
					{
						if( !file_exists( $arr_file[path]."/".$arr_file[name].".dbl" ) )
						$val_parameter[file] = module_image::module_image_convert( array("file"=>$val_parameter[file],"type"=>"dbl") );
						else
						$val_parameter[file] = $arr_file[path]."/".$arr_file[name].".dbl";
						break;
					}
				default :
					{
						break;
					}
			}
			$val_image = file_get_contents( $val_parameter[file] );

			$obj_shape = new swfshape();
			$obj_image = new swfbitmap( $val_image );
			$res_image = $obj_shape->addfill( $obj_image );

			$obj_shape->setrightfill( $res_image );
			$obj_shape->drawLine( $obj_image->getwidth(),0 );
			$obj_shape->drawLine( 0, $obj_image->getheight() );
			$obj_shape->drawLine( ($obj_image->getwidth()*-1), 0 );
			$obj_shape->drawLine( 0, ($obj_image->getheight()*-1) );

			$int_width = $obj_image->getWidth();
			$int_height = $obj_image->getHeight();

			$res_movie = new swfmovie();
			$res_movie->setDimension( $int_width, $int_height );
			$res_movie->add( $obj_shape );

			$res_movie->save( $arr_file[path]."/".$arr_file[name].".swf" );

			return $arr_file[path]."/".$arr_file[name].".swf";
	}
	 */
}
?>