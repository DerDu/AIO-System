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
use \AIOSystem\Module\Database\DatabaseRoute;
use \AIOSystem\Api\Session;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Cache;
use \AIOSystem\Api\Xml;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Database
 */
interface InterfaceDatabase {
	public static function Open( $HostType, $HostName = null, $UserName = null, $UserPassword = null, $DatabaseName = null, $asDsn = false );
	public static function Pipe();
	public static function Close();

	public static function Route( $Identifier = null );
	public static function RouteList();

	public static function RouteEngine();
	public static function RouteHost();
	public static function RouteDatabase();
	public static function RouteUser();
	public static function RoutePassword();

	public static function Execute( $Statement, $Cache = false );
	public static function LastId();

	public static function CreateTable( $Table, $FieldSet );
	public static function DropTable( $Table );
	public static function Structure( $XmlStructureFile, $DropBeforeCreate = false );

	public static function Record( $Table, $FieldSet = array(), $Where = null, $Delete = false );
	public static function RecordSet( $Table, $WhereOrderBy, $asResultArray = false );

	public static function BeginTransaction();
	public static function CompleteTransaction();
}
/**
 * @package AIOSystem\Module
 * @subpackage Database
 */
class Database implements InterfaceDatabase {
	const DEBUG = false;
	const ADODB_CACHE_DIR = 'Adodb5Sql';
	const ADODB_ASSOC_CASE = 2;

	/** @var array|DatabaseRoute[] $DatabaseRoute */
	private static $DatabaseRouteList = array();
	/** @var null|DatabaseRoute $DatabaseRoute */
	private static $DatabaseRoute = null;

