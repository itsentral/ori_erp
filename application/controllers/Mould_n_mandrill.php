<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Mould_n_mandrill extends CI_Controller
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
  			'title'			=> 'Indeks Of Master Mould & Mandrill',
  			'action'		=> 'index',
  			'data_menu'		=> $menu_akses,
  			'akses_menu'	=> $Arr_Akses
  		);
  		history("View Master Mould and Mandrill ".$productN);
  		$this->load->view('Mould_n_mandrill/index',$data);
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
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['product_parent'])."</div>";
			$nestedData[]	= "<div align='right'>".$row['diameter']."</div>";
			$nestedData[]	= "<div align='right'>".$row['diameter2']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['dimensi'], 0)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['harga'],2)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['est_pakai'],0)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['biaya_per_pcs'],2)."</div>";

			$update = "";
			$delete = "";
			if($Arr_Akses['update']=='1'){
				$update	= "<a id='editM' data-id='".$row['id']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
			}if($Arr_Akses['update']=='1'){
				$delete	= "<a id='deleteM' data-id='".$row['id']."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
			}

			$nestedData[]	= "<div align='center'>
									<a id='viewM' data-id='".$row['id']."' class='btn btn-sm btn-success' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>
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
		$where_sts = "";
		// if(!empty($sts_mesin)){
			// $where_sts = " AND status = 'Y' ";
		// }

		$sql = "
			SELECT
				*
			FROM
				mould_mandrill
			WHERE 1=1
				".$where_sts."
				AND status='N' AND(
				product_parent LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR diameter LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR diameter2 LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR dimensi LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";

		// echo $sql;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'id',
			1 => 'product_parent',
			2 => 'diameter',
			3 => 'diameter2',
			4 => 'dimensi',
			5 => 'harga',
			6 => 'est_pakai',
			7 => 'biaya_per_pcs'
		);

		$sql .= " ORDER BY id ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
	public function add(){	 	
		if($this->input->post()){
			$Arr_Kembali			= array();			
			$data					= $this->input->post();
			$data_session			= $this->session->userdata;
			

			$product_parent	= $data['product_parent'];
			$diameter		= $data['diameter'];
			$diameter2		= $data['diameter2'];
			$dimensi		= $data['dimensi'];
			$harga			= $data['harga'];
			$est_pakai		= $data['est_pakai'];
			$biaya_per_pcs	= $data['biaya_per_pcs'];
			
			// echo $numType; exit;
			$data	= array(
				'product_parent' 	=> $product_parent,
				'diameter' 			=> $diameter,
				'diameter2' 		=> $diameter2,
				'dimensi' 			=> $dimensi,
				'harga' 			=> $harga,
				'est_pakai'			=> $est_pakai,
				'biaya_per_pcs' 	=> $biaya_per_pcs,
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);
			
			// print_r($data);
			// exit;
			
			
			$this->db->trans_start();
				$this->db->insert('mould_mandrill', $data);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'			=> 'Failed Add Mould Mandrill. Please try again later ...',
					'status'		=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'			=> 'Success Add Mould Mandrill. Thanks ...',
					'status'		=> 1
					
				);
				history('Add Mould Mandrill : '.$product_parent."/".$diameter."/".$diameter2."/".$dimensi); 
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
			
			$get_Data			= $this->db->query("SELECT * FROM product_parent WHERE estimasi='Y' ORDER BY product_parent ASC")->result_array();
			$data = array(
				'title'			=> 'Add Mould & Mandrill',
				'action'		=> 'add',
				'product'		=> $get_Data
			);
			$this->load->view('Mould_n_mandrill/add',$data);
		}
	}
	
	function hapus(){
		$id = $this->uri->segment(3);
		$data_session			= $this->session->userdata;	
		// echo $id_mesin; exit;
		
		$Arr_Update = array(
			'status' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
		);
		// echo "<pre>"; print_r($Arr_Update);
		// exit;
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('mould_mandrill', $Arr_Update);
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
			history('Delete Machine with Kode/Id : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetail(){
		$this->load->view('Mould_n_mandrill/modalDetail');
	}
	
	public function modalEdit(){
		$this->load->view('Mould_n_mandrill/modalEdit');
	}

	public function edit(){	 	

		$Arr_Kembali			= array();			
		$data					= $this->input->post();
		$data_session			= $this->session->userdata;
		
		$id				= $data['id'];
		$product_parent	= $data['product_parent'];
		$diameter		= $data['diameter'];
		$diameter2		= $data['diameter2'];
		$dimensi		= $data['dimensi'];
		$harga			= $data['harga'];
		$est_pakai		= $data['est_pakai'];
		$biaya_per_pcs	= $data['biaya_per_pcs'];
		
		// echo $numType; exit;
		$Arr_Update	= array(
			'product_parent' 	=> $product_parent,
			'diameter' 			=> $diameter,
			'diameter2' 		=> $diameter2,
			'dimensi' 			=> $dimensi,
			'harga' 			=> $harga,
			'est_pakai'			=> $est_pakai,
			'biaya_per_pcs' 	=> $biaya_per_pcs,
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		// print_r($data);
		// exit;
		
		
		$this->db->trans_start();
			$this->db->where('id', $id);
			$this->db->update('mould_mandrill', $Arr_Update);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'			=> 'Failed Edit Mould Mandrill. Please try again later ...',
				'status'		=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'			=> 'Success Edit Mould Mandrill. Thanks ...',
				'status'		=> 1
				
			);
			history('Edit Mould Mandrill : '.$product_parent."/".$diameter."/".$diameter2."/".$dimensi); 
		}
		echo json_encode($Arr_Kembali);
		
		
	}
}

?>
