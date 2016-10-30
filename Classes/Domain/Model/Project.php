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
 ***************************************************************/

/**
 * Revision 117
 */

/**
 * Project
 */
class Project extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name = '';
	
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
	 * 
	 * @var \Goettertz\BcVoting\Domain\Model\Category
	 * 
	 */
	protected $category = null;
	
	/**
	 * 
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $usergroup;
	 */
	protected $usergroup;
	
	/**
	 * walletAddress for native currency VTC
	 *
	 * @var string
	 */
	protected $walletAddress = NULL;
	
	/**
	 * description
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $description = '';
	


	/**
	 * tstamp start
	 *
	 * @var string
	 * 
	 * @validate NotEmpty
	 */
	protected $start = '';

	/**
	 * tstamp end
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $end = '';

	/**
	 * assignments
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Assignment>
	 * @cascade remove
	 */
	protected $assignments = NULL;
	
	/**
	 * ballots
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Ballot>
	 * @cascade remove
	 */
	protected $ballots = NULL;

	
	/**
	 * 
	 * @var string
	 */
	protected $infosite = '';
	
	/**
	 *
	 * @var string
	 */
	protected $forumUrl = '';
	
	/**
	 * 
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Argument>
	 */
	protected $arguments = '';
		
	/**
	 * blockchain
	 * 
	 * type of blockchain e.g. bitcoin, multichain, ethereum etc. or NULL = no blockchain
	 * @var string
	 */
	protected $blockchain = NULL;
	
	/**
	 * blockchainName
	 * @var string
	 */
	protected $blockchainName = '';
	
	/**
	 * blockchainPort
	 * @var integer
	 */
	protected $blockchainPort = 0;
	
	/**
	 * RPC-Server
	 * @var string
	 */
	protected $rpcServer = NULL;
	
	
	/**
	 * rpcUser
	 * @var string
	 * 
	 */
	protected $rpcUser = '';
	
	/**
	 * rpc_port
	 * @var integer
	 */
	protected $rpcPort = NULL;
	
	/**
	 * rpcPassword
	 * @var string
	 */
	protected $rpcPassword = NULL;
	
	/**
	 * nodes
	 * @var integer
	 */
	protected $nodes = 0;
	
	/**
	 * returns nodes #
	 */
	public function getNodes() {
		return $this->nodes;
	}
	
	/** 
	 * set nodes #
	 */
	public function setNodes($nodes) {
		$this->nodes = $nodes;
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
	 * Returns the category
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Category $category
	 */
	public function getCategory() {
		return $this->category;
	}
	
	
	/**
	 * Sets the category
	 *
	 * @param string $category
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * Returns the start
	 *
	 * @return string $start
	 */
	public function getStart() {
		return $this->start;
	}

	/**
	 * Sets the start
	 *
	 * @param string $start
	 * @return void
	 */
	public function setStart($start) {
		$this->start = $start;
	}

	/**
	 * Returns the end
	 *
	 * @return string $end
	 */
	public function getEnd() {
		return $this->end;
	}

	/**
	 * Sets the end
	 *
	 * @param string $end
	 * @return void
	 */
	public function setEnd($end) {
		$this->end = $end;
	}
	
	/**
	 * returns the blockchain name
	 * @return string
	 */
	public function getBlockchainName() {
		return $this->blockchainName;
	}
	
	/**
	 * sets the blockchain name
	 * 
	 * @param string $blockchainName
	 * @return void
	 */
	public function setBlockchainName($blockchainName)
	{
		$this->blockchainName = $blockchainName;
	}
	

	/**
	 * returns the blockchain port
	 * @return integer
	 */
	public function getBlockchainPort() {
		return $this->blockchainPort;
	}
	
	/**
	 * sets the blockchain port
	 *
	 * @param integer $blockchainPort
	 * @return void
	 */
	public function setBlockchainPort($blockchainPort)
	{
		$this->blockchainPort = $blockchainPort;
	}
	
	/**
	 * gets the RPC user
	 * @return string
	 */
	public function getRpcUser()
	{
		return $this->rpcUser;
	}
	
	/**
	 * sets RPC user
	 * 
	 * @param $rpcUser
	 * @return void
	 */
	public function setRpcUser($rpcUser)
	{
		$this->rpcUser = $rpcUser;
	}

	/**
	 * gets the RPC Password
	 * @return string
	 */
	public function getRpcPassword()
	{
		return $this->rpcPassword;
	}
	
	/**
	 * sets RPC password
	 *
	 * @param $rpcPassword
	 * @return void
	 */
	public function setRpcPassword($rpcPassword)
	{
		$this->rpcPassword = $rpcPassword;
	}

	/**
	 * gets the RPC Server
	 * @return string
	 */
	public function getRpcServer()
	{
		return $this->rpcServer;
	}
	
	/**
	 * sets RPC server
	 *
	 * @param $rpcServer
	 * @return void
	 */
	public function setRpcServer($rpcServer)
	{
		$this->rpcServer = $rpcServer;
	}
	
	/**
	 * gets the RPC Port
	 * @return integer
	 */
	public function getRpcPort()
	{
		return $this->rpcPort;
	}
	
	/**
	 * sets RPC port
	 *
	 * @param $rpcPort
	 * @return void
	 */
	public function setRpcPort($rpcPort)
	{
		$this->rpcPort = $rpcPort;
	}
	
	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {

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
	 * Returns the walletAddress
	 *
	 * @return string $walletAddress
	 */
	public function getWalletAddress() {
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
	 * Returns the ballots
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Ballot> $ballots
	 */
	public function getBallots($bc = false) {
		if ($bc === false) return $this->ballots;
		
		else {
			
		}
	}
	
	/**
	 * Sets the ballots
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Ballot> $ballot
	 * @return void
	 */
	public function setBallots(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $ballot) {
		$this->ballots = $ballot;
	}	

	/**
	 * Removes a Assignment
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignmentToRemove The Assignment to be removed
	 * @return void
	 */
	public function removeAssignment(\Goettertz\BcVoting\Domain\Model\Assignment $assignmentToRemove) {
		$this->assignments->detach($assignmentToRemove);
	}

	/**
	 * Returns the assignments
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Assignment> $assignments
	 */
	public function getAssignments() {
		return $this->assignments;
	}

	/**
	 * Sets the assignments
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Assignment> $assignments
	 * @return void
	 */
	public function setAssignments(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $assignments) {
		$this->assignments = $assignments;
	}

	/**
	 *
	 * Gets the project assignment for a specific user.
	 * Da könnte ein Fehler sein, wenn mehrere Assignments vorhanden sind ...
	 * 
	 * @param  \Goettertz\BcVoting\Domain\Model\User $user The user for which the assignment
	 * @param  string $role optional
	 *                                                             		is to be retrieved
	 * @return \Goettertz\BcVoting\Domain\Model\Assignment $assignment  The project assignment for the specified
	 *                                                         			user, or NULL if the user is not a member
	 *                                                            		of this project
	 */
	Public Function getAssignmentForUser(\Goettertz\BcVoting\Domain\Model\User  $user, $role = '') {
		$myAssignment = null;
		ForEach($this->assignments As $assignment) {
			If($assignment->getUser()->getUID() === $user->getUID()) {
				if ($role == '') {
					$myAssignment = $assignment;
				}
				else {
					if ($role == $assignment->getRole()->getName()) {
						$myAssignment = $assignment;
					}
				}
			}
		}
		return $myAssignment;
	}
	
	/**
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup 
	 */
	public function getUsergroup() {
		return $this->usergroup;
	}
	
	/**
	 * sets the usergroup for closed projects - muss noch in tca und sql!
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $usergroup
	 * @return void
	 */
	public function setUsergroup($usergroup) {
		$this->usergroup = $usergroup;
	}
	
	/**
	 *
	 * Determines whether a specific user is assigned to this project
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\User  $user The user whose assignment is
	 *                                                   to be checked.
	 * @return boolean                                   TRUE, if the user is assigned
	 *                                                   to this project, otherwise
	 *                                                   FALSE.
	 *
	 */	
	Public Function getIsAssigned(\Goettertz\BcVoting\Domain\Model\User  $user) {
		Return $this->getAssignmentForUser($user) !== NULL;
	}
	
	/**
	 * returns the infosite
	 */
	public function getInfosite() {
		return $this->infosite;
	}
	
	/**
	 * sets the infosite
	 */
	public function setInfosite($infosite) {
		$this->infosite = $infosite;
	}
	
	/**
	 * returns the forumUrl
	 */
	public function getForumUrl() {
		return $this->forumUrl;
	}
	
	/**
	 * sets the forumUrl
	 */
	public function setForumUrl($forumUrl) {
		$this->forumUrl = $forumUrl;
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
		$returnObject->description = $this->getDescription();
		$returnObject->start = $this->getStart();
		$returnObject->end = $this->getEnd();
		$returnObject->walletaddress = $this->getWalletAddress();

 		foreach ($this->getBallots() AS $ballot) {
 			$returnObject->ballots[] = $ballot->getReference();
 		}

		return json_encode($returnObject, JSON_FORCE_OBJECT);
	}	
}
?>
