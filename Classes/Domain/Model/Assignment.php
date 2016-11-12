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
 ***************************************************************/

/**
 * Revision 80
 */

/**
 * Assignment
 * hier fehlt noch der Username als abgeleitete Eigenschaft? Nein Nein nein
 */
class Assignment extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * project
	 *
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 * @validate NotEmpty
	 */
	protected $project = NULL;

	/**
	 * role
	 *
	 * @var \Goettertz\BcVoting\Domain\Model\Role
	 * @validate NotEmpty
	 */
	protected $role = NULL;
	
	/**
	 * fe_user
	 * @var \Goettertz\BcVoting\Domain\Model\User 
	 * @validate NotEmpty
	 */
 	protected $user = NULL;
 	

 	/**
 	 * walletAddress
 	 *
 	 * @var string
 	 */
 	protected $walletAddress = NULL;
 	
 	/**
 	 * 
 	 * @var integer $votes
 	 */
 	protected $votes;
 	

	public function __construct() 
	{
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
	 * Returns the role
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Role $role
	 */
	public function getRole() {
		return $this->role;
	}
	
	/**
	 * Sets the role
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Role $role
	 * @return void
	 */
	public function setRole(\Goettertz\BcVoting\Domain\Model\Role $role) {
		$this->role = $role;
	}

	/**
	 * Returns the project
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function getProject() {
		return $this->project;
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
	 * returns votes
	 * @return $votes
	 */
	public function getVotes() {
		return $this->votes;
	}
	
	/**
	 * sets the votes
	 * @param integer $votes
	 * @return void
	 */
	public function setVotes($votes) {
		$this->votes = $votes;
	}
	
	/**
	 * Returns the associated user
	 * @return \Goettertz\BcVoting\Domain\Model\User  $user The associated user
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * Sets the associated user
	 * @param \Goettertz\BcVoting\Domain\Model\User  $user The associated user
	 * @return void
	 */
	public function setUser(\Goettertz\BcVoting\Domain\Model\User  $user) {
		$this->user = $user;
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
	
}