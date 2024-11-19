<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Yunas Handra
 * @copyright Copyright (c) 2018, Yunas Handra
 *
 * This is model class for table "Customer"
 */

class Cycletime_model extends BF_Model
{

    /**
     * @var string  User Table Name
     */
    protected $table_name = 'tr_cycletime_header';
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

        $today=date("ymd");
		$year=date("y");
		$month=date("m");
		$day=date("d");

        $cek = date('y').$kode_bln;
        $query = "SELECT MAX(RIGHT(id_cycletime,5)) as max_id from tr_cycletime_hd ";
        $q = $this->db->query($query);
		$r = $q->row();
        $query_cek = $q->num_rows();
		$kode2 = $r->max_id;
		$kd_noreg = "";

        if ($query_cek == 0) {
          $kd_noreg = 1;
          $reg = sprintf("%02d%05s", $year,$kode_noreg);

        }else {

        // jk sudah ada maka
			$kd_new = $kode2+1; // kode sebelumnya ditambah 1.
			$reg = sprintf("%02d%05s", $year,$kd_new);

        }

		$tr ="CT$reg";


          // print_r($tr);
		  // exit();

      return $tr;
	}

 	public function get_data($table,$where_field='',$where_value=''){
		if($where_field !='' && $where_value!=''){
			$query = $this->db->get_where($table, array($where_field=>$where_value));
		}else{
			$query = $this->db->get($table);
		}

		return $query->result();
	}

	public function get_data_group($table,$where_field='',$where_value='',$where_group=''){
		if($where_field !='' && $where_value!=''){
			$query = $this->db->group_by($where_group)->get_where($table, array($where_field=>$where_value));

		}else{
			$query = $this->db->get($table);
		}

		return $query->result();
	}

    function getById($id)
    {
       return $this->db->get_where('ms_inventory_type',array('id_type' => $id))->row_array();
    }


	public function get_data_id_tr_cycletime($id){
		$this->db->select('a.*, b.nama as nama_material, c.nama_costcenter, d.nm_asset as nama_mesin, e.nm_asset as nama_mold');
		$this->db->from('tr_cycletime_header a');
		$this->db->join('ms_material b','b.id_material=a.produk');
		$this->db->join('ms_costcenter c','c.id_costcenter =a.cost_center');
		$this->db->join('asset d','d.kd_asset =a.mesin');
		$this->db->join('asset e','e.kd_asset =a.mold_tools');
		$this->db->where('a.deleted','0');
		$this->db->where('a.id_cycletime',$id);

		$query = $this->db->get();
		return $query->result();
	}

	function get_name($table, $field, $where, $value)
    {
       $query = "SELECT ".$field." FROM ".$table." WHERE ".$where."='".$value."' LIMIT 1";
	   $result = $this->db->query($query)->result();

	   return $result->$field;
    }

  public function get_json_cycletime(){
    $controller			= ucfirst(strtolower($this->uri->segment(1)));
    // $Arr_Akses			= getAcccesmenu($controller);
    $requestData		= $_REQUEST;
    $fetch					= $this->get_query_json_cycletime(
      $requestData['search']['value'],
      $requestData['order'][0]['column'],
      $requestData['order'][0]['dir'],
      $requestData['start'],
      $requestData['length']
    );
    $totalData			= $fetch['totalData'];
    $totalFiltered	= $fetch['totalFiltered'];
    $query					= $fetch['query'];

    $data	= array();
    $urut1  = 1;
    $urut2  = 0;
    foreach($query->result_array() as $row){
      $total_data     = $totalData;
      $start_dari     = $requestData['start'];
      $asc_desc       = $requestData['order'][0]['dir'];
      if($asc_desc == 'asc'){
        $nomor = $urut1 + $start_dari;
      }
      if($asc_desc == 'desc'){
        $nomor = ($total_data - $start_dari) - $urut2;
      }

      $nestedData 	= array();
      $nestedData[]	= "<div align='center'>".$nomor."</div>";
      $nestedData[]	= "<div align='left'>".strtoupper(get_name('ms_inventory_category2', 'nama', 'id_category2', $row['id_product']))."</div>";
      $nestedData[]	= "<div align='right'>".number_format(get_total_time_ct($row['id_product']),2)."</div>";

      $last_create = (!empty($row['updated_by']))?$row['updated_by']:$row['created_by'];
      $nestedData[]	= "<div align='left'>".strtolower(get_name('users', 'username', 'id_user', $last_create))."</div>";

      $last_date = (!empty($row['updated_date']))?$row['updated_date']:$row['created_date'];
      $nestedData[]	= "<div align='center'>".date('d-m-Y H:i:s', strtotime($last_date))."</div>";

      $edit	= "";
      $delete	= "";
      $print	= "";
      $approve = "";
      $download = "";

      $edit	= "&nbsp;<a href='".site_url($this->uri->segment(1)).'/edit/'.$row['id_time']."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
      $delete	= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id_time='".$row['id_time']."'><i class='fa fa-trash'></i></button>";
      // $print	= "&nbsp;<a href='".site_url($this->uri->segment(1).'/excel_ct_per_product/'.$row['id_time'])."' class='btn btn-sm btn-info' target='_blank' title='Print Sales Order' data-role='qtip'><i class='fa fa-print'></i></a>";

      $nestedData[]	= "<div align='center'>
                        <button type='button' class='btn btn-sm btn-warning detail' title='Detail' data-id_time='".$row['id_time']."'><i class='fa fa-eye'></i></button>

                        ".$edit."
                        ".$print."
                        ".$approve."
                        ".$download."
                        ".$delete."
                        </div>";
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

  public function get_query_json_cycletime($like_value = NULL, $column_order = NULL, $column_dir = NULL, $limit_start = NULL, $limit_length = NULL){

    $sql = "
      SELECT
      (@row:=@row+1) AS nomor,
        a.*,
        b.nama
      FROM
        cycletime_header a
        LEFT JOIN ms_inventory_category2 b ON a.id_product = b.id_category2,
        (SELECT @row:=0) r
       WHERE 1=1 AND a.deleted='N' AND (
        a.id_product LIKE '%".$this->db->escape_like_str($like_value)."%'
        OR b.nama LIKE '%".$this->db->escape_like_str($like_value)."%'
          )
    ";
    // echo $sql; exit;

    $data['totalData'] = $this->db->query($sql)->num_rows();
    $data['totalFiltered'] = $this->db->query($sql)->num_rows();
    $columns_order_by = array(
      0 => 'nomor',
      1 => 'id_product'
    );

    $sql .= " ORDER BY  ".$columns_order_by[$column_order]." ".$column_dir." ";
    $sql .= " LIMIT ".$limit_start." ,".$limit_length." ";

    $data['query'] = $this->db->query($sql);
    return $data;
  }



}
