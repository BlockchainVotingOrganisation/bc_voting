<?php
namespace Goettertz\BcVoting\Controller;

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
 * Revision 138:
 *
 */
use Goettertz\BcVoting\Service\Blockchain;
use Goettertz\BcVoting\Service\MCrypt;

/**
 * VotingController
 * @author louis
 *
 */
class VotingController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {


	/**
	 * votingRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;

	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * action create
	 * @param \Goettertz\BcVoting\Domain\Model\Voting $newVoting
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Voting $newVoting, \Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		
		#Test
		if ($this->request->hasArgument['test']) {
			$this->addFlashMessage($this->request->getArgument['optionCode'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		}
		
		$project = $ballot->getProject();

		if ($project->getStart() < time() && time() < $project->getEnd()) {
			if ($user = $this->userRepository->getCurrentFeUser()) {

				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user) : NULL;

				# Wenn angemeldet
				If($assignment !== NULL) {
						
					if ($project->getRpcServer() === '') {
						$this->addFlashMessage('Kein RPC-Server', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					}
					else {
						if ($result = $this->votingBc($newVoting, $assignment, $ballot)) {
							if(is_array($result)) {
								if (is_string($result['error'])) {
									$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
								}
								else if (is_string($result['msg'])) {
									$this->addFlashMessage($result['msg'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
								}
							}
							
							else {
								$this->addFlashMessage('No Result! 82 '.$result, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
							}
						}
						else {
								$this->addFlashMessage('No Result! 86', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
						}
					}
				}
				else {
					$this->addFlashMessage('Not assigned!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
			}
			else {
				$this->addFlashMessage('Vote failed: your login is expired!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		}
		
		else {
			$this->addFlashMessage('Voting period has not begun/has end.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		
		#Finally
		$this->redirect('show', 'Election', NULL, array('project' => $project, 'count' => $countVotings));
		
	}
	
	/**
	 * votingBc
	 *
	 * Blockchain voting sending assets
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Voting $voting
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignment
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 *
	 * @return NULL|mixed
	 */
	private function votingBc(\Goettertz\BcVoting\Domain\Model\Voting $voting, \Goettertz\BcVoting\Domain\Model\Assignment $assignment, \Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		
		$result = array();
		$result['error'] = NULL;
		
		$balance = 0;
	
		if (empty($voting->getOptionCode())) {
			$result['error'] = 'No options! 132';
			return $result;
		}
	
// 		$ballot = $option->getBallot();
 		$project = $ballot->getProject();
	

		# Wahl mit Multichain
		if (!empty($project->getRpcServer())) {
				
// 			$asset = trim($ballot->getAsset());
				
			$fromaddress = trim($assignment->getWalletAddress());
			if (empty($fromaddress)) {
				$result['error'] = 'Error (525): no address to send from!';
				return $result;
			}
				
			$toaddress = $ballot->getWalletAddress(); // ballot-address, wenn geheime Wahl
			$balance = Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $fromaddress, $asset);
	
			# Stimmrechte Anzahl
				
			if ($balance > 0) {
				$mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
	
				$record = $voting->getOptionCode();
				$record = explode("###", $record);
				
				if (count($record) == 0) {
					$result['error'] = 'No codes 168';
					return $result;
				}
				
				$random = $record[0];
				$vote = $record[1];
				$secret = $mcrypt->encrypt($vote);
				$hash = $record[2];
				$meta = trim($record[0]).'###'.trim($secret).'###'.trim($record[2]);
	
				$amount = array($ballot->getAsset() => 1);
					
				# Assets versenden
				if ($ref = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendwithmetadatafrom($fromaddress,$toaddress,$amount,bin2hex($meta)) ) {
					# wenn erfolgreich
					if (is_string($ref)) {
							
						#
						$newvoting = new \Goettertz\BcVoting\Domain\Model\Voting();// 									$this->votingRepository->add($voting);
						$newvoting->setTxid($ref);
						$newvoting->setSecret($secret);
						$newvoting->setProject($project);
						$newvoting->setReference($ballot->getReference());
							
						$this->votingRepository->add($newvoting);
						
						
						$strVotes = print_r($balance[$fromaddress][0]['qty'],true);
						$result['msg'] = 'Voting success!<br />TxId: '.$ref;
						$result['msg'] .='<br />Meta:<pre>'.$meta.'</pre>';
						$result['msg'] .='<br />Encrypted option text:<pre>'.$secret.'</pre>';
					}
					else {
						$result['error'] = 'Voting failed (198). RPC-Error: '.$ref['error'].' '.$hash.' '.$ref['ref'];
					}
				}
				else {
					$result['error'] = 'Voting failed (202): No result.';
				}
			}
			else {
				$result['error'] = 'Voting failed (206): Not enough assets! '.$fromaddress.' '.$asset.' '.$balance;
			}
		}
		else {
// 			$balance = $assignment->getVotes();
// 			$voting = new \Goettertz\BcVoting\Domain\Model\Voting();// 									$this->votingRepository->add($voting);
// 			$voting->setTxid($ref);
// 			$voting->setSecret($secret);
// 			$voting->setProject($project);
// 			$voting->setReference($ballot->getReference());
	
// 			$this->votingRepository->add($voting);
// 			$assignment->setVotes($assignment->getVotes() - 1);
// 			$strVotes = print_r($balance[$fromaddress][0]['qty'],true);
// 			$result['msg'] = 'Voting '.$project->getName().': success!<br />TxId: '.$ref.' Address: '.$toaddress.' Asset amount: '.implode(':',$amount);
		}
		return $result;
	}
}
?>