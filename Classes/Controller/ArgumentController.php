<?php
namespace Goettertz\BcVoting\Controller;

//error_reporting(E_ALL);
ini_set("display_errors", 1);

/************************************************************************
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
 *  
 *  Revision 57
 *  
 *************************************************************************/

/**
 * ArgumentController
 */
class ArgumentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * argumentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\ArgumentRepository
	 * @inject
	 */
	protected $argumentRepository = NULL;
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * optionRepository:
	 *  database table option abstract layer
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\OptionRepository
	 * @inject
	 */
	protected $optionRepository = NULL;
	
	public function __construct() {

	}
	
	/**
	 * action list
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 *
	 * @return void
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$arguments = $this->argumentRepository->findByProject($project);
		if ($user = $this->getCurrentFeUser()) {
			$this->view->assign('user', $user);
			$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
			If($assignment != NULL) {
				$this->view->assign('assigned', true);
				$this->view->assign('assignment', $assignment);
				if ($admin = $user ? $project->getAssignmentForUser($user,'admin') : NULL)
				{
					$assignment = $admin;
				}
			}
		}		
		$this->view->assign('project', $project);
		$this->view->assign('arguments', $arguments);
		
	}
	
	/**
	 * action new
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Argument $newArgument=null) {
		if ($user = $this->getCurrentFeUser()) {
			$this->view->assign('user', $user);
			$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
			If($assignment != NULL) {
				$this->view->assign('assigned', true);
				$this->view->assign('assignment', $assignment);
				if ($admin = $user ? $project->getAssignmentForUser($user,'admin') : NULL)
				{
					$assignment = $admin;
				}
			}
		}

		$this->view->assign('argument', $newArgument);
		$this->view->assign('project', $project);
	}
	
	/**
	 * creates an argument
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Argument $newArgument
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Argument $newArgument, \Goettertz\BcVoting\Domain\Model\Project $project)  {
		
		$newArgument->setProject($project);
		$this->argumentRepository->add($newArgument);
		
		$persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		$persistenceManager->persistAll();
		
		$this->addFlashMessage('The object was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->redirect('list', 'Argument', NULL, array(project=>$project, 'newArgument' => $newArgument));
	}
	
	/**
	 * edit
	 * @param \Goettertz\BcVoting\Domain\Model\Argument
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\Argument $argument) {
		$this->view->assign('argument', $argument);
	}
	
	/**
	 * update
	 * @param \Goettertz\BcVoting\Domain\Model\Argument
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Argument $argument) {
		$this->argumentRepository->update($argument);
		$this->view->assign('argument', $argument);
		$this->redirect('edit', 'Argument', NULL, array(project=>$project, 'argument' => $argument));
	}
	
	/**
	 * edit
	 * @param \Goettertz\BcVoting\Domain\Model\Argument
	 */
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\Argument $argument) {
		$this->redirect('list', 'Argument', NULL, array(project=>$project));
	}
	
	/**
	 *
	 * Gets the currently logged in frontend user.
	 * @return \Goettertz\BcVoting\Domain\Model\User  The currently logged in frontend
	 *                                              user, or NULL if no user is
	 *                                              logged in.
	 *
	 */
	Protected Function getCurrentFeUser() {
		Return intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0
		? $this->userRepository->findByUid( intval($GLOBALS['TSFE']->fe_user->user['uid']) )
		: NULL;
	}
}
?>