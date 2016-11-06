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
 * Rev. 118
 */

interface Rpc {
	
	/**
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 */
	public static function getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword);
}


/**
 * @author louis
 * 
 * Blockchain Communication Service
 *
 */
class Blockchain implements Rpc {
	
	/**
	 * getRpcResult ob static gut ist?
	 * 
	 * verrichtet der Service nur Arbeit? ja
	 * wird der Service nur einmal während der Laufzeit benötigt? ja
	 * also ist static gut!
	 *
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * 
	 * @return \Goettertz\BcVoting\Service\jsonRPCClient $blockchain
	 */
	public static function getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword) {		
		return $blockchain =  new \Goettertz\BcVoting\Service\jsonRPCClient('http://'.$rpcUser.':'.$rpcPassword.'@'.$rpcServer.':'.$rpcPort.'/');
	}
	
	/**
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * 
	 * @return string
	 */
	public static function getNewAddress($rpcServer, $rpcPort, $rpcUser, $rpcPassword) {
		return self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->getnewaddress();
	}
	
	/**
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * @param string $fromaddress
	 * @param string $toaddress
	 * @param double $assetAmount
	 * @param string $data
	 * @return mixed
	 */
	public static function storeData($rpcServer, $rpcPort, $rpcUser, $rpcPassword, $fromaddress, $toaddress, $assetAmount, $data) {
		$data = bin2hex($data);
		return self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->sendwithmetadatafrom($fromaddress,$toaddress,$assetAmount,$data);
	}
	
	/**
	 * retrieves data from blockchain
     *
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * @param string $txid
	 * 
	 * @return array|string
	 */
	public static function retrieveData($rpcServer, $rpcPort, $rpcUser, $rpcPassword, $txid) {
		$data = self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->getrawtransaction(trim($txid), 1);
		
		if (is_string($data['error'])) { return $data; }
		else { return hex2bin($data['data'][0]); }
	}
	
	/**
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * 
	 * @param string $fromAddress
	 * @param unknown $asset
	 * @return mixed
	 */
	public static function getAssetBalanceFromAddress($rpcServer, $rpcPort, $rpcUser, $rpcPassword, $fromAddress, $assetref = NULL) {
		
		if($assetref)
		{
			$data = self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->getmultibalances($fromAddress, array($assetref));
			return $data[$fromAddress][0]['qty'];
		}
		else {
			$data = self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->getmultibalances($fromAddress);
			return $data;
		}
			
	}
	
	/**
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * @param string $toAddress
	 * @param string $ssset
	 * @param double $amount
	 */
	public static function sendassettoaddress($rpcServer, $rpcPort, $rpcUser, $rpcPassword, $toAddress, $asset, $amount) {
		return self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->sendassettoaddress($toAddress, $asset, $amount);
	}
}
?>
