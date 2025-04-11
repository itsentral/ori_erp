<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_aksesoris_gudang extends CI_Controller {
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

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Request List Accesories',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		history('View request list aksesoris gudang');
		$this->load->view('Request_aksesoris/request_gudang',$data);
	}

    public function server_side_request(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request(
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
			$nestedData[]	= "<div align='center'>".$row['kode']."</div>";
			$nestedData[]	= "<div align='center'>".$row['no_ipp']."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='center'>".strtoupper($row['created_by'])."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s',strtotime($row['created_date']))."</div>";
            
            $badge = "<span class='badge bg-blue'>Parsial</span>";
            if($row['qty_out'] == 0){
                $badge = "<span class='badge bg-yellow'>Waiting Confirmation</span>";
            }
            if($row['qty_out'] >= $row['qty_req']){
                $badge = "<span class='badge bg-green'>Confirmed</span>";
            }

			$nestedData[]	= "<div align='center'>".$badge."</div>";

            $create	= "";
            if($Arr_Akses['create']=='1'){
                $create	= "<button class='btn btn-sm btn-primary request' title='Request' data-kode='".$row['kode']."'><i class='fa fa-edit'></i></button>";
            }
            $print	= "&nbsp;<a href='".base_url('request_aksesoris_gudang/print_outgoing/'.$row['kode'])."' target='_blank' class='btn btn-sm btn-default' title='Print'><i class='fa fa-print'></i></a>";

            $nestedData[]	= "<div align='center'>".$create.$print."</div>";

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

	public function query_data_json_request($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.kode,
				a.no_ipp,
                a.created_by,
                a.created_date,
				b.so_number,
                c.nm_customer,
                c.project,
                COUNT(a.id) AS qtyCount,
                SUM(a.qty_out) AS qty_out,
                SUM(a.qty_request) AS qty_req
			FROM
				request_accessories a
                LEFT JOIN so_number b ON CONCAT('BQ-',a.no_ipp) = b.id_bq
                LEFT JOIN production c ON a.no_ipp = c.no_ipp,
				(SELECT @row:=0) r
		    WHERE a.deleted_date IS NULL
                AND (
                    a.kode LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.project LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY a.kode
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode',
			2 => 'no_ipp',
			3 => 'b.so_number',
			4 => 'c.nm_customer',
			5 => 'c.project',
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}

    public function add(){
		if($this->input->post()){
			$tanda 			= $this->uri->segment(3);
		 	$data 			= $this->input->post();
			$data_session	= $this->session->userdata;
            $username       = $data_session['ORI_User']['username'];
            $datetime       = date('Y-m-d H:i:s');
            $kode	        = $data['kode'];

			$gudang_before = 10;
			$gudang_after = 15;
			
			if(!empty($data['add'])){
				$dataDetail	= $data['add'];
			}

			$Ym 			= date('ym');
			//pengurutan kode
			$srcMtr			= "SELECT MAX(kode_trans) as maxP FROM warehouse_adjustment WHERE kode_trans LIKE 'TRN".$Ym."%' ";
			$numrowMtr		= $this->db->query($srcMtr)->num_rows();
			$resultMtr		= $this->db->query($srcMtr)->result_array();
			$angkaUrut2		= $resultMtr[0]['maxP'];
			$urutan2		= (int)substr($angkaUrut2, 7, 4);
			$urutan2++;
			$urut2			= sprintf('%04s',$urutan2);
			$kode_trans		= "TRN".$Ym.$urut2;

			$ArrDeatil 			= array();
			$ArrDeatilUpdate 	= array();
			$ArrStock	 		= array();
			$ArrUpdateStock	 	= array();
			$ArrHist	 		= array();
			$ArrDeatilAdj	 	= array();
			$SUM_MAT = 0;
			if(!empty($data['add'])){
				foreach($dataDetail AS $val => $valx){
					$QTY = str_replace(',','',$valx['request']);
					if($QTY > 0){
						$GET_REQ    = $this->db->get_where('request_accessories',array('id' => $valx['id']))->result();
						$QTY_OUT    = $GET_REQ[0]->qty_out + $QTY;

						$SUM_MAT += $QTY;

						$ArrDeatil[$val]['id']              = $valx['id'];
						$ArrDeatil[$val]['qty_out']         = $QTY_OUT;
						$ArrDeatil[$val]['updated_by']    	= $username;
						$ArrDeatil[$val]['updated_date']    = $datetime;

						$GetStock	= $this->db->get_where('warehouse_rutin_stock',array('code_group'=>$valx['code_group'], 'gudang'=>$gudang_before))->result();

						$STOCK_QTY = (!empty($GetStock[0]->stock) AND $GetStock[0]->stock > 0)?$GetStock[0]->stock:0;

						$ArrStock[$val]['id'] 			= $GetStock[0]->id;
						$ArrStock[$val]['stock'] 		= $STOCK_QTY - $QTY;
						$ArrStock[$val]['update_by'] 	= $username;
						$ArrStock[$val]['update_date'] 	= $datetime;

						$ArrUpdateStock[$val]['id'] 		= $valx['code_group'];
						$ArrUpdateStock[$val]['qty_good'] 	= $QTY;

						//insert history
						$ArrHist[$val]['code_group'] 		= $GetStock[0]->code_group;
						$ArrHist[$val]['category_awal'] 	= $GetStock[0]->category_awal;
						$ArrHist[$val]['category_code'] 	= $GetStock[0]->category_code;
						$ArrHist[$val]['material_name'] 	= $GetStock[0]->material_name;
						$ArrHist[$val]['id_gudang'] 		= $gudang_before;
						$ArrHist[$val]['gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
						$ArrHist[$val]['id_gudang_dari'] 	= $gudang_before;
						$ArrHist[$val]['gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_before);
						$ArrHist[$val]['id_gudang_ke'] 		= $gudang_after;
						$ArrHist[$val]['gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $gudang_after);
						$ArrHist[$val]['qty_stock_awal'] 	= $STOCK_QTY;
						$ArrHist[$val]['qty_stock_akhir'] 	= $STOCK_QTY - $QTY;
						$ArrHist[$val]['qty_rusak_awal'] 	= $GetStock[0]->rusak;
						$ArrHist[$val]['qty_rusak_akhir'] 	= $GetStock[0]->rusak;
						$ArrHist[$val]['no_trans'] 			= $kode_trans;
						$ArrHist[$val]['jumlah_qty'] 		= $QTY;
						$ArrHist[$val]['ket'] 				= 'outgoing accessories';
						$ArrHist[$val]['update_by'] 		= $username;
						$ArrHist[$val]['update_date'] 		= $datetime;

						//detail adjustmeny
						$ArrDeatilAdj[$val]['kode_trans'] 		= $kode_trans;
						$ArrDeatilAdj[$val]['id_material'] 		= $GetStock[0]->code_group;
						$ArrDeatilAdj[$val]['nm_material'] 		= $GetStock[0]->material_name;
						$ArrDeatilAdj[$val]['id_category'] 		= $GetStock[0]->category_awal;
						$ArrDeatilAdj[$val]['nm_category'] 		= $GetStock[0]->category_code;
						$ArrDeatilAdj[$val]['qty_order'] 		= $QTY;
						$ArrDeatilAdj[$val]['qty_oke'] 			= $QTY;
						$ArrDeatilAdj[$val]['keterangan'] 		= 'outgoing accessories';
						$ArrDeatilAdj[$val]['check_qty_oke'] 	= $QTY;
						$ArrDeatilAdj[$val]['check_keterangan']	= 'outgoing accessories';
						$ArrDeatilAdj[$val]['update_by'] 		= $username;
						$ArrDeatilAdj[$val]['update_date'] 		= $datetime;

					}
				}
			}

			$ArrInsertH = array(
				'kode_trans' 		=> $kode_trans,
				'category' 			=> 'outgoing accessories',
				'jumlah_mat' 		=> $SUM_MAT,
				'tanggal' 			=> date('Y-m-d'),
				'no_so' 			=> $kode,
				'id_gudang_dari' 	=> $gudang_before,
				'kd_gudang_dari' 	=> get_name('warehouse', 'kd_gudang', 'id', $gudang_before),
				'id_gudang_ke' 		=> $gudang_after,
				'kd_gudang_ke' 		=> get_name('warehouse', 'kd_gudang', 'id', $gudang_after),
				'checked'			=> 'Y',
				'created_by' 		=> $username,
				'created_date' 		=> $datetime,
				'checked_by' 		=> $username,
				'checked_date' 		=> $datetime
			);

			//grouping sum
			$temp = [];
			$grouping_temp = [];
			$key = 0;
			foreach($ArrUpdateStock as $value) { $key++;
				if(!array_key_exists($value['id'], $temp)) {
					$temp[$value['id']]['good'] = 0;
				}
				$temp[$value['id']]['good'] += $value['qty_good'];

				$grouping_temp[$value['id']]['id'] 			= $value['id'];
				$grouping_temp[$value['id']]['qty_good'] 	= $temp[$value['id']]['good'];
			}
			
			// print_r($ArrInsertH);
			// print_r($ArrStock);
			// print_r($ArrInsertH);
			// print_r($ArrDeatilAdj);
			// exit();
			$this->db->trans_start();
				if(!empty($ArrDeatil)){
					$this->db->update_batch('request_accessories', $ArrDeatil, 'id');
				}

				if(!empty($ArrDeatilAdj)){
					$this->db->insert('warehouse_adjustment', $ArrInsertH);
					$this->db->insert_batch('warehouse_adjustment_detail', $ArrDeatilAdj);
				}
				if(!empty($ArrStock)){
					$this->db->update_batch('warehouse_rutin_stock', $ArrStock, 'id');
					$this->db->insert_batch('warehouse_rutin_history', $ArrHist);
				}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Save process failed. Please try again ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Save process success. Thanks ...',
					'status'	=> 1
				);
				history('Outgoing aksesoris '.$kode);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$kode              = $this->uri->segment(3);
			$result_aksesoris   = $this->db
                                        ->select('b.id_material, b.qty, a.qty_request, a.qty_out, a.id, b.satuan, b.berat, b.category, a.no_ipp')
                                        ->join('so_acc_and_mat b','a.id_milik=b.id')
                                        ->get_where('request_accessories a',array('a.kode'=>$kode))
                                        ->result_array();

			$data = array(
				'kode' 		        => $kode,
				'GET_STOK' 		    => get_warehouseStock(),
				'GET_ACCESSORIES' 	=> get_detail_accessories(),
				'result_aksesoris' 	=> $result_aksesoris,
			);
			$this->load->view('Request_aksesoris/add_gudang', $data);
		}
	}

    public function print_outgoing(){
		$kode           = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$printby		= $data_session['ORI_User']['username'];
	
		$data_url		= base_url();
		$Split_Beda		= explode('/',$data_url);
		$Jum_Beda		= count($Split_Beda);
		$Nama_Beda		= $Split_Beda[$Jum_Beda - 2];

        $result_aksesoris   = $this->db
                                        ->select('b.id_material, b.qty, a.qty_request, a.qty_out, a.id, b.satuan, b.berat, b.category, a.no_ipp, a.created_date')
                                        ->join('so_acc_and_mat b','a.id_milik=b.id')
                                        ->get_where('request_accessories a',array('a.kode'=>$kode))
                                        ->result_array();
		
		$data = array(
			'Nama_Beda' => $Nama_Beda,
			'printby' => $printby,
			'result_aksesoris' => $result_aksesoris,
			'kode' => $kode
		);
		history('Print outgoing aksesories '.$kode);
		$this->load->view('Print/print_outgoing_accessories', $data);
	}


}