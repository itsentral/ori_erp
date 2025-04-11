<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author Harboens
 * @copyright Copyright (c) 2020
 *
 * This is model class for table "Budget Rutin"
 */

class Pembayaran_periodik_model extends CI_Model
{
    /**
     * @var string  User Table Name
     */
    protected $table_name = 'tr_pengajuan_rutin';
    protected $key        = 'id';

    /**
     * @var string Field name to use for the created time column in the DB table
     * if $set_created is enabled.
     */
    protected $created_field = 'created_on';

    /**
     * @var string Field name to use for the modified time column in the DB
     * table if $set_modified is enabled.
     */
    protected $modified_field = 'modified_on';

    /**
     * @var bool Set the created time automatically on a new record (if true)
     */
    protected $set_created = true;

    /**
     * @var bool Set the modified time automatically on editing a record (if true)
     */
    protected $set_modified = true;

    /**
     * @var string The type of date/time field used for $created_field and $modified_field.
     * Valid values are 'int', 'datetime', 'date'.
     */
    protected $date_format = 'datetime';

    /**
     * @var bool If true, will log user id in $created_by_field, $modified_by_field,
     * and $deleted_by_field.
     */
    protected $log_user = true;

    /**
     * Function construct used to load some library, do some actions, etc.
     */
    public function __construct()
    {
        parent::__construct();
    }

	// list data
	public function GetPengajuanRutin($key=''){
		$this->db->select('a.*, b.nm_dept');
		$this->db->from($this->table_name.' a');
		$this->db->join('department b','a.departement=b.id');
		if($key!='') $this->db->where('a.departement',$key);
		$this->db->where('a.status >=',1);
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPengajuanRutin($id=''){
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

	public function GetDataPengajuanRutinDetail($nodoc=''){
		$this->db->select('a.*');
		$this->db->from('tr_pengajuan_rutin_detail'.' a');
		$this->db->where('a.no_doc',$nodoc);
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPengajuanRutinDetailView($nodoc=''){
		$this->db->select('a.*');
		$this->db->from('tr_pengajuan_rutin_detail'.' a');
		$this->db->where('a.no_doc',$nodoc);
		$this->db->where("status='A' OR status='P'");
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPengajuanRutinDetailPayment($nodoc=''){
		$this->db->select('a.*');
		$this->db->from('tr_pengajuan_rutin_detail'.' a');
		$this->db->where('a.no_doc',$nodoc);
		$this->db->where('a.status','A');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPengajuanRutinDetailPaymentOnly($nodoc=''){
		$this->db->select('a.*');
		$this->db->from('tr_pengajuan_rutin_detail'.' a');
		$this->db->where('a.no_doc',$nodoc);
		$this->db->where('a.status','P');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	// get data
	public function GetDataBudgetRutin($dept,$tanggal='',$idbudget=''){
		$sql="select * from ms_budget_rutin where departement='".$dept."' ";
		if($idbudget!='') $sql.=" and id not in (".implode(",",$idbudget).")";
		if($tanggal!='') $sql.=" and (tipe ='bulan' or (tipe='tahun' and left(tanggal,2)='".date("m",strtotime($tanggal))."'))";
		$query = $this->db->query($sql);
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

    public function DataSave($table, $data)
	{
		$this->db->insert($table, $data);
		$last_id = $this->db->insert_id();
		return $last_id;
	}

	public function DataUpdate($table, $data, $where)
	{
		$this->db->update($table, $data, $where);
	}

	public function DataDelete($table, $where)
	{
		return $this->db->delete($table, $where);
	}

}
