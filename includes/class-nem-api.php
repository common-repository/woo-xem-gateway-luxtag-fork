<?php

class NemApi {

	private static $instance;

	private static $servers = array(
		'bigalice3.nem.ninja',
		'alice2.nem.ninja',
		'go.nem.ninja'
	);



	private static $testservers = array(
		'bob.nem.ninja',
		'104.128.226.60',
		'192.3.61.243'
	);

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/*
	 * todo: Much todo here, only get from last got hash. But lets keep it stupid meanwhile.
	 * */
	public static function get_latest_transactions( $address , $test = false) {
		//test params
        if($test){
            $servers = self::$testservers;
        }else{
            $servers = self::$servers;
        }
		$address = str_replace('-','', $address);


		//Need api to support transactions forward
		/*if(!empty(WC()->session->get('last_nem_transaction_hash'))){
			$last_nem_transaction_hash = WC()->session->get('last_nem_transaction_hash');
			//Test
			$last_nem_transaction_hash = "4f4c5368d1f630743518755ea9aa3549e02e058cb6331fff892646aa41cfca0e";
			$address = $address.'&hash='.$last_nem_transaction_hash;
		}*/

		$path = ':7890/account/transfers/incoming?address='.$address;
		foreach ($servers as $server){
			$res = wp_remote_get('http://'.$server.$path);
			$res = rest_ensure_response($res);
			if($res->status === 200){
				break;
			}
		}
		if(empty($res) && empty($res->status) && $res->status !== 200){
			return false;
		}
		$transactions = json_decode($res->data['body']);
		if(is_object($transactions) && !empty($transactions->data)){
			//Need api to support transactions forward
			//WC()->session->set('last_nem_transaction_hash', $transactions->data[0]->meta->hash->data);
			return $transactions->data;
		}
		return false;
	}


}