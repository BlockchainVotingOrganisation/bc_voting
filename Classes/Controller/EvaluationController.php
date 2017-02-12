<?php
namespace Goettertz\BcVoting\Controller;
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

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
 * Revision 135
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
		
		if (empty($project->getReference())) {
			$this->addFlashMessage('No Reference-ID.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}


#		#Else ################### Check if rpc-settings are configured -> soll in eingene Funktion! return result[rpc] und result[bcinfo]
		
		$rpc = $project->checkRpc($project, $this->settings);
		if (is_string($rpc)) { // Fehlermeldung wurde ausgegeben
			$this->addFlashMessage($rpc, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		else if (is_object($rpc)){
			$project = $rpc; // Object 'Project' mit RPC-Eigenschaften wurde ausgegeben
		}
		else { // Irgendetwas anderes wurde ausgegeben.
			$this->addFlashMessage('Unkown error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		if (is_string($project->getRpcServer()) && $project->getRpcServer() !== '') {
			try {
				if($bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getinfo()) {
					$this->view->assign('bcResult', $bcArray);
				}
				else {
					$this->addFlashMessage('Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}						
			}
			catch (\Exception $e) {
				$this->addFlashMessage('Error 131: '.$e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			
			if (!is_string($bcArray['nodeaddress'])) {
				$this->addFlashMessage('Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		}
 		else {
			$this->addFlashMessage('Blockchain not configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
 		}
# 		#################################################
 		
 		$result['Database']['TxId'] = $project->getReference();
 		$result['Database']['Name'] = $project->getName();
 		$result['Database']['ballots'] = $project->getBallots();
 		$result = array_merge($result, $this->bcInfo($project));
 		
		$this->view->assign('project', $project);
		$this->view->assign('result', $result);
		$this->view->assign('isAdmin', $isAdmin);
		$this->view->assign('isAssigned', $isAssigned);
		$this->view->assign('date_now', new \DateTime());
	}
	
	/**
	 * proceedAction
	 * 
	 * Darf nur einmal pro Stimmzettel ausgeführt werden!
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param string $address - ballot walletAddress
	 * @param string $asset - asset reference
	 * @return void
	 */
	public function proceedAction(\Goettertz\BcVoting\Domain\Model\Project $project, $address, $asset) {
		
		$mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
		# get transactions
		$result['txIds'] = $this->getTxidsAddress($project, $address);
		$i = 0;
		foreach ($result['txIds'] AS $transaction) { // muss sortiert werden absteigend nach Zeit
			 # Wenn kein Eintrag in Voting (Streams)
				if (!empty($transaction['balance']['assets'] && !empty($transaction['data']) && $transaction['confirmations'] > 1)) {
					if (!empty($optionaddress = Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $transaction['txid']))) {
						$secret = $optionaddress;
						$optionaddress = $mcrypt->decrypt($optionaddress);
						$optionaddress = explode("-", $optionaddress);
						$optionaddress = $optionaddress[1];
							
						if ($balance = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getmultibalances($address, $asset, 1, false))
							if (!empty($optionaddress)&& $transaction['balance']['assets'][0]['qty'] > 0) {
								if ($asset === $transaction['balance']['assets'][0]['assetref']) {
									if ($tx = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendassetfrom($address, $optionaddress, $asset, 1)) {
										if (!is_array($tx)) {
											
											# Eintrag in Voting-Stream
											$item = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->publish($project->getReference(),'decrypted',bin2hex($tx));
											
											# Eintrag in Flash-Log
											$this->addFlashMessage($tx.' => '.$secret, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
										}
										else {
											$this->addFlashMessage(implode($tx). ' '.$secret, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
											break;
										}
									}
								}
							}
					}
				}				


			$i++;
		} # end for transactions
// 		$this->view->assign('result', $result);
		$this->redirect('show', NULL, NULL, array('project' => $project));
	}
	
	/**
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return array $result
	 */
	private function bcInfo(\Goettertz\BcVoting\Domain\Model\Project $project) {
		# Blockchain Result - in eigene Funktion!
		
		$metadata = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getwallettransaction($project->getReference(),true);
		$result['blockchain']['Metadata'] = $metadata[data][0];
			
		$result['blockchain']['json'] = Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), trim($project->getReference()));
			
		if (isset($result['blockchain']['json']['error'])) {
			# On Error
			$this->addFlashMessage($result['blockchain']['json']['error'] . ' (168)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		else {
			# Cast json to stdClass
			$result['blockchain']['object'] = json_decode($result['blockchain']['json']);
		}
			
		$ballots = $result['blockchain']['object']->ballots;
			
		$i = 0;
		foreach ($ballots AS $ballot) {
			$result = $this->bcBallot($project, $ballot, $i, $result);
			$i++;
		}
		return $result;
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param unknown $ballot
	 * @param int $i
	 * @return array $result
	 */
	private function bcBallot(\Goettertz\BcVoting\Domain\Model\Project $project, $ballot, $i, $result) {
		
		if (is_a($ballot, 'stdClass', true)) {
			$ballot = (array) $ballot;
		}
		$result['blockchain']['ballots'][$i]['json'] =
		Blockchain::retrieveData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), trim($ballot));
		
		$ballotO = json_decode($result['blockchain']['ballots'][$i]['json']);
		if (is_array($result['txIds']))
			$result['txIds'] = array_merge($result['txIds'], $this->getTxidsAddress($project, $ballotO->walletaddress));
		else $result['txIds'] = $this->getTxidsAddress($project, $ballotO->walletaddress);

		$result['blockchain']['ballots'][$i]['asset'] = $ballotO->asset;
		$result['blockchain']['ballots'][$i]['address'] = $ballotO->walletaddress;
		$result['blockchain']['ballots'][$i]['balance'] = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($ballotO->walletaddress);
		$result['blockchain']['ballots'][$i]['balance'] = $result['blockchain']['ballots'][$i]['balance'][0]['qty'];
		$result['blockchain']['ballots'][$i]['end'] = $ballotO->end;
		
		$options = (array) $ballotO->options;
		
		$j = 0;
		foreach ($options AS $option) {
			$result['blockchain']['ballots'][$i]['options'][$j] = json_decode($option);
			$balance = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($result['blockchain']['ballots'][$i]['options'][$j]->walletaddress);
			$result['blockchain']['ballots'][$i]['options'][$j]->balance = $balance[0]['qty'];
			$j++;
		}		
		return $result;
	}
	
// 	/**
// 	 * bcOption
// 	 * @param \stdClass $option
// 	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
// 	 * @param int $j Zählvariable
// 	 * @return array
// 	 */
// 	private function bcOption(\Goettertz\BcVoting\Domain\Model\Project $project, $option, $j) {
// 		$result['blockchain']['ballots'][$i]['options'][$j] = json_decode($option);
// 		$balance = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaddressbalances($address);
// 		$result['blockchain']['ballots'][$i]['options'][$j]->balance = $balance[1];
// 		return $result;
// 	}
	
	/**
	 * getTxidsAddress
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param string $address
	 * @return array $result
	 */
	private function getTxidsAddress(\Goettertz\BcVoting\Domain\Model\Project $project, $address, $max = 100) {
		$result = array();
		if ($obj = Blockchain::checkWalletAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $address, true))
		{
			$result = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listaddresstransactions($address, 100);
		}
		return $result;
	}
}
?>