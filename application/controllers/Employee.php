<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Employee extends CI_Controller{
	var $API ="";
    public function __construct(){
        parent::__construct();
		// $this->API="http://103.228.117.98/hrori/assets/api/api_karyawan.min.php";
		// $this->load->library('curl');
		$this->load->model('employee_model');
		$this->load->model('master_model');
    }

    public function index(){ 
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		
		// $data_array = json_decode($this->curl->simple_get($this->API), true);
		// print_r($data_array);
		// exit;
		
		$data = array(
			'title'			=> 'Indeks Of Employee',
			'action'		=> 'asset',
			'akses_menu'	=> $Arr_Akses,
			'kategori' => $this->employee_model->getList('asset_category')
		);
        history("View index employee");
        $this->load->view('Employee/index', $data);
    }

    public function data_side_employee(){
		$this->employee_model->get_json_employee();
	}

	public function detail(){ 
		$id = $this->uri->segment(3);
		$header = $this->db->query("SELECT * FROM employee WHERE id='".$id."' LIMIT 1")->result();

		$department = $this->db->query("SELECT * FROM department WHERE deleted='N' ORDER BY nm_dept ASC")->result_array();
		$pendidikan = $this->db->query("SELECT * FROM list_help WHERE group_by='pendidikan' AND sts='Y' ORDER BY id ASC")->result_array();
		$provinsi = $this->db->query("SELECT * FROM provinsi")->result_array();
		$bank = $this->db->query("SELECT * FROM bank")->result_array();
		$agama = $this->db->query("SELECT * FROM list_help WHERE group_by='agama' AND sts='Y' ORDER BY id ASC")->result_array();
		$gender = $this->db->query("SELECT * FROM list_help WHERE group_by='gender' AND sts='Y' ORDER BY id ASC")->result_array();
		$sts_karyawan = $this->db->query("SELECT * FROM list_help WHERE group_by='status karyawan' AND sts='Y' ORDER BY id ASC")->result_array();
		$status = $this->db->query("SELECT * FROM list_help WHERE group_by='status aktif' AND sts='Y' ORDER BY id ASC")->result_array();
		
		
		$data = array(
			'title'			=> 'Add Employee',
			'action'		=> 'add',
			'departmentx'	=> $department,
			'pendidikanx'	=> $pendidikan,
			'provinsix'		=> $provinsi,
			'bankx'			=> $bank,
			'agamax'		=> $agama,
			'genderx'		=> $gender,
			'sts_karyawanx'	=> $sts_karyawan,
			'statusx'		=> $status,
			'header' 		=> $header
			);
			
        history("View detail Employee ".$id);
		$this->load->view('Employee/detail', $data);
	}

	public function add(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$db2 			= $this->load->database('instalasi', TRUE);
			// print_r($data); exit;
			$id					= $data['id'];
			$nik				= $data['nik'];
			$nm_karyawan		= strtolower($data['nm_karyawan']);
			$no_ktp				= strtolower($data['no_ktp']);
			$tmp_lahir			= strtolower($data['tmp_lahir']);
			$tgl_lahir			= date('Y-m-d', strtotime($data['tgl_lahir']));
			$gender				= $data['gender'];
			$agama				= $data['agama'];
			$department			= $data['department'];
			$cost_center		= $data['cost_center'];
			$no_ponsel			= strtolower($data['no_ponsel']);
			$email				= strtolower($data['email']);
			$pendidikan			= $data['pendidikan'];
			$position			= $data['position'];
			$ktp_provinsi		= $data['ktp_provinsi'];
			$domisili_provinsi	= $data['domisili_provinsi'];
			$ktp_kota			= $data['ktp_kota'];
			$domisili_kota		= $data['domisili_kota'];
			$ktp_kecamatan		= $data['ktp_kecamatan'];
			$domisili_kecamatan	= $data['domisili_kecamatan'];
			$ktp_kelurahan		= $data['ktp_kelurahan'];
			$domisili_kelurahan	= $data['domisili_kelurahan'];
			$ktp_kode_pos		= $data['ktp_kode_pos'];
			$domisili_kode_pos	= $data['domisili_kode_pos'];
			$ktp_alamat			= strtolower($data['ktp_alamat']);
			$domisili_alamat	= strtolower($data['domisili_alamat']);
			$npwp				= $data['npwp'];
			$bpjs				= $data['bpjs'];
			$tgl_join			= date('Y-m-d', strtotime($data['tgl_join']));
			$tgl_end			= date('Y-m-d', strtotime($data['tgl_end']));
			$rek_number			= $data['rek_number'];
			$bank_account		= $data['bank_account'];
			$sts_karyawan		= $data['sts_karyawan'];
			$status				= $data['status'];
			
			$created_by 		= 'updated_by';
			$created_date 		= 'updated_date';
			$tanda 				= 'Update';


			if(empty($id)){
				$Y = date('y');

				$created_by 		= 'created_by';
				$created_date 		= 'created_date';
				$tanda 				= 'Insert';
				//kode group
				$q_group		= "SELECT max(nik) as maxP FROM employee WHERE nik LIKE 'ID".$Y."%' ";
				$rest_group		= $this->db->query($q_group)->result_array();
				$angka_group	= $rest_group[0]['maxP'];
				$urut_g			= (int)substr($angka_group, 4, 5);
				$urut_g++;
				$urut			= sprintf('%05s',$urut_g);
				$nik			= "ID".$Y.$urut;
			}

			$ArrHeader = array(
				'nik' 	=> $nik,
				'nm_karyawan' => $nm_karyawan,
				'tmp_lahir' => $tmp_lahir,
				'tgl_lahir' => $tgl_lahir,
				'department' => $department,
				'cost_center' => $cost_center,
				'gender' => $gender,
				'agama' => $agama,
				'pendidikan' => $pendidikan,
				'position' => $position,
				'ktp_provinsi' => $ktp_provinsi,
				'ktp_kota' => $ktp_kota,
				'ktp_kecamatan' => $ktp_kecamatan,
				'ktp_kelurahan' => $ktp_kelurahan,
				'ktp_kode_pos' => $ktp_kode_pos,
				'ktp_alamat' => $ktp_alamat,
				'domisili_provinsi' => $domisili_provinsi,
				'domisili_kota' => $domisili_kota,
				'domisili_kecamatan' => $domisili_kecamatan,
				'domisili_kelurahan' => $domisili_kelurahan,
				'domisili_kode_pos' => $domisili_kode_pos,
				'domisili_alamat' => $domisili_alamat,
				'no_ponsel' => $no_ponsel,
				'email' => $email,
				'npwp' => $npwp,
				'bpjs' => $bpjs,
				'no_ktp' => $no_ktp,
				'tgl_join' => $tgl_join,
				'tgl_end' => $tgl_end,
				'sts_karyawan' => $sts_karyawan,
				'rek_number' => $rek_number,
				'bank_account' => $bank_account,
				'status' => $status,
				$created_by => $this->session->userdata['ORI_User']['username'],
				$created_date => date('Y-m-d h:i:s')
			);

			// print_r($ArrHeader);
			// exit;

			$this->db->trans_start();
				if(empty($id)){
					$this->db->insert('employee', $ArrHeader);
				}
				if(!empty($id)){
					$this->db->where('id', $id);
					$this->db->update('employee', $ArrHeader);
				}
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save data failed ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save data success. Thanks ...',
					'status'	=> 1
				);
			history($tanda." data employee ".$nik);
			}

			echo json_encode($Arr_Data);
		}
		else{
            $controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
			}

			$id = $this->uri->segment(3);
			$header = $this->db->query("SELECT * FROM employee WHERE id='".$id."' LIMIT 1")->result();

			$department = $this->db->query("SELECT * FROM department WHERE deleted='N' ORDER BY nm_dept ASC")->result_array();
            $pendidikan = $this->db->query("SELECT * FROM list_help WHERE group_by='pendidikan' AND sts='Y' ORDER BY id ASC")->result_array();
			$provinsi = $this->db->query("SELECT * FROM provinsi")->result_array();
			$bank = $this->db->query("SELECT * FROM bank")->result_array();
			$agama = $this->db->query("SELECT * FROM list_help WHERE group_by='agama' AND sts='Y' ORDER BY id ASC")->result_array();
			$gender = $this->db->query("SELECT * FROM list_help WHERE group_by='gender' AND sts='Y' ORDER BY id ASC")->result_array();
			$sts_karyawan = $this->db->query("SELECT * FROM list_help WHERE group_by='status karyawan' AND sts='Y' ORDER BY id ASC")->result_array();
			$status = $this->db->query("SELECT * FROM list_help WHERE group_by='status aktif' AND sts='Y' ORDER BY id ASC")->result_array();
			
            
			$data = array(
				'title'			=> 'Add Employee',
				'action'		=> 'add',
				'departmentx'	=> $department,
				'pendidikanx'	=> $pendidikan,
				'provinsix'		=> $provinsi,
				'bankx'			=> $bank,
				'agamax'		=> $agama,
				'genderx'		=> $gender,
				'sts_karyawanx'	=> $sts_karyawan,
				'statusx'		=> $status,
				'header' 		=> $header
				);
			$this->load->view('Employee/add', $data); 
		}
	}

	public function list_center(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM costcenter WHERE id_dept='".$id."' ORDER BY nm_costcenter ASC";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An Division</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id_costcenter == $cs)?'selected':'';
			$option .= "<option value='".$row->id_costcenter."' ".$selx.">".strtoupper($row->nm_costcenter)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function list_position(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM costcenter_position WHERE id_costcenter='".$id."' ORDER BY nm_position ASC";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An Position</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id == $cs)?'selected':'';
			$option .= "<option value='".$row->id."' ".$selx.">".strtoupper($row->nm_position)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function get_kota(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM kabupaten WHERE id_prov='".$id."'";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An Districts</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id_kab == $cs)?'selected':'';
			$option .= "<option value='".$row->id_kab."' ".$selx.">".strtoupper($row->nama)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function get_kecamatan(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM kecamatan WHERE id_kab='".$id."'";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An Sub-Districts</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id_kec == $cs)?'selected':'';
			$option .= "<option value='".$row->id_kec."' ".$selx.">".strtoupper($row->nama)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function get_desa(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM kelurahan WHERE id_kec='".$id."'";
		$Q_result	= $this->db->query($query)->result();
		$option 	= "<option value='0'>Select An Village</option>";
		foreach($Q_result as $row)	{
			$selx = ($row->id_kel == $cs)?'selected':'';
			$option .= "<option value='".$row->id_kel."' ".$selx.">".strtoupper($row->nama)."</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}

	public function hapus(){
		$id 			= $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);

		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->update('employee', $ArrPlant);
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
			history('Delete employee data : '.$id);
		}
		echo json_encode($Arr_Data);
	}


}
?>
