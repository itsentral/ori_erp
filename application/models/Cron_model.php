<?php
class Cron_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->model('tanki_model');

		$this->db2 = $this->load->database('tanki', TRUE);
	}

	//==========================================================================================================================
	//======================================================SERVER SIDE=========================================================
	//==========================================================================================================================
	
	public function get_json_report_product(){
		// $controller			= ucfirst(strtolower($this->uri->segment(1)));
		// $Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json_report_product(
			$requestData['tanggal'],
            $requestData['bulan'],
            $requestData['tahun'],
			$requestData['range'],
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
		$SERACH_DETAIL_IPP 			= get_detail_ipp();
		$SERACH_DETAIL_SPEC 		= get_detail_spec_fd();
		$SEARCH_DETAIL_BERAT 		= get_input_produksi_detail();
		$SEARCH_DETAIL_BERAT_PLUS 	= get_input_produksi_plus();
		$SEARCH_DETAIL_BERAT_ADD 	= get_input_produksi_add();
		$SEARCH_DETAIL_BERAT_PLUS_EX 	= get_input_produksi_plus_exclude();
		foreach($query->result_array() as $row_Cek)
		{
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc')
            {
                $nomor = ($total_data - $start_dari) - $urut2;
            }
            if($asc_desc == 'desc')
            {
                
				$nomor = $urut1 + $start_dari;
            }

			$NO_IPP 				= str_replace('PRO-','',$row_Cek['id_produksi']);
			$GET_PRODUKSI_DETAIL	= $this->db->get_where('production_detail',array('id'=>$row_Cek['id_production_detail']))->result();
			$kode_hist				= (!empty($GET_PRODUKSI_DETAIL[0]->print_merge_date))?$GET_PRODUKSI_DETAIL[0]->print_merge_date:'-';
			$id_milik				= $row_Cek['id_milik'];
			$QTY_ORDER				= (!empty($GET_PRODUKSI_DETAIL[0]->qty))?$GET_PRODUKSI_DETAIL[0]->qty:'-';
			$START_PRODUKSI			= (!empty($GET_PRODUKSI_DETAIL[0]->production_date))?$GET_PRODUKSI_DETAIL[0]->production_date:'-';
			$SELESAI_PRODUKSI		= (!empty($GET_PRODUKSI_DETAIL[0]->finish_production_date))?$GET_PRODUKSI_DETAIL[0]->finish_production_date:'-';
			$GET_PRODUKSI_PARSIAL 	= $this->db->get_where('production_spk_parsial',array('id_milik'=>$id_milik,'created_date'=>$kode_hist))->result();
			$id_gudang				= (!empty($GET_PRODUKSI_PARSIAL[0]->id_gudang))?$GET_PRODUKSI_PARSIAL[0]->id_gudang:'-';
			

			$tandaIPP = substr($NO_IPP,0,4);
			// $no_spk = (!empty($GET_PRODUKSI_DETAIL[0]->no_spk))?$GET_PRODUKSI_DETAIL[0]->no_spk:'-';
			if($tandaIPP == 'IPPT'){
				$getDetailTanki = $this->tanki_model->get_ipp_detail($NO_IPP);
				$nm_customer		= $getDetailTanki['customer'];
				$nm_project		= $getDetailTanki['nm_project'];
				$so_number		= $row_Cek['no_so'];
				$length			= 0;
				$thickness		= 0;
				$no_spk		= $row_Cek['no_spk'];
			}
			else{
				$nm_customer 	= $SERACH_DETAIL_IPP[$NO_IPP]['nm_customer'];
				$nm_project 	= $SERACH_DETAIL_IPP[$NO_IPP]['nm_project'];
				$so_number 		= $SERACH_DETAIL_IPP[$NO_IPP]['so_number'];
				$length			= $SERACH_DETAIL_SPEC[$id_milik]['length'];
				$thickness		= $SERACH_DETAIL_SPEC[$id_milik]['thickness'];
				$no_spk		= $row_Cek['no_spk2'];
			}

            $nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".get_name('warehouse','nm_gudang','id',$id_gudang)."</div>";
			$nestedData[]	= "<div align='left'>".$nm_customer."</div>";
			$nestedData[]	= "<div align='left'>".$nm_project."</div>";
			$nestedData[]	= "<div align='center'>".$so_number."</div>";
			$nestedData[]	= "<div align='center'>".$no_spk."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($row_Cek['status_date']))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($START_PRODUKSI))."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y',strtotime($SELESAI_PRODUKSI))."</div>";
			$nestedData[]	= "<div align='left'>".$row_Cek['id_category']."</div>";
			$nestedData[]	= "<div align='center'>".$row_Cek['diameter']."</div>";
			$nestedData[]	= "<div align='center'>".$row_Cek['diameter2']."</div>";
			$nestedData[]	= "<div align='center'>".$length."</div>";
			$nestedData[]	= "<div align='center'>".$thickness."</div>";
			$nestedData[]	= "<div align='center'>".$row_Cek['liner']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($QTY_ORDER)."</div>";
			$QTY = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;
			$nestedData[]	= "<div align='center'>".number_format($QTY)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row_Cek['qty_awal'])."-".number_format($row_Cek['qty_akhir'])."</div>";

			$nm_veil		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['nm_material']:'';
			$berat_veil		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0003']['terpakai']:0;
			$nm_csm			= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['nm_material']:'';
			$berat_cms		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0004']['terpakai']:0;
			$nm_rooving		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['nm_material']:'';
			$berat_rooving	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0005']['terpakai']:0;
			$nm_wr			= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['nm_material']:'';
			$berat_wr		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0006']['terpakai']:0;
			$nm_resin		= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['nm_material']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['nm_material']:'';
			$berat_resin	= (!empty($SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']:0;
			$nm_catalys		= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['nm_material']:'';
			$berat_catalys	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0002']['terpakai']:0;
			$berat_resin_tc	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$row_Cek['id_production_detail']]['TYP-0001']['terpakai']:0;
			
			$berat_lainnya	= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['terpakai']:0;
			$berat_add		= (!empty($SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['terpakai']))?$SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['terpakai']:0;
			$nm_lainnya		= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$row_Cek['id_production_detail']]['nm_material']:'';
			$nm_add			= (!empty($SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['nm_material']))?$SEARCH_DETAIL_BERAT_ADD[$row_Cek['id_production_detail']]['nm_material']:'';

			$nestedData[]	= "<div align='left'>".strtoupper($nm_veil)."</div>";
			$nestedData[]	= "<div align='right' title='".$row_Cek['id_production_detail']."'>".number_format($berat_veil,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_csm)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_cms,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_rooving)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_rooving,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_wr)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_wr,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_resin)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_resin+$berat_resin_tc,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_catalys)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_catalys,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_lainnya)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_lainnya,4)."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($nm_add)."</div>";
			$nestedData[]	= "<div align='right'>".number_format($berat_add,4)."</div>";

			$TOTAL_MATERIAL = $berat_veil+$berat_cms+$berat_rooving+$berat_wr+$berat_resin+$berat_catalys+$berat_resin_tc+$berat_lainnya+$berat_add;
			$GET_DETAIL_SO = $this->db->get_where('so_detail_header',array('id'=>$id_milik))->result();
			$WH = 0;
			$MP = 0;
			$MH = 0;
			if(!empty($GET_DETAIL_SO)){
				$WH = $GET_DETAIL_SO[0]->total_time;
				$MP = $GET_DETAIL_SO[0]->man_power;
				$MH = $GET_DETAIL_SO[0]->man_hours;
			}

			if($tandaIPP == 'IPPT'){
				$GET_DETAIL_SO_TANKI = $this->db2->get_where('bq_detail_detail',array('id'=>$id_milik))->result();
				if(!empty($GET_DETAIL_SO_TANKI)){
					$WH = $GET_DETAIL_SO_TANKI[0]->t_time;
					$MP = $GET_DETAIL_SO_TANKI[0]->mp;
					$MH = $GET_DETAIL_SO_TANKI[0]->man_hours;
				}
			}

			$nestedData[]	= "<div align='right'>".number_format($TOTAL_MATERIAL,4)."</div>";
			$nestedData[]	= "<div align='center'>".$QTY * $WH."</div>";
			$nestedData[]	= "<div align='center'>".$QTY * $MP."</div>";
			$nestedData[]	= "<div align='center'>".$QTY * $MH."</div>";
            
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

	public function get_query_json_report_product($tanggal, $bulan, $tahun, $range, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
        $where_tgl = "";
        if($tanggal > 0){
            $where_tgl = "AND DAY(a.status_date) = '".$tanggal."' ";
        }
		
		$where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(a.status_date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(a.status_date) = '".$tahun."' ";
        }
		
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND DATE(a.status_date) BETWEEN '".$date_awal."' AND '".$date_akhir."' ";
        }
		
		//REPLACE(a.id_produksi,'PRO','BQ') = c.id_bq AND

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_spk AS no_spk2
			FROM
				laporan_per_hari a
				LEFT JOIN so_detail_header b ON a.id_milik = b.id,
                (SELECT @row:=0) r
		    WHERE 1=1 AND a.id_category <> '' ".$where_tgl." ".$where_bln." ".$where_thn." ".$where_range." AND (
				a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit; OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi',
			2 => 'id_produksi',
			3 => 'id_produksi',
			4 => 'id_produksi',
			5 => 'id_produksi'
			
		);

		$sql .= " ORDER BY a.status_date DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
}
