<?php
namespace Goettertz\BcVoting\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015-2016 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 * Revision 121
 * - Feature Property colors
 */

use \Goettertz\BcVoting\Service\Blockchain;

use Goettertz\BcVoting\Property\TypeConverter\UploadedFileReferenceConverter;
/**
 * OptionController
 */
class OptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * optionRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\OptionRepository
	 * @inject
	 */
	protected $optionRepository = NULL;
	
	/**
	 * votingRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\VotingRepository
	 * @inject
	 */
	protected $votingRepository = NULL;
	
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
	 * assihnmentRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\AssignmentRepository
	 * @inject
	 */
	protected $assignmentRepository = NULL;
	
	/**
	 * action list
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
				
		$options = $this->optionRepository->findByBallot($ballot);
		$this->view->assign('options', $options);
		$this->view->assign('ballot', $ballot);
	}
	
	/**
	 * action new
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @param \Goettertz\BcVoting\Domain\Model\Option $newOption
	 * @return void
	 */
	public function newAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot) {
		# Check if sealed
		
		$newOption = new \Goettertz\BcVoting\Domain\Model\Option();
		$newOption->setBallot($ballot);
		
		$project = $ballot->getProject();		
		if ($ballot->getReference() === '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {
				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
				If($assignment != NULL) {
					$this->view->assign('newOption', $newOption);
					$this->view->assign('ballot', $ballot);
					$colors = array('#000' => 'Black', '#0000ff' => 'Blue', '#00ff00' => 'Green', '006400' => 'Darkgreen', '#ccc' => 'Grey', '#ff3300' => 'Orange', '#ff0000' => 'Red', '#fff' =>'White', '#ffff00'=>'Yellow');
					$this->view->assign('colors', $colors);
				}
				else {
					$this->addFlashMessage('You are no admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('show','Project','BcVoting',array('project'=>$project));
				}
			}			
		}
		else {
			$this->addFlashMessage('Ballot is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list','Option','BcVoting',array('project'=>$project));
		}

	}
	
	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeCreateAction() {
		$this->setTypeConverterConfigurationForImageUpload('newOption');
	}
	
	/**
	 * action create
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Ballot $ballot
	 * @param \Goettertz\BcVoting\Domain\Model\Option $newOption
	 * @return void
	 */
	public function createAction(\Goettertz\BcVoting\Domain\Model\Ballot $ballot, 
			\Goettertz\BcVoting\Domain\Model\Option $newOption) {
		
		$project = $ballot->getProject();
		
		if ($ballot->getReference() === '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {
				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
				If($assignment != NULL) {
// 					$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
					if ($project->getRpcServer() != '') {
						$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaccountaddress($newOption->getName());
					}
					$newOption->setWalletAddress($newAddress);
					$this->addFlashMessage('The option was created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					$newOption->setBallot($ballot);
					$this->optionRepository->add($newOption);
				}
			}
			else {
				$this->addFlashMessage('The option was not created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}					
		}
		else {
			$this->addFlashMessage('Ballot is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		}
		$this->redirect('edit', 'Ballot', NULL, array('ballot'=>$ballot));
	}
	
	/**
	 * action show
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Option $option) {
		$this->view->assign('option', $option);
	}
	
	/**
	 * action editOption
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @return void
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\Option $option) {
		if ($ballot = $option->getBallot()) {
			if ($ballot->getReference() === '') {
				if ($user = $this->userRepository->getCurrentFeUser()) {
					$isAssigned = false;
					$project = $ballot->getProject();
					$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
					If($assignment != NULL) {
						$this->view->assign('option', $option);
						$colors = array('#000' => 'Black', '#0000ff' => 'Blue', '#00ff00' => 'Green', '006400' => 'Darkgreen', '#ccc' => 'Grey', '#ff3300' => 'Orange', '#ff0000' => 'Red', '#fff' =>'White', '#ffff00'=>'Yellow');
						$this->view->assign('colors', $colors);
					}
					else {
						$this->addFlashMessage('No admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
						$this->redirect('show', 'Ballot', NULL, array('ballot'=>$ballot));
					}
				}
				else {
					$this->addFlashMessage('Not logged in!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
					$this->redirect('show', 'Ballot', NULL, array('ballot'=>$ballot));
				}
			}
			else {
				$this->addFlashMessage('Ballot is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				$this->redirect('show', 'Ballot', NULL, array('ballot'=>$ballot));
			}			
		}
		else {
			$this->addFlashMessage('No Ballot!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('list', 'Project', NULL, NULL);
		}
	}

	/**
	 * Set TypeConverter option for image upload
	 */
	public function initializeUpdateAction() {
		$this->setTypeConverterConfigurationForImageUpload('option');
	}
	
	/**
	 * action update
	 * 
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * 
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\Option $option) {
		$ballot = $option->getBallot();
		$project = $ballot->getProject();
		if ($ballot->getReference() === '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {
				
				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
				If($assignment != NULL) {
// 					$blockchain = new \Goettertz\BcVoting\Service\Blockchain();
					if ($project->getRpcServer() != '') {
						if (empty($option->getWalletAddress())) {
							$newAddress = Blockchain::getRpcResult($project->getRpcServer(), $project->getRpcPort(), $project->getRpcUser(), $project->getRpcPassword())->getaccountaddress($option->getName());
							$option->setWalletAddress($newAddress);
						}
					}
					
					$this->optionRepository->update($option);
					$this->addFlashMessage('The option was updated.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
				else {
					$this->addFlashMessage('The option was not updated: No admin!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
				}
			}
			else {
				$this->addFlashMessage('The option was not updated: Not allowed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
			
			$this->redirect('edit', 'Ballot', NULL, array(ballot=>$ballot));			
		}
		else {
			$this->addFlashMessage('Project is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('show', 'Ballot', NULL, array('ballot'=>$ballot));
		}
	}

	/**
	 * action delete
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 * @return void
	 */
	public function deleteAction(\Goettertz\BcVoting\Domain\Model\Option $option) {
		$ballot = $option->getBallot();
		$project = $ballot->getProject();
		if ($ballot->getReference() === '') {
			if ($user = $this->userRepository->getCurrentFeUser()) {				
				$isAssigned = false;
				$assignment = $user ? $project->getAssignmentForUser($user,'admin') : NULL;
				If($assignment != NULL) {
					$this->addFlashMessage('The object was deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					$this->optionRepository->remove($option);
					$this->redirect('edit', 'Ballot', NULL, array('ballot'=>$ballot));
				}
			}			
		}
		else {
			$this->addFlashMessage('Project is sealed!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$this->redirect('edit', 'Ballot', NULL, array('ballot'=>$ballot));
		}
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Option $option
	 */
	public function removeLogoAction(\Goettertz\BcVoting\Domain\Model\Option $option) {
		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\'tx_bcvoting_domain_model_option\' AND fieldname=\'logo\' AND uid_foreign = '.$option->getUid().' AND deleted = 0';
		$db = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$this->redirect('edit','Option','BcVoting',array('option'=>$option));
	}
	
	/**
	 *
	 */
	protected function setTypeConverterConfigurationForImageUpload($argumentName) {
		$uploadConfiguration = array(
				UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/tx_bc_voting',
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
