<?php
/**
 * Database:HierarchicalData
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
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package AIOSystem\Module
 * @subpackage Database
 */
interface InterfaceHierarchicalData {

}
	
class ClassHierarchicalData {
	const propertyFieldNameId = '_HDTreeId';
	const propertyFieldNameLink = '_HDTreeLink';
	const propertyFieldNameLeft = '_HDTreeLeft';
	const propertyFieldNameRight = '_HDTreeRight';
	const propertyAliasNameDepth = '_HDTreeDepth';
	const propertyAliasNameWidth = '_HDTreeWidth';

	private $propertyTableName = '_HierarchicalData';

	private $propertyLinkTable = array();
	private $propertyDataTable = array();
	private $propertyJoinTable = array();

	function __construct( $TableName ) {
		$this->propertyTableName( $TableName );
	}

	private function propertyTableName( $propertyTableName = null ) {
		if( null !== $propertyTableName ) {
			$this->propertyTableName = $propertyTableName.'_HierarchicalData';
		} return $this->propertyTableName;
	}

	public static function Instance( $TableName ) {
		return new ClassHierarchicalData( $TableName );
	}

	/**
	 * Create hierarchical table for given data table and insert/init tree root (row id = 1) with link id = 0
	 * @return void
	 */
	public function Install() {
		Database::CreateTable( $this->propertyTableName(), array(
			array(self::propertyFieldNameId     ,'I',20,'PRIMARY','AUTOINCREMENT'),
			array(self::propertyFieldNameLink   ,'I',20),
			array(self::propertyFieldNameLeft   ,'I',20),
			array(self::propertyFieldNameRight  ,'I',20)
		));
		Database::Record( $this->propertyTableName(), array(
			self::propertyFieldNameLink => 0,
			self::propertyFieldNameLeft => 1,
			self::propertyFieldNameRight => 2,
		),array(
			self::propertyFieldNameId." = 1 "
		));
	}

	public function LinkTable( $TableName, $FieldName, $SelectList = array(), $FilterExpression = null ) {
		$this->propertyLinkTable = array(
			'LinkTableName' => $TableName, 'LinkFieldName' => $FieldName,
			'SelectList' => $SelectList,
			'FilterExpression' => $FilterExpression
		);
	}

	public function DataTable( $DataTableName, $DataFieldName, $LinkFieldName, $SelectList = array(), $FilterExpression = null ) {
		array_push( $this->propertyDataTable, array(
			'DataTableName' => $DataTableName, 'DataFieldName' => $DataFieldName,
			'LinkFieldName' => $LinkFieldName,
			'SelectList' => $SelectList, 'FilterExpression' => $FilterExpression
		));
	}

	public function JoinTable( $DataTableName, $DataFieldName, $JoinTableName, $JoinFieldName, $SelectList = array(), $FilterExpression = null ) {
		array_push( $this->propertyJoinTable, array(
			'DataTableName' => $DataTableName, 'DataFieldName' => $DataFieldName,
			'JoinTableName' => $JoinTableName, 'JoinFieldName' => $JoinFieldName,
			'SelectList' => $SelectList, 'FilterExpression' => $FilterExpression
		));
	}

