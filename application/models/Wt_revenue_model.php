<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @Author Syamsudin
 * @Copyright (c) 2022, Syamsudin
 *
 * This is model class for table "Wt_penawaran"
 */

class Wt_revenue_model extends CI_Model
{
    /**
     * @var string  User Table Name
     */
    protected $table_name = 'tr_sales_order';
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
    /**
     * @var bool Enable/Disable soft deletes.
     * If false, the delete() method will perform a delete of that row.
     * If true, the value in $deleted_field will be set to 1.
     */
    protected $soft_deletes = true;

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

    function generate_id($kode='') {
      $query = $this->db->query("SELECT MAX(id_so) as max_id FROM tr_sales_order");
      $row = $query->row_array();
      $thn = date('y');
      $max_id = $row['max_id'];
      $max_id1 =(int) substr($max_id,3,5);
      $counter = $max_id +1;
      $idcust = "P".$thn.str_pad($counter, 5, "0", STR_PAD_LEFT);
      return $counter;
	}
		
    function generate_code($kode='') {
      $query = $this->db->query("SELECT MAX(no_so) as max_id FROM tr_sales_order");
      $row = $query->row_array();
      $thn = date('y');
      $max_id = $row['max_id'];
      $max_id1 =(int) substr($max_id,3,5);
      $counter = $max_id1 +1;
      $idcust = "P".$thn.str_pad($counter, 5, "0", STR_PAD_LEFT);
      return $idcust;
	}
	function BuatNomor($kode='') {
	$bulan = date('m');
	$tahun = date('Y');
		if ($bulan=='01'){$romawi = 'I';}
		elseif ($bulan=='02'){$romawi = 'II';}
		elseif ($bulan=='03'){$romawi = 'III';}
		elseif ($bulan=='04'){$romawi = 'IV';}
		elseif ($bulan=='05'){$romawi = 'V';}
		elseif ($bulan=='06'){$romawi = 'VI';}
		elseif ($bulan=='07'){$romawi = 'VII';}
		elseif ($bulan=='08'){$romawi = 'VIII';}
		elseif ($bulan=='09'){$romawi = 'IX';}
		elseif ($bulan=='10'){$romawi = 'X';}
		elseif ($bulan=='11'){$romawi = 'XI';}
		elseif ($bulan=='12'){$romawi = 'XII';}
		$blnthn = date('Y-m');
      $query = $this->db->query("SELECT MAX(no_surat) as max_id FROM tr_sales_order WHERE month(tgl_so)='$bulan' and Year(tgl_so)='$tahun'");
      $row = $query->row_array();
      $thn = date('T');
      $max_id = $row['max_id'];
      $max_id1 =(int) substr($max_id,0,3);
      $counter = $max_id1 +1;
      $idcust = sprintf("%03s",$counter)."/WI/SO/".$romawi."/".$tahun;
      return $idcust;
	}

    public function get_data($table,$where_field='',$where_value=''){
		if($where_field !='' && $where_value!=''){
			$query = $this->db->get_where($table, array($where_field=>$where_value));
		}else{
			$query = $this->db->get($table);
		}
		
		return $query->result();
	}
  public function cariSalesOrder()
  {
		$this->db->select('a.*');
		$this->db->from('billing_so a');
		// $this->db->join('master_customers b','b.id_customer=a.id_customer');
    // $this->db->join('tr_penawaran c','c.no_penawaran=a.no_penawaran');
    $where = "a.jurnal_revenue ='OPN'";
    // $where2 = "a.status !='0'";
    $this->db->where($where);
    // $this->db->where($where2);
		// $this->db->order_by('a.no_ipp', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
	
	public function cariSalesOrderId($noso)
  {
		$this->db->select('a.*, b.name_customer as name_customer, c.grand_total as total_penawaran, c.no_surat as nomor_penawaran, c.tgl_penawaran');
		$this->db->from('tr_sales_order a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $this->db->join('tr_penawaran c','c.no_penawaran=a.no_penawaran');
    $where = "a.no_so = '$noso'";
    $where2 = "a.status !='0'";
    $this->db->where($where);
    $this->db->where($where2);
		$this->db->order_by('a.no_so', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
	
  public function cariSalesOrderNodeal()
  {
		$this->db->select('a.*, b.name_customer as name_customer, c.grand_total as total_penawaran');
		$this->db->from('tr_sales_order a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $this->db->join('tr_penawaran c','c.no_penawaran=a.no_penawaran');
    // $where = "a.status<>'6'";
    $where2 = "a.status ='0'";
    // $this->db->where($where);
    $this->db->where($where2);
		$this->db->order_by('a.no_penawaran', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
  public function CariPenawaranApproval()
  {
		$this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $where = "a.status='1'";
    $this->db->where($where);
		$this->db->order_by('a.no_penawaran', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
  public function CariPenawaranSo()
  {
		$this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $where = "a.status='6'";
    $this->db->where($where);
		$this->db->order_by('a.no_penawaran', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
  public function CariPenawaranLoss()
  {
		$this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $where = "a.status='7'";
    $this->db->where($where);
		$this->db->order_by('a.no_penawaran', DESC);
		$query = $this->db->get();	
		return $query->result();
	}

  public function CariPenawaranHistory()
  {
		$this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran_history a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
   	$this->db->order_by('a.no_penawaran', DESC);
		$query1 = $this->db->get();	

    $this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran  a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $where = "a.status_so='1'";
    $this->db->where($where);
   	$this->db->order_by('a.no_penawaran', DESC);
		$query2 = $this->db->get();	

    $query3= $this->db->query($query1 . ' UNION ' . $query2);



		return $query3->result();
	}
  public function CariHeaderHistory($no,$rev)
  {
    $this->db->select('a.*, b.name_customer as name_customer');
		$this->db->from('tr_penawaran_history a');
		$this->db->join('master_customers b','b.id_customer=a.id_customer');
    $where = "a.no_penawaran='$no'";
    $where2 = "a.revisi='$rev'";
    $this->db->where($where);
    $this->db->where($where2);
		$this->db->order_by('a.no_penawaran', DESC);
		$query = $this->db->get();	
		return $query->result();
	}
  public function CariDetailHistory($no,$rev)
  {
    $this->db->select('a.*');
		$this->db->from('tr_penawaran_detail_history a');	
    $where = "a.no_penawaran='$no'";
    $where2 = "a.revisi='$rev'";
    $this->db->where($where);
    $this->db->where($where2);
		$query = $this->db->get();	
		return $query->result();
	}

  public function cariRevenue()
  {
		$this->db->select('a.*, b.nm_customer as name_customer, c.project');
		$this->db->from('tr_revenue a');
		$this->db->join('customer b','b.id_customer=a.id_customer');
		$this->db->join('billing_so c','a.no_so=c.no_ipp');
		$where = "a.status_jurnal='OPN'";
		$this->db->where($where);
		$this->db->order_by('a.no_so');
		$query = $this->db->get();	
		return $query->result();
	}



	
}