<?php
namespace AIOSystem\Module\Soap;
use \AIOSystem\Module\Xml\Xml;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Proxy;
use \AIOSystem\Api\Cache;
use \AIOSystem\Api\Event;

class Client {
	private $WSDLLocation = '';
	private $WSDLServiceList = array();
	private $SoapObject = null;
	private $SoapDebugTrace = false;

	public static function Instance( $WSDLHost, $OptionList = array(), $useDebugTrace = false ) {
		return new Client( $WSDLHost, $OptionList, $useDebugTrace );
	}

	public function ServiceList() {
		$this->LoadWSDL();
		return $this->WSDLServiceList;
	}

	private function __construct( $WSDLHost, $OptionList, $useDebugTrace ) {
		$this->SoapDebugTrace = $useDebugTrace;
		$this->WSDLLocation = $WSDLHost;
		if( $this->SoapDebugTrace ) {
			$OptionList = array_merge( $OptionList, array( 'trace' => 1, 'exceptions' => false, 'cache_wsdl' => WSDL_CACHE_NONE ) );
		}
		if( Proxy::IsUsed() ) {
			$OptionList = array_merge( $OptionList, Proxy::Credentials() );
		}
		$this->SoapObject = new \SoapClient( $this->WSDLLocation, $OptionList );
	}

	private function CacheWSDL() {
		$WSDLCacheIdentifier = sha1( $this->WSDLLocation );
		if( false === ( $WSDLCache = Cache::Get( $WSDLCacheIdentifier, __CLASS__, true ) ) ) {
			Cache::Set( $WSDLCacheIdentifier, Proxy::HttpGet( $this->WSDLLocation ), __CLASS__, true, 60 );
			$WSDLCache = Cache::Get( $WSDLCacheIdentifier, __CLASS__, true );
		}
		return $WSDLCache;
	}

	private function LoadWSDL() {
		if( empty( $this->WSDLServiceList ) ) {
			$WSDLDefinition = Xml::Parse( $this->CacheWSDL() );
			$WSDLPortType = $WSDLDefinition->Like( '!portType!' );
			$WSDLSchema = $WSDLDefinition->Like( '!schema!' );
			$RunOperation = 0;
			while( is_object( $WSDLOperation = $WSDLPortType->Like( '!operation!', null, $RunOperation++ ) ) ) {
				$this->WSDLServiceList[$WSDLOperation->GetAttribute('name')] = array();

				$WSDLOperationInput = end( explode( ':', $WSDLOperation->Like('!input!')->GetAttribute('message') ) );
				$WSDLMessage = $WSDLDefinition->Like( '!message!', array('name'=>$WSDLOperationInput) );
				$RunParameter = 0;
				while( is_object( $WSDLParameter = $WSDLMessage->Like( '!part!', null, $RunParameter++ ) ) ) {
					if( null === ( $WSDLParameterComplex = $WSDLParameter->GetAttribute( 'element' ) ) ) {
					// Simple Parameter-Type
						$this->WSDLServiceList[
							$WSDLOperation->GetAttribute('name')
						][
							$WSDLParameter->GetAttribute( 'name' )
						] = end( explode( ':', $WSDLParameter->GetAttribute( 'type' ) ) );
					} else {
					// Complex Parameter-Type
						$WSDLSchemaElement = $WSDLSchema->Like('!element!', array('name'=>end( explode( ':', $WSDLParameterComplex ) ) ) );
						if( is_object( $WSDLParameterSequence = $WSDLSchemaElement->Like('!complexType!')->Like('!sequence!') ) ) {
							$RunParameterSequence = 0;
							$this->WSDLServiceList[
								$WSDLOperation->GetAttribute('name')
							] = new \stdClass();
							while( is_object( $WSDLParameter = $WSDLParameterSequence->Like( '!element!', null, $RunParameterSequence++ ) ) ) {
								/*
								$this->WSDLServiceList[
									$WSDLOperation->GetAttribute('name')
								][
									$WSDLParameter->GetAttribute( 'name' )
								] = end( explode( ':', $WSDLParameter->GetAttribute( 'type' ) ) );
								*/
								$this->WSDLServiceList[
									$WSDLOperation->GetAttribute('name')
								]->{$WSDLParameter->GetAttribute( 'name' )} = end( explode( ':', $WSDLParameter->GetAttribute( 'type' ) ) );
							}
						}
					}
				}
			}
		}
	}

