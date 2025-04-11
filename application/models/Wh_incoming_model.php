<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author Harboens
 * @copyright Copyright (c) 2021
 *
 * This is model class for table "Purchase Request Rutin"
 */

class Wh_incoming_model extends CI_Model
{
    /**
     * @var string  User Table Name
     */
    protected $table_name = 'warehouse_adjustment';
    protected $key        = 'id';

    public function __construct()
    {
        parent::__construct();
    }

	// list data
	public function GetListWHIncoming(){
		$this->db->select('a.*');		
		$this->db->from($this->table_name.' a');
		$this->db->where('a.category','RUTIN_IN');
		$this->db->order_by('a.id', 'desc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	// get data
	public function GetDataWHIncoming($id){
		$this->db->select('a.*');
		$this->db->from($this->table_name.' a');
		$this->db->where('a.id',$id);
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->row();
		} else {
			return false;
		}
	}
	
	public function GetPoCombo($kategori=''){
		$aCombo		= array();
/*
		$this->db->select('a.no_po, a.no_po');
		$this->db->from('tran_material_po_header a');
		$this->db->where('a.status','WAITING IN');
		$this->db->order_by('a.no_po', 'asc');
		$query = $this->db->get();
*/
		$query = $this->db->query("select a.no_po from tran_po_header a where a.status ='WAITING IN'
				union 
				select a.no_non_po as no_po from tran_non_po_header a where a.status='WAITING IN'
				");
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['no_po']]	= $vals['no_po'];
			}
		}
		return $aCombo;
	}

	public function GetDataWHIncomingDetail($id){
		$this->db->select('a.*');
		$this->db->from('warehouse_adjustment_detail a');
		$this->db->where('a.kode_trans',$id);
		$this->db->order_by('a.id asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPurchaseOrder($id){

		$query = $this->db->query("
				select 'PO' as tipe, a.id, a.no_po, a.nm_barang as nm_material, a.qty_purchase, a.qty_in, a.id_barang as idmaterial
				from tran_po_detail a 
				where a.no_po ='".$id."' and a.qty_purchase <> a.qty_in
				
				union 
				select 'NONPO' as tipe, a.id, a.no_non_po as no_po,a.nm_barang as nm_material, a.qty as qty_purchase, a.qty_in, a.id_barang as idmaterial 
				from tran_non_po_detail a 				
				where a.no_non_po='".$id."'
				");

		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	
	function GetWhCombo(){
		$aCombo		= array();
		$this->db->select('a.kd_gudang, a.nm_gudang');
		$this->db->from('warehouse a');
		$this->db->order_by('a.nm_gudang', 'asc');
		$query = $this->db->get();
		$results	= $query->result_array();
		if($results){
			foreach($results as $key=>$vals){
				$aCombo[$vals['kd_gudang']]	= $vals['kd_gudang'].' - '.$vals['nm_gudang'];
			}
		}
		return $aCombo;
	}
}
