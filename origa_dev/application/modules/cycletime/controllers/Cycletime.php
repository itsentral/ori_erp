<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Cycletime extends Admin_Controller
{
    //Permission
    protected $viewPermission 	= 'Cycletime.View';
    protected $addPermission  	= 'Cycletime.Add';
    protected $managePermission = 'Cycletime.Manage';
    protected $deletePermission = 'Cycletime.Delete';

   public function __construct()
    {
        parent::__construct();

        // $this->load->library(array('Mpdf', 'upload', 'Image_lib'));
        $this->load->model(array('Cycletime/Cycletime_model',
		                         'Aktifitas/aktifitas_model',
                                ));
        $this->template->title('Manage Data Supplier');
        $this->template->page_icon('fa fa-building-o');

        date_default_timezone_set('Asia/Bangkok');
    }

    public function index()
    {
      $this->auth->restrict($this->viewPermission);
      $session = $this->session->userdata('app_session');
      $this->template->page_icon('fa fa-users');
      $deleted = '0';
      $data = $this->Cycletime_model->get_data('cycletime_header','deleted','N');

      $getBy				= "SELECT created_by, created_date FROM cycletime_fast ORDER BY created_date DESC LIMIT 1";
  		$restgetBy			= $this->db->query($getBy)->result_array();
  		$datax = array(
  			'get_by'		=> $restgetBy
  		);

      history("View index cycletime");
      $this->template->set('results', $data);
      $this->template->title('Cycletime');
      $this->template->render('index', $datax);
    }

    public function data_side_cycletime(){
      $this->Cycletime_model->get_json_cycletime();
    }

    public function add(){

    	$session = $this->session->userdata('app_session');

			$customer    = $this->Cycletime_model->get_data('master_customer');
			$supplier    = $this->Cycletime_model->get_data('master_supplier');
			$material    = $this->Cycletime_model->get_data('ms_inventory_category2');
			$mesin      = $this->Cycletime_model->get_data_group('asset','category','4','nm_asset');
      $mould      = $this->Cycletime_model->get_data_group('asset','category','5','nm_asset');
			$costcenter  = $this->Cycletime_model->get_data('ms_costcenter','deleted','0');
			$data = [
			'customer' => $customer,
			'supplier' => $supplier,
			'material' => $material,
			'mesin' => $mesin,
      'mould' => $mould,
			'costcenter' => $costcenter,
			];
			$this->template->set('results', $data);
      $this->template->title('Add Cycletime');
      $this->template->page_icon('fa fa-edit');
      $this->template->title('Add Cycletime');
      $this->template->render('add');
    }

    public function edit(){

    	$session = $this->session->userdata('app_session');
      $id_time = $this->uri->segment(3);
			$customer    = $this->Cycletime_model->get_data('master_customer');
			$supplier    = $this->Cycletime_model->get_data('master_supplier');
			$material    = $this->Cycletime_model->get_data('ms_inventory_category2');
			// $machine      = $this->Cycletime_model->get_data_group('asset','category','4','nm_asset');
      // $mould      = $this->Cycletime_model->get_data_group('asset','category','5','nm_asset');
			// $costcenter  = $this->Cycletime_model->get_data('ms_costcenter','deleted','0');
      $header	= $this->db->query("SELECT * FROM cycletime_header WHERE id_time='".$id_time."' LIMIT 1 ")->result();
      $costcenter	= $this->db->query("SELECT * FROM ms_costcenter WHERE deleted='0' ORDER BY nama_costcenter ASC ")->result_array();
      $machine	= $this->db->query("SELECT * FROM asset WHERE category='4' GROUP BY nm_asset ORDER BY nm_asset ASC ")->result_array();
      $mould	= $this->db->query("SELECT * FROM asset WHERE category='5' GROUP BY nm_asset ORDER BY nm_asset ASC ")->result_array();
			$data = [
			'customer' => $customer,
			'supplier' => $supplier,
			'material' => $material,
			'mesin' => $machine,
      'mould' => $mould,
			'costcenter' => $costcenter,
      'header' => $header
			];
			$this->template->set('results', $data);
      $this->template->page_icon('fa fa-edit');
      $this->template->title('Edit Cycletime');
      $this->template->render('edit', $data);
    }


	public function view(){
		$this->auth->restrict($this->viewPermission);
		$id 	= $this->input->post('id');
		$header = $this->db->get_where('cycletime_header',array('id_time' => $id))->result();
    // print_r($header);
		$data = [
			'header' => $header
			];
    $this->template->set('results', $data);
		$this->template->render('view', $data);
	}

  public function get_add(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

		$costcenter	= $this->db->query("SELECT * FROM ms_costcenter WHERE deleted='0' ORDER BY nama_costcenter ASC ")->result_array();
    $machine	= $this->db->query("SELECT * FROM asset WHERE category='4' GROUP BY nm_asset ORDER BY nm_asset ASC ")->result_array();
    $mould	= $this->db->query("SELECT * FROM asset WHERE category='5' GROUP BY nm_asset ORDER BY nm_asset ASC ")->result_array();
		// echo $qListResin; exit;
		$d_Header = "";
		// $d_Header .= "<tr>";
			$d_Header .= "<tr class='header_".$id."'>";
				$d_Header .= "<td align='center'>".$id."</td>";
				$d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][costcenter]' class='chosen_select form-control input-sm inline-blockd costcenter'>";
        $d_Header .= "<option value='0'>Select Costcenter</option>";
        foreach($costcenter AS $val => $valx){
          $d_Header .= "<option value='".$valx['id_costcenter']."'>".strtoupper($valx['nama_costcenter'])."</option>";
        }
        $d_Header .= 		"</select>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][machine]' class='chosen_select form-control input-sm inline-blockd'>";
        $d_Header .= "<option value='0'>Select Machine</option>";
        foreach($machine AS $val => $valx){
          $d_Header .= "<option value='".$valx['kd_asset']."'>".strtoupper($valx['nm_asset'])."</option>";
        }
        $d_Header .= "<option value='0'>NONE MACHINE</option>";
        $d_Header .= 		"</select>";

				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<select name='Detail[".$id."][mould]' class='chosen_select form-control input-sm inline-blockd'>";
        $d_Header .= "<option value='0'>Select Mould/Tools</option>";
        foreach($mould AS $val => $valx){
          $d_Header .= "<option value='".$valx['kd_asset']."'>".strtoupper($valx['nm_asset'])."</option>";
        }
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

		$process	= $this->db->query("SELECT * FROM ms_process ORDER BY nm_process ASC ")->result_array();
		// echo $qListResin; exit;
		$d_Header = "";
		// $d_Header .= "<tr>";
			$d_Header .= "<tr class='header_".$id."'>";
				$d_Header .= "<td align='center'></td>";
				$d_Header .= "<td align='left' style='vertical-align:middle; padding-left: 30px;'>";
        $d_Header .= "<select name='Detail[".$id."][detail][".$no."][process]' class='chosen_select form-control input-sm inline-blockd process'>";
        $d_Header .= "<option value='0'>Select Process Name</option>";
        foreach($process AS $val => $valx){
          $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nm_process'])."</option>";
        }
        $d_Header .= 		"</select>";
				$d_Header .= "</td>";
        $d_Header .= "<td align='left'>";
        $d_Header .= "<input type='text' name='Detail[".$id."][detail][".$no."][cycletime]' class='form-control input-md maskM' placeholder='Cycletime (Minutes)' >";
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


  public function save_cycletime(){

  	$Arr_Kembali	= array();
		$data			= $this->input->post();
    // print_r($data);
    // exit;
		$session 		= $this->session->userdata('app_session');
    $Detail 	= $data['Detail'];
    $Ym						= date('ym');
    //pengurutan kode
    $srcMtr			= "SELECT MAX(id_time) as maxP FROM cycletime_header WHERE id_time LIKE 'TM-".$Ym."%' ";
    $numrowMtr		= $this->db->query($srcMtr)->num_rows();
    $resultMtr		= $this->db->query($srcMtr)->result_array();
    $angkaUrut2		= $resultMtr[0]['maxP'];
    $urutan2		= (int)substr($angkaUrut2, 7, 3);
    $urutan2++;
    $urut2			= sprintf('%03s',$urutan2);
    $id_material	= "TM-".$Ym.$urut2;

    $ArrHeader		= array(
      'id_time'			=> $id_material,
      'id_product'	=> $data['produk'],
      'created_by'	=> $session['id_user'],
      'created_date'	=> date('Y-m-d H:i:s')
    );



    $ArrDetail	= array();
    $ArrDetail2	= array();
    foreach($Detail AS $val => $valx){
      $urut				= sprintf('%02s',$val);
      $ArrDetail[$val]['id_time'] 			= $id_material;
      $ArrDetail[$val]['id_costcenter'] = $id_material."-".$urut;
      $ArrDetail[$val]['costcenter'] 		= $valx['costcenter'];
      $ArrDetail[$val]['machine'] 			= $valx['machine'];
      $ArrDetail[$val]['mould'] 				= $valx['mould'];
      foreach($valx['detail'] AS $val2 => $valx2){
        $ArrDetail2[$val2.$val]['id_time'] 			= $id_material;
        $ArrDetail2[$val2.$val]['id_costcenter'] = $id_material."-".$urut;
        $ArrDetail2[$val2.$val]['id_process'] 	= $valx2['process'];
        $ArrDetail2[$val2.$val]['cycletime'] 		= $valx2['cycletime'];
        $ArrDetail2[$val2.$val]['qty_mp'] 			= $valx2['qty_mp'];
        $ArrDetail2[$val2.$val]['note'] 				= $valx2['note'];
      }
    }

    // print_r($ArrHeader);
		// print_r($ArrDetail);
		// print_r($ArrDetail2);
		// exit;

		$this->db->trans_start();
			$this->db->insert('cycletime_header', $ArrHeader);
      if(!empty($ArrDetail)){
  			$this->db->insert_batch('cycletime_detail_header', $ArrDetail);
      }
      if(!empty($ArrDetail)){
        $this->db->insert_batch('cycletime_detail_detail', $ArrDetail2);
      }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
      history("Insert cycletime ".$id_material);
		}

		echo json_encode($Arr_Data);
	}

	public function list_center(){
		$id = $this->uri->segment(3);
		$query	 	= "SELECT * FROM ms_costcenter WHERE id_dept='".$id."' ORDER BY nama_costcenter ASC";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select an Option</option>";
		foreach($Q_result as $row)	{
		$option .= "<option value='".$row->nama_costcenter."'>".strtoupper($row->nama_costcenter)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

  public function edit_cycletime(){

  	$Arr_Kembali	= array();
		$data			    = $this->input->post();
    // print_r($data);
    // exit;
		$session 		   = $this->session->userdata('app_session');
    $Detail 	     = $data['Detail'];
    $id_material	 = $data['id_time'];

    $ArrHeader		  = array(
      'id_time'			=> $id_material,
      'id_product'	=> $data['produk'],
      'updated_by'	=> $session['id_user'],
      'updated_date'	=> date('Y-m-d H:i:s')
    );



    $ArrDetail	= array();
    $ArrDetail2	= array();
    foreach($Detail AS $val => $valx){
      $urut				= sprintf('%02s',$val);
      $ArrDetail[$val]['id_time'] 			= $id_material;
      $ArrDetail[$val]['id_costcenter'] = $id_material."-".$urut;
      $ArrDetail[$val]['costcenter'] 		= $valx['costcenter'];
      $ArrDetail[$val]['machine'] 			= $valx['machine'];
      $ArrDetail[$val]['mould'] 				= $valx['mould'];
      foreach($valx['detail'] AS $val2 => $valx2){
        $ArrDetail2[$val2.$val]['id_time'] 			= $id_material;
        $ArrDetail2[$val2.$val]['id_costcenter'] = $id_material."-".$urut;
        $ArrDetail2[$val2.$val]['id_process'] 	= $valx2['process'];
        $ArrDetail2[$val2.$val]['cycletime'] 		= $valx2['cycletime'];
        $ArrDetail2[$val2.$val]['qty_mp'] 			= $valx2['qty_mp'];
        $ArrDetail2[$val2.$val]['note'] 				= $valx2['note'];
      }
    }

    // print_r($ArrHeader);
		// print_r($ArrDetail);
		// print_r($ArrDetail2);
		// exit;

		$this->db->trans_start();
      $this->db->where('id_time', $id_material);
			$this->db->update('cycletime_header', $ArrHeader);

      $this->db->delete('cycletime_detail_header', array('id_time' => $id_material));
      $this->db->delete('cycletime_detail_detail', array('id_time' => $id_material));

      if(!empty($ArrDetail)){
  			$this->db->insert_batch('cycletime_detail_header', $ArrDetail);
      }
      if(!empty($ArrDetail)){
        $this->db->insert_batch('cycletime_detail_detail', $ArrDetail2);
      }
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Save gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Save berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
      history("Update cycletime ".$id_material);
		}

		echo json_encode($Arr_Data);
	}

  public function delete_cycletime(){

  	$Arr_Kembali	= array();
		$data			    = $this->input->post();
    // print_r($data);
    // exit;
		$session 		   = $this->session->userdata('app_session');
    $id_material	 = $data['id'];

    $ArrHeader		  = array(
      'deleted'			=> "Y",
      'deleted_by'	=> $session['id_user'],
      'deleted_date'	=> date('Y-m-d H:i:s')
    );

		$this->db->trans_start();
      $this->db->where('id_time', $id_material);
			$this->db->delete('cycletime_header');

      $this->db->where('id_time', $id_material);
			$this->db->delete('cycletime_detail_header');

      $this->db->where('id_time', $id_material);
			$this->db->delete('cycletime_detail_detail');
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
      history("Delete cycletime ".$id_material);
		}

		echo json_encode($Arr_Data);
	}


  function insert_select_ct(){
    $session 		   = $this->session->userdata('app_session');
		$sql 	= "SELECT a.* FROM list_cycletime a";
		$rest 	= $this->db->query($sql)->result_array();

    $sql2 	= "SELECT a.* FROM list_ct_full a";
		$rest2 	= $this->db->query($sql2)->result_array();

		$ArrInsert = array();
		foreach($rest AS $val => $valx){
			$ArrInsert[$val]['id_time'] 		= $valx['id_time'];
			$ArrInsert[$val]['id_costcenter'] 	= $valx['id_costcenter'];
			$ArrInsert[$val]['id_product'] 	= $valx['id_product'];
			$ArrInsert[$val]['costcenter'] 	= $valx['costcenter'];
			$ArrInsert[$val]['cycletime'] 	= $valx['cycletime'];
			$ArrInsert[$val]['mp'] 		      = $valx['mp'];
      $ArrInsert[$val]['urut'] 		    = $valx['urut'];

			$ArrInsert[$val]['created_by'] 		= $session['id_user'];
			$ArrInsert[$val]['created_date'] 	= date('Y-m-d H:i:s');
		}

    $ArrInsert2 = array();
		foreach($rest2 AS $val => $valx){
			$ArrInsert2[$val]['id_product'] 		  = $valx['id_product'];
			$ArrInsert2[$val]['nama_project'] 	  = $valx['nama_project'];
			$ArrInsert2[$val]['nama_product'] 	  = $valx['nama_product'];
			$ArrInsert2[$val]['id_costcenter'] 	  = $valx['id_costcenter'];
			$ArrInsert2[$val]['nama_costcenter'] 	= $valx['nama_costcenter'];
			$ArrInsert2[$val]['id_process'] 		  = $valx['id_process'];
      $ArrInsert2[$val]['nm_process'] 		  = $valx['nm_process'];

      $ArrInsert2[$val]['cycletime'] 		    = $valx['cycletime'];
      $ArrInsert2[$val]['mp'] 		          = $valx['qty_mp'];
      $ArrInsert2[$val]['man_hours'] 		    = $valx['man_hours'];

			$ArrInsert2[$val]['created_by'] 		= $session['id_user'];
			$ArrInsert2[$val]['created_date'] 	= date('Y-m-d H:i:s');
		}

		$this->db->trans_start();
      $this->db->truncate('cycletime_fast');
			$this->db->insert_batch('cycletime_fast',$ArrInsert);

      $this->db->truncate('cycletime_full');
			$this->db->insert_batch('cycletime_full',$ArrInsert2);
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Update Failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Update Success. Thanks ...',
				'status'	=> 1
			);
			history('Success insert select cycletime');
		}
		echo json_encode($Arr_Data);

	}

  public function excel_report(){
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
      'color' => array('rgb'=>'e0e0e0'),
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
      'color' => array('rgb'=>'e0e0e0'),
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

    $cycletime    = $this->db->query("SELECT * FROM list_ct_full_excel")->result_array();

    $Row		= 1;
    $NewRow		= $Row+1;
    $Col_Akhir	= $Cols	= getColsChar(11);
    $sheet->setCellValue('A'.$Row, 'CYCLETIME');
    $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
    $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

    $NewRow	= $NewRow +2;
    $NextRow= $NewRow +1;

    $sheet->setCellValue('A'.$NewRow, 'No');
    $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
    $sheet->getColumnDimension('A')->setAutoSize(true);

    $sheet->setCellValue('B'.$NewRow, 'Project');
    $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C'.$NewRow, 'Id Product');
    $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $sheet->setCellValue('D'.$NewRow, 'Product');
    $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
    $sheet->getColumnDimension('D')->setAutoSize(true);

    $sheet->setCellValue('E'.$NewRow, 'Id Costcenter');
    $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
    $sheet->getColumnDimension('E')->setAutoSize(true);

    $sheet->setCellValue('F'.$NewRow, 'Costcenter');
    $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
    $sheet->getColumnDimension('F')->setAutoSize(true);

    $sheet->setCellValue('G'.$NewRow, 'Id Process');
    $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
    $sheet->getColumnDimension('G')->setAutoSize(true);

    $sheet->setCellValue('H'.$NewRow, 'Nama Process');
    $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
    $sheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->setCellValue('I'.$NewRow, 'Cycletime');
    $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
    $sheet->getColumnDimension('I')->setAutoSize(true);

    $sheet->setCellValue('J'.$NewRow, 'Qty Man Power');
    $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
    $sheet->getColumnDimension('J')->setAutoSize(true);

    $sheet->setCellValue('K'.$NewRow, 'Note');
    $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
    $sheet->getColumnDimension('K')->setAutoSize(true);

    if($cycletime){
    $awal_row	= $NextRow;
    $no=0;
    foreach($cycletime as $key => $row_Cek){
      $no++;
      $awal_row++;
      $awal_col	= 0;

      $awal_col++;
      $nomor	= $no;
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $nomor);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_product'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_project']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_product']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_costcenter'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_costcenter']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_process'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nm_process']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['cycletime'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['qty_mp'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['note'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

    }
  }


    $sheet->setTitle('List Cycletime');
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
    header('Content-Disposition: attachment;filename="Cycletime_'.date('YmdHis').'.xls"');
    //unduh file
    $objWriter->save("php://output");
  }

  public function excel_ct_per_product(){
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
      'color' => array('rgb'=>'e0e0e0'),
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
      'color' => array('rgb'=>'e0e0e0'),
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
    $id_product = $this->uri->segment(3);
    $cycletime    = $this->db->query("SELECT * FROM list_ct_full_excel WHERE id_product='".$id_product."'")->result_array();

    $Row		= 1;
    $NewRow		= $Row+1;
    $Col_Akhir	= $Cols	= getColsChar(11);
    $sheet->setCellValue('A'.$Row, 'CYCLETIME '.strtoupper($cycletime[0]['nama_product']));
    $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
    $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

    $NewRow	= $NewRow +2;
    $NextRow= $NewRow +1;

    $sheet->setCellValue('A'.$NewRow, 'No');
    $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
    $sheet->getColumnDimension('A')->setAutoSize(true);

    $sheet->setCellValue('B'.$NewRow, 'Project');
    $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C'.$NewRow, 'Id Product');
    $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    $sheet->setCellValue('D'.$NewRow, 'Product');
    $sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
    $sheet->getColumnDimension('D')->setAutoSize(true);

    $sheet->setCellValue('E'.$NewRow, 'Id Costcenter');
    $sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
    $sheet->getColumnDimension('E')->setAutoSize(true);

    $sheet->setCellValue('F'.$NewRow, 'Costcenter');
    $sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
    $sheet->getColumnDimension('F')->setAutoSize(true);

    $sheet->setCellValue('G'.$NewRow, 'Id Process');
    $sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
    $sheet->getColumnDimension('G')->setAutoSize(true);

    $sheet->setCellValue('H'.$NewRow, 'Nama Process');
    $sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
    $sheet->getColumnDimension('H')->setAutoSize(true);

    $sheet->setCellValue('I'.$NewRow, 'Cycletime');
    $sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
    $sheet->getColumnDimension('I')->setAutoSize(true);

    $sheet->setCellValue('J'.$NewRow, 'Qty Man Power');
    $sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
    $sheet->getColumnDimension('J')->setAutoSize(true);

    $sheet->setCellValue('K'.$NewRow, 'Note');
    $sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
    $sheet->getColumnDimension('K')->setAutoSize(true);

    if($cycletime){
    $awal_row	= $NextRow;
    $no=0;
    foreach($cycletime as $key => $row_Cek){
      $no++;
      $awal_row++;
      $awal_col	= 0;

      $awal_col++;
      $nomor	= $no;
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $nomor);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_product'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_project']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_product']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_costcenter'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nama_costcenter']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_process'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= strtolower($row_Cek['nm_process']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['cycletime'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['qty_mp'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['note'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

    }
  }


    $sheet->setTitle('CT '.ucwords($cycletime[0]['nama_product']));
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
    header('Content-Disposition: attachment;filename="cycletime_'.$cycletime[0]['nama_product']."_".date('YmdHis').'.xls"');
    //unduh file
    $objWriter->save("php://output");
  }

  public function excel_report_upload_stock(){
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
      'color' => array('rgb'=>'e0e0e0'),
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
      'color' => array('rgb'=>'e0e0e0'),
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

    $cycletime    = $this->db->query("SELECT * FROM ms_inventory_category2 WHERE deleted='0'")->result_array();

    $Row		= 1;
    $NewRow		= $Row+1;
    $Col_Akhir	= $Cols	= getColsChar(3);
    $sheet->setCellValue('A'.$Row, 'UPLOAD STOCK');
    $sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
    $sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

    $NewRow	= $NewRow +2;
    $NextRow= $NewRow +1;

    $sheet->setCellValue('A'.$NewRow, 'No');
    $sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
    $sheet->getColumnDimension('A')->setAutoSize(true);

    $sheet->setCellValue('B'.$NewRow, 'Product');
    $sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
    $sheet->getColumnDimension('B')->setAutoSize(true);

    $sheet->setCellValue('C'.$NewRow, 'Costcenter Last');
    $sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
    $sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
    $sheet->getColumnDimension('C')->setAutoSize(true);

    if($cycletime){
    $awal_row	= $NextRow;
    $no=0;
    foreach($cycletime as $key => $row_Cek){
      $no++;
      $awal_row++;
      $awal_col	= 0;

      $awal_col++;
      $nomor	= $no;
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $nomor);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= $row_Cek['id_category2'];
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

      $awal_col++;
      $id_product	= get_last_costcenter_warehouse($row_Cek['id_category2']);
      $Cols			= getColsChar($awal_col);
      $sheet->setCellValue($Cols.$awal_row, $id_product);
      $sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

    }
  }


    $sheet->setTitle('Upload Stock');
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
    header('Content-Disposition: attachment;filename="upload_stock_'.date('YmdHis').'.xls"');
    //unduh file
    $objWriter->save("php://output");
  }
}

?>
