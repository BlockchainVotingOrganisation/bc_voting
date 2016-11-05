<?php
namespace Goettertz\BcVoting\Controller;

ini_set("display_errors", 1);

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
 * Revision 118:
 */

use \Goettertz\BcVoting\Service\Blockchain;
use \Goettertz\BcVoting\Service\MCrypt;

/**
 * ProjectController
 */
class ProjectController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * projectRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\ProjectRepository
	 * @inject
	 */
	protected $projectRepository = NULL;

	/**
	 * votingRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository;
	
	/**
	 * optionRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\OptionRepository
	 * @inject
	 */
	protected $optionRepository;
	
	/**
	 * assignmentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssignmentRepository
	 * @inject
	 */
	protected $assignmentRepository = NULL;
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * roleRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\RoleRepository
	 * @inject
	 */
	protected $roleRepository = NULL;	
	
	/**
	 * categoryRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository = NULL;
	
	/**
	 * argumentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\ArgumentRepository
	 * @inject
	 */
	protected $argumentRepository = NULL;	
	
	/**
	 * ballotRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\BallotRepository
	 * @inject
	 */
	protected $ballotRepository = NULL;
	
	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$projects = $this->projectRepository->findAll();
		$this->view->assign('projects', $projects);
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign('feuser', $feuser);
		}
	}
	
	/**
	 * action nav1
	 *
	 * @return void
	 */
	public function nav1Action() {
		$this->listAction();
	}
	
	/**
	 * action show
	 * - shows projekt-data
	 * - Abgegebene Stimmen zählen
	 * - Blockchain-Info laden
	 * - Projekt-Optionen suchen
	 * - Benutzerdaten projektbezogen laden
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		$isAssigned = 'false';
		$isAdmin 	= 'false';
		
		$amount = 0;
		// Benutzerdaten projektbezogen laden
		
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign('feuser', $feuser);
			$username = $feuser->getUsername();
			
			$assignment = $feuser ? $project->getAssignmentForUser($feuser) : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$this->view->assign('isAssigned', $isAssigned);
				$walletAddress = $assignment->getWalletAddress();
			}

				
			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				$isAdmin = 'true';
				$this->view->assign('isAdmin', $isAdmin);
			}
			
			$rpcServer = $project->getRpcServer();

			if (is_string($rpcServer) && $rpcServer !== '') {
				try {
					if($bcArray = Blockchain::getRpcResult($project)->getinfo()) {
						$this->view->assign('bcResult', $bcArray);
					}
					
					if ($assets = Blockchain::getRpcResult($project)->getmultibalances($walletAddress)) {
						$ballots = $project->getBallots();
						if (count($ballots) > 0) {
							foreach ($ballots AS $ballot) {
							
								if ($assetref = $ballot->getAsset()) {
									foreach ($assets[$walletAddress] as $asset) {
											
										if ($assetref == $asset['assetref']) {
											$ballot->setBalance($asset['qty']);
										}
										$this->ballotRepository->update($ballot);
									}
								}
							}							
						}

					}
				}
				catch (\Exception $e) {
				}
			}
			else {
				$this->view->assign('blockchain', 'No rpc.');
			}
		}		
		$this->view->assign('project', $project);
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('date_now', new \DateTime());
	}

	/**
	 * action new
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $newProject
	 * @ignorevalidation $newProject
	 * @return void
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\Project $newProject = NULL) {
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			if ($newProject == NULL) $newProject = new \Goettertz\BcVoting\Domain\Model\Project();
			$this->view->assign('newProject', $newProject);
			$this->view->assign('feuser', $user);
		}
		else {				
			$this->addFlashMessage('You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		}
	}

	/**
	 * action create
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $newProject
	 * 
	 * @return void
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Project $newProject) {
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			
			# gets correct UNIX timestamp only if contained in formdata
			$start = strtotime($newProject->getStart()); $end = strtotime($newProject->getEnd());
			if($start > 0) $newProject->setStart($start); if ($end > 0) $newProject->setEnd($end);
				
			
			$this->projectRepository->add($newProject);
			
			$this->addFlashMessage('The project "'.$newProject->getName().'" was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				
			$roles = $this->roleRepository->findByName('admin');
				
			if (count($roles) == 0) {
				$newRole = new \Goettertz\BcVoting\Domain\Model\Role();
				$newRole->setName('admin');
				$this->roleRepository->add($newRole);
				$roles[0] = $newRole;
			}

			if ($this->addAssignment($newProject, $user, $roles[0])) {
				$this->addFlashMessage('The project\'s "'.$newProject->getName().'" assignment "'.$newProject->getName().'" was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			}
			
			$persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
			$persistenceManager->persistAll();
		}
		else {
			$this->addFlashMessage('You aren\'t currently logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}

	

		if ($project = $this->projectRepository->findByUid($newProject->getUid())) {
			$this->redirect('createSettings', 'Project', 'BcVoting', array('project' => $project), $this->settings['redirectUrl']);
		}
		else {
			$this->addFlashMessage('Can\'t find new project!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		}	
	}
	
	/**
	 * action createSettings
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * 
	 * @return void
	 */
	public function createSettingsAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($user = $this->userRepository->getCurrentFeUser()) {			
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				
				$categories = $this->categoryRepository->findAll();
				
				$this->view->assign('categories', $categories);
				$this->view->assign('project', $project);
				$this->view->assign('isAdmin', 'true');
			}
		}
	}
	
	/**
	 * action settings
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * 
	 * @return void
	 */
	public function settingsAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($user = $this->userRepository->getCurrentFeUser()) {			
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				
				$categories = $this->categoryRepository->findAll();
				
				$this->view->assign('categories', $categories);
				$this->view->assign('project', $project);
				$this->view->assign('isAdmin', 'true');
			}
		}
	}
	
	/**
	 * action edit
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @ignorevalidation $project
	 * @return void
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
 		# Get the user assignment and throw an exception if the current user is not a
 		# member of the selected project.
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$isAssigned = 'false';
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$isAdmin = 'true';
				$role = $assignment->getRole($assignment);
				$roleName = $role->getName($role);
				$categories = $this->categoryRepository->findAll();
				if (empty($project->getReference())) {
					$this->view->assignMultiple(array('project' => $project, 'isAssigned' => $isAssigned, 'isAdmin' => $isAdmin, 'categories' => $categories, 'feuser' => $user));
				}
				else {
					$this->addFlashMessage('Project already sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('show','Project','BcVoting',array('project'=>$project));
				}
				
			}
			else {
					$this->addFlashMessage('No admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('show','Project','BcVoting',array('project'=>$project));
// 				die('No admin!');
			}
		}
		else {
			$this->addFlashMessage('Not allowed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show','Project','BcVoting',array('project'=>$project));
				
			die('Not allowed!');
		}
	}
	
	/**
	 * action editbcparams -Blockchain-Parameter e.g. Blockchain name, rpc etc.
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @ignorevalidation $project
	 * @return void
	 */
	public function editbcparamsAction(\Goettertz\BcVoting\Domain\Model\Project $project)
	{
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$isAssigned = false;
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('isAdmin', 'true');
				$rpcServer = $project->getRpcServer();
				if (is_string($rpcServer) && $rpcServer !== '') {
					try {
						$bcArray = Blockchain::getRpcResult($project)->getinfo();
						if(is_array($bcArray)) {
							$this->view->assign('blockchain', $bcArray);
						}
					}
					catch (\Exception $e) {
						// 			echo $e.' '.$project->getName();
					}
				}
				else {
					$this->view->assign('blockchain', NULL);
				}
				
				$this->view->assign('project', $project);
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
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * 
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Project $project) {

		// Nur update, wenn login noch möglich
		if ($this->request->hasArgument('save_seal')) {
			$this->addFlashMessage('The project was sealed.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		}
		if ($user = $this->userRepository->getCurrentFeUser()) {

			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				
				# gets correct UNIX timestamp only if contained in formdata
				$start = strtotime($project->getStart()); $end = strtotime($project->getEnd());
				if($start > 0) $project->setStart($start); if ($end > 0) $project->setEnd($end);
								
				$this->projectRepository->update($project);
				$this->addFlashMessage('The project was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			}
			else {
				$this->addFlashMessage('You\'re no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		}		
		else {
			$this->addFlashMessage('You\'re not logged in!!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		if ($this->request->hasArgument('redirect')) {
			$redirect = $this->request->getArgument('redirect');
			if (is_array($redirect)) {
				$this->redirect($redirect['action'],$redirect['controller'],$redirect['extension'], array('project' => $project));
			}			
		}
		$this->redirect('edit','Project','BcVoting',array('project'=>$project));
	}

	/**
	 * action delete
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$isAssigned = false;
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				$this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				$this->projectRepository->remove($project);
			}
			else {
				$this->addFlashMessage('You\'re no admin!!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}			
		}
		else {
			$this->addFlashMessage('You\'re not logged in!!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		$this->redirect('list');
	}

	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function removeLogoAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\'tx_bcvoting_domain_model_ptoject\' AND fieldname=\'logo\' AND uid_foreign = '.$ptoject->getUid().' AND deleted = 0';
		$db = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$this->redirect('edit','Ptoject','BcVoting',array('ptoject'=>$ptoject));
	}
		
	/**
	 * action assign
	 * 
	 * assigns an user to a project as a member/voter
	 * - send votes/assets to the wallet-address for this assignment
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function assignAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\User $user = NULL) {
		
		$user = $this->userRepository->getCurrentFeUser();
		if ($user === NULL) {
			$loginPid = $this->settings['login'];
			$registrationPid = $this->settings['registration'];
			
			$this->view->assign('project', $project);
			$this->view->assign('login', $loginPid);
			$this->view->assign('register', $registrationPid);
		}
		else {
			//Prüfen, ob bereits Mitglied
			if (!$assignment = $user ? $project->getAssignmentForUser($user) : NULL) {
				// 				$assignment = new \Goettertz\BcVoting\Domain\Model\Assignment();
			
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
									$this->addFlashMessage($ballot->getName().': sending assets...ok ', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
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
			$this->redirect('show',NULL,NULL,array('project' => $project, 'bcResult' => $bcArray));
		}
	}
	
	/**
	 * action arguments
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 *
	 * @return void
	 */
	public function argumentsAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$arguments = $this->argumentRepository->findByProject($project);
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign('user', $user);
			$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('assigned', true);
				$this->view->assign('assignment', $assignment);
				$this->view->assign('project', $project);
				$this->view->assign('arguments', $arguments);	
			}
		}
	}
	
	/**
	 * 
	 * @return string[]|number[]
	 */
	public function checkVotingAction() {
		
		if ($this->request->hasArgument('voting')) {
			$voting = $this->request->getArgument('voting');
			$result['txid'] = $voting;
			$this->view->assign('voting', $voting);
		}
		
		$result = array();
		$result['result'] = false;
		$result['error'] = NULL;
		
		if (!$txid = trim($voting['reference'])) {
			
			$result['error'] = 'No txid!';
			$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 			return $result;			
		}
		else {
			$result['result'] = true;
			$result['txid'] = $txid;
		}
		
		# Daten aus DB
		$project = NULL;
		$votes = $this->votingRepository->findByTxid($txid);
		if (count($votes) > 0) {
			$vote = $votes[0];
			$secretDB = $vote->getSecret();
			# getProject
			if ($project = $vote->getProject()) {
				$this->view->assign('project',$project);
				$result['secretDB'] = $secretDB;				
			}
		}
		else {
			$result['error'] = 'TxId not found in DB!';
			$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}

		# Daten aus BC
		if ($project) {
			if (!$result['secretBC']  = Blockchain::retrieveData($project, $txid)) {
				$result['error'] = 'No blockchain data!';
				$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			else {
				if ($result['secretDB'] === $result['secretBC']) {
					$mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
					$result['decrypted'] = $mcrypt->decrypt($result['secretBC']);
					$this->addFlashMessage('Option: ' . $result['decrypted'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
			}
		}
		
		
		// Benutzerdaten projektbezogen laden
		$isAssigned = 'false';
		$isAdmin = 'false';
		$amount = 0;
		
		if ($project) {
			if ($user = $this->userRepository->getCurrentFeUser()) {
				$username = $user->getUsername();
				if ($blockchain) {
					$amount = $blockchain->getUserBalance($username, $project);
				}
			
				$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
				If($assignment != NULL) {
					$isAssigned = 'true';
					$role = $assignment->getRole($assignment);
					$roleName = $role->getName($role);
				}
			
				$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
				If($assignment != NULL) {
					$isAdmin = 'true';
				}
			}
		}
		$this->view->assign('result',$result);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('isAdmin', $isAdmin);
	}
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function getOptionListResults($data) {		
 		$x = 0;
 		foreach ($data AS $option) {	
 			if ($option['parent'] == 0) {
  				$balance = $option['balance'];
 				$i = 0;
 				foreach ($data AS $suboption) {
  					if ($suboption['parent'] == $option['uid']) {
 						# Balance der Child-Options summieren
  						$balance = $suboption['balance'] + $balance;
  						$base = $suboption['base'];
  						
  						#Child-Option entfernen
   						array_splice($data, $i, 1);
  					}
   					$i++;
				}
 				$data[$x]['balance'] = $balance;
 				if (($base + $option['base']) !== 0) {
 					$data[$x]['percent'] = 100*$balance/($base + $option['base']);
 				}
				$data[$x]['base'] = $base + $data[$x]['base'];
 			}
 			$x++;
  		}
		return $data;
	}
	
	/**
	 * evaluation of voting results
	 * - data from database compared with bc
	 * - better would be data only from bc see getOptions()
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function evaluationAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
			
		if ($result = $this->getOptions($project)) {
			if (is_string($result['error'])) {
				$this->addFlashMessage($result['error']. ' (748)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show',NULL,NULL, array('project' => $project));
			}
			
			$result['url_csv'] = $this->settings['downloadurl'];
			
			$csv = $this->generateCsvFromArray($result);
			
			if (is_string($csv['error'])) {
				$result['error'] = $csv['error'];
				$this->addFlashMessage($result['error'] . ' (756)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			
			$data = array();
			foreach($project->getBallots() AS $ballot) {
				$data = array_merge($data, $result[$ballot->getName()]['options']);
			}
			
			$result['lists'] = $this->getOptionListResults($data);
		}
		else 
		{
			$result['error'] = 'Can\'t get project options from blockchain! (748)';
			$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show',NULL,NULL, array('project' => $project));
		}

		# Begin old methods from db - should be replaced by bc		
		if($bcArray = Blockchain::getRpcResult($project)->getinfo()) {
			$this->view->assign('bcResult', $bcArray);
		}

		# votes (from db)
		$votes = $this->votingRepository->findByProject($project);
		
		if (count($votes) === 0) {
			$result['error'] = 'No  votings!';
			$this->redirect('show',NULL,NULL, array('project' => $project));			
		}
		
		$i = 0;
		$j = 0;
		$values = array();
		$allBalance = 0;
		
		if (count($votes) > 0)
		foreach ($votes as $vote) {
			
			$ref = $vote->getReference();
			$secretDB = $vote->getSecret();
			
			$secretBC = Blockchain::retrieveData($project, $vote->getTxid());

			if ($secretBC === $secretDB) { //Ist gültig
				$mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
				$j++;
				$values[$i] = $mcrypt->decrypt($secretBC);
				$values[$i] = explode("-", $values[$i]);
				$values[$i] = $values[$i][0];
				
				if (count($options = $this->optionRepository->findByName(trim($values[$i]))) > 0) {
					
					$option = $options[0];
					$ballot = $option->getBallot();
					if (!$result1[$ballot->getName()]) $result1[$ballot->getName()] = 0;
					$result1[$ballot->getName()]++;
				
				}
			}
			else {
				$values[$i] = 'wrong';
			}
			$i++;
		}

		# Neues Array: Häufigkeiten der einzelnen Ergebnisse sortiert 
		$counts = array_count_values($values);

		# Array mit Häufigkeiten und anderen Details

		$i = 0;
		
		if (count($counts) > 0) {
			foreach ($counts as $key => $value) {
	
				$counts[$key] = $value;
				$votings[$i]['name'] = htmlspecialchars(trim($key));
				$votings[$i]['counts'] = $value; 
				$votings[$i]['color'] = '#000000';
				
					
				if (count($options = $this->optionRepository->findByName(trim($key))) > 0) {
					$option = $options[0];
					$votings[$i]['color'] = str_replace("#","",$option->getColor());
	
					$votings[$i]['walletAddress'] = $option->getWalletAddress();
					$ballot = $option->getBallot();
					$votings[$i]['ballot'] = $ballot->getName();
				}
	
				# Wichtung
	 			$votings[$i]['width'] = 100 * ($value/$result1[$ballot->getName()]);
	
				$i++;
			}
			
			usort($votings, function ($a, $b) {
				return $b['counts'] - $a['counts'];
			});
		}

		// Benutzerdaten projektbezogen laden
		$isAssigned = 'false';
		$isAdmin = 'false';
		$$assetHolders = 0;			

		if ($user = $this->userRepository->getCurrentFeUser()) {
			$username = $user->getUsername();
			if ($blockchain) {
				$$assetHolders = $blockchain->getUserBalance($username, $project);
			}
					
			$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$role = $assignment->getRole($assignment);
				$roleName = $role->getName($role);
				
			}
					
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$isAdmin = 'true';
				
			}
		}
		
		$this->view->assign('project', $project);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('counts', $votings);
		$this->view->assign('result', $result);
		$this->view->assign('date_now', new \DateTime());
	}

	/**
	 * execute votings
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function executeAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
	
		# Checks
		$isAdmin = 'false';
		if ($user = $this->userRepository->getCurrentFeUser()) {
	
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$isAdmin = 'true';
			}
			else {
	
				#Fehlermeldung und break
				$this->addFlashMessage('No Admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				// 				break;
			}
				
			# Blockchain eingetragen?
			if (empty($project->getRpcServer()) || !is_string($project->getRpcServer())) {
	
				# Fehlermeldung und break
				$this->addFlashMessage('No RPC-Server!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				// 				break;
			}
			$end = $project->getEnd();
			if ($end <  time()) {
				if ($this->request->hasArgument('password')) {
					$result['msg'] = '1';
					if (!empty($this->request->getArgument('password'))) {
						$result = $this->execute($project);
// 						$result['msg'] = '2';
					}
				 else $result['error'] = '2';
				}
				else {
					$result['error'] = '3';
				}
			}

			$this->view->assign('result', $result);
			$this->view->assign('isAdmin', $isAdmin);
			$this->view->assign('project', $project);
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function sealAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		# Check if sealed
		if ($project->getReference() === '') {
		
			# The data for sealing ...
			$json = $project->getJson($project);
			$hash = hash('sha256', $json);
		
			# Saving data in the blockchain ...
			
			$vtc_amount = $this->settings['payment_sealing'];
			if ($vtc_amount < 0.00000001) $vtc_amount = 0.00000001;
			$this->addFlashMessage('VTC: '.doubleval($vtc_amount), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);

			if ($ref = Blockchain::storeData($project, $project->getWalletAddress(), $project->getWalletAddress(), doubleval($vtc_amount), $json)  ) {
					
				$project->setReference($ref);
				$this->projectRepository->update($project);
// 				$this->addFlashMessage('The project was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				
				$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
				$persistenceManager->persistAll();
				
				if (!is_array($ref)) {
					if (is_string($ref)) $this->addFlashMessage('The project was sealed. TxId: '.$ref, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
				elseif (is_string($ref['error']))  $this->addFlashMessage('ERROR:  '.$ref['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				else  $this->addFlashMessage('ERROR:  '.implode('-', $ref), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
// 			$this->addFlashMessage('Success:  '.$project->getName().' sealed', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		}
		
		$this->view->assign('ref', $ref);
		$this->view->assign('project', $project);
		$this->view->assign('json', $json);
		$this->view->assign('hash', $hash);
	}
	
	/**
	 * import
	 *  
	 * imports project from blockchain
	 * 
	 * @param string $reference
	 */
	public function importAction() {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			if ($this->request->hasArgument('reference')) {
				$reference = $this->request->getArgument('reference');
				$this->addFlashMessage($reference, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			}
			
			$this->view->assign('isAssigned', 'true');
			$this->view->assign('reference', $reference);
			$this->view->assign('rpc_server', $this->settings['rpc_server']);
			$this->view->assign('rpc_port', $this->settings['rpc_port']);
			$this->view->assign('rpc_user', $this->settings['rpc_user']);
			$this->view->assign('rpc_password', $this->settings['rpc_passwd']);
		}
		else {				
			$this->addFlashMessage('You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return mixed[]
	 */
	protected function getOptions(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$result = array();
		$bc = false;
	
		if (!empty($project->getReference())) {
			$bc = true;
		}
	
		# find ballots (later from bc)
		$ballots = $project->getBallots();
	
		foreach ($ballots AS $ballot) {
				
			if (!empty($ballot->getReference())) {
				$bc = true;
			}

			$options = $ballot->getOptions(bc);
			$assetref = $ballot->getAsset();

			if (!empty($assetref)) {
				$result[$ballot->getName()]['assetref'] = $ballot->getAsset();
				$result[$ballot->getName()]['options'] = $options;				
			}
			else {
// 				$result['error'] = 'Kein Asset generiert!';
			}

		}
		return $result;
	}
	
	/**
	 * @param array $array
	 * @return array
	 */
	private function generateCsvFromArray($array) {
		$result = false;
		if (file_exists($this->settings['downloadpath'])) {
			$filename = $this->settings['downloadpath'];
			try {
				if (!$fp = fopen($filename, 'w')) {
					return $result['error'] = 'Can\'t open file!';
				}
				fwrite($fp, 'Uid,Name,Parent,Ballot,Description,Color,Logo,Walletaddress,Balance,Percent,Base'."\n");
				$i = 0;
				if (is_array($array)) {
					foreach ($array as $record) {
						if (is_array($record)) {
							foreach($record AS $options) {
								if (is_array($options)) {
									foreach($options AS $option) {
										fwrite($fp, implode(",",$option)."\n");
										$result = true;
									}
								}
							}
							$i++;
						}
					}
				}
				if ($fp) fclose($fp);
				
			} catch (Exception $e) {
				$result['error'] = $e;
			}
			
		
		}
		else $result['error'] = 'No csv path!';

		return $result;
	}
	
	private function execute(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		$result = array();
		
		# Foreach ballot ...
		foreach ($project->getBallots() AS $ballot) {
			$address = $ballot->getWalletAddress();
		
			# Find transactions
			$txid = array();
			$result['transactions'] = Blockchain::getRpcResult($project)->listaddresstransactions($address);
			
			foreach ($result['transactions'] AS $transaction) {
				
				$secret = Blockchain::retrieveData($project, $transaction['txid']);
				$decrypted = MCrypt::decrypt($secret);
				$decrypted = explode("-", $decrypted);
				$fromaddress = $transaction['myaddresses'][0];
				$toaddress = $decrypted[1];
				$asset = $transaction['balance']['assets'][0]['assetref'];				
				$assetAmount = array($asset => 1);
			
				# if balance > 0
				if (Blockchain::getAssetBalanceFromAddress($project, $fromaddress, $asset) > 0) {
					if ($txid[] = Blockchain::getRpcResult($project)->sendwithmetadatafrom($fromaddress,$toaddress,$assetAmount,bin2hex($transaction['txid'])) ) {}	
				}
			}
			$result['txids'] = $txid;
		}
		return $result;
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