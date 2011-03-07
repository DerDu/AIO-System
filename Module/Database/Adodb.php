<?php
/**
 * Adodb
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
 * @subpackage Database
 */
namespace AioSystem\Module\Database;
/**
 * @package AioSystem\Module
 * @subpackage Database
 */
interface InterfaceAdodb {
	public static function Instance();

	public function openAdodb( $HostType, $HostName, $UserName, $Password, $Database );
	public function executeAdodb( $Sql, $Cache = false );
	public function closeAdodb();

	public function createTable( $TableName, $TableFieldset );
	public function dropTable( $TableName );

	public function RecordSet( $TableName, $WhereOrderBy, $ResultSet = false );
	public function Record( $TableName, $FieldSet = array(), $Where = null, $Delete = false );
}
/**
 * @package AioSystem\Module
 * @subpackage Database
 */
class ClassAdodb implements InterfaceAdodb
{
	/** @var \ADOConnection $propertyAdodbResource */
	private $propertyAdodbResource = null;
	private $propertyAdodbResult = null;
	private $adodb5_cache_timeout = 30;
	private $bool_debug = false;

	/**
	 * @return ClassAdodb
	 */
	public static function Instance() {
		if( !class_exists( 'ADOConnection' ) ) require_once(__DIR__ . '/Adodb/adodb.inc.php');
		return new ClassAdodb();
	}
	/**
	 *
	 */
	public function adodb5_object() {
		return $this->propertyAdodbResource;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @throws \Exception
	 * @param string $HostType
	 * @param string $HostName
	 * @param string $UserName
	 * @param string $Password
	 * @param string $Database
	 * @return void
	 */
	public function openAdodb( $HostType, $HostName, $UserName, $Password, $Database ) {
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Open: '.$HostName.'|'.$UserName.'|'.$Password.'|'.$Database);

		$this->propertyAdodbResource( NewADOConnection( $HostType ) );
		//$this->propertyAdodbResource()->debug=true;
		if( ! $this->propertyAdodbResource()->Connect( $HostName, $UserName, $Password, $Database ) )
		throw new \Exception( 'Connection failed!<br/>'.ClassDatabase::database_route() );
	}
	/**
	 * @throws \Exception
	 * @param string $Sql
	 * @param bool $Cache
	 * @return array
	 */
	public function executeAdodb( $Sql, $Cache = false )
	{
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Execute: '.$Sql);
		$this->propertyAdodbResource()->SetFetchMode( ADODB_FETCH_ASSOC );

		if( $Cache > 1 ) $this->adodb5_cache_timeout = $Cache;

		if( $Cache ){
			global $ADODB_CACHE_DIR;
			$ADODB_CACHE_DIR =  \AioSystem\Core\ClassCacheDisc::getCacheLocation( 'AIOAdodb5Shell' );
			if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Cached: '.$ADODB_CACHE_DIR);

			$this->propertyAdodbResult( $this->propertyAdodbResource()->CacheExecute( $this->adodb5_cache_timeout, $Sql ) );
		} else {
			$this->propertyAdodbResult( $this->propertyAdodbResource()->Execute( $Sql ) );
		}

		if( $this->propertyAdodbResult() === false )
		throw new \Exception( 'Execution failed!'
			.'<br/><br/>'.$this->propertyAdodbResource()->ErrorNo().' : '.$this->propertyAdodbResource()->ErrorMsg()."\n\n"
			.'<blockquote>'.$Sql.'</blockquote>'
		);
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Result: ');
		if( preg_match( '!^select!is', trim($Sql) ) )
		return $this->propertyAdodbResult()->GetArray();
	}
	/**
	 * @return ClassAdodb
	 */
	public function closeAdodb() {
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Close');
		return $this->propertyAdodbResource()->Close();
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param string $TableName
	 * @param array $TableFieldset
	 */
	public function createTable( $TableName, $TableFieldset ) {
		// $TableFieldset: Array( Name, Type, Size, Options.. )
		$NewDataDictionary = \NewDataDictionary( $this->propertyAdodbResource );
		return $NewDataDictionary->ExecuteSQLArray(
			$NewDataDictionary->CreateTableSQL( $TableName, $TableFieldset )
		);
	}
	/**
	 * @param string $TableName
	 */
	public function dropTable( $TableName ) {
		$NewDataDictionary = \NewDataDictionary( $this->propertyAdodbResource );
		return $NewDataDictionary->ExecuteSQLArray(
			$NewDataDictionary->DropTableSQL( $TableName )
		);
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @param string $TableName
	 * @param string $WhereOrderBy
	 * @param bool $ResultSet
	 * @return array|void
	 */
	public function RecordSet( $TableName, $WhereOrderBy, $ResultSet = false ) {
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('RecordSet: '.$TableName);
		if( $ResultSet ) {
			$RecordSet = $this->propertyAdodbResource()->GetActiveRecords( $TableName, $WhereOrderBy );
			$Return = array();
			if( !empty( $RecordSet ) ) {
				$FieldSet = $RecordSet[0]->GetAttributeNames();
				/** @var \ADODB_Active_Record $Record */
				foreach( (array)$RecordSet as $Index => $Record ) {
					foreach( (array)$FieldSet as $Name ) {
						$Return[$Index][$Name] = $Record->$Name;
					}
				}
			}
			return (array)$Return;
		} else {
			return $this->propertyAdodbResource()->GetActiveRecords( $TableName, $WhereOrderBy );
		}
	}
	/**
	 * @param string $TableName
	 * @param array $FieldSet
	 * @param null|array $Where
	 * @param bool $Delete
	 * @return bool|int
	 */
	public function Record( $TableName, $FieldSet = array(), $Where = null, $Delete = false ) {
		if( $this->bool_debug ) \AioSystem\Api\Event::Debug('Record: '.$TableName);

		if( !class_exists( 'ADODB_Active_Record' ) ) require_once(__DIR__ . '/Adodb/adodb-active-record.inc.php');
		\ADODB_Active_Record::SetDatabaseAdapter( $this->propertyAdodbResource );
		// Create Object
		$ADODB_Active_Record = new \ADODB_Active_Record( $TableName );
		if( $Where !== null ) $ADODB_Active_Record->Load( implode(' AND ',(array)$Where) );

		if( $Delete ) {
			return $ADODB_Active_Record->Delete();
		}
		else {
			foreach( (array)$FieldSet as $FieldName => $FieldValue ) {
				$ADODB_Active_Record->$FieldName = $FieldValue;
			}
			return $ADODB_Active_Record->Save();
		}
	}
// ---------------------------------------------------------------------------------------
	private function propertyAdodbResource( $propertyAdodbResource = null ) {
		if( $propertyAdodbResource !== null ) {
			$this->propertyAdodbResource = $propertyAdodbResource;
		}
		if( $this->propertyAdodbResource === null ) {
			throw new \Exception( 'No Database available!' );
		} else {
			return $this->propertyAdodbResource;
		}
	}
	private function propertyAdodbResult( $propertyAdodbResult = null ) {
		if( $propertyAdodbResult !== null ) {
			$this->propertyAdodbResult = $propertyAdodbResult;
		} return $this->propertyAdodbResult;
	}
// ---------------------------------------------------------------------------------------
	/**
	 * @return void
	 */
	public function __wakeup() {
		// Reestablish connection ?
		if( !$this->propertyAdodbResource->IsConnected() ) {
			$this->propertyAdodbResource->Connect( $this->propertyAdodbResource->host, $this->propertyAdodbResource->user, $this->propertyAdodbResource->password, $this->propertyAdodbResource->database );
		}
	}
}
?>