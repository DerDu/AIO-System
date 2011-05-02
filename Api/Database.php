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
use \AIOSystem\Module\Database\ClassDatabase as AIODatabase;
use \AIOSystem\Module\Database\ClassHierarchicalData as AIOHierarchicalData;
/**
 * @package AIOSystem\Api
 */
class Database {
	/**
	 * Open database connection
	 *
	 * @static
	 * @param  string $Type
	 * @param  string $Host
	 * @param  string $User
	 * @param  string $Password
	 * @param  string $Database
	 * @return null|string
	 */
	public static function Open( $Type, $Host, $User, $Password, $Database ) {
		return AIODatabase::database_open( $Type, $Host, $User, $Password, $Database );
	}
	/**
	 * Open database connection
	 *
	 * <code>
	 * driver : // username : password @ hostname / database ? options [ = value ]
	 * </code>
	 *
	 * @param string $Dsn
	 * @return null|string
	 */
	public static function OpenDsn( $Dsn ) {
		return AIODatabase::database_open( $Dsn, null, null, null, null );
	}
	/**
	 * @static
	 * @param null|string $Route
	 * @return null
	 */
	public static function Route( $Route = null ) {
		return AIODatabase::database_route( $Route );
	}
	/**
	 * @static
	 * @return array|boolean|string
	 */
	public static function RouteList( $Index = null ) {
		if( $Index !== null ) {
			$List = AIODatabase::database_list();
			if( array_key_exists( $Index, $List ) ) {
				return $List[$Index];
			}
			return false;
		}
		return AIODatabase::database_list();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteEngine() {
		return AIODatabase::database_route_engine();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteDatabase() {
		return AIODatabase::database_route_database();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteHost() {
		return AIODatabase::database_route_host();
	}
	/**
	 * @static
	 * @return string
	 */
	public static function RouteUser() {
		return AIODatabase::database_route_user();
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
		return AIODatabase::database_execute( $Sql, $Cache );
	}

	public static function LastInsertId() {
		return AIODatabase::database_last_id();
	}
	/**
	 * Close database connection
	 *
	 * @static
	 * @param null|string $Route
	 * @return void
	 */
	public static function Close( $Route = null ) {
		return AIODatabase::database_close( $Route );
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
		return AIODatabase::database_record( $Table, $Fieldset, $Where, $Delete );
	}
	/**
	 * @param string $Table
	 * @param string $WhereOrderBy
	 * @param bool $ResultSet
	 * @return array
	 */
	public static function RecordSet( $Table, $WhereOrderBy, $ResultSet = false ) {
		return AIODatabase::database_recordset( $Table, $WhereOrderBy, $ResultSet );
	}
	/**
	 * Create database table
	 *
	 * Type:
	 *
	 * C:  Varchar, capped to 255 characters.
	 * X:  Larger varchar, capped to 4000 characters (to be compatible with Oracle).
	 * XL: For Oracle, returns CLOB, otherwise the largest varchar size.
	 * C2: Multibyte varchar
	 * X2: Multibyte varchar (largest size)
	 * B:  BLOB (binary large object)
	 * D:  Date (some databases do not support this, and we return a datetime type)
	 * T:  Datetime or Timestamp accurate to the second.
	 * TS: Datetime or Timestamp supporting Sub-second accuracy.
	 *     Supported by Oracle, PostgreSQL and SQL Server currently.
	 *     Otherwise equivalent to T.
	 * L:  Integer field suitable for storing booleans (0 or 1)
	 * I:  Integer (mapped to I4)
	 * I1: 1-byte integer
	 * I2: 2-byte integer
	 * I4: 4-byte integer
	 * I8: 8-byte integer
	 * F:  Floating point number
	 * N:  Numeric or decimal number
	 *
	 * Options:
	 *
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
	 *
	 * @static
	 * @param string $Name
	 * @param array $Fieldset
	 * @return bool
	 */
	public static function CreateTable( $Name, $Fieldset ) {
		return AIODatabase::database_create_table( $Name, $Fieldset );
	}
	/**
	 * Drop database table
	 *
	 * @static
	 * @param string $Name
	 * @return bool
	 */
	public static function DropTable( $Name ) {
		return AIODatabase::database_drop_table( $Name );
	}
	/**
	 * @static
	 * @param string $XmlFile
	 * @param bool $Drop
	 * @return void
	 */
	public static function CreateStructure( $XmlFile, $Drop = false ) {
		return AIODatabase::database_structure( $XmlFile, $Drop );
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
		return AIODatabase::database_begin_transaction();
	}
	/**
	 * @static
	 * @return bool|null
	 */
	public static function TransactionEnd() {
		return AIODatabase::database_complete_transaction();
	}
}
?>