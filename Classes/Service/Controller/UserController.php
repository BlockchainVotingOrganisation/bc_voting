<?php
namespace Goettertz\BcVoting\Controller;
ini_set("display_errors", 1);
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Louis G�ttertz <info2015@goettertz.de>, goettertz.de
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
 * Rev. 60
 */

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
	 * action list
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		if ($project !== null)
		{
			$this->view->assign('project', $project);
			$this->view->assign('members', $project->getAssignments());
		}
		else 
			$this->view->assign('members', $this->userRepository->findAll());
	}
	
	/**
	 * action edit
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\User $user) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
		
			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('user', $user);
			}
		}
		
	}
	
	/**
	 * action update
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Project $user) {
		$this->userRepository->update($user);
		$this->addFlashMessage('The object was updated', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->redirect('edit');
	}
	
	/**
	 * action showRegistration
	 * @param \Goettertz\BcVoting\Domain\Model\User $newUser
	 * @return void
	 */
	public function actionShowRegistration(Goettertz\BcVoting\Domain\Model\User $newUser) {
		$this->view->assign('user', $newUser);
	}
}
?>
