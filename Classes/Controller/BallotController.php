<?php

namespace Goettertz\BcVoting\Controller;

// ini_set("display_errors", 1);

/**
 * *************************************************************
 *
 * Copyright notice
 *
 * (c) 2015 - 2016 Louis Göttertz <info2016@goettertz.de>, goettertz.de
 *
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * *************************************************************
 */

/**
 * Revision 138:
 */
use Goettertz\BcVoting\Property\TypeConverter\UploadedFileReferenceConverter;
use Goettertz\BcVoting\Service\Blockchain;
use Goettertz\BcVoting\Service\MCrypt;

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
	 * optionRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\OptionRepository 
	 * @inject
	 */
	protected $optionRepository = NULL;
	
	/**
	 * assetRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssetRepository @inject
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
			$this->view->assign ( 'user', $user );
		}
		$ballots = $this->ballotRepository->findByProject ( $project );
		$this->view->assign ( 'ballots', $ballots );
		$this->view->assign ( 'project', $project );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $newBallot        	
	 * @param string $redirect        	
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Ballot $newBallot = NULL, $redirect = '') {
		if ($user = $this->userRepository->getCurrentFeUser ()) {
			$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
			If ($assignment != NULL) {
				$this->view->assign ( 'newBallot', $newBallot );
				$this->view->assign ( 'project', $project );
				$this->view->assign ( 'redirect', $redirect );
			} else {
				// msg und redirect zu listaction
				$this->addFlashMessage ( 'You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				$this->redirect ( 'show', 'Project', 'BcVoting', array (
						'project' => $project 
				) );
			}
		} else {
			$this->addFlashMessage ( 'You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			$this->redirect ( 'show', 'Project', 'BcVoting', array (
					'project' => $project 
			) );
		}
	}
	
	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeCreateAction() {
		$this->setTypeConverterConfigurationForImageUpload ( 'newBallot' );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $newBallot        	
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Project $project, \Goettertz\BcVoting\Domain\Model\Ballot $newBallot) {
		if ($user = $this->userRepository->getCurrentFeUser ()) {
			$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
			If ($assignment != NULL) {
				$newBallot->setProject ( $project );
				
				// gets correct UNIX timestamp only if contained in formdata
				if ($newBallot->getStart () != '') {
					$start = strtotime ( $newBallot->getStart () );
					if ($start > 0)
						$newBallot->setStart ( $start );
				}
				
				if ($newBallot->getEnd () != '') {
					$end = strtotime ( $newBallot->getEnd () );
					if ($end > 0)
						$newBallot->setEnd ( $end );
				}
				
				if ($project->getRpcServer () != '') {
					$newAddress = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getaccountaddress ( $newBallot->getName () );
				}
				$newBallot->setWalletAddress ( $newAddress );
				
				$this->ballotRepository->add ( $newBallot );
				$this->addFlashMessage ( 'The object was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
				
				$this->redirect ( 'list', 'Ballot', 'BcVoting', array (
						'project' => $project 
				) );
			} else {
				// msg und redirect zu listaction
				$this->addFlashMessage ( 'You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				$this->redirect ( 'show', 'Project', 'BcVoting', array (
						'project' => $project 
				) );
			}
		} else {
			// msg und redirect zu listaction
			$this->addFlashMessage ( 'You are not currently logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			$this->redirect ( 'show', 'Project', 'BcVoting', array (
					'project' => $project 
			) );
		}
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject ();
		$isAssigned = 'false';
		$isAdmin = 'false';
		
		// OptionCodes
		foreach ( $project->getBallots () as $ballot ) {
			foreach ( $ballot->getOptions () as $option ) {
				$option->setOptionCode ( $this->rand_string ( 10 ) );
				$option->setOptionHash ( $this->getHash ( $option->getOptionCode () . $option->getWalletAddress () ) );
			}
		}
		
		if ($user = $this->userRepository->getCurrentFeUser ()) {
			
			if ($project) {
				$assignment = $user ? $project->getAssignmentForUser ( $user ) : NULL;
				If ($assignment != NULL) {
					$isAssigned = 'true';
					$this->view->assign ( 'isAssigned', $isAssigned );
				}
			}
		}
		$voting = new \Goettertz\BcVoting\Domain\Model\Voting ();
		
		$this->view->assign ( 'voting', $voting );
		
		$this->view->assign ( 'ballot', $ballot );
		$this->view->assign ( 'isAssigned', $isAssigned );
		$this->view->assign ( 'result', $result );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject ();
		
		// Check if sealed
		if ($ballot->getReference () === '') {
			// Check FE-User
			if ($user = $this->userRepository->getCurrentFeUser ()) {
				
				$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
				If ($assignment != NULL) {
					$bcArray = array ();
					$rpcServer = $project->getRpcServer ();
					
					if (is_string ( $rpcServer ) && $rpcServer !== '') {
						try {
							if ($bcArray = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->listpermissions ( 'issue' )) {
								if (! is_string ( $bcArray ['error'] )) {
									$this->view->assign ( 'issuePermission', $bcArray [0] ['address'] );
								} else {
									$this->addFlashMessage ( 'Blockchain not properly configured!<br />' . $bcArray ['error'] . '<br />(238)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
								}
							}
						} catch ( \Exception $e ) {
							$this->addFlashMessage ( 'Blockchain not properly configured!<br /><br />(238)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
						}
					}
					$this->view->assign ( 'ballot', $ballot );
					$this->view->assign ( 'assigned', true );
					$this->view->assign ( 'admin', 'true' );
				} else {
					// msg und redirect zu listaction
					$this->addFlashMessage ( 'You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
					$this->redirect ( 'show', 'Project', 'BcVoting', array (
							'project' => $project 
					) );
				}
			} else {
				// msg und redirect zu listaction
				$this->addFlashMessage ( 'You are not currently logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				$this->redirect ( 'show', 'Project', 'BcVoting', array (
						'project' => $project 
				) );
			}
		} else {
			$this->addFlashMessage ( 'Project is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			$this->redirect ( 'list', 'Ballot', 'BcVoting', array (
					'project' => $project 
			) );
		}
	}
	
	/**
	 * action delete
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 * @return void
	 */
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject ();
		if ($ballot->getReference () === '') {
			if ($user = $this->userRepository->getCurrentFeUser ()) {
				$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
				If ($assignment != NULL) {
					$this->addFlashMessage ( 'The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
					$this->ballotRepository->remove ( $ballot );
					$this->redirect ( 'edit', 'Project', NULL, array (
							'project' => $project 
					) );
				}
			}
		}
	}
	
	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeUpdateAction() {
		$this->setTypeConverterConfigurationForImageUpload ( 'ballot' );
	}
	
	/**
	 * action update
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject ();
		
		// Nur Update, wenn noch nicht sealed
		if ($ballot->getReference () === '') {
			
			// Nur update, wenn login
			if ($user = $this->userRepository->getCurrentFeUser ()) {
				
				$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
				If ($assignment != NULL) {
					
					// gets correct UNIX timestamp only if contained in formdata
					if ($ballot->getStart () != '') {
						$start = strtotime ( $ballot->getStart () );
						if ($start > 0)
							$ballot->setStart ( $start );
					}
					
					if ($ballot->getEnd () != '') {
						$end = strtotime ( $ballot->getEnd () );
						if ($end > 0)
							$ballot->setEnd ( $end );
					}
					if (empty ( $ballot->getWalletAddress () )) {
						if (is_string ( $project->getRpcServer () ) && $project->getRpcServer () != '') {
							// $bc = new Blockchain();
							$newAddress = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getnewaddress ();
						}
						$ballot->setWalletAddress ( $newAddress );
					}
					
					$this->ballotRepository->update ( $ballot );
					$this->addFlashMessage ( 'The ballot was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
				} else {
					$this->addFlashMessage ( 'You\'re no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				}
			} else {
				$this->addFlashMessage ( 'You\'re not logged in!!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			}
		} else {
			$this->addFlashMessage ( 'Ballot is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			$this->redirect ( 'edit', 'Project', 'BcVoting', array (
					'project' => $project 
			) );
		}
		
		$this->redirect ( 'edit', 'Ballot', 'BcVoting', array (
				'ballot' => $ballot 
		) );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 */
	public function removeLogoAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\'tx_bcvoting_domain_model_ballot\' AND fieldname=\'logo\' AND uid_foreign = ' . $ballot->getUid () . ' AND deleted = 0';
		$db = $GLOBALS ['TYPO3_DB']->sql_query ( $sql );
		$this->redirect ( 'edit', 'Ballot', 'BcVoting', array (
				'ballot' => $ballot 
		) );
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
		$project = $ballot->getProject ();
		
		// foreach ($options as $option) {
		// if (empty($option->getWalletAddress())) die ('ERROR: options not complete (367)');
		// }
		
		if ($project->getRpcPassword () !== '') {
			if ($user = $this->userRepository->getCurrentFeUser ()) {
				
				$assignment = $user ? $project->getAssignmentForUser ( $user, 'admin' ) : NULL;
				If ($assignment != NULL) {
					
					// Check if sealed
					if ($ballot->getReference () === '') {
						
						if (empty ( $ballot->getAsset () )) {
							$asset = $this->createAsset ( $ballot );
						}
						
						// verify asset
						
						
						
						$this->setNewAddresses ( $ballot );
						
						// The current data for sealing ...
						$json = $ballot->getJson ( $ballot );
						$hash = $this->getHash ( $json );
						
						// Saving data in the blockchain ...
						$vtc_amount = $this->settings ['payment_sealing'];
						if ($vtc_amount < 0.00000001)
							$vtc_amount = 0.00000001;
							
							// check if balance is enough -> soll in model->project
						if ($balance = $project->getBalance ( $project )) {
							if (doubleval ( $balance ) < doubleval ( $vtc_amount )) {
								$this->addFlashMessage ( 'Not enough inputs: ' . $project->getWalletAddress () . ' ' . doubleval ( $balance ), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
								$this->redirect ( 'show', 'Wallet', NULL, array (
										'project' => $project 
								) );
							}
						} else {
							$this->addFlashMessage ( 'Error: no balance: ' . $project->getWalletAddress () . ' ' . doubleval ( $balance [0] ['qty'] ), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
							$this->redirect ( 'show', 'Wallet', NULL, array (
									'project' => $project 
							) );
						}
						if ($ref = Blockchain::storeData ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword (), $project->getWalletAddress (), $project->getWalletAddress (), $vtc_amount, $json )) {
							
							$ballot->setReference ( $ref );
							$this->ballotRepository->update ( $ballot );
							
							if (! is_array ( $ref )) {
								if (is_string ( $ref ))
									$this->addFlashMessage ( 'The ballot was sealed. ', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
							} elseif (is_string ( $ref ['error'] ))
								$this->addFlashMessage ( $ref ['error'] . ' ' . $json, 'ERROR', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
							else
								$this->addFlashMessage ( 'ERROR:  ' . implode ( '-', $ref ), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
						}
						
						$this->view->assign ( 'ref', $ref );
						$this->view->assign ( 'project', $project );
						$this->view->assign ( 'json', $json );
						$this->view->assign ( 'hash', $hash );
						$this->view->assign ( 'result', $result );
					} else {
						// redirect show ballot
						die ( 'Already sealed! (' . $ballot->getReference () . ')' );
					}
				} else {
					// redirect show ballot
					die ( 'No admin!' );
				}
			} else {
				$this->addFlashMessage ( 'Your login is expired!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				// die('Not currently logged in!');
			}
		} else {
			$this->addFlashMessage ( 'No Blockchain configured!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		$this->view->assign ( 'project', $project );
	}
	
	// /**
	// * action vote
	// *
	// * @param \Goettertz\BcVoting\Domain\Model\Voting $voting
	// * @return void
	// */
	// public function voteAction(\Goettertz\BcVoting\Domain\Model\Voting $voting) {
	
	// $project = $voting->getProject();
	
	// if ($project->getStart() < time() && time() < $project->getEnd()) {
	// if ($user = $this->userRepository->getCurrentFeUser()) {
	
	// // $votings = $this->votingRepository->findByProject($project);
	// // $countVotings = count($votings);
	
	// $isAssigned = false;
	// $assignment = $user ? $project->getAssignmentForUser($user) : NULL;
	
	// # Wenn angemeldet
	// If($assignment !== NULL) {
	
	// if ($project->getRpcServer() === '') {
	// $result = $this->votingDb($project, $option, $user);
	// }
	// else {
	
	// $result = $this->votingBc($voting);
	// }
	
	// if(!isset($result['error'])) {
	// $this->addFlashMessage($result['msg'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
	// }
	// else {
	// $this->addFlashMessage($result['error'], '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
	// }
	// }
	// else {
	// $this->addFlashMessage('Not assigned!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
	// }
	// }
	// else {
	// $this->addFlashMessage('Vote failed: your login is expired!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
	// }
	// }
	
	// else {
	// $this->addFlashMessage('Voting period has not begun/has end.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
	// }
	
	// #Finally
	// $this->redirect('show', 'Election', NULL, array('project' => $project, 'count' => $countVotings));
	// }
	
	// /**
	// * votingBc
	// *
	// * Blockchain voting sending assets
	// *
	// * @param \Goettertz\BcVoting\Domain\Model\Voting $voting
	// *
	// *
	// * @return NULL|mixed
	// */
	// protected function votingBc(\Goettertz\BcVoting\Domain\Model\Voting $voting) {
	// $result = NULL;
	// $balance = 0;
	
	// if (empty($option = $voting->getOption())) {
	// return $result['error'] = 'No options! 555';
	// }
	
	// $ballot = $option->getBallot();
	// $project = $ballot->getProject();
	
	// if ($user = $this->userRepository->getCurrentFeUser()) {
	
	// // $votings = $this->votingRepository->findByProject($project);
	// // $countVotings = count($votings);
	
	// $isAssigned = false;
	// $assignment = $user ? $project->getAssignmentForUser($user) : NULL;
	// }
	
	// # Wahl mit Multichain
	// if (!empty($project->getRpcServer())) {
	
	// $asset = trim($ballot->getAsset());
	
	// $fromaddress = trim($assignment->getWalletAddress());
	// if (empty($fromaddress)) {
	// return $result['error'] = 'Error (525): no address to send from!';
	// }
	
	// $toaddress = $ballot->getWalletAddress(); // ballot-address, wenn geheime Wahl
	// $balance = Blockchain::getAssetBalanceFromAddress($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword(), $fromaddress, $asset);
	
	// # Stimmrechte Anzahl
	
	// if ($balance > 0) {
	// $mcrypt = new \Goettertz\BcVoting\Service\MCrypt();
	
	// $codes = $voting->getOptionCode();
	// if (count($codes) == 0) {
	// return $result['error'] = 'No codes 590';
	// }
	
	// $vote = new \stdClass();
	// $vote->label = trim($option->getName());
	// $vote->address = trim($option->getWalletAddress());
	
	// $vote->code = $codes[$option->getUid()];
	
	// $plaintext = json_encode($vote); //$option->getName().'-'.$option->getWalletAddress(); //"This string was AES-256 / CBC / ZeroBytePadding encrypted.";
	// $secret = $mcrypt->encrypt($plaintext);
	
	// $amount = array($asset => 1);
	
	// # Assets versenden
	// if ($ref = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->sendwithmetadatafrom($fromaddress,$toaddress,$amount,bin2hex(trim($secret))) ) {
	// # wenn erfolgreich
	// if (is_string($ref)) {
	
	// #
	// $voting = new \Goettertz\BcVoting\Domain\Model\Voting();// $this->votingRepository->add($voting);
	// $voting->setTxid($ref);
	// $voting->setSecret($secret);
	// $voting->setProject($project);
	// $voting->setReference($ballot->getReference());
	
	// $this->votingRepository->add($voting);
	// $strVotes = print_r($balance[$fromaddress][0]['qty'],true);
	// $result['msg'] = 'Voting success!<br />TxId: '.$ref;
	// $result['msg'] .='<br />Encrypted option text:<pre>'.$secret.'</pre>';
	// }
	// else {
	// $result['error'] = 'Voting failed (623). RPC-Error: '.$ref['error'].' '.$hash.' '.$ref['ref'];
	// }
	// }
	// else {
	// $result['error'] = 'Voting failed (627): No result.';
	// }
	// }
	// else {
	// $result['error'] = 'Voting failed (537): Not enough assets! '.$fromaddress.' '.$asset.' '.$balance;
	// }
	
	// }
	// else {
	// $balance = $assignment->getVotes();
	// $voting = new \Goettertz\BcVoting\Domain\Model\Voting();// $this->votingRepository->add($voting);
	// $voting->setTxid($ref);
	// $voting->setSecret($secret);
	// $voting->setProject($project);
	// $voting->setReference($ballot->getReference());
	
	// $this->votingRepository->add($voting);
	// $assignment->setVotes($assignment->getVotes() - 1);
	// $strVotes = print_r($balance[$fromaddress][0]['qty'],true);
	// $result['msg'] = 'Voting '.$project->getName().': success!<br />TxId: '.$ref.' Address: '.$toaddress.' Asset amount: '.implode(':',$amount);
	// }
	// return $result;
	// }
	
	/**
	 *
	 * @param string $string        	
	 * @return string
	 */
	protected function getHash($string) {
		return $hash = hash ( 'sha256', $string );
	}
	
	/**
	 *
	 * @param string $argumentName
	 *        	- object model name (lowercase)
	 */
	protected function setTypeConverterConfigurationForImageUpload($argumentName) {
		$uploadConfiguration = array (
				UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS ['TYPO3_CONF_VARS'] ['GFX'] ['imagefile_ext'],
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/tx_bc_voting/',
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_CONFLICT_MODE => '2' 
		);
		/** @var PropertyMappingConfiguration $newExampleConfiguration */
		$newExampleConfiguration = $this->arguments [$argumentName]->getPropertyMappingConfiguration ();
		$newExampleConfiguration->forProperty ( 'logo' )->setTypeConverterOptions ( 'Goettertz\\BcVoting\\Property\\TypeConverter\\UploadedFileReferenceConverter', $uploadConfiguration );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 */
	private function setNewAddresses(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		$project = $ballot->getProject ();
		
		// Ballot
		$walletAddress = Blockchain::getNewAddress ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () );
		$ballot->setWalletAddress ( $walletAddress );
		$this->ballotRepository->update ( $ballot );
		// Options
		// Check options
		$options = $ballot->getOptions ();
		if (count ( $options ) === 0)
			$result ['error'] = ('No options available (383)');
		else {
			foreach ( $options as $option ) {
				$walletaddress = Blockchain::getNewAddress ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () );
				$option->setWalletAddress ( $walletaddress );
				$this->optionRepository->update ( $option );
			}
		}
		return $result;
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Asset $asset        	
	 * @return boolean
	 */
	private function verifyAsset(\Goettertz\BcVoting\Domain\Model\Asset $asset) {
		
		
		if ($asset = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->listassets ( $result )) {
			if (is_array($asset)) return $asset;
		}
		return NULL;
	}
	private function createAsset(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		
		$project = $ballot->getProject ();
		
		// issue asset for ballot
		$bcArray = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->listpermissions ( 'issue' );
		$issueAddress = $bcArray [0] ['address'];
		$asset = NULL;
		
		// no asset in bc
		if (count ( $asset ) === 0) {
			$newAsset = new \Goettertz\BcVoting\Domain\Model\Asset ();
			$newAsset->setName ( $ballot->getName () );
			$newAsset->setQuantity ( 20000000 );
			$newAsset->setDivisibility ( 1 );
			$params = array (
					'name' => $newAsset->getName (),
					'open' => true 
			);
			if ($result = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->issue ( $issueAddress, $params, $newAsset->getQuantity (), $newAsset->getDivisibility () )) {
				
				if ($asset = $this->verifyAsset($asset)) {
					$newAsset->setAssetId ( $asset [0] ['assetref'] );
					
					$this->assetRepository->add ( $newAsset );
					
					$ballot->setAsset ( $asset [0] ['assetref'] );
					
					$this->ballotRepository->update ( $ballot );
						
				}
				else
					die ( 'No asset "' . $newAsset->getName () . '" was issued!' );
				
			} 
			else
				die ( 'No asset "' . $newAsset->getName () . '" could be issued!' );
		}
	}
	
	/**
	 *
	 * @param integer $length        	
	 * @return string
	 */
	private function rand_string($length) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$size = strlen ( $chars );
		for($i = 0; $i < $length; $i ++) {
			$str .= $chars [rand ( 0, $size - 1 )];
		}
		return $str;
	}
}
?>