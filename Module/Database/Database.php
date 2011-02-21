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
interface InterfaceDatabase
{
	public static function database_open( $string_hosttype, $string_hostname, $string_username, $string_password, $string_database );
	public static function database_route( $string_database_route = null );
	public static function database_list();
	public static function database_execute( $string_statement, $bool_cached = false );
	public static function database_close( $string_database_route = null );

	public static function database_route_engine();
	public static function database_route_host();
	public static function database_route_database();
	public static function database_route_user();

	public static function database_create_table( $string_table_name, $array_table_fieldset );
	public static function database_drop_table( $string_table_name );

	public static function database_recordset( $string_table_name, $string_where_order_by, $bool_resultset = false );
	public static function database_record( $string_table_name, $array_fieldset = array(), $array_where = null, $bool_delete = false );

	public static function database_adodb5();
	public static function database_structure( $string_xml_file, $bool_drop = false );
}
/**
 * @package AioSystem\Module
 * @subpackage Database
 */
class ClassDatabase implements InterfaceDatabase
{
	/**
	 * @var \AioSystem\Module\Database\ClassAdodb[] $database_stage
	 */
	private static $database_stage = array();
	private static $database_route = null;
	private static $database_mtimeout = null;

	public function __construct() {
	}
	public static function database_adodb5()
	{
		return self::database_stage()->adodb5_object();
	}

	private static function database_session_set(){
		\AioSystem\Core\ClassSession::writeSession(
			'AIO-Database[ROUTE]',
			\AioSystem\Library\ClassEncryption::encodeSessionEncryption(
				serialize( self::database_route() )
			)
		);
		\AioSystem\Core\ClassSession::writeSession(
			'AIO-Database[STAGE]',
			\AioSystem\Library\ClassEncryption::encodeSessionEncryption(
				serialize( self::$database_stage )
			)
		);
	}
	private static function database_session_get(){
		if( self::$database_route === null ){
			$string_route = unserialize(
			\AioSystem\Library\ClassEncryption::decodeSessionEncryption(
				\AioSystem\Core\ClassSession::readSession( 'AIO-Database[ROUTE]' )
			));
			if( substr( $string_route, 0, 5 ) == 'ROUTE' ){
				self::$database_route = $string_route;
			}
		}
		if( count( self::$database_stage ) < 1 ){
			// Load Class
			if( !class_exists('cls__shell_adodb5') ) {
				require_once(__DIR__ . '/Adodb.php');
				if( !class_exists('ADOConnection') ) {
					require_once(__DIR__ . '/Adodb/adodb.inc.php');
				}
			}
			// Load Driver
			if( !class_exists( 'ADODB_'.strtolower(self::database_route_engine()) ) ) {
				require_once( __DIR__.('/Adodb/drivers/adodb-'.strtolower(self::database_route_engine()).'.inc.php') );
			}
			$array_stage = unserialize(
			\AioSystem\Library\ClassEncryption::decodeSessionEncryption(
				\AioSystem\Core\ClassSession::readSession( 'AIO-Database[STAGE]' )
			));
			if( is_array( $array_stage ) ){
				self::$database_stage = $array_stage;
			}
		}
	}

