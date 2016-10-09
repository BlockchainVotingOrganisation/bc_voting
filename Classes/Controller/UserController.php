<?php
namespace Goettertz\BcVoting\Controller;
ini_set("display_errors", 1);
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
 * Rev. 110
 */
use Goettertz\BcVoting\Service\Blockchain;
/**
 * UserController
 */
class UserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * userRepository
	 *
	 * @var \Goettertz\BcVoting\Domain\Repository\UserRepository
	 * @inject
	 */
	protected $userRepository = NULL;
	
	/**
	 * action list
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function listAction(\Goettertz\BcVoting\Domain\Model\Project $project = NULL) {
		
		if ($project !== null)
		{
			$this->view->assign('project', $project);
			$this->view->assign('members', $project->getAssignments());
		}
		else $this->view->assign('members', $this->userRepository->findAll());
	}
	
	/**
	 * action edit
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 */
	public function editAction(\Goettertz\BcVoting\Domain\Model\User $user) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			if ($feuser === $user) {
				$this->view->assign('user', $user);
			}
		}
	}
	
	/**
	 * action update
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function updateAction(\Goettertz\BcVoting\Domain\Model\User $user) {
		$this->userRepository->update($user);
		$this->addFlashMessage('The object was updated', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->redirect('edit');
	}
	/**
	 * action show
	 * @param \Goettertz\BcVoting\Domain\Model\User $user
	 * @return void
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\User $user, \Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($feuser = $this->userRepository->getCurrentFeUser()) {
			$assignment = $feuser ? $project->getAssignmentForUser($feuser, 'admin') : NULL;
			If($assignment != NULL) {
				$this->view->assign('user', $user);
				$this->view->assign('project', $project);
				$this->view->assign('isAdmin', 'true');

				$assignments = $user ? $project->getAssignmentForUser($user) : NULL;
				
				$this->view->assign('assignments', $assignments);
				
				# Nur, wenn BC!!!
				if (!empty($project->getRpcServer())) {
					
					$address = NULL;
					if (!empty($assignments->getWalletAddress())) $address = $assignments->getWalletAddress();
					
					if (is_string($address)) {
						$transactions = array();
						$newtransactions = Blockchain::getRpcResult($project)->listaddresstransactions($address, 10);
						if (!is_string($newtransactions['error'])) {
							$transactions = array_merge($transactions, $newtransactions);
							$this->view->assign('transactions', $transactions);
						}
						$assets = Blockchain::getAssetBalanceFromAddress($project, $address);
					}
					$this->view->assign('assets', $assets);
				}
			}
		}
	}	
	/**
	 * action showRegistration
	 * @param \Goettertz\BcVoting\Domain\Model\User $newUser
	 * @return void
	 */
	public function actionShowRegistration(\Goettertz\BcVoting\Domain\Model\User $newUser) {
		$this->view->assign('user', $newUser);
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function uploadAction(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->addFlashMessage('Try upload...', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		if (strval($_FILES['tx_bcvoting_project']['tmp_name']['file']['csv']))
		{
			//Import uploaded file to Database
			try {
				$this->addFlashMessage('Upload ok.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		
				$handle = fopen($_FILES['tx_bcvoting_project']['tmp_name']['file']['csv'], "r");
		
				$i = 0;
				// zeilenweise lesen
				$records = array();
				while (($data = fgetcsv($handle, 1000000, ";")) != FALSE) {
					$records[$i] = $data;
					$i++;
				}
			} catch (Exception $e) {
				$this->addFlashMessage($e, 'CSV import failed!', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			}
		
			$csvColumns = $this->getColumnNames($records);
		}		

		$this->view->assign('project', $project);
		$this->view->assign('files', $_FILES);
		$this->view->assign('options', $csvColumns);
		$this->view->assign('data', $records);
	}
	
	/**
	 * @param array $data
	 */
	public function importAction($data) {
		if($this->request->hasArgument('process'))
		{
			if ($this->request->hasArgument('process') === TRUE) {	
				if ($options  = $this->getColumnNames($data)) {
					$this->addFlashMessage('Process...', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
					// Process Data
					$records = array();
					for ($i = 1; $i < count($data); $i++)
					{
						// Spalten belegen (assoziatives Array anlegen)
						$records[$i]['username'] = $data[$i][$fieldnames['username']];
						$records[$i]['password'] = $data[$i][$fieldnames['password']];
						$records[$i]['email'] = $data[$i][$fieldnames['email']];
					
						// $records[$i]['price'] = $data[$i][$fieldnames['price']];
						// Neues Object
						$user = new \Goettertz\BcVoting\Domain\Model\User();
					
						// Titel
						$user->setUsername('test');
					
						// Description
						$user->setPassword($this->saltedPassword($records[$i]['password']));
					
						// Quantity
						$user->setEmailAddress($records[$i]['email']);
					
						// Cart-Object hinzufügen
						$this->userRepository->add($user);
					
						// now persist all to have the possibility to use the new ITEM-UID p.e. in view...
						$persistenceManager = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
						$persistenceManager->persistAll();
					}					
				}
				else {
					$this->addFlashMessage('No input data!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
				}
			}
		}
		$this->view->assign('project', $project);
	}
	
	protected function saltedPassword($password) {		
		$saltedPassword = '';
		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('saltedpasswords')) {
			if (\TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility::isUsageEnabled('FE')) {
				$objSalt = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance(NULL);
				if (is_object($objSalt)) {
					return $saltedPassword = $objSalt->getHashedPassword($password);
				}
			}
		}
	}
	
	/**
	 * Extract first line values
	 *
	 * @param array $records
	 * @return array
	 */
	protected function getColumnNames($records) {
		foreach ($records[0] AS $key => $value) {
			$csvColumns[] = $value;
		}
		return $csvColumns;
	}
	

	private function insertUser($user) {
	
		$table = 'fe_users';
		$user['pid'] = $this->importPID;
	
		if ($this->encryptPasswords) {
			$user['password'] = md5($user['password']);
		}
	
		$user['usergroup'] = $this->userGroup;
		$fields_values = $user;
		$GLOBALS['TYPO3_DB']->exec_INSERTquery( $table, $fields_values, $no_quote_fields=FALSE );
	}
	
	/* compare user with typo3 database */
	private function checkUsername($username) {
			
		$selectFields='username';
		$fromTable='fe_users';
		$whereClause='username="'.$username.'" and deleted=0 and pid='.$this->importPID;
		$groupBy='';
		$orderBy=''; // 'field(uid,' . $orderedUidList . ')';
		$limit='1';
	
		$recordList=$GLOBALS['TYPO3_DB']->exec_SELECTquery( $selectFields
				, $fromTable
				, $whereClause
				, $groupBy
				, $orderBy
				, $limit
				);
	
		if ($GLOBALS['TYPO3_DB']->sql_num_rows( $recordList ) == 1) {
			return true;
		} else {
			return false;
		}
	}
}
?>
