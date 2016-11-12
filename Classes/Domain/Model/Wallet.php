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

/******************************************************************
 * for Revision 22, 30.05.2016
 */

/**
 * Options for voters
 */
class Wallet extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * owner
	 * 
	 * @var \Goettertz\BcVoting\Domain\Model\User
	 */
	protected $owner = NULL;
	
	/**
	 * wallet file
	 * @var string
	 */
	protected $walletFile = '';
	
	/**
	 * gets the owner of the wallet
	 * @return \Goettertz\BcVoting\Domain\Model\User
	 */
	public function getOwner() {
		return $this->owner;
	}
	
	/**
	 * sets the owner
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\User $owner
	 * @return void
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	
	/**
	 * gets the wallet file
	 * @return string
	 */
	public function getWalletFile() {
		return $this->walletFile;
	}
	
	/**
	 * set the wallet file
	 * @param string $walletFile
	 * @return void
	 */
	public function setWalletFile($walletFile) {
		$this->walletFile = $walletFile;
	}
}
?>