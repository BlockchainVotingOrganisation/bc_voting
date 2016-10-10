<?php
namespace Goettertz\BcVoting\Domain\Repository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 * The repository for (fe-)users
 * 
 * Rev. 90
 */
class UserRepository extends \TYPO3\CMS\Extbase\Persistence\Repository  {

	/**
	 * Gets the currently logged in frontend user, 
	 * or NULL if no user is logged in.
	 * 
	 * @return \Goettertz\BcVoting\Domain\Model\User|NULL  
	 *
	 */
	public Function getCurrentFeUser() {
		try {
			Return intval($GLOBALS['TSFE']->fe_user->user['uid']) > 0
			? $this->findByUid(intval($GLOBALS['TSFE']->fe_user->user['uid']))
			: NULL;
		} catch (Exception $e) {
	
			# Error written to protocol
	
			return NULL;
		}
	}
}
?>
