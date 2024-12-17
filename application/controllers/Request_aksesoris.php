<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_aksesoris extends CI_Controller {
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
		  'title'			=> 'Request Item Project',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		history('View request item project');
		$this->load->view('Request_aksesoris/index',$data);
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qtyCount'])."</div>";

            $create	= "";
            if($Arr_Akses['create']=='1'){
                $create	= "<button class='btn btn-sm btn-success request' title='Request' data-id_bq='".$row['id_bq']."'><i class='fa fa-plus'></i></button>";
            }

            $nestedData[]	= "<div align='center'>".$create."</div>";

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
				a.id_bq,
				b.so_number,
                c.nm_customer,
                c.project,
                COUNT(a.id) AS qtyCount
			FROM
				so_acc_and_mat a
                LEFT JOIN so_number b ON a.id_bq=b.id_bq
                LEFT JOIN production c ON REPLACE(a.id_bq,'BQ-','')=c.no_ipp,
				(SELECT @row:=0) r
		    WHERE a.category IN ('baut','plate','gasket','lainnya') 
                AND (
                    a.id_bq LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR b.so_number LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.nm_customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.project LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY a.id_bq
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'id_bq',
			2 => 'b.so_number',
			3 => 'c.nm_customer',
			4 => 'c.project',
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
            $no_ipp	        = $data['no_ipp'];

			$tandaTanki = substr($no_ipp,0,4);
			
			if(!empty($data['add'])){
				$dataDetail	= $data['add'];
			}

			$TableUpdate = 'so_acc_and_mat';
			if($tandaTanki == 'IPPT'){
				$TableUpdate = 'planning_tanki_detail';
			}

            $Ym             = date('ym');
            $SQL			= "SELECT MAX(kode) as maxP FROM request_accessories WHERE kode LIKE 'P".$Ym."%' ";
            $resultIPP		= $this->db->query($SQL)->result_array();
            $angkaUrut2		= $resultIPP[0]['maxP'];
            $urutan2		= (int)substr($angkaUrut2, 5, 5);
            $urutan2++;
            $urut2			= sprintf('%05s',$urutan2);
            $kode			= "P".$Ym.$urut2;

			$ArrDeatil = array();
			$ArrDeatilUpdate = array();
            foreach($dataDetail AS $val => $valx){
                $QTY = str_replace(',','',$valx['request']);
                if($QTY > 0){
                    $ArrDeatil[$val]['kode']            = $kode;
                    $ArrDeatil[$val]['no_ipp']          = $no_ipp;
                    $ArrDeatil[$val]['id_milik']        = $valx['id'];
                    $ArrDeatil[$val]['qty_request']     = $QTY;
                    $ArrDeatil[$val]['created_by']    	= $username;
                    $ArrDeatil[$val]['created_date']    = $datetime;

                   

                    $ArrDeatilUpdate[$val]['id']          	= $valx['id'];
					if($tandaTanki == 'IPPT'){
						$GET_REQ        = $this->db->get_where($TableUpdate,array('id' => $valx['id']))->result();
						$QTY_REQUEST    = $GET_REQ[0]->request + $QTY;
						$ArrDeatilUpdate[$val]['request']    	= $QTY_REQUEST;
						$ArrDeatilUpdate[$val]['id_material']   = $valx['id_material2'];
						$ArrDeatilUpdate[$val]['id_material2']  = $valx['id_material'];
					}
					else{
						$GET_REQ        = $this->db->get_where($TableUpdate,array('id' => $valx['id']))->result();
						$QTY_REQUEST    = $GET_REQ[0]->qty_req + $QTY;

						$ArrDeatilUpdate[$val]['id_material2']  = $valx['id_material2'];
                   		$ArrDeatilUpdate[$val]['qty_req']    	= $QTY_REQUEST;
					}
                }
            }
			
			$this->db->trans_start();
				if(!empty($ArrDeatil)){
					$this->db->insert_batch('request_accessories', $ArrDeatil);
				}
                if(!empty($ArrDeatilUpdate)){
					$this->db->update_batch($TableUpdate, $ArrDeatilUpdate,'id');
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
				history('Request aksesoris '.$kode);
			}
			echo json_encode($Arr_Data);
		}
		else{
			$id_bq              = $this->uri->segment(3);
			$tandaTanki 	= substr($id_bq,0,4);
			$result_aksesoris   = $this->db->get_where('so_acc_and_mat',array('category <>'=>'mat','id_bq'=>$id_bq,'qty >'=>0))->result_array();
			$list_aksesoris   	= $this->db->get_where('accessories',array('deleted_date'=>NULL,'id_acc_tanki'=>NULL))->result_array();
			if($tandaTanki == 'IPPT'){
				$result_aksesoris   = $this->db
                                        ->select('a.*, b.customer as nm_customer, e.id_customer AS id_customer, a.berat as qty, c.id_material AS code_group, a.request as qty_req')
                                        ->where('a.close_sts','0')
                                        ->join('planning_tanki b','a.no_ipp=b.no_ipp','left')
                                        ->join('accessories c','a.id_material=c.id_acc_tanki','left')
										->join('customer e','b.customer=e.nm_customer','left')
                                        ->get_where('planning_tanki_detail a',
                                            array(
                                                'a.no_ipp'=>$id_bq,
												'a.category'=>'acc'
                                                )
                                            )
										->group_by('a.id')
                                        ->result_array();
				$list_aksesoris   	= $this->db->select('id_material,nama,spesifikasi,material,id_acc_tanki as id')->get_where('accessories',array('deleted_date'=>NULL,'id_acc_tanki <>'=>NULL))->result_array();
			}
			$data = array(
				'tandaTanki' 		=> $tandaTanki,
				'id_bq' 		    => $id_bq,
				'result_aksesoris' 	=> $result_aksesoris,
				'list_aksesoris' 	=> $list_aksesoris,
			);
			$this->load->view('Request_aksesoris/add', $data);
		}
	}

	public function tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
		  $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
		  redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
		  'title'			=> 'Request Item Project Tanki',
		  'action'		    => 'index',
		  'row_group'		=> $data_Group,
		  'akses_menu'	    => $Arr_Akses
		);
		history('View request item project tanki');
		$this->load->view('Request_aksesoris/tanki',$data);
	}

    public function server_side_request_tanki(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);

		$requestData	= $_REQUEST;
		$fetch			= $this->query_data_json_request_tanki(
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
			$nestedData[]	= "<div align='center'>".str_replace('BQ-','',$row['id_bq'])."</div>";
			$nestedData[]	= "<div align='center'>".$row['so_number']."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['nm_customer'])."</div>";
			$nestedData[]	= "<div align='left'>".strtoupper($row['project'])."</div>";
			$nestedData[]	= "<div align='center'>".number_format($row['qtyCount'])."</div>";

            $create	= "";
            if($Arr_Akses['create']=='1'){
                $create	= "<button class='btn btn-sm btn-success request' title='Request' data-id_bq='".$row['id_bq']."'><i class='fa fa-plus'></i></button>";
            }

            $nestedData[]	= "<div align='center'>".$create."</div>";

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

	public function query_data_json_request_tanki($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

		$sql = "
			SELECT
				(@row:=@row+1) AS nomor,
				a.no_ipp as id_bq,
				c.no_so as so_number,
                c.customer as nm_customer,
                c.project,
                COUNT(a.id) AS qtyCount
			FROM
				planning_tanki_detail a
                LEFT JOIN planning_tanki c ON a.no_ipp = c.no_ipp,
				(SELECT @row:=0) r
		    WHERE a.category IN ('acc') 
                AND (
                    a.no_ipp LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.no_so LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.customer LIKE '%".$this->db->escape_like_str($like_value)."%'
                    OR c.project LIKE '%".$this->db->escape_like_str($like_value)."%'
                )
            GROUP BY a.no_ipp
		";
		// echo $sql; exit;

		$data['totalData'] = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'a.no_ipp',
			2 => 'c.no_so',
			3 => 'c.customer',
			4 => 'c.project',
		);

		$sql .= " ORDER BY a.id DESC, ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

		$data['query'] = $this->db->query($sql);
		return $data;
	}


}