	public function GetTreeHtml( Template $DataTemplateInstance = null, $FilterExpression = null, $HDTreeId = null ) {
		$Data = $this->GetTree( $FilterExpression, $HDTreeId );
		$IndentLevel = -1;
		$Html = '';
		foreach( (array)$Data as $RowIndex => $RowData ) {
			if( $RowData[self::propertyFieldNameLink] == 0 ) {
				continue;
			}
			if( $RowData['_HDTreeDepth'] > $IndentLevel ) {
				if( $RowIndex > 0 ) {
					$Html.= "\n";
				}
				$Html.= str_repeat("\t",$RowData['_HDTreeDepth']).'<ul>';
				$Html.= "\n".str_repeat("\t",$RowData['_HDTreeDepth']+1).'<li id="'.$this->propertyTableName().'_'.$RowIndex.'">';
			} else
			if( $RowData['_HDTreeDepth'] < $IndentLevel ) {
				$Gap = $IndentLevel - $RowData['_HDTreeDepth'];
				for( $Run = $Gap; $Run > 0; $Run--) {
					$Html.= '</li>';
					$Html.= "\n".str_repeat("\t",$Run+1);
					$Html.= '</ul>';
				}
				$Html.= "\n".str_repeat("\t",$Gap+1).'</li>';
				$Html.= "\n".str_repeat("\t",$Gap+1).'<li id="'.$this->propertyTableName().'_'.$RowIndex.'">';
			} else {
				if( $RowIndex > 0 ) {
					$Html.= '</li>'."\n";
				}
				$Html.= str_repeat("\t",$RowData['_HDTreeDepth']+1).'<li id="'.$this->propertyTableName().'_'.$RowIndex.'">';
			}

			if( null !== $DataTemplateInstance ) {
				$DataTemplateContent = $DataTemplateInstance->Content();
				foreach( (array)$RowData as $DataKey => $DataValue ) {
					$DataTemplateInstance->Assign( $DataKey, $DataValue );
				}
				$Html.= $DataTemplateInstance->Parse();
				$DataTemplateInstance->Content( $DataTemplateContent );
			} else {
				$DataTemplateExampleInstance = Template::Load( System::DirectorySyntax( __DIR__ ).'HierarchicalData.tpl' );
				$DataTemplateContent = $DataTemplateExampleInstance->Content();
				foreach( (array)$RowData as $DataKey => $DataValue ) {
					$DataTemplateExampleInstance->Assign( $DataKey, $DataValue );
				}
				$DataAvailableList = $DataTemplateExampleInstance->MapAssign();
				$DataAvailableString = '';
				foreach( (array)$DataAvailableList as $DataAvailable ) {
					$DataAvailableString .= '<tr><td style="padding: 0 2px 0 2px; text-align: left; border-width: 1px;">{'.$DataAvailable[0].'}</td><td style="padding: 0 2px 0 2px; text-align: left; border-width: 1px;">'.substr($DataAvailable[1],0,10).(strlen($DataAvailable[1])>10?'(...)':'').'</td></tr>';
				}
				$DataTemplateExampleInstance->Assign( 'DataAvailable', $DataAvailableString );
				$Html.= $DataTemplateExampleInstance->Parse();
				$DataTemplateExampleInstance->Content( $DataTemplateContent );
			}
			//$Html.= $RowData['UserNickname'].'#'.$RowData['UserEmail'];

			$IndentLevel = $RowData['_HDTreeDepth'];
		}
		for( $Run = $IndentLevel; $Run >= 0; $Run--) {
			$Html.= '</li>';
			$Html.= "\n".str_repeat("\t",$Run);
			$Html.= '</ul>';
			$Html.= "\n".str_repeat("\t",$Run);
		}
		return $Html;
	}

