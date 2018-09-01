<?php
namespace Goettertz\BcVoting\Domain\Model;

use Goettertz\BcVoting\Service\Blockchain;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015-2018 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 ***************************************************************/

/**
 * Revision 200
 */
 
/**
 * Ballot - Stimmzettel
 * 
 * There maybe more than one ballot per project for example one for the candidates and one for parties.
 */
class Ballot extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * name
	 *
	 * @var string
	 * 
	 */
	protected $name = '';
	
	/**
	 * starttime tstamp 
	 *
	 * @var string
	 *
	 * 
	 */
	protected $start = '';
	
	/**
	 * endtime tstamp 
	 *
	 * @var string
	 * 
	 */
	protected $end = '';
	
	/**
	 * project
	 *
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 */
	protected $project = NULL;

	/**
	 * reference
	 *
	 * blockchain-reference
	 *
	 * @var string
	 */
	protected $reference = '';
	

	/**
	 * logo
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 *
	 */
	protected $logo = NULL;
	
	/**
	 * asset
	 * 
	 * @var string
	 */
	protected $asset = NULL;

	/**
	 * text
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $text = '';
	
	/**
	 * footer
	 *
	 * @var string
	 *
	 */
	protected $footer = '';
	
	/**
	 * options
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Option>
	 * @cascade remove
	 */
	protected $options = NULL;
	
	
	/**
	 * json
	 * @var string
	 */
	protected $json = NULL;
	
	/**
	 * votes
	 * amount of votes for each ballot
	 * @var integer
	 */
	protected $votes;
	
	/**
	 * balance
	 * users balance of assets/voting rights
	 * @var float;
	 */
	protected $balance;

	/**
	 * walletAddress
	 * 
	 * Temp address for encrypted votings
	 *
	 * @var string
	 */
	protected $walletAddress = NULL;	
	
	/**
	 * ballotRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\BallotRepository
	 * @inject
	 */
	protected $ballotRepository = NULL;
	
	/**
	 * Returns the project
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function getProject() {
		return $this->project;
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
	 * Returns the starttime
	 *
	 * @return string $start
	 */
	public function getStart() {
		return $this->start;
	}
	
	/**
	 * Sets the starttime
	 *
	 * @param string $start
	 * @return void
	 */
	public function setStart($start) {
		$this->start = $start;
	}
	
	/**
	 * Returns the endtime
	 *
	 * @return string $endtime
	 */
	public function getEnd() {
		return $this->end;
	}
	
	/**
	 * Sets the endtime
	 *
	 * @param string $end
	 * @return void
	 */
	public function setEnd($end) {
		$this->end = $end;
	}

	/**
	 * Returns the walletAddress
	 * 
	 * Temp address for encrypted votings
	 *
	 * @return string $walletAddress
	 */
	public function getWalletAddress() {
		
// 		$bc = new \Goettertz\BcVoting\Service\Blockchain();
// 		$project = $this->getProject();
// 		if ($result = $bc->getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->validateaddress($this->walletAddress)) {
// 			if ($result) return $result['address'];
// 		}
// 		return NULL;
		return $this->walletAddress;
	}
	
	/**
	 * Sets the walletAddress
	 * 
	 * Temp address for encrypted votings
	 * 
	 * @param string $walletAddress
	 * 
	 * @return void
	 */
	public function setWalletAddress($walletAddress) {
		$this->walletAddress = $walletAddress;
	}	

	/**
	 * gets the reference id
	 *
	 * @return string
	 */
	public function getReference() {
		return $this->reference;
	}
	
	/**
	 * @param string $reference
	 * @return void
	 */
	public function setReference($reference) {
		$this->reference = $reference;
	}

	/**
	 * gets the reference id
	 *
	 * @return integer
	 */
	public function getVotes() {
		return $this->votes;
	}
	
	/**
	 * @param integer $votes
	 * @return void
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}
	
	/**
	 * Sets the project
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function setProject(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->project = $project;
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
	 * Returns the asset
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Asset $asset
	 */
	public function getAsset() {
		return $this->asset;
	}
	
	/**
	 * Sets the asset
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Asset $asset
	 * @return void
	 */
	public function setAsset($asset) {
		$this->asset = $asset;
	}
	
	/**
	 * @return \Goettertz\BcVoting\Domain\Model\float;
	 */
	public function getBalance() {
		return $this->balance;
	}
	
	/**
	 * @param float $balance
	 */
	public function setBalance($balance) {
		$this->balance = $balance;
	}
	
	/**
	 * Returns the text
	 *
	 * @return string $text
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Sets the text
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
	 * Returns the footer
	 *
	 * @return string $footer
	 */
	public function getFooter() {
		return $this->footer;
	}
	
	/**
	 * Sets the footer
	 *
	 * @param string $footer
	 * @return void
	 */
	public function setFooter($footer) {
		$this->footer = $footer;
	}
	
	/**
	 * Adds a Option
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @return void
	 */
	public function addOption(\Goettertz\BcVoting\Domain\Model\Option $option) {
		$this->options->attach($option);
	}
	
	/**
	 * Removes a Option
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Option $optionToRemove The Option to be removed
	 * @return void
	 */
	public function removeOption(\Goettertz\BcVoting\Domain\Model\Option $optionToRemove) {
		$this->options->detach($optionToRemove);
	}
	
	/**
	 * Returns the options
	 * 
	 * @param bool $blockchain
	 * @param bool $compare
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Option> $options
	 *
	 */
	public function getOptions($blockchain = false, $compare = false) {
		if ($blockchain === false) 
			return $this->options;
		else {
			# JSON from ballot-reference
			if (!empty($txid = self::getReference())) {
				$project = self::getProject();
				$result['json'] = \Goettertz\BcVoting\Service\Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $txid);
				$result['array'] = json_decode($result['json']);
				$i = 0;
				$allBalance = 0;
// 				$addresses = array();
				$options  = (array) $result['array']->options;
				foreach ($options AS $option) {
// 					$addresses[] = $result['ballot'][$i]['walletaddress'];
					$result['ballot'][$i] = (array) json_decode($option);
					
					if (!empty($result['ballot'][$i]['color']))
						$result['ballot'][$i]['color'] = str_replace("#","",$result['ballot'][$i]['color']);
					else $result['ballot'][$i]['color'] = '000000';
					
					if (empty($result['ballot'][$i]['logo'])) $result['ballot'][$i]['logo'] = '----';
					$result['ballot'][$i]['walletaddress'] = $result['ballot'][$i]['walletaddress'];
					$bc = new Blockchain();
					
					If ($balance = $bc->getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $result['ballot'][$i]['walletaddress'], self::getAsset())) {
						if (is_double($balance)) {
							$result['ballot'][$i]['balance'] = $balance;
							$allBalance = $balance + $allBalance;
						}
					}

					$i++;
				}
				
				$i = 0;
				if ($allBalance > 0) foreach ($options AS $option) {
					$result['ballot'][$i]['percent'] = 100*$result['ballot'][$i]['balance']/$allBalance;
					$result['ballot'][$i]['base'] = $allBalance;
					
					$i++;
				}
				return $result['ballot'];
			}
			else return $this->options;
		}
	}
	
	/**
	 * Sets the options
	 *
	
	 * @return void
	 */
	public function setOptions(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $options) {
		$this->options = $options;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getJson() {

		$returnObject = new \stdClass();
		$returnObject->uid = $this->getUid();
		$returnObject->name = $this->getName();
		$returnObject->logo = $this->getLogo();
		$returnObject->text = $this->getText();
		$returnObject->footer = $this->getFooter();
		$returnObject->start = $this->getStart();
		$returnObject->end = $this->getEnd();
		$returnObject->walletaddress = $this->getWalletAddress();
		$returnObject->reference = $this->getReference();
		$returnObject->asset = $this->getAsset();
		
		if (!empty($this->getOptions()))
		foreach ($this->getOptions() as $option) {
			$returnObject->options[] = $option->getJson();
		}

		return json_encode($returnObject, JSON_FORCE_OBJECT);
	}

	/**
	 * @param string $reference
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function import($reference, \Goettertz\BcVoting\Domain\Model\Project $project) {
		
		# get data from bc
		$bc = new \Goettertz\BcVoting\Service\Blockchain();
		$data = $bc::retrieveData($rpcServer, $rpcPort, $rpcUser, $rpcPassword, trim($reference));
		
		$data = json_decode($data);
		if (is_a($data, 'stdClass', false)) {
			$ballot = (array) $data;
		}
		else die ('No JSON object!');
		
		$this->setProject($project);
			
		$this->setReference($reference);
		$this->setName($ballot['name']);
		$this->setStart($ballot['start']);
		$this->setEnd($ballot['end']);
		$this->setText($ballot['text']);
		$this->setFooter($ballot['footer']);
		$this->setAsset($ballot['asset']);
		$this->setWalletAddress($ballot['walletaddress']);

		$this->ballotRepository->add($this);
		
		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManager->persistAll();
		
		# Import options
		foreach ($ballot['options'] AS $option) {
			$newOption = new \Goettertz\BcVoting\Domain\Model\Option();
				
			# Cast json to stdClass and array
			$option = json_decode($option);
			$option = (array) $option;
		
			# Set option
			$newOption->setBallot($this);
			$newOption->setName($option['name']);
			$newOption->setDescription($option['description']);
		
			$newOption->setWalletAddress($option['walletaddress']);
			$newOption->setColor($option['color']);
			$newOption->setParent($option['parent']);
		
			# Add option
			$this->optionRepository->add($newOption);
			$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
			$persistenceManager->persistAll();
		
		}		
		
		
	}
}

?>
