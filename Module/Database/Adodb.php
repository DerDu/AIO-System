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
 * @subpackage Adodb
 */
namespace AioSystem\Module\Database;
/**
 * @package AioSystem\Module
 * @subpackage Adodb
 */
interface InterfaceAdodb {
	public static function Instance();
	public function openAdodb( $string_hosttype, $string_hostname, $string_username, $string_password, $string_database );
	public function executeAdodb( $string_sql, $bool_cache = false );
	public function closeAdodb();

	public function adodb5_create_table( $string_table_name, $array_table_fieldset );
	public function adodb5_drop_table( $string_table_name );

	public function adodb5_recordset( $string_table_name, $string_where_order_by );
	public function adodb5_record( $string_table_name, $array_fieldset = array(), $array_where = null, $bool_delete = false );
}
/**
 * @package AioSystem\Module
 * @subpackage Adodb
 */
class ClassAdodb implements InterfaceAdodb
{
	/** @var \ADOConnection $propertyAdodbResource */
	private $propertyAdodbResource = null;
	private $propertyAdodbResult = null;
	private $adodb5_cache_timeout = 30;
	private $bool_debug = false;

	public function __construct() {
	}
	public static function Instance()
	{
		if( !class_exists( 'ADOConnection' ) ) require_once(__DIR__ . '/Adodb/adodb.inc.php');
		return new ClassAdodb();
	}
	public function adodb5_object()
	{
		return $this->propertyAdodbResource;
	}
// ---------------------------------------------------------------------------------------
	public function openAdodb( $string_hosttype, $string_hostname, $string_username, $string_password, $string_database )
	{
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Open: '.$string_hostname.'|'.$string_username.'|'.$string_password.'|'.$string_database);

		$this->propertyAdodbResource( NewADOConnection( $string_hosttype ) );
		//$this->propertyAdodbResource()->debug=true;
		if( ! $this->propertyAdodbResource()->Connect( $string_hostname, $string_username, $string_password, $string_database ) )
		throw new \Exception( 'Connection failed!<br/>'.ClassDatabase::database_route() );
	}
	public function executeAdodb( $string_sql, $bool_cache = false )
	{
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Execute: '.$string_sql);
		$this->propertyAdodbResource()->SetFetchMode( ADODB_FETCH_ASSOC );

		if( $bool_cache > 1 ) $this->adodb5_cache_timeout = $bool_cache;

		if( $bool_cache ){
			global $ADODB_CACHE_DIR;
			$ADODB_CACHE_DIR =  \AioSystem\Core\ClassCacheDisc::getCacheLocation( 'AIOAdodb5Shell' );
			if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Cached: '.$ADODB_CACHE_DIR);

			$this->propertyAdodbResult( $this->propertyAdodbResource()->CacheExecute( $this->adodb5_cache_timeout, $string_sql ) );
		} else {
			$this->propertyAdodbResult( $this->propertyAdodbResource()->Execute( $string_sql ) );
		}

		if( $this->propertyAdodbResult() === false )
		throw new \Exception( 'Execution failed!'
			.'<br/><br/>'.$this->propertyAdodbResource()->ErrorNo().' : '.$this->propertyAdodbResource()->ErrorMsg()."\n\n"
			.'<blockquote>'.$string_sql.'</blockquote>' 
		);
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Result: ');
		if( preg_match( '!^select!is', trim($string_sql) ) )
		return $this->propertyAdodbResult()->GetArray();
	}
	/**
	 * @return ClassAdodb
	 */
	public function closeAdodb() {
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Close');
		return $this->propertyAdodbResource()->Close();
	}
// ---------------------------------------------------------------------------------------
	public function adodb5_create_table( $string_table_name, $array_table_fieldset )
	{
		// $array_table_fieldset: Array( Name, Type, Size, Options.. )
		$NewDataDictionary = \NewDataDictionary( $this->propertyAdodbResource );
		return $NewDataDictionary->ExecuteSQLArray(
			$NewDataDictionary->CreateTableSQL( $string_table_name, $array_table_fieldset )
		);
	}
	public function adodb5_drop_table( $string_table_name )
	{
		$NewDataDictionary = \NewDataDictionary( $this->propertyAdodbResource );
		return $NewDataDictionary->ExecuteSQLArray(
			$NewDataDictionary->DropTableSQL( $string_table_name )
		);
	}
// ---------------------------------------------------------------------------------------
	public function adodb5_recordset( $string_table_name, $string_where_order_by, $bool_resultset = false )
	{
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('RecordSet: '.$string_table_name);
		if( $bool_resultset )
		{
			$array_recordset = $this->propertyAdodbResource()->GetActiveRecords( $string_table_name, $string_where_order_by );
			$array_return = array();
			if( !empty( $array_recordset ) ) {
				$array_fieldset = $array_recordset[0]->GetAttributeNames();
				foreach( (array)$array_recordset as $index_record => $object_record ){
					foreach( (array)$array_fieldset as $string_name ){
						$array_return[$index_record][$string_name] = $object_record->$string_name;
					}
				}
			}
			return (array)$array_return;
		} else return $this->propertyAdodbResource()->GetActiveRecords( $string_table_name, $string_where_order_by );
	}
	public function adodb5_record( $string_table_name, $array_fieldset = array(), $array_where = null, $bool_delete = false )
	{
		if( $this->bool_debug ) \AioSystem\Api\ClassEvent::Debug('Record: '.$string_table_name);

		if( !class_exists( 'ADODB_Active_Record' ) ) require_once(__DIR__ . '/Adodb/adodb-active-record.inc.php');
		\ADODB_Active_Record::SetDatabaseAdapter( $this->propertyAdodbResource );
		// Create Object
		$object_record = new \ADODB_Active_Record( $string_table_name );
		if( $array_where !== null ) $object_record->Load( implode(' AND ',(array)$array_where) );

		if( $bool_delete ){
			return $object_record->Delete();
		}
		else {
			foreach( (array)$array_fieldset as $string_field_name => $mixed_field_value ){
				$object_record->$string_field_name = $mixed_field_value;
			}
			return $object_record->Save();
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
	public function __wakeup() {
		// Reestablish connection ?
		if( !$this->propertyAdodbResource->IsConnected() ) {
			$this->propertyAdodbResource->Connect( $this->propertyAdodbResource->host, $this->propertyAdodbResource->user, $this->propertyAdodbResource->password, $this->propertyAdodbResource->database );
		}
	}
}
?>