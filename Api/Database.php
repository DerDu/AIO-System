<?php
/**
 * This file contains the API:Database
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
 * @package AIOSystem\Api
 */
namespace AIOSystem\Api;
use \AIOSystem\Module\Database\Database as AIODatabase;
use \AIOSystem\Module\Database\DatabaseRoute as AIODatabaseRoute;
use \AIOSystem\Module\Database\ClassHierarchicalData as AIOHierarchicalData;
/**
 * @package AIOSystem\Api
 */
class Database {
	/**
	 * Open database connection
	 *
	 * DSN example
	 * <code>
	 * driver : // username : password @ hostname / database ? options [ = value ]
	 * </code>
	 *
	 * @static
	 * @param string $Type Engine or DSN
	 * @param string|null $Host
	 * @param string|null $User
	 * @param string|null $Password
	 * @param string|null $Database
	 * @return string
	 */
	public static function Open( $Type, $Host = null, $User = null, $Password = null, $Database = null, $Dsn = false ) {
		return AIODatabase::Open( $Type, $Host, $User, $Password, $Database, $Dsn );
	}
	/**
	 * @static
	 * @param null|string $Route
	 * @return null
	 */
	public static function Route( $Identifier = null ) {
		return AIODatabase::Route( $Identifier );
	}
	/**
	 * @static
	 * @return array|boolean|string
	 */
	public static function RouteList() {
		return AIODatabase::RouteList();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteEngine() {
		return AIODatabase::RouteEngine();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteDatabase() {
		return AIODatabase::RouteDatabase();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteHost() {
		return AIODatabase::RouteHost();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteUser() {
		return AIODatabase::RouteUser();
	}
	/**
	 * Execute sql statement
	 *
	 * @static
	 * @param string $Sql
	 * @param bool $Cache
	 * @return array
	 */
	public static function Execute( $Sql, $Cache = false ) {
		return AIODatabase::Execute( $Sql, $Cache );
	}

	public static function LastInsertId() {
		return AIODatabase::LastId();
	}
	/**
	 * Close database connection
	 *
	 * @static
	 * @param null|string $Route
	 * @return void
	 */
	public static function Close( $Route = null ) {
		AIODatabase::Close( $Route );
	}
	/**
	 * Database connection object
	 *
	 * @return \ADOConnection
	 */
	public static function ADOConnection( $ADODB_ASSOC_CASE = null ) {
		global $ADODB_ASSOC_CASE;
		if( $ADODB_ASSOC_CASE === null ) {
			$ADODB_ASSOC_CASE = 2;
		}
		return AIODatabase::Pipe();
	}
	/**
	 * Edit database record (INSERT/UPDATE)
	 *
	 * @static
	 * @param string $Table
	 * @param array $Fieldset
	 * @param null|array $Where
	 * @param bool $Delete
	 * @return bool
	 */
	public static function Record( $Table, $Fieldset = array(), $Where = null, $Delete = false ) {
		return AIODatabase::Record( $Table, $Fieldset, $Where, $Delete );
	}
	/**
	 * @param string $Table
	 * @param string $WhereOrderBy
	 * @param bool $ResultSet
	 * @return array|\ADODB_Active_Record[]|void
	 */
	public static function RecordSet( $Table, $WhereOrderBy, $ResultSet = false ) {
		return AIODatabase::RecordSet( $Table, $WhereOrderBy, $ResultSet );
	}
	/**
	 * Create database table
	 *
	 * <code>
	 * array( array( 'Name', 'Type', 'Length', 'Option', 'Option', ... ) )
	 * </code>
	 *
	 * Type:
	 * <ul>
	 *  <li>C:  Varchar, capped to 255 characters.</li>
	 *  <li>X:  Larger varchar, capped to 4000 characters (to be compatible with Oracle).</li>
	 *  <li>XL: For Oracle, returns CLOB, otherwise the largest varchar size.</li>
	 *  <li>C2: Multibyte varchar</li>
	 *  <li>X2: Multibyte varchar (largest size)</li>
	 *  <li>B:  BLOB (binary large object)</li>
	 *  <li>D:  Date (some databases do not support this, and we return a datetime type)</li>
	 *  <li>T:  Datetime or Timestamp accurate to the second.</li>
	 *  <li>TS: Datetime or Timestamp supporting Sub-second accuracy. Supported by Oracle, PostgreSQL and SQL Server currently. Otherwise equivalent to T.</li>
	 *  <li>L:  Integer field suitable for storing booleans (0 or 1)</li>
	 *  <li>I:  Integer (mapped to I4)</li>
	 *  <li>I1: 1-byte integer</li>
	 *  <li>I2: 2-byte integer</li>
	 *  <li>I4: 4-byte integer</li>
	 *  <li>I8: 8-byte integer</li>
	 *  <li>F:  Floating point number</li>
	 *  <li>N:  Numeric or decimal number</li>
	 * </ul>
	 *
	 * Options:
	 * <ul>
	 *  <li>AUTO            <br/>For autoincrement number. Emulated with triggers if not available. Sets NOTNULL also.</li>
	 *  <li>AUTOINCREMENT   <br/>Same as auto.</li>
	 *  <li>KEY             <br/>Primary key field. Sets NOTNULL also. Compound keys are supported.</li>
	 *  <li>PRIMARY         <br/>Same as KEY.</li>
	 *  <li>DEF             <br/>Synonym for DEFAULT for lazy typists.</li>
	 *  <li>DEFAULT         <br/>The default value. Character strings are auto-quoted unless the string begins and ends with spaces, eg ' SYSDATE '.</li>
	 *  <li>NOTNULL         <br/>If field is not null.</li>
	 *  <li>DEFDATE         <br/>Set default value to call function to get today's date.</li>
	 *  <li>DEFTIMESTAMP    <br/>Set default to call function to get today's datetime.</li>
	 *  <li>NOQUOTE         <br/>Prevents autoquoting of default string values.</li>
	 *  <li>CONSTRAINTS     <br/>Additional constraints defined at the end of the field definition.</li>
	 * </ul>
	 *
	 * @static
	 * @param string $Name
	 * @param array $Fieldset
	 * @return bool
	 */
	public static function CreateTable( $Name, $Fieldset ) {
		return AIODatabase::CreateTable( $Name, $Fieldset );
	}
	/**
	 * Drop database table
	 *
	 * @static
	 * @param string $Name
	 * @return bool
	 */
	public static function DropTable( $Name ) {
		return AIODatabase::DropTable( $Name );
	}
	/**
	 * @static
	 * @param string $XmlFile
	 * @param bool $Drop
	 * @return void
	 */
	public static function CreateStructure( $XmlFile, $Drop = false ) {
		AIODatabase::Structure( $XmlFile, $Drop );
	}
	/**
	 * @static
	 * @param string $TableName
	 * @return \AIOSystem\Module\Database\ClassHierarchicalData
	 */
	public static function HierarchicalData( $TableName ) {
		return AIOHierarchicalData::Instance( $TableName );
	}
	/**
	 * @static
	 * @return bool
	 */
	public static function TransactionBegin() {
		return AIODatabase::BeginTransaction();
	}
	/**
	 * @static
	 * @return bool|null
	 */
	public static function TransactionEnd() {
		return AIODatabase::CompleteTransaction();
	}
}
