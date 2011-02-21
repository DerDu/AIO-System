<?php
/**
 * Test Environment
 * @package AioSystem
 */
/**
 * Load AioSystem\Api
 */
require_once('Api.php');
/**
 * Define API-Object usage
 */
use AioSystem\Api\ClassXml as AioXml;
use AioSystem\Api\ClassSession as AioSession;
use AioSystem\Api\ClassEvent as AioEvent;
use AioSystem\Api\ClassSocket as AioSocket;
use AioSystem\Api\ClassCache as AioCache;
use AioSystem\Api\ClassStack as AioStack;

use AioSystem\Api\ClassExcel as AioExcel;
use AioSystem\Api\ClassChart as AioChart;
use AioSystem\Api\ClassPdf as AioPdf;
use AioSystem\Api\ClassDatabase as AioDatabase;
?>
<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title></title>
</head>
<body>
<?php
	//var_dump( AioCache::Check('0') );

	//use AioSystem\Api as Aio;

	//AioSystem\Api::XmlNodeInstance();
	//var_dump( new AioSystem\Core\ClassXmlContent() );
	//var_dump( new AioSystem\Core\ClassXmlNode() );
	//var_dump( Aio::JournalWrite( 'Huhu :)' ) );
	//var_dump( AioXml::Create() );
//	var_dump( AioXml::Parser( 'Go' ) );
//	var_dump( AioSession::Read( 'Oi' ) );
//	var_dump( AioSession::Write( 'Oi', 'Nüx' ) );
//	var_dump( AioEvent::Exception( 0, 'Test', __FILE__, __LINE__ ) );

	//AioSystem\Api::JournalWrite( 'Things to come..' );
	//AioSystem\Api::SessionWrite( 'Test', null );

	//AioSystem\Api::methodName();
	//AioSystem\Api\->ClassXmlNode::Instance();

	//AioExcel::Open();
	//AioExcel::Close('testAioExcel.xls');
	//var_dump( AioChart::Instance() );
	//AioPdf::Open('testAioPdf.pdf');
	//AioPdf::Close();
?>
</body>
</html>