<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery_cogs extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('Acc_model');
		$this->load->model('Jurnal_model');
		if(!$this->session->userdata('isORIlogin')) redirect('login');
	}
	public function index(){
		$controller			= 'delivery_cogs';
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			//redirect(site_url('dashboard'));
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Index Of Delivery Cogs',
			'action'		=> 'delivery_cogs',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses
		);
		history('View Data Delivery To COGS');
		$this->load->view('Delivery_cogs/index',$data);
	}
	public function data_side(){
		$controller			= 'delivery_cogs';
		$Arr_Akses			= getAcccesmenu($controller);
		$requestData	= $_REQUEST;
		$fetch			= $this->get_query_json(
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
		foreach($query->result_array() as $row) {
			$total_data     = $totalData;
            $start_dari     = $requestData['start'];
            $asc_desc       = $requestData['order'][0]['dir'];
            if($asc_desc == 'asc') $nomor = $urut1 + $start_dari;
            if($asc_desc == 'desc') $nomor = ($total_data - $start_dari) - $urut2;
			$nestedData 	= array();
			$nestedData[]	= "<div align='center'>".$nomor."</div>";
			$nestedData[]	= "<div align='left'>".$row['no_do']." - <a target='_blank' href='".base_url("delivery/view_delivery/".$row['no_do'])."'>".$row['nomor_sj']."</a></div>";
			$nestedData[]	= "<div align='left'>".$row['tanggal']."</div>";
			$nestedData[]	= "<div align='left'>".$row['keterangan']."</div>";
			$detail		= "";
			$edit		= "";
			if($Arr_Akses['read']=='1'){
				if($row['status']==0){
					if($Arr_Akses['update']=='1'){
						$detail	.= "&nbsp;<button type='button' class='btn btn-sm btn-warning edit' title='Edit data' data-code='".$row['id']."'><i class='fa fa-pencil'></i></button>";
					}
					if($Arr_Akses['approve']=='1'){
						$detail	.= "&nbsp;<button type='button' class='btn btn-sm btn-success approve' title='Update data' data-code='".$row['id']."'><i class='fa fa-check'></i></button>";
					}
					if($Arr_Akses['delete']=='1'){
						$detail	.= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete data' data-code='".$row['id']."'><i class='fa fa-trash'></i></button>";
					}
				}
				$detail	.= "&nbsp;<button type='button' class='btn btn-sm btn-default view' title='View data' data-code='".$row['id']."'><i class='fa fa-search'></i></button>";
			}
			$nestedData[]	= "<div align='left'> ".$detail." </div>";
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
	public function get_query_json($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){
		$sql = "
            SELECT
                (@row:=@row+1) AS nomor,
				a.*,b.nomor_sj
			FROM
                delivery_cogs a join delivery_product b on a.no_do=b.kode_delivery,
                (SELECT @row:=0) r
            WHERE
                1=1 AND (
                a.no_do LIKE '%".$this->db->escape_like_str($like_value)."%'
				OR a.keterangan LIKE '%".$this->db->escape_like_str($like_value)."%'
	        )
		";
		$data['totalData']  = $this->db->query($sql)->num_rows();
		$data['totalFiltered'] = $this->db->query($sql)->num_rows();
		$columns_order_by = array(
			0 => 'nomor',
			1 => 'no_do',
			2 => 'tanggal',
			3 => 'keterangan',
		);
		$sql .= " ORDER BY ".$columns_order_by[$column_order]." ".$column_dir." ";
		$sql .= " LIMIT ".$limit_start." ,".$limit_length." ";
		$data['query'] = $this->db->query($sql);
		return $data;
    }
	public function add_data(){
		if($this->input->post()){
			$Arr_Kembali	= array();
			$data			= $this->input->post();
			$data_session	= $this->session->userdata;
			$dateTime		= date('Y-m-d H:i:s');
			$id 		    = $data['id'];
			$no_do			= ($data['no_do']);
			$tanggal		= $data['tanggal'];
			$keterangan		= $data['keterangan'];
			if(empty($id)){
                $ArrHeader = array(
                    'no_do'			=> $no_do,
                    'tanggal' 		=> $tanggal,
                    'keterangan'	=> $keterangan,
                    'created_by' 	=> $data_session['ORI_User']['username'],
                    'created_date' 	=> $dateTime
                );
                $TandaI = "Insert";
			}
			if(!empty($id)){
                $ArrHeader = array(
                    'no_do'    		=> $no_do,
                    'tanggal' 		=> $tanggal,
                    'keterangan'	=> $keterangan,
                );
                $TandaI = "Update";
            }
            $this->db->trans_start();
                if(empty($id)) $this->db->insert('delivery_cogs', $ArrHeader);
                if(!empty($id)){
                    $this->db->where('id', $id);
                    $this->db->update('delivery_cogs', $ArrHeader);
                }
            $this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data failed. Please try again later ...',
					'status'	=> 0
				);
			} else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success.',
					'status'	=> 1
				);
				history($TandaI.' delivery_cogs '.$id);
			}
			echo json_encode($Arr_Kembali);
		} else{
			$controller			= 'delivery_cogs';
			$Arr_Akses			= getAcccesmenu($controller);
			if($Arr_Akses['create'] !='1'){
				$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
				redirect(site_url('users'));
            }
            $id = $this->uri->segment(3);
            $tipe = $this->uri->segment(4);
            $query = "SELECT * FROM delivery_cogs WHERE id ='".$id."' LIMIT 1 ";
            $result = $this->db->query($query)->result();
			$where="";
			if($id=="" || $tipe=="edit")  $where=" and st_cogs=0";
			if($tipe=="view" || $tipe=="update" )  $where=" and kode_delivery='".$result[0]->no_do."'";
            $dt_delivery = $this->db->query("SELECT * FROM delivery_product where confirm_date is not null ".$where)->result();
			$data = array(
				'title'		=> 'Data Delivery COGS',
                'action'	=> 'add',
                'dt_delivery'	=> $dt_delivery,
                'tipe'		=> $tipe,
                'data'      => $result
			);
			$this->load->view('Delivery_cogs/form',$data);
		}
	}
	public function update_data($id){
			$TandaI = "Update";
			$data_session	= $this->session->userdata;
			$UserName		= $data_session['ORI_User']['username'];
			$DateTime		= date('Y-m-d H:i:s');
			if(!empty($id)){
				$datadelivery = $this->db->query("select * from delivery_product_detail where kode_delivery in (select no_do from delivery_cogs where id='".$id."')" )->result_array();
				$total_cogs=0;
				$kode_delivery='';
				foreach ($datadelivery as  $keys => $val) {
					$kode_delivery=$val['kode_delivery'];
					$total_cogs=($total_cogs+$val['nilai_cogs']);
				}
                $ArrHeader = array(
                    'status' => 1,
					'total_cogs' => $total_cogs
                );
				$this->db->trans_start();
                $this->db->where('id', $id);
				$this->db->update('delivery_cogs', $ArrHeader);
				$this->db->query("update delivery_product set st_cogs='1' where kode_delivery ='".$kode_delivery."'");
				$this->db->query("update delivery_product_detail set sts_invoice='1' where kode_delivery ='".$kode_delivery."'");

				$kodejurnal='JV062';
				$datajurnal = $this->db->query("select * from delivery_cogs where id='".$id."'" )->row();
				$no_reff=$datajurnal->no_do;
				$tgl_voucher = $datajurnal->tanggal;
				$totalall = $datajurnal->total_cogs;
				$keterangandetail=$datajurnal->keterangan;
				$jenisjurnal = 'customer-cogs';
				$Keterangan_INV="Customer - COGS ".$no_reff;
				$no_request = $id;
				$nilaibayar	= 0;
				$totalbayar	= 0;
				$masterjurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				$det_Jurnaltes = [];
				foreach($masterjurnal AS $record){
					$posisi = $record->posisi;
					$nokir  = $record->no_perkiraan;
					$param  = 'id';
					$value_param  = $id;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangandetail,
						  'no_reff'       => $no_reff,
						  'debet'         => $totalall,
						  'kredit'        => 0,
						  'jenis_jurnal'  => $jenisjurnal,
						  'no_request'    => $no_request,
						  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangandetail,
						  'no_reff'       => $no_reff,
						  'debet'         => 0,
						  'kredit'        => $totalall,
						  'jenis_jurnal'  => $jenisjurnal,
						  'no_request'    => $no_request,
						  'stspos'		  =>1
						 );
					}
				}
				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
				$Bln	= substr($tgl_voucher,5,2);
				$Thn	= substr($tgl_voucher,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalall, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangandetail, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $no_reff, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
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
				$this->db->trans_complete();
            }
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Kembali	= array(
					'pesan'		=> ' data failed. Please try again later ...',
					'status'	=> 0
				);
			} else{
				$this->db->trans_commit();
				$Arr_Kembali	= array(
					'pesan'		=> $TandaI.' data success.',
					'status'	=> 1
				);
				history($TandaI.' delivery_cogs '.$id);
			}
			echo json_encode($Arr_Kembali);
	}
	public function hapus_data(){
		$id = $this->uri->segment(3);
		$data_session	= $this->session->userdata;
		$this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->delete('delivery_cogs');
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Delete data failed. Please try again later ...',
				'status'	=> 0
			);
		} else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Delete data success. Thanks ...',
				'status'	=> 1
			);
			history('Delete Delivery COGS Data : '.$id);
		}
		echo json_encode($Arr_Data);
	}
}
