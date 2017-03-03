<?php
namespace Goettertz\BcVoting\Controller;
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

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
 * Revision 140
 */

/**
 * TransactionController
 */
class TransactionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @param string $txid
	 */
	public function showAction(\Goettertz\BcVoting\Domain\Model\Project $project, $txid) {
		
		$isAssigned = 'false';
		$isAdmin 	= 'false';

		$data = \Goettertz\BcVoting\Service\Blockchain::retrieveData($project->getRpcServer(),$project->getRpcPort(),$project->getRpcUser(), $project->getRpcPassword(), $txid);
		
		$transaction = array('txid' => $txid);
		$data = explode("###", $data);
		$transaction['vote'] = $data[1];
		$transaction['random'] = $data[0];
		$transaction['hash'] = $data[2];
// 		$result = array('transaction' => $transaction, 'error' => NULL);
		
		
		$this->view->assign('transaction',$transaction);
 		$this->view->assign('project',$project);
	}
}

?>