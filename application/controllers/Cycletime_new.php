<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cycletime_new extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('cycletime_new_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//======================================================================================================================
    //===================================================CYCLETIME==========================================================
    //======================================================================================================================

	public function index(){
		$this->cycletime_new_model->index();
    }
    
    public function data_side_cycletime(){
		$this->cycletime_new_model->get_json_cycletime();
	}
	
	public function view_cycletime2(){
		$this->cycletime_new_model->view_cycletime2();
	}
	
	public function add_cycletime2(){
		$this->cycletime_new_model->add_cycletime2();
	}
	
	public function get_add(){
		$this->cycletime_new_model->get_add();
	}
	
	public function delete_permanent(){
		$this->cycletime_new_model->delete_permanent();
	}
	
	
	
	
	
	public function add_cycletime(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$id_costcenter 	= $data['id_costcenter'];
			$Detail 		= $data['detail'];
			$Ym				= date('ym');

			$ArrDetail	= array();
			foreach($Detail AS $val => $valx){
				if($valx['product'] != '0' AND !empty($valx['dn1']) AND $valx['pn'] != '0' AND $valx['liner'] != '0' ){
					$man_power		= str_replace(',','',$valx['man_power']);
					$time_process	= str_replace(',','',$valx['time_process']);
					$curing_time	= str_replace(',','',$valx['curing_time']);
					$total_time		= $time_process + $curing_time;
					$ArrDetail[$val]['id_costcenter'] 	= $id_costcenter;
					$ArrDetail[$val]['product'] 		= $valx['product'];
					$ArrDetail[$val]['dn1'] 			= str_replace(',','',$valx['dn1']);
					$ArrDetail[$val]['dn2'] 			= str_replace(',','',$valx['dn2']);
					$ArrDetail[$val]['sudut'] 			= str_replace(',','',$valx['sudut']);
					$ArrDetail[$val]['srlr'] 			= $valx['srlr'];
					$ArrDetail[$val]['pn'] 				= $valx['pn'];
					$ArrDetail[$val]['liner'] 			= $valx['liner'];
					$ArrDetail[$val]['man_power'] 		= $man_power;
					$ArrDetail[$val]['mesin'] 			= $valx['mesin'];
					$ArrDetail[$val]['time_process']	= $time_process;
					$ArrDetail[$val]['curing_time'] 	= $curing_time;
					$ArrDetail[$val]['total_time'] 		= $total_time;
					$ArrDetail[$val]['man_hours'] 		= $total_time * $man_power;
					$ArrDetail[$val]['created_by'] 		= $data_session['ORI_User']['username'];
					$ArrDetail[$val]['created_date'] 	= $dateTime;
				}
			}

			print_r($ArrDetail);
			exit;
			
			$this->db->trans_start();
				if(!empty($ArrDetail)){
					$this->db->insert_batch('cycletime', $ArrDetail);
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Insert data success. Thanks ...',
					'status'	=> 1
				);
				history('Insert cycletime '.$id_time);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			// $product = $this->cycletime_new_model->get_list_order('product','parent_product');
			$product 	= $this->db->query("SELECT * FROM product ORDER BY parent_product ASC, value_d ASC, value_d2 ASC")->result_array();
			$pressure	= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$liner		= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$data = array(
				'title'		=> 'Add Cycletime',
				'action'	=> 'add_cycletime',
				'product'	=> $product,
				'pressure'	=> $pressure,
				'liner'		=> $liner
			);
			$this->load->view('Cycletime_new/add_cycletime',$data);
		}
	}

	public function edit_cycletime(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			$Detail 	= $data['Detail'];
			$id_time	= $data['id_time'];

			$ArrHeader		= array(
				'id_time'		=> $id_time,
				'pressure'		=> $data['pressure'],
				'liner'			=> $data['liner'],
				'updated_by'	=> $data_session['ORI_User']['username'],
				'updated_date'	=> $dateTime
			);

            $ArrDetail	= array();
			$ArrDetail2	= array();
			foreach($Detail AS $val => $valx){
			$urut				= sprintf('%02s',$val);
			$ArrDetail[$val]['id_time'] 		= $id_time;
			$ArrDetail[$val]['id_costcenter'] 	= $id_time."-".$urut;
			$ArrDetail[$val]['costcenter'] 		= $valx['costcenter'];
			$ArrDetail[$val]['nm_costcenter'] 	= get_name('costcenter', 'nm_costcenter', 'id_costcenter', $valx['costcenter']);
			$ArrDetail[$val]['machine'] 		= $valx['machine'];
			$ArrDetail[$val]['nm_machine'] 		= get_name('machine', 'nm_mesin', 'id_mesin', $valx['machine']);
			$ArrDetail[$val]['mould'] 			= $valx['mould'];
			$ArrDetail[$val]['nm_mould'] 		= '';

				foreach($valx['detail'] AS $val2 => $valx2){
					$ArrDetail2[$val2.$val]['id_time'] 			= $id_time;
					$ArrDetail2[$val2.$val]['id_costcenter'] 	= $id_time."-".$urut;
					$ArrDetail2[$val2.$val]['id_process'] 		= $valx2['id_process'];
					$ArrDetail2[$val2.$val]['nm_process'] 		= get_name('process', 'nm_process', 'code_process', $valx2['id_process']);
					$ArrDetail2[$val2.$val]['cycletime'] 		= $valx2['cycletime'];
					$ArrDetail2[$val2.$val]['qty_mp'] 			= $valx2['qty_mp'];
					$ArrDetail2[$val2.$val]['note'] 			= $valx2['note'];
				}
			}

			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// print_r($ArrDetail2);
			// exit;
            
            $this->db->trans_start();
				$this->db->where('id_time', $id_time);
				$this->db->update('cycletime_header', $ArrHeader);

				$this->db->delete('cycletime_detail_header', array('id_time' => $id_time));
				$this->db->delete('cycletime_detail_detail', array('id_time' => $id_time));

				if(!empty($ArrDetail)){
					$this->db->insert_batch('cycletime_detail_header', $ArrDetail);
				}
				if(!empty($ArrDetail)){
					$this->db->insert_batch('cycletime_detail_detail', $ArrDetail2);
				}
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Cycletime '.$id_time);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }

            $id_time = $this->uri->segment(3);
            $query 	= "SELECT * FROM cycletime_header WHERE id_time='".$id_time."' LIMIT 1";
			$result = $this->db->query($query)->result();
			$detail = $this->db->query("SELECT * FROM cycletime_detail_header WHERE id_time='".$result[0]->id_time."'")->result_array();
			
			$product = $this->db->query("SELECT * FROM product ORDER BY parent_product ASC, value_d ASC, value_d2 ASC")->result_array();
			$costcenter	= $this->cycletime_new_model->get_list_where_order('costcenter','deleted','N','nm_costcenter');
			$machine	= $this->cycletime_new_model->get_list_where_order('machine','sts_mesin','Y','nm_mesin');
			$process	= $this->cycletime_new_model->get_list_where_order('process','deleted','N','nm_process');

			$pressure	= $this->db->query("SELECT * FROM list_help WHERE group_by ='pressure' AND sts='Y'")->result_array();
			$liner		= $this->db->query("SELECT * FROM list_help WHERE group_by ='liner' AND sts='Y'")->result_array();

			$data = array(
				'title'		=> 'Edit Cycletime',
                'action'	=> 'edit_cycletime',
				'data' 		=> $result,
				'product' 	=> $product,
				'costcenter'=> $costcenter,
				'machine' 	=> $machine,
				'detail' 	=> $detail,
				'process'	=> $process,
				'pressure'	=> $pressure,
				'liner'		=> $liner
			);
			$this->load->view('Cycletime_new/edit_cycletime',$data);
		}
    }
	
	public function hapus_cycletime(){
		$id_time = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('id_time', $id_time);
            $this->db->update('cycletime_header', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Cycletime Data : '.$id_time);
		}
		echo json_encode($Arr_Data);
	}


	public function view_cycletime(){
		$id_time = $this->uri->segment(3);

		$query 	= "SELECT * FROM cycletime_header WHERE id_time='".$id_time."' LIMIT 1";
		$result = $this->db->query($query)->result();
		$detail = $this->db->query("SELECT * FROM cycletime_detail_header WHERE id_time='".$result[0]->id_time."'")->result_array();
		
		$data = array(
			'title'		=> 'Edit Cycletime',
			'action'	=> 'edit_cycletime',
			'data' 		=> $result,
			'detail' 	=> $detail
		);
		$this->load->view('Cycletime_new/view_cycletime',$data);
	}
	
	public function get_addx(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$costcenter	= $this->cycletime_new_model->get_list_where_order('costcenter','deleted','N','nm_costcenter');
    	$machine	= $this->cycletime_new_model->get_list_where_order('machine','sts_mesin','Y','nm_mesin');
    	
		// $mould		= $this->db->query("SELECT * FROM asset WHERE category='5' GROUP BY nm_asset ORDER BY nm_asset ASC ")->result_array();
		
		$d_Header = "";
		// $d_Header .= "<tr>";
			$d_Header .= "<tr class='header_".$id."'>";
				$d_Header .= "<td align='center'>".$id."</td>";
				$d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][costcenter]' class='chosen_select form-control input-sm inline-blockd costcenter'>";
        $d_Header .= "<option value='0'>Select Costcenter</option>";
        foreach($costcenter AS $val => $valx){
          $d_Header .= "<option value='".$valx['id_costcenter']."'>".strtoupper($valx['nm_costcenter'])."</option>";
        }
        $d_Header .= 		"</select>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][machine]' class='chosen_select form-control input-sm inline-blockd'>";
        $d_Header .= "<option value='0'>Select Machine</option>";
        foreach($machine AS $val => $valx){
          $d_Header .= "<option value='".$valx['id_mesin']."'>".strtoupper($valx['nm_mesin'])."</option>";
        }
        $d_Header .= "<option value='0'>NONE MACHINE</option>";
        $d_Header .= 		"</select>";

				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][mould]' class='chosen_select form-control input-sm inline-blockd'>";
        $d_Header .= "<option value='0'>Select Mould/Tools</option>";
        // foreach($mould AS $val => $valx){
        //   $d_Header .= "<option value='".$valx['kd_asset']."'>".strtoupper($valx['nm_asset'])."</option>";
        // }
        $d_Header .= "<option value='0'>NONE MOULD/TOOLS</option>";
        $d_Header .= 		"</select>";
				$d_Header .= "</td>";
				$d_Header .= "<td align='left'></td>";
				$d_Header .= "<td align='center'>";
				$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
				$d_Header .= "</td>";
			$d_Header .= "</tr>";

		//add nya
		$d_Header .= "<tr id='add_".$id."_".$no."' class='header_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-primary addSubPart' title='Add Process'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Process</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-warning addPart' title='Add Costcenter'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Costcenter</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

  	public function get_add_sub(){
		$id 	= $this->uri->segment(3);
    	$no 	= $this->uri->segment(4);

		$process	= $this->cycletime_new_model->get_list_where_order('process','deleted','N','nm_process');
		// echo $qListResin; exit;
		$d_Header = "";
		// $d_Header .= "<tr>";
			$d_Header .= "<tr class='header_".$id."'>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='left' style='vertical-align:middle; padding-left: 30px;'>";
        $d_Header .= "<select name='Detail[".$id."][detail][".$no."][id_process]' class='chosen_select form-control input-sm inline-blockd process'>";
        $d_Header .= "<option value='0'>Select Process Name</option>";
        foreach($process AS $val => $valx){
          $d_Header .= "<option value='".$valx['code_process']."'>".strtoupper($valx['nm_process'])."</option>";
        }
        $d_Header .= 		"</select>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<input type='text' name='Detail[".$id."][detail][".$no."][cycletime]' class='form-control input-md maskM' placeholder='Cycletime (Minutes)'  data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<input type='text' name='Detail[".$id."][detail][".$no."][qty_mp]' class='form-control input-md maskM' placeholder='Qty Man Power'  data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<input type='text' name='Detail[".$id."][detail][".$no."][note]' class='form-control input-md' placeholder='Information'  data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
        $d_Header .= "</td>";
				$d_Header .= "<td align='center'>";
				$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delSubPart' title='Delete Part'><i class='fa fa-close'></i></button>";
				$d_Header .= "</td>";
			$d_Header .= "</tr>";

		//add nya
		$d_Header .= "<tr id='add_".$id."_".$no."' class='header_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-primary addSubPart' title='Add Process'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Process</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}


    //======================================================================================================================
    //===================================================PROCESS============================================================
    //======================================================================================================================

	public function process(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Process Data',
			'action'		=> 'process',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Process');
		$this->load->view('Cycletime_new/process',$data);
	}

	public function data_side_process(){
		$this->cycletime_new_model->get_json_process();
	}

	public function add_process(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			//header
			$id 		    = $data['id'];
			$code_process	= $data['code_process'];
			$nm_process		= strtolower($data['nm_process']);
			$keterangan		= strtolower($data['keterangan']);
            
			

			if(empty($id)){
                $Y				= date('y');
                $qIPP			= "SELECT MAX(code_process) as maxP FROM process WHERE code_process LIKE 'PS".$Y."%' ";
                $numrowIPP		= $this->db->query($qIPP)->num_rows();
                $resultIPP		= $this->db->query($qIPP)->result_array();
                $angkaUrut2		= $resultIPP[0]['maxP'];
                $urutan2		= (int)substr($angkaUrut2, 4, 3);
                $urutan2++;
                $urut2			= sprintf('%03s',$urutan2);
                $code_process	= "PS".$Y.$urut2;

                //project_header
                $ArrHeader = array(
                    'code_process' 	=> $code_process,
                    'nm_process'    => $nm_process,
                    'keterangan' 	=> $keterangan,
                    'created_by' 	=> $data_session['ORI_User']['username'],
                    'created_date' 	=> $dateTime
                );

                $TandaI = "Insert";
			}

			if(!empty($id)){
                //project_header
                $ArrHeader = array(
                    'code_process' 	=> $code_process,
                    'nm_process'    => $nm_process,
                    'keterangan' 	=> $keterangan,
                    'updated_by' 	=> $data_session['ORI_User']['username'],
                    'updated_date' 	=> $dateTime
                );
                $TandaI = "Update";
            }

            // print_r($ArrHeader);
			// exit;
            
            $this->db->trans_start();
                if(empty($id)){
                    $this->db->insert('process', $ArrHeader);
                }
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('process', $ArrHeader);
                }
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success. Thanks ...',
					'status'	=> 1
				);
				history($TandaI.' Process '.$code_process);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }
            
            $code_process = $this->uri->segment(3);
            $query = "SELECT * FROM process WHERE code_process ='".$code_process."' LIMIT 1 ";
            $result = $this->db->query($query)->result();

			$data = array(
				'title'		=> 'Add Process',
                'action'	=> 'add',
                'data'      => $result
			);
			$this->load->view('Cycletime_new/add_process',$data);
		}
	}

	public function hapus_process(){
		$code_process = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('code_process', $code_process);
            $this->db->update('process', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Process Data : '.$code_process);
		}
		echo json_encode($Arr_Data);
	}

	//======================================================================================================================
    //===================================================COST CENTER============================================================
    //======================================================================================================================

	public function costcenter(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/costcenter';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Costcenter Data',
			'action'		=> 'costcenter',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Costcenter');
		$this->load->view('Cycletime_new/costcenter',$data);
    }
    
    public function data_side_costcenter(){
		$this->cycletime_new_model->get_json_costcenter();
    }
    
    public function add_costcenter(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$detail 		    = $data['detail'];
            
            $Y				= date('y');
            $qIPP			= "SELECT MAX(id_costcenter) as maxP FROM costcenter WHERE id_costcenter LIKE 'CC".$Y."%' ";
            $numrowIPP		= $this->db->query($qIPP)->num_rows();
            $resultIPP		= $this->db->query($qIPP)->result_array();
            $angkaUrut2		= $resultIPP[0]['maxP'];
            $urutan2		= (int)substr($angkaUrut2, 4, 3);
            $urutan2++;
            $urut2			= sprintf('%03s',$urutan2);
            $code_cc	    = "CC".$Y.$urut2;

            $ArrDetail = array();
            foreach($detail AS $val => $valx){
                $ArrDetail[$val]['id_costcenter']   = "CC".$Y.sprintf('%03s',$urut2);
                $ArrDetail[$val]['id_dept']         = $valx['id_dept'];
                $ArrDetail[$val]['nm_costcenter']   = $valx['nm_costcenter'];
                $ArrDetail[$val]['shift1']          = ($valx['mp_1'] != '0' AND $valx['mp_1'] != '')?'Y':'N';
                $ArrDetail[$val]['shift2']          = ($valx['mp_2'] != '0' AND $valx['mp_2'] != '')?'Y':'N';
                $ArrDetail[$val]['shift3']          = ($valx['mp_3'] != '0' AND $valx['mp_3'] != '')?'Y':'N';
                $ArrDetail[$val]['mp_1']            = $valx['mp_1'];
                $ArrDetail[$val]['mp_2']            = $valx['mp_2'];
                $ArrDetail[$val]['mp_3']            = $valx['mp_3'];
                $ArrDetail[$val]['created_date']    = date('Y-m-d H:i:s');
                $ArrDetail[$val]['created_by']      = $data_session['ORI_User']['username'];

                $urut2++;
            }

            $TandaI = "Insert";

			

            // print_r($ArrDetail);
			// exit;
            
            $this->db->trans_start();
                $this->db->insert_batch('costcenter', $ArrDetail);  
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success. Thanks ...',
					'status'	=> 1
				);
				history($TandaI.' Costcenter '.$code_cc);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/costcenter';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$department = $this->cycletime_new_model->get_list_where_order('department','status', 'Y', 'nm_dept');

			$data = array(
				'title'		=> 'Add Costcenter',
				'action'	=> 'costcenter',
				'department'=> $department
			);
			$this->load->view('Cycletime_new/add_costcenter',$data);
		}
    }

    public function edit_costcenter(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
            $id_costcenter = $data['id_costcenter'];
			//header
			$ArrHeader =  array(
				'nm_costcenter' => $data['nm_costcenter'],
				'id_dept' 		=> $data['id_dept'],
                'shift1'        => ($data['mp_1'] != '0' AND $data['mp_1'] != '')?'Y':'N',
                'shift2'        => ($data['mp_2'] != '0' AND $data['mp_2'] != '')?'Y':'N',
                'shift3'        => ($data['mp_3'] != '0' AND $data['mp_3'] != '')?'Y':'N',
                'mp_1'          => $data['mp_1'],
                'mp_2'          => $data['mp_2'],
                'mp_3'          => $data['mp_3'],
                'updated_date'  => $dateTime,
                'updated_by'    => $data_session['ORI_User']['username']
            );
            // print_r($ArrDetail);
			// exit;
            
            $this->db->trans_start();
                $this->db->where('id_costcenter',$id_costcenter);
                $this->db->update('costcenter',$ArrHeader); 
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Costcenter '.$id_costcenter);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/costcenter';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }

            $id_cc = $this->uri->segment(3);
            $query = "SELECT * FROM costcenter WHERE id_costcenter='".$id_cc."' LIMIT 1";
			$result = $this->db->query($query)->result();
			
			$department = $this->cycletime_new_model->get_list_where_order('department','status', 'Y', 'nm_dept');

			$data = array(
				'title'		=> 'Add Costcenter',
                'action'	=> 'costcenter',
				'data' 		=> $result,
				'department'=> $department
			);
			$this->load->view('Cycletime_new/edit_costcenter',$data);
		}
    }
    
    public function hapus_costcenter(){
		$code_process = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('id_costcenter', $code_process);
            $this->db->update('costcenter', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Process Data : '.$code_process);
		}
		echo json_encode($Arr_Data);
	}

	//======================================================================================================================
    //===================================================SHIFT============================================================
    //======================================================================================================================

	public function shift(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Shift Data',
			'action'		=> 'shift',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Shift');
		$this->load->view('Cycletime_new/shift',$data);
    }
    
    public function data_side_shift(){
		$this->cycletime_new_model->get_json_shift();
    }
    
    public function add_shift(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			//header
			$detail 		    = $data['detail'];
            
            $Y				= date('y');
            $qIPP			= "SELECT MAX(id_shift) as maxP FROM shift WHERE id_shift LIKE 'SF".$Y."%' ";
            $numrowIPP		= $this->db->query($qIPP)->num_rows();
            $resultIPP		= $this->db->query($qIPP)->result_array();
            $angkaUrut2		= $resultIPP[0]['maxP'];
            $urutan2		= (int)substr($angkaUrut2, 4, 3);
            $urutan2++;
            $urut2			= sprintf('%03s',$urutan2);
            $code_cc	    = "SF".$Y.$urut2;

            $ArrDetail = array();
            foreach($detail AS $val => $valx){
                $ArrDetail[$val]['id_shift']  	= "SF".$Y.sprintf('%03s',$urut2);
                $ArrDetail[$val]['id_type']   	= $valx['id_type'];
				$ArrDetail[$val]['nm_type']   	= get_name('shift_type', 'name', 'id', $valx['id_type']);
				$ArrDetail[$val]['day']   		= $valx['day'];
				$ArrDetail[$val]['start_work']  = $valx['start_work'];
				$ArrDetail[$val]['done_work']   = $valx['done_work'];
				$ArrDetail[$val]['start_break_1']  = $valx['start_break_1'];
				$ArrDetail[$val]['done_break_1']   = $valx['done_break_1'];
				$ArrDetail[$val]['start_break_2']  = $valx['start_break_2'];
				$ArrDetail[$val]['done_break_2']   = $valx['done_break_2'];
				$ArrDetail[$val]['start_break_3']  = $valx['start_break_3'];
				$ArrDetail[$val]['done_break_3']   = $valx['done_break_3'];
                $ArrDetail[$val]['created_date']   = date('Y-m-d H:i:s');
                $ArrDetail[$val]['created_by']     = $data_session['ORI_User']['username'];

                $urut2++;
            }

            $TandaI = "Insert";

			

            // print_r($ArrDetail);
			// exit;
            
            $this->db->trans_start();
                $this->db->insert_batch('shift', $ArrDetail);  
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success. Thanks ...',
					'status'	=> 1
				);
				history($TandaI.' Shift '.$code_cc);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$shift = $this->db->query("SELECT * FROM shift_type ORDER BY id ASC")->result_array();

			$data = array(
				'title'		=> 'Add Shift',
				'action'	=> 'shift',
				'shift'		=> $shift
			);
			$this->load->view('Cycletime_new/add_shift',$data);
		}
    }

    public function edit_shift(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
            $id_shift = $data['id_shift'];
			//header
			$ArrHeader =  array(
				'id_type' 		=> $data['id_type'],
				'nm_type' 		=> get_name('shift_type', 'name', 'id', $data['id_type']),
				'day'          	=> $data['day'],
				
                'start_work'    => $data['start_work'],
				'done_work'     => $data['done_work'],

				'start_break_1' => $data['start_break_1'],
				'done_break_1'  => $data['done_break_1'],

				'start_break_2' => $data['start_break_2'],
				'done_break_2'  => $data['done_break_2'],

				'start_break_3' => $data['start_break_3'],
				'done_break_3'  => $data['done_break_3'],
				
                'updated_date'  => $dateTime,
                'updated_by'    => $data_session['ORI_User']['username']
            );
            // print_r($ArrHeader);
			// exit;
            
            $this->db->trans_start();
                $this->db->where('id_shift',$id_shift);
                $this->db->update('shift',$ArrHeader); 
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Update data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Shift '.$id_shift);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }

            $id_shift = $this->uri->segment(3);
            $query = "SELECT * FROM shift WHERE id_shift='".$id_shift."' LIMIT 1";
			$result = $this->db->query($query)->result();
			
			$shift = $this->db->query("SELECT * FROM shift_type ORDER BY id ASC")->result_array();

			$data = array(
				'title'		=> 'Edit Shift',
                'action'	=> 'shift',
				'data' => $result,
				'shift' => $shift
			);
			$this->load->view('Cycletime_new/edit_shift',$data);
		}
    }
    
    public function hapus_shift(){
		$id_shift = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('id_shift', $id_shift);
            $this->db->update('shift', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Shift Data : '.$id_shift);
		}
		echo json_encode($Arr_Data);
	}

	//======================================================================================================================
    //===================================================DEPARTMENT============================================================
    //======================================================================================================================

	public function department(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/department';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Department Data',
			'action'		=> 'department',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Department');
		$this->load->view('Cycletime_new/department',$data);
	}

	public function data_side_department(){
		$this->cycletime_new_model->get_json_department();
	}

	public function add_department(){ 
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			//header
			$id 		    = $data['id'];
			$nm_dept		= strtoupper($data['nm_dept']);
			$status			= $data['status'];

			if(empty($id)){
                $ArrHeader = array(
                    'nm_dept'    	=> $nm_dept,
                    'status' 		=> $status,
                    'created_by' 	=> $data_session['ORI_User']['username'],
                    'created_date' 	=> $dateTime
                );
                $TandaI = "Insert";
			}

			if(!empty($id)){
                $ArrHeader = array(
                    'nm_dept'    	=> $nm_dept,
                    'status' 		=> $status,
                    'updated_by' 	=> $data_session['ORI_User']['username'],
                    'updated_date' 	=> $dateTime
                );
                $TandaI = "Update";
            }

            // print_r($ArrHeader);
			// exit;
            
            $this->db->trans_start();
                if(empty($id)){
                    $this->db->insert('department', $ArrHeader);
                }
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('department', $ArrHeader);
                }
            $this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success. Thanks ...',
					'status'	=> 1
				);
				history($TandaI.' Department '.$id.' / '.$nm_dept);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/department';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }
            
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM department WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();

			$data = array(
				'title'		=> 'Add Department',
                'action'	=> 'add',
                'data'      => $result
			);
			$this->load->view('Cycletime_new/add_department',$data);
		}
	}

	public function hapus_department(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->update('department', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Department Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}

}
