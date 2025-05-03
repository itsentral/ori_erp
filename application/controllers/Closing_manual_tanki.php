<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Closing_manual_tanki extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('master_model');
		$this->load->model('produksi_model');
		$this->load->model('tanki_model');
		$this->load->model('Jurnal_model');
		$this->load->model('Acc_model');

		$this->tanki = $this->load->database("tanki",TRUE);
		// Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
		
		$this->get_user = get_detail_user();
	}

    public function index(){
        $status = 0;
        if($status == 1){
            $SQL    = "SELECT kode_spk, print_merge2_date AS hist_produksi  from production_detail where id in (219511,219512,219513,219514,219515,219516,219517,219526,219527,219534,219535,219536,219537,219538,219539,219540,219541,219542,219543,219544,219545,219546,219547,219548,219549,219550,219551,219552,219553,219554,219555,219556,219557,219558,219559,219560,219561,219562,219563,219564,219565,219566,219567,219568,219569,219570,219571,219580,219581,219588,219589,219590,219591,219592,219593,219594,219595,219596,219597,219598,219599,219600,219601,219602,219603,219604,219605,219606,219607,219608,219609,219610,219611,219612,219613,219614,219615,219616,219617,219619,219620,219621,219622,219623,219624,219625,219637,219638,219641,219642,219643,219644,219645,219646,219647,219648,219649,219650,219651,219652,219653,219654,219655,219656,219657,219658,219659,219660,219661,219662,219663,219664,219665,219666,219667,219668,219669,219670,219671,219672,219673,219674,219676,219677,219679,219680,219681,219682,219683,219684,219685,219686,219687,219688,219689,219690,219691,219692,219693,219694,219695,219696,219697,219698,219699,219700,219701,219702,219703,219704,219705,219706,219707,219708,219709,219710,219711,219712,219713,219714,219715,219716,219717,219718,219719,219720,219721,219722,219723,219724,219725,219727,219728,219729,219730,219739,219740,219741,219742,219743,219744,219745,219746,219755,219756,219757,219758,219759,219760,219761,219762,219763,219764,219765,219766,219767,219768,219769,219770,219771,219772,219773,219774,219775,219776,219777,219778,219779,219780,219781,219782,219783,219784,219785,219786,219787,219788,219789,219790,219791,219792,219794,219795,219796,219797,219798,219799,219800,219801,219802,219803,219804,219805,219806,219807,219808,219809,219810,219811,219812,219813,219814,219815,219816,219817,219818,219819,219820,219821,219822,219823,219824,219825,219826,219827,219828,219829,219830,219831,219833,219834,219835,219836,219837,219838,219839,219840,219841,219842,219843,219844,219845,219846,219847,219848,219849,219850,219851,219852,219853,219854,219855,219856,219857,219858,219859,219860,219861,219862,219863,219864,219865,219866,219867,219868,219869,219870,219872,219873,219874,219875,219876,219877,219878,219879,219880,219881,219882,219883,219884,219885,219886,219887,219888,219889,219890,219891,219892,219893,219894,219895,219896,219897,219898,219899,219900,219901,219902,219903,219904,219905,219906,219907,219908,219909,219911,219912,219913,219914,219915,219916,219921,219922,219923,219924,219925,219926,219927,219928,219929,219930,219931,219932,219933,219934,219935,219936,219937,219938,219939,219940,219941,219942,219943,219944,219945,219946,219947,219948,219949,219950,219952,219953,219954,219955,219962,219963,219964,219965,219966,219967,219968,219969,219970,219971,219972,219973,219974,219975,219976,219977,219978,219979,219980,219981,219982,219983,219984,219985,219986,219987,219988,219989,219990,219991,219992,219993,219994,219995,219996,219997,219998,219999,220000,220001,220002,220003,220004,220005,220006,220007,220008,220009,220010,220011,220012,220013,220014,220015,220016,220017,220018,220019,220020,220021,220022,220023,220024,220025,220026,220027,220028,220029,220030,220031,220032,220033,220034,220035,220036,220037,220038,220039,220040,220041,220042,220043,220044,220045,220046,220047,220048,220049,220050,220051,220052,220053,220054,220055,220056,220057,220058,220059,220060,220061,220090,220091,220092,220093,220094,220095,220096,220097,220098,220099,220100,220101,220102,220103,220104,220105,220106,220107,220108,220109,220110,220111,220112,220113,220114,220115,220116,220117,220118,220119,220120,220121,220122,220123,220124,220125,220126,220127,220136,220137,220138,220139,220140,220141,220142,220143,220144,220145,220146,220147,220148,220149,220150,220151,220156,220157,220158,220159,220160,220161,220162,220163,220172,220173,220174,220175,220195,220196,220197,220198,220210,220211,220212,220213,220222,220223,220224,220225,220234,220235,220236,220237,220246,220247,220248,220249,220250,220251,220252,220253,220254,220255,220256,220257,220258,220259,220260,220261,220262,220263,220264,220265,220266,220267,220268,220269,220270,220271,220272,220273,220274,220275,220276,220277,220278,220279,220280,220281,220290,220291,220292,220293,220294,220295,220296,220297,220298,220299,220300,220301,220302,220303,220304,220305,220306,220307,220308,220309,220310,220311,220312,220313,220314,220315,220316,220317,220318,220319,220320,220321,220322,220323,220324,220325,220334,220335,220336,220337,220338,220339,220340,220341,220342,220343,220344,220345,220346,220347,220348,220349,220350,220351,220352,220353,220354,220355,220356,220357,220358,220359,220360,220361,220362,220363,220364,220365,220366,220367,220368,220730,220731,220732,220733,220734,220735,220736,220737,220738,220739,220740,220741,220742,220743,220744,220745,220746,220747,220748,220749,220750,220751,220752,220753,220754,220755,220756,220757,220758,220759,220760,220761,220766,220767,220768,220769,220770,220771,220772,220773,220774,220784,220785,220786,220787,220788,220789,220790,220791,220792,220793,220794,220795,220796,220797,220798,220799,220800,220801,220802,220803,220804,220805,220806,220807,220808,220809,220810,220811,220812,220813,220814,220815,220816,220817,220818,220819,220820,220821,220822,220823,220824,220825,220826,220827,220828,220829,220830,220831,220832,220833,220834,220835,220836,220837,220897,220899,220901,220903,220905,220907,220909,220911,220912,220913,220914,220935,220936,220937,220938,220939,220968,220969,220970,220971,220972,220973,220974,220975,220976,220977,220978,220979,220980,220981,220982,220983,220984,220985,220986,220987,220988,220989,220990,220991,220992,220993,220994,220996,220997,220999,221000,221001,221002,221003,221004,221005,221043,221044,221045,221046,221047,221048,221049,221050,221051,221052,221053,221054,221055,221056,221057,221058,221059,221060,221061,221062,221229,221230,221231,221232,221233,221234,221235,221236,221237,221238,221239,221240,221241,221242,221243,221244,221245,221246,221247,221248,221249,221250,221251,221252,221253,221254,221256,221257,221258,221259,221260,221261,221262,221263,221264,221265,221266,221267,221268,221269,221270,221271,221272,221273,221274,221275,221276,221277,221278,221279,221280,221281,221282,221283,221284,221285,221286,221287,221288,221289,221290,221291,221292,221293,221294,221295,221296,221297,221298,221299,221300,221301,221302,221303,221304,221305,221306,221307,221308,221309,221310,221311,221312,221313,221314,221315,221316,221317,221318,221319,221320,221321,221322,221323,221324,221325,221326,221327,221328,221329,221330,221331,221332,221333,221334,221335,221336,221337,221338,221339,221340,221341,221342,221343,221344,221345,221346,221347,221348,221349,221350,221351,221352,221353,221354,221355,221356,221357,221358,221359,221360,221361,221362,221363,221364,221365,221366,221367,221368,221369,221370,221371,221372,221373,221374,221375,221376,221377,221378,221379,221380,221381,221382,221383,221384,221385,221386,221387,221388,221389,221390,221391,221392,221393,221394,221395,221396,221397,221398,221399,221400,221401,221402,221403,221404,221405,221406,221407,221408,221409,221411,221412,221417,221418,221419,221420,221421,221422,221423,221424,221425,221426,221427,221428,221429,221430,221431,221432,221433,221434,221435,221436,221437,221438,221439,221440,221604,221605,221606,221607,221608,221609,221610,221611,221612,221613,221614,221615,221616,221617,221618,221619,221620,221621,221622,221623,221624,221625,221626,221627,221628,221629,221630,221631,221632,221633,221634,221635,221644,221645,221646,221647,221648,221649,221650,221651,221652,221653,221654,221655,221656,221657,221658,221659,221660,221661,221662,221663,221664,221665,221666,221667,221668,221669,221670,221671,221672,221673,221674,221675,221687,221688,221689,221690,221691,221692,221693,221694,221695,221696,221697,221698,221699,221700,221703,221704,221705,221706,221707,221708,221709,221710,221711,221712,221713,221714,221719,221720,221723,221724,221725,221726,221727,221728,221731,221732,221733,221734,221763,221764,221765,221766,221767,221768,221769,221770,221771,221772,221773,221774,221775,221776,221777,221778,221779,221780,221781,221782,221783,221784,221785,221786,221787,221788,221789,221790,221791,221792,221793,221796,221797,221798,221799,221800,221801,221802,221803,221804,221805,221806,221807,221808,221809,221810,221811,221812,221813,221817,221818,221819,221820,221821,221822,221823,221828,221829,221838,221839,221844,221845,221846,221847,221848,221849,221854,221855,221863,221864,221865,221866,221867,221868,221869,221870,221872,221873,221874,221875,221876,221877,221878,221879,221881,221882,221883,221884,221885,221886,221887,221888,221889,221890,221891,221892,221893,221894,221895,221896,221897,221898,221907,221908,221909,221910,221911,221912,221913,221914,221915,221916,221917,221918,221919,221920,221921,221922,221923,221924,221925,221926,221927,221928,221929,221930,221931,221932,221933,221934,221935,221936,221937,221938,221939,221940,221941,221942,221943,221944,221945,221946,221947,221948,221949,221950,221951,221952,221953,221954,221955,221956,221957,221958,221959,221960,221961,221962,221998,221999,222000,222001,222002,222003,222004,222005,222006,222007,222008,222009,222012,222013,222014,222015,222016,222017,222018,222019,222020,222021,222022,222023,222028,222029,222030,222031,222032,222033,222034,222035,222036,222037,222038,222039,222042,222043,222044,222045,222046,222047,222048,222049,222050,222051,222052,222053,222058,222059,222060,222061,222062,222063,222064,222065,222066,222067,222068,222069,222072,222073,222074,222075,222076,222077,222078,222079,222080,222081,222082,222083,222088,222089,222090,222091,222092,222093,222094,222095,222096,222097,222098,222099,222102,222103,222104,222105,222106,222107,222108,222109,222110,222111,222112,222113,222118,222119,222120,222121,222122,222123,222124,222125,222126,222127,222128,222129,222132,222133,222134,222135,222136,222137,222138,222139,222140,222141,222142,222143,222148,222149,222150,222151,222152,222153,222154,222155,222156,222157,222158,222159,222162,222163,222164,222165,222166,222167,222168,222169,222170,222171,222172,222173,222178,222179,222180,222181,222182,222183,222184,222185,222186,222187,222188,222189,222192,222193,222194,222195,222196,222197,222198,222199,222200,222201,222202,222203,222208,222209,222210,222211,222212,222213,222214,222215,222216,222217,222218,222219,222222,222223,222224,222225,222226,222227,222228,222229,222230,222231,222232,222233,222238,222239,222562,222563,222564,222565,222566,222572,222573,222574,222575,222576,222577,222578,222579,222580,222581,222582,222583,222584,222585,222586,222587,222588,222589,222590,222591,222592,222593,222594,222595,222596,222597,222598,222599,222600,222601,222602,222603,222604,222605,222606,222607,222608,222609,222610,222611,222612,222613,222614,222615,222616,222617,222618,222619,222620,222621,222622,222623,222624,222625,222626,222627,222628,222629,222630,222631,222632,222633,222634,222635,222636,222637,222638,222639,222640,222641,222642,222643,222674,222675,222676,222677,222678,222679,222680,222681,222682,222683,222684,222685,222688,222689,222690,222691,222692,222693,222694,222695,222696,222697,222698,222699,222734,222735,222736,222737,222738,222739,222740,222741,222742,222743,222744,222745,222748,222749,222750,222751,222752,222753,222754,222755,222756,222757,222758,222759,222794,222795,222796,222797,222798,222799,222800,222801,222802,222803,222804,222805,222808,222809,222810,222811,222812,222813,222814,222815,222816,222817,222818,222819,222854,222855,222856,222857,222858,222859,222860,222861,222862,222863,222864,222865,222868,222869,222870,222871,222872,222873,222874,222875,222876,222877,222878,222879,222884,222885,222886,222887,222888,222889,222890,222891,222892,222893,222894,222895,222898,222899,222900,222901,222902,222903,222904,222905,222906,222907,222908,222909,222914,222915,222918,222919,225843,225844,225845,225846,225847,225848,225849,225850,225851,225852,225853,225854,225855,225856,225857,225858,225869,225870,225871,225872,225873,225874,225875,225876,225877,225878,225879,225880,225881,225882,225883,225884,225885,225886,225887,225888,225890,225891,225892,225893,225894,225895,225896,225897,225898,225899,225900,225901,225902,225903,225904,225905,225906,225907,225908,225909,225910,225911,225912,225913,225914,225915,225916,225917,225918,225919,225920,225921,225922,225923,225924,225925,225926,225927,225928,225929,225930,225931,225932,225933,225934,225935,225936,225937,225938,225939,225940,225941,225942,225943,225944,225945,225946,225947,225948,225949,225950,225951,225952,225953,225954,225955,225956,225957,225958,225959,225960,225961,225962,225963,225964,225965,225966,225967,225968,225969,225970,225971,225972,225973,225974,225975,225978,225979,225980,225981,225982,225983,225984,225985,225986,225987,225988,225989,225990,225991,225992,225993,225994,225995,225996,225997,225998,225999,226001,226004,226005,226007,226008,226009,226010,226011,226012,226013,226014,226015,226016,226017,226018,226019,226020,226021,226022,226023,226024,226025,226026,226027,226028,226029,226030,226031,226032,226033,226034,226035,226036,226037,226038,226039,226040,226041,226042,226043,226044,226045,226046,226047,226048,226049,226050,226051,226052,226053,226054,226055,226056,226057,226058,226059,226060,226061,226062,226063,226064,226065,226067,226068,226069,226070,226071,226072,228453) and process_manual='1' and result_manual is null GROUP BY kode_spk, print_merge2_date order by id asc";
            $result = $this->db->query($SQL)->result_array();
			// echo $SQL;
			// exit;
            foreach ($result as $key => $value) {
                $datetime       = date('Y-m-d H:i:s');
                $kode_spk 		= $value['kode_spk'];
                $hist_produksi	= $value['hist_produksi'];
                $id_gudang      = NULL;
                $closing_date   = $datetime;

                $detail_input	= [];
                $get_detail_spk2 = $this->db
                                ->select('b.*, a.qty AS qty_parsial, a.tanggal_produksi, a.id_gudang, a.upload_eng_change, c.no_spk AS no_spk2, c.adjustment_type AS typeTanki, c.no_so, a.closing_produksi_date')
                                ->from('production_spk_parsial a')	
                                ->join('production_spk b','a.id_spk = b.id')
                                ->join('warehouse_adjustment c',"a.kode_spk = c.kode_spk AND c.no_ipp = 'resin mixing' AND c.status_id='1'")
                                ->where('a.kode_spk',$kode_spk)
                                ->where('a.created_date',$hist_produksi)
                                ->where('c.created_date',$hist_produksi)
                                ->where('a.spk','1')
                                ->get()
                                ->result_array();
                
                foreach ($get_detail_spk2 as $keyX => $valueX) {
                    $detail_input[$keyX]['id']          = $valueX['id'];
                    $detail_input[$keyX]['id_milik']    = $valueX['id_milik'];
                    $detail_input[$keyX]['qty_all']     = $valueX['qty'];
                    $detail_input[$keyX]['qty']         = $valueX['qty_parsial'];

                    $id_gudang      = $valueX['id_gudang'];
                    $closing_date   = $valueX['closing_produksi_date'];
                }

                $dateCreated = $datetime;
                if($hist_produksi != '0'){
                    $dateCreated = $hist_produksi;
                }

                $kode_spk_created = $kode_spk.'/'.$dateCreated;

                $ArrWhereIN_= [];
                foreach ($detail_input as $key => $value) {
                    $QTY = str_replace(',','',$value['qty']);
                    if($QTY > 0){
                        $ArrWhereIN_[] = $value['id'];
                    }
                }

				if(!empty($ArrWhereIN_)){
					$get_detail_spk = $this->db->where_in('id',$ArrWhereIN_)->get_where('production_spk', array('kode_spk'=>$kode_spk))->result_array();

					$nomor = 0;
					$ID_PRODUKSI_DETAIL = [];
					foreach ($get_detail_spk as $key => $value) {
						$get_produksi 	= $this->db->limit(1)->select('id')->get_where('production_detail', array('id_milik'=>$value['id_milik'],'id_produksi'=>'PRO-'.$value['no_ipp'],'kode_spk'=>$value['kode_spk'],'upload_date'=>$dateCreated))->result();
						$id_pro_det		= (!empty($get_produksi[0]->id))?$get_produksi[0]->id:0;
						
						if($id_pro_det != 0){
							$ID_PRODUKSI_DETAIL[] = $id_pro_det;
						}
					}


					$ARR_ID_PRO_UNIQ = array_unique($ID_PRODUKSI_DETAIL);

					$this->closing_produksi_tanki($ARR_ID_PRO_UNIQ,$closing_date);
					$this->closing_produksi_base_jurnal($kode_spk_created,$id_gudang,14,$closing_date);

					$this->db->where('kode_spk',$kode_spk);
					$this->db->where('print_merge2_date',$dateCreated);
					$this->db->update('production_detail',['result_manual'=>1]);

					echo $kode_spk_created.' Success Process !<br>';
				}
				else{
					echo $kode_spk_created.' <b>Failed Process !</b><br>';
				}
            }
        }
		else{
			echo "Proses Stop !";
		}
    }

    public function closing_produksi_tanki($ARR_ID_PRO_UNIQ,$closing_date){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$dateTime 		= $closing_date;
		
		$HelpDet3 		= "tmp_production_real_detail";
		$HelpDet4 		= "tmp_production_real_detail_plus";
		$HelpDet5 		= "tmp_production_real_detail_add";
		// print_r($ARR_ID_PRO_UNIQ); exit;
		if(!empty($ARR_ID_PRO_UNIQ)){
			$restDetail1	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet3)->result_array();
			$restDetail2	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet4)->result_array();
			$restDetail3	= $this->db->where_in('id_production_detail',$ARR_ID_PRO_UNIQ)->get($HelpDet5)->result_array();
			// exit;

			$ArrDetail = array();
			if(!empty($restDetail1)){
				foreach($restDetail1 AS $val => $valx){
					$ArrDetail[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrDetail[$val]['id_detail'] = $valx['id_detail'];
					$ArrDetail[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrDetail[$val]['id_product'] = $valx['id_product'];
					$ArrDetail[$val]['batch_number'] = $valx['batch_number'];
					$ArrDetail[$val]['actual_type'] = $valx['actual_type'];
					$ArrDetail[$val]['benang'] = $valx['benang'];
					$ArrDetail[$val]['bw'] = $valx['bw'];
					$ArrDetail[$val]['layer'] = $valx['layer'];
					$ArrDetail[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrDetail[$val]['status'] = $valx['status'];
					$ArrDetail[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrDetail[$val]['status_date'] = $dateTime;
					$ArrDetail[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrDetail[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrDetail[$val]['spk'] = $valx['spk'];
					$ArrDetail[$val]['id_spk'] = $valx['id_spk'];
					$ArrDetail[$val]['updated_by'] = $valx['updated_by'];
					$ArrDetail[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrPlus = array();
			if(!empty($restDetail2)){
				foreach($restDetail2 AS $val => $valx){
					$ArrPlus[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrPlus[$val]['id_detail'] = $valx['id_detail'];
					$ArrPlus[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrPlus[$val]['id_product'] = $valx['id_product'];
					$ArrPlus[$val]['batch_number'] = $valx['batch_number'];
					$ArrPlus[$val]['actual_type'] = $valx['actual_type'];
					$ArrPlus[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrPlus[$val]['status'] = $valx['status'];
					$ArrPlus[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrPlus[$val]['status_date'] = $dateTime;
					$ArrPlus[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrPlus[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrPlus[$val]['spk'] = $valx['spk'];
					$ArrPlus[$val]['id_spk'] = $valx['id_spk'];
					$ArrPlus[$val]['updated_by'] = $valx['updated_by'];
					$ArrPlus[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			$ArrAdd = array();
			if(!empty($restDetail3)){
				foreach($restDetail3 AS $val => $valx){
					$ArrAdd[$val]['id_produksi'] = $valx['id_produksi'];
					$ArrAdd[$val]['id_detail'] = $valx['id_detail'];
					$ArrAdd[$val]['id_production_detail'] = $valx['id_production_detail'];
					$ArrAdd[$val]['id_product'] = $valx['id_product'];
					$ArrAdd[$val]['batch_number'] = $valx['batch_number'];
					$ArrAdd[$val]['actual_type'] = $valx['actual_type'];
					$ArrAdd[$val]['material_terpakai'] = $valx['material_terpakai'];
					$ArrAdd[$val]['status'] = $valx['status'];
					$ArrAdd[$val]['status_by'] = $data_session['ORI_User']['username'];
					$ArrAdd[$val]['status_date'] = $dateTime;
					$ArrAdd[$val]['catatan_programmer'] = $valx['catatan_programmer'];
					$ArrAdd[$val]['tanggal_catatan'] = $valx['tanggal_catatan'];
					$ArrAdd[$val]['spk'] = $valx['spk'];
					$ArrAdd[$val]['id_spk'] = $valx['id_spk'];
					$ArrAdd[$val]['updated_by'] = $valx['updated_by'];
					$ArrAdd[$val]['updated_date'] = $valx['updated_date'];
				}
			}
			
			// if(!empty($ArrDetail)){
			// 	$this->db->insert_batch('production_real_detail', $ArrDetail);
			// }
			// if(!empty($ArrPlus)){
			// 	$this->db->insert_batch('production_real_detail_plus', $ArrPlus);
			// }
			// if(!empty($ArrAdd)){
			// 	$this->db->insert_batch('production_real_detail_add', $ArrAdd);
			// }

			foreach ($ARR_ID_PRO_UNIQ as $value) {

				$QUERY_GET1 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET2 = "(SELECT
								a.id_produksi AS id_produksi,
								b.id_category AS id_category,
								a.id_product AS id_product,
								b.qty_awal AS product_ke,
								b.qty_akhir AS qty_akhir,
								b.qty AS qty,
								a.status_by AS status_by,
								a.updated_date AS status_date,
								a.id_production_detail AS id_production_detail,
								a.id AS id,
								a.id_spk AS id_spk,
								b.id_milik AS id_milik,
								a.catatan_programmer AS kode_trans
							FROM
								(
									tmp_production_real_detail_plus a
									LEFT JOIN update_real_list b ON ((
											a.id_production_detail = b.id 
										))) 
								WHERE 
									a.id_production_detail = '".$value."'
									AND a.updated_date = '".$valx['updated_date']."'
							GROUP BY
								cast( a.updated_date AS DATE ),
								a.id_production_detail 
							ORDER BY
								a.updated_date DESC)";
				$QUERY_GET = $QUERY_GET1.'UNION'.$QUERY_GET2;
				$getData = $this->db->query($QUERY_GET)->result_array();
				
				if(!empty($getData)){
					$ArrWIP = array(
						'id_produksi' => $getData[0]['id_produksi'],
						'id_milik' => $getData[0]['id_milik'],
						'id_production_detail' => $getData[0]['id_production_detail'],
						'qty_akhir' => $getData[0]['qty_akhir'],
						'product_ke' => $getData[0]['product_ke'],
						'id_category' => $getData[0]['id_category'],
						'id_product' => $getData[0]['id_product'],
						'status_date' => $getData[0]['status_date'],
						'kode_trans' => $getData[0]['kode_trans'],
						'qty' => $getData[0]['qty'],
					);

					$this->save_report_wip_closing_tanki($ArrWIP,$closing_date);

				}
			}
		}
	}

    public function save_report_wip_closing_tanki($ArrData,$closing_date){
        $dateNow    = date('Y-m-d',strtotime($closing_date));
        $username       = 'manual system 2';
		$datetime       = $closing_date;

		$sqlkurs	= "select * from ms_kurs where tanggal <='".$dateNow."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$sqlEstMaterial = "SELECT SUM(berat) AS est_berat, SUM(berat*price) AS est_price FROM est_material_tanki WHERE id_det='".$ArrData['id_milik']."' GROUP BY id_det";
        $restEstMat	    = $this->db->query($sqlEstMaterial)->result_array();

		$jumTot     = ($ArrData['qty_akhir'] - $ArrData['product_ke']) + 1;

        $est_material_bef          = (!empty($restEstMat[0]['est_berat']))?$restEstMat[0]['est_berat']:0;
        $est_harga_bef             = (!empty($restEstMat[0]['est_price']))?$restEstMat[0]['est_price']:0;

        $est_material           = $est_material_bef * $jumTot;
        $est_harga              = $est_harga_bef * $jumTot;

        $pe_direct_labour           = 4.05;
        $pe_indirect_labour         = 0.34;
        $pe_machine                 = 0.02;
        $pe_consumable              = 0.2;
        $pe_foh_consumable          = 0.5;
        $pe_foh_depresiasi          = 0.5;
        $pe_biaya_gaji_non_produksi = 1;
        $pe_biaya_non_produksi      = 1;
        $pe_biaya_rutin_bulanan     = 0.5;

		$sqlBy 		= " SELECT
							NULL AS diameter,
							NULL AS diameter2,
							(a.man_power * a.total_time) AS man_hours,
							((a.man_power * a.total_time) * $pe_direct_labour) AS direct_labour,
							((a.man_power * a.total_time) * $pe_indirect_labour) AS indirect_labour,
							(a.total_time * $pe_machine) AS machine,
							0 AS mould_mandrill,
							($est_material * $pe_consumable) AS consumable,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_foh_consumable / 100 ) AS foh_consumable,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_foh_depresiasi / 100 ) AS foh_depresiasi,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
							(
									(((a.man_power * a.total_time) * $pe_direct_labour)+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
							(
									((((a.man_power * a.total_time) * $pe_direct_labour))+((a.man_power * a.total_time) * $pe_indirect_labour)+(a.total_time * $pe_machine)+($est_material * $pe_consumable))+ $est_harga 
							) * ( $pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
						FROM
								production_detail a
						WHERE a.id_milik='".$ArrData['id_milik']."' AND a.process_manual = '1' LIMIT 1";
		
		$restBy		= $this->db->query($sqlBy)->result_array();
		
		$sqlBan         = " SELECT 
								SUM(a.material_terpakai) AS real_material, 
								SUM(a.material_terpakai*b.price) AS real_harga 
							FROM 
								production_real_detail a
								INNER JOIN est_material_tanki b ON a.id_detail=b.id
							WHERE a.id_production_detail='".$ArrData['id_production_detail']."' 
							GROUP BY a.id_production_detail";
		$restBan	= $this->db->query($sqlBan)->result_array();

		$real_material          = (!empty($restBan[0]['real_material']))?$restBan[0]['real_material']:0;
        $real_harga             = (!empty($restBan[0]['real_harga']))?$restBan[0]['real_harga']:0;
        $real_harga_rp          = $real_harga * $kurs;
		// echo $sqlEst."<br>";
		
		
		$sqlInsertDet = "INSERT INTO laporan_wip_per_hari_action
							(id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
							qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
							real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
							consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
							biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,kode_trans)
							VALUE
							('".$ArrData['id_produksi']."','".$ArrData['id_category']."','".$ArrData['id_product']."',
							'".$restBy[0]['diameter']."','".$restBy[0]['diameter2']."','0',
							'0','".$ArrData['status_date']."','".$ArrData['product_ke']."',
							'".$ArrData['qty_akhir']."','".$ArrData['qty']."','".date('Y-m-d',strtotime($ArrData['status_date']))."','".$ArrData['id_production_detail']."',
							'".$ArrData['id_milik']."','".$est_material."','".$est_harga."',
							'".$real_material."','".$real_harga."','".$restBy[0]['direct_labour'] * $jumTot."',
							'".$restBy[0]['indirect_labour'] * $jumTot."','".$restBy[0]['machine'] * $jumTot."',
							'".$restBy[0]['mould_mandrill'] * $jumTot."','".$restBy[0]['consumable'] * $jumTot."',
							'".$restBy[0]['foh_consumable'] * $jumTot."','".$restBy[0]['foh_depresiasi'] * $jumTot."',
							'".$restBy[0]['biaya_gaji_non_produksi'] * $jumTot."','".$restBy[0]['biaya_non_produksi'] * $jumTot."',
							'".$restBy[0]['biaya_rutin_bulanan'] * $jumTot."','".$username."','".$datetime."','".$restBy[0]['man_hours'] * $jumTot."','".$real_harga_rp."','".$kurs."','".$ArrData['kode_trans']."')
						";
		// echo $sqlInsertDet.'<br>';
		// exit;
		$this->db->query($sqlInsertDet);
	}

    public function closing_produksi_base_jurnal($kode_spk_time,$id_gudang,$id_gudang_ke,$closing_date){
		$data 			= $this->input->post();
		$data_session	= $this->session->userdata;
		$username       = 'manual system 2';
		$datetime       = $closing_date;
		
		$restDetail1	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail2	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_plus',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();
		$restDetail3	= $this->db->select('REPLACE(id_produksi,"PRO-","") AS no_ipp, id_production_detail AS id_pro_det, actual_type AS id_material, SUM(CAST(material_terpakai AS DECIMAL(16,4))) AS berat, id_spk, catatan_programmer AS kode_trans')->group_by('id_production_detail,actual_type')->get_where('tmp_production_real_detail_add',array('catatan_programmer'=>$kode_spk_time,'CAST(material_terpakai AS DECIMAL(16,4)) >'=>0))->result_array();

		$restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3);
		$dateKurs       = date('Y-m-d',strtotime($closing_date));
		$GET_COSTBOOK   = getPriceBookByDateproduksi($dateKurs);
		$GET_MAERIALS   = get_detail_material();
		$GET_MATERIAL	= get_detail_material();
		//KURS
		$sqlkurs	= "select * from ms_kurs where tanggal <='".$dateKurs."' and mata_uang='USD' order by tanggal desc limit 1";
		$dtkurs		= $this->db->query($sqlkurs)->result_array();
		$kurs		= (!empty($dtkurs[0]['kurs']))?$dtkurs[0]['kurs']:1; 

		$temp = [];
		$tempMaterial = [];
		$ArrIDSPK = [];
		$ArrUpdateStock = [];
		$SUM_MATERIAL = 0;
		$QTY_OKE = 0;
		foreach ($restDetail as $key => $value) {
			$UNIQ = $value['kode_trans'].'-'.$value['id_material'];

			if(!array_key_exists($UNIQ, $temp)) {
				$temp[$UNIQ]['berat'] = 0;
			}
			$temp[$UNIQ]['berat'] += $value['berat'];

			$temp[$UNIQ]['tanggal'] 	= $dateKurs;
			$temp[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
			$temp[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
			$temp[$UNIQ]['id_material'] = $value['id_material'];

			$nm_material = (!empty($GET_MAERIALS[$value['id_material']]['nm_material']))?$GET_MAERIALS[$value['id_material']]['nm_material']:'';
			$temp[$UNIQ]['nm_material'] = $nm_material;
			$temp[$UNIQ]['id_spk'] 		= $value['id_spk'];
			$temp[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
			$temp[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";

			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$temp[$UNIQ]['no_so'] 		= (!empty($getDetailSPK[0]['product_code']))?substr($getDetailSPK[0]['product_code'],0,9):'';
			$temp[$UNIQ]['product'] 	= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$temp[$UNIQ]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$temp[$UNIQ]['id_milik']	= (!empty($getDetailSPK[0]['id_milik']))?$getDetailSPK[0]['id_milik']:'';

			$costbook 	= (!empty($GET_COSTBOOK[$value['id_material']]))?$GET_COSTBOOK[$value['id_material']]:0;
			$berat 		= $temp[$UNIQ]['berat'];
			// $SUM_MATERIAL += round($costbook * $berat);
			
			$temp[$UNIQ]['costbook'] 		= $costbook;
			$temp[$UNIQ]['kurs'] 			= $kurs;
			$temp[$UNIQ]['total_price'] 	= round($costbook * $berat);
			$temp[$UNIQ]['total_price_debet'] 	= 0;
			$temp[$UNIQ]['created_by'] 		= $username;
			$temp[$UNIQ]['created_date'] 	= $datetime;

			$ArrUpdateStock[$UNIQ]['id'] 	= $value['id_material'];
			$ArrUpdateStock[$UNIQ]['qty'] 	= $berat;

			$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'insert_by'=>$username))->result_array();
			$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
			$temp[$UNIQ]['id_trans'] = $id_trans;
			
			$ArrIDSPK[$value['id_pro_det']] = $value['id_pro_det'];


			//Group Material
			$UNIQ2 = $value['id_material'];
			if(!array_key_exists($UNIQ2, $tempMaterial)) {
				$tempMaterial[$UNIQ2]['qty'] = 0;
			}
			
			$getDetailSPK = $this->db->get_where('production_spk',array('id'=>$value['id_spk']))->result_array();
			$tempMaterial[$UNIQ2]['tanggal'] 		= $datetime;
			$tempMaterial[$UNIQ2]['keterangan'] 	= 'laporan produksi';
			$tempMaterial[$UNIQ2]['no_ipp'] 		= $value['no_ipp'];
			$tempMaterial[$UNIQ2]['no_spk'] 		= (!empty($getDetailSPK[0]['no_spk']))?$getDetailSPK[0]['no_spk']:'';
			$tempMaterial[$UNIQ2]['product'] 		= (!empty($getDetailSPK[0]['product']))?$getDetailSPK[0]['product']:'';
			$tempMaterial[$UNIQ2]['kode_trans'] 	= $value['kode_trans'];
			$tempMaterial[$UNIQ2]['id_material'] 	= $value['id_material'];
			$tempMaterial[$UNIQ2]['nm_material'] 	= $nm_material;
			$tempMaterial[$UNIQ2]['qty'] 			+= $value['berat'];
			$tempMaterial[$UNIQ2]['cost_book'] 		= $costbook;
			$tempMaterial[$UNIQ2]['created_by'] 	= $username;
			$tempMaterial[$UNIQ2]['created_date'] 	= $datetime;
			$tempMaterial[$UNIQ2]['tipe'] 			= 'out';
			$tempMaterial[$UNIQ2]['gudang'] 		= $id_gudang;
			$tempMaterial[$UNIQ2]['gudang_dari'] 	= $id_gudang;
			$tempMaterial[$UNIQ2]['gudng_ke'] 		= $id_gudang_ke;
			
			$getDetailSPK1 = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det'],'insert_by'=>$username))->result_array();
			
			$id_trans1 = (!empty($getDetailSPK1[0]['id']))?$getDetailSPK1[0]['id']:0;
			
			$id_material = $value['id_material'];

                $coa_1    = $this->db->get_where('warehouse', array('id'=>$id_gudang))->row();
				$coa_gudang = $coa_1->coa_1;
				$kategori_gudang = $coa_1->category;				 
					
					$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$stok_jurnal_akhir = $this->db->order_by('id', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$id_material),1)->row();
				if(!empty($stok_jurnal_akhir)) $stokjurnalakhir=$stok_jurnal_akhir->qty_stock_akhir;
				
				if(!empty($stok_jurnal_akhir)) $nilaijurnalakhir=$stok_jurnal_akhir->nilai_akhir_rp;
				
				$tanggal		= date('Y-m-d');
				$Bln 			= substr($tanggal,5,2);
				$Thn 			= substr($tanggal,0,4);
				$Nojurnal      = $this->Jurnal_model->get_Nomor_Jurnal_Sales_pre('101', $tanggal);
				
				
				
				$QTY_OKE  = $berat; 
				$ACTUAL_MAT = $value['id_material'];
				$kode_trans = $id_trans1;
				$PRICE     = $costbook;
				
				$ArrJurnalNew[$UNIQ2]['id_material'] 		= $ACTUAL_MAT;
				$ArrJurnalNew[$UNIQ2]['idmaterial'] 		= $GET_MATERIAL[$ACTUAL_MAT]['idmaterial'];
				$ArrJurnalNew[$UNIQ2]['nm_material'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_material'];
				$ArrJurnalNew[$UNIQ2]['id_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['id_category'];
				$ArrJurnalNew[$UNIQ2]['nm_category'] 		= $GET_MATERIAL[$ACTUAL_MAT]['nm_category'];
				$ArrJurnalNew[$UNIQ2]['id_gudang'] 			= $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang'] 			= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_dari'] 	    = $id_gudang;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_dari'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang);
				$ArrJurnalNew[$UNIQ2]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrJurnalNew[$UNIQ2]['kd_gudang_ke'] 		= get_name('warehouse', 'kd_gudang', 'id', $id_gudang_ke);
				$ArrJurnalNew[$UNIQ2]['qty_stock_awal'] 		= $stokjurnalakhir;
				$ArrJurnalNew[$UNIQ2]['qty_stock_akhir'] 	= $stokjurnalakhir-$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['kode_trans'] 			= $kode_trans;
				$ArrJurnalNew[$UNIQ2]['tgl_trans'] 			= $datetime;
				$ArrJurnalNew[$UNIQ2]['qty_out'] 			= $QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['ket'] 				= 'pindah gudang produksi - wip';
				$ArrJurnalNew[$UNIQ2]['harga'] 			= $PRICE;
				$ArrJurnalNew[$UNIQ2]['harga_bm'] 		= 0;
				$ArrJurnalNew[$UNIQ2]['nilai_awal_rp']	= $nilaijurnalakhir;
				$ArrJurnalNew[$UNIQ2]['nilai_trans_rp']	= $PRICE*$QTY_OKE;
				$ArrJurnalNew[$UNIQ2]['nilai_akhir_rp']	= $nilaijurnalakhir-($PRICE*$QTY_OKE);
				$ArrJurnalNew[$UNIQ2]['update_by'] 		= $username;
				$ArrJurnalNew[$UNIQ2]['update_date'] 		= $datetime;
				$ArrJurnalNew[$UNIQ2]['no_jurnal'] 		= $Nojurnal;
				$ArrJurnalNew[$UNIQ2]['coa_gudang'] 		= $coa_gudang;
			
				
		}
		//biaya WIP
		$ArrDataWIP = ['Direct labour','Indirect labour','Consumable','FOH','Total'];
		$temp2 = [];
		if(!empty($temp)){
			foreach ($ArrDataWIP as $value2) {
				foreach ($temp as $key => $value) {
					$UNIQ = $value['id_spk'].'-'.$value2;

					$temp2[$UNIQ]['berat'] 		= 0;

					$WIPNmProduct = ($value2 == 'Total')?$value['product']:$value2;

					$temp2[$UNIQ]['tanggal'] 		= $dateKurs;
					$temp2[$UNIQ]['no_ipp'] 		= $value['no_ipp'];
					$temp2[$UNIQ]['id_pro_det'] 	= $value['id_pro_det'];
					$temp2[$UNIQ]['id_material'] = NULL;
					$temp2[$UNIQ]['nm_material'] = 'WIP '.$WIPNmProduct;
					$temp2[$UNIQ]['id_spk'] 		= $value['id_spk'];
					$temp2[$UNIQ]['kode_trans'] 	= $value['kode_trans'];
					$temp2[$UNIQ]['keterangan']	= "Gudang Produksi to WIP";
					$temp2[$UNIQ]['no_so'] 		= $value['no_so'];
					$temp2[$UNIQ]['product'] 	= $value['product'];
					$temp2[$UNIQ]['no_spk'] 		= $value['no_spk'];
					$temp2[$UNIQ]['id_milik']	= $value['id_milik'];
					
					// $Explode = explode('/',$value['kode_trans']);
					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$value['kode_trans'],'id_production_detail'=>$value['id_pro_det'],'insert_by'=>$username))->result_array();
					$real_harga = (!empty($getDetailSPK[0]['real_harga']))?$getDetailSPK[0]['real_harga']:0;
					$direct_labour = (!empty($getDetailSPK[0]['direct_labour']))?$getDetailSPK[0]['direct_labour']:0;
					$indirect_labour = (!empty($getDetailSPK[0]['indirect_labour']))?$getDetailSPK[0]['indirect_labour']:0;
					$consumable = (!empty($getDetailSPK[0]['consumable']))?$getDetailSPK[0]['consumable']:0;
					$machine = (!empty($getDetailSPK[0]['machine']))?$getDetailSPK[0]['machine']:0;
					$mould_mandrill = (!empty($getDetailSPK[0]['mould_mandrill']))?$getDetailSPK[0]['mould_mandrill']:0;
					$foh_depresiasi = (!empty($getDetailSPK[0]['foh_depresiasi']))?$getDetailSPK[0]['foh_depresiasi']:0;
					$biaya_rutin_bulanan = (!empty($getDetailSPK[0]['biaya_rutin_bulanan']))?$getDetailSPK[0]['biaya_rutin_bulanan']:0;
					$foh_consumable = (!empty($getDetailSPK[0]['foh_consumable']))?$getDetailSPK[0]['foh_consumable']:0;
					
					$nilai = 0;
					$nilai2 = 0;
					if($value2 == 'Direct labour'){
						$nilai = round($direct_labour*$kurs);
					}
					if($value2 == 'Indirect labour'){
						$nilai = round($indirect_labour*$kurs);
					}
					if($value2 == 'Consumable'){
						$nilai = round($consumable*$kurs);
					}
					if($value2 == 'FOH'){
						$nilai = round(($machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
					}
					if($value2 == $value['product']){
						$nilai1 = round(($direct_labour+ $indirect_labour+$consumable + $machine + $mould_mandrill + $foh_depresiasi + $biaya_rutin_bulanan + $foh_consumable)*$kurs);
						$nilai  = $nilai1;
						$nilai2 = $nilai1;
					}					
					
					$temp2[$UNIQ]['costbook'] 		= 0;
					$temp2[$UNIQ]['kurs'] 			= $kurs;
					$temp2[$UNIQ]['total_price'] 		= $nilai;
					$temp2[$UNIQ]['total_price_debet'] 	= $nilai2;
					$temp2[$UNIQ]['created_by'] 		= $username;
					$temp2[$UNIQ]['created_date'] 	= $datetime;

					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;
					$temp2[$UNIQ]['id_trans'] = $id_trans;
				}
			}
		}

		$dataWIP = array_merge($temp,$temp2);
		// echo "<pre>";
		// print_r($dataWIP);
		// exit;
		if(!empty($dataWIP)){
			$this->db->insert_batch('data_erp_wip',$dataWIP);
		}
		// if(!empty($ArrUpdateStock)){
		// 	move_warehouse($ArrUpdateStock,$id_gudang,$id_gudang_ke,$kode_spk_time);
		// }

		//GROUP DATA
		$ArrGroup = [];
		if(!empty($ArrIDSPK)){
			foreach ($ArrIDSPK as $value) {
				if($value > 0){
					$getSummary = $this->db->select('no_so,product,no_spk')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username))->result_array();

					$ArrGroup[$value]['tanggal'] = $dateKurs;
					$ArrGroup[$value]['keterangan'] = 'Gudang produksi to WIP';
					$ArrGroup[$value]['no_so'] = (!empty($getSummary[0]['no_so']))?$getSummary[0]['no_so']:NULL;
					$ArrGroup[$value]['product'] = (!empty($getSummary[0]['product']))?$getSummary[0]['product']:NULL;
					$ArrGroup[$value]['no_spk'] = (!empty($getSummary[0]['no_spk']))?$getSummary[0]['no_spk']:NULL;
					$ArrGroup[$value]['kode_trans'] = $kode_spk_time;
					$ArrGroup[$value]['id_pro_det'] = $value;

					$getDetailSPK = $this->db->get_where('laporan_wip_per_hari_action',array('kode_trans'=>$kode_spk_time,'id_production_detail'=>$value,'insert_by'=>$username))->result_array();
					$qty_awal = (!empty($getDetailSPK[0]['qty_awal']))?$getDetailSPK[0]['qty_awal']:0;
					$qty_akhir = (!empty($getDetailSPK[0]['qty_akhir']))?$getDetailSPK[0]['qty_akhir']:0;
					$id_trans = (!empty($getDetailSPK[0]['id']))?$getDetailSPK[0]['id']:0;

					$ArrGroup[$value]['qty'] = $qty_akhir - $qty_awal + 1;

					$getSummaryMaterial 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username,'id_material <>'=>NULL))->result_array();
					$getSummaryDirect 		= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username,'nm_material'=>'WIP Direct labour'))->result_array();
					$getSummaryIndirect 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username,'nm_material'=>'WIP Indirect labour'))->result_array();
					$getSummaryConsumable 	= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username,'nm_material'=>'WIP Consumable'))->result_array();
					$getSummaryFOH 			= $this->db->select('SUM(total_price) AS nilai')->get_where('data_erp_wip',array('kode_trans'=>$kode_spk_time,'created_by'=>$username,'nm_material'=>'WIP FOH'))->result_array();
					
					$nilai_material 	= (!empty($getSummaryMaterial[0]['nilai']))?$getSummaryMaterial[0]['nilai']:0;
					$nilai_direct 		= (!empty($getSummaryDirect[0]['nilai']))?$getSummaryDirect[0]['nilai']:0;
					$nilai_indirect 	= (!empty($getSummaryIndirect[0]['nilai']))?$getSummaryIndirect[0]['nilai']:0;
					$nilai_consumable 	= (!empty($getSummaryConsumable[0]['nilai']))?$getSummaryConsumable[0]['nilai']:0;
					$nilai_foh 			= (!empty($getSummaryFOH[0]['nilai']))?$getSummaryFOH[0]['nilai']:0;
					$nilai_wip			= $nilai_material + $nilai_direct + $nilai_indirect + $nilai_consumable + $nilai_foh;
					
					$ArrGroup[$value]['nilai_wip'] = $nilai_wip;
					$ArrGroup[$value]['material'] = $nilai_material;
					$ArrGroup[$value]['wip_direct'] =  $nilai_direct;
					$ArrGroup[$value]['wip_indirect'] =  $nilai_indirect;
					$ArrGroup[$value]['wip_consumable'] =  $nilai_consumable;
					$ArrGroup[$value]['wip_foh'] =  $nilai_foh;
					$ArrGroup[$value]['created_by'] = $username;
					$ArrGroup[$value]['created_date'] = $datetime;
					$ArrGroup[$value]['id_trans'] = $id_trans;
					
					$this->db->where('id_trans',$id_trans);
					$this->db->where('nm_material','WIP '.$getSummary[0]['product']);
					$this->db->update('data_erp_wip',array('total_price'=>0,'total_price_debet'=>$nilai_wip)); 
				}
			}
		}


		

		if(!empty($ArrGroup)){
			$this->db->insert_batch('data_erp_wip_group',$ArrGroup);
			// $this->jurnalWIP($id_trans,$closing_date);
		}
		// if(!empty($tempMaterial)){
		// 	$this->db->insert_batch('erp_data_subgudang',$tempMaterial);
		// }
		// if(!empty($ArrJurnalNew)){
		// 	$this->db->insert_batch('tran_warehouse_jurnal_detail',$ArrJurnalNew);
		// }


	}

	function jurnalWIP($idtrans,$closing_date){
		$UserName		= 'manual system 2';
		$DateTime		= $closing_date;
		
		$kodejurnal='JV004';
		
		$wip = $this->db->query("SELECT * FROM data_erp_wip WHERE id_trans ='".$idtrans."'")->result();
		
		$totalwip =0;
		$wiptotal =0; 
		$det_Jurnaltes = [];
			
		foreach($wip AS $data){
			
			$nm_material = $data->nm_material;	
			$product 	 = $data->product;	
			$tgl_voucher = $data->tanggal;	
			$keterangan  = $data->nm_material;
			$id          = $data->id_trans;
			$no_request  = $data->no_spk;	
			$kredit      = $data->total_price;
			$totalwip       = $data->total_price_debet;	
			$wiptotal       += $data->total_price;	
			
			if($nm_material=='WIP Direct labour'){					
				$nokir = '2107-01-02' ;
			}elseif($nm_material=='WIP Indirect labour'){					
				$nokir = '2107-01-03' ;
			}elseif($nm_material=='WIP Consumable'){					
				$nokir = '2107-01-01' ;				
			}elseif($nm_material=='WIP FOH'){					
				$nokir = '2107-01-04' ;
			}
			else{
				$nokir = '1103-01-03' ;
			}

			$debit  = $totalwip;			
			
			if($totalwip != 0 ){
					$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => '1103-03-02',
					'keterangan'    => $keterangan,
					'no_reff'       => $id,
					'debet'         => $wiptotal,
					'kredit'        => 0,
					'jenis_jurnal'  => 'produksi wip',
					'no_request'    => $no_request,
					'stspos'		  =>1
					);
				
			}else{
							
				$det_Jurnaltes[]  = array(
					'nomor'         => '',
					'tanggal'       => $tgl_voucher,
					'tipe'          => 'JV',
					'no_perkiraan'  => $nokir,
					'keterangan'    => $keterangan,
					'no_reff'       => $id,
					'debet'         => 0,
					'kredit'        => $kredit,
					'jenis_jurnal'  => 'produksi wip',
					'no_request'    => $no_request,
					'stspos'		  =>1
					);
			}
			
		}
		
		$this->db->query("delete from jurnaltras WHERE jenis_jurnal='produksi wip' and no_reff ='$id'");
		$this->db->insert_batch('jurnaltras',$det_Jurnaltes); 
		
		$Nomor_JV = $this->Jurnal_model->get_Nomor_Jurnal_Sales('101', $tgl_voucher);
		$Bln	= substr($tgl_voucher,5,2);
		$Thn	= substr($tgl_voucher,0,4);
		$idlaporan = $id;
		$Keterangan_INV = 'Jurnal Produksi - WIP';
		$dataJVhead = array('nomor' => $Nomor_JV, 'tgl' => $tgl_voucher, 'jml' => $totalwip, 'koreksi_no' => '-', 'kdcab' => '101', 'jenis' => 'JV', 'keterangan' => $Keterangan_INV.$idlaporan.' No. Produksi'.$id, 'bulan' => $Bln, 'tahun' => $Thn, 'user_id' => $UserName, 'memo' => $id, 'tgl_jvkoreksi' => $tgl_voucher, 'ho_valid' => '');
		$this->db->insert(DBACC.'.javh',$dataJVhead);
		$datadetail=array();
		foreach ($det_Jurnaltes as $vals) {
			$datadetail = array(
				'tipe'			=> 'JV',
				'nomor'			=> $Nomor_JV,
				'tanggal'		=> $tgl_voucher,
				'no_perkiraan'	=> $vals['no_perkiraan'],
				'keterangan'	=> $vals['keterangan'],
				'no_reff'		=> $vals['no_reff'],
				'debet'			=> $vals['debet'],
				'kredit'		=> $vals['kredit'],
				);
			$this->db->insert(DBACC.'.jurnal',$datadetail);
		}
		unset($det_Jurnaltes);unset($datadetail);
	}

	public function generateFG(){
        $status = 0;
        if($status == 1){
            $SQL    = "SELECT * FROM data_erp_wip_group where id_trans in (2954,2955,2956,2957,2958,2959,2960,2961,2962,2963,2964,2965,2966,2967,2968,2969,2970,2971,2972,2973,2974,2975,2976,2977,2978,2979,2980,2981,2982,2983,2984,2985,2986,2987,2988,2989,2990,2991,2992,2993,2994,2995,2996,2997,2998,2999,3000,3001,3002,3003,3004,3005,3006,3007,3008,3009,3010,3011,3012,3013,3014,3015,3016,3017,3018,3019,3020,3021,3022,3023,3024,3025,3026,3027,3028,3029,3030,3031,3032,3033,3034,3035,3036,3037,3038,3039,3040,3041,3042,3043,3044,3045,3046,3047,3048,3049,3050,3051,3052,3053,3054,3055,3056,3057,3058,3059,3060,3061,3062,3063,3064,3065,3066,3067,3068,3069,3070,3071,3072,3073,3074,3075,3076,3077,3078,3079,3080,3081,3082,3083,3084,3085,3086,3087,3088,3089,3090,3091,3092,3093,3094,3095,3096,3097,3098,3099,3100,3101,3102,3103,3104,3105,3106,3107,3108,3109,3110,3111,3112,3113,3114,3115,3116,3117,3118,3119,3120,3121,3122,3123,3124,3125,3126,3127,3128,3129,3130,3131,3132,3133,3134,3135,3136,3137,3138,3139,3140,3141,3142,3143,3144,3145,3146,3147,3148,3149,3150,3151,3152,3153,3154,3155,3156,3157,3158,3159,3160,3161,3162,3163,3164,3165,3166,3167,3168,3169,3170,3171,3172,3173,3174,3175,3176,3177,3178,3179,3180,3181,3182,3183,3184,3185,3186,3187,3188,3189,3190,3191,3192,3193,3194,3195,3196,3197,3198,3199,3200,3202,3203,3204,3205,3206,3207,3208,3209,3210,3211,3212,3213,3214,3215,3216,3217,3218,3219,3220,3221,3222,3223,3224,3225,3226,3227,3228,3229,3230,3231,3232,3233,3235,3236,3237,3238,3239,3240,3242,3243,3244,3245,3246,3247,3248,3249,3250,3251,3252,3253,3254,3255,3256,3257,3258,3259,3260,3261,3262,3263,3264,3265,3267,3268,3269,3270,3271,3272,3273,3274,3275,3276,3277,3278,3279,3280,3281,3282,3283,3284,3285,3286,3287,3288,3289,3290,3291,3292,3293,3294,3296,3297,3298,3299,3300,3301,3302,3303,3304,3305,3306,3307,3308,3309,3310,3311,3312,3313,3314,3315,3316,3317,3318,3319,3320,3321,3322,3324,3325,3326,3327,3328,3329,3330,3331,3332,3333,3334,3335,3336,3337,3338,3339,3340,3341,3342,3343,3344,3345,3346,3347,3348,3350,3351,3352,3353,3354,3355,3356,3357,3358,3359,3360,3361,3362,3363,3364,3365,3366,3367,3368,3369,3370,3371,3372,3374,3375,3376,3377,3378,3379,3383,3384,3385,3386,3387,3388,3389,3390,3391,3392,3393,3394,3395,3396,3397,3398,3399,3400,3401,3402,3403,3404,3405,3406,3407,3408,3409,3410,3411,3412,3413,3414,3415,3416,3417,3418,3419,3420,3421,3422,3423,3424,3425,3426,3427,3428,3429,3430,3431,3432,3433,3434,3435,3436,3437,3438,3439,3440,3441,3442,3443,3444,3445,3446,3447,3448,3449,3450,3451,3452,3453,3454,3455,3456,3457,3458,3459,3460,3461,3462,3463,3464,3465,3466,3467,3468,3469,3470,3471,3472,3473,3474,3475,3477,3478,3479,3480,3481,3483,3484,3485,3486,3487,3488,3489,3490,3491,3492,3493,3494,3495,3496,3497,3498,3499,3500,3501,3502,3503,3504,3505,3506,3507,3508,3509,3511,3512,3513,3514,3515,3516,3517,3518,3519,3520,3521,3522,3523,3524,3525,3526,3527,3528,3529,3530,3531,3532,3533,3534,3535,3536,3537,3538,3539,3540,3541,3542,3543,3544,3545,3546,3547,3548,3549,3550,3551,3552,3553,3554,3555,3556,3557,3558,3559,3560,3561,3562,3563,3564,3565,3566,3567,3568,3569,3570,3571,3572,3573,3574,3575,3576,3577,3578,3579,3580,3581,3582,3583,3584,3585,3586,3587,3588,3589,3590,3591,3592,3593,3594,3595,3596,3597,3598,3599,3600,3601,3602,3603,3604,3605,3606,3607,3608,3609,3610,3611,3612,3613,3614,3615,3616,3617,3618,3619,3620,3621,3622,3623,3624,3625,3626,3627,3628,3629,3630,3631,3632,3633,3634,3635,3636,3637,3638,3639,3640,3641,3642,3643,3644,3645,3646,3647,3648,3649,3650,3651,3652,3653,3654,3655,3656,3657,3658,3659,3660,3661,3662,3663,3664,3665,3666,3667,3668,3669,3670,3671,3672,3673,3674,3675,3676,3677,3678,3679,3680,3681,3682,3683,3684,3685,3686,3687,3688,3689,3690,3691,3692,3693,3694,3695,3696,3697,3698,3699,3700,3701,3702,3703,3704,3705,3706,3707,3708,3709,3710,3711,3712,3713,3714,3715,3716,3717,3718,3719,3720,3721,3722,3723,3724,3725,3726,3727,3728,3729,3730,3731,3732,3733,3734,3735,3736,3737,3738,3739,3740,3741,3742,3743,3744,3745,3746,3747,3748,3749,3750,3751,3752,3753,3754,3755,3756,3757,3758,3759,3760,3761,3762,3763,3764,3765,3766) AND created_by = 'manual system 2' and jenis='out'";
            $result = $this->db->query($SQL)->result_array();
			
			$ArrInsertBatch = [];
			$nomor = 0;
			foreach ($result as $key => $value) {
				$GetQTY = $this->db->select('product_ke')->get_where('production_detail',['id'=>$value['id_pro_det']])->result_array();
				$ProductKe = (!empty($GetQTY[0]['product_ke']))?$GetQTY[0]['product_ke']:1;
				for ($i=1; $i <= $value['qty']; $i++) {  $nomor++;
					$ArrInsertBatch[$nomor]['tanggal'] = $value['tanggal'];
					$ArrInsertBatch[$nomor]['keterangan'] = 'WIP to Finish Good';
					$ArrInsertBatch[$nomor]['no_so'] = $value['no_so'];
					$ArrInsertBatch[$nomor]['product'] = $value['product'];
					$ArrInsertBatch[$nomor]['no_spk'] = $value['no_spk'];
					$ArrInsertBatch[$nomor]['kode_trans'] = $value['kode_trans'];
					$ArrInsertBatch[$nomor]['id_pro_det'] = $value['id_pro_det'];
					$ArrInsertBatch[$nomor]['qty'] = $value['qty'];
					$ArrInsertBatch[$nomor]['nilai_wip'] = $value['nilai_wip']/$value['qty'];
					$ArrInsertBatch[$nomor]['material'] = $value['material']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_direct'] = $value['wip_direct']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_indirect'] = $value['wip_indirect']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_consumable'] = $value['wip_consumable']/$value['qty'];
					$ArrInsertBatch[$nomor]['wip_foh'] = $value['wip_foh']/$value['qty'];
					$ArrInsertBatch[$nomor]['created_by'] = $value['created_by'];
					$ArrInsertBatch[$nomor]['created_date'] = $value['tanggal'];
					$ArrInsertBatch[$nomor]['id_trans'] = $value['id_trans'];
					$ArrInsertBatch[$nomor]['id_pro'] = $value['id_pro_det'] + $i;
					$ArrInsertBatch[$nomor]['qty_ke'] = $ProductKe++;
					$ArrInsertBatch[$nomor]['nilai_unit'] = $value['nilai_wip']/$value['qty'];
					$ArrInsertBatch[$nomor]['jenis'] = 'in';
				}
				

			}

			if(!empty($ArrInsertBatch)){
				$this->db->insert_batch('data_erp_fg',$ArrInsertBatch);
			}
			
        }
		else{
			echo "Proses Stop !";
		}
    }

}