	public static function database_open( $string_hosttype, $string_hostname, $string_username, $string_password, $string_database )
	{
		// Engine: Module-XMLFFDB ?
		//if( strtoupper( $string_hosttype ) == 'XMLFFDB' ){
			//if( !class_exists( 'cls__module_xmlffdb_engine' ) ) require_once( __DIR__.'/../module/module.xmlffdb_engine.php' );
			//$object_adodb5_instance = new cls__module_xmlffdb_engine();
			//$string_username = $string_password = 'DUMMY';
		//} else {
			$object_adodb5_instance = ClassAdodb::Instance();
		//}
		self::database_route( strtoupper('ROUTE[ENGINE:'.$string_hosttype.':HOST:'.$string_hostname.':DB:'.$string_database.':USER:'.$string_username.']') );
		$object_adodb5_instance->openAdodb( $string_hosttype, $string_hostname, $string_username, $string_password, $string_database );
		self::$database_stage[self::$database_route] = $object_adodb5_instance;

		self::database_session_set();

		return self::$database_route;
	}
	public static function database_list()
	{
		self::database_session_get();
		return array_keys( self::$database_stage );
	}
	public static function database_route( $string_database_default = null )
	{
		if( $string_database_default !== null ) self::$database_route = $string_database_default;
		return self::$database_route;
	}
	public static function database_route_engine()
	{
		preg_match( '!(?<=\[ENGINE:).*?(?=:HOST:)!is', self::database_route(), $array_match );
		return $array_match[0];
	}
	public static function database_route_host()
	{
		preg_match( '!(?<=:HOST:).*?(?=:DB:)!is', self::database_route(), $array_match );
		return $array_match[0];
	}
	public static function database_route_database()
	{
		preg_match( '!(?<=:DB:).*?(?=:USER:)!is', self::database_route(), $array_match );
		return $array_match[0];
	}
	public static function database_route_user()
	{
		preg_match( '!(?<=:USER:).*?(?=\])!is', self::database_route(), $array_match );
		return $array_match[0];
	}
	public static function database_execute( $string_statement, $bool_cached = false )
	{
		self::database_mtimeout(true);
		$array_result = self::database_stage()->executeAdodb( $string_statement, $bool_cached );
		self::database_mtimeout(false);
		return $array_result;
	}
	public static function database_close( $string_database_route = null )
	{
		if( $string_database_route !== null ){
			self::database_route($string_database_route);
			self::database_stage()->closeAdodb();
			self::database_stage($string_database_route);
		} else {
			self::database_stage()->closeAdodb();
			self::database_stage(self::database_route());
		}
	}
	private static function database_stage( $string_database_route2close = null )
	{
		self::database_session_get();
		if( !isset( self::$database_stage[self::database_route()] ) ){
			throw new \Exception( 'Connection not available!<br/>'.self::database_route() );
		}
		if( $string_database_route2close !== null ) {
			unset(self::$database_stage[$string_database_route2close]);
			return true;
		}
		return self::$database_stage[self::database_route()];
	}
	private static function database_mtimeout( $bool_set = true )
	{
		if( $bool_set ){
			self::$database_mtimeout = ini_get('max_execution_time');
			ini_set('max_execution_time', 0 );
		} else {
			ini_set('max_execution_time', self::$database_mtimeout );
		}
	}
// ---------------------------------------------------------------------------------------
	public static function database_create_table( $string_table_name, $array_table_fieldset )
	{
		// $array_table_fieldset: Array( Name, Type, Size, Options.. )
		return self::database_stage()->adodb5_create_table( $string_table_name, $array_table_fieldset );
		/*
		-------------------------------
		Type:
		-------------------------------
		C:  Varchar, capped to 255 characters.
		X:  Larger varchar, capped to 4000 characters (to be compatible with Oracle).
		XL: For Oracle, returns CLOB, otherwise the largest varchar size.
		C2: Multibyte varchar
		X2: Multibyte varchar (largest size)
		B:  BLOB (binary large object)
		D:  Date (some databases do not support this, and we return a datetime type)
		T:  Datetime or Timestamp accurate to the second.
		TS: Datetime or Timestamp supporting Sub-second accuracy.
			Supported by Oracle, PostgreSQL and SQL Server currently.
			Otherwise equivalent to T.
		L:  Integer field suitable for storing booleans (0 or 1)
		I:  Integer (mapped to I4)
		I1: 1-byte integer
		I2: 2-byte integer
		I4: 4-byte integer
		I8: 8-byte integer
		F:  Floating point number
		N:  Numeric or decimal number
		-------------------------------
		Options:
		-------------------------------
		AUTO            For autoincrement number. Emulated with triggers if not available.
						Sets NOTNULL also.
		AUTOINCREMENT   Same as auto.
		KEY             Primary key field. Sets NOTNULL also. Compound keys are supported.
		PRIMARY         Same as KEY.
		DEF				Synonym for DEFAULT for lazy typists.
		DEFAULT         The default value. Character strings are auto-quoted unless
						the string begins and ends with spaces, eg ' SYSDATE '.
		NOTNULL         If field is not null.
		DEFDATE         Set default value to call function to get today's date.
		DEFTIMESTAMP    Set default to call function to get today's datetime.
		NOQUOTE         Prevents autoquoting of default string values.
		CONSTRAINTS     Additional constraints defined at the end of the field
						definition.
		*/
	}
	public static function database_drop_table( $string_table_name )
	{
		return self::database_stage()->adodb5_drop_table( $string_table_name );
	}
// ---------------------------------------------------------------------------------------
	public static function database_recordset( $string_table_name, $string_where_order_by, $bool_resultset = false )
	{
		return self::database_stage()->adodb5_recordset( $string_table_name, $string_where_order_by, $bool_resultset );
	}
	public static function database_record( $string_table_name, $array_fieldset = array(), $array_where = null, $bool_delete = false )
	{
		// TODO: [REMOVE] Unstable Bugfix
		return self::database_record_bugfix( $string_table_name, $array_fieldset, $array_where, $bool_delete );
		// TODO: [FIX BUG] In shell.adodb5_record oAR->Save on Fieldset DB != Fieldset INSERT (e.g MSSQL NOT NULL)
		//return self::database_stage()->adodb5_record( $string_table_name, $array_fieldset, $array_where, $bool_delete );
	}
	private static function database_record_bugfix( $string_table_name, $array_fieldset = array(), $array_where = null, $bool_delete = false )
	{
		if( $bool_delete ){
			if( $array_where !== null ){
				return self::database_execute( "DELETE FROM ".$string_table_name." WHERE ".implode(' AND ',(array)$array_where) );
			} else {
				return false;
			}
		} else {
			$array_result = self::database_execute( "SELECT * FROM ".$string_table_name." WHERE ".implode(' AND ',(array)$array_where) );
			if( empty($array_result) ){
				return self::database_execute( "INSERT INTO ".$string_table_name
					." ( ".implode( ', ', array_keys( (array)$array_fieldset ) )." ) "
					." VALUES ( '".implode( "', '", array_values( (array)$array_fieldset ) )."' ) " );
			} else {
				$string_sql_update = 'UPDATE '.$string_table_name.' SET ';
				foreach( (array)$array_fieldset as $string_field_name => $string_field_value ){
					$string_sql_update .= $string_field_name." = '".$string_field_value."', ";
				}
				return self::database_execute( substr( $string_sql_update, 0, -2 )." WHERE ".implode( ' AND ', array_values( (array)$array_where ) ) );
			}
		}
	}
	public static function database_structure( $string_xml_file, $bool_drop = false )
	{
		$object_database_definition = \AioSystem\Core\ClassXmlParser::parseXml( $string_xml_file )->searchXmlNode('database_definition');
		$object_database_table_list = $object_database_definition->groupXmlNode( 'database_table' );
		/** @var \AioSystem\Core\ClassXmlNode $object_database_table */
		foreach( (array)$object_database_table_list as $object_database_table ){
			$array_table = array();

			$object_database_column_list = $object_database_table->groupXmlNode( 'database_column' );
			/** @var \AioSystem\Core\ClassXmlNode $object_database_column */
			foreach( (array)$object_database_column_list as $object_database_column ){
				$array_column = array();
				array_push( $array_column, $object_database_column->propertyAttribute( 'column_name' ) );
				array_push( $array_column, $object_database_column->propertyAttribute( 'column_type' ) );
				array_push( $array_column, $object_database_column->propertyAttribute( 'column_size' ) );

				$object_database_option_list = $object_database_column->groupXmlNode( 'database_option' );
				/** @var \AioSystem\Core\ClassXmlNode $object_database_option */
				foreach( (array)$object_database_option_list as $object_database_option ){
					// Option => Column
					if( strlen( $object_database_option->propertyContent() ) == 0 )
					array_push( $array_column, $object_database_option->propertyAttribute( 'option_name' ) );
					else
					$array_column[$object_database_option->propertyAttribute( 'option_name' )] = $object_database_option->propertyContent();
				}
				// Column => Table
				array_push( $array_table, $array_column );
			}
			// Drop Table ?
			if( $bool_drop ){
				self::database_drop_table(
					$object_database_table->propertyAttribute( 'table_name' )
				);
			}
			// Table => Database
			self::database_create_table(
				$object_database_table->propertyAttribute( 'table_name' )
				,$array_table
			);
		}
	}
}
?>