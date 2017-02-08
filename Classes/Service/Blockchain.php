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
 * Rev. 133
 */

define('const_issue_custom_fields', 10);

use \Goettertz\BcVoting\Service\jsonRPCClient;

class Blockchain {
	
	/**
	 * @var string
	 */
	protected $multichain_chain = '';
	
	/**
	 * @param unknown $chain
	 */
	public function set_multichain_chain($chain)
	{
		$this->multichain_chain=$chain;
	}
	
	/**
	 * @var unknown
	 */
	protected $multichain_labels;
	
	/**
	 * @var unknown
	 */
	protected $multichain_getinfo;
	
	/**
	 * @var integer
	 */
	protected $multichain_max_data_size = 3072;
	
	/**
	 * @param \Goettertz\BcVoting\Domain\Model\Project $project
	 * 
	 * @return void
	 */
	public function setConfig(\Goettertz\BcVoting\Domain\Model\Project $project) {
		$this->multichain_chain['rpchost'] = $project->getRpcServer();
		$this->multichain_chain['rpcport'] = $project->getRpcPort();
		$this->multichain_chain['rpcport'] = $project->getRpcUser();
		$this->multichain_chain['rpcpassword'] = $project->getRpcPassword();
	}
	
	public function output_rpc_error($error)
	{
		echo '<div class="bg-danger" style="padding:1em;">Error: '.html($error['code']).'<br/>'.html($error['message']).'</div>';
	}
	
	public function no_displayed_error_result(&$result, $response)
	{
		if (is_array($response['error'])) {
			$result=null;
			$this->output_rpc_error($response['error']);
			return false;
	
		} else {
			$result=$response['result'];
			return true;
		}
	}
	
	public function json_rpc_send($host, $port, $user, $password, $method, $params=array())
	{
		$url='http://'.$host.':'.$port.'/';
		$agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
	
		$payload=json_encode(array(
				'id' => time(),
				'method' => $method,
				'params' => $params,
		));
	
		//	echo '<PRE>'; print_r($payload); echo '</PRE>';
	
		$ch=curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $user.':'.$password);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: '.strlen($payload)
		));
	
		$response=curl_exec($ch);
	
// 		echo '<PRE>'; print_r($response); echo '</PRE>';
	
		$result=json_decode($response, true);
	
		if (!is_array($result)) {
			$info=curl_getinfo($ch);
			$result=array('error' => array(
					'code' => 'HTTP '.$info['http_code'],
					'message' => strip_tags($response).' '.$url
			));
		}
	
		return $result;
	}
		
		
	/**
	 * @param string $method
	 * @return mixed
	 */
	public function multichain($method) // other params read from func_get_args()
	{
		$args=func_get_args();
	
		return self::json_rpc_send($this->multichain_chain['rpchost'], $this->multichain_chain['rpcport'], $this->multichain_chain['rpcuser'],
				$this->multichain_chain['rpcpassword'], $method, array_slice($args, 1));
	}
	
	public function multichain_max_data_size()
	{
// 		global $multichain_max_data_size;
	
		if (!isset($this->multichain_max_data_size))
			if ($this->no_displayed_error_result($params, $this->multichain('getblockchainparams')))
				$this->multichain_max_data_size=min(
						$params['maximum-block-size']-80-320,
						$params['max-std-tx-size']-320,
						$params['max-std-op-return-size']
						);
	
				return $multichain_max_data_size;
	}
	
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
		$blockchain =  new jsonRPCClient('http://'.$rpcUser.':'.$rpcPassword.'@'.$rpcServer.':'.$rpcPort.'/');
// 		if (is_string($blockchain['error'])) {
			
