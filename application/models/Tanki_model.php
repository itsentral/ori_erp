<?php

class Tanki_model extends CI_Model {

	public function __construct() {
			parent::__construct();
			// Your own constructor code
			$this->tanki = $this->load->database("tanki",TRUE);
	}

	function get_spec($id_milik){ 
		$array = [
            'id'=>$id_milik
        ];
		$result = $this->tanki->limit(0,1000)->get_where('bq_detail_detail',$array)->result();
        $SPEC = (!empty($result))?number_format($result[0]->dia_lebar).' x '.number_format($result[0]->panjang).' x '.number_format($result[0]->t_dsg,2):'';
        return $SPEC;
	}

    function get_spec_check($no_ipp){
		$result = $this->tanki->get_where('bq_detail_detail',array('no_ipp'=>$no_ipp))->result_array();
        $ArrSpec = [];
        foreach ($result as $key => $value) {
            $SPEC = number_format($value['dia_lebar']).' x '.number_format($value['panjang']).' x '.number_format($value['t_dsg'],2);
            $ArrSpec[$value['id']] = $SPEC;
        }
        return $ArrSpec;
	}

    function get_ipp_detail($no_ipp){ 
		$array = [
            'no_ipp'=>$no_ipp
        ];
		$result = $this->tanki->get_where('ipp_header',$array)->result();
        $customer = (!empty($result))?$result[0]->nm_customer:'';
        $nm_project = (!empty($result))?$result[0]->project:'';
        $no_so = (!empty($result))?$result[0]->no_so:'';
        $id_customer = (!empty($result))?$result[0]->id_customer:'';
        $no_po = (!empty($result))?$result[0]->no_po:'';

        $ArrResult = [
            'id_customer' =>  $id_customer,
            'customer' =>  $customer,
            'nm_project' =>  $nm_project,
            'no_so' =>  $no_so,
            'no_po' =>  $no_po,
        ];
        return $ArrResult;
	}

    function get_est_material($no_ipp){
        $GetSPK = $this->tanki
                            ->select('a.*, b.material, b.type, b.spk_pemisah')
                            ->join('temp_material b','a.id_tipe=b.id','left')
                            ->get_where('bq_detail_material_new a',
                                array(
                                    'a.no_ipp' => $no_ipp,
                                    'a.id_det >' => 0,
                                    'a.id_material !=' => 'MTL-1903000',
                                    'a.berat >' => 0)
                                )->result_array();
        return $GetSPK;
    }

