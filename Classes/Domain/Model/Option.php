<?php
namespace Goettertz\BcVoting\Domain\Model;

/***************************************************************
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
 * Revision 138
 *  
 **********************************************************************/

use \Goettertz\BcVoting\Service\Blockchain;

/**
 * Options for voters
 */
class Option extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * parent
	 * 
	 * @var \Goettertz\BcVoting\Domain\Model\Option
	 */
	protected $parent = NULL;

	/**
	 * name
	 *
	 * @var string
	 */
	protected $name = '';
	
	
	/**
	 * description
	 * 
	 * @var string
	 */
	protected $description = '';

	/**
	 * walletAddress
	 *
	 * @var string
	 */
	protected $walletAddress = NULL;
	
	/**
	 * ballot
	 *
	 * @var \Goettertz\BcVoting\Domain\Model\Ballot
	 */
	protected $ballot = NULL;
	

	/**
	 * logo
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 *
	 */
	protected $logo = NULL;
	
	/**
	 * color
	 * 
	 * @var string
	 */
	protected $color = '';
	
	/**
	 * optionCode
	 * 
	 * temp indovidual code - not in db
	 * 
	 * @var string
	 */
	protected $optionCode = '';

	/**
	 * optionHash
	 *
	 * temp indovidual code - not in db
	 *
	 * @var string
	 */
	protected $optionHash = '';
	
	/**
	 * computed balance from db
	 * @var double $balance;
	 */
	protected $balance = 0;
	
	/**
	 * (voting) balance from BC
	 * 
	 * @var integer
	 */
	protected $votings;
	
	/**
	 *
	 * Creates a new option. All arguments are optional, since every model class
	 * has to implement an empty constructor.
	 *
	 *
	 */
	public function __construct()
	{
	
	}
	
	/**
	 * @return \Goettertz\BcVoting\Domain\Model\Option
	 */
	public function getParent() {
		return $this->parent;
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @return void
	 */
	public function setParent($option) {
		$this->parent = $option;
	}

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * gets the description
	 * 
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * sets the description
	 * 
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the walletAddress
	 *
	 * @return string $walletAddress
	 */
	public function getWalletAddress() {
// 		$bc = new \Goettertz\BcVoting\Service\Blockchain();
// 		$ballot = $this->getBallot();
// 		$project = $ballot->getProject();
// 		if ($result = $bc->getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->validateaddress($this->walletAddress)) {
// 			if ($result) return $result['address'];
// 		}
// 		return NULL;
		return $this->walletAddress;
	}

	/**
	 * Sets the walletAddress
	 *
	 * @param string $walletAddress
	 * @return void
	 */
	public function setWalletAddress($walletAddress) {
		$this->walletAddress = $walletAddress;
	}

	/**
	 * Returns the ballot
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function getBallot() {
		return $this->ballot;
	}

	/**
	 * Sets the project
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @return void
	 */
	public function setBallot(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$this->ballot = $ballot;
	}

	/**
	 * sets the logo
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $logo
	 *
	 * @return void
	 */
	public function setLogo($logo) {
		$this->logo = $logo;
	}
	
	/**
	 * get the logo
	 *
	 * @return \TYPO3\CMS\Core\Resource\FileReference
	 */
	public function getLogo() {
		if (!is_object($this->logo)){
			return null;
		} elseif ($this->logo instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy) {
			$this->logo->_loadRealInstance();
		}
		return $this->logo->getOriginalResource();
	}
	
	/**
	 * gets the color
	 * @return color;
	 */
	public function getColor() {
		return $this->color;
	}

	/**
	 * sets the color
	 * @param string $color
	 * @return void
	 */
	public function setColor($color) {
		$this->color = $color;
	}
	
	/**
	 * @return string
	 */
	public function getOptionCode() {
		return $this->optionCode;
	}
	
	/**
	 * @param string $optionCode
	 */
	public function setOptionCode($optionCode) {
		$this->optionCode = $optionCode;
	}
	
	/**
	 * @return string
	 */
	public function getOptionHash() {
		return $this->optionHash;
	}
	
	/**
	 * @param string $optionHash
	 */
	public function setOptionHash($optionHash) {
		$this->optionHash = $optionHash;
	}

	/**
	 * get balance
	 * @return double
	 */
	public function getBalance() {
		return $this->balance;
	}

	/**
	 * gets the balance
	 * @ return void
	 */
	public function setBalance($balance) {
		$this->balance = $balance;
	}

	/**
	 * gets (voting) balance from BC
	 *
	 * @return NULL|mixed
	 */
	public function getVotings() {
	
		# getAssetBalance (ballot->getAssest())
		$ballot = $this->getBallot();
		$project = $ballot->getProject();
		$fromAddress = $ballot->getWalletAddress();
		return $this->votings = ($fromAddress && $project) ? (\Goettertz\BcVoting\Service\Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $fromAddress)) : NULL;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getJson() {
		
		$ballot = $this->getBallot();
		
		$returnObject = new \stdClass();
		$returnObject->uid = trim($this->getUid());
		$returnObject->name = trim($this->getName());
		if ($parent =  $this->getParent()) $returnObject->parent = $parent->getUid();
		else $returnObject->parent = 0;
		$returnObject->ballot = $ballot->getUid();
		$returnObject->description = $this->getDescription();
		$returnObject->color = $this->getColor();
		
		if ($this->getLogo()) $returnObject->logo = $this->getLogo()->getOriginalFile()->getIdentifier();
		else $returnObject->logo ='----';
		$returnObject->walletaddress = trim($this->getWalletAddress());
	
		return json_encode($returnObject, JSON_FORCE_OBJECT);
	}
}