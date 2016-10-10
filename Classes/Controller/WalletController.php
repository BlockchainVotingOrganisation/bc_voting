<?php
namespace Goettertz\BcVoting\Controller;
ini_set("display_errors", 1);
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 - 2016 Louis G�ttertz <info2016@goettertz.de>, goettertz.de
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
 * Revision 107
 */

use Goettertz\BcVoting\Service\Blockchain;

/**
 * WalletController
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
	 * projectRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\ProjectRepository
	 * @inject
	 */
	protected $projectRepository = NULL;

	/**
	 * assignmentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssignmentRepository
	 * @inject
	 */
	protected $assignmentRepository = NULL;
	
	/**
	 * ballotRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\BallotRepository
	 * @inject
	 */
	protected $ballotRepository = NULL;
	
	/**
	 * action show
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Project $project = NULL) {
		
		$isAssigned = 'false';
		if ($user = $this->userRepository->getCurrentFeUser()) {
			if ($project) {
				$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
				If($assignment != NULL) {
					$isAssigned = 'true';
					$role = $assignment->getRole($assignment);
					$roleName = $role->getName($role);
				}
				else {
					$result['error'] = 'Not assigned!';
					$this->addFlashMessage('Not assigned!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
			}
			
// 			$bcArray = array();
			if (!$project) {
				$projects = $this->projectRepository->findAll();
				foreach ($projects as $project) {
					if (!empty($project->getWalletAddress())) {
						if (empty($project->getRpcServer())) {
							$result['projects'][$project]['error'] = 'No RPC-Server!';
							
						}						
					}
				}
			}
			else {
				if (empty($project->getRpcServer())) {
					$result['error'] = 'No RPC-Server!';
					$this->addFlashMessage('No RPC-Server!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}				
			}
			
			$transactions = array();
			$addresses = array();
			$assets = array();
			$rpcAssets = array();
			
			# Abfrage für alle assignments (transaktionen, asset balances)
			$assignments = $this->assignmentRepository->findByUser($user);
			
			foreach ($assignments as $assignment) {
				if (!empty($assignment->getWalletAddress())) {
					if (!in_array($assignment->getWalletAddress(), $addresses))
						$addresses[] = $assignment->getWalletAddress();
					
				}
				
				foreach ($addresses AS $address) {
// 					if (!in_array($address, $uniqueaddresses)) $uniqueaddresses[] = $address;
					#get transaction-data from bc ...
					$newtransactions = Blockchain::getRpcResult($project)->listaddresstransactions("$address", 10);
					$transactions = array_merge($transactions, $newtransactions);
					$assets[$address] = Blockchain::getAssetBalanceFromAddress($project, $address);
// 					sort($assets[$address]['total'], 0);
					$assets[$address] = $assets[$address]['total'];
					
					$i = 0;
					foreach ($assets[$address] AS $key) {
						$assets[$address][$i]['address'] = $address;
						$ballots = $this->ballotRepository->findByAsset($assets[$address][$i]['assetref']);
						if ($ballot = $ballots[0]) {
							$project = $ballot->getProject();
							$assets[$address][$i]['project'] = $project;
							if (!empty($assets[$address][$i]['name'])) $assets[$address][$i]['name'] = str_repeat('0',(2 - strlen($project->getUid()))).$project->getUid() . ' ' . $assets[$address][$i]['name'];
						}					
						$i++;
					}
					sort($assets[$address], 0);
				}
			}
		}
		else {
			
		}
		$this->view->assign('result', $result);
		$this->view->assign('user', $user);
		$this->view->assign('project', $project);
		$this->view->assign('assigned', $isAssigned);
		$this->view->assign('addresses', $addresses);
		$this->view->assign('transactions', $transactions);
		$this->view->assign('assets', $assets);
		$this->view->assign('amount', $amount);
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param string $toAddress
	 * @param string $asset
	 * @param array $redirect
	 */
	public function sendAssetsAction(\Goettertz\BcVoting\Domain\Model\Project $project, $toAddress, $redirect = NULL, $fromAddress = '') {
		
		$isAdmin = 'false';
		# redirect default
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			# default args
			$args = array('user' => $user, 'project' => $project);
			if (!$redirect) $redirect = array(
					'action' => 'show', 
					'controller' => 'Wallet',
					'pluginName' => 'Wallet',
					'args' => $args);
				
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$isAdmin = 'true';
			}
		}
		
		if ($isAdmin === 'false') {
			$this->addFlashMessage('Not admin', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			# Error handling and/or break;
// 			break;
		}
		
		if ($project->getRpcServer() === '') {
			$this->addFlashMessage('No rpc', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				
			# Error handling and/or break;
			// 			break;
		}
		
		# SendsAssets for all Ballots
		// foreach ballots as ballot
		foreach ($project->getBallots() AS $ballot) {
			
			# if asset, votes
			if (!empty($ballot->getReference())) {
				$assetAmount = array($ballot->getAsset() => $ballot->getVotes());
			}
			else {
				$this->addFlashMessage('Ballot not complete!' . $toAddress .' ' .implode('-', $assetAmount), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 				break;
			}
			
			#if walletaddress
			if (!empty($project->getWalletAddress()))
				$fromAddress = $project->getWalletAddress();
			else {
				$this->addFlashMessage('Project not complete!' . $toAddress .' ' .implode('-', $assetAmount), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 				break;
			}
			
			# if checks ...
			
			$result[] = Blockchain::storeData($project, $fromAddress, $toAddress, $assetAmount, 'Admin: asset allocation!');
			$this->addFlashMessage('Send Assets: ' . $toAddress .' ' .implode('-', $assetAmount), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		}
		
		// for debugging purposes
		$this->view->assign('result', $result); 
		
		# finally redirect
		$this->redirect($redirect['action'], $redirect['controller'],'BcVoting', $redirect['args']);
	}
// 	/**
// 	 * send action
// 	 * @param \Goettertz\BcVoting\Domain\Model\User $user
// 	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
// 	 * @param string $toaddress;
// 	 * @param double $amount
// 	 * @return void
// 	 */
// 	public function sendAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project, $toaddress, $amount) {
		
// 		$isAssigned = false;
// 		$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
// 		If($assignment != NULL) {
// 			$isAssigned = true;
// 			$role = $assignment->getRole($assignment);
// 			$roleName = $role->getName($role);
// 		}
		
// 		$bcArray = array();
// 		if ($project->getRpcServer() !== '') {
// 			$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
// 		  	if ($amount >= 1) {
//   				// Senden an Option-Adresse
// 				if ($bcArray = \Goettertz\BcVoting\Service\Blockchain::getRpcResult($project)->sendfrom($user->getUsername(),$toaddress, $amount)) {
// 					$this->addFlashMessage('Send done!<br /> '.$bcArray,'', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
// 				}
// 				else {
// 					$this->addFlashMessage('Vote: Error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 				}
//  			}
// 			else {
//  				$this->addFlashMessage('Vote failed: Not enough inputs!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 			}
			
// 		}
// 		$this->redirect('show', NULL, NULL, array('user' => $user, 'project' => $project, 'assignment' => $isAssigned));
// 	}
}

	
?>