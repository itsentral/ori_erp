<?php
class Bq_estimasi_model extends CI_Model {

	public function __construct() {
		parent::__construct();
    }

    public function get_add(){
		$id 			= $this->uri->segment(3);
		
		$jenis_barang	    = $this->db->query("SELECT * FROM con_nonmat_new WHERE `deleted`='N' AND category_awal='7' ORDER BY material_name ASC ")->result_array();
		
		$satuan				= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC")->result_array();
		
		$d_Header = "";
		$d_Header .= "<tr class='header_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm getSpec'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($jenis_barang AS $val => $valx){
				  $d_Header .= "<option value='".$valx['code_group']."'>".strtoupper($valx['material_name'].' - '.$valx['spec'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail[".$id."][satuan]' class='chosen_select form-control input-sm'>";
				$d_Header .= "<option value='0'>Select Satuan</option>";
				foreach($satuan AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id_satuan']."'>".strtoupper($valx['nama_satuan'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
    }
    
    public function get_add2(){
		$id 			= $this->uri->segment(3);

        $raw_material	    = $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND `delete`='N' ORDER BY nm_material ASC ")->result_array();
		
		$d_Header = "";
		$d_Header .= "<tr class='header2_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_material[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm getSpec'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($raw_material AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id_material']."'>".strtoupper($valx['nm_material'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_material[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_material[".$id."][satuan]' class='chosen_select form-control input-sm'>";
					$d_Header .= "<option value='1'>KG</option>";
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_material[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add2_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart2' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
	}
    
	public function get_add3(){
		$id 			= $this->uri->segment(3);
		
		// $jenis_barang	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='1' ORDER BY nama ASC ")->result_array();
		// $satuan				= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC")->result_array();
		// $raw_material		= $this->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND `delete`='N' ORDER BY nm_material ASC ")->result_array();
		
		$jenis_barang	    = $this->db->order_by('nama','ASC')->get_where('accessories', array('deleted'=>'N','category'=>'1'))->result_array();
		$satuan				= $this->db->order_by('nama_satuan','ASC')->get_where('raw_pieces', array('delete'=>'N'))->result_array();
		// $raw_material		= $this->db->order_by('nm_material','ASC')->get_where('raw_materials', array('flag_active'=>'Y','delete'=>'N'))->result_array();


		$d_Header = "";
		$d_Header .= "<tr class='header3_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_baut[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm get_detail_baut'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($jenis_barang AS $val => $valx){
					$radx = (!empty($valx['radius']) AND $valx['radius'] > 0)?'x '.floatval($valx['radius']).' R':'';
					$tipe = ', '.strtoupper($valx['standart']);
					$d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nama']).' '.$valx['spesifikasi']."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_baut[".$id."][material]' id='bt_material_".$id."' class='form-control input-md text-left' placeholder='Material' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_baut[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_baut[".$id."][satuan]' class='chosen_select form-control input-sm'>";
				$d_Header .= "<option value='0'>Select Satuan</option>";
				foreach($satuan AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id_satuan']."'>".strtoupper($valx['nama_satuan'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_baut[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add3_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart3' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
    }

	public function get_add4(){
		$id 			= $this->uri->segment(3);
		
		$jenis_barang	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='2' ORDER BY nama ASC ")->result_array();
		
		$d_Header = "";
		$d_Header .= "<tr class='header4_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_plate[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm get_detail_plate'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($jenis_barang AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nama'].' - '.$valx['material']).' x '.floatval($valx['thickness'])." T</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][ukuran_standart]' id='pl_ukuran_standart_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][standart]' id='pl_standart_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][lebar]' id='pl_lebar_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][panjang]' id='pl_panjang_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0'>";
				$d_Header .= "<input type='hidden' name='detail_plate[".$id."][thickness]' id='pl_thickness_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
				$d_Header .= "<input type='hidden' name='detail_plate[".$id."][density]' id='pl_density_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
								
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][qty]' id='pl_qty_".$id."' class='form-control input-md text-center maskM get_berat' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][berat]' id='pl_berat_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][sheet]' id='pl_sheet_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_plate[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add4_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart4' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
    }
	
	public function get_add4g(){
		$id 			= $this->uri->segment(3);
		
		$jenis_barang	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='3' ORDER BY nama ASC ")->result_array();
		$satuan				= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC")->result_array();
		$d_Header = "";
		$d_Header .= "<tr class='header4g_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_gasket[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm get_detail_gasket'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($jenis_barang AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nama'].' '.$valx['dimensi'].' - '.$valx['material']).' x '.$valx['spesifikasi']."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][ukuran_standart]' id='gs_ukuran_standart_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][dimensi]' id='gs_dimensi_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][lebar]' id='gs_lebar_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][panjang]' id='gs_panjang_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][qty]' id='gs_qty_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][sheet]' id='gs_sheet_".$id."' class='form-control input-md text-center maskM' placeholder='0'>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_gasket[".$id."][satuan]' id='gs_satuan_".$id."' class='chosen_select form-control input-sm'>";
				// $d_Header .= "<option value='0'>Select Satuan</option>";
				// foreach($satuan AS $val => $valx){
				  // $d_Header .= "<option value='".$valx['id_satuan']."'>".strtoupper($valx['nama_satuan'])."</option>";
				// }
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_gasket[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add4g_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart4g' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
    }

	public function get_add5(){
		$id 			= $this->uri->segment(3);
		
		$jenis_barang	    = $this->db->query("SELECT * FROM accessories WHERE `deleted`='N' AND category='4' ORDER BY nama ASC ")->result_array();
		
		$satuan				= $this->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY nama_satuan ASC")->result_array();
		
		$d_Header = "";
		$d_Header .= "<tr class='header5_".$id."'>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_lainnya[".$id."][id_material]' data-no='".$id."' class='chosen_select form-control input-sm get_detail_lainnya'>";
				$d_Header .= "<option value='0'>Select Material</option>";
				foreach($jenis_barang AS $val => $valx){
				  $d_Header .= "<option value='".$valx['id']."'>".strtoupper($valx['nama'].', '.$valx['material'].' - '.$valx['dimensi'].' - '.$valx['spesifikasi'])."</option>";
				}
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_lainnya[".$id."][ukuran_standart]' id='ln_ukuran_standart_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_lainnya[".$id."][standart]' id='ln_standart_".$id."' class='form-control input-md text-left' readonly>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_lainnya[".$id."][qty]' class='form-control input-md text-center maskM' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<select name='detail_lainnya[".$id."][satuan]' id='ln_satuan_".$id."' class='chosen_select form-control input-sm'>";
				// $d_Header .= "<option value='0'>Select Satuan</option>";
				// foreach($satuan AS $val => $valx){
				  // $d_Header .= "<option value='".$valx['id_satuan']."'>".strtoupper($valx['nama_satuan'])."</option>";
				// }
				$d_Header .= "</select>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='left'>";
				$d_Header .= "<input name='detail_lainnya[".$id."][note]' class='form-control input-md text-left' value=''>";
			$d_Header .= "</td>";
			$d_Header .= "<td align='center'>";
			$d_Header .= "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
			$d_Header .= "</td>";
		$d_Header .= "</tr>";

		//add part
		$d_Header .= "<tr id='add5_".$id."'>";
			$d_Header .= "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-sm btn-success addPart5' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
			$d_Header .= "<td align='center'></td>";
		$d_Header .= "</tr>";

		 echo json_encode(array(
				'header'			=> $d_Header,
		 ));
    }

	public function get_detail_lainnya(){
		$id 			= $this->uri->segment(3);
		$get_detail = $this->db->select('spesifikasi, standart, ukuran_standart, satuan')->get_where('accessories', array('id'=>$id))->result();
		
		$get_sat = $this->db->select('kode_satuan')->get_where('raw_pieces', array('id_satuan'=>$get_detail[0]->satuan))->result();		

		 echo json_encode(array(
				'spesifikasi'	=> strtoupper($get_detail[0]->spesifikasi),
				'satuan'	=> $get_detail[0]->satuan,
				'satuan_view'	=> $get_sat[0]->kode_satuan,
				'ukuran_standart'	=> strtoupper($get_detail[0]->ukuran_standart),
				'standart'		=> strtoupper($get_detail[0]->standart)
		 ));
    }

	public function get_detail_plate(){
		$id 			= $this->uri->segment(3);
		$get_detail = $this->db->select('ukuran_standart, standart, thickness, density, satuan')->get_where('accessories', array('id'=>$id))->result();
							

		 echo json_encode(array(
				'satuan'	=> strtoupper($get_detail[0]->satuan),
				'thickness'	=> strtoupper($get_detail[0]->thickness),
				'density'	=> strtoupper($get_detail[0]->density),
				'ukuran_standart'	=> strtoupper($get_detail[0]->ukuran_standart),
				'standart'			=> strtoupper($get_detail[0]->standart)
		 ));
    }
	
	public function get_detail_gasket(){
		$id 			= $this->uri->segment(3);
		$get_detail = $this->db->select('ukuran_standart, standart, thickness, density, satuan, dimensi')->get_where('accessories', array('id'=>$id))->result();
		$get_sat = $this->db->select('kode_satuan')->get_where('raw_pieces', array('id_satuan'=>$get_detail[0]->satuan))->result();						

		 echo json_encode(array(
				'thickness'	=> strtoupper($get_detail[0]->thickness),
				'satuan'	=> $get_detail[0]->satuan,
				'satuan_view'	=> $get_sat[0]->kode_satuan,
				'density'	=> strtoupper($get_detail[0]->density),
				'dimensi'	=> strtoupper($get_detail[0]->dimensi),
				'ukuran_standart'	=> strtoupper($get_detail[0]->ukuran_standart),
				'standart'			=> strtoupper($get_detail[0]->standart)
		 ));
    }
	
	public function get_detail_baut(){
		$id 			= $this->uri->segment(3);
		$get_detail = $this->db->select('material')->get_where('accessories', array('id'=>$id))->result();
							

		 echo json_encode(array(
				'material'	=> strtoupper($get_detail[0]->material)
		 ));
    }
	
	public function save_rutin_material(){
		$Arr_Kembali	= array();
		$data			= $this->input->post();
		$id_bq			= "BQ-".$data['no_ipp'];
		$pembeda		= $data['pembeda'];
		
		$detail_acc		= array();
		if(!empty($data['detail'])){
			$detailAcc		= $data['detail'];
			//DETAIL
			foreach($detailAcc AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$qty_m = str_replace(',','',$valx['qty']);
					$price = get_price_rutin($valx['id_material'], $valx['satuan']);
					$detail_acc[$val]['id_bq']			= $id_bq;
					$detail_acc[$val]['category']		= 'acc';
					$detail_acc[$val]['id_material']	= $valx['id_material'];
					$detail_acc[$val]['qty']			= $qty_m;
					$detail_acc[$val]['satuan']			= $valx['satuan'];
					$detail_acc[$val]['note']			= strtolower($valx['note']);
					$detail_acc[$val]['unit_price']		= $price;
					$detail_acc[$val]['total_price']	= $qty_m * $price;
					$detail_acc[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_acc[$val]['updated_date']	= date('Y-m-d H:i:s');
				}
			}
		}
		$detail_mat		= array();
		if(!empty($data['detail_material'])){
			$detailMat		= $data['detail_material'];
			//DETAIL MATERIAL 
			foreach($detailMat AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$qty_m = str_replace(',','',$valx['qty']);
					$price = get_price_ref($valx['id_material']);
					$detail_mat[$val]['id_bq']			= $id_bq;
					$detail_mat[$val]['category']		= 'mat';
					$detail_mat[$val]['id_material']	= $valx['id_material'];
					$detail_mat[$val]['note']			= strtolower($valx['note']);
					$detail_mat[$val]['qty']			= $qty_m;
					$detail_mat[$val]['satuan']			= '1';
					$detail_mat[$val]['unit_price']		= $price;
					$detail_mat[$val]['total_price']	= $qty_m * $price;
					$detail_mat[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_mat[$val]['updated_date']	= date('Y-m-d H:i:s');
					
				}
			}
		}
		
		$detail_baut		= array();
		if(!empty($data['detail_baut'])){
			$detailBaut		= $data['detail_baut'];
			foreach($detailBaut AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$getRef 	= get_price_aksesoris($valx['id_material']);
					$qty_m 		= str_replace(',','',$valx['qty']);
					$price 		= $getRef['price'];
					$detail_baut[$val]['id_bq']			= $id_bq;
					$detail_baut[$val]['category']		= 'baut';
					$detail_baut[$val]['id_material']	= $valx['id_material'];
					// $detail_baut[$val]['id_material2']	= $valx['id_material2'];
					$detail_baut[$val]['qty']			= $qty_m;
					$detail_baut[$val]['satuan']		= $valx['satuan'];
					$detail_baut[$val]['unit_price']	= $price;
					$detail_baut[$val]['total_price']	= $qty_m * $price;
					$detail_baut[$val]['note']			= strtolower($valx['note']);
					$detail_baut[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_baut[$val]['updated_date']	= date('Y-m-d H:i:s');
					$detail_baut[$val]['expired_date']	= $getRef['expired'];
					
				}
			}
		}

		$detail_plate		= array();
		if(!empty($data['detail_plate'])){
			$detailPlate		= $data['detail_plate'];
			foreach($detailPlate AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$get_detail = $this->db->select('harga, satuan')->get_where('accessories', array('id'=>$valx['id_material']))->result();
					$getRef 	= get_price_aksesoris($valx['id_material']);
					$price 		= $getRef['price'];
					$qty_m 		= str_replace(',','',$valx['qty']);
					$berat_m 	= str_replace(',','',$valx['berat']);
					$total_price= $qty_m * $price;
					if(!empty($berat_m) AND $berat_m > 0){
						$total_price= $berat_m * $price;
					}

					$detail_plate[$val]['id_bq']		= $id_bq;
					$detail_plate[$val]['category']		= 'plate';
					$detail_plate[$val]['id_material']	= $valx['id_material'];
					$detail_plate[$val]['qty']			= $qty_m;
					$detail_plate[$val]['satuan']		= $get_detail[0]->satuan;
					$detail_plate[$val]['lebar']		= str_replace(',','',$valx['lebar']);
					$detail_plate[$val]['panjang']		= str_replace(',','',$valx['panjang']);
					$detail_plate[$val]['berat']		= $berat_m;
					$detail_plate[$val]['unit_price']	= $price;
					$detail_plate[$val]['total_price']	= $total_price;
					$detail_plate[$val]['note']			= strtolower($valx['note']);
					$detail_plate[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_plate[$val]['updated_date']	= date('Y-m-d H:i:s');
					$detail_plate[$val]['expired_date']	= $getRef['expired'];
					
				}
			}
		}
		
		$detail_gasket		= array();
		if(!empty($data['detail_gasket'])){
			$detailGasket		= $data['detail_gasket'];
			foreach($detailGasket AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$getRef 	= get_price_aksesoris($valx['id_material']);
					$price 		= $getRef['price'];
					$qty_m 		= str_replace(',','',$valx['qty']);
					$detail_gasket[$val]['id_bq']		= $id_bq;
					$detail_gasket[$val]['category']		= 'gasket';
					$detail_gasket[$val]['id_material']	= $valx['id_material'];
					$detail_gasket[$val]['qty']			= $qty_m;
					$detail_gasket[$val]['satuan']		= $valx['satuan'];
					$detail_gasket[$val]['lebar']		= str_replace(',','',$valx['lebar']);
					$detail_gasket[$val]['panjang']		= str_replace(',','',$valx['panjang']);
					$detail_gasket[$val]['unit_price']	= $price;
					$detail_gasket[$val]['total_price']	= $qty_m * $price;
					$detail_gasket[$val]['note']			= strtolower($valx['note']);
					$detail_gasket[$val]['updated_by']	= $this->session->userdata['ORI_User']['username'];
					$detail_gasket[$val]['updated_date']	= date('Y-m-d H:i:s');
					$detail_gasket[$val]['expired_date']	= $getRef['expired'];
					
				}
			}
		}

		$detail_lainnya		= array();
		if(!empty($data['detail_lainnya'])){
			$detailLainnya		= $data['detail_lainnya'];
			foreach($detailLainnya AS $val => $valx){
				if($valx["id_material"] <> '0'){
					$getRef 	= get_price_aksesoris($valx['id_material']);
					$qty_m 		= str_replace(',','',$valx['qty']);
					$price 		= $getRef['price'];
					$detail_lainnya[$val]['id_bq']			= $id_bq;
					$detail_lainnya[$val]['category']		= 'lainnya';
					$detail_lainnya[$val]['id_material']	= $valx['id_material'];
					$detail_lainnya[$val]['qty']			= $qty_m;
					$detail_lainnya[$val]['satuan']			= $valx['satuan'];
					$detail_lainnya[$val]['unit_price']		= $price;
					$detail_lainnya[$val]['total_price']	= $qty_m * $price;
					$detail_lainnya[$val]['note']			= strtolower($valx['note']);
					$detail_lainnya[$val]['updated_by']		= $this->session->userdata['ORI_User']['username'];
					$detail_lainnya[$val]['updated_date']	= date('Y-m-d H:i:s');
					$detail_lainnya[$val]['expired_date']	= $getRef['expired'];
					
				}
			}
		}
		
		$this->db->trans_start();
			$this->db->delete('bq_acc_and_mat', array('id_bq' => $id_bq));

			if(!empty($detail_acc)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_acc);
			}
			if(!empty($detail_mat)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_mat);
			}
			if(!empty($detail_baut)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_baut);
			}
			if(!empty($detail_plate)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_plate);
			}
			if(!empty($detail_gasket)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_gasket);
			}
			if(!empty($detail_lainnya)){
				$this->db->insert_batch('bq_acc_and_mat', $detail_lainnya);
			}

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Kembali	= array(
				'pesan'		=>'Save data failed. Please try again later ...',
				'status'	=> 0
			);
		}
		else{
			$this->db->trans_commit();
			$Arr_Kembali	= array(
				'id_bqx'	=> $id_bq,
				'pembeda'	=> $pembeda,
				'pesan'		=>'Savedata success. Thanks ...',
				'status'	=> 1
			);
			history('Save rutin & material : '.$id_bq);
		}

		echo json_encode($Arr_Kembali);
	}
}