	/**
	 * @static
	 * @param string $HostType
	 * @param null|string $HostName
	 * @param null|string $UserName
	 * @param null|string $UserPassword
	 * @param null|string $DatabaseName
	 * @return string
	 */
	public static function Open( $HostType, $HostName = null, $UserName = null, $UserPassword = null, $DatabaseName = null, $asDsn = false ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$Route = new DatabaseRoute( $HostType, $HostName, $UserName, $UserPassword, $DatabaseName, $asDsn );
		self::$DatabaseRouteList[$Route->Identifier()] = $Route;
		self::_SaveDatabaseRouteList();
		self::$DatabaseRoute = $Route;
		self::_SaveDatabaseRoute();
		$Route->Open();
		return $Route->Identifier();
	}
	/**
	 * RoutePipe -> ADODB
	 *
	 * @static
	 * @throws \Exception
	 * @return \ADOConnection
	 */
	public static function Pipe() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		return self::DatabaseRoute()->Pipe();
	}
	/**
	 * Close current route
	 *
	 * @static
	 * @return void
	 */
	public static function Close() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		self::Pipe()->Close();
		unset( self::$DatabaseRouteList[self::$DatabaseRoute->Identifier()] );
		self::_SaveDatabaseRouteList();
		self::$DatabaseRoute = end(self::$DatabaseRouteList);
		self::_SaveDatabaseRoute();
	}

	/**
	 * Set: Select Route / Get: Current Route-Identifier
	 *
	 * @static
	 * @param null|string $Identifier
	 * @return string
	 */
	public static function Route( $Identifier = null ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( $Identifier !== null ) {
			self::_LoadDatabaseRouteList();
			self::$DatabaseRoute = self::$DatabaseRouteList[$Identifier];
		} return self::DatabaseRoute()->Identifier();
	}
	/**
	 * @static
	 * @return array|false
	 */
	public static function RouteList() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( empty(self::$DatabaseRouteList) ) {
			self::_LoadDatabaseRouteList();
		}
		return array_keys( self::$DatabaseRouteList );
	}

	/**
	 * @static
	 * @return null|string
	 */
	public static function RouteEngine() {
		return self::$DatabaseRoute->HostType();
	}
	/**
	 * @static
	 * @return null|string
	 */
	public static function RouteDatabase() {
		return self::$DatabaseRoute->DatabaseName();
	}
	/**
	 * @static
	 * @return null|string
	 */
	public static function RouteHost() {
		return self::$DatabaseRoute->HostName();
	}
	/**
	 * @static
	 * @return null|string
	 */
	public static function RoutePassword() {
		return self::$DatabaseRoute->UserPassword();
	}
	/**
	 * @static
	 * @return null|string
	 */
	public static function RouteUser() {
		return self::$DatabaseRoute->UserName();
	}

	/**
	 * @static
	 * @throws \Exception
	 * @param string $Statement
	 * @param bool $Cache
	 * @return bool
	 */
	public static function Execute( $Statement, $Cache = false ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		//$Timeout = ini_set('max_execution_time',0);
		self::Pipe()->SetFetchMode( ADODB_FETCH_ASSOC );
		if( $Cache === false ) {
			$Result = self::Pipe()->Execute( $Statement );
		} else {
			global $ADODB_CACHE_DIR;
			$ADODB_CACHE_DIR = Cache::Location( self::ADODB_CACHE_DIR );
			$Result = self::Pipe()->CacheExecute( ($Cache===true?30:$Cache), $Statement );
		}
		if( $Result === false ) {
			throw new \Exception( 'Execution failed!'
				.'<br/><br/>'.self::Pipe()->ErrorNo().' : '.self::Pipe()->ErrorMsg()."\n\n"
				.'<blockquote>'.$Statement.'</blockquote>'
			);
		} else {
			if( preg_match( '!^select!is', trim($Statement) ) ) {
				return $Result->GetArray();
			} else {
				return true;
			}
		}
	}
	/**
	 * @static
	 * @param string $Table
	 * @param string $Column
	 * @return int
	 */
	public static function LastId( $Table = '', $Column = '' ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		return self::Pipe()->Insert_ID( $Table, $Column );
	}

	/**
	 * Create Table
	 *
	 * -------------------------------
	 * Type:
	 * -------------------------------
	 * C:  Varchar, capped to 255 characters.
	 * X:  Larger varchar, capped to 4000 characters (to be compatible with Oracle).
	 * XL: For Oracle, returns CLOB, otherwise the largest varchar size.
	 * C2: Multibyte varchar
	 * X2: Multibyte varchar (largest size)
	 * B:  BLOB (binary large object)
	 * D:  Date (some databases do not support this, and we return a datetime type)
	 * T:  Datetime or Timestamp accurate to the second.
	 * TS: Datetime or Timestamp supporting Sub-second accuracy.
	 * 	Supported by Oracle, PostgreSQL and SQL Server currently.
	 * 	Otherwise equivalent to T.
	 * L:  Integer field suitable for storing booleans (0 or 1)
	 * I:  Integer (mapped to I4)
	 * I1: 1-byte integer
	 * I2: 2-byte integer
	 * I4: 4-byte integer
	 * I8: 8-byte integer
	 * F:  Floating point number
	 * N:  Numeric or decimal number
	 * -------------------------------
	 * Options:
	 * -------------------------------
	 * AUTO            For autoincrement number. Emulated with triggers if not available.
	 *                 Sets NOTNULL also.
	 * AUTOINCREMENT   Same as auto.
	 * KEY             Primary key field. Sets NOTNULL also. Compound keys are supported.
	 * PRIMARY         Same as KEY.
	 * DEF             Synonym for DEFAULT for lazy typists.
	 * DEFAULT         The default value. Character strings are auto-quoted unless
	 *                 the string begins and ends with spaces, eg ' SYSDATE '.
	 * NOTNULL         If field is not null.
	 * DEFDATE         Set default value to call function to get today's date.
	 * DEFTIMESTAMP    Set default to call function to get today's datetime.
	 * NOQUOTE         Prevents autoquoting of default string values.
	 * CONSTRAINTS     Additional constraints defined at the end of the field
	 *                 definition.
	 * -------------------------------
	 * @static
	 * @param string $Table
	 * @param array $FieldSet Array( Array( Name, Type, Size, Options ... ), ... )
	 * @return void
	 */
	public static function CreateTable( $Table, $FieldSet ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$Pipe =  self::Pipe();
		$Dictionary = \NewDataDictionary( $Pipe );
		return $Dictionary->ExecuteSQLArray(
			$Dictionary->CreateTableSQL( $Table, $FieldSet )
		);
	}
	/**
	 * Drop Table
	 *
	 * @static
	 * @param string $Table
	 * @return void
	 */
	public static function DropTable( $Table ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$Pipe =  self::Pipe();
		$Dictionary = \NewDataDictionary( $Pipe );
		return $Dictionary->ExecuteSQLArray(
			$Dictionary->DropTableSQL( $Table )
		);
	}
	/**
	 * Create database structure
	 *
	 * @static
	 * @param string $XmlStructureFile
	 * @param bool $DropBeforeCreate
	 * @return void
	 */
	public static function Structure( $XmlStructureFile, $DropBeforeCreate = false ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		$XmlStructure = Xml::Parser( $XmlStructureFile )->searchXmlNode('database_definition');
		$XmlTableList = $XmlStructure->groupXmlNode( 'database_table' );
		/** @var \AIOSystem\Core\ClassXmlNode $Table */
		foreach( (array)$XmlTableList as $Table ){
			$TableList = array();
			$XmlColumnList = $Table->groupXmlNode( 'database_column' );
			/** @var \AIOSystem\Core\ClassXmlNode $Column */
			foreach( (array)$XmlColumnList as $Column ){
				$ColumnList = array();
				array_push( $ColumnList, $Column->propertyAttribute( 'column_name' ) );
				array_push( $ColumnList, $Column->propertyAttribute( 'column_type' ) );
				array_push( $ColumnList, $Column->propertyAttribute( 'column_size' ) );
				$XmlOptionList = $Column->groupXmlNode( 'database_option' );
				/** @var \AIOSystem\Core\ClassXmlNode $Option */
				foreach( (array)$XmlOptionList as $Option ){
					// Option => Column
					if( strlen( $Option->propertyContent() ) == 0 ){
						array_push( $ColumnList, $Option->propertyAttribute( 'option_name' ) );
					} else {
						$ColumnList[$Option->propertyAttribute( 'option_name' )] = $Option->propertyContent();
					}
				}
				// Column => Table
				array_push( $TableList, $ColumnList );
			}
			// Drop Table ?
			if( $DropBeforeCreate ){
				self::DropTable( $Table->propertyAttribute( 'table_name' ) );
			}
			// Table => Database
			self::CreateTable( $Table->propertyAttribute( 'table_name' ) ,$TableList );
		}
	}

	/**
	 * @static
	 * @param string $Table
	 * @param array $FieldSet
	 * @param array $Where
	 * @param bool $Delete
	 * @return array|bool
	 */
	public static function Record( $Table, $FieldSet = array(), $Where = array(), $Delete = false ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		// TODO: [FIX BUG] In shell.Record oAR->Save on Fieldset DB != Fieldset INSERT (e.g MSSQL NOT NULL)
		// TODO: [REMOVE] Unstable Bugfix
		if( self::DEBUG )Event::Debug($Table,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($FieldSet,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($Where,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($Delete,__FILE__,__LINE__);

		return self::_recordBugFix( $Table, $FieldSet, $Where, $Delete );
	}
	/**
	 * @static
	 * @param string $Table
	 * @param string $WhereOrderBy
	 * @param bool $asResultArray
	 * @return array|void
	 */
	public static function RecordSet( $Table, $WhereOrderBy, $asResultArray = false) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($Table,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($WhereOrderBy,__FILE__,__LINE__);
		if( self::DEBUG )Event::Debug($asResultArray,__FILE__,__LINE__);
		$ADODB_ASSOC_CASE = self::ADODB_ASSOC_CASE;
		if( $asResultArray === false ) {
			return self::Pipe()->GetActiveRecords( $Table, $WhereOrderBy );
		} else {
			$RecordList = self::Pipe()->GetActiveRecords( $Table, $WhereOrderBy );
			$ReturnList = array();
			if( !empty( $RecordList ) ) {
				$Fieldset = $RecordList[0]->GetAttributeNames();
				foreach( (array)$RecordList as $Index => $Record ) {
					foreach( (array)$Fieldset as $Name ) {
						$ReturnList[$Index][$Name] = $Record->$Name;
					}
				}
			}
			return $ReturnList;
		}
	}

	/**
	 * Begin transaction
	 *
	 * @return bool
	 */
	public static function BeginTransaction() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		return self::Pipe()->StartTrans();
	}
	/**
	 * Complete transaction (Auto:Commit/Rollback)
	 *
	 * @return bool|null
	 */
	public static function CompleteTransaction() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		return self::Pipe()->CompleteTrans();
	}

	/**
	 * AutoLoader (ADODB-Driver)
	 *
	 * @static
	 * @param null|string $Class
	 * @return void
	 */
	/*
	public static function LoadADODB( $Class = null ) {
		//require_once( __DIR__ . '/Adodb/adodb.inc.php' );
		//require_once( __DIR__ . '/Adodb/adodb-active-record.inc.php');
		if( $Class !== null && !class_exists( $Class ) ) {
			$Driver = __DIR__ . '/Adodb/drivers/'.str_replace('_','-',strtolower($Class)).'.inc.php';
			if( self::DEBUG )Event::Message(__METHOD__ . $Driver,__FILE__,__LINE__);
			if( file_exists( $Driver ) ) {
				if( self::DEBUG )Event::Message(__METHOD__.' Driver: '.$Driver,__FILE__,__LINE__);
				require_once( $Driver );
				return true;
			}
			return false;
		}
	}*/

	private static function _SaveDatabaseRouteList() {
		$OStack = Stack::Objects( __CLASS__ );
		$OStack->SaveObject( 'List', self::$DatabaseRouteList );
	}
	private static function _SaveDatabaseRoute() {
		$OStack = Stack::Objects( __CLASS__ );
		$OStack->SaveObject( 'Route', self::$DatabaseRouteList );
	}
	private static function _LoadDatabaseRouteList() {
		$OStack = Stack::Objects( __CLASS__ );
		self::$DatabaseRouteList = $OStack->LoadObject( 'List' );
	}
	private static function _LoadDatabaseRoute() {
		$OStack = Stack::Objects( __CLASS__ );
		self::$DatabaseRouteList = $OStack->LoadObject( 'Route' );
	}

	private static function DatabaseRoute() {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( empty(self::$DatabaseRouteList) ) {
			self::_LoadDatabaseRouteList();
		}
		if( self::$DatabaseRoute === null ) {
			self::_LoadDatabaseRoute();
		}
		if( self::$DatabaseRoute === null && !empty(self::$DatabaseRouteList) ) {
			self::$DatabaseRoute = end(self::$DatabaseRouteList);
		}
		if( self::$DatabaseRoute === null ) {
			throw new \Exception('No connection available!');
		}
		if( !is_object( self::$DatabaseRoute->DatabaseAdapter() ) ) {
			//self::LoadADODB();
			self::$DatabaseRoute->Open();
		}
		return self::$DatabaseRoute;
	}

	/**
	 * @static
	 * @param string $Table
	 * @param array $Fieldset
	 * @param array $Where
	 * @param bool $Delete
	 * @return array|bool
	 */
	private static function _recordBugFix( $Table, $Fieldset = array(), $Where = array(), $Delete = false ) {
		if( self::DEBUG )Event::Message(__METHOD__,__FILE__,__LINE__);
		if( $Delete ){
			if( !empty($Where) ){
				return self::Execute( "DELETE FROM ".$Table." WHERE ".implode(' AND ',(array)$Where) );
			} else {
				return false;
			}
		} else {
			$Result = self::Execute( "SELECT * FROM ".$Table." WHERE ".implode(' AND ',(array)$Where) );
			if( empty($Result) ){
				return self::Execute( "INSERT INTO ".$Table
					." ( ".implode( ', ', array_keys( (array)$Fieldset ) )." ) "
					." VALUES ( '".implode( "', '", array_values( (array)$Fieldset ) )."' ) " );
			} else {
				$Update = 'UPDATE '.$Table.' SET ';
				foreach( (array)$Fieldset as $Column => $Value ){
					$Update .= $Column." = '".$Value."', ";
				}
				return self::Execute( substr( $Update, 0, -2 )." WHERE ".implode( ' AND ', array_values( (array)$Where ) ) );
			}
		}
	}
}
