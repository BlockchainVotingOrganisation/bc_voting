<?php
namespace Goettertz\BcVoting\Controller;

ini_set("display_errors", 1);

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
 * Revision 129:
 * - Bugfix #15
 * 
 */


use Goettertz\BcVoting\Property\TypeConverter\UploadedFileReferenceConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Goettertz\BcVoting\Service\Blockchain;
use Goettertz\BcVoting\Service\MCrypt;
use Goettertz\BcVoting\Service\OP_RETURN;
use Goettertz;

/**
 * BallotController
 */
class BallotController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;	

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
	protected $votingRepository = NULL;	
	
	/**
	 * assignmentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssignmentRepository
	 * @inject
	 */
	protected $assignmentRepository = NULL;

	/**
	 * ballotRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\BallotRepository
	 * @inject
	 */
	protected $ballotRepository = NULL;
	
	/**
	 * assetRepository
	 * 
	 * @var \Goettertz\BcVoting\Domain\Repository\AssetRepository
	 * @inject
	 */
	protected $assetRepository = NULL;

	/**
	 * action list
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 *
	 * @return void
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$this->view->assign('user', $user);
		}
		$ballots = $this->ballotRepository->findByProject($project);
		$this->view->assign('ballots', $ballots);
		$this->view->assign('project', $project);
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $newBallot
	 * @param string $redirect
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Ballot $newBallot = NULL, $redirect = '') {
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('newBallot', $newBallot);
				$this->view->assign('project', $project);
				$this->view->assign('redirect', $redirect);
			}
			else {
				# msg und redirect zu listaction
				$this->addFlashMessage('You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show','Project','BcVoting',array('project'=>$project));
			}			
		}
		else {
			$this->addFlashMessage('You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show','Project','BcVoting',array('project'=>$project));
		}		
	}
	
	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeCreateAction() {
		$this->setTypeConverterConfigurationForImageUpload('newBallot');
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $newBallot
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Ballot $newBallot) {
		
		if ($user = $this->userRepository->getCurrentFeUser()) {
			$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
			If($assignment != NULL) {
				$newBallot->setProject($project);
				
				# gets correct UNIX timestamp only if contained in formdata
				if ($newBallot->getStart() != '') {
					$start = strtotime($newBallot->getStart());
					if($start > 0) $newBallot->setStart($start);
				}
					 
				if ($newBallot->getEnd() != '') {
					$end = strtotime($newBallot->getEnd());
					if ($end > 0) $newBallot->setEnd($end);
				}
				
				if ($project->getRpcServer() != '') {
					$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaccountaddress($newBallot->getName());
				}
				$newBallot->setWalletAddress($newAddress);
				
				$this->ballotRepository->add($newBallot);
				$this->addFlashMessage('The object was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					
				$this->redirect('list','Ballot','BcVoting', array('project' => $project));
			}
			else {
				# msg und redirect zu listaction
				$this->addFlashMessage('You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show','Project','BcVoting',array('project'=>$project));
			}
		}
		else {
			# msg und redirect zu listaction
			$this->addFlashMessage('You are not currently logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show', 'Project', 'BcVoting', array('project'=>$project));
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject();
		$isAssigned = 'false';
		$isAdmin 	= 'false';

		if ($user = $this->userRepository->getCurrentFeUser()) {

			if ($project) {
				$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
				If($assignment != NULL) {
					$isAssigned = 'true';
					$this->view->assign('isAssigned', $isAssigned);
				}							
			}
		}
		$this->view->assign('ballot', $ballot);
		$this->view->assign('isAssigned', $isAssigned);

	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		
		$project = $ballot->getProject();
		
		# Check if sealed
		if ($ballot->getReference() === '') {
			# Check FE-User
			if ($user = $this->userRepository->getCurrentFeUser()) {
			
				$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
				If($assignment != NULL) {
					
					if (!empty($project->getRpcUser())) $bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listpermissions('issue');
					
					$this->view->assign('issuePermission', $bcArray[0]['address']);
					$this->view->assign('ballot', $ballot);
					$this->view->assign('assigned', true);
					$this->view->assign('admin', 'true');
				}
				else {
					# msg und redirect zu listaction
					$this->addFlashMessage('You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('show','Project','BcVoting',array('project'=>$project));						
				}
			}
			else {
				# msg und redirect zu listaction
				$this->addFlashMessage('You are not currently logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show', 'Project', 'BcVoting', array('project'=>$project));	
			}			
		}
		else {
			$this->addFlashMessage('Project is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list','Ballot','BcVoting',array('project'=>$project));
		}
	}
	
	/**
	 * action delete
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @return void
	 */	
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject();
		if ($ballot->getReference() === '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {
				$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
				If($assignment != NULL) {
					$this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					$this->ballotRepository->remove($ballot);
					$this->redirect('edit', 'Project', NULL, array('project'=>$project));						
				}
			}
		}
	}

	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeUpdateAction() {
		$this->setTypeConverterConfigurationForImageUpload('ballot');
	}
	
	/**
	 * action update
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		
		$project = $ballot->getProject();
		
		# Nur Update, wenn noch nicht sealed
		if ($ballot->getReference() === '') {
			
			# Nur update, wenn login
			if ($user = $this->userRepository->getCurrentFeUser()) {
			
				$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
				If($assignment != NULL) {
			
					# gets correct UNIX timestamp only if contained in formdata
					if ($ballot->getStart() != '') {
						$start = strtotime($ballot->getStart());
						if($start > 0) $ballot->setStart($start);
					}
					 
					if ($ballot->getEnd() != '') {
						$end = strtotime($ballot->getEnd());
						if ($end > 0) $ballot->setEnd($end);
					}
					if (empty($ballot->getWalletAddress())) {
						if ($project->getRpcServer() != '') {
							$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaccountaddress($ballot->getName());
						}
					$ballot->setWalletAddress($newAddress);
					}
				
					$this->ballotRepository->update($ballot);
					$this->addFlashMessage('The ballot was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
						
				}
				else {
					$this->addFlashMessage('You\'re no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
				
			}
			else {
				$this->addFlashMessage('You\'re not logged in!!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
				
		}
		else {
			$this->addFlashMessage('Ballot is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit','Project','BcVoting',array('project'=>$project));
		}
		
	$this->redirect('edit','Ballot','BcVoting',array('ballot'=>$ballot));			
	}

	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function removeLogoAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\'tx_bcvoting_domain_model_ballot\' AND fieldname=\'logo\' AND uid_foreign = '.$ballot->getUid().' AND deleted = 0';
		$db = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$this->redirect('edit','Ballot','BcVoting',array('ballot'=>$ballot));
	}
	
	/**
	 * seals the ballot properties
	 * 
	 * after sealing the relevant properties of the project 
	 * (ballot, options, time periods) should not be changed anymore
	 * 
	 * to seal the properties a hash is stored in the blockchain and the Blocknumber, reference-id ist stored as a project property
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function sealBallotAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {

		$project = $ballot->getProject();
		
		# Check options
		$options = $ballot->getOptions();
		if (count($options) === 0) die ('options not complete (365)');
		foreach ($options as $option) {
			if (empty($option->getWalletAddress())) die ('ERROR: options not complete (367)');
		}

		if ($project->getRpcPassword() !== '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {
			
				$assignment = $user ? $project->getAssignmentForUser($user, 'admin') : NULL;
				If($assignment != NULL) {
					
 					if (empty($ballot->getAsset())) {
						
 						# issue asset for ballot
 						$bcArray = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listpermissions('issue');
						$issueAddress = $bcArray[0]['address'];
 						$asset = array();
// // 						try {
// // 							$asset = Blockchain::getRpcResult($project)->listassets($ballot->getName());
// // 						} catch (Exception $e) {
// // 							$result['error'] = $e;
// // 						}
						
						
						# no asset in bc
						if (count($asset) === 0) {
							$newAsset = new \Goettertz\BcVoting\Domain\Model\Asset();
							$newAsset->setName($ballot->getName());
							$newAsset->setQuantity(20000000);
							$newAsset->setDivisibility(1);
							
							if ($result = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->issue($issueAddress, $newAsset->getName(), $newAsset->getQuantity(), $newAsset->getDivisibility())) {
									
							
								$asset = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->listassets($result);
							
									
								$newAsset->setAssetId($asset[0]['assetref']);
									
								$this->assetRepository->add($newAsset);
									
								$ballot->setAsset($asset[0]['assetref']);
								
								$this->ballotRepository->update($ballot);
							}
							else die ('No asset "'.$newAsset->getName().'" issued!');
						}
						# asset already in bc
						else {
							$ballot->setAsset($asset[0]['assetref']);
							$this->ballotRepository->update($ballot);
						}
 					}

		
					# Check if sealed
					if ($ballot->getReference() === '') {
						
						# The data for sealing ...
						$json = $ballot->getJson($ballot);
						$hash = $this->getHash($json);

						# Saving data in the blockchain ...
						if ($ref = Blockchain::storeData($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $project->getWalletAddress(), $project->getWalletAddress(), 0.00000001, $json)  ) {
							
							$ballot->setReference($ref);
							$this->ballotRepository->update($ballot);

							if (!is_array($ref)) {
								if (is_string($ref)) $this->addFlashMessage('The ballot was sealed. ', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
							}
							elseif (is_string($ref['error']))  $this->addFlashMessage('ERROR:  '.$ref['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);						
							else  $this->addFlashMessage('ERROR:  '.implode('-', $ref), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
						}

						$this->view->assign('ref', $ref);
						$this->view->assign('project', $project);
						$this->view->assign('json', $json);
						$this->view->assign('hash', $hash);
					}
					else {
						# redirect show ballot
						die('Already sealed! ('.$ballot->getReference().')');
					}
				}
				else {
					# redirect show ballot
					die('No admin!');
				}
			}
			else {
				$this->addFlashMessage('Your login is expired!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				//die('Not currently logged in!');
			}
		}
		else {
			$this->addFlashMessage('No Blockchain configured!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		$this->view->assign('project', $project);
	}

	/**
	 * action vote
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function voteAction(\Goettertz\BcVoting\Domain\Model\Option $option, \Goettertz\BcVoting\Domain\Model\Project $project) {
	
		if ($project->getStart() < time() && time() < $project->getEnd()) {
			if ($user = $this->userRepository->getCurrentFeUser()) {
					
				$votings = $this->votingRepository->findByProject($project);
				$countVotings = count($votings);
				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user) : NULL;
			
				# Wenn angemeldet
				If($assignment !== NULL) {
			
					# Nur bei Wahl ohne Blockchain und Coins
					if ($project->getRpcServer() === '') {
						$result = $this->votingDb($project, $option, $user);
					}
			
					# Wahl mit Blockchain ...
					else {
						$result = $this->votingBc($project, $option, $assignment);
					}
			
					if(!isset($result['error'])) {
						$this->addFlashMessage($result, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					}
					else {
						$this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
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
		$this->redirect('evaluation', 'Project', NULL, array('project' => $project, 'count' => $countVotings));
	}
	
	/**
	 * votingBc
	 * 
	 * Blockchain voting sending assets
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @param \Goettertz\BcVoting\Domain\Model\Assignment $assignment
	 * @return NULL|mixed
	 */
	protected function votingBc(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Option $option, \Goettertz\BcVoting\Domain\Model\Assignment $assignment) {

		$result = NULL;
		$votes = 0;
		
		$ballot = $option->getBallot();
		
		# Wahl mit Multichain
		if (!empty($project->getRpcServer())) {
			$asset = trim($ballot->getAsset());
			
			$fromaddress = trim($assignment->getWalletAddress());
			if (empty($fromaddress)) {
				return $result['error'] = 'Error (525): no address to send from!';
			}
			
			if ($project->getCategory()->getUlterrior() == true) {
				
				#prüfen, ob es schon eine addresse gibt...
				# wenn nicht, dann neue addresse erzeugen...
				
				$toaddress = $ballot->getWalletAddress(); // ballot-address, wenn geheime Wahl
			}
			else $toaddress = $option->getWalletAddress(); // option-address, wenn nicht geheim
			
			$votes = Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $fromaddress, $asset);

			# Stimmrechte Anzahl
			
			if ($votes > 0) {
				$mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
				$plaintext = $option->getName().'-'.$option->getWalletAddress(); //"This string was AES-256 / CBC / ZeroBytePadding encrypted.";
				$secret = $mcrypt->encrypt($plaintext);
			
				$amount = array($asset => 1);
					
				# Assets versenden
				if ($ref = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendwithmetadatafrom($fromaddress,$toaddress,$amount,bin2hex($secret)) ) {
					# wenn erfolgreich
					if (is_string($ref)) {
			
						#
						$voting = new \Goettertz\BcVoting\Domain\Model\Voting();// 									$this->votingRepository->add($voting);
						$voting->setTxid($ref);
						$voting->setSecret($secret);
						$voting->setProject($project);
						$voting->setReference($ballot->getReference());
			
						$this->votingRepository->add($voting);
						$strVotes = print_r($votes[$fromaddress][0]['qty'],true);
						$result['msg'] = 'Voting '.$project->getName().': success!<br />TxId: '.$ref.' Address: '.$toaddress.' Amount: '.implode(':',$amount);
					}
					else {
						$result['error'] = 'Voting failed (545). RPC-Error: '.$ref['error'].' '.$hash.' '.$ref['ref'];
					}
				}
				else {
					$result['error'] = 'Voting failed (545): No result.';
				}
			}
			else {
				$result['error'] = 'Voting failed (537): Not enough assets! '.$fromaddress.' '.$asset.' '.$votes;
			}
				
		}
		else {
			$votes = $assignment->getVotes();
			$voting = new \Goettertz\BcVoting\Domain\Model\Voting();// 									$this->votingRepository->add($voting);
			$voting->setTxid($ref);
			$voting->setSecret($secret);
			$voting->setProject($project);
			$voting->setReference($ballot->getReference());
				
			$this->votingRepository->add($voting);
			$assignment->setVotes($assignment->getVotes() - 1);
			$strVotes = print_r($votes[$fromaddress][0]['qty'],true);
			$result['msg'] = 'Voting '.$project->getName().': success!<br />TxId: '.$ref.' Address: '.$toaddress.' Asset amount: '.implode(':',$amount);
		}		
		return $result;
	}
	
	/**
	 * @param string $string
	 * @return string
	 */
	protected function getHash($string) {
		return $hash = hash('sha256', $string);
	}

	/**
	 * @param string $argumentName - object model name (lowercase)
	 */
	protected function setTypeConverterConfigurationForImageUpload($argumentName) {
		$uploadConfiguration = array(
				UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/tx_bc_voting/',
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_CONFLICT_MODE => '2'
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