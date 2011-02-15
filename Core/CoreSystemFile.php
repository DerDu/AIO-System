<?php
require_once( dirname(__FILE__).'/CoreSystemWrite.php' );
// ---------------------------------------------------------------------------------------
// InterfaceCoreSystemFile, ClassCoreSystemFile
// ---------------------------------------------------------------------------------------
interface InterfaceCoreSystemFile
{
	public static function Instance( $propertyFileName );
// ---------------------------------------------------------------------------------------
	public function propertyFileName( $propertyFileName = null );
	public function propertyFilePath( $propertyFilePath = null );
	public function propertyFileLocation( $propertyFileLocation = null );
	public function propertyFileSize( $propertyFileSize = null );
	public function propertyFileTime( $propertyFileTime = null );
	public function propertyFileContent( $propertyFileContent = null );
// ---------------------------------------------------------------------------------------
	public function readFile();
	public function writeFile( $_writeMode = 'wb' );
	public function writeFileAs( $propertyFileLocation, $_writeMode = 'wb' );
	public function moveFile( $propertyFileLocation );
	public function copyFile( $propertyFileLocation );
	public function removeFile();
	public function touchFile();
}
// ---------------------------------------------------------------------------------------
// LICENSE (BSD)
//
//	Copyright (c) 2011, Gerd Christian Kunze
//	All rights reserved.
//
//	Redistribution and use in source and binary forms, with or without
//	modification, are permitted provided that the following conditions are
//	met:
//
//		* Redistributions of source code must retain the above copyright
//		  notice, this list of conditions and the following disclaimer.
//		* Redistributions in binary form must reproduce the above copyright
//		  notice, this list of conditions and the following disclaimer in the
//		  documentation and/or other materials provided with the distribution.
//		* Neither the name of the Gerd Christian Kunze nor the names of its
//		  contributors may be used to endorse or promote products derived from
//		  this software without specific prior written permission.
//
//	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
//	IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
//	THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
//	PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
//	CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
//	EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
//	PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
//	PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
//	LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
//	NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
//	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
class ClassCoreSystemFile implements InterfaceCoreSystemFile
{
	private $propertyFileName = null;
	private $propertyFilePath = null;
	private $propertyFileLocation = null;
	private $propertyFileSize = null;
	private $propertyFileTime = null;
	private $propertyFileContent = null;
	private $isChanged = false;
// ---------------------------------------------------------------------------------------
	public static function Instance( $propertyFileName )
	{
		return new ClassCoreSystemFile( $propertyFileName );
	}
	public function __construct( $propertyFileLocation )
	{
		$this->propertyFileLocation( $propertyFileLocation );
		$this->_loadFileAttributeList();
		if( ! file_exists( $this->propertyFileLocation() ) ) $this->touchFile();
	}
// ---------------------------------------------------------------------------------------
	public function writeFile( $_writeMode = 'wb' )
	{
		ClassCoreSystemWrite::writeFile( $this->propertyFileLocation(), $this->propertyFileContent(), $_writeMode );
		$this->_loadFileAttributeList();
		$this->isChanged = false;
	}
	public function writeFileAs( $propertyFileLocation, $_writeMode = 'wb' )
	{
		ClassCoreSystemWrite::writeFile( $propertyFileLocation, $this->propertyFileContent(), $_writeMode );
		$this->propertyFileLocation( $propertyFileLocation );
		$this->_loadFileAttributeList();
		$this->isChanged = false;
	}
	public function readFile() {
		if( is_file( $this->propertyFileLocation() ) ){
			$this->propertyFileContent( file_get_contents( $this->propertyFileLocation() ) );
		} else {
			$this->propertyFileContent('');
		}
		$this->isChanged = false;
		return $this->propertyFileContent();
	}
	public function moveFile( $propertyFileLocation )
	{
		if( file_exists( $this->propertyFileLocation() ) )
		if( rename( $this->propertyFileLocation(), $propertyFileLocation ) ) {
			$this->propertyFileLocation( $propertyFileLocation );
			$this->_loadFileAttributeList();
		}
	}
	public function copyFile( $propertyFileLocation )
	{
		if( file_exists( $this->propertyFileLocation() ) )
		if( copy( $this->propertyFileLocation(), $propertyFileLocation ) ) {
			$this->propertyFileLocation( $propertyFileLocation );
			$this->_loadFileAttributeList();
		}
	}
	public function removeFile()
	{
		if( file_exists( $this->propertyFileLocation() ) ){
			unlink( $this->propertyFileLocation() );
			unset($this);
		}
	}
	public function touchFile()
	{
		if( strlen( $this->propertyFileLocation ) > 0 )
		fclose( fopen( $this->propertyFileLocation, 'a' ) );
	}
// ---------------------------------------------------------------------------------------
	public function propertyFileName( $propertyFileName = null ){
		if( $propertyFileName !== null ) $this->propertyFileName = $propertyFileName; return $this->propertyFileName;
	}
	public function propertyFilePath( $propertyFilePath = null ){
		if( $propertyFilePath !== null ) $this->propertyFilePath = str_replace( '\\', '/', $propertyFilePath ); return $this->propertyFilePath;
	}
	public function propertyFileLocation( $propertyFileLocation = null ){
		if( $propertyFileLocation !== null ) $this->propertyFileLocation = str_replace( '\\', '/', $propertyFileLocation ); return $this->propertyFileLocation;
	}
	public function propertyFileSize( $propertyFileSize = null ){
		if( $propertyFileSize !== null ) $this->propertyFileSize = $propertyFileSize; return $this->propertyFileSize;
	}
	public function propertyFileTime( $propertyFileTime = null ){
		if( $propertyFileTime !== null ) $this->propertyFileTime = $propertyFileTime; return $this->propertyFileTime;
	}
	public function propertyFileContent( $propertyFileContent = null ){
		if( $propertyFileContent !== null ) {
			if( $this->propertyFileContent !== null ) $this->isChanged = true;
			$this->propertyFileContent = $propertyFileContent;
		}
		if( $this->propertyFileContent === null ) $this->readFile();
		return $this->propertyFileContent;
	}
// ---------------------------------------------------------------------------------------
	private function _loadFileAttributeList()
	{
		$this->propertyFileName( basename( $this->propertyFileLocation() ) );
		$this->propertyFilePath( dirname( $this->propertyFileLocation() ) );
		if( file_exists( $this->propertyFileLocation() ) ) {
			$this->propertyFileSize( filesize( $this->propertyFileLocation() ) );
			$this->propertyFileTime( filemtime( $this->propertyFileLocation() ) );
		}
	}
// ---------------------------------------------------------------------------------------
	function __toString() {
		return $this->propertyFileLocation()."\t".date('YmdHis',$this->propertyFileTime())."\t".($this->propertyFileSize()/1024).'KB';
	}
}
?>