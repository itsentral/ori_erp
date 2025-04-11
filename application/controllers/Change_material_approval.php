<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_material_approval extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('material_planning_model');
		$this->load->model('purchase_request_model');
		$this->load->model('purchase_order_model');
		$this->load->model('warehouse_model');
		$this->load->model('adjustment_material_model');
		$this->load->model('Jurnal_model');
		$this->load->model('tanki_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		// print_r($Arr_Akses);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$no_ipp				= $this->db->query("SELECT
                                                    REPLACE(a.id_produksi, 'PRO-', '') AS no_ipp,
													b.so_number AS nomor_so,
                                                    c.no_so AS nomor_so_tanki
												FROM
													production_detail a
                                                    LEFT JOIN so_number b ON REPLACE(b.id_bq, 'BQ-', '') = REPLACE(a.id_produksi, 'PRO-', '')
                                                    LEFT JOIN planning_tanki c ON c.no_ipp = REPLACE(a.id_produksi, 'PRO-', '')
												GROUP BY a.id_produksi")->result_array();

        $list_ipp_req		= $this->db->query("SELECT no_ipp FROM warehouse_adjustment WHERE category='request material change' GROUP BY no_ipp")->result_array();
		
		$data = array(
			'title'			=> 'Approval Material Change',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'no_ipp'		=> $no_ipp,
			'list_ipp_req'		=> $list_ipp_req,
			'tanki'			=> $this->tanki_model,
		);
		$this->load->view('Engineering_change/approval_change/index',$data);
	}

    public function list_spk(){
		$data       = $this->input->post();
        $no_ipp     = $data['no_ipp'];
		$Q_result	= $this->db->group_by('no_spk')->get_where('production_detail',array('id_produksi'=>'PRO-'.$no_ipp))->result();
        if(!empty($Q_result)){
            $option = "<option value='0'>Pilih No SPK</option>";
            foreach($Q_result as $row)
            {
                $option .= "<option value='".$row->no_spk."'>".$row->no_spk."</option>";
            }
        }
        else{
            $option = "<option value='0'>Data not found !</option>";
        }
		echo json_encode(array(
			'option' => $option
		));
	}

    public function get_data_json_material_change(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_material_change(
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

			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_so'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_spk'])."</div>";
			$nestedData[]	= "<div align='left'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i', strtotime($row['created_date']))."</div>";
            
            $FileEC = "";
			if(!empty($row['file_eng_change'])){
				$FileEC = "<a href='".base_url('assets/file/produksi/').$row['file_eng_change']."' target='_blank'>Download</a>";
			}

			$nestedData[]	= "<div align='center'>".$FileEC."</div>";
            $nestedData[]	= "<div align='left'>".$row['keterangan']."</div>";
            $plus	= "&nbsp;<button type='button' class='btn btn-sm btn-info check' title='Konfirmasi' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-check'></i></button>";
					

			$nestedData[]	= "<div align='center'>".$plus."</div>";
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

	public function query_data_json_material_change($no_ipp, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where_no_ipp ='';
		if(!empty($no_ipp)){
			$where_no_ipp = " AND a.no_ipp = '".$no_ipp."' ";
		}

        $sql = "
            SELECT
                (@row:=@row+1) AS nomor,
                a.*
            FROM
                warehouse_adjustment a,
                (SELECT @row:=0) r
            WHERE 1=1 
                AND a.category = 'request material change' 
                AND a.deleted IS NULL 
                AND a.status_id = '1'
                AND a.checked = 'N'
                ".$where_no_ipp."
            AND(
                a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.created_by LIKE '%".$this->db->escape_like_str($like_value)."%'
            )
        ";
		
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_trans',
			2 => 'no_so',
			3 => 'no_spk',
			4 => 'created_by',
			5 => 'created_date'
		);

		$sql .= " ORDER BY id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function modal_request(){
        $data           = $this->input->post();
        $kode_trans     = $data['kode_trans'];
        $get_detail     = $this->db->limit(1)->get_where('warehouse_adjustment',array('kode_trans'=>$kode_trans))->result_array();
        $no_ipp         = (!empty($get_detail[0]['no_ipp']))?$get_detail[0]['no_ipp']:'';

        $tandaTank  = substr($no_ipp,0,4);

        if($tandaTank != 'IPPT'){
            $get_liner_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."' 
											AND keterangan = 'LINER THIKNESS / CB'
										)
										UNION
										(
                                        SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."' 
											AND keterangan = 'RESIN AND ADD'
										)")->result_array();
            $get_structure_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."'
                                            AND keterangan = 'STRUKTUR THICKNESS' 
                                        )")->result_array();
            $get_external_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."'
                                            AND keterangan = 'EXTERNAL LAYER THICKNESS' 
                                        )")->result_array();
            $get_topcoat_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."'
                                            AND keterangan = 'TOPCOAT' 
                                        )")->result_array();
            $get_str_n1_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."'
                                            AND keterangan = 'STRUKTUR NECK 1' 
                                        )")->result_array();
                                        
            $get_str_n2_mix = $this->db->query("
                                        (SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
										FROM
											warehouse_adjustment_detail 
										WHERE
											kode_trans = '".$kode_trans."'
                                            AND keterangan = 'STRUKTUR NECK 2' 
                                        )")->result_array();
        }
        else{
            $get_external_mix = [];
            $get_str_n1_mix = [];
            $get_str_n2_mix = [];
            $get_liner_mix = $this->db->query("	SELECT
                                                    id,
                                                    id_po_detail AS id_milik,
                                                    id_material_req AS id_material,
                                                    nm_material,
                                                    qty_order AS berat,
                                                    keterangan AS layer,
                                                    ket_req_pro AS ket_req_pro,
                                                    check_keterangan AS check_keterangan
                                                FROM
                                                    warehouse_adjustment_detail 
                                                WHERE
                                                    kode_trans = '".$kode_trans."' 
                                                    AND (keterangan = 'liner' OR keterangan = 'primer')
                                                ")->result_array();
            $get_structure_mix = $this->db->query("	SELECT
                                                id,
                                                id_po_detail AS id_milik,
                                                id_material_req AS id_material,
                                                nm_material,
                                                qty_order AS berat,
                                                keterangan AS layer,
                                                ket_req_pro AS ket_req_pro,
                                                check_keterangan AS check_keterangan
                                            FROM
                                                warehouse_adjustment_detail 
                                            WHERE
                                                kode_trans = '".$kode_trans."' 
                                                AND keterangan = 'structure'
                                            ")->result_array();
            $get_topcoat_mix = $this->db->query("	SELECT
                                            id,
											id_po_detail AS id_milik,
											id_material_req AS id_material,
											nm_material,
										    qty_order AS berat,
                                            keterangan AS layer,
                                            ket_req_pro AS ket_req_pro,
                                            check_keterangan AS check_keterangan
                                        FROM
                                            warehouse_adjustment_detail 
                                        WHERE
                                            kode_trans = '".$kode_trans."' 
                                            AND keterangan = 'topcoat'
                                        ")->result_array();
        }	
        
        
        $data = array(
			'kode_trans' 	=> $kode_trans,
			'no_ipp' 	=> $no_ipp,
			'no_spk'    => $get_detail[0]['no_spk'],
			'no_so'    => $get_detail[0]['no_so'],
			'upload_spk'    => $get_detail[0]['file_eng_change'],
            'keterangan'    => $get_detail[0]['keterangan'],
			'get_liner_utama'    => $get_liner_mix,
			'get_structure_utama'    => $get_structure_mix,
			'get_external_utama'    => $get_external_mix,
			'get_topcoat_utama'    => $get_topcoat_mix,
			'get_str_n1_utama'    => $get_str_n1_mix,
			'get_str_n2_utama'    => $get_str_n2_mix,
		);

		$this->load->view('Engineering_change/approval_change/modal_request', $data);
	}

    public function process_request(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
        $kode_trans     = $data['kode_trans'];
        $no_ipp     = $data['no_ipp'];
		$UserName = $data_session['ORI_User']['username'];
		$DateTime = date('Y-m-d H:i:s');

        $detailReqChange = [];
        $detailReqUpdate = [];
        $nomor = 0;
		if(!empty($data['detail_liner'])){
			foreach ($data['detail_liner'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}
        if(!empty($data['detail_strn1'])){
			foreach ($data['detail_strn1'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}
        if(!empty($data['detail_strn2'])){
			foreach ($data['detail_strn2'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}
        if(!empty($data['detail_str'])){
			foreach ($data['detail_str'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}
        if(!empty($data['detail_ext'])){
			foreach ($data['detail_ext'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}
        if(!empty($data['detail_topcoat'])){
			foreach ($data['detail_topcoat'] as $key => $value) { $nomor++;
                $detailReqChange[$nomor]['id'] 		        = $value['id'];
                $detailReqChange[$nomor]['id_material']     = $value['actual_type'];
                if($value['actual_type'] != $value['id_material']){
                    $det_mat        = $this->db->get_where('raw_materials', array('id_material'=>$value['actual_type']))->result();

                    $detailReqUpdate[$nomor]['id_update'] 		= $value['id_update'];
                    $detailReqUpdate[$nomor]['table_update'] 	= $value['table_update'];
                    $detailReqUpdate[$nomor]['id_material']     = $value['actual_type'];
                    $detailReqUpdate[$nomor]['nm_material']     = $det_mat[0]->nm_material;
                }
            }
		}

		$ArrInsertH = array(
			'checked' 		    => 'Y',
			'checked_by' 		=> $UserName,
			'checked_date' 		=> $DateTime
		);

        if(empty($detailReqUpdate)){
            $ArrInsertH = array(
                'checked' 		    => 'Y',
                'checked_by' 		=> $UserName,
                'checked_date' 		=> $DateTime,
                'deleted' 		    => $DateTime
            );
        }

        $tandaTank  = substr($no_ipp,0,4);
		// print_r($ArrInsertH);
		// print_r($detailReqChange);
		// exit;
		$this->db->trans_start();
			$this->db->where('kode_trans', $kode_trans);
			$this->db->update('warehouse_adjustment', $ArrInsertH);
			if(!empty($detailReqChange)){
				$this->db->update_batch('warehouse_adjustment_detail', $detailReqChange, 'id');
			}
            if(!empty($detailReqUpdate)){
                foreach ($detailReqUpdate as $key => $value) {
                    $ArrUpdate = [
                        'id_material' => $value['id_material'],
                        'nm_material' => $value['nm_material']
                    ];
                    if($tandaTank != 'IPPT'){
                        $this->db->where_in('id_detail', $value['id_update']);
                        $this->db->update($value['table_update'], $ArrUpdate);
                    }
                    else{
                        $this->db->where_in('id', $value['id_update']);
                        $this->db->update($value['table_update'], $ArrUpdate);
                    }
                }
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
			history("Request engeneering change : ".$kode_trans);
		}
		echo json_encode($Arr_Data);
	}

}