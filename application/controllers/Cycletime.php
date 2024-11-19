<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Cycletime extends CI_Controller
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
  		$get_Data		= $this->db->query("SELECT a.*, b.nm_customer FROM component_header a LEFT JOIN customer b ON b.id_customer=a.standart_by WHERE a.deleted ='N' ORDER BY a.status DESC")->result();
  		$menu_akses		= $this->master_model->getMenu();
  		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
  		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
  		$ListCustomer	= $this->db->query("SELECT id_customer, nm_customer FROM customer WHERE id_customer <> 'C100-1903000' ORDER BY nm_customer ASC")->result_array();



  		$data = array(
  			'title'			=> 'Indeks Of Estimation',
  			'action'		=> 'index',
  			'listseries'	=> $getSeries,
  			'listkomponen'	=> $getKomp,
  			'cust'	=> $ListCustomer,
  			'row'			=> $get_Data,
  			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		history("View Master ".$productN);
  		$this->load->view('Component_custom/index',$data);
	}


	public function standard_time(){ 
  		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/standard_time';
		// echo $controller; exit;
  		$Arr_Akses			= getAcccesmenu($controller);
		$menu_akses		= $this->master_model->getMenu();
  		if($Arr_Akses['read'] !='1'){
  			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
  			redirect(site_url('dashboard'));
  		}
		
		$getBy				= "SELECT update_by, update_on FROM cost_process_auto LIMIT 1";
		$restgetBy			= $this->db->query($getBy)->result_array();
		$getSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted = 'N' AND `status` = 'Y' ORDER BY pressure ASC, resin_system ASC, liner ASC")->result_array();
		$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();


		$data = array(
  			'title'			=> 'Indeks Of Standard Time',
  			'action'		=> 'index',
			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses,
			'get_by'		=> $restgetBy,
			'listseries'	=> $getSeries,
			'listkomponen'	=> $getKomp
  		);
  		$this->load->view('Cycletime/index_standard_time',$data);
	}


	public function getDataJSON2(){
  		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/standard_time';
  		$Arr_Akses			= getAcccesmenu($controller);
  		$requestData	= $_REQUEST;
  		$fetch			= $this->queryDataJSON2(
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
  			$nestedData[]	= "<div align='center'>".$nomor."</div>";
  			$nestedData[]	= "<div align='left'>".$row['standard_code']."</div>";
  			$nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
  			$nestedData[]	= "<div align='center'>".$row['pn']."</div>";
  			$nestedData[]	= "<div align='center'>".floatval($row['liner'])."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:30px;'>".$row['diameter']."</div>";
				$dim2xc = (strtolower($row['product_parent'])=='equal tee mould' OR strtolower($row['product_parent'])=='equal tee mould')?floatval($row['diameter']):floatval($row['diameter2']);
  			$nestedData[]	= "<div align='right' style='padding-right:30px;'>".$dim2xc."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:30px;'>".$row['total_time']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['man_power'])."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:30px;'>".number_format($row['man_hours'], 1)."</div>";
			$mesinDS = ($row['id_mesin'] == 'FW00')?'NONE':$row['id_mesin'];
			$nestedData[]	= "<div align='center'>".$mesinDS."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update	= "<a id='TimeEdit' data-id='".$row['id']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}if($Arr_Akses['update']=='1'){
				$delete	= "<a id='TimeDelete' data-kode='".$row['kode']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
			}

			$nestedData[]	= "<div align='center' >
									<a id='TimeDetail' data-id='".$row['id']."' data-nm_product='".$row['product_parent']."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>
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

	public function queryDataJSON2($group, $komponen, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		// echo $series."<br>";
		// echo $group."<br>";
		// echo $komponen."<br>";

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
				".$where_komponen."
				AND a.deleted ='N' AND (
				a.standard_code LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.diameter2 LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'standard_code',
			2 => 'product_parent',
			3 => 'pn',
			4 => 'liner',
			5 => 'diameter',
			6 => 'diameter2',
			7 => 'rev'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		// echo $sql; exit;
		$data['query'] = $this->db->query($sql);
		return $data;
	}

  
	public function modalAdd_Step(){
		$this->load->view('Cycletime/modalAdd_Step');
	}

  public function modalAdd_Time(){
		$this->load->view('Cycletime/modalAdd_Time');
	}

  public function modalView_Step(){
		$this->load->view('Cycletime/modalView_Step');
	}

  public function modalView_Time(){
		$this->load->view('Cycletime/modalView_Time');
	}

  public function modalEdit_Step(){
		$this->load->view('Cycletime/modalEdit_Step');
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
  			// 'diameter'         => $data['diameter'],
  			// 'diameter2'        => $data['diameter2'],
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
  			// 'diameter'         => $data['diameter'],
  			// 'diameter2'        => $data['diameter2'],
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
  			'parent_product'=> $data['komponen'],
			'standart_code'	=> $data['standart_code']
  		);

  		$getNum	= $this->db->get_where('cycle_time_step',$whereData)->num_rows();

  		if($getNum > 0){
  			$getData 	= $this->db->get_where('cycle_time_step',$whereData)->row();
			$getDetail 	= $this->db->get_where('cycle_time_step',$whereData)->result();
			$viewDetail = '';
			$nomor		= 0;
			foreach ($getDetail as $key => $v) {
				$nomor++;
				$viewDetail .= '<tr>';
				$viewDetail .= '<td class="text-left vMid">'.strtoupper($v->step).'</td>';
				$viewDetail .= '<td class="text-center">';
				$viewDetail .=  form_input(array('type'=>'text','id'=>'timing_'.$nomor.'','name'=>'DataStep['.$nomor.'][timing]','class'=>'form-control input-sm inSp2 numberOnly', 'placeholder'=>'Input MP', 'maxlength'=>'5','autocomplete'=>'off'));
				$viewDetail .=  form_input(array('type'=>'hidden','id'=>'step_'.$nomor.'','name'=>'DataStep['.$nomor.'][step]'),$v->step);
				$viewDetail .= '</td>';
				$viewDetail .= '</tr>';
			}


			$Arr_Kembali	= array(
			  'pesan'		=>'Success Add Default Step. Thanks ...',
			  'status'		=> 1,
			  'step'    	=>  $getData,
			  'stepDetail'  =>  $viewDetail,
			);

  		}
  		else{
  			$Arr_Kembali	= array(
				'pesan'		=>'Default Step does not exists',
				'status'	=> 0
			);
  		}

  		echo json_encode($Arr_Kembali);
	}
	
	
	//ARWANT CODE
	public function addTimeSave(){
		$data			= $this->input->post();
		$data_session	= $this->session->userdata;
		
		$ArrWhere		= array(
			'standard_code'		=> $data['komponen'],
			'product_parent'	=> $data['standart_code'],
			'diameter'			=> $data['diameter'],
			'diameter2'			=> $data['diameter2'],
			'pn'				=> $data['pn'],
			'liner'				=> $data['liner'],
			'standard_length'	=> $data['standard_length']
		);
		
		$getNum	= $this->db->get_where('cycletime_default',$ArrWhere)->num_rows(); 
		if($getNum < 1){
		
			$ArrData		= $data['DataStep'];
			
			$Ym						= date('ym');
			
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode) as maxP FROM cycletime_default WHERE kode LIKE '".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 5);
			$urutan2++;
			$urut2			= sprintf('%05s',$urutan2);
			$id_kode		= $Ym.$urut2;
			
			
			$ArrInsert		= array();
			$no				= 0;
			$totTime		= 0;
			foreach($ArrData AS $val => $valx){
				$no++;
				$totTime += $valx['timing'];
				$ArrInsert[$val]['product_parent']	= $data['komponen'];
				$ArrInsert[$val]['standart_code'] 	= $data['standart_code'];
				$ArrInsert[$val]['kode'] 			= $id_kode;
				$ArrInsert[$val]['urutan'] 			= $no;
				$ArrInsert[$val]['step'] 			= $valx['step'];
				$ArrInsert[$val]['timing'] 			= $valx['timing'];
				$ArrInsert[$val]['created_by'] 		= $data_session['ORI_User']['username'];
				$ArrInsert[$val]['created_date'] 	= date('Y-m-d H:i:s');
			}
			$dim2xc = (strtolower($data['komponen'])=='equal tee mould' OR strtolower($data['komponen'])=='equal tee mould')?floatval($data['diameter']):floatval($data['diameter2']);
							
			$ArrHeader		= array(
				'standard_code'		=> $data['standart_code'],
				'kode'				=> $id_kode,
				'product_parent'	=> $data['komponen'],
				'diameter'			=> $data['diameter'],
				'diameter2'			=> $dim2xc,
				'pn'				=> $data['pn'],
				'liner'				=> $data['liner'],
				'man_power'			=> $data['man_power'],
				'id_mesin'			=> $data['machine'],
				'total_time'		=> $totTime/60,
				'man_hours'			=> ($totTime/60) * $data['man_power'],
				'standard_length'	=> $data['standard_length'],
				'created_by'		=> $data_session['ORI_User']['username'],
				'created_on'		=> date('Y-m-d H:i:s')
			);
			
			// print_r($ArrHeader);
			// print_r($ArrInsert);
			// exit;
			
			$this->db->trans_start();
				$this->db->insert('cycletime_default', $ArrHeader);
				$this->db->insert_batch('cycle_time_step_detail', $ArrInsert);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Cycle Time. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Cycle Time. Thanks ...',
					'status'		=> 1
					
				);
				history('Add Mould Mandrill : '.$data['komponen']."/".$data['standart_code']."/".$data['diameter']."/".$data['diameter2']); 
			}
		}
		else{
			$Arr_Kembali	= array(
				'pesan'			=> 'Data sudah ada, input data yang lain ...',
				'status'		=> 0
			);
		}
		echo json_encode($Arr_Kembali);		
	}
	
	public function editTimeSave(){
		$data				= $this->input->post();
		$data_session			= $this->session->userdata;
		
		$ArrWhere		= array(
			'standard_code'		=> $data['komponen'],
			'product_parent'	=> $data['standart_code'],
			'diameter'			=> $data['diameter'],
			'diameter2'			=> $data['diameter2'],
			'pn'				=> $data['pn'],
			'liner'				=> $data['liner'],
			'standard_length'	=> $data['standard_length']
		);
		
	
			
		if(!empty($data['DataStep'])){
			$ArrData		= $data['DataStep'];
			
			$ArrInsert		= array();
			$no				= 0;
			$totTime		= 0;
			foreach($ArrData AS $val => $valx){
				$no++;
				$totTime += $valx['timing'];
				$ArrInsert[$val]['product_parent']	= $data['komponen'];
				$ArrInsert[$val]['standart_code'] 	= $data['standart_code'];
				$ArrInsert[$val]['kode'] 			= $data['kode'];
				$ArrInsert[$val]['urutan'] 			= $no;
				$ArrInsert[$val]['step'] 			= $valx['step'];
				$ArrInsert[$val]['timing'] 			= $valx['timing'];
				$ArrInsert[$val]['created_by'] 		= $data_session['ORI_User']['username'];
				$ArrInsert[$val]['created_date'] 	= date('Y-m-d H:i:s');
			}
		
		}
		$dim2xc = (strtolower($data['komponen'])=='equal tee mould' OR strtolower($data['komponen'])=='equal tee mould')?floatval($data['diameter']):floatval($data['diameter2']);
							
		$ArrHeader		= array(
			'standard_code'		=> $data['standart_code'],
			'product_parent'	=> $data['komponen'],
			'diameter'			=> $data['diameter'],
			'diameter2'			=> $dim2xc,
			'pn'				=> $data['pn'],
			'liner'				=> $data['liner'],
			'man_power'			=> $data['man_power'],
			'id_mesin'			=> $data['machine'],
			'total_time'		=> $totTime/60,
			'man_hours'			=> ($totTime/60) * $data['man_power'],
			'standard_length'	=> $data['standard_length'],
			'modified_by'		=> $data_session['ORI_User']['username'],
			'modified_on'		=> date('Y-m-d H:i:s')
		);
		
		// print_r($ArrHeader);
		// print_r($ArrInsert);
		// exit;
		
		$this->db->trans_start();
			$this->db->delete('cycle_time_step_detail', array('kode' => $data['kode'], 'status'=>'N'));
			if(!empty($data['DataStep'])){
				$this->db->insert_batch('cycle_time_step_detail', $ArrInsert);
			}
		
			$this->db->where('id', $data['id']);
			$this->db->update('cycletime_default', $ArrHeader);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Cycle Time. Please try again later ...',
				'status'		=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Cycle Time. Thanks ...',
				'status'		=> 1
				
			);
			history('Edit Mould Mandrill : '.$data['komponen']."/".$data['standart_code']."/".$data['diameter']."/".$data['diameter2']); 
		}
		
		echo json_encode($Arr_Kembali);	
	}
	
	public function deleteTime($id){
		$data_session			= $this->session->userdata;
		$ArrDel1		= array(
			'deleted'		=> 'Y',
			'deleted_by'	=> $data_session['ORI_User']['username'],
			'deleted_date'	=> date('Y-m-d H:i:s')
		);
		
		$ArrDel12		= array(
			'status'		=> 'Y',
			'deleted_by'	=> $data_session['ORI_User']['username'],
			'deleted_date'	=> date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			$this->db->where('kode', $id);
			$this->db->update('cycletime_default', $ArrDel1);
			
			$this->db->where('kode', $id);
			$this->db->update('cycle_time_step_detail', $ArrDel12);
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


		echo json_encode($Arr_Kembali);
	}
	
	function update_cost(){
		$data_session = $this->session->userdata;

		$this->db->trans_start(); 
			$this->db->truncate('cost_process_auto');
			
			$sqlUpdate = "
				INSERT INTO cost_process_auto ( id, kode, standard_code, product_parent, diameter, diameter2, pn, liner, direct_labour, indirect_labour, machine, mould_mandrill, total, update_by, update_on ) SELECT
					a.id,
					a.kode,
					a.standard_code,
					a.product_parent,
					a.diameter,
					IFNULL(a.diameter2, 0),
					a.pn,
					a.liner,
					a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '1' ) AS direct_labour,
					a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '2' ) AS indirect_labour,
					IFNULL((a.total_time * (SELECT x.machine_cost_per_hour FROM machine x WHERE x.no_mesin=a.id_mesin LIMIT 1)),0) AS machine,
					IFNULL((SELECT y.biaya_per_pcs FROM mould_mandrill y WHERE y.product_parent=a.product_parent AND y.diameter=a.diameter AND y.diameter2=a.diameter2 LIMIT 1),0) AS mould_mandrill,
					((a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '1' ))+
					(a.man_hours * (SELECT cost_process.std_rate FROM cost_process WHERE cost_process.id = '2' ))+
					(IFNULL((a.total_time * (SELECT x.machine_cost_per_hour FROM machine x WHERE x.no_mesin=a.id_mesin LIMIT 1)),0))+
					(IFNULL((SELECT y.biaya_per_pcs FROM mould_mandrill y WHERE y.product_parent=a.product_parent AND y.diameter=a.diameter AND y.diameter2=a.diameter2 LIMIT 1),0))) AS total,
					'".$data_session['ORI_User']['username']."',
					'".date('Y-m-d H:i:s')."'
				FROM cycletime_default a";
			
			$this->db->query($sqlUpdate);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Process Failed Updated. Please try again ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Process Suucess Updated. Thanks ...',
				'status'	=> 1
			);				
			history('Update Cost Process by Cyletime'); 
		}
		echo json_encode($Arr_Data);
	}
}

?>
