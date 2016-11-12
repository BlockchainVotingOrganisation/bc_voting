<?php
namespace Goettertz\BcVoting\Tests\Unit\Controller;
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
 * Test case for class Goettertz\BcVoting\Controller\VotingTargetsController.
 *
 * @author Louis Göttertz <com@goettertz.de>
 */
class VotingTargetsControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Goettertz\BcVoting\Controller\VotingTargetsController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Goettertz\\BcVoting\\Controller\\VotingTargetsController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllVotingTargetssFromRepositoryAndAssignsThemToView() {

		$allVotingTargetss = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$votingTargetsRepository = $this->getMock('', array('findAll'), array(), '', FALSE);
		$votingTargetsRepository->expects($this->once())->method('findAll')->will($this->returnValue($allVotingTargetss));
		$this->inject($this->subject, 'votingTargetsRepository', $votingTargetsRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('votingTargetss', $allVotingTargetss);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenVotingTargetsToView() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('votingTargets', $votingTargets);

		$this->subject->showAction($votingTargets);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenVotingTargetsToView() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newVotingTargets', $votingTargets);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($votingTargets);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenVotingTargetsToVotingTargetsRepository() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$votingTargetsRepository = $this->getMock('', array('add'), array(), '', FALSE);
		$votingTargetsRepository->expects($this->once())->method('add')->with($votingTargets);
		$this->inject($this->subject, 'votingTargetsRepository', $votingTargetsRepository);

		$this->subject->createAction($votingTargets);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenVotingTargetsToView() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('votingTargets', $votingTargets);

		$this->subject->editAction($votingTargets);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenVotingTargetsInVotingTargetsRepository() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$votingTargetsRepository = $this->getMock('', array('update'), array(), '', FALSE);
		$votingTargetsRepository->expects($this->once())->method('update')->with($votingTargets);
		$this->inject($this->subject, 'votingTargetsRepository', $votingTargetsRepository);

		$this->subject->updateAction($votingTargets);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenVotingTargetsFromVotingTargetsRepository() {
		$votingTargets = new \Goettertz\BcVoting\Domain\Model\VotingTargets();

		$votingTargetsRepository = $this->getMock('', array('remove'), array(), '', FALSE);
		$votingTargetsRepository->expects($this->once())->method('remove')->with($votingTargets);
		$this->inject($this->subject, 'votingTargetsRepository', $votingTargetsRepository);

		$this->subject->deleteAction($votingTargets);
	}
}
