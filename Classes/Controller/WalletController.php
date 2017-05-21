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
 * Revision 148
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
	 * action funding
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * 
	 */
	public function fundingAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$amount = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($project->getWalletAddress());
		
		$vtc = 100;
 		$priceEuro = 0.12; //$this->setting['payment_voting'];
		$euroBtc = 1350;
		$priceBtc = $priceEuro / $euroBtc;
		$btc = $vtc * $priceBtc;
		
		$this->view->assign('amount', $amount);
		$this->view->assign('project', $project);
		$this->view->assign('btc', $btc);
		$this->view->assign('priceEuro', $priceEuro);
		$this->view->assign('priceBtc', $priceBtc);
		$this->view->assign('euroBtc', $euroBtc);
		$this->view->assign('vtc', $vtc);
	}
	
	/**
	 * action withdraw
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 *
	 */
	public function withdrawAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->view->assign('project', $project);
	}	
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
				$amount = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($project->getWalletAddress());
			}
			
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
				$this->view->assign('project', $project);
				if (empty($project->getRpcServer())) {
					$result['error'] = 'No RPC-Server!';
					$this->addFlashMessage('No RPC-Server!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}				
			}
			
			$transactions = array();
			$addresses = array();
			$assets = array();
			$rpcAssets = array();
			
			if ($project !== null) {
				$transactions = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listaddresstransactions($project->getWalletAddress(), 100);
			} 
			else {
				# Abfrage für alle assignments (transaktionen, asset balances)
				$assignments = $this->assignmentRepository->findByUser($user);
					
				foreach ($assignments as $assignment) {
					if (!empty($assignment->getWalletAddress())) {
						if (!in_array($assignment->getWalletAddress(), $addresses))
							$addresses[] = $assignment->getWalletAddress();
								
					}
					if ($assigment->getProject()) {
						$project = $assigment->getProject();
						foreach ($addresses AS $address) {
							// 					if (!in_array($address, $uniqueaddresses)) $uniqueaddresses[] = $address;
							#get transaction-data from bc ...
								
							if(is_string($address)) $newtransactions = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listaddresstransactions($address, 10);
							$transactions = array_merge($transactions, $newtransactions);
							$assets[$address] = Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $address);
							// 					sort($assets[$address]['total'], 0);
							if ($assets[$address] = $assets[$address]['total']) {
								$i = 0;
								foreach ($assets[$address] AS $key) {
									$assets[$address][$i]['address'] = $address;
									$ballots = $this->ballotRepository->findByAsset($assets[$address][$i]['assetref']);
									if ($ballot = $ballots[0]) {
										// 									$project = $ballot->getProject();
										$assets[$address][$i]['project'] = $project;
										if (!empty($assets[$address][$i]['name'])) $assets[$address][$i]['name'] = str_repeat('0',(2 - strlen($project->getUid()))).$project->getUid() . ' ' . $assets[$address][$i]['name'];
									}
									$i++;
								}
								sort($assets[$address], 0);
							}
						}
					}
				} # end foreach assignments
			} # end if no project
		}
		else {
			# Warning:  not logged in!
			die('Not logged in!');
		}
		
		$this->view->assign('result', $result);
		$this->view->assign('user', $user);
		
		$this->view->assign('assigned', $isAssigned);
		$this->view->assign('addresses', $addresses);
		$this->view->assign('transactions', $transactions);
		$this->view->assign('assets', $assets);
		$this->view->assign('amount', $amount);
	}
	
	/**
	 * importAction
	 * 
	 * Adds the privkey private key (as obtained from a prior call to dumpprivkey) 
	 * to the wallet, together with its associated public address. If rescan is true, 
	 * the entire blockchain is checked for transactions relating to all addresses 
	 * in the wallet, including the added one.
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignment
	 * @param string $key
	 * @param string $address
	 */
	public function importAction() {

		# Nur der Feuser selbst darf eine PaperWallet importieren.
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$assignments = $this->assignmentRepository->findByUser($user);
			$this->view->assign('assignments', $assignments);
			
			# Balances for assignment address
			
			# Transactions  for assignment address
		}
	}

	/**
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignment
	 * @param string $key
	 * @param string $address
	 * @return void
	 */
	public function importPrivKeyAction(\Goettertz\BcVoting\Domain\Model\Assignment $assignment, $key, $address) {
		
		# Sicherheitsabfragen fehlen
		if ($user = $this->userRepository->getCurrentFeUser()) {
			if ($user !== $assignment->getUser()) {
				die('Not allowed!');
			}
		}
		
		$project = $assignment->getProject();
		$project = $project->checkRpc($project);
		
		if ($result = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->importprivkey($key,'Imported',true) === NULL) {
			
			# Adresse registrieren
			$assignment->setWalletAddress($address);
			$this->assignmentRepository->update($assignment);
		}
		
		
		$this->redirect('show');
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