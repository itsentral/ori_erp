<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Non_rutin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('non_rutin_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	//===============================================================================================================================
  	//=============================================RUTIN=============================================================================
  	//===============================================================================================================================
	
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda				= $this->uri->segment(2);
		$data = array(
			'title'			=> 'PR Departemen',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'tanda'			=> $tanda
		);
		history('View data pengajuan pr non-rutin (departemen)');
		$this->load->view('Non_rutin/index',$data);
	}
	
	public function server_side_non_rutin(){
		$this->non_rutin_model->get_data_json_non_rutin();
	}
	
	public function add(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
            // print_r($data); exit;
			$code_plan  	= $data['id'];
			$code_planx  	= $data['id'];
			$tanda        	= $data['tanda'];
			$approve        = $data['approve'];
			$no_so        	= (!empty($data['no_so']))?$data['no_so']:NULL; 
			$project_name   = (!empty($data['project_name']))?$data['project_name']:NULL; 
			$id_dept 		= (!empty($data['id_dept']))?$data['id_dept']:NULL;
			$id_costcenter 	= (!empty($data['id_costcenter']))?$data['id_costcenter']:NULL;
			$coa 			= (!empty($data['coa']))?$data['coa']:NULL;
			$budget 		= str_replace(',','',$data['budget']);
			$sisa_budget 	= str_replace(',','',$data['sisa_budget']);
			
			$detail 		= $data['detail'];
			
			//approve
			$sts_app        = (!empty($data['sts_app']))?$data['sts_app']:'';
			$reason        	= (!empty($data['reason']))?$data['reason']:'';
			
			$ym = date('ym');
			
			
			
			if(empty($code_planx)){
				$srcMtr			= "SELECT MAX(no_pengajuan) as maxP FROM rutin_non_planning_header WHERE no_pengajuan LIKE 'PLN".$ym."%' ";
				$numrowMtr		= $this->db->query($srcMtr)->num_rows();
				$resultMtr		= $this->db->query($srcMtr)->result_array();
				$angkaUrut2		= $resultMtr[0]['maxP'];
				$urutan2		= (int)substr($angkaUrut2, 7, 3);
				$urutan2++;
				$urut2			= sprintf('%03s',$urutan2);
				$code_plan		= "PLN".$ym.$urut2;
			}
			
			$SUM_QTY = 0;
			$SUM_HARGA = 0;
			if(empty($approve)){
				$ArrDetail = array();
				if(!empty($detail)){
					foreach($detail AS $val => $valx){
						$qty 	= str_replace(',','',$valx['qty']);
						$harga 	= str_replace(',','',$valx['harga']);
						
						$SUM_QTY 	+= $qty;
						$SUM_HARGA 	+= $harga * $qty;
				
						$ArrDetail[$val]['no_pengajuan'] 	= $code_plan;
						$ArrDetail[$val]['nm_barang'] 		= strtolower($valx['nm_barang']);
						$ArrDetail[$val]['spec'] 			= strtolower($valx['spec']);
						$ArrDetail[$val]['satuan'] 			= $valx['satuan'];
						$ArrDetail[$val]['qty'] 			= $qty;
						$ArrDetail[$val]['harga'] 			= $harga;
						$ArrDetail[$val]['keterangan'] 		= strtolower($valx['keterangan']);
						$ArrDetail[$val]['tanggal'] 		= $valx['tanggal'];
						$ArrDetail[$val]['created_by'] 		= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['created_date'] 	= $dateTime;
					}
				}
			}

			//UPLOAD DOCUMENT
			$file_name = NULL;
			if(!empty($_FILES["upload_spk"]["name"])){
				$target_dir     = "assets/file/produksi/";
				$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
				$name_file      = 'lampiran_pr_dept_'.date('Ymdhis');
				$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
				$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
				$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
				$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
				$file_name    	= $name_file.".".$imageFileType;

				if(!empty($_FILES["upload_spk"]["tmp_name"])){
					$terupload = move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
				}
			}
			
			//header edit
			$ArrHeader		= array(
				'id_dept' 		=> $id_dept,
				'id_costcenter' => $id_costcenter,
				'coa' 			=> $coa,
				'budget' 		=> $budget,
				'no_so' 		=> $no_so,
				'project_name'	=> $project_name,
				'sisa_budget' 	=> $sisa_budget,
				'qty' 			=> $SUM_QTY,
				'harga' 		=> $SUM_HARGA,
				'document' 		=> $file_name,
				'updated_by'	=> $data_session['ORI_User']['username'],
				'updated_date'	=> $dateTime
			);
			
			//header insert
			if(empty($code_planx)){
				$ArrHeader		= array(
					'id_dept' 		=> $id_dept,
					'no_pengajuan' 	=> $code_plan,
					'id_costcenter' => $id_costcenter,
					'coa' 			=> $coa,
					'budget' 		=> $budget,
					'sisa_budget' 	=> $sisa_budget,
					'no_so' 		=> $no_so,
					'project_name'	=> $project_name,
					'qty' 			=> $SUM_QTY,
					'harga' 		=> $SUM_HARGA,
					'document' 		=> $file_name,
					'created_by'	=> $data_session['ORI_User']['username'],
					'created_date'	=> $dateTime
				);
			}
			
			//header approve
			if(!empty($approve)){
				$ArrDetail = array();
				$ArrDetailPR = array();
				
				$Ym = date('ym');
				$qIPP			= "SELECT MAX(no_pr) as maxP FROM tran_pr_header WHERE no_pr LIKE 'PRN".$Ym."%' ";
				$numrowIPP		= $this->db->query($qIPP)->num_rows();
				$resultIPP		= $this->db->query($qIPP)->result_array();
				$angkaUrut2		= $resultIPP[0]['maxP'];
				$urutan2		= (int)substr($angkaUrut2, 7, 4);
				$urutan2++;
				$urut2			= sprintf('%04s',$urutan2);
				$no_pr			= "PRN".$Ym.$urut2;
				
				

				$Ym = date('ym');
				$qIPPX			= "SELECT MAX(no_pr_group) as maxP FROM tran_pr_header WHERE no_pr_group LIKE 'PR".$Ym."%' ";
				$numrowIPPX		= $this->db->query($qIPPX)->num_rows();
				$resultIPPX		= $this->db->query($qIPPX)->result_array();
				$angkaUrut2X	= $resultIPPX[0]['maxP'];
				$urutan2X		= (int)substr($angkaUrut2X, 6, 4);
				$urutan2X++;
				$urut2X			= sprintf('%04s',$urutan2X); 
				$no_pr_group	= "PR".$Ym.$urut2X;
				
				$ArrHeaderPR = array(
					'no_pr' => $no_pr,
					'no_pr_group' => $no_pr_group,
					'category' => 'non rutin',
					'tgl_pr'	=> date('Y-m-d'),
					'created_by' => $this->session->userdata['ORI_User']['username'],
					'created_date' => date('Y-m-d H:i:s')
				);
				
				$SUM_QTY = 0;
				$SUM_HARGA = 0;
				if(!empty($detail)){
					foreach($detail AS $val => $valx){
						$qty 	= str_replace(',','',$valx['qty']);
						$harga 	= str_replace(',','',$valx['harga']);
						
						$SUM_QTY 	+= $qty;
						$SUM_HARGA 	+= $harga * $qty;
						
						$ArrDetail[$val]['id'] 			= $valx['id'];
						// $ArrDetail[$val]['no_pr'] 		= $no_pr;
						$ArrDetail[$val]['qty_rev'] 	= $qty;
						$ArrDetail[$val]['harga_rev'] 	= $harga;
						$ArrDetail[$val]['sts_app'] 	= $sts_app;
						$ArrDetail[$val]['sts_app_by'] 	= $data_session['ORI_User']['username'];
						$ArrDetail[$val]['sts_app_date']= $dateTime;
						
						
						$ArrDetailPR[$val]['no_pr'] 		= $no_pr;
						$ArrDetailPR[$val]['no_pr_group'] 	= $no_pr_group;
						$ArrDetailPR[$val]['category'] 		= 'non rutin';
						$ArrDetailPR[$val]['tgl_pr'] 		= date('Y-m-d');
						$ArrDetailPR[$val]['id_barang'] 	= $valx['id'];
						$ArrDetailPR[$val]['nm_barang'] 	= strtolower($valx['nm_barang'].' - '.$valx['spec']);
						$ArrDetailPR[$val]['qty'] 			= $qty;
						$ArrDetailPR[$val]['nilai_pr'] 		= $harga;
						$ArrDetailPR[$val]['tgl_dibutuhkan']= $valx['tanggal'];
						$ArrDetailPR[$val]['satuan']		= $valx['satuan'];
						$ArrDetailPR[$val]['spec']		= $valx['spec'];
						$ArrDetailPR[$val]['info']		= $valx['keterangan'];
						$ArrDetailPR[$val]['app_status'] 	= 'Y';
						$ArrDetailPR[$val]['app_reason']	= strtolower($valx['keterangan']);
						$ArrDetailPR[$val]['app_by'] = $data_session['ORI_User']['username'];
						$ArrDetailPR[$val]['app_date']= $dateTime;
						$ArrDetailPR[$val]['created_by'] 	= $data_session['ORI_User']['username'];
						$ArrDetailPR[$val]['created_date'] 	= $dateTime;
					}
				}
			
				$ArrHeader		= array(
					'qty_rev' 		=> $SUM_QTY,
					'harga_rev' 	=> $SUM_HARGA,
					// 'no_pr' 		=> $no_pr,
					'sts_app' 		=> $sts_app,
					'reason' 		=> $reason,
					'sts_app_by'	=> $data_session['ORI_User']['username'],
					'sts_app_date'	=> $dateTime
				);
				// print_r($ArrHeaderPR);
				// print_r($ArrDetailPR);
			}

			
			// print_r($ArrHeader);
			// print_r($ArrDetail);
			// exit;

			$link_approval = (!empty($approve))?'approval':'';
			
			$this->db->trans_start();
				if(empty($approve)){
					if(!empty($code_planx)){
						$this->db->where(array('no_pengajuan' => $code_planx));
						$this->db->update('rutin_non_planning_header', $ArrHeader);
						
						$this->db->where(array('no_pengajuan' => $code_planx));
						$this->db->delete('rutin_non_planning_detail');
						$this->db->insert_batch('rutin_non_planning_detail', $ArrDetail);
					}
					if(empty($code_planx)){
						$this->db->insert('rutin_non_planning_header', $ArrHeader);
						$this->db->insert_batch('rutin_non_planning_detail', $ArrDetail);
					}
				}
				if(!empty($approve)){
					$this->db->where(array('no_pengajuan' => $code_planx));
					$this->db->update('rutin_non_planning_header', $ArrHeader);
					
					$this->db->update_batch('rutin_non_planning_detail', $ArrDetail, 'id');
					
					// $this->db->insert('tran_pr_header', $ArrHeaderPR);
					// $this->db->insert_batch('tran_pr_detail', $ArrDetailPR);
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data failed. Please try again later ...',
					'status'	=> 0,
					'approve'	=> $link_approval
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> 'Process data success. Thanks ...',
					'status'	=> 1,
					'approve'	=> $link_approval
				);
				history($tanda.' pengajuan budget non rutin '.$code_plan);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/index';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['read'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('dashboard'));
			}
			
			$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
			$id 		= $this->uri->segment(3);
			$approve 	= $this->uri->segment(4);
			$header 	= $this->db->query("SELECT * FROM rutin_non_planning_header WHERE no_pengajuan='".$id."' ")->result();
			$detail 	= $this->db->query("SELECT * FROM rutin_non_planning_detail WHERE no_pengajuan='".$id."' ")->result_array();
			$datacoa 	= $this->db->query("SELECT a.coa,b.nama FROM coa_category a join ".DBACC.".coa_master b on a.coa=b.no_perkiraan WHERE a.tipe='NONRUTIN' order by a.coa")->result_array();
			$satuan		= $this->db->get_where('raw_pieces',array('delete'=>'N'))->result_array();
			$tanda 		= (!empty($header))?'Edit':'Add';
			if(!empty($approve)){
				$tanda 		= ($approve == 'view')?'View':'Approve';
			}
			$data = array(
				'title'				=> $tanda.' PR Departemen',
					'action'		=> strtolower($tanda),
					'akses_menu'	=> $Arr_Akses,
					'header'		=> $header,
					'detail'		=> $detail,
					'datacoa'		=> $datacoa,
					'satuan'		=> $satuan,
					'approve'		=> $approve,
					'id'			=> $id 
			);
			
			$this->load->view('Non_rutin/add',$data);
		}
	}
	
	public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;
		$satuan		= $this->db->get_where('raw_pieces',array('delete'=>'N'))->result_array();

		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='center'>".$id."</td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][nm_barang]' class='form-control input-md'></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][spec]' class='form-control input-md'></td>";
			$d_Header .= "<td align='left'><input type='text' id='qty_".$id."' name='detail[".$id."][qty]' class='form-control input-md text-center autoNumeric2 sum_tot'></td>";
			$d_Header .= "<td align='left'><select name='detail[".$id."][satuan]' class='form-control chosen_select wajib' required>";
				$d_Header .= "<option value='0'>Pilih</option>";
				foreach ($satuan as $key => $value) {
					$d_Header .= "<option value='".$value['id_satuan']."'>".$value['kode_satuan']."</option>";
				}
			$d_Header .= "	</select></td>";
			$d_Header .= "<td align='left'><input type='text' id='harga_".$id."' name='detail[".$id."][harga]' class='form-control input-md text-right maskM sum_tot' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
			$d_Header .= "<td align='left'><input type='text' id='total_harga_".$id."' name='detail[".$id."][total_harga]' class='form-control input-md text-right maskM jumlah_all' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][tanggal]' class='form-control input-md text-center datepicker tgl_dibutuhkan' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' name='detail[".$id."][keterangan]' class='form-control input-md'></td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";


		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-warning addPart' title='Add Barang'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Barang</button></td>";
			$d_Header .= "<td align='center' colspan='7'></td>";
		$d_Header .= "</tr><script>$('.autoNumeric2').autoNumeric('init', {mDec: '2', aPad: false});</script>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}
	
	public function approval(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/approval';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$tanda				= $this->uri->segment(2);
		$data = array(
			'title'			=> 'Approval PR Departemen',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'tanda'			=> $tanda
		);
		history('View data approval pr department (non-rutin)');
		$this->load->view('Non_rutin/index',$data);
	}
	
	public function print_pengajuan_non_rutin(){
		$kode_trans     = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
		
		$header 	= $this->db->query("SELECT * FROM rutin_non_planning_header WHERE no_pengajuan='".$kode_trans."' ")->result();
		$detail 	= $this->db->query("SELECT * FROM rutin_non_planning_detail WHERE no_pengajuan='".$kode_trans."' ")->result_array();
		$datacoa 	= $this->db->query("SELECT * FROM coa_category WHERE tipe='NONRUTIN' ")->result_array();

		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'header' => $header,
			'detail' => $detail,
			'datacoa' => $datacoa
		);
		
		history('Print pengajuan non rutin '.$kode_trans);
		$this->load->view('Print/print_pengajuan_non_rutin', $data);
	}
	
	
}