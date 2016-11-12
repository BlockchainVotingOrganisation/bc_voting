<?php
namespace Goettertz\BcVoting\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015-2016 Louis G�ttertz <info2015@goettertz.de>, goettertz.de
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
 * Revision 23
 */

/**
 * BlockchainController
 */
class BlockchainController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
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
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
// 		$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
		$rpcServer = $project->getRpcServer();
		if (is_string($rpcServer) && $rpcServer !== '') {
			try {
				if(is_array(Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo())) {
					$bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo();
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

		$user       = $this->getCurrentFeUser();
		$isAssigned = false;
		$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
		If($assignment != NULL) {
			$isAssigned = true;
			$role = $assignment->getRole($assignment);
			$roleName = $role->getName($role);
		}		
		$this->view->assign('project', $project);
		$this->view->assign('assigned', $isAssigned);
		$this->view->assign('user', $user);
	}
	
// 	/**
// 	 *
// 	 * Gets the currently logged in frontend user.
// 	 * @return \Goettertz\BcVoting\Domain\Model\User  The currently logged in frontend
// 	 *                                              user, or NULL if no user is
// 	 *                                              logged in.
// 	 *
// 	 */
// 	Protected Function getCurrentFeUser() {
// 		Return intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0
// 		? $this->userRepository->findByUid( intval($GLOBALS['TSFE']->fe_user->user['uid']) )
// 		: NULL;
// 	}
}
	
?>