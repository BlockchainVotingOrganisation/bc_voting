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
 * Test case for class Goettertz\BcVoting\Controller\WalletsController.
 *
 * @author Louis Göttertz <com@goettertz.de>
 */
class WalletsControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Goettertz\BcVoting\Controller\WalletsController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Goettertz\\BcVoting\\Controller\\WalletsController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllWalletssFromRepositoryAndAssignsThemToView() {

		$allWalletss = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$walletsRepository = $this->getMock('Goettertz\\BcVoting\\Domain\\Repository\\WalletsRepository', array('findAll'), array(), '', FALSE);
		$walletsRepository->expects($this->once())->method('findAll')->will($this->returnValue($allWalletss));
		$this->inject($this->subject, 'walletsRepository', $walletsRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('walletss', $allWalletss);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenWalletsToView() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('wallets', $wallets);

		$this->subject->showAction($wallets);
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenWalletsToView() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newWallets', $wallets);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($wallets);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenWalletsToWalletsRepository() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$walletsRepository = $this->getMock('Goettertz\\BcVoting\\Domain\\Repository\\WalletsRepository', array('add'), array(), '', FALSE);
		$walletsRepository->expects($this->once())->method('add')->with($wallets);
		$this->inject($this->subject, 'walletsRepository', $walletsRepository);

		$this->subject->createAction($wallets);
	}

	/**
	 * @test
	 */
	public function editActionAssignsTheGivenWalletsToView() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('wallets', $wallets);

		$this->subject->editAction($wallets);
	}

	/**
	 * @test
	 */
	public function updateActionUpdatesTheGivenWalletsInWalletsRepository() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$walletsRepository = $this->getMock('Goettertz\\BcVoting\\Domain\\Repository\\WalletsRepository', array('update'), array(), '', FALSE);
		$walletsRepository->expects($this->once())->method('update')->with($wallets);
		$this->inject($this->subject, 'walletsRepository', $walletsRepository);

		$this->subject->updateAction($wallets);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenWalletsFromWalletsRepository() {
		$wallets = new \Goettertz\BcVoting\Domain\Model\Wallets();

		$walletsRepository = $this->getMock('Goettertz\\BcVoting\\Domain\\Repository\\WalletsRepository', array('remove'), array(), '', FALSE);
		$walletsRepository->expects($this->once())->method('remove')->with($wallets);
		$this->inject($this->subject, 'walletsRepository', $walletsRepository);

		$this->subject->deleteAction($wallets);
	}
}
