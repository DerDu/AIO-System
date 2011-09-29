<?php
namespace AIOSystem\Module\Soap;
use \AIOSystem\Module\Xml\Xml;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Cache;
use \AIOSystem\Api\Proxy;
use \AIOSystem\Api\Template;

class Server {
	private $WSDLLocation = '';
	/** @var null|\SoapServer $SoapObject */
	private $SoapObject = null;

	public static function Instance( $WSDLHost = null ) {
		return new Server( $WSDLHost );
	}

	private function __construct( $WSDLHost ) {
		if( $WSDLHost !== null ) {
			$this->WSDLLocation = $WSDLHost;
			$this->SoapObject = new \SoapServer( $this->WSDLLocation );
			$this->LoadWSDL();
			$this->Run( $WSDLHost );
		}
	}

	function Run( $WSDLHost ) {
		if( !is_object( $this->SoapObject ) ) {
			$this->WSDLLocation = $WSDLHost;
			$this->SoapObject = new \SoapServer( $this->WSDLLocation );
			$this->LoadWSDL();
		}
		$this->SoapObject->handle();
		$this->ServicePage();
	}

	function CreateWSDLDocument( $WSDLFile, $ClassName, $Location = null, $Namespace = null ) {
		require_once( __DIR__ . '/WSDLDocument/WSDLDocument.php' );
		if( false !== ( $WSDLPath = realpath( pathinfo( $WSDLFile, PATHINFO_DIRNAME ) ) ) ) {
			$WSDLDocument = new \WSDLDocument( $ClassName, $Location, $Namespace );
			$WSDLFile = System::File(
				$WSDLPath.'/'.pathinfo( $WSDLFile, PATHINFO_FILENAME ).'.'.pathinfo( $WSDLFile, PATHINFO_EXTENSION )
			);
			$WSDLFile->FileContent( $WSDLDocument->saveXML() );
			$WSDLFile->writeFile();
		}
	}

	private function LoadWSDL() {
		$WSDLDefinition = Xml::Parse( $this->CacheWSDL() );
		$WSDLService = $WSDLDefinition->Like( '!service!' );
		$this->SoapObject->setClass( $WSDLService->GetAttribute('name') );
	}

	private function CacheWSDL() {
		$WSDLCacheIdentifier = sha1( $this->WSDLLocation );
		if( false === ( $WSDLCache = Cache::Get( $WSDLCacheIdentifier, __CLASS__, true ) ) ) {
			Cache::Set( $WSDLCacheIdentifier, Proxy::HttpGet( $this->WSDLLocation ), __CLASS__, true, 60 );
			$WSDLCache = Cache::Get( $WSDLCacheIdentifier, __CLASS__, true );
		}
		return $WSDLCache;
	}

