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
		$result = $this->tanki->get_where('bq_detail_detail',$array)->result();
        $SPEC = (!empty($result))?number_format($result[0]->dia_lebar).' x '.number_format($result[0]->panjang).' x '.number_format($result[0]->t_dsg,2):'';
        return $SPEC;
	}

    function get_spec_check(){
		$result = $this->tanki->get('bq_detail_detail')->result_array();
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

        $ArrResult = [
            'customer' =>  $customer,
            'nm_project' =>  $nm_project,
            'no_so' =>  $no_so,
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


}

