<?php
/**
 * @author louis
 *
 */
namespace Goettertz\BcVoting\Domain\Model;

/*************************************************************************
 * 
 * Open Asset Protocol Implementation
 *
 *  Copyright notice
 *
 *  (c) 2015 Louis Göttertz <info2015@goettertz.de>, goettertz.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 *  
 *  Rev. 76
 *
 ************************************************************************/

class Asset extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * asset reference 
	 * @var string $assetId 
	 */
	protected $assetId = '';
	
	/**
	 * asset name
	 * @var string $name
	 */
	protected $name;
	
	/**
	 * @var string $ticker
	 */
	protected $ticker;
	
	/**
	 * @var integer $divisibility
	 */
	protected $divisibility;
	
	/**
	 * @var integer $number
	 */
	protected $quantity = 0;
	
	/**
	 * @var string $iconUrl
	 */
	protected $iconUrl;
	
	/**
	 * @var boolean $isUnknown
	 */
	protected $isUnknown;

	/**
	 * @return string
	 */
	public function getAssetId()
	{
		return $this->assetId;
	}
	
	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId) {
		if (is_string($assetId)) $this->assetId = $assetId;
	}

	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param $string $name
	 * @return void
	 */
	public function setName($name) {
		if (is_string($name)) $this->name = $name;
	}

	public function getTicker()
	{
		return $this->ticker;
	}

	public function getDivisibility()
	{
		return $this->divisibility;
	}
	
	/**
	 * @param integer $divisibility
	 */
	public function setDivisibility($divisibility) {
		if (is_int($divisibility)) $this->divisibility = $divisibility;
	}
	
	/**
	 * @return number
	 */
	public function getQuantity() {
		return $this->quantity;
	}
	
	/**
	 * @param integer $quantity
	 */
	public function setQuantity($quantity) {
		if (is_int($quantity)) $this->quantity = $quantity;
	}
	
	public function getIsUnknown()
	{
		return $this->isUnknown;
	}

	public function getIconUrl()
	{
		return $this->iconUrl;
	}
	

}
?>