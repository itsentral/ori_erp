<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produksi extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');

		$this->tanki = $this->load->database("tanki",TRUE);
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
		
		$this->get_user = get_detail_user();
	}

    public function index_loose($tanda=null){
		if(!empty($tanda)){
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/index_loose/'.$tanda;
			$judul = ($tanda == 'aktual')?'Warehouse Material >> Aktual Mixing':'Warehouse Material >> Request Mixing';
		}
		else{
			$controller			= ucfirst(strtolower($this->uri->segment(1))).'/index_loose';
			$judul = 'Warehouse Material >> Request SPK';
		}

		$Arr_Akses			= getAcccesmenu($controller);
		
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
        $data_list1 = $this->db->query(" SELECT
                                            a.id_produksi
                                        FROM
                                            production_detail a 
											LEFT JOIN production c ON REPLACE(a.id_produksi, 'PRO-', '') = c.no_ipp
                                        WHERE
                                            a.sts_produksi = 'Y' 
                                            AND ( a.upload_real = 'N' AND a.upload_real2 = 'N' ) 
                                            AND ( a.print_merge = 'N' AND a.print_merge2 = 'N' ) 
                                            AND a.sts_produksi_date >= '2021-01-01'
											AND c.status != 'FINISH'
                                        GROUP BY
                                            a.id_produksi 
                                        ORDER BY
                                            a.id_produksi ASC")->result_array();
		$data_list2 = $this->db->query(" SELECT
											a.id_produksi
										FROM
											production_detail a
										WHERE
											a.product_code_cut = 'tanki'
											AND a.sts_produksi_date >= '2021-01-01'
										GROUP BY
											a.id_produksi 
										ORDER BY
											a.id_produksi ASC")->result_array();
		$data_list = array_merge($data_list1,$data_list2);
		$data_spk = $this->db->group_by('kode_spk')->get('production_spk')->result_array();
		$data = array(
			'title'			=> $judul,
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'list_ipp'		=> $data_list,
			'data_spk'		=> $data_spk,
			'tanda'			=> $tanda,
			'akses_menu'	=> $Arr_Akses
		);
		history('View data request spk new');
		$this->load->view('Produksi/index',$data);
	}

	public function create_spk(){
		$data 		= $this->input->post();

		$check = $data['check'];
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		//pengurutan kode
		$YM	= date('ym');
		$srcPlant		= "SELECT MAX(kode_spk) as maxP FROM production_spk WHERE kode_spk LIKE '".$YM."%' ";
		$resultPlant	= $this->db->query($srcPlant)->result_array();
		$angkaUrut2		= $resultPlant[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 4, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_spk		= $YM.$urut2;

		$WHERE_IN_NOT_TANKI = [];
		$WHERE_IN_TANKI = [];
		foreach ($check as $key => $value) {
			$EXPLODE = explode('-',$value);
			if($EXPLODE[1] == 'tanki'){
				$WHERE_IN_TANKI[] = $EXPLODE[0];
			}
			else{
				$WHERE_IN_NOT_TANKI[] = $EXPLODE[0];
			}
		}
		// print_r($WHERE_IN_NOT_TANKI);
		// print_r($WHERE_IN_TANKI);
		// exit;
		if(!empty($WHERE_IN_NOT_TANKI)){
			$get_detail_produksi = $this->db
										->select('*')
										->from('so_detail_header')
										->where_in('id', $WHERE_IN_NOT_TANKI)
										->get()
										->result_array();
			$InsertKode = [];
			foreach ($get_detail_produksi as $key => $value) {
				$id_qty = "spk_".$value['id'];
				$QTY 	= $data[$id_qty];
				if($QTY > 0){
					$no_ipp = $data["ipp_".$value['id']];
					$no_pro = "PRO-".$no_ipp;
					
					$InsertKode[$key]['kode_spk'] 		= $kode_spk;
					$InsertKode[$key]['id_milik'] 		= $value['id'];
					$InsertKode[$key]['product'] 		= $value['id_category'];
					$InsertKode[$key]['id_product'] 	= $value['id_product'];
					$InsertKode[$key]['no_spk'] 		= $value['no_spk'];
					$InsertKode[$key]['no_ipp'] 		= $no_ipp;
					$InsertKode[$key]['qty'] 			= $QTY;
					$InsertKode[$key]['created_by'] 	= $username;
					$InsertKode[$key]['created_date'] 	= $datetime;

					$get_urut = $this->db->select('MAX(urut_product) AS urut_max')->get_where('production_detail', array('id_produksi'=>$no_pro,'id_milik'=>$value['id']))->result();
					$urut_nomor = (!empty($get_urut))?$get_urut[0]->urut_max + 1:0;
					$nomor_so = get_nomor_so($no_ipp);
					$kode_urut = substr($value['no_komponen'],-3);

					$product_code = $nomor_so.'-'.$kode_urut.'.'.$urut_nomor;

					$InsertKode[$key]['product_code'] 	= $product_code;
					$InsertKode[$key]['urut_product'] 	= $urut_nomor;

					$qUpdate 	= $this->db->query("UPDATE 
														production_detail
													SET 
														kode_spk='$kode_spk',
														no_spk='".$value['no_spk']."',
														product_code='$product_code',
														urut_product='$urut_nomor',
														print_merge='Y',
														print_merge_by='$username',
														print_merge_date='$datetime',
														print_merge2='Y',
														print_merge2_by='$username',
														print_merge2_date='$datetime'
													WHERE 
														id_milik='".$value['id']."'
														AND id_produksi= '".$no_pro."'
														AND kode_spk IS NULL
														AND print_merge = 'N'
													ORDER BY 
														id ASC 
													LIMIT $QTY");
					// echo $qUpdate."<br>";
				}
			}
			// exit;
			$this->db->insert_batch('production_spk', $InsertKode);
		}
		if(!empty($WHERE_IN_TANKI)){
			foreach ($WHERE_IN_TANKI as $key => $value) {
				$id_qty = "spk_".$value;
				$QTY 	= $data[$id_qty];
				if($QTY > 0){
					$no_ipp = $data["ipp_".$value];
					$no_pro = "PRO-".$no_ipp;

					$get_urut 		= $this->db->select('MAX(urut_product) AS urut_max, id_product, no_spk, sub_delivery, product_code')->get_where('production_detail', array('id_produksi'=>$no_pro,'id_milik'=>$value))->result();
					$urut_nomor 	= (!empty($get_urut))?$get_urut[0]->urut_max + 1:0;
					$id_product 	= (!empty($get_urut))?$get_urut[0]->id_product:null;
					$no_spk 		= (!empty($get_urut))?$get_urut[0]->no_spk:null;
					$sub_delivery 	= (!empty($get_urut))?$get_urut[0]->sub_delivery:null;
					$product_so 	= (!empty($get_urut))?$get_urut[0]->product_code:null;

					$product_code = $product_so.'-'.$sub_delivery.'.'.$urut_nomor;

					$InsertKode[$key]['kode_spk'] 		= $kode_spk;
					$InsertKode[$key]['id_milik'] 		= $value;
					$InsertKode[$key]['product'] 		= $id_product;
					$InsertKode[$key]['id_product'] 	= 'tanki';
					$InsertKode[$key]['no_ipp'] 		= $no_ipp;
					$InsertKode[$key]['no_spk'] 		= $no_spk;
					$InsertKode[$key]['qty'] 			= $QTY;
					$InsertKode[$key]['created_by'] 	= $username;
					$InsertKode[$key]['created_date'] 	= $datetime;
					$InsertKode[$key]['product_code'] 	= $product_code;
					$InsertKode[$key]['urut_product'] 	= $urut_nomor;

					$qUpdate 	= $this->db->query("UPDATE 
														production_detail
													SET 
														kode_spk='$kode_spk',
														product_code='$product_code',
														urut_product='$urut_nomor',
														print_merge='Y',
														print_merge_by='$username',
														print_merge_date='$datetime',
														print_merge2='Y',
														print_merge2_by='$username',
														print_merge2_date='$datetime'
													WHERE 
														id_milik='".$value."'
														AND id_produksi= '".$no_pro."'
														AND kode_spk IS NULL
														AND print_merge = 'N'
													ORDER BY 
														id ASC 
													LIMIT $QTY");
				}
			}
			$this->db->insert_batch('production_spk', $InsertKode);
		}
	
		// print_r($InsertKode);
		// exit;
		$Arr_Kembali	= array(
			'status' => 1,
			'kode_spk'	=> $kode_spk
		);

		echo json_encode($Arr_Kembali);
	}

	public function spk_baru(){
		$kode_spk	= $this->uri->segment(3);
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		foreach ($get_detail_spk as $key => $value) {
			$WHERE_IN_KEY[] = $value['id_milik'];
			$WHERE_KEY[] 	= $value['id'];
		}

		$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
		//UTAMA
		$get_liner_utama = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0002' 
											ORDER BY
												id_detail
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'GLASS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'RESIN AND ADD' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0002' 
											ORDER BY
												id_detail 
											)")->result_array();
		$get_str_n1_utama = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
										)
										UNION
										(
											SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0002' 
										ORDER BY
											id_detail
										)")->result_array();
		$get_str_n2_utama = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
										)
										UNION
										(
											SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0002' 
										ORDER BY
											id_detail
										)")->result_array();
		$get_structure_utama = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR THICKNESS' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
									ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR THICKNESS' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0002' 
									ORDER BY
										id_detail
									)")->result_array();
		$get_external_utama = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
										)
										UNION
										(
											SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0002' 
										ORDER BY
											id_detail
										)")->result_array();
		$get_topcoat_utama = $this->db->query("SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0002' 
										ORDER BY
											id_detail 
										")->result_array();

		//MIXING
		$get_liner_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001' 
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'RESIN AND ADD' 
											AND id_material <> 'MTL-1903000'
											AND id_category <> 'TYP-0002' 
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'RESIN AND ADD' 
											AND id_material <> 'MTL-1903000'
											AND id_category <> 'TYP-0002' 
										ORDER BY
											id_detail DESC
										)")->result_array();
		$get_structure_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'STRUKTUR THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
													AND id_category <> 'TYP-0002' 
												ORDER BY
												id_detail 
											)")->result_array();
		$get_external_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'
											GROUP BY
												id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'EXTERNAL LAYER THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
													AND id_category <> 'TYP-0002' 
												ORDER BY
												id_detail 
											)")->result_array();
		$get_topcoat_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											MAX(last_cost) AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'TOPCOAT' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
										)")->result_array();
		$get_str_n1_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
										AND id_category <> 'TYP-0002' 
									ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
											id_detail 
									)")->result_array();
		$get_str_n2_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
												id_detail 
											)")->result_array();

		
		history('Print SPK New '.$kode_spk);
		if($get_detail_spk[0]['id_product'] != 'tanki'){
			if($get_detail_spk[0]['id_product'] != 'deadstok'){
				$data = array(
					'Nama_Beda' 			=> $Nama_Beda,
					'printby' 				=> $printby,
					'get_detail_spk' 		=> $get_detail_spk,
					'get_liner_utama' 		=> $this->getDataGroupMaterial($get_liner_utama, $WHERE_KEY),
					'get_str_n1_utama' 		=> $this->getDataGroupMaterial($get_str_n1_utama, $WHERE_KEY),
					'get_str_n2_utama' 		=> $this->getDataGroupMaterial($get_str_n2_utama, $WHERE_KEY),
					'get_structure_utama' 	=> $this->getDataGroupMaterial($get_structure_utama, $WHERE_KEY),
					'get_external_utama' 	=> $this->getDataGroupMaterial($get_external_utama, $WHERE_KEY),
					'get_topcoat_utama' 	=> $this->getDataGroupMaterial($get_topcoat_utama, $WHERE_KEY),
					'get_liner_mix' 		=> $this->getDataGroupMaterialLiner($get_liner_mix, $WHERE_KEY),
					'get_structure_mix' 	=> $this->getDataGroupMaterial($get_structure_mix, $WHERE_KEY),
					'get_external_mix' 		=> $this->getDataGroupMaterial($get_external_mix, $WHERE_KEY),
					'get_topcoat_mix' 		=> $this->getDataGroupMaterial($get_topcoat_mix, $WHERE_KEY),
					'get_str_n1_mix' 		=> $this->getDataGroupMaterial($get_str_n1_mix, $WHERE_KEY),
					'get_str_n2_mix' 		=> $this->getDataGroupMaterial($get_str_n2_mix, $WHERE_KEY),
					'kode_spk' 				=> $kode_spk
				);
				$this->load->view('Print/print_spk_baru', $data);
			}
			else{
				$kode_deadstok 		= $get_detail_spk[0]['product_code_cut'];
				$qty 				= $get_detail_spk[0]['qty'];
				$get_liner_utama 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,0);
				$get_structure_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,0);
				$get_external_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,0);

				$get_liner_utama_resin 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,1);
				$get_structure_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,1);
				$get_external_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,1);

				$get_liner_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','plus',null,null);
				$get_structure_utama_mixing = getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','plus',null,null);
				$get_external_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','plus',null,null);
				$get_topcoat_mixing 		= getEstimasiDeadstok($kode_deadstok,$qty,'TOPCOAT','plus',null,null);

				$data = array(
					'Nama_Beda' 			=> $Nama_Beda,
					'printby' 				=> $printby,
					'get_detail_spk' 		=> $get_detail_spk,
					'get_liner_utama' 		=> $get_liner_utama,
					'get_str_n1_utama' 		=> [],
					'get_str_n2_utama' 		=> [],
					'get_structure_utama' 	=> $get_structure_utama,
					'get_external_utama' 	=> $get_external_utama,
					'get_topcoat_utama' 	=> [],
					'get_liner_mix' 		=> array_merge($get_liner_utama_resin,$get_liner_utama_mixing),
					'get_structure_mix' 	=> array_merge($get_structure_utama_resin,$get_structure_utama_mixing),
					'get_external_mix' 		=> array_merge($get_external_utama_resin,$get_external_utama_mixing),
					'get_topcoat_mix' 		=> $get_topcoat_mixing,
					'get_str_n1_mix' 		=> [],
					'get_str_n2_mix' 		=> [],
					'kode_spk' 				=> $kode_spk
				);
				$this->load->view('Print/print_spk_baru', $data);
			}
		}
		else{
			$UNIQ_MAT 			= $get_detail_spk[0]['id_milik'].'-';
			$GET_EST_TANKI 		= $this->tanki_model->get_est_material_tanki($get_detail_spk[0]['no_ipp']);
			$GET_DETSPEC_TANKI 	= $this->tanki_model->get_detail_tanki($get_detail_spk[0]['id_milik']);
			// echo '<pre>';
			// print_r($GET_EST_TANKI[$UNIQ_MAT.'liner-1']);
			// exit;
			$qty 				= $get_detail_spk[0]['qty'];
			$qty_est_tanki 		= (!empty($GET_DETSPEC_TANKI['qty']))?$GET_DETSPEC_TANKI['qty']:0;
			$data = array(
				'qty_est_tanki' 		=> $qty_est_tanki,
				'qty' 					=> $qty,
				'Nama_Beda' 			=> $Nama_Beda,
				'printby' 				=> $printby,
				'get_detail_spk' 		=> $get_detail_spk,
				'GET_DETAIL_TANKI'		=> $this->tanki_model->get_detail_ipp_tanki(),
				'GET_MATERIAL'			=> get_detail_material(),
				'kode_spk' 				=> $kode_spk,
				'tanki_model' 			=> $this->tanki_model,
				'get_liner_utama' 		=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'liner-1']))?$GET_EST_TANKI[$UNIQ_MAT.'liner-1']:[],
				'get_structure_utama' 	=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'structure-1']))?$GET_EST_TANKI[$UNIQ_MAT.'structure-1']:[],
				'get_external_utama' 	=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'external-1']))?$GET_EST_TANKI[$UNIQ_MAT.'external-1']:[],
				'get_topcoat_utama' 	=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'topcoat-1']))?$GET_EST_TANKI[$UNIQ_MAT.'topcoat-1']:[],
				'get_liner_mix' 		=> array_merge(
					(!empty($GET_EST_TANKI[$UNIQ_MAT.'primer-2']))?$GET_EST_TANKI[$UNIQ_MAT.'primer-2']:[],
					(!empty($GET_EST_TANKI[$UNIQ_MAT.'liner-2']))?$GET_EST_TANKI[$UNIQ_MAT.'liner-2']:[]
				),
				'get_structure_mix' 	=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'structure-2']))?$GET_EST_TANKI[$UNIQ_MAT.'structure-2']:[],
				'get_external_mix' 		=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'external-2']))?$GET_EST_TANKI[$UNIQ_MAT.'external-2']:[],
				'get_topcoat_mix' 		=> (!empty($GET_EST_TANKI[$UNIQ_MAT.'topcoat-2']))?$GET_EST_TANKI[$UNIQ_MAT.'topcoat-2']:[],
			);
			$this->load->view('Print/print_spk_baru_tanki', $data);
		}
	}

	public function print_req_mixing(){
		$ID	= $this->uri->segment(3);
		$detAdjustment = $this->db->get_where('warehouse_adjustment', array('id'=>$ID))->result_array();
		$get_detail_spk2 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang')
							->from('production_spk_parsial a')	
							->join('production_spk b','a.id_spk = b.id')
							->where('a.kode_spk',$detAdjustment[0]['kode_spk'])
							->where('a.created_date',$detAdjustment[0]['created_date'])
							->where('a.spk','1')
							->get()
							->result_array();
		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$getWherehouse2 = $this->db->get_where('warehouse', array('category'=>'subgudang'))->result_array();
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		//DETAIL MATERIAL
		$kode_spk		= $detAdjustment[0]['kode_spk'];
		// $detail_input	= $data['detail_input'];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($get_detail_spk2 as $key => $value) {
			if($value['qty_parsial'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty_parsial'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty'];
			}
		}

		$get_detail_spk = $this->db
							->select('*')
							->from('production_spk')
							->where('kode_spk', $kode_spk)
							->where_in('id',$where_in_ID)
							->get()
							->result_array();

		foreach ($get_detail_spk as $key => $value) {
			$WHERE_IN_KEY[] = $value['id_milik'];
			$WHERE_KEY[] 	= $value['id'];
		}

		$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
		// MIXING
		$get_liner_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'LINER THIKNESS / CB' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001' 
									GROUP BY
										id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'LINER THIKNESS / CB' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
										AND id_category <> 'TYP-0002' 
									ORDER BY
									id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'RESIN AND ADD' 
											AND id_material <> 'MTL-1903000'
											AND id_category <> 'TYP-0002'
										ORDER BY
										id_detail 
									)")->result_array();
		$get_structure_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
										)")->result_array();
		$get_external_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
												AND id_category <> 'TYP-0002' 
											ORDER BY
											id_detail 
										)")->result_array();
		$get_topcoat_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										MAX(last_cost) AS berat 
									FROM
										so_component_detail_plus 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'TOPCOAT' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'TOPCOAT' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
										AND id_category <> 'TYP-0002' 
									ORDER BY
									id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
										id_detail 
									)")->result_array();
		$get_str_n1_mix = $this->db->query("(SELECT
									id_milik,
									id_material,
									nm_material,
									id_category,
									nm_category,
								MAX(last_cost) AS berat 
								FROM
									so_component_detail 
								WHERE
									id_milik IN ".$IMPLODE_IN." 
									AND detail_name = 'STRUKTUR NECK 1' 
									AND id_material <> 'MTL-1903000' 
									AND id_category = 'TYP-0001'  
									GROUP BY
										id_milik
								ORDER BY
									id_detail DESC
								)
								UNION
								(
								SELECT
									id_milik,
									id_material,
									nm_material,
									id_category,
									nm_category,
									last_cost AS berat 
								FROM
									so_component_detail_plus
								WHERE
									id_milik IN ".$IMPLODE_IN." 
									AND detail_name = 'STRUKTUR NECK 1' 
									AND id_material <> 'MTL-1903000' 
									AND id_category <> 'TYP-0001' 
									AND id_category <> 'TYP-0002' 
								ORDER BY
									id_detail 
								)
								UNION
								(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_add
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
										AND id_category <> 'TYP-0002' 
									ORDER BY
										id_detail 
								)")->result_array();
		$get_str_n2_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
											id_detail 
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
											AND id_category <> 'TYP-0002' 
										ORDER BY
											id_detail 
										)")->result_array();

		$data = array(
			'Nama_Beda' 			=> $Nama_Beda,
			'printby' 				=> $printby,
			'warehouse' 	=> $getWherehouse,
			'warehouse2' 	=> $getWherehouse2,
			'get_detail_spk2' 	=> $get_detail_spk2,
			'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
			'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
			'gudang_to' 		=> $detAdjustment[0]['id_gudang_ke'],
			'kode_spk' 			=> $detAdjustment[0]['kode_spk'],
			'kode_trans' 		=> $detAdjustment[0]['kode_trans'],
			'hist_produksi'		=> $detAdjustment[0]['created_date']
		);
		$this->load->view('Print/print_req_mixing', $data);
	}

	public function print_req_mixing_edit(){
		$ID	= $this->uri->segment(3);
		$detAdjustment = $this->db->get_where('warehouse_adjustment', array('id'=>$ID))->result_array();
		$get_detail_spk2 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang')
							->from('production_spk_parsial a')	
							->join('production_spk b','a.id_spk = b.id')
							->where('a.kode_spk',$detAdjustment[0]['kode_spk'])
							->where('a.created_date',$detAdjustment[0]['created_date'])
							->where('a.spk','1')
							->get()
							->result_array();
		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$getWherehouse2 = $this->db->get_where('warehouse', array('category'=>'subgudang'))->result_array();
		
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		//DETAIL MATERIAL
		$kode_spk		= $detAdjustment[0]['kode_spk'];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($get_detail_spk2 as $key => $value) {
			if($value['qty_parsial'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty_parsial'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty'];
			}
		}

		$get_detail_spk = $this->db
							->select('*')
							->from('production_spk')
							->where('kode_spk', $kode_spk)
							->where_in('id',$where_in_ID)
							->get()
							->result_array();

		foreach ($get_detail_spk as $key => $value) {
			$WHERE_IN_KEY[] = $value['id_milik'];
			$WHERE_KEY[] 	= $value['id'];
		}

		$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
		// MIXING
		$get_liner_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'LINER THIKNESS / CB' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001' 
									GROUP BY
										id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'LINER THIKNESS / CB' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001'
									ORDER BY
									id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'RESIN AND ADD' 
											AND id_material <> 'MTL-1903000'
										ORDER BY
										id_detail 
									)")->result_array();
		$get_structure_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
										)")->result_array();
		$get_external_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'EXTERNAL LAYER THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
										)")->result_array();
		$get_topcoat_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										MAX(last_cost) AS berat 
									FROM
										so_component_detail_plus 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'TOPCOAT' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'TOPCOAT' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001'
									ORDER BY
									id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
									)")->result_array();
		$get_str_n1_mix = $this->db->query("(SELECT
									id_milik,
									id_material,
									nm_material,
									id_category,
									nm_category,
								MAX(last_cost) AS berat 
								FROM
									so_component_detail 
								WHERE
									id_milik IN ".$IMPLODE_IN." 
									AND detail_name = 'STRUKTUR NECK 1' 
									AND id_material <> 'MTL-1903000' 
									AND id_category = 'TYP-0001'  
									GROUP BY
										id_milik
								ORDER BY
									id_detail DESC
								)
								UNION
								(
								SELECT
									id_milik,
									id_material,
									nm_material,
									id_category,
									nm_category,
									last_cost AS berat 
								FROM
									so_component_detail_plus
								WHERE
									id_milik IN ".$IMPLODE_IN." 
									AND detail_name = 'STRUKTUR NECK 1' 
									AND id_material <> 'MTL-1903000' 
									AND id_category <> 'TYP-0001'
								ORDER BY
									id_detail 
								)
								UNION
								(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_add
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001'
									ORDER BY
										id_detail 
								)")->result_array();
		$get_str_n2_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
											id_detail 
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 2' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
											id_detail 
										)")->result_array();
		
		$getSudahRequest = $this->db->select('SUM(request-aktual) AS selisih_request, SUM(request) AS total_request, id_key')->group_by('id_key')->get_where('print_detail',array('kode_trans'=>$detAdjustment[0]['kode_trans']))->result_array();
		$ArrSearchRequest = [];
		foreach ($getSudahRequest as $key => $value) {
			$ArrSearchRequest[$value['id_key']] = $value['total_request'] - $value['selisih_request'];
		}

		$print_ke = $detAdjustment[0]['print_ke'] + 1;
		if($get_detail_spk[0]['id_product'] != 'tanki'){
			if($get_detail_spk[0]['id_product'] != 'deadstok'){
				$data = array(
					'title' 		=> 'PRINT SPK MIXING (PRINT KE-'.$print_ke.')',
					'action' 		=> 'print',
					'ArrSearch' => $ArrSearchRequest,
					'print_ke' => $print_ke,
					'id'	=> $ID,
					'Nama_Beda' 			=> $Nama_Beda,
					'printby' 				=> $printby,
					'warehouse' 	=> $getWherehouse,
					'warehouse2' 	=> $getWherehouse2,
					'get_detail_spk2' 	=> $get_detail_spk2,
					'GET_SPEC_TANK' 	=> $this->tanki_model->get_spec_check($get_detail_spk[0]['no_ipp']),
					'costcenter' 	=> $this->db->order_by('nm_gudang','asc')->get_where('warehouse',array('category'=>'produksi'))->result_array(),
					'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
					'gudang_to' 		=> $detAdjustment[0]['id_gudang_ke'],
					'kode_spk' 			=> $detAdjustment[0]['kode_spk'],
					'kode_trans' 		=> $detAdjustment[0]['kode_trans'],
					'hist_produksi'		=> $detAdjustment[0]['created_date']
				);
			}
			else{
				$kode_deadstok 		= $get_detail_spk[0]['product_code_cut'];
				$qty 				= $get_detail_spk[0]['qty'];
				$get_liner_utama 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,0);
				$get_structure_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,0);
				$get_external_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,0);

				$get_liner_utama_resin 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,1);
				$get_structure_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,1);
				$get_external_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,1);

				$get_liner_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','plus',null,null);
				$get_structure_utama_mixing = getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','plus',null,null);
				$get_external_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','plus',null,null);
				$get_topcoat_mixing 		= getEstimasiDeadstok($kode_deadstok,$qty,'TOPCOAT','plus',null,null);

				$data = array(
					'title' 		=> 'PRINT SPK MIXING (PRINT KE-'.$print_ke.')',
					'action' 		=> 'print',
					'ArrSearch' => $ArrSearchRequest,
					'print_ke' => $print_ke,
					'id'	=> $ID,
					'Nama_Beda' 			=> $Nama_Beda,
					'printby' 				=> $printby,
					'warehouse' 	=> $getWherehouse,
					'warehouse2' 	=> $getWherehouse2,
					'get_detail_spk2' 	=> $get_detail_spk2,
					'GET_SPEC_TANK' 	=> $this->tanki_model->get_spec_check($get_detail_spk[0]['no_ipp']),
					'costcenter' 	=> $this->db->order_by('nm_gudang','asc')->get_where('warehouse',array('category'=>'produksi'))->result_array(),
					'get_liner_utama' 		=> array_merge($get_liner_utama_resin,$get_liner_utama_mixing),
					'get_str_n1_utama' 		=> [],
					'get_str_n2_utama' 		=> [],
					'get_structure_utama' 	=> array_merge($get_structure_utama_resin,$get_structure_utama_mixing),
					'get_external_utama' 	=> array_merge($get_external_utama_resin,$get_external_utama_mixing),
					'get_topcoat_utama' 	=> $get_topcoat_mixing,
					'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
					'gudang_to' 		=> $detAdjustment[0]['id_gudang_ke'],
					'kode_spk' 			=> $detAdjustment[0]['kode_spk'],
					'kode_trans' 		=> $detAdjustment[0]['kode_trans'],
					'hist_produksi'		=> $detAdjustment[0]['created_date']
				);
			}
		}
		else{
			$get_liner_mix = $this->db->query("	SELECT
													a.id_det AS id_milik,
													a.id_material,
													b.nm_material,
													b.id_category,
													b.nm_category,
													a.berat 
												FROM
													est_material_tanki a
													LEFT JOIN raw_materials b ON a.id_material = b.id_material
												WHERE
													a.id_det IN ".$IMPLODE_IN." 
													AND (a.layer = 'liner' OR a.layer = 'primer')
													AND (a.spk_pemisah = '2' OR a.id_tipe='14')
												")->result_array();
			$get_structure_mix = $this->db->query("	SELECT
												a.id_det AS id_milik,
												a.id_material,
												b.nm_material,
												b.id_category,
												b.nm_category,
												a.berat 
											FROM
												est_material_tanki a
												LEFT JOIN raw_materials b ON a.id_material = b.id_material
											WHERE
												a.id_det IN ".$IMPLODE_IN." 
												AND a.layer = 'structure'
												AND (a.spk_pemisah = '2' OR a.id_tipe='14')
											")->result_array();
			$get_topcoat_mix = $this->db->query("	SELECT
											a.id_det AS id_milik,
											a.id_material,
											b.nm_material,
											b.id_category,
											b.nm_category,
											a.berat 
										FROM
											est_material_tanki a
											LEFT JOIN raw_materials b ON a.id_material = b.id_material
										WHERE
											a.id_det IN ".$IMPLODE_IN." 
											AND a.layer = 'topcoat'
											AND (a.spk_pemisah = '2' OR a.id_tipe='14')
										")->result_array();
			$data = array(
				'title' 		=> 'PRINT SPK MIXING (PRINT KE-'.$print_ke.')',
				'action' 		=> 'print',
				'ArrSearch' => $ArrSearchRequest,
				'print_ke' => $print_ke,
				'id'	=> $ID,
				'Nama_Beda' 			=> $Nama_Beda,
				'printby' 				=> $printby,
				'warehouse' 	=> $getWherehouse,
				'warehouse2' 	=> $getWherehouse2,
				'get_detail_spk2' 	=> $get_detail_spk2,
				'GET_SPEC_TANK' 	=> $this->tanki_model->get_spec_check($get_detail_spk[0]['no_ipp']),
				'costcenter' 	=> $this->db->order_by('nm_gudang','asc')->get_where('warehouse',array('category'=>'produksi'))->result_array(),
				'get_liner_utama' 		=> $this->getDataGroupMaterialNewTanki($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_str_n1_utama' 		=> [],
				'get_str_n2_utama' 		=> [],
				'get_structure_utama' 	=> $this->getDataGroupMaterialNewTanki($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_external_utama' 	=> [],
				'get_topcoat_utama' 	=> $this->getDataGroupMaterialNewTanki($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
				'gudang_to' 		=> $detAdjustment[0]['id_gudang_ke'],
				'kode_spk' 			=> $detAdjustment[0]['kode_spk'],
				'kode_trans' 		=> $detAdjustment[0]['kode_trans'],
				'hist_produksi'		=> $detAdjustment[0]['created_date']
			);
		}
		$this->load->view('Produksi/print_req_mixing_edit', $data);
	}

	public function print_req_mixing_save(){
		$data 			= $this->input->post();
		$id 			= $data['id'];
		$kode_trans 	= $data['kode_trans'];
		$hist_produksi 	= $data['hist_produksi'];
		$edit_request 	= $data['edit_request'];
		$tgl_plan 		= $data['tgl_plan'];
		$print_ke 		= $data['print_ke'];
		$costcenter 	= $data['costcenter'];
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$YM				= date('ym');
		$qIPP			= "SELECT MAX(kode_uniq) as maxP FROM print_header WHERE kode_uniq LIKE 'UQ".$YM."%' ";
		$resultIPP		= $this->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 5);
		$urutan2++;
		$urut2			= sprintf('%05s',$urutan2);
		$kode_uniq		= "UQ".$YM.$urut2;
		// print_r($edit_request);
		// exit;
		$ArrHeader = [
			'kode_uniq' => $kode_uniq,
			'id_adjustment' => $id,
			'id_gudang' => $costcenter,
			'kode_trans' => $kode_trans,
			'hist_produksi' => $hist_produksi,
			'tgl_planning' => $tgl_plan,
			'print_ke' => $print_ke,
			'print_by' => $username,
			'print_date' => $datetime
		];

		$getPrintKe = $this->db->get_where('warehouse_adjustment',array('id'=>$id))->result();
		$ArrUpdate = [
			'print_ke' => $getPrintKe[0]->print_ke + 1
		];

		$getDetailAdjust = $this->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
		$GET_KEY_ID = [];
		foreach ($getDetailAdjust as $key => $value) {
			$GET_KEY_ID[$value['key_gudang']]['id'] = $value['id'];
			$GET_KEY_ID[$value['key_gudang']]['req_mix'] = $value['qty_req_mixing'];
		}

		$ArrDetail = [];
		$ArrUpdateAdjust = [];
		$nomor = 0;
		foreach ($edit_request as $key => $value) {$nomor++;
			$REQUEST = str_replace(',','',$value['request']);
			$ArrDetail[$nomor]['kode_uniq'] = $kode_uniq;
			$ArrDetail[$nomor]['layer'] = $value['layer'];
			$ArrDetail[$nomor]['id_material'] = $value['id_material'];
			$ArrDetail[$nomor]['nm_material'] =  $value['material'];
			$ArrDetail[$nomor]['category'] =  $value['category'];
			$ArrDetail[$nomor]['estimasi'] =  $value['estimasi'];
			$ArrDetail[$nomor]['total_req'] =  $value['total_req'];
			$ArrDetail[$nomor]['keterangan'] =  $value['keterangan'];
			$ArrDetail[$nomor]['request'] =  $REQUEST;
			$ArrDetail[$nomor]['id_key'] =  $key;
			$ArrDetail[$nomor]['kode_trans'] =  $kode_trans;

			$ArrUpdateAdjust[$nomor]['id'] = $GET_KEY_ID[$key]['id'];
			$ArrUpdateAdjust[$nomor]['qty_req_mixing'] = $REQUEST + $GET_KEY_ID[$key]['req_mix'];
		}

		// print_r($ArrHeader);
		// print_r($ArrDetail);
		// print_r($ArrUpdate);
		// exit;

		$this->db->trans_start();
			$this->db->insert('print_header', $ArrHeader);
			$this->db->insert_batch('print_detail', $ArrDetail); 

			$this->db->where('id', $id); 
			$this->db->update('warehouse_adjustment', $ArrUpdate); 

			$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdateAdjust,'id'); 
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 2,
				'kode_uniq' => $kode_uniq
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1,
				'kode_uniq' => $kode_uniq
			);
			history("Print spk mixing edit qty ".$id);
		}
		
		echo json_encode($Arr_Kembali);
	}

	public function print_req_mixing_new(){
		$IDUNIQ	= $this->uri->segment(3);
		$PrintHedaer = $this->db->get_where('print_header', array('kode_uniq'=>$IDUNIQ))->result();
		$ID	= $PrintHedaer[0]->id_adjustment;
		$tgl_planning	= $PrintHedaer[0]->tgl_planning;
		$detAdjustment = $this->db->get_where('warehouse_adjustment', array('id'=>$ID))->result_array();
		$get_detail_spk2 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang')
							->from('production_spk_parsial a')	
							->join('production_spk b','a.id_spk = b.id')
							->where('a.kode_spk',$detAdjustment[0]['kode_spk'])
							->where('a.created_date',$detAdjustment[0]['created_date'])
							->where('a.spk','1')
							->get()
							->result_array();
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($get_detail_spk2 as $key => $value) {
			if($value['qty_parsial'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty_parsial'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty'];
			}
		}

		$getPrintData = $this->db->get_where('print_detail',array('kode_uniq'=>$IDUNIQ))->result_array();
		$ArrDataPrin = [];
		foreach ($getPrintData as $key => $value) {
			$ArrDataPrin[$value['layer']][] = $value;
		}

		$get_liner_utama = (!empty($ArrDataPrin['LINER THIKNESS / CB']))?$ArrDataPrin['LINER THIKNESS / CB']:array();
		$get_str_n1_utama = (!empty($ArrDataPrin['STRUCTURE NECK 1']))?$ArrDataPrin['STRUCTURE NECK 1']:array();
		$get_str_n2_utama = (!empty($ArrDataPrin['STRUCTURE NECK 2']))?$ArrDataPrin['STRUCTURE NECK 2']:array();
		$get_structure_utama = (!empty($ArrDataPrin['STRUKTUR THICKNESS']))?$ArrDataPrin['STRUKTUR THICKNESS']:array();
		$get_external_utama = (!empty($ArrDataPrin['EXTERNAL THICKNESS']))?$ArrDataPrin['EXTERNAL THICKNESS']:array();
		$get_topcoat_utama = (!empty($ArrDataPrin['TOPCOAT']))?$ArrDataPrin['TOPCOAT']:array();

		$data = array(
			'Nama_Beda' 			=> $Nama_Beda,
			'printby' 				=> $printby,
			'get_detail_spk2' 	=> $get_detail_spk2,
			'get_liner_utama' 		=> $get_liner_utama,
			'get_str_n1_utama' 		=> $get_str_n1_utama,
			'get_str_n2_utama' 		=> $get_str_n2_utama,
			'get_structure_utama' 	=> $get_structure_utama,
			'get_external_utama' 	=> $get_external_utama,
			'get_topcoat_utama' 	=> $get_topcoat_utama,
			'tgl_planning' 	=> $tgl_planning,
			'GET_SPEC_TANK' 	=> $this->tanki_model->get_spec_check($get_detail_spk2[0]['no_ipp']),
			'gudang_from' 		=> $detAdjustment[0]['id_gudang_dari'],
			'print_ke' 			=> $detAdjustment[0]['print_ke'],
			'gudang_to' 		=> $PrintHedaer[0]->id_gudang,
			'kode_spk' 			=> $detAdjustment[0]['kode_spk'],
			'kode_trans' 		=> $detAdjustment[0]['kode_trans'],
			'hist_produksi'		=> $detAdjustment[0]['created_date']
		);
		$this->load->view('Print/print_req_mixing_new', $data);
		history("Print spk mixing edit qty ".$IDUNIQ);
	}

	public function getDataGroupMaterial($data=null,$uniq=null) {
		$groups = array();
		foreach ($data as $item) {
			$get_qty 	= $this->db->select('qty')->from('production_spk')->where('id_milik',$item['id_milik'])->where_in('id',$uniq)->get()->result();
			$key 		= $item['id_material'];
			$qty		= (!empty($get_qty[0]->qty))?$get_qty[0]->qty:0;
			$berat 		= $item['berat'] * $qty;
			if (!array_key_exists($key, $groups)) {
				$groups[$key] = array(
					'id_milik' 		=> $item['id_milik'],
					'id_material' 	=> $item['id_material'],
					'nm_material' 	=> $item['nm_material'],
					'id_category' 	=> $item['id_category'],
					'nm_category' 	=> $item['nm_category'],
					'berat' 		=> $berat,
				);
			} else {
				$groups[$key]['berat'] = $groups[$key]['berat'] + $berat;
			}
		}
		return $groups;
	}

	public function getDataGroupMaterialLiner($data=null,$uniq=null) {
		$groups = array();
		foreach ($data as $item) {
			$get_qty 	= $this->db->select('qty')->from('production_spk')->where('id_milik',$item['id_milik'])->where_in('id',$uniq)->get()->result();
			$key 		= $item['id_material'].$item['nm_category'];
			$qty		= (!empty($get_qty[0]->qty))?$get_qty[0]->qty:0;
			$berat 		= $item['berat'] * $qty;
			if (!array_key_exists($key, $groups)) {
				$groups[$key] = array(
					'id_milik' 		=> $item['id_milik'],
					'id_material' 	=> $item['id_material'],
					'nm_material' 	=> $item['nm_material'],
					'id_category' 	=> $item['id_category'],
					'nm_category' 	=> $item['nm_category'],
					'berat' 		=> $berat,
				);
			} else {
				$groups[$key]['berat'] = $groups[$key]['berat'] + $berat;
			}
		}
		return $groups;
	}

	public function getDataGroupMaterialNew($data=null,$uniq=null,$data_qty=null,$data_qty_all=null) {
		$groups = array();
		foreach ($data as $item) {
			$get_qty 	= $this->db->select('id')->from('production_spk')->where('id_milik',$item['id_milik'])->where_in('id',$uniq)->get()->result();
			$qty		= $data_qty[$get_qty[0]->id];
			$qty_all	= $data_qty_all[$get_qty[0]->id];
			
			$key 		= $item['id_material'];
			$berat_unit = $item['berat'];
			$berat_all 	= $item['berat'] * $qty_all;
			$berat 		= $item['berat'] * $qty;
			if (!array_key_exists($key, $groups)) {
				$groups[$key] = array(
					'id_milik' 		=> $item['id_milik'],
					'id_material' 	=> $item['id_material'],
					'nm_material' 	=> $item['nm_material'],
					'id_category' 	=> $item['id_category'],
					'nm_category' 	=> $item['nm_category'],
					'berat_unit' 	=> $berat_unit,
					'berat_all' 	=> $berat_all,
					'berat' 		=> $berat,
				);
			} else {
				$groups[$key]['berat'] 		= $groups[$key]['berat'] + $berat;
				$groups[$key]['berat_all'] 	= $groups[$key]['berat_all'] + $berat_all;
				$groups[$key]['berat_unit']	= $groups[$key]['berat_unit'] + $berat_unit;
			}
		}
		return $groups;
	}

	public function getDataGroupMaterialNewTanki($data=null,$uniq=null,$data_qty=null,$data_qty_all=null) {
		$groups = array();
		foreach ($data as $item) {
			$get_qty 	= $this->db->select('id')->from('production_spk')->where('id_milik',$item['id_milik'])->where_in('id',$uniq)->get()->result();
			$qty		= $data_qty[$get_qty[0]->id];
			$qty_all	= $data_qty_all[$get_qty[0]->id];
			
			$key 		= $item['id_material'];
			$berat_unit = $item['berat'] / $qty;
			$berat_all 	= $item['berat'];
			$berat 		= $item['berat'] / $qty_all * $qty;
			if (!array_key_exists($key, $groups)) {
				$groups[$key] = array(
					'id_milik' 		=> $item['id_milik'],
					'id_material' 	=> $item['id_material'],
					'nm_material' 	=> $item['nm_material'],
					'id_category' 	=> $item['id_category'],
					'nm_category' 	=> $item['nm_category'],
					'berat_unit' 	=> $berat_unit,
					'berat_all' 	=> $berat_all,
					'berat' 		=> $berat,
				);
			} else {
				$groups[$key]['berat'] 		= $groups[$key]['berat'] + $berat;
				$groups[$key]['berat_all'] 	= $groups[$key]['berat_all'] + $berat_all;
				$groups[$key]['berat_unit']	= $groups[$key]['berat_unit'] + $berat_unit;
			}
		}
		return $groups;
	}

	public function modalDetail(){
		$kode_spk		= $this->uri->segment(3);
		$result_data 	= $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$ArrData = [];
		foreach ($result_data as $key => $value) {
			$ArrData[$key]['product'] = $value['product'];
			$ArrData[$key]['spec'] = ($value['id_product'] == 'tanki')?$this->tanki_model->get_spec($value['id_milik']):spec_bq2($value['id_milik']);
			$ArrData[$key]['qty'] = $value['qty'];
			$ArrData[$key]['spk1'] = $value['spk1'];
			$ArrData[$key]['spk2'] = $value['spk2'];
		}

		$data = array(
			'kode_spk' 		=> $kode_spk,
			'result_data' 	=> $ArrData
		);
		$this->load->view('Produksi/modalDetail', $data);
	}

	public function aktual_1(){
		$kode_spk	= $this->uri->segment(3);
		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$hist_produksi = $this->db->group_by('created_date')->get_where('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'))->result_array();

		$link_submit 			= 'save_update_produksi_1_new';
		$link_submit_close 		= 'save_update_produksi_1_new_closing';
		if($get_detail_spk[0]['id_product'] == 'tanki'){
			$link_submit 		= 'save_update_produksi_1_new_tanki';
			$link_submit_close 	= 'save_update_produksi_1_new_closing_tanki';
		}
		if($get_detail_spk[0]['id_product'] == 'deadstok'){
			$link_submit 		= 'save_update_produksi_1_new_deadstok';
			$link_submit_close 	= 'save_update_produksi_1_new_closing_deadstok';
		}

		$data = array(
			'title'				=> 'Aktual SPK',
			'action'			=> 'index',
			'warehouse' 		=> $getWherehouse,
			'get_detail_spk' 	=> $get_detail_spk,
			'hist_produksi' 	=> $hist_produksi,
			'kode_spk' 			=> $kode_spk,
			'link_submit'		=> $link_submit,
			'link_submit_close'	=> $link_submit_close
		);
		
		$this->load->view('Produksi/aktual_1', $data);
	}

	public function aktual_2(){
		$kode_spk		= $this->uri->segment(3);
		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		$getWherehouse 	= $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$hist_produksi 	= $this->db->group_by('created_date')->get_where('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'))->result_array();
		$link_submit = 'save_update_produksi_2_new';
		if($get_detail_spk[0]['id_product'] == 'tanki'){
			$link_submit = 'save_update_produksi_2_new_tanki';
		}
		if($get_detail_spk[0]['id_product'] == 'deadstok'){
			$link_submit = 'save_update_produksi_2_new_deadstok';
		}
		$data = array(
			'title'			=> 'Aktual SPK Mixing',
			'action'		=> 'index',
			'warehouse' 	=> $getWherehouse,
			'get_detail_spk' 		=> $get_detail_spk,
			'hist_produksi' 		=> $hist_produksi,
			'kode_spk' 				=> $kode_spk,
			'link_submit'	=> $link_submit
		);
		$this->load->view('Produksi/aktual_2', $data);
	}

	public function save_update_produksi_2_costing(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 				= $data['kode_spk'];
		$id_gudang 				= $data['id_gudang'];

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db->select('MAX(id_detail) AS id_detail, id_milik, id_product, id_material, MAX(last_cost) AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$total_est 	= $value3['berat'] * $value['qty'];
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $value['qty'];
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $value['qty'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $datetime;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 2;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;


									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $unit_act;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0002','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$total_est 	= $value3['berat'] * $value['qty'];
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $value['qty'];
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $value['qty'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $datetime;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 2;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $unit_act;
								}
							}
						}
						//ADD
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_add', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$total_est 	= $value3['berat'] * $value['qty'];
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $value['qty'];
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $value['qty'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['status_date'] 			= $datetime;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['spk'] 	= 2;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;


									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $unit_act;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		foreach ($temp as $key => $value) {
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			
			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock[$key]['update_date'] 	= date('Y-m-d H:i:s');

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['id_gudang_dari'] 	= NULL;
				$ArrHist[$key]['kd_gudang_dari'] 	= NULL;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi';
				$ArrHist[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$key]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrAktualAdd);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$UpdateRealFlag = array(
			'upload_real2' => "Y",
			'upload_by2' => $data_session['ORI_User']['username'],
			'upload_date2' => date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			//update flag produksi input
			$this->db->where('kode_spk',$kode_spk);
			$this->db->update('production_detail',$UpdateRealFlag);
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
			$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk,'spk'=>'2'));
			$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk,'spk'=>'2'));
			$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk,'spk'=>'2'));
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
				$this->db->insert_batch('production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
				$this->db->insert_batch('production_real_detail_plus',$ArrAktualPlus);
			}
			if(!empty($ArrAktualAdd)){
				$this->db->insert_batch('tmp_production_real_detail_add',$ArrAktualAdd);
				$this->db->insert_batch('production_real_detail_add',$ArrAktualAdd);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi mixing to costing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_1_costing(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 				= $data['kode_spk'];
		$id_gudang 				= $data['id_gudang'];

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$total_est 	= $value3['berat'] * $value['qty'];
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $value['qty'];
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $value['qty'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $datetime;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $unit_act;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category'=>'TYP-0002','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$total_est 	= $value3['berat'] * $value['qty'];
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $value['qty'];
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $value['qty'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $datetime;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 1;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_costby'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $unit_act;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		foreach ($temp as $key => $value) {
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			
			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $data_session['ORI_User']['username'];
				$ArrStock[$key]['update_date'] 	= date('Y-m-d H:i:s');

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['id_gudang_dari'] 	= NULL;
				$ArrHist[$key]['kd_gudang_dari'] 	= NULL;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi';
				$ArrHist[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrHist[$key]['update_date'] 		= date('Y-m-d H:i:s');
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$UpdateRealFlag = array(
			'upload_real' => "Y",
			'upload_by' => $data_session['ORI_User']['username'],
			'upload_date' => date('Y-m-d H:i:s')
		);

		$this->db->trans_start();
			//update flag produksi input
			$this->db->where('kode_spk',$kode_spk);
			$this->db->update('production_detail',$UpdateRealFlag);
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
			$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk,'spk'=>'1'));
			$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk,'spk'=>'1'));
			$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk,'spk'=>'1'));
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
				$this->db->insert_batch('production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
				$this->db->insert_batch('production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi utama to costing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function server_side_request(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index_loose';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_request(
			$requestData['no_ipp'],
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

			$sisa_spk = $this->db->select('COUNT(id) AS sisa_spk')->get_where('production_detail',array('kode_spk'=>NULL,'id_milik'=>$row['id_milik'],'id_produksi'=>$row['id_produksi'],'id_product_deadstok'=>NULL,))->result();
			$qty_release = $this->db->select('COUNT(id) AS sisa_spk')->get_where('production_detail',array('id_milik'=>$row['id_milik'],'id_produksi'=>$row['id_produksi']))->result();

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
				$customer = ($row['sts_tanki'] == 'tanki')?$this->tanki_model->get_ipp_detail($row['no_ipp'])['customer']:$row['nm_customer'];
			$nestedData[]	= "<div align='left'>".strtoupper($customer)."</div>";
				$cutting='';
				if($row['cutting']=='Y') $cutting=' - <span class="badge bg-red">Untuk Cutting</span>';
				$product = ($row['sts_tanki'] == 'tanki')?$row['id_product']:$row['product'].' '.$cutting;
			$nestedData[]	= "<div align='left'>".strtoupper($product)."</div>";
				$no_spk = ($row['sts_tanki'] == 'tanki')?$row['no_spk_tanki']:$row['no_spk'];
			$nestedData[]	= "<div align='left'>".$no_spk."</div>";
				$spec = ($row['sts_tanki'] == 'tanki')?$this->tanki_model->get_spec($row['id_milik']):spec_bq2($row['id_milik']);
			$nestedData[]	= "<div align='left'>".$spec."</div>";
				$id_product = ($row['sts_tanki'] == 'tanki')?"<span class='text-bold text-red'>TANKI</span>":$row['id_product'];
			$nestedData[]	= "<div align='left'>".$id_product."</div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-blue'>".$qty_release[0]->sisa_spk."</span></div>";
			$nestedData[]	= "<div align='center'><span class='badge bg-green sisa_spk'>".$sisa_spk[0]->sisa_spk."</span></div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_".$row['id_milik']."' id='spk_".$row['id_milik']."' class='form-control text-center qty_spk input-sm maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero='' style='width:70px;'><script>$('.maskMoney').maskMoney();</script>
									<input type='hidden' name='ipp_".$row['id_milik']."' value='".$row['no_ipp']."'>
								</div>";
			$type_tanki = ($row['sts_tanki'] == 'tanki')?'tanki':'no';
			if($row['type_product'] != 'field'){				
				$nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' class='chk_personal' data-nomor='$nomor' value='".$row['id_milik']."-".$type_tanki."' ></div>";
			}
			else{
				$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-primary go_to_outgoing' data-nomor='$nomor' data-no_ipp='".$row['no_ipp']."' data-id_milik='".$row['id_milik']."' title='Pindahkan Ke Outgoing'><i class='fa fa-external-link' aria-hidden='true'></i></button>";
			}
			if($row['type_product'] != 'field' AND $sisa_spk[0]->sisa_spk > 0){				
				$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-warning go_to_deadstok' data-nomor='$nomor' data-no_ipp='".$row['no_ipp']."' data-id_milik='".$row['id_milik']."' data-sisa_spk='".$sisa_spk[0]->sisa_spk."' title='Booking dari Deadstok'><i class='fa fa-hand-lizard-o' aria-hidden='true'></i></button>";
			}
			else{
				// if($row['type_product'] == 'field'){
				// 	$nestedData[]	= "<div align='center'><button type='button' class='btn btn-sm btn-default history_print' data-nomor='$nomor' data-no_ipp='".$row['no_ipp']."' data-id_milik='".$row['id_milik']."' title='History Print'><i class='fa fa-print' aria-hidden='true'></i></button>";
				// }
				// else{
					$nestedData[]	= "";
				// }
			}
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

	public function query_data_request($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";
		if($no_ipp <> '0'){
			$where = " AND a.id_produksi='".$no_ipp."' ";
		}

		$where2 = "";
		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		//(SELECT COUNT(b.id) FROM production_detail b WHERE b.kode_spk IS NULL AND b.id_milik=a.id_milik AND b.id_produksi=a.id_produksi)
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.id,
				REPLACE(a.id_produksi,'PRO-','') AS no_ipp,
				a.id_produksi,
				a.id_milik,
				a.id_category AS product,
				a.id_product,
				a.qty,
				b.no_spk,
				a.no_spk AS no_spk_tanki,
				a.product_code_cut AS sts_tanki,
				c.nm_customer,
				b.cutting,
				d.type AS type_product
			FROM
				production_detail a
				LEFT JOIN so_detail_header b ON a.id_milik = b.id AND REPLACE(a.id_produksi, 'PRO-', 'BQ-') = b.id_bq
				LEFT JOIN production c ON REPLACE(a.id_produksi, 'PRO-', '') = c.no_ipp
				LEFT JOIN product_parent d ON a.id_category = d.product_parent,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." ".$where2."
				AND a.sts_produksi = 'Y' 
				AND ( a.upload_real = 'N' AND a.upload_real2 = 'N' ) 
				AND ( a.print_merge = 'N' AND a.print_merge2 = 'N' ) 
				AND a.sts_produksi_date >= '2021-01-01'
				AND (c.status != 'FINISH' OR a.product_code_cut = 'tanki')
				AND (
					a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			GROUP BY
				a.id_milik
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'c.no_ipp',
			2 => 'c.nm_customer',
			3 => 'a.product',
			4 => 'b.no_spk',
			5 => 'a.id_milik',
			6 => 'a.id_product'
		);

		$sql .= " ORDER BY a.id_milik ASC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function server_side_aktual(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index_loose';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_aktual(
			$requestData['status'],
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

		$FLAG = $requestData['status'];
		$GET_NM_USER = $this->get_user;
		foreach($query->result_array() as $row)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'desc')
            {
                $nomor = $urut1 + $start_dari;
            }
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }

		
			$checkStatus1 = $this->db->select('closing_produksi_date, tanggal_produksi, created_date, updated_date, qty AS qty')->group_by('created_date')->get_where('production_spk_parsial', array('kode_spk'=>$row['kode_spk'],'spk'=>'1'))->result_array();
			$checkStatus2 = $this->db->select('a.*, b.checked_date')->group_by('a.created_date')->join('warehouse_adjustment b','a.created_date=b.created_date','left')->get_where('production_spk_parsial a', array('a.kode_spk'=>$row['kode_spk'],'a.spk'=>'2'))->result_array();

			$Status1 = "";
			$Status2 = "";
			$CLOSING_SUM = 0;
			foreach ($checkStatus1 as $key => $value) { $key++;
				$downloadEC = "";
				if(!empty($value['upload_eng_change'])){
					$downloadEC = " | <a href='".base_url('assets/file/produksi/').$value['upload_eng_change']."' target='_blank'>Eng-change</a>";
				}

				$closing_label 	= (!empty($value['closing_produksi_date']))?" | CLOSING":'';
				$closingNum 	= (!empty($value['closing_produksi_date']))?$value['qty']:0;
				$CLOSING_SUM 	+= $closingNum;
				$input_label 	= ($value['created_date'] == $value['updated_date'])?"":' | DONE';
				
				$Status1 .= "<span class='text-green' style='font-size: 11px;'>".$key.". ".date('d-M-Y',strtotime($value['tanggal_produksi'])).$input_label.$closing_label.$downloadEC."</span><br>";
			}
			$QTY_INPUT = 0;
			foreach ($checkStatus2 as $key => $value) { $key++;
				$QTY_INPUT += $value['qty'];
				$downloadEC = "";
				if(!empty($value['upload_eng_change'])){
					$downloadEC = " | <a href='".base_url('assets/file/produksi/').$value['upload_eng_change']."' target='_blank'>Eng-change</a>";
				}

				$closing_label 	= (!empty($value['closing_produksi_date']))?" | CLOSING":'';
				$input_label 	= ($value['created_date'] == $value['updated_date'])?"":' | DONE';
				$checked_label 	= (empty($value['checked_date']))?" | <span class='text-red'>BELUM INPUT</span>":'';
				
				$Status2 .= "<span class='text-orange' style='font-size: 11px;'>".$key.". ".date('d-M-Y',strtotime($value['tanggal_produksi'])).$checked_label.$input_label.$closing_label.$downloadEC."</span><br>";
			}
			
			

			$update_spk_1 = "";
			$update_spk_2 = "";
			$update_get_warehouse = "";
			$print_req_mixing = "";
			$print_spk = "";
			$view_spk = "";
			$release = "";
			$print_spk_mixing = "";
			$print_spk_request = "";

			$history_print	= "<button type='button' class='btn btn-sm btn-default history_print' data-nomor='$nomor' data-no_ipp='".$row['no_ipp']."' data-id_milik='".$row['id_milik']."' title='History Print'><i class='fa fa-print' aria-hidden='true'></i></button>";
			
			if(empty($row['closing_produksi_date']) AND $FLAG == 0){
				$update_spk_1 = "<a href='".base_url('produksi/aktual_1/'.$row['kode_spk'])."' class='btn btn-sm btn-success' title='Update SPK'><i class='fa fa-edit'></i></a>";
			}
			if(empty($row['closing_produksi_date']) AND $FLAG == 1){
				$update_spk_2 = "<a href='".base_url('produksi/aktual_2/'.$row['kode_spk'])."' class='btn btn-sm btn-primary' title='Update SPK Mixing'><i class='fa fa-edit'></i></a>";
			}
			if(empty($row['closing_produksi_date']) AND $FLAG == 2){
				$hist_produksi = $this->db->select('id, created_date, print_ke')->get_where('warehouse_adjustment', array('kode_spk'=>$row['kode_spk'],'status_id'=>'1'))->result_array();

				$List_print = "";
				foreach ($hist_produksi as $key => $value) { $key++;
					// $List_print .= $key.". <a href='".base_url('produksi/print_req_mixing/'.$value['id'])."' target='_blank'>Print Req: ".date('d-M-y H:i:s', strtotime($value['created_date']))."</a><br>";
					$List_print .= "<span class='text-blue printSPKNew' style='cursor:pointer;font-size: 11px;' data-id='".$value['id']."' data-print_ke='".$value['print_ke']."'>".$key.". Print: ".date('d-M-y H:i:s', strtotime($value['created_date']))."</span><br>";
				}

				$update_get_warehouse = "<a href='".base_url('produksi/request_material/'.$row['kode_spk'])."' class='btn btn-sm btn-info' title='Request Material'><i class='fa fa-hand-pointer-o'></i></a>";
				// $print_spk_request = "<a href='".base_url('produksi/spk_mixing_request/'.$row['kode_spk'])."' target='_blank' class='btn btn-sm btn-info' title='Print'><i class='fa fa-print'></i></a>";
				$print_req_mixing = "<br>
									".$List_print."
									";
			}
			if($FLAG == 0){
				if($row['id_product'] != 'tanki'){
					$print_spk = "<a href='".base_url('produksi/spk_baru/'.$row['kode_spk'])."' target='_blank' class='btn btn-sm btn-info' title='Print SPK'><i class='fa fa-print'></i></a>";
				}
				$view_spk = "<button class='btn btn-sm btn-warning detail_spk' title='Detail SPK' data-kode_spk='".$row['kode_spk']."'><i class='fa fa-eye'></i></button>";
			}

			$get_split_ipp = $this->db->select('no_ipp, id_milik, product_code, product, id_product, no_spk')->get_where('production_spk',array('kode_spk'=>$row['kode_spk']))->result_array();
			$ArrNo_IPP = [];
			$ArrNo_SPK = [];
			$ArrNo_PRODUCT = [];
			foreach ($get_split_ipp as $key => $value) {
				$no_spk_list = $this->db->select('no_spk')->get_where('production_detail',array('id_milik'=>$value['id_milik'],'kode_spk'=>$row['kode_spk']))->result();
				$no_spk = (!empty($no_spk_list))?$no_spk_list[0]->no_spk:'not set';
				
				$IMPLODE = explode('.', $value['product_code']);

				if($value['id_product'] == 'tanki'){
					$product = "<span class='text-bold text-red'>TANKI - ".strtoupper($value['product'])."</span> (".$this->tanki_model->get_spec($value['id_milik']).")";
				}
				else{
					$product = strtoupper($value['product']).' ('.spec_bq2($value['id_milik']).')';
				}

				if($value['id_product'] == 'deadstok'){
					$product = strtoupper($value['product']);
					$no_spk = strtoupper($value['no_spk']);
				}

				$ArrNo_IPP[] = $value['no_ipp'];
				$ArrNo_PRODUCT[] = $product;
				$ArrNo_SPK[] = $no_spk.' / '.$IMPLODE[0];
			}
			// print_r($ArrGroup); exit;
			$explode_ipp = implode('<br>',$ArrNo_IPP);
			$explode_spk = implode('<br>',$ArrNo_SPK);
			$explode_product = implode('<br>',$ArrNo_PRODUCT);

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$explode_product."</div>";
			$nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$explode_spk."</div>";
			$nestedData[]	= "<div align='left'>".$Status1."</div>";
			$nestedData[]	= "<div align='left'>".$Status2."</div>";
			$nm_lengkap = (!empty(get_detail_user()[strtolower($row['created_by'])]['nm_lengkap']))?get_detail_user()[strtolower($row['created_by'])]['nm_lengkap']:'';
			$nestedData[]	= "<div align='left'>".strtoupper($nm_lengkap)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
			$nestedData[]	= "<div align='center' class='text-bold text-primary'>".number_format($row['qty_spk'])."</div>";
			$nestedData[]	= "<div align='center' class='text-bold text-success'>".number_format($QTY_INPUT)."</div>";
			$nestedData[]	= "<div align='center' class='text-bold text-red'>".number_format($row['qty_spk']-$QTY_INPUT)."</div>";
			$nestedData[]	= "<div align='center' class='text-bold text-purple'>".number_format($CLOSING_SUM)."</div>";
			$nestedData[]	= "<div align='left'>
									".$view_spk."
									".$print_spk."
							 		".$update_spk_1."
							 		".$update_spk_2."
							 		".$update_get_warehouse."
							 		".$print_spk_mixing."
							 		".$print_spk_request."
							 		".$print_req_mixing."
									".$history_print."
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

	public function query_data_aktual($status, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				SUM(a.qty) AS qty_spk,
				SUM(a.qty_input) AS qty_input
			FROM
				production_spk a,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND a.status_id = '1'
				AND (
					a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			GROUP BY
				a.kode_spk
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id',
			2 => 'no_ipp',
			3 => 'product_code'
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	// BARU
	public function show_material_input(){
		$data	= $this->input->post();

		$data_html = "";
		$kode_spk	= $data['kode_spk'];
		$hist_produksi = $data['hist_produksi'];
		$detail_input	= $data['detail_input'];
		$id_gudang	= $data['id_gudang'];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($detail_input as $key => $value) {
			if($value['qty'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty_all'];
			}
		}

		if(!empty($where_in_ID)){
			$get_detail_spk = $this->db
								->select('*')
								->from('production_spk')
								->where('kode_spk', $kode_spk)
								->where_in('id',$where_in_ID)
								->get()
								->result_array();

			foreach ($get_detail_spk as $key => $value) {
				$WHERE_IN_KEY[] = $value['id_milik'];
				$WHERE_KEY[] 	= $value['id'];
			}

			$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
			//UTAMA
			$get_liner_utama = $this->db->query("(SELECT
													id_detail,
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail 
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'LINER THIKNESS / CB' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
													id_detail 
												)
												UNION
												(
													SELECT
													id_detail,
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail 
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'GLASS' 
													AND id_material <> 'MTL-1903000'
												ORDER BY
													id_detail
												)
												UNION
												(
													SELECT
													id_detail,
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail 
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'RESIN AND ADD' 
													AND id_material <> 'MTL-1903000'
													AND id_category = 'TYP-0002' 
												ORDER BY
													id_detail
												)")->result_array();
			$get_str_n1_utama = $this->db->query("(SELECT
												id_detail,
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 1' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)")->result_array();
			$get_str_n2_utama = $this->db->query("(SELECT
												id_detail,
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)")->result_array();
			$get_structure_utama = $this->db->query("(SELECT
											id_detail,
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR THICKNESS' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
										)")->result_array();
			$get_external_utama = $this->db->query("(SELECT
												id_detail,
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)")->result_array();

			$kode_trans = (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:0;
			if($get_detail_spk[0]['id_product'] != 'tanki'){
				if($get_detail_spk[0]['id_product'] != 'deadstok'){
					$data = array(
						'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_topcoat_utama' 	=> [],
						'kode_spk' 				=> $kode_spk,
						'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
						'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
						'id_spk' 				=> $WHERE_KEY,
						'id_gudang_from' 		=> $id_gudang,
						'hist_produksi'			=> $hist_produksi
					);
				}
				else{
					$kode_deadstok 		= $get_detail_spk[0]['product_code_cut'];
					$qty 				= $get_detail_spk[0]['qty'];
					$get_liner_utama 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,0);
					$get_structure_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,0);
					$get_external_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,0);

					$get_liner_utama_resin 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,1);
					$get_structure_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,1);
					$get_external_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,1);

					$get_liner_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','plus',null,null);
					$get_structure_utama_mixing = getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','plus',null,null);
					$get_external_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','plus',null,null);
					$get_topcoat_mixing 		= getEstimasiDeadstok($kode_deadstok,$qty,'TOPCOAT','plus',null,null);

					$data = array(
						'get_liner_utama' 		=> $get_liner_utama,
						'get_str_n1_utama' 		=> [],
						'get_str_n2_utama' 		=> [],
						'get_structure_utama' 	=> $get_structure_utama,
						'get_external_utama' 	=> $get_external_utama,
						'get_topcoat_utama' 	=> [],
						'kode_spk' 				=> $kode_spk,
						'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
						'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
						'id_spk' 				=> $WHERE_KEY,
						'id_gudang_from' 		=> $id_gudang,
						'hist_produksi'			=> $hist_produksi
					);
				}
			}
			else{
				$get_liner_utama = $this->db->query("	SELECT
														a.id_det AS id_milik,
														a.id_material,
														b.nm_material,
														b.id_category,
														b.nm_category,
														a.berat 
													FROM
														est_material_tanki a
														LEFT JOIN raw_materials b ON a.id_material = b.id_material
													WHERE
														a.id_det IN ".$IMPLODE_IN." 
														AND b.id_category IS NOT NULL
														AND (a.layer = 'liner' OR a.layer = 'primer')
														AND a.spk_pemisah = '1'
													")->result_array();
				$get_structure_utama = $this->db->query("	SELECT
													a.id_det AS id_milik,
													a.id_material,
													b.nm_material,
													b.id_category,
													b.nm_category,
													a.berat 
												FROM
													est_material_tanki a
													LEFT JOIN raw_materials b ON a.id_material = b.id_material
												WHERE
													a.id_det IN ".$IMPLODE_IN." 
													AND b.id_category IS NOT NULL
													AND a.layer = 'structure'
													AND a.spk_pemisah = '1'
												")->result_array();

				$data = array(
					'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_utama, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_topcoat_utama' 	=> [],
					'kode_spk' 				=> $kode_spk,
					'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
					'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
					'id_spk' 				=> $WHERE_KEY,
					'id_gudang_from' 		=> $id_gudang,
					'hist_produksi'			=> $hist_produksi
				);
			}
			
			$data_html = $this->load->view('Produksi/input_material_1', $data, TRUE);
		}
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_material_input2(){
		$data	= $this->input->post();

		$data_html = "";
		$kode_spk	= $data['kode_spk'];
		$hist_produksi = $data['hist_produksi'];
		$detail_input	= $data['detail_input'];
		$id_gudang	= $data['id_gudang'];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($detail_input as $key => $value) {
			if($value['qty'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty_all'];
			}
		}

		if(!empty($where_in_ID)){
			$get_detail_spk = $this->db
								->select('*')
								->from('production_spk')
								->where('kode_spk', $kode_spk)
								->where_in('id',$where_in_ID)
								->get()
								->result_array();

			foreach ($get_detail_spk as $key => $value) {
				$WHERE_IN_KEY[] = $value['id_milik'];
				$WHERE_KEY[] 	= $value['id'];
			}

			$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
			// MIXING
			$get_liner_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001' 
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'RESIN AND ADD' 
												AND id_material <> 'MTL-1903000'
											ORDER BY
											id_detail 
										)")->result_array();
			$get_structure_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'STRUKTUR THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
												id_detail 
											)")->result_array();
			$get_external_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'
											GROUP BY
												id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'EXTERNAL LAYER THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001'
												ORDER BY
												id_detail 
											)")->result_array();
			$get_topcoat_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											MAX(last_cost) AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'TOPCOAT' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
										)")->result_array();
			$get_str_n1_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001'
									ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
											id_detail 
									)")->result_array();
			$get_str_n2_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
												id_detail 
											)")->result_array();
			$GET_KODE_TRANS = get_kode_trans_by_key_time();

			$kode_trans = (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:0;
			if($get_detail_spk[0]['id_product'] != 'tanki'){
				if($get_detail_spk[0]['id_product'] != 'deadstok'){
					$data = array(
						'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'kode_spk' 				=> $kode_spk,
						'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
						'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
						'id_spk' 				=> $WHERE_KEY,
						'id_gudang_from' 		=> $id_gudang,
						'hist_produksi'			=> $hist_produksi
					);
				}
				else{
					$kode_deadstok 		= $get_detail_spk[0]['product_code_cut'];
					$qty 				= $get_detail_spk[0]['qty'];
					$get_liner_utama 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,0);
					$get_structure_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,0);
					$get_external_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,0);

					$get_liner_utama_resin 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,1);
					$get_structure_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,1);
					$get_external_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,1);

					$get_liner_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','plus',null,null);
					$get_structure_utama_mixing = getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','plus',null,null);
					$get_external_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','plus',null,null);
					$get_topcoat_mixing 		= getEstimasiDeadstok($kode_deadstok,$qty,'TOPCOAT','plus',null,null);

					$data = array(
						'get_liner_utama' 		=> array_merge($get_liner_utama_resin,$get_liner_utama_mixing),
						'get_str_n1_utama' 		=> [],
						'get_str_n2_utama' 		=> [],
						'get_structure_utama' 	=> array_merge($get_structure_utama_resin,$get_structure_utama_mixing),
						'get_external_utama' 	=> array_merge($get_external_utama_resin,$get_external_utama_mixing),
						'get_topcoat_utama' 	=> $get_topcoat_mixing,
						'kode_spk' 				=> $kode_spk,
						'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
						'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
						'id_spk' 				=> $WHERE_KEY,
						'id_gudang_from' 		=> $id_gudang,
						'hist_produksi'			=> $hist_produksi
					);
				}
			}
			else{
				$get_liner_mix = $this->db->query("	SELECT
														a.id_det AS id_milik,
														a.id_material,
														b.nm_material,
														b.id_category,
														b.nm_category,
														a.berat 
													FROM
														est_material_tanki a
														LEFT JOIN raw_materials b ON a.id_material = b.id_material
													WHERE
														a.id_det IN ".$IMPLODE_IN." 
														AND (a.layer = 'liner' OR a.layer = 'primer')
														AND (a.spk_pemisah = '2' OR a.id_tipe='14')
													")->result_array();
				$get_structure_mix = $this->db->query("	SELECT
													a.id_det AS id_milik,
													a.id_material,
													b.nm_material,
													b.id_category,
													b.nm_category,
													a.berat 
												FROM
													est_material_tanki a
													LEFT JOIN raw_materials b ON a.id_material = b.id_material
												WHERE
													a.id_det IN ".$IMPLODE_IN." 
													AND a.layer = 'structure'
													AND (a.spk_pemisah = '2' OR a.id_tipe='14')
												")->result_array();
				$get_topcoat_mix = $this->db->query("	SELECT
												a.id_det AS id_milik,
												a.id_material,
												b.nm_material,
												b.id_category,
												b.nm_category,
												a.berat 
											FROM
												est_material_tanki a
												LEFT JOIN raw_materials b ON a.id_material = b.id_material
											WHERE
												a.id_det IN ".$IMPLODE_IN." 
												AND a.layer = 'topcoat'
												AND (a.spk_pemisah = '2' OR a.id_tipe='14')
											")->result_array();

				$data = array(
					'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'kode_spk' 				=> $kode_spk,
					'get_percent' 			=> get_persent_by_subgudang_filter($kode_trans),
					'kode_trans' 			=> (!empty($GET_KODE_TRANS[$hist_produksi]['kode_trans']))?$GET_KODE_TRANS[$hist_produksi]['kode_trans']:'',
					'id_spk' 				=> $WHERE_KEY,
					'id_gudang_from' 		=> $id_gudang,
					'hist_produksi'			=> $hist_produksi
				);
			}
			$data_html = $this->load->view('Produksi/input_material_2', $data, TRUE);
		}
		// echo 'Ini'; exit;
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_product_input(){
		$data	= $this->input->post();

		$kode_spk	= $data['kode_spk'];
		$hist_produksi = $data['hist_produksi'];
		$tanda_mixing	= (!empty($data['tanda_mixing']))?$data['tanda_mixing']:'';

		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		$get_detail_spk2 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang, a.upload_eng_change, c.no_spk AS no_spk2, c.adjustment_type AS typeTanki, c.no_so')
							->from('production_spk_parsial a')	
							->join('production_spk b','a.id_spk = b.id')
							->join('warehouse_adjustment c',"a.kode_spk = c.kode_spk AND c.no_ipp = 'resin mixing' AND c.status_id='1'")
							->where('a.kode_spk',$kode_spk)
							->where('a.created_date',$hist_produksi)
							->where('c.created_date',$hist_produksi)
							->where('a.spk','1')
							->get()
							->result_array();
		$get_detail_spk3 = $this->db
							->select('production_date AS tgl_mulai, finish_production_date AS tgl_selesai, id')
							->from('production_detail')
							->where('kode_spk',$kode_spk)
							->where('print_merge_date',$hist_produksi)
							->get()
							->result_array();
		$get_detail_spk4 = $this->db
							->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang, a.upload_eng_change')
							->from('production_spk_parsial a')	
							->join('production_spk b','a.id_spk = b.id')
							->where('a.kode_spk',$kode_spk)
							->where('a.created_date',$hist_produksi)
							->where('a.spk','2')
							->get()
							->result_array();

		$tanggal_produksi = (!empty($get_detail_spk3[0]['tgl_selesai']))?$get_detail_spk3[0]['tgl_selesai']:'';
		$tanggal_start = (!empty($get_detail_spk3[0]['tgl_mulai']))?date('d-M-Y',strtotime($get_detail_spk3[0]['tgl_mulai'])):'';
		$id_milik2 = (!empty($get_detail_spk3[0]['id']))?$get_detail_spk3[0]['id']:'';
		$id_gudang = (!empty($get_detail_spk2[0]['id_gudang']))?$get_detail_spk2[0]['id_gudang']:'0';
		$upload_eng_change = (!empty($get_detail_spk4[0]['upload_eng_change']))?base_url('assets/file/produksi/'.$get_detail_spk4[0]['upload_eng_change']):'';
		$upload_eng_change1 = (!empty($get_detail_spk2[0]['upload_eng_change']))?base_url('assets/file/produksi/'.$get_detail_spk2[0]['upload_eng_change']):'';

		$data = array(
			'id_milik2' 		=> $id_milik2,
			'kode_spk' 			=> $kode_spk,
			'get_detail_spk'	=> $get_detail_spk,
			'get_detail_spk2'	=> $get_detail_spk2,
			'hist_produksi'		=> $hist_produksi,
			'tanki_model' 		=> $this->tanki_model,
		);
		
		$data_html = '';
		if($tanda_mixing == '1'){
			$data_html = $this->load->view('Produksi/input_product_1', $data, TRUE);
		}
		
		if($tanda_mixing == '2' AND $hist_produksi != '0'){
			$data_html = $this->load->view('Produksi/input_product_1', $data, TRUE);
		}
		
		
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html,
			'upload_eng_change2'	=> $upload_eng_change,
			'upload_eng_change1'	=> $upload_eng_change1,
			'tanggal_produksi'	=> $tanggal_produksi,
			'tanggal_start'	=> $tanggal_start,
			'id_gudang'	=> $id_gudang
		);
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_1_new(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];
		
		// print_r($detail_input);
		// exit;
		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				// if($hist_produksi == '0'){
					// $ARrUpdateSPK[$key]['id'] 				= $value['id'];
					// $ARrUpdateSPK[$key]['qty_input'] 		= get_name('production_spk','qty_input','id',$value['id']) + $QTY;
				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
					$qUpdate 	= $this->db->query("UPDATE 
												production_detail
											SET 
												upload_real='Y',
												finish_production_date='$date_produksi',
												upload_by='$username',
												upload_date='$dateCreated'
											WHERE 
												id_milik='".$value['id_milik']."'
												AND kode_spk= '".$kode_spk."'
												AND print_merge_date= '".$dateCreated."'
											ORDER BY 
												id ASC 
											LIMIT $QTY");
				}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Update! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
				// }
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						if($value['product'] == 'field joint' OR $value['product'] == 'branch joint' OR $value['product'] == 'shop joint'){
							$DETAIL_NAME = 'GLASS';
						}
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$TERPAKAI 	= (!empty($value2['terpakai']))?str_replace(',','',$value2['terpakai']):0;
									$KEBUTUHAN 	= (!empty($value2['kebutuhan']))?str_replace(',','',$value2['kebutuhan']):0;

									$total_act  = $TERPAKAI;
									if($KEBUTUHAN > 0 AND $total_est > 0){
										$total_act 	= ($total_est / $KEBUTUHAN) * $TERPAKAI;
									}

									$unit_act = 0;
									if($total_act > 0 AND $QTY_INP > 0){
										$unit_act 	= $total_act / $QTY_INP;
									}

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0002','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 1;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_spk'] 				= $value['id'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_date'] 		= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flag produksi input
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi utama '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_1_new_tanki(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
				
				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
					$qUpdate 	= $this->db->query("UPDATE 
											production_detail
										SET 
											upload_real='Y',
											finish_production_date='$date_produksi',
											upload_by='$username',
											upload_date='$dateCreated'
										WHERE 
											id_milik='".$value['id_milik']."'
											AND kode_spk= '".$kode_spk."'
											AND print_merge_date= '".$dateCreated."'
										ORDER BY 
											id ASC 
										LIMIT $QTY");
										}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Closing! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						$DETAIL_WHERE = 'liner';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
						$DETAIL_WHERE = 'structure';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
						$DETAIL_WHERE = 'topcoat';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('est_material_tanki a', array('a.id_det'=>$value['id_milik'],'a.spk_pemisah'=>'1','layer'=>$DETAIL_WHERE))
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'tanki';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flag produksi input
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi utama '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_1_new_deadstok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					$no_spk			= $value['no_spk'];
					$kode_estimasi	= $value['product_code_cut'];

					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.last_cost AS berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('deadstok_estimasi a', array('a.kode'=>$kode_estimasi,'a.detail_name'=>$DETAIL_NAME))
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = null;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'deadstok';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrUpdate);
		// print_r($ArrJurnal);
		// exit;

		$this->db->trans_start();
			//update flag produksi input
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			if(!empty($ArrStockInsert)){
				$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			}
			if(!empty($ArrHistInsert)){
				$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			}

			if(!empty($ArrStock2)){
				$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			}
			if(!empty($ArrHist2)){
				$this->db->insert_batch('warehouse_history', $ArrHist2);
			}

			if(!empty($ArrStockInsert2)){
				$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			}
			if(!empty($ArrHistInsert2)){
				$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			}
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi utama '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_2_new(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $hist_produksi;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 2;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				// if($hist_produksi == '0'){
					// $ARrUpdateSPK[$key]['id'] 				= $value['id'];
					// $ARrUpdateSPK[$key]['qty_input'] 		= get_name('production_spk','qty_input','id',$value['id']) + $QTY;
				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
					$qUpdate 	= $this->db->query("UPDATE 
												production_detail
											SET 
												upload_real2='Y',
												finish_production_date='$date_produksi',
												upload_by2='$username',
												upload_date2='$dateCreated'
											WHERE 
												id_milik='".$value['id_milik']."'
												AND kode_spk= '".$kode_spk."'
												AND print_merge_date= '".$dateCreated."'
											ORDER BY 
												id ASC 
											LIMIT $QTY");
				}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Update! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
				// }
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						if($value['product'] == 'field joint' OR $value['product'] == 'branch joint' OR $value['product'] == 'shop joint'){
							$DETAIL_NAME = 'RESIN AND ADD';
						}
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date2'=>$dateCreated))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db->select('MAX(id_detail) AS id_detail, id_milik, id_product, id_material, MAX(last_cost) AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if($value['product'] == 'field joint' OR $value['product'] == 'branch joint' OR $value['product'] == 'shop joint'){
							$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						}
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;
									$PERSEN = str_replace(',','',$value2['persen']);

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['persen'] 		= $PERSEN;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 2;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 2;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;
									$PERSEN = str_replace(',','',$value2['persen']);

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['persen'] 		= $PERSEN;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 2;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_spk'] 				= $value['id'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_date'] 		= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 2;
								}
							}
						}
						//ADD
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_add', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;
									$PERSEN = str_replace(',','',$value2['persen']);

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['persen'] 		= $PERSEN;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['spk'] 	= 2;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['id_spk'] 				= $value['id'];
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualAdd[$key.$key2.$key3.$nomor]['updated_date'] 		= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 2;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip2)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip2)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip2)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip2)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flah produksi spk group
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}

			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
			if(!empty($ArrAktualAdd)){
				$this->db->insert_batch('tmp_production_real_detail_add',$ArrAktualAdd);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_2_new_tanki(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $hist_produksi;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 2;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
					$qUpdate 	= $this->db->query("UPDATE 
											production_detail
										SET 
											upload_real2='Y',
											finish_production_date='$date_produksi',
											upload_by2='$username',
											upload_date2='$dateCreated'
										WHERE 
											id_milik='".$value['id_milik']."'
											AND kode_spk= '".$kode_spk."'
											AND print_merge_date= '".$dateCreated."'
										ORDER BY 
											id ASC 
										LIMIT $QTY");
										}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Closing! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						$DETAIL_WHERE = 'liner';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
						$DETAIL_WHERE = 'structure';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
						$DETAIL_WHERE = 'topcoat';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					// exit;
					$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date2'=>$dateCreated))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					//LINER
					foreach ($detailX as $key2 => $value2) {
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->where("(a.spk_pemisah='2' OR a.id_tipe=14)")
												->get_where('est_material_tanki a', array('a.id_det'=>$value['id_milik'],'layer'=>$DETAIL_WHERE))
												->result_array();
						// print_r($get_liner);
						// exit;
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;
									$PERSEN = str_replace(',','',$value2['persen']);

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['persen'] 		= $PERSEN;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'tanki';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 2;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 2;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip2)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip2)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip2)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip2)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flah produksi spk group
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}

			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
			if(!empty($ArrAktualAdd)){
				$this->db->insert_batch('tmp_production_real_detail_add',$ArrAktualAdd);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_2_new_deadstok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $hist_produksi;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 2;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					$no_spk			= $value['no_spk'];
					$kode_estimasi	= $value['product_code_cut'];

					foreach ($detailX as $key2 => $value2) {
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.last_cost AS berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('deadstok_estimasi a', array('a.kode'=>$kode_estimasi,'a.detail_name'=>$DETAIL_NAME))
												->result_array();
						// print_r($get_liner);
						// exit;
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;
									$PERSEN = str_replace(',','',$value2['persen']);

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['persen'] 		= $PERSEN;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = null;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'deadstok';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 2;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 2;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip2)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip2)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip2)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip2)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip2)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrUpdate);
		// print_r($ArrJurnal);
		// exit;

		$this->db->trans_start();
			//update flah produksi spk group
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}

			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}
			//update stock
			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			//end update stock
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'2'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
			if(!empty($ArrAktualAdd)){
				$this->db->insert_batch('tmp_production_real_detail_add',$ArrAktualAdd);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Input aktual spk produksi mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function request_material(){
		$kode_spk	= $this->uri->segment(3);
		$get_detail_spk = $this->db->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$getWherehouse = $this->db->get_where('warehouse', array('category'=>'produksi'))->result_array();
		$getWherehouse2 = $this->db->get_where('warehouse', array('category'=>'subgudang'))->result_array();
		$hist_produksi = $this->db->group_by('created_date')->get_where('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'))->result_array();

		$link_submit = 'save_request_material';
		if($get_detail_spk[0]['id_product'] == 'tanki'){
			$link_submit = 'save_request_material_tanki';
		}
		if($get_detail_spk[0]['id_product'] == 'deadstok'){
			$link_submit = 'save_request_material_deadstok';
		}

		$data = array(
			'title'			=> 'Request Resin Mixing',
			'action'		=> 'index',
			'warehouse' 	=> $getWherehouse,
			'warehouse2' 		=> $getWherehouse2,
			'get_detail_spk' 	=> $get_detail_spk,
			'hist_produksi' 	=> $hist_produksi,
			'GET_SPEC_TANK' 	=> $this->tanki_model->get_spec_check($get_detail_spk[0]['no_ipp']),
			'kode_spk' 			=> $kode_spk,
			'link_submit'		=> $link_submit
		);
		
		$this->load->view('Produksi/request_material', $data);
	}

	public function save_request_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang_from = $data['id_gudang_from'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= $data['date_produksi'];
		$hist_produksi 	= $data['hist_produksi'];
		$Ym 			= date('ym');


		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRM".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRM".$Ym.$urut2;

		$detail_input	= $data['detail_input'];
		$dateCreated = $datetime;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_request_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 			= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				if($hist_produksi == '0'){
					$ARrUpdateSPK[$key]['id'] 				= $value['id'];
					$ARrUpdateSPK[$key]['qty_input'] 		= get_name('production_spk','qty_input','id',$value['id']) + $QTY;
					//sebab adjustment beda error input material warehouse
					$qUpdate 	= $this->db->query("UPDATE 
												production_detail
											SET 
												sts_print_spk='Y',
												-- upload_by='$username',
												-- upload_date='$datetime',
												production_date='$date_produksi',
												print_merge_date='$datetime',
												print_merge2_date='$datetime'
											WHERE 
												id_milik='".$value['id_milik']."'
												AND kode_spk= '".$kode_spk."'
												AND upload_date IS NULL
												AND sts_print_spk IS NULL
												AND upload_real = 'N'
											ORDER BY 
												id ASC 
											LIMIT $QTY");
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrDeatilAdj = [];
		$ArrDeatilAdj2 = [];

		$nomor = 0;
		$nomor2 = 0;
		$SUM_AKTUAL = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id, id_category')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;

					$product		= (!empty($get_produksi[0]->id_category))?$get_produksi[0]->id_category:0;
					
					if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
						$DETAIL_NAME = 'RESIN AND ADD';
					}
					//LINER
					foreach ($detailX as $key2 => $value2) { $nomor2++;
						//RESIN
						if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
							$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, nm_material, id_category, nm_category, last_cost AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						}
						else{
							$get_liner 		= $this->db->select('MAX(id_detail) AS id_detail, id_milik, id_product, id_material, nm_material, id_category, nm_category, MAX(last_cost) AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						}
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									$qty_kebutuhan = str_replace(',','',$value2['kebutuhan']);
									if($qty_kebutuhan > 0){
										$total_act 	= ($total_est / $qty_kebutuhan) * str_replace(',','',$value2['terpakai']);
									}

									
									$SUM_AKTUAL += $qty_kebutuhan;
									$unit_act 	= $total_act / $QTY_INP;
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									//REQUEST
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 		= $kode_trans;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material_req'] 	= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 		= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 		= $value3['nm_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 		= $value3['id_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 		= $value3['nm_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 		= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 			= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 		= $DETAIL_NAME;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_request'] 		= $value2['keterangan'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_req_pro'] 		= 'req resin mixing';
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, nm_material, id_category, nm_category, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									$qty_kebutuhan = str_replace(',','',$value2['kebutuhan']);
									if($qty_kebutuhan > 0){
										$total_act 	= ($total_est / $qty_kebutuhan) * str_replace(',','',$value2['terpakai']);
									}
									$SUM_AKTUAL += $qty_kebutuhan;
									$unit_act 	= $total_act / $QTY_INP;
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									//REQUEST
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 		= $kode_trans;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material_req'] 	= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 		= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 		= $value3['nm_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 		= $value3['id_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 		= $value3['nm_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 		= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 			= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 		= $DETAIL_NAME;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_request'] 		= $value2['keterangan'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_req_pro'] 		= 'req resin mixing';
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
								}
							}
						}
						//ADD
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, nm_material, id_category, nm_category, last_cost AS berat')->get_where('so_component_detail_add', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									$qty_kebutuhan = str_replace(',','',$value2['kebutuhan']);
									if($qty_kebutuhan > 0){
										$total_act 	= ($total_est / $qty_kebutuhan) * str_replace(',','',$value2['terpakai']);
									}
									$SUM_AKTUAL += $qty_kebutuhan;
									$unit_act 	= $total_act / $QTY_INP;
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									//REQUEST
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 		= $kode_trans;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material_req'] 	= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 		= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 		= $value3['nm_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 		= $value3['id_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 		= $value3['nm_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 		= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 			= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 		= $DETAIL_NAME;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_request'] 		= $value2['keterangan'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_req_pro'] 		= 'req resin mixing';
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
								}
							}
						}

						//New
						//REQUEST
						// $qty_req = str_replace(',','',$value2['kebutuhan']);
						// $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value2['actual_type']))->result();
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['kode_trans'] 		= $kode_trans;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['id_material_req'] 	= $value2['actual_type'];
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['id_material'] 		= $value2['actual_type'];
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['nm_material'] 		= $det_mat[0]->nm_material;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['id_category'] 		= $det_mat[0]->id_category;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['nm_category'] 		= $det_mat[0]->nm_category;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['qty_order'] 		= $qty_req;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['qty_oke'] 			= $qty_req;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['keterangan'] 		= $DETAIL_NAME;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['ket_request'] 		= $value2['keterangan'];
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['ket_req_pro'] 		= 'req resin mixing';
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['update_by'] 		= $username;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['update_date'] 		= $datetime;
						// $ArrDeatilAdj2[$key.$key2.$nomor2]['key_gudang'] 		= $nomor2;
					}
				}
			}
		}

		$nomor2 = 0;
		foreach ($ArrLooping as $valueX) {
			if(!empty($data[$valueX])){
				if($valueX == 'detail_liner'){
					$DETAIL_NAME = 'LINER THIKNESS / CB';
				}
				else if($valueX == 'detail_strn1'){
					$DETAIL_NAME = 'STRUKTUR NECK 1';
				}
				else if($valueX == 'detail_strn2'){
					$DETAIL_NAME = 'STRUKTUR NECK 2';
				}
				else if($valueX == 'detail_str'){
					$DETAIL_NAME = 'STRUKTUR THICKNESS';
				}
				else if($valueX == 'detail_ext'){
					$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
				}
				else if($valueX == 'detail_topcoat'){
					$DETAIL_NAME = 'TOPCOAT';
				}
				else{
					$DETAIL_NAME = 'RESIN AND ADD';
				}
				$detailX = $data[$valueX];
				//LINER
				foreach ($detailX as $key2 => $value2) { $nomor2++;
					
					//REQUEST
					$qty_req = str_replace(',','',$value2['kebutuhan']);
					$det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value2['actual_type']))->result();
					$ArrDeatilAdj2[$key.$key2.$nomor2]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_material_req'] 	= $value2['actual_type'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_material'] 		= $value2['actual_type'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['nm_material'] 		= $det_mat[0]->nm_material;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_category'] 		= $det_mat[0]->id_category;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['nm_category'] 		= $det_mat[0]->nm_category;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['qty_order'] 		= $qty_req;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['qty_oke'] 			= $qty_req;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['keterangan'] 		= $DETAIL_NAME;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['ket_request'] 		= $value2['keterangan'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['ket_req_pro'] 		= 'req resin mixing';
					$ArrDeatilAdj2[$key.$key2.$nomor2]['update_by'] 		= $username;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['update_date'] 		= $datetime;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['key_gudang'] 		= $nomor2;
				}
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'request produksi',
			'jumlah_mat' 		=> $SUM_AKTUAL,
			'no_ipp' 			=> 'resin mixing',
			'no_spk' 			=> NULL,
			'keterangan' 		=> NULL,
			'req_mixing' 		=> 'Y',
			'kode_spk' 			=> $kode_spk,
			'id_gudang_dari' 	=> $id_gudang_from,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang_from),
			'id_gudang_ke' 		=> $id_gudang,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang),
			'created_by' 		=> $username,
			'created_date' 		=> $datetime
		);

		// print_r($ArrDeatilAdj);
		// print_r($ArrInsertH);
		// print_r($ARrUpdateSPK);
		// print_r($ArrDeatilAdj2);
		// exit;

		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			if(!empty($ArrDeatilAdj2)){
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj2);
			}
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}

			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
			}
			else{
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',null);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',null);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
			}
			
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Request material resin & mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_request_material_tanki(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang_from = $data['id_gudang_from'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= $data['date_produksi'];
		$hist_produksi 	= $data['hist_produksi'];
		$Ym 			= date('ym');


		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRM".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRM".$Ym.$urut2;

		$detail_input	= $data['detail_input'];
		$dateCreated = $datetime;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_request_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				if($hist_produksi == '0'){
					$ARrUpdateSPK[$key]['id'] 				= $value['id'];
					$ARrUpdateSPK[$key]['qty_input'] 		= get_name('production_spk','qty_input','id',$value['id']) + $QTY;
					//sebab adjustment beda error input material warehouse
					$qUpdate 	= $this->db->query("UPDATE 
												production_detail
											SET 
												sts_print_spk='Y',
												-- upload_by='$username',
												-- upload_date='$datetime',
												production_date='$date_produksi',
												print_merge_date='$datetime',
												print_merge2_date='$datetime'
											WHERE 
												id_milik='".$value['id_milik']."'
												AND kode_spk= '".$kode_spk."'
												AND upload_date IS NULL
												AND sts_print_spk IS NULL
												AND upload_real = 'N'
											ORDER BY 
												id ASC 
											LIMIT $QTY");
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();
		
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrDeatilAdj = [];
		$ArrDeatilAdj2 = [];

		$nomor = 0;
		$SUM_AKTUAL = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						$DETAIL_WHERE = 'liner';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
						$DETAIL_WHERE = 'structure';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
						$DETAIL_WHERE = 'topcoat';
					}
					$detailX = $data[$valueX];
					// print_r($detailX);
					$get_produksi 	= $this->db->limit(1)->select('id, id_category,no_spk')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk']))->result();
					$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
					$no_spk		= (!empty($get_produksi[0]->no_spk))?$get_produksi[0]->no_spk:NULL;

					
					foreach ($detailX as $key2 => $value2) {
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.berat')
												->from('est_material_tanki a')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->where(array('a.id_det'=>$value['id_milik'],'layer'=>$DETAIL_WHERE))
												->group_start()
													->where('a.spk_pemisah', '2')
													->or_where('a.id_tipe', '14')
												->group_end()
												->get()
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									$qty_kebutuhan = str_replace(',','',$value2['kebutuhan']);
									if($qty_kebutuhan > 0){
										$total_act 	= ($total_est / $qty_kebutuhan) * str_replace(',','',$value2['terpakai']);
									}

									
									$SUM_AKTUAL += $qty_kebutuhan;
									$unit_act 	= $total_act / $QTY_INP;
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									//REQUEST
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 		= $kode_trans;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material_req'] 	= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 		= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 		= $value3['nm_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 		= $value3['id_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 		= $value3['nm_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 		= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 			= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 		= $DETAIL_NAME;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_request'] 		= $value2['keterangan'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_req_pro'] 		= 'req resin mixing';
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
								}
							}
						}
					}
				}
			}
		}

		$nomor2 = 0;
		foreach ($ArrLooping as $valueX) {
			if(!empty($data[$valueX])){
				$detailX = $data[$valueX];
				if($valueX == 'detail_liner'){
					$DETAIL_NAME = 'LINER THIKNESS / CB';
				}
				else if($valueX == 'detail_strn1'){
					$DETAIL_NAME = 'STRUKTUR NECK 1';
				}
				else if($valueX == 'detail_strn2'){
					$DETAIL_NAME = 'STRUKTUR NECK 2';
				}
				else if($valueX == 'detail_str'){
					$DETAIL_NAME = 'STRUKTUR THICKNESS';
				}
				else if($valueX == 'detail_ext'){
					$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
				}
				else if($valueX == 'detail_topcoat'){
					$DETAIL_NAME = 'TOPCOAT';
				}
				else{
					$DETAIL_NAME = 'RESIN AND ADD';
				}
				//LINER
				foreach ($detailX as $key2 => $value2) { $nomor2++;
					
					//REQUEST
					$qty_req = str_replace(',','',$value2['kebutuhan']);
					$det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value2['actual_type']))->result();
					$ArrDeatilAdj2[$key.$key2.$nomor2]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_material_req'] 	= $value2['actual_type'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_material'] 		= $value2['actual_type'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['nm_material'] 		= $det_mat[0]->nm_material;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['id_category'] 		= $det_mat[0]->id_category;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['nm_category'] 		= $det_mat[0]->nm_category;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['qty_order'] 		= $qty_req;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['qty_oke'] 			= $qty_req;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['keterangan'] 		= $DETAIL_NAME;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['ket_request'] 		= $value2['keterangan'];
					$ArrDeatilAdj2[$key.$key2.$nomor2]['ket_req_pro'] 		= 'req resin mixing';
					$ArrDeatilAdj2[$key.$key2.$nomor2]['update_by'] 		= $username;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['update_date'] 		= $datetime;
					$ArrDeatilAdj2[$key.$key2.$nomor2]['key_gudang'] 		= $nomor2;
				}
			}
		}

		$GET_DET_TANKI = $this->tanki_model->get_detail_ipp_tanki();

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'request produksi',
			'jumlah_mat' 		=> $SUM_AKTUAL,
			'no_ipp' 			=> 'resin mixing',
			'adjustment_type' 	=> 'tanki',
			'no_spk' 			=> $no_spk,
			'no_so' 			=> (!empty($GET_DET_TANKI[$value['no_ipp']]['no_so']))?$GET_DET_TANKI[$value['no_ipp']]['no_so']:NULL,
			'keterangan' 		=> $value['no_ipp'],
			'req_mixing' 		=> 'Y',
			'kode_spk' 			=> $kode_spk,
			'id_gudang_dari' 	=> $id_gudang_from,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang_from),
			'id_gudang_ke' 		=> $id_gudang,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang),
			'created_by' 		=> $username,
			'created_date' 		=> $datetime
		);

		// print_r($ArrDeatilAdj);
		// print_r($ArrInsertH);
		// print_r($ARrUpdateSPK);
		// print_r($ARrInsertSPK);
		// exit;

		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			if(!empty($ArrDeatilAdj2)){
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj2);
			}
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}

			if($hist_produksi != '0'){
			$this->db->where_in('id_spk',$ArrWhereIN_);
			$this->db->where('created_date',$hist_produksi);
			$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

			$this->db->where_in('id_spk',$ArrWhereIN_);
			$this->db->where('status_date',$hist_produksi);
			$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
			}
			
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Request material resin & mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_request_material_deadstok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang_from = $data['id_gudang_from'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= $data['date_produksi'];
		$hist_produksi 	= $data['hist_produksi'];
		$Ym 			= date('ym');

		// exit;
		//pengurutan kode
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRM".$Ym."%' ";
		$numrowMtr		= $this->db->query($srcMtr)->num_rows();
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRM".$Ym.$urut2;

		$detail_input	= $data['detail_input'];
		$dateCreated = $datetime;

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_request_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;

				if($hist_produksi == '0'){
					$ARrUpdateSPK[$key]['id'] 				= $value['id'];
					$ARrUpdateSPK[$key]['qty_input'] 		= get_name('production_spk','qty_input','id',$value['id']) + $QTY;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$no_spk			= (!empty($get_detail_spk[0]['no_spk']))?$get_detail_spk[0]['no_spk']:null;
		
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrDeatilAdj = [];

		$nomor = 0;
		$SUM_AKTUAL = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					$no_spk			= $value['no_spk'];
					$kode_estimasi	= $value['product_code_cut'];

					
					foreach ($detailX as $key2 => $value2) {
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.last_cost AS berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('deadstok_estimasi a', array('a.kode'=>$kode_estimasi,'a.detail_name'=>$DETAIL_NAME))
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;
									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									$qty_kebutuhan = (!empty($value2['kebutuhan']))?str_replace(',','',$value2['kebutuhan']):0;
									$qty_terpakai = (!empty($value2['terpakai']))?str_replace(',','',$value2['terpakai']):0;

									if($qty_kebutuhan > 0 AND $total_est > 0){
										$total_act 	= ($total_est / $qty_kebutuhan) * $qty_terpakai;
									}

									
									$SUM_AKTUAL += $qty_kebutuhan;
									$unit_act 	= $total_act / $QTY_INP;
									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 2;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_cost'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costby'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk2_costdate']	= $datetime;
									$ArrUpdate[$key.$key2.$key3.$nomor]['gudang2']	= $id_gudang;

									//REQUEST
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['kode_trans'] 		= $kode_trans;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material_req'] 	= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_material'] 		= $value3['id_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_material'] 		= $value3['nm_material'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['id_category'] 		= $value3['id_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['nm_category'] 		= $value3['nm_category'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_order'] 		= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['qty_oke'] 			= $qty_kebutuhan;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['keterangan'] 		= $DETAIL_NAME;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_request'] 		= $value2['keterangan'];
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['ket_req_pro'] 		= 'req resin mixing';
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_by'] 		= $username;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['update_date'] 		= $datetime;
									$ArrDeatilAdj[$key.$key2.$key3.$nomor]['key_gudang'] 		= $nomor;
								}
							}
						}
					}
				}
			}
		}

		$ArrInsertH = array(
			'kode_trans' 		=> $kode_trans,
			'category' 			=> 'request produksi',
			'jumlah_mat' 		=> $SUM_AKTUAL,
			'no_ipp' 			=> 'resin mixing',
			'adjustment_type' 	=> 'deadstok',
			'no_spk' 			=> $no_spk,
			'no_so' 			=> $value['product_code'],
			'keterangan' 		=> $value['no_ipp'],
			'req_mixing' 		=> 'Y',
			'kode_spk' 			=> $kode_spk,
			'id_gudang_dari' 	=> $id_gudang_from,
			'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang_from),
			'id_gudang_ke' 		=> $id_gudang,
			'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $id_gudang),
			'created_by' 		=> $username,
			'created_date' 		=> $datetime
		);

		// print_r($ArrDeatilAdj);
		// print_r($ArrInsertH);
		// print_r($ARrUpdateSPK);
		// print_r($ARrInsertSPK);
		// exit;

		$this->db->trans_start();
			$this->db->insert('warehouse_adjustment', $ArrInsertH);
			if(!empty($ArrDeatilAdj)){
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
			}
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}

			if($hist_produksi != '0'){
			$this->db->where_in('id_spk',$ArrWhereIN_);
			$this->db->where('created_date',$hist_produksi);
			$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'2'));

			$this->db->where_in('id_spk',$ArrWhereIN_);
			$this->db->where('status_date',$hist_produksi);
			$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'2'));
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);

			history('Request material resin & mixing '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function show_material_input_request(){
		$data	= $this->input->post();

		$data_html = "";
		$kode_spk	= $data['kode_spk'];
		$hist_produksi = $data['hist_produksi'];
		$detail_input	= $data['detail_input'];
		
		$where_in_ID = [];
		$WHERE_KEY_QTY_SAT = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($detail_input as $key => $value) {
			if($value['qty'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY_SAT[] = $value['qty'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty_all'];
			}
		}

		if(!empty($where_in_ID)){
			$get_detail_spk = $this->db
								->select('*')
								->from('production_spk')
								->where('kode_spk', $kode_spk)
								->where_in('id',$where_in_ID)
								->get()
								->result_array();

			foreach ($get_detail_spk as $key => $value) {
				$WHERE_IN_KEY[] = $value['id_milik'];
				$WHERE_KEY[] 	= $value['id'];
			}

			$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
			// MIXING
			$get_liner_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001' 
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001'
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001'
											ORDER BY
											id_detail 
										)
										UNION
										(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'RESIN AND ADD' 
											AND id_material <> 'MTL-1903000'
										ORDER BY
											id_detail DESC
										)")->result_array();
			$get_structure_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'STRUKTUR THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
												id_detail 
											)")->result_array();
			$get_external_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'
											GROUP BY
												id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'EXTERNAL LAYER THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
												id_detail 
											)")->result_array();
			$get_topcoat_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											MAX(last_cost) AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'TOPCOAT' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
										)")->result_array();
			$get_str_n1_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
									ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
									)")->result_array();
			$get_str_n2_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)")->result_array();
			
			
			if($get_detail_spk[0]['id_product'] != 'tanki'){
				if($get_detail_spk[0]['id_product'] != 'deadstok'){
					$data = array(
						'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
						'kode_spk' 				=> $kode_spk,
						'id_spk' 				=> $WHERE_KEY,
						'hist_produksi'			=> $hist_produksi
					);
				}
				else{
					$kode_deadstok 			= $get_detail_spk[0]['product_code_cut'];
					$qty 					= $WHERE_KEY_QTY_SAT[0];
					$get_liner_utama 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,0);
					$get_structure_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,0);
					$get_external_utama 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,0);

					$get_liner_utama_resin 		= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','utama',1,1);
					$get_structure_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','utama',1,1);
					$get_external_utama_resin 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','utama',1,1);

					$get_liner_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'LINER THIKNESS / CB','plus',null,null);
					$get_structure_utama_mixing = getEstimasiDeadstok($kode_deadstok,$qty,'STRUKTUR THICKNESS','plus',null,null);
					$get_external_utama_mixing 	= getEstimasiDeadstok($kode_deadstok,$qty,'EXTERNAL LAYER THICKNESS','plus',null,null);
					$get_topcoat_mixing 		= getEstimasiDeadstok($kode_deadstok,$qty,'TOPCOAT','plus',null,null);

					$data = array(
						'get_liner_utama' 		=> array_merge($get_liner_utama_resin,$get_liner_utama_mixing),
						'get_str_n1_utama' 		=> [],
						'get_str_n2_utama' 		=> [],
						'get_structure_utama' 	=> array_merge($get_structure_utama_resin,$get_structure_utama_mixing),
						'get_external_utama' 	=> array_merge($get_external_utama_resin,$get_external_utama_mixing),
						'get_topcoat_utama' 	=> $get_topcoat_mixing,
						'kode_spk' 				=> $kode_spk,
						'id_spk' 				=> $WHERE_KEY,
						'hist_produksi'			=> $hist_produksi
					);
				}
				
				$data_html = $this->load->view('Produksi/input_material_request', $data, TRUE);
			}
			else{

				$GET_DETSPEC_TANKI     	= $this->tanki_model->get_detail_tanki($get_detail_spk[0]['id_milik']);
				$qty                 	= $get_detail_spk[0]['qty'];
				$qty_est_tanki         	= (!empty($GET_DETSPEC_TANKI['qty']))?$GET_DETSPEC_TANKI['qty']:0;

				$get_liner_mix = $this->db->query("	SELECT
														a.id_det AS id_milik,
														a.id_material,
														b.nm_material,
														b.id_category,
														b.nm_category,
														a.berat 
													FROM
														est_material_tanki a
														LEFT JOIN raw_materials b ON a.id_material = b.id_material
													WHERE
														a.id_det IN ".$IMPLODE_IN." 
														AND (a.layer = 'liner' OR a.layer = 'primer')
														AND (a.spk_pemisah = '2' OR a.id_tipe='14')
													")->result_array();
				$get_structure_mix = $this->db->query("	SELECT
													a.id_det AS id_milik,
													a.id_material,
													b.nm_material,
													b.id_category,
													b.nm_category,
													a.berat 
												FROM
													est_material_tanki a
													LEFT JOIN raw_materials b ON a.id_material = b.id_material
												WHERE
													a.id_det IN ".$IMPLODE_IN." 
													AND a.layer = 'structure'
													AND (a.spk_pemisah = '2' OR a.id_tipe='14')
												")->result_array();
				$get_topcoat_mix = $this->db->query("	SELECT
												a.id_det AS id_milik,
												a.id_material,
												b.nm_material,
												b.id_category,
												b.nm_category,
												a.berat 
											FROM
												est_material_tanki a
												LEFT JOIN raw_materials b ON a.id_material = b.id_material
											WHERE
												a.id_det IN ".$IMPLODE_IN." 
												AND a.layer = 'topcoat'
												AND (a.spk_pemisah = '2' OR a.id_tipe='14')
											")->result_array();
				$data = array(
					'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					// 'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
					'kode_spk' 				=> $kode_spk,
					'id_spk' 				=> $WHERE_KEY,
					'hist_produksi'			=> $hist_produksi,
					'qty'					=> $qty,
					'qty_est_tanki'			=> $qty_est_tanki
				);
				
				$data_html = $this->load->view('Produksi/input_material_request_tanki', $data, TRUE);
			}
		}
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_material_input2_request_22_08_13(){
		$data	= $this->input->post();

		$data_html = "";
		$kode_spk	= $data['kode_spk'];
		$kode_trans	= $data['kode_trans'];
		$hist_produksi = $data['hist_produksi'];
		$detail_input	= $data['detail_input'];

		$get_material_add = $this->db->get_where('production_spk_add',array('kode_spk'=>$kode_spk,'kode_trans'=>$kode_trans,'actual_type <>'=>'0'))->result_array();
		$ArrMaterialAdd = [];
		foreach ($get_material_add as $key => $value) {
			$ArrMaterialAdd[$value['layer']][$key] = $value;
		}
		// print_r($ArrMaterialAdd);
		// exit;
		
		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		$WHERE_KEY_QTY_ALL = [];
		foreach ($detail_input as $key => $value) {
			if($value['qty'] > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $value['qty'];
				$WHERE_KEY_QTY_ALL[$value['id']] 	= $value['qty_all'];
			}
		}

		if(!empty($where_in_ID)){
			$get_detail_spk = $this->db
								->select('*')
								->from('production_spk')
								->where('kode_spk', $kode_spk)
								->where_in('id',$where_in_ID)
								->get()
								->result_array();

			foreach ($get_detail_spk as $key => $value) {
				$WHERE_IN_KEY[] = $value['id_milik'];
				$WHERE_KEY[] 	= $value['id'];
			}

			$IMPLODE_IN	= "('".implode("','", $WHERE_IN_KEY)."')";
			// MIXING
			$get_liner_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
										MAX(last_cost) AS berat 
										FROM
											so_component_detail 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001' 
										GROUP BY
											id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'LINER THIKNESS / CB' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'LINER THIKNESS / CB' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'RESIN AND ADD' 
												AND id_material <> 'MTL-1903000'
											ORDER BY
											id_detail 
										)")->result_array();
			$get_structure_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'STRUKTUR THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
												id_detail 
											)")->result_array();
			$get_external_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'
											GROUP BY
												id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'EXTERNAL LAYER THICKNESS' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
											)
											UNION
											(
												SELECT
													id_milik,
													id_material,
													nm_material,
													id_category,
													nm_category,
													last_cost AS berat 
												FROM
													so_component_detail_add
												WHERE
													id_milik IN ".$IMPLODE_IN." 
													AND detail_name = 'EXTERNAL LAYER THICKNESS' 
													AND id_material <> 'MTL-1903000' 
													AND id_category <> 'TYP-0001' 
												ORDER BY
												id_detail 
											)")->result_array();
			$get_topcoat_mix = $this->db->query("(SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											MAX(last_cost) AS berat 
										FROM
											so_component_detail_plus 
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category = 'TYP-0001'  
											GROUP BY
												id_milik
										ORDER BY
											id_detail DESC
										)
										UNION
										(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_plus
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'TOPCOAT' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
										id_detail 
										)
										UNION
										(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'TOPCOAT' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
											id_detail 
										)")->result_array();
			$get_str_n1_mix = $this->db->query("(SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
									MAX(last_cost) AS berat 
									FROM
										so_component_detail 
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category = 'TYP-0001'  
										GROUP BY
											id_milik
									ORDER BY
										id_detail DESC
									)
									UNION
									(
									SELECT
										id_milik,
										id_material,
										nm_material,
										id_category,
										nm_category,
										last_cost AS berat 
									FROM
										so_component_detail_plus
									WHERE
										id_milik IN ".$IMPLODE_IN." 
										AND detail_name = 'STRUKTUR NECK 1' 
										AND id_material <> 'MTL-1903000' 
										AND id_category <> 'TYP-0001' 
									ORDER BY
										id_detail 
									)
									UNION
									(
										SELECT
											id_milik,
											id_material,
											nm_material,
											id_category,
											nm_category,
											last_cost AS berat 
										FROM
											so_component_detail_add
										WHERE
											id_milik IN ".$IMPLODE_IN." 
											AND detail_name = 'STRUKTUR NECK 1' 
											AND id_material <> 'MTL-1903000' 
											AND id_category <> 'TYP-0001' 
										ORDER BY
											id_detail 
									)")->result_array();
			$get_str_n2_mix = $this->db->query("(SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
											MAX(last_cost) AS berat 
											FROM
												so_component_detail 
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category = 'TYP-0001'  
												GROUP BY
													id_milik
											ORDER BY
												id_detail DESC
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_plus
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)
											UNION
											(
											SELECT
												id_milik,
												id_material,
												nm_material,
												id_category,
												nm_category,
												last_cost AS berat 
											FROM
												so_component_detail_add
											WHERE
												id_milik IN ".$IMPLODE_IN." 
												AND detail_name = 'STRUKTUR NECK 2' 
												AND id_material <> 'MTL-1903000' 
												AND id_category <> 'TYP-0001' 
											ORDER BY
												id_detail 
											)")->result_array();
			// print_r(get_material_by_category());
			// exit;
			$data = array(
				'get_liner_utama' 		=> $this->getDataGroupMaterialNew($get_liner_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_str_n1_utama' 		=> $this->getDataGroupMaterialNew($get_str_n1_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_str_n2_utama' 		=> $this->getDataGroupMaterialNew($get_str_n2_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_structure_utama' 	=> $this->getDataGroupMaterialNew($get_structure_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_external_utama' 	=> $this->getDataGroupMaterialNew($get_external_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'get_topcoat_utama' 	=> $this->getDataGroupMaterialNew($get_topcoat_mix, $WHERE_KEY, $WHERE_KEY_QTY, $WHERE_KEY_QTY_ALL),
				'kode_spk' 				=> $kode_spk,
				'kode_trans'			=> $kode_trans,
				'id_spk' 				=> $WHERE_KEY,
				'hist_produksi'			=> $hist_produksi,
				'ArrMaterialAdd'		=> $ArrMaterialAdd,
				'ArrGetCategory'		=> get_detail_material(),
				'get_detail_final_drawing'=> get_detail_final_drawing(),
				'get_material_by_category' => get_material_by_category()
			);
			
			$data_html = $this->load->view('Produksi/input_material_2_request', $data, TRUE);
		}
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function show_material_input2_request(){
		$data	= $this->input->post();

		$data_html = "";
		$kode_spk	= $data['kode_spk'];
		$kode_trans	= $data['kode_trans'];
		$hist_produksi = $data['hist_produksi'];
		$detail_input	= $data['detail_input'];
		$id_gudang_from	= $data['id_gudang_from'];

		$get_material_add = $this->db->get_where('production_spk_add',array('kode_spk'=>$kode_spk,'kode_trans'=>$kode_trans,'actual_type <>'=>'0'))->result_array();
		$ArrMaterialAdd = [];
		foreach ($get_material_add as $key => $value) {
			$ArrMaterialAdd[$value['layer']][$key] = $value;
		}
		// print_r($ArrMaterialAdd);
		// exit;
		if(!empty($detail_input)){
			$getDetail = $this->db
							->select('*')
							->from('warehouse_adjustment_detail')
							->where('kode_trans', $kode_trans)
							->get()
							->result_array();
			$ArrData = [];
			foreach ($getDetail as $key => $value) {
				$ArrData[$value['keterangan']][] = $value;
			}

			// print_r($ArrData);
			// exit;

			// exit;
			$data = array(
				'get_liner_utama' 		=> (!empty($ArrData['LINER THIKNESS / CB']))?$ArrData['LINER THIKNESS / CB']:array(),
				'get_joint_utama' 		=> (!empty($ArrData['RESIN AND ADD']))?$ArrData['RESIN AND ADD']:array(),
				'get_str_n1_utama' 		=> (!empty($ArrData['STRUKTUR NECK 1']))?$ArrData['STRUKTUR NECK 1']:array(),
				'get_str_n2_utama' 		=> (!empty($ArrData['STRUKTUR NECK 2']))?$ArrData['STRUKTUR NECK 2']:array(),
				'get_structure_utama' 	=> (!empty($ArrData['STRUKTUR THICKNESS']))?$ArrData['STRUKTUR THICKNESS']:array(),
				'get_external_utama' 	=> (!empty($ArrData['EXTERNAL LAYER THICKNESS']))?$ArrData['EXTERNAL LAYER THICKNESS']:array(),
				'get_topcoat_utama' 	=> (!empty($ArrData['TOPCOAT']))?$ArrData['TOPCOAT']:array(),
				'id_gudang_from' 				=> $id_gudang_from,
				'kode_spk' 				=> $kode_spk,
				'kode_trans'			=> $kode_trans,
				'hist_produksi'			=> $hist_produksi,
				'ArrMaterialAdd'		=> $ArrMaterialAdd,
				'ArrGetCategory'		=> get_detail_material(),
				'get_detail_final_drawing'=> get_detail_final_drawing(),
				'get_material_by_category' => get_material_by_category()
			);
			
			$data_html = $this->load->view('Produksi/input_material_2_request', $data, TRUE);
		}
		$Arr_Kembali	= array(
			'status'	=> 1,
			'data_html'	=> $data_html
		);
		echo json_encode($Arr_Kembali);
	}

	public function add_material_request(){
		$data 			= $this->input->post();
		$id_category 	= $data['id_category'];
		$id_material 	= $data['id_material'];
		$layer 			= $data['layer'];
		$nomoradd 		= (int)$data['nomoradd'] + 1;

		$materials		= $this->db->get_where('raw_materials',array('id_material'=>$id_material))->result();
		$raw_materials	= $this->db->get_where('raw_materials',array('id_category'=>$id_category,'delete'=>'N'))->result_array();
    	
		$d_Header = "";
		$d_Header .= "<tr>";
			$d_Header .= "<td align='center'>#</td>";
			$d_Header .= "<td align='left'>".$materials[0]->nm_category."</td>";
			$d_Header .= "<td align='left'>".$materials[0]->nm_material."</td>";
			$d_Header .= "<td align='left'></td>";
			$d_Header .= "<td align='left' hidden></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='requesta_add[$nomoradd][actual_type]' class='chosen_select form-control input-sm inline-blockd costcenter'>";
					$d_Header .= "<option value='0'>Select Material</option>";
					foreach($raw_materials AS $val => $valx){
						$d_Header .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
					}
				$d_Header .= 		"</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'><input type='text' name='requesta_add[$nomoradd][persen]' class='form-control input-sm text-center autoNumeric3' autocomplete='off'></td>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<input type='hidden' name='requesta_add[$nomoradd][id_material]' value='".$id_material."'>";
				$d_Header .= "<input type='hidden' name='requesta_add[$nomoradd][layer]' value='".$layer."'>";
				$d_Header .= "<input type='text' name='requesta_add[$nomoradd][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off'>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";
        
		echo json_encode(array(
			'data_html'	=> $d_Header,
			'status' => 1,
			'nomoradd' => $nomoradd
		));
	}

	public function add_material_request_add(){
		$data 			= $this->input->post();
		$layer 			= $data['layer'];
		$nomoradd 		= (int)$data['nomoradd'] + 1;

		$raw_category	= $this->db->order_by('category','asc')->get_where('raw_categories',array('delete'=>'N'))->result_array();
    	
		$d_Header = "";
		$d_Header .= "<tr>";
			$d_Header .= "<td align='center'>#</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='requesta_add[$nomoradd][category]' class='chosen_select form-control input-sm inline-blockd change_category'>";
					$d_Header .= "<option value='0'>Select Category</option>";
					foreach($raw_category AS $val => $valx){
						$d_Header .= "<option value='".$valx['id_category']."'>".strtoupper($valx['category'])."</option>";
					}
				$d_Header .= 		"</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left' class='nm_material_add'></td>";
			$d_Header .= "<td align='left'></td>";
			$d_Header .= "<td align='left' hidden></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='requesta_add[$nomoradd][actual_type]' class='chosen_select form-control input-sm inline-blockd list_material'>";
					$d_Header .= "<option value='0'>List Empty</option>";
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'><input type='text' name='requesta_add[$nomoradd][persen]' class='form-control input-sm text-center autoNumeric3' autocomplete='off'></td>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<input type='hidden' name='requesta_add[$nomoradd][id_material]' value=''>";
				$d_Header .= "<input type='hidden' name='requesta_add[$nomoradd][layer]' value='".$layer."'>";
				$d_Header .= "<input type='text' name='requesta_add[$nomoradd][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off'>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";
        
		echo json_encode(array(
			'data_html'	=> $d_Header,
			'status' => 1,
			'nomoradd' => $nomoradd
		));
	}

	public function get_material(){
		$data 			= $this->input->post();
		$id_category 	= $data['id_category'];

		$raw_materials	= $this->db->get_where('raw_materials',array('id_category'=>$id_category,'delete'=>'N'))->result_array();
    	
		$d_Header = "";
		$d_Header .= "<option value='0'>Select Material</option>";
		foreach($raw_materials AS $val => $valx){
			$d_Header .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
		}
        
		echo json_encode(array(
			'option'	=> $d_Header,
			'status' => 1
		));
	}

	public function field_joint_to_outgoing(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username 		= $this->session->userdata['ORI_User']['username'];
		$datetime 		= date('Y-m-d H:i:s');

		$no_ipp_filter 	= $data['no_ipp_filter'];
		$no_ipp 		= $data['no_ipp'];
		$customer 		= get_name('production','nm_customer','no_ipp',$no_ipp);
		$id_milik 		= $data['id_milik'];
		$qty 			= $data['qty'];
		$no_produksi 	= "PRO-".$no_ipp;
		$id_bq 			= "BQ-".$no_ipp;

		$YM	= date('ym');
		$srcPlant		= "SELECT MAX(kode_spk) as maxP FROM production_spk WHERE kode_spk LIKE '".$YM."%' ";
		$resultPlant	= $this->db->query($srcPlant)->result_array();
		$angkaUrut2		= $resultPlant[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 4, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_spk		= $YM.$urut2;

		// BackToDevelopment();
		// exit;
		$detail_product = $this->db->select('*')->from('so_detail_header')->where('id', $id_milik)->get()->result();

		$get_urut 		= $this->db->select('MAX(urut_product) AS urut_max')->get_where('production_detail', array('id_produksi'=>$no_produksi,'id_milik'=>$id_milik))->result();
		$urut_nomor 	= (!empty($get_urut))?$get_urut[0]->urut_max + 1:0;
		$nomor_so 		= get_nomor_so($no_ipp);
		$kode_urut 		= substr($detail_product[0]->no_komponen,-3);
		$no_spk 		= $detail_product[0]->no_spk;
		$product 		= $detail_product[0]->id_category;
		$id_product 	= $detail_product[0]->id_product;
		$product_code 	= $nomor_so.'-'.$kode_urut.'.'.$urut_nomor;
		
		$ArrUpdate = [
			'kode_spk'			=>$kode_spk,
			'no_spk'			=>$no_spk,
			'product_code'		=>$product_code,
			'urut_product'		=>$urut_nomor,
			'print_merge'		=>'Y',
			'print_merge_by'	=>$username,
			'print_merge_date'	=>$datetime,
			'print_merge2'		=>'Y',
			'print_merge2_by'	=>$username,
			'print_merge2_date'	=>$datetime
		];

		$ArrUpdateWhere = [
			'id_milik'		=> $id_milik,
			'id_produksi'	=> $no_produksi,
			'kode_spk'		=> NULL,
			'print_merge'	=> 'N',
		];

		//WAREHOUSE ADJUSTMENT
		$Ym 			= date('ym'); 
		$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
		$resultMtr		= $this->db->query($srcMtr)->result_array();
		$angkaUrut2		= $resultMtr[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 7, 4);
		$urutan2++;
		$urut2			= sprintf('%04s',$urutan2);
		$kode_trans		= "TRS".$Ym.$urut2;

		$jumlah_material1 = $this->db->select('id_material, SUM(material_weight) AS berat, SUM(last_cost) AS berat2, nm_material, id_category, nm_category, price_mat')->group_by('id_material')->get_where('so_component_detail',array('id_milik'=>$id_milik,'id_bq'=>$id_bq,'id_material !='=>'MTL-1903000'))->result_array();
		$jumlah_material2 = $this->db->select('id_material, SUM(last_cost) AS berat, SUM(last_cost) AS berat2, nm_material, id_category, nm_category, price_mat')->group_by('id_material')->get_where('so_component_detail_add',array('id_milik'=>$id_milik,'id_bq'=>$id_bq,'id_material !='=>'MTL-1903000'))->result_array();
		
		$jumlah_material = array_merge($jumlah_material1,$jumlah_material2);
		$SUM_MAT = 0;
		$ArrDetAdjust = [];
		$ArrDetField = [];
		foreach($jumlah_material AS $val => $valx){
			$berat = ($valx['berat'] > 0)?$valx['berat']:$valx['berat2'];
			$JUMLAH_MAT = $berat * $qty;
			$SUM_MAT 	+= $JUMLAH_MAT;

			//detail adjustmeny
			// $ArrDetAdjust[$val]['no_ipp'] 			= $id_bq;
			// $ArrDetAdjust[$val]['kode_trans'] 		= $kode_trans;
			// $ArrDetAdjust[$val]['id_po_detail'] 	= NULL;
			// $ArrDetAdjust[$val]['id_material_req'] 	= $valx['id_material'];
			// $ArrDetAdjust[$val]['id_material'] 		= $valx['id_material'];
			// $ArrDetAdjust[$val]['nm_material'] 		= $valx['nm_material'];
			// $ArrDetAdjust[$val]['id_category'] 		= $valx['id_category'];
			// $ArrDetAdjust[$val]['nm_category'] 		= $valx['nm_category'];
			// $ArrDetAdjust[$val]['qty_order'] 		= $JUMLAH_MAT;
			// $ArrDetAdjust[$val]['qty_oke'] 			= $JUMLAH_MAT;
			// $ArrDetAdjust[$val]['expired_date'] 	= NULL;
			// $ArrDetAdjust[$val]['keterangan'] 		= NULL;
			// $ArrDetAdjust[$val]['update_by'] 		= $username;
			// $ArrDetAdjust[$val]['update_date'] 		= $datetime;

			$ArrDetField[$val]['id_bq'] 			= $id_bq;
			$ArrDetField[$val]['id_milik'] 			= $id_milik;
			$ArrDetField[$val]['id_material'] 		= $valx['id_material'];
			$ArrDetField[$val]['qty'] 				= $JUMLAH_MAT;
			$ArrDetField[$val]['unit_price'] 		= $valx['price_mat'];
			$ArrDetField[$val]['total_price'] 		= $valx['price_mat'] * $JUMLAH_MAT;
			$ArrDetField[$val]['note'] 				= 'Request Date : '.$datetime;
			$ArrDetField[$val]['updated_by'] 		= $username;
			$ArrDetField[$val]['updated_date'] 		= $datetime;
			$ArrDetField[$val]['approve_by'] 		= $username;
			$ArrDetField[$val]['approve_date'] 		= $datetime;
		}

		// $ArrInsertH = array(
		// 	'kode_trans' 		=> $kode_trans,
		// 	'no_ipp' 			=> $id_bq,
		// 	'note' 			    => $customer.' (field joint)',
		// 	'category' 			=> 'outgoing pusat',
		// 	'jumlah_mat' 		=> $SUM_MAT,
		// 	'id_gudang_dari' 	=> 1,
		// 	'kd_gudang_dari' 	=> 'OPC3',
		// 	'kd_gudang_ke' 		=> 'SUBGUDANG',
		// 	'created_by' 		=> $username,
		// 	'created_date' 		=> $datetime
		// );

		// print_r($ArrInsertH);
		// print_r($ArrDetAdjust);
		// print_r($ArrDetField);
		// print_r($ArrUpdate);
		// print_r($ArrUpdateWhere);
		// exit;

		$key = 1;	
		$InsertKode[$key]['kode_spk'] 		= $kode_spk;
		$InsertKode[$key]['id_milik'] 		= $id_milik;
		$InsertKode[$key]['product'] 		= $product;
		$InsertKode[$key]['id_product'] 	= $id_product;
		$InsertKode[$key]['no_ipp'] 		= $no_ipp;
		$InsertKode[$key]['qty'] 			= $qty;
		$InsertKode[$key]['no_spk'] 		= $no_spk;
		$InsertKode[$key]['created_by'] 	= $username;
		$InsertKode[$key]['created_date'] 	= $datetime;
		$InsertKode[$key]['product_code'] 	= $product_code;
		$InsertKode[$key]['urut_product'] 	= $urut_nomor;
		$InsertKode[$key]['status_id'] 		= 0;
		$InsertKode[$key]['product_code_cut'] = $product;

		// exit;
		

		$this->db->trans_start();
			// $this->db->insert('warehouse_adjustment', $ArrInsertH);
			// if(!empty($ArrDetAdjust)){
			// 	$this->db->insert_batch('warehouse_adjustment_detail', $ArrDetAdjust);
			// }

			if(!empty($ArrDetField)){
				$this->db->insert_batch('request_outgoing', $ArrDetField);
			}
			$this->db->insert_batch('production_spk', $InsertKode);
			//UPDATE PRODUCTION DETAIL
			$this->db->where($ArrUpdateWhere);
			$this->db->limit($qty);
			$this->db->update('production_detail',$ArrUpdate);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'no_ipp_filter'	=> $no_ipp_filter
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'no_ipp_filter'	=> $no_ipp_filter,
				'kode_spk' => $kode_spk
			);

			history('Create outgoing '.$product.' '.$id_bq.'/'.$id_milik);
		}
		echo json_encode($Arr_Kembali);
	}
	
	public function save_update_produksi_1_new_closing(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
				$ARrInsertSPK[$key]['closing_produksi_by'] 		= $username;
				$ARrInsertSPK[$key]['closing_produksi_date'] 	= $datetime;

				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
					$qUpdate 	= $this->db->query("UPDATE 
											production_detail
										SET 
											upload_real='Y',
											finish_production_date='$date_produksi',
											upload_by='$username',
											upload_date='$dateCreated',
											closing_produksi_by='$username',
											closing_produksi_date='$datetime'
										WHERE 
											id_milik='".$value['id_milik']."'
											AND kode_spk= '".$kode_spk."'
											AND print_merge_date= '".$dateCreated."'
										ORDER BY 
											id ASC 
										LIMIT $QTY");
				}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Closing! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
				
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		$ID_PRODUKSI_DETAIL = [];
		foreach ($get_detail_spk as $key => $value) {
			$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
			$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
			
			if($id_pro_det != 0){
				$ID_PRODUKSI_DETAIL[] = $id_pro_det;
			}
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						if($value['product'] == 'field joint' OR $value['product'] == 'branch joint' OR $value['product'] == 'shop joint'){
							$DETAIL_NAME = 'GLASS';
						}
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0001','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$TERPAKAI 	= (!empty($value2['terpakai']))?str_replace(',','',$value2['terpakai']):0;
									$KEBUTUHAN 	= (!empty($value2['kebutuhan']))?str_replace(',','',$value2['kebutuhan']):0;

									$total_act  = $TERPAKAI;
									if($KEBUTUHAN > 0 AND $total_est > 0){
										$total_act 	= ($total_est / $KEBUTUHAN) * $TERPAKAI;
									}

									$unit_act = 0;
									if($total_act > 0 AND $QTY_INP > 0){
										$unit_act 	= $total_act / $QTY_INP;
									}

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_by'] 	= $username;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0002','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 1;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_spk'] 				= $value['id'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_date'] 		= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_by'] 	= $username;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}
		
		// print_r($ID_PRODUKSI_DETAIL);
		$ARR_ID_PRO_UNIQ = array_unique($ID_PRODUKSI_DETAIL);
		// print_r($ARR_ID_PRO_UNIQ);
		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flag produksi input
			if(!empty($ArrJurnal)){
				//insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);
			$this->closing_produksi($ARR_ID_PRO_UNIQ);
			$this->closing_produksi_base_jurnal($kode_spk_created,$id_gudang,14);
			history('Input aktual spk produksi utama (closing) '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_report_wip_closing($ArrData){

		$sqlkurs	= "select * from ms_kurs where tanggal <='".date('Y-m-d')."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

        $restCh		= $this->db->select('jalur')->get_where('production_header',array('id_produksi'=>$ArrData['id_produksi']))->result_array();
        
		if(!empty($restCh[0]['jalur'])){
			$HelpDet 	= "estimasi_cost_and_mat";
			$HelpDet2 	= "banding_mat_pro";
			$HelpDet3 	= "bq_product";
			if($restCh[0]['jalur'] == 'FD'){
				$HelpDet = "so_estimasi_cost_and_mat";
				$HelpDet2 	= "banding_so_mat_pro";
				$HelpDet3 	= "bq_product_fd";
			}

			$sqlBy 		= " SELECT
								b.diameter AS diameter,
								b.diameter2 AS diameter2,
								b.pressure AS pressure,
								b.liner AS liner,
								b.man_hours AS man_hours,
								a.direct_labour AS direct_labour,
								a.indirect_labour AS indirect_labour,
								a.machine AS machine,
								a.mould_mandrill AS mould_mandrill,
								a.consumable AS consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) AS biaya_gaji_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) AS biaya_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
								) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan 
							FROM
								".$HelpDet." a
								INNER JOIN ". $HelpDet3." b ON a.id_milik = b.id
							WHERE id_milik='".$ArrData['id_milik']."' LIMIT 1";
			
			$restBy		= $this->db->query($sqlBy)->result_array();
			
			$sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$ArrData['id_production_detail']."' LIMIT 1";
			$restBan	= $this->db->query($sqlBan)->result_array();
			// echo $sqlEst."<br>";
			$jumTot     = ($ArrData['qty_akhir'] - $ArrData['product_ke']) + 1;
			
			$sqlInsertDet = "INSERT INTO laporan_wip_per_hari_action
								(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
								qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
								real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
								consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
								biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,kode_trans)
								VALUE
								('".$ArrData['id_produksi']."','".$ArrData['id_category']."','".$ArrData['id_product']."',
								'".$restBy[0]['diameter']."','".$restBy[0]['diameter2']."','".$restBy[0]['pressure']."',
								'".$restBy[0]['liner']."','".$ArrData['status_date']."','".$ArrData['product_ke']."',
								'".$ArrData['qty_akhir']."','".$ArrData['qty']."','".date('Y-m-d',strtotime($ArrData['status_date']))."','".$ArrData['id_production_detail']."',
								'".$ArrData['id_milik']."','".$restBan[0]['est_material'] * $jumTot."','".$restBan[0]['est_harga'] * $jumTot."',
								'".$restBan[0]['real_material']."','".$restBan[0]['real_harga']."','".$restBy[0]['direct_labour'] * $jumTot."',
								'".$restBy[0]['indirect_labour'] * $jumTot."','".$restBy[0]['machine'] * $jumTot."',
								'".$restBy[0]['mould_mandrill'] * $jumTot."','".$restBy[0]['consumable'] * $jumTot."',
								'".$restBy[0]['foh_consumable'] * $jumTot."','".$restBy[0]['foh_depresiasi'] * $jumTot."',
								'".$restBy[0]['biaya_gaji_non_produksi'] * $jumTot."','".$restBy[0]['biaya_non_produksi'] * $jumTot."',
								'".$restBy[0]['biaya_rutin_bulanan'] * $jumTot."','system','".date('Y-m-d H:i:s')."','".$restBy[0]['man_hours'] * $jumTot."','".$restBan[0]['real_harga_rp']."','".$kurs."','".$ArrData['kode_trans']."')
							";
			// echo $sqlInsertDet;
			// exit;
			$this->db->query($sqlInsertDet);
		}
	}

	public function save_update_produksi_1_new_closing_tanki(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
				$ARrInsertSPK[$key]['closing_produksi_by'] 		= $username;
				$ARrInsertSPK[$key]['closing_produksi_date'] 	= $datetime;

				$CheckDataUpdate = $this->db->get_where('production_detail',['id_milik'=>$value['id_milik'],'kode_spk'=>$kode_spk,'print_merge_date'=>$dateCreated])->result_array();
				if(!empty($CheckDataUpdate)){
				$qUpdate 	= $this->db->query("UPDATE 
											production_detail
										SET 
											upload_real='Y',
											finish_production_date='$date_produksi',
											upload_by='$username',
											upload_date='$dateCreated',
											closing_produksi_by='$username',
											closing_produksi_date='$datetime'
										WHERE 
											id_milik='".$value['id_milik']."'
											AND kode_spk= '".$kode_spk."'
											AND print_merge_date= '".$dateCreated."'
										ORDER BY 
											id ASC 
										LIMIT $QTY");
				}
				else{
					$Arr_Kembali	= array(
						'pesan'		=>'Failed Update! Error Msg: id:'.$value['id_milik'].', idspk:'.$kode_spk.', datespk:'.$dateCreated,
						'status'	=> 2,
						'id_milik'	=> $value['id_milik'],
						'kode_spk'	=> $kode_spk,
						'print_merge_date'	=> $dateCreated
					);
					echo json_encode($Arr_Kembali);
					return false;
				}
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		$ID_PRODUKSI_DETAIL = [];
		foreach ($get_detail_spk as $key => $value) {
			$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
			$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
			
			if($id_pro_det != 0){
				$ID_PRODUKSI_DETAIL[] = $id_pro_det;
			}
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
						$DETAIL_WHERE = 'liner';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
						$DETAIL_WHERE = 'structure';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
						$DETAIL_WHERE = 'topcoat';
					}
					$detailX = $data[$valueX];
					
					//LINER
					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('est_material_tanki a', array('a.id_det'=>$value['id_milik'],'a.spk_pemisah'=>'1','layer'=>$DETAIL_WHERE))
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = doubleval(str_replace(',','',$value2['terpakai']));
									$kebutuhan  = doubleval(str_replace(',','',$value2['kebutuhan']));
									if($kebutuhan > 0){
										$total_act 	= ($total_est / $kebutuhan) * $total_act;
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'tanki';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_by'] 	= $username;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
						//PLUS
						$get_liner 		= $this->db->select('id_detail, id_milik, id_product, id_material, last_cost AS berat')->get_where('so_component_detail_plus', array('id_milik'=>$value['id_milik'],'id_bq'=>'BQ-'.$value['no_ipp'],'id_category <>'=>'TYP-0002','detail_name'=>$DETAIL_NAME,'id_material <>'=>'MTL-1903000'))->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['actual_type'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = 0;
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_production_detail'] = $id_pro_det;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_product'] 			= $value3['id_product'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['spk'] 	= 1;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['id_spk'] 				= $value['id'];
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualPlus[$key.$key2.$key3.$nomor]['updated_date'] 		= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 	= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_by'] 	= $username;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['closing_produksi_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}
		
		// print_r($ID_PRODUKSI_DETAIL);
		$ARR_ID_PRO_UNIQ = array_unique($ID_PRODUKSI_DETAIL);
		// print_r($ARR_ID_PRO_UNIQ);
		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrAktualPlus);
		// print_r($ArrUpdate);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();
			//update flag produksi input
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			// if(!empty($ArrStock)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			// }
			// if(!empty($ArrHist)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist);
			// }

			// if(!empty($ArrStockInsert)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			// }
			// if(!empty($ArrHistInsert)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			// }

			// if(!empty($ArrStock2)){
			// 	$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			// }
			// if(!empty($ArrHist2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHist2);
			// }

			// if(!empty($ArrStockInsert2)){
			// 	$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			// }
			// if(!empty($ArrHistInsert2)){
			// 	$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			// }
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);
			$this->closing_produksi_tanki($ARR_ID_PRO_UNIQ);
			$this->closing_produksi_base_jurnal($kode_spk_created,$id_gudang,14);
			history('Input aktual spk produksi utama (closing) '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}

	public function save_update_produksi_1_new_closing_deadstok(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');

		$kode_spk 		= $data['kode_spk'];
		$id_gudang 		= $data['id_gudang'];
		$date_produksi 	= (!empty($data['date_produksi']))?$data['date_produksi']:NULL;
		$detail_input	= $data['detail_input'];
		$hist_produksi	= $data['hist_produksi'];

		$dateCreated = $datetime;
		if($hist_produksi != '0'){
			$dateCreated = $hist_produksi;
		}

		$kode_spk_created = $kode_spk.'/'.$dateCreated;

		$where_in_ID = [];
		$WHERE_KEY_QTY = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$where_in_ID[] = $value['id'];
				$WHERE_KEY_QTY[$value['id']] 	= $QTY;
			}
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}
		}

		$ARrInsertSPK = [];
		$ARrUpdateSPK = [];
		$ArrWhereIN_= [];
		$ArrWhereIN_IDMILIK= [];
		$ID_PRODUKSI_DETAIL = [];
		foreach ($detail_input as $key => $value) {
			$QTY = str_replace(',','',$value['qty']);
			if($QTY > 0){
				$ArrWhereIN_[] = $value['id'];
				$ArrWhereIN_IDMILIK[] = $value['id_milik'];
				$ARrInsertSPK[$key]['kode_spk'] 		= $kode_spk;
				$ARrInsertSPK[$key]['id_milik'] 		= $value['id_milik'];
				$ARrInsertSPK[$key]['id_spk'] 			= $value['id'];
				$ARrInsertSPK[$key]['qty'] 				= $QTY;
				$ARrInsertSPK[$key]['tanggal_produksi'] = $date_produksi;
				$ARrInsertSPK[$key]['id_gudang']		= $id_gudang;
				$ARrInsertSPK[$key]['spk']				= 1;
				$ARrInsertSPK[$key]['created_by'] 		= $username;
				$ARrInsertSPK[$key]['created_date'] 	= $dateCreated;
				$ARrInsertSPK[$key]['updated_by'] 		= $username;
				$ARrInsertSPK[$key]['updated_date'] 	= $datetime;
				$ARrInsertSPK[$key]['upload_eng_change']= $file_name;
				$ARrInsertSPK[$key]['closing_produksi_by'] 		= $username;
				$ARrInsertSPK[$key]['closing_produksi_date'] 	= $datetime;
			}
		}

		$ArrLooping = ['detail_liner','detail_strn1','detail_strn2','detail_str','detail_ext','detail_topcoat'];

		$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

		// print_r($ARrInsertSPK);
		// print_r($ARrUpdateSPK);
		// print_r($get_detail_spk);
		// exit;
		$ArrGroup = [];
		$ArrAktualResin = [];
		$ArrAktualPlus = [];
		$ArrAktualAdd = [];
		$ArrUpdate = [];
		$ArrUpdateStock = [];
		$ArrJurnal = [];

		$nomor = 0;
		foreach ($get_detail_spk as $key => $value) {
			foreach ($ArrLooping as $valueX) {
				if(!empty($data[$valueX])){
					if($valueX == 'detail_liner'){
						$DETAIL_NAME = 'LINER THIKNESS / CB';
					}
					if($valueX == 'detail_strn1'){
						$DETAIL_NAME = 'STRUKTUR NECK 1';
					}
					if($valueX == 'detail_strn2'){
						$DETAIL_NAME = 'STRUKTUR NECK 2';
					}
					if($valueX == 'detail_str'){
						$DETAIL_NAME = 'STRUKTUR THICKNESS';
					}
					if($valueX == 'detail_ext'){
						$DETAIL_NAME = 'EXTERNAL LAYER THICKNESS';
					}
					if($valueX == 'detail_topcoat'){
						$DETAIL_NAME = 'TOPCOAT';
					}
					$detailX = $data[$valueX];
					$no_spk			= $value['no_spk'];
					$kode_estimasi	= $value['product_code_cut'];

					foreach ($detailX as $key2 => $value2) {
						//RESIN
						$get_liner 		= $this->db
												->select('a.id AS id_detail, a.id_material, b.nm_material, b.id_category, b.nm_category, a.last_cost AS berat')
												->join('raw_materials b','a.id_material=b.id_material','left')
												->get_where('deadstok_estimasi a', array('a.kode'=>$kode_estimasi,'a.detail_name'=>$DETAIL_NAME))
												->result_array();
						if(!empty($get_liner)){
							foreach ($get_liner as $key3 => $value3) {
								if($value2['id_material'] == $value3['id_material']){ 
									$nomor++;

									$QTY_INP	= $WHERE_KEY_QTY[$value['id']];
									$total_est 	= $value3['berat'] * $QTY_INP;
									$total_act  = str_replace(',','',$value2['terpakai']);
									if($value2['kebutuhan'] > 0){
										$total_act 	= ($total_est / str_replace(',','',$value2['kebutuhan'])) * str_replace(',','',$value2['terpakai']);
									}
									$unit_act 	= $total_act / $QTY_INP;

									//PRICE BOOK
									$PRICE_BOOK = get_price_book($value2['actual_type']);
									$AMOUNT 	= $total_act * $PRICE_BOOK;

									$ID_PRODUKSI_DETAIL[] = $value3['id_detail'];

									//INSERT DETAIL GROUP
									$ArrGroup[$key.$key2.$key3.$nomor]['kode_spk'] 		= $kode_spk;
									$ArrGroup[$key.$key2.$key3.$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_detail'] 	= $value3['id_detail'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_spk'] 		= $value['id'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material'] 	= $value2['id_material'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_material_aktual'] 	= $value2['actual_type'];
									$ArrGroup[$key.$key2.$key3.$nomor]['product'] 		= $value['product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['id_product'] 	= $value['id_product'];
									$ArrGroup[$key.$key2.$key3.$nomor]['qty'] 			= $QTY_INP;
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_unit'] 		= $value3['berat'];
									$ArrGroup[$key.$key2.$key3.$nomor]['ori_total'] 	= $total_est;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_unit'] 	= $unit_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['aktual_total'] 	= $total_act;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['status_date'] 	= $dateCreated;
									$ArrGroup[$key.$key2.$key3.$nomor]['spk'] 			= 1;
									$ArrGroup[$key.$key2.$key3.$nomor]['layer'] 		= $DETAIL_NAME;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_by'] 	= $username;
									$ArrGroup[$key.$key2.$key3.$nomor]['updated_date'] 	= $datetime;
									$ArrGroup[$key.$key2.$key3.$nomor]['price_book'] 	= $PRICE_BOOK;
									$ArrGroup[$key.$key2.$key3.$nomor]['amount'] 		= $AMOUNT;
									//INSERT AKTUAL KE JALUR UTAMA
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_produksi'] 			= 'PRO-'.$value['no_ipp'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_detail'] 			= $value3['id_detail'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_production_detail'] = null;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_product'] 			= 'deadstok';
									$ArrAktualResin[$key.$key2.$key3.$nomor]['batch_number'] 		= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['actual_type'] 			= $value2['actual_type'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['material_terpakai'] 	= $total_act;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['status_date'] 			= $dateCreated;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['catatan_programmer'] 	= $kode_spk_created;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['spk'] 				= 1;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['id_spk'] 	= $value['id'];
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_by'] 			= $username;
									$ArrAktualResin[$key.$key2.$key3.$nomor]['updated_date'] 			= $datetime;
									//UPDATE FLAG SPK 2
									$ArrUpdate[$key.$key2.$key3.$nomor]['id'] 			= $value['id'];
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1'] 		= 'Y';
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_by'] 		= $username;
									$ArrUpdate[$key.$key2.$key3.$nomor]['spk1_date']	= $datetime;
									// $ArrUpdate[$key.$key2.$key3.$nomor]['gudang1']	= $id_gudang;

									$ArrUpdateStock[$nomor]['id'] 	= $value2['actual_type'];
									$ArrUpdateStock[$nomor]['qty'] 	= $total_act;

									$ArrJurnal[$value['id_milik']][$nomor]['id_material'] 	= $value2['actual_type'];
									$ArrJurnal[$value['id_milik']][$nomor]['amount'] 		= $AMOUNT;
									$ArrJurnal[$value['id_milik']][$nomor]['qty'] 			= $total_act;
									$ArrJurnal[$value['id_milik']][$nomor]['id_spk'] 		= $value['id'];
									$ArrJurnal[$value['id_milik']][$nomor]['no_ipp'] 		= $value['no_ipp'];
									$ArrJurnal[$value['id_milik']][$nomor]['id_milik'] 		= $value['id_milik'];
									$ArrJurnal[$value['id_milik']][$nomor]['product'] 		= $value['product'];
									$ArrJurnal[$value['id_milik']][$nomor]['spk'] 			= 1;
								}
							}
						}
					}
				}
			}
		}

		// print_r($ArrUpdateStock);
		// exit;

		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ARR_ID_PRO_UNIQ = array_unique($ID_PRODUKSI_DETAIL);

		// print_r($ArrGroup);
		// print_r($ArrAktualResin);
		// print_r($ArrUpdate);
		// print_r($ArrJurnal);
		// exit;

		$ArrInputProduksi = [
			'status_close_produksi' => 'Y',
			'close_produksi_by' => $username,
			'close_produksi_date' => $datetime
		];

		//New Report FG
		$getDetDeadStock = $this->db->get_where('deadstok_modif',array('kode_spk'=>$kode_spk))->result_array();
		$ArrOUT_FG = [];
		$ArrIN_WIP = [];
		$ArrIN_WIP_MATERIAL = [];

		$catatanPro = $kode_spk.'/'.$hist_produksi;
		$TMP_DET = $this->db->select('actual_type as id, material_terpakai as qty')->get_where('tmp_production_real_detail',array('catatan_programmer'=>$catatanPro))->result_array();
		$TMP_PLUS = $this->db->select('actual_type as id, material_terpakai as qty')->get_where('tmp_production_real_detail_plus',array('catatan_programmer'=>$catatanPro))->result_array();
		$TMP_ADD = $this->db->select('actual_type as id, material_terpakai as qty')->get_where('tmp_production_real_detail_add',array('catatan_programmer'=>$catatanPro))->result_array();
		$TMP_GROUP = array_merge($TMP_DET,$TMP_PLUS,$TMP_ADD);

		$tempMixing = [];
		foreach($TMP_GROUP as $value) {
			if(!array_key_exists($value['id'], $tempMixing)) {
				$tempMixing[$value['id']] = 0;
			}
			$tempMixing[$value['id']] += $value['qty'];
		}

		$tanggalNow = date('Y-m-d');
		$GETDetMaterial = get_detail_material();
		$GETPriceBookProduksi = getPriceBookByDateproduksi($tanggalNow);

		if(!empty($getDetDeadStock)){
			foreach ($getDetDeadStock as $key => $value) {
				$getDataFG = $this->db->order_by('id','desc')->limit(1)->get_where('data_erp_fg',array('id_pro_det'=>$value['id_deadstok'],'jenis'=>'in deadstok'))->result_array();
				if(!empty($getDataFG)){
					$ArrOUT_FG[$key]['tanggal'] = date('Y-m-d');
					$ArrOUT_FG[$key]['keterangan'] = 'Finish Good to WIP (Deadstock Modif)';
					$ArrOUT_FG[$key]['no_so'] = $getDataFG[0]['no_so'];
					$ArrOUT_FG[$key]['product'] = $getDataFG[0]['product'];
					$ArrOUT_FG[$key]['no_spk'] = $getDataFG[0]['no_spk'];
					$ArrOUT_FG[$key]['kode_trans'] = $kode_spk;
					$ArrOUT_FG[$key]['id_pro_det'] = $getDataFG[0]['id_pro_det'];
					$ArrOUT_FG[$key]['qty'] = 1;
					$ArrOUT_FG[$key]['nilai_wip'] = $getDataFG[0]['nilai_wip'];
					$ArrOUT_FG[$key]['nilai_unit'] = $getDataFG[0]['nilai_unit'];
					$ArrOUT_FG[$key]['material'] = 0;
					$ArrOUT_FG[$key]['wip_direct'] =  0;
					$ArrOUT_FG[$key]['wip_indirect'] =  0;
					$ArrOUT_FG[$key]['wip_consumable'] =  0;
					$ArrOUT_FG[$key]['wip_foh'] =  0;
					$ArrOUT_FG[$key]['created_by'] = $username;
					$ArrOUT_FG[$key]['created_date'] = $datetime;
					$ArrOUT_FG[$key]['id_trans'] =  $getDataFG[0]['id_trans'];
					$ArrOUT_FG[$key]['id_pro'] =  $value['id_deadstok'];
					$ArrOUT_FG[$key]['jenis'] =  'out deadstok';

					$SUM_DEADSTICK = 0;
					if(!empty($temp)){
						foreach ($temp as $key2 => $value2) {
							$nm_material = (!empty($GETDetMaterial[$key2]['nm_material']))?$GETDetMaterial[$key2]['nm_material']:null;
							$cost_book = (!empty($GETPriceBookProduksi[$key2]))?$GETPriceBookProduksi[$key2]:0;
							$key_uniq = $key.'-'.$key2.'-Mix2';
							$qtyValue = $value2 / COUNT($getDetDeadStock);

							$ArrIN_WIP_MATERIAL[$key_uniq]['tanggal'] = date('Y-m-d');
							$ArrIN_WIP_MATERIAL[$key_uniq]['keterangan'] = 'Finish Good to WIP (Deadstock Modif)';
							$ArrIN_WIP_MATERIAL[$key_uniq]['no_so'] = $getDataFG[0]['no_so'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['product'] = $getDataFG[0]['product'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['no_spk'] = $getDataFG[0]['no_spk'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['kode_trans'] = $kode_spk;
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_pro_det'] = $getDataFG[0]['id_pro_det'];
							// $ArrIN_WIP_MATERIAL[$key_uniq]['qty'] = 1;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['nilai_wip'] = $cost_book * $qtyValue;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['material'] = $cost_book * $qtyValue;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_direct'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_indirect'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_consumable'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_foh'] =  0;
							$ArrIN_WIP_MATERIAL[$key_uniq]['created_by'] = $username;
							$ArrIN_WIP_MATERIAL[$key_uniq]['created_date'] = $datetime;
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_trans'] =  $getDataFG[0]['id_trans'];
							// $ArrIN_WIP_MATERIAL[$key_uniq]['jenis'] =  'in deadstok';
			
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_material'] =  $key2;
							$ArrIN_WIP_MATERIAL[$key_uniq]['nm_material'] =  $nm_material;
							$ArrIN_WIP_MATERIAL[$key_uniq]['berat'] =  $qtyValue;
							$ArrIN_WIP_MATERIAL[$key_uniq]['costbook'] =  $cost_book;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['gudang'] =  $id_gudang;
							$ArrIN_WIP_MATERIAL[$key_uniq]['total_price'] =  $qtyValue * $cost_book;
							$SUM_DEADSTICK += $qtyValue * $cost_book;
						}
					}
					if(!empty($tempMixing)){
						foreach ($tempMixing as $key2 => $value2) {
							$nm_material = (!empty($GETDetMaterial[$key2]['nm_material']))?$GETDetMaterial[$key2]['nm_material']:null;
							$cost_book = (!empty($GETPriceBookProduksi[$key2]))?$GETPriceBookProduksi[$key2]:0;
							$key_uniq = $key.'-'.$key2.'-Mix';
							$qtyValue = $value2 / COUNT($getDetDeadStock);

							$ArrIN_WIP_MATERIAL[$key_uniq]['tanggal'] = date('Y-m-d');
							$ArrIN_WIP_MATERIAL[$key_uniq]['keterangan'] = 'Finish Good to WIP (Deadstock Modif)';
							$ArrIN_WIP_MATERIAL[$key_uniq]['no_so'] = $getDataFG[0]['no_so'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['product'] = $getDataFG[0]['product'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['no_spk'] = $getDataFG[0]['no_spk'];
							$ArrIN_WIP_MATERIAL[$key_uniq]['kode_trans'] = $kode_spk;
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_pro_det'] = $getDataFG[0]['id_pro_det'];
							// $ArrIN_WIP_MATERIAL[$key_uniq]['qty'] = 1;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['nilai_wip'] = $cost_book * $qtyValue;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['material'] = $cost_book * $qtyValue;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_direct'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_indirect'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_consumable'] =  0;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['wip_foh'] =  0;
							$ArrIN_WIP_MATERIAL[$key_uniq]['created_by'] = $username;
							$ArrIN_WIP_MATERIAL[$key_uniq]['created_date'] = $datetime;
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_trans'] =  $getDataFG[0]['id_trans'];
							// $ArrIN_WIP_MATERIAL[$key_uniq]['jenis'] =  'in deadstok';
			
							$ArrIN_WIP_MATERIAL[$key_uniq]['id_material'] =  $key2;
							$ArrIN_WIP_MATERIAL[$key_uniq]['nm_material'] =  $nm_material;
							$ArrIN_WIP_MATERIAL[$key_uniq]['berat'] =  $qtyValue;
							$ArrIN_WIP_MATERIAL[$key_uniq]['costbook'] =  $cost_book;
							// $ArrIN_WIP_MATERIAL[$key_uniq]['gudang'] =  $id_gudang;
							$ArrIN_WIP_MATERIAL[$key_uniq]['total_price'] =  $qtyValue * $cost_book;

							$SUM_DEADSTICK += $qtyValue * $cost_book;
						}
					}

					$ArrIN_WIP[$key]['tanggal'] = date('Y-m-d');
					$ArrIN_WIP[$key]['keterangan'] = 'Finish Good to WIP (Deadstock Modif)';
					$ArrIN_WIP[$key]['no_so'] = $getDataFG[0]['no_so'];
					$ArrIN_WIP[$key]['product'] = $getDataFG[0]['product'];
					$ArrIN_WIP[$key]['no_spk'] = $getDataFG[0]['no_spk'];
					$ArrIN_WIP[$key]['kode_trans'] = $kode_spk;
					$ArrIN_WIP[$key]['id_pro_det'] = $getDataFG[0]['id_pro_det'];
					$ArrIN_WIP[$key]['qty'] = 1;
					$ArrIN_WIP[$key]['nilai_wip'] = $SUM_DEADSTICK;
					$ArrIN_WIP[$key]['material'] = 0;
					$ArrIN_WIP[$key]['wip_direct'] =  0;
					$ArrIN_WIP[$key]['wip_indirect'] =  0;
					$ArrIN_WIP[$key]['wip_consumable'] =  0;
					$ArrIN_WIP[$key]['wip_foh'] =  0;
					$ArrIN_WIP[$key]['created_by'] = $username;
					$ArrIN_WIP[$key]['created_date'] = $datetime;
					$ArrIN_WIP[$key]['id_trans'] =  $getDataFG[0]['id_trans'];
					$ArrIN_WIP[$key]['jenis'] =  'in deadstok';
				}
			}
		}

		//PENGURANGAN STOCK
		$tempMerge = array_merge($temp,$tempMixing);

		$tempMergeGroup = [];
		foreach($tempMerge as $material => $value) {
			if(!array_key_exists($material, $tempMergeGroup)) {
				$tempMergeGroup[$material] = 0;
			}
			$tempMergeGroup[$material] += $value;
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();
		foreach ($tempMergeGroup as $key => $value) {
			//PENGURANGAN GUDANG PRODUKSI
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang, 'id_material'=>$key))->result();
			$kode_gudang = get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
			$id_gudang_wip = 14;
			$kode_gudang_wip = get_name('warehouse', 'kd_gudang', 'id', $id_gudang_wip);
			$kode_spk_created = $kode_spk.'/'.$dateCreated;
			$check_edit = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist = (!empty($check_edit))?$check_edit[0]->jumlah_mat:0;
			$hist_tambahan = (!empty($check_edit))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrStock[$key]['update_by'] 	= $username;
				$ArrStock[$key]['update_date'] 	= $datetime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang;
				$ArrHist[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value + $berat_hist;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHist[$key]['ket'] 				= 'pengurangan aktual produksi (wip1)'.$hist_tambahan;
				$ArrHist[$key]['update_by'] 		=  $username;
				$ArrHist[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value + $berat_hist;
				$ArrStockInsert[$key]['update_by'] 		= $data_session['ORI_User']['username'];
				$ArrStockInsert[$key]['update_date'] 	= $datetime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_stock_akhir'] 	= 0 - $value + $berat_hist;
				$ArrHistInsert[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value - $berat_hist;
				$ArrHistInsert[$key]['ket'] 				= 'pengurangan aktual produksi (insert new) (wip1)'.$hist_tambahan;
				$ArrHistInsert[$key]['update_by'] 		=  $username;
				$ArrHistInsert[$key]['update_date'] 		= $datetime;
			}

			//PENAMBAHAN GUDANG WIP
			
			$rest_pusat = $this->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key))->result();
			$check_edit_wip = $this->db
							->select('SUM(jumlah_mat) as jumlah_mat')
							->from('warehouse_history')
							->where('id_gudang',$id_gudang_wip)
							->where('id_material',$key)
							->where('no_ipp',$kode_spk_created)
							->like('ket','(wip1)')
							->get()
							->result();
			$berat_hist_wip = (!empty($check_edit_wip))?$check_edit_wip[0]->jumlah_mat:0;
			$hist_tambahan_wip = (!empty($check_edit_wip))?' / tambah kurang':'';

			if(!empty($rest_pusat)){
				$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrStock2[$key]['update_by'] 	=  $username;
				$ArrStock2[$key]['update_date'] 	= $datetime;

				$ArrHist2[$key]['id_material'] 	= $key;
				$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang;
				$ArrHist2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHist2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHist2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value - $berat_hist_wip;
				$ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHist2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHist2[$key]['ket'] 				= 'penambahan aktual produksi (wip1)'.$hist_tambahan_wip;
				$ArrHist2[$key]['update_by'] 		=  $username;
				$ArrHist2[$key]['update_date'] 		= $datetime;
			}
			else{
				$sqlMat	= "SELECT * FROM raw_materials WHERE id_material='".$key."' LIMIT 1 ";
				$restMat	= $this->db->query($sqlMat)->result();

				$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrStockInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrStockInsert2[$key]['qty_stock'] 		= $value - $berat_hist_wip;
				$ArrStockInsert2[$key]['update_by'] 		=  $username;
				$ArrStockInsert2[$key]['update_date'] 	= $datetime;

				$ArrHistInsert2[$key]['id_material'] 	= $key;
				$ArrHistInsert2[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['id_gudang_dari'] 	= $id_gudang;;
				$ArrHistInsert2[$key]['kd_gudang_dari'] 	= $kode_gudang;
				$ArrHistInsert2[$key]['id_gudang_ke'] 		= $id_gudang_wip;
				$ArrHistInsert2[$key]['kd_gudang_ke'] 		= $kode_gudang_wip;
				$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_booking_akhir'] = 0;
				$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
				$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert2[$key]['no_ipp'] 			= $kode_spk_created;
				$ArrHistInsert2[$key]['jumlah_mat'] 		= $value - $berat_hist_wip;
				$ArrHistInsert2[$key]['ket'] 				= 'penambahan aktual produksi (insert new) (wip1)'.$hist_tambahan_wip;
				$ArrHistInsert2[$key]['update_by'] 		=  $username;
				$ArrHistInsert2[$key]['update_date'] 		= $datetime;
			}
		}
		
		
		// print_r($tempMergeGroup);
		// print_r($ArrStock);
		// print_r($ArrHist);
		// exit;

		$this->db->trans_start();

			$this->db->where('kode_spk',$kode_spk);
			$this->db->update('deadstok_modif',$ArrInputProduksi);

			if(!empty($ArrOUT_FG)){
				$this->db->insert_batch('data_erp_fg',$ArrOUT_FG);
			}
			if(!empty($ArrIN_WIP)){
				$this->db->insert_batch('data_erp_wip_group',$ArrIN_WIP);
			}
			if(!empty($ArrIN_WIP_MATERIAL)){
				$this->db->insert_batch('data_erp_wip',$ArrIN_WIP_MATERIAL);
			}
			//update flag produksi input
			if(!empty($ArrJurnal)){
				insert_jurnal_wip($ArrJurnal,$id_gudang,14,'laporan produksi','pengurangan gudang produksi','wip',$kode_spk_created);
			}
			//update flah produksi spk group
			if(!empty($ARrUpdateSPK)){
				$this->db->update_batch('production_spk',$ARrUpdateSPK,'id');
			}
			if(!empty($ArrUpdate)){
				$this->db->update_batch('production_spk',$ArrUpdate,'id');
			}

			if(!empty($ArrStock)){
				$this->db->update_batch('warehouse_stock', $ArrStock, 'id');
			}
			if(!empty($ArrHist)){
				$this->db->insert_batch('warehouse_history', $ArrHist);
			}

			if(!empty($ArrStockInsert)){
				$this->db->insert_batch('warehouse_stock', $ArrStockInsert);
			}
			if(!empty($ArrHistInsert)){
				$this->db->insert_batch('warehouse_history', $ArrHistInsert);
			}

			if(!empty($ArrStock2)){
				$this->db->update_batch('warehouse_stock', $ArrStock2, 'id');
			}
			if(!empty($ArrHist2)){
				$this->db->insert_batch('warehouse_history', $ArrHist2);
			}

			if(!empty($ArrStockInsert2)){
				$this->db->insert_batch('warehouse_stock', $ArrStockInsert2);
			}
			if(!empty($ArrHistInsert2)){
				$this->db->insert_batch('warehouse_history', $ArrHistInsert2);
			}
			
			if($hist_produksi != '0'){
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('created_date',$hist_produksi);
				$this->db->delete('production_spk_parsial', array('kode_spk'=>$kode_spk,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('production_spk_input', array('kode_spk'=>$kode_spk,'spk'=>'1'));
				
				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_plus', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));

				$this->db->where_in('id_spk',$ArrWhereIN_);
				$this->db->where('status_date',$hist_produksi);
				$this->db->delete('tmp_production_real_detail_add', array('catatan_programmer'=>$kode_spk_created,'spk'=>'1'));
			}

			if(!empty($ARrInsertSPK)){
				$this->db->insert_batch('production_spk_parsial',$ARrInsertSPK);
			}
			if(!empty($ArrGroup)){
				$this->db->insert_batch('production_spk_input',$ArrGroup);
			}
			if(!empty($ArrAktualResin)){
				$this->db->insert_batch('tmp_production_real_detail',$ArrAktualResin);
			}
			if(!empty($ArrAktualPlus)){
				$this->db->insert_batch('tmp_production_real_detail_plus',$ArrAktualPlus);
			}
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_spk'	=> $kode_spk
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=>'Process Success. Thank you & have a nice day ...',
				'status'	=> 1,
				'kode_spk'	=> $kode_spk
			);
			$this->closing_produksi_deadstok($ARR_ID_PRO_UNIQ);
			$this->closing_produksi_base_jurnal($kode_spk_created,$id_gudang,14);
			history('Input aktual spk produksi utama '.$kode_spk);
		}
		echo json_encode($Arr_Kembali);
	}
	
	
	public function closing_produksi($ARR_ID_PRO_UNIQ){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		
		$HelpDet3 		= "tmp_production_real_detail";
		$HelpDet4 		= "tmp_production_real_detail_plus";
		$HelpDet5 		= "tmp_production_real_detail_add";
		// print_r($ARR_ID_PRO_UNIQ); exit;
		if(!empty($ARR_ID_PRO_UNIQ)){
			$restDetail1	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet3)->result_array();
			$restDetail2	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet4)->result_array();
			$restDetail3	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet5)->result_array();
			// exit;

			$ArrDetail = array();
			if(!empty($restDetail1)){
				foreach($restDetail1 AS $val => $valx){
					$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
					$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrDetail[$val]['id_product'] = $valx['id_product'];
					$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
					$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
					$ArrDetail[$val]['benang'] = $valx['benang'];
					$ArrDetail[$val]['bw'] = $valx['bw'];
					$ArrDetail[$val]['layer'] = $valx['layer'];
					$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrDetail[$val]['status'] = $valx['status'];
					$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrDetail[$val]['status_date'] = $dateTime;
					$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrDetail[$val]['spk'] = $valx['spk'];
					$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
					$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
					$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrPlus = array();
			if(!empty($restDetail2)){
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status'] = $valx['status'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = $dateTime;
					$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrPlus[$val]['spk'] = $valx['spk'];
					$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
					$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
					$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrAdd = array();
			if(!empty($restDetail3)){
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status'] = $valx['status'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = $dateTime;
					$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrAdd[$val]['spk'] = $valx['spk'];
					$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
					$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
					$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			
			if(!empty($ArrDetail)){
				$this->db->insert_batch('production_real_detail', $ArrDetail);
			}
			if(!empty($ArrPlus)){
				$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			}
			if(!empty($ArrAdd)){
				$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			}

			foreach ($ARR_ID_PRO_UNIQ as $value) {

				$QUERY_GET1 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";

				$QUERY_GET2 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail_plus a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET = $QUERY_GET1.'UNION'.$QUERY_GET2;
				// echo $QUERY_GET;
				// exit;
				$getData = $this->db->query($QUERY_GET)->result_array();
				
				if(!empty($getData)){
					$ArrWIP = array(
						'id_produksi' => $getData[0]['id_produksi'],
						'id_milik' => $getData[0]['id_milik'],
						'id_production_detail' => $getData[0]['id_production_detail'],
						'qty_akhir' => $getData[0]['qty_akhir'],
						'product_ke' => $getData[0]['product_ke'],
						'id_category' => $getData[0]['id_category'],
						'id_product' => $getData[0]['id_product'],
						'status_date' => $getData[0]['status_date'],
						'kode_trans' => $getData[0]['kode_trans'],
						'qty' => $getData[0]['qty'],
					);

					$this->save_report_wip_closing($ArrWIP);

				}
			}
		}
	}

	public function closing_produksi_deadstok($ARR_ID_PRO_UNIQ){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		
		$HelpDet3 		= "tmp_production_real_detail";
		$HelpDet4 		= "tmp_production_real_detail_plus";
		$HelpDet5 		= "tmp_production_real_detail_add";
		
		if(!empty($ARR_ID_PRO_UNIQ)){
			$restDetail1	= $this->db->where_in('id_detail',$ARR_ID_PRO_UNIQ)->get_where($HelpDet3,array('id_product'=>'deadstok'))->result_array();
			$restDetail2	= $this->db->where_in('id_detail',$ARR_ID_PRO_UNIQ)->get_where($HelpDet4,array('id_product'=>'deadstok'))->result_array();
			$restDetail3	= $this->db->where_in('id_detail',$ARR_ID_PRO_UNIQ)->get_where($HelpDet5,array('id_product'=>'deadstok'))->result_array();
			// exit;

			$ArrDetail = array();
			if(!empty($restDetail1)){
				foreach($restDetail1 AS $val => $valx){
					$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
					$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrDetail[$val]['id_product'] = $valx['id_product'];
					$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
					$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
					$ArrDetail[$val]['benang'] = $valx['benang'];
					$ArrDetail[$val]['bw'] = $valx['bw'];
					$ArrDetail[$val]['layer'] = $valx['layer'];
					$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrDetail[$val]['status'] = $valx['status'];
					$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrDetail[$val]['status_date'] = $dateTime;
					$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrDetail[$val]['spk'] = $valx['spk'];
					$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
					$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
					$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrPlus = array();
			if(!empty($restDetail2)){
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status'] = $valx['status'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = $dateTime;
					$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrPlus[$val]['spk'] = $valx['spk'];
					$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
					$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
					$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrAdd = array();
			if(!empty($restDetail3)){
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status'] = $valx['status'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = $dateTime;
					$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrAdd[$val]['spk'] = $valx['spk'];
					$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
					$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
					$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			
			if(!empty($ArrDetail)){
				$this->db->insert_batch('production_real_detail', $ArrDetail);
			}
			if(!empty($ArrPlus)){
				$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			}
			if(!empty($ArrAdd)){
				$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			}
		}
	}

	public function get_stock_material(){
		$data 			= $this->input->post();
		$id_material 	= $data['id_material'];
		$id_gudang 		= $data['id_gudang'];
		$GET_STOCK 		= get_warehouseStockMaterial();
		$KEY 			= $id_material .'-'.$id_gudang;
		echo json_encode(array(
			'stock'	=> (!empty($GET_STOCK[$KEY]))?number_format($GET_STOCK[$KEY],4):0,
			'status' => 1
		));
	}

	//DEADSTOCK
	public function booking_deadstok($no_ipp=null, $id_milik=null, $max_booking=null){
		$username 		= $this->session->userdata['ORI_User']['username'];

		$this->db->where('created_by', $username);
		$this->db->delete('booking_deadstok_temp');

		$data = array(
			'title'			=> 'Booking Deadstok',
			'action'		=> 'index',
			'no_ipp'		=> $no_ipp,
			'id_milik'		=> $id_milik,
			'max_booking'	=> $max_booking,
			'tandaTanki'	=> substr($no_ipp,0,4),
			'GET_NO_SPK'	=> get_detail_final_drawing()
		);
		$this->load->view('Deadstok/booking_deadstok', $data);
	}

	public function server_side_deadstok()
	{
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_deadstok(
			$requestData['no_ipp'],
			$requestData['id_milik'],
			$requestData['max_booking'],
			$requestData['search']['value'],
			$requestData['order'][0]['column'],
			$requestData['order'][0]['dir'],
			$requestData['start'],
			$requestData['length']
		);
		$totalData		= $fetch['totalData'];
		$totalFiltered	= $fetch['totalFiltered'];
		$query			= $fetch['query'];

		// print_r($query->result_array());
		// exit;

		$data	= array();
		$urut1  = 1;
		$urut2  = 0;
		foreach ($query->result_array() as $row) {
			$total_data     = $totalData;
			$start_dari     = $requestData['start'];
			$asc_desc       = $requestData['order'][0]['dir'];
			if ($asc_desc == 'asc') {
				$nomor = $urut1 + $start_dari;
			}
			if ($asc_desc == 'desc') {
				$nomor = ($total_data - $start_dari) - $urut2;
			}

			$username 		= $this->session->userdata['ORI_User']['username'];

			$CHECK = $this->db->get_where('booking_deadstok_temp', array('id_product' => $row['id_product'], 'created_by' => $username))->result();
			$checked = (!empty($CHECK[0]->qty_booking)) ? $CHECK[0]->qty_booking : '';

			$GETBOOK = $this->db->select('COUNT(id) AS qty_book')->get_where('production_detail',array('id_product_deadstok' => $row['id_product'],'id_deadstok_dipakai'=>NULL))->result();
			$QTY_BOOK = (!empty($GETBOOK[0]->qty_book))?$GETBOOK[0]->qty_book:0;

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>" . $nomor . "</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['type']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_name']."</div>";
			$nestedData[]	= "<div align='left'>".$row['type_std']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product_spec']."</div>";
			$nestedData[]	= "<div align='left'>".$row['resin']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['length'])."</div>";
			$nestedData[]	= "<div align='center' class='qty_stock' title='".$QTY_BOOK."'>".number_format($row['qty_stock'] - $QTY_BOOK)."</div>";
			$nestedData[]	= "<div align='center'>
									<input type='text' name='spk_" . $row['id_product'] . "' style='width:80px;' class='form-control text-center qty_booking input-md numberOnly0' autocomplete='off'  data-no_ipp='".$requestData['no_ipp']."' data-id_milik='".$requestData['id_milik']."' data-max_booking='".$requestData['max_booking']."' data-id_product='".$row['id_product']."' value='".$checked."' ><script>$('.numberOnly0').autoNumeric('init', {mDec: '0', aPad: false});</script>
								</div>";
			// $nestedData[]	= "<div align='center'><input type='checkbox' name='check[$nomor]' $checked class='chk_personal  chk_material' data-no_ipp='".$requestData['no_ipp']."' data-id_milik='".$requestData['id_milik']."' data-max_booking='".$requestData['max_booking']."' value='".$row['id_product']."' ></div>";

			$data[] = $nestedData;
			$urut1++;
			$urut2++;
		}

		// print_r($data);
		// exit;

		$json_data = array(
			"draw"            	=> intval($requestData['draw']),
			"recordsTotal"    	=> intval($totalData),
			"recordsFiltered" 	=> intval($totalFiltered),
			"data"            	=> $data
		);

		echo json_encode($json_data);
	}

	public function query_data_deadstok($no_ipp, $id_milik, $max_booking, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL)
	{

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.*,
					COUNT(qty) AS qty_stock
				FROM
					deadstok a,
					(SELECT @row:=0) r
				WHERE 
					a.deleted_date IS NULL
					AND a.kode_delivery IS NULL
					AND a.id_booking IS NULL
				AND(
					a.type LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_name LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product_spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
				GROUP BY a.id_product
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();

		$columns_order_by = array(
			0 => 'nomor',
			1 => 'type',
			2 => 'product_name',
			3 => 'type_std',
			4 => 'product_spec',
			5 => 'resin',
			6 => 'length',
			7 => 'qty'
		);

		$sql .= " ORDER BY " . $columns_order_by[$column_order] . " " . $column_dir . " ";
		$sql .= " LIMIT " . $limit_start . " ," . $limit_length . " ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function temporerBookingDeadstok()
	{
		$username 		= $this->session->userdata['ORI_User']['username'];
		$no_ipp 		= $this->input->post('no_ipp');
		$id_milik 		= $this->input->post('id_milik');
		$max_booking 	= $this->input->post('max_booking');
		$id_product 	= $this->input->post('id_product');
		$qty_booking 	= str_replace(',', '', $this->input->post('qty_booking'));

		$ArrData = [
			'no_ipp' => $no_ipp,
			'id_milik' => $id_milik,
			'max_booking' => $max_booking,
			'id_product' => $id_product,
			'qty_booking' => $qty_booking,
			'created_by' => $username
		];

		$CHECK = $this->db->get_where('booking_deadstok_temp', array('id_product' => $id_product, 'id_milik' => $id_milik))->result();


		$this->db->trans_start();
			if (empty($CHECK)) {
				$this->db->insert('booking_deadstok_temp', $ArrData);
			} else {
				$this->db->where('id_product', $id_product);
				$this->db->where('id_milik', $id_milik);
				$this->db->update('booking_deadstok_temp', $ArrData);
			}
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'status'	=> '0',
				'pesan'	=> 'Failed !!!',
			);
		} else {
			$this->db->trans_commit();

			$DETAIL_BACK = $this->db->select('SUM(qty_booking) AS qty_booking, max_booking')->get_where('booking_deadstok_temp', array('created_by' => $username))->result();

			$Arr_Kembali	= array(
				'status'	=> '1',
				'pesan'	=> 'Success !!!',
				'qty_booking'	=> (!empty($DETAIL_BACK))?$DETAIL_BACK[0]->qty_booking:0,
				'max_booking'	=> (!empty($DETAIL_BACK))?$DETAIL_BACK[0]->max_booking:0
			);
		}
		echo json_encode($Arr_Kembali);
	}

	public function process_booking_deadstok()
	{
		$data 		= $this->input->post();

		$username 	= $this->session->userdata['ORI_User']['username'];
		$datetime 	= date('Y-m-d H:i:s');

		$no_ipp 	= $data['no_ipp'];
		$id_milik 	= $data['id_milik'];

		$DETAIL 			= $this->db->get_where('booking_deadstok_temp', array('created_by' => $username))->result_array();
		$DETAIL_SUM 		= $this->db->select('SUM(qty_booking) AS jumlah')->get_where('booking_deadstok_temp', array('created_by' => $username))->result_array();
		$DETAILUPDATE 		= $this->db->get_where('production_detail', array('id_produksi' => 'PRO-'.$no_ipp,'id_milik' => $id_milik,'kode_spk' => NULL))->result_array();

		$YM	= date('y');
		$srcPlant		= "SELECT MAX(kode_booking_deadstok) as maxP FROM production_detail WHERE kode_booking_deadstok LIKE 'BK-" . $YM . "%' ";
		$resultPlant	= $this->db->query($srcPlant)->result_array();
		$angkaUrut2		= $resultPlant[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 5, 6);
		$urutan2++;
		$urut2			= sprintf('%06s', $urutan2);
		$kode_booking_deadstok	= "BK-" . $YM . $urut2;

		// print_r($DETAIL);
		// exit;
		
		$ArrUpdatePro = [];
		$nomor = 1;
		$SUM_MAX = $DETAIL_SUM[0]['jumlah'];
		foreach ($DETAIL as $key => $value) {
			for ($i=1; $i <= $value['qty_booking']; $i++) { 
				$ArrUpdatePro[$nomor]['id_product_deadstok'] = $value['id_product'];

				$nomor++;
			}
		}
		// echo $SUM_MAX;
		$nomor = 1;
		foreach ($DETAILUPDATE as $key => $value) { 
			if($nomor <= $SUM_MAX){ 
				$ArrUpdatePro[$nomor]['id'] = $value['id'];
				$ArrUpdatePro[$nomor]['booking_by'] = $username;
				$ArrUpdatePro[$nomor]['booking_date'] = $datetime;
				$ArrUpdatePro[$nomor]['kode_booking_deadstok'] = $kode_booking_deadstok;

				$nomor++;
			}
		}

		// print_r($ArrUpdatePro);
		// exit;
		$this->db->trans_start();
			if (!empty($ArrUpdatePro)) {
				$this->db->update_batch('production_detail', $ArrUpdatePro, 'id');
			}

			$this->db->where('created_by', $username);
			$this->db->delete('booking_deadstok_temp');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Failed. Please try again later ...',
				'status'	=> 2,
				'kode_booking_deadstok' => $kode_booking_deadstok
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'pesan'		=> 'Process Success. Thanks ...',
				'status'	=> 1,
				'kode_booking_deadstok' => $kode_booking_deadstok
			);
			history('Create booking deadstok '.$kode_booking_deadstok);
		}
		echo json_encode($Arr_Kembali);
	}

	public function print_booking_deadstok(){
		$id_booking	= $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

		$GET_NO_SPK = get_detail_final_drawing();
		$GET_DET_IPP = get_detail_ipp();

		$detail = $this->db
							->select('a.*, COUNT(id) AS qty_booking')
							->group_by('id_product_deadstok')
							->get_where('production_detail a', array('a.kode_booking_deadstok'=>$id_booking))
							->result_array();
		$no_ipp 	= str_replace('PRO-','',$detail[0]['id_produksi']);
		$SOTanki 	= substr($detail[0]['product_code'],0,9);

		$so_pipafitting = (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'-';

		$TandaTanki = substr($no_ipp,0,4);

		$so_number 	= ($TandaTanki=='IPPT')?$SOTanki:$so_pipafitting;

		$data = array(
			'title' 		=> 'Booking Deadstok',
			'action' 		=> 'print',
			'printby' 		=> $printby,
			'detail' 		=> $detail,
			'id_booking'	=> $id_booking,
			'no_ipp' 		=> $no_ipp,
			'no_so'			=> $so_number,
			'no_spk'		=> $detail[0]['no_spk'],
		);
		$this->load->view('Deadstok/print_booking_deadstok', $data);
	}

	public function history_print_outgoing(){
		$no_ipp			= $this->uri->segment(3);
		$id_milik		= $this->uri->segment(4);
		$result_data 	= $this->db->get_where('production_spk', array('no_ipp'=>$no_ipp,'id_milik'=>$id_milik))->result_array();

		$data = array(
			'result_data' 	=> $result_data,
			'tanki_model' 			=> $this->tanki_model
		);
		$this->load->view('Produksi/history_print_outgoing', $data);
	}

	public function spk_print_outgoing(){
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');

		$data = array(
			'title'			=> 'Re-Print SPK',
			'action'		=> 'index',
			// 'result'		=> $result,
			'row_group'		=> $data_Group,
			// 'akses_menu'	=> $Arr_Akses
		);
		history('View Data spk');
		$this->load->view('Produksi/spk_print_outgoing',$data);
	}

	public function server_side_spk_print(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1))).'/index_loose';
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spk_print(
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

			$spec = spec_bq2($row['id_milik']);
			if($row['tanda'] == 'deadstok'){
				$est_deadstok = $row['est_deadstok'];
				$HeaderDeadstok = $this->db
									->select('a.id, b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty, b.id_milik')
									->group_by('a.kode')
									->join('deadstok b','a.id_deadstok=b.id','left')
									->get_where('deadstok_modif a',array('kode'=>$est_deadstok))
									->result_array();
				$spec = $HeaderDeadstok[0]['product_spec'];
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".$row['nm_customer']."</div>";
			$nestedData[]	= "<div align='left'>".$row['product']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_spk']."</div>";
			$nestedData[]	= "<div align='left'>".$spec."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qty'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s',strtotime($row['created_date']))."</div>";

			$print = "<a href='".site_url('produksi/spk_baru/').$row['kode_spk']."' target='_blank'>Print</a>";

			$nestedData[]	= "<div align='center'>".$print."</div>";
			
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

	public function query_data_spk_print($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$where = "";

		$sql = "SELECT
					(@row:=@row+1) AS nomor,
					a.no_ipp AS no_ipp,
					b.so_number,
					a.id_milik,
					a.no_spk,
					a.product AS product,
					a.kode_spk AS kode_spk,
					c.nm_customer,
					a.qty AS qty,
					a.created_by,
					a.created_date,
					a.id_product AS tanda,
					a.product_code_cut AS est_deadstok
				FROM
					production_spk a 
					LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp) = b.id_bq
					LEFT JOIN production c ON a.no_ipp = c.no_ipp
				WHERE
					a.id_product != 'tanki'
				AND (
					a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.product LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.id_milik LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
		";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.no_ipp',
			2 => 'b.so_number',
			3 => 'c.nm_customer',
			4 => 'a.product',
			5 => 'a.no_spk',
			6 => 'a.id_milik'
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

	public function closing_produksi_base_jurnal($kode_spk_time,$id_gudang,$id_gudang_ke){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username = $this->session->userdata['ORI_User']['username'];
		$datetime = date('Y-m-d H:i:s');
		
		$UserName = $this->session->userdata['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');
		
		$restDetail1	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail2	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_plus',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail3	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_add',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();

		$restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3);
		$dateKurs = date('Y-m-d');
		// $dateKurs = '2025-01-02';
		$GET_COSTBOOK = getPriceBookByDateproduksi($dateKurs);
		$GET_MAERIALS = get_detail_material();
		$GET_MATERIAL	= get_detail_material();
		//KURS
		$sqlkurs	= "select * from ms_kurs where tanggal <='".$dateKurs."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$temp = [];
		$tempMaterial = [];
		$ArrIDSPK = [];
		$ArrUpdateStock = [];
		$SUM_MATERIAL = 0;
		$QTY_OKE = 0;
		foreach ($restDetail as $key => $value) {
			$UNIQ = $value['kode_trans'].'-'.$value['id_material'];

			if(!array_key_exists($UNIQ, $temp)) {
				$temp[$UNIQ]['berat'] = 0;
			}
			$temp[$UNIQ]['berat'] += $value['berat'];

			$temp[$UNIQ]['tanggal'] 	= $dateKurs;
			$temp[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
			$temp[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
			$temp[$UNIQ]['id_material'] = $value['id_material'];

			$nm_material = (!empty($GET_MAERIALS[$value['id_material']]['nm_material']))?$GET_MAERIALS[$value['id_material']]['nm_material']:'';
			$temp[$UNIQ]['nm_material'] = $nm_material;
			$temp[$UNIQ]['id_spk'] 		= $value['id_spk'];
			$temp[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
			$temp[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";

			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$temp[$UNIQ]['no_so'] 		= (!empty($getDetailSPK[0]['product_code']))?substr($getDetailSPK[0]['product_code'],0,9):'';
			$temp[$UNIQ]['product'] 	= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$temp[$UNIQ]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$temp[$UNIQ]['id_milik']	= (!empty($getDetailSPK[0]['id_milik']))?$getDetailSPK[0]['id_milik']:'';

			//$costbook 	= (!empty($GET_COSTBOOK[$value['id_material']]))?$GET_COSTBOOK[$value['id_material']]:0;
			$idmaterial2 = $value['id_material'];

			$getcostbook = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$idmaterial2),1)->row();
			if(!empty($getcostbook)) $costbook=$getcostbook->harga;
			$berat 		 = $temp[$UNIQ]['berat'];
			// $SUM_MATERIAL += round($costbook * $berat);
			
			$temp[$UNIQ]['costbook'] 		= $costbook;
			$temp[$UNIQ]['kurs'] 			= $kurs;
			$temp[$UNIQ]['total_price'] 	= round($costbook * $berat);
			$temp[$UNIQ]['total_price_debet'] 	= 0;
			$temp[$UNIQ]['created_by'] 		= $username;
			$temp[$UNIQ]['created_date'] 	= $datetime;

			$ArrUpdateStock[$UNIQ]['id'] 	= $value['id_material'];
			$ArrUpdateStock[$UNIQ]['qty'] 	= $berat;

			$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans']))->result_array();
			$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
			$temp[$UNIQ]['id_trans'] = $id_trans;

			$ArrIDSPK[$value['id_pro_det']] = $value['id_pro_det'];


			//Group Material
			$UNIQ2 = $value['id_material'];
			if(!array_key_exists($UNIQ2, $tempMaterial)) {
				$tempMaterial[$UNIQ2]['qty'] = 0;
			}
			
			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$tempMaterial[$UNIQ2]['tanggal'] 		= $datetime;
			$tempMaterial[$UNIQ2]['keterangan'] 	= 'laporan produksi';
			$tempMaterial[$UNIQ2]['no_ipp'] 		= $value['no_ipp'];
			$tempMaterial[$UNIQ2]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$tempMaterial[$UNIQ2]['product'] 		= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$tempMaterial[$UNIQ2]['kode_trans'] 	= $value['kode_trans'];
			$tempMaterial[$UNIQ2]['id_material'] 	= $value['id_material'];
			$tempMaterial[$UNIQ2]['nm_material'] 	= $nm_material;
			$tempMaterial[$UNIQ2]['qty'] 			+= $value['berat'];
			$tempMaterial[$UNIQ2]['cost_book'] 		= $costbook;
			$tempMaterial[$UNIQ2]['created_by'] 	= $username;
			$tempMaterial[$UNIQ2]['created_date'] 	= $datetime;
			$tempMaterial[$UNIQ2]['tipe'] 			= 'out';
			$tempMaterial[$UNIQ2]['gudang'] 		= $id_gudang;
			$tempMaterial[$UNIQ2]['gudang_dari'] 	= $id_gudang;
			$tempMaterial[$UNIQ2]['gudng_ke'] 		= $id_gudang_ke;
			
			$getDetailSPK1 = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det']))->result_array();
			
			$id_trans1 = (!empty($getDetailSPK1[0]['id']))?$getDetailSPK1[0]['id']:0;
			
			$id_material = $value['id_material'];

                $coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;				 
					
					$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$QTY_OKE  = $berat; 
				$ACTUAL_MAT = $value['id_material'];
				$kode_trans = $id_trans1;
				$PRICE     = $costbook;
				
				$ArrJurnalNew[$UNIQ2]['id_material'] 		= $ACTUAL_MAT;
				$ArrJurnalNew[$UNIQ2]['idmaterial'] 		= $GET_MATERIAL[$ACTUAL_MAT]['idmaterial'];
				$ArrJurnalNew[$UNIQ2]['nm_material'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
				$ArrJurnalNew[$UNIQ2]['id_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
				$ArrJurnalNew[$UNIQ2]['nm_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
				$ArrJurnalNew[$UNIQ2]['id_gudang'] 			= $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_dari'] 	    = $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew[$UNIQ2]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$UNIQ2]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$UNIQ2]['tgl_trans'] 			= $DateTime;
				$ArrJurnalNew[$UNIQ2]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['ket'] 				= 'pindah gudang produksi - wip';
				$ArrJurnalNew[$UNIQ2]['harga'] 			= $PRICE;
				$ArrJurnalNew[$UNIQ2]['harga_bm'] 		= 0;
				$ArrJurnalNew[$UNIQ2]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$UNIQ2]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$UNIQ2]['update_by'] 		= $UserName;
				$ArrJurnalNew[$UNIQ2]['update_date'] 		= $DateTime;
				$ArrJurnalNew[$UNIQ2]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$UNIQ2]['coa_gudang'] 		= $coa_gudang;
			
				
		}
		//biaya WIP
		$ArrDataWIP = ['Direct labour','Indirect labour','Consumable','FOH','Total'];
		$temp2 = [];
		if(!empty($temp)){
			foreach ($ArrDataWIP as $value2) {
				foreach ($temp as $key => $value) {
					$UNIQ = $value['id_spk'].'-'.$value2;

					$temp2[$UNIQ]['berat'] 		= 0;

					$WIPNmProduct = ($value2 == 'Total')?$value['product']:$value2;

					$temp2[$UNIQ]['tanggal'] 		= $dateKurs;
					$temp2[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
					$temp2[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
					$temp2[$UNIQ]['id_material'] = NULL;
					$temp2[$UNIQ]['nm_material'] = 'WIP '.$WIPNmProduct;
					$temp2[$UNIQ]['id_spk'] 		= $value['id_spk'];
					$temp2[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
					$temp2[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";
					$temp2[$UNIQ]['no_so'] 		= $value['no_so'];
					$temp2[$UNIQ]['product'] 	= $value['product'];
					$temp2[$UNIQ]['no_spk'] 		= $value['no_spk'];
					$temp2[$UNIQ]['id_milik']	= $value['id_milik'];
					
					// $Explode = explode('/',$value['kode_trans']);
					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det']))->result_array();
					$real_harga = (!empty($getDetailSPK[0]['real_harga']))?$getDetailSPK[0]['real_harga']:0;
					$direct_labour = (!empty($getDetailSPK[0]['direct_labour']))?$getDetailSPK[0]['direct_labour']:0;
					$indirect_labour = (!empty($getDetailSPK[0]['indirect_labour']))?$getDetailSPK[0]['indirect_labour']:0;
					$consumable = (!empty($getDetailSPK[0]['consumable']))?$getDetailSPK[0]['consumable']:0;
					$machine = (!empty($getDetailSPK[0]['machine']))?$getDetailSPK[0]['machine']:0;
					$mould_mandrill = (!empty($getDetailSPK[0]['mould_mandrill']))?$getDetailSPK[0]['mould_mandrill']:0;
					$foh_depresiasi = (!empty($getDetailSPK[0]['foh_depresiasi']))?$getDetailSPK[0]['foh_depresiasi']:0;
					$biaya_rutin_bulanan = (!empty($getDetailSPK[0]['biaya_rutin_bulanan']))?$getDetailSPK[0]['biaya_rutin_bulanan']:0;
					$foh_consumable = (!empty($getDetailSPK[0]['foh_consumable']))?$getDetailSPK[0]['foh_consumable']:0;
					
					$nilai = 0;
					$nilai2 = 0;
					if($value2 == 'Direct labour'){
						$nilai = round($direct_labour*$kurs);
					}
					if($value2 == 'Indirect labour'){
						$nilai = round($indirect_labour*$kurs);
					}
					if($value2 == 'Consumable'){
						$nilai = round($consumable*$kurs);
					}
					if($value2 == 'FOH'){
						$nilai = round(($machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
					}
					if($value2 == $value['product']){
						$nilai1 = round(($direct_labour+ $indirect_labour+$consumable + $machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
						$nilai  = $nilai1;
						$nilai2 = $nilai1;
					}					
					
					$temp2[$UNIQ]['costbook'] 		= 0;
					$temp2[$UNIQ]['kurs'] 			= $kurs;
					$temp2[$UNIQ]['total_price'] 		= $nilai;
					$temp2[$UNIQ]['total_price_debet'] 	= $nilai2;
					$temp2[$UNIQ]['created_by'] 		= $username;
					$temp2[$UNIQ]['created_date'] 	= $datetime;

					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
					$temp2[$UNIQ]['id_trans'] = $id_trans;
				}
			}
		}

		$dataWIP = array_merge($temp,$temp2);
		// echo "<pre>";
		// print_r($dataWIP);
		// exit;
		if(!empty($dataWIP)){
			$this->db->insert_batch('data_erp_wip',$dataWIP);
		}
		if(!empty($ArrUpdateStock)){
			move_warehouse($ArrUpdateStock,$id_gudang,$id_gudang_ke,$kode_spk_time);
		}

		//GROUP DATA
		$ArrGroup = [];
		if(!empty($ArrIDSPK)){
			foreach ($ArrIDSPK as $value) {
				if($value > 0){
					$getSummary = $this->db->select('no_so,product,no_spk')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time))->result_array();

					$ArrGroup[$value]['tanggal'] = $dateKurs;
					$ArrGroup[$value]['keterangan'] = 'Gudang produksi to WIP';
					$ArrGroup[$value]['no_so'] = (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroup[$value]['kode_trans'] = $kode_spk_time;
					$ArrGroup[$value]['id_pro_det'] = $value;

					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$kode_spk_time,'id_production_detail'=>$value))->result_array();
					$qty_awal = (!empty($getDetailSPK[0]['qty_awal']))?$getDetailSPK[0]['qty_awal']:0;
					$qty_akhir = (!empty($getDetailSPK[0]['qty_akhir']))?$getDetailSPK[0]['qty_akhir']:0;
					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;

					$ArrGroup[$value]['qty'] = $qty_akhir - $qty_awal + 1;

					$getSummaryMaterial 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'id_material <>'=>NULL))->result_array();
					$getSummaryDirect 		= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Direct labour'))->result_array();
					$getSummaryIndirect 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Indirect labour'))->result_array();
					$getSummaryConsumable 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP Consumable'))->result_array();
					$getSummaryFOH 			= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'nm_material'=>'WIP FOH'))->result_array();
					
					$nilai_material 	= (!empty($getSummaryMaterial[0]['nilai']))?$getSummaryMaterial[0]['nilai']:0;
					$nilai_direct 		= (!empty($getSummaryDirect[0]['nilai']))?$getSummaryDirect[0]['nilai']:0;
					$nilai_indirect 	= (!empty($getSummaryIndirect[0]['nilai']))?$getSummaryIndirect[0]['nilai']:0;
					$nilai_consumable 	= (!empty($getSummaryConsumable[0]['nilai']))?$getSummaryConsumable[0]['nilai']:0;
					$nilai_foh 			= (!empty($getSummaryFOH[0]['nilai']))?$getSummaryFOH[0]['nilai']:0;
					$nilai_wip			= $nilai_material + $nilai_direct + $nilai_indirect + $nilai_consumable + $nilai_foh;
					
					$ArrGroup[$value]['nilai_wip'] = $nilai_wip;
					$ArrGroup[$value]['material'] = $nilai_material;
					$ArrGroup[$value]['wip_direct'] =  $nilai_direct;
					$ArrGroup[$value]['wip_indirect'] =  $nilai_indirect;
					$ArrGroup[$value]['wip_consumable'] =  $nilai_consumable;
					$ArrGroup[$value]['wip_foh'] =  $nilai_foh;
					$ArrGroup[$value]['created_by'] = $username;
					$ArrGroup[$value]['created_date'] = $datetime;
					$ArrGroup[$value]['id_trans'] = $id_trans;
					
					$this->db->where('id_trans',$id_trans);
					$this->db->where('nm_material','WIP '.$getSummary[0]['product']);
					$this->db->update('data_erp_wip',array('total_price'=>0,'total_price_debet'=>$nilai_wip)); 
				}
			}
		}


		

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_wip_group',$ArrGroup);
			$this->jurnalWIP($id_trans);
		}
		if(!empty($tempMaterial)){
			$this->db->insert_batch('erp_data_subgudang',$tempMaterial);
		}
		if(!empty($ArrJurnalNew)){
			$this->db->insert_batch('tran_warehouse_jurnal_detail',$ArrJurnalNew);
		}


	}

	public function closing_produksi_tanki($ARR_ID_PRO_UNIQ){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime 		= date('Y-m-d H:i:s');
		
		$HelpDet3 		= "tmp_production_real_detail";
		$HelpDet4 		= "tmp_production_real_detail_plus";
		$HelpDet5 		= "tmp_production_real_detail_add";
		// print_r($ARR_ID_PRO_UNIQ); exit;
		if(!empty($ARR_ID_PRO_UNIQ)){
			$restDetail1	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet3)->result_array();
			$restDetail2	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet4)->result_array();
			$restDetail3	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet5)->result_array();
			// exit;

			$ArrDetail = array();
			if(!empty($restDetail1)){
				foreach($restDetail1 AS $val => $valx){
					$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
					$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrDetail[$val]['id_product'] = $valx['id_product'];
					$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
					$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
					$ArrDetail[$val]['benang'] = $valx['benang'];
					$ArrDetail[$val]['bw'] = $valx['bw'];
					$ArrDetail[$val]['layer'] = $valx['layer'];
					$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrDetail[$val]['status'] = $valx['status'];
					$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrDetail[$val]['status_date'] = $dateTime;
					$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrDetail[$val]['spk'] = $valx['spk'];
					$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
					$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
					$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrPlus = array();
			if(!empty($restDetail2)){
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status'] = $valx['status'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = $dateTime;
					$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrPlus[$val]['spk'] = $valx['spk'];
					$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
					$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
					$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrAdd = array();
			if(!empty($restDetail3)){
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status'] = $valx['status'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = $dateTime;
					$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrAdd[$val]['spk'] = $valx['spk'];
					$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
					$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
					$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			
			if(!empty($ArrDetail)){
				$this->db->insert_batch('production_real_detail', $ArrDetail);
			}
			if(!empty($ArrPlus)){
				$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			}
			if(!empty($ArrAdd)){
				$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			}

			foreach ($ARR_ID_PRO_UNIQ as $value) {

				$QUERY_GET1 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET2 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail_plus a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET = $QUERY_GET1.'UNION'.$QUERY_GET2;
				$getData = $this->db->query($QUERY_GET)->result_array();
				
				if(!empty($getData)){
					$ArrWIP = array(
						'id_produksi' => $getData[0]['id_produksi'],
						'id_milik' => $getData[0]['id_milik'],
						'id_production_detail' => $getData[0]['id_production_detail'],
						'qty_akhir' => $getData[0]['qty_akhir'],
						'product_ke' => $getData[0]['product_ke'],
						'id_category' => $getData[0]['id_category'],
						'id_product' => $getData[0]['id_product'],
						'status_date' => $getData[0]['status_date'],
						'kode_trans' => $getData[0]['kode_trans'],
						'qty' => $getData[0]['qty'],
					);

					$this->save_report_wip_closing_tanki($ArrWIP);

				}
			}
		}
	}

	public function save_report_wip_closing_tanki($ArrData){

		$sqlkurs	= "select * from ms_kurs where tanggal <='".date('Y-m-d')."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$sqlEstMaterial = "SELECT SUM(berat) AS est_berat, SUM(berat*price) AS est_price FROM est_material_tanki WHERE id_det='".$ArrData['id_milik']."' GROUP BY id_det";
        $restEstMat	    = $this->db->query($sqlEstMaterial)->result_array();

		$jumTot     = ($ArrData['qty_akhir'] - $ArrData['product_ke']) + 1;

        $est_material_bef          = (!empty($restEstMat[0]['est_berat']))?$restEstMat[0]['est_berat']:0;
        $est_harga_bef             = (!empty($restEstMat[0]['est_price']))?$restEstMat[0]['est_price']:0;

        $est_material           = $est_material_bef * $jumTot;
        $est_harga              = $est_harga_bef * $jumTot;

		$sqlBy 		= " SELECT
							a.dia_lebar AS diameter,
							a.panjang AS diameter2,
							a.t_dsg AS pressure,
							a.t_est AS liner,
							a.man_hours AS man_hours,
							(a.man_hours * a.pe_direct_labour) AS direct_labour,
							(a.man_hours * a.pe_indirect_labour) AS indirect_labour,
							(a.t_time * a.pe_machine) AS machine,
							0 AS mould_mandrill,
							($est_material * a.pe_consumable) AS consumable,
							(
									((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
							) * ( a.pe_foh_consumable / 100 ) AS foh_consumable,
							(
									((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
							) * ( a.pe_foh_depresiasi / 100 ) AS foh_depresiasi,
							(
									((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
							) * ( a.pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
							(
									((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
							) * ( a.pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
							(
									(((a.man_hours * a.pe_direct_labour))+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
							) * ( a.pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
						FROM
								bq_detail_detail a
						WHERE a.id='".$ArrData['id_milik']."' LIMIT 1";
		
		$restBy		= $this->tanki->query($sqlBy)->result_array();
		
		$sqlBan         = " SELECT 
								SUM(a.material_terpakai) AS real_material, 
								SUM(a.material_terpakai*b.price) AS real_harga 
							FROM 
								production_real_detail a
								INNER JOIN est_material_tanki b ON a.id_detail=b.id
							WHERE a.id_production_detail='".$ArrData['id_production_detail']."' 
							GROUP BY a.id_production_detail";
		$restBan	= $this->db->query($sqlBan)->result_array();

		$real_material          = (!empty($restBan[0]['real_material']))?$restBan[0]['real_material']:0;
        $real_harga             = (!empty($restBan[0]['real_harga']))?$restBan[0]['real_harga']:0;
        $real_harga_rp          = $real_harga * $kurs;
		// echo $sqlEst."<br>";
		
		
		$sqlInsertDet = "INSERT INTO laporan_wip_per_hari_action
							(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
							qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
							real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
							consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
							biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,kode_trans)
							VALUE
							('".$ArrData['id_produksi']."','".$ArrData['id_category']."','".$ArrData['id_product']."',
							'".$restBy[0]['diameter']."','".$restBy[0]['diameter2']."','0',
							'0','".$ArrData['status_date']."','".$ArrData['product_ke']."',
							'".$ArrData['qty_akhir']."','".$ArrData['qty']."','".date('Y-m-d',strtotime($ArrData['status_date']))."','".$ArrData['id_production_detail']."',
							'".$ArrData['id_milik']."','".$est_material."','".$est_harga."',
							'".$real_material."','".$real_harga."','".$restBy[0]['direct_labour'] * $jumTot."',
							'".$restBy[0]['indirect_labour'] * $jumTot."','".$restBy[0]['machine'] * $jumTot."',
							'".$restBy[0]['mould_mandrill'] * $jumTot."','".$restBy[0]['consumable'] * $jumTot."',
							'".$restBy[0]['foh_consumable'] * $jumTot."','".$restBy[0]['foh_depresiasi'] * $jumTot."',
							'".$restBy[0]['biaya_gaji_non_produksi'] * $jumTot."','".$restBy[0]['biaya_non_produksi'] * $jumTot."',
							'".$restBy[0]['biaya_rutin_bulanan'] * $jumTot."','system','".date('Y-m-d H:i:s')."','".$restBy[0]['man_hours'] * $jumTot."','".$real_harga_rp."','".$kurs."','".$ArrData['kode_trans']."')
						";
		// echo $sqlInsertDet;
		// exit;
		$this->db->query($sqlInsertDet);
	}
	
	
	function jurnalWIP($idtrans){
		
		//$idtrans       = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$UserName		= $data_session['ORI_User']['username'];
		$DateTime		= date('Y-m-d H:i:s');
		
		
	
		    $kodejurnal='JV004';
		  	

			$wip = $this->db->query("SELECT * FROM data_erp_wip WHERE id_trans ='".$idtrans."'")->result();
			
			$totalwip =0;
			$wiptotal =0; 
			$det_Jurnaltes = [];
			  
			foreach($wip AS $data){
				
				$nm_material = $data->nm_material;	
				$product 	 = $data->product;	
				$tgl_voucher = $data->tanggal;	
				$keterangan  = $data->nm_material;
				$id          = $data->id_trans;
				$noso 		 = ','.$data->no_so;
                $no_request  = $data->no_spk;	
				$kredit      = $data->total_price;
				$totalwip       = $data->total_price_debet;	
				$wiptotal       += $data->total_price;	
				
				if($nm_material=='WIP Direct labour'){					
					$nokir = '2107-01-02' ;
				}elseif($nm_material=='WIP Indirect labour'){					
					$nokir = '2107-01-03' ;
				}elseif($nm_material=='WIP Consumable'){					
					$nokir = '2107-01-01' ;				
				}elseif($nm_material=='WIP FOH'){					
					$nokir = '2107-01-04' ;
                }
				else{
					$nokir = '1103-01-03' ;
				}
				
				
				
				
					if($product=='pipe'){
						$nokirwip ='1103-03-02';	
					}else{
						$nokirwip ='1103-03-03';	
					}					
					

			    $debit  = $totalwip;			
				
				if($totalwip != 0 ){
					 $det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokirwip,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id.$noso,
					  'debet'         => $wiptotal,
					  'kredit'        => 0,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					   );
					
				}else{
								
					$det_Jurnaltes[]  = array(
					  'nomor'         => '',
					  'tanggal'       => $tgl_voucher,
					  'tipe'          => 'JV',
					  'no_perkiraan'  => $nokir,
					  'keterangan'    => $keterangan,
					  'no_reff'       => $id,
					  'debet'         => 0,
					  'kredit'        => $kredit,
					  'jenis_jurnal'  => 'produksi wip',
					  'no_request'    => $no_request,
					  'stspos'		  =>1
					 );
				}
				
			}
			
			       
				
			
			$this->db->query("delete from jurnaltras WHERE jenis_jurnal='produksi wip' and no_reff ='$id'");
			$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
			
			
			
			$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
			$Bln	= substr($tgl_voucher,5,2);
			$Thn	= substr($tgl_voucher,0,4);
			$idlaporan = $id;
			$Keterangan_INV = 'Jurnal Produksi - WIP';
			$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalwip, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
			$this->db->insert(DBACC.'.javh',$dataJVhead);
			$datadetail=array();
			foreach ($det_Jurnaltes as $vals) {
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tgl_voucher,
					'no_perkiraan'	=> $vals['no_perkiraan'],
					'keterangan'	=> $vals['keterangan'],
					'no_reff'		=> $vals['no_reff'],
					'debet'			=> $vals['debet'],
					'kredit'		=> $vals['kredit'],
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
			}
			unset($det_Jurnaltes);unset($datadetail);
		  
		}
		
}