<?php
/**
 * Spam
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
 * @package AioSystem\Module
 * @subpackage Spam
 */
namespace AioSystem\Module\Spam;
use \AioSystem\Api\Database as Database;
/**
 * @package AioSystem\Module
 * @subpackage Spam
 */
interface InterfaceSpam {
	public static function Install();
	public static function Trainer( $Content, $IsSpam = true );
	public static function IsSpam( $Content );
	public static function SpamLevel( $Content );
}
/**
 * @package AioSystem\Module
 * @subpackage Spam
 *
 * Enumeration:
 *
 * Token:   A   B   C   Count
 * Spam :   30  4   8   42
 * Ham  :   4   50  7   61
 * Count:   34  54  15  103
 *
 * Probability:
 *
 * Token:   A       B       C       Count
 * Spam :   30/103  4/103   8/103   42/103
 * Ham  :   5/103   50/103  7/103   61/103
 * Count:   34/103  54/103  15/103  103/103
 *
 * P( Spam | Token ) = ( Count(Token[Spam]) / Count(Total) ) / ( Count(Token[Total]) / Count(Total) )
 *
 * P( Spam | A ) = ( 30 / 103 ) / ( 34 / 103 ) = 0.291 / 0.330 = 0.881 -> 88% = Spam
 * P( Spam | B ) = ( 4 / 103 ) / ( 54 / 103 ) = 0.039 / 0.524 = 0.074 -> 7% = Ham
 *
 */
class ClassSpam implements InterfaceSpam {
	const SPAM_TYPE_SPAM = 1;
	const SPAM_TYPE_HAM = 2;

	const SPAM_LEVEL_SPAM = 0.8;

	private static $KnowledgeCountTotal = 1;
	private static $KnowledgeData = array();

	public static function Install() {
		Database::DropTable( 'ClassSpamKnowledgeBase' );
		Database::CreateTable( 'ClassSpamKnowledgeBase', array(
			array( 'id', 'I', 20, 'PRIMARY', 'AUTOINCREMENT' ),
			array( 'token', 'C', 10, 'KEY' ),
			array( 'type', 'I', 1 ),
			array( 'count', 'I', 20 ),
		) );
	}
	public static function Trainer( $Content, $IsSpam = true ) {
		$Content = self::Tokenizer( self::Normalizer( $Content ) );
		foreach( (array)$Content as $Token => $Count ) {
			$Current = Database::RecordSet(
				'ClassSpamKnowledgeBase',
				"token = '".$Token."' AND type = '".($IsSpam===true?self::SPAM_TYPE_SPAM:self::SPAM_TYPE_HAM)."'",
				true
			);
			if( !empty($Current) ) {
				$Count = $Count+$Current[0]['count'];
			}
			Database::Record(
				'ClassSpamKnowledgeBase',
				array( 'token'=>$Token, 'type'=>($IsSpam===true?self::SPAM_TYPE_SPAM:self::SPAM_TYPE_HAM), 'count'=>$Count ),
				array( "token = '".$Token."'", "type = '".($IsSpam===true?self::SPAM_TYPE_SPAM:self::SPAM_TYPE_HAM)."'" )
			);
		}
	}
	public static function IsSpam( $Content ) {
		if( self::SpamLevel( $Content ) >= self::SPAM_LEVEL_SPAM ) {
			return true;
		} else {
			return false;
		}
	}
	public static function SpamLevel( $Content ) {
		$ResultKnowledgeCountTotal = Database::Execute("
			SELECT SUM(count) as Total FROM ".'ClassSpamKnowledgeBase'."
		");
		self::$KnowledgeCountTotal = $ResultKnowledgeCountTotal[0]['Total'];
		$Content = self::Tokenizer( self::Normalizer( $Content ) );
		$KnowledgeData = Database::Execute("
				SELECT token, type, count FROM ".'ClassSpamKnowledgeBase '
				."WHERE "
					."token IN ('".implode("', '",array_keys($Content))."') "
				."ORDER BY type
		");
		foreach( (array)$KnowledgeData as $Knowledge ) {
			self::$KnowledgeData[$Knowledge['token']][$Knowledge['type']] = $Knowledge['count'];
		}
		$SpamLevel = 0;
		foreach( (array)$Content as $Token => $Count ) {
			if( self::SPAM_LEVEL_SPAM <= ($ProbabilityTokenSpam = self::TokenRatingSpam( $Token )) ) {
				$SpamLevel += ( $ProbabilityTokenSpam * $Count / array_sum($Content) );
			}
			//var_dump( $Token.'['.$ProbabilityTokenSpam.']'.':'.round(( $ProbabilityTokenSpam * $Count / array_sum($Content) ),3).'->'.round($SpamLevel,3) );
		}
		return $SpamLevel;
	}
	private static function TokenRatingSpam( $Token ) {
		$Probability = 0;
		if( isset(self::$KnowledgeData[$Token]) )
		switch( count( self::$KnowledgeData[$Token] ) ) {
			case 2: {
				$Probability = self::Probability(
					self::$KnowledgeData[$Token][self::SPAM_TYPE_SPAM],
					(self::$KnowledgeData[$Token][self::SPAM_TYPE_SPAM]+self::$KnowledgeData[$Token][self::SPAM_TYPE_HAM]),
					self::$KnowledgeCountTotal
				);
				break;
			}
			case 1: {
				if( key(self::$KnowledgeData[$Token]) == self::SPAM_TYPE_HAM ) {
					$Probability = 0.0001;
				} else {
					$Probability = 0.85;
				}
				break;
			}
			case 0: {
				$Probability = 0.01;
				break;
			}
		}
		return $Probability;
	}
	private static function Probability( $TypeCount, $TokenCount, $TokenTotal ) {
		return ( ( $TypeCount / $TokenTotal ) / ( $TokenCount / $TokenTotal ) );
	}
	private static function Tokenizer( $Content ) {
		preg_match_all( '!(?<= )([a-z]{3,10})(?= )!is', $Content, $Matches );
		return array_count_values( $Matches[1] );
	}
	private static function Normalizer( $Content ) {
		$Content = html_entity_decode( ' '.$Content.' ', ENT_QUOTES, 'UTF-8' );
		$Content = strip_tags( $Content );
		$Content = strtolower($Content);
		$Content = preg_replace( '!(?<= )\.(?= )!is', '', $Content );
		$Content = preg_replace( '!\W!is', ' ', $Content );
		$Content = preg_replace( '!(?<= )[0-9\.%]+!is', '', $Content );
		$Content = preg_replace( '!\s{2,}!is', ' ', $Content );
		return $Content;
	}
}
?>