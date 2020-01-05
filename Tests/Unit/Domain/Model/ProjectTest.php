<?php

namespace Goettertz\BcVoting\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Louis Göttertz <info2015@goettertz.de>, goettertz.de
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
 * Test case for class \Goettertz\BcVoting\Domain\Model\Project.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Louis Göttertz <info2015@goettertz.de>
 */
class ProjectTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Goettertz\BcVoting\Domain\Model\Project
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Goettertz\BcVoting\Domain\Model\Project();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getNameReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getName()
		);
	}

	/**
	 * @test
	 */
	public function setNameForStringSetsName() {
		$this->subject->setName('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'name',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getDescription()
		);
	}

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() {
		$this->subject->setDescription('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'description',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getStartReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getStart()
		);
	}

	/**
	 * @test
	 */
	public function setStartForStringSetsStart() {
		$this->subject->setStart('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'start',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getEndReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getEnd()
		);
	}

	/**
	 * @test
	 */
	public function setEndForStringSetsEnd() {
		$this->subject->setEnd('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'end',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getAssignmentsReturnsInitialValueForAssignment() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getAssignments()
		);
	}

	/**
	 * @test
	 */
	public function setAssignmentsForObjectStorageContainingAssignmentSetsAssignments() {
		$assignment = new \Goettertz\BcVoting\Domain\Model\Assignment();
		$objectStorageHoldingExactlyOneAssignments = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneAssignments->attach($assignment);
		$this->subject->setAssignments($objectStorageHoldingExactlyOneAssignments);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneAssignments,
			'assignments',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addAssignmentToObjectStorageHoldingAssignments() {
		$assignment = new \Goettertz\BcVoting\Domain\Model\Assignment();
		$assignmentsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$assignmentsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($assignment));
		$this->inject($this->subject, 'assignments', $assignmentsObjectStorageMock);

		$this->subject->addAssignment($assignment);
	}

	/**
	 * @test
	 */
	public function removeAssignmentFromObjectStorageHoldingAssignments() {
		$assignment = new \Goettertz\BcVoting\Domain\Model\Assignment();
		$assignmentsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$assignmentsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($assignment));
		$this->inject($this->subject, 'assignments', $assignmentsObjectStorageMock);

		$this->subject->removeAssignment($assignment);

	}
}