	/**
	 * @param null|string $FilterExpression
	 * @param null|int $HDTreeId
	 * @return array
	 */
	public function GetTree( $FilterExpression = null, $HDTreeId = null ) {
		if( $HDTreeId !== null ) {
			return $this->GetSubTree( $HDTreeId, $FilterExpression );
		}
		$Request  = "SELECT {TemplateSelectList} FROM "
					."( SELECT "
						."HDNode.".self::propertyFieldNameId.", "
						."HDNode.".self::propertyFieldNameLink.", "
						."HDNode.".self::propertyFieldNameLeft.", "
						."HDNode.".self::propertyFieldNameRight.", "
						."( COUNT( HDParent.".self::propertyFieldNameLink." ) - 1 ) AS ".self::propertyAliasNameDepth." "
						."FROM ".$this->propertyTableName()." AS HDNode, ".$this->propertyTableName()." AS HDParent "
						."WHERE HDNode.".self::propertyFieldNameLeft." BETWEEN "
							."HDParent.".self::propertyFieldNameLeft." AND HDParent.".self::propertyFieldNameRight." "
						."GROUP BY "
							."HDNode.".self::propertyFieldNameId.", "
							."HDNode.".self::propertyFieldNameLink.", "
							."HDNode.".self::propertyFieldNameLeft.", "
							."HDNode.".self::propertyFieldNameRight." "
						."ORDER BY HDNode.".self::propertyFieldNameLeft." "
					.") AS HDTree ";
			if( !empty( $this->propertyLinkTable ) ) {
				$Request .= "LEFT OUTER JOIN ".$this->propertyLinkTable['LinkTableName']." "
							."ON HDTree.".self::propertyFieldNameLink." = ".$this->propertyLinkTable['LinkTableName'].".".$this->propertyLinkTable['LinkFieldName']." "
							.( null !== $this->propertyLinkTable['FilterExpression']
								?"AND ".$this->propertyLinkTable['FilterExpression']." "
								:" "
							);
						$TemplateSelectList = '';
					if( $this->propertyLinkTable['SelectList'] !== null ) {
						if( empty( $this->propertyLinkTable['SelectList'] ) ) {
							$TemplateSelectList = ", ".$this->propertyLinkTable['LinkTableName'].".*";
						} else {
							foreach( (array)$this->propertyLinkTable['SelectList'] as $SelectField => $SelectAlias ) {
								$TemplateSelectList .= ", ".$this->propertyLinkTable['LinkTableName'].".".$SelectField." AS ".$SelectAlias;
							}
						}
					}
				foreach( (array) $this->propertyDataTable as $DataTable ) {
					$Request .= "LEFT OUTER JOIN ".$DataTable['DataTableName']." "
								."ON ".$this->propertyLinkTable['LinkTableName'].".".$DataTable['LinkFieldName']." = ".$DataTable['DataTableName'].".".$DataTable['DataFieldName']." "
								.( null !== $DataTable['FilterExpression']
									?"AND ".$DataTable['FilterExpression']." "
									:" "
								);
					if( $DataTable['SelectList'] !== null ) {
						if( empty( $DataTable['SelectList'] ) ) {
							$TemplateSelectList .= ", ".$DataTable['DataTableName'].".*";
						} else {
							foreach( (array)$DataTable['SelectList'] as $SelectField => $SelectAlias ) {
								$TemplateSelectList .= ", ".$DataTable['DataTableName'].".".$SelectField." AS ".$SelectAlias;
							}
						}
					}
				}
				foreach( (array) $this->propertyJoinTable as $JoinTable ) {
					$Request .= "LEFT OUTER JOIN ".$JoinTable['JoinTableName']." "
								."ON ".$JoinTable['DataTableName'].".".$JoinTable['DataFieldName']." = ".$JoinTable['JoinTableName'].".".$JoinTable['JoinFieldName']." "
								.( null !== $JoinTable['FilterExpression']
									?"AND ".$JoinTable['FilterExpression']." "
									:" "
								);
						if( empty( $JoinTable['SelectList'] ) ) {
							$TemplateSelectList .= ", ".$JoinTable['JoinTableName'].".*";
						} else {
							foreach( (array)$JoinTable['SelectList'] as $SelectField => $SelectAlias ) {
								$TemplateSelectList .= ", ".$JoinTable['JoinTableName'].".".$SelectField." AS ".$SelectAlias;
							}
						}
				}
				$Request = str_replace('{TemplateSelectList}','HDTree.*'.$TemplateSelectList,$Request);
			} else {
				$Request = str_replace('{TemplateSelectList}','*',$Request);
			}
		$Request .= ( null !== $FilterExpression
						?"WHERE ".$FilterExpression." "
						:" "
					);
		//Event::Message( $Request );
		return Database::Execute( $Request );
	}

