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
 * Revision 54
 */

/**
 * Argument
 */
class Argument extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * 
	 * @var string
	 */
	protected $name = '';
	
	/**
	 * 
	 * @var string
	 */
	protected $text = '';
	
	/**
	 * 
	 * @var \Goettertz\BcVoting\Domain\Model\Option:
	 */
	protected $prooption;
	
	/**
	 * project
	 *
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 */
	protected $project = NULL;
	
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
	 * Returns the text
	 *
	 * @return string $text
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Sets the text
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text) {
		$this->text = $text;
	}
	
	/**
	 * returns the option
	 */
	public function getProoption() {
		return $this->prooption;
	}
	
	/**
	 * Sets the option
	 *
	 * @param integer $option
	 * @return void
	 */
	public function setProoption($option) {
		$this->prooption = $option;
	}
	
	/**
	 * Returns the project
	 *
	 * @return \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function getProject() {
		return $this->project;
	}
	
	/**
	 * Sets the project
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return void
	 */
	public function setProject(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->project = $project;
	}
	
}