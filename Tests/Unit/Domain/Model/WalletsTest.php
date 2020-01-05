<?php

namespace Goettertz\BcVoting\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Louis Göttertz <com@goettertz.de>, Louis Göttertz Internetprogrammierung
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Goettertz\BcVoting\Domain\Model\Wallets.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Louis Göttertz <com@goettertz.de>
 */
class WalletsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Goettertz\BcVoting\Domain\Model\Wallets
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Goettertz\BcVoting\Domain\Model\Wallets();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getAddressReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getAddress()
		);
	}

	/**
	 * @test
	 */
	public function setAddressForStringSetsAddress() {
		$this->subject->setAddress('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'address',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPrivateKeyReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getPrivateKey()
		);
	}

	/**
	 * @test
	 */
	public function setPrivateKeyForStringSetsPrivateKey() {
		$this->subject->setPrivateKey('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'privateKey',
			$this->subject
		);
	}
}