// 		}
		return $blockchain;
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
	
	
	/**
	 * @param unknown $address
	 * @return mixed|string[][]
	 */
	public function validateAddress($address) {
// 		$result = array('isvalid' => false);
		$result = self::multichain('validateaddress', $address);
		return $result;
	}
	
	/**
	 * @param unknown $issueasset
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $qty
	 * @param unknown $units
	 */
	public function issueAsset($issueasset, $from, $to, $qty, $units) {
				
		$max_upload_size=$this->multichain_max_data_size()-512; // take off space for file name and mime type
		
		if (@$_POST['issueasset']) {
			$multiple=(int)round(1/$_POST['units']);
		
			$addresses=array( // array of addresses to issue units to
					$_POST['to'] => array(
							'issue' => array(
									'raw' => (int)($_POST['qty']*$multiple)
							)
					)
			);
		
			$custom=array();
		
			for ($index=0; $index<const_issue_custom_fields; $index++)
				if (strlen(@$_POST['key'.$index]))
					$custom[$_POST['key'.$index]]=$_POST['value'.$index];
		
					$datas=array( // to create array of data items
							array( // metadata for issuance details
									'name' => $_POST['name'],
									'multiple' => $multiple,
									'open' => true,
									'details' => $custom,
							)
					);
		
					$upload=@$_FILES['upload'];
					$upload_file=@$upload['tmp_name'];
		
					if (strlen($upload_file)) {
						$upload_size=filesize($upload_file);
		
						if ($upload_size>$max_upload_size) {
							echo '<div class="bg-danger" style="padding:1em;">Uploaded file is too large ('.number_format($upload_size).' > '.number_format($max_upload_size).' bytes).</div>';
							return;
		
						} else {
							$datas[0]['details']['@file']=fileref_to_string(2, $upload['name'], $upload['type'], $upload_size); // will be in output 2
							$datas[1]=bin2hex(file_to_txout_bin($upload['name'], $upload['type'], file_get_contents($upload_file)));
						}
					}
		
					if (!count($datas[0]['details'])) // to ensure it's converted to empty JSON object rather than empty JSON array
						$datas[0]['details']=new stdClass();
		
						$success=no_displayed_error_result($issuetxid, multichain('createrawsendfrom', $_POST['from'], $addresses, $datas, 'send'));
		
						if ($success)
							output_success_text('Asset successfully issued in transaction '.$issuetxid);
		}
		
		$getinfo=multichain_getinfo();
		
		$issueaddresses=array();
		$keymyaddresses=array();
		$receiveaddresses=array();
		$labels=array();
		
		if (no_displayed_error_result($getaddresses, multichain('getaddresses', true))) {
		
			if (no_displayed_error_result($listpermissions,
					multichain('listpermissions', 'issue', implode(',', array_get_column($getaddresses, 'address')))
					))
				$issueaddresses=array_get_column($listpermissions, 'address');
		
				foreach ($getaddresses as $address)
					if ($address['ismine'])
						$keymyaddresses[$address['address']]=true;
		
						if (no_displayed_error_result($listpermissions, multichain('listpermissions', 'receive')))
							$receiveaddresses=array_get_column($listpermissions, 'address');
		
							$labels=multichain_labels();
		}		
	}
	
	/**
	 * checkWalletAddress
	 * 
	 * checks if address is inside local wallet and creates a watchonly address if not and $createWatchonlyAddress is true.
	 * 
	 * returns true if address exists or watchonly address is imported.
	 *  
	 * @param string $rpcServer
	 * @param string $rpcPort
	 * @param string $rpcUser
	 * @param string $rpcPassword
	 * @param string $address
	 * @param bool $createWatchonlyAddress
	 * @return bool
	 */
	public static function checkWalletAddress($rpcServer, $rpcPort, $rpcUser, $rpcPassword, $address, $createWatchonlyAddress = FALSE) {
		if ($walletaddresses =  self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->getaddresses())
		foreach ($walletaddresses AS $myaddress) {
			if ($myaddress === $address) {
				return true;
			}
		}
		# else if not found: create watchonly address
		if ($createWatchonlyAddress === true)
		if (self::getRpcResult($rpcServer, $rpcPort, $rpcUser, $rpcPassword)->importaddress($address) === false)
			return true;
		# else if import failed
		return false;
	}
}
?>
