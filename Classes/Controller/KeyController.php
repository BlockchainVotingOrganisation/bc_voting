<?php
namespace Goettertz\BcVoting\Controller;

// ini_set("display_errors", 1);

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
 *  Revision 148
 *  
 *************************************************************************/

/**
 * KeyController
 */
class KeyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * GnuPG path
	 */
	protected $GnuPgHome = '';
	
	
	/**
	 * action show 
	 */
	public function showAction() {
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign ( 'user', $user );
		}
		# else die with message or redirect
		
		$result = shell_exec('gpg -h');
		$this->view->assign ( 'result', $result );
	}
	
	/**
	 * action list
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 *
	 * @return void
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project=NULL) {
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign ( 'user', $user );
		}
// 		$ballots = $this->ballotRepository->findByProject ( $project );
// 		$this->view->assign ( 'ballots', $ballots );
		$this->view->assign ( 'project', $project );
	}

	/**
	 * action new
	 * 
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('nix', null);
	}
	
	/**
	 * action create
	 * 
	 * @return void
	 */
	public function createAction() {
		$this->view->assign('nix', null);
	}
}
?>
