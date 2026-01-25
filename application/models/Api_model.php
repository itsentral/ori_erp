<?php
class Api_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
    
    //=================================================================================================================
    //==========================================LATE ENGINNERING=======================================================
    //=================================================================================================================

    public function api_late_enginnering()
	{
		$result = $this->db
                    ->select('  a.no_ipp, 
                                a.nm_customer, 
                                a.project, 
                                a.status, 
                                a.modified_date AS release_ipp
                                ')
                    ->from('production a')
                    ->join('bq_header b','a.no_ipp = b.no_ipp','join')
                    ->where("(
                                a.status = 'WAITING STRUCTURE BQ' 
                                OR a.status = 'WAITING APPROVE STRUCTURE BQ'
                                OR a.status = 'WAITING ESTIMATION PROJECT' 
                                OR a.status = 'WAITING APPROVE EST PROJECT'
                            )")
                    ->where('b.approved_est_date',NULL)
                    ->where('a.sts_hide','N')
                    ->order_by('a.modified_date','asc')
                    ->get()
                    ->result_array();
		return $result;
	}

    public function api_late_enginnering_count()
	{
		$data_result = $this->api_late_enginnering();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$release_plus2 = date('Y-m-d', strtotime('+2 days', strtotime($value['release_ipp'])));
			if(date('Y-m-d') > $release_plus2){
				$ArrDetail[$key]['no_ipp'] = $value['no_ipp'];
				$ArrDetail[$key]['nm_customer'] = $value['nm_customer'];
				$ArrDetail[$key]['project'] = $value['project'];
				$ArrDetail[$key]['status'] = $value['status'];
				$ArrDetail[$key]['release_date'] = $value['release_ipp'];
			}
		}
		return count($ArrDetail);
	}

    //=================================================================================================================
    //==============================================LATE COSTING=======================================================
    //=================================================================================================================

    public function api_late_costing()
	{
		$result = $this->db
                    ->select('  a.no_ipp, 
                                a.nm_customer, 
                                a.project, 
                                a.status, 
                                b.approved_est_date
                                ')
                    ->from('production a')
                    ->join('bq_header b','a.no_ipp = b.no_ipp','join')
                    ->where("(
                                a.status = 'WAITING EST PRICE PROJECT'
                            )")
                    ->where('a.sts_price_date',NULL)
                    ->where('a.sts_hide','N')
                    ->order_by('b.approved_est_date','asc')
                    ->get()
                    ->result_array();
		return $result;
	}

    public function api_late_costing_count()
	{
		$data_result = $this->api_late_costing();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			$release_plus2 = date('Y-m-d', strtotime('+2 days', strtotime($value['approved_est_date'])));
			if(date('Y-m-d') > $release_plus2){
				$ArrDetail[$key]['no_ipp'] = $value['no_ipp'];
				$ArrDetail[$key]['nm_customer'] = $value['nm_customer'];
				$ArrDetail[$key]['project'] = $value['project'];
				$ArrDetail[$key]['status'] = $value['status'];
				$ArrDetail[$key]['release_date'] = $value['approved_est_date'];
			}
		}
		return count($ArrDetail);
	}

    //=================================================================================================================
    //==============================================LATE QUOTATION=======================================================
    //=================================================================================================================

    public function api_late_quotation()
	{
		$result = $this->db
                    ->select('  a.no_ipp, 
                                a.nm_customer, 
                                a.project, 
                                a.status, 
                                a.sts_price_date
                                ')
                    ->from('production a')
                    ->join('bq_header b','a.no_ipp = b.no_ipp','join')
                    ->where("(
                                a.status = 'ALREADY ESTIMATED PRICE'
                            )")
                    ->where('b.app_quo','N')
                    ->where('a.sts_hide','N')
                    ->order_by('a.sts_price_date','asc')
                    ->get()
                    ->result_array();
		return $result;
	}

    public function api_late_quotation_count()
	{
		$data_result = $this->api_late_quotation();
		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
			// $release_plus2 = date('Y-m-d', strtotime('+2 days', strtotime($value['sts_price_date'])));
			// if(date('Y-m-d') > $release_plus2){
				$ArrDetail[$key]['no_ipp'] = $value['no_ipp'];
				$ArrDetail[$key]['nm_customer'] = $value['nm_customer'];
				$ArrDetail[$key]['project'] = $value['project'];
				$ArrDetail[$key]['status'] = $value['status'];
				$ArrDetail[$key]['release_date'] = $value['sts_price_date'];
			// }
		}
		return count($ArrDetail);
	}

    //=================================================================================================================
    //===========================================TOTAL QUOTATION=======================================================
    //=================================================================================================================

    public function api_total_quotation($year)
	{
		$result = $this->db
                    ->select('  a.no_ipp, 
                                a.nm_customer, 
                                a.project, 
                                a.status, 
                                a.sts_price_date
                                ')
                    ->from('production a')
                    ->join('bq_header b','a.no_ipp = b.no_ipp','join')
                    ->where('b.app_quo','Y')
                    ->where('a.sts_hide','N')
                    ->where('YEAR(b.app_quo_date)',$year)
                    ->order_by('a.sts_price_date','asc')
                    ->get()
                    ->result_array();
		return $result;
	}

    public function api_total_quotation_count($year)
	{
		$data_result = $this->api_total_quotation($year);
		$SUM = 0;
		foreach ($data_result as $key => $value) {
            $get_revisi_max     = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_header',array('id_bq'=>'BQ-'.$value['no_ipp']))->result();
		    $revised_no         = (!empty($get_revisi_max))?$get_revisi_max[0]->revised_no:0;

            $get_nilai          = $this->db->select('SUM(price_project) AS total_quo')->get_where('laporan_revised_header',array('id_bq'=>'BQ-'.$value['no_ipp'], 'revised_no'=>$revised_no))->result();
            $nilai_quotation    = (!empty($get_nilai))?$get_nilai[0]->total_quo:0;

            $SUM += $nilai_quotation;
        }
		return $SUM;
	}

    //=================================================================================================================
    //===========================================TOTAL SALES ORDER=======================================================
    //=================================================================================================================

    public function api_total_so($year)
	{
		$result = $this->db
                    ->select('a.total_deal_usd')
                    ->from('billing_so a')
					->join('so_bf_header b','a.no_ipp=b.no_ipp AND b.approved = "Y"','join')
                    ->where('YEAR(b.approved_date)',$year)
                    ->get()
                    ->result_array();
		return $result;
	}

    public function api_total_so_count($year)
	{
		$data_result = $this->api_total_so($year);
		$SUM = 0;
		foreach ($data_result as $key => $value) {
            $SUM += $value['total_deal_usd'];
        }
		return $SUM;
	}

    public function api_get_material($where=null)
	{
		$this->db->select('a.*');
        $this->db->from('raw_materials a');
        $this->db->where_in('id_category',$where);
        $this->db->where('delete','N');
        $this->db->where('id_material <> ','MTL-2105003');
        $this->db->order_by('nm_material','asc');

        $result =  $this->db->get()->result_array();
		return $result;
	}

    public function api_get_price_ref_material($id_material=null)
	{
		$this->db->select('a.price_ref_estimation AS price');
        $this->db->from('raw_materials a');
        $this->db->where('id_material',$id_material);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_material_name($id_material=null)
	{
		$this->db->select('a.nm_material AS nama');
        $this->db->from('raw_materials a');
        $this->db->where('id_material',$id_material);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_accessories()
	{
		$this->db->select('a.*');
        $this->db->from('accessories a');
        $this->db->where('deleted','N');

        $result =  $this->db->get()->result_array();
		return $result;
	}

    public function api_get_price_ref_acc($id_material=null)
	{
		$this->db->select('a.harga AS price');
        $this->db->from('accessories a');
        $this->db->where('id',$id_material);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_acc_name($id_material=null)
	{
		$this->db->select('a.*');
        $this->db->from('accessories a');
        $this->db->where('id',$id_material);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_unit_name($id_material=null)
	{
		$this->db->select('a.kode_satuan AS kode');
        $this->db->from('raw_pieces a');
        $this->db->where('a.id_satuan',$id_material);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_rate_mp()
	{
		$this->db->select('a.std_rate AS rate');
        $this->db->from('cost_process a');
        $this->db->where('a.id',1);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_percent_foh($id=null)
	{
		$this->db->select('a.std_rate AS rate');
        $this->db->from('cost_foh a');
        $this->db->where('a.id',$id);

        $result =  $this->db->get()->result();
		return $result;
	}

    public function api_get_percent_process($id=null)
	{
		$this->db->select('a.std_rate AS rate');
        $this->db->from('cost_process a');
        $this->db->where('a.id',$id);

        $result =  $this->db->get()->result();
		return $result;
	}
	
	public function api_get_list_shipping()
	{
		$this->db->select('a.id, a.shipping_name AS nama, a.type AS tipe');
        $this->db->from('list_shipping a');
        $this->db->where('a.flag','Y');
		$this->db->order_by('a.urut','ASC');

        $result =  $this->db->get()->result_array();
		return $result;
	}
	
	public function api_get_country($id=null)
	{
		$this->db->select('a.country_name AS nama');
        $this->db->from('country a');
        $this->db->where('a.country_code',$id);

        $result =  $this->db->get()->result();
		return $result;
	}
	
	public function api_get_list_trucking()
	{
        $result =  $this->db->order_by('urut','ASC')->get_where('list_help',array('group_by'=>'via'))->result_array();
		return $result;
	}
	
	public function api_get_list_darat()
	{
        $result =  $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'darat'))->result_array();
		return $result;
	}
	
	public function api_get_list_laut()
	{
        $result =  $this->db->select('area')->group_by('area')->order_by('area','ASC')->get_where('cost_trucking', array('category'=>'laut'))->result_array();
		return $result;
	}
	
	public function api_get_cost_export($id=null,$country=null)
	{
		$this->db->select('a.price AS harga');
        $this->db->from('cost_export_trans a');
        $this->db->where('a.country_code',$country);
		$this->db->where('LOWER(a.shipping_name)',$id);

        $result =  $this->db->get()->result();
		return $result;
	}
	
	public function api_get_destination($area=null)
	{
		$this->db->select('a.tujuan AS tujuan');
        $this->db->from('cost_trucking a');
        $this->db->where('a.area',$area);
		$this->db->group_by('a.tujuan');
		$this->db->order_by('a.tujuan','asc');

        $result =  $this->db->get()->result_array();
		return $result;
	}
	
	public function api_get_truck($area=null, $dest=null)
	{
		$this->db->select('a.id_truck AS id_truck, b.nama_truck AS nama_truck');
        $this->db->from('cost_trucking a');
		$this->db->join('truck b', 'a.id_truck = b.id');
        $this->db->where('a.area',$area);
		$this->db->where('a.tujuan',$dest);
		$this->db->group_by('a.id_truck');
		$this->db->order_by('b.nama_truck','asc');

        $result =  $this->db->get()->result_array();
		return $result;
	}
	
	public function api_get_rate_truck_lokal($area=null,$dest=null,$truck=null)
	{
		$this->db->select('a.price AS rate');
        $this->db->from('cost_trucking a');
        $this->db->where('a.area',$area);
		$this->db->where('a.tujuan',$dest);
		$this->db->where('a.id_truck',$truck);
		$this->db->limit(1);

        $result =  $this->db->get()->result();
		return $result;
	}
	
	public function api_get_truck_name($id=null)
	{
		$this->db->select('a.nama_truck AS nama_truck');
        $this->db->from('truck a');
        $this->db->where('a.id',$id);

        $result =  $this->db->get()->result();
		return $result;
	}
	
	public function api_get_customer()
	{
		$this->db->select('id_customer, nm_customer');
        $this->db->from('customer');
        $this->db->where('delete','N');
        $this->db->order_by('nm_customer','asc');

        $result =  $this->db->get()->result_array();
		return $result;
	}

	public function api_app_bq()
	{
		$result = $this->db
                    ->select('a.*')
                    ->from('production a')
                    ->where("a.status = 'WAITING APPROVE STRUCTURE BQ'")
                    ->get()
                    ->result_array();
		return $result;
	}

	public function api_app_est()
	{
		$result = $this->db
                    ->select('a.*')
                    ->from('production a')
                    ->where("a.status = 'WAITING APPROVE EST PROJECT'")
                    ->get()
                    ->result_array();
		return $result;
	}

	public function api_app_est_fd()
	{
		$result = $this->db
                    ->select('a.*')
                    ->from('production a')
                    ->where("(a.status = 'WAITING FINAL DRAWING' AND a.sts_hide = 'N')")
                    ->get()
                    ->result_array();
		return $result;
	}

	public function api_app_est_fd_parsial()
	{
		$result = $this->db
                    ->select('a.*')
                    ->from('production a')
                    ->where("(a.status = 'PARTIAL PROCESS')")
                    ->get()
                    ->result_array();
		return $result;
	}

	public function api_cost_machine_tanki()
	{
		$result = $this->db
                    ->select('a.machine_cost_per_hour')
                    ->from('machine a')
                    ->where("(a.id_mesin = 'MC-022')")
                    ->get()
                    ->result_array();
		return $result;
	}
	
}
?>