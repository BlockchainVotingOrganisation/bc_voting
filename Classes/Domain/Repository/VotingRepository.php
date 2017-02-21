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
 *  
 *  Revision 138 by Louis Göttertz
 ***************************************************************/


/**
 * The repository for Votings
 */
class VotingRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	
	/**
	 * finds voting in blockchain
	 * 
	 * @param string $address
	 * @param boolean $decrypt
	 * 
	 * @return string|NULL
	 */
	public function findByAddress($address, $decrypt) {
		
		$result = NULL;
		return $result;
	}
	
}
?>