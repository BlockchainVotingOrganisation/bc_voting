<?php
namespace Goettertz\BcVoting\Controller;
ini_set("display_errors", 1);
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
 * Rev. 114
 */
use Goettertz\BcVoting\Service\Blockchain;
/**
 * UserController
 */
class UserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
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
	 * roleRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\RoleRepository
	 * @inject
	 */
	protected $roleRepository = NULL;

	/**
	 * assignmentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssignmentRepository
	 * @inject
	 */
	protected $assignmentRepository = NULL;
	
	/**
	 * action list
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project = NULL) {
		
		if ($project !== null)
		{
			$this->view->assign('project', $project);
			$this->view->assign('members', $project->getAssignments());
		}
		else $this->view->assign('members', $this->userRepository->findAll());
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\User $newuser
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\User $newuser = NULL) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$assignment = $feuser ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('newuser', $newuser);
				$this->view->assign('project', $project);
				$this->view->assign('redirect', $redirect);				
			}
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\User $newuser
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\User $newuser) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$assignment = $user ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				
			}
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 */
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\User $user) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$assignment = $user ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				
				if($this->request->hasArgument('process')) {
					if ($this->request->hasArgument('process') === TRUE) {
						# remove assignments 
						// find
						// foreach remove
						
						# remove user
						$this->userRepository->remove($user);
						$this->addFlashMessage('The user has been removed.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
						
						# redirect
// 						$this->redirect('edit', 'Project', NULL, array('project'=>$project));
					}
				}
				else {
					$this->view->assign('user', $user);
					$this->view->assign('project', $project);
				}

			}
		}		
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function removeAssignmentAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('user', $user);
				$this->view->assign('project', $project);
			}
		}		
	}
	
	/**
	 * action edit
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project) {
		# Get the user assignment and throw an exception if the current user is not a
		# member of the selected project.
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$isAssigned = 'false';
			$assignment = $feuser ? $project->getAssignmentForUser($feuser,'admin') : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$isAdmin = 'true';
				$this->view->assign('user', $user);
				$this->view->assign('project', $project);
				$this->view->assign('isAdmin', $isAdmin);
			}
			else {
				die('No admin!');
			}
		}
		else {
			die('Not allowed!');
		}
	}
	
	/**
	 * action update
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\User $user) {
		$this->userRepository->update($user);
		$this->addFlashMessage('The object was updated', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->redirect('edit');
	}
	/**
	 * action show
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('user', $user);
				$this->view->assign('project', $project);
				$this->view->assign('isAdmin', 'true');

				$assignments = $user ? $project->getAssignmentForUser($user) : NULL;
				
				$this->view->assign('assignments', $assignments);
				
				# Nur, wenn BC!!!
				if (!empty($project->getRpcServer())) {
					
					$address = NULL;
					if (!empty($assignments->getWalletAddress())) $address = $assignments->getWalletAddress();
					
					if (is_string($address)) {
						$transactions = array();
						$newtransactions = Blockchain::getRpcResult($project)->listaddresstransactions($address, 10);
						if (!is_string($newtransactions['error'])) {
							$transactions = array_merge($transactions, $newtransactions);
							$this->view->assign('transactions', $transactions);
						}
						
						
						$assets = Blockchain::getAssetBalanceFromAddress($project, $address);
					}
					$this->view->assign('assets', $assets);
				}
			}
		}
	}	
	
	/**
	 * action showRegistration
	 * @param \Goettertz\BcVoting\Domain\Model\User $newUser
	 * @return void
	 */
	public function actionShowRegistration(\Goettertz\BcVoting\Domain\Model\User $newUser) {
		$this->view->assign('user', $newUser);
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
		// 		$this->redirect($redirect['action'], $redirect['controller'], NULL, $redirect['args']);
		$this->redirect('list',NULL,NULL, array('project' => $project));
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function uploadAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->addFlashMessage('Try upload...', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		if (strval($_FILES['tx_bcvoting_project']['tmp_name']['file']['csv']))
		{
			//Import uploaded file to Database
			try {
				$this->addFlashMessage('Upload ok.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
				$handle = fopen($_FILES['tx_bcvoting_project']['tmp_name']['file']['csv'], "r");
		
				$i = 0;
				// zeilenweise lesen
				$records = array();
				while (($data = fgetcsv($handle, 1000000, ";")) != FALSE) {
					$records[$i] = $data;
					$i++;
				}
			} catch (Exception $e) {
				$this->addFlashMessage($e, 'CSV import failed!', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		
			$csvColumns = $this->getColumnNames($records);
		}		

		$this->view->assign('project', $project);
		$this->view->assign('files', $_FILES);
		$this->view->assign('options', $csvColumns);
		$this->view->assign('data', $records);
	}
	
	/**
	 * @param array $data
	 * @param array $fieldnames
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function importAction($data, $fieldnames, \Goettertz\BcVoting\Domain\Model\Project $project) {
		if($this->request->hasArgument('process'))
		{
			if ($this->request->hasArgument('process') === TRUE) {	
				if (is_array($fieldnames)) {
					if (count($fieldnames) > 0) {
						$this->addFlashMessage('Process...', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					}
					else {
						$this->addFlashMessage('No fieldnames!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					}
					
					// Process Data
					$records = array();
					for ($i = 1; $i < count($data); $i++)
					{
						// Spalten belegen (assoziatives Array anlegen)
						$records[$i]['username'] = $data[$i][$fieldnames['username']];
						$records[$i]['password'] = $data[$i][$fieldnames['password']];
						$records[$i]['email'] = $data[$i][$fieldnames['email']];

						// Neues Object
						$user = new \Goettertz\BcVoting\Domain\Model\User();
					
						// Titel
						$user->setUsername($records[$i]['username']);
					
						// Description
						$user->setPassword($this->saltedPassword($records[$i]['password']));
					
						// Quantity
						$user->setEmailAddress($records[$i]['email']);
					
						// Cart-Object hinzufügen
						$this->userRepository->add($user);
						
						// now persist all to have the possibility to use the new ITEM-UID p.e. in view...
						$persistenceManager = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
						$persistenceManager->persistAll();						
						
						# Assignment hinzufügen
						if (!$assignment = $user ? $project->getAssignmentForUser($user) : NULL) {
						
							# Falls noch keine Rolle member vorhanden ist
							$roles = $this->roleRepository->findByName('Member');
							if (count($roles) == 0) {
								$newRole = new \Goettertz\BcVoting\Domain\Model\Role();
								$newRole->setName('Member');
								$this->roleRepository->add($newRole);
								$roles[0] = $newRole;
							}
						
							# Mitglied als Member registrieren
							try {
								$assignment = $this->addAssignment($project, $user, $roles[0]);
									
								$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
								$persistenceManager->persistAll();
									
								if (!empty($project->getRpcServer())) {
									if ($assignment) {
											
										$newAddress = Blockchain::getRpcResult($project)->getnewaddress();
										$assignment->setWalletAddress($newAddress);
										$this->assignmentRepository->update($assignment);
											
										$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
										$persistenceManager->persistAll();
											
										$this->addFlashMessage('New Address: '.$newAddress.'.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
											
										# Für jeden Stimmzettel Assets senden
										foreach ($project->getBallots() as $ballot) {
											if ($bcArray = Blockchain::getRpcResult($project)->sendassettoaddress($newAddress,$ballot->getAsset(),$ballot->getVotes())) {
												$this->addFlashMessage($ballot->getName().': sending assets...ok '.$bcArray, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
												# VTC für Transaktionen bereitstellen ...
												if (!$bcArray['error']) $this->addFlashMessage('Send '.$ballot->getVotes().' Asset "'.$ballot->getAsset().'" to '.$newAddress.' ... ok!','', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
												else $this->addFlashMessage($bcArray['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
											}
											else {
												$this->addFlashMessage($ballot->getName().': sending Assets...failed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
											}
										}
									}
									else {
										$this->addFlashMessage('No Assignment!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
									}
								}
								else $this->addFlashMessage('No RPC-Server!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
						
							} catch (Exception $e) {
								$this->addFlashMessage($e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
							}
						}
					

					}

				}
				else {
					$this->addFlashMessage('No input data!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}	
			}
		}
		$this->view->assign('project', $project);
	}
	
	protected function saltedPassword($password) {		
		$saltedPassword = '';
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('saltedpasswords')) {
			if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
				$objSalt = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance(NULL);
				if (is_object($objSalt)) {
					return $saltedPassword = $objSalt->getHashedPassword($password);
				}
			}
		}
	}
	
	/**
	 * Extract first line values
	 *
	 * @param array $records
	 * @return array
	 */
	protected function getColumnNames($records) {
		foreach ($records[0] AS $key => $value) {
			$csvColumns[] = $value;
		}
		return $csvColumns;
	}
	

	/* compare user with typo3 database */
	private function checkUsername($username) {
			
		$selectFields='username';
		$fromTable='fe_users';
		$whereClause='username="'.$username.'" and deleted=0 and pid='.$this->importPID;
		$groupBy='';
		$orderBy=''; // 'field(uid,' . $orderedUidList . ')';
		$limit='1';
	
		$recordList=$GLOBALS['TYPO3_DB']->exec_SELECTquery( $selectFields
				, $fromTable
				, $whereClause
				, $groupBy
				, $orderBy
				, $limit
				);
	
		if ($GLOBALS['TYPO3_DB']->sql_num_rows( $recordList ) == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * adds a new assignment
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project;
	 * @param \Goettertz\BcVoting\Domain\Model\User $user;
	 * @param string $role
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Assignment
	 */
	protected function addAssignment(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\User $user, $role) {
		$assignment = New \Goettertz\BcVoting\Domain\Model\Assignment();
		$assignment->setProject($project);
		$assignment->setUser($user);
		$assignment->setRole($role);
		$assignment->setVotes(1);
	
		$this->assignmentRepository->add($assignment);
		return $assignment;
	}
}
?>
