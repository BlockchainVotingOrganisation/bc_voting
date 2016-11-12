<?php
namespace Goettertz\BcVoting\Domain\Model;

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
 * Revision 48
 */

/**
 * Category
 * 
 * Class for storing project categories, e.g. survey, petition, election etc.
 * 
 */
class Category extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name = '';
	
	/**
	 * description
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $description = '';
	
	/**
	 * closed - closed user group
	 * @var boolean
	 */
	protected $closed;
	
	/**
	 * $ulterior - remains secret until end of period
	 * @var boolean
	 */
	protected $ulterior;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}	
	
	/**
	 * @return boolean
	 */
	public function getUlterrior() {
		return $this->ulterior;
	}
	
	/**
	 * @param bool $ulterior#
	 * @return void
	 */
	public function setUlterior($ulterior) {
		$this->ulterior = $ulterior;
	}
}
?>