	/**
	 * @param int $HDTreeId
	 * @param null|string $FilterExpression
	 * @return array
	 */
	private function GetSubTree( $HDTreeId, $FilterExpression = null, $SqlExpression = false ) {
		$Request  = "SELECT * FROM "
					."( SELECT "
						."HDNode.".self::propertyFieldNameId.", "
						."HDNode.".self::propertyFieldNameLink.", "
						."HDNode.".self::propertyFieldNameLeft.", "
						."HDNode.".self::propertyFieldNameRight.", "
						."(COUNT(HDParent.".self::propertyFieldNameLink.") - (HDSubTreeNode.".self::propertyAliasNameDepth." + 1)) AS ".self::propertyAliasNameDepth." "
						."FROM ".$this->propertyTableName()." AS HDNode, ".$this->propertyTableName()." AS HDParent "
						.", ".$this->propertyTableName()." AS HDSubTreeParent "
						.",("
							."SELECT "
							."HDNode.".self::propertyFieldNameId.", HDNode.".self::propertyFieldNameLink.", (COUNT(HDParent.".self::propertyFieldNameLink.") - 1) AS ".self::propertyAliasNameDepth." "
							."FROM ".$this->propertyTableName()." AS HDNode, ".$this->propertyTableName()." AS HDParent "
							."WHERE HDNode.".self::propertyFieldNameLeft." BETWEEN HDParent.".self::propertyFieldNameLeft." AND HDParent.".self::propertyFieldNameRight." "
								."AND HDNode.".self::propertyFieldNameId." = ".$HDTreeId." "
							."GROUP BY HDNode.".self::propertyFieldNameId.", HDNode.".self::propertyFieldNameLink." "
							."ORDER BY HDNode.".self::propertyFieldNameLeft." "
						.") AS HDSubTreeNode "
					."WHERE HDNode.".self::propertyFieldNameLeft." BETWEEN HDParent.".self::propertyFieldNameLeft." AND HDParent.".self::propertyFieldNameRight." "
						."AND HDNode.".self::propertyFieldNameLeft." BETWEEN HDSubTreeParent.".self::propertyFieldNameLeft." AND HDSubTreeParent.".self::propertyFieldNameRight." "
						."AND HDSubTreeParent.".self::propertyFieldNameId." = HDSubTreeNode.".self::propertyFieldNameId." "
					."GROUP BY "
						."HDNode.".self::propertyFieldNameId.", "
						."HDNode.".self::propertyFieldNameLink.", "
						."HDNode.".self::propertyFieldNameRight." "
					."ORDER BY HDNode.".self::propertyFieldNameLeft." "
					.") AS HDTree ";
			if( !empty( $this->propertyLinkTable ) ) {
				$Request .= "LEFT OUTER JOIN ".$this->propertyLinkTable['LinkTableName']." "
							."ON HDTree.".self::propertyFieldNameLink." = ".$this->propertyLinkTable['LinkTableName'].".".$this->propertyLinkTable['LinkFieldName']." "
							.( null !== $this->propertyLinkTable['FilterExpression']
								?"AND ".$this->propertyLinkTable['FilterExpression']." "
								:" "
							);
				foreach( (array) $this->propertyDataTable as $DataTable ) {
					$Request .= "LEFT OUTER JOIN ".$DataTable['DataTableName']." "
								."ON ".$this->propertyLinkTable['LinkTableName'].".".$DataTable['LinkFieldName']." = ".$DataTable['DataTableName'].".".$DataTable['DataFieldName']." "
								.( null !== $DataTable['FilterExpression']
									?"AND ".$DataTable['FilterExpression']." "
									:" "
								);
				}
				foreach( (array) $this->propertyJoinTable as $JoinTable ) {
					$Request .= "LEFT OUTER JOIN ".$JoinTable['JoinTableName']." "
								."ON ".$JoinTable['DataTableName'].".".$JoinTable['DataFieldName']." = ".$JoinTable['JoinTableName'].".".$JoinTable['JoinFieldName']." "
								.( null !== $JoinTable['FilterExpression']
									?"AND ".$JoinTable['FilterExpression']." "
									:" "
								);
				}
			}
		$Request .= ( null !== $FilterExpression
						?"WHERE ".$FilterExpression." "
						:" "
					);
		//Event::Message( $Request );
		if( $SqlExpression === true ) {
			return $Request;
		}
		return Database::Execute( $Request );
	}

