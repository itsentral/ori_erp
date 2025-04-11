<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Master_machine extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->model('master_model');

    $this->load->database();
    // $this->load->library('Mpdf');
        if(!$this->session->userdata('isORIlogin')){
      redirect('login');
    }
  }

  public function index(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)));
  		$Arr_Akses			= getAcccesmenu($controller);
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}

  		$productN		= $this->uri->segment(3);
  		$menu_akses		= $this->master_model->getMenu();




  		$data = array(
  			'title'			=> 'Indeks Of Master Machine',
  			'action'		=> 'index',
  			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		history("View Master ".$productN);
  		$this->load->view('Master_machine/index',$data);
	}

  public function standard_step(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)));
  		$Arr_Akses			= getAcccesmenu($controller);
      $menu_akses		= $this->master_model->getMenu();
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}

      $data = array(
  			'title'			=> 'Indeks Of Standard Step',
  			'action'		=> 'index',
        'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		$this->load->view('Cycletime/index_standard_step',$data);
	}

  public function standard_time(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)));
  		$Arr_Akses			= getAcccesmenu($controller);
      $menu_akses		= $this->master_model->getMenu();
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}

      $data = array(
  			'title'			=> 'Indeks Of Standard Step',
  			'action'		=> 'index',
        'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		$this->load->view('Cycletime/index_standard_time',$data);
	}

  //JSON Master
	public function getDataJSON(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
			$requestData['sts_mesin'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		$data	= array();
		$urut1  = 1;
        $urut2  = 0;
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'desc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

			$nestedData 	= array();
				$detail = "";
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_mesin']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_mesin']." (".$row['capacity'].")</div>";
			$nestedData[]	= "<div align='center'>".$row['capacity']."</div>";
			$nestedData[]	= "<div align='center'>".$row['unit']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['machine_price'],0)."</div>";
			$nestedData[]	= "<div align='center'>".$row['utilization_estimate']."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['depresiation_per_month'],0)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['machine_cost_per_hour'],2)."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update	= "<a id='StepEdit' data-id='".$row['id_mesin']."' data-nm_product='".$row['nm_mesin']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}if($Arr_Akses['update']=='1'){
				$delete	= "<a id='del_type' data-id='".$row['id_mesin']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
			}


			$nestedData[]	= "<div align='center'>
									<a id='StepDetail' data-id='".$row['id_mesin']."' data-nm_product='".$row['nm_mesin']."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>
									".$update."
									".$delete."
								</div>";
			$data[] = $nestedData;
            $urut1++;
            $urut2++;
		}

		$json_data = array(
			"draw"            	=> intval( $requestData['draw'] ),
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function queryDataJSON($sts_mesin, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		// echo $series."<br>";
		// echo $group."<br>";
		// echo $komponen."<br>";
		$where_sts = "";
		if(!empty($sts_mesin)){
			$where_sts = " AND sts_mesin = 'Y' ";
		}

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				machine a,
				(SELECT @row:=0) r
			WHERE 1=1
				".$where_sts."
				AND (
				a.id_mesin LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_mesin LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id_mesin',
			1 => 'no_mesin',
			2 => 'nm_mesin',
			3 => 'capacity',
			4 => 'unit',
			5 => 'utilization_estimate',
			6 => 'depresiation_per_month',
			7 => 'machine_cost_per_hour'
		);

		$sql .= " ORDER BY nm_mesin ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

  public function getDataJSON2(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1)));
  		$Arr_Akses			= getAcccesmenu($controller);
  		$requestData	= $_REQUEST;
  		$fetch			= $this->queryDataJSON(
  			$requestData['series'],
  			$requestData['group'],
  			$requestData['komponen'],
  			$requestData['search']['value'],
  			$requestData['order'][0]['column'],
  			$requestData['order'][0]['dir'],
  			$requestData['start'],
  			$requestData['length']
  		);
  		$totalData		= $fetch['totalData'];
  		$totalFiltered	= $fetch['totalFiltered'];
  		$query			= $fetch['query'];

  		$data	= array();
  		$urut1  = 1;
          $urut2  = 0;
  		foreach($query->result_array() as $row)
  		{
  			$total_data     = $totalData;
              $start_dari     = $requestData['start'];
              $asc_desc       = $requestData['order'][0]['dir'];
              if($asc_desc == 'asc')
              {
                  $nomor = $urut1 + $start_dari;
              }
              if($asc_desc == 'desc')
              {
                  $nomor = ($total_data - $start_dari) - $urut2;
              }

  			$nestedData 	= array();
  				$detail = "";
  			$nestedData[]	= "<div align='center'>".$nomor."</div>";
  			$nestedData[]	= "<div align='left'>".$row['standard_code']."</div>";
  			// $nestedData[]	= "<div align='center'>".$row['series']."</div>";
  			$nestedData[]	= "<div align='left'>".$row['product_parent']." ".$detail."</div>";
  			$nestedData[]	= "<div align='center'>".$row['pn']."</div>";
  			$nestedData[]	= "<div align='center'>".$row['liner']."</div>";
  			$nestedData[]	= "<div align='center'>".$row['diameter']."</div>";
  			$nestedData[]	= "<div align='center'>".$row['diameter2']."</div>";
        $nestedData[]	= "<div align='center'>".$row['total_time']."</div>";
        $nestedData[]	= "<div align='center'>".$row['man_power']."</div>";
        $nestedData[]	= "<div align='center'>".$row['man_hours']."</div>";
        $nestedData[]	= "<div align='center' >
  									<a id='TimeDetail' data-id='".$row['id']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>

                    <a id='TimeEdit' data-id='".$row['id']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>

                    <a id='TimeDelete' data-id='".$row['id']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>

  									</div>";
  			$data[] = $nestedData;
              $urut1++;
              $urut2++;
  		}

  		$json_data = array(
  			"draw"            	=> intval( $requestData['draw'] ),
  			"recordsTotal"    	=> intval( $totalData ),
  			"recordsFiltered" 	=> intval( $totalFiltered ),
  			"data"            	=> $data
  		);

  		echo json_encode($json_data);
	}

	public function queryDataJSON2($series, $group, $komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		// echo $series."<br>";
		// echo $group."<br>";
		// echo $komponen."<br>";
		$where_series = "";
		if(!empty($series)){
			$where_series = " AND a.series = '".$series."' ";
		}

		$where_group = "";
		if(!empty($group)){
			$where_group = " AND a.product_parent = '".$group."' ";
		}

		$where_komponen = "";
		if(!empty($komponen)){
			$where_komponen = " AND a.product_parent = '".$komponen."' ";
		}

		$sql = "
			SELECT
				a.*, b.nm_customer
			FROM
				cycletime_default a
				LEFT JOIN customer b ON b.id_customer=a.kd_cust
			WHERE 1=1
				".$where_group."
				".$where_series."
				".$where_komponen."
				AND a.deleted ='N' AND a.kd_cust IS NULL AND (
				a.standard_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'standard_code',
			2 => 'product_parent',
			3 => 'pn',
			4 => 'liner',
			5 => 'diameter',
			6 => 'diameter2',
			7 => 'rev'
		);

		$sql .= " ORDER BY a.standard_code DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

  public function modalAdd(){
		$this->load->view('Master_machine/modalAdd');
	}

  public function modalAdd_Time(){
		$this->load->view('Cycletime/modalAdd_Time');
	}

  public function modalView_Step(){
		$this->load->view('Master_machine/modalView_Step');
	}

  public function modalView_Time(){
		$this->load->view('Cycletime/modalView_Time');
	}

  public function modalEdit_Step(){
		$this->load->view('Master_machine/modalEdit_Step');
	}

  public function modalEdit_Time(){
		$this->load->view('Cycletime/modalEdit_Time');
	}

  public function modalAddP(){
		$this->load->view('Component_custom/modalAddP');
	}

  public function modalAddStep_Master(){
		$this->load->view('Cycletime/modalAddStep_Master');
	}

  public function addPSave(){
  		$data				= $this->input->post();

  		$insertData	= array(
  			'nm_default'	=> strtoupper($data['standart_codex'])
  		);

  		$getNum	= $this->db->query("SELECT * FROM help_default_name WHERE nm_default='".strtoupper($data['standart_codex'])."' ")->num_rows();

  		if($getNum < 1){
  			$this->db->trans_start();
  				$this->db->insert('help_default_name', $insertData);
  			$this->db->trans_complete();

  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add Default Data');
  			}
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Name Already exists',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}

  public function code($pro='',$d2='',$type='')
  {
    $num = ($this->db->get('cycletime_default')->num_rows())+1;
    $code = "F-".$d2."-".str_pad($num, 3, "0", STR_PAD_LEFT).strtoupper($pro);
    return $code;
  }

  public function addStepSave(){
  		$data				= $this->input->post();
      $code = $this->code($data['komponen'],$data['diameter2'],'step');
      $data_session			= $this->session->userdata;
      $id = $this->db->query("SELECT AUTO_INCREMENT as id FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'cycletime_default' AND table_schema = 'ori_development'")->row();
  		$insertData	= array(
        'id'         => $id->id,
        'standard'    => $code,
  			'product_parent'   => $data['komponen'],
  			'standard_code'         => $data['standart_code'],
  			'pn'               => $data['pn'],
  			'diameter'         => $data['diameter'],
  			'diameter2'        => $data['diameter2'],
  			'liner'            => $data['liner'],
  			'standard_length'  => $data['standard_length'],
        'created_by'			 => $data_session['ORI_User']['username'],
        'created_on'     => date('Y-m-d H:i:s'),
        'deleted'          => 'N'
  		);

      for ($i=0; $i < count($data['step']); $i++) {
        $step = $data['step'][$i];
        $detailStep[$i]['product_parent'] = $data['komponen'];
        $detailStep[$i]['standard_code'] = $code;
        $detailStep[$i]['step_num'] = $i+1;
        $detailStep[$i]['step'] = $step;
        $detailStep[$i]['id_cycle'] = $id->id;
      }

  		// print_r($insertData);
  		// exit;


  		$getNum	= $this->db->query("SELECT * FROM cycletime_default WHERE product_parent='".$data['komponen']."' AND diameter='".$data['diameter']."' AND standard='".$data['standart_code']."' ")->num_rows();

  		if($getNum < 1){
  			$this->db->trans_start();
  				$this->db->insert('cycletime_default', $insertData);
          $this->db->insert_batch('cycletime_detail_step', $detailStep);
  			$this->db->trans_complete();

  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add Default Data = '.$data['komponen']." ".$data['diameter']." ".$data['standart_code']);
  			}
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Already exists, Please Check Back',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}

  public function addTimeSave(){
  		$data				= $this->input->post();
      $id         = $data['id'];
      $data_session			= $this->session->userdata;
      $insertData	= array(
        'man_power'        => $data['man_power'],
  		);
      $this->db->trans_start();
      $this->db->where(array('id'=>$id));
      $this->db->update('cycletime_default',$insertData);


      for ($i=0; $i < count($data['timing']); $i++) {
        //$step = $data['step'][$i];
        $insertDetail	= array(
          'timing'        => $data['timing'][$i],
    		);
        $this->db->where(array('id_cycle'=>$id,'step'=>$data['step_name'][$i]));
        $this->db->update('cycletime_detail_step',$insertDetail);
      }
      $this->db->trans_complete();
  		// print_r($insertData);
  		// exit;




  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add Default Data = '.$data['komponen']." ".$data['diameter']." ".$data['standart_code']);
  			}


  		echo json_encode($Arr_Kembali);
	}

  public function editStepSave(){
  		$data				= $this->input->post();
      $code = $this->code($data['komponen'],$data['diameter2'],'step');
      $data_session			= $this->session->userdata;
      $id = $this->input->post('id_cycle');
  		$insertData	= array(
        'standard'         => $code,
  			'product_parent'   => $data['komponen'],
  			'standard_code'    => $data['standart_code'],
  			'pn'               => $data['pn'],
  			'diameter'         => $data['diameter'],
  			'diameter2'        => $data['diameter2'],
  			'liner'            => $data['liner'],
  			'standard_length'  => $data['standard_length'],
        'modified_by'			 => $data_session['ORI_User']['username'],
        'modified_on'      => date('Y-m-d H:i:s'),
        'deleted'          => 'N'
  		);

      for ($i=0; $i < count($data['step']); $i++) {
        $step = $data['step'][$i];
        $detailStep[$i]['product_parent'] = $data['komponen'];
        $detailStep[$i]['standard_code'] = $code;
        $detailStep[$i]['step_num'] = $i+1;
        $detailStep[$i]['step'] = $step;
        $detailStep[$i]['id_cycle'] = $id;
      }

  		// print_r($insertData);
  		// exit;


  		$getNum	= $this->db->query("SELECT * FROM cycletime_default WHERE product_parent='".$data['komponen']."' AND diameter='".$data['diameter']."' AND standard='".$data['standart_code']."' ")->num_rows();

  		if($getNum < 1){
  			$this->db->trans_start();
  				$this->db->where(array('id'=>$id))->update('cycletime_default', $insertData);
          $this->db->where(array('id_cycle'=>$id))->delete('cycletime_detail_step');
          $this->db->insert_batch('cycletime_detail_step', $detailStep);
  			$this->db->trans_complete();

  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add Default Data = '.$data['komponen']." ".$data['diameter']." ".$data['standart_code']);
  			}
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Already exists, Please Check Back',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}

  public function editTimeSave(){
    $data				= $this->input->post();
    $data_session			= $this->session->userdata;
    $id = $this->input->post('id_cycle');
    $insertData	= array(
      'product_parent'   => $data['komponen'],
      'standard_code'    => $data['standart_code'],
      'pn'               => $data['pn'],
      'man_power'        => $data['man_power'],
      'diameter'         => $data['diameter'],
      'diameter2'        => $data['diameter2'],
      'liner'            => $data['liner'],
      'standard_length'  => $data['standard_length'],
      'modified_by'			 => $data_session['ORI_User']['username'],
      'modified_on'      => date('Y-m-d H:i:s'),
      'deleted'          => 'N'
    );
    $this->db->trans_start();
    for ($i=0; $i < count($data['timing']); $i++) {
      //$step = $data['step'][$i];
      $detailTime = array(
        'timing' => $data['timing'][$i],
      );
      $this->db->where(array('id'=>$data['step_id'][$i],'id_cycle'=>$data['id_cycle']))->update('cycletime_detail_step', $detailTime);
    }

    // print_r($insertData);
    // exit;


    $getNum	= $this->db->query("SELECT * FROM cycletime_default WHERE product_parent='".$data['komponen']."' AND diameter='".$data['diameter']."' AND standard='".$data['standart_code']."' ")->num_rows();

    if($getNum < 1){
      //$this->db->trans_start();
        $this->db->where(array('id'=>$id))->update('cycletime_default', $insertData);
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $Arr_Kembali	= array(
          'pesan'		=>'Failed Add Default. Please try again later ...',
          'status'	=> 0
        );
      }
      else{
        $this->db->trans_commit();
        $Arr_Kembali	= array(
          'pesan'		=>'Success Add Default. Thanks ...',
          'status'	=> 1
        );
        history('Add Default Data = '.$data['komponen']." ".$data['diameter']." ".$data['standart_code']);
      }
    }
    else{
      $Arr_Kembali	= array(
          'pesan'		=>'Default Already exists, Please Check Back',
          'status'	=> 0
        );
    }

    echo json_encode($Arr_Kembali);
	}

  public function deleteStepSave($id){

      $getNum	= $this->db->query("SELECT * FROM cycletime_default WHERE id='".$id."'")->num_rows();

      if($getNum >= 1){
        $this->db->trans_start();
          $this->db->where(array('id'=>$id))->delete('cycletime_default');
          $this->db->where(array('id_cycle'=>$id))->delete('cycletime_detail_step');
        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
          $this->db->trans_rollback();
          $Arr_Kembali	= array(
            'pesan'		=>'Failed Delete Step. Please try again later ...',
            'status'	=> 0
          );
        }
        else{
          $this->db->trans_commit();
          $Arr_Kembali	= array(
            'pesan'		=>'Success Delete Step. Thanks ...',
            'status'	=> 1
          );
          history('Delete Step ID = '.$id);
        }
      }
      else{
        $Arr_Kembali	= array(
            'pesan'		=>'Step Already gone, Please Check Back',
            'status'	=> 0
          );
      }

      echo json_encode($Arr_Kembali);
  }

  public function deleteTimeSave(){
      $data				= $this->input->post();
      $id         = $data['id'];
      $data_session			= $this->session->userdata;
      $insertData	= array(
        'man_power'        => $data['man_power'],
      );
      $this->db->trans_start();
      $this->db->where(array('id'=>$id));
      $this->db->update('cycletime_default',$insertData);


      for ($i=0; $i < count($data['timing']); $i++) {
        //$step = $data['step'][$i];
        $insertDetail	= array(
          'timing'        => $data['timing'][$i],
        );
        $this->db->where(array('id_cycle'=>$id,'step'=>$data['step_name'][$i]));
        $this->db->update('cycletime_detail_step',$insertDetail);
      }
      $this->db->trans_complete();
      // print_r($insertData);
      // exit;




        if($this->db->trans_status() === FALSE){
          $this->db->trans_rollback();
          $Arr_Kembali	= array(
            'pesan'		=>'Failed Add Default. Please try again later ...',
            'status'	=> 0
          );
        }
        else{
          $this->db->trans_commit();
          $Arr_Kembali	= array(
            'pesan'		=>'Success Add Default. Thanks ...',
            'status'	=> 1
          );
          history('Add Default Data = '.$data['komponen']." ".$data['diameter']." ".$data['standart_code']);
        }


      echo json_encode($Arr_Kembali);
  }

  public function addStepSave_Master(){
  		$data				= $this->input->post();

  		$insertData	= array(
  			'step_name'	=> strtoupper($data['step_name'])
  		);

  		$getNum	= $this->db->query("SELECT * FROM cycletime_step WHERE step_name='".strtoupper($data['step_name'])."' ")->num_rows();

  		if($getNum < 1){
  			$this->db->trans_start();
  				$this->db->insert('cycletime_step', $insertData);
  			$this->db->trans_complete();

  			if($this->db->trans_status() === FALSE){
  				$this->db->trans_rollback();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Failed Add Default. Please try again later ...',
  					'status'	=> 0
  				);
  			}
  			else{
  				$this->db->trans_commit();
  				$Arr_Kembali	= array(
  					'pesan'		=>'Success Add Default. Thanks ...',
  					'status'	=> 1
  				);
  				history('Add Default Data');
  			}
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Name Already exists',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}

  public function getStepData(){
  		$data				= $this->input->post();

  		$whereData	= array(
  			'product_parent'	=> $data['komponen'],
        'pn'	=> $data['pn'],
        'liner'	=> $data['liner'],
        'standard_code'	=> $data['standart_code'],
  		);

  		$getNum	= $this->db->get_where('cycletime_default',$whereData)->num_rows();

  		if($getNum > 0){
  			$getData = $this->db->get_where('cycletime_default',$whereData)->row();
        $getDetail = $this->db->get_where('cycletime_detail_step',array('id_cycle'=>$getData->id))->result();
        $viewDetail = '';
        foreach ($getDetail as $key => $v) {
          $viewDetail .= '<tr>
  					<td class="text-left vMid">'.strtoupper($v->step).'</td>
  					<td class="text-center">';

  					$viewDetail .=  form_input(array('type'=>'text','id'=>'timing[]','name'=>'timing[]','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Input MP', 'maxlength'=>'5','autocomplete'=>'off'));
            $viewDetail .=  form_input(array('type'=>'hidden','id'=>'step_name[]','name'=>'step_name[]','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Input MP', 'maxlength'=>'5','autocomplete'=>'off','value'=>$v->step));
            $viewDetail .=  form_input(array('type'=>'hidden','id'=>'step_id[]','name'=>'step_id[]','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Input Time', 'maxlength'=>'5','autocomplete'=>'off','value'=>$v->id));

          $viewDetail .= '</td>
  				</tr>';
        }


        $Arr_Kembali	= array(
          'pesan'		=>'Success Add Default. Thanks ...',
          'status'	=> 1,
          'step'    =>  $getData,
          'stepDetail'    =>  $viewDetail,
        );
        history('Add Default Data');
  		}
  		else{
  			$Arr_Kembali	= array(
  					'pesan'		=>'Default Step does not exists',
  					'status'	=> 0
  				);
  		}

  		echo json_encode($Arr_Kembali);
	}
	
	public function add(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$no_mesin				= strtoupper($data['no_mesin']);
			$nm_mesin				= $data['nm_mesin'];
			$unit					= $data['unit'];
			$capacity				= strtoupper($data['capacity']);
			$machine_price			= $data['machine_price'];
			$utilization_estimate	= $data['utilization_estimate'];
			$depresiation_per_month	= $data['depresiation_per_month'];
			$machine_cost_per_hour	= $data['machine_cost_per_hour'];
			
			//pengurutan kode
			$srcMtr			= "SELECT MAX(id_mesin) as maxP FROM machine WHERE id_mesin LIKE 'MC-%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 3, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);
			$id_machine		= "MC-".$urut2;
			
			// echo $numType; exit;
			$data	= array(
				'id_mesin' 		=> $id_machine,
				'no_mesin' 		=> str_replace(' ', '',$no_mesin),
				'nm_mesin' 		=> $nm_mesin,
				'unit' 			=> $unit,
				'capacity' 		=> $capacity,
				'machine_price' => $machine_price,
				'utilization_estimate' 		=> $utilization_estimate,
				'depresiation_per_month' 	=> $depresiation_per_month,
				'machine_cost_per_hour' 	=> $machine_cost_per_hour,
				'sts_mesin' 	=> 'Y',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'sts_date' 	=> date('Y-m-d'),
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// print_r($data);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->insert('machine', $data);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Machine. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Machine. Thanks ...',
					'status'		=> 1
					
				);
				history('Add Machine by id : '.$id_machine); 
			}
			echo json_encode($Arr_Kembali);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			
			$get_Data			= $this->db->query("SELECT * FROM raw_pieces WHERE flag_active='Y' ORDER BY kode_satuan ASC")->result_array();
			$data = array(
				'title'			=> 'Add Machine',
				'action'		=> 'add',
				'satuan'		=> $get_Data
			);
			$this->load->view('Master_machine/add',$data);
		}
	}
	
	function hapus(){
		$id_mesin = $this->uri->segment(3);
		$data_session			= $this->session->userdata;	
		// echo $id_mesin; exit;
		
		$Arr_Update = array(
			'sts_mesin' 		=> 'N',
			'deleted_by' 		=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);
		// echo "<pre>"; print_r($Arr_Update);
		// exit;
		$this->db->trans_start();
			$this->db->where('id_mesin', $id_mesin);
			$this->db->update('machine', $Arr_Update);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete machine data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete machine data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Machine with Kode/Id : '.$id_mesin);
		}
		echo json_encode($Arr_Data);
	}
	
	public function update(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$id_mesin				= $data['id_mesin'];
			$no_mesin				= strtoupper($data['no_mesin']);
			$nm_mesin				= $data['nm_mesin'];
			$unit					= $data['unit'];
			$capacity				= strtoupper($data['capacity']);
			$machine_price			= $data['machine_price'];
			$utilization_estimate	= $data['utilization_estimate'];
			$depresiation_per_month	= $data['depresiation_per_month'];
			$machine_cost_per_hour	= $data['machine_cost_per_hour'];
			
			// echo $numType; exit;
			$Arr_Update	= array(
				'no_mesin' 		=> $no_mesin,
				'nm_mesin' 		=> $nm_mesin,
				'unit' 			=> $unit,
				'capacity' 		=> $capacity,
				'machine_price' => $machine_price,
				'utilization_estimate' 		=> $utilization_estimate,
				'depresiation_per_month' 	=> $depresiation_per_month,
				'machine_cost_per_hour' 	=> $machine_cost_per_hour,
				'sts_mesin' 	=> 'Y',
				'sts_date' 	=> date('Y-m-d'),
				'updated_by' 	=> $data_session['ORI_User']['username'],
				'updated_date' 	=> date('Y-m-d H:i:s')
			);
			
			// print_r($data);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->where('id_mesin', $id_mesin);
				$this->db->update('machine', $Arr_Update);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Edit Machine. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Edit Machine. Thanks ...',
					'status'		=> 1
					
				);
				history('Edit Machine by id : '.$id_mesin); 
			}
			echo json_encode($Arr_Kembali);
		}
	}
}

?>
