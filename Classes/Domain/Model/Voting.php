<?php
namespace Goettertz\BcVoting\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015-2016 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 *  Rev. 139
 ***************************************************************/

/**
 * Voting
 */
class Voting extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject {
	
	/**
	 * project
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 */
	protected $project = null;
	

	/**
	 * ballot
	 * @var \Goettertz\BcVoting\Domain\Model\Ballot
	 */
	protected $ballot = null;
	
	/**
	 * 
	 * @var string
	 */
	protected $txid;
	
	/**
	 * @var string
	 */
	protected $reference;
	
	/**
	 * 
	 * @var string
	 */
	protected $hash;
	
	/**
	 * @var string
	 */
	protected $optionCode = array();
	
	/**
	 * option
	 * @var \Goettertz\BcVoting\Domain\Model\Option
	 */
	protected $option;

	/**
	 * gets the option
	 * @return \Goettertz\BcVoting\Domain\Model\Option
	 */
	public function getOption() {
		return $this->option;
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Option  $option
	 * @return void
	 */
	public function setOption($option) {
		$this->option = $option;
	}
	
	/**
	 * gets the optionCode
	 * @string 
	 */
	public function getOptionCode() {
		return $this->optionCode;
	}
	
	/**
	 * @param string $optionCode
	 * @return void
	 */
	public function setOptionCode($optionCode) {
		$this->optionCode = $optionCode;
	}
	
	/**
	 * gets txid
	 * @return string
	 */
	public function getTxid() {
		return $this->txid;
	}
	
	/**
	 * sets txid
	 * @param string $txid
	 * @return void 
	 */
	public function setTxid($txid) {
		$this->txid = $txid;
	}
	/**
	 * gets ref
	 * @return string
	 */
	public function getReference() {
		return $this->reference;
	}
	
	/**
	 * sets ref
	 * @param string $reference
	 * @return void
	 */
	public function setReference($reference) {
		$this->reference = $reference;
	}
	
	/**
	 * gets hash
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}
	
	/**
	 * sets hash
	 * @param string $hash
	 * @return void
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}

	/**
	 * gets ballot
	 * @return \Goettertz\BcVoting\Domain\Model\Ballot
	 */
	public function getBallot() {
		return $this->ballot;
	}
	
	/**
	 * sets ballot
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @return void
	 */
	public function setBallot($ballot) {
		$this->ballot = $ballot;
	}	

	/**
	 * gets project
	 * @return \Goettertz\BcVoting\Domain\Model\Project
	 */
	public function getProject() {
		return $this->project;
	}
	
	/**
	 * sets project
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function setProject($project) {
		$this->project = $project;
	}
}
?>