	/**
	 * @return array
	 */
	public function GetLeafs() {
		return Database::Execute("
			SELECT *
			FROM ".$this->propertyTableName()."
			WHERE ".self::propertyFieldNameRight." = ".self::propertyFieldNameLeft." + 1;
		");
	}
	public function GetPath( $HDTreeLink = null, $FilterExpression = null ) {
		$Request  = "SELECT * "
					."FROM ".$this->propertyTableName()." AS HDNode, ".$this->propertyTableName()." AS HDParent "
					."WHERE "
						."HDNode.".self::propertyFieldNameLeft." BETWEEN "
							."HDParent.".self::propertyFieldNameLeft." AND HDParent.".self::propertyFieldNameRight." ";
			foreach( (array)$this->propertyWhereCondition as $Condition ) {
				$Request .= " AND HDNode.".$Condition." ";
			}

		$Request .= "ORDER BY HDParent.".self::propertyFieldNameLeft;
		return Database::Execute( $Request );
	}

	/**
	 * @param int $HDTreeId
	 * @return array
	 */
	public function GetNode( $HDTreeId ) {
		return Database::Execute("
			SELECT *
			FROM ".$this->propertyTableName()."
			WHERE ".self::propertyFieldNameId." = '".$HDTreeId."'"
		);
	}

	public function MoveNode( $CurrentHDTreeId, $TargetHDTreeId ) {
		$this->ShiftTree( $CurrentHDTreeId, $TargetHDTreeId, 'MOVE' );
	}
	
	public function SortNode( $CurrentHDTreeId, $TargetHDTreeId ) {
		$this->ShiftTree( $CurrentHDTreeId, $TargetHDTreeId, 'SORT' );
	}

	/**
	 * @param int $CurrentHDTreeId
	 * @param int $TargetHDTreeId
	 * @param string $Type
	 * @return bool
	 */
	private function ShiftTree( $CurrentHDTreeId, $TargetHDTreeId, $Type ) {
		//Event::Message( 'ARG: '.print_r( func_get_args(), true ) );
		$CurrentNodeList = $this->GetSubTree( $CurrentHDTreeId );
		//Event::Message( 'TREE: '.print_r( $CurrentNodeList, true ) );
		if( sizeof( $CurrentNodeList ) < 1 ) {
			return false;
		};
		$CurrentDepthStack = array();
		// First Node (Init)
		$CurrentNode = array_shift( $CurrentNodeList );
		switch( strtoupper( $Type ) ) {
			case 'MOVE': $TargetHDTreeId = $this->AddChild( $TargetHDTreeId, $CurrentNode[self::propertyFieldNameLink] ); break;
			case 'SORT': $TargetHDTreeId = $this->AddSibling( $TargetHDTreeId, $CurrentNode[self::propertyFieldNameLink] ); break;
		}
		array_push( $CurrentDepthStack, $this->GetNode( $TargetHDTreeId ) );
		// Traverse Sub Tree
		foreach( (array)$CurrentNodeList as $IndexCurrentNode => $CurrentNode ) {
			//Event::Message( 'CurrentNode: '.print_r( $CurrentNode, true ) );
			if( $CurrentNode[self::propertyAliasNameDepth] > sizeof( $CurrentDepthStack ) -1 ) {
				$CurrentDepthNode = array_peek( $CurrentDepthStack );
				$CurrentDepthNode = $CurrentDepthNode[0];
				//Event::Message( 'LevelUp: '.print_r( $CurrentDepthNode, true ) );
				$TargetHDTreeId = $this->AddChild( $CurrentDepthNode[self::propertyFieldNameId], $CurrentNode[self::propertyFieldNameLink] );
				array_push( $CurrentDepthStack, $this->GetNode( $TargetHDTreeId ) );
			} else
			if( $CurrentNode[self::propertyAliasNameDepth] < sizeof( $CurrentDepthStack ) -1 ) {
				$CurrentDepthStack = array_slice( $CurrentDepthStack, 0, $CurrentNode[self::propertyAliasNameDepth] );
				$CurrentDepthNode = array_peek( $CurrentDepthStack );
				$CurrentDepthNode = $CurrentDepthNode[0];
				//Event::Message( 'LevelDown: '.print_r( $CurrentDepthNode, true ) );
				$TargetHDTreeId = $this->AddChild( $CurrentDepthNode[self::propertyFieldNameId], $CurrentNode[self::propertyFieldNameLink] );
				array_push( $CurrentDepthStack, $this->GetNode( $TargetHDTreeId ) );
			} else
			if( $CurrentNode[self::propertyAliasNameDepth] == sizeof( $CurrentDepthStack ) -1 ) {
				$CurrentDepthNode = array_pop( $CurrentDepthStack );
				$CurrentDepthNode = $CurrentDepthNode[0];
				//Event::Message( 'LevelSame: '.print_r( $CurrentDepthNode, true ) );
				$TargetHDTreeId = $this->AddSibling( $CurrentDepthNode[self::propertyFieldNameId], $CurrentNode[self::propertyFieldNameLink] );
				array_push( $CurrentDepthStack, $this->GetNode( $TargetHDTreeId ) );
			}
		}
		// Remove Original Sub Tree
		$this->RemoveNode( $CurrentHDTreeId );
		return true;
	}

	/**
	 * Adds a new sibling node after the given id
	 * @param int $HDTreeId
	 * @param null $NewHDTreeLink
	 * @return int
	 */
	public function AddSibling( $HDTreeId, $NewHDTreeLink = null ) {
		$TreeValue = Database::Execute( "SELECT ".self::propertyFieldNameRight." "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameId." = ".$HDTreeId." "
		);
		$TreeValue = $TreeValue[0];
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameRight." = ".self::propertyFieldNameRight." + 2 "
							."WHERE ".self::propertyFieldNameRight." > ".$TreeValue[self::propertyFieldNameRight]
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameLeft." = ".self::propertyFieldNameLeft." + 2 "
							."WHERE ".self::propertyFieldNameLeft." > ".$TreeValue[self::propertyFieldNameRight]
		);
		Database::Execute( "INSERT INTO ".$this->propertyTableName()." "
							."( ".self::propertyFieldNameLink.", ".self::propertyFieldNameLeft.", ".self::propertyFieldNameRight.") "
							."VALUES "
							."( ".$NewHDTreeLink.", ".$TreeValue[self::propertyFieldNameRight]." + 1, ".$TreeValue[self::propertyFieldNameRight]." + 2)"
		);
		$LastId = Database::LastInsertId();
		//Event::Message( 'ADDSibling: '.print_r( $LastId, true ) );
		return $LastId;
	}