	private function ParameterCheck( $Argument, $ParameterType ) {
		switch( $ArgumentType = gettype( $Argument ) ) {
			case 'integer': {
				if( $ParameterType == 'int' ) { return true; }
				if( $ArgumentType == $ParameterType ) { return true; }
				break;
			}
			default: {
				return ( $ArgumentType == $ParameterType );
			}
		}
		return false;
	}

	private function CallService( $Service, $Parameter, $RetryMax = 5 ) {
		$Result = false;
		$RetryCount = 0;
		while(! $Result && $RetryCount < $RetryMax) {
			try {
				$Result = call_user_func_array( array( $this->SoapObject, $Service ), $Parameter );
			}
			catch( \SoapFault $Fault ) {
				if( $Fault->faultstring != 'Could not connect to host' ) {
					throw $Fault;
				}
			}
			sleep(1);
			$RetryCount ++;
		}
		if( $RetryCount >= $RetryMax ) {
		  throw new SoapFault('Could not connect to host after '.$RetryMax.' attempts');
		}
		return $Result;
	}

	function __call( $Service, $Arguments ) {
		$this->LoadWSDL();
		if( key_exists( $Service, $this->WSDLServiceList ) ) {

			// Complex
			if( is_object( $this->WSDLServiceList[$Service] ) ) {
				$Parameter = new \ReflectionObject( $this->WSDLServiceList[$Service] );
				$ParameterList = $Parameter->getProperties();

				$ArgumentsComplex = new \stdClass();
				/** @var \ReflectionProperty $ParameterObject */
				foreach( (array)$ParameterList as $ParameterIndex => $ParameterObject ) {
					if( isset( $Arguments[$ParameterObject->getName()] ) ) {
						$ArgumentsComplex->{$ParameterObject->getName()} = $Arguments[$ParameterObject->getName()];
					} else {
						$ArgumentsComplex->{$ParameterObject->getName()} = $Arguments[$ParameterIndex];
					}
				}

				$Arguments = array($ArgumentsComplex);
			// Simple
			} else {
				$RunParameterCheck = 0;
				foreach( (array)$this->WSDLServiceList[$Service] as $ParameterName => $ParameterType ) {
					// Check Count
					if( !isset( $Arguments[$RunParameterCheck] ) ) {
						throw new \Exception( 'Wrong Parameter-Count! '.$Service.' -> '.print_r( $this->WSDLServiceList[$Service], true ) );
					}
					// Check Type
					if( !$this->ParameterCheck( $Arguments[$RunParameterCheck], $ParameterType ) ) {
						throw new \Exception( 'Wrong Parameter-Type '.gettype($Arguments[$RunParameterCheck]).' given for '.$ParameterName.'@'.$Service.'('.$RunParameterCheck.'), awaits '.$ParameterType.'!' );
					}
					$RunParameterCheck++;
				}
			}

			if( $this->SoapDebugTrace ) {
				Event::Message( 'Call Soap: '.$Service, __METHOD__, __LINE__ );
				Event::Debug( $Arguments, __METHOD__, __LINE__ );
				$Result = $this->CallService( $Service, $Arguments );
				Event::Debug( "Request:\n\n".Xml::Parse( $this->SoapObject->__getLastRequest() )->Code(), __METHOD__, __LINE__ );
				Event::Debug( "Response:\n\n".Xml::Parse( $this->SoapObject->__getLastResponse() )->Code(), __METHOD__, __LINE__ );
				return $Result;
			} else {
				return $this->CallService( $Service, $Arguments );
			}
		} else {
			throw new \Exception( 'Service '.$Service.' not available!' );
		}
	}

	public function SoapObject() {
		return $this->SoapObject;
	}
}
