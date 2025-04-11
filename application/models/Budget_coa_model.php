<?php
class Budget_coa_model extends CI_Model{

	protected $table_name = 'ms_budget';
    protected $key        = 'id';
	protected $created_field = 'created_on';
	protected $modified_field = 'modified_on';
	protected $set_created = true;
	protected $set_modified = true;
    protected $soft_deletes = false;
	protected $date_format = 'datetime';
	protected $log_user = true;


    public function __construct()
    {
        parent::__construct();
    }

	function GetBudget($tahun='',$all='',$query=''){
		$this->db->select('a.*, b.no_perkiraan , b.nama as nama_perkiraan, c.nm_dept');
		if($tahun!='') {
			if($all!='') {
				$this->db->from('(select * from '.$this->table_name.' where tahun='.$tahun.' ) a');
			}else{
				$this->db->from($this->table_name.' a');
				$this->db->where('a.tahun', $tahun);
			}
		}else{
			$this->db->from($this->table_name.' a');
		}
//		$this->db->join('ms_coa_category c','a.coa=c.coa','right');
		$this->db->join(DBACC.'.coa_master b','a.coa=b.no_perkiraan','right');
		$this->db->join('department c','a.divisi=c.id','left');
		if($query!='') $this->db->where($query);
		$this->db->where("b.level='5'");
		$this->db->order_by('b.no_perkiraan', 'asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	function updateBatch($data,$key){
		$this->db->update_batch($this->table_name, $data, $key);
	}

	function insertData($data){
		$this->db->insert($this->table_name, $data);
	}

	function insertDataPeriodik($data){
		$this->db->insert('ms_budget_periodik', $data);
	}
	function updateBatchPeriodik($data,$key){
		$this->db->update_batch('ms_budget_periodik', $data, $key);
	}

	function GetCoa($level='5',$query=''){
		$aMenu		= array();
		$this->db->select("a.no_perkiraan as coa, a.nama, a.no_perkiraan, a.nama as nama_perkiraan");
		$this->db->from(DBACC.'.coa_master a');
		$this->db->where('a.level',$level);
		if($query!='') $this->db->where($query);
		$this->db->order_by('a.no_perkiraan', 'asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

// 	function GetCoaCategory($tipe,$coa='',$name=''){
// 		$aMenu		= array();
// 		$this->db->select("a.coa, a.nama, b.no_perkiraan, b.nama as nama_perkiraan");
// 		$this->db->from('ms_coa_category a');
// 		$this->db->join(DBACC.'.coa_master b','a.coa=b.no_perkiraan');
// 		$this->db->where('a.tipe',$tipe);
// 		if($coa!=''){
// 			$this->db->where('a.coa',$coa);
// 		}
// 		$this->db->order_by('a.coa', 'asc');
// 		$query = $this->db->get();
// 		if($query->num_rows() != 0) {
// 			return $query->result();
// 		} else {
// 			return false;
// 		}
// 	}

// 	function SearchBudget($coa,$tahun){
// 		$this->db->select('a.*');
// 		$this->db->from($this->table_name.' a');
// 		$this->db->where('a.coa', $coa);
// 		$this->db->where('a.tahun', $tahun);
// 		$query = $this->db->get();
// 		if($query->num_rows() != 0) {
// 			return $query->row();
// 		} else {
// 			return false;
// 		}
// 	}

	function GetBudgetCategory($where){
		$this->db->select('a.*, b.no_perkiraan , b.nama as nama_perkiraan, c.nm_dept');
		$this->db->from($this->table_name.' a');
		$this->db->join(DBACC.'.coa_master b','a.coa=b.no_perkiraan');
		$this->db->join('department c','a.divisi=c.id','left');
		$this->db->where($where);
		$this->db->order_by('b.no_perkiraan', 'asc');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}
	
	function GetListBudgetDept($kategori){
		$this->db->select('a.created_on_dept, a.revisi, a.tahun, a.status, a.kategori, a.divisi, c.nm_dept');
		$this->db->from($this->table_name.' a');
		$this->db->join('department c','a.divisi=c.id','left');
		$this->db->where('a.status>0');
		$this->db->where('a.kategori',$kategori);
		$this->db->group_by('a.tahun ,a.status, a.kategori, a.divisi, c.nm_dept, a.created_on_dept, a.revisi'); 
		$this->db->order_by('a.created_on_dept', 'desc');
		$query = $this->db->get();
//		echo  $this->db->last_query();die();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function GetBudgetRutin($key=''){
		$this->db->select('a.*, b.nm_dept, c.nama as nama_perkiraan');
		$this->db->from('ms_budget_periodik a');
		$this->db->join('department b','a.departement=b.id','left');
		$this->db->join(DBACC.'.coa_master  c','a.coa=c.no_perkiraan','left');
		if($key!='') $this->db->where('a.departement',$key);
		$this->db->order_by('a.coa asc,a.nama');
		$query = $this->db->get();
		if($query->num_rows() != 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	public function updatebudget($data){
		$data_session	= $this->session->userdata;
		if($data['tipe']=='bulan'){
			$this->db->query("update ms_budget set bulan_1=(bulan_1+".$data['nilai']."),bulan_2=(bulan_2+".$data['nilai']."), bulan_3=(bulan_3+".$data['nilai']."), bulan_4=(bulan_4+".$data['nilai']."), bulan_5=(bulan_5+".$data['nilai']."), bulan_6=(bulan_6+".$data['nilai']."), bulan_7=(bulan_7+".$data['nilai']."), bulan_8=(bulan_8+".$data['nilai']."), bulan_9=(bulan_9+".$data['nilai']."), bulan_10=(bulan_10+".$data['nilai']."), bulan_11=(bulan_11+".$data['nilai']."), bulan_12=(bulan_12+".$data['nilai']."), total=(".($data['nilai']*12)."), created_by_dept='".$data_session['ORI_User']['username']."', created_on_dept='".date('Y-m-d H:i:s')."' WHERE tahun='".$data['tahun']."' and coa='".$data['coa']."' and divisi='".$data['departement']."' and kategori='PEMBAYARAN PERIODIK'");
		}
		if($data['tipe']=='tahun'){
			$bulan=date("n",strtotime('2021-'.$data['tanggal']));
			$this->db->query("update ms_budget set bulan_".$bulan."=(bulan_".$bulan."+".$data['nilai']."), total=(total+".$data['nilai']."), created_by_dept='".$data_session['ORI_User']['username']."', created_on_dept='".date('Y-m-d H:i:s')."' WHERE tahun='".$data['tahun']."' and coa='".$data['coa']."' and divisi='".$data['departement']."' and kategori='PEMBAYARAN PERIODIK' ");
		}
	}

}
