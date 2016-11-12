<?php
namespace Goettertz\BcVoting\Service;
/*
 COPYRIGHT

 Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

 This file is part of JSON-RPC PHP.

 JSON-RPC PHP is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 JSON-RPC PHP is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with JSON-RPC PHP; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * 
 * @author louis
 * Rev. 80 01.09.2016
 */

interface Rpc {
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 */
	public function getRpcResult(\Goettertz\BcVoting\Domain\Model\Project $project);
}


class Blockchain {
	
	/**
	 * getRpcResult ob static gut ist?
	 * 
	 * verrichtet der Service nur Arbeit? ja
	 * wird der Service nur einmal während der Laufzeit benötigt? ja
	 * also ist static gut!
	 *
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return \Goettertz\BcVoting\Service\jsonRPCClient $blockchain
	 */
	public static function getRpcResult(\Goettertz\BcVoting\Domain\Model\Project $project) {
		
		$rpcServer = $project->getRpcServer();
		$rpcUser = $project->getRpcUser();
		$rpcPassword = $project->getRpcPassword();
		$rpcPort = $project->getRpcPort();
		
		try
		{
			if ($rpcServer > NULL)
			{
				return $blockchain =  new \Goettertz\BcVoting\Service\jsonRPCClient('http://'.$rpcUser.':'.$rpcPassword.'@'.$rpcServer.':'.$rpcPort.'/');
			}
			else
			{
				return NULL;
			}
				
		}
		catch (Exception $e)
		{
// 			$blockchain = array('error' => $e);
			return NULL;
		}
	}
	
	/**
	 * gets the balance of native currency
	 * @param string $username
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * @return double
	 */
	public function getUserBalance($username, \Goettertz\BcVoting\Domain\Model\Project $project) {
		if ($this->getRpcResult($project)) {
			return $balance = $this->getRpcResult($project)->getbalance($username);
		}
		else return 0;				
	}
}
?>
