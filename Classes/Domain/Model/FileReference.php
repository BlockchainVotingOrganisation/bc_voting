<?php
namespace Goettertz\BcVoting\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel
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
 * Class FileReference
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {
	
	
	/**
	 * 
	 * @var integer
	 */
	protected $uid;
	


	/**
	 * Uid of a sys_file
	 *
	 * @var integer
	 */
	protected $originalFileIdentifier;

	/**
	 * @param \TYPO3\CMS\Core\Resource\FileReference $originalResource
	 */
	public function setOriginalResource(\TYPO3\CMS\Core\Resource\FileReference $originalResource) {
		$this->originalResource = $originalResource;
		$this->originalFileIdentifier = (int)$originalResource->getOriginalFile()->getUid();
	}
	
	/**
	 * @return \TYPO3\CMS\Core\Resource\FileReference
	 */
	public function getOriginalResource()
	{
		if ($this->originalResource === null) {
			$this->originalResource = \TYPO3\CMS\Core\Resource\ResourceFactory::getInstance()->getFileReferenceObject($this->getUid());
		}
		return $this->originalResource;
	}
	/**
	 * 
	 * @param string $table
	 * @param string $field
	 * @param integer $uid
	 */
	public function delete($table, $field, $uid) {
 		$sql = 'UPDATE sys_file_reference SET deleted=1 WHERE tablenames=\''.$table.'\' AND fieldname=\''.$field.'\' AND uid_foreign = '.$uid.' AND deleted = 0';
 		$db = $GLOBALS['TYPO3_DB']->sql_query($sql);
	}

}
?>