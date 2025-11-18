<?php

class Non_rutin_model extends CI_Model {

	public function __construct() { 
		parent::__construct();
		// Your own constructor code
	}

	public function get_data_json_non_rutin(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)))."/index";
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_non_rutin(
			$requestData['tanda'],
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
			
			$tanda = $requestData['tanda'];
			
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$no_pr = (!empty($row['no_pr']))?$row['no_pr']:"<span class='text-red' title='No Pengajuan'>".$row['no_pengajuan']."</span>";
			$nestedData[]	= "<div align='left'>".$no_pr."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_dept'])."</div>";

			// $list_barang	= $this->db->get_where('rutin_non_planning_detail',array('no_pengajuan'=>$row['no_pengajuan']))->result_array();
			// $arr_nmbarang = array();
			// $arr_spec = array();
			// $arr_qty = array();
			// $arr_tanggal = array();
			// $arr_ket = array();
			// foreach($list_barang AS $val => $valx){
			// 	$get_satuan = $this->db->get_where('raw_pieces',array('id_satuan'=>$valx['satuan']))->result();
			// 	$nm_satuan = (!empty($get_satuan))?strtolower($get_satuan[0]->kode_satuan):'';
			// 	$arr_nmbarang[$val] = "&bull; ".strtoupper($valx['nm_barang']);
			// 	$arr_spec[$val] = "&bull; ".strtoupper($valx['spec']);
			// 	$arr_qty[$val] = "&bull; ".floatval($valx['qty']).' '.$nm_satuan;
			// 	$tgl_dibutuhkan = ($valx['tanggal'] <> '0000-00-00' AND $valx['tanggal'] != NULL)?date('d-M-Y', strtotime($valx['tanggal'])):'not set';
			// 	$arr_tanggal[$val] = "&bull; ".$tgl_dibutuhkan;
			// 	$arr_ket[$val] = "&bull; ".strtoupper($valx['keterangan']);
			// }
			// $dt_nama_barang	= implode("<br>", $arr_nmbarang);
			// $dt_spec	= implode("<br>", $arr_spec);
			// $dt_qty	= implode("<br>", $arr_qty);
			// $dt_tanggal	= implode("<br>", $arr_tanggal);
			// $dt_ket	= implode("<br>", $arr_ket);

			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_barang_group'])."</div>";
			// $nestedData[]	= "<div align='left'>".strtoupper($row['spec_group'])."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_qty."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_tanggal."</div>";
			// $nestedData[]	= "<div align='left'>".$dt_ket."</div>";
			
			$last_by 	= (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
			$last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
			
			$nestedData[]	= "<div align='left'>".strtoupper($last_by)."</div>";
			$nestedData[]	= "<div align='center'>".date('d-M-Y', strtotime($last_date))."</div>";
			
			if($row['sts_app'] == 'N'){
				$warna 	= 'blue';
				$sts 	= 'WAITING APPROVAL';
			}
			elseif($row['sts_app'] == 'Y'){
				$warna 	= 'green';
				$sts 	= 'APPROVED';
			}
			else{
				$warna 	= 'red';
				$sts 	= 'REJECTED';
			}
			
			$nestedData[]	= "<div align='left'><span class='badge' style='background-color: ".$warna.";'>".$sts."</span></div>";
				$view		= "<a href='".base_url('non_rutin/add/'.$row['no_pengajuan'].'/view')."' class='btn btn-sm btn-warning' title='View' data-role='qtip'><i class='fa fa-eye'></i></a>";
				$edit		= "";
				$approve	= "";
				$cancel		= "";
				$print	= "&nbsp;<a href='".base_url('non_rutin/print_pengajuan_non_rutin/'.$row['no_pengajuan'])."' target='_blank' class='btn btn-sm btn-success' title='Print'><i class='fa fa-print'></i></a>";
				
				
				if($tanda <> 'approval'){
					if($Arr_Akses['update']=='1'){
						if($row['sts_app'] == 'N'){
							$edit	= "&nbsp;<a href='".base_url('non_rutin/add/'.$row['no_pengajuan'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-edit'></i></a>";
						}
					}
				}
				
				if($tanda == 'approval'){
					$view		= "";
					if($Arr_Akses['approve']=='1'){
						if($row['sts_app'] == 'N'){
							$approve	= "&nbsp;<a href='".base_url('non_rutin/add/'.$row['no_pengajuan'].'/approve')."' class='btn btn-sm btn-info' title='Approve' data-role='qtip'><i class='fa fa-check'></i></a>";
						}
					}
				}
			$nestedData[]	= "<div align='left'>
									".$view."
                                    ".$edit."
									".$approve."
									".$cancel."
									".$print."
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

	public function query_data_json_non_rutin($tanda, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		
		$where = "";
		if($tanda == 'approval'){
			$where = "AND a.sts_app = 'N' ";
		}
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*,
				b.nm_dept,
				GROUP_CONCAT(CONCAT(z.nm_barang,', ',z.spec,' <b>(',z.qty,' ',LOWER(y.kode_satuan),')</b>, ',z.tanggal,', ',LOWER(z.keterangan)) ORDER BY z.id ASC SEPARATOR '<br>') AS nm_barang_group
			FROM
				rutin_non_planning_detail z
				LEFT JOIN rutin_non_planning_header a ON z.no_pengajuan=a.no_pengajuan
				LEFT JOIN department b ON a.id_dept=b.id
				LEFT JOIN raw_pieces y ON z.satuan=y.id_satuan,
				(SELECT @row:=0) r
		    WHERE 1=1 ".$where." AND a.status_id = 1 AND (
				a.no_pengajuan LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.tanggal LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.no_pr LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR b.nm_dept LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.nm_barang LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.spec LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR z.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
			GROUP BY z.no_pengajuan
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_pr',
			2 => 'b.nm_dept'
		);

		$sql .= " ORDER BY id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
}