<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_wip_spool extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
	}
	public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1).'/'.$this->uri->segment(2)));
		$Arr_Akses			= getAcccesmenu($controller);		
		$data_Group	= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Gudang WIP Spool',
			'action'		=> 'index',
			'row_group'		=> $data_Group
			// 'akses_menu'	=> $Arr_Akses
		);
		history('View data gudang wip spool');
		$this->load->view('Gudang_wg/gudang_wip_spool',$data);
	}

	public function server_side_spool(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/spool';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_spool(
			$requestData['date_filter'],
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

		$DATEFILTER = $requestData['date_filter'];
		$GET_NO_SPK = get_detail_final_drawing();
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

			$ArrNo_Spool    = [];
			$ArrNo_IPP      = [];
			$ArrNo_SPK      = [];
			$ArrNo_Drawing  = [];
			$wip=0;
            if(empty($DATEFILTER)){
                $get_split_ipp  = $this->db->select('id_produksi, id_milik, kode_spool, product_code, no_drawing, nilai_wip, nilai_fg')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
                $get_split_ipp2  = $this->db->select('kode_spool, product_code, id_category as product, COUNT(id) AS qty, sts, id_milik')->group_by('sts,kode_spool,id_milik')->get_where('spool_group_all',array('spool_induk'=>$row['spool_induk']))->result_array();
                foreach ($get_split_ipp as $key => $value) { $key++;
                    $ArrNo_Spool[]  = $value['kode_spool'];
                    $ArrNo_IPP[]    = str_replace('PRO-','',$value['id_produksi']);
                    $ArrNo_Drawing[]= $value['no_drawing'];
					$wip=($wip+$value['nilai_wip']);
                }
                foreach ($get_split_ipp2 as $key => $value) { $key++;
					$no_spk         = (!empty($GET_NO_SPK[$value['id_milik']]['no_spk']))?$GET_NO_SPK[$value['id_milik']]['no_spk']:'not set';
                    $IMPLODE        = explode('-', $value['product_code']);
                    $ArrNo_SPK[]    = $key.'. '.$value['kode_spool'].'/'.$IMPLODE[0].'/'.strtoupper($value['product']).' <b class="text-blue">['.$value['qty'].' PCS]</b>/'.$no_spk.'/'.strtoupper($value['sts']);
                }
            }
            else{
                $get_split_ipp  = $this->db->select('no_ipp, id_milik, kode_spool, no_drawing')->get_where('stock_barang_wip_per_day',array('spool_induk'=>$row['spool_induk'],'category'=>'spool'))->result_array();
                $get_split_ipp2  = $this->db->select('kode_spool, product, COUNT(id) AS qty, sts, no_so, id_milik')->group_by('sts,kode_spool,id_milik')->get_where('stock_barang_wip_per_day',array('spool_induk'=>$row['spool_induk'],'category'=>'spool'))->result_array();
                foreach ($get_split_ipp as $key => $value) { $key++;
                    $ArrNo_Spool[]  = $value['kode_spool'];
                    $ArrNo_IPP[]    = $value['no_ipp'];
                    $ArrNo_Drawing[]= $value['no_drawing'];
                }
                foreach ($get_split_ipp2 as $key => $value) { $key++;
					$no_spk         = (!empty($GET_NO_SPK[$value['id_milik']]['no_spk']))?$GET_NO_SPK[$value['id_milik']]['no_spk']:'not set';
                    $ArrNo_SPK[]    = $key.'. '.$value['kode_spool'].'/'.$value['no_so'].'/'.strtoupper($value['product']).' <b class="text-blue">['.$value['qty'].' PCS]</b>/'.$no_spk.'/'.strtoupper($value['sts']);
                }
            }
			
			$explode_spo     = implode('<br>',array_unique($ArrNo_Spool));
			$explode_ipp    = implode('<br>',array_unique($ArrNo_IPP));
			$explode_spk    = implode('<br>',$ArrNo_SPK);
			$explode_drawing= implode('<br>',array_unique($ArrNo_Drawing));

			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='center'>".$row['spool_induk']."</div>";
			$nestedData[]	= "<div align='center'>".$explode_spo."</div>";
			$nestedData[]	= "<div align='left'>".$explode_drawing."</div>";
			$nestedData[]	= "<div align='center'>".$explode_ipp."</div>";
			$nestedData[]	= "<div align='left'>".$explode_spk."</div>";
			$nestedData[]	= "<div align='center'>".number_format($wip)."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['nilai_fg'])."</div>";
			// $nestedData[]	= "<div align='center'>".$row['spool_by']."</div>";
			// $nestedData[]	= "<div align='center'>".date('d-M-Y H:i:s', strtotime($row['spool_date']))."</div>";
			
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

	public function query_data_spool($date_filter, $like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$where2 = " AND a.id_produksi NOT IN ".filter_not_in()." ";
		if($date_filter == ''){
		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.*
			FROM
				spool_group_all a,
				(SELECT @row:=0) r
		    WHERE 1=1 
				AND a.release_spool_date IS NULL 
				AND a.lock_spool_date IS NOT NULL 
	 			".$where2."
				AND (
					a.kode_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
					OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
				)
			GROUP BY
				a.spool_induk
		";
		}
		else{
			$sql = "
				SELECT
					(@row:=@row+1) AS nomor,
					a.*
				FROM
					stock_barang_wip_per_day a,
					(SELECT @row:=0) r
				WHERE 1=1
					AND a.category = 'spool'
					AND DATE(a.hist_date) = '".$date_filter."'
					AND (
						a.no_spk LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.kode_spool LIKE '%".$this->db->escape_like_str($like_value)."%'
						OR a.spool_induk LIKE '%".$this->db->escape_like_str($like_value)."%'
					)
				GROUP BY
					a.spool_induk
			";
		}
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'kode_spool',
			2 => 'kode_spk',
			3 => 'kode_spk',
			4 => 'kode_spk'
		);

		$sql .= " ORDER BY a.spool_induk DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}
	
}