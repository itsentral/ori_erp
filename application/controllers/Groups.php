<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_input_vars', 5000);
class Groups extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->database();
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
		
		$get_Data			= $this->master_model->getData('groups','id <>','1');
		
		
		$data = array(
			'title'			=> 'Indeks Of Access Group',
			'action'		=> 'index',
			'row'			=> $get_Data,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Group');
		$this->load->view('Groups/index',$data);
	}
	public function add(){
		if($this->input->post()){
			$Group_Name			= $this->input->post('name');
			$Keterangan			= $this->input->post('descr');
			$Cek_Data			= $this->master_model->getCount('groups',"LOWER(name)",strtolower($Group_Name));
			if($Cek_Data > 0){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Group Already Exist. Please Different Group Name.......'
				);
			}else{
				$data_session		= $this->session->userdata;
				$det_Insert			= array(
					'name'				=> ucwords(strtolower($Group_Name)),
					'descr'				=> $Keterangan,
					'created'			=> date('Y-m-d H:i:s'),
					'created_by'		=> $data_session['ORI_User']['username']
					
				);
				if($this->master_model->simpan('groups',$det_Insert)){
					$Get_Data			= $this->master_model->getData('groups',"LOWER(name)",strtolower($Group_Name));
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Add Group Success. Thank you & have a nice day.......',
						'urut'			=> $Get_Data[0]->id
					);
					history('Add Data Group'.$Group_Name);
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Add Group failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('groups'));
			}
			$data = array(
				'title'			=> 'ADD GROUP',
				'action'		=> 'add'
			);
			
			$this->load->view('Groups/add_group',$data);
		}
	}
	public function access_menu($id=''){
		if($this->input->post()){
			//echo"<pre>";print_r($this->input->post());exit;
			
			$Group_id				= $this->input->post('id');
			$Cek_Data				= $this->master_model->getCount('group_menus','group_id',$Group_id);
			
			$data_session			= $this->session->userdata;
			$Jam					= date('Y-m-d H:i:s');
			$Arr_Detail				= array();
			$Loop					= 0;
			$dataDetail				= $this->input->post('tree');
			foreach($dataDetail as $key=>$value){
				if(isset($value['read']) || isset($value['create']) || isset($value['update']) || isset($value['delete']) || isset($value['approve']) || isset($value['download'])){
					$Loop++;
					$a_read			= (isset($value['read']) && $value['read'])?$value['read']:0;
					$a_create		= (isset($value['create']) && $value['create'])?$value['create']:0;
					$a_update		= (isset($value['update']) && $value['update'])?$value['update']:0;
					$a_delete		= (isset($value['delete']) && $value['delete'])?$value['delete']:0;
					$a_download		= (isset($value['download']) && $value['download'])?$value['download']:0;
					$a_approve		= (isset($value['approve']) && $value['approve'])?$value['approve']:0;
					$det_Detail		= array(
						'menu_id'		=> $value['menu_id'],
						'group_id'		=> $Group_id,
						'read'			=> $a_read,
						'create'		=> $a_create,
						'update'		=> $a_update,
						'delete'		=> $a_delete,
						'approve'		=> $a_approve,
						'download'		=> $a_download,
						'created'		=> $Jam,
						'created_by'	=> $data_session['ORI_User']['username']
					);
					$Arr_Detail[$Loop]	= $det_Detail;
					
				}
			}
			$this->db->trans_begin();
			if($Cek_Data > 0){
				$Q_Del				= "DELETE FROM `group_menus` WHERE `group_id`='".$Group_id."'";
				$this->db->query($Q_Del);
			}
			$this->db->insert_batch('group_menus',$Arr_Detail);
			
			
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Manage Access Group Failed. Please Try Again.......'
				);
			}else{
				$this->db->trans_commit();
				$Arr_Kembali		= array(
					'status'		=> 1,
					'pesan'			=> 'Manage Access Group Success. Thank you & have a nice day.......'
				);				
				history('Manage Access Group '.$this->input->post('name'));
				
			}			
			echo json_encode($Arr_Kembali);
		}else{		
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1' || $Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('group'));
			}
			
			$get_Data			= $this->master_model->getDataArray('menus','flag_active','1');
			$detail				= group_access($id);
			
			$int_data			= $this->master_model->getData('groups','id',$id);
			
			$data = array(
				'title'			=> 'Manage Access Group',
				'action'		=> 'access_menu',
				'data_menu'		=> $get_Data,
				'row_akses'		=> $detail,
				'rows'			=> $int_data
			);
			
			$this->load->view('Groups/akses_menu',$data);
		}
	}
	
	public function edit_group($kode=''){
		if($this->input->post()){
			$Group_id			= $this->input->post('id');
			$Group_Name			= $this->input->post('name');
			$Keterangan			= $this->input->post('descr');
			$Query_Cek			= "SELECT * FROM groups WHERE LOWER(name)='".strtolower($Group_Name)."' AND id <> '".$Group_id."'";
			$Cek_Data			= $this->db->query($Query_Cek)->num_rows();
			if($Cek_Data > 0){
				$Arr_Kembali		= array(
					'status'		=> 2,
					'pesan'			=> 'Group Already Exist. Please Different Group Name.......'
				);
			}else{
				$data_session		= $this->session->userdata;
				$det_Insert			= array(
					'name'				=> ucwords(strtolower($Group_Name)),
					'descr'				=> $Keterangan,
					'modified'			=> date('Y-m-d H:i:s'),
					'modified_by'		=> $data_session['ORI_User']['username']
					
				);
				if($this->master_model->getUpdate('groups',$det_Insert,'id',$Group_id)){
					
					$Arr_Kembali		= array(
						'status'		=> 1,
						'pesan'			=> 'Update Group Success. Thank you & have a nice day.......'
					);
					history('Edit Data Group ID : '.$Group_id);
				}else{
					$Arr_Kembali		= array(
						'status'		=> 2,
						'pesan'			=> 'Update Group failed. Please try again later......'
					);
					
				}
			}
			echo json_encode($Arr_Kembali);
		}else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('groups'));
			}
			$int_data			= $this->master_model->getData('groups','id',$kode);
			$data = array(
				'title'			=> 'EDIT GROUP',
				'action'		=> 'edit',
				'rows_data'		=> $int_data
			);
			
			$this->load->view('Groups/edit_group',$data);
		}
	}
	
	public function download_excel($id=null){
        set_time_limit(0);
        ini_set('memory_limit','1024M');
        $this->load->library("PHPExcel");

        $objPHPExcel    = new PHPExcel();

        $whiteCenterBold    = whiteCenterBold();
		$whiteRightBold    	= whiteRightBold();
		$whiteCenter    	= whiteCenter();
		$mainTitle    		= mainTitle();
		$tableHeader    	= tableHeader();
		$tableBodyCenter    = tableBodyCenter();
		$tableBodyLeft    	= tableBodyLeft();
		$tableBodyRight    	= tableBodyRight();

        $Arr_Bulan  = array(1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
        $sheet      = $objPHPExcel->getActiveSheet();

		$getDataGroup = $this->db->get_where('groups',array('id'=>$id))->result_array();

        $dateX	= date('Y-m-d H:i:s');
        $Row        = 1;
        $NewRow     = $Row+1;
        $Col_Akhir  = $Cols = getColsChar(8);
        $sheet->setCellValue('A'.$Row, "GROUPS ".strtoupper($getDataGroup[0]['name']));
        $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($mainTitle);
        $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

        $NewRow = $NewRow +2;
        $NextRow= $NewRow;

		$sheet ->getColumnDimension("A")->setAutoSize(true);
        $sheet->setCellValue('A'.$NewRow, '#');
        $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);

		$sheet ->getColumnDimension("B")->setAutoSize(true);
        $sheet->setCellValue('B'.$NewRow, 'Menu');
        $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);

		$sheet ->getColumnDimension("C")->setAutoSize(true);
		$sheet->setCellValue('C'.$NewRow, 'Read');
        $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);

		$sheet ->getColumnDimension("D")->setAutoSize(true);
        $sheet->setCellValue('D'.$NewRow, 'Add');
        $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);

		$sheet ->getColumnDimension("E")->setAutoSize(true);
        $sheet->setCellValue('E'.$NewRow, 'Edit');
        $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);

		$sheet ->getColumnDimension("F")->setAutoSize(true);
		$sheet->setCellValue('F'.$NewRow, 'Delete');
        $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);

		$sheet ->getColumnDimension("G")->setAutoSize(true);
		$sheet->setCellValue('G'.$NewRow, 'Approve');
        $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);

		$sheet ->getColumnDimension("H")->setAutoSize(true);
		$sheet->setCellValue('H'.$NewRow, 'Download');
        $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($tableHeader);
        $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);

		// $sheet ->getColumnDimension("I")->setAutoSize(true);
		// $sheet->setCellValue('I'.$NewRow, 'Qty');
        // $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($tableHeader);
        // $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);

		$dataResult   = $this->db->order_by('weight','asc')->get_where('menus',array('parent_id'=>0,'flag_active'=>1))->result_array();

		$CHECK_GROUP = getAccessGroupMenu($id);

		if($dataResult){
			$awal_row   = $NextRow;
			 $no = 0;
			foreach($dataResult as $key=>$vals){
				$no++;
				$awal_row++;
				$awal_col   = 0;

				$awal_col++;
				$no   = $no;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $no);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$name   = $vals['name'];
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$read 		= (!empty($CHECK_GROUP[$vals['id']]['read']))?$CHECK_GROUP[$vals['id']]['read']:'';
				$create 	= (!empty($CHECK_GROUP[$vals['id']]['create']))?$CHECK_GROUP[$vals['id']]['create']:'';
				$update 	= (!empty($CHECK_GROUP[$vals['id']]['update']))?$CHECK_GROUP[$vals['id']]['update']:'';
				$delete 	= (!empty($CHECK_GROUP[$vals['id']]['delete']))?$CHECK_GROUP[$vals['id']]['delete']:'';
				$approve 	= (!empty($CHECK_GROUP[$vals['id']]['approve']))?$CHECK_GROUP[$vals['id']]['approve']:'';
				$download 	= (!empty($CHECK_GROUP[$vals['id']]['download']))?$CHECK_GROUP[$vals['id']]['download']:'';

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $read);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $create);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $update);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $delete);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $approve);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$awal_col++;
				$Cols       = getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $download);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

				$dataResultLv2   = $this->db->order_by('weight','asc')->get_where('menus',array('parent_id'=>$vals['id'],'flag_active'=>1))->result_array();
				foreach($dataResultLv2 as $key2=>$vals2){
					$awal_row++;
					$awal_col   = 0;
	
					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, '');
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
	
					$awal_col++;
					$name   	= "     ".$vals2['name'];
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $name);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$read 		= (!empty($CHECK_GROUP[$vals2['id']]['read']))?$CHECK_GROUP[$vals2['id']]['read']:'';
					$create 	= (!empty($CHECK_GROUP[$vals2['id']]['create']))?$CHECK_GROUP[$vals2['id']]['create']:'';
					$update 	= (!empty($CHECK_GROUP[$vals2['id']]['update']))?$CHECK_GROUP[$vals2['id']]['update']:'';
					$delete 	= (!empty($CHECK_GROUP[$vals2['id']]['delete']))?$CHECK_GROUP[$vals2['id']]['delete']:'';
					$approve 	= (!empty($CHECK_GROUP[$vals2['id']]['approve']))?$CHECK_GROUP[$vals2['id']]['approve']:'';
					$download 	= (!empty($CHECK_GROUP[$vals2['id']]['download']))?$CHECK_GROUP[$vals2['id']]['download']:'';

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $read);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $create);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $update);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $delete);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $approve);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

					$awal_col++;
					$Cols       = getColsChar($awal_col);
					$sheet->setCellValue($Cols.$awal_row, $download);
					$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
	
					$dataResultLv3   = $this->db->order_by('weight','asc')->get_where('menus',array('parent_id'=>$vals2['id'],'flag_active'=>1))->result_array();
					foreach($dataResultLv3 as $key3=>$vals3){
						$awal_row++;
						$awal_col   = 0;
		
						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, '');
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
		
						$awal_col++;
						$name   	= "          ".$vals3['name'];
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $name);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$read 		= (!empty($CHECK_GROUP[$vals3['id']]['read']))?$CHECK_GROUP[$vals3['id']]['read']:'';
						$create 	= (!empty($CHECK_GROUP[$vals3['id']]['create']))?$CHECK_GROUP[$vals3['id']]['create']:'';
						$update 	= (!empty($CHECK_GROUP[$vals3['id']]['update']))?$CHECK_GROUP[$vals3['id']]['update']:'';
						$delete 	= (!empty($CHECK_GROUP[$vals3['id']]['delete']))?$CHECK_GROUP[$vals3['id']]['delete']:'';
						$approve 	= (!empty($CHECK_GROUP[$vals3['id']]['approve']))?$CHECK_GROUP[$vals3['id']]['approve']:'';
						$download 	= (!empty($CHECK_GROUP[$vals3['id']]['download']))?$CHECK_GROUP[$vals3['id']]['download']:'';

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $read);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $create);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $update);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $delete);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $approve);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

						$awal_col++;
						$Cols       = getColsChar($awal_col);
						$sheet->setCellValue($Cols.$awal_row, $download);
						$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
		
						$dataResultLv4   = $this->db->order_by('weight','asc')->get_where('menus',array('parent_id'=>$vals3['id'],'flag_active'=>1))->result_array();
						foreach($dataResultLv4 as $key4=>$vals4){
							$awal_row++;
							$awal_col   = 0;
			
							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, '');
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
			
							$awal_col++;
							$name   	= "               ".$vals4['name'];
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $name);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$read 		= (!empty($CHECK_GROUP[$vals4['id']]['read']))?$CHECK_GROUP[$vals4['id']]['read']:'';
							$create 	= (!empty($CHECK_GROUP[$vals4['id']]['create']))?$CHECK_GROUP[$vals4['id']]['create']:'';
							$update 	= (!empty($CHECK_GROUP[$vals4['id']]['update']))?$CHECK_GROUP[$vals4['id']]['update']:'';
							$delete 	= (!empty($CHECK_GROUP[$vals4['id']]['delete']))?$CHECK_GROUP[$vals4['id']]['delete']:'';
							$approve 	= (!empty($CHECK_GROUP[$vals4['id']]['approve']))?$CHECK_GROUP[$vals4['id']]['approve']:'';
							$download 	= (!empty($CHECK_GROUP[$vals4['id']]['download']))?$CHECK_GROUP[$vals4['id']]['download']:'';

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $read);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $create);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $update);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $delete);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $approve);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							$awal_col++;
							$Cols       = getColsChar($awal_col);
							$sheet->setCellValue($Cols.$awal_row, $download);
							$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
			
							$dataResultLv5   = $this->db->order_by('weight','asc')->get_where('menus',array('parent_id'=>$vals4['id'],'flag_active'=>1))->result_array();
							foreach($dataResultLv5 as $key5=>$vals5){
								$awal_row++;
								$awal_col   = 0;
				
								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, '');
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);
				
								$awal_col++;
								$name   	= "                    ".$vals5['name'];
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $name);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$read 		= (!empty($CHECK_GROUP[$vals5['id']]['read']))?$CHECK_GROUP[$vals5['id']]['read']:'';
								$create 	= (!empty($CHECK_GROUP[$vals5['id']]['create']))?$CHECK_GROUP[$vals5['id']]['create']:'';
								$update 	= (!empty($CHECK_GROUP[$vals5['id']]['update']))?$CHECK_GROUP[$vals5['id']]['update']:'';
								$delete 	= (!empty($CHECK_GROUP[$vals5['id']]['delete']))?$CHECK_GROUP[$vals5['id']]['delete']:'';
								$approve 	= (!empty($CHECK_GROUP[$vals5['id']]['approve']))?$CHECK_GROUP[$vals5['id']]['approve']:'';
								$download 	= (!empty($CHECK_GROUP[$vals5['id']]['download']))?$CHECK_GROUP[$vals5['id']]['download']:'';

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $read);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $create);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $update);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $delete);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $approve);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

								$awal_col++;
								$Cols       = getColsChar($awal_col);
								$sheet->setCellValue($Cols.$awal_row, $download);
								$sheet->getStyle($Cols.$awal_row)->applyFromArray($tableBodyLeft);

							}
						}
					}
				}

			}
		}

		history('Download Group');
        $sheet->setTitle('Group');
        //mulai menyimpan excel format xlsx, kalau ingin xls ganti Excel2007 menjadi Excel5
        $objWriter      = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_end_clean();
        //sesuaikan headernya
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //ubah nama file saat diunduh
        header('Content-Disposition: attachment;filename="download-group.xls"');
        //unduh file
        $objWriter->save("php://output");
    }
	
	
}