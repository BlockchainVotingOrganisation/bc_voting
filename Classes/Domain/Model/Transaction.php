<?php
namespace Goettertz\BcVoting\Domain\Model;

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
 *  
 *  Revision 128
 *  
 *  
 ***************************************************************/

/**
 * Transaction
 */
class Transaction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	
	/**
	 * @var string
	 * 
	 */
	protected $txid = '';
	
	/**
	 * @var string
	 */
	protected $sourceAccount='';
	
	/**
	 * @var string
	 */
	protected $destinationAccount = '';
	
	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Input>
	 * @cascade remove

	 */
	protected $inputs = NULL;
	
	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Goettertz\BcVoting\Domain\Model\Output>
	 * @cascade remove
	 */
	protected $outputs = NULL;

	
	/**
	 * @var long
	 */
	protected $locktime = 0;
	protected $version = 0;
	protected $updatedAt = 0;
	protected $hash	 = NULL;
	protected $exchangeRate = NULL;
	protected $memo	= NULL;

	/**
	 * 
	 * @var unknown
	 */
	protected $confidence = NULL;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $appearsInHashes = false;
	
	protected $optimalEncodingMessageSize = NULL;
}
?>
