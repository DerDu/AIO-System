<?php
/**
 * Database
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
 * @subpackage Database
 */
namespace AIOSystem\Module\Database;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Database
 */
class DatabaseRoute {
	const DEBUG = false;

	private $HostType = null;
	private $HostName = null;

	private $UserName = null;
	private $UserPassword = null;

	private $DatabaseName = null;
	private $DatabaseAdapter = null;

	private $isDsnConnection = false;

	function __construct( $HostType, $HostName = null, $UserName = null, $UserPassword = null, $DatabaseName = null ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$this->HostType( $HostType );
		$this->HostName( $HostName );
		$this->UserName( $UserName );
		$this->UserPassword( $UserPassword );
		$this->DatabaseName( $DatabaseName );
		if( $this->HostName() === null && $this->UserName() === null && $this->UserPassword() === null && $this->DatabaseName() === null ) {
			$this->isDsnConnection = true;
		}
	}
	public function Open() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( $this->isDsnConnection ) {
			$this->DatabaseAdapter( \NewADOConnection( $this->HostType() ) );
			if( is_object( $this->DatabaseAdapter() ) ) {
				$this->DatabaseAdapter()->debug = self::DEBUG;
				$Password = array();
				preg_match('!(?<=:)[^:]*(?=@)!is',$this->HostType(),$Password);
				$this->DatabaseAdapter()->password = $Password[0];
			} else {
				throw new \Exception( 'Connection failed!' );
			}
		} else {
			$this->DatabaseAdapter( \NewADOConnection( $this->HostType() ) );
			$this->DatabaseAdapter()->debug = self::DEBUG;
			if( $this->DatabaseAdapter()->Connect( $this->HostName(), $this->UserName(), $this->UserPassword(), $this->DatabaseName() ) ) {
				$this->DatabaseAdapter()->password = $this->UserPassword();
			} else {
				throw new \Exception( 'Connection failed!' );
			}
		}
	}
	/**
	 * @return string
	 */
	public function Identifier() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( $this->isDsnConnection ) {
			return ''.preg_replace( '!(?<=:)[^:]*?@!is', '', $this->HostType() ).'';
		} else {
			return ''.$this->HostType().':'.$this->HostName().'->'.$this->UserName().'@'.$this->DatabaseName().'';
		}
	}
	/**
	 * @return \ADOConnection
	 */
	public function Pipe() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( !$this->DatabaseAdapter()->IsConnected() ) {
			$this->Open();
		}
		return $this->DatabaseAdapter();
	}
	/**
	 * @return void
	 */
	public function Close() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		return $this->DatabaseAdapter()->Close();
	}
	/**
	 * @param null|string $HostType
	 * @return null|string
	 */
	public function HostType( $HostType = null ) {
		if( $HostType !== null ) {
			$this->HostType = $HostType;
		} return $this->HostType;
	}
	/**
	 * @param null|string $HostName
	 * @return null|string
	 */
	public function HostName( $HostName = null ) {
		if( $HostName !== null ) {
			$this->HostName = $HostName;
		} return $this->HostName;
	}
	/**
	 * @param null|string $UserName
	 * @return null|string
	 */
	public function UserName( $UserName = null ) {
		if( $UserName !== null ) {
			$this->UserName = $UserName;
		} return $this->UserName;
	}
	/**
	 * @param null|string $UserPassword
	 * @return null|string
	 */
	public function UserPassword( $UserPassword = null ) {
		if( $UserPassword !== null ) {
			$this->UserPassword = $UserPassword;
		} return $this->UserPassword;
	}
	/**
	 * @param null|string $DatabaseName
	 * @return null|string
	 */
	public function DatabaseName( $DatabaseName = null ) {
		if( $DatabaseName !== null ) {
			$this->DatabaseName = $DatabaseName;
		} return $this->DatabaseName;
	}
	/**
	 * @param null|\ADOConnection $DatabaseAdapter
	 * @return \ADOConnection|null
	 */
	public function DatabaseAdapter( $DatabaseAdapter = null ) {
		if( $DatabaseAdapter !== null ) {
			$this->DatabaseAdapter = $DatabaseAdapter;
		} return $this->DatabaseAdapter;
	}
}
