<?php
/**
 * Sprite
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
 * @subpackage Sprite
 */
namespace AioSystem\Module\Sprite;
/**
 * @package AioSystem\Module
 * @subpackage Sprite
 */
interface InterfaceSprite {
}
/**
 * @package AioSystem\Module
 * @subpackage Sprite
 */
class ClassSprite implements InterfaceSprite {
	/** @var \AioSystem\Core\ClassStackPriority $_InstanceClassStackPriorityClassSpriteItem */
	private static $_InstanceClassStackPriorityClassSpriteItem = null;
	/** @var \AioSystem\Core\ClassStackPriority $_InstanceClassStackPriorityClassSpriteContainer */
	public static $_InstanceClassStackPriorityClassSpriteContainer = null;
	/** @var \AioSystem\Core\ClassStackQueue $_InstanceClassStackPrioritySprite */
	public static $_InstanceClassStackQueueSprite = null;
	/** @var int $_SpriteWidth */
	public static $_SpriteWidth = 0;
	/** @var int $_SpriteHeight */
	public static $_SpriteHeight = 0;

	/**
	 * @static
	 * @return \AioSystem\Core\ClassStackPriority|null
	 */
	public static function Sprite() {
		// Init
		$Container = new ClassSpriteContainer();
		$Width = 0; $Height = 0;
		$ItemList = self::$_InstanceClassStackPriorityClassSpriteItem->listData();
		/** @var ClassSpriteItem $Item */
		foreach( $ItemList as $Index => $Item ) {
			$Width += $Item->propertyWidth();
			$Height += $Item->propertyHeight();
		}

		$Container->propertyWidth( $Width*0.7);
		$Container->propertyHeight( $Height*0.7 );
		self::addContainer( $Container );
		// Run
		$BREAK =0;
		while( self::$_InstanceClassStackPriorityClassSpriteItem->peekData() !== null ) {
			self::placeNextItem();
			self::combineContainer();
		}
		self::$_InstanceClassStackPriorityClassSpriteContainer->sortData();
		return self::$_InstanceClassStackQueueSprite;
	}
	/**
	 * @static
	 * @return void
	 */
	private static function placeNextItem() {
		/** @var ClassSpriteItem $Item */
		$Item = self::$_InstanceClassStackPriorityClassSpriteItem->popData();
		/** @var ClassSpriteContainer $Container */
		$Container = self::searchContainer( $Item );
		$Container->propertyItem( $Item );
		if( self::$_SpriteWidth < $Container->propertyPositionX()+$Item->propertyWidth() ) {
			self::$_SpriteWidth = $Container->propertyPositionX()+$Item->propertyWidth();
		}
		if( self::$_SpriteHeight < $Container->propertyPositionY()+$Item->propertyHeight() ) {
			self::$_SpriteHeight = $Container->propertyPositionY()+$Item->propertyHeight();
		}
		$Cut = $Container->getCut();
		if( $Cut == ClassSpriteContainer::CUT_HORIZONTAL ) {
			$ContainerCutH = new ClassSpriteContainer( ($Container->propertyPositionX()+$Item->propertyWidth()), $Container->propertyPositionY(), ($Container->propertyWidth()-$Item->propertyWidth()), $Item->propertyHeight() );
			$ContainerCutL = new ClassSpriteContainer( $Container->propertyPositionX(), ($Container->propertyPositionY()+$Item->propertyHeight()), $Container->propertyWidth(), ($Container->propertyHeight()-$Item->propertyHeight()) );
			$Container->propertyWidth( $Item->propertyWidth() );
			$Container->propertyHeight( $Item->propertyHeight() );
			self::addContainer( $ContainerCutH );
		} else {
			$ContainerCutV = new ClassSpriteContainer( $Container->propertyPositionX(), ($Container->propertyPositionY()+$Item->propertyHeight()), $Item->propertyWidth(), ($Container->propertyHeight()-$Item->propertyHeight()) );
			$ContainerCutL = new ClassSpriteContainer( ($Container->propertyPositionX()+$Item->propertyWidth()), $Container->propertyPositionY(), ($Container->propertyWidth()-$Item->propertyWidth()), $Container->propertyHeight() );
			$Container->propertyWidth( $Item->propertyWidth() );
			$Container->propertyHeight( $Item->propertyHeight() );
			self::addContainer( $ContainerCutV );
		}
		if( $ContainerCutL->propertyWidth() > 0 && $ContainerCutL->propertyHeight() > 0 ) {
			self::addContainer( $ContainerCutL );
		}
		self::saveContainer( $Container );
	}
	/**
	 * @param ClassSpriteItem $Item
	 * @return void
	 */
	public static function addItem( ClassSpriteItem $Item ) {
		if( self::$_InstanceClassStackPriorityClassSpriteItem === null ) {
			self::$_InstanceClassStackPriorityClassSpriteItem = \AioSystem\Core\ClassStackPriority::Instance( __NAMESPACE__.'\ClassSprite::sortItemStack' );
		}
		self::$_InstanceClassStackPriorityClassSpriteItem->pushData( $Item );
	}
	/**
	 * @param ClassSpriteContainer $Container
	 * @return void
	 */
	public static function addContainer( ClassSpriteContainer $Container ) {
		if( self::$_InstanceClassStackPriorityClassSpriteContainer === null ) {
			self::$_InstanceClassStackPriorityClassSpriteContainer = \AioSystem\Core\ClassStackPriority::Instance( __NAMESPACE__.'\ClassSprite::sortContainerStack' );
		}
		self::$_InstanceClassStackPriorityClassSpriteContainer->pushData( $Container );
	}
	/**
	 * @param ClassSpriteContainer $Container
	 * @return void
	 */
	public static function saveContainer( ClassSpriteContainer $Container ) {
		if( self::$_InstanceClassStackQueueSprite === null ) {
			self::$_InstanceClassStackQueueSprite = \AioSystem\Core\ClassStackQueue::Instance();
		}
		self::$_InstanceClassStackQueueSprite->pushData( $Container );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function combineContainer() {
		$ContainerList = self::$_InstanceClassStackPriorityClassSpriteContainer->listData();
		/** @var ClassSpriteContainer $Container */
		foreach( $ContainerList as $Container ) {
			$ContainerScanList = self::$_InstanceClassStackPriorityClassSpriteContainer->listData();
			/** @var ClassSpriteContainer $ContainerScan */
			foreach( $ContainerScanList as $Index => $ContainerScan ) {
				// Right
				$NewWidth = 0;
				$NewHeight = 0;
				if( $ContainerScan->propertyPositionX() == $Container->propertyPositionX()+$Container->propertyWidth() ) {
					if( $ContainerScan->propertyHeight() == $Container->propertyHeight() ) {
						$NewWidth = $Container->propertyWidth()+$ContainerScan->propertyWidth();
					}
				}
				// Bottom
				if( $ContainerScan->propertyPositionX() == $Container->propertyPositionX() ) {
					if( $ContainerScan->propertyPositionY() == $Container->propertyPositionY()+$Container->propertyHeight() ) {
						if( $ContainerScan->propertyWidth() == $Container->propertyWidth() ) {
							$NewHeight = $Container->propertyHeight()+$ContainerScan->propertyHeight();
						}
					}
				}
				if( $NewWidth != 0 || $NewHeight != 0 ) {
					if(
						($NewWidth * $Container->propertyHeight())
						>
						($NewHeight * $Container->propertyWidth())
					) {
						//var_dump('Combine:Width');
						$Container->propertyWidth( $NewWidth );
						self::$_InstanceClassStackPriorityClassSpriteContainer->removeData($Index);
					} else {
						//var_dump('Combine:Height');
						$Container->propertyHeight( $NewHeight );
						self::$_InstanceClassStackPriorityClassSpriteContainer->removeData($Index);
					}
					$Container->Combine = 1;
				}
			}
		}
	}
	/**
	 * @static
	 * @param ClassSpriteItem $Item
	 * @return ClassSpriteContainer|null
	 */
	public static function searchContainer( ClassSpriteItem $Item ) {
		$ContainerList = self::$_InstanceClassStackPriorityClassSpriteContainer->listData();
		/** @var ClassSpriteContainer $Container */
		/** @var ClassSpriteContainer $Select */
		$Select = null;
		$Leftover = null;
		$Index = null;
		foreach( $ContainerList as $ContainerIndex => $Container ) {
			//if( $Container->propertyItem() === null ) {
				if(
					$Container->propertyWidth() >= $Item->propertyWidth()
					&&  $Container->propertyHeight() >= $Item->propertyHeight()
				) {
					// powered by Axel
					if(
						( $Container->propertyWidth() == $Item->propertyWidth() )
						||  ( $Container->propertyHeight() == $Item->propertyHeight() )
					) {
						if(
							( self::$_SpriteWidth >= $Container->propertyPositionX() + $Item->propertyWidth() )
							&&  ( self::$_SpriteHeight >= $Container->propertyPositionY() + $Item->propertyHeight() )
						) {
							$Select = $Container;
							$Index = $ContainerIndex;
							//var_dump('Stack!');
							break;
						}
					}


					if( self::$_SpriteWidth < $Container->propertyPositionX() + $Item->propertyWidth() ) {
						$maxWidth = $Container->propertyPositionX() + $Item->propertyWidth();
					} else {
						$maxWidth = self::$_SpriteWidth;
					}
					if( self::$_SpriteHeight < $Container->propertyPositionY() + $Item->propertyHeight() ) {
						$maxHeight = $Container->propertyPositionY() + $Item->propertyHeight();
					} else {
						$maxHeight = self::$_SpriteHeight;
					}
					if(
						$Leftover === null || ($maxWidth * $maxHeight) < $Leftover
					) {
						$Leftover = ($maxWidth * $maxHeight);
						$Select = $Container;
						$Index = $ContainerIndex;
						// Optimize
						if(
							( self::$_SpriteWidth-$Container->propertyPositionX() >= $Item->propertyWidth() )
							&&  ( self::$_SpriteHeight-$Container->propertyPositionY() >= $Item->propertyHeight() )
						) {
							$Leftover = ($Container->propertyPositionX() + $Item->propertyWidth()) * ($Container->propertyPositionY() + $Item->propertyHeight());
							//var_dump('Optimize');
						}
					}
				}
			//} else break;
		}
		self::$_InstanceClassStackPriorityClassSpriteContainer->removeData( $Index );
		return $Select;
	}
	/**
	 * @static
	 * @param ClassSpriteItem $ItemA
	 * @param ClassSpriteItem $ItemB
	 * @return int
	 */
	public static function sortItemStack( ClassSpriteItem $ItemA, ClassSpriteItem $ItemB ) {
		if( $ItemA->getArea() > $ItemB->getArea() ) {
			return -1;
		} else if( $ItemA->getArea() < $ItemB->getArea() ) {
			return 1;
		} else if( $ItemA->propertyWidth() > $ItemB->propertyWidth() ) {
			return -1;
		} return 0;
	}
	/**
	 * @static
	 * @param ClassSpriteContainer $ContainerA
	 * @param ClassSpriteContainer $ContainerB
	 * @return int
	 */
	public static function sortContainerStack( ClassSpriteContainer $ContainerA, ClassSpriteContainer $ContainerB ) {

		if( ($ContainerA->propertyPositionX()+$ContainerA->propertyPositionY())>($ContainerB->propertyPositionX()+$ContainerB->propertyPositionY()) ) {
			return 1;
		} else if( ($ContainerA->propertyPositionX()+$ContainerA->propertyPositionY())<($ContainerB->propertyPositionX()+$ContainerB->propertyPositionY()) ) {
			return -1;
		}
		return 0;
		/*
		if( $ContainerA->getArea() < $ContainerB->getArea() ) {
			return -1;
		} else if( $ContainerA->getArea() > $ContainerB->getArea() ) {
			return 1;
		} else if( $ContainerA->propertyWidth() > $ContainerB->propertyWidth() ) {
			return -1;
		} return 0;*/
	}
	/**
	 * @static
	 * @return void
	 */
	public static function debugItemStack() {
		var_dump( self::$_InstanceClassStackPriorityClassSpriteItem->listData() );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function debugContainerStack() {
		var_dump( self::$_InstanceClassStackPriorityClassSpriteContainer->listData() );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function debugSpriteStack() {
		var_dump( self::$_InstanceClassStackQueueSprite->listData() );
	}
	/**
	 * @static
	 * @return void
	 */
	public static function debugSpriteStructure() {
		// Item
		$ItemArea = 0;
		$ContainerList = self::$_InstanceClassStackQueueSprite->listData();
		/** @var ClassSpriteContainer $Container */
		foreach( $ContainerList as $Container ) {
			print '<div style="'
				.'position:absolute;'
					.'top:'.($Container->propertyPositionY()).'px;'
					.'left:'.($Container->propertyPositionX()).'px;'
					.'width:'.($Container->propertyWidth()).'px;'
					.'height:'.($Container->propertyHeight()).'px;'
				.($Container->Combine==1?'background-color:#FF'.dechex(rand(80,180)).'FF;':'background-color:#'.dechex(rand(150,250)).dechex(rand(20,50)).dechex(rand(20,50)).';')
				.'"></div>';
			$ItemArea += ( $Container->propertyWidth() * $Container->propertyHeight() );
		}
		// Container
		$ContainerList = self::$_InstanceClassStackPriorityClassSpriteContainer->listData();
		/** @var ClassSpriteContainer $Container */
		foreach( $ContainerList as $Container ) {
			print '<div style="'
				.'position:absolute;'
					.'top:'.($Container->propertyPositionY()).'px;'
					.'left:'.($Container->propertyPositionX()).'px;'
					.'width:'.($Container->propertyWidth()).'px;'
					.'height:'.($Container->propertyHeight()).'px;'
				.($Container->Combine==1?'background-color:#'.str_repeat( dechex(rand(80,180)), 2).'FF;':'background-color:#'.str_repeat(dechex(rand(200,250)),3).';')
				.'"></div>';
		}
		// Sprite
		$SpriteArea = self::$_SpriteWidth*self::$_SpriteHeight;
		print '<div style="'
			.'position: absolute; top: 0; left: 0;'
				.'width:'.(self::$_SpriteWidth).'px;'
				.'height:'.(self::$_SpriteHeight).'px;'
			.'border: 1px solid blue;'
			.'">'
			.'ItemArea: '.$ItemArea.'</br>SpriteArea: '.$SpriteArea.'<br/>Overhead: '.round(100-(100/$SpriteArea*($ItemArea==0?1:$ItemArea)),5)
			.'</div>';
	}
}
?>