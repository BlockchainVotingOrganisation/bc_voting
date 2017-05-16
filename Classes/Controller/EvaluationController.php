<?php

namespace Goettertz\BcVoting\Controller;
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

/**
 * *************************************************************
 *
 * Copyright notice
 *
 * (c) 2015 - 2017 Louis Göttertz <info2016@goettertz.de>, goettertz.de
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
 * Revision 146
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
	 * @var \Goettertz\BcVoting\Domain\Repository\ProjectRepository @inject
	 */
	protected $projectRepository = NULL;
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * ballotRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\BallotRepository @inject
	 */
	protected $ballotRepository = NULL;
	
	/**
	 * optionRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\OptionRepository @inject
	 */
	protected $optionRepository = NULL;
	
	/**
	 * votingRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\VotingRepository @inject
	 */
	protected $votingRepository = NULL;
	
	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$projects = $this->projectRepository->findAll ();
		$this->view->assign ( 'projects', $projects );
		if ($feuser = $this->userRepository->getCurrentFeUser ()) {
			$this->view->assign ( 'feuser', $feuser );
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
		$isAdmin = 'false';
		$isLoggedin = 'false';
		
		if ($feuser = $this->userRepository->getCurrentFeUser ()) {
			$isAssigned = 'false';
			$assignment = $feuser ? $project->getAssignmentForUser ( $feuser, 'admin' ) : NULL;
			If ($assignment === NULL) {
				// $this->addFlashMessage('No admin: '.$feuser.'!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				// $this->redirect('list',NULL,NULL, array('project' => $project));
			} else {
				$isAssigned = 'true';
				$isAdmin = 'true';
			}
		}
		
		$result = array ();
		
		if (empty ( $project->getReference () )) {
			$this->addFlashMessage ( 'No Reference-ID.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		// Todo ################### Check if rpc-settings are configured -> soll in eingene Funktion! return result[rpc] und result[bcinfo]
		
		$rpc = $project->checkRpc ( $project, $this->settings );
		if (is_string ( $rpc )) { // Fehlermeldung wurde ausgegeben
			$this->addFlashMessage ( $rpc, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		} else if (is_object ( $rpc )) {
			$project = $rpc; // Object 'Project' mit RPC-Eigenschaften wurde ausgegeben
		} else { // Irgendetwas anderes wurde ausgegeben.
			$this->addFlashMessage ( 'Unkown error.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		try {
			if ($bcArray = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getinfo ()) {
				$this->view->assign ( 'bcResult', $bcArray );
			} else {
				$this->addFlashMessage ( 'Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			}
		} catch ( \Exception $e ) {
			$this->addFlashMessage ( 'Error 131: ' . $e, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		if (! is_string ( $bcArray ['nodeaddress'] )) {
			$this->addFlashMessage ( 'Blockchain not properly configured.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		// #################################################
		
		$result ['Database'] ['TxId'] = $project->getReference ();
		$result ['Database'] ['Name'] = $project->getName ();
		$result ['Database'] ['ballots'] = $project->getBallots ();
		$result = array_merge ( $result, $this->bcInfo ( $project ) );
		
		$this->view->assign ( 'project', $project );
		$this->view->assign ( 'result', $result );
		$this->view->assign ( 'isAdmin', $isAdmin );
		$this->view->assign ( 'isAssigned', $isAssigned );
		$this->view->assign ( 'date_now', new \DateTime () );
	}
	
	/**
	 * proceedAction
	 *
	 * Darf nur einmal pro Stimmzettel ausgeführt werden!
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param string $address
	 *        	- ballot walletAddress
	 * @param string $asset
	 *        	- asset reference
	 * @return void
	 */
	public function proceedAction(\Goettertz\BcVoting\Domain\Model\Project $project, $address, $asset) {
		
		// $result = array();
		if ($feuser = $this->userRepository->getCurrentFeUser ()) {
			$isAssigned = 'false';
			$assignment = $feuser ? $project->getAssignmentForUser ( $feuser, 'admin' ) : NULL;
			If ($assignment === NULL) {
				$this->addFlashMessage ( 'No admin: ' . $feuser . '!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				$this->redirect ( 'list', NULL, NULL, array (
						'project' => $project 
				) );
			} else {
				$isAssigned = 'true';
				$isAdmin = 'true';
			}
		} else {
			If ($assignment === NULL) {
				$this->addFlashMessage ( 'You aren\'t currently logged in! Please goto <a href="/login/">login</a> or <a href="/register/">register</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				$this->redirect ( 'list', NULL, NULL, array (
						'project' => $project 
				) );
			}
		}
		
		// check if project evaluation has started twice: look for stream item.
		$items = array ();
		
		if ($items = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->liststreamkeyitems ( $project->getStream (), substr ( $address, 0, 10 ) )) {
			$this->addFlashMessage ( 'Evaluation started ' . count ( $items ) . ' times before!', 'Error', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			$this->redirect ( 'list', NULL, NULL, array (
					'project' => $project 
			) );
		} else {
			// $msg = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->publish($project->getStream(),'decrypted',bin2hex('test'));
			if (! is_array ( $msg )) {
				$this->addFlashMessage ( 'Evaluation started! ' . $msg, '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
			} else
				$this->addFlashMessage ( 'Evaluation not started! ' . implode ( $msg ), '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		$mcrypt = new \Goettertz\BcVoting\Service\MCrypt ();
		// get transactions
		$result ['txIds'] = $this->getTxidsAddress ( $project, $address );
		
		if (count ( $result ['txIds'] ) === 0) {
			$this->addFlashMessage ( 'No transactions found! (217)', 'Error', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}
		
		// Check if voting period has ended
		$ballots = $this->ballotRepository->findByWalletAddress ( $address );
		foreach ( $ballots as $ballot ) {
			if ($ballot->getEnd () > time ()) {
				$this->addFlashMessage ( 'Voting period is not over!', 'Error (241)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				unset ( $result );
				$this->redirect ( 'show', NULL, NULL, array (
						'project' => $project,
						'isAdmin' => $isAdmin 
				) );
			}
		}
		
		// Check balance
		
		$balance = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getmultibalances ( $address, $asset, 0, false );
		
		if ($balance === 0) {
			$this->addFlashMessage ( 'No asset balance!', 'Error (256)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			unset ( $result );
			$this->redirect ( 'show', NULL, NULL, array (
					'project' => $project,
					'isAdmin' => $isAdmin 
			) );
		}
		
		// ## Mix Prozedur -> Send Assets an eigene Adresse? ###
		$amount = array (
				$asset => $balance 
		);
		Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->sendwithmetadatafrom ( $address, $address, $amount, bin2hex ( 'Mix' ) );
		
		$i = 0;
		foreach ( $result ['txIds'] as $transaction ) { // muss sortiert werden absteigend nach Zeit
		                                             
			// CHECK einzelnd vorher !!!!!!!!!!!!!!!!
			if (empty ( $transaction ['balance'] ['assets'] )) {
				$msg = $i . ') No asset in transaction.! (244)';
				$publish = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->publish ( $project->getStream (), 'Error', bin2hex ( $address . '###' . $msg ) );
				$this->addFlashMessage ( $msg, 'Error (246)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			}
			if (empty ( $transaction ['data'] )) {
				$msg = $i . ') No data.! (248)';
				$publish = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->publish ( $project->getStream (), 'Error', bin2hex ( $address . '###' . $msg ) );
				$this->addFlashMessage ( $msg, 'Error (251)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
			}
			// $transaction['confirmations']
			
			if (! empty ( $transaction ['balance'] ['assets'] && ! empty ( $transaction ['data'] ) && $transaction ['confirmations'] > 0 )) {
				if (! empty ( $meta = Blockchain::retrieveData ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword (), $transaction ['txid'] ) )) {
					if (substr_count ( $meta, '###' ) > 1) {
						$voting = explode ( "###", $meta );
						if (is_array ( $voting )) {
							if (count ( $voting ) !== 3) {
								$msg = $i . ' Code error! (256)<br />' . $meta;
								$publish = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->publish ( $project->getStream (), 'Error', bin2hex ( $address . '###' . $msg ) );
								// $this->addFlashMessage($msg', 'Error (259)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
							} else {
								$random = $voting [0];
								$hash = $voting [2];
								
								// Da kommts drauf an...
								
								if (is_string ( $secret = $voting [1] )) {
									if (strlen ( $secret ) >= 16) {
										if ($targetAddress = $mcrypt->decrypt ( trim ( $secret ) ))
											if (is_string ( $targetAddress ) && strlen ( $targetAddress ) >= 10) {
												
												// Validate Addresses!!
												
												$this->addFlashMessage ( $i . ') Address: ' . htmlspecialchars ( $targetAddress ) . ' Secret: ' . htmlspecialchars ( $secret ), 'Success!' . ' (252)', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
												
												if ($balance >= 1) {
													if (! empty ( $targetAddress ) && $transaction ['balance'] ['assets'] [0] ['qty'] > 0) {
														if ($asset === $transaction ['balance'] ['assets'] [0] ['assetref']) {
															$amount = array (
																	$asset => 1 
															);
															
															// Check DB: Nur wenn ballot korrekt in db gespeichert wurde!
															
															// Eintrag in Db table votings -
															$myballots = $this->ballotRepository->findByWalletAddress ( $address );
															if (count ( $myballots ) === 0) {
																$this->addFlashMessage ( $i . ') No ballot! (' . htmlspecialchars ( $targetAddress ) . ')', 'Error' . ' (313)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
																break;
															}
															$myballot = $myballots [0];
															//
															// Eintrag in Voting-Stream, was soll, was darf eingetragen werden?
															$this->publishVoting ( $project, substr ( $address, 0, 10 ), $myballot->getReference () . '###' . $hash );
															$this->storeVotings ( $myballot->getReference (), $hash, $myballot, $targetAddress );
															
															if ($tx = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->sendwithmetadatafrom ( $address, $targetAddress, $amount, bin2hex ( $hash ) )) {
																
																if (! is_array ( $tx )) {
																	
																	// Eintrag in Flash-Log
																	$this->addFlashMessage ( $tx . ' => ' . $meta, '277', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK );
																} else {
																	$msg = $i . ') Target: ' . $targetAddress . ', ' . implode ( $tx );
																	$publish = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->publish ( $project->getStream (), 'Error', bin2hex ( $address . '###' . $msg . '###' . $transaction ['txid'] ) );
																	$this->addFlashMessage ( $msg, 'Error 299', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
																	break;
																}
															}
														}
													}
												}
											} else {
												$this->addFlashMessage ( $i . ' No target address! (282)<br />' . $transaction ['txid'], 'Error', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
											}
									} else {
										$this->addFlashMessage ( 'Secret is too short! (286)', 'Error!', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
									}
								} else {
									$this->addFlashMessage ( $i . ' No string secret! (262)<br />' . implode ( $voting ), 'Error!', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
								}
							}
						}
					} else {
						$this->addFlashMessage ( $i . ' No string voting (297)', 'Error!', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
					}
					
					// depreceated
					
					// if (!is_string($sendOption['address'])) {
					// $this->addFlashMessage($transaction['txid'] .' '.json_last_error_msg().'!<br />'.$json, 'JSON Error', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					// }
					// else {
					// // $this->addFlashMessage($i .' '.$sendOption['address'],'OK', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					// }
				} else {
					$this->addFlashMessage ( $i . ') No data from BC. (235)', 'Error (324)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
				}
			}
			// else {
			// $this->addFlashMessage($i.') Data from BC not complete. (234)','Error (328)', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			// }
			$i ++;
		} // end for transactions
		
		$this->redirect ( 'show', NULL, NULL, array (
				'project' => $project,
				'isAdmin' => $isAdmin 
		) );
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @return array $result
	 */
	private function bcInfo(\Goettertz\BcVoting\Domain\Model\Project $project) {
		// Blockchain Result - in eigene Funktion!
		$metadata = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getwallettransaction ( $project->getReference (), true );
		$result ['blockchain'] ['Metadata'] = $metadata [data] [0];
		
		$result ['blockchain'] ['json'] = Blockchain::retrieveData ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword (), trim ( $project->getReference () ) );
		
		if (isset ( $result ['blockchain'] ['json'] ['error'] )) {
			// On Error
			$this->addFlashMessage ( $result ['blockchain'] ['json'] ['error'] . ' (168)', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		} else {
			// Cast json to stdClass
			$result ['blockchain'] ['object'] = json_decode ( $result ['blockchain'] ['json'] );
		}
		
		$ballots = $result ['blockchain'] ['object']->ballots;
		
		$i = 0;
		foreach ( $ballots as $ballot ) {
			$result = $this->bcBallot ( $project, $ballot, $i, $result );
			$i ++;
		}
		return $result;
	}
	
	/**
	 * gets the ballot from blockchain
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param unknown $ballot        	
	 * @param int $i        	
	 * @return array $result
	 */
	private function bcBallot(\Goettertz\BcVoting\Domain\Model\Project $project, $ballot, $i, $result) {
		if (is_a ( $ballot, 'stdClass', true )) {
			$ballot = ( array ) $ballot;
		}
		$result ['blockchain'] ['ballots'] [$i] ['json'] = Blockchain::retrieveData ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword (), trim ( $ballot ) );
		if (is_array ( $result )) {
			if (is_string ( $result ['blockchain'] ['ballots'] [$i] ['json'] )) {
				
				$ballotO = json_decode ( $result ['blockchain'] ['ballots'] [$i] ['json'] );
				if (is_array ( $result ['txIds'] ))
					$result ['txIds'] = array_merge ( $result ['txIds'], $this->getTxidsAddress ( $project, $ballotO->walletaddress ) );
				else
					$result ['txIds'] = $this->getTxidsAddress ( $project, $ballotO->walletaddress );
				
				$result ['blockchain'] ['ballots'] [$i] ['asset'] = $ballotO->asset;
				$result ['blockchain'] ['ballots'] [$i] ['address'] = $ballotO->walletaddress;
				$result ['blockchain'] ['ballots'] [$i] ['balance'] = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getaddressbalances ( $ballotO->walletaddress );
				$result ['blockchain'] ['ballots'] [$i] ['balance'] = $result ['blockchain'] ['ballots'] [$i] ['balance'] [0] ['qty'];
				$result ['blockchain'] ['ballots'] [$i] ['end'] = $ballotO->end;
				
				$options = ( array ) $ballotO->options;
				
				$j = 0;
				foreach ( $options as $option ) {
					$result ['blockchain'] ['ballots'] [$i] ['options'] [$j] = json_decode ( $option );
					$balance = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->getaddressbalances ( $result ['blockchain'] ['ballots'] [$i] ['options'] [$j]->walletaddress );
					$result ['blockchain'] ['ballots'] [$i] ['options'] [$j]->balance = $balance [0] ['qty'];
					$j ++;
				}
			}
		}
		return $result;
	}
	
	/**
	 * getTxidsAddress
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param string $address        	
	 * @return array $result
	 */
	private function getTxidsAddress(\Goettertz\BcVoting\Domain\Model\Project $project, $address, $max = 100) {
		$result = array ();
		if ($obj = Blockchain::checkWalletAddress ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword (), $address, true )) {
			$result = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->listaddresstransactions ( $address, $max );
		}
		return $result;
	}
	
	/**
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project        	
	 * @param string $ballotAddress        	
	 * @param string $txid        	
	 *
	 * @return void
	 */
	private function publishVoting(\Goettertz\BcVoting\Domain\Model\Project $project, $ballotAddress, $txid) {
		if (is_array ( $item = Blockchain::getRpcResult ( $project->getRpcServer (), $project->getRpcPort (), $project->getRpcUser (), $project->getRpcPassword () )->publish ( $project->getStream (), substr ( $ballotAddress, 0, 10 ), bin2hex ( $txid ) ) )) {
			// $this->addFlashMessage('Item creation failed'.implode($item). ' ', 'Stream Error 267', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
	}
	
	/**
	 *
	 * @param string $ref        	
	 * @param string $hash        	
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot        	
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option        	
	 */
	private function storeVotings($ref, $hash, $ballot, $option) {
		$newvoting = new \Goettertz\BcVoting\Domain\Model\Voting ();
		$newvoting->setTxid ( $ref );
		$newvoting->setHash ( $hash );
		$newvoting->setProject ( $ballot->getProject () );
		$newvoting->setBallot ( $ballot );
		$newvoting->setCandidate ( $option );
		$newvoting->setReference ( $ballot->getReference () );
		
		$this->votingRepository->add ( $newvoting );
	}
}
?>