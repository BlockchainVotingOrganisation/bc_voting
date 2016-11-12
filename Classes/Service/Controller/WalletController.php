<?php
namespace Goettertz\BcVoting\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 - 2016 Louis Göttertz <info2016@goettertz.de>, goettertz.de
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
 * Revision 29 
 */

/**
 * ProjectController
 */
class WalletController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * action show
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\User $user) {
		
		$isAssigned = false;
		$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
		If($assignment != NULL) {
			$isAssigned = true;
			$role = $assignment->getRole($assignment);
			$roleName = $role->getName($role);
		}
		$bcArray = array();
		if ($project->getRpcServer() !== '') {
			$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
			if(is_array($blockchain->getBlockchain($project)->getaddressesbyaccount($user->getUsername()))) {
 				$bcArray = $blockchain->getBlockchain($project)->getaddressesbyaccount($user->getUsername());
				if (count($bcArray) == 0) {
					$blockchain->getBlockchain($project)->getaccountaddress($user->getUsername());
					$bcArray = $blockchain->getBlockchain($project)->getaddressesbyaccount($user->getUsername());
				}
			}
			$amount = $blockchain->getBlockchain($project)->getbalance($user->getUsername());			
		}
		
		$transactions = array();
		if ($project->getRpcServer() !== '') {
			$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
			if(is_array($blockchain->getBlockchain($project)->listtransactions($user->getUsername()))) {
				$transactions = $blockchain->getBlockchain($project)->listtransactions($user->getUsername());
			}
		}
		
		$this->view->assign('user', $user);
		$this->view->assign('project', $project);
		$this->view->assign('assigned', $isAssigned);
		$this->view->assign('addresses', $bcArray);
		$this->view->assign('transactions', $transactions);
		$this->view->assign('amount', $amount);
	}
	
	/**
	 * send action
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param string $toaddress;
	 * @param double $amount
	 * @return void
	 */
	public function sendAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project, $toaddress, $amount) {
		
		$isAssigned = false;
		$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
		If($assignment != NULL) {
			$isAssigned = true;
			$role = $assignment->getRole($assignment);
			$roleName = $role->getName($role);
		}
		
		$bcArray = array();
		if ($project->getRpcServer() !== '') {
			$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
		  	if ($amount >= 1) {
  				// Senden an Option-Adresse
				if ($bcArray = $blockchain->getBlockchain($project)->sendfrom($user->getUsername(),$toaddress, $amount)) {
					$this->addFlashMessage('Send done!<br /> '.$bcArray,'', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
				else {
					$this->addFlashMessage('Vote: Error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
 			}
			else {
 				$this->addFlashMessage('Vote failed: Not enough inputs!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			
		}
		$this->redirect('show', NULL, NULL, array('user' => $user, 'project' => $project, 'assignment' => $isAssigned));
	}
}

	
?>