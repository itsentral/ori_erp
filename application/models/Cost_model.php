<?php
class Cost_model extends CI_Model {

	public function __construct() {
		parent::__construct();
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
		foreach($query->result_array() as $row)
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
            $nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".str_replace('PRO-','',$row['id_produksi'])."</div>";
			$prodate = (!empty($row['production_date']))?$row['production_date']:$row['status_date'];
			$nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($prodate))."</div>";
            $nestedData[]	= "<div align='center'>".date('d-m-Y', strtotime($row['status_date']))."</div>";
			$nestedData[]	= "<div align='center'>".get_name_report(get_jalur($row['id_produksi'])['bq'], 'no_spk', 'id', $row['id_milik'])."</div>";
			$nestedData[]	= "<div align='left'>".ucwords(strtolower($row['id_category']))."</div>";
			$nestedData[]	= "<div align='left'>".$row['id_product']."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".get_name_report(get_jalur($row['id_produksi'])['bq'], 'diameter_1', 'id', $row['id_milik'])."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".get_name_report(get_jalur($row['id_produksi'])['bq'], 'diameter_2', 'id', $row['id_milik'])."</div>";
			$nestedData[]	= "<div align='center'>".substr(get_name_report(get_jalur($row['id_produksi'])['bq'], 'series', 'id', $row['id_milik']),1,4)."</div>";
			$nestedData[]	= "<div align='center'>".substr(get_name_report(get_jalur($row['id_produksi'])['bq'], 'series', 'id', $row['id_milik']),6)."</div>";
			$rgn = get_name_report('product_range', 'qty_awal', 'id', $row['id'])." - ".get_name_report('product_range', 'qty_akhir', 'id', $row['id']);
			if(get_name_report('product_range', 'qty_awal', 'id', $row['id']) == get_name_report('product_range', 'qty_akhir', 'id', $row['id'])){
				$rgn = get_name_report('product_range', 'qty_awal', 'id', $row['id']);
			}
			
			$nestedData[]	= "<div align='center'>".get_name_report(get_jalur($row['id_produksi'])['bq'], 'qty', 'id', $row['id_milik'])."</div>";
			$nestedData[]	= "<div align='center'>".$rgn."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".number_format(get_name_report(get_jalur($row['id_produksi'])['comp'], 'est', 'id_milik', $row['id_milik']),2)."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".number_format(get_name_report(get_jalur($row['id_produksi'])['comp'], 'max_toleransi', 'id_milik', $row['id_milik']) * 100 ,2)."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".number_format(get_name_report(get_jalur($row['id_produksi'])['comp'], 'min_toleransi', 'id_milik', $row['id_milik']) * 100 ,2)."</div>";
			$nestedData[]	= "<div align='right' style='padding-right:20px;'>".number_format($row['est_real'],2)."</div>";
            
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
				a.id_produksi,
				a.status_date,
				b.id_category,
				b.id_product,
				b.production_date,
				b.est_real,
				b.id_milik,
				b.id,
				c.no_spk
			FROM
				production_real_detail a
					LEFT JOIN production_detail b ON a.id_production_detail=b.id
					LEFT JOIN so_detail_header c ON b.id_milik=c.id,
                (SELECT @row:=0) r
		    WHERE 1=1 AND  b.id_category <> '' ".$where_tgl." ".$where_bln." ".$where_thn." ".$where_range." AND (
				a.status_date LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.id_produksi LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.id_category LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR c.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY
				a.id_production_detail
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_produksi',
			2 => 'status_date',
			3 => 'no_spk',
			4 => 'id_category',
			5 => 'id_product'
			
		);

		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
    
}
