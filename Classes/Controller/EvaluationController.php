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
 * Revision 133
 */

use \Goettertz\BcVoting\Service\Blockchain;
// use \Goettertz\BcVoting\Service\MCrypt;

/**
 * EvaluationController
 */
class EvaluationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
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
		
		$result = array();
		
		# Check if rpc-settings are configured
		$rpc = $project->checkRpc($project, $this->settings);
		if (is_string($rpc)) { // Fehlermeldung wurde ausgegeben
			$this->addFlashMessage($rpc, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 				$this->redirect('show',NULL,NULL, array('project' => $project));
		}
		else if (is_object($rpc)){
			$project = $rpc; // Object 'Project' mit RPC-Eigenschaften wurde ausgegeben
		}
		else { // Irgendetwas anderes wurde ausgegeben.
			$this->addFlashMessage('Unkown error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
// 				$this->redirect('show',NULL,NULL, array('project' => $project));
		}
					
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
			}
			catch (\Exception $e) {
//				$this->addFlashMessage('Error 146: '.$e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			if (!is_string($bcArray['nodeaddress'])) {
				$this->addFlashMessage('Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		}
 		else {
			$this->addFlashMessage('Blockchain not configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
 		}
 		
 		if (empty($project->getReference())) {
 			$this->addFlashMessage('No Reference-ID.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
 		}
 		
 		$result['Project']['Database']['TxId'] = $project->getReference();
 		$result['Project']['Database']['Name'] = $project->getName();
 		$result['Project']['Database']['Ballots'] = $project->getBallots();
		
 		$data = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getwallettransaction($project->getReference(),true);
 		$result['Project']['Blockchain']['Metadata'] = $data[data][0]; 
 		$result['Project']['Blockchain']['Json'] = Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), trim($project->getReference()));
 		if (isset($result['Project']['Blockchain']['Json']['error'])) {
 			# On Error
 			$this->addFlashMessage($bcdata['error'].' (776)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
 		}
 		else { 
 			# Cast json to stdClass
 			$result['Project']['Blockchain']['ProjectObject'] = json_decode($result['Project']['Blockchain']['Json']);
 		}
 		
//  		$result['Project']['Blockchain']['Ballot Information'] = 
 		
 		$ballots = $result['Project']['Blockchain']['ProjectObject']->ballots;
 		
 		$i = 0;
 		foreach ($ballots AS $ballot) {
 			if (is_a($ballot, 'stdClass', true)) {
 				$ballot = (array) $ballot;
 				
 			}
 			$result['Project']['Blockchain']['Ballot Information'][$i]['Json'] =
 			Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), trim($ballot));
//  			Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getwallettransaction($ballot,true);
//  			$ballot;
 			
 			$ballotO = json_decode($result['Project']['Blockchain']['Ballot Information'][$i]['Json']);
 			
 			$result['Project']['Blockchain']['Ballot Information'][$i]['Address'] = $ballotO->walletaddress;
 			if ($obj = Blockchain::checkWalletAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $ballotO->walletaddress, true))
 			{
 				$result['Project']['Blockchain']['Ballot Information'][$i]['TxIds'] =
 				$data = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listaddresstransactions($ballotO->walletaddress, 100);
 			}
 			$result['Project']['Blockchain']['Ballot Information'][$i]['Option Results'] = 'Noch nich fertich.';
 			$i++;
 		}
 		
		$this->view->assign('project', $project);
		$this->view->assign('result', $result);
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('date_now', new \DateTime());
	}
	

}
?>