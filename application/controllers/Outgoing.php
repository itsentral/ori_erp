<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outgoing extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

    public function outgoing_gudang_pusat(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category='pusat' ORDER BY urut ASC")->result_array();
		$no_po	    = $this->db->group_by('id_bq')->order_by('id_bq','asc')->select('id_bq, SUM(qty_out) AS qty_out, SUM(qty) AS qty')->get_where('so_acc_and_mat',array('category'=>'mat','approve'=>'P'))->result_array();
		$no_field	= $this->db->group_by('id_bq')->order_by('id_bq','asc')->select('id_bq, SUM(qty_out) AS qty_out, SUM(qty) AS qty')->get_where('request_outgoing',array('category'=>'mat','approve'=>'P'))->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'outgoing pusat'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'outgoing pusat'))->result_array();

		$data = array(
			'title'			=> 'Warehouse Material >> Gudang Pusat >> Outgoing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
			'no_field'		=> $no_field,
			'data_gudang'	=> $data_gudang,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po
		);
		history('View outgoing gudang pusat'); 
		$this->load->view('Outgoing/outgoing_gudang_pusat',$data);
	}

    public function get_customer(){
		$data       = $this->input->post();
		$tipe_out   = $data['tipe_out'];
		
        $tujuan_out = '';
        if($tipe_out != '0'){
            $no_ipp   = str_replace('BQ-','',$data['tipe_out']);
            $tujuan_out = get_name('production','nm_customer','no_ipp',$no_ipp);
        }

        $Arr_Kembali	= array(
            'tujuan_out' => $tujuan_out
        );
        echo json_encode($Arr_Kembali);
	}

    public function server_side_outgoing(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_outgoing(
			$requestData['no_po'],
			$requestData['gudang'],
			$requestData['tipe'],
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

			$IP_ID = $row['no_ipp'];
			if($requestData['tipe'] == 'subgudang'){
				$IP_ID = get_name('warehouse_adjustment','no_ipp','kode_trans',$row['no_ipp']);
			}

			$type = $IP_ID;
			if($type != 'non-so'){
				$type = $row['so_number'];
			}

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['kode_trans'])."</div>"; 
			$nestedData[]	= "<div align='center'>".strtoupper($type)."</div>"; 
			$nestedData[]	= "<div align='center'>".strtoupper($row['no_spk'])."</div>"; 

			$WHEREHOUSE = get_name('warehouse', 'nm_gudang', 'id', $row['id_gudang_dari']);
			if($requestData['tipe'] == 'subgudang'){
				$get_wherehouse = $this->db->group_by('key_gudang')->select('key_gudang')->get_where('warehouse_adjustment_detail',array('kode_trans'=>$row['kode_trans'],'key_gudang !='=>null))->result_array();
				$ArrGroup = [];
				foreach ($get_wherehouse as $key => $value){
					$ArrGroup[] = get_name('warehouse', 'nm_gudang', 'id', $value['key_gudang']);
				}
				$WHEREHOUSE = implode('<br>',$ArrGroup);;
			}
			// $nestedData[]	= "<div align='left'>".$row['kd_gudang_dari']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($WHEREHOUSE)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($row['jumlah_mat'],2)."</div>";
			$nestedData[]	= "<div align='center'>".$row['created_by']."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['created_date']))."</div>";
				$plus	= "";
				
				$print	= "&nbsp;<a href='".base_url('outgoing/print_outgoing_non_so/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Incoming'><i class='fa fa-print'></i></a>";
				if($type == 'non-so'){
					$print	= "&nbsp;<a href='".base_url('outgoing/print_outgoing_non_so/'.$row['kode_trans'])."' target='_blank' class='btn btn-sm btn-warning' title='Print Incoming'><i class='fa fa-print'></i></a>";
				}

				$edit	= "<button type='button' class='btn btn-sm btn-success edit' title='Edit Print' data-kode_trans='".$row['kode_trans']."'><i class='fa fa-edit'></i></button>";


			$nestedData[]	= "<div align='center'>
									<button type='button' class='btn btn-sm btn-primary detailAjust' title='View Incoming' data-kode_trans='".$row['kode_trans']."' ><i class='fa fa-eye'></i></button>
                                    ".$print."
									".$edit."
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

	public function query_data_json_outgoing($no_po, $gudang, $tipe, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where_no_po ='';
		if(!empty($no_po)){
			$where_no_po = " AND a.no_ipp = '".$no_po."' ";
		}
		
		$where_gudang ='';
		if(!empty($gudang)){
			$where_gudang = " AND a.id_gudang_ke = '".$gudang."' ";
		}

		$table_where ='outgoing pusat';
		if($tipe == 'subgudang'){
			$table_where ='outgoing subgudang';
		}
		
		$sql = "
			SELECT
				a.*,
				b.so_number
			FROM
				warehouse_adjustment a
				LEFT JOIN so_number b ON a.no_ipp = b.id_bq
		    WHERE 1=1 AND a.category = '".$table_where."' AND a.status_id = '1'
				".$where_no_po."
				".$where_gudang."
			AND(
				a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kd_gudang_ke LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.kode_trans LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_ipp',
			2 => 'b.so_number',
			3 => 'a.no_spk'
		);

		$sql .= " ORDER BY created_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function modal_outgoing(){
		$data 			= $this->input->post();
		$tipe_out 		= $data['tipe_out'];
		$field_joint 	= $data['field_joint'];
		$gudang_before 	= $data['gudang_before'];
		$gudang_origa 	= $data['gudang_origa'];
		$tujuan_out 	= $data['tujuan_out'];
		$NO_IPP 		= str_replace('BQ-','',$tipe_out);
		$ID_CUST 		= (!empty(get_detail_ipp()[$NO_IPP]['id_customer']))?get_detail_ipp()[$NO_IPP]['id_customer']:0;

		$result	= array();
		if($tipe_out != 'non-so'){
			if($field_joint == 'no'){
				$sql = "SELECT a.* FROM so_acc_and_mat a WHERE a.id_bq='".$tipe_out."' AND qty_out < qty AND category = 'mat'";
			}
			else{
				$sql = "SELECT a.* FROM request_outgoing a WHERE a.id_bq='".$tipe_out."' AND qty_out < qty AND category = 'mat'";
			}
            $result	= $this->db->query($sql)->result_array();
		}
		$subgudang		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
		$data = array(
			'id_customer' => $ID_CUST,
			'GET_STOCK' => get_warehouseStockMaterial(),
			'tipe_out' => $tipe_out,
			'field_joint' => $field_joint,
			'gudang' => $gudang_before,
			'tujuan_out' => $tujuan_out,
			'gudang_origa' => $gudang_origa,
			'subgudang' => $subgudang,
			'result' => $result
		);
		
		$this->load->view('Outgoing/modal_outgoing', $data);
	}

	public function print_outgoing_non_so(){
		$kode_trans     = $this->uri->segment(3);
		$check     		= $this->uri->segment(4);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'kode_trans' => $kode_trans,
			'check' => $check
		);
		history('Print outgoing gudang pusat non-so '.$kode_trans);
		$this->load->view('Print/print_outgoing_non_so', $data);
	}

    public function get_add(){
		$id 			= $this->uri->segment(3);
		$gudang_origa 	= $this->uri->segment(4);
		$no 			= 0;

		$WHERE_ORIGA = " AND id != '23'";
		if($gudang_origa == '23'){
			$WHERE_ORIGA = " AND id = '23'";
		}

        $material	 = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
        $subgudang		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ".$WHERE_ORIGA." ORDER BY urut ASC")->result_array();
    	
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='addInMat[".$id."][id_material]' class='chosen_select form-control input-sm material'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($material AS $row){
				  $d_Header .= "<option value='".$row->id_material."'>".strtoupper($row->nm_material)."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='addInMat[".$id."][stock]' placeholder='Stock' class='form-control input-md text-center autoNumeric4 stockval' readonly></td>";
			$d_Header .= "<td align='left'><input type='text' name='addInMat[".$id."][qty_in]' placeholder='Qty' class='form-control input-md text-center autoNumeric4 qtyval'></td>";
            $d_Header .= "<td align='left'>";
                $d_Header .= "<select name='addInMat[".$id."][sub_gudang]' class='chosen_select form-control input-sm sub_gudang'>";
				foreach($subgudang AS $row => $valx){
                    $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
                  }
				$d_Header .= "</select>";
            $d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='addInMat[".$id."][keterangan]' placeholder='Keterangan' class='form-control input-md text-left'></td>";
			
            $d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>";
			$d_Header .= "<td align='center' colspan='4'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

	public function get_add_custom(){
		$id 	= $this->uri->segment(3);
		$no 	= 0;

        // $material	 = $this->master_model->getDataOrderBy('raw_materials','delete','N','nm_material');
        $category	 = $this->master_model->getDataOrderBy('raw_categories','flag_active','Y','category');
        $subgudang		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();
    	
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='center'>";
				$d_Header .= "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'></td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='addInMat[".$id."][id_category]' class='chosen_select form-control input-sm category'>";
				$d_Header .= "<option value='0'>Select Category</option>";
				foreach($category AS $row){
				  $d_Header .= "<option value='".$row->id_category."'>".strtoupper($row->category)."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='addInMat[".$id."][id_material]' class='chosen_select form-control input-sm material'>";
				$d_Header .= "<option value='0'>List Empty</option>";
				// foreach($material AS $row){
				//   $d_Header .= "<option value='".$row->id_material."'>".strtoupper($row->nm_material)."</option>";
				// }
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'></td>";
			$d_Header .= "<td align='right'><input type='text' name='addInMat[".$id."][qty_stock]' readonly data-no='".$id."' class='form-control input-md text-right autoNumeric2 stockval'></td>";
			$d_Header .= "<td align='left'></td>";
			$d_Header .= "<td align='left'><input type='text' name='addInMat[".$id."][qty_in]' placeholder='Qty' class='form-control input-md text-right autoNumeric4 qtyval'></td>";
            $d_Header .= "<td align='left'>";
                $d_Header .= "<select name='addInMat[".$id."][sub_gudang]' class='chosen_select form-control input-sm sub_gudang'>";
				foreach($subgudang AS $row => $valx){
                    $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
                  }
				$d_Header .= "</select>";
            $d_Header .= "</td>";
			$d_Header .= "<td align='left'><input type='text' name='addInMat[".$id."][keterangan]' placeholder='Keterangan' class='form-control input-md text-left'></td>";
			
           
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='left'><button type='button' class='btn btn-sm btn-success addPartCustom' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>";
			$d_Header .= "<td align='center' colspan='4'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}

    public function process_out_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tipe_out		= $data['tipe_out'];
		$gudang			= $data['gudang'];
		$tujuan_out		= $data['tujuan_out'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$field_joint 	= $data['field_joint'];
		$Ym 			= date('ym');

		$table_utama = 'so_acc_and_mat';
		if($field_joint == 'yes'){
			$table_utama = 'request_outgoing';
		}
		// echo $no_po;
		// print_r($addInMat);
		// exit;
        $histHlp = "Material outgoing: ".$nm_gudang_ke." / ".$tipe_out;

		if($adjustment == 'OUT'){
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrMaterial	 = array();
			$SumMat = 0;
            if($tipe_out != 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $sqlWhDetail	= "	SELECT
                                        a.*,
                                        b.idmaterial,
                                        b.nm_material,
                                        b.id_category,
                                        b.nm_category
                                    FROM
                                        ".$table_utama." a
                                        LEFT JOIN raw_materials b
                                            ON a.id_material=b.id_material
                                    WHERE
                                        a.id = '".$valx['id']."'
                                    ";
                    $restWhDetail	= $this->db->query($sqlWhDetail)->result();

                    //update detail so material
                    $ArrUpdate[$val]['id'] 			= $valx['id'];
                    $ArrUpdate[$val]['qty_out'] 	= $restWhDetail[0]->qty_out + $qtyIN;
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material_req'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
					$ArrDeatilAdj[$val]['key_gudang'] 		= $valx['sub_gudang'];
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id_material'] 	= $valx['id_material_req'];
                    $ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;

                }
            }

            if($tipe_out == 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $restWhDetail	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= NULL;
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= $qtyIN;
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
                    $ArrDeatilAdj[$val]['key_gudang'] 		= $valx['sub_gudang'];
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id_material'] 	= $valx['id_material'];
                    $ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;

                }
            }

            //GROUPING UPDATE MATERIAL PER GUDANG
			$ArrGrouping = [];
			foreach ($ArrMaterial as $key => $value) {
				$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
				$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
			}

			$gudang_dari = $gudang;
			foreach ($ArrGrouping as $key => $value) {
				move_warehouse($value,$gudang_dari,$key,$kode_trans);
			}

			// print_r($ArrGrouping);
			// exit;

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $tipe_out,
				'note' 			    => $tujuan_out,
				'category' 			=> 'outgoing pusat',
				'jumlah_mat' 		=> $SumMat,
				'id_gudang_dari' 	=> $gudang,
				'kd_gudang_dari' 	=> $nm_gudang_ke,
				'kd_gudang_ke' 		=> 'SUBGUDANG',
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime
			);


			// print_r($ArrUpdate);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit;
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);

				// if($tipe_out == 'non-so'){
					
				// }
				if($tipe_out != 'non-so'){
					$this->db->update_batch($table_utama, $ArrUpdate, 'id');
				}
			$this->db->trans_complete();
		}


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
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}

	public function process_fg_material(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tipe_out		= $data['tipe_out'];
		$gudang			= $data['gudang'];
		$tujuan_out		= $data['tujuan_out'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$id_customer 	= $data['id_customer'];
		$id_gudang_origa 	= $data['id_gudang_origa'];
		$field_joint 	= $data['field_joint'];
		$Ym 			= date('ym'); 

		$table_utama = 'so_acc_and_mat';
		if($field_joint == 'yes'){
			$table_utama = 'request_outgoing';
		}
		// echo $no_po;
		// print_r($addInMat);
		// exit;
        $histHlp = "Material outgoing to FG: ".$nm_gudang_ke." / ".$tipe_out;

		if($id_customer == 'C100-2104003'){
			$YM	= date('y');
			$srcPlant		= "SELECT MAX(kode_delivery) as maxP FROM delivery_product WHERE kode_delivery LIKE 'DV-".$YM."%' ";
			$resultPlant	= $this->db->query($srcPlant)->result_array();
			$angkaUrut2		= $resultPlant[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 5, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_delivery	= "DV-".$YM.$urut2;

			$HeaderDelivery = [
				'kode_delivery' => $kode_delivery,
				'fm_no' => 'FM-C4.1-02',
				'issue_date' => 'Jan 18th, 2016',
				'created_by' => $data_session['ORI_User']['username'],
				'created_date' => $dateTime,
				'updated_by' => $data_session['ORI_User']['username'],
				'updated_date' => $dateTime,
				'lock_delivery_by' => $data_session['ORI_User']['username'],
				'lock_delivery_date' => $dateTime,
				'material' => 'Y'
			];
		}

		if($adjustment == 'OUT'){
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrMaterial	 = array();
			$DetailDelivery	 = array();
			$SumMat = 0;
            if($tipe_out != 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $sqlWhDetail	= "	SELECT
                                        a.*,
                                        b.idmaterial,
                                        b.nm_material,
                                        b.id_category,
                                        b.nm_category
                                    FROM
                                        ".$table_utama." a
                                        LEFT JOIN raw_materials b
                                            ON a.id_material=b.id_material
                                    WHERE
                                        a.id = '".$valx['id']."'
                                    ";
                    $restWhDetail	= $this->db->query($sqlWhDetail)->result();

                    //update detail so material
                    $ArrUpdate[$val]['id'] 			= $valx['id'];
                    $ArrUpdate[$val]['qty_out'] 	= $restWhDetail[0]->qty_out + $qtyIN;
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material_req'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
					$ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id'] 	= $valx['id_material_req'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;


					//MASUK DELIVERY
					if($id_customer == 'C100-2104003'){
						$DetailDelivery[$val]['kode_delivery'] = $kode_delivery;
						$DetailDelivery[$val]['product'] = $valx['id_material_req'];
						$DetailDelivery[$val]['id_milik'] = $valx['id'];
						$DetailDelivery[$val]['id_produksi'] = $tipe_out;
						$DetailDelivery[$val]['updated_by'] = $data_session['ORI_User']['username'];
						$DetailDelivery[$val]['updated_date'] = $dateTime;
						$DetailDelivery[$val]['posisi'] = 'TRANSIT';
						$DetailDelivery[$val]['sts_product'] = 'so material';
						$DetailDelivery[$val]['berat'] = $qtyIN;
					}
                }
            }

            if($tipe_out == 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $restWhDetail	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= NULL;
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= $qtyIN;
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
                    $ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id'] 	= $valx['id_material'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;

                }
            }

            //MOVING TO FINISH GOOD
			$gudang_dari = $gudang;
			$gudang_ke = 15;
			$kd_gudang_ke = 'FINISH GOOD';
			if($id_customer == 'C100-2104003' OR $id_gudang_origa == '23'){
				$gudang_ke = 23;
				$kd_gudang_ke = 'ORIGA MULIA';
			}

			$temp = [];
			$tempx = [];
			$grouping_temp = [];
			foreach($ArrMaterial as $value) {
				if(!array_key_exists($value['id'], $tempx)) {
					$tempx[$value['id']]['good'] = 0;
				}
				$tempx[$value['id']]['good'] += $value['qty'];
	
				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['qty_good'] 	= $tempx[$value['id']]['good'];
			}

			move_warehouse($ArrMaterial,$gudang_dari,$gudang_ke,$kode_trans);

			// print_r($ArrGrouping);
			// exit;

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $tipe_out,
				'note' 			    => $tujuan_out,
				'category' 			=> 'outgoing pusat',
				'jumlah_mat' 		=> $SumMat,
				'id_gudang_dari' 	=> $gudang,
				'kd_gudang_dari' 	=> $nm_gudang_ke,
				'id_gudang_ke' 		=> $gudang_ke,
				'kd_gudang_ke' 		=> $kd_gudang_ke,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime
			);


			// print_r($ArrUpdate);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit;
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);

				if($id_customer == 'C100-2104003' OR $id_gudang_origa == '23'){
					insert_jurnal($grouping_temp,$gudang_dari,$gudang_ke,$kode_trans,'gudang pusat - origa','pengurangan gudang pusat','outgoing ke origa');
				}

				if(!empty($HeaderDelivery)){
					$this->db->insert('delivery_product',$HeaderDelivery);
				}
				if(!empty($DetailDelivery)){
					$this->db->insert_batch('delivery_product_detail',$DetailDelivery);
				}
				if($tipe_out != 'non-so'){
					$this->db->update_batch($table_utama, $ArrUpdate, 'id');
				}
			$this->db->trans_complete();
		}


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
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}

	public function modal_detail_outgoing(){
		$kode_trans     = $this->uri->segment(3);

		$sql 		= "SELECT * FROM warehouse_adjustment_detail WHERE kode_trans='".$kode_trans."' ";
		$result		= $this->db->query($sql)->result_array();
		
		$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
		$result_header		= $this->db->query($sql_header)->result();

		$no_po = $result_header[0]->no_ipp;
		if($result_header[0]->no_ipp != 'non-so'){
			$no_po = get_name('so_number','so_number','id_bq',$result_header[0]->no_ipp);
		}
		
		$data = array(
			'result' 	=> $result,
			'checked' 	=> $result_header[0]->checked,
			'kode_trans'=> $result_header[0]->kode_trans,
			'outgoing_ke'=> $result_header[0]->note,
			'outgoing_dari'=> get_name('warehouse','nm_gudang','id',$result_header[0]->id_gudang_dari),
			'no_po' 	=> $no_po,
			'dated' 	=> date('ymdhis', strtotime($result_header[0]->created_date)),
			'resv' 		=> date('d F Y', strtotime($result_header[0]->created_date))
			
		);

		$this->load->view('Outgoing/modal_detail_outgoing', $data);
	}

	//SUBGUDANG
	public function outgoing_subgudang(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)).'/'.strtolower($this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$SQL = "SELECT b.no_ipp, a.kode_trans FROM warehouse_adjustment_detail a LEFT JOIN warehouse_adjustment b ON a.kode_trans=b.kode_trans WHERE b.category='outgoing pusat' AND b.checked='N' AND a.key_gudang IS NOT NULL AND a.proccess_date IS NULL GROUP BY a.kode_trans ORDER BY a.no_ipp ASC ";
		$no_po	    = $this->db->query($SQL)->result_array();
		$no_po2	    = $this->db->group_by('id_bq')->order_by('id_bq','asc')->select('id_bq, SUM(qty_out) AS qty_out, SUM(qty) AS qty')->get_where('so_acc_and_mat',array('category'=>'mat','approve'=>'P'))->result_array();
		$no_field2	= $this->db->group_by('id_bq')->order_by('id_bq','asc')->select('id_bq, SUM(qty_out) AS qty_out, SUM(qty) AS qty, id_milik')->get_where('request_outgoing',array('category'=>'mat','approve'=>'P'))->result_array();
		$list_po	= $this->db->group_by('no_ipp')->get_where('warehouse_adjustment',array('category'=>'outgoing subgudang'))->result_array();
		$data_gudang= $this->db->group_by('id_gudang_ke')->get_where('warehouse_adjustment',array('category'=>'outgoing subgudang'))->result_array();
		$pusat		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();

		$QUERY_FIELD = "SELECT
							CONCAT( 'BQ-', a.no_ipp ) AS id_bq,
							a.id_milik,
							SUM( a.qty ) AS qty,
							SUM( b.qty ) AS qty_out 
						FROM
							production_spk a
							INNER JOIN outgoing_field_joint b ON a.id_milik = b.id_milik 
							AND a.created_date = b.date_uniq 
						WHERE
							b.deleted_date IS NULL 
						GROUP BY
							a.no_ipp
						ORDER BY
							a.no_ipp";
		$no_field	= $this->db->query($QUERY_FIELD)->result_array();

		$data = array(
			'title'			=> 'Warehouse Material >> Sub Gudang >> Outgoing',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'list_po'		=> $list_po,
			'data_gudang'	=> $data_gudang,
			'no_field'		=> $no_field,
			'no_field2'		=> $no_field2,
			'pusat'			=> $pusat,
			'no_po'			=> $no_po,
			'no_po2'		=> $no_po2
		);
		history('View outgoing subgudang'); 
		$this->load->view('Outgoing/outgoing_subgudang',$data);
	}

	public function get_customer2(){
		$data       = $this->input->post();
		$tipe_out   = $data['tipe_out'];
		$field_joint   = $data['field_joint'];
		$tanda 		= substr($tipe_out,0,3);

        $tujuan_out = '';
        $option = '';
        if($tipe_out != '0'){
            // $no_ipp   = str_replace('BQ-','',$data['tipe_out']);
			if($tanda == 'TRS'){
				$tujuan_out 	= get_name('warehouse_adjustment','note','kode_trans',$tipe_out);
				$gudang_dari 	= get_name('warehouse_adjustment','id_gudang_dari','kode_trans',$tipe_out);
				$nm_gudang 		= get_name('warehouse','nm_gudang','id',$gudang_dari);
				$option			= "<option value='$gudang_dari'>".strtoupper($nm_gudang)."</option>";
			}
			else{
				$no_ipp   	= str_replace('BQ-','',$tipe_out);
            	$tujuan_out = get_name('production','nm_customer','no_ipp',$no_ipp);
				$get_gudang = $this->db->get_where('warehouse',array('category'=>'subgudang'))->result_array();
				foreach ($get_gudang as $key => $value) {
					$option	.= "<option value='".$value['id']."'>".strtoupper($value['nm_gudang'])."</option>";
				}
			}
        }

		//FIELD JOINT
		$option2 = '';
		if($field_joint == 'yes'){
			$getSPK = $this->db->select('a.id_milik, a.approve_date, b.id_category AS product, b.no_spk')->group_by('a.id_milik, a.approve_date')->join('so_detail_header b','a.id_milik=b.id','left')->get_where('request_outgoing a',array('a.id_bq'=>$tipe_out,'a.id_milik !='=>NULL))->result_array();
			// $option2 .= "<option value='0'>ALL SPK</option>";
			foreach ($getSPK as $key => $value) {
				$option2	.= "<option value='".$value['id_milik']."/".$value['approve_date']."'>SPK ".$value['no_spk']." -  [".date('d-M-Y H:i:s',strtotime($value['approve_date']))."] - ".strtoupper($value['product'])."</option>";
			}
		}

        $Arr_Kembali	= array(
            'tujuan_out' => strtoupper($tujuan_out),
            'option' => $option,
            'option2' => $option2
        );
        echo json_encode($Arr_Kembali);
	}

	public function modal_outgoing_subgudang(){
		$data = $this->input->post();
		$tipe_out 	= $data['tipe_out'];
		$gudang_before = $data['gudang_before'];
		$field_joint 	= $data['field_joint'];
		$tujuan_out = $data['tujuan_out'];
		$no_spk_field = $data['no_spk_field'];

		$tanda 		= substr($tipe_out,0,3);
		$result	= array();
		$QTY_SPK = '-';
		$QTY_DONE = 0;
		if($tipe_out != 'non-so'){
			if($field_joint == 'no'){
				if($tanda == 'TRS'){
					$sql = "SELECT a.*, a.qty_order AS qty FROM warehouse_adjustment_detail a WHERE a.kode_trans='".$tipe_out."' AND a.proccess_date IS NULL";
				}
				else{
					if($tipe_out != 'non-so'){
						$sql = "SELECT a.* FROM so_acc_and_mat a WHERE a.id_bq='".$tipe_out."' AND qty_out < qty AND category = 'mat'";
					}
				}
			}
			else{
				if($no_spk_field != '0'){
					$EXPLODE_FJ = explode('/',$no_spk_field);
					$sql = "SELECT a.* FROM request_outgoing a WHERE a.id_bq='".$tipe_out."' AND category = 'mat' AND id_milik='".$EXPLODE_FJ[0]."' AND approve_date='".$EXPLODE_FJ[1]."' AND id_material <> '0'";
				
					$GET_QTY_SPK = $this->db->select('SUM(qty) AS qty')->get_where('production_spk',array('id_milik'=>$EXPLODE_FJ[0],'created_date'=>$EXPLODE_FJ[1]))->result_array();
					$QTY_SPK = (!empty($GET_QTY_SPK[0]['qty']))?$GET_QTY_SPK[0]['qty']:'tidak ditemukan';

					$GET_QTY_OUT = $this->db->select('SUM(qty) AS qty')->get_where('outgoing_field_joint',array('id_milik'=>$EXPLODE_FJ[0],'date_uniq'=>$EXPLODE_FJ[1],'deleted_date'=>NULL))->result_array();
					$QTY_DONE = (!empty($GET_QTY_OUT[0]['qty']))?$GET_QTY_OUT[0]['qty']:0;
				}
				else{
					$sql = "SELECT a.* FROM request_outgoing a WHERE a.id_bq='".$tipe_out."' AND qty_out < qty AND category = 'mat'";
				}
			}
			// echo $sql;
			$result	= $this->db->query($sql)->result_array();
		}

		$subgudang		= $this->db->query("SELECT * FROM warehouse WHERE category='subgudang' ORDER BY urut ASC")->result_array();

		$GET_STOCK_MAT = get_warehouseStockMaterial();
		$data = array(
			'tanda' => $tanda,
			'QTY_SPK' => (is_numeric($QTY_SPK)?number_format($QTY_SPK):$QTY_SPK),
			'QTY_DONE' => number_format($QTY_DONE,2),
			'tipe_out' => $tipe_out,
			'field_joint' => $field_joint,
			'gudang' => $gudang_before,
			'tujuan_out' => $tujuan_out,
			'subgudang' => $subgudang,
			'no_spk_field' => $no_spk_field,
			'GET_STOCK_MAT' => $GET_STOCK_MAT,
			'result' => $result
		);
		
		$this->load->view('Outgoing/modal_outgoing_subgudang', $data);
	}

	public function process_out_material_sub(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tanda			= $data['tanda'];
		$tipe_out		= $data['tipe_out'];
		$gudang			= $data['gudang'];
		$tujuan_out		= $data['tujuan_out'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$field_joint 	= $data['field_joint'];
		$GET_NO_SPK 	= get_detail_final_drawing();
		$no_spk = null;
		if(!empty($data['no_spk_field'])){
			$no_spk_field 	= explode('/',$data['no_spk_field']);
			$no_spk 		= (!empty($GET_NO_SPK[$no_spk_field[0]]['no_spk']))?$GET_NO_SPK[$no_spk_field[0]]['no_spk']:null;
		}
		$Ym 			= date('ym'); 

		$table_utama = 'so_acc_and_mat';
		if($field_joint == 'yes'){
			$table_utama = 'request_outgoing';
		}

		//UPLOAD DOCUMENT
		$file_name = '';
		$ArrEndChange = [];
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_req_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEndChange = array(
				'file_eng_change' 	=> $file_name
			);
		}
		// echo $no_po;
		// print_r($addInMat);
		// exit;
        $histHlp = "Material outgoing: ".$tipe_out;

		if($adjustment == 'OUT'){
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrMaterial	 = array();
			$SumMat = 0;
           
			foreach($addInMat AS $val => $valx){
				if(!empty($valx['id'])){
					$qtyIN 		= str_replace(',','',$valx['qty_in']);
					$SumMat 	+= $qtyIN;

					if($tanda == 'TRS'){
						$restWhDetail	= $this->db->get_where('warehouse_adjustment_detail',array('id'=>$valx['id']))->result();

						//update detail so material
						$ArrUpdate[$val]['id'] 				= $valx['id'];
						$ArrUpdate[$val]['proccess_by'] 	= $data_session['ORI_User']['username'];
						$ArrUpdate[$val]['proccess_date'] 	= $dateTime;
					}
					else{
						$sqlWhDetail	= "	SELECT
											a.*,
											b.idmaterial,
											b.nm_material,
											b.id_category,
											b.nm_category
										FROM
											".$table_utama." a
											LEFT JOIN raw_materials b
												ON a.id_material=b.id_material
										WHERE
											a.id = '".$valx['id']."'
										";
						$restWhDetail	= $this->db->query($sqlWhDetail)->result();

						//update detail so material
						$ArrUpdate[$val]['id'] 			= $valx['id'];
						$ArrUpdate[$val]['qty_out'] 	= $restWhDetail[0]->qty_out + $qtyIN;
					}
					
					//detail adjustmeny
					$ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
					$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
					$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
					$ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material_req'];
					$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
					$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
					$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
					$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
					$ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
					$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
					$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN;
					$ArrDeatilAdj[$val]['key_gudang'] 		= $valx['sub_gudang'];
					$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
					$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
					$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
					$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

					//detail material
					$ArrMaterial[$val]['id_material'] 	= $valx['id_material_req'];
					$ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
					$ArrMaterial[$val]['qty'] 	        = $qtyIN;
				}
			}
            

            //GROUPING UPDATE MATERIAL PER GUDANG
			$ArrGrouping = [];
			foreach ($ArrMaterial as $key => $value) {
				$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
				$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
			}

			$no_ipp = null;
			$nm_product = null;
			if(!empty($no_spk)){
				$getDetSPK	= $this->db->get_where('so_detail_header',array('no_spk'=>$no_spk))->result_array();
				$no_ipp 	= (!empty($getDetSPK[0]['id_bq']))?str_replace('BQ-','',$getDetSPK[0]['id_bq']):null;
				$nm_product = (!empty($getDetSPK[0]['id_category']))?$getDetSPK[0]['id_category']:null;
			}

			$gudang_dari = $gudang;
			foreach ($ArrGrouping as $key => $value) {
				move_warehouse($value,$key,NULL,$kode_trans);
				insertDataGroupReport($value, $key, null, $kode_trans, $no_ipp, $no_spk, $nm_product);
			}

			// print_r($ArrGrouping);
			// exit;

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $tipe_out,
				'note' 			    => $tujuan_out,
				'category' 			=> 'outgoing subgudang',
				'jumlah_mat' 		=> $SumMat,
				'id_gudang_dari' 	=> $gudang_dari,
				'file_eng_change' 	=> $file_name,
				'kd_gudang_dari' 	=> 'SUBGUDANG',
				'kd_gudang_ke' 		=> 'OUT',
				'no_spk' 			=> $no_spk,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime,
				'checked_by' 		=> $data_session['ORI_User']['username'],
				'checked_date' 		=> $dateTime
			);


			// print_r($ArrUpdate);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit;
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				if($tanda == 'TRS'){
					$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdate, 'id');
				}
				else{
					$this->db->update_batch($table_utama, $ArrUpdate, 'id');
				}
			$this->db->trans_complete();
		}


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
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}

	public function process_fg_material_sub(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$tanda			= $data['tanda'];
		$tipe_out		= $data['tipe_out'];
		$gudang			= $data['gudang'];
		$tujuan_out		= $data['tujuan_out'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$field_joint 	= $data['field_joint'];
		$GET_NO_SPK = get_detail_final_drawing();
		$no_spk = null;
		if(!empty($data['no_spk_field'])){
			$no_spk_field 	= explode('/',$data['no_spk_field']);
			$no_spk 		= (!empty($GET_NO_SPK[$no_spk_field[0]]['no_spk']))?$GET_NO_SPK[$no_spk_field[0]]['no_spk']:null;
		}
		$Ym 			= date('ym'); 

		$table_utama = 'so_acc_and_mat';
		if($field_joint == 'yes'){
			$table_utama = 'request_outgoing';
		}
		// echo $no_po;
		// print_r($addInMat);
		// exit;
        $histHlp = "Material outgoing (subgudang): ".$tipe_out;

		if($adjustment == 'OUT'){
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrMaterial	 = array();
			$SumMat = 0;
			
			if($tipe_out != 'non-so'){
				foreach($addInMat AS $val => $valx){
					if(!empty($valx['id'])){
						$qtyIN 		= str_replace(',','',$valx['qty_in']);
						$SumMat 	+= $qtyIN;

						if($tanda == 'TRS'){
							$restWhDetail	= $this->db->get_where('warehouse_adjustment_detail',array('id'=>$valx['id']))->result();

							//update detail so material
							$ArrUpdate[$val]['id'] 				= $valx['id'];
							$ArrUpdate[$val]['proccess_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdate[$val]['proccess_date'] 	= $dateTime;
						}
						else{
							$sqlWhDetail	= "	SELECT
													a.*,
													b.idmaterial,
													b.nm_material,
													b.id_category,
													b.nm_category
												FROM
													".$table_utama." a
													LEFT JOIN raw_materials b
														ON a.id_material=b.id_material
												WHERE
													a.id = '".$valx['id']."'
												";
							$restWhDetail	= $this->db->query($sqlWhDetail)->result();

							//update detail so material
							$ArrUpdate[$val]['id'] 			= $valx['id'];
							$ArrUpdate[$val]['qty_out'] 	= $restWhDetail[0]->qty_out + $qtyIN;
							$ArrUpdate[$val]['kode_trans'] 	= $kode_trans;
						}
						
						//detail adjustmeny
						$ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
						$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
						$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
						$ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material_req'];
						$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
						$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
						$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
						$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
						$ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
						$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
						$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN;
						$ArrDeatilAdj[$val]['key_gudang'] 		= $valx['sub_gudang'];
						$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
						$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
						$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
						$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

						//detail material
						$ArrMaterial[$val]['id_material'] 	= $valx['id_material_req'];
						$ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
						$ArrMaterial[$val]['qty'] 	        = $qtyIN;
					}
				}
			}

			if($tipe_out == 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $restWhDetail	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= NULL;
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= $qtyIN;
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
					$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN;
                    $ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id_material'] 	= $valx['id_material'];
					$ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;

                }
            }
            

            //GROUPING UPDATE MATERIAL PER GUDANG
			$ArrGrouping = [];
			foreach ($ArrMaterial as $key => $value) {
				$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
				$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
			}

			$gudang_dari = $gudang;
			$gudang_ke = 15;
			foreach ($ArrGrouping as $key => $value) {
				move_warehouse($value,$key,$gudang_ke,$kode_trans);
			}

			// print_r($ArrGrouping);
			// exit;

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $tipe_out,
				'note' 			    => $tujuan_out,
				'category' 			=> 'outgoing subgudang',
				'jumlah_mat' 		=> $SumMat,
				'id_gudang_dari' 	=> $gudang_dari,
				'kd_gudang_dari' 	=> 'SUBGUDANG',
				'kd_gudang_ke' 		=> 'OUT',
				'no_spk' 			=> $no_spk,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime,
				'checked_by' 		=> $data_session['ORI_User']['username'],
				'checked_date' 		=> $dateTime
			);


			// print_r($ArrUpdate);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit;
			$this->db->trans_start();
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				if($tipe_out != 'non-so'){
					if($tanda == 'TRS'){
						$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdate, 'id');
					}
					else{
						$this->db->update_batch($table_utama, $ArrUpdate, 'id');
					}
				}
			$this->db->trans_complete();
		}


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
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}

	public function modalEditReport($id=null){
		if($this->input->post()){
			$data 		= $this->input->post();
			$id			= $data['id'];
			$form_no	= $data['form_no'];
			$rev_no	= $data['rev_no'];
			$issue_date	= $data['issue_date'];
			$no_surat_jalan	= $data['no_surat_jalan'];
			$no_memo	= $data['no_memo'];
			$no_so	= $data['no_so'];

			$ArrUpdate = [
				'form_no' => $form_no,
				'rev_no' => $rev_no,
				'issue_date' => $issue_date,
				'no_surat_jalan' => $no_surat_jalan,
				'no_memo' => $no_memo,
				'no_so' => $no_so
			];
			// exit;
			$this->db->trans_start();
				$this->db->where('kode_trans',$id);
				$this->db->update('warehouse_adjustment',$ArrUpdate);
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=>'Failed process data. Please try again later ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=>'Success process data. Thanks ...',
					'status'	=> 1
				);
				history('Edit print outgoing '.$id);
			}
			echo json_encode($Arr_Kembali);
		}
		else{
			$get_detail = $this->db->get_where('warehouse_adjustment',array('kode_trans'=>$id))->result();
			// print_r($get_detail);
			$data = [
				'get_detail' => $get_detail,
				'id' => $id
			];
			$this->load->view('Outgoing/modalEditReport', $data);
		}
	}

	public function get_raw_materials(){
		$data       = $this->input->post();
		$id_category   = $data['id_category'];

		$material	 = $this->master_model->getDataOrderBy('raw_materials','id_category',$id_category,'nm_material');
		
        $d_Header = "<option value='0'>Select Material</option>";
        foreach($material AS $row){
			$d_Header .= "<option value='".$row->id_material."'>".strtoupper($row->nm_material)."</option>";
		}

        $Arr_Kembali	= array(
            'option' => $d_Header
        );
        echo json_encode($Arr_Kembali);
	}

	public function process_save_sementara(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$addInMat		= $data['addInMat'];
		$tipe_out		= $data['tipe_out'];
		$no_spk_field	= $data['no_spk_field'];

		$EXPLODE_FJ 	= explode('/',$no_spk_field);
		
		$ArrUpdated	 = array();
		$ArrInsert	 = array();
		foreach($addInMat AS $val => $valx){
			if(!empty($valx['id'])){
				$ArrUpdated[$val]['id'] 			= $valx['id'];
				$ArrUpdated[$val]['qty'] 			= str_replace(',','',$valx['qty_in']);
				$ArrUpdated[$val]['sheet'] 			= $valx['sub_gudang'];
				$ArrUpdated[$val]['note'] 			= strtolower($valx['keterangan']);
				$ArrUpdated[$val]['updated_by'] 	= $data_session['ORI_User']['username'];
				$ArrUpdated[$val]['updated_date'] 	= $dateTime;
			}
			else{
				$ArrInsert[$val]['id_bq'] 			= $tipe_out;
				$ArrInsert[$val]['id_milik'] 		= $EXPLODE_FJ[0];
				$ArrInsert[$val]['category'] 		= 'mat';
				$ArrInsert[$val]['satuan'] 			= '1';
				$ArrInsert[$val]['id_material'] 	= $valx['id_material'];
				$ArrInsert[$val]['qty'] 			= str_replace(',','',$valx['qty_in']);
				$ArrInsert[$val]['sheet'] 			= $valx['sub_gudang'];
				$ArrInsert[$val]['note'] 			= strtolower($valx['keterangan']);
				$ArrInsert[$val]['updated_by'] 		= $data_session['ORI_User']['username'];
				$ArrInsert[$val]['updated_date'] 	= $dateTime;
				$ArrInsert[$val]['approve_by'] 		= $data_session['ORI_User']['username'];
				$ArrInsert[$val]['approve_date'] 	= $EXPLODE_FJ[1];
			}
		}

		// print_r($ArrUpdated);
		// print_r($ArrInsert);
		// exit;
		$this->db->trans_start();
			if(!empty($ArrUpdated)){
				$this->db->update_batch('request_outgoing', $ArrUpdated, 'id');
			}
			if(!empty($ArrInsert)){
				$this->db->insert_batch('request_outgoing', $ArrInsert);
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
			history('Save sementara field joint outgoing '.$tipe_out);
		}
		echo json_encode($Arr_Data);
	}

	public function get_stock(){
		$data       	= $this->input->post();
		$id_material   	= $data['id_material'];
		$gudang   		= $data['gudang'];

		$WHERE = [
			'id_material' => $id_material,
			'id_gudang' => $gudang
		];
		
        $getStock = $this->db->get_where('warehouse_stock',$WHERE)->result();
		$stock = (!empty($getStock[0]->qty_stock))?floatval($getStock[0]->qty_stock):0;

        $Arr_Kembali	= array(
            'stock' => $stock
        );
        echo json_encode($Arr_Kembali);
	}

	public function process_fg_material_sub_new(){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime		= date('Y-m-d H:i:s');
		$DateTime		= date('Y-m-d H:i:s');
		$tanda			= $data['tanda'];
		$tipe_out		= $data['tipe_out'];
		$tipe_out		= $data['tipe_out'];
		$gudang			= $data['gudang'];		
		$tujuan_out		= $data['tujuan_out'];
		$nm_gudang_ke 	= get_name('warehouse', 'kd_gudang', 'id', $gudang);
		$addInMat		= $data['addInMat'];
		$adjustment 	= $data['adjustment'];
		$field_joint 	= $data['field_joint'];
		$GET_NO_SPK = get_detail_final_drawing();
		$no_spk = null;
		if(!empty($data['no_spk_field'])){
			$no_spk_field 	= explode('/',$data['no_spk_field']);
			$no_spk 		= (!empty($GET_NO_SPK[$no_spk_field[0]]['no_spk']))?$GET_NO_SPK[$no_spk_field[0]]['no_spk']:null;
		}
		$Ym 			= date('ym'); 

		$table_utama = 'so_acc_and_mat';
		if($field_joint == 'yes'){
			$table_utama = 'request_outgoing';
		}
		// echo $no_po;
		// print_r($addInMat);
		// exit;
        $histHlp = "Material outgoing (subgudang): ".$tipe_out;

		//UPLOAD DOCUMENT
		$file_name = '';
		$ArrEndChange = [];
		if(!empty($_FILES["upload_spk"]["name"])){
			$target_dir     = "assets/file/produksi/";
			$target_dir_u   = $_SERVER['DOCUMENT_ROOT']."/assets/file/produksi/";
			$name_file      = 'qc_eng_change_req_'.date('Ymdhis');
			$target_file    = $target_dir . basename($_FILES["upload_spk"]["name"]);
			$name_file_ori  = basename($_FILES["upload_spk"]["name"]);
			$imageFileType  = strtolower(pathinfo($target_file,PATHINFO_EXTENSION)); 
			$nama_upload    = $target_dir_u.$name_file.".".$imageFileType;
			$file_name    	= $name_file.".".$imageFileType;

			if(!empty($_FILES["upload_spk"]["tmp_name"])){
				move_uploaded_file($_FILES["upload_spk"]["tmp_name"], $nama_upload);
			}

			$ArrEndChange = array(
				'file_eng_change' 	=> $file_name
			);
		}

		if($adjustment == 'OUT'){
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRS".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRS".$Ym.$urut2;
		
			$ArrUpdate		 = array();
			$ArrDeatilAdj	 = array();
			$ArrMaterial	 = array();
			$ArrMaterialQc	 = array();
			$SumMat = 0;

			$SumPriceBook = 0;
			
			if($tipe_out != 'non-so'){
				foreach($addInMat AS $val => $valx){
					if(!empty($valx['id'])){
						$qtyIN 		= (!empty($valx['qty_in']))?str_replace(',','',$valx['qty_in']):0;
						$SumMat 	+= $qtyIN;

						if($tanda == 'TRS'){
							$restWhDetail	= $this->db->get_where('warehouse_adjustment_detail',array('id'=>$valx['id']))->result();

							//update detail so material
							$ArrUpdate[$val]['id'] 				= $valx['id'];
							$ArrUpdate[$val]['proccess_by'] 	= $data_session['ORI_User']['username'];
							$ArrUpdate[$val]['proccess_date'] 	= $dateTime;
						}
						else{
							$sqlWhDetail	= "	SELECT
													a.*,
													b.idmaterial,
													b.nm_material,
													b.id_category,
													b.nm_category
												FROM
													".$table_utama." a
													LEFT JOIN raw_materials b
														ON a.id_material=b.id_material
												WHERE
													a.id = '".$valx['id']."'
												";
							$restWhDetail	= $this->db->query($sqlWhDetail)->result();

							//update detail so material
							$ArrUpdate[$val]['id'] 			= $valx['id'];
							$ArrUpdate[$val]['qty_out'] 	= $restWhDetail[0]->qty_out + $qtyIN;
							$ArrUpdate[$val]['kode_trans'] 	= $kode_trans;
						}
						
						//detail adjustmeny
						$ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
						$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
						$ArrDeatilAdj[$val]['id_po_detail'] 	= $valx['id'];
						$ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material_req'];
						$ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
						$ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
						$ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
						$ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
						$ArrDeatilAdj[$val]['qty_order'] 		= str_replace(',','',$valx['qty_order']);
						$ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
						$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN;
						$ArrDeatilAdj[$val]['key_gudang'] 		= $valx['sub_gudang'];
						$ArrDeatilAdj[$val]['expired_date'] 	= NULL;
						$ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
						$ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
						$ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

						//detail material
						$ArrMaterial[$val]['id_material'] 	= $valx['id_material_req'];
						$ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
						$ArrMaterial[$val]['qty'] 	        = $qtyIN;

						if($field_joint == 'yes'){
							if($qtyIN > 0){
								$price_book = get_price_book($restWhDetail[0]->id_material);
								$total_value = $price_book * $qtyIN;
								$ArrMaterialQc[$val]['kode_trans'] 	    = $kode_trans;
								$ArrMaterialQc[$val]['id_material'] 	= $restWhDetail[0]->id_material;
								$ArrMaterialQc[$val]['nm_material'] 	= $restWhDetail[0]->nm_material;
								$ArrMaterialQc[$val]['id_gudang'] 	    = $valx['sub_gudang'];
								$ArrMaterialQc[$val]['qty'] 	        = $qtyIN;
								$ArrMaterialQc[$val]['price_book'] 	    = $price_book;
								$ArrMaterialQc[$val]['nilai_value'] 	= $total_value;

								$SumPriceBook += $total_value;
							}
						}
					}
				}
			}

			if($tipe_out == 'non-so'){
                foreach($addInMat AS $val => $valx){
                    $qtyIN 		= str_replace(',','',$valx['qty_in']);
                    $SumMat 	+= $qtyIN;

                    $restWhDetail	= $this->db->get_where('raw_materials',array('id_material'=>$valx['id_material']))->result();
                    
                    //detail adjustmeny
                    $ArrDeatilAdj[$val]['no_ipp'] 			= $tipe_out;
                    $ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
                    $ArrDeatilAdj[$val]['id_po_detail'] 	= NULL;
                    $ArrDeatilAdj[$val]['id_material_req'] 	= $valx['id_material'];
                    $ArrDeatilAdj[$val]['id_material'] 		= $restWhDetail[0]->id_material;
                    $ArrDeatilAdj[$val]['nm_material'] 		= $restWhDetail[0]->nm_material;
                    $ArrDeatilAdj[$val]['id_category'] 		= $restWhDetail[0]->id_category;
                    $ArrDeatilAdj[$val]['nm_category'] 		= $restWhDetail[0]->nm_category;
                    $ArrDeatilAdj[$val]['qty_order'] 		= $qtyIN;
                    $ArrDeatilAdj[$val]['qty_oke'] 			= $qtyIN;
					$ArrDeatilAdj[$val]['check_qty_oke'] 	= $qtyIN;
                    $ArrDeatilAdj[$val]['key_gudang'] 		= NULL;
                    $ArrDeatilAdj[$val]['expired_date'] 	= NULL;
                    $ArrDeatilAdj[$val]['keterangan'] 		= strtolower($valx['keterangan']);
                    $ArrDeatilAdj[$val]['update_by'] 		= $data_session['ORI_User']['username'];
                    $ArrDeatilAdj[$val]['update_date'] 		= $dateTime;

                    //detail material
                    $ArrMaterial[$val]['id_material'] 	= $valx['id_material'];
					$ArrMaterial[$val]['gudang'] 	    = $valx['sub_gudang'];
                    $ArrMaterial[$val]['qty'] 	        = $qtyIN;

                }
            }
            
            //GROUPING UPDATE MATERIAL PER GUDANG
			$ArrGrouping = [];
			foreach ($ArrMaterial as $key => $value) {
				$ArrGrouping[$value['gudang']][$key]['id'] = $value['id_material'];
				$ArrGrouping[$value['gudang']][$key]['qty'] = $value['qty'];
			}

			$gudang_dari = $gudang;
			$gudang_ke = getGudangFG();

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'no_ipp' 			=> $tipe_out,
				'note' 			    => $tujuan_out,
				'category' 			=> 'outgoing subgudang',
				'jumlah_mat' 		=> $SumMat,
				'id_gudang_dari' 	=> $gudang_dari,
				'file_eng_change' 	=> $file_name,
				'kd_gudang_dari' 	=> 'SUBGUDANG',
				'kd_gudang_ke' 		=> 'OUT',
				'no_spk' 			=> $no_spk,
				'created_by' 		=> $data_session['ORI_User']['username'],
				'created_date' 		=> $dateTime,
				'checked_by' 		=> $data_session['ORI_User']['username'],
				'checked_date' 		=> $dateTime
			);


			//=======NEW=============
			$ArrQcHeader = [];
			if($field_joint == 'yes'){
				$ArrQcHeader = array(
					'no_ipp' 			=> str_replace('BQ-','',$tipe_out),
					'id_milik' 			=> $no_spk_field[0],
					'date_uniq' 		=> $no_spk_field[1],
					'no_spk' 			=> $no_spk,
					'kode_trans' 		=> $kode_trans,
					'id_gudang' 		=> $gudang_dari,
					'qty' 				=> str_replace(',','',$data['qty_kit']),
					'nilai_value' 		=> $SumPriceBook,
					'created_by' 		=> $data_session['ORI_User']['username'],
					'created_date' 		=> $dateTime
				);
			}

			//=======END NEW=============
			// exit;
			$this->db->trans_start();
				$UserName=$data_session['ORI_User']['username'];
				$this->db->insert('warehouse_adjustment', $ArrInsertH);
				$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				if($tipe_out != 'non-so'){
					if($tanda == 'TRS'){
						$this->db->update_batch('warehouse_adjustment_detail', $ArrUpdate, 'id');
					}
					else{
						$this->db->update_batch($table_utama, $ArrUpdate, 'id');
					}
				}

				if($field_joint == 'yes'){
					if(!empty($ArrQcHeader)){
						$this->db->insert('outgoing_field_joint', $ArrQcHeader);
					}
					if(!empty($ArrMaterialQc)){
						$this->db->insert_batch('outgoing_field_joint_detail', $ArrMaterialQc);
					}
				}

				$tempx = [];
				$no_ipp = null;
				$nm_product = null;
				if(!empty($no_spk)){
					$getDetSPK	= $this->db->get_where('so_detail_header',array('no_spk'=>$no_spk))->result_array();
					$no_ipp 	= (!empty($getDetSPK[0]['id_bq']))?str_replace('BQ-','',$getDetSPK[0]['id_bq']):null;
					$nm_product = (!empty($getDetSPK[0]['id_category']))?$getDetSPK[0]['id_category']:null;
				}

				if(!empty($ArrGrouping)){
					foreach ($ArrGrouping as $key => $valueParent) {
						$grouping_temp = [];
						$grouping_tempGudang = [];
						$temp = [];
						foreach($valueParent as $value) {
							if(!array_key_exists($value['id'], $temp)) {
								$temp[$value['id']]['good'] = 0;
							}
							$QtyIn = (!empty($value['qty']))?$value['qty']:0;
							$temp[$value['id']]['good'] += $QtyIn;
			
							$grouping_temp[$value['id']]['id'] 			= $value['id'];
							$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
							
							
							
							$id_material = $value['id'];
							$id_gudang   = $gudang; 

							$coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang))->row();
							$coa_gudang = $coa_1->coa_1;
							$kategori_gudang = $coa_1->category;				 
								
							$stokjurnalakhir=0;
							$nilaijurnalakhir=0;
							$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
							if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
							
							if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
							
							$tanggal		= date('Y-m-d');
							$Bln 			= substr($tanggal,5,2);
							$Thn 			= substr($tanggal,0,4);
							$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
							
							
							
							$GudangFrom = $kategori_gudang;
							if($GudangFrom == 'pusat'){
								//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
								//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
								$bmunit = 0;
								$bm = 0;

								$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>2,'id_material'=>$id_material),1)->row();
								if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;


							}elseif($GudangFrom == 'subgudang'){
								//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_subgudang',array('id_material'=>$id_material))->result();
								//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
								$bmunit = 0;
								$bm = 0;
								$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>3, 'id_material'=>$id_material),1)->row();
								if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
					
							}elseif($GudangFrom == 'produksi'){
								//$get_price_book = $this->db->order_by('id','desc')->get_where('price_book_project',array('id_material'=>$id_material))->result();
								//$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
								$bmunit = 0;
								$bm = 0;
								$harga_jurnal_akhir2 = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('coa_gudang'=>'1103-01-03', 'id_material'=>$id_material),1)->row();
								if(!empty($harga_jurnal_akhir2)) $PRICE=$harga_jurnal_akhir2->harga;
						
								
							}
							
							$QTY_OKE  = $temp[$value['id']]['good'];
							$ACTUAL_MAT = $id_material;
							
							$restWhDetail2	= $this->db->get_where('raw_materials',array('id_material'=>$id_material))->result();
							$DateTime		= date('Y-m-d H:i:s');
							
								
							
							$ArrJurnalNew[$value['id']]['id_material'] 		= $ACTUAL_MAT;
							$ArrJurnalNew[$value['id']]['idmaterial'] 		= $restWhDetail2[0]->id_material;
							$ArrJurnalNew[$value['id']]['nm_material'] 		= $restWhDetail2[0]->nm_material;
							$ArrJurnalNew[$value['id']]['id_category'] 		= $restWhDetail2[0]->id_category;
							$ArrJurnalNew[$value['id']]['nm_category'] 		= $restWhDetail2[0]->nm_category;
							$ArrJurnalNew[$value['id']]['id_gudang'] 			= $id_gudang;
							$ArrJurnalNew[$value['id']]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
							$ArrJurnalNew[$value['id']]['id_gudang_dari'] 	    = $id_gudang;
							$ArrJurnalNew[$value['id']]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
							$ArrJurnalNew[$value['id']]['id_gudang_ke'] 		= $gudang_ke;
							$ArrJurnalNew[$value['id']]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_ke);
							$ArrJurnalNew[$value['id']]['qty_stock_awal'] 		= $stokjurnalakhir;
							$ArrJurnalNew[$value['id']]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
							$ArrJurnalNew[$value['id']]['kode_trans'] 			= $kode_trans;
							$ArrJurnalNew[$value['id']]['tgl_trans'] 			= $DateTime;
							$ArrJurnalNew[$value['id']]['qty_out'] 			= $QTY_OKE;
							$ArrJurnalNew[$value['id']]['ket'] 				= 'outgoing subgudang - finisgood';
							$ArrJurnalNew[$value['id']]['harga'] 			= $PRICE;
							$ArrJurnalNew[$value['id']]['harga_bm'] 		= 0;
							$ArrJurnalNew[$value['id']]['nilai_awal_rp']	= $nilaijurnalakhir;
							$ArrJurnalNew[$value['id']]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
							$ArrJurnalNew[$value['id']]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
							$ArrJurnalNew[$value['id']]['update_by'] 		= $UserName;
							$ArrJurnalNew[$value['id']]['update_date'] 		= $DateTime;
							$ArrJurnalNew[$value['id']]['no_jurnal'] 		= $Nojurnal;
							$ArrJurnalNew[$value['id']]['coa_gudang'] 		= $coa_gudang;

							$grouping_tempGudang[$value['id']]['id'] 	= $value['id'];
							$grouping_tempGudang[$value['id']]['qty'] 	= $temp[$value['id']]['good'];
						}
						
						move_warehouse($grouping_tempGudang,$key,$gudang_ke,$kode_trans);
						insertDataGroupReport($grouping_tempGudang, $key, $gudang_ke, $kode_trans, $no_ipp, $no_spk, $nm_product);
						if(!empty($grouping_temp)){
							insert_jurnal($grouping_temp,$key,15,$kode_trans,'material to FG','pengurangan subgudang','penambahan gudang finish good');
							$this->db->insert_batch('tran_warehouse_jurnal_detail', $ArrJurnalNew);
						}
					}
				}
			$this->db->trans_complete();
		}


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
			history($histHlp);
		}
		echo json_encode($Arr_Data);
	}
}


