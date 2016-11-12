<?php
namespace Goettertz\BcVoting\ViewHelpers\Form;
/*                                                                      *
 *  COPYRIGHT NOTICE                                                    *
 *                                                                      *
 *  (c) 2010 Martin Helmich <m.helmich@mittwald.de>                     *
 *           Mittwald CM Service GmbH & Co KG                           *
 *           All rights reserved                                        *
 *                                                                      *
 *  This script is part of the TYPO3 project. The TYPO3 project is      *
 *  free software; you can redistribute it and/or modify                *
 *  it under the terms of the GNU General Public License as published   *
 *  by the Free Software Foundation; either version 2 of the License,   *
 *  or (at your option) any later version.                              *
 *                                                                      *
 *  The GNU General Public License can be found at                      *
 *  http://www.gnu.org/copyleft/gpl.html.                               *
 *                                                                      *
 *  This script is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of      *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the       *
 *  GNU General Public License for more details.                        *
 *                                                                      *
 *  This copyright notice MUST APPEAR in all copies of the script!      *
 *                                                                      */



	/**
	 *
	 * A ViewHelper for displaying a user role select field. In addition to the
	 * SelectViewHelper, this ViewHelper takes a "project" and a "user" argument. The
	 * "options" argument must contain an array of user role objects.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @package    MittwaldTimetrack
	 * @subpackage ViewHelpers
	 * @version    $Id: UserRoleViewHelper.php 17 2010-03-03 09:26:45Z helmich $
	 * @license    GNU Public License, version 2
	 *             http://opensource.org/licenses/gpl-license.php
	 *
	 */

Class UserRoleViewHelper Extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper {
		/**
		 *
		 * Initializes the ViewHelper arguments.
		 * @return void
		 *
		 */

	Public Function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument ( 'project', 'Tx_BcVoting_Domain_Model_Project', '', TRUE );
		$this->registerArgument ( 'user'   , 'Tx_Extbase_Domain_Model_FrontendUser'     , '', TRUE );
	}



		/**
		 *
		 * Gets the selectable options for this select field. This methods overrides the
		 * respective method in the Tx_Fluid_ViewHelpers_Form_SelectViewHelper class.
		 * @return array The selectable options for this select field.
		 *
		 */

	Protected Function getOptions() {
		$options = Array(0 => 'Kein Mitglied');
		ForEach($this->arguments['options'] As $option) {
			If($option InstanceOf Tx_BcVoting_Domain_Model_Role)
				$options[$option->getUid()] = $option->getName();
		} Return $options;
	}



		/**
		 *
		 * Determines the selected value of this select field. This method determines if
		 * the user (specified by the "user" argument) is a member of the current project
		 * (specified by the "project" argument) in a specific role.
		 * This method overrides the respective method of the
		 * Tx_Fluid_ViewHelpers_Form_SelectViewHelper class.
		 *
		 * @return int The Uid of the user role, the current user is assigned in, or 0 if
		 *             the user is not a member of the project.
		 *
		 */

	Protected Function getSelectedValue() {
		$assignment = $this->arguments['project'] ? $this->arguments['project']->getAssignmentForUser($this->arguments['user']) : NULL;
		Return $assignment ? $assignment->getRole()->getUid() : 0;
	}



		/**
		 *
		 * Gets the name of the form field. This method overrides the respective method
		 * of the Tx_Fluid_ViewHelpers_Form_SelectViewHelper class.
		 *
		 * @return string The form field name
		 *
		 */
	Protected Function getName() {
		Return parent::getName().'['.$this->arguments['user']->getUid().']';
	}
}

?>
