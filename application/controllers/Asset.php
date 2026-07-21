<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Asset extends CI_Controller{

    public function __construct(){
        parent::__construct();

		$this->load->model('asset_model');
		$this->load->model('master_model');

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

		// $SQL_LAST = $this->db->select('MAX(MONTH(bulan)) AS bulan, MAX(YEAR(tahun)) AS tahun')->get_where('asset_generatex',array('flag'=>'N'))->result();
		
		$data = array(
			'title'			=> 'Indeks Of Assets',
			'action'		=> 'asset',
			'akses_menu'	=> $Arr_Akses,
			'kategori' 		=> $this->asset_model->getList('asset_category'),
			// 'bulan' 		=> date('F',strtotime($SQL_LAST[0]->bulan)),
			// 'tahun' 		=> date('Y',strtotime($SQL_LAST[0]->tahun)),
		);
        history("View index asset");
        $this->load->view('Asset/index', $data);
    }

	public function data_side(){
		$this->asset_model->getDataJSON();
	}

	public function modal_view(){
		$id = $this->uri->segment(3);
		$qData	= "SELECT a.*, b.nm_costcenter FROM asset a LEFT JOIN costcenter b ON a.id_costcenter=b.id_costcenter WHERE a.id='".$this->uri->segment(3)."'";
		$dataD	= $this->db->query($qData)->result_array();

		$data = array(
			'title'			=> 'Indeks Of Assets',
			'action'		=> 'asset',
			'dataD'			=> $dataD, 
			'list_cab' 		=> $this->asset_model->getList('asset_branch'),
			'list_pajak'	=> $this->asset_model->getList('asset_category_pajak'),
			'list_dept' 	=> $this->asset_model->getList('department'),
			'list_catg' 	=> $this->asset_model->getList('asset_category'),
			'list_coa' 		=> $this->asset_model->getList('asset_coa')
		);
        history("View index asset");
		$this->load->view('Asset/modal_view', $data);
	}

	public function add(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$db2 			= $this->load->database('instalasi', TRUE);
			$id				= $data['id'];
			$kd_asset		= $data['kd_asset'];

			
			$nmCategory		= $this->asset_model->getWhere('asset_category', 'id', $data['category']);

			$id_coa			= $data['id_coa'];
			$category		= $data['category'];
			$penyusutan		= $data['penyusutan'];
			$category_pajak	= $data['category_pajak'];
			$KdCategory		= sprintf('%02s',$category);
			$KdCategoryPjk	= sprintf('%02s',$category_pajak);
			$Ym				= date('ym');
			$tgl_oleh		= date('Y-m-d');
			$tgl_perolehan	= date('Y-m-d');
			
			$branch		= $data['branch'];

			if(!empty($data['tanggal'])){
				$tgl_oleh		= date('Y-m-d', strtotime($data['tanggal']));
				$Year			= date('y', strtotime($data['tanggal']));
				$Month			= date('m', strtotime($data['tanggal']));
				$Ym				= $Year.$Month;
			}
			
			if(!empty($data['tanggal_oleh'])){
				$tgl_perolehan		= date('Y-m-d', strtotime($data['tanggal_oleh']));
				$Year_perolehan		= date('y', strtotime($data['tanggal_oleh']));
				$Month_perolehan	= date('m', strtotime($data['tanggal_oleh']));
				$Ym_perolehan		= $Year_perolehan.$Month_perolehan;
			}

			$qQuery			= "SELECT max(kd_asset) as maxP FROM asset WHERE kd_asset LIKE '".$branch."-".$Ym_perolehan.$KdCategory.$KdCategoryPjk."-%' ";//category='".$category."' AND 
			$restQuery		= $this->db->query($qQuery)->result_array();

			$angkaUrut2		= $restQuery[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 13, 3);
			$urutan2++;
			$urut2			= sprintf('%03s',$urutan2);

			$kode_assets	= $branch."-".$Ym_perolehan.$KdCategory.$KdCategoryPjk."-".$urut2;

			//kode group
			$q_group		= "SELECT max(code_group) as maxP FROM asset WHERE code_group LIKE 'AS%' ";
			$rest_group		= $this->db->query($q_group)->result_array();

			$angka_group	= $rest_group[0]['maxP'];
			$urut_g			= (int)substr($angka_group, 2, 5);
			$urut_g++;
			$urut			= sprintf('%05s',$urut_g);
			$kode_group		= "AS".$urut;

			//insert to instalasi
			$ArrHeaderInstalasi = array(
				'code_group' 	=> $kode_group,
				'category' 		=> 'asset '.strtolower($nmCategory[0]['nm_category']),
				'spec' 			=> strtolower($data['nm_asset']),
				'created_by' 	=> $this->session->userdata['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d h:i:s')
			);

			$num_cty = $db2->query("SELECT * FROM vehicle_tool_category WHERE category='asset ".strtolower($nmCategory[0]['nm_category'])."' ")->num_rows();

			$ArrCategory = array(
				'category' 		=> 'asset '.strtolower($nmCategory[0]['nm_category']),
				'created_by' 	=> 'asset',
				'created_date' 	=> date('Y-m-d h:i:s')
			);

			$region = $db2->query("SELECT * FROM region ORDER BY urut ASC")->result_array();
			$ArrPrice = array();
			foreach ($region as $key => $value) {
				$ArrPrice[$key]['category'] 		= 'vehicle tool';
				$ArrPrice[$key]['code_group'] 		= $kode_group;
				$ArrPrice[$key]['unit_material'] 	= 'month';
				$ArrPrice[$key]['kurs'] 			= 'IDR';
				$ArrPrice[$key]['region'] 			= $value['region'];
				$ArrPrice[$key]['rate'] 			= str_replace(',', '', $data['value']);
				$ArrPrice[$key]['updated_by'] 		= $this->session->userdata['ORI_User']['username'];
				$ArrPrice[$key]['updated_date'] 	= date('Y-m-d h:i:s');
			}
			
			$config = array(
			  'upload_path' 		=> './assets/foto/',
			  'allowed_types' 		=> 'gif|jpg|png|jpeg|JPG|PNG',
			  'file_name' 			=> $kode_assets,
			  'file_ext_tolower' 	=> TRUE,
			  'overwrite' 			=> TRUE,
			  'max_size' 			=> 2000048,
			  'remove_spaces' 		=> TRUE
			);
			
			$tmp 		= explode(".", $_FILES['foto']['name']);
			$ext 		= end($tmp);
			$pic 		= $kode_assets.".".strtolower($ext);
			
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('foto')){
				// $result = $this->upload->display_errors();
				$error = array('error' => $this->upload->display_errors());
				$Arr_Kembali		= array(
					'status'		=> 3,
					'pesan'			=> $error['error']
				);
				echo json_encode($Arr_Kembali);
				return false;
			}
			else{
				$paths 		= $_SERVER['DOCUMENT_ROOT'].'/assets/foto/'.$pic; 
				if(file_exists($paths)){
					unlink($paths);
				}
				$data_foto  = array('upload_data' => $this->upload->data('foto'));
			}

			$detailDataDash	= array();
			// echo $kode_assets; exit;

			$lopp 	= 0;
			$lopp2 	= 0;
			for($no=1; $no <= $data['qty']; $no++){
				$Nomor	= sprintf('%03s',$no);
				$lopp++;
				$detailData[$lopp]['kd_asset'] 		= $kode_assets.$Nomor;
				$detailData[$lopp]['code_group'] 	= $kode_group;
				$detailData[$lopp]['nm_asset'] 		= $data['nm_asset'];
				$detailData[$lopp]['tgl_perolehan'] = $tgl_perolehan;
				$detailData[$lopp]['id_coa'] 		= $id_coa;
				$detailData[$lopp]['category'] 		= $data['category'];
				$detailData[$lopp]['category_pajak']= $data['category_pajak'];
				$detailData[$lopp]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
				$detailData[$lopp]['nilai_asset'] 	= str_replace(',', '', $data['nilai_asset']);
				$detailData[$lopp]['qty'] 			= $data['qty'];
				$detailData[$lopp]['asset_ke'] 		= $no;
				$detailData[$lopp]['depresiasi'] 	= $data['depresiasi'];
				$detailData[$lopp]['value'] 		= str_replace(',', '', $data['value']);
				$detailData[$lopp]['kdcab'] 		= $branch;
				$detailData[$lopp]['foto'] 			= $pic;
				$detailData[$lopp]['penyusutan'] 	= $penyusutan;
				$detailData[$lopp]['id_dept'] 		= $data['lokasi_asset'];
				$detailData[$lopp]['department'] 	= get_name('department', 'nm_dept', 'id', $data['lokasi_asset']);
				$detailData[$lopp]['id_costcenter'] = $data['cost_center'];
				$detailData[$lopp]['nama_user'] 	= $data['nama_user'];
				$detailData[$lopp]['cost_center'] 	= get_name('costcenter', 'nm_costcenter', 'id_costcenter', $data['cost_center']);
				$detailData[$lopp]['code_ori'] 		= $data['code_ori'];
				$detailData[$lopp]['lokasi'] 		= $data['lokasi'];
				$detailData[$lopp]['status_asset'] 	= $data['status_asset'];
				$detailData[$lopp]['created_by'] 	= $this->session->userdata['ORI_User']['username'];
				$detailData[$lopp]['created_date'] 	= date('Y-m-d h:i:s');
				$detailData[$lopp]['tgl_depresiasi'] = $tgl_oleh;

				$jmlx   	= $data['depresiasi'] * 12;
				$date_now 	= date('Y-m-d');
				$date_now_real 	= date('Y-m-d');

				if(!empty($data['tanggal'])){
					$date_now 	= date('Y-m-d', strtotime($data['tanggal']));
				}

				for($x=1; $x <= $jmlx; $x++){
					$lopp2 += $x;

					//bulan depat mulai menyusut
					// $Tanggal 	= date('Y-m', mktime(0,0,0,substr($date_now,5,2)+ $x,1,substr($date_now,0,4)));
					//bulan sekarang langsung disusutkan
					$TglNow		= date('Y-m', strtotime($date_now_real));
					$Tanggal 	= date('Y-m', mktime(0,0,0,substr($date_now,5,2)+ $x,0,substr($date_now,0,4)));
					$flagx		= 'X';
					if($penyusutan == 'Y'){
						$flagx		= 'N';
						if($Tanggal < $TglNow){
							$flagx	= 'Y';
						}
					}

					$detailDataDash[$lopp2]['kd_asset'] 	= $kode_assets.$Nomor;
					$detailDataDash[$lopp2]['nm_asset'] 	= $data['nm_asset'];
					$detailDataDash[$lopp2]['category'] 	= $data['category'];
					$detailDataDash[$lopp2]['category_pajak'] 	= $data['category_pajak'];
					$detailDataDash[$lopp2]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
					$detailDataDash[$lopp2]['bulan'] 		= substr($Tanggal, 5,2);
					$detailDataDash[$lopp2]['tahun'] 		= substr($Tanggal, 0,4);
					$detailDataDash[$lopp2]['lokasi_asset'] = $data['lokasi_asset'];
					$detailDataDash[$lopp2]['cost_center'] 	= $data['cost_center'];
					$detailDataDash[$lopp2]['nilai_susut'] 	= str_replace(',', '', $data['value']);
					$detailDataDash[$lopp2]['kdcab'] 		= $branch;
					$detailDataDash[$lopp2]['flag'] 		= $flagx;
				}

			}

			$tanda = "Insert ";
			$tanda2 = $kode_assets;
			

			// print_r($ArrHeaderInstalasi);
			// print_r($ArrPrice);
			// echo $num_cty;
			// exit;

			$this->db->trans_start();
				$this->db->insert_batch('asset', $detailData);
				$this->db->insert_batch('asset_generate', $detailDataDash);

				$db2->insert('vehicle_tool_new', $ArrHeaderInstalasi);
				$db2->insert_batch('price_ref', $ArrPrice);
				if($num_cty < 1){
					$db2->insert('vehicle_tool_category', $ArrCategory);
				}
				
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Asset gagal disimpan ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Asset berhasil disimpan. Thanks ...',
					'status'	=> 1
				);
			history($tanda."asset ".$tanda2);
			}

			echo json_encode($Arr_Data);
		}
		else{
			$id = $this->uri->segment(3);
			$header = $this->asset_model->getWhere('asset', 'id', $id);
			$data = array(
				'title'			=> 'Add Asset',
				'action'		=> 'add',
				'data' 			=> $header,
				'list_cab' 		=> $this->asset_model->getList('asset_branch'),
				'list_coa' 		=> $this->asset_model->getList('asset_coa'),
				'list_pajak'	=> $this->asset_model->getList('asset_category_pajak'),
				'list_dept' => $this->asset_model->getList('department'),
				'list_catg' => $this->asset_model->getList('asset_category')
				);
			$this->load->view('Asset/add', $data);
		}
	}
	
	
	public function edit(){
		
			$id = $this->uri->segment(3);
			$header = $this->asset_model->getWhere('asset', 'id', $id);
			$data = array(
				'title'			=> 'Edit Asset',
				'action'		=> 'edit',
				'data' 			=> $header,
				'list_cab' 		=> $this->asset_model->getList('asset_branch'),
				'list_coa' 		=> $this->asset_model->getList('asset_coa'),
				'list_pajak'	=> $this->asset_model->getList('asset_category_pajak'),
				'list_dept' => $this->asset_model->getList('department'),
				'list_catg' => $this->asset_model->getList('asset_category')
				);
			$this->load->view('Asset/edit', $data);
	}
	
	
	//move asset
	public function edited(){
		
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		
		$branch				= $data['branch'];
		$kd_asset			= $data['kd_asset'];
		$lokasi_asset_new	= $data['lokasi_asset_new'];
		$cost_center_new	= $data['cost_center_new'];
		
		$ArrUpHeader = array(
		    'id_coa'		=> $data['id_coa'],
			'category'		=> $data['category'],
			'modified_by' 	=> $this->session->userdata['ORI_User']['username'],
			'modified_date' => date('Y-m-d h:i:s')
		);
		
	
		
		$this->db->trans_start();
			$this->db->where('kd_asset', $kd_asset);
			$this->db->update('asset', $ArrUpHeader);			
			
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal disimpan ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
			history('Move asset to asset');
		}
		
		echo json_encode($Arr_Data);
	}
	
	
	//move asset
	public function move_asset(){
		
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		
		$branch				= $data['branch'];
		$kd_asset			= $data['kd_asset'];
		$lokasi_asset_new	= $data['lokasi_asset_new'];
		$cost_center_new	= $data['cost_center_new'];
		
		$ArrUpHeader = array(
			'kdcab' 	=> $branch,
			'id_dept' 	=> $lokasi_asset_new,
			'department' 	=> get_name('department', 'nm_dept', 'id', $lokasi_asset_new),
			'id_costcenter'	=> $cost_center_new,
			'cost_center' 	=> get_name('costcenter', 'nm_costcenter', 'id_costcenter', $cost_center_new),
			'nama_user' 	=> $data['nama_user'],
			'code_ori' 		=> $data['code_ori'],
			'lokasi' 		=> $data['lokasi'],
			'status_asset' 	=> $data['status_asset'],
			'modified_by' 	=> $this->session->userdata['ORI_User']['username'],
			'modified_date' => date('Y-m-d h:i:s')
		);
		
		$ArrUpGen = array(
			'kdcab' 	=> $branch,
			'lokasi_asset' 	=> $lokasi_asset_new,
			'cost_center'	=> $cost_center_new
		);
		
		// echo $cost_center_new; exit;
		
		
		
		// print_r($detailData);
		// print_r($detailDataDash);
		// exit;
		
		$this->db->trans_start();
			$this->db->where('kd_asset', $kd_asset);
			$this->db->update('asset', $ArrUpHeader);
			
			$this->db->where(array('kd_asset'=>$kd_asset,'flag'=>'N'));
			$this->db->update('asset_generate', $ArrUpGen);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal disimpan ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil disimpan. Thanks ...',
				'status'	=> 1
			);
			history('Move asset to asset');
		}
		
		echo json_encode($Arr_Data);
	}
	
	//delete asset
	public function delete_asset(){
		
		$Arr_Kembali	= array();
		$data			= $this->input->post();

		$kd_asset		= $this->uri->segment(3);
		
		$ArrUpHeader = array(
			'deleted_by' 	=> $this->session->userdata['ORI_User']['username'],
			'deleted_date' => date('Y-m-d h:i:s')
		);
		
		$ArrUpGen = array(
			'flag' 	=> 'L'
		);

		$this->db->trans_start();
			$this->db->where('kd_asset', $kd_asset);
			$this->db->update('asset', $ArrUpHeader);
			
			$this->db->where(array('kd_asset'=>$kd_asset,'flag'=>'N'));
			$this->db->update('asset_generate', $ArrUpGen);
			
			$this->db->where(array('kd_asset'=>$kd_asset,'flag'=>'X'));
			$this->db->update('asset_generate', $ArrUpGen);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal dihapus ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil dihapus. Thanks ...',
				'status'	=> 1
			);
			history('Delete asset '.$kd_asset);
		}
		
		echo json_encode($Arr_Data);
	}

	public function list_center(){
		$id = $this->uri->segment(3);
		$cs = $this->uri->segment(4);
		$query	 	= "SELECT * FROM costcenter WHERE id_dept='".$id."' AND deleted='N' ORDER BY nm_costcenter ASC";
		$Q_result	= $this->db->query($query)->result();
		if(!empty($Q_result)){
			$option 	= "<option value='0'>Select Costcenter</option>";
			foreach($Q_result as $row)	{
				$selx = ($row->id_costcenter == $cs)?'selected':'';
				$option .= "<option value='".$row->id_costcenter."' ".$selx.">".strtoupper($row->nm_costcenter)."</option>";
			}
		}
		else{
			$option 	= "<option value='0'>List Empty</option>";
		}
		echo json_encode(array(
			'option' => $option
		));
	}
	
	public function get_jangka_waktu(){
		$id = $this->uri->segment(3);
		$query	 	= "SELECT * FROM asset_category_pajak WHERE id='".$id."' ";
		$Q_result	= $this->db->query($query)->result();
		$data 	 	= $Q_result[0]->jangka_waktu;
		echo json_encode(array(
			'jangka_waktu' => $data
		));
	}

	//======================================================================================================================
    //===================================================CATEGORY============================================================
    //======================================================================================================================

	public function category(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Asset Category',
			'action'		=> 'category',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Master Asset category');
		$this->load->view('Asset/category',$data);
	}

	public function data_side_category(){
		$this->asset_model->get_json_category();
	}

	public function add_category(){ 
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			//header
			$id 		    = $data['id'];
			$nm_category	= strtoupper($data['nm_category']);
			$status			= $data['status'];

			if(empty($id)){
                $ArrHeader = array(
                    'nm_category'   => $nm_category,
                    'status' 		=> $status,
                    'created_by' 	=> $data_session['ORI_User']['username'],
                    'created_date' 	=> $dateTime
                );
                $TandaI = "Insert";
			}

			if(!empty($id)){
                $ArrHeader = array(
                    'nm_category'   => $nm_category,
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
                    $this->db->insert('asset_category', $ArrHeader);
                }
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('asset_category', $ArrHeader);
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
				history($TandaI.' Category Asset '.$id.' / '.$nm_category);
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
            
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM asset_category WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();

			$data = array(
				'title'		=> 'Add Category Asset',
                'action'	=> 'add',
                'data'      => $result
			);
			$this->load->view('Asset/add_category',$data);
		}
	}

	public function hapus_category(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'deleted' 		=> 'Y',
			'deleted_by' 	=> $data_session['ORI_User']['username'],
			'deleted_date' 	=> date('Y-m-d H:i:s')
			);


		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->update('asset_category', $ArrPlant);
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
			history('Delete Category Asset Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
	
	//======================================================================================================================
    //===================================================PR ASSET============================================================
    //======================================================================================================================
		
	public function pr(){ 
        $this->asset_model->pr();
    }
	
	public function server_side_pr_asset(){ 
        $this->asset_model->get_data_json_pr_asset();
    }
	
	public function add_pr(){ 
        $this->asset_model->add_pr();
    }
	
	public function server_side_add_pr_asset(){ 
        $this->asset_model->get_data_json_add_pr_asset();
    }
	
	public function approve_pr(){ 
        $this->asset_model->approve_pr();
    }

	public function print_pr_asset(){
		$no_pr 	= $this->uri->segment(3);
		$sql	= "	SELECT
						a.*
					FROM
						tran_pr_detail a LEFT JOIN tran_pr_header b ON a.no_pr = b.no_pr
					WHERE  1=1 AND a.category='asset' AND a.no_pr = '".$no_pr."'";
		$result = $this->db->query($sql)->result_array();

		$data = array(
		  'no_pr'		=> $no_pr,
		  'result'		=> $result
		);
		$this->load->view('Print/print_pr_asset',$data);
	}

	//generate asset manual
	public function generate_asset_manual(){
		// $get_asset = $this->db->get_where('asset',array('depresiasi >'=>0,'id'=>'4'))->result_array();
		$get_asset = $this->db->order_by('kd_asset','asc')->get_where('asset',array('depresiasi >'=>0))->result_array();
		$ArrDetailAsset = [];
		$nomor = 0;
		foreach ($get_asset as $key => $value) { 
			if($value['kd_asset'] != 'ORI-22000000-000001' AND $value['kd_asset'] != 'ORI-22000000-000002' AND $value['kd_asset'] != 'ORI-22000000-000419'){
			// if($value['kd_asset'] != 'ORI-22000000-000001' AND $value['kd_asset'] != 'ORI-22000000-000002'){
				$key++;
				$TOTAL_BULAN 	= $value['depresiasi'] * 12;
				$TGL_AWAL 		= $value['tgl_perolehan'];
				$TGL_NOW		= 202205;
				for ($i=0; $i < $TOTAL_BULAN ; $i++) { $nomor++;
					$BULAN = date('m', strtotime('+'.$i.' month', strtotime($TGL_AWAL)));
					$TAHUN = date('Y', strtotime('+'.$i.' month', strtotime($TGL_AWAL)));
					$TGLYM = date('Ym', strtotime('+'.$i.' month', strtotime($TGL_AWAL)));
					$FLAG = 'Y';
					if($TGLYM >= $TGL_NOW){
						$FLAG = 'N';
					}
					$ArrDetailAsset[$nomor]['kd_asset'] 		= $value['kd_asset'];
					$ArrDetailAsset[$nomor]['nm_asset'] 		= $value['nm_asset'];
					$ArrDetailAsset[$nomor]['category'] 		= $value['category'];
					$ArrDetailAsset[$nomor]['category_pajak'] 	= $value['category_pajak'];
					$ArrDetailAsset[$nomor]['nm_category'] 		= $value['nm_category'];
					$ArrDetailAsset[$nomor]['bulan'] 			= $BULAN;
					$ArrDetailAsset[$nomor]['tahun'] 			= $TAHUN;
					$ArrDetailAsset[$nomor]['nilai_susut'] 		= $value['value'];
					$ArrDetailAsset[$nomor]['lokasi_asset'] 	= $value['id_dept'];
					$ArrDetailAsset[$nomor]['cost_center'] 		= $value['id_costcenter'];
					$ArrDetailAsset[$nomor]['kdcab'] 			= $value['kdcab'];
					$ArrDetailAsset[$nomor]['flag'] 			= $FLAG;
				}
			}
			# code...
		}
		// echo "<pre>";
		// print_r($ArrDetailAsset);
		$whereNotIN = array('ORI-22000000-000001', 'ORI-22000000-000002', 'ORI-22000000-000419');
		// $whereNotIN = array('ORI-22000000-000001', 'ORI-22000000-000002');
		$this->db->where_not_in('kd_asset', $whereNotIN);
		$this->db->delete('asset_generate');

		$this->db->insert_batch('asset_generate',$ArrDetailAsset);
		echo 'Success Insert !';
	}


	//JURNAL
	public function modal_jurnal(){
		$this->load->view('Asset/modal_jurnal');
	}

	public function saved_jurnal(){
		$session 		= $this->session->userdata('app_session');
		$ArrDel = $this->db->query("SELECT nomor FROM jurnaltras WHERE jenis_trans = 'asset jurnal' AND SUBSTRING_INDEX(tanggal, '-', 2) = '".date('Y-m')."' GROUP BY nomor ")->result_array();

		$dtListArray = array();
		foreach($ArrDel AS $val => $valx){
			$dtListArray[$val] = $valx['nomor'];
		}

		$dtImplode	= "('".implode("','", $dtListArray)."')";

		$date_now	= date('Y-m-d');
		$bln		= ltrim(date('m'), 0);
		$thn		= date('Y');
		$bulanx		= date('m');

		if(!empty($this->input->post('tgl_jurnal'))){
			$date_now	= $this->input->post('tgl_jurnal')."-01";
			$DtExpl		= explode('-', $date_now);
			$bln		= ltrim($DtExpl[1], 0);
			$thn		= $DtExpl[0];
			$bulanx		= $DtExpl[1];
		}
		// print_r($dtImplode);
		// exit;

		$ArrJurnal_D = $this->Asset_model->getList('asset_jurnal');
		$ArrDebit = array();
		$ArrKredit = array();
		$ArrJavh = array();
		$Loop = 0;
		foreach($ArrJurnal_D AS $val => $valx){
			$Loop++;

			if($valx['category'] == 1){
				$coaD 	= "6831-02-01";
				$ketD	= "BIAYA PENYUSUTAN KENDARAAN";
				$coaK 	= "1309-05-01";
				$ketK	= "AKUMULASI PENYUSUTAN KENDARAAN";
			}
			if($valx['category'] == 2){
				$coaD 	= "6831-06-01";
				$ketD	= "BIAYA PENYUSUTAN HARTA LAINNYA";
				$coaK 	= "1309-08-01";
				$ketK	= "AKUMULASI PENYUSUTAN HARTA LAINNYA";
			}
			if($valx['category'] == 3){
				$coaD 	= "6831-01-01";
				$ketD	= "BIAYA PENYUSUTAN BANGUNAN";
				$coaK 	= "1309-07-01";
				$ketK	= "AKUMULASI PENYUSUTAN BANGUNAN";
			}

			$ArrDebit[$Loop]['tipe'] 			= "JV";
			$ArrDebit[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrDebit[$Loop]['tanggal'] 		= $date_now;
			$ArrDebit[$Loop]['no_perkiraan'] 	= $coaD;
			$ArrDebit[$Loop]['keterangan'] 		= $ketD;
			$ArrDebit[$Loop]['no_reff'] 		= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
			$ArrDebit[$Loop]['kredit'] 			= 0;
			$ArrDebit[$Loop]['jenis_trans'] 	= 'asset jurnal';

			$ArrKredit[$Loop]['tipe'] 			= "JV";
			$ArrKredit[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrKredit[$Loop]['tanggal'] 		= $date_now;
			$ArrKredit[$Loop]['no_perkiraan'] 	= $coaK;
			$ArrKredit[$Loop]['keterangan'] 	= $ketK;
			$ArrKredit[$Loop]['no_reff'] 		= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrKredit[$Loop]['debet'] 			= 0;
			$ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];
			$ArrKredit[$Loop]['jenis_trans'] 	= 'asset jurnal';

			$ArrJavh[$Loop]['nomor'] 			= $this->Jurnal_model->get_Nomor_Jurnal_Memorial($valx['kdcab'],date('Y-m-d'));
			$ArrJavh[$Loop]['tgl'] 				= $date_now;
			$ArrJavh[$Loop]['jml'] 				= $valx['sisa_nilai'];
			$ArrJavh[$Loop]['kdcab'] 			= $valx['kdcab'];
			$ArrJavh[$Loop]['jenis'] 			= "V";
			$ArrJavh[$Loop]['keterangan'] 		= "PENYUSUTAN ASSET";
			$ArrJavh[$Loop]['bulan'] 			= $bln;
			$ArrJavh[$Loop]['tahun'] 			= $thn;
			$ArrJavh[$Loop]['user_id'] 			= "System";
			$ArrJavh[$Loop]['tgl_jvkoreksi'] 	= $date_now;

			$this->Jurnal_model->update_Nomor_Jurnal($valx['kdcab'],'JM');
		}

		// echo "<pre>";
		// print_r($ArrDebit);
		// print_r($ArrKredit);
		// print_r($ArrJavh);
		// exit;

		$this->db->trans_start();
			$this->db->query("DELETE FROM jurnaltras WHERE nomor IN ".$dtImplode." ");
			$this->db->query("DELETE FROM javh WHERE nomor IN ".$dtImplode." ");
			$this->db->insert_batch('jurnaltras', $ArrDebit);
			$this->db->insert_batch('jurnaltras', $ArrKredit);
			$this->db->insert_batch('javh', $ArrJavh);
			$this->db->query("UPDATE asset_generate SET flag='Y' WHERE bulan='".$bulanx."' AND tahun='".$thn."' ");
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$this->db->query("INSERT INTO asset_jurnal_log (tanggal, ket, jurnal_by, bulan, tahun, kdcab) VALUES ('".date('Y-m-d H:i:s')."', 'FAILED', '".$this->session->userdata['app_session']['username']."', '".$bulanx."', '".$thn."', '".$session['kdcab']."')");
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$this->db->query("INSERT INTO asset_jurnal_log (tanggal, ket, jurnal_by, bulan, tahun, kdcab) VALUES ('".date('Y-m-d H:i:s')."', 'SUCCESS', '".$this->session->userdata['app_session']['username']."', '".$bulanx."', '".$thn."', '".$session['kdcab']."')");
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil disimpan. Terimakasih ...',
				'status'	=> 1
			);
		}

		echo json_encode($Arr_Data);
	}

	public function download_excel($category=null,$bulan=null,$tahun=null){
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

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'DATA ASSETS DEPRESIASI PER '.$bulan.'-'.$tahun);
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CODE');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ASSET NAME');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'TGL PEROLEHAN');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'KELOMPOK PENYUSUTAN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'COSTCENTER');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'DEPRESIASI (YEAR)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'NILAI PEROLEHAN');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'DEPRESIASI');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'AKUMULASI DEPRESIASI');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		$sheet->setCellValue('L'.$NewRow, 'ASSET VALUE');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(10);

		$where_kategori = "";
		if($category != '0'){
			$where_kategori = " AND a.category = '".$category."' ";
		}

		$WHERE_PERIODE = "AND (b.flag='N' OR b.flag='X')";
		$WHERE_PERIODE2 = "AND (b.flag='Y')";
		if($bulan != '0' AND $tahun != '0'){
//			$WHERE_PERIODE = "AND CONCAT(b.tahun,'-',b.bulan,'-01') > '".$tahun."-".$bulan."-01' OR a.penyusutan = 'N'";
			$WHERE_PERIODE2 = "AND CONCAT(b.tahun,'-',b.bulan,'-01') <= '".$tahun."-".$bulan."-01'";
		}

		$SQL = "
		SELECT
			a.id,
			a.kd_asset,
			a.nm_asset,
			a.category,
			a.penyusutan,
			c.nm_category,
			a.nilai_asset,
			a.depresiasi,
			a.`value`,
			(SELECT SUM(b.nilai_susut) FROM asset_generate b WHERE a.kd_asset = b.kd_asset AND a.deleted = 'N' ".$WHERE_PERIODE.") as sisa_nilai,
			(SELECT SUM(b.nilai_susut) FROM asset_generate b WHERE a.kd_asset = b.kd_asset AND a.deleted = 'N' AND b.flag='Y' ".$WHERE_PERIODE2.") as total_depresiasi,
			a.department,
			a.kdcab,
			a.cost_center,
			a.tgl_perolehan,
			d.coa AS no_perkiraan,
			d.keterangan AS ket_coa
		FROM
			asset a 
			LEFT JOIN asset_category c ON a.category = c.id
			LEFT JOIN asset_coa d ON a.id_coa = d.id
		WHERE 1=1
			AND a.deleted_date IS NULL
			".$where_kategori."
		GROUP BY a.kd_asset
		ORDER BY a.id
		";

		$result = $this->db->query($SQL)->result_array();
		$GET_DEPRESIASI = get_valueDepresiasi();
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$UNIQ = $row_Cek['kd_asset'].'-'.$bulan.$tahun;

				$awal_col++;
				$kd_asset	= strtoupper($row_Cek['kd_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kd_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_asset	= strtoupper($row_Cek['nm_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$tgl_perolehan	= $row_Cek['tgl_perolehan'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tgl_perolehan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= strtoupper($row_Cek['nm_category']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$KEL_PENYUSUTAN = (!empty($row_Cek['no_perkiraan']))?strtoupper($row_Cek['no_perkiraan'].' | '.$row_Cek['ket_coa']):'';
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $KEL_PENYUSUTAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$cost_center		= strtoupper($row_Cek['cost_center']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_center);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$depresiasi		= $row_Cek['depresiasi'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$SISA_NILAI 	= ($row_Cek['penyusutan'] == 'N')?$row_Cek['nilai_asset']:$row_Cek['sisa_nilai'];
				// $DEPRESIASI 	= ($SISA_NILAI > 0 AND $SISA_NILAI != $row_Cek['nilai_asset'])?$row_Cek['value'] : 0;
				if(intval($bulan) >= 4  AND intval($tahun) >= 2022 AND $row_Cek['kd_asset'] == 'ORI-22000000-000122'){
//					$SISA_NILAI = $SISA_NILAI + 29887;
				}

				$TGL_PEROLEHAN 	= date('Y-m-01',strtotime($row_Cek['tgl_perolehan']));
				$DEPRESIASI_BLN = $row_Cek['depresiasi'] * 12;
				$TGL_LAST_DEPT	= date('Ym', strtotime('+'.$DEPRESIASI_BLN.' month', strtotime($TGL_PEROLEHAN)));
				$TGL_NOW 		= $tahun.$bulan;
				$TGL_NOW_DATE 	= date('Y-m-01',strtotime($tahun.'-'.$bulan.'-01'));
				$DEPRESIASI = 0;
				if($TGL_LAST_DEPT > $TGL_NOW AND $TGL_PEROLEHAN <= $TGL_NOW_DATE){
					// $DEPRESIASI = $row_Cek['value'];
					$DEPRESIASI = (!empty($GET_DEPRESIASI[$UNIQ]))?$GET_DEPRESIASI[$UNIQ]:0;
				}

				$awal_col++;
				$nilai_asset		= $row_Cek['nilai_asset'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $DEPRESIASI);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['total_depresiasi']);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $SISA_NILAI);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('ASSETS');
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
		header('Content-Disposition: attachment;filename="data-assets.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function download_excel_all($category=null){
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

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(12);
		$sheet->setCellValue('A'.$Row, 'DATA ASSETS');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CODE');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ASSET NAME');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'TGL PEROLEHAN');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'KELOMPOK PENYUSUTAN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'COSTCENTER');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'DEPRESIASI (YEAR)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'NILAI PEROLEHAN');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$sheet->setCellValue('J'.$NewRow, 'DEPRESIASI');
		$sheet->getStyle('J'.$NewRow.':J'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('J'.$NewRow.':J'.$NextRow);
		$sheet->getColumnDimension('J')->setWidth(10);

		$sheet->setCellValue('K'.$NewRow, 'AKUMULASI DEPRESIASI');
		$sheet->getStyle('K'.$NewRow.':K'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('K'.$NewRow.':K'.$NextRow);
		$sheet->getColumnDimension('K')->setWidth(10);

		$sheet->setCellValue('L'.$NewRow, 'ASSET VALUE');
		$sheet->getStyle('L'.$NewRow.':L'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('L'.$NewRow.':L'.$NextRow);
		$sheet->getColumnDimension('L')->setWidth(10);

		$where_kategori = "";
		if($category != '0'){
			$where_kategori = " AND a.category = '".$category."' ";
		}

		$SQL = "
		SELECT
			a.id,
			a.kd_asset,
			a.nm_asset,
			a.category,
			a.penyusutan,
			c.nm_category,
			a.nilai_asset,
			a.depresiasi,
			a.`value`,
			b.sisa_nilai as sisa_nilai,
			a.department,
			a.kdcab,
			a.cost_center,
			a.tgl_perolehan,
			d.coa AS no_perkiraan,
			d.keterangan AS ket_coa
		FROM
			asset a 
			LEFT JOIN asset_nilai b ON a.kd_asset = b.kd_asset
			LEFT JOIN asset_category c ON a.category = c.id
			LEFT JOIN asset_coa d ON a.id_coa = d.id
		WHERE 1=1
			AND a.deleted_date IS NULL
			".$where_kategori."
		";

		$result = $this->db->query($SQL)->result_array();

		$tahun = date('Y');
		$bulan = date('m');
		$GET_DEPRESIASI = get_valueDepresiasi();
		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$UNIQ = $row_Cek['kd_asset'].'-'.$bulan.$tahun;

				$awal_col++;
				$kd_asset	= strtoupper($row_Cek['kd_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kd_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_asset	= strtoupper($row_Cek['nm_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$tgl_perolehan	= $row_Cek['tgl_perolehan'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tgl_perolehan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= strtoupper($row_Cek['nm_category']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$KEL_PENYUSUTAN = (!empty($row_Cek['no_perkiraan']))?strtoupper($row_Cek['no_perkiraan'].' | '.$row_Cek['ket_coa']):'';
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $KEL_PENYUSUTAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$cost_center		= strtoupper($row_Cek['cost_center']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_center);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$depresiasi		= $row_Cek['depresiasi'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$SISA_NILAI 	= ($row_Cek['penyusutan'] == 'N')?$row_Cek['nilai_asset']:$row_Cek['sisa_nilai'];
				// $DEPRESIASI 	= ($SISA_NILAI > 0 AND $SISA_NILAI != $row_Cek['nilai_asset'])?$row_Cek['value'] : 0;

				$TGL_PEROLEHAN 	= date('Y-m-01',strtotime($row_Cek['tgl_perolehan']));
				$DEPRESIASI_BLN = $row_Cek['depresiasi'] * 12;
				$TGL_LAST_DEPT	= date('Ym', strtotime('+'.$DEPRESIASI_BLN.' month', strtotime($TGL_PEROLEHAN)));
				$TGL_NOW 		= $tahun.$bulan;
				$TGL_NOW_DATE 	= date('Y-m-01',strtotime($tahun.'-'.$bulan.'-01'));
				$DEPRESIASI = 0;
				if($TGL_LAST_DEPT > $TGL_NOW AND $TGL_PEROLEHAN <= $TGL_NOW_DATE){
					// $DEPRESIASI = $row_Cek['value'];
					$DEPRESIASI = (!empty($GET_DEPRESIASI[$UNIQ]))?$GET_DEPRESIASI[$UNIQ]:0;
				}

				$awal_col++;
				$nilai_asset		= $row_Cek['nilai_asset'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $DEPRESIASI);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $row_Cek['nilai_asset'] - $SISA_NILAI);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $SISA_NILAI);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('ASSETS');
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
		header('Content-Disposition: attachment;filename="data-assets-depresiasi-all.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function download_excel_all_default($category=null){
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

		$Row		= 1;
		$NewRow		= $Row+1;
		$Col_Akhir	= $Cols	= getColsChar(9);
		$sheet->setCellValue('A'.$Row, 'DATA ASSETS');
		$sheet->getStyle('A'.$Row.':'.$Col_Akhir.$NewRow)->applyFromArray($style_header2);
		$sheet->mergeCells('A'.$Row.':'.$Col_Akhir.$NewRow);

		$NewRow	= $NewRow +2;
		$NextRow= $NewRow;

		$sheet->setCellValue('A'.$NewRow, '#');
		$sheet->getStyle('A'.$NewRow.':A'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('A'.$NewRow.':A'.$NextRow);
		$sheet->getColumnDimension('A')->setWidth(10);

		$sheet->setCellValue('B'.$NewRow, 'CODE');
		$sheet->getStyle('B'.$NewRow.':B'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('B'.$NewRow.':B'.$NextRow);
		$sheet->getColumnDimension('B')->setWidth(20);

		$sheet->setCellValue('C'.$NewRow, 'ASSET NAME');
		$sheet->getStyle('C'.$NewRow.':C'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('C'.$NewRow.':C'.$NextRow);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->setCellValue('D'.$NewRow, 'TGL PEROLEHAN');
		$sheet->getStyle('D'.$NewRow.':D'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('D'.$NewRow.':D'.$NextRow);
		$sheet->getColumnDimension('D')->setAutoSize(true);

		$sheet->setCellValue('E'.$NewRow, 'CATEGORY');
		$sheet->getStyle('E'.$NewRow.':E'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('E'.$NewRow.':E'.$NextRow);
		$sheet->getColumnDimension('E')->setWidth(10);

		$sheet->setCellValue('F'.$NewRow, 'KELOMPOK PENYUSUTAN');
		$sheet->getStyle('F'.$NewRow.':F'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('F'.$NewRow.':F'.$NextRow);
		$sheet->getColumnDimension('F')->setWidth(10);

		$sheet->setCellValue('G'.$NewRow, 'COSTCENTER');
		$sheet->getStyle('G'.$NewRow.':G'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('G'.$NewRow.':G'.$NextRow);
		$sheet->getColumnDimension('G')->setWidth(10);

		$sheet->setCellValue('H'.$NewRow, 'DEPRESIASI (YEAR)');
		$sheet->getStyle('H'.$NewRow.':H'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('H'.$NewRow.':H'.$NextRow);
		$sheet->getColumnDimension('H')->setWidth(10);

		$sheet->setCellValue('I'.$NewRow, 'NILAI PEROLEHAN');
		$sheet->getStyle('I'.$NewRow.':I'.$NextRow)->applyFromArray($style_header);
		$sheet->mergeCells('I'.$NewRow.':I'.$NextRow);
		$sheet->getColumnDimension('I')->setWidth(10);

		$where_kategori = "";
		if($category != '0'){
			$where_kategori = " AND a.category = '".$category."' ";
		}

		$SQL = "
		SELECT
			a.id,
			a.kd_asset,
			a.nm_asset,
			a.category,
			a.penyusutan,
			c.nm_category,
			a.nilai_asset,
			a.depresiasi,
			a.`value`,
			a.department,
			a.kdcab,
			a.cost_center,
			a.tgl_perolehan,
			d.coa AS no_perkiraan,
			d.keterangan AS ket_coa
		FROM
			asset a
			LEFT JOIN asset_category c ON a.category = c.id
			LEFT JOIN asset_coa d ON a.id_coa = d.id
		WHERE 1=1
			AND a.deleted_date IS NULL
			".$where_kategori."
			ORDER BY a.id
		";

		$result = $this->db->query($SQL)->result_array();

		if($result){
			$awal_row	= $NextRow;
			$no=0;
			foreach($result as $key => $row_Cek){
				$no++;
				$awal_row++;
				$awal_col	= 0;

				$awal_col++;
				$detail_name	= $no;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $detail_name);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				

				$awal_col++;
				$kd_asset	= strtoupper($row_Cek['kd_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $kd_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_asset	= strtoupper($row_Cek['nm_asset']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$tgl_perolehan	= $row_Cek['tgl_perolehan'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $tgl_perolehan);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$nm_category	= strtoupper($row_Cek['nm_category']);
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nm_category);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$KEL_PENYUSUTAN = (!empty($row_Cek['no_perkiraan']))?strtoupper($row_Cek['no_perkiraan'].' | '.$row_Cek['ket_coa']):'';
				$awal_col++;
				$Cols			= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $KEL_PENYUSUTAN);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$cost_center		= strtoupper($row_Cek['cost_center']);
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $cost_center);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray3);

				$awal_col++;
				$depresiasi		= $row_Cek['depresiasi'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $depresiasi);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);

				$awal_col++;
				$nilai_asset		= $row_Cek['nilai_asset'];
				$Cols		= getColsChar($awal_col);
				$sheet->setCellValue($Cols.$awal_row, $nilai_asset);
				$sheet->getStyle($Cols.$awal_row)->applyFromArray($styleArray4);
			}
		}


		$sheet->setTitle('ASSETS');
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
		header('Content-Disposition: attachment;filename="data-assets-all.xls"');
		//unduh file
		$objWriter->save("php://output");
	}

	public function saved_jurnal_erp(){
		$data_session	= $this->session->userdata;
		$username = $data_session['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$get_jurnal = $this->db->get_where('asset_jurnal_temp',array('created_by'=>$username,'kredit'=>0))->result_array();
		$ArrJurnal = [];
		foreach ($get_jurnal as $key => $value) {
			$ArrJurnal[$key]['category'] 		= 'assets';
			$ArrJurnal[$key]['tanggal'] 		= $value['tanggal'];
			$ArrJurnal[$key]['id_detail'] 		= $value['id_category'];
			$ArrJurnal[$key]['product'] 		= $value['category'];
			$ArrJurnal[$key]['id_material'] 	= $value['no_perkiraan'];
			$ArrJurnal[$key]['nm_material'] 	= $value['keterangan'];
			$ArrJurnal[$key]['total_nilai'] 	= $value['debet'];
			$ArrJurnal[$key]['created_by'] 		= $username;
			$ArrJurnal[$key]['created_date'] 	= $datetime;
		}
		

		// echo "<pre>";
		// print_r($ArrDebit);
		// print_r($ArrKredit);
		// print_r($ArrJurnal);
		// exit;
		$ArrDelete = [
			'tanggal' => $value['tanggal'],
			'category' => 'assets'
		];

		$this->db->trans_start();
			$this->db->delete('jurnal',$ArrDelete);
			$this->db->insert_batch('jurnal',$ArrJurnal);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Asset gagal disimpan ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Asset berhasil disimpan. Terimakasih ...',
				'status'	=> 1
			);
			history('Insert jurnal erp '.$value['tanggal']);
		}

		echo json_encode($Arr_Data);
	}
	

	//======================================================================================================================
    //===================================================PR ASSET============================================================
    //======================================================================================================================
	public function depreciation(){ 
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		// $SQL_LAST = $this->db->select('MAX(MONTH(bulan)) AS bulan, MAX(YEAR(tahun)) AS tahun')->get_where('asset_generatex',array('flag'=>'N'))->result();
		
		$data = array(
			'title'			=> 'Indeks Of Depreciation Assets',
			'action'		=> 'asset',
			'akses_menu'	=> $Arr_Akses,
			'bulan_'		=> date('m'),
			'kategori' 		=> $this->asset_model->getList('asset_category')
		);
        history("View index asset depreciation");
        $this->load->view('Asset/depreciation', $data);
    }

	public function data_side_depreciation(){
		$this->asset_model->data_side_depreciation();
	}

	//======================================================================================================================
    //===================================================ASSET COA============================================================
    //======================================================================================================================

	public function asset_coa(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Indeks Of Asset COA',
			'action'		=> 'category',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Asset COA');
		$this->load->view('Asset/asset_coa',$data);
	}

	public function data_side_asset_coa(){
		$this->asset_model->get_json_asset_coa();
	}

	public function add_asset_coa(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');

			//header
			$id 		    = $data['id'];
			$keterangan		= strtoupper($data['keterangan']);
			$coa			= $data['coa'];
			$coa_kredit		= $data['coa_kredit'];
			$status			= $data['status'];

			if(empty($id)){
                $ArrHeader = array(
                    'keterangan'=> $keterangan,
                    'coa' 		=> $coa,
                    'coa_kredit'=> $coa_kredit,
					'status'	=> $status,
                );
                $TandaI = "Insert";
			}

			if(!empty($id)){
                $ArrHeader = array(
                    'keterangan'   => $keterangan,
                    'coa' 		=> $coa,
                    'coa_kredit'=> $coa_kredit,
					'status'	=> $status,
                );
                $TandaI = "Update";
            }
            
            $this->db->trans_start();
                if(empty($id)){
                    $this->db->insert('asset_coa', $ArrHeader);
                }
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('asset_coa', $ArrHeader);
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
				history($TandaI.' Asset COA '.$id.' / '.$keterangan);
			}

			echo json_encode($Arr_Kembali);
		}
		else{
			$this->load->model('All_model');
			$controller			= ucfirst(strtolower($this->uri->segment(1)));
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }
            
            $id = $this->uri->segment(3);
            $query = "SELECT * FROM asset_coa WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$data_coa = $this->All_model->GetCoaCombo();
			$data = array(
				'title'		=> 'Add Asset COA',
                'action'	=> 'add',
                'data'      => $result,
                'coalist'      => $data_coa
			);
			$this->load->view('Asset/add_asset_coa',$data);
		}
	}

	public function hapus_asset_coa(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;

		$ArrPlant		= array(
			'status' 		=> 'N',
			);


		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->update('asset_coa', $ArrPlant);
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
			history('Delete Asset COA : '.$id);
		}
		echo json_encode($Arr_Data);
	}

	// ==================== UPLOAD ASSET ====================

	public function upload(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['create'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data = array(
			'title'			=> 'Upload Asset (Bulk Import)',
			'action'		=> 'upload',
			'akses_menu'	=> $Arr_Akses
		);
		$this->load->view('Asset/upload', $data);
	}

	public function download_template_upload(){
		set_time_limit(0);
		ini_set('memory_limit','512M');
		$this->load->library("PHPExcel");
		$objPHPExcel = new PHPExcel();

		$style_header = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb'=>'000000')
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'4472C4'),
			),
			'font' => array(
				'bold' => true,
				'color' => array('rgb'=>'FFFFFF'),
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			)
		);

		$style_note = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'FFF2CC'),
			),
			'font' => array(
				'italic' => true,
				'size' => 9,
			)
		);

		// ============ GET DATA MASTER ============
		$categories = $this->db->query("SELECT id, nm_category FROM asset_category WHERE status='Y' ORDER BY id")->result_array();
		$cat_pajak = $this->db->query("SELECT id, nm_category FROM asset_category_pajak ORDER BY id")->result_array();
		$coa_list = $this->db->query("SELECT id, coa, keterangan FROM asset_coa WHERE status='Y' ORDER BY id")->result_array();
		$branches = $this->db->query("SELECT * FROM asset_branch ORDER BY id_branch")->result_array();
		$depts = $this->db->query("SELECT id, nm_dept FROM department WHERE deleted='N' ORDER BY id")->result_array();
		$costcenters = $this->db->query("SELECT id_costcenter, nm_costcenter FROM costcenter ORDER BY id_costcenter")->result_array();

		// ============ SHEET 1 - DATA ASSET (INPUT) ============
		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->setTitle('Data Asset');

		$headers = array(
			'A'=>'NAMA ASSET *',
			'B'=>'TGL PEROLEHAN * (YYYY-MM-DD)',
			'C'=>'CATEGORY *',
			'D'=>'CATEGORY PAJAK',
			'E'=>'KELOMPOK PENYUSUTAN (COA)',
			'F'=>'NILAI ASSET *',
			'G'=>'DEPRESIASI (TAHUN)',
			'H'=>'BRANCH *',
			'I'=>'CODE ORI',
			'J'=>'NAMA USER',
			'K'=>'LOKASI',
			'L'=>'STATUS ASSET',
			'M'=>'TGL MULAI DEPRESIASI (YYYY-MM-DD)',
			'N'=>'DEPARTMENT',
			'O'=>'COST CENTER',
			'P'=>'PENYUSUTAN *'
		);

		foreach($headers as $col => $val){
			$sheet->setCellValue($col.'1', $val);
			$sheet->getStyle($col.'1')->applyFromArray($style_header);
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// Baris 2 = petunjuk isi
		$sheet->setCellValue('A2', '(isi nama asset)');
		$sheet->setCellValue('B2', '(format: 2024-01-15)');
		$sheet->setCellValue('C2', '(pilih dari dropdown)');
		$sheet->setCellValue('D2', '(pilih dari dropdown)');
		$sheet->setCellValue('E2', '(pilih dari dropdown)');
		$sheet->setCellValue('F2', '(angka, tanpa titik/koma)');
		$sheet->setCellValue('G2', '(angka tahun, misal: 4)');
		$sheet->setCellValue('H2', '(pilih dari dropdown)');
		$sheet->setCellValue('I2', '(kode lama, opsional)');
		$sheet->setCellValue('J2', '(nama pengguna)');
		$sheet->setCellValue('K2', '(pilih dari dropdown)');
		$sheet->setCellValue('L2', '(pilih dari dropdown)');
		$sheet->setCellValue('M2', '(format: 2024-01-15)');
		$sheet->setCellValue('N2', '(pilih dari dropdown)');
		$sheet->setCellValue('O2', '(pilih dari dropdown)');
		$sheet->setCellValue('P2', '(pilih dari dropdown)');
		$sheet->getStyle('A2:P2')->applyFromArray($style_note);

		// ============ SHEET 2 - LIST (HIDDEN, untuk dropdown source) ============
		$objPHPExcel->createSheet();
		$sheet2 = $objPHPExcel->getSheet(1);
		$sheet2->setTitle('List');

		// Kolom A: Category => "ID - NAMA"
		$sheet2->setCellValue('A1', 'CATEGORY');
		$sheet2->getStyle('A1')->applyFromArray($style_header);
		$rowCat = 2;
		foreach($categories as $cat){
			$sheet2->setCellValue('A'.$rowCat, $cat['id'].' - '.strtoupper($cat['nm_category']));
			$rowCat++;
		}

		// Kolom B: Category Pajak => "ID - NAMA"
		$sheet2->setCellValue('B1', 'CATEGORY PAJAK');
		$sheet2->getStyle('B1')->applyFromArray($style_header);
		$rowPjk = 2;
		foreach($cat_pajak as $cp){
			$sheet2->setCellValue('B'.$rowPjk, $cp['id'].' - '.strtoupper($cp['nm_category']));
			$rowPjk++;
		}

		// Kolom C: COA => "ID - COA | KETERANGAN"
		$sheet2->setCellValue('C1', 'KELOMPOK PENYUSUTAN');
		$sheet2->getStyle('C1')->applyFromArray($style_header);
		$rowCoa = 2;
		foreach($coa_list as $coa){
			$sheet2->setCellValue('C'.$rowCoa, $coa['id'].' - '.$coa['coa'].' | '.strtoupper($coa['keterangan']));
			$rowCoa++;
		}

		// Kolom D: Branch => "ID_BRANCH - NAMA"
		$sheet2->setCellValue('D1', 'BRANCH');
		$sheet2->getStyle('D1')->applyFromArray($style_header);
		$rowBr = 2;
		foreach($branches as $br){
			$nama_br = (!empty($br['nm_alias']))?$br['nm_alias']:'';
			$sheet2->setCellValue('D'.$rowBr, $br['id_branch'].' - '.strtoupper($nama_br));
			$rowBr++;
		}

		// Kolom E: Lokasi (fixed list)
		$sheet2->setCellValue('E1', 'LOKASI');
		$sheet2->getStyle('E1')->applyFromArray($style_header);
		$arr_lokasi = array('OPC 1','OPC 2','OPC 3','Office','Site');
		$rowLok = 2;
		foreach($arr_lokasi as $lok){
			$sheet2->setCellValue('E'.$rowLok, $lok);
			$rowLok++;
		}

		// Kolom F: Status Asset (fixed list)
		$sheet2->setCellValue('F1', 'STATUS ASSET');
		$sheet2->getStyle('F1')->applyFromArray($style_header);
		$arr_status = array('Digunakan','Tidak digunakan','Terjual');
		$rowSts = 2;
		foreach($arr_status as $sts){
			$sheet2->setCellValue('F'.$rowSts, $sts);
			$rowSts++;
		}

		// Kolom G: Department => "ID - NAMA"
		$sheet2->setCellValue('G1', 'DEPARTMENT');
		$sheet2->getStyle('G1')->applyFromArray($style_header);
		$rowDept = 2;
		foreach($depts as $d){
			$sheet2->setCellValue('G'.$rowDept, $d['id'].' - '.strtoupper($d['nm_dept']));
			$rowDept++;
		}

		// Kolom H: Costcenter => "ID - NAMA"
		$sheet2->setCellValue('H1', 'COSTCENTER');
		$sheet2->getStyle('H1')->applyFromArray($style_header);
		$rowCc = 2;
		foreach($costcenters as $cc){
			$sheet2->setCellValue('H'.$rowCc, $cc['id_costcenter'].' - '.strtoupper($cc['nm_costcenter']));
			$rowCc++;
		}

		// Kolom I: Penyusutan (Yes/No)
		$sheet2->setCellValue('I1', 'PENYUSUTAN');
		$sheet2->getStyle('I1')->applyFromArray($style_header);
		$sheet2->setCellValue('I2', 'Yes');
		$sheet2->setCellValue('I3', 'No');
		$rowPny = 4; // last row + 1

		$sheet2->getColumnDimension('A')->setAutoSize(true);
		$sheet2->getColumnDimension('B')->setAutoSize(true);
		$sheet2->getColumnDimension('C')->setAutoSize(true);
		$sheet2->getColumnDimension('D')->setAutoSize(true);
		$sheet2->getColumnDimension('E')->setAutoSize(true);
		$sheet2->getColumnDimension('F')->setAutoSize(true);
		$sheet2->getColumnDimension('G')->setAutoSize(true);
		$sheet2->getColumnDimension('H')->setAutoSize(true);
		$sheet2->getColumnDimension('I')->setAutoSize(true);

		// ============ SET DATA VALIDATION (DROPDOWN) di Sheet 1 ============
		$maxRow = 1000; // Dropdown berlaku sampai baris 1000

		// C: Category dropdown
		$lastCat = $rowCat - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('C'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$A$2:$A$'.$lastCat);
		}

		// D: Category Pajak dropdown
		$lastPjk = $rowPjk - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('D'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$B$2:$B$'.$lastPjk);
		}

		// E: COA dropdown
		$lastCoa = $rowCoa - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('E'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$C$2:$C$'.$lastCoa);
		}

		// H: Branch dropdown
		$lastBr = $rowBr - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('H'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$D$2:$D$'.$lastBr);
		}

		// K: Lokasi dropdown
		$lastLok = $rowLok - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('K'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$E$2:$E$'.$lastLok);
		}

		// L: Status Asset dropdown
		$lastSts = $rowSts - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('L'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$F$2:$F$'.$lastSts);
		}

		// N: Department dropdown
		$lastDept = $rowDept - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('N'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$G$2:$G$'.$lastDept);
		}

		// O: Costcenter dropdown
		$lastCc = $rowCc - 1;
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('O'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$H$2:$H$'.$lastCc);
		}

		// P: Penyusutan dropdown (Yes/No)
		for($r = 3; $r <= $maxRow; $r++){
			$objValidation = $sheet->getCell('P'.$r)->getDataValidation();
			$objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
			$objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
			$objValidation->setAllowBlank(true);
			$objValidation->setShowDropDown(true);
			$objValidation->setFormula1('List!$I$2:$I$3');
		}

		// ============ SHEET 3 - REFERENCE (readable) ============
		$objPHPExcel->createSheet();
		$sheet3 = $objPHPExcel->getSheet(2);
		$sheet3->setTitle('Reference');

		$sheet3->setCellValue('A1', 'KETERANGAN:');
		$sheet3->setCellValue('A2', 'Sheet "Data Asset" sudah memiliki dropdown di kolom-kolom yang memerlukan ID.');
		$sheet3->setCellValue('A3', 'Kolom dengan tanda * adalah WAJIB diisi.');
		$sheet3->setCellValue('A4', 'Data mulai diisi dari BARIS 3 (baris 2 adalah petunjuk).');
		$sheet3->setCellValue('A5', 'Format dropdown: "ID - NAMA". Sistem akan otomatis mengambil ID-nya.');
		$sheet3->setCellValue('A6', '');
		$sheet3->setCellValue('A7', 'DAFTAR KOLOM:');
		$sheet3->setCellValue('A8', 'A = Nama Asset (ketik manual)');
		$sheet3->setCellValue('A9', 'B = Tanggal Perolehan (format YYYY-MM-DD, misal 2024-01-15)');
		$sheet3->setCellValue('A10', 'C = Category (pilih dari dropdown)');
		$sheet3->setCellValue('A11', 'D = Category Pajak (pilih dari dropdown)');
		$sheet3->setCellValue('A12', 'E = Kelompok Penyusutan/COA (pilih dari dropdown)');
		$sheet3->setCellValue('A13', 'F = Nilai Asset (angka, tanpa titik/koma, misal 10000000)');
		$sheet3->setCellValue('A14', 'G = Depresiasi dalam tahun (angka, misal 4)');
		$sheet3->setCellValue('A15', 'H = Branch (pilih dari dropdown)');
		$sheet3->setCellValue('A16', 'I = Code ORI / kode asset lama (ketik manual, opsional)');
		$sheet3->setCellValue('A17', 'J = Nama User / pengguna asset (ketik manual)');
		$sheet3->setCellValue('A18', 'K = Lokasi (pilih dari dropdown)');
		$sheet3->setCellValue('A19', 'L = Status Asset (pilih dari dropdown)');
		$sheet3->setCellValue('A20', 'M = Tanggal Mulai Depresiasi (format YYYY-MM-DD, opsional. Jika kosong = sama dengan tgl perolehan)');
		$sheet3->setCellValue('A21', 'N = Department (pilih dari dropdown)');
		$sheet3->setCellValue('A22', 'O = Cost Center (pilih dari dropdown)');
		$sheet3->setCellValue('A23', 'P = Penyusutan (pilih dari dropdown: Yes / No)');
		$sheet3->setCellValue('A24', '');
		$sheet3->setCellValue('A25', 'CATATAN:');
		$sheet3->setCellValue('A26', '- Jika Tax Category (D) diisi, Depresiasi (G) bisa dikosongkan (otomatis diambil dari jangka waktu tax category).');
		$sheet3->setCellValue('A27', '- Jika Penyusutan = No, jadwal depresiasi tidak akan di-generate.');

		$sheet3->getColumnDimension('A')->setWidth(80);
		$sheet3->getStyle('A1')->getFont()->setBold(true);
		$sheet3->getStyle('A7')->getFont()->setBold(true);

		$objPHPExcel->setActiveSheetIndex(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		ob_end_clean();
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="template-upload-asset.xlsx"');
		$objWriter->save("php://output");
	}

	public function proses_upload(){
		$Arr_Kembali = array();

		if(empty($_FILES['excel_file']['name'])){
			echo json_encode(array('status'=>3,'pesan'=>'File tidak ditemukan.'));
			return;
		}

		$tmp = explode(".", $_FILES['excel_file']['name']);
		$exts = strtolower(end($tmp));
		if(!in_array($exts, array('xls','xlsx'))){
			echo json_encode(array('status'=>3,'pesan'=>'Format file harus .xls atau .xlsx'));
			return;
		}

		$this->load->library(array('PHPExcel'));
		$config['upload_path'] = './assets/file/';
		$config['file_name'] = 'upload_asset_'.date('YmdHis');
		$config['allowed_types'] = 'xls|xlsx';
		$config['max_size'] = 10000;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if(!$this->upload->do_upload('excel_file')){
			$error = $this->upload->display_errors();
			echo json_encode(array('status'=>3,'pesan'=>$error));
			return;
		}

		$media = $this->upload->data();
		$inputFileName = './assets/file/'.$media['file_name'];

		try{
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($inputFileName);
		}catch(Exception $e){
			echo json_encode(array('status'=>3,'pesan'=>'Error loading file: '.$e->getMessage()));
			return;
		}

		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();

		if($highestRow < 3){
			echo json_encode(array('status'=>3,'pesan'=>'File kosong atau tidak ada data. Data dimulai dari baris 3.'));
			return;
		}

		$db2 = $this->load->database('instalasi', TRUE);
		$data_session = $this->session->userdata;
		$errors = array();
		$detailData = array();
		$detailDataDash = array();
		$ArrInstalasi = array();
		$ArrPrice = array();
		$ArrCategoryCheck = array();

		$region = $db2->query("SELECT * FROM region ORDER BY urut ASC")->result_array();

		// Get current max code_group
		$q_group = "SELECT max(code_group) as maxP FROM asset WHERE code_group LIKE 'AS%' ";
		$rest_group = $this->db->query($q_group)->result_array();
		$angka_group = $rest_group[0]['maxP'];
		$urut_g = (int)substr($angka_group, 2, 5);

		$success_count = 0;

		// Data dimulai dari baris 3 (baris 1 = header, baris 2 = petunjuk)
		for($i = 3; $i <= $highestRow; $i++){
			$nm_asset 		= trim($sheet->getCell('A'.$i)->getValue());
			$tgl_perolehan 	= trim($sheet->getCell('B'.$i)->getValue());
			$raw_category 	= trim($sheet->getCell('C'.$i)->getValue());
			$raw_cat_pajak 	= trim($sheet->getCell('D'.$i)->getValue());
			$raw_coa 		= trim($sheet->getCell('E'.$i)->getValue());
			$nilai_asset 	= trim($sheet->getCell('F'.$i)->getValue());
			$raw_depresiasi = trim($sheet->getCell('G'.$i)->getValue());
			$raw_branch 	= trim($sheet->getCell('H'.$i)->getValue());
			$code_ori 		= trim($sheet->getCell('I'.$i)->getValue());
			$nama_user 		= trim($sheet->getCell('J'.$i)->getValue());
			$lokasi 		= trim($sheet->getCell('K'.$i)->getValue());
			$status_asset 	= trim($sheet->getCell('L'.$i)->getValue());
			$tgl_depresiasi = trim($sheet->getCell('M'.$i)->getValue());
			$raw_dept 		= trim($sheet->getCell('N'.$i)->getValue());
			$raw_costcenter = trim($sheet->getCell('O'.$i)->getValue());
			$raw_penyusutan = trim($sheet->getCell('P'.$i)->getValue());

			// Skip empty row
			if(empty($nm_asset)) continue;

			// ====== PARSE ID dari format dropdown "ID - NAMA" ======
			$category 		= $this->_parse_id($raw_category);
			$category_pajak = $this->_parse_id($raw_cat_pajak);
			$id_coa 		= $this->_parse_id($raw_coa);
			$branch 		= $this->_parse_id($raw_branch);
			$id_dept 		= $this->_parse_id($raw_dept);
			$id_costcenter 	= $this->_parse_id($raw_costcenter);

			// Depresiasi: bisa langsung angka atau otomatis dari tax category
			$depresiasi = $raw_depresiasi;
			if(empty($depresiasi) && !empty($category_pajak)){
				$q_jw = $this->db->query("SELECT jangka_waktu FROM asset_category_pajak WHERE id='".$category_pajak."'")->result_array();
				$depresiasi = (!empty($q_jw[0]['jangka_waktu']))?$q_jw[0]['jangka_waktu']:0;
			}

			// Penyusutan (Yes/No)
			$penyusutan = 'Y';
			if(!empty($raw_penyusutan)){
				$penyusutan = (strtolower($raw_penyusutan) == 'no' || strtolower($raw_penyusutan) == 'n') ? 'N' : 'Y';
			}

			// Validasi wajib
			if(empty($tgl_perolehan)){
				$errors[] = "Baris $i: Tgl Perolehan kosong.";
				continue;
			}
			if(empty($category)){
				$errors[] = "Baris $i: Category kosong.";
				continue;
			}
			if(empty($nilai_asset)){
				$errors[] = "Baris $i: Nilai Asset kosong.";
				continue;
			}
			if(empty($depresiasi)){
				$errors[] = "Baris $i: Depresiasi kosong (isi manual atau pilih Tax Category).";
				continue;
			}
			if(empty($branch)){
				$errors[] = "Baris $i: Branch kosong.";
				continue;
			}

			// Format tanggal
			if(is_numeric($tgl_perolehan)){
				$tgl_perolehan = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tgl_perolehan));
			}
			if(is_numeric($tgl_depresiasi)){
				$tgl_depresiasi = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tgl_depresiasi));
			}
			if(empty($tgl_depresiasi)){
				$tgl_depresiasi = $tgl_perolehan;
			}

			// Generate kd_asset
			$nmCategory = $this->asset_model->getWhere('asset_category', 'id', $category);
			$nm_category_text = (!empty($nmCategory[0]['nm_category']))?$nmCategory[0]['nm_category']:'';

			$KdCategory = sprintf('%02s', $category);
			$KdCategoryPjk = sprintf('%02s', $category_pajak);
			$Year_perolehan = date('y', strtotime($tgl_perolehan));
			$Month_perolehan = date('m', strtotime($tgl_perolehan));
			$Ym_perolehan = $Year_perolehan.$Month_perolehan;

			$qQuery = "SELECT max(kd_asset) as maxP FROM asset WHERE kd_asset LIKE '".$branch."-".$Ym_perolehan.$KdCategory.$KdCategoryPjk."-%' ";
			$restQuery = $this->db->query($qQuery)->result_array();
			$angkaUrut2 = $restQuery[0]['maxP'];
			$urutan2 = (int)substr($angkaUrut2, 13, 3);
			$urutan2++;
			$urut2 = sprintf('%03s', $urutan2);
			$kode_assets = $branch."-".$Ym_perolehan.$KdCategory.$KdCategoryPjk."-".$urut2;

			// Generate code_group
			$urut_g++;
			$kode_group = "AS".sprintf('%05s', $urut_g);

			// Hitung value penyusutan per bulan
			$value = 0;
			if($depresiasi > 0){
				$value = $nilai_asset / ($depresiasi * 12);
			}

			// Data asset
			$detailData[] = array(
				'kd_asset' 		=> $kode_assets.'001',
				'code_group' 	=> $kode_group,
				'nm_asset' 		=> $nm_asset,
				'tgl_perolehan' => $tgl_perolehan,
				'id_coa' 		=> $id_coa,
				'category' 		=> $category,
				'category_pajak'=> $category_pajak,
				'nm_category' 	=> strtoupper($nm_category_text),
				'nilai_asset' 	=> $nilai_asset,
				'qty' 			=> 1,
				'asset_ke' 		=> 1,
				'depresiasi' 	=> $depresiasi,
				'value' 		=> $value,
				'kdcab' 		=> $branch,
				'penyusutan' 	=> $penyusutan,
				'id_dept' 		=> $id_dept,
				'department' 	=> get_name('department', 'nm_dept', 'id', $id_dept),
				'id_costcenter' => $id_costcenter,
				'nama_user' 	=> $nama_user,
				'cost_center' 	=> get_name('costcenter', 'nm_costcenter', 'id_costcenter', $id_costcenter),
				'code_ori' 		=> $code_ori,
				'lokasi' 		=> $lokasi,
				'status_asset' 	=> $status_asset,
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s'),
				'tgl_depresiasi'=> $tgl_depresiasi
			);

			// Data asset_generate (jadwal depresiasi) - hanya jika penyusutan = Y
			if($penyusutan == 'Y'){
				$jmlx = $depresiasi * 12;
				$date_now = $tgl_depresiasi;
				$date_now_real = date('Y-m-d');

				for($x = 1; $x <= $jmlx; $x++){
					$Tanggal = date('Y-m', mktime(0,0,0, substr($date_now,5,2)+$x, 0, substr($date_now,0,4)));
					$TglNow = date('Y-m', strtotime($date_now_real));
					$flagx = 'N';
					if($Tanggal < $TglNow){
						$flagx = 'Y';
					}

					$detailDataDash[] = array(
						'kd_asset' 		=> $kode_assets.'001',
						'nm_asset' 		=> $nm_asset,
						'category' 		=> $category,
						'category_pajak'=> $category_pajak,
						'nm_category' 	=> strtoupper($nm_category_text),
						'bulan' 		=> substr($Tanggal, 5, 2),
						'tahun' 		=> substr($Tanggal, 0, 4),
						'lokasi_asset' 	=> $id_dept,
						'cost_center' 	=> $id_costcenter,
						'nilai_susut' 	=> $value,
						'kdcab' 		=> $branch,
						'flag' 			=> $flagx
					);
				}
			}

			// Data vehicle_tool_new (instalasi)
			$ArrInstalasi[] = array(
				'code_group' 	=> $kode_group,
				'category' 		=> 'asset '.strtolower($nm_category_text),
				'spec' 			=> strtolower($nm_asset),
				'created_by' 	=> $data_session['ORI_User']['username'],
				'created_date' 	=> date('Y-m-d H:i:s')
			);

			// Data price_ref (instalasi)
			foreach($region as $reg){
				$ArrPrice[] = array(
					'category' 			=> 'vehicle tool',
					'code_group' 		=> $kode_group,
					'unit_material' 	=> 'month',
					'kurs' 				=> 'IDR',
					'region' 			=> $reg['region'],
					'rate' 				=> $nilai_asset,
					'updated_by' 		=> $data_session['ORI_User']['username'],
					'updated_date' 		=> date('Y-m-d H:i:s')
				);
			}

			// Check category untuk vehicle_tool_category
			$cat_key = 'asset '.strtolower($nm_category_text);
			if(!in_array($cat_key, $ArrCategoryCheck)){
				$ArrCategoryCheck[] = $cat_key;
			}

			$success_count++;
		}

		if(empty($detailData)){
			echo json_encode(array('status'=>3,'pesan'=>'Tidak ada data valid untuk diimport.','errors'=>$errors));
			return;
		}

		// Insert ke database
		$this->db->trans_start();
			// 1. Insert asset
			$this->db->insert_batch('asset', $detailData);

			// 2. Insert asset_generate
			if(!empty($detailDataDash)){
				// Batch insert per 500 rows to avoid memory issue
				$chunks = array_chunk($detailDataDash, 500);
				foreach($chunks as $chunk){
					$this->db->insert_batch('asset_generate', $chunk);
				}
			}

			// 3. Insert vehicle_tool_new (instalasi)
			if(!empty($ArrInstalasi)){
				$db2->insert_batch('vehicle_tool_new', $ArrInstalasi);
			}

			// 4. Insert price_ref (instalasi)
			if(!empty($ArrPrice)){
				$chunks_price = array_chunk($ArrPrice, 500);
				foreach($chunks_price as $chunk){
					$db2->insert_batch('price_ref', $chunk);
				}
			}

			// 5. Insert vehicle_tool_category (jika belum ada)
			foreach($ArrCategoryCheck as $cat_name){
				$num_cty = $db2->query("SELECT * FROM vehicle_tool_category WHERE category='".$cat_name."'")->num_rows();
				if($num_cty < 1){
					$db2->insert('vehicle_tool_category', array(
						'category' 		=> $cat_name,
						'created_by' 	=> 'asset',
						'created_date' 	=> date('Y-m-d H:i:s')
					));
				}
			}
		$this->db->trans_complete();

		// Hapus file upload
		if(file_exists($inputFileName)){
			unlink($inputFileName);
		}

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali = array(
				'pesan'		=> 'Upload gagal. Silakan coba lagi.',
				'status'	=> 0,
				'errors'	=> $errors
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali = array(
				'pesan'		=> 'Upload berhasil! '.$success_count.' asset berhasil diimport.',
				'status'	=> 1,
				'errors'	=> $errors
			);
			history('Upload bulk asset: '.$success_count.' items');
		}

		echo json_encode($Arr_Kembali);
	}

	/**
	 * Helper: Parse ID dari format dropdown "ID - NAMA"
	 * Contoh: "1 - BANGUNAN I" => return "1"
	 * Contoh: "ORI - HEAD OFFICE" => return "ORI"
	 * Jika tidak ada " - ", return value apa adanya (plain ID)
	 */
	private function _parse_id($value){
		if(empty($value)) return '';
		if(strpos($value, ' - ') !== false){
			$parts = explode(' - ', $value, 2);
			return trim($parts[0]);
		}
		return trim($value);
	}

}
?>
