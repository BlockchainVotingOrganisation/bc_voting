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
 *  
 *  Rev.: 58
 ***************************************************************/

/**
 * Options for voters
 */
class Element extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 */
	protected $project = NULL;
	
	/**
	 * @var \Goettertz\BcVoting\Domain\Model\Elementtype
	 */
	protected $type = NULL;
	
	/**
	 * @var integer
	 */
	protected $sort = 0;
	
	/**
	 * @return \Goettertz\BcVoting\Domain\Model\Project
	 */
	public function getProject() {
		return $this->project;
	}
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function setProject($project) {
		$this->project = $project;
	}
	/**
	 * @return integer
	 */
	public function getSort() {
		return $this->sort;
	}
	
	/**
	 * @param integer $sort
	 * @return void
	 */
	public function setSort($sort) {
		$this->sort = $sort;
	}
}
?>