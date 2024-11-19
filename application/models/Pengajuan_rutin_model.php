<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pengajuan_rutin_model extends CI_Model
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
		$this->db->order_by('id','desc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetPengajuanRutinApproval($key=''){
		$this->db->select('a.*, b.nm_dept');
		$this->db->from($this->table_name.' a');
		$this->db->join('department b','a.departement=b.id');
		$this->db->join('tr_pengajuan_rutin_detail c','a.no_doc=c.no_doc');
		$this->db->where('c.status','Y');
		if($key!='') $this->db->where('a.departement',$key);
		$this->db->group_by('a.no_doc');
		$this->db->order_by('a.id','desc');
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
		$this->db->where("(a.status = 'N' OR a.status = 'R')");
		// $this->db->where('a.status','R');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetDataPengajuanRutinDetailApproval($nodoc=''){
		$this->db->select('a.*');
		$this->db->from('tr_pengajuan_rutin_detail'.' a');
		$this->db->where('a.no_doc',$nodoc);
		$this->db->where("(a.status = 'Y')");
		// $this->db->where('a.status','R');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	// get data
	public function GetDataBudgetRutin($dept,$tanggal='',$idbudget=''){
		if($tanggal!=''){
		$time = strtotime($tanggal);
		$next_month = date("Y-m-d", strtotime("+1 month", $time));
		}
		$sql="select * from ms_budget_periodik where departement='".$dept."' ";
		if($idbudget!='') $sql.=" and id not in (".implode(",",$idbudget).")";
		// if($tanggal!='') $sql.=" and (tipe ='bulan' or (tipe='tahun' and left(tanggal,2)='".date("m",strtotime($tanggal))."'))";
		if($tanggal!='') $sql.=" and ((tipe ='bulan' or (tipe='tahun' and left(tanggal,2)='".date("m",strtotime($tanggal))."')) OR (tipe ='bulan' or (tipe='tahun' and left(tanggal,2)='".date("m",strtotime($next_month))."')))";
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

	public function GenerateAutoNumber($kode, $inisial = '', $digit = 4)
	{
		$this->db->select('a.*');
		$this->db->from('ms_generate a');
		$this->db->where('a.tipe', $kode);
		$query = $this->db->get();
		if ($query->num_rows() != 0) {
			$datainfo = $query->result();
			$nodoc = explode(';', $datainfo[0]->info);
			$nomorurut = '';
			if ($inisial == '') $inisial = $datainfo[0]->kode_1;
			if ($nodoc[0] == date("Y")) {
				if ($inisial == '') {
					$nomorurut = $nodoc[0] . '-' . sprintf('%0' . $digit . 'd', $nodoc[1]);
				} else {
					$nomorurut = str_replace("YY", $nodoc[0], $inisial);
					$nomorurut = str_replace("XX", sprintf('%0' . $digit . 'd', $nodoc[1]), $nomorurut);
				}
				$updno = $nodoc[0] . ';' . ($nodoc[1] + 1);
			} else {
				if ($inisial == '') {
					$nomorurut = $inisial . ($nodoc[0] + 1) . '-' . sprintf('%0' . $digit . 'd', 1);
				} else {
					$nomorurut = str_replace("YY", ($nodoc[0] + 1), $inisial);
					$nomorurut = str_replace("XX", sprintf('%0' . $digit . 'd', $nodoc[1]), $nomorurut);
				}
				$updno = date("Y") . ';2';
			}
			$this->DataUpdate('ms_generate', array('info' => $updno), array('tipe' => ($kode)));
			return $nomorurut;
		} else {
			return false;
		}
	}

}
