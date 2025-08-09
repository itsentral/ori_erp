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
			$kode_hist				= $row_Cek['print_merge_date'];
			$id_milik				= $row_Cek['id_milik'];
			$QTY_ORDER				= $row_Cek['qty'];
			$START_PRODUKSI			= $row_Cek['production_date'];
			$SELESAI_PRODUKSI		= $row_Cek['finish_production_date'];
			$id_gudang				= $row_Cek['id_gudang'];
			$so_number				= $row_Cek['nomor_so'];
			$no_spk					= $row_Cek['nomor_spk'];

			$tandaIPP = substr($NO_IPP,0,4);
			if($tandaIPP == 'IPPT'){
				$getDetailTanki = $this->tanki_model->get_ipp_detail($NO_IPP);
				$nm_customer	= $getDetailTanki['customer'];
				$nm_project		= $getDetailTanki['nm_project'];
				$length			= 0;
				$thickness		= 0;
				$nm_product		= $row_Cek['nm_tanki'];
				
			}
			else{
				$nm_customer 	= $SERACH_DETAIL_IPP[$NO_IPP]['nm_customer'];
				$nm_project 	= $SERACH_DETAIL_IPP[$NO_IPP]['nm_project'];
				$length			= $SERACH_DETAIL_SPEC[$id_milik]['length'];
				$thickness		= $SERACH_DETAIL_SPEC[$id_milik]['thickness'];
				$nm_product		= $row_Cek['id_category'];
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
			$nestedData[]	= "<div align='left'>".$nm_product."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row_Cek['diameter'],2)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row_Cek['diameter2'],2)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($length,2)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($thickness,2)."</div>";
			$nestedData[]	= "<div align='center'>".$row_Cek['liner']."</div>";
			$nestedData[]	= "<div align='center'>".number_format($QTY_ORDER)."</div>";
			$QTY = $row_Cek['qty_akhir'] - $row_Cek['qty_awal'] + 1;
			$nestedData[]	= "<div align='center'>".number_format($QTY)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row_Cek['qty_awal'])."-".number_format($row_Cek['qty_akhir'])."</div>";

			$id_production_detail = $row_Cek['id_production_detail'];
			$nm_veil		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0003']['nm_material']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0003']['nm_material']:'';
			$berat_veil		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0003']['terpakai']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0003']['terpakai']:0;
			$nm_csm			= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0004']['nm_material']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0004']['nm_material']:'';
			$berat_cms		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0004']['terpakai']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0004']['terpakai']:0;
			$nm_rooving		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0005']['nm_material']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0005']['nm_material']:'';
			$berat_rooving	= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0005']['terpakai']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0005']['terpakai']:0;
			$nm_wr			= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0006']['nm_material']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0006']['nm_material']:'';
			$berat_wr		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0006']['terpakai']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0006']['terpakai']:0;
			$nm_resin		= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0001']['nm_material']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0001']['nm_material']:'';
			$berat_resin	= (!empty($SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT[$id_production_detail]['TYP-0001']['terpakai']:0;
			$nm_catalys		= (!empty($SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0002']['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0002']['nm_material']:'';
			$berat_catalys	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0002']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0002']['terpakai']:0;
			$berat_resin_tc	= (!empty($SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0001']['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS[$id_production_detail]['TYP-0001']['terpakai']:0;
			
			$berat_lainnya	= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$id_production_detail]['terpakai']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$id_production_detail]['terpakai']:0;
			$berat_add		= (!empty($SEARCH_DETAIL_BERAT_ADD[$id_production_detail]['terpakai']))?$SEARCH_DETAIL_BERAT_ADD[$id_production_detail]['terpakai']:0;
			$nm_lainnya		= (!empty($SEARCH_DETAIL_BERAT_PLUS_EX[$id_production_detail]['nm_material']))?$SEARCH_DETAIL_BERAT_PLUS_EX[$id_production_detail]['nm_material']:'';
			$nm_add			= (!empty($SEARCH_DETAIL_BERAT_ADD[$id_production_detail]['nm_material']))?$SEARCH_DETAIL_BERAT_ADD[$id_production_detail]['nm_material']:'';

			$nestedData[]	= "<div align='left'>".strtoupper($nm_veil)."</div>";
			$nestedData[]	= "<div align='right' title='".$id_production_detail."'>".number_format($berat_veil,4)."</div>";
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
            $where_tgl = "AND DAY(a.insert_date) = '".$tanggal."' ";
        }
		
		$where_bln = "";
        if($bulan > 0){
            $where_bln = "AND MONTH(a.insert_date) = '".$bulan."' ";
        }

        $where_thn = "";
        if($tahun > 0){
            $where_thn = "AND YEAR(a.insert_date) = '".$tahun."' ";
        }
		
		$where_range = "";
        if($range > 0){
			$exP = explode(' - ', $range);
			$date_awal = date('Y-m-d', strtotime($exP[0]));
			$date_akhir = date('Y-m-d', strtotime($exP[1]));
			// echo $exP[0];exit;
            $where_range = "AND DATE(a.insert_date) BETWEEN '".$date_awal."' AND '".$date_akhir."' ";
        }
		
		//REPLACE(a.id_produksi,'PRO','BQ') = c.id_bq AND

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.no_spk AS nomor_spk,
				b.id_product AS nm_tanki,
				SUBSTRING(b.product_code,1,9) as nomor_so,
				b.production_date,
				b.finish_production_date,
				b.print_merge_date,
				c.id_gudang
			FROM
				laporan_wip_per_hari_action a
				INNER JOIN production_detail b ON a.id_production_detail = b.id
				LEFT JOIN production_spk_parsial c ON a.id_milik=c.id_milik AND b.print_merge_date=c.created_date,
                (SELECT @row:=0) r
		    WHERE 1=1 AND a.id_category <> '' ".$where_tgl." ".$where_bln." ".$where_thn." ".$where_range." AND (
				a.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.product_code LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		// echo $sql; exit;

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

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
}
