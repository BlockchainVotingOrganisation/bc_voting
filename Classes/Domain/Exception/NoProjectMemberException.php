<?php
namespace Goettertz\BcVoting\Domain\Exception;
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
	 * An Exception that is thrown, when an user tries to save timesets for a project
	 * that he is not a member of.
	 *
	 * @author     Martin Helmich <m.helmich@mittwald.de>
	 * @package    MittwaldTimetrack
	 * @subpackage Domain_Exception
	 * @version    $Id: NoProjectMemberException.php 6 2010-02-22 09:33:54Z helmich $
	 * @license    GNU Public License, version 2
	 *             http://opensource.org/licenses/gpl-license.php
	 *
	 */

Class NoProjectMemberException Extends \Goettertz\BcVoting\Domain\Exception\AbstractException {
	Protected $code = 1266412844;
	Protected $message = "You are no member of this project!";
}

?>