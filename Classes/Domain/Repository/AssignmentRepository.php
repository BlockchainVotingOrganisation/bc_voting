<?php
namespace Goettertz\BcVoting\Domain\Repository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 * The repository for Assignments
 */
class AssignmentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
	/**
	 * @param integer $project
	 * @param integer $user
	 * @param integer $role
	 */
	public function findDuplicates($project,$user,$role) {
		$sql = 'SELECT a.uid
					FROM tx_bcvoting_domain_model_assignment as a
				WHERE a.user='.$user.' AND a.project='.$project.' AND a.role='.$role;
	
	
		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(true);
		$query->statement($sql);
	
		return $query->execute();
	}
	
	/**
	 * @param integer $project
	 * @param integer $user
	 * @param integer $role
	 */
	public function deleteDuplicates($project,$user,$role) {
		$sql = 'Delete FROM tx_bcvoting_domain_model_assignment 
				WHERE user='.$user.' AND project='.$project.' AND role='.$role;
		
		$query = $this->createQuery();
		$query->getQuerySettings()->setReturnRawQueryResult(true);
		$query->statement($sql);
	
		return $query->execute();
	}
}
?>