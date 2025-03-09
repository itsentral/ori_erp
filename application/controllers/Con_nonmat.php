<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Con_nonmat extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('serverside_model');
		$this->load->model('rutin_model');

		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}

	public function index(){
		$this->rutin_model->index_rutin();
	}
	
	public function data_side_rutin(){
		$this->rutin_model->get_json_rutin();
	}
	
	public function pr_rutin(){
		$this->rutin_model->pr_rutin_new();
	}
	
	public function warehouse_rutin(){
		$this->rutin_model->warehouse_rutin();
	}
	
	public function save_rutin(){
		$this->rutin_model->save_rutin();
	}

	public function save_rutin_change(){
		$this->rutin_model->save_rutin_change();
	}

	public function save_rutin_change_date(){
		$this->rutin_model->save_rutin_change_date();
	}

	public function auto_update_rutin(){
		$this->rutin_model->auto_update_rutin();
	}

	public function clear_update_rutin(){
		$this->rutin_model->clear_update_rutin();
	}

	public function save_pr_rutin_all(){
		$this->rutin_model->save_pr_rutin_all();
	}
	
	public function data_side_warehouse_rutin(){
		$this->rutin_model->get_json_warehouse_rutin();
	}
	
	public function server_side_app_rutin(){
		$this->rutin_model->get_data_json_pr_rutin();
	}
	
	public function add_pr(){
		$this->rutin_model->add_pr();
	}
	
	public function approval_pr_rutin(){
		$this->rutin_model->index_approval_pr_rutin();
	}
	
	public function modal_detail_pr(){
		$this->rutin_model->modal_detail_pr_rutin();
	}

	public function modal_detail_pr_rutin_edit(){
		$no_ipp 	= $this->uri->segment(3);
		$sts_app 	= $this->uri->segment(4);
		$tanda 		= $this->uri->segment(5);
		$id_user 	= $this->input->post('id_user');
		$pengajuangroup 	= $this->input->post('pengajuangroup');
		$user 		= get_name('users','username','id_user',$id_user);
		$where = " AND a.category_awal = '2' ";
		if($tanda == ''){
			$where = " AND a.category_awal <> '2'  ";
		}

		$where = "";
		
		$sql		= "SELECT a.*, b.sts_app FROM rutin_planning_detail a LEFT JOIN rutin_planning_header b ON a.no_pengajuan=b.no_pengajuan WHERE b.no_pengajuan_group='".$pengajuangroup."' AND a.purchase > 0 AND a.sts_app = '".$sts_app."' ".$where."  ORDER BY a.nm_material ASC";
		$result = $this->db->query($sql)->result_array();

		$data = array(
		  'GET_COMSUMABLE'	=> get_detail_consumable(),
		  'no_ipp'			=> $no_ipp,
		  'pengajuangroup'	=> $pengajuangroup,
		  'result'			=> $result
		);
		$this->load->view('Rutin/modal_detail_pr_edit',$data);
	}

	public function modal_detail_pr_rutin_edit_save(){
		$data = $this->input->post();
		$pengajuangroup = $data['pengajuangroup'];
		$UserName 		= $this->session->userdata['ORI_User']['username'];
		$DateTime 		= date('Y-m-d H:i:s');

		$Ym = date('ym');
		$qIPP			= "SELECT MAX(no_pengajuan) as maxP FROM rutin_planning_header WHERE no_pengajuan LIKE 'R".$Ym."%' ";
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 5);

		
		$ArrayUpdate = [];
		$ArrayInsert = [];
		$ArrayInsertHeader = [];
		if(!empty($data['update_data'])){
			foreach ($data['update_data'] as $key => $value) {
				$ArrayUpdate[$key]['id'] 		= $key;
				$ArrayUpdate[$key]['purchase'] 	= str_replace(',','',$value['qty']);
			}
		}
		$tgl_dibutuhkan = $value['tanggal'];
		
		if(!empty($data['detail'])){
			foreach ($data['detail'] as $key => $value) {
				$urutan2++;
				$urut2			= sprintf('%05s',$urutan2);
				$kodeP			= "R".$Ym.$urut2;
				
				$get_rutin 	= $this->db->get_where('con_nonmat_new',array('code_group'=>$value['id_barang']))->result_array();
				$ArrayInsert[$key]['no_pengajuan'] 	= $kodeP;
				$ArrayInsert[$key]['id_material'] 	= $value['id_barang'];
				$ArrayInsert[$key]['category_awal'] = $get_rutin[0]['category_awal'];
				$ArrayInsert[$key]['spec'] 			= $get_rutin[0]['spec'];
				$ArrayInsert[$key]['satuan'] 		= $get_rutin[0]['satuan'];
				$ArrayInsert[$key]['nm_material'] 	= $get_rutin[0]['material_name'];
				$ArrayInsert[$key]['purchase'] 		= str_replace(',','',$value['qty']);
				$ArrayInsert[$key]['tanggal'] 		= (!empty($value['dibutuhkan']))?date('Y-m-d',strtotime($value['dibutuhkan'])):$tgl_dibutuhkan;
				$ArrayInsert[$key]['spec_pr'] 		= $value['spec'];
				$ArrayInsert[$key]['info_pr'] 		= $value['info'];

				$ArrayInsertHeader[$key]['no_Pengajuan_group']= $pengajuangroup;
				$ArrayInsertHeader[$key]['no_pengajuan'] 		= $kodeP;
				$ArrayInsertHeader[$key]['purchase'] 			= str_replace(',','',$value['qty']);
				$ArrayInsertHeader[$key]['book_by'] 			= $UserName;
				$ArrayInsertHeader[$key]['book_date'] 		= $DateTime;
				$ArrayInsertHeader[$key]['created_by'] 		= $UserName;
				$ArrayInsertHeader[$key]['created_date'] 		= $DateTime;
			}
		}

		// echo "<pre>";
		// print_r($ArrayInsertHeader);
		// print_r($ArrayInsert);
		// exit;
		
		$this->db->trans_start();
			if(!empty($ArrayUpdate)){
				$this->db->update_batch('rutin_planning_detail', $ArrayUpdate, 'id');
			}
			if(!empty($ArrayInsertHeader)){
				$this->db->insert_batch('rutin_planning_header', $ArrayInsertHeader);
			}
			if(!empty($ArrayInsert)){
				$this->db->insert_batch('rutin_planning_detail', $ArrayInsert);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history('Change qty purchase pr stok, kode: '.$pengajuangroup);
			}
  		echo json_encode($Arr_Data);
	}

	public function get_add(){
		$id = $this->uri->segment(3);
		$id_category = $this->uri->segment(4);
		$jenis_barang		= $this->db->select('code_group,material_name,spec')->get_where('con_nonmat_new',array('category_awal'=>$id_category,'deleted'=>'N'))->result_array();

		$d_Header = "";
		// $d_Header .= "<tr>";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='center'>".$id."</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][id_barang]' data-no='".$id."' class='chosen_select form-control input-sm getSpec'>";
				$d_Header .= "<option value='0'>Select Barang</option>";
				foreach($jenis_barang AS $val => $valx){
				  $d_Header .= "<option value='".$valx['code_group']."'>".strtoupper($valx['material_name']." - ".$valx['spec'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left' class='rutin_category'></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][qty]' class='form-control text-center input-md numberOnly2'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left' class='rutin_unit'></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][dibutuhkan]' class='form-control text-center input-md datepicker' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][spec]' class='form-control text-left'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][info]' class='form-control text-left'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'><button type='button' data-category='".$id_category."' class='btn btn-sm btn-success addPart' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center' colspan='7'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

	public function print_detail_pr(){
		$this->rutin_model->print_detail_pr_rutin();
	}
	
	public function modal_edit_pr(){
		$this->rutin_model->modal_edit_pr();
	}
	
	public function modal_approve_pr(){
		$this->rutin_model->modal_approve_pr_rutin();
	}
	
	public function server_side_app_pr_rutin(){
		$this->rutin_model->get_data_json_app_pr_rutin(); 
	}

	public function server_side_pr_rutin(){
		$this->rutin_model->get_data_json_pr_rutin_new(); 
	}
	
	public function reject_sebagian_pr_rutin(){
		$this->rutin_model->reject_sebagian_pr_rutin();
	}

	public function reject_all_pr_rutin(){
		$this->rutin_model->reject_all_pr_rutin();
	}
	
	public function approve_pr_rutin(){
		$this->rutin_model->approve_pr_rutin();
	}
	
	public function pdf_report(){
		$this->rutin_model->pdf_report();
	}
	

	public function data_side_consumable(){
		$this->serverside_model->get_json_consumable();
	}

	public function modalDetail(){
		$code_group = $this->uri->segment(3);

		$qHeader 	= "SELECT a.*, b.category AS cate_awal FROM con_nonmat_new a LEFT JOIN con_nonmat_category_awal b ON a.category_awal=b.id WHERE a.code_group='".$code_group."'";
		$qDetailKon = "SELECT * FROM con_nonmat_new_konversi WHERE code_group='".$code_group."' AND deleted='N'";
		$qDetailMat = "SELECT * FROM con_nonmat_new_add WHERE code_group='".$code_group."' AND category='material' AND deleted='N'";
		$qDetailSup = "SELECT * FROM con_nonmat_new_add WHERE code_group='".$code_group."' AND category='supplier' AND deleted='N'";

		$restHeader = $this->db->query($qHeader)->result();
		$restDetKon = $this->db->query($qDetailKon)->result_array();
		$restDetMat = $this->db->query($qDetailMat)->result_array();
		$restDetSup = $this->db->query($qDetailSup)->result_array();

		$data = array(
			'header'		=> $restHeader,
			'konversi'	=> $restDetKon,
			'material'	=> $restDetMat,
			'supplier'	=> $restDetSup
		);

		$this->load->view('Con_nonmat/modalDetail', $data);
	}

	public function add_new(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			// print_r($data);
			// exit;
			$code_group		= $data['code_group'];
			$tanda_edit		= strtolower($data['tanda_edit']);

			$category_awal	= $data['category_awal'];
			$id_category_acc	= $data['id_category_acc'];
			$id_acc				= $data['id_acc'];
			$kode_excel		= trim($data['kode_excel']);
			$kode_item		= trim($data['kode_item']);
			$material_name	= trim($data['material_name']);
			$id_accurate	= trim($data['id_accurate']);
			$trade_name		= trim($data['trade_name']);
			$spec			= trim($data['spec']);
			$brand			= trim($data['brand']);

			$min_order		= str_replace(',','',$data['min_order']);
			$lead_time		= str_replace(',','',$data['lead_time']);
			$konversi		= str_replace(',','',$data['konversi']);

			$satuan				= $data['satuan'];
			$satuan_konversi	= $data['satuan_konversi'];
			$no_rak				= trim($data['no_rak']);
			$note				= trim($data['note']);
			$status				= (!empty($data['status']))?1:0;
			
			$Hist = (empty($tanda_edit))?'Add ':'Edit ';
			$LastBy = (empty($tanda_edit))?'created_by':'updated_by';
			$LastDate = (empty($tanda_edit))?'created_date':'updated_date';
			//insert
			if(empty($tanda_edit)){
				//pengurutan kode
				$srcMtr			= "SELECT MAX(code_group) as maxP FROM con_nonmat_new WHERE code_group LIKE 'CN%' ";
				$numrowMtr		= $this->db->query($srcMtr)->num_rows();
				$resultMtr		= $this->db->query($srcMtr)->result_array();
				$angkaUrut2		= $resultMtr[0]['maxP'];
				$urutan2		= (int)substr($angkaUrut2, 2, 5);
				$urutan2++;
				$urut2			= sprintf('%05s',$urutan2);
				$code_group		= "CN".$urut2;
			}
				
			//header
			$ArrInsert = array(
				'code_group' => $code_group,
				'category_awal' => $category_awal,
				'id_category_acc' => $id_category_acc,
				'id_acc' => $id_acc,
				'kode_excel' => $kode_excel,
				'kode_item' => $kode_item,
				'id_accurate' => $id_accurate,
				'material_name' => $material_name,
				'trade_name' => $trade_name,
				'spec' => $spec,
				'brand' => $brand,
				'min_order' => $min_order,
				'lead_time' => $lead_time,
				'konversi' => $konversi,
				'satuan' => $satuan,
				'satuan_konversi' => $satuan_konversi,
				'no_rak' => $no_rak,
				'note' => $note,
				'status' => $status,
				$LastBy => $data_session['ORI_User']['username'],
				$LastDate => $dateTime
			);

			$gudang_awal = 10;
			if($category_awal == '2' OR $category_awal == '10'){
				$gudang_awal = 24;
			}
			
			$ArrInsertWarehouse = array(
				'code_group' 	=> $code_group,
				'category_awal' => $category_awal,
				'category_code' => '121',
				'material_name' => $material_name,
				'gudang'		=> $gudang_awal,
				'update_by' 	=> $data_session['ORI_User']['username'],
				'update_date' 	=> $dateTime
			);

			$ArrUpdateWarehouse = array(
				'category_awal' => $category_awal,
				'gudang'		=> $gudang_awal,
				'update_by' 	=> $data_session['ORI_User']['username'],
				'update_date' 	=> $dateTime
			);

			//MASUK AKSESORIS
			$ArrAKsesoris = [];
			// if($category_awal == '7'){
				$ArrAKsesoris = array(
					'id_material' => $code_group,
					'category' => $id_category_acc,
					'nama' => $material_name,
					'spesifikasi' => $spec,
					'satuan' => $satuan,
					'keterangan' => $note,
					'dimensi' => $status,
					$LastBy => $data_session['ORI_User']['username'],
					$LastDate => $dateTime
				);
			// }

			$this->db->trans_start();
				if(empty($tanda_edit)){
					$this->db->insert('con_nonmat_new', $ArrInsert);
					$this->db->insert('warehouse_rutin_stock', $ArrInsertWarehouse);

					if(!empty($ArrAKsesoris)){
						$this->db->insert('accessories', $ArrAKsesoris);
					}
				}
				else{
					$this->db->where('code_group', $code_group);
					$this->db->update('con_nonmat_new', $ArrInsert);

					$this->db->where('code_group', $code_group);
					$this->db->update('warehouse_rutin_stock', $ArrUpdateWarehouse);

					if(!empty($ArrAKsesoris)){
						$this->db->where('id_material', $code_group);
						$this->db->update('accessories', $ArrAKsesoris);
					}
				}
			$this->db->trans_complete();


			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $Hist.' data success. Thanks ...',
					'status'	=> 1
				);
				history($Hist.'Rutin '.$code_group);
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

			$code_group = $this->uri->segment(3);

			$tanda1 = 'Add';
			if(!empty($code_group)){
				$tanda1 = 'Edit';
			}

			$restHeader = $this->db->get_where('con_nonmat_new',array('code_group'=>$code_group))->result();

			$query	 	= "SELECT * FROM raw_pieces WHERE flag_active = 'Y' AND `delete` = 'N' ORDER BY kode_satuan ASC";
 			$Q_result	= $this->db->query($query)->result_array();
			$restCateMPUtama	= $this->db->order_by('category','asc')->get('con_nonmat_category_awal')->result_array();
			$categoryAcc = $this->db->get_where('accessories_category',array('id <>'=>5))->result_array();
			$data = array(
				'title'			=> $tanda1.' Barang Stok',
				'action'		=> 'add',
				'cateMPUtama'	=> $restCateMPUtama,
				'header'		=> $restHeader,
				'categoryAcc'		=> $categoryAcc,
				'satuan'		=> $Q_result
			);
			$this->load->view('Con_nonmat/add',$data);
		}
	}

	public function add_category(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			$add_category	= strtolower($data['add_category']);
			$add_category_awal	= strtolower($data['add_category_awal']);
			$information	= strtolower($data['information']);
			$code_group	= strtolower($data['code_group']);

			// echo $tanda_category;
			// exit;
			//Pencarian data yang sudah ada
			$ValueProduct	= "SELECT * FROM con_nonmat_category WHERE category='".$add_category."' AND category_awal='".$add_category_awal."' ";
			$NumProduct		= $this->db->query($ValueProduct)->num_rows();

			if($NumProduct > 0){
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> 'Category Consumable Non Material sudah digunakan. Input catgeory lain ...'
				);
			}
			else{
				$ArrInsert = array(
					'category' => $add_category,
					'category_awal' => $add_category_awal,
					'information' => $information,
					'created_by' => $data_session['ORI_User']['username'],
					'created_date' => $dateTime
				);

				$this->db->trans_start();
				$this->db->insert('con_nonmat_category', $ArrInsert);
				$this->db->trans_complete();


				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Kembali	= array(
						'pesan'		=> 'Add Category data failed. Please try again later ...',
						'status'	=> 2,
						'code_group'	=> $code_group
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Kembali	= array(
						'pesan'		=> 'Add Category data success. Thanks ...',
						'status'	=> 1,
						'code_group'	=> $code_group
					);
					history('Add Category '.$add_category);
				}
			}

			echo json_encode($Arr_Kembali);
		}
	}

	public function hapus(){
		$code_group = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);

		$this->db->trans_start();
				$this->db->where('code_group', $code_group);
				$this->db->update('con_nonmat_new', $ArrPlant);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete Consumable Non Material data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete Consumable Non Material data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Consumable Non Material category : '.$code_group);
		}
		echo json_encode($Arr_Data);
	}

	public function ExcelMasterDownload($category,$status){
		//membuat objek PHPExcel
		set_time_limit(0);
		ini_set('memory_limit','1024M');

		$this->load->library("PHPExcel");
		// $this->load->library("PHPExcel/Writer/Excel2007");
		$objPHPExcel	= new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'f2f2f2'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_header2 = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'59c3f7'),
			),
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$styleArray = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		$styleArray3 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
		 $styleArray4 = array(
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			  ),
			  'borders' => array(
				'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN,
					  'color' => array('rgb'=>'000000')
				  )
			)
		  );
	    $styleArray1 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );
		$styleArray2 = array(
			  'borders' => array(
				  'allborders' => array(
					  'style' => PHPExcel_Style_Border::BORDER_THIN
				  )
			  ),
			  'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			  )
		  );

		$Arr_Bulan	= array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		$sheet 		= $objPHPExcel->getActiveSheet();

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'MASTER STOK');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow +1;

		$sheet->setCellValue('A'.$NewRow, 'No');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(5);

		$sheet->setCellValue('B'.$NewRow, 'Code Program');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'Category');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setWidth(20);

		$sheet->setCellValue('D'.$NewRow, 'Excel Code');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setWidth(20);

		$sheet->setCellValue('E'.$NewRow, 'Item Code');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(20);

		$sheet->setCellValue('F'.$NewRow, 'Accurate Code');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(20);

		$sheet->setCellValue('G'.$NewRow, 'Material Name');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(20);

		$sheet->setCellValue('H'.$NewRow, 'Trade Name');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(20);

		$sheet->setCellValue('I'.$NewRow, 'Spec');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(20);

		$sheet->setCellValue('J'.$NewRow, 'Brand');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(20);

		$sheet->setCellValue('K'.$NewRow, 'Status');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(20);

		$where_inventory = "";
		if($category != '0'){
			$where_inventory = " AND a.category_awal = '".$category."' ";
		}

		$where_status = "";
		if($status != 'X'){
			$where_status = " AND a.status = '".$status."' ";
		}
		
		$SQL = "SELECT
					a.*,
					b.category AS categoryb
				FROM
					con_nonmat_new a LEFT JOIN con_nonmat_category_awal b ON a.category_awal = b.id
				WHERE a.code_group LIKE 'CN%'
					AND a.deleted='N' ".$where_inventory." ".$where_status." ORDER BY category_awal, a.material_name ";
		$row		= $this->db->query($SQL)->result_array();

		if($row){
			$awal_row	= $NextRow;
			$no=0;
			foreach($row as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$nomor	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nomor);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$code_group	= strtoupper($row_Cek['code_group']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $code_group);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$categoryb	= strtoupper($row_Cek['categoryb']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $categoryb);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_excel	= strtoupper($row_Cek['kode_excel']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_excel);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$kode_item	= strtoupper($row_Cek['kode_item']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kode_item);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$id_accurate	= strtoupper($row_Cek['id_accurate']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $id_accurate);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$material_name	= strtoupper($row_Cek['material_name']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $material_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$trade_name	= strtoupper($row_Cek['trade_name']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $trade_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$spec	= strtoupper($row_Cek['spec']);
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $spec);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$brand	= strtoupper($row_Cek['brand']);
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $brand);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$status	= 'Active';
				if($row_Cek['status'] == 0){
					$status	= 'Not Active';
				}

				$awal_col++;
				$Cols	= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $status);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);


			}
		}

		$sheet->setTitle('Master Stok');
		//mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
		$objWriter		= PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		//sesuaikan headernya
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//ubah nama file saat diunduh
		header('Content-Disposition: attachment;filename="master-stok.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function list_supplier(){
			$code_group = $this->uri->segment(3);
			// echo $code_group;
			$qDetailSup = "SELECT * FROM con_nonmat_new_add WHERE code_group='".$code_group."' AND category='supplier' AND deleted='N'";
			$restDetSup = $this->db->query($qDetailSup)->result_array();

			$supplierx = '';
			if(!empty($restDetSup)){
				$ArrData1 = array();
				foreach($restDetSup as $vaS => $vaA){
					 $ArrData1[] = $vaA['value'];
				}
				$ArrData1 = implode("," ,$ArrData1);
				$supplierx = explode("," ,$ArrData1);
			}

			$query	 	= "SELECT id_supplier, nm_supplier FROM supplier ORDER BY nm_supplier ASC";
			$Q_result	= $this->db->query($query)->result();
			$option 	= "";
			foreach($Q_result as $row){
				$sel3 = '';
				if(!empty($supplierx)){
					$sel3 = (isset($supplierx) && in_array($row->id_supplier, $supplierx))?'selected':'';
				}
			 	$option .= "<option value='".$row->id_supplier."' ".$sel3.">".strtoupper($row->nm_supplier)."</option>";
			}
		echo json_encode(array(
			'option' => $option
		));
	 }

	public function list_satuan(){
		$query	 	= "SELECT * FROM raw_pieces WHERE flag_active = 'Y' ORDER BY kode_satuan ASC";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "";
		foreach($Q_result as $row)	{
		 $option .= "<option value='".$row->kode_satuan."'>".strtoupper($row->nama_satuan)."</option>";
		}
	echo json_encode(array(
		'option' => $option
	));
 }

	public function get_category(){
		$id 		= $this->uri->segment(3);
		$code_group = $this->uri->segment(4);
		
		$get_code = get_name('con_nonmat_new', 'category_code', 'code_group', $code_group);
		
		$query	 	= "SELECT * FROM con_nonmat_category WHERE category_awal = '".$id."' ORDER BY category ASC";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select Category</option>";
		foreach($Q_result as $row)	{
			$selx = (!empty($code_group) AND $row->id == $get_code)?'selected':'';
			$option .= "<option value='".$row->id."' ".$selx.">".strtoupper($row->category)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	
	
	public function satuan(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		$get_Data			= $this->master_model->getData('con_nonmat_category_awal');
		$menu_akses			= $this->master_model->getMenu();
		
		$data = array(
			'title'			=> 'Kategori Stok',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'data_menu'		=> $menu_akses,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Inventory Type');
		$this->load->view('Con_nonmat/satuan',$data);
	}
	
	public function satuan_add(){		
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			
			$category			= strtolower($data['category']);
			$descr				= $data['descr'];
			$coa				= $data['coa'];
			
			
			//check kode satuan
			$qNmSatu	= "SELECT * FROM con_nonmat_category_awal WHERE category = '".$category."' ";
			$numNmSt	= $this->db->query($qNmSatu)->num_rows();
			
			// echo $numType; exit;
			$data	= array(
				'category' 	=> $category,
				'descr' 		=> $descr,
				'coa' 			=> $coa,
				'flag_active' 	=> 'Y',
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// echo "<pre>"; print_r($data);
			if($numNmSt > 0){
				$Arr_Kembali		= array(
					'status'		=> 4,
					'pesan'			=> 'Inventory Type name already exists. Please check back ...'
				);
			}
			else{
				if($this->master_model->simpan('con_nonmat_category_awal',$data)){
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Add Success. Thank you & have a nice day.......'
					);
					history('Add Inventory Type with code '.$category);
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Add failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menu'));
			}
			$arr_Where			= array('flag_active'=>'1');
			$get_Data			= $this->master_model->getMenu($arr_Where);
			$datacoa			= $this->master_model->GetCoaCombo();
			$data = array(
				'title'			=> 'Add Inventory Type',
				'action'		=> 'add',
				'data_menu'		=> $get_Data,
				'datacoa'		=> $datacoa
			);
			$this->load->view('Con_nonmat/satuan_add',$data);
		}
	}
	
	public function satuan_edit(){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			$data					= $this->input->post();
			$Arr_Kembali			= array();
			$id_satuan				= $this->input->post('id');
			$category				= strtolower($this->input->post('category'));
			$flag_active			= ($this->input->post('flag_active') == 'Y')?'Y':'N';
			$descr					= $this->input->post('descr');
			$data_session			= $this->session->userdata;
			$coa					= $this->input->post('coa');		

			
			//check kode satuan
			$qNmSatu	= "SELECT * FROM con_nonmat_category_awal WHERE category = '".$category."' ";
			$numNmSt	= $this->db->query($qNmSatu)->num_rows();
			
			$Arr_Update = array(
				'coa' 				=> $coa,
				'category' 			=> $category,
				'descr' 			=> $descr,
				'flag_active' 		=> $flag_active,
				'modified_by' 		=> $data_session['ORI_User']['username'],
				'modified_date' 	=> date('Y-m-d H:i:s')
			);
			// echo "<pre>"; print_r($Arr_Update);
			// exit;
			$this->db->trans_start();
			$this->db->where('id', $id_satuan);
			$this->db->update('con_nonmat_category_awal', $Arr_Update);
			$this->db->trans_complete();
			if($numNmSt > 0){
				$Arr_Data		= array(
					'status'		=> 4,
					'pesan'			=> 'Inventory Type name already exists. Please check back ...'
				);
			}
			else{
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$Arr_Data	= array(
						'pesan'		=>'Update Inventory Type data failed. Please try again later ...',
						'status'	=> 2
					);
				}
				else{
					$this->db->trans_commit();
					$Arr_Data	= array(
						'pesan'		=>'Update Inventory Type data success. Thanks ...',
						'status'	=> 1
					);
					history('Update Inventory Type ['.$id_satuan.'] with username : '.$data_session['ORI_User']['username']);
				}
			}
			// print_r($Arr_Data); exit; 
			echo json_encode($Arr_Data);
		}
		else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('menus'));
			}
			
			$id = $this->uri->segment(3);
			
			// $detail				= $this->master_model->getData('raw_pieces','id_category',$id);  
			$detail		= $this->db->query("SELECT * FROM con_nonmat_category_awal WHERE id = '".$id."' ")->result_array();
			$datacoa		= $this->master_model->GetCoaCombo();
			$data = array(
				'title'			=> 'Edit Inventory Type',
				'action'		=> 'edit',
				'row'			=> $detail,
				'datacoa'			=> $datacoa
			);
			
			$this->load->view('Con_nonmat/satuan_edit',$data);   
		}
	}

	function satuan_hapus(){
		$idCategory = $this->uri->segment(3);
		// echo $idCategory; exit;
		//nm satuan yang dihapus untuk history
		$qNmStuan	= "SELECT * FROM con_nonmat_category_awal WHERE id='".$idCategory."' ";
		$restDtSt	= $this->db->query($qNmStuan)->result_array();
		$kd_satuan	= $restDtSt[0]['category'];
		
		$this->db->trans_start();
		$this->db->where('id', $idCategory);
		$this->db->delete('con_nonmat_category_awal');
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete Inventory Type data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete Inventory Type data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Inventory Type with Kode/Id : '.$kd_satuan.'/'.$idCategory);
		}
		echo json_encode($Arr_Data);
	}
	function coa_conmat_parameter(){
		$controller			= 'con_nonmat/coa_conmat_parameter';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data_row			= $this->db->query("SELECT a.*, b.nm_costcenter, c.category nm_category, d.nama FROM con_nonmat_category_costcenter a left join costcenter b on a.costcenter=b.id left join con_nonmat_category_awal c on a.category=c.id left join ".DBACC.".coa_master d on a.coa_biaya=d.no_perkiraan order by nm_category, nm_costcenter, nama")->result();
		$data_costcenter 	= $this->db->query("SELECT * FROM costcenter order by nm_costcenter")->result();
		$data_category 		= $this->db->query("SELECT * FROM con_nonmat_category_awal order by category")->result();
		$data_coa			= $this->master_model->GetCoaCombo();
		$data = array(
			'title'			=> 'Index Of Outgoing Stock Parameter',
			'action'		=> 'coa_conmat_parameter',
			'row_group'		=> $data_Group,
			'data_row'		=> $data_row,
			'data_costcenter'	=> $data_costcenter,
			'data_category'	=> $data_category,
			'data_coa'		=> $data_coa,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Outgoing Stock Parameter');
		$this->load->view('Con_nonmat/coa_conmat_parameter',$data);
	}
	public function add_data(){
		$controller			= 'con_nonmat/coa_conmat_parameter';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$id 		    = $data['id'];
			$category		= ($data['category']);
			$costcenter		= $data['costcenter'];
			$coa_biaya		= $data['coa_biaya'];
			if(empty($id)){
                $ArrHeader = array(
                    'category'		=> $category,
                    'costcenter'	=> $costcenter,
                    'coa_biaya'		=> $coa_biaya,
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'category'		=> $category,
                    'costcenter'	=> $costcenter,
                    'coa_biaya'		=> $coa_biaya,
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('con_nonmat_category_costcenter', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('con_nonmat_category_costcenter', $ArrHeader);
                }
            $this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 0
				);
			} else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success.',
					'status'	=> 1
				);
				history($TandaI.' Outgoing Stock Parameter '.$id);
			}

			echo json_encode($Arr_Kembali);
		}
	}
	public function hapus_data(){
		$controller			= 'con_nonmat/coa_conmat_parameter';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['delete'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$id = $this->uri->segment(3);
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('con_nonmat_category_costcenter');
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Outgoing Stock Parameter Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
}
