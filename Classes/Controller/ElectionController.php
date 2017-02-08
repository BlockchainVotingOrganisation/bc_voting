<?php
namespace Goettertz\BcVoting\Controller;
// error_reporting(E_ALL);
ini_set("display_errors", 1);

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 - 2017 Louis Göttertz <info2016@goettertz.de>, goettertz.de
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
 * Revision 132
 */

use \Goettertz\BcVoting\Service\Blockchain;
// use \Goettertz\BcVoting\Service\MCrypt;

/**
 * ElectionController
 */
class ElectionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * projectRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\ProjectRepository
	 * @inject
	 */
	protected $projectRepository = NULL;
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
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
		$isLoggedin = 'false';
		
		$amount = 0;
		// Benutzerdaten projektbezogen laden
		if (empty($project->getReference())) {
			$this->addFlashMessage('No Reference-ID.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign('feuser', $feuser);
			$username = $feuser->getUsername();
				
			$assignment = $feuser ? $project->getAssignmentForUser($feuser) : NULL;
			If($assignment != NULL) {
				$isAssigned = 'true';
				$this->view->assign('isAssigned', $isAssigned);
				$walletAddress = $assignment->getWalletAddress();
			}
		
		
// 			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
// 			If($assignment != NULL) {
// 				$isAdmin = 'true';
// 				$this->view->assign('isAdmin', $isAdmin);
// 			}
				
 			$rpcServer = $project->getRpcServer();
		
			if (is_string($rpcServer) && $rpcServer !== '') {
				try {
					if($bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo()) {
						$this->view->assign('bcResult', $bcArray);

// 						$this->addFlashMessage($bcArray['nodeaddress'].' 118', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					}
					else {
						$this->addFlashMessage('Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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
//					$this->addFlashMessage('Error 146: '.$e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
				if (!is_string($bcArray['nodeaddress'])) {
					$this->addFlashMessage('Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
			}
 			else {
				$this->addFlashMessage('Blockchain not configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
 			}
 		}

 		
		$this->view->assign('project', $project);
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('date_now', new \DateTime());
	}
	
}

?>