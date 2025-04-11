<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Harboens
 * @copyright Copyright (c) 2022, Harboens
 *
 * This is controller for All
 */

class All extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('All_model'));
        date_default_timezone_set("Asia/Bangkok");
    }
    public function index() {
	}
	public function list_bank($id='') {
		$results = $this->db->query("select bank,rekening,nama from ms_bank order by nama")->result();
		if(!empty($results)){
			$numb=0;
			echo '<div class="row">
			<div class="table-responsive col-md-12">
			<table id="mylistbank" class="table table-bordered table-hover table-condensed">
			<thead>
			<tr>
				<th>Bank</th>
				<th>No Rekening</th>
				<th>Nama Rekening</th>
			</tr>
			</thead>
			<tbody>
			';
			foreach($results AS $record) {
				echo '
				<tr style="cursor:pointer" onclick=\'pilihini("'.$record->bank.'","'.$record->rekening.'","'.$record->nama.'","'.$id.'")\'>
					<td>'.$record->bank.'</td>
					<td>'.$record->rekening.'</td>
					<td>'.$record->nama.'</td>
				</tr>
				';
			}
			echo '</tbody></table></div></div>';
		}else{
			echo 'No data found';
		}
    }
}