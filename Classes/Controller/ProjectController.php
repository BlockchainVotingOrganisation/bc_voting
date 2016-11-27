<?php
namespace Goettertz\BcVoting\Controller;

//ini_set("display_errors", 1);

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
 * Revision 128
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
					if($bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo()) {
						$this->view->assign('bcResult', $bcArray);
					}
					
					if ($assets = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getmultibalances($walletAddress)) {
						$ballots = $project->getBallots();
						if (count($ballots) > 0) {
							foreach ($ballots AS $ballot) {
							
								if ($assetref = $ballot->getAsset()) {
									if (is_array($assets))
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
	 * creates new project from formdata
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

			if ($this->addAssignment($newProject, $user, 'admin')) {
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
						$bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo();
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
	 * adds a new assignment
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project;
	 * @param \Goettertz\BcVoting\Domain\Model\User $user;
	 * @param string $role
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Assignment
	 */
	protected function addAssignment(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\User $user, $role) {
		
		$roles = $this->roleRepository->findByName($role);
		if (count($roles) === 0) {
			$newRole = new \Goettertz\BcVoting\Domain\Model\Role();
			$newRole->setName($role);
			$this->roleRepository->add($newRole);
			$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
			$persistenceManager->persistAll();
		}
		else {
			$newRole = $roles[0];
		}
		
// 		die('Project: '.$project->getName().', '.'User: '.$user->getUsername().', Role: '.$newRole->getName());
		
		# Check issue #19 duplicate entries
// 		$duplicates = $this->assignmentRepository->findDuplicates($project->getUid(),$user->getUid(),$newRole->getUid());
// 		$this->assignmentRepository->deleteDuplicates($project->getUid(),$user->getUid(),$newRole->getUid());
		
		$myassignment = New \Goettertz\BcVoting\Domain\Model\Assignment();
		
		$myassignment->setProject($project);
		$myassignment->setUser($user);
		$myassignment->setRole($newRole);
		
		
// 		die ('Project: '.$project->getName().', '.'User: '.$user->getUsername().', Role: '.$newRole->getName().', Assignment User: '.$myassignment->getUser()->getUsername());
		$this->assignmentRepository->add($myassignment);

// 		else return NULL;
		
// 		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
// 		$persistenceManager->persistAll();
		
 		
	}
	
	/**
	 * action assign
	 * 
	 * assigns an user to a project as a member/voter
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function assignAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		# Widerspruch zu Funktionsargumenten!!!

		$user = $this->userRepository->getCurrentFeUser();
		
		if ($user === NULL) {
			$this->view->assign('project', $project);
			$this->view->assign('login', $this->settings['login']);
			$this->view->assign('register', $this->settings['registration']);
		}
		else {
			//Prüfen, ob bereits Mitglied
			if (!$assignment = $user ? $project->getAssignmentForUser($user) : NULL) {
				
				$this->addFlashMessage('Project: '.$project->getName(), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				$this->addFlashMessage('User   : '.$user->getUsername(), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				$this->addFlashMessage('Role   : Member', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				# Mitglied als Member registrieren
				
			$this->addAssignment($project, $user, 'Member');
			$this->addFlashMessage('New Assignment created! Now you have to import your credentials, given from election office.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				
			
			}
			$this->redirect('show','Project',NULL,array('project' => $project));
		}
	}
	
	/**
	 * allocate assets
	 * zunächst unnütz! alte methode, um automatisch assets an neue Mitglieder zu versenden.
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignment
	 */
	protected function allocateAssets(\Goettertz\BcVoting\Domain\Model\Assignment $assignment) {
		
		$project = $assignment->getProject();
		$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getnewaddress();
		$assignment->setWalletAddress($newAddress);
		$this->assignmentRepository->update($assignment);
		
		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManager->persistAll();
		
		$this->addFlashMessage('New Address: '.$newAddress.'.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		# Für jeden Stimmzettel Assets senden
		foreach ($project->getBallots() as $ballot) {
			if ($bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendassettoaddress($newAddress,$ballot->getAsset(),$ballot->getVotes())) {
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
	 * checkVoting
	 * 
	 * Should be moved to voting controller
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
			if (!$result['secretBC']  = Blockchain::retrieveData($project->getRpcServer(),$project->getRpcPort(),$project->getRpcUser(), $project->getRpcPassword(), $txid)) {
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
	 * @return array $data
	 */
	protected function getOptionListResults($data) {		
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
				$this->addFlashMessage($result['error']. ' (716)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show',NULL,NULL, array('project' => $project));
			}
			
			$result['url_csv'] = $this->settings['downloadurl'];
			
			$csv = $this->generateCsvFromArray($result);
			
			if (is_string($csv['error'])) {
				$result['error'] = $csv['error'];
				$this->addFlashMessage($result['error'] . ' (724)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			
			$data = array();
			
			foreach($project->getBallots() AS $ballot) {
				$mergeArray = ($result[$ballot->getName()]['options']);
				$data = array_merge($data, (array) $mergeArray);
			}
// 			$result['data'] = $mergeArray[0];
 			$result['lists'] = $this->getOptionListResults($data);
		}
		else 
		{
			$result['error'] = 'Can\'t get project options from blockchain! (716)';
			$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show',NULL,NULL, array('project' => $project));
		}

		# Begin old methods from db - should be replaced by bc		
		if($bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo()) {
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
			
			$secretBC = Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $vote->getTxid());

			if (isset($secretBC['error'])) {
				$this->addFlashMessage(implode($secretBC).' : "'.$vote->getTxid().'"', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}

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
// 				$this->addFlashMessage(implode($secretBC).' : '.$secretDB.' : '.$vote->getTxid(), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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

		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$username = $feuser->getUsername();
					
			if ($blockchain) {
				$$assetHolders = $blockchain->getUserBalance($username, $project);
			}
					
			$assignment = $feuser ? $project->getAssignmentForUser($feuser) : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$role = $assignment->getRole($assignment);
				$roleName = $role->getName($role);
				
			}
					
			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				$isAdmin = 'true';
			}
		}
		$this->view->assign('feuser', $feuser);
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
		
		# Checks
		// VTC payment address
		$paymentAddress = $this->settings['payment_vtc_address'];
		if (empty($paymentAddress)) {
			$this->addFlashMessage('No payment address! '.$this->settings['payment_vtc_address'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit',NULL,NULL, array('project' => $project));			
		}
		
		# Check if rpc-settings are configured
		$rpc = $project->checkRpc($project,$this->settings);
		if (is_string($rpc)) {
			$this->addFlashMessage($rpc, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit',NULL,NULL, array('project' => $project));		
		}
		else if (is_object($rpc)){
			$project = $rpc;
		}
		else {
			$this->addFlashMessage('Unkown error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		# wallet address
		$walletAddress = $project->getWalletAddress();
		if (empty($walletAddress)) {
			$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getnewaddress();
			$project->setWalletAddress($newAddress);
			$this->projectRepository->update($project);
			$this->addFlashMessage('No wallet address. Got new one. '.$newAddress, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			// 			$this->redirect('edit',NULL,NULL, array('project' => $project));
		}
		
		# Check if already sealed
		if (!$project->getReference() === '') {
			$this->addFlashMessage('Project already sealed.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				
		}

		# The data for sealing ...
		$json = $project->getJson(); 
		if ($json === NULL) {
			$this->addFlashMessage('Fehler JSON! ', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		}
		
		$hash = hash('sha256', $json);
		
		# Saving data in the blockchain ...			
		$vtc_amount = $this->settings['payment_sealing'];
// 		if ($vtc_amount < 0.00000001) $vtc_amount = 0.00000001;
			
		# check if balance is enough
		$balance = Blockchain::getRpcResult($project->getRpcServer(),$project->getRpcPort(),$project->getRpcUser(),$project->getRpcPassword())->getaddressbalances($project->getWalletAddress());
		if (is_double($balance[0]['qty'])) {
			$balance = $balance[0]['qty'];
		}
		else {
			$this->addFlashMessage('Insufficient funds in '.$project->getWalletAddress().' '.doubleval($balance[0]['qty']), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit',NULL,NULL,array('project' => $project));
		}
		
		if (doubleval($balance) < doubleval($vtc_amount)) {
			$this->addFlashMessage('Not enough inputs! '.$project->getWalletAddress().' '.doubleval($balance), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit',NULL,NULL,array('project' => $project));
		}
			
		$this->addFlashMessage('From: '.$project->getWalletAddress().' Amount: '.doubleval($vtc_amount).' to '.$paymentAddress, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		if ($ref = Blockchain::storeData($project->getRpcServer(),$project->getRpcPort(),$project->getRpcUser(),$project->getRpcPassword(), trim($project->getWalletAddress()), trim($paymentAddress), doubleval($vtc_amount), $json)  ) {				
			// wenn ein array zurückgegeben wird ist irgendetwas schiefgelaufen ...
			if (!is_array($ref)) {					
				// wenn erolgreich wird ein string mit txid zurückgegeben
				if (is_string($ref)) {
					$project->setReference($ref);
					$this->projectRepository->update($project);
						$this->addFlashMessage('The project was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			
					$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
					$persistenceManager->persistAll();
					$this->addFlashMessage('The project was sealed and project data had been stored in a transaction to propagade to the block chain. Please be patient until confirmation has happened. Returned transaction ID: '.$ref, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
			}
			// wenn string "$ref['error']" existiert, ist ein Fehler in Blockchain:: passiert.
			elseif (is_string($ref['error']))  $this->addFlashMessage('ERROR:  '.$ref['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);

			// ein unbekannter Fehler. Versucht ein Rückgabewerte-Array -wenn vorhanden- auszulesen
			else  $this->addFlashMessage('ERROR:  '.implode('-', $ref), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
//  			$this->addFlashMessage('Success:  '.$project->getName().' sealed', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		
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
				
				# Check connection settings -> should be migrated to method setRpcConnection and checkRpcConnection
				$rpcServer = trim($this->settings['rpc_server']);
				if (!$this->request->hasArgument('rpcServer')) {
					$rpcServer = trim($this->request->getArgument('rpcServer'));
				}
				
				$rpcPort = trim($this->settings['rpc_port']);
				if (!$this->request->hasArgument('rpcPort')) {
					$rpcPort = trim($this->request->getArgument('rpcPort'));
				}
				$rpcUser = trim($this->settings['rpc_user']);
				if (!$this->request->hasArgument('rpcUser')) {
					$rpcUser = trim($this->request->getArgument('rpcUser'));
				}
				$rpcPassword = trim($this->settings['rpc_passwd']);
				if (!$this->request->hasArgument('rpcPassword')) {
					$rpcPassword = trim($this->request->getArgument('rpcPassword'));
				}
				
				# Try to get data from blockchain
				$data = Blockchain::retrieveData($rpcServer, $rpcPort, $rpcUser, $rpcPassword, trim($reference));
				if (isset($data['error'])) {
 					$this->addFlashMessage($data['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
				
				# Cast json to stdClass
				else $data = json_decode($data); 
				
				# Cast stdClass to array
				if (is_a($data, 'stdClass', false)) $data = (array) $data;
				
				# Check existing projects
				$projects = $this->projectRepository->findByReference($reference);
				if (count($projects) > 0) {
					$this->addFlashMessage('Error: Project with same reference already exists on this server:<br /> <b>&quot;'.$projects[0]->getName().'&quot;</b>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 					$this->redirect('list');					
				}
				$projects = $this->projectRepository->findByName($data['name']);
				if (count($projects) > 0) {
					$this->addFlashMessage('Error: Project with same name already exists on this server. Please rename or delete the old Project.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 					$this->redirect('list');
				}
				
				# Import array project data into DB ... 
				// should be migrated to project->setProject
				$newproject = new \Goettertz\BcVoting\Domain\Model\Project();
				$newproject->setName($data['name']);
				$newproject->setDescription($data['description']);
				//logo -> importLogo($uri)
				$newproject->setStart($data['start']);
				$newproject->setEnd($data['end']);
				$newproject->setWalletAddress($data['walletaddress']);
				$newproject->setCategory($data['category']);
				$newproject->setInfosite($data['infosite']);
				$newproject->setForumurl($data['forumUrl']);
				$newproject->setReference($reference);
				$newproject->setRpcServer($rpcServer);
				$newproject->setRpcPort($rpcPort);
				$newproject->setRpcUser($rpcUser);
				$newproject->setRpcPassword($rpcPassword);
				
				$this->projectRepository->add($newproject);
				
				$roles = $this->roleRepository->findByName('admin');
				
				if (count($roles) == 0) {
					$newRole = new \Goettertz\BcVoting\Domain\Model\Role();
					$newRole->setName('admin');
					$this->roleRepository->add($newRole);
					$roles[0] = $newRole;
				}
	
				if ($this->addAssignment($newproject, $feuser, $roles[0])) {
					$this->addFlashMessage('The project\'s "'.$newproject->getName().'" assignment "'.$newproject->getName().'" was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
				
				$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
				$persistenceManager->persistAll();
				
				# Import ballots with options (for each)
				foreach ($data['ballots'] AS $ballot) {
					$this->addFlashMessage($ballot, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$newBallot = new \Goettertz\BcVoting\Domain\Model\Ballot();
					
					# Cast json to stdClass and array
					$ballot = json_decode($ballot);
					$ballot = (array) $ballot;
	
					$newBallot->setProject($newproject);
					
					$newBallot->setReference($ballot['reference']);
					$newBallot->setName($ballot['name']);
					$newBallot->setStart($ballot['start']);
					$newBallot->setEnd($ballot['end']);
					$newBallot->setText($ballot['text']);
					$newBallot->setFooter($ballot['footer']);
					$newBallot->setAsset($ballot['asset']);
					$newBallot->setReference($ballot['reference']);
					$newBallot->setWalletAddress($ballot['walletaddress']);
					
					$this->ballotRepository->add($newBallot);
					$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
					$persistenceManager->persistAll();

// 					# Import options
					foreach ($ballot['options'] AS $option) {
						$newOption = new \Goettertz\BcVoting\Domain\Model\Option();
							
// 						# Cast json to stdClass and array
						$option = json_decode($option);
						$option = (array) $option;
						
						# Set option
						$newOption->setBallot($newBallot);
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
				
				$this->view->assign('project',$newproject);
			}
			
			$this->view->assign('data', $data);
			$this->view->assign('isAssigned', 'true');
			$this->view->assign('reference', $reference);
			$this->view->assign('rpc_server', $rpcServer);
			$this->view->assign('rpc_port', $rpcPort);
			$this->view->assign('rpc_user', $rpcUser);
			$this->view->assign('rpc_password', $rpcPassword);
		}
		else {				
			$this->addFlashMessage('You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return array
	 */
	public function getOptions(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
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

			$options = $ballot->getOptions(true);
			
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
			
			# Temp address for encrypted votings
			$address = $ballot->getWalletAddress();
		
			# Find transactions
			$txid = array();
			$result['transactions'] = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listaddresstransactions($address, 1000000);
			
			foreach ($result['transactions'] AS $transaction) {
				
				$decrypted = MCrypt::decrypt(hex2bin($transaction['data'][0]));
				$decrypted = explode("-", $decrypted);
				$fromaddress = $transaction['myaddresses'][0];
				$toaddress = $decrypted[1];
				$asset = $transaction['balance']['assets'][0]['assetref'];				
				$assetAmount = array($asset => 1);
			
				# if balance > 0
				if (Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $fromaddress, $asset) > 0) {
					$txid[] = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendwithmetadatafrom($fromaddress,$toaddress,$assetAmount,$transaction['txid']);
				}
			}
			$result['txids'] = $txid;
		}
		return $result;
	}
	

	
	/**
	 * @param array $settings
	 * @param array $request
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	private function setRpcConnection($settings = NULL, $request = NULL, \Goettertz\BcVoting\Domain\Model\Project $project = NULL) {
		
		$rpcServer = trim($this->settings['rpc_server']);
		if (!$this->request->hasArgument('rpcServer')) {
			$rpcServer = trim($this->request->getArgument('rpcServer'));
		}
		
		$rpcPort = trim($this->settings['rpc_port']);
		if (!$this->request->hasArgument('rpcPort')) {
			$rpcPort = trim($this->request->getArgument('rpcPort'));
		}
		$rpcUser = trim($this->settings['rpc_user']);
		if (!$this->request->hasArgument('rpcUser')) {
			$rpcUser = trim($this->request->getArgument('rpcUser'));
		}
		$rpcPassword = trim($this->settings['rpc_passwd']);
		if (!$this->request->hasArgument('rpcPassword')) {
			$rpcPassword = trim($this->request->getArgument('rpcPassword'));
		}
	}
}
?>