    public function get_est_material_tanki($no_ipp)
	{
		$data_result = $this->get_est_material($no_ipp);

		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
            $UNIQ = $value['id_det'].'-'.$value['layer'].'-'.$value['spk_pemisah'];
			$TYPE = ($value['type'] > 0)?' '.$value['type']:'';

			$ArrDetail[$UNIQ][] = [
				'id' => $value['id'],
				'nm_category' => $value['material'].$TYPE,
				'id_material' => $value['id_material'],
				'berat' => $value['berat'],
				'jenis_spk' => $value['jenis_spk'],
			];
		}
		return $ArrDetail;
	}

    public function get_detail_ipp_tanki()
	{
		$data_result = $this->tanki->get_where('ipp_header',array('deleted_date'=>NULL))->result_array();

		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
            $UNIQ = $value['no_ipp'];
            $ArrDetail[$UNIQ]['no_ipp'] = $value['no_ipp'];
            $ArrDetail[$UNIQ]['no_so'] = $value['no_so'];
            $ArrDetail[$UNIQ]['nm_customer'] = $value['nm_customer'];
            $ArrDetail[$UNIQ]['project'] = $value['project'];
		}
		return $ArrDetail;
	}

    public function get_budget_so($no_ipp)
	{
		$data_result = $this->tanki
                            ->select('
                                SUM(frp_mat_cost) AS frp_mat_cost,
                                SUM(frp_mp) AS frp_mp,
                                SUM(frp_foh) AS frp_foh,
                                SUM(frp_admin) AS frp_admin,
                                SUM(frp_total) AS frp_total,
                                SUM(nonfrp_mat_cost) AS nonfrp_mat_cost,
                                SUM(nonfrp_mp) AS nonfrp_mp,
                                SUM(nonfrp_total) AS nonfrp_total,
                                SUM(testing) AS testing,
                                SUM(tot_testing) AS tot_testing,
                                SUM(test_mat_cost) AS test_mat_cost,
                                SUM(test_mp) AS test_mp,
                                SUM(test_foh) AS test_foh,
                                SUM(test_admin) AS test_admin,
                                SUM(test_total) AS test_total,
                                SUM(sum_mat_cost) AS sum_mat_cost,
                                SUM(sum_mp) AS sum_mp,
                                SUM(sum_foh) AS sum_foh,
                                SUM(sum_admin) AS sum_admin,
                                SUM(sum_total) AS sum_total,
                                SUM(profit) AS profit,
                                SUM(tot_profit) AS tot_profit,
                                SUM(tot_button_price) AS tot_button_price,
                                SUM(allowance) AS allowance,
                                SUM(tot_allowance) AS tot_allowance,
                                SUM(ed) AS ed,
                                SUM(tot_ed) AS tot_ed,
                                SUM(interest) AS interest,
                                SUM(tot_interest) AS tot_interest,
                                SUM(tot_selling_price) AS tot_selling_price,
                                SUM(packing) AS packing,
                                SUM(tot_packing) AS tot_packing,
                                SUM(tot_eksport) AS tot_eksport,
                                SUM(tot_lokal) AS tot_lokal,
                                SUM(tot_penawaran) AS tot_penawaran,
                                SUM(total_deal) AS total_deal,
                                SUM(total_deal_diskon) AS total_deal_diskon,
                                SUM(total_deal_idr) AS total_deal_idr
                            ')->group_by('no_ipp')->get_where('bq_selling_price',array('qty_deal >'=>0))->result_array();

		$ArrDetail = [];
		foreach ($data_result as $key => $value) {
            $ArrDetail[$no_ipp]['frp_mat_cost'] = $value['frp_mat_cost'];
            $ArrDetail[$no_ipp]['frp_mp'] = $value['frp_mp'];
            $ArrDetail[$no_ipp]['frp_foh'] = $value['frp_foh'];
            $ArrDetail[$no_ipp]['frp_admin'] = $value['frp_admin'];
            $ArrDetail[$no_ipp]['frp_total'] = $value['frp_total'];
            $ArrDetail[$no_ipp]['nonfrp_mat_cost'] = $value['nonfrp_mat_cost'];
            $ArrDetail[$no_ipp]['nonfrp_mp'] = $value['nonfrp_mp'];
            $ArrDetail[$no_ipp]['nonfrp_total'] = $value['nonfrp_total'];
            $ArrDetail[$no_ipp]['testing'] = $value['testing'];
            $ArrDetail[$no_ipp]['tot_testing'] = $value['tot_testing'];
            $ArrDetail[$no_ipp]['test_mat_cost'] = $value['test_mat_cost'];
            $ArrDetail[$no_ipp]['test_mp'] = $value['test_mp'];
            $ArrDetail[$no_ipp]['test_foh'] = $value['test_foh'];
            $ArrDetail[$no_ipp]['test_admin'] = $value['test_admin'];
            $ArrDetail[$no_ipp]['test_total'] = $value['test_total'];
            $ArrDetail[$no_ipp]['sum_mat_cost'] = $value['sum_mat_cost'];
            $ArrDetail[$no_ipp]['sum_mp'] = $value['sum_mp'];
            $ArrDetail[$no_ipp]['sum_foh'] = $value['sum_foh'];
            $ArrDetail[$no_ipp]['sum_admin'] = $value['sum_admin'];
            $ArrDetail[$no_ipp]['sum_total'] = $value['sum_total'];
            $ArrDetail[$no_ipp]['profit'] = $value['profit'];
            $ArrDetail[$no_ipp]['tot_profit'] = $value['tot_profit'];
            $ArrDetail[$no_ipp]['tot_button_price'] = $value['tot_button_price'];
            $ArrDetail[$no_ipp]['allowance'] = $value['allowance'];
            $ArrDetail[$no_ipp]['tot_allowance'] = $value['tot_allowance'];
            $ArrDetail[$no_ipp]['ed'] = $value['ed'];
            $ArrDetail[$no_ipp]['tot_ed'] = $value['tot_ed'];
            $ArrDetail[$no_ipp]['interest'] = $value['interest'];
            $ArrDetail[$no_ipp]['tot_interest'] = $value['tot_interest'];
            $ArrDetail[$no_ipp]['tot_selling_price'] = $value['tot_selling_price'];
            $ArrDetail[$no_ipp]['packing'] = $value['packing'];
            $ArrDetail[$no_ipp]['tot_packing'] = $value['tot_packing'];
            $ArrDetail[$no_ipp]['tot_eksport'] = $value['tot_eksport'];
            $ArrDetail[$no_ipp]['tot_lokal'] = $value['tot_lokal'];
            $ArrDetail[$no_ipp]['tot_penawaran'] = $value['tot_penawaran'];
            $ArrDetail[$no_ipp]['total_deal'] = $value['total_deal'];
            $ArrDetail[$no_ipp]['total_deal_diskon'] = $value['total_deal_diskon'];
            $ArrDetail[$no_ipp]['total_deal_idr'] = $value['total_deal_idr'];
		}
		return $ArrDetail;
	}

    function get_detail_tanki($id_milik){ 
		$array = [
            'id'=>$id_milik
        ];
		$result = $this->tanki->get_where('bq_detail_detail',$array)->result();
        $Array = [
            'qty' => $result[0]->jml
        ];
        return $Array;
	}


}

