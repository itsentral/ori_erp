<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Xtest extends CI_Controller {
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$sql	= "SELECT * FROM warehouse_adjustment where category='incoming material' order by id ";
		$result	= $this->db->query($sql)->result();
        if(!empty($result)){
		    foreach ($result as $keys) {
				echo $keys->kode_trans.'<br />';
				$kurs=1;
				if ($keys->no_ros!='') {
					$sql	= "SELECT * FROM report_of_shipment where no_ros='". $keys->no_ros ."' ";
					$ros	= $this->db->query($sql)->row();
					if(! empty($ros) ) $kurs = $ros->freight_curs;
				}
			}
		}
	}
	public function manual_generate(){
		$this->load->model('asset_model');
		$sql	= "SELECT * FROM asset where penyusutan='Y' and tgl_perolehan is not null order by id ";
		$result	= $this->db->query($sql)->result_array();
		$lopp2 	= 0;
        if(!empty($result)){
			$detailDataDash	= array();
		    foreach ($result as $data) {
				$jmlx   	= $data['depresiasi'] * 12;
				$date_now 	= date('Y-m-d');
				$date_now_real 	= date('2023-04-01');
				if(!empty($data['tgl_perolehan'])){
					$date_now 	= date('Y-m-d', strtotime($data['tgl_perolehan']));
				}
				$penyusutan		= $data['penyusutan'];
				$kode_assets	= $data['kd_asset'];
				$cost_center	= $data['cost_center'];
				$nmCategory		= $this->asset_model->getWhere('asset_category', 'id', $data['category']);
				for($x=1; $x <= $jmlx; $x++){
					$lopp2 += $x;
					$TglNow		= date('Y-m', strtotime($date_now_real));
					$Tanggal 	= date('Y-m', mktime(0,0,0,substr($date_now,5,2)+ $x,0,substr($date_now,0,4)));
					$flagx		= 'N';
					if($penyusutan == 'Y'){
						$flagx		= 'N';
						if($Tanggal < $TglNow) $flagx	= 'Y';
					}
					$detailDataDash[$lopp2]['kd_asset'] 	= $kode_assets;
					$detailDataDash[$lopp2]['nm_asset'] 	= $data['nm_asset'];
					$detailDataDash[$lopp2]['category'] 	= $data['category'];
					$detailDataDash[$lopp2]['category_pajak'] 	= $data['category_pajak'];
					$detailDataDash[$lopp2]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
					$detailDataDash[$lopp2]['bulan'] 		= substr($Tanggal, 5,2);
					$detailDataDash[$lopp2]['tahun'] 		= substr($Tanggal, 0,4);
					$detailDataDash[$lopp2]['lokasi_asset'] = $data['id_dept'];
					$detailDataDash[$lopp2]['cost_center'] 	= $data['cost_center'];
					$detailDataDash[$lopp2]['nilai_susut'] 	= $data['value'];
					$detailDataDash[$lopp2]['kdcab'] 		= 'ORI';
					$detailDataDash[$lopp2]['flag'] 		= $flagx;
				}
			}
			$this->db->trans_start();
//				$this->db->insert_batch('asset_generate', $detailDataDash);				
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Asset gagal disimpan ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Asset berhasil disimpan. Thanks ...',
					'status'	=> 1
				);
			}
			echo json_encode($Arr_Data);			
		}
	}
	public function update_acc() {
		$sql	= "SELECT * FROM accessories order by id ";
		$result	= $this->db->query($sql)->result();$i=0;
        if(!empty($result)){
		    foreach ($result as $keys) {
				$i++;
				//$this->db->query("update accessories set id_material='AC".sprintf('%05d', $keys->id)."' where id='".$keys->id."' ");
			}
			echo $i." updated";
		}
	}
	public function insert_acc_to_conmat() {
		$sql	= "SELECT * FROM accessories order by id ";
		$result	= $this->db->query($sql)->result();$i=0;
        if(!empty($result)){
			$this->db->trans_start();
		    foreach ($result as $keys) {
				$i++;
				$ArrInsert = array(
					'code_group'	=> $keys->id_material,
					'category_awal' => '9',
					'kode_excel'	=> $keys->id_material,
					'kode_item'		=> $keys->id_material,
					'material_name' => $keys->nama,
					'trade_name'	=> '',
					'spec'			=> $keys->spesifikasi,
					'brand'			=> '',
					'min_order'		=> '0',
					'lead_time'		=> '0',
					'konversi'		=> '0',
					'satuan'		=> $keys->satuan,
					'satuan_konversi' => '0',
					'no_rak'		=> '',
					'note'			=> '',
					'status'		=> '1',
					'created_by'	=> 'system',
					'created_date'	=> date('Y-m-d H:i:s')
				);
//				$this->db->insert('con_nonmat_new', $ArrInsert);
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				echo $i." error insert";
			}
			else{
				$this->db->trans_commit();
				echo $i." insert";
			}
		}
	}
	public function upd_date() {
		$sql	= "select * from stock_barang_jadi_per_day
where no_spk in
('20P.22.0070','20P.22.0066','20P.22.0065','20P.22.0063','20P.22.0346','20P.22.0344','20P.22.0340','20P.22.0337','20P.22.0336','20P.23.0007','20P.23.0006','20P.23.0058','20P.23.0082','20P.23.0083','20P.23.0093','20P.23.0092','20P.23.0091','20P.23.0090','20P.23.0089','20P.23.0088','20P.23.0087','20P.23.0086','20P.23.0085','20P.23.0100','20P.23.0099','20P.23.0104','20P.23.0103','20P.23.0102','20P.23.0125','20P.23.0124','20P.22.0195','20P.22.0366','20P.22.0365','20P.23.0079','20P.23.0077','20P.23.0114','20P.23.0112','20P.23.0111','20P.23.0119','20P.23.0117','20P.23.0074','20P.23.0126')
 and hist_date>='2023-07-01'
 and hist_date<'2023-07-02'";
		$result	= $this->db->query($sql)->result();
        if(!empty($result)){
		    foreach ($result as $keys) {
				$waktu=explode(" ",$keys->hist_date);
				$id=$keys->id;
				echo $waktu[0].' '.$waktu[1].'<br>';
				//$this->db->query("update jurnal_temp_detail set updated_date='2023-06-30 ".$waktu[1]."' where id='".$id."'");

//				echo $waktu[0].' '.$waktu[1].'<br>';
//				echo $waktu.'<br>';
//				$this->db->query("update stock_barang_jadi_per_day set hist_date='2023-06-30 ".$waktu[1]."' where id='".$id."'");
			}
		}
	}

	function update_wip(){
/*
direct_labour = bq_detail_header.man_hour * bq_detail_header.pe_direct_labour

indirect_labour = bq_detail_header.man_hour * bq_detail_header.pe_indirect_labour

SELECT last_cost FROM sentralsistem.estimasi_total;
consumable = bq_detail_header.pe_consumable * last_cost

machine = bq_detail_header.total_time * bq_detail_header.pe_machine

mould_mandrill = bq_detail_header.pe_mould_mandrill

SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi
foh_depresiasi = cost_foh/100

SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan
biaya_rutin_bulanan = cost_foh/100

SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable
foh_consumable = cost_foh/100

foh = machine + mould_mandrill + foh_depresiasi + biaya_rutin_bulanan + foh_consumable

*/
	}
	function form_upd_invoice_kurs(){
		echo '
		  <form method="post" action="'.base_url('xtest/update_invoice_kurs').'">
		  No Invoice : <input type="text" name="noinvoice" required /><br>
		  Kurs : <input type="text" name="kurs" required /><br>
		  <input type="submit" />
		  </form>
		';
	}
	function update_invoice_kurs(){
		$data		= $this->input->post();
		$noinvoice	= $data['noinvoice'];
		$kurs		= $data['kurs'];
		if($noinvoice!=''){
			$this->db->trans_start();
			$sqlheader="update tr_invoice_header set
			kurs_jual=".$kurs.",
			total_product_idr=(total_product*".$kurs."),
			total_gab_product_idr=(total_gab_product*".$kurs."),
			total_material_idr=(total_material*".$kurs."),
			total_bq_idr=(total_bq*".$kurs."),
			total_enginering_idr=(total_enginering*".$kurs."),
			total_packing_idr=(total_packing*".$kurs."),
			total_trucking_idr=(total_trucking*".$kurs."),
			total_dpp_rp=(total_dpp_usd*".$kurs."),
			total_diskon_idr=(total_diskon*".$kurs."),
			total_retensi_idr=(total_retensi*".$kurs."),
			total_ppn_idr=(total_ppn*".$kurs."),
			total_invoice_idr=(total_invoice*".$kurs."),
			total_um_idr=(total_um*".$kurs."),
			total_um_idr2=(total_um2*".$kurs."),
			total_retensi2_idr=(total_retensi2*".$kurs."),
			sisa_invoice_idr=(sisa_invoice*".$kurs.")
			where no_invoice='".$noinvoice."'";
			$this->db->query($sqlheader);

			$sqldetail="update tr_invoice_detail set
			harga_satuan_idr=(harga_satuan*".$kurs."),
			harga_total_idr=(harga_total*".$kurs.")
			where no_invoice='".$noinvoice."'";
			$this->db->query($sqldetail);
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				echo $noinvoice." error update. <a href='".base_url('xtest/form_upd_invoice_kurs')."'>Kembali</a>";
			}
			else{
				$this->db->trans_commit();
				echo $noinvoice." update. <a href='".base_url('xtest/form_upd_invoice_kurs')."'>Kembali</a>";
			}
		}
	}
	function update_outgoing_stock_costbook(){
		$i=0;
		$this->db->trans_start();
		$result=$this->db->query("select * from jurnal where category='outgoing stok' and cost_book=0 limit 5000")->result();
		if(!empty($result)){
		    foreach ($result as $keys) {
				$i++;
				$get_price_book = $this->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$keys->id_material))->row();
				$PRICE	= (!empty($get_price_book->price_book))?$get_price_book->price_book:0;
				echo $i.". ".$keys->id_material.":".$PRICE."<br/>";
				$this->db->query("update jurnal set cost_book=".$PRICE.", total_nilai=(qty*".$PRICE.") where category='outgoing stok' and cost_book=0 and id=".$keys->id);
			}
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			echo "Update Error";
		}
		else{
			$this->db->trans_commit();
			echo "Update Success";
		}
	}
	
    public function depresiasi_assets_manual($tgl_awal){
        //SAVE JURNAL TO TEMP
		$DATE_NOW	= $tgl_awal;
		// $DATE_NOW	= date('2022-09-01');
        $TANGGAL    = date('Y-m-t',strtotime($DATE_NOW));
		$TAHUN		= date('Y',strtotime($TANGGAL));
		$BULAN		= date('m',strtotime($TANGGAL));
        $username   = 'system';
	    $datetime   = date('Y-m-d H:i:s');

        //INSERT JURNAL TEMP
        $SQL = "SELECT
                    a.category AS category,
                    a.nm_category AS nm_category,
                    sum( a.nilai_susut ) AS sisa_nilai,
                    a.kdcab AS kdcab,
					b.id_coa,
					c.coa,
					c.coa_kredit
                FROM
                    asset_generate a
					LEFT JOIN asset b ON a.kd_asset=b.kd_asset
					LEFT JOIN asset_coa c ON b.id_coa=c.id
                WHERE
                    (a.bulan = '$BULAN' AND a.tahun = '$TAHUN' and a.flag='N') AND b.deleted_date IS NULL
                GROUP BY 
                    a.category, 
                    a.kdcab,
					b.id_coa,
					c.coa,
					c.coa_kredit";
        // echo $SQL;

        $ArrJurnal = $this->db->query($SQL)->result_array();
        
        $ArrDebit = array();
        $ArrKredit = array();
        $ArrJavh = array();
        $Loop = 0;
		if(!empty($ArrJurnal)){
			foreach($ArrJurnal AS $val => $valx){
				$Loop++;
				$ArrDebit[$Loop]['id_category'] 	= $valx['id_coa'];
				$ArrDebit[$Loop]['category'] 		= $valx['category'];
				$ArrDebit[$Loop]['tipe'] 			= "JV";
				$ArrDebit[$Loop]['nomor'] 			= $Loop;
				$ArrDebit[$Loop]['tanggal'] 		= $TANGGAL;
				$ArrDebit[$Loop]['no_perkiraan'] 	= $valx['coa'];
				$ArrDebit[$Loop]['keterangan'] 		= $valx['nm_category'];
				$ArrDebit[$Loop]['kdcab'] 			= $valx['kdcab'];
				$ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
				$ArrDebit[$Loop]['kredit'] 			= 0;
				$ArrDebit[$Loop]['created_by'] 		= $username;
				$ArrDebit[$Loop]['created_date'] 	= $datetime;

				$ArrKredit[$Loop]['id_category'] 	= $valx['id_coa'];
				$ArrKredit[$Loop]['category'] 		= $valx['category'];
				$ArrKredit[$Loop]['tipe'] 			= "JV";
				$ArrKredit[$Loop]['nomor'] 			= $Loop;
				$ArrKredit[$Loop]['tanggal'] 		= $TANGGAL;
				$ArrKredit[$Loop]['no_perkiraan'] 	= $valx['coa_kredit'];
				$ArrKredit[$Loop]['keterangan'] 	= $valx['nm_category'];
				$ArrKredit[$Loop]['kdcab'] 			= $valx['kdcab'];
				$ArrKredit[$Loop]['debet'] 			= 0;
				$ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];
				$ArrKredit[$Loop]['created_by'] 	= $username;
				$ArrKredit[$Loop]['created_date'] 	= $datetime;
			}
			$this->db->trans_start();
				$this->db->delete('asset_jurnal_temp',array('created_by'=>$username));
				$this->db->insert_batch('asset_jurnal_temp', $ArrDebit);
				$this->db->insert_batch('asset_jurnal_temp', $ArrKredit);

				$this->db->query("UPDATE asset_generate SET flag='Y' WHERE bulan='".$BULAN."' AND tahun='".$TAHUN."' ");
			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
				$this->saved_jurnal_erp();
			}
			echo "Update";
		}else{
			echo "Empty";
		}
	}	
   public function saved_jurnal_erp(){
		$username = 'system';
		$datetime = date('Y-m-d H:i:s');

		$get_jurnal = $this->db->get_where('asset_jurnal_temp',array('created_by'=>$username,'kredit'=>0))->result_array();
		$ArrJurnal = [];
		foreach ($get_jurnal as $key => $value) {
			$ArrJurnal[$key]['category'] 		= 'assets';
			$ArrJurnal[$key]['tanggal'] 		= $value['tanggal'];
			$ArrJurnal[$key]['id_detail'] 		= $value['id_category'];
			$ArrJurnal[$key]['product'] 		= $value['category'];
			$ArrJurnal[$key]['id_material'] 	= $value['no_perkiraan'];
			$ArrJurnal[$key]['nm_material'] 	= $value['keterangan'];
			$ArrJurnal[$key]['total_nilai'] 	= $value['debet'];
			$ArrJurnal[$key]['created_by'] 		= $username;
			$ArrJurnal[$key]['created_date'] 	= $datetime;
		}

		$this->db->trans_start();
			$this->db->insert_batch('jurnal',$ArrJurnal);
		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		}
	}	

	public function manual_aset_sisa(){
		$this->load->model('asset_model');
		$sql	= "SELECT a.depresiasi_bulan, b.* FROM asset_temp a join asset b on a.nomor=b.kd_asset order by a.nomor";
		$result	= $this->db->query($sql)->result_array();
		$lopp2 	= 0;
        if(!empty($result)){
			$detailDataDash	= array();
		    foreach ($result as $data) {
				$jmlx   	= $data['depresiasi_bulan'];
				$date_now 	= '2024-01-31';// date('Y-m-d');// '2023-04-31';
				$kode_assets	= $data['kd_asset'];
				$nmCategory		= $this->asset_model->getWhere('asset_category', 'id', $data['category']);
				for($x=1; $x <= $jmlx; $x++){
					$lopp2 += $x;
					$Tanggal 	= date('Y-m', mktime(0,0,0,substr($date_now,5,2)+ $x,0,substr($date_now,0,4)));
					$flagx		= 'N';
					$detailDataDash[$lopp2]['kd_asset'] 	= $kode_assets;
					$detailDataDash[$lopp2]['nm_asset'] 	= $data['nm_asset'];
					$detailDataDash[$lopp2]['category'] 	= $data['category'];
					$detailDataDash[$lopp2]['category_pajak'] 	= $data['category_pajak'];
					$detailDataDash[$lopp2]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
					$detailDataDash[$lopp2]['bulan'] 		= substr($Tanggal, 5,2);
					$detailDataDash[$lopp2]['tahun'] 		= substr($Tanggal, 0,4);
					$detailDataDash[$lopp2]['lokasi_asset'] = $data['id_dept'];
					$detailDataDash[$lopp2]['cost_center'] 	= $data['cost_center'];
					$detailDataDash[$lopp2]['nilai_susut'] 	= $data['value'];
					$detailDataDash[$lopp2]['kdcab'] 		= 'ORI';
					$detailDataDash[$lopp2]['flag'] 		= $flagx;
				}
			}
			$this->db->trans_start();
//				$this->db->insert_batch('asset_generate', $detailDataDash);				
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Asset gagal disimpan ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Asset berhasil disimpan. Thanks ...',
					'status'	=> 1
				);
			}
			echo json_encode($Arr_Data);			
		}
	}


	public function manual_akumulasi_aset(){
		$this->load->model('asset_model');
		$sql	= "SELECT a.nilai_aset as akumulasi_total, b.* FROM asset_temp a join asset b on a.nomor=b.kd_asset order by a.nomor";
		$result	= $this->db->query($sql)->result_array();
		$lopp2 	= 0;
        if(!empty($result)){
			$detailDataDash	= array();
		    foreach ($result as $data) {
				$nmCategory		= $this->asset_model->getWhere('asset_category', 'id', $data['category']);
				$lopp2++;
				$kode_assets	= $data['kd_asset'];
				$detailDataDash[$lopp2]['kd_asset'] 	= $kode_assets;
				$detailDataDash[$lopp2]['nm_asset'] 	= $data['nm_asset'];
				$detailDataDash[$lopp2]['category'] 	= $data['category'];
				$detailDataDash[$lopp2]['category_pajak'] 	= $data['category_pajak'];
				$detailDataDash[$lopp2]['nm_category'] 	= strtoupper($nmCategory[0]['nm_category']);
				$detailDataDash[$lopp2]['bulan'] 		= '12';
				$detailDataDash[$lopp2]['tahun'] 		= '2023';
				$detailDataDash[$lopp2]['lokasi_asset'] = $data['id_dept'];
				$detailDataDash[$lopp2]['cost_center'] 	= $data['cost_center'];
				$detailDataDash[$lopp2]['nilai_susut'] 	= $data['akumulasi_total'];
				$detailDataDash[$lopp2]['kdcab'] 		= 'ORI';
				$detailDataDash[$lopp2]['flag'] 		= 'Y';
				
			}
			$this->db->trans_start();
//				$this->db->insert_batch('asset_generate', $detailDataDash);				
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$Arr_Data	= array(
					'pesan'		=>'Asset gagal disimpan ...',
					'status'	=> 0
				);
			}
			else{
				$this->db->trans_commit();
				$Arr_Data	= array(
					'pesan'		=>'Asset berhasil disimpan. Thanks ...',
					'status'	=> 1
				);
			}
			echo json_encode($Arr_Data);			
			print_r($detailDataDash);
		}
	}

	public function update_gltras_query(){
		$this->load->model('Jurnal_model');
		$Username = 'system';
		$datetime = date('Y-m-d H:i:s');		
		$sql	= "select * from jurnaltras where no_reff in ('PY-2023-00261','PY-2023-00255','PY-2023-00326','PY-2023-00327','PY-2023-00328','PY-2023-00329','PY-2023-00330','PY-2023-00332','PY-2023-00333','PY-2023-00552','PY-2023-00553','PY-2023-00334','PY-2023-00336','PY-2023-00338','PY-2023-00340','PY-2023-00342','PY-2023-00343','PY-2023-00344','PY-2023-00418','PY-2023-00545','PY-2023-00339','PY-2023-00345','PY-2023-00546','PY-2023-00346','PY-2023-00361','PY-2023-00337','PY-2023-00554','PY-2023-00386','PY-2023-00387','PY-2023-00420','PY-2023-00341','PY-2023-00348','PY-2023-00350','PY-2023-00351','PY-2023-00352','PY-2023-00353','PY-2023-00354','PY-2023-00355','PY-2023-00419','PY-2023-00547','PY-2023-00349','PY-2023-00356','PY-2023-00357','PY-2023-00364','PY-2023-00374','PY-2023-00376','PY-2023-00358','PY-2023-00360','PY-2023-00375','PY-2023-00377','PY-2023-00378','PY-2023-00380','PY-2023-00385','PY-2023-00367','PY-2023-00369','PY-2023-00370','PY-2023-00381','PY-2023-00382','PY-2023-00384','PY-2023-00549','PY-2023-00551','PY-2023-00383','PY-2023-00548','PY-2023-00550','PY-2023-00555','PY-2023-00556','PY-2023-00347','PY-2023-00464','PY-2023-00465','PY-2023-00540','PY-2023-00541','PY-2023-00466','PY-2023-00467','PY-2023-00468','PY-2023-00469','PY-2023-00470','PY-2023-00471','PY-2023-00473','PY-2023-00474','PY-2023-00475','PY-2023-00476','PY-2023-00477','PY-2023-00478','PY-2023-00479','PY-2023-00480','PY-2023-00481','PY-2023-00482','PY-2023-00483','PY-2023-00484','PY-2023-00485','PY-2023-00486','PY-2023-00487','PY-2023-00488','PY-2023-00489','PY-2023-00490','PY-2023-00491','PY-2023-00492','PY-2023-00493','PY-2023-00494','PY-2023-00495','PY-2023-00496','PY-2023-00542','PY-2023-00543','PY-2023-00544','PY-2023-00539','PY-2023-00497','PY-2023-00498','PY-2023-00499','PY-2023-00500','PY-2023-00501','PY-2023-00502','PY-2023-00503','PY-2023-00504','PY-2023-00505','PY-2023-00506','PY-2023-00507','PY-2023-00508','PY-2023-00509','PY-2023-00510','PY-2023-00511','PY-2023-00512','PY-2023-00513','PY-2023-00514','PY-2023-00515','PY-2023-00516','PY-2023-00517','PY-2023-00518','PY-2023-00316','PY-2023-00519','PY-2023-00520','PY-2023-00521','PY-2023-00522','PY-2023-00523','PY-2023-00524','PY-2023-00525','PY-2023-00526','PY-2023-00527','PY-2023-00528','PY-2023-00529','PY-2023-00530','PY-2023-00531','PY-2023-00532','PY-2023-00533','PY-2023-00534','PY-2023-00535','PY-2023-00536','PY-2023-00537','PY-2023-00538','PY-2023-00264','PY-2023-00269','PY-2023-00284','PY-2023-00286','PY-2023-00287','PY-2023-00288','PY-2023-00297','PY-2023-00307','PY-2023-00311','PY-2023-00570','PY-2023-00571','PY-2023-00632','PY-2023-00280','PY-2023-00282','PY-2023-00290','PY-2023-00267','PY-2023-00283','PY-2023-00293','PY-2023-00300','PY-2023-00306','PY-2023-00319','PY-2023-00321','PY-2023-00634','PY-2023-00265','PY-2023-00271','PY-2023-00274','PY-2023-00277','PY-2023-00291','PY-2023-00294','PY-2023-00312','PY-2023-00561','PY-2023-00635','PY-2023-00636','PY-2023-00637','PY-2023-00638','PY-2023-00273','PY-2023-00276','PY-2023-00314','PY-2023-00275','PY-2023-00569','PY-2023-00639','PY-2023-00309','PY-2023-00640','PY-2023-00359','PY-2023-00362','PY-2023-00363','PY-2023-00642','PY-2023-00433','PY-2023-00434','PY-2023-00441','PY-2023-00444','PY-2023-00446','PY-2023-00455','PY-2023-00429','PY-2023-00435','PY-2023-00436','PY-2023-00437','PY-2023-00438','PY-2023-00439','PY-2023-00442','PY-2023-00443','PY-2023-00460','PY-2023-00472','PY-2023-00302','PY-2023-00303','PY-2023-00424','PY-2023-00440','PY-2023-00459','PY-2023-00462','PY-2023-00421','PY-2023-00422','PY-2023-00423','PY-2023-00425','PY-2023-00426','PY-2023-00428','PY-2023-00430','PY-2023-00431','PY-2023-00432','PY-2023-00445','PY-2023-00447','PY-2023-00448','PY-2023-00449','PY-2023-00450','PY-2023-00451','PY-2023-00452','PY-2023-00453','PY-2023-00454','PY-2023-00456','PY-2023-00457','PY-2023-00458','PY-2023-00461','PY-2023-00633','PY-2023-00572','PY-2023-00573','PY-2023-00574','PY-2023-00575','PY-2023-00576','PY-2023-00577','PY-2023-00578','PY-2023-00579','PY-2023-00581','PY-2023-00580','PY-2023-00598','PY-2023-00582','PY-2023-00583','PY-2023-00584','PY-2023-00585','PY-2023-00586','PY-2023-00587','PY-2023-00588','PY-2023-00589','PY-2023-00590','PY-2023-00591','PY-2023-00592','PY-2023-00593','PY-2023-00594','PY-2023-00595','PY-2023-00596','PY-2023-00597','PY-2023-00599','PY-2023-00600','PY-2023-00601','PY-2023-00602','PY-2023-00603','PY-2023-00604','PY-2023-00605','PY-2023-00606','PY-2023-00607','PY-2023-00608','PY-2023-00609','PY-2023-00611','PY-2023-00612','PY-2023-00613','PY-2023-00614','PY-2023-00615','PY-2023-00618','PY-2023-00619','PY-2023-00620','PY-2023-00621','PY-2023-00622','PY-2023-00623','PY-2023-00624','PY-2023-00625','PY-2023-00626','PY-2023-00627','PY-2023-00628','PY-2023-00629','PY-2023-00630','PY-2023-00631') and (debet<>0 or kredit <>0) and stspos='0' order by tanggal,nomor,id";

		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor='';
			$total=0;
			$Nomor_JV='';
		    foreach ($result as $data) {
				if($nomor!=$data['nomor']){
					if($nomor!=""){
						// update jurnaltras
						$this->db->query("update jurnaltras set stspos='1' where nomor='".$nomor."'");
						// update japh
						$this->db->query("update ".DBACC.".japh set jml='".$total."' where nomor='".$Nomor_JV."'");
					}
					$total=0;
					// insert header apjv
					$tanggal= $data['tanggal'];
					$Bln	= substr($tanggal,5,2);
					$Thn	= substr($tanggal,0,4);
					$Nomor_JV = $this->Jurnal_model->get_no_buk('101', $tanggal);
					$nmsupplier='';
					$qrysup=$this->db->query("select nm_supplier from supplier where id_supplier='".$data['nocust']."' limit 1")->row();
					if(!empty($qrysup)) $nmsupplier=$qrysup->nm_supplier;
					$keterangan		= 'Pembayaran '.$data['no_reff'];
					$dataJVhead = array(
						'nomor' 	    	=> $Nomor_JV,
						'tgl'	         	=> $tanggal,
						'jml'	            => $total,
						'jenis_ap'	        => 'V',
						'customer'			=> $data['nocust'],
						'bayar_kepada'		=> $nmsupplier,
						'kdcab'				=> '101',
						'jenis_reff' 		=> 'BUK',
						'no_reff' 			=> $data['no_reff'],
						'note'				=> $keterangan,
						'user_id'			=> $Username,
						'ho_valid'			=> '',
					);
					$this->db->insert(DBACC.'.japh',$dataJVhead);
					$qry_ptb = "UPDATE ".DBACC.".pastibisa_tb_cabang SET nobuk=nobuk + 1 WHERE nocab='101'";
					$this->db->query($qry_ptb);
				}
				// insert jurnal
				$datadetail = array(
					'tipe'			=> 'BUK',
					'jenis_trans'	=> 'BUK',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tanggal,
					'no_perkiraan'	=> $data['no_perkiraan'],
					'keterangan'	=> $data['keterangan'],
					'no_reff'		=> $data['no_reff'],
					'debet'			=> $data['debet'],
					'kredit'		=> $data['kredit'],
					'nocust'		=> $data['nocust'],
					'created_by'	=> $Username,
					'created_on'	=> $datetime
				);
				$total=($total+$data['debet']);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
				$nomor=$data['nomor'];
			}
			if($nomor!=""){
				// update jurnaltras
				$this->db->query("update jurnaltras set stspos='1' where nomor='".$nomor."'");
				// update japh
				$this->db->query("update ".DBACC.".japh set jml='".$total."' where nomor='".$Nomor_JV."'");
			}			
			$this->db->trans_complete();
			if ($this->db->trans_status()) {
				$this->db->trans_commit();
				$result = TRUE;
				echo "OK";
			} else {
				$this->db->trans_rollback();
				$result = FALSE;
				echo "ERROR";
			}
		}
	}

	public function update_gltras_terima_invoice_po(){
		$this->load->model('Jurnal_model');
		$Username = 'system';
		$datetime = date('Y-m-d H:i:s');		
		$sql	= "select * from jurnaltras where (jenis_jurnal='JV053' or jenis_jurnal='JV041') and (debet<>0 or kredit <>0) and stspos='0' and nomor in ( 'JV041POX22100067970','JV041POX22100111805','JV041POX23020040966','JV041POX23020091526','JV041POX23020129150','JV041POX23030001196','JV041POX23030029557','JV041POX23030068778','JV041POX23030068828','JV041POX23030069515','JV041POX23030075283','JV041POX23030075302','JV041POX23030075333','JV041POX23030113456','JV041POX23030155161','JV041POX23030155192','JV041POX23030155329','JV041POX23030155476','JV041POX23030155518','JV041POX23030155598','JV041POX23030155618','JV041POX23030155714','JV041POX23030155728','JV041POX23030163228','JV041POX23030185648','JV041POX23030185786','JV041POX23030185971','JV041POX23040008845','JV041POX23040039674','JV041POX23040054403','JV041POX23040083139','JV041POX23050001598','JV041POX23050089155','JV041POX23050089294','JV041POX23050089465','JV041POX23050089727','JV041POX23050089830','JV041POX23050090922','JV041POX23050091326','JV041POX23050093279','JV041POX23050093334','JV041POX23050093660','JV041POX23050093698','JV041POX23050093771','JV041POX23050093815','JV041POX23050093921','JV041POX23050099609','JV041POX23050100970','JV041POX23050101288','JV041POX23050104253','JV041POX23050104539','JV041POX23050104840','JV041POX23060008404','JV041POX23060008447','JV041POX23060008517','JV041POX23060008585','JV041POX23060008658','JV041POX23060008741','JV041POX23060008783','JV041POX23060028672','JV041POX23060029577','JV041POX23060041525','JV041POX23060050436','JV041POX23060050992','JV041POX23060060825','JV041POX23060061106','JV041POX23060061352','JV041POX23060062290','JV041POX23060069499','JV041POX23060087450','JV041POX23060087452','JV041POX23060087456','JV041POX23060087470','JV041POX23060087562','JV041POX23060087597','JV041POX23060087676','JV041POX23060087792','JV041POX23060087825','JV041POX23060087853','JV041POX23060090475','JV041POX23060094922','JV041POX23060097403','JV041POX23060099205','JV041POX23060103482','JV041POX23060110813','JV041POX23060114103','JV041POX23060118973','JV041POX23060122518','JV041POX23060123192','JV041POX23060129316','JV041POX23060130842','JV041POX23070007478','JV041POX23070011989','JV041POX23070012954','JV041POX23070016170','JV041POX23070021158','JV041POX23070022524','JV041POX23070028485','JV041POX23070033213','JV041POX23070037307','JV041POX23070039172','JV041POX23070039271','JV041POX23070040768','JV041POX23070047147','JV041POX23070047261','JV041POX23070047405','JV041POX23070047415','JV041POX23070047449','JV041POX23070054946','JV041POX23070063392','JV041POX23070067466','JV041POX23070084404','JV041POX23070085682','JV041POX23070089500','JV041POX23070094180','JV041POX23070097824','JV041POX23070100975','JV041POX23070102719','JV041POX23070104454','JV041POX23070105300','JV041POX23070110869','JV041POX23070112351','JV041POX23070116531','JV041POX23070119228','JV041POX23070119436','JV041POX23070121353','JV041POX23070124499','JV041POX23070126920','JV041POX23070131861','JV041POX23070135144','JV041POX23070139604','JV041POX23070140133','JV041POX23070141644','JV041POX23070146333','JV041POX23070147307','JV041POX23070150363','JV041POX23070151250','JV041POX23070153486','JV041POX23070155527','JV041POX23070155531','JV041POX23070156749','JV041POX23070157119','JV041POX23070157610','JV041POX23070160602','JV041POX23070161588','JV041POX23080001493','JV041POX23080002903','JV041POX23080004791','JV041POX23080006616','JV041POX23080008106','JV041POX23080008346','JV041POX23080008434','JV041POX23080008500','JV041POX23080008536','JV041POX23080008538','JV041POX23080008662','JV041POX23080008671','JV041POX23080008806','JV041POX23080012951','JV041POX23080015359','JV041POX23080019436','JV041POX23080022177','JV041POX23080022276','JV041POX23080022419','JV041POX23080023461','JV041POX23080023717','JV041POX23080023733','JV041POX23080023897','JV041POX23080029571','JV041POX23080034862','JV041POX23080035574','JV041POX23080036833','JV041POX23080045325','JV041POX23080045395','JV041POX23080050132','JV041POX23080052747','JV041POX23080053875','JV041POX23080054791','JV041POX23080054950','JV041POX23080055370','JV041POX23080060646','JV041POX23080061154','JV041POX23080061917','JV041POX23080062896','JV041POX23080064195','JV041POX23080068667','JV041POX23080069104','JV041POX23080070778','JV041POX23080071261','JV041POX23080072227','JV041POX23080074287','JV041POX23080078509','JV041POX23080079682','JV041POX23080081991','JV041POX23080082460','JV041POX23080085610','JV041POX23080086305','JV041POX23080088793','JV041POX23080089739','JV041POX23080090682','JV041POX23080092532','JV041POX23080093997','JV041POX23080094154','JV041POX23080098640','JV041POX23080099933','JV041POX23080100463','JV041POX23080101886','JV041POX23080102477','JV041POX23080104498','JV041POX23080105406','JV041POX23080106799','JV041POX23080107561','JV041POX23080108485','JV041POX23080109301','JV041POX23080110865','JV041POX23080111697','JV041POX23080113892','JV041POX23080116903','JV041POX23080117346','JV041POX23080118198','JV041POX23080119930','JV041POX23080120937','JV041POX23080122564','JV041POX23080123888','JV041POX23080124108','JV041POX23080124541','JV041POX23080124575','JV041POX23080124653','JV041POX23080124738','JV041POX23080124964','JV041POX23080125361','JV041POX23080126206','JV041POX23080127247','JV041POX23080128737','JV041POX23080129370','JV041POX23080131781','JV041POX23080132721','JV041POX23080134155','JV041POX23080135935','JV041POX23080136998','JV041POX23080137315','JV041POX23080138417','JV041POX23080140720','JV041POX23080141730','JV041POX23080142111','JV041POX23080144615','JV041POX23080145765','JV041POX23090001533','JV041POX23090004590','JV041POX23090005851','JV041POX23090006427','JV041POX23090007623','JV041POX23090008636','JV041POX23090009186','JV041POX23090010379','JV041POX23090012464','JV041POX23090012559','JV041POX23090012608','JV041POX23090013203','JV041POX23090014975','JV041POX23090015147','JV041POX23090016966','JV041POX23090019402','JV041POX23090020695','JV041POX23090021468','JV041POX23090022552','JV041POX23090024454','JV041POX23090025900','JV041POX23090026830','JV041POX23090027125','JV041POX23090029951','JV041POX23090030822','JV041POX23090031109','JV041POX23090032760','JV041POX23090033390','JV041POX23090034807','JV041POX23090036501','JV041POX23090037417','JV041POX23090038363','JV041POX23090039352','JV041POX23090040299','JV041POX23090044823','JV041POX23090045408','JV041POX23090045522','JV041POX23090046231','JV041POX23090047270','JV041POX23090048707','JV041POX23090049925','JV041POX23090050783','JV041POX23090051518','JV041POX23090052953','JV041POX23090054498','JV041POX23090055821','JV041POX23090057839','JV041POX23090058869','JV041POX23090059953','JV041POX23090060276','JV041POX23090063286','JV041POX23090065911','JV041POX23090068770','JV041POX23090069798','JV041POX23090070570','JV041POX23090071571','JV041POX23090072177','JV041POX23090072389','JV041POX23090072435','JV041POX23090072685','JV041POX23090073734','JV041POX23090074271','JV041POX23090075653','JV041POX23090076523','JV041POX23090081188','JV041POX23090083264','JV041POX23090083518','JV041POX23090083599','JV041POX23090083979','JV041POX23090085428','JV041POX23090087244','JV041POX23090088873','JV041POX23090089342','JV041POX23090090332','JV041POX23090092762','JV041POX23090094898','JV041POX23090095365','JV041POX23090097997','JV041POX23090098525','JV041POX23090100327','JV041POX23090102282','JV041POX23090104896','JV041POX23090105465','JV041POX23090106357','JV041POX23090107413','JV041POX23090108108','JV041POX23090109584','JV041POX23090110614','JV041POX23090112346','JV041POX23090114358','JV041POX23090116358','JV041POX23090117304','JV041POX23090118729','JV041POX23090119354','JV041POX23090119366','JV041POX23090122602','JV041POX23090124207','JV041POX23090125237','JV041POX23090126668','JV041POX23090127672','JV041POX23090132741','JV041POX23090133228','JV041POX23090135519','JV041POX23090136568','JV041POX23090136872','JV041POX23090137310','JV041POX23090138986','JV041POX23090140888','JV041POX23090141792','JV041POX23090143763','JV041POX23090144474','JV041POX23090149903','JV041POX23090152211','JV041POX23090152263','JV041POX23090152907','JV041POX23090153664','JV041POX23090156608','JV041POX23090157838','JV041POX23090158929','JV041POX23090159892','JV041POX23090160210','JV041POX23090161574','JV041POX23090162993','JV041POX23090163498','JV041POX23090166931','JV041POX23090167385','JV041POX23090168285','JV041POX23090169750','JV041POX23090170382','JV041POX23090171197','JV041POX23090172182','JV041POX23090174706','JV041POX23090175750','JV041POX23090176776','JV041POX23100001789','JV041POX23100002993','JV041POX23100005131','JV041POX23100007415','JV041POX23100008362','JV041POX23100011861','JV041POX23100012785','JV041POX23100013954','JV041POX23100014655','JV041POX23100017360','JV041POX23100018493','JV041POX23100019448','JV041POX23100023203','JV041POX23100025863','JV041POX23100027621','JV041POX23100028810','JV041POX23100029279','JV041POX23100031524','JV041POX23100033608','JV041POX23100034947','JV041POX23100035106','JV041POX23100036394','JV041POX23100037270','JV041POX23100042705','JV041POX23100044872','JV041POX23100044909','JV041POX23100047124','JV041POX23100049371','JV041POX23100053563','JV041POX23100057893','JV041POX23100058473','JV041POX23100060368','JV041POX23100064922','JV041POX23100065337','JV041POX23100068422','JV041POX23100069271','JV041POX23100070446','JV041POX23100071631','JV041POX23100072546','JV041POX23100077989','JV041POX23100078958','JV041POX23100080695','JV041POX23100082922','JV041POX23100085683','JV041POX23100086511','JV041POX23100094704','JV041POX23110007167','JV041POX23110008273','JV041POX23110009375','JV041POX23110012113','JV041POX23110013844','JV053POX23030069944','JV053POX23030113526','JV053POX23030185210','JV053POX23030185271','JV053POX23030185921','JV053POX23070044566','JV053POX23070079182','JV053POX23070106444','JV053POX23070127399','JV053POX23070148292','JV053POX23070149362','JV053POX23080035762','JV053POX23080048457','JV053POX23080103575','JV053POX23080114383','JV053POX23080133982','JV053POX23090023369','JV053POX23090082700','JV053POX23090091430','JV053POX23090094298','JV053POX23090114751','JV053POX23090119516','JV053POX23090130458','JV053POX23090134772','JV053POX23090148914','JV053POX23090150484','JV053POX23090154626','JV053POX23100004927','JV053POX23100006982','JV053POX23100010212','JV053POX23100024382','JV053POX23100037682','JV053POX23100043559','JV053POX23100045787','JV053POX23100046838','JV053POX23100073807'
		) order by tanggal,nomor";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor='';
			$total=0;
			$Nomor_JV='';
		    foreach ($result as $data) {
				if($nomor!=$data['nomor']){
					if($nomor!=""){
						// update jurnaltras
						$this->db->query("update jurnaltras set stspos='1' where nomor='".$nomor."'");
						// update javh
						$this->db->query("update ".DBACC.".javh set jml='".$total."' where nomor='".$Nomor_JV."'");
					}
					$total=0;
					// insert header javh
					$tanggal= $data['tanggal'];
					$Bln	= substr($tanggal,5,2);
					$Thn	= substr($tanggal,0,4);
					$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tanggal);
					$keterangan		= 'Receive Invoice '.$data['no_reff'];
					$dataJVhead = array(
						'nomor' 	    	=> $Nomor_JV,
						'tgl'	         	=> $tanggal,
						'jml'	            => $total,
						'bulan'	            => $Bln,
						'tahun'	            => $Thn,
						'kdcab'				=> '101',
						'jenis'			    => 'JV',
						'keterangan'		=> $keterangan,
						'memo'	            => $data['no_reff'],
						'user_id'			=> $Username,
						'ho_valid'			=> '',
					);
					$this->db->insert(DBACC.'.javh',$dataJVhead);
				}
				// insert jurnal
				$datadetail = array(
					'tipe'			=> 'JV',
					'nomor'			=> $Nomor_JV,
					'tanggal'		=> $tanggal,
					'no_perkiraan'	=> $data['no_perkiraan'],
					'keterangan'	=> $data['keterangan'],
					'no_reff'		=> $data['no_reff'],
					'debet'			=> $data['debet'],
					'kredit'		=> $data['kredit'],
					'nocust'		=> $data['nocust'],
					'created_by'	=> $Username,
					'created_on'	=> $datetime
				);
				$total=($total+$data['debet']);
				$this->db->insert(DBACC.'.jurnal',$datadetail);
				$nomor=$data['nomor'];
			}
			if($nomor!=""){
				// update jurnaltras
				$this->db->query("update jurnaltras set stspos='1' where nomor='".$nomor."'");
				// update japh
				$this->db->query("update ".DBACC.".javh set jml='".$total."' where nomor='".$Nomor_JV."'");
			}			
			$this->db->trans_complete();
			if ($this->db->trans_status()) {
				$this->db->trans_commit();
				$result = TRUE;
				echo "OK";
			} else {
				$this->db->trans_rollback();
				$result = FALSE;
				echo "ERROR";
			}
		}
	}
	function upd_delivery_cogs_material(){
		$sql="select * from request_outgoing where kode_delivery <>'' order by kode_delivery, id";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id_uniq=$data['id'];
				$totaldollar=$data['total_price'];
				$sql="select * from delivery_product_detail where kode_delivery ='".$kode_delivery."' and id_uniq='".$id_uniq."'";
				$dt_dv = $this->db->query($sql)->row();
				if(!empty($dt_dv)){
					$id_dv=$dt_dv->id;
					$sql="select * from ms_kurs where tanggal <='".$dt_dv->updated_date."' order by tanggal desc limit 1";
					$dt_kurs = $this->db->query($sql)->row();
					$hargacogs=($dt_kurs->kurs*$totaldollar);
					echo $hargacogs.':'.$kode_delivery.'.<br>';
					$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id_dv."'");
				}else{
					echo $kode_delivery.':'.$totaldollar.'.xxxx<br>';
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}
	function new_upd_material_delivery(){
		$limits=1000;echo $limits.'<hr>';
		$sql="select kode_delivery ,id ,id_uniq ,product_code ,product,berat from delivery_product_detail where sts_product ='so material' and id_uniq>0 order by kode_delivery, id limit ".$limits.",100";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id=$data['id'];
				$id_uniq=$data['id_uniq'];
				$product_code=$data['product_code'];
				$id_material=$data['product'];
				$berat=$data['berat'];
				if($product_code=='field joint'){
					$sql="select kode_trans,approve_date from request_outgoing where id='".$id_uniq."'";
					$dt_dv = $this->db->query($sql)->row();
					if(!empty($dt_dv)){
						$tanggal=$dt_dv->approve_date;
						$sql="select total_nilai from jurnal where kode_trans='".$dt_dv->kode_trans."' and id_material='".$id_material."' limit 1";
						$dtJurnal=$this->db->query($sql)->row();
						$hargacogs=0;
						if(!empty($dtJurnal)) {
							$hargacogs=$dtJurnal->total_nilai;
							$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");
						}else{
							$sql="select price_book from price_book where id_material='".$id_material."' order by updated_date desc limit 1";
							$dtPricebook=$this->db->query($sql)->row();
							if(!empty($dtPricebook)) $hargacogs=($dtPricebook->price_book*$berat);
							$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");
						}
						echo $kode_delivery.':'.$hargacogs.'.xfieldx'.$id_material.'<br>';
					}
				}else{
					$sql="select kode_trans,update_date from warehouse_adjustment_detail where id='".$id_uniq."'";
					$dt_dv = $this->db->query($sql)->row();
					if(!empty($dt_dv)){
						$tanggal=$dt_dv->update_date;
						$sql="select total_nilai from jurnal where kode_trans='".$dt_dv->kode_trans."' and id_material='".$id_material."' limit 1";
						$dtJurnal=$this->db->query($sql)->row();
						$hargacogs=0;
						if(!empty($dtJurnal)) {
							$hargacogs=$dtJurnal->total_nilai;
							$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");
						}else{
							$sql="select price_book from price_book where  id_material='".$id_material."' order by updated_date desc limit 1";
							$dtPricebook=$this->db->query($sql)->row();
							if(!empty($dtPricebook)) $hargacogs=($dtPricebook->price_book*$berat);
							$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");							
						}
						echo $kode_delivery.':'.$hargacogs.'.xmtlx'.$id_material.'<br>';
					}
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}			
	}
	function new_upd_field_joint_delivery(){
		//$limits=1000;echo $limits.'<hr>';
		$sql="select kode_delivery ,id ,id_uniq ,product_code ,product,berat from delivery_product_detail where sts_product ='field joint' and id_uniq>0 order by kode_delivery, id ";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id=$data['id'];
				$id_uniq=$data['id_uniq'];
				$product_code=$data['product_code'];
				$id_material=$data['product'];
				$berat=$data['berat'];
				$sql="select kode_trans from outgoing_field_joint where id='".$id_uniq."'";
				$dt_dv = $this->db->query($sql)->row();
				if(!empty($dt_dv)){
					$sql="select total_nilai from jurnal where kode_trans='".$dt_dv->kode_trans."' ";
					$dtJurnal=$this->db->query($sql)->row();
					$hargacogs=0;
					if(!empty($dtJurnal)) {
						$sql="select sum(total_nilai) ttl_nilai from jurnal where kode_trans='".$dt_dv->kode_trans."' ";
						$dtJurnal=$this->db->query($sql)->row();
						$hargacogs=$dtJurnal->ttl_nilai;
						$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");
						echo $kode_delivery.':'.$hargacogs.'.xjurnalx'.$dt_dv->kode_trans.'<br>';
					}else{
						$sql="select check_qty_oke,id_material from warehouse_adjustment_detail where kode_trans='".$dt_dv->kode_trans."' ";
						$dtWh=$this->db->query($sql)->result_array();
						if(!empty($dtWh)){
							$hargacogs=0;
							foreach ($dtWh as $rwh) {
								$jumlah=$rwh['check_qty_oke'];
								$id_material=$rwh['id_material'];
								$sql="select price_book from price_book where  id_material='".$id_material."' order by updated_date desc limit 1";
								$dtPricebook=$this->db->query($sql)->row();
								if(!empty($dtPricebook)) $hargacogs=($hargacogs+($dtPricebook->price_book*$jumlah));
							}
							$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where id='".$id."'");	
							echo $kode_delivery.':'.$hargacogs.'.xmaterx'.$dt_dv->kode_trans.'<br>';
						}else{
							echo $kode_delivery.':'.$hargacogs.'.xerrorx'.$dt_dv->kode_trans.'<br>';
						}
					}
				}else{
					echo $kode_delivery.':'.$hargacogs.'.xerror <br>';
				}
			}
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}			
	}

	function upd_delivery_cogs_cutting(){
		$sql="select * from so_cutting_detail where kode_delivery <>'' order by kode_delivery, id";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id_uniq=$data['id'];
				$hargacogs=$data['finish_good'];
				$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where kode_delivery ='".$kode_delivery."' and id_uniq='".$id_uniq."'");
				$nomor++;
			}
			echo $nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}

	function finish_good_cutting(){
		$sql="select * from so_cutting_detail where kode_delivery <>'' order by kode_delivery, id";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id_uniq=$data['id'];
				$hargacogs=$data['finish_good'];
				$this->db->query("update delivery_product_detail set nilai_cogs='".$hargacogs."' where kode_delivery ='".$kode_delivery."' and id_uniq='".$id_uniq."'");
				$nomor++;
			}
			echo $nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}	
	function update_data_pro_detail(){
		$result = $this->db->query("select * from so_detail_header where approve_by is not null and approve='P'" )->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$dtid=$data['id'];
				$rpd = $this->db->query("SELECT * FROM sentralsistem.production_detail where id_milik='".$dtid."' and finish_production_date is not null limit 1")->row();
				if(!empty($rpd)){
					$tgl_kurs=$rpd->finish_production_date;
					$dtkurs	= $this->db->query("select kurs from ms_kurs where tanggal <='".$tgl_kurs."' and mata_uang='USD' order by tanggal desc limit 1")->row();
					$kurs=$dtkurs->kurs;
					$wip_material=$rpd->amount;
					$pe_direct_labour=($data['pe_direct_labour']*$kurs);
					$foh=(($data['pe_machine'] + $data['pe_mould_mandrill'] + $data['pe_foh_depresiasi'] + $data['pe_biaya_rutin_bulanan'] + $data['pe_foh_consumable'])*$kurs);
					$pe_indirect_labour=($data['pe_indirect_labour']*$kurs);
					$pe_consumable=($data['pe_consumable']*$kurs);
					$finish_good=($wip_material+$pe_direct_labour+$foh+$pe_indirect_labour+$pe_consumable);
					if($wip_material<>0){
						$this->db->query("update production_detail set wip_kurs='".$kurs."'
						, wip_material='".$wip_material."'
						, wip_dl='".$pe_direct_labour."'
						, wip_foh='".$foh."'
						, wip_il='".$pe_indirect_labour."'
						, wip_consumable='".$pe_consumable."'
						, finish_good='".$finish_good."'
						WHERE id_milik ='".$dtid."'");
						/*
						*/
						echo "update production_detail set wip_kurs='".$kurs."' , wip_material='".$wip_material."' , wip_dl='".$pe_direct_labour."' , wip_foh='".$foh."' , wip_il='".$pe_indirect_labour."' , wip_consumable='".$pe_consumable."' , finish_good='".$finish_good."' WHERE id_milik ='".$dtid."'"."<hr>";
					}
				}
			}
		}
	}

	function upd_finish_good_cutting_detail_and_delivery_detil(){
		$sql="select * from so_cutting_detail where kode_delivery <>'' order by kode_delivery, id";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$kode_delivery=$data['kode_delivery'];
				$id_header=$data['id_header'];
				$id_milik=$data['id_milik'];
				$dtfg	= $this->db->query("select b.finish_good from so_cutting_header a join production_detail b on a.id_milik=b.id_milik and a.qty_ke=b.product_ke where a.id='".$id_header."' and b.id_milik='".$id_milik."' limit 1")->row();
				$hargattlfg=$dtfg->finish_good;
				$length=$data['length'];
				$length_split=$data['length_split'];
				$hargafg=round(($dtfg->finish_good*$length_split/$length));echo $kode_delivery.':'.$hargafg.'<hr>';
				$this->db->query("update so_cutting_detail set finish_good='".$hargafg."' where id ='".$data['id']."'");
				$this->db->query("update delivery_product_detail set nilai_cogs='".$hargafg."' where id_uniq='".$data['id']."' and kode_delivery='".$kode_delivery."' and sts='cut'");
				$nomor++;
			}
			echo $nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}
	
	function setup_from_laporoan_perhari() {
		$sql="select * from laporan_per_hari where qty_awal is not null and kurs >1";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$id_milik=$data['id_milik'];
				$qty_awal=$data['qty_awal'];
				$qty_akhir=$data['qty_akhir'];
				$total_qty=($qty_akhir-$qty_awal+1);
				$material=$data['real_harga'];
				$dl=$data['direct_labour'];
				$idl=$data['indirect_labour'];
				$mch=$data['machine'];
				$mml=$data['mould_mandrill'];
				$csm=$data['consumable'];
				$fcsm=$data['foh_consumable'];
				$fdpr=$data['foh_depresiasi'];
				$bgnp=$data['biaya_gaji_non_produksi'];
				$bnp=$data['biaya_non_produksi'];
				$brb=$data['biaya_rutin_bulanan'];
				$wip_kurs=$data['kurs'];
				$wip_material=round($material*$wip_kurs/$total_qty);
				$wip_dl=round($dl*$wip_kurs/$total_qty);
				$wip_foh=round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs/$total_qty);
				$wip_il=round($idl*$wip_kurs/$total_qty);
				$wip_consumable=round($csm*$wip_kurs/$total_qty);
				$finish_good=($wip_material+$wip_dl+$wip_foh+$wip_il+$wip_consumable);
				$this->db->query("update production_detail set wip_kurs='".$wip_kurs."', wip_material='".$wip_material."', wip_dl='".$wip_dl."', wip_foh='".$wip_foh."', wip_il='".$wip_il."', wip_consumable='".$wip_consumable."', finish_good='".$finish_good."' where id_milik ='".$id_milik."' and product_ke between ".$qty_awal." and ".$qty_akhir."");
				$nomor++;
				echo $id_milik."<br>";
			}
			echo "<hr>".$nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}

	function update_gp_to_wip_base_lap_pro() {
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="select * from laporan_per_hari where qty_awal is not null and kurs >1 order by `date`";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$id_milik=$data['id_milik'];
				$qty_awal=$data['qty_awal'];
				$qty_akhir=$data['qty_akhir'];
				$total_qty=($qty_akhir-$qty_awal+1);
				$material=$data['real_harga'];
				$dl=$data['direct_labour'];
				$idl=$data['indirect_labour'];
				$mch=$data['machine'];
				$mml=$data['mould_mandrill'];
				$csm=$data['consumable'];
				$fcsm=$data['foh_consumable'];
				$fdpr=$data['foh_depresiasi'];
				$bgnp=$data['biaya_gaji_non_produksi'];
				$bnp=$data['biaya_non_produksi'];
				$brb=$data['biaya_rutin_bulanan'];
				$wip_kurs=$data['kurs'];
				$wip_material=round($material*$wip_kurs/$total_qty);
				$wip_dl=round($dl*$wip_kurs/$total_qty);
				$wip_foh=round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs/$total_qty);
				$wip_il=round($idl*$wip_kurs/$total_qty);
				$wip_consumable=round($csm*$wip_kurs/$total_qty);
				$finish_good=($wip_material+$wip_dl+$wip_foh+$wip_il+$wip_consumable);
				$this->db->query("update production_detail set wip_kurs='".$wip_kurs."', wip_material='".$wip_material."', wip_dl='".$wip_dl."', wip_foh='".$wip_foh."', wip_il='".$wip_il."', wip_consumable='".$wip_consumable."', finish_good='".$finish_good."' where id_milik ='".$id_milik."' and product_ke between ".$qty_awal." and ".$qty_akhir."");
				$nomor++;
				echo $id_milik."<br>";

		// jurnal
				$tgl_po=$data['date'];
				$tgl_voucher=$data['date'];
				$total=round(($mch+$mml+$fdpr+$brb+$fcsm+$material+$dl+$idl+$csm)*$wip_kurs);
				$keterangan='GD. PRODUKSI - WIP ( '.$tgl_po.' )';
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV004';
				$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				foreach($datajurnal AS $record) {
					$posisi = $record->posisi;
					$nokir  = $record->no_perkiraan;
					$parameter_no = $record->parameter_no;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Material",
						  'no_reff'       => $reff,
						  'debet'         => round($material*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Direct Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($dl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Indirect Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($idl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Consumable",
						  'no_reff'       => $reff,
						  'debet'         => round($csm*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP FOH",
						  'no_reff'       => $reff,
						  'debet'         => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						if($parameter_no=="1") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "GUDANG PRODUKSI (Material)",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($material*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="2") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "DIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($dl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="3") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "INDIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($idl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="4") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "CONSUMABLE PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($csm*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="5") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "FOH PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							  'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
					}
				}
				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
			echo "<hr>".$nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}
	function update_wip_to_fg() {
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE category = 'quality control' order by tanggal";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$id=$data['id'];
				$coa=$data['coa'];
				$no_request = $id;
				$no_so=$data['no_so'];
				$product=$data['product'];
				$no_spk=$data['id_spk'];
				$id_milik=$data['id_milik'];
				$id_detail=$data['id_detail'];
				$coa_fg=$data['coa_fg'];
				$dt_pd=$this->db->query("select * from production_detail where id_milik ='".$id_milik."' and id='".$id_detail."'")->row();
				if(empty($dt_pd)) continue;
				$wip_material=$dt_pd->wip_material;
				$wip_dl=$dt_pd->wip_dl;
				$wip_foh=$dt_pd->wip_foh;
				$wip_il=$dt_pd->wip_il;
				$wip_consumable=$dt_pd->wip_consumable;
				$finish_good=$dt_pd->finish_good;
				$nomor++;
				echo $id_detail."<br>";

		// jurnal
				$tgl_po=$data['tanggal'];
				$tgl_voucher=$data['tanggal'];
				$keterangan='WIP - FINISH GOOD ('.$no_so.' - '.$product.' - '.$no_spk.') '.$id_detail;
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id_detail'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $finish_good, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV005';

				$masterjurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				$totaldebit=0;$totalkredit=0;$coa_cogm='';
				foreach($masterjurnal AS $record){
					$debit=0;$kredit=0;
					$nokir  	= $record->no_perkiraan;
					$posisi 	= $record->posisi;
					$parameter  = $record->parameter_no;
					$keterangan = $record->keterangan;
					if ($parameter=='1'){
						$debit=($wip_material);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => $debit,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='2'){
						$debit=($wip_dl);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => $debit,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='3'){
						$debit=($wip_il);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => $debit,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='4'){
						$debit=($wip_consumable);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => $debit,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='5'){
						$debit=($wip_foh);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => ($debit),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='6'){
						$kredit=($wip_material);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $coa,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $kredit,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='7'){
						$kredit=($wip_dl);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $coa,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $kredit,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='8'){
						$kredit=($wip_il);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $coa,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $kredit,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='9'){
						$kredit=($wip_consumable);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $coa,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $kredit,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='10'){
						$kredit=($wip_foh);
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $coa,
						  'keterangan'    => $keterangan.' '.$no_spk,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $kredit,
						  'jenis_jurnal'  => 'wip finishgood',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
					if ($parameter=='11'){
						$coa_cogm=$nokir;
					}
					$totaldebit+=$debit;$totalkredit+=$kredit;
				}
				$Keterangan_INV= 'WIP - FINISH GOOD ('.$no_so.' - '.$product.' - '.$no_spk.')';
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $coa_cogm,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => 0,
				  'kredit'        => $totalkredit,
				  'jenis_jurnal'  => 'wip finishgood',
				  'no_request'    => $no_request,
							  'stspos'		  =>1
				 );
				$det_Jurnaltes[]  = array(
				  'nomor'         => '',
				  'tanggal'       => $tgl_voucher,
				  'tipe'          => 'JV',
				  'no_perkiraan'  => $coa_fg,
				  'keterangan'    => $Keterangan_INV,
				  'no_reff'       => $id,
				  'debet'         => $totaldebit,
				  'kredit'        => 0,
				  'jenis_jurnal'  => 'wip finishgood',
				   'no_request'    => $no_request,
							  'stspos'		  =>1
				 );

				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
			echo "<hr>".$nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}

    public function daily_report($tanggal){
		$this->load->model('master_model');
		$dateC = $tanggal;
		$date = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));
		// echo $date; exit;
        // $date = date('2020-03-15'); 
		$Sum_real_harga_rp	= 0;
		$kurs=1;
		$sqlkurs="select * from ms_kurs where tanggal <='".$tanggal."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs	= $this->db->query($sqlkurs)->row();
		if(!empty($dtkurs)) $kurs=$dtkurs->kurs;
        $sqlHeader = "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' ";
        $restHeader = $this->db->query($sqlHeader)->result_array();
        if(!empty($restHeader)){
            // echo $sqlHeader;
            $ArrDay = array();
            $Sum_est_mat        = 0;
            $Sum_est_harga      = 0;
            $Sum_real_mat       = 0;
            $Sum_real_harga     = 0;
            $Sum_direct_labour  = 0;
            $Sum_in_labour      = 0;
            $Sum_machine        = 0;
            $Sum_mould_mandrill = 0;
            $Sum_consumable     = 0;
            $Sum_foh_consumable = 0;
            $Sum_foh_depresiasi = 0;
            $Sum_by_gaji        = 0;
            $Sum_by_non_pro     = 0;
            $Sum_by_rutin       = 0;
            foreach($restHeader AS $val=>$valx){
                $sqlCh = "SELECT jalur FROM production_header WHERE id_produksi='".$valx['id_produksi']."' ";
                $restCh		= $this->db->query($sqlCh)->result_array();
                $HelpDet 	= "estimasi_cost_and_mat";
                $HelpDet2 	= "banding_mat_pro";
                $HelpDet3 	= "bq_product";
                if($restCh[0]['jalur'] == 'FD'){
                    $HelpDet = "so_estimasi_cost_and_mat";
                    $HelpDet2 	= "banding_so_mat_pro";
                    $HelpDet3 	= "bq_product_fd";
                }

                $sqlBy 		= " SELECT
                                    b.diameter AS diameter,
                                    b.diameter2 AS diameter2,
                                    b.pressure AS pressure,
                                    b.liner AS liner,
                                    a.direct_labour AS direct_labour,
                                    a.indirect_labour AS indirect_labour,
                                    a.machine AS machine,
                                    a.mould_mandrill AS mould_mandrill,
                                    a.consumable AS consumable,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) AS biaya_gaji_non_produksi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) AS biaya_non_produksi,
                                    (
                                        ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                                    ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan 
                                FROM
                                    ".$HelpDet." a
                                    INNER JOIN ". $HelpDet3." b ON a.id_milik = b.id
                                WHERE id_milik='".$valx['id_milik']."' LIMIT 1";

                $restBy     = $this->db->query($sqlBy)->result_array();
                
                $sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$valx['id_production_detail']."' LIMIT 1";
                $restBan    = $this->db->query($sqlBan)->result_array();
                // echo $sqlEst."<br>";
                $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;
                
                $Sum_est_mat        += $restBan[0]['est_material'] * $jumTot;
                $Sum_est_harga      += $restBan[0]['est_harga'] * $jumTot;
                $Sum_real_mat       += $restBan[0]['real_material'];
                $Sum_real_harga     += $restBan[0]['real_harga'];
                $Sum_real_harga_rp  += $restBan[0]['real_harga_rp'];
                $Sum_direct_labour  += $restBy[0]['direct_labour'] * $jumTot;
                $Sum_in_labour      += $restBy[0]['indirect_labour'] * $jumTot;
                $Sum_machine        += $restBy[0]['machine'] * $jumTot;
                $Sum_mould_mandrill += $restBy[0]['mould_mandrill'] * $jumTot;
                $Sum_consumable     += $restBy[0]['consumable'] * $jumTot;
                $Sum_foh_consumable += $restBy[0]['foh_consumable'] * $jumTot;
                $Sum_foh_depresiasi += $restBy[0]['foh_depresiasi'] * $jumTot;
                $Sum_by_gaji        += $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $Sum_by_non_pro     += $restBy[0]['biaya_non_produksi'] * $jumTot;
                $Sum_by_rutin       += $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['id_produksi']            = $valx['id_produksi'];
                $ArrDay[$val]['id_category']            = $valx['id_category'];
                $ArrDay[$val]['id_product']             = $valx['id_product'];
                $ArrDay[$val]['diameter']               = $restBy[0]['diameter'];
                $ArrDay[$val]['diameter2']              = $restBy[0]['diameter2'];
                $ArrDay[$val]['pressure']               = $restBy[0]['pressure'];
                $ArrDay[$val]['liner']                  = $restBy[0]['liner'];
                $ArrDay[$val]['status_date']            = $valx['status_date'];
                $ArrDay[$val]['qty_awal']               = $valx['product_ke'];
                $ArrDay[$val]['qty_akhir']              = $valx['qty_akhir'];
                $ArrDay[$val]['qty']                    = $valx['qty'];
                $ArrDay[$val]['date']                   = $date;
                $ArrDay[$val]['id_production_detail']   = $valx['id_production_detail'];
                $ArrDay[$val]['id_milik']               = $valx['id_milik'];
                $ArrDay[$val]['est_material']           = $restBan[0]['est_material'] * $jumTot;
                $ArrDay[$val]['est_harga']              = $restBan[0]['est_harga'] * $jumTot;
                $ArrDay[$val]['real_material']          = $restBan[0]['real_material'];
                $ArrDay[$val]['real_harga']             = $restBan[0]['real_harga'];
                $ArrDay[$val]['real_harga_rp']			= $restBan[0]['real_harga_rp'];
                $ArrDay[$val]['kurs']                   = $kurs;

                $ArrDay[$val]['direct_labour']          = $restBy[0]['direct_labour'] * $jumTot;
                $ArrDay[$val]['indirect_labour']        = $restBy[0]['indirect_labour'] * $jumTot;
                $ArrDay[$val]['machine']                = $restBy[0]['machine'] * $jumTot;
                $ArrDay[$val]['mould_mandrill']         = $restBy[0]['mould_mandrill'] * $jumTot;
                $ArrDay[$val]['consumable']             = $restBy[0]['consumable'] * $jumTot;
                $ArrDay[$val]['foh_consumable']         = $restBy[0]['foh_consumable'] * $jumTot;
                $ArrDay[$val]['foh_depresiasi']         = $restBy[0]['foh_depresiasi'] * $jumTot;
                $ArrDay[$val]['biaya_gaji_non_produksi']= $restBy[0]['biaya_gaji_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_non_produksi']     = $restBy[0]['biaya_non_produksi'] * $jumTot;
                $ArrDay[$val]['biaya_rutin_bulanan']    = $restBy[0]['biaya_rutin_bulanan'] * $jumTot;

                $ArrDay[$val]['insert_by']              = 'system';
                $ArrDay[$val]['insert_date']            = date('Y-m-d H:i:s');

            }

            $ArrDayMonth = array(
                'date' => $date,
                'est_material' => $Sum_est_mat,
                'est_harga' => $Sum_est_harga,
                'real_material' => $Sum_real_mat,
                'real_harga' => $Sum_real_harga,
                'real_harga_rp' => $Sum_real_harga_rp,
                'kurs' => $kurs,
                'direct_labour' => $Sum_direct_labour,
                'indirect_labour' => $Sum_in_labour,
                'machine' => $Sum_machine,
                'mould_mandrill' => $Sum_mould_mandrill,
                'consumable' => $Sum_consumable,
                'foh_consumable' => $Sum_foh_consumable,
                'foh_depresiasi' => $Sum_foh_depresiasi,
                'biaya_gaji_non_produksi' => $Sum_by_gaji,
                'biaya_non_produksi' => $Sum_by_non_pro,
                'biaya_rutin_bulanan' => $Sum_by_rutin,
                'insert_by' => 'system',
                'insert_date' => date('Y-m-d H:i:s')

            );

            // echo "<pre>";
            // print_r($ArrDay);
            // print_r($ArrDayMonth);
            // exit;
            $this->db->trans_start();
                $this->db->delete('laporan_per_bulan', array('date' => $date));
                $this->db->delete('laporan_per_hari', array('date' => $date));

                $this->db->insert('laporan_per_bulan', $ArrDayMonth);
                $this->db->insert_batch('laporan_per_hari', $ArrDay);
				// agus : update production detail jurnal
				$this->upd_production_detail($date);
            $this->db->trans_complete();

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $ArrHistF	= array(
                    'date'			=> $date,
                    'status'		=> 'FAILED',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s')
                );
                $this->db->insert('laporan_status', $ArrHistF);
                echo "Failed Insert Data";
            }
            else{
                $this->db->trans_commit();
                $ArrHistS	= array(
                    'date'			=> $date,
                    'status'		=> 'SUCCESS',
                    'insert_by'		=> 'system',
                    'insert_date'	=> date('Y-m-d H:i:s') 
                );
                $this->db->insert('laporan_status', $ArrHistS);
                echo "Success Insert Data";
            }
        }
        else{
            $ArrHistE	= array(
                'date'			=> $date,
                'status'		=> 'EMPTY',
                'insert_by'		=> 'system',
                'insert_date'	=> date('Y-m-d H:i:s') 
            );
            $this->db->insert('laporan_status', $ArrHistE);
            echo "No Data Insert Data";
        }
	}

	public function upd_production_detail($tanggal){
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="select * from laporan_per_hari where qty_awal is not null and kurs >1 and `date`='".$tanggal."' order by `date`";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
		    foreach ($result as $data) {
				$id_milik=$data['id_milik'];
				$qty_awal=$data['qty_awal'];
				$qty_akhir=$data['qty_akhir'];
				$total_qty=($qty_akhir-$qty_awal+1);
				$material=$data['real_harga'];
				$dl=$data['direct_labour'];
				$idl=$data['indirect_labour'];
				$mch=$data['machine'];
				$mml=$data['mould_mandrill'];
				$csm=$data['consumable'];
				$fcsm=$data['foh_consumable'];
				$fdpr=$data['foh_depresiasi'];
				$bgnp=$data['biaya_gaji_non_produksi'];
				$bnp=$data['biaya_non_produksi'];
				$brb=$data['biaya_rutin_bulanan'];
				$wip_kurs=$data['kurs'];
				$wip_material=round($material*$wip_kurs/$total_qty);
				$wip_dl=round($dl*$wip_kurs/$total_qty);
				$wip_foh=round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs/$total_qty);
				$wip_il=round($idl*$wip_kurs/$total_qty);
				$wip_consumable=round($csm*$wip_kurs/$total_qty);
				$finish_good=($wip_material+$wip_dl+$wip_foh+$wip_il+$wip_consumable);
				$this->db->query("update production_detail set wip_kurs='".$wip_kurs."', wip_material='".$wip_material."', wip_dl='".$wip_dl."', wip_foh='".$wip_foh."', wip_il='".$wip_il."', wip_consumable='".$wip_consumable."', finish_good='".$finish_good."' where id_milik ='".$id_milik."' and product_ke between ".$qty_awal." and ".$qty_akhir."");
				$nomor++;
		// jurnal
				$tgl_po=$data['date'];
				$tgl_voucher=$data['date'];
				$total=round(($mch+$mml+$fdpr+$brb+$fcsm+$material+$dl+$idl+$csm)*$wip_kurs);
				$keterangan='GD. PRODUKSI - WIP ( '.$tgl_po.' )';
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $total, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV004';
				$datajurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				foreach($datajurnal AS $record) {
					$posisi = $record->posisi;
					$nokir  = $record->no_perkiraan;
					$parameter_no = $record->parameter_no;
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Material",
						  'no_reff'       => $reff,
						  'debet'         => round($material*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Direct Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($dl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Indirect Labour",
						  'no_reff'       => $reff,
						  'debet'         => round($idl*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP Consumable",
						  'no_reff'       => $reff,
						  'debet'         => round($csm*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => "WIP FOH",
						  'no_reff'       => $reff,
						  'debet'         => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'jurnal_laporan_produksi',
						  'no_request'    => $reff,
						  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						if($parameter_no=="1") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "GUDANG PRODUKSI (Material)",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($material*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="2") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "DIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($dl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="3") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "INDIRECT LABOUR PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($idl*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="4") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "CONSUMABLE PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round($csm*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							   'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
						if($parameter_no=="5") {
							$det_Jurnaltes[]  = array(
							  'nomor'         => '',
							  'tanggal'       => $tgl_voucher,
							  'tipe'          => 'JV',
							  'no_perkiraan'  => $nokir,
							  'keterangan'    => "FOH PC LIABILITIES",
							  'no_reff'       => $reff,
							  'debet'         => 0,
							  'kredit'        => round(($mch+$mml+$fdpr+$brb+$fcsm)*$wip_kurs),
							  'jenis_jurnal'  => 'jurnal_laporan_produksi',
							  'no_request'    => $reff,
							  'stspos'		  =>1
							 );
						}
					}
				}
				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
		}		
	}
	function update_fg_to_transit() {
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE category = 'delivery' order by tanggal";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$id=$data['id'];
				$coa=$data['coa'];
				$no_request = $id;
				$no_so=$data['no_so'];
				$product=$data['product'];
				$no_spk=$data['id_spk'];
				$no_surat_jalan=$data['no_surat_jalan'];
				$id_milik=$data['id_milik'];
				$id_detail=$data['id_detail'];
				$coa_fg=$data['coa_fg'];
				$dt_pd=$this->db->query("select * from production_detail where id_milik ='".$id_milik."' and id='".$id_detail."'")->row();
				if(empty($dt_pd)) continue;
				$wip_material=$dt_pd->wip_material;
				$wip_dl=$dt_pd->wip_dl;
				$wip_foh=$dt_pd->wip_foh;
				$wip_il=$dt_pd->wip_il;
				$wip_consumable=$dt_pd->wip_consumable;
				$finish_good=$dt_pd->finish_good;
				$totalall=$finish_good;
				$nomor++;
				echo $id_detail."<br>";

		// jurnal
				$tgl_po=$data['tanggal'];
				$tgl_voucher=$data['tanggal'];
				$keterangan='FINISH GOOD - TRANSIT ('.$no_so.' - '.$product.' - '.$no_surat_jalan.') '.$id_detail;
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id_detail'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $finish_good, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV006';

				$masterjurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				foreach($masterjurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;
					$no_voucher = $reff;
					$param  = 'id';
					if ($posisi=='D'){
						$value_param  = $id;
						$val = $this->Acc_model->GetData($tabel,$field,$param,$value_param);
						$nilaibayar = $val[0]->$field;
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan,
						  'no_reff'       => $reff,
						  'debet'         => $totalall,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'finish good intransit',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						$coa = 	$this->db->query("SELECT a.*, b.coa,b.coa_fg FROM jurnal_product a join product_parent b on a.product=b.product_parent WHERE a.id ='$id'")->result();
						$nokir=$coa[0]->coa_fg;
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan,
						  'no_reff'       => $reff,
						  'debet'         => 0,
						  'kredit'        => $totalall,
						  'jenis_jurnal'  => 'finish good intransit',
						   'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
				}

				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
			echo "<hr>".$nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}
	function update_transit_to_customer() {
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');
        $session = $this->session->userdata('app_session');
		$data_session	= $this->session->userdata;
		// update jurnal gudang produksi ke wip berdasarkan laporan produksi
		$sql="SELECT * FROM jurnal_product WHERE category = 'diterima customer' and approval_by <>'' and status_jurnal=0 order by tanggal limit 1000";
		$result	= $this->db->query($sql)->result_array();
        if(!empty($result)){
			$nomor=0;
			$this->db->trans_start();
		    foreach ($result as $data) {
				$id=$data['id'];
				$no_request = $id;
				$no_so=$data['no_so'];
				$product=$data['product'];
				$no_spk=$data['id_spk'];
				$no_surat_jalan=$data['no_surat_jalan'];
				$id_milik=$data['id_milik'];
				$id_detail=$data['id_detail'];
				$dt_pd=$this->db->query("select * from production_detail where id_milik ='".$id_milik."' and id='".$id_detail."'")->row();
				if(empty($dt_pd)) continue;
				$wip_material=$dt_pd->wip_material;
				$wip_dl=$dt_pd->wip_dl;
				$wip_foh=$dt_pd->wip_foh;
				$wip_il=$dt_pd->wip_il;
				$wip_consumable=$dt_pd->wip_consumable;
				$finish_good=$dt_pd->finish_good;
				$totalall=$finish_good;
				$nomor++;
				echo $id_detail."<br>";
				$this->db->query("update jurnal_product set status_jurnal=1 WHERE category = 'diterima customer' and id ='".$id."'");

		// jurnal
				$tgl_po=$data['tanggal'];
				$tgl_voucher=$data['tanggal'];
				$keterangan='TRANSIT - CUSTOMER ('.$no_so.' - '.$product.' - '.$no_surat_jalan.') '.$id_detail;
				$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_po);
				$reff=$data['id_detail'];
				$Bln	= substr($tgl_po,5,2);
				$Thn	= substr($tgl_po,0,4);
				$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_po, 'jml' => $finish_good, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $keterangan, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => 'system', 'memo' => $reff, 'tgl_jvkoreksi' => $tgl_po, 'ho_valid' => '');
				$this->db->insert(DBACC.'.javh',$dataJVhead);
				$kodejurnal='JV007';

				$masterjurnal  	 = $this->Acc_model->GetTemplateJurnal($kodejurnal);
				foreach($masterjurnal AS $record){
					$tabel  = $record->menu;
					$posisi = $record->posisi;
					$field  = $record->field;
					$nokir  = $record->no_perkiraan;
					$no_voucher = $reff;
					$param  = 'id';
					if ($posisi=='D'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan,
						  'no_reff'       => $id,
						  'debet'         => $totalall,
						  'kredit'        => 0,
						  'jenis_jurnal'  => 'intransit incustomer',
						  'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					} elseif ($posisi=='K'){
						$det_Jurnaltes[]  = array(
						  'nomor'         => '',
						  'tanggal'       => $tgl_voucher,
						  'tipe'          => 'JV',
						  'no_perkiraan'  => $nokir,
						  'keterangan'    => $keterangan,
						  'no_reff'       => $id,
						  'debet'         => 0,
						  'kredit'        => $totalall,
						  'jenis_jurnal'  => 'intransit incustomer',
						   'no_request'    => $no_request,
							  'stspos'		  =>1
						 );
					}
				}

				$this->db->insert_batch('jurnaltras',$det_Jurnaltes);
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
						'created_by'	=> 'system',
						'created_on'	=> date("Y-m-d"),
						);
					$this->db->insert(DBACC.'.jurnal',$datadetail);
				}
				unset ($det_Jurnaltes);
			}
			echo "<hr>".$nomor;
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}
	function outstanding_so(){
		// 'IPP19019L','IPP20972L','IPP210011L','IPP210021L','IPP210061E','IPP20975L','IPP210079L','IPP210098L','IPP20530L','IPP210117L','IPP210108L','IPP210096L','IPP20546L','IPP20750E','IPP20874L','IPP210114L','IPP210144L','IPP210135L','IPP20933L','IPP210136L','IPP20986L','IPP210120L','IPP210151L','IPP210193E','IPP210094L','IPP210216E','IPP20904L','IPP210158L','IPP210191L','IPP20713L','IPP20712L','IPP210204L','IPP210163L','IPP210234L','IPP210246L','IPP210085E','IPP210213L','IPP210208E','IPP210282E','IPP210283E','IPP20711L','IPP210305L','IPP210236E','IPP210245L','IPP210097L','IPP210084E','IPP210284L','IPP210249L','IPP210325L','IPP210354L','IPP210285L','IPP210344L','IPP210278L','IPP210306L','IPP210224L','IPP210292L','IPP210129E','IPP210264L','IPP210248L','IPP210299L','IPP210197L','IPP210387L','IPP210422E','IPP210390L','IPP210415L','IPP210340L','IPP210430L','IPP210150L','IPP210323L','IPP210324L','IPP210347L','IPP210437E','IPP210369L','IPP210442L','IPP210412L','IPP210467L','IPP210348L','IPP20679L','IPP210287L','IPP210501L','IPP210465L','IPP210423E','IPP210428L','IPP210443L','IPP210445L','IPP210446L','IPP20942E','IPP210389L','IPP210537E','IPP210486E','IPP210543E','IPP210490L','IPP210531E','IPP210493L','IPP210515L','IPP210483L','IPP210517L','IPP210565L','IPP210506E','IPP210319L','IPP210579L','IPP210441E','IPP210576E','IPP210538L','IPP210519L','IPP210484L','IPP210550L','IPP210275L','IPP210072L','IPP210402L','IPP210073L','IPP210403L','IPP210074L','IPP20939L','IPP210558L','IPP210489L','IPP20927E','IPP210568L','IPP210513L','IPP210548E','IPP210503E','IPP210602E','IPP210504E','IPP210371L','IPP210541L','IPP210608L','IPP210479L','IPP210626L','IPP210625L','IPP210646L','IPP210601L','IPP210635L','IPP210566E','IPP210574L','IPP210652L','IPP210413L','IPP210577E','IPP210334L','IPP210699E','IPP210658L','IPP210002L','IPP210661L','IPP210481L','IPP210600E','IPP210474L','IPP210688L','IPP210721L','IPP210740L','IPP210745L','IPP210750L','IPP210746L','IPP210732L','IPP210444E','IPP210779L','IPP210222L','IPP210712L','IPP210223L','IPP210781L','IPP210780E','IPP210792L','IPP210664L','IPP210653L','IPP210795L','IPP210737L','IPP210657E','IPP210747L','IPP210810L','IPP210805E','IPP210701E','IPP210804L','IPP210796E','IPP210833L','IPP210767L','IPP210647L','IPP210631L','IPP210827L','IPP210521E','IPP210684L','IPP210765L','IPP210865E','IPP210809L','IPP210250L','IPP210861L','IPP210733L','IPP210808L','IPP210799L','IPP210184E','IPP210841E','IPP210866E','IPP210879E','IPP20214E','IPP210895E','IPP210927L','IPP210834L','IPP210693L','IPP210889L','IPP210546L','IPP210928E','IPP210685L','IPP210916E','IPP20753L','IPP210405L','IPP210948E','IPP210464L','IPP210949E','IPP210950E','IPP210683L','IPP210992E','IPP210998L','IPP210929E','IPP210952L','IPP210953L','IPP211011E','IPP210770L','IPP210991E','IPP211025L','IPP210934L','IPP211008L','IPP210858L','IPP211035L','IPP220012L','IPP210774L','IPP210790E','IPP211031E','IPP210886L','IPP220052L','IPP220037E','IPP220057L','IPP211020E','IPP220091L','IPP220058E','IPP210851E','IPP210643L','IPP20012L','IPP220131L','IPP220172L','IPP220186L','IPP220193L','IPP220171L','IPP220258L','IPP220220E','IPP220293L','IPP220121L','IPP220201L','IPP220385L','IPP220393L','IPP220388L','IPP220430L','IPP220460L','IPP220473L','IPP220453L','IPP220511L','IPP220514L','IPP220522L','IPP220544L','IPP220591L','IPP19498E','IPP210373L','IPP230051L','IPP230031L','IPP230056L','IPP230059L','IPP230054L','IPP230086E','IPP20727L','IPP20288L','IPP230113L','IPP230078L','IPP230128E','IPP230121L','IPP230125L','IPP230126L','IPP20123E','IPP230145L','IPP230159L','IPP230181E','IPP230179E','IPP230182E','IPP230176E','IPP230167L','IPP230168L','IPP230057L','IPP230193E','IPP230192E','IPP230178L','IPP230177L','IPP230175L','IPP230083L','IPP230055E','IPP230169L','IPP230170L','IPP220213L','IPP230171L','ISO230001L','ISO230002L','ISO230003L','ISO230004L','ISO230005L','IPP230211E','IPP230212E','IPP230213L','IPP230204L','IPP230087E','IPP230165E','IPP230103L','IPP210754L','IPP230241L','IPP210466L','IPP220439L','IPP220251L','IPP220493L','IPP220494L','IPP220502L','IPP210630L','IPP210888L','IPP210461L','IPP210462L','IPP210755L','IPP210870L','IPP210639L','IPP220435L','IPP230006L','IPP220584L','IPP230240L','IPP230207L','IPP230250E','IPP230260L','IPP230251L','IPP230252L','IPP230253L','ISO230007L','IPP230248L','IPP220519L','IPP220151L','IPP230139L','IPP230308L','IPP230307L','IPP230302L','IPP230304L','IPP230305L','IPP230238E','IPP230198L','IPP220256L','IPP230306L','IPP230208E','IPP230209E','IPP230314E','IPP230315E','IPP230210L','IPP230320L','IPP230082L','IPP230322E','IPP220560L','IPP220391L','IPP230036L','IPP220419L','IPP220168L','IPP220452L','IPP220513L','IPP230317L','IPP230331L','IPP230330L','IPP220249E','IPP230309L','IPP230338L','IPP230337L','IPP230336L','IPP230339E','IPP230067L','IPP230342L','IPP230360E','IPP230344L','IPP230376L','IPP230375L','IPP230348L','IPP230349L','IPP230350L','IPP230363L','IPP230366L','IPP230401L','IPP230406E','IPP230392L','IPP230374L','IPP230391L','IPP230390L','IPP230380L','IPP230382L','IPP230383L','IPP230385L','IPP230418L','IPP230416L','IPP230388L','IPP230422L','IPP230110E','IPP230426L','IPP230419E','IPP230387L','IPP230389L','IPP230381L','IPP230379L','IPP230409L','IPP230395L','IPP230378L','IPP230440E','IPP230454L','IPP230455E','IPP220476L','IPP230194E','IPP230300L','IPP230372L','IPP230370L','IPP230462L','IPP230475E','IPP230112L','IPP230368E','IPP230476L','IPP220402E','IPP230503L','IPP230492L','IPP230502L','IPP230531E','IPP230530E','IPP230507E','IPP230523L','IPP230522L','IPP230480L','IPP230514L','IPP230586L','IPP230593L','IPP230594E','IPP230386L','IPP230606L','IPP230604L','IPP230618L','IPP230635E','IPP230613L','IPP230602E','IPP230650L','IPP230639E','IPP230655E','IPP230660L','IPP230667E','IPP230483L','IPP230407L','IPP230680L','IPP230521E','IPP230673L','IPP230674E','IPP230685L','IPP230662L','IPP230698L','IPP230547L'
		//$result=$this->db->query(" select a.*, b.so_number from billing_so a left join so_number b on a.no_ipp=REPLACE(id_bq,'BQ-','') where year(updated_date)<2024 order by a.no_po,a.no_ipp,a.updated_date")->result_array();
		$result=$this->db->query(" select a.*, b.so_number from billing_so a left join so_number b on a.no_ipp=REPLACE(id_bq,'BQ-','') where a.no_ipp in ('IPP230378L','IPP230586L','IPP230514L','IPP210413L','IPP230389L','IPP230375L','IPP230349L','IPP220476L','IPP230492L','IPP230502L','IPP230673L','IPP230407L','IPP230391L','IPP230685L','IPP230392L','IPP230426L','IPP220494L','IPP230406E','IPP220402E','IPP220249E','IPP220151L','IPP230386L','IPP230308L','IPP230300L','IPP220256L','IPP230360E','IPP220388L','IPP230376L','IPP230368E','IPP230418L','IPP230662L','IPP230165E','IPP230602E','IPP230639E','IPP230521E','IPP230674E') order by a.no_po,a.no_ipp,a.updated_date")->result_array();		
		echo "<table border=1><tr><td nowrap>NO IPP</td><td nowrap>NO SO</td><td nowrap>NO PO</td><td nowrap>CUSTOMER</td><td nowrap>TGL IPP</td><td nowrap>CURR</td><td nowrap>NILAI SO IDR</td><td nowrap>NILAI SO USD</td>
		<td nowrap>No Invoice</td><td nowrap>TGL INVOICE</td><td nowrap>TIPE</td><td nowrap>DPP</td><td nowrap>DP</td><td nowrap>RETENSI</td><td nowrap>PPN</td><td nowrap>RETENSI PPN</td>
		<td nowrap>TOTAL INVOICE</td>
		</tr>"; 
		foreach ($result as $data) {
			$currency=$data['base_cur'];
			$no_ipp=$data['no_ipp'];
			echo "<tr><td nowrap>".$data['no_ipp']."</td><td nowrap>".$data['so_number']."</td><td>".$data['no_po']."</td>
			<td>".$data['nm_customer']."</td><td>".$data['updated_date']."</td><td>".$currency."</td>";
			if($currency=='USD'){
				echo "<td>0</td>";
				echo "<td>".$data['total_deal_usd']."</td>";
			}else{
				echo "<td>".$data['total_deal_idr']."</td>";
				echo "<td>0</td>";
			}
//			echo "<td>".$data['total_deal_idr']."</td>";
//			echo "<td>".$data['total_deal_usd']."</td>";
			echo "<td colspan='9'></td></tr>";
			$dtinvoice=$this->db->query(" select * from bakcup_erp_db.tr_invoice_header where no_ipp like '%".$no_ipp."%' and Year(tgl_invoice)<2024")->result_array();
			foreach ($dtinvoice as $invdetail) {
				$no_ippd=$invdetail['no_ipp'];
				$base_cur=$invdetail['base_cur'];
				$no_invoice=$invdetail['no_invoice'];
				$jenis_invoice=$invdetail['jenis_invoice'];
				echo "<tr><td colspan=8>".$base_cur." - ".$no_ippd."</td><td>".$no_invoice."</td><td>".$invdetail['tgl_invoice']."</td><td>".$jenis_invoice."</td>";
				if($currency=='USD'){
					echo "<td>".$invdetail['total_dpp_usd']."</td>";
					echo "<td>".$invdetail['total_um']."</td>";
					echo "<td>".$invdetail['total_retensi']."</td>";
					echo "<td>".$invdetail['total_ppn']."</td>";
					echo "<td>".$invdetail['total_retensi2']."</td>";
					echo "<td>".$invdetail['total_invoice']."</td>";
				}else{
					echo "<td>".$invdetail['total_dpp_rp']."</td>";
					echo "<td>".$invdetail['total_um_idr']."</td>";
					echo "<td>".$invdetail['total_retensi_idr']."</td>";
					echo "<td>".$invdetail['total_ppn_idr']."</td>";
					echo "<td>".$invdetail['total_retensi2_idr']."</td>";
					echo "<td>".$invdetail['total_invoice_idr']."</td>";
				}
				echo "</tr>";
			}
		}
		echo "</table>";
	}
	function outstanding_dp_po(){
		$result=$this->db->query("SELECT * FROM sentralsistem.purchase_order_request_payment_nm where tipe='TR-01' and no_payment <>'';")->result_array();
		echo "<table border=1><tr><td>NO PO</td><td>NILAI DP</td><td>TERIMA BARANG</td>
		</tr>"; 
		foreach ($result as $data) {
			$no_po=$data['no_po'];
			echo "<tr><td>".$data['no_po']."</td>";
			echo "<td>".number_format($data['nilai_po_invoice'])."</td>";
			echo "<td></td></tr>";
			$dtinvoice=$this->db->query("SELECT sum(total_harga_product) as bayar,kode_trans,no_ipp FROM warehouse_adjustment where category in ( 'incoming rutin','incoming non rutin','incoming asset') and year(tanggal)<2024 and no_ipp='".$no_po."' group by kode_trans,no_ipp ")->result_array();
		  if(!empty($dtinvoice)){
			foreach ($dtinvoice as $invdetail) {
				$bayar=$invdetail['bayar'];
				echo "<tr><td colspan=2 align=right>".$invdetail['kode_trans']."</td><td>".number_format($bayar)."</td>";
				echo "</tr>";
			}
		  }
		}
		echo "</table>";
	}
	function outstanding_dp_invoice(){
		$result=$this->db->query("select * from tr_invoice_header where jenis_invoice='uang muka' and year(tgl_invoice)<2024 order  by so_number")->result_array();
		echo "<table border=1><tr><td>CUSTOMER</td><td>NO PO</td><td>NO IPP</td><td>NO SO</td><td>NO INVOICE</td><td>CURR</td><td>NILAI DP US</td><td>NILAI DP IDR</td><td>DIPAKAI USD</td>
		<td>DIPAKAI IDR</td>
		</tr>";
		foreach ($result as $data) {
			$no_ipp=$data['no_ipp'];
			echo "<tr><td>".$data['nm_customer']."</td>";
			echo "<td>".($data['no_po'])."</td>";
			echo "<td>".($data['no_ipp'])."</td>";
			echo "<td>".($data['so_number'])."</td>";
			echo "<td>".($data['no_invoice'])."</td>";
			echo "<td>".($data['base_cur'])."</td>";
			$curr=$data['base_cur'];
			echo "<td>".($curr=='USD'?number_format($data['total_dpp_usd']):'')."</td>";
			echo "<td>".($curr=='IDR'?number_format($data['total_dpp_rp']):'')."</td>";
			echo "<td colspan=2></td></tr>";
			$expipp=explode(',', $no_ipp);
			$wheresql=array();
			foreach ($expipp as $val) {
				$wheresql[]="no_ipp like '%".$val."%'";
			}
			$ippwhere=implode(" or ",$wheresql);
			$sqlquery="select * from tr_invoice_header where jenis_invoice='progress' and year(tgl_invoice)<2024 and (".$ippwhere.")";
			$dtinvoice=$this->db->query($sqlquery)->result_array();
		  if(!empty($dtinvoice)){
			$sumtotal=0;$sumtotalidr=0;
			foreach ($dtinvoice as $invdetail) {
				$base_cur=$invdetail['base_cur'];
				$total_um=$invdetail['total_um'];
				$total_um_idr=$invdetail['total_um_idr'];
				$sumtotal=($sumtotal+$total_um);
				$sumtotalidr=($sumtotalidr+$total_um_idr);
/*
				echo "<tr><td colspan=6 align=right>".$invdetail['base_cur']."</td>";
				echo "<td>".($base_cur=='USD'?number_format($total_um):'')."</td>";
				echo "<td>".($base_cur=='IDR'?number_format($total_um_idr):'')."</td>";
				echo "</tr>";
*/
			}
			echo "<tr><td colspan=6 align=right>TOTAL</td><td>".($base_cur=='USD'?number_format($sumtotal):'')."</td>
			<td>".($base_cur=='IDR'?number_format($sumtotalidr):'')."</td>";
			echo "</tr>";
		  }
		}
		echo "</table>";
	}
	function cek_payment_po(){
		// cek po sudah di bayar atau masih direquest
		$result=$this->db->query("SELECT * FROM purchase_order_request_payment_nm")->result_array();
		echo "<table border=1><tr style='background-color:yellow'><td>NO PO NON MATERIAL</td><td>NOMOR PAYMENT</td><td>TGL PAYMENT</td><td>NO REQUEST</td>
		</tr>"; 
		foreach ($result as $data) {
			$no_po=$data['no_po'];
			echo "<tr><td>".$data['no_po']."</td>";
			echo "<td>".$data['no_payment']."</td>";
			if($data['no_payment']!="") {
				$resultpo=$this->db->query("SELECT id,payment_date FROM purchase_order_request_payment_header where no_payment='".$data['no_payment']."'")->row();
				echo "<td><a href='".base_url("pembayaran_material/view_payment_new_nonmaterial/".$resultpo->id)."' target='_blank'>".$resultpo->payment_date."</td><td></td>";				
			}else{
				echo "<td></td><td><a href='".base_url("pembayaran_material/view_request/".$data['id'])."/2' target='_blank'>".$data['no_request']."</td>";
			}
			echo "</tr>";
		}
		$result=$this->db->query("SELECT * FROM purchase_order_request_payment")->result_array();
		echo "<tr style='background-color:red'><td>NO PO MATERIAL</td><td>NOMOR PAYMENT</td><td>TGL PAYMENT</td>
		</tr>"; 
		foreach ($result as $data) {
			$no_po=$data['no_po'];
			echo "<tr><td>".$data['no_po']."</td>";
			echo "<td>".$data['no_payment']."</td>";
			if($data['no_payment']!="") {
				$resultpo=$this->db->query("SELECT id,payment_date FROM purchase_order_request_payment_header where no_payment='".$data['no_payment']."'")->row();
				echo "<td><a href='".base_url("pembayaran_material/view_payment_new/".$resultpo->id)."' target='_blank'>".$resultpo->payment_date."</td><td></td>";				
			}else{
				echo "<td></td><td><a href='".base_url("pembayaran_material/view_request/".$data['id'])."/1' target='_blank'>".$data['no_request']."</td>";
			}
			echo "</tr>";
		}

		echo "</table>";
	}
	function list_so_produk_kosong(){
		$result=$this->db->query(" select a.*, b.so_number from billing_so a left join so_number b on a.no_ipp=REPLACE(id_bq,'BQ-','') where year(updated_date)<2024 and id_bq in ('BQ-IPP210412L','BQ-IPP210501L','BQ-IPP210486E','BQ-IPP210493L','BQ-IPP210579L','BQ-IPP210576E','BQ-IPP210402L','BQ-IPP210568L','BQ-IPP210413L','BQ-IPP210002L','BQ-IPP210600E','BQ-IPP210688L','BQ-IPP19286L','BQ-IPP19286L','BQ-IPP20660E','BQ-IPP20302L','BQ-IPP210222L','BQ-IPP210827L','BQ-IPP210865E','BQ-IPP210653L','BQ-IPP210652L','BQ-IPP210733L','BQ-IPP210405L','BQ-IPP211011E','BQ-IPP220012L','BQ-IPP220052L','BQ-IPP220037E','BQ-IPP220057L','BQ-IPP210643L','BQ-IPP220091L','BQ-IPP220131L','BQ-IPP220168L','BQ-IPP220186L','BQ-IPP220172L','BQ-IPP220171L','BQ-IPP220193L','BQ-IPP220258L','BQ-IPP220293L','BQ-IPP220385L','BQ-IPP220393L','BQ-IPP220391L','BQ-IPP220419L','BQ-IPP220430L','BQ-IPP220460L','BQ-IPP220473L','BQ-IPP220452L','BQ-IPP220494L','BQ-IPP220513L','BQ-IPP220514L','BQ-IPP220522L','BQ-IPP220544L','BQ-IPP220560L','BQ-IPP230036L','BQ-IPP230051L','BQ-IPP230031L','BQ-IPP230056L','BQ-IPP230057L','BQ-IPP230055E','BQ-IPP230054L','BQ-IPP230059L','BQ-IPP230086E','BQ-IPP230113L','BQ-IPP230128E','BQ-IPP230078L','BQ-IPP230121L','BQ-IPP230145L','BQ-IPP230126L','BQ-IPP230167L','BQ-IPP230182E','BQ-IPP230176E','BQ-IPP230179E','BQ-IPP230181E','BQ-IPP230168L','BQ-IPP230159L','BQ-IPP230175L','BQ-IPP230177L','BQ-IPP230178L','BQ-IPP230192E','BQ-IPP230193E','BQ-IPP230207L','BQ-IPP230211E','BQ-IPP230212E','BQ-IPP230087E','BQ-IPP230213L','BQ-IPP230241L','BQ-IPP230240L','BQ-IPP230251L','BQ-IPP230252L','BQ-IPP230253L','BQ-IPP230308L','BQ-IPP230302L','BQ-IPP230304L','BQ-IPP230307L','BQ-IPP230260L','BQ-IPP230198L','BQ-IPP230315E','BQ-IPP230314E','BQ-IPP230305L','BQ-IPP230210L','BQ-IPP230208E','BQ-IPP230209E','BQ-IPP230331L','BQ-IPP230330L','BQ-IPP230322E','BQ-IPP230317L','BQ-IPP230336L','BQ-IPP230338L','BQ-IPP230339E','BQ-IPP230360E','BQ-IPP230348L','BQ-IPP230363L','BQ-IPP230406E','BQ-IPP230392L','BQ-IPP230391L','BQ-IPP230401L','BQ-IPP230374L','BQ-IPP230380L','BQ-IPP230390L','BQ-IPP230337L','BQ-IPP230395L','BQ-IPP230409L','BQ-IPP230426L','BQ-IPP230110E','BQ-IPP230349L','BQ-IPP230375L','BQ-IPP230376L','BQ-IPP230387L','BQ-IPP230389L','BQ-IPP230462L','BQ-IPP230476L','BQ-IPP230507E','BQ-IPP230522L','BQ-IPP230523L','BQ-IPP230514L','BQ-IPP230586L','BQ-IPP230606L','BQ-IPP230618L','BQ-IPP230613L','BQ-IPP230667E','BQ-IPP230407L','BQ-IPP230483L','BQ-IPP230680L','BQ-IPP230698L','BQ-IPP230547L','BQ-IPP240040L','BQ-IPP240006E') order by a.no_po,a.no_ipp,a.updated_date")->result_array();		
		echo "<table border=1><tr><td nowrap>NO IPP</td><td nowrap>NO SO</td><td nowrap>NO PO</td><td nowrap>CUSTOMER</td><td nowrap>TGL IPP</td><td nowrap>CURR</td><td nowrap>NILAI SO IDR</td><td nowrap>NILAI SO USD</td>
		<td nowrap>No Invoice</td><td nowrap>TGL INVOICE</td><td nowrap>TIPE</td><td nowrap>DPP</td><td nowrap>DP</td><td nowrap>RETENSI</td><td nowrap>PPN</td><td nowrap>RETENSI PPN</td>
		<td nowrap>TOTAL INVOICE</td>
		</tr>"; 
		foreach ($result as $data) {
			$currency=$data['base_cur'];
			$no_ipp=$data['no_ipp'];
			echo "<tr><td nowrap>".$data['no_ipp']."</td><td nowrap>".$data['so_number']."</td><td>".$data['no_po']."</td>
			<td>".$data['nm_customer']."</td><td>".$data['updated_date']."</td><td>".$currency."</td>";
/*
			if($currency=='USD'){
				echo "<td>".$data['total_deal_usd']."</td>";
				echo "<td>".$data['total_deal_idr']."</td>";
			}else{
				echo "<td>".$data['total_deal_idr']."</td>";
				echo "<td>".$data['total_deal_usd']."</td>";
			}
*/
			echo "<td>".$data['total_deal_idr']."</td>";
			echo "<td>".$data['total_deal_usd']."</td>";
			echo "<td colspan='9'></td></tr>";
			$dtinvoice=$this->db->query(" select * from bakcup_erp_db.tr_invoice_header where no_ipp like '%".$no_ipp."%' and Year(tgl_invoice)<2024")->result_array();
			foreach ($dtinvoice as $invdetail) {
				$no_ippd=$invdetail['no_ipp'];
				$base_cur=$invdetail['base_cur'];
				$no_invoice=$invdetail['no_invoice'];
				$jenis_invoice=$invdetail['jenis_invoice'];
				echo "<tr><td colspan=8>".$base_cur." - ".$no_ippd."</td><td>".$no_invoice."</td><td>".$invdetail['tgl_invoice']."</td><td>".$jenis_invoice."</td>";
				if($currency=='USD'){
					echo "<td>".$invdetail['total_dpp_usd']."</td>";
					echo "<td>".$invdetail['total_um']."</td>";
					echo "<td>".$invdetail['total_retensi']."</td>";
					echo "<td>".$invdetail['total_ppn']."</td>";
					echo "<td>".$invdetail['total_retensi2']."</td>";
					echo "<td>".$invdetail['total_invoice']."</td>";
				}else{
					echo "<td>".$invdetail['total_dpp_rp']."</td>";
					echo "<td>".$invdetail['total_um_idr']."</td>";
					echo "<td>".$invdetail['total_retensi_idr']."</td>";
					echo "<td>".$invdetail['total_ppn_idr']."</td>";
					echo "<td>".$invdetail['total_retensi2_idr']."</td>";
					echo "<td>".$invdetail['total_invoice_idr']."</td>";
				}
				echo "</tr>";
			}
		}
		echo "</table>";
	}
	function update_history_warehouse_stok_perday(){
		$sql="SELECT * FROM warehouse_stock_per_day where DATE(hist_date) = '2024-02-16' order by id_material,id_gudang";
		$result	= $this->db->query($sql)->result();$i=0;
        if(!empty($result)){
		    foreach ($result as $keys) {
				$i++;
				//$this->db->query("update warehouse_stock_per_day set costbook='".$keys->costbook."', total_value='".$keys->total_value."' where id_material='".$keys->id_material."' and id_gudang='".$keys->id_gudang."' and DATE(hist_date) = '2023-12-31' ");
			}
			echo $i." updated";
		}
	}
	function insert_kartu_unbill(){
		$sql="select a.*,b.keterangan info from ".DBACC.".jurnal a join ".DBACC.".javh b on a.nomor=b.nomor where a.no_perkiraan='2101-01-03' and a.kredit>0 and a.nomor not in (select nomor from tr_kartu_hutang) order by nomor;";
		$result	= $this->db->query($sql)->result();$i=0;
		echo "<table border=1><tr><th>Nomor</th><th>Tanggal</th><th>COA</th><th>Keterangan</th><th>NO PO</th><th>Kredit</th><th>ID Supplier</th><th>Nama Supplier</th><th>No Request</th></tr>";
		$ArrKartu = array();
        if(!empty($result)){
		    foreach ($result as $keys) {
				$no_po='';
				$curr='';
				$id_supp='';
				$nm_supp='';
				if(substr($keys->no_reff,0,3)=='TRS' || substr($keys->no_reff,0,3)=='TRN'){
					$dt_warehouse = $this->db->query("select * from warehouse_adjustment where kode_trans='".$keys->no_reff."'")->row();
					if(!empty($dt_warehouse)) {
						$no_po=$dt_warehouse->no_ipp;
						$dt_supp = $this->db->query("select * from tran_po_header where no_po='".$no_po."'")->row();
						$id_supp=$dt_supp->id_supplier;
						$nm_supp=$dt_supp->nm_supplier;
						$curr=$dt_supp->mata_uang;
					}
				}else{
					$dt_warehouse = $this->db->query("select * from report_of_shipment where id='".$keys->no_reff."'")->row();
					if(!empty($dt_warehouse)) {
						$no_po=$dt_warehouse->id_po;
						$dt_supp = $this->db->query("select * from tran_material_po_header where no_po='".$no_po."'")->row();
						$id_supp=$dt_supp->id_supplier;
						$nm_supp=$dt_supp->nm_supplier;
						$curr=$dt_supp->mata_uang;
					}
				}
				if($curr=='IDR'){
					echo "<tr><td>".$keys->nomor."</td><td>".$keys->tanggal."</td><td>".$keys->no_perkiraan."</td><td>".$keys->info."</td><td>".$no_po."</td><td>".$keys->kredit."</td><td>".$id_supp."</td><td>".$nm_supp."</td><td>".$keys->no_reff."</td></tr>";
					$i++;
					$ArrKartu[$i]['tipe']='JV';
					$ArrKartu[$i]['debet']='0';
					$ArrKartu[$i]['nomor']=$keys->nomor;
					$ArrKartu[$i]['tanggal']=$keys->tanggal;
					$ArrKartu[$i]['no_perkiraan']=$keys->no_perkiraan;
					$ArrKartu[$i]['keterangan']=$keys->info;
					$ArrKartu[$i]['no_reff']=$no_po;
					$ArrKartu[$i]['kredit']=$keys->kredit;
					$ArrKartu[$i]['stspos']='0';
					$ArrKartu[$i]['jenis_trans']='GENERATESYS';
					$ArrKartu[$i]['id_supplier']=$id_supp;
					$ArrKartu[$i]['nama_supplier']=$nm_supp;
					$ArrKartu[$i]['no_request']=$keys->no_reff;
				}
			}
			if(!empty($ArrKartu)){
//				$this->db->insert_batch('tr_kartu_hutang', $ArrKartu);
			}
		}
		echo "</table>";
		echo $i." data";
	}
	function insert_kartu_terima_invoice(){
		$sql="select a.*,b.keterangan info from ".DBACC.".jurnal a join ".DBACC.".javh b on a.nomor=b.nomor where a.no_perkiraan='2101-01-03' and a.debet>0 and a.nomor not in (select nomor from tr_kartu_hutang) order by nomor;";
		$result	= $this->db->query($sql)->result();$i=0;
		echo "<table border=1><tr><th>Nomor</th><th>Tanggal</th><th>COA</th><th>Keterangan</th><th>NO PO</th><th>Kredit</th><th>ID Supplier</th><th>Nama Supplier</th><th>No Request</th></tr>";
		$ArrKartu = array();
        if(!empty($result)){
		    foreach ($result as $keys) {
				$no_po='';
				$curr='';
				$id_supp='';
				$nm_supp='';
				$coa_debet='2101-01-03';
				$debet=$keys->debet;
				$coa_kredit='2101-01-01';
				$kredit='0';
				$dt_warehouse = $this->db->query("select * from ".DBACC.".jurnal where no_perkiraan='2101-01-01' and nomor='".$keys->nomor."'")->row();
				if(!empty($dt_warehouse)) {
					$no_po=(str_ireplace('PO ','',$dt_warehouse->keterangan));
					$kredit=$dt_warehouse->kredit;
					$dt_supp = $this->db->query("select * from tran_po_header where no_po='".$no_po."'")->row();
					if(!empty($dt_supp)){
						$id_supp=$dt_supp->id_supplier;
						$nm_supp=$dt_supp->nm_supplier;
						$curr=$dt_supp->mata_uang;
					}else{
						$dt_supp = $this->db->query("select * from tran_material_po_header where no_po='".$no_po."'")->row();
						if(!empty($dt_supp)){
							$id_supp=$dt_supp->id_supplier;
							$nm_supp=$dt_supp->nm_supplier;
							$curr=$dt_supp->mata_uang;						
						}
					}
				}
				if($curr=='IDR' && $kredit>0 && $debet>0){
					echo "<tr><td>".$keys->nomor."</td><td>".$keys->tanggal."</td><td>".$coa_debet."</td><td>".$keys->info."</td><td>".$no_po."</td><td>".$debet."</td><td>".$id_supp."</td><td>".$nm_supp."</td><td>".$keys->no_reff."</td></tr>";
					echo "<tr><td>".$keys->nomor."</td><td>".$keys->tanggal."</td><td>".$coa_kredit."</td><td>".$keys->info."</td><td>".$no_po."</td><td>".$kredit."</td><td>".$id_supp."</td><td>".$nm_supp."</td><td>".$keys->no_reff."</td></tr>";
					$i++;
// debit
					$ArrKartu[$i]['tipe']='JV';
					$ArrKartu[$i]['debet']=$debet;
					$ArrKartu[$i]['kredit']=0;
					$ArrKartu[$i]['nomor']=$keys->nomor;
					$ArrKartu[$i]['tanggal']=$keys->tanggal;
					$ArrKartu[$i]['no_perkiraan']=$coa_debet;
					$ArrKartu[$i]['keterangan']=$keys->info;
					$ArrKartu[$i]['no_reff']=$no_po;
					$ArrKartu[$i]['stspos']='0';
					$ArrKartu[$i]['jenis_trans']='GENERATESYS';
					$ArrKartu[$i]['id_supplier']=$id_supp;
					$ArrKartu[$i]['nama_supplier']=$nm_supp;
					$ArrKartu[$i]['no_request']=$keys->no_reff;
					$i++;
// kredit
					$ArrKartu[$i]['tipe']='JV';
					$ArrKartu[$i]['debet']=0;
					$ArrKartu[$i]['kredit']=$kredit;
					$ArrKartu[$i]['nomor']=$keys->nomor;
					$ArrKartu[$i]['tanggal']=$keys->tanggal;
					$ArrKartu[$i]['no_perkiraan']=$coa_kredit;
					$ArrKartu[$i]['keterangan']=$keys->info;
					$ArrKartu[$i]['no_reff']=$no_po;
					$ArrKartu[$i]['stspos']='0';
					$ArrKartu[$i]['jenis_trans']='GENERATESYS';
					$ArrKartu[$i]['id_supplier']=$id_supp;
					$ArrKartu[$i]['nama_supplier']=$nm_supp;
					$ArrKartu[$i]['no_request']=$keys->no_reff;
				}else{
					echo "<tr><td>".$keys->nomor."</td><td>".$keys->tanggal."</td><td>".$coa_kredit."</td><td>".$keys->info."</td><td>".$no_po."</td><td>".$kredit."</td><td>".$id_supp."</td><td>".$nm_supp."</td><td>".$keys->no_reff."</td><td>Error</td></tr>";
				}
			}
			if(!empty($ArrKartu)){
//				$this->db->insert_batch('tr_kartu_hutang', $ArrKartu);
			}
		}
		echo "</table>";
		echo $i." data";
	}
}
