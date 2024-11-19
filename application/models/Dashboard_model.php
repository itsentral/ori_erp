<?php
class Dashboard_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
    
    function api_late_enginnering(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://120.29.159.11:8080/api/api_late_enginnering_count',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Cookie: ci_session=papr96l9v10256dcqk4t58bg5sivul0n'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }
}
?>