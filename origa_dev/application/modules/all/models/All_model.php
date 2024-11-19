<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author Harboens
 * @copyright Copyright (c) 2020
 *
 * This is model class for table "All Model"
 */

class All_model extends BF_Model
{
    public function __construct()
    {
        parent::__construct();
    }

	// save data
	public function dataSave($table,$data) {
		$this->db->insert($table, $data);
		$last_id = $this->db->insert_id();
		return $last_id;
    }

	// update data
	public function dataUpdate($table,$data,$where) {
		$this->db->update($table, $data, $where);
    }

	// delete data
	public function dataDelete($table,$where) {
		$this->db->delete($table,$where);
    }

	// Get one data
    public function GetOneData($table,$where) {
		$this->db->select('*');
		$this->db->from($table);
		$this->db->where($where);
		$query = $this->db->get();
		if($query->num_rows() != 0)		{
			return $query->row();
		}		else		{
			return false;
		}
    }

	// list data material
	public function GetListMaterial($where=''){
		$this->db->select('a.*');
		$this->db->from('ms_material a');
		if($where!=''){
			$this->db->where($where);
		}
		$this->db->order_by('a.nama', 'asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	function GetKursCombo(){
		$aCombo		= array();
		$this->db->select('a.kode, a.mata_uang');
		$this->db->from('mata_uang a');
		$this->db->order_by('a.kode', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['kode']]	= $vals['kode'].' - '.$vals['mata_uang'];
			}
		}
		return $aCombo;
	}

	function GetSatuanMaterial($idmaterial=""){
		$this->db->select('a.nama');
		$this->db->from('ms_satuan a');
		if($idmaterial!=''){
			$this->db->where("a.nama IN (select nama_satuan from ms_material_konversi where id_material='".$idmaterial."')",NULL,FALSE);
		}
		$this->db->order_by('a.nama', 'asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	function GetWhCombo(){
		$aCombo		= array();
		$this->db->select('a.wh_code, a.wh_name');
		$this->db->from('ms_warehouse a');
		$this->db->order_by('a.wh_name', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['wh_code']]	= $vals['wh_code'].' - '.$vals['wh_name'];
			}
		}
		return $aCombo;
	}

	function GetInventoryTypeCombo($where=''){
		$aCombo = array();
		$this->db->select('a.id_type, a.nama');
		$this->db->from('ms_inventory_type a');
		if($where!=''){
			$this->db->where($where);
		}
		$this->db->where('aktif','aktif',FALSE);
		$this->db->order_by('a.nama', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['id_type']]	= $vals['nama'];
			}
		}
		return $aCombo;
	}

	function GetAutoGenerate($tipe){
		$newcode='';
		$data=$this->GetOneData('ms_generate',array('tipe'=>$tipe));
		if($data!==false) {
			if(stripos($data->info,'YEAR',0)!==false){
				if($data->info3!=date("Y")) {
					$years=date("Y");
					$number=1;
					$newnumber=sprintf('%0'.$data->info4.'d', $number);
				}else{
					$years=$data->info3;
					$number=($data->info2+1);
					$newnumber=sprintf('%0'.$data->info4.'d', $number);
				}
				$newcode=str_ireplace('XXXX',$newnumber,$data->info);
				$newcode=str_ireplace('YEAR',$years,$newcode);
				$newdata=array('info2'=>$number,'info3'=>$years);
			}else{
				$number=($data->info2+1);
				$newnumber=sprintf('%0'.$data->info4.'d', $number);
				$newcode=str_ireplace('XXXX',$newnumber,$data->info);
				$newdata=array('info2'=>$number);
			}
			$this->dataUpdate('ms_generate',$newdata,array('tipe'=>$tipe));
			return $newcode;
		}else{
			return false;
		}
	}

	function GetSupplierCombo(){
		$aCombo		= array();
		$this->db->select('a.id_supplier, a.nm_supplier');
		$this->db->from('master_supplier_backup a');
		$this->db->order_by('a.nm_supplier', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['id_supplier']]	= $vals['nm_supplier'];
			}
		}
		return $aCombo;
	}

	public function GetMaterialStockList($where){
		$this->db->select('a.*, b.stock, (a.spec13-b.stock) as material_qty, c.nama_satuan satuan');
		$this->db->from('ms_material a');
		$this->db->join('ms_material_konversi c','a.id_material=c.id_material');
		$this->db->join('(select sum(stock)as stock ,id_material, satuan from ms_warehouse_stock group by id_material,satuan) b','a.id_material=b.id_material and b.satuan=c.nama_satuan','left');
		if($where!=''){
			$this->db->where($where);
		}
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function update_stock_in($data){
		$id_material=$data['material_id'];
		$qty_in=$data['material_qty'];
		$harga=$data['material_price'];
		$satuan=$data['material_unit'];
		$wh_code=$data['wh_code'];
		$code=$data['code'];
		$doc_no=$data['doc_no'];
		$tanggal=$data['tanggal'];
		$created_by=$data['created_by'];
		$created_on=$data['created_on'];
		$modified_by=$data['created_by'];
		$modified_on=$data['created_on'];

		//cek ms_warehouse_stock
		$where=array('id_material'=>$id_material,'wh_code'=>$wh_code,'satuan'=>$satuan);
		$data_stok=$this->GetOneData('ms_warehouse_stock',$where);
		if($data_stok!==false) {
			$harga_rata=((($data_stok->stock*$data_stok->harga)+($qty_in*$harga))/($data_stok->stock+$qty_in));
			$datatosave=array('stock'=>($data_stok->stock+$qty_in),'harga'=>$harga_rata,'modified_by'=>$modified_by,'modified_on'=>$modified_on);
			$this->dataUpdate('ms_warehouse_stock',$datatosave,$where);
		} else {
			$datatosave=array('stock'=>$qty_in,'harga'=>$harga,'id_material'=>$id_material,'wh_code'=>$wh_code,'satuan'=>$satuan,'created_by'=>$created_by,'created_on'=>$created_on);
			$this->dataSave('ms_warehouse_stock',$datatosave);
		}
		//cek ms_warehouse_stock_log
		$this->db->select('*');
		$this->db->from('ms_warehouse_stock_log');
		$this->db->where($where);
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query->num_rows() != 0)		{
			$data_stok=$query->row();
			$datatosave=array('stock_awal'=>$data_stok->stock_akhir,'stock_out'=>0,'stock_in'=>$qty_in,'stock_akhir'=>($data_stok->stock_akhir+$qty_in),'harga'=>$harga,'id_material'=>$id_material,'wh_code'=>$wh_code,'satuan'=>$satuan,'tanggal'=>$tanggal,'doc_no'=>$doc_no,'code'=>$code,'created_by'=>$created_by,'created_on'=>$created_on);
		} else {
			$datatosave=array('stock_awal'=>0,'stock_out'=>0,'stock_in'=>$qty_in,'stock_akhir'=>$qty_in,'harga'=>$harga,'id_material'=>$id_material,'wh_code'=>$wh_code,'satuan'=>$satuan,'tanggal'=>$tanggal,'doc_no'=>$doc_no,'code'=>$code,'created_by'=>$created_by,'created_on'=>$created_on);
		}
		$this->dataSave('ms_warehouse_stock_log',$datatosave);
	}
}