	private function ServicePage() {
		if( !in_array( 'WSDL', array_map( create_function('$K','return strtoupper($K);'), array_keys( $_REQUEST ) ) ) ) {
			$SoapServerDefinition = Xml::Parse( System::File( System::DirectoryCurrent().'/WSDLFile.xml' )->FileContent() );
			$SoapServerService = $SoapServerDefinition->Like('!service!');
			$SoapServerPortType = $SoapServerDefinition->Like('!portType!');

			$HtmlTemplate = Template::Load(
			'
			<!DOCTYPE html
					PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
			<head>
				<meta http-equiv="content-type" content="text/html;charset=utf-8" />
				<title>Soap</title>
				<style type="text/css">
					* {
						font-family: arial, monospace;
						font-size: 14px;
						color: #333333;
						background-color: #F0F0F0;
						padding: 0;
						border: 0;
						margin: 0;
					}
					html {
						background-color: #F6F7F8;
					}
					body {
						width: 70%;
						height: 96%;
						margin: auto;
						margin-top: 1%;
						border: 1px solid silver;
						padding: 1px;
					}
					h1 {
						font-size: 1.8em;
						background-color: #F6F6F6;
						padding: 8px 8px 8px 6px;
						margin-bottom: 4px;
						border-bottom: 1px solid #DDDDDD;
					}
					h1:before {
						font-weight: normal;
						font-size: 1.1em;
						background-color: transparent;
						content: "Service - ";
					}
					h2 {
						font-size: 1.5em;
						background-color: #F6F6F6;
						padding: 6px 6px 6px 8px;
						border-top: 1px solid #DDDDDD;
					}
					h3 {
						font-size: 1.2em;
						background-color: #F3F3F3;
						padding: 6px 4px 4px 10px;
						font-weight: bold;
						font-style: oblique;
						color: #6A0A0A;
						border-top: 1px solid #E6E6E6;
						border-bottom: 1px solid #E6E6E6;
					}
					h3:before {
						content: "\2023  ";
						font-style: normal;
					}
					p {
						padding: 8px 10px 8px 10px;
						font-size: 15px;
						line-height: 150%;
					}
					blockquote {
						margin: 5px;
						padding: 5px;
						background-color: #e6e6e6;
						font-size: 1.15em;
					}
					blockquote > p {
						padding: 4px 10px 4px 10px;
						line-height: normal;
						margin-bottom: 1px;
						margin-top: 1px;
					}

					span {
						font-size: 0.85em;
						color: #999999;
					}
					div {
						font-size: 0.9em;
						margin-left: 10px;
					}
					a {
						text-decoration: none;
						background-color: transparent;
						font-size: 0.8em;
					}
					a:before {
						content: " \2022  ";
						color: #6A0A0A;
						font-size: 1.1em;
					}
					a:after {
						content: " \2023";
						color: #6A0A0A;
						font-size: 1.2em;
					}
				</style>
				</head>
				<body>
					<h1>{service:name}</h1>
					<p>{service:documentation}<br/><a href="?WSDL" target="_blank">WSDL Definition</a></p>
					<h2>Operation List</h2>
					{operation:list}
					<h3>{operation:name}</h3>
					<p>{operation:documentation}</p>
							<div>
								<span>Input-Message</span>
								<blockquote>
									{operation:input}
								</blockquote>
								<span>Input-Parameter</span>
								<blockquote>
									{operation:input:parameter:list}
										<p>{parameter:name} <span>{parameter:type}</span></p>
									{/operation:input:parameter:list}
								</blockquote>
								<span>Output-Message</span>
								<blockquote>
									{operation:output}
								</blockquote>
								<span>Output-Parameter</span>
								<blockquote>
									{operation:output:parameter:list}
										<p>{parameter:name} <span>{parameter:type}</span></p>
									{/operation:output:parameter:list}
								</blockquote>
							</div>
					{/operation:list}
				</body>
			</html>', false, false, true );


			$HtmlTemplate->Assign( 'service:name', $SoapServerService->GetAttribute('name') );
			$HtmlTemplate->Assign( 'service:documentation', nl2br( $SoapServerService->Like('!documentation!')->Content() ) );

			$RunMessage = 0;
			$SoapServerMessageList = array();
			while( is_object( $SoapServerMessage = $SoapServerDefinition->Like( '!message!', null, $RunMessage++ ) ) ) {
				$RunPart = 0;
				while( is_object( $SoapServerPart = $SoapServerMessage->Like( '!part!', null, $RunPart++ ) ) ) {
					$SoapServerMessageList[ $SoapServerMessage->GetAttribute('name') ][$RunPart] = array(
						'name' => $SoapServerPart->GetAttribute('name'),
						'type' => $SoapServerPart->GetAttribute('type')
					);
				}
			}

			$RunOperation = 0;
			$HtmlTemplateListOperation = array();
			while( is_object( $SoapServerOperation = $SoapServerPortType->Like( '!operation!', null, $RunOperation++ ) ) ) {
				$SoapServerOperationInput = end( explode( ':', $SoapServerOperation->Like('!input!')->GetAttribute('message') ) );
				$SoapServerOperationOutput = end( explode( ':', $SoapServerOperation->Like('!output!')->GetAttribute('message') ) );

				$HtmlTemplateListParameterInput = array();
				if( isset($SoapServerMessageList[$SoapServerOperationInput]) )
				foreach( (array)$SoapServerMessageList[$SoapServerOperationInput] as $Parameter ) {
					array_push( $HtmlTemplateListParameterInput,
						array(
							'parameter:type' => $Parameter['type'],
							'parameter:name' => $Parameter['name']
						)
					);
				}
				$HtmlTemplateListParameterOutput = array();
				if( isset($SoapServerMessageList[$SoapServerOperationOutput]) )
				foreach( (array)$SoapServerMessageList[$SoapServerOperationOutput] as $Parameter ) {
					array_push( $HtmlTemplateListParameterOutput,
						array(
							'parameter:type' => $Parameter['type'],
							'parameter:name' => $Parameter['name']
						)
					);
				}

				array_push( $HtmlTemplateListOperation,
					array(
						'operation:name' => $SoapServerOperation->GetAttribute( 'name' ),
						'operation:documentation' => nl2br( $SoapServerOperation->Like('!documentation!')->Content() ),

						'operation:input' => $SoapServerOperationInput,
						'operation:output' => $SoapServerOperationOutput,
						'operation:input:parameter:list' => $HtmlTemplateListParameterInput,
						'operation:output:parameter:list' => $HtmlTemplateListParameterOutput,
					)
				);
			}

			$HtmlTemplate->Repeat( 'operation:list', $HtmlTemplateListOperation );

			print $HtmlTemplate->Parse();
		}
	}
}
