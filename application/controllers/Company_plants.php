<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company_plants extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');

		// Your own constructor code
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

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Company Plants',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Company Plants');
		$this->load->view('Company_plants/index',$data);
	}

	public function getDataJSON(){

		$requestData	= $_REQUEST;
		$fetch			= $this->queryDataJSON(
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
			$nestedData[]	= "<div align='left'>".$row['id_plant']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_plant'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['cabang']."</div>";
			$nestedData[]	= "<div align='left'>".$row['phone']."</div>";
					$updX	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/edit/'.$row['id_plant'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
					$delX	= "<button class='btn btn-sm btn-danger' id='deletePlant' title='Permanent Company Plant' data-id_plant='".$row['id_plant']."'><i class='fa fa-trash'></i></button>";
			$nestedData[]	= "<div align='center'>
									<button class='btn btn-sm btn-warning' id='detailPlant' title='Detail Company Plant' data-id_plant='".$row['id_plant']."' data-nm_plant='".$row['nm_plant']."'><i class='fa fa-eye'></i></button>
									".$updX."
									".$delX."
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

	public function queryDataJSON($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				a.*,
				b.cabang
			FROM
				company_plants a INNER JOIN branch b ON a.kdcab = b.nocab
		    WHERE `a`.`delete` = 'N' AND (
				a.id_plant LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.nm_plant LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_plant',
			2 => 'nm_plant'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function add_company_plant(){
		if($this->input->post()){
			$Arr_Kembali			= array();
			$data							= $this->input->post();
			$YM	= date('ym');
			//pengurutan kode
			$srcPlant				= "SELECT MAX(id_plant) as maxP FROM company_plants WHERE id_plant LIKE 'CPLN-".$YM."%' ";
			$numrowPlant		= $this->db->query($srcPlant)->num_rows();
			$resultPlant		= $this->db->query($srcPlant)->result_array();
			$angkaUrut2			= $resultPlant[0]['maxP'];
			$urutan2				= (int)substr($angkaUrut2, 9, 4);
			$urutan2++;
			$urut2					= sprintf('%04s',$urutan2);
			$kode_plant			= "CPLN-".$YM.$urut2;

			//check nama plant
			$NmPlant	= strtoupper($data['nm_plant']);
			$qNmPlant	= "SELECT * FROM company_plants WHERE nm_plant = '".$NmPlant."' ";
			$numName	= $this->db->query($qNmPlant)->num_rows();

			$Data_Insert			= array(
				'id_plant'			=> $kode_plant,
				'nm_plant'			=> strtoupper($data['nm_plant']),
				'inisial_plant'		=> strtoupper($data['inisial_plant']),
				'kdcab'				=> $data['kdcab'],
				'address'			=> $data['address'],
				'province'			=> $data['province'],
				'phone'				=> $data['phone'],
				'fax'				=> $data['fax'],
				'email'				=> $data['email'],
				'created_date'		=> date('Y-m-d H:i:s'),
				'created_by'		=> $this->session->userdata['ORI_User']['username']
			);

			// echo "<pre>"; print_r($Data_Insert);
			// exit;

			## CEK INITIAL PLANT ##
			
			if($numName > 0){
				$Arr_Kembali		= array(
					'status'		=> 4,
					'pesan'			=> 'Plant Name Already Exists. Please input different name ...'
				);
			}
			else{
				//check initial
				$IniPlant	= strtoupper($data['inisial_plant']);
				$qInitial	= "SELECT * FROM company_plants WHERE inisial_plant = '".$IniPlant."' ";
				$numIni	= $this->db->query($qInitial)->num_rows();
				if($numIni > 0){
					$Arr_Kembali		= array(
						'status'		=> 3,
						'pesan'			=> 'Initial Name Already Exists. Please input different initial ...'
					);
				}
				else{
					$this->db->trans_start();
					$this->db->insert('company_plants', $Data_Insert);
					$this->db->trans_complete();
					
					if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$Arr_Kembali	= array(
							'pesan'		=>'Add company plants data failed. Please try again later ...',
							'status'	=> 2
						);
					}
					else{
						$this->db->trans_commit();
						$Arr_Kembali	= array(
							'pesan'		=>'Add company plants data success. Thanks ...',
							'status'	=> 1
						);
						history('Add Company Plant with code : '.$kode_plant);
					}		
				}
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

			$det_Province		= $this->master_model->getArray('provinsi',array(),'nama','nama');
			$det_branch			= $this->master_model->getArray('branch',array(),'nocab','cabang');
			$data = array(
				'title'					=> 'Add Company Plants',
				'action'				=> 'add_company_plant',
				'rows_province'	=> $det_Province,
				'branch'				=> $det_branch
			);
			$this->load->view('Company_plants/add',$data);
		}
	}

	public function edit() {
		if($this->input->post()){
			$Arr_Data	= array();
			$id_plant 	= $this->uri->segment(3);
			$data		= $this->input->post();
			
			$ArrUpdate	= array(
				'kdcab' => $data['kdcab'],
				'address' => $data['address'],
				'province' => $data['province'],
				'phone' => $data['phone'],
				'fax' => $data['fax'],
				'email' => $data['email'],
				);
			// echo "<pre>";
			// print_r($ArrUpdate);
			// exit;

			$this->db->trans_start();
			$this->db->where('id_plant', $id_plant);
			$this->db->update('company_plants', $ArrUpdate);
			$this->db->trans_complete();
			
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Update company plant data failed. Please try again later ...',
					'status'	=> 2
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Update company plant data success. Thanks ...',
					'status'	=> 1
				);
				history('Update Company Plant : '.$id_plant);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['update'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}
			
			$id_plant	= $this->uri->segment(3);
			$dataPlant 	= $this->db->query("SELECT * FROM company_plants WHERE id_plant='".$id_plant."' ")->result_array();

			$det_Province		= $this->master_model->getArray('provinsi',array(),'nama','nama');
			$det_branch			= $this->master_model->getArray('branch',array(),'nocab','cabang');
			$data = array(
				'title'			=> 'Edit Company Plants',
				'action'		=> 'edit',
				'rows_province'	=> $det_Province,
				'branch'		=> $det_branch,
				'row'			=> $dataPlant
			);
			$this->load->view('Company_plants/edit',$data);
		}
	}

	function hapus(){
		$idPlant 		= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		
		$ArrPlant		= array(
			'delete' 		=> 'Y',
			'delete_by' 	=> $data_session['ORI_User']['username'],
			'delete_date' 	=> date('Y-m-d H:i:s')
			);
		
		$this->db->trans_start();
		$this->db->where('id_plant', $idPlant);
		$this->db->update('company_plants', $ArrPlant);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete pieces data success. Thanks ...',
				'status'	=> 1
			);				
			history('Delete Company Plants with Kode/Id : '.$idPlant);
		}
		echo json_encode($Arr_Data);
	}
	
	public function modalDetail(){
		$this->load->view('Company_plants/modalDetail');
	}

}
