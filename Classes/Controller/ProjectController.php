<?php
namespace Goettertz\BcVoting\Controller;
//error_reporting(E_ALL);
// ini_set("display_errors", 1);

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
 * Revision 148
 */

use \Goettertz\BcVoting\Property\TypeConverter\UploadedFileReferenceConverter;
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
	 * initialize the controller
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();
		//fallback to current pid if no storagePid is defined
		if (version_compare(TYPO3_version, '6.0.0', '>=')) {
			$configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		} else {
			$configuration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		}
		if (empty($configuration['persistence']['storagePid'])) {
			if (empty($this->settings['storagePid'])) {
				$currentPid['persistence']['storagePid'] = $GLOBALS["TSFE"]->id;
			}
			else {
				$currentPid['persistence']['storagePid'] = $this->settings['storagePid'];
			}
			
			$this->configurationManager->setConfiguration(array_merge($configuration, $currentPid));
		}
	}
	
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
					$this->addFlashMessage('No Blockchain configured!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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
	 * Set TypeConverter option for image upload
	 */
	public function initializeCreateAction() {
		$this->setTypeConverterConfigurationForImageUpload('newProject');
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
			$this->initializeAction();
			
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
				
// 				$categories = $this->categoryRepository->findAll();
				
// 				$this->view->assign('categories', $categories);
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
				$this->view->assign('priceVtc', $this->settings['payment_voting']);
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
// 				$categories = $this->categoryRepository->findAll();
				if (empty($project->getReference())) {
					$this->view->assignMultiple(array('project' => $project, 'isAssigned' => $isAssigned, 'isAdmin' => $isAdmin, 'feuser' => $user));
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
// 							$this->view->assign('blockchain', $bcArray);
							if (empty($project->getWalletAddress())) $this->setWalletAddress($project);
							
							# Get balance for wallet address
							$balance = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($project->getWalletAddress());
							$this->view->assign('balance', $balance[0]['qty']);
						}
					}
					catch (\Exception $e) {
						
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
	 * Set TypeConverter option for image upload
	 */
	public function initializeUpdateAction() {
		$this->setTypeConverterConfigurationForImageUpload('project');
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
			$this->addFlashMessage('The project was sealed.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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
		$this->redirect('show','Project','BcVoting',array('project'=>$project));
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
		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\'tx_bcvoting_domain_model_project\' AND fieldname=\'logo\' AND uid_foreign = '.$project->getUid().' AND deleted = 0';
		$db = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$this->redirect('edit','Project','BcVoting',array('project'=>$project));
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
// 		$bc = new \Goettertz\BcVoting\Service\Blockchain();
		$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getnewaddress();
		$assignment->setWalletAddress($newAddress);
		$this->assignmentRepository->update($assignment);
		
		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManager->persistAll();
		
		$this->addFlashMessage('New Address: '.$newAddress.'.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		# Für jeden Stimmzettel Assets senden
		foreach ($project->getBallots() as $ballot) {
			if ($bcArray = $bc::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendassettoaddress($newAddress,$ballot->getAsset(),$ballot->getVotes())) {
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
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function sealAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		# Get the user assignment and throw an exception if the current user is not a
		# admin of the selected project.
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$isAssigned = 'false';
			$assignment = $feuser ? $project->getAssignmentForUser($feuser,'admin') : NULL;
			If($assignment === NULL) {
				$this->addFlashMessage('No admin: '.$feuser.'!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('list',NULL,NULL, array('project' => $project));				
			}
			else {
				$isAssigned = 'true';
				$isAdmin = 'true';
			}
		}
		else {
			If($assignment === NULL) {
				$this->addFlashMessage('You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('list',NULL,NULL, array('project' => $project));
			}			
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
			$this->setWalletAddress($project);
		}
		
		# Check if already sealed
		if (!$project->getReference() === '') {
			$this->addFlashMessage('The Project is already sealed. You cannot modify a sealed Project.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);				
		}

		# The data for sealing ...
		$json = $project->getJson(); 
		if ($json === NULL) {
			$this->addFlashMessage('Fehler JSON! ', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');
		} elseif (isset($json['error'])) {
			$this->addFlashMessage($json['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list');			
		}
		
		# Check json length
		
		if (strlen($json) > 80 ) {
			$this->addFlashMessage('Sorry, the string istoo long!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit','Project',NULL,array('project' => $project)); # brutal.
		}
		
		$hash = hash('sha256', $json);
		
		# Saving data in the blockchain ...			
		$vtc_amount = $this->settings['payment_sealing'];
		if ($vtc_amount < 0.00000001) $vtc_amount = 0.00000001;
			
		# check if balance is enough -> soll in model->project
		if ($balance = $project->getBalance($project)) {
			if (doubleval($balance) < doubleval($vtc_amount)) {
				$this->addFlashMessage('Not enough inputs: '.$project->getWalletAddress().' '.doubleval($balance), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show','Wallet',NULL,array('project' => $project));
			}
		}
		else {
			$this->addFlashMessage('Error: no balance: '.$project->getWalletAddress().' '.doubleval($balance[0]['qty']), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show','Wallet',NULL,array('project' => $project));
		}
		
			
// 		$this->addFlashMessage('From: '.$project->getWalletAddress().' Amount: '.doubleval($vtc_amount).' to '.$paymentAddress, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		if ($ref = Blockchain::storeData($project->getRpcServer(),$project->getRpcPort(),$project->getRpcUser(),$project->getRpcPassword(), trim($project->getWalletAddress()), $project->getWalletAddress(), doubleval($vtc_amount), $json)  ) {				
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
			elseif (is_string($ref['error']))  {
				$this->addFlashMessage('ERROR:  '.$ref['error'].' </br><b>Please check the node\'s debug.log.</b>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}

			// ein unbekannter Fehler. Versucht ein Rückgabewerte-Array -wenn vorhanden- auszulesen
			else  $this->addFlashMessage('ERROR:  '.implode('-', $ref), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
//  			$this->addFlashMessage('Success:  '.$project->getName().' sealed', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
		# create stream
		$streamName = substr($project->getReference(), 0, 12);
		if (!is_array($stream = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->create('stream', $streamName, true))) {
			$this->addFlashMessage('Stream '.$project->getName().': '.$streamname.' created: '.$stream, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
			$project->setStream($streamName);
			$this->projectRepository->update($project);
		}
		else {
			$this->addFlashMessage('Stream '.$streamName.' not created! '.implode($stream), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		# subscribe stream
		if ($success = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->subscribe($streamName, false)) {
			$this->addFlashMessage('Stream '.$streamName.' not subscribed! '.$success, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		
		
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('isAssigned', $isAssigned);
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
				if ($this->request->hasArgument('rpcServer')) {
					$rpcServer = trim($this->request->getArgument('rpcServer'));
				}
				
				$rpcPort = trim($this->settings['rpc_port']);
				if ($this->request->hasArgument('rpcPort')) {
					$rpcPort = trim($this->request->getArgument('rpcPort'));
				}
				$rpcUser = trim($this->settings['rpc_user']);
				if ($this->request->hasArgument('rpcUser')) {
					$rpcUser = trim($this->request->getArgument('rpcUser'));
				}
				$rpcPassword = trim($this->settings['rpc_passwd']);
				if ($this->request->hasArgument('rpcPassword')) {
					$rpcPassword = trim($this->request->getArgument('rpcPassword'));
				}
			
				# End of checks!!! #####################################################
				# get data from blockchain
				$bc = new \Goettertz\BcVoting\Service\Blockchain();
				$data = $bc::retrieveData($rpcServer, $rpcPort, $rpcUser, $rpcPassword, trim($reference));
				if (isset($data['error'])) {
					$this->addFlashMessage($data['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('list');
				}
				
				# Cast json to stdClass
				else $data = json_decode($data);
				
				# Cast stdClass to array
				if (is_a($data, 'stdClass', false)) {
					$data = (array) $data;
					# make array additions rpc
					$data['reference'] = $reference;
					$data['rpcServer'] = $rpcServer;
					$data['rpcPort'] = $rpcPort;
					$data['rpcUser'] = $rpcUser;
					$data['rpcPassword'] = $rpcPassword;
				}

				# Import array project data into DB ... 
				$newproject = new \Goettertz\BcVoting\Domain\Model\Project();
				$newproject->importArray($data); 
				
				// check result?
				{
					$this->projectRepository->add($newproject);
				}
				
				# Assign admin
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
					$newBallot->import($ballot, $newproject);
					$this->ballotRepository->add($newBallot);
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
 				$result['error'] = 'Kein Asset generiert!';
			}

		}
		return $result;
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	private function setWalletAddress(\Goettertz\BcVoting\Domain\Model\Project $project) {
		try {
			$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getnewaddress();
			$project->setWalletAddress($newAddress);
			$this->projectRepository->update($project);
			$this->addFlashMessage('No wallet address. Got new one. '.$newAddress, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		} catch (Exception $e) {
			$this->addFlashMessage('Error: '.$e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		}
			// 			$this->redirect('edit',NULL,NULL, array('project' => $project));
				
	}
	
	protected function setTypeConverterConfigurationForImageUpload($argumentName) {
		$uploadConfiguration = array(
				UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/tx_bc_voting/',
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_CONFLICT_MODE => '1'
		);
		/** @var PropertyMappingConfiguration $newExampleConfiguration */
		$newExampleConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
		$newExampleConfiguration->forProperty('logo')
		->setTypeConverterOptions(
				'Goettertz\\BcVoting\\Property\\TypeConverter\\UploadedFileReferenceConverter',
				$uploadConfiguration
				);
	}
	


}
?>