	/**
	 * Adds a new child node to the given id
	 * @param int $HDTreeId
	 * @param null $NewHDTreeLink
	 * @return int
	 */
	public function AddChild( $HDTreeId, $NewHDTreeLink = null ) {
		$TreeValue = Database::Execute( "SELECT ".self::propertyFieldNameLeft." "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameId." = ".$HDTreeId." "
		);
		$TreeValue = $TreeValue[0];
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameRight." = ".self::propertyFieldNameRight." + 2 "
							."WHERE ".self::propertyFieldNameRight." > ".$TreeValue[self::propertyFieldNameLeft]
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameLeft." = ".self::propertyFieldNameLeft." + 2 "
							."WHERE ".self::propertyFieldNameLeft." > ".$TreeValue[self::propertyFieldNameLeft]
		);
		Database::Execute( "INSERT INTO ".$this->propertyTableName()." "
							."( ".self::propertyFieldNameLink.", ".self::propertyFieldNameLeft.", ".self::propertyFieldNameRight.") "
							."VALUES "
							."( ".$NewHDTreeLink.", ".$TreeValue[self::propertyFieldNameLeft]." + 1, ".$TreeValue[self::propertyFieldNameLeft]." + 2)"
		);
		$LastId = Database::LastInsertId();
		//Event::Message( 'ADDChild: '.print_r( $LastId, true ) );
		return $LastId;
	}

	/**
	 * Removes the node and its children
	 * @param int $HDTreeId
	 * @return void
	 */
	public function RemoveNode( $HDTreeId ) {
		$TreeValue = Database::Execute( "SELECT ".self::propertyFieldNameLeft.", ".self::propertyFieldNameRight." "
							.", ".self::propertyFieldNameRight." - ".self::propertyFieldNameLeft." +1 AS ".self::propertyAliasNameWidth." "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameId." = ".$HDTreeId." "
		);
		$TreeValue = $TreeValue[0];
		Database::Execute( "DELETE "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameLeft." BETWEEN "
								.$TreeValue[self::propertyFieldNameLeft]." AND ".$TreeValue[self::propertyFieldNameRight]." "
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameRight." = ".self::propertyFieldNameRight." - ".$TreeValue[self::propertyAliasNameWidth]." "
							."WHERE ".self::propertyFieldNameRight." > ".$TreeValue[self::propertyFieldNameRight]
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameLeft." = ".self::propertyFieldNameLeft." - ".$TreeValue[self::propertyAliasNameWidth]." "
							."WHERE ".self::propertyFieldNameLeft." > ".$TreeValue[self::propertyFieldNameRight]
		);
	}

	/**
	 * Removes the node and sets the immediate children to his level
	 * @param int $HDTreeId
	 * @return void
	 */
	public function RemoveParent( $HDTreeId ) {
		$TreeValue = Database::Execute( "SELECT ".self::propertyFieldNameLeft.", ".self::propertyFieldNameRight." "
							.", ".self::propertyFieldNameRight." - ".self::propertyFieldNameLeft." +1 AS ".self::propertyAliasNameWidth." "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameId." = ".$HDTreeId." "
		);
		$TreeValue = $TreeValue[0];
		Database::Execute( "DELETE "
							."FROM ".$this->propertyTableName()." "
							."WHERE ".self::propertyFieldNameLeft." = ".$TreeValue[self::propertyFieldNameLeft]." "
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameRight." = ".self::propertyFieldNameRight." - 1 "
							.", ".self::propertyFieldNameLeft." = ".self::propertyFieldNameLeft." - 1 "
							."WHERE ".self::propertyFieldNameRight." BETWEEN ".$TreeValue[self::propertyFieldNameLeft]." AND ".$TreeValue[self::propertyFieldNameRight]
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameRight." = ".self::propertyFieldNameRight." - 2 "
							."WHERE ".self::propertyFieldNameRight." > ".$TreeValue[self::propertyFieldNameRight]
		);
		Database::Execute( "UPDATE ".$this->propertyTableName()." "
							."SET ".self::propertyFieldNameLeft." = ".self::propertyFieldNameLeft." - 2 "
							."WHERE ".self::propertyFieldNameLeft." > ".$TreeValue[self::propertyFieldNameRight]
		);
	}
}
?>