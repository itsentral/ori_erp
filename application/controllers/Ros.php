<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ros extends CI_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->model('master_model');
		$this->load->model('ros_model');
		$this->load->model('purchase_order_model');
		$this->load->model('All_model');
		$this->load->model('Jurnal_model');
		$this->load->database();
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }

	//==========================================================================================================================
	//===================================================REPORT OF SHIPMENT=====================================================
	//==========================================================================================================================

	public function index(){
		$this->ros_model->index_ros();
	}

	public function server_side_ros(){
		$this->ros_model->get_data_json_ros();
	}

	public function detail_ros($id_ros,$tipetrans){
		$this->ros_model->detail_ros($id_ros,'view',$tipetrans); 
	}

	public function add_ros($no_po="",$tipetrans){
		$this->ros_model->add_ros($no_po,$tipetrans);
	}

	public function edit_ros($id_ros,$tipetrans){
		$this->ros_model->detail_ros($id_ros,'edit',$tipetrans);
	}

    protected function _getIdRos()
    {
        $m = [
            '01' => 'A', '02' => 'B', '03' => 'C', '04' => 'D', '05' => 'E', '06' => 'F',
            '07' => 'G', '08' => 'H', '09' => 'I', '10' => 'J', '11' => 'K', '12' => 'L'
        ];
        $y = date('y');
        $sql    = "SELECT MAX(RIGHT(id,4)) as id from report_of_shipment where substr(id,3,2) = '" . $y . "'";
        $getId  = $this->db->query($sql)->row();

        if ($getId->id == '') {
            $count = '1';
        } else {
            $count = $getId->id + 1;
        }
        $idRos = "RS" . $y . "-" . $m[date('m')] . date('d') . "-" . str_pad($count, 4, "0", STR_PAD_LEFT);
        return $idRos;
    }

    protected function _getNoRos() {
        $m = [
            '01' => 'A', '02' => 'B', '03' => 'C', '04' => 'D', '05' => 'E', '06' => 'F',
            '07' => 'G', '08' => 'H', '09' => 'I', '10' => 'J', '11' => 'K', '12' => 'L'
        ];
        $mnt = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV', '05' => 'V', '06' => 'VI',
            '07' => 'VII', '08' => 'VIII', '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII'
        ];
        $ym     = date('ym');
        $sql    = "SELECT MAX(right(no_ros,4)) as no_ros from report_of_shipment where left(no_ros,4) = '" . $ym . "'";
        $getNo  = $this->db->query($sql)->row();
        if ($getNo->no_ros == '') {
            $count = 1;
        } else {
            $count = $getNo->no_ros + 1;
        }
        $noRos = $ym . str_pad($count, 4, "0", STR_PAD_LEFT);
        return $noRos;
    }

	public function save_ros(){
        $data = $this->input->post();
        $idRos = ($data['id_ros']!='') ? $data['id_ros'] : $this->_getIdRos();
        $noRos = ($data['no_ros']!='') ? $data['no_ros'] : $this->_getNoRos();
		$data_session			= $this->session->userdata;
        $this->db->trans_begin();

        if (!empty($data)) {
			$status_rg_check=(isset($data['status_rg_check'])?$data['status_rg_check']:'');
			if($status_rg_check=='') {
				$status_rg_check='OPEN';
			}
            $ArrRos = [
                'id'                    => $idRos,
                'no_ros'                => $noRos,
                'date'                  => ($data['date']),
                'id_po'              	=> $data['no_po'],
                'no_po'             	=> $data['no_po'],
                'id_supplier'           => $data['id_supplier'],
                'shipment'              => $data['shipment'],
                'deliv_date'            => ($data['deliv_date']),
                'awb_date'              => ($data['awb_date']),
                'awb_number'            => $data['awb_number'],
                'eta_date'              => ($data['eta_date']),
                'est_day'               => $data['est_day'],
                'fc_cost'               => $data['fc_cost'],
                'fc_cost_m'             => 0,
                'ppn_fc_cost'           => $data['ppn_fc_cost'],
                'grand_total_fc_cost'   => $data['grand_total_fc_cost'],
                'total_qty_ship'        => 0,
                'gtotal_price'          => $data['gtotal_price'],
                'gtotal_price_us'		=> $data['gtotal_price_us'],
                'total_fc_cost'         => $data['total_fc_cost'],
                'total_price_aft_fc'    => $data['total_price_aft_fc'],
                'freight_curs'			=> $data['freight_curs'],
                'status_rg_check'		=> $status_rg_check,
                'created_by'            => $data_session['ORI_User']['username'],
                'created_on'            => date('Y-m-d H:i:s'),
                'type_material'         => $data['type_material'],
                'tax'             		=> $data['tax'],
                'lokasi'             	=> $data['lokasi'],
            ];

            $ArrItemFc = [];
			$ArrFccost=[];
            if (isset($data['dtlFccost'])) {
                $no = 0;
                foreach ($data['dtlFccost'] as $fc => $cost) {
                    $no++;
                    $ArrFccost[] = [
                        'id'                    => $idRos . "-" . str_pad($no, 3, "0", STR_PAD_LEFT),
                        'id_ros'                => $idRos,
                        'farward_name'          => $cost['fwd_name'],
                        'weight'                => $cost['weight'],
                        'volume'                => $cost['volume'],
                        'cost'                  => $cost['cost'],
                        'ppn'                   => $cost['ppn'],
                        'grand_total'           => $cost['grand_total'],
                        'remark'                => $cost['remark'],
                        'created_by'            => $data_session['ORI_User']['username'],
                        'created_on'            => date('Y-m-d H:i:s'),
                    ];

                    if (isset($cost['itemFc'])) {
                        $count = 0;
                        $idFw = $idRos . "-" . str_pad($no, 3, "0", STR_PAD_LEFT);
                        foreach ($cost['itemFc'] as $itm => $item) {
                            $count++;
                            $idFwItem = $idFw . "-" . $count;
                            $ArrItemFc[$fc . $itm] = [
                                'id'                => $idFwItem,
                                'id_ros'            => $idRos,
                                'id_forwarder'      => $idFw,
                                'item'              => $item['item'],
                                'curency'           => $item['curency'],
                                'cost_curency'      => $item['cost_curency'],
                                'kurs'              => $item['kurs'],
                                'cost'              => $item['cost'],
                                'ppn'               => $item['ppn'],
                                'total'             => $item['total'],
								'created_by'		=> $data_session['ORI_User']['username'],
								'created_on'		=> date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                }
            }
            if (!empty($data['nomor'])) {
                $x = 0;
                foreach ($data['nomor'] as $keys => $val) {
                    $x++;
                    $ArrProduct[] = [
                        'id_ros'                => $idRos,
                        'no_po'              	=> $data['no_po'],
                        'id_material'           => $data['id_material'][$keys],
                        'nm_material'           => $data['nm_material'][$keys],
                        'curency'            	=> $data['curency'][$keys],
                        'price_ref_sup'         => $data['price_ref_sup'][$keys],
                        'price'               	=> $data['price'][$keys],
                        'qty_po'                => $data['qty_po'][$keys],
                        'qty_ship'              => $data['qty_ship'][$keys],
                        'fc_cost_unit'          => $data['fc_cost_unit'][$keys],
                        'price_aft_fc'          => $data['price_aft_fc'][$keys],
                        'idpo'             		=> $data['idpo'][$keys],
						'bm'                    => $data['bm'][$keys],
                        'tipe'                  => "",
                        'created_by'            => $data_session['ORI_User']['username'],
                        'created_on'            => date('Y-m-d H:i:s'),
                    ];
                }
            }
			$this->db->delete('report_of_shipment_forward', ['id_ros' => $idRos]);
			$this->db->delete('report_of_shipment_product', ['id_ros' => $idRos]);
			$this->db->delete('report_of_shipment_forward_details', ['id_ros' => $idRos]);
            //header DATA
            $cek = $this->db->get_where('report_of_shipment', ['id' => $idRos])->num_rows();
            if ($cek > 0) {
				if($data['lokasi']=='import'){
					if($status_rg_check=='DONE'){
						$cekstatus=$this->db->get_where('report_of_shipment', ['id' => $idRos])->row();
						if($cekstatus->status_rg_check=='OPEN'){
							$this->jurnal_ros($noRos,$data['id_supplier'],$data['gtotal_price'],$data['no_po']);
						}
					}
				}
                $this->db->where('id', $idRos)->update('report_of_shipment', $ArrRos);
            } else {
                $this->db->insert('report_of_shipment', $ArrRos);
				if($data['lokasi']=='import'){
					if($status_rg_check=='DONE'){
						$this->jurnal_ros($noRos,$data['id_supplier'],$data['gtotal_price'],$data['no_po']);
					}
				}
            }
            if (!empty($ArrProduct)) {
                foreach ($ArrProduct as $prditem) {
					$this->db->insert('report_of_shipment_product', $prditem);
                }
            } else {
                $this->db->delete('report_of_shipment_product', ['id_ros' => $idRos]);
            }
            if (!empty($ArrFccost)) {
                foreach ($ArrFccost as $fcitem) {
					$this->db->insert('report_of_shipment_forward', $fcitem);
                }
            } else {
                $this->db->delete('report_of_shipment_forward', ['id_ros' => $idRos]);
            }

            if (!empty($ArrItemFc)) {
                foreach ($ArrItemFc as $itemFc) {
					$this->db->insert('report_of_shipment_forward_details', $itemFc);
                }
            } else {
                $this->db->delete('report_of_shipment_forward_details', ['id_ros' => $idRos]);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Arr_Kembali    = array(
                    'pesan'        => 'Failed. Please try again later ...',
                    'status'    => 0
                );
            } else {
                $this->db->trans_commit();
                $Arr_Kembali    = array(
                    'pesan'        => 'Success Save. Thanks ...',
                    'status'    => 1
                );
            }
        }
        echo json_encode($Arr_Kembali);
	}
	
	private function jurnal_ros($no_ros,$id_supplier,$gtotal_price,$no_po){
		$type_material=$this->input->post('type_material');
		$gtotal_price_us=$this->input->post('gtotal_price_us');
		$jenis_jurnal = 'JV040';
        $nomor_jurnal = $jenis_jurnal . $no_ros . rand(100, 999);
		$payment_date=date("Y-m-d");
		if($type_material=="1"){
			$table="tran_material_po_header";
			$data_po = $this->db->query("select * from tran_material_po_header where no_po='" . $no_po . "' ")->row();
		}else{
			$table="tran_po_header";
			$data_po = $this->db->query("select * from tran_po_header where no_po='" . $no_po . "' ")->row();
		}
        $det_Jurnaltes1 = array();

		$datajurnal1 = $this->db->query("select * from " . DBACC . ".master_oto_jurnal_detail where kode_master_jurnal='" . $jenis_jurnal . "' order by parameter_no")->result();
		$hutang=0;
		foreach ($datajurnal1 as $rec) {
			// BARANG INTRANSIT
			if ($rec->parameter_no == "1") {
				$det_Jurnaltes1[] = array(
					'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'ROS ' . $no_ros, 'no_request' => $no_ros, 'debet' => ($rec->posisi == 'K' ? 0 : ($gtotal_price)), 'kredit' => ($rec->posisi == 'D' ? 0 : ($gtotal_price)), 'no_reff' => $no_po, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $id_supplier
				);
			}
			// DP
			if ($rec->parameter_no == "2") {
				$uangmuka = $gtotal_price;
				if ($data_po->nilai_dp_kurs > 0) {
					if ($data_po->nilai_dp_kurs <= $gtotal_price) {
						$uangmuka = $data_po->nilai_dp_kurs;
						$hutang = ($gtotal_price - $uangmuka);
						$this->db->query("update ".$table." set proses_uang_muka='Y', nilai_dp=0,nilai_dp_kurs=0, sisa_dp=0 where no_po='" . $no_po . "'");
					} else {
						$uangmuka = ($uangmuka - $gtotal_price);
						$hutang=0;
						$this->db->query("update ".$table." set proses_uang_muka='Y', nilai_dp=(nilai_dp-" . $gtotal_price_us . "), sisa_dp=(sisa_dp-" . $gtotal_price_us . "),nilai_dp_kurs=(nilai_dp_kurs-".$gtotal_price.") where no_po='" . $no_po . "'");
					}
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'ROS ' . $no_ros, 'no_request' => $no_ros, 'debet' => ($rec->posisi == 'K' ? 0 : $uangmuka), 'kredit' => ($rec->posisi == 'D' ? 0 : $uangmuka), 'no_reff' => $no_po, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $id_supplier
					);
				}else{
					$hutang=$gtotal_price;
				}
			}
			// AP
			if ($rec->parameter_no == "3") {
				if ($hutang > 0) {
					$det_Jurnaltes1[] = array(
						'nomor' => $nomor_jurnal, 'tanggal' => $payment_date, 'tipe' => 'JV', 'no_perkiraan' => $rec->no_perkiraan, 'keterangan' => 'ROS ' . $no_ros, 'no_request' => $no_po, 'debet' => ($rec->posisi == 'K' ? 0 : $hutang), 'kredit' => ($rec->posisi == 'D' ? 0 : $hutang), 'no_reff' => $no_po, 'jenis_jurnal' => $jenis_jurnal, 'nocust' => $id_supplier
					);
				}
			}
		}
		$this->db->insert_batch('jurnaltras', $det_Jurnaltes1);		
	}

	public function delete_ros($idros){
        $this->db->trans_begin();
		$this->db->where('id_ros', $idros);
		$this->db->delete('report_of_shipment_forward_details');
		$this->db->where('id_ros', $idros);
		$this->db->delete('report_of_shipment_forward');
		$this->db->where('id', $idros);
		$this->db->delete('report_of_shipment');
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali    = array(
				'pesan'        => 'Failed. Please try again later ...',
				'status'    => 0
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali    = array(
				'pesan'        => 'Success Delete. Thanks ...',
				'status'    => 1
			);
		}
		echo json_encode($Arr_Kembali);
	}

	public function update_ros($idros){
        $this->db->trans_begin();
		$ArrRos = [
                'status'	=> "APV",
            ];
		$this->db->where('id', $idros)->update('report_of_shipment', $ArrRos);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$Arr_Kembali    = array(
				'pesan'        => 'Failed. Please try again later ...',
				'status'    => 0
			);
		} else {
			$this->db->trans_commit();
			$Arr_Kembali    = array(
				'pesan'        => 'Success Update. Thanks ...',
				'status'    => 1
			);
		}
		echo json_encode($Arr_Kembali);
	}

	public function index_jurnal_incoming(){
		$this->ros_model->index_jurnal_incoming();
	}

	public function server_side_jurnal_incoming(){
		$this->ros_model->get_data_json_jurnal_incoming();
	}

	public function view_jurnal($id){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data = $this->db->query("select * from jurnaltras where nomor='".$id."' order by kredit,debet,no_perkiraan")->result();
        $datacoa	= $this->All_model->GetCoaCombo();
		$datapayterm  = $this->db->query("select data2,name from list_help where group_by='top' order by urut")->result();
		$payterm=array();
		foreach($datapayterm as $key=>$val){
			$payterm[$val->data2]=$val->name;
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'View Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'payterm'		=> $payterm,
			'datacoa'		=> $datacoa,
			'data'			=> $data,
			'status'		=> "view",
		);
		history('View Jurnal');
		$this->load->view('Ros/form_jurnal',$data);
	}
	public function edit_jurnal($id){

		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data = $this->db->query("select * from jurnaltras where nomor='".$id."' order by kredit,debet,no_perkiraan")->result();
        $datacoa	= $this->All_model->GetCoaCombo();
		$datapayterm  = $this->db->query("select data2,name from list_help where group_by='top' order by urut")->result();
		$payterm=array();
		foreach($datapayterm as $key=>$val){
			$payterm[$val->data2]=$val->name;
		}
		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
		$data = array(
			'title'			=> 'Edit Jurnal',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
			'payterm'		=> $payterm,
			'datacoa'		=> $datacoa,
			'data'			=> $data,
		);
		history('Edit Jurnal');
		$this->load->view('Ros/form_jurnal',$data);
	}
	public function jurnal_save(){
		$id = $this->input->post("id");
		$no_perkiraan = $this->input->post("no_perkiraan");
		$keterangan = $this->input->post("keterangan");
		$debet = $this->input->post("debet");
		$kredit = $this->input->post("kredit");

		$tanggal		= $this->input->post('tanggal');
		$tipe			= $this->input->post('tipe');
		$no_reff        = $this->input->post('no_reff');
		$no_request		= $this->input->post('no_request');
		$jenis_jurnal	= $this->input->post('jenis_jurnal');
		$nocust         = $this->input->post('nocust');
		$total			= 0;
		$total_po		= $this->input->post('total_po');
		$data_vendor 	= $this->db->query("select * from supplier where id_supplier='".$nocust."'")->row();
		$nama_vendor 	= $data_vendor->nm_supplier;
		$Bln 			= substr($tanggal,5,2);
		$Thn 			= substr($tanggal,0,4);
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);

        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		$unbill_coa='';
		$unbill_nilai='0';
		$matauang='';
		if($jenis_jurnal=='JV032'){
			$no_reff = $no_request;
			$row_po = $this->db->query("select * from tran_material_po_header where no_po='".$no_reff."'")->row();
			if(!empty($row_po)) {
				if($row_po->mata_uang=='IDR') $matauang='IDR';
			}
		}

		$this->db->trans_begin();

        for($i=0;$i < count($id);$i++){
			$dataheader =  array(
				'stspos' => "1",
				'no_perkiraan' => $no_perkiraan[$i],
				'keterangan' => $keterangan[$i],
				'debet' => $debet[$i],
				'kredit' => $kredit[$i]
			);
			$total=($total+$debet[$i]);
			$this->All_model->DataUpdate('jurnaltras', $dataheader, array('id' => $id[$i]));

			if($debet[$i]==0 && $kredit[$i]==0){
			}else{
				$datadetail = array(
					'tipe'        	=> $tipe,
					'nomor'       	=> $Nomor_JV,
					'tanggal'     	=> $tanggal,
					'no_reff'     	=> $no_reff,
					'no_perkiraan'	=> $no_perkiraan[$i],
					'keterangan' 	=> $keterangan[$i],
					'debet' 		=> $debet[$i],
					'kredit' 		=> $kredit[$i]
					);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
				$unbill_coa=$no_perkiraan[$i];
				$unbill_nilai=$debet[$i];

			}
		}

		if($jenis_jurnal=='JV040') {
			$keterangan		= 'ROS';
		}else{
			$keterangan		= 'Incoming Material';
		}
		$dataJVhead = array(
			'nomor' 	    	=> $Nomor_JV,
			'tgl'	         	=> $tanggal,
			'jml'	            => $total,
			'bulan'	            => $Bln,
			'tahun'	            => $Thn,
			'kdcab'				=> '101',
			'jenis'			    => 'JV',
			'keterangan'		=> $keterangan,
			'user_id'			=> $session['username'],
			'ho_valid'			=> '',
		);
		if($tipe=='JV') {
			$this->db->insert(DBACC . '.javh', $dataJVhead);
			$Qry_Update_Cabang_acc = "UPDATE " . DBACC . ".pastibisa_tb_cabang SET nomorJC=nomorJC + 1 WHERE nocab='101'";
			$this->db->query($Qry_Update_Cabang_acc);
		}
		if($jenis_jurnal=='JV032' && $matauang=='IDR' ){
			$datahutang = array(
				'tipe'       	 => 'JV',
				'nomor'       	 => $Nomor_JV,
				'tanggal'        => $tanggal,
				'no_perkiraan'    => $unbill_coa,
				'keterangan'      => $keterangan,
				'no_reff'     	  => $no_reff,
				'kredit'      	  => $unbill_nilai,
				'debet'          => 0,
				'id_supplier'     => $nocust,
				'nama_supplier'   => $nama_vendor,
				'no_request'      => $no_request,
			);
			$this->db->insert('tr_kartu_hutang',$datahutang);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			$result         = TRUE;
			history('Save Jurnal');
		} else {
			$this->db->trans_rollback();
			$result = FALSE;
		}
		$param = array(
			'save' => $result
		);
		echo json_encode($param);
	}

	public function index_jurnal_ros(){
		$this->ros_model->index_jurnal_ros();
	}

	public function server_side_jurnal_ros(){
		$this->ros_model->get_data_json_jurnal_ros();
	}
}
