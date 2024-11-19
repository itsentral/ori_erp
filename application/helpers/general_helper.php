<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function BackToDevelopment(){
    $Arr_Kembali	= array(
        'pesan'		=>'Development Process !!!',
        'status'	=> 2
    );
    echo json_encode($Arr_Kembali);
}

function get_detail_user() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('users')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[strtolower($value['username'])]['nm_lengkap'] = strtolower($value['nm_lengkap']);
    }
    return $ArrGetCategory;
}

function get_detail_unit() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('raw_pieces')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id_satuan']]['kode'] = strtolower($value['kode_satuan']);
    }
    return $ArrGetCategory;
}

function get_detail_material() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('raw_materials')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id_material']]['id_category'] = $value['id_category'];
        $ArrGetCategory[$value['id_material']]['nm_category'] = $value['nm_category'];
        $ArrGetCategory[$value['id_material']]['nm_material'] = $value['nm_material'];
        $ArrGetCategory[$value['id_material']]['idmaterial'] = $value['idmaterial'];
        $ArrGetCategory[$value['id_material']]['id_accurate'] = $value['id_accurate'];
    }
    return $ArrGetCategory;
}

function get_detail_consumable() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->order_by('material_name','asc')->get_where('con_nonmat_new',array('deleted_date'=>NULL))->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['code_group']]['material_name']  = $value['material_name'];
        $ArrGetCategory[$value['code_group']]['nm_barang']      = $value['material_name'].' '.$value['spec'].' '.$value['brand'];
        $ArrGetCategory[$value['code_group']]['spec']           = $value['spec'];
        $ArrGetCategory[$value['code_group']]['kode_excel']     = $value['kode_excel'];
        $ArrGetCategory[$value['code_group']]['id_accurate']    = $value['id_accurate'];
        $ArrGetCategory[$value['code_group']]['material_spec']  = $value['kode_excel'].' - '.$value['spec'];
    }
    return $ArrGetCategory;
}

function get_detail_accessories() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('accessories')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['code_group']  = $value['id_material'];
        $ArrGetCategory[$value['id']]['spec']  = $value['nama'].', '.$value['spesifikasi'];
    }
    return $ArrGetCategory;
}

function get_detail_final_drawing() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('so_detail_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['product'] = $value['id_category'];
        $ArrGetCategory[$value['id']]['no_spk'] = $value['no_spk'];
        $ArrGetCategory[$value['id']]['qty'] = $value['qty'];
    }
    return $ArrGetCategory;
}

function get_detail_sales_order() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('bq_detail_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['product'] = $value['id_category'];
        $ArrGetCategory[$value['id']]['series'] = $value['series'];
    }
    return $ArrGetCategory;
}

function get_detail_selling() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('cost_project_detail')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['category']       = $value['category'];
        $ArrGetCategory[$value['id']]['category_sub']   = $value['caregory_sub'];
        $ArrGetCategory[$value['id']]['area']           = $value['area'];
        $ArrGetCategory[$value['id']]['tujuan']         = $value['tujuan'];
        $ArrGetCategory[$value['id']]['kendaraan']      = $value['kendaraan'];
    }
    return $ArrGetCategory;
}

function get_detail_ipp() {
    $CI =& get_instance();
    $SQL = "SELECT a.no_ipp,a.nm_customer,a.project,b.so_number,a.id_customer,a.status FROM production a LEFT JOIN so_number b ON a.no_ipp=REPLACE(b.id_bq, 'BQ-', '') ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['id_customer']    = $value['id_customer'];
        $ArrGetCategory[$value['no_ipp']]['nm_customer']    = $value['nm_customer'];
        $ArrGetCategory[$value['no_ipp']]['nm_project']     = $value['project'];
        $ArrGetCategory[$value['no_ipp']]['so_number']      = $value['so_number'];
        $ArrGetCategory[$value['no_ipp']]['status']         = $value['status'];
    }
    return $ArrGetCategory;
}

function get_detail_so_number() {
    $CI =& get_instance();
    $SQL = "SELECT a.no_ipp,a.nm_customer,a.project,b.so_number,a.id_customer,a.status FROM production a LEFT JOIN so_number b ON a.no_ipp=REPLACE(b.id_bq, 'BQ-', '') ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['so_number']]['id_customer']    = $value['id_customer'];
        $ArrGetCategory[$value['so_number']]['nm_customer']    = $value['nm_customer'];
        $ArrGetCategory[$value['so_number']]['nm_project']     = $value['project'];
        $ArrGetCategory[$value['so_number']]['so_number']      = $value['so_number'];
        $ArrGetCategory[$value['so_number']]['status']         = $value['status'];
    }
    return $ArrGetCategory;
}

function get_detail_spec_fd() {
    $CI =& get_instance();
    $SQL = "SELECT a.id,a.length,a.thickness,a.total_time,a.man_power,a.man_hours FROM so_detail_header a ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['length']     = $value['length'];
        $ArrGetCategory[$value['id']]['thickness']  = $value['thickness'];
        $ArrGetCategory[$value['id']]['work_hour']  = $value['total_time'];
        $ArrGetCategory[$value['id']]['man_power']  = $value['man_power'];
        $ArrGetCategory[$value['id']]['man_hour']   = $value['man_hours'];
    }
    return $ArrGetCategory;
}

function get_detail_warehouse() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('warehouse')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['nm_gudang'] = $value['nm_gudang'];
    }
    return $ArrGetCategory;
}

function get_material_by_category() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->select('id_material,nm_material,id_category')->order_by('nm_material','asc')->get_where('raw_materials',array('delete'=>'N'))->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id_category']][] = $value;
    }
    return $ArrGetCategory;
}

function getStatusPeriodik($char)
{
    switch($char){
        case 'N':$numb = 'Waiting Submission-blue';break;
        case 'Y':$numb = 'On Progress-orange';break;
        case 'R':$numb = 'Reject-red';break;
        case 'A':$numb = 'Approved-green';break;
        case 'P':$numb = 'Payment-yellow';break;
    }
    return $numb;
}

function get_nama_user($username){
    $CI =& get_instance();
    $get_name = $CI->db->get_where('users',array('username'=>$username))->result();
    $nm_lengkap = (!empty($get_name))?$get_name[0]->nm_lengkap:'not found';
    return $nm_lengkap;
}

function filter_not_in(){
    $filter = "('PRO-IPP19417E','PRO-IPP210404E','PRO-IPP211009L')";
    // $filter = "('PRO-IPP19417E','PRO-IPP210404E','PRO-IPP20198L','PRO-IPP211009L')";
    return $filter;
}

function get_input_produksi_detail() {
    $CI =& get_instance();
    $SQL = "SELECT
                a.id_production_detail AS id,
                a.actual_type AS material_id,
                -- b.nm_material AS nm_material,
                GROUP_CONCAT(DISTINCT b.nm_material ORDER BY b.nm_material ASC SEPARATOR '<br>') AS nm_material,
                b.id_category AS category_id,
                SUM( a.material_terpakai ) AS terpakai 
            FROM
                production_real_detail a
                LEFT JOIN raw_materials b ON a.actual_type = b.id_material 
            WHERE
                a.actual_type LIKE 'MTL-%'  AND a.material_terpakai != '0'
            GROUP BY
                b.id_category, 
                a.id_production_detail 
            ORDER BY 
                a.id_production_detail
            ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']][$value['category_id']]['category'] = $value['category_id'];
        $ArrGetCategory[$value['id']][$value['category_id']]['terpakai'] = $value['terpakai'];
        $ArrGetCategory[$value['id']][$value['category_id']]['nm_material'] = $value['nm_material'];
    }
    return $ArrGetCategory;
}

function get_input_produksi_plus() {
    $CI =& get_instance();
    $SQL = "SELECT
                a.id_production_detail AS id,
                a.actual_type AS material_id,
                -- b.nm_material AS nm_material,
                GROUP_CONCAT(DISTINCT b.nm_material ORDER BY b.nm_material ASC SEPARATOR '<br>') AS nm_material,
                b.id_category AS category_id,
                SUM( a.material_terpakai ) AS terpakai 
            FROM
                production_real_detail_plus a
                LEFT JOIN raw_materials b ON a.actual_type = b.id_material 
            WHERE
                a.actual_type LIKE 'MTL-%'  AND a.material_terpakai != '0'
            GROUP BY
                b.id_category, 
                a.id_production_detail 
            ORDER BY 
                a.id_production_detail
            ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']][$value['category_id']]['category'] = $value['category_id'];
        $ArrGetCategory[$value['id']][$value['category_id']]['terpakai'] = $value['terpakai'];
        $ArrGetCategory[$value['id']][$value['category_id']]['nm_material'] = $value['nm_material'];
    }
    return $ArrGetCategory;
}

function get_input_produksi_plus_exclude() {
    $CI =& get_instance();
    $SQL = "SELECT
                a.id_production_detail AS id,
                a.actual_type AS material_id,
                -- b.nm_material AS nm_material,
                GROUP_CONCAT(DISTINCT b.nm_material ORDER BY b.nm_material ASC SEPARATOR '<br>') AS nm_material,
                b.id_category AS category_id,
                SUM( a.material_terpakai ) AS terpakai 
            FROM
                production_real_detail_plus a
                LEFT JOIN raw_materials b ON a.actual_type = b.id_material 
            WHERE
                a.actual_type LIKE 'MTL-%' AND b.id_category NOT IN ('TYP-0001','TYP-0002','TYP-0003','TYP-0004','TYP-0005','TYP-0006') AND a.material_terpakai != '0'
            GROUP BY
                a.id_production_detail 
            ORDER BY 
                a.id_production_detail
            ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['category'] = $value['category_id'];
        $ArrGetCategory[$value['id']]['terpakai'] = $value['terpakai'];
        $ArrGetCategory[$value['id']]['nm_material'] = $value['nm_material'];
    }
    return $ArrGetCategory;
}

function get_input_produksi_add() {
    $CI =& get_instance();
    $SQL = "SELECT
                a.id_production_detail AS id,
                a.actual_type AS material_id,
                -- b.nm_material AS nm_material,
                GROUP_CONCAT(DISTINCT b.nm_material ORDER BY b.nm_material ASC SEPARATOR '<br>') AS nm_material,
                b.id_category AS category_id,
                SUM( a.material_terpakai ) AS terpakai 
            FROM
                production_real_detail_add a
                LEFT JOIN raw_materials b ON a.actual_type = b.id_material 
            WHERE
                a.actual_type LIKE 'MTL-%' AND a.material_terpakai != '0'
            GROUP BY
                a.id_production_detail 
            ORDER BY 
                a.id_production_detail
            ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']]['category'] = $value['category_id'];
        $ArrGetCategory[$value['id']]['terpakai'] = $value['terpakai'];
        $ArrGetCategory[$value['id']]['nm_material'] = $value['nm_material'];
    }
    return $ArrGetCategory;
}

function get_ipp_release() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('monitoring_ipp')->result_array();
    $ArrGetCategory = [];
    $GET_STATUS = get_detail_ipp();
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['ipp_release_date']   = (!empty($value['ipp_release_date']) AND $GET_STATUS[$value['no_ipp']]['status'] != 'WAITING IPP RELEASE')?date('d-M-Y',strtotime($value['ipp_release_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['est_price_release_date']   = (!empty($value['est_price_release_date']))?date('d-M-Y',strtotime($value['est_price_release_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['so_release_date']   = (!empty($value['so_release_date']))?date('d-M-Y',strtotime($value['so_release_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['app_so_date']   = (!empty($value['app_so_date']))?date('d-M-Y',strtotime($value['app_so_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['so_number']          = (!empty($GET_STATUS[$value['no_ipp']]['so_number']))?$GET_STATUS[$value['no_ipp']]['so_number']:'-';
    }
    return $ArrGetCategory;
}

function get_ipp_enginerring() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('bq_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['bq_release_date'] = (!empty($value['aju_approved_date']) AND $value['aju_approved'] == 'Y')?date('d-M-Y',strtotime($value['aju_approved_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['app_bq_date'] = (!empty($value['approved_date']) AND $value['approved'] == 'Y')?date('d-M-Y',strtotime($value['approved_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['est_release_date'] = (!empty($value['aju_approved_est_date']) AND $value['aju_approved_est'] == 'Y')?date('d-M-Y',strtotime($value['aju_approved_est_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['app_est_date'] = (!empty($value['approved_est_date']) AND $value['approved_est'] == 'Y')?date('d-M-Y',strtotime($value['approved_est_date'])):'-';
        $ArrGetCategory[$value['no_ipp']]['app_quotation_date'] = (!empty($value['app_quo_date']) AND $value['app_quo'] == 'Y')?date('d-M-Y',strtotime($value['app_quo_date'])):'-';
    }
    return $ArrGetCategory;
}

function get_ipp_costing() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('production')->result_array();
    $ArrGetCategory = [];
    $GET_STATUS = get_detail_ipp();
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['est_price_release_date']   = (!empty($value['sts_price_date']))?date('d-M-Y',strtotime($value['sts_price_date'])):'-';
    }
    return $ArrGetCategory;
}

function get_ipp_quotation() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->select("MIN(DATE(so_date)) as min_date, MAX(DATE(so_date)) as max_date, REPLACE(id_bq,'BQ-','') AS no_ipp")->group_by('id_bq')->get_where('bq_detail_header',array('so_sts'=>'Y'))->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['quotation_release_date']   = (!empty($value['min_date']))?($value['min_date'] == $value['max_date'])?date('d-M-Y',strtotime($value['min_date'])):date('d-M-Y',strtotime($value['min_date'])).'<br>to<br>'.date('d-M-Y',strtotime($value['max_date'])):'-';
    }
    return $ArrGetCategory;
}

function get_costbook() {
    $CI =& get_instance();
    $SQL_COST_BOOK = "	SELECT a.id_material, a.price_book
                        FROM price_book a 
                        LEFT JOIN price_book b ON (a.id_material = b.id_material AND a.id < b.id)
                        WHERE b.id IS NULL";
    $REST_COST_BOOK = $CI->db->query($SQL_COST_BOOK)->result_array();
    $GetCostBook = [];
    foreach ($REST_COST_BOOK as $key => $value) {
        $GetCostBook[$value['id_material']] = (!empty($value['price_book']))?$value['price_book']:0;
    }
    return $GetCostBook;
}

function get_final_drawing() {
    $CI =& get_instance();
    $SQL = "SELECT 
                MIN(DATE(approve_date)) as min_date_fd, 
                MAX(DATE(approve_date)) as max_date_fd, 
                MIN(DATE(release_date)) as min_date_so, 
                MAX(DATE(release_date)) as max_date_so,
                REPLACE(id_bq,'BQ-','') AS no_ipp
            FROM so_detail_detail
            WHERE approve = 'Y' OR approve = 'P'
            GROUP BY id_bq
            ";
    $listGetCategory = $CI->db->query($SQL)->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['fd_release_date']   = (!empty($value['min_date_fd']))?($value['min_date_fd'] == $value['max_date_fd'])?date('d-M-Y',strtotime($value['min_date_fd'])):date('d-M-Y',strtotime($value['min_date_fd'])).'<br>to<br>'.date('d-M-Y',strtotime($value['max_date_fd'])):'-';
        $ArrGetCategory[$value['no_ipp']]['app_fd_date']   = (!empty($value['min_date_so']))?($value['min_date_so'] == $value['max_date_so'])?date('d-M-Y',strtotime($value['min_date_so'])):date('d-M-Y',strtotime($value['min_date_so'])).'<br>to<br>'.date('d-M-Y',strtotime($value['max_date_so'])):'-';
    }
    return $ArrGetCategory;
}

function get_ipp_spk() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->select("MIN(DATE(created_date)) as min_date, MAX(DATE(created_date)) as max_date, no_ipp")->group_by('no_ipp')->get_where('production_spk',array('status_id'=>'1'))->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['no_ipp']]['spk_release_date']   = (!empty($value['min_date']))?($value['min_date'] == $value['max_date'])?date('d-M-Y',strtotime($value['min_date'])):date('d-M-Y',strtotime($value['min_date'])).'<br>to<br>'.date('d-M-Y',strtotime($value['max_date'])):'-';
    }
    return $ArrGetCategory;
}


function get_kode_trans_by_key_time() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('warehouse_adjustment')->result_array();
    $ArrGetCategory = [];
    $GET_STATUS = get_detail_ipp();
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['created_date']]['kode_trans']   = (!empty($value['kode_trans']))?$value['kode_trans']:null;
    }
    return $ArrGetCategory;
}

function get_persent_by_subgudang() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('warehouse_adjustment_detail')->result_array();
    $ArrGetCategory = [];
    $GET_STATUS = get_detail_ipp();
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['kode_trans'].'-'.$value['key_gudang'];
        $ArrGetCategory[$KEY]['persen'] = (!empty($value['qty_rusak']))?$value['qty_rusak']:'';
    }
    return $ArrGetCategory;
}

function get_persent_by_subgudang_filter($kode_trans) {
    $CI =& get_instance();
    $ArrGetCategory = [];
    if($kode_trans != '0'){
        $listGetCategory = $CI->db->get_where('warehouse_adjustment_detail',array('kode_trans'=>$kode_trans))->result_array();
        $GET_STATUS = get_detail_ipp();
        foreach ($listGetCategory as $key => $value) {
            $KEY = $value['kode_trans'].'-'.$value['key_gudang'];
            $ArrGetCategory[$KEY]['persen'] = (!empty($value['qty_rusak']))?$value['qty_rusak']:'';
        }
    }
    return $ArrGetCategory;
}

function get_kebutuhanPerMonth() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->select('SUM(kebutuhan_month) AS sum_keb, id_barang')->group_by('id_barang')->get('budget_rutin_detail')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['id_barang'];
        $ArrGetCategory[$KEY]['kebutuhan'] = (!empty($value['sum_keb']))?$value['sum_keb']:0;
    }
    return $ArrGetCategory;
}

function get_kebutuhanPerMonthGudang($id_gudang) {
    $CI =& get_instance();
    $listGetCategory = $CI->db
                        ->select('SUM(a.kebutuhan_month) AS sum_keb, a.id_barang')
                        ->group_by('a.id_barang')
                        ->join('budget_rutin_header b','a.code_budget=b.code_budget')
                        ->get_where('budget_rutin_detail a',array('b.id_gudang'=>$id_gudang))
                        ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['id_barang'];
        $ArrGetCategory[$KEY]['kebutuhan'] = (!empty($value['sum_keb']))?$value['sum_keb']:0;
    }
    return $ArrGetCategory;
}

function get_warehouseStock() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('warehouse_rutin_stock')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['code_group'];
        $ArrGetCategory[$KEY] = (!empty($value['stock']))?$value['stock']:0;
    }
    return $ArrGetCategory;
}

function get_warehouseStockMaterial() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('warehouse_stock')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['id_material'].'-'.$value['id_gudang'];
        $ArrGetCategory[$KEY] = (!empty($value['qty_stock']))?$value['qty_stock']:0;
    }
    return $ArrGetCategory;
}

function get_descDealSO() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('billing_so_product')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_SO = $value['id_milik'];
        $ArrGetCategory[$ID_SO] = $value['customer_item'].'/'.$value['desc'];
    }
    return $ArrGetCategory;
}

function get_idMilikSODeal() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('so_detail_header')->result_array();
    $ArrGetCategory = [];
    $GET_ID_MILIK_SO = get_idMilik();
    foreach ($listGetCategory as $key => $value) {
        $ID_SO = $value['id'];
        $ArrGetCategory[$ID_SO] = (!empty($GET_ID_MILIK_SO[$value['id_milik']]))?$GET_ID_MILIK_SO[$value['id_milik']]:0;
    }
    return $ArrGetCategory;
}

function get_idMilik() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('so_bf_detail_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_SO = $value['id'];
        $ArrGetCategory[$ID_SO] = (!empty($value['id_milik']))?$value['id_milik']:0;
    }
    return $ArrGetCategory;
}

function get_detailFinalDrawing() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('so_detail_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_SO = $value['id'];
        $ArrGetCategory[$ID_SO]['series'] = (!empty($value['series']))?$value['series']:0;
    }
    return $ArrGetCategory;
}

function get_MaxRevisedSellingPrice() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->select('MAX(revised_no) AS revisi, id_bq')->group_by('id_bq')->get('laporan_revised_header')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_SO = $value['id_bq'];
        $ArrGetCategory[$ID_SO] = (!empty($value['revisi']))?$value['revisi']:0;
    }
    return $ArrGetCategory;
}

function get_CountMaterialDealSO($id_bq,$revisi) {
    $CI =& get_instance();
    $no_ipp = str_replace('BQ-','',$id_bq);
    $listGetCategory = $CI->db
                            ->select('
                                a.est_material AS est_selling,
                                a.qty AS qty_selling,
                                a.revised_no AS revisi,
                                a.id_milik AS id_milik,
                                a.est_harga AS est_harga,
                                a.direct_labour AS direct_labour,
                                a.indirect_labour AS indirect_labour,
                                a.machine AS machine,
                                a.mould_mandrill AS mould_mandrill,
                                a.consumable AS consumable,
                                a.foh_consumable AS foh_consumable,
                                a.foh_depresiasi AS foh_depresiasi,
                                a.biaya_gaji_non_produksi AS biaya_gaji_non_produksi,
                                a.biaya_non_produksi AS biaya_non_produksi,
                                a.biaya_rutin_bulanan AS biaya_rutin_bulanan,
                                a.profit AS profit,
                                a.allowance AS allowance,
                                a.total_price_last AS total_last,
                                a.unit_price AS unit_price,
                                a.product_parent AS product,
                                a.id_milik AS id_milik,
                                b.qty AS qty_deal,
                                b.no_ipp AS no_ipp,
                                b.total_deal_usd AS total_deal
                            ')
                            ->from('billing_so_product b')
                            ->join('laporan_revised_detail a','a.id_milik=b.id_milik','left')
                            ->where('b.no_ipp',$no_ipp)
                            ->where('a.id_bq',$id_bq)
                            ->where('a.revised_no',$revisi)
                            ->get()
                            ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_UNIQ    = $value['id_milik'].'-'.$value['revisi'];
        $EST_MAT    = (!empty($value['est_selling']))?$value['est_selling']:0;
        $EST_QTY    = (!empty($value['qty_selling']))?$value['qty_selling']:0;
        $DEAL_QTY   = (!empty($value['qty_deal']))?$value['qty_deal']:0;
        $DEAL_MAT   = ($EST_MAT/$EST_QTY) * $DEAL_QTY;

        $TOTAL_SELLING  = (!empty($value['total_last']))?$value['total_last']:0;
        $UNIT_SELLING   = $TOTAL_SELLING / $EST_QTY;

        $TOTAL_DEAL     = (!empty($value['total_deal']))?$value['total_deal']:0;
        $UNIT_DEAL      = $TOTAL_DEAL / $DEAL_QTY;

        $COMPARE    = 0;
        if($UNIT_DEAL != 0 AND $UNIT_SELLING != 0){
            $COMPARE    = $UNIT_DEAL / $UNIT_SELLING;
        }

        $EST_HARGA  = (($value['est_harga'] / $EST_QTY) * $COMPARE)* $DEAL_QTY;
        $PRICE_UNIT = $value['unit_price'] * $COMPARE;
        $PRICE_QTY  = $PRICE_UNIT * $DEAL_QTY;
        $PROFIT     = $PRICE_QTY + (($value['profit']/100)*$PRICE_QTY);
        $ALLOWANCE  = $PROFIT + (($value['allowance']/100)*$PROFIT);

        //FOH & PROCESS
        $direct_labour              = ($value['direct_labour'] / $EST_QTY) * $COMPARE;
        $indirect_labour            = ($value['indirect_labour'] / $EST_QTY) * $COMPARE;
        $machine                    = ($value['machine'] / $EST_QTY) * $COMPARE;
        $mould_mandrill             = ($value['mould_mandrill'] / $EST_QTY) * $COMPARE;
        $consumable                 = ($value['consumable'] / $EST_QTY) * $COMPARE;
        $foh_consumable             = ($value['foh_consumable'] / $EST_QTY) * $COMPARE;
        $foh_depresiasi             = ($value['foh_depresiasi'] / $EST_QTY) * $COMPARE;
        $biaya_gaji_non_produksi    = ($value['biaya_gaji_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_non_produksi         = ($value['biaya_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_rutin_bulanan        = ($value['biaya_rutin_bulanan'] / $EST_QTY) * $COMPARE;

        $direct_labour_qty              = $direct_labour * $DEAL_QTY;
        $indirect_labour_qty            = $indirect_labour * $DEAL_QTY;
        $machine_qty                    = $machine * $DEAL_QTY;
        $mould_mandrill_qty             = $mould_mandrill * $DEAL_QTY;
        $consumable_qty                 = $consumable * $DEAL_QTY;
        $foh_consumable_qty             = $foh_consumable * $DEAL_QTY;
        $foh_depresiasi_qty             = $foh_depresiasi * $DEAL_QTY;
        $biaya_gaji_non_produksi_qty    = $biaya_gaji_non_produksi * $DEAL_QTY;
        $biaya_non_produksi_qty         = $biaya_non_produksi * $DEAL_QTY;
        $biaya_rutin_bulanan_qty        = $biaya_rutin_bulanan * $DEAL_QTY;

        $ArrGetCategory[$ID_UNIQ]['id_milik']       = $value['id_milik'];
        $ArrGetCategory[$ID_UNIQ]['product']        = $value['product'];
        $ArrGetCategory[$ID_UNIQ]['no_ipp']         = $value['no_ipp'];
        $ArrGetCategory[$ID_UNIQ]['revisi']         = $value['revisi'];
        $ArrGetCategory[$ID_UNIQ]['price_mat']      = $EST_HARGA;
        $ArrGetCategory[$ID_UNIQ]['est_selling']    = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['qty_selling']    = $EST_QTY;
        $ArrGetCategory[$ID_UNIQ]['qty_deal']       = $DEAL_QTY;
        $ArrGetCategory[$ID_UNIQ]['est_deal']       = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['total_deal']     = $TOTAL_DEAL;
        $ArrGetCategory[$ID_UNIQ]['profit']         = $PROFIT - $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['allowance']      = $ALLOWANCE -  $PROFIT;
        $ArrGetCategory[$ID_UNIQ]['direct_labour']              = $direct_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['indirect_labour']            = $indirect_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['machine']                    = $machine_qty;
        $ArrGetCategory[$ID_UNIQ]['mould_mandrill']             = $mould_mandrill_qty;
        $ArrGetCategory[$ID_UNIQ]['consumable']                 = $consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_consumable']             = $foh_consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_depresiasi']             = $foh_depresiasi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_gaji_non_produksi']    = $biaya_gaji_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_non_produksi']         = $biaya_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_rutin_bulanan']        = $biaya_rutin_bulanan_qty;
    }
    return $ArrGetCategory;
}

function get_CountMaterialDealSOTotal($ListIPP,$ListIPP2) {
    $CI =& get_instance();
    $listGetCategory = $CI->db
                            ->select('
                                a.est_material AS est_selling,
                                a.qty AS qty_selling,
                                a.revised_no AS revisi,
                                a.id_milik AS id_milik,
                                a.est_harga AS est_harga,
                                a.direct_labour AS direct_labour,
                                a.indirect_labour AS indirect_labour,
                                a.machine AS machine,
                                a.mould_mandrill AS mould_mandrill,
                                a.consumable AS consumable,
                                a.foh_consumable AS foh_consumable,
                                a.foh_depresiasi AS foh_depresiasi,
                                a.biaya_gaji_non_produksi AS biaya_gaji_non_produksi,
                                a.biaya_non_produksi AS biaya_non_produksi,
                                a.biaya_rutin_bulanan AS biaya_rutin_bulanan,
                                a.profit AS profit,
                                a.allowance AS allowance,
                                a.total_price_last AS total_last,
                                a.unit_price AS unit_price,
                                b.qty AS qty_deal,
                                b.no_ipp AS no_ipp,
                                b.total_deal_usd AS total_deal
                            ')
                            ->from('billing_so_product b')
                            ->join('laporan_revised_detail a','a.id_milik=b.id_milik','left')
                            ->where_in('b.no_ipp',$ListIPP2)
                            ->where_in('a.id_bq',$ListIPP)
                            // ->where('a.revised_no','4')
                            ->get()
                            ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_UNIQ    = $value['id_milik'].'-'.$value['revisi'];
        $EST_MAT    = (!empty($value['est_selling']))?$value['est_selling']:0;
        $EST_QTY    = (!empty($value['qty_selling']))?$value['qty_selling']:0;
        $DEAL_QTY   = (!empty($value['qty_deal']))?$value['qty_deal']:0;
        $DEAL_MAT   = ($EST_MAT/$EST_QTY) * $DEAL_QTY;

        $TOTAL_SELLING  = (!empty($value['total_last']))?$value['total_last']:0;
        $UNIT_SELLING   = $TOTAL_SELLING / $EST_QTY;

        $TOTAL_DEAL     = (!empty($value['total_deal']))?$value['total_deal']:0;
        $UNIT_DEAL      = $TOTAL_DEAL / $DEAL_QTY;

        $COMPARE    = 0;
        if($UNIT_DEAL != 0 AND $UNIT_SELLING != 0){
            $COMPARE    = $UNIT_DEAL / $UNIT_SELLING;
        }

        $EST_HARGA  = (($value['est_harga'] / $EST_QTY) * $COMPARE)* $DEAL_QTY;
        $PRICE_UNIT = $value['unit_price'] * $COMPARE;
        $PRICE_QTY  = $PRICE_UNIT * $DEAL_QTY;
        $PROFIT     = $PRICE_QTY + (($value['profit']/100)*$PRICE_QTY);
        $ALLOWANCE  = $PROFIT + (($value['allowance']/100)*$PROFIT);

        //FOH & PROCESS
        $direct_labour              = ($value['direct_labour'] / $EST_QTY) * $COMPARE;
        $indirect_labour            = ($value['indirect_labour'] / $EST_QTY) * $COMPARE;
        $machine                    = ($value['machine'] / $EST_QTY) * $COMPARE;
        $mould_mandrill             = ($value['mould_mandrill'] / $EST_QTY) * $COMPARE;
        $consumable                 = ($value['consumable'] / $EST_QTY) * $COMPARE;
        $foh_consumable             = ($value['foh_consumable'] / $EST_QTY) * $COMPARE;
        $foh_depresiasi             = ($value['foh_depresiasi'] / $EST_QTY) * $COMPARE;
        $biaya_gaji_non_produksi    = ($value['biaya_gaji_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_non_produksi         = ($value['biaya_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_rutin_bulanan        = ($value['biaya_rutin_bulanan'] / $EST_QTY) * $COMPARE;

        $direct_labour_qty              = $direct_labour * $DEAL_QTY;
        $indirect_labour_qty            = $indirect_labour * $DEAL_QTY;
        $machine_qty                    = $machine * $DEAL_QTY;
        $mould_mandrill_qty             = $mould_mandrill * $DEAL_QTY;
        $consumable_qty                 = $consumable * $DEAL_QTY;
        $foh_consumable_qty             = $foh_consumable * $DEAL_QTY;
        $foh_depresiasi_qty             = $foh_depresiasi * $DEAL_QTY;
        $biaya_gaji_non_produksi_qty    = $biaya_gaji_non_produksi * $DEAL_QTY;
        $biaya_non_produksi_qty         = $biaya_non_produksi * $DEAL_QTY;
        $biaya_rutin_bulanan_qty        = $biaya_rutin_bulanan * $DEAL_QTY;


        $ArrGetCategory[$ID_UNIQ]['no_ipp']         = $value['no_ipp'];
        $ArrGetCategory[$ID_UNIQ]['revisi']         = $value['revisi'];
        $ArrGetCategory[$ID_UNIQ]['price_mat']      = $EST_HARGA;
        $ArrGetCategory[$ID_UNIQ]['est_selling']    = $EST_MAT;
        $ArrGetCategory[$ID_UNIQ]['qty_selling']    = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['qty_deal']       = $DEAL_QTY;
        $ArrGetCategory[$ID_UNIQ]['est_deal']       = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['profit']         = $PROFIT - $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['allowance']      = $ALLOWANCE -  $PROFIT;
        $ArrGetCategory[$ID_UNIQ]['direct_labour']              = $direct_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['indirect_labour']            = $indirect_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['machine']                    = $machine_qty;
        $ArrGetCategory[$ID_UNIQ]['mould_mandrill']             = $mould_mandrill_qty;
        $ArrGetCategory[$ID_UNIQ]['consumable']                 = $consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_consumable']             = $foh_consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_depresiasi']             = $foh_depresiasi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_gaji_non_produksi']    = $biaya_gaji_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_non_produksi']         = $biaya_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_rutin_bulanan']        = $biaya_rutin_bulanan_qty;
    }
    // echo '<pre>';
    // print_r($ArrGetCategory);
    // exit;
    $temp = [];
    foreach ($ArrGetCategory as $key => $value) {
        $key_uniq = $value['no_ipp'].'-'.$value['revisi'];
        if(!array_key_exists($key_uniq, $temp)) {
            $temp[$key_uniq]['est_selling'] = 0;
            $temp[$key_uniq]['profit'] = 0;
            $temp[$key_uniq]['allowance'] = 0;
            $temp[$key_uniq]['price_mat'] = 0;
            $temp[$key_uniq]['direct_labour'] = 0;
            $temp[$key_uniq]['indirect_labour'] = 0;
            $temp[$key_uniq]['machine'] = 0;
            $temp[$key_uniq]['mould_mandrill'] = 0;
            $temp[$key_uniq]['consumable'] = 0;
            $temp[$key_uniq]['foh_consumable'] = 0;
            $temp[$key_uniq]['foh_depresiasi'] = 0;
            $temp[$key_uniq]['biaya_gaji_non_produksi'] = 0;
            $temp[$key_uniq]['biaya_non_produksi'] = 0;
            $temp[$key_uniq]['biaya_rutin_bulanan'] = 0;
        }
        $temp[$key_uniq]['price_mat']   += $value['price_mat'];
        $temp[$key_uniq]['est_selling']    += $value['qty_selling'];
        $temp[$key_uniq]['profit']      += $value['profit'];
        $temp[$key_uniq]['allowance']   += $value['allowance'];
        $temp[$key_uniq]['direct_labour']       += $value['direct_labour'];
        $temp[$key_uniq]['indirect_labour']     += $value['indirect_labour'];
        $temp[$key_uniq]['machine']             += $value['machine'];
        $temp[$key_uniq]['mould_mandrill']      += $value['mould_mandrill'];
        $temp[$key_uniq]['consumable']          += $value['consumable'];
        $temp[$key_uniq]['foh_consumable']      += $value['foh_consumable'];
        $temp[$key_uniq]['foh_depresiasi']      += $value['foh_depresiasi'];
        $temp[$key_uniq]['biaya_gaji_non_produksi'] += $value['biaya_gaji_non_produksi'];
        $temp[$key_uniq]['biaya_non_produksi']      += $value['biaya_non_produksi'];
        $temp[$key_uniq]['biaya_rutin_bulanan']     += $value['biaya_rutin_bulanan'];
    }

    return $temp;
}

function get_DealSOMaterial($id_bq) {
    $CI =& get_instance();
    $no_ipp = str_replace('BQ-','',$id_bq);
    $listGetCategory = $CI->db
                            ->select('
                                a.qty AS qty_deal,
                                a.no_ipp,
                                a.id_milik,
                                a.nm_material,
                                a.satuan,
                                c.fumigasi AS unit_price,
                                a.total_deal_usd AS total_deal,
                                c.weight AS qty_so,
                                c.persen AS profit,
                                c.extra AS allowance,
                                c.price_total AS total_so
                            ')
                            ->from('billing_so_add a')
                            ->join('so_bf_acc_and_mat b','a.id_milik=b.id','left')
                            ->join('cost_project_detail c','b.id_milik=c.id_milik','left')
                            ->where('a.no_ipp',$no_ipp)
                            ->where('b.id_bq',$id_bq)
                            ->where('c.id_bq',$id_bq)
                            ->where('a.category','mat')
                            ->where('c.category','aksesoris')
                            ->get()
                            ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_UNIQ    = $value['id_milik'];
        $QTY_SO     = (!empty($value['qty_so']))?$value['qty_so']:0;
        $QTY_DEAL   = (!empty($value['qty_deal']))?$value['qty_deal']:0;

        $TOTAL_SELLING  = (!empty($value['total_so']))?$value['total_so']:0;
        $UNIT_SELLING   = $TOTAL_SELLING / $QTY_SO;

        $TOTAL_DEAL     = (!empty($value['total_deal']))?$value['total_deal']:0;
        $UNIT_DEAL      = $TOTAL_DEAL / $QTY_DEAL;

        $COMPARE    = 0;
        if($UNIT_DEAL != 0 AND $UNIT_SELLING != 0){
            $COMPARE    = $UNIT_DEAL / $UNIT_SELLING;
        }

        $PRICE_UNIT = ($value['unit_price']/$QTY_SO) * $COMPARE;
        $PRICE_QTY  = $PRICE_UNIT*$QTY_DEAL;
        $PROFIT     = $PRICE_QTY + (($value['profit']/100)*$PRICE_QTY);
        $ALLOWANCE  = $PROFIT + (($value['allowance']/100)*$PROFIT);


        $ArrGetCategory[$ID_UNIQ]['id_milik']       = $value['id_milik'];
        $ArrGetCategory[$ID_UNIQ]['nm_material']    = $value['nm_material'];
        $ArrGetCategory[$ID_UNIQ]['no_ipp']         = $value['no_ipp'];
        $ArrGetCategory[$ID_UNIQ]['satuan']         = $value['satuan'];
        $ArrGetCategory[$ID_UNIQ]['price_unit']     = $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['qty_deal']       = $QTY_DEAL;
        $ArrGetCategory[$ID_UNIQ]['total_deal']     = $TOTAL_DEAL;
        $ArrGetCategory[$ID_UNIQ]['profit']         = $PROFIT - $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['allowance']      = $ALLOWANCE -  $PROFIT;
    }
    return $ArrGetCategory;
}

function get_DealSONonFRP($id_bq) {
    $CI =& get_instance();
    $no_ipp = str_replace('BQ-','',$id_bq);
    $listGetCategory = $CI->db
                            ->select('
                                a.qty AS qty_deal,
                                a.no_ipp,
                                a.id_milik,
                                a.nm_material,
                                a.satuan,
                                c.fumigasi AS unit_price,
                                a.total_deal_usd AS total_deal,
                                c.qty AS qty_so,
                                c.persen AS profit,
                                c.extra AS allowance,
                                c.price_total AS total_so
                            ')
                            ->from('billing_so_add a')
                            ->join('so_bf_acc_and_mat b','a.id_milik=b.id','left')
                            ->join('cost_project_detail c','b.id_milik=c.id_milik','left')
                            ->where('a.no_ipp',$no_ipp)
                            ->where('b.id_bq',$id_bq)
                            ->where('c.id_bq',$id_bq)
                            ->where("(a.category = 'baut' OR a.category = 'gasket' OR a.category = 'lainnya' OR a.category = 'plate')")
                            ->where("(c.category = 'baut' OR c.category = 'gasket' OR c.category = 'lainnya' OR c.category = 'plate')")
                            ->get()
                            ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_UNIQ    = $value['id_milik'];
        $QTY_SO     = (!empty($value['qty_so']))?$value['qty_so']:0;
        $QTY_DEAL   = (!empty($value['qty_deal']))?$value['qty_deal']:0;

        $TOTAL_SELLING  = (!empty($value['total_so']))?$value['total_so']:0;
        $UNIT_SELLING   = $TOTAL_SELLING / $QTY_SO;

        $TOTAL_DEAL     = (!empty($value['total_deal']))?$value['total_deal']:0;
        $UNIT_DEAL      = $TOTAL_DEAL / $QTY_DEAL;

        $COMPARE    = 0;
        if($UNIT_DEAL != 0 AND $UNIT_SELLING != 0){
            $COMPARE    = $UNIT_DEAL / $UNIT_SELLING;
        }

        $PRICE_UNIT = ($value['unit_price']/$QTY_SO) * $COMPARE;
        $PRICE_QTY  = $PRICE_UNIT*$QTY_DEAL;
        $PROFIT     = $PRICE_QTY + (($value['profit']/100)*$PRICE_QTY);
        $ALLOWANCE  = $PROFIT + (($value['allowance']/100)*$PROFIT);


        $ArrGetCategory[$ID_UNIQ]['id_milik']       = $value['id_milik'];
        $ArrGetCategory[$ID_UNIQ]['nm_material']    = $value['nm_material'];
        $ArrGetCategory[$ID_UNIQ]['no_ipp']         = $value['no_ipp'];
        $ArrGetCategory[$ID_UNIQ]['satuan']         = $value['satuan'];
        $ArrGetCategory[$ID_UNIQ]['price_unit']     = $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['qty_deal']       = $QTY_DEAL;
        $ArrGetCategory[$ID_UNIQ]['total_deal']     = $TOTAL_DEAL;
        $ArrGetCategory[$ID_UNIQ]['profit']         = $PROFIT - $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['allowance']      = $ALLOWANCE -  $PROFIT;
    }
    // echo "<pre>";
    // print_r($ArrGetCategory);
    // exit;
    return $ArrGetCategory;
}

function get_valueDepresiasi() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('asset_generate')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $UNIQ = $value['kd_asset'].'-'.$value['bulan'].$value['tahun'];
        $ArrGetCategory[$UNIQ] = (!empty($value['nilai_susut']))?$value['nilai_susut']:0;
    }
    return $ArrGetCategory;
}

function get_detailAktualAdjustmentCheck() {
    $CI =& get_instance();
    $listGetCategory1 = $CI->db->select('keterangan AS layer, UPPER(nm_material) AS material, qty_oke AS qty, kode_trans, update_by, update_date')->get_where('warehouse_adjustment_check',array('qty_oke >'=>0))->result_array();
    $listGetCategory2 = $CI->db->select('layer AS layer, UPPER(actual_type) AS material, terpakai AS qty, kode_trans, created_by AS update_by, created_date AS update_date')->get_where('production_spk_add_hist',array('terpakai >'=>0))->result_array();
    $listGetCategory = array_merge($listGetCategory1,$listGetCategory2);
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $UNIQ = $value['kode_trans'].'-'.$value['update_by'].'-'.$value['update_date'];
        $ArrGetCategory[$UNIQ][] = $value;
    }
    return $ArrGetCategory;
}

function getContainingEditEstimasi($nomor){
    switch($nomor){
        case 1:$numb = 1;break;
        case 2:$numb = 1;break;
        case 3:$numb = 0.6;break;
        case 4:$numb = 0.4;break;
        case 5:$numb = 0.1;break;
        case 6:$numb = 0.9;break;
        default:$numb = 0;
    }

    return $numb;
}

function get_MaterialOutJoint() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->query("select id_bq, id_milik, sum(qty) AS total_material, sum(qty_out) AS total_material_out from request_outgoing where kode_trans is not null group by id_milik")->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $id_milik = $value['id_milik'];
        $ArrGetCategory[$id_milik]['est'] = $value['total_material'];
        $ArrGetCategory[$id_milik]['out'] = $value['total_material_out'];
    }
    return $ArrGetCategory;
}

function get_MaterialEstJoint() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->query("select id_bq, id_milik, sum(material_weight) AS total_material from so_component_detail where id_product LIKE 'FJ-%' and id_material != 'MTL-1903000' group by id_milik")->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $id_milik = $value['id_milik'];
        $ArrGetCategory[$id_milik]['est'] = $value['total_material'];
    }
    return $ArrGetCategory;
}

function spec_global(){
    $CI 		=& get_instance();
    $qHeader		= "SELECT a.*, b.panjang FROM so_detail_header a LEFT JOIN so_component_header b ON a.id=b.id_milik";
    $dim = 'not found (old ipp)';
    $restHeader		= $CI->db->query($qHeader)->result_array();
    $ArrSpec = [];
    foreach ($restHeader as $key => $value) {
        $parent_cat		= $value['id_category'];
        $panjang        = (!empty($value['panjang']))?$value['panjang']:$value['length'];
        if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
            $dim = floatval($value['diameter_1'])." x ".floatval($value['length'])." x ".floatval($value['thickness']);
        }
        elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
            $dim = floatval($value['diameter_1'])." x ".floatval($value['thickness']).", ".$value['type']." ".floatval($value['sudut']);
        }
        elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
            $dim = floatval($value['diameter_1'])." x ".floatval($value['diameter_2'])." x ".floatval($value['thickness']);
        }
        elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
            $dim = floatval($value['diameter_1'])." x ".floatval($value['thickness']);
        }
        elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
            $dim = floatval($value['diameter_1'])." x ".floatval($panjang)." x ".floatval($value['thickness']);
        }
        elseif(
                $parent_cat == 'inlet cone' || 
                $parent_cat == 'taper plate' ||
                $parent_cat == 'rib taper plate' ||
                $parent_cat == 'end plate' ||
                $parent_cat == 'rib end plate' ||
                $parent_cat == 'square flange' ||
                $parent_cat == 'joint saddle' ||
                $parent_cat == 'bellmouth' || 
                $parent_cat == 'plate' || 
                $parent_cat == 'puddle flange' || 
                $parent_cat == 'rib' || 
                $parent_cat == 'joint rib' || 
                $parent_cat == 'support' || 
                $parent_cat == 'spectacle blind' || 
                $parent_cat == 'spacer' || 
                $parent_cat == 'spacer ring' || 
                $parent_cat == 'loose flange' || 
                $parent_cat == 'blind spacer' || 
                $parent_cat == 'joint puddle flange' || 
                $parent_cat == 'blind flange with hole' || 
                $parent_cat == 'laminate pad' || 
                $parent_cat == 'handle' ||
                $parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
                $parent_cat == 'nexus' || 
                $parent_cat == 'csm' || 
                $parent_cat == 'woven roving' || 
                $parent_cat == 'resin' || 
                $parent_cat == 'sic powder' || 
                $parent_cat == 'katalis' || 
                $parent_cat == 'accelator' || 
                $parent_cat == 'putty' || 
                $parent_cat == 'veil' || 
                $parent_cat == 'resin top coat' || 
                $parent_cat == 'build up penebalan' || 
                $parent_cat == 'penebalan mandril' || 
                $parent_cat == 'lining flange' || 
                $parent_cat == 'joint square flange depan 8 mm' || 
                $parent_cat == 'joint square flange belakang 6 mm' || 
                $parent_cat == 'oval flange' || 
                $parent_cat == 'joint oval flange belakang 6 mm' || 
                $parent_cat == 'joint oval flange depan 8 mm' || 
                $parent_cat == 'shimplate 2mm' || 
                $parent_cat == 'shimplate 3mm' || 
                $parent_cat == 'shimplate 5mm' || 
                $parent_cat == 'joint end plate' || 
                $parent_cat == 'joint taper plate' || 
                $parent_cat == 'joint flange' || 
                $parent_cat == 'flange fuji resin' ||
                $parent_cat == 'proses acs' ||
                $parent_cat == 'nozzle holder' ||
                $parent_cat == 'lining' ||
                $parent_cat == 'waterproof plate' ||
                $parent_cat == 'joint waterproof' ||
                $parent_cat == 'blind plate' ||
                $parent_cat == 'y tee' ||
                $parent_cat == 'sudden reducer' ||
                $parent_cat == 'joint sudden reducer' ||
                $parent_cat == 'manhole' ||
                $parent_cat == 'dummy support' ||
                $parent_cat == 'lining coupling' ||
                $parent_cat == 'plate assy' ||
                $parent_cat == 'abr end cover' ||
                $parent_cat == 'abr cover' ||
                $parent_cat == 'lining elbow' ||
                $parent_cat == 'damper' ||
                $parent_cat == 'additional accessories' ||
                $parent_cat == 'cross tee' ||
                $parent_cat == 'horn mouth' ||
                $parent_cat == 'joint plate' ||
                $parent_cat == 'lateral tee' ||
                $parent_cat == 'lining colar' ||
                $parent_cat == 'manhole cover' ||
                $parent_cat == 'mold cover' ||
                $parent_cat == 'orifice plate' ||
                $parent_cat == 'pipe support' ||
                $parent_cat == 'reinforce saddle' ||
                $parent_cat == 'rod' ||
                $parent_cat == 'stiffening ring' ||
                $parent_cat == 'tinuvin solution' ||
                $parent_cat == 'vortex breaker' ||
                $parent_cat == 'inlet cover' ||
                $parent_cat == 'joint spacer' ||
                $parent_cat == 'elbow 5d' ||
                $parent_cat == 'orifice' ||
                $parent_cat == 'lining concrete' ||
                $parent_cat == 'joint nozzle'
            )
        {
            $dim = floatval($value['diameter_1'])." x ".floatval($value['diameter_2'])." x ".floatval($value['thickness']);
        }
        elseif($parent_cat == 'figure 8'){
            $dim = floatval($value['diameter_2'])." x A ".floatval($value['diameter_1']);
        }
        elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
            $dim = floatval($value['diameter_1'])." x ".floatval($value['length'])." x ".floatval($value['thickness']);
        }
        else{
            $dim = floatval($value['diameter_1'])." x ".floatval($value['diameter_2'])." x ".floatval($value['thickness']);
        }

        $ArrSpec[$value['id']] = $dim;
    }
    return $ArrSpec;
}

function NotInProduct(){
    // $NOT_IN_PRODUCT = "('branch joint','field joint','field joint project','frp branch joint','frp field joint','frp joint rib','frp shop joint','joint duct','joint elbow support','joint end plate','joint flange','joint for elbow','joint for jacket','joint lifitng lug','joint lifting lug','joint nozzle','joint oval flange belakang 6 mm','joint oval flange depan 8 mm','joint plate','joint puddle flange','joint rib','joint saddle','joint sleeve','joint spacer','joint square flange belakang 6 mm','joint square flange depan 8 mm','joint square manhole','joint sudden reducer','joint taper plate','joint waterproof','lining field joint coupling','lining joint','shop joint','t reinforce dan joint','proses acs','putty')";
    $NOT_IN_PRODUCT = "('shop jointx')";
	return $NOT_IN_PRODUCT;
}

function NotInProductArray(){
    // $NOT_IN_PRODUCT = array('branch joint','field joint','field joint project','frp branch joint','frp field joint','frp joint rib','frp shop joint','joint duct','joint elbow support','joint end plate','joint flange','joint for elbow','joint for jacket','joint lifitng lug','joint lifting lug','joint nozzle','joint oval flange belakang 6 mm','joint oval flange depan 8 mm','joint plate','joint puddle flange','joint rib','joint saddle','joint sleeve','joint spacer','joint square flange belakang 6 mm','joint square flange depan 8 mm','joint square manhole','joint sudden reducer','joint taper plate','joint waterproof','lining field joint coupling','lining joint','shop joint','t reinforce dan joint','proses acs','putty');
    $NOT_IN_PRODUCT = array('shop jointx');
	return $NOT_IN_PRODUCT;
}

function get_generate_jurnal($kode,$tanggal,$bank=''){
    $CI =& get_instance();
    $template_jurnal = $CI->db->query("SELECT * from ms_generate where tipe='ms_generate_jurnal' and info='".$kode."'")->row();
	$tahun=substr($tanggal,0,2);
	$bulan=substr($tanggal,3,2);
	$angka=1;
    $no_urut = $CI->db->query("SELECT * from ms_generate_jurnal where tipe='".$kode."' and tahun='".$tahun."' and bulan='".$bulan."'")->row();
	if (!empty($no_urut)) {
	  $angka=($no_urut->angka+1);
	  $CI->db->query("update ms_generate_jurnal set angka=(angka+1) where id='".$no_urut->id."'");
    //	  $CI->db->query("update ms_generate_jurnal set angka='".$angka."' where tipe='".$kode."' and tahun='".$tahun."' and bulan='".$bulan."'");
	} else {
	  $CI->db->query("insert into ms_generate_jurnal (tipe,tahun,bulan,angka) values ('".$kode."','".$tahun."','".$bulan."','1')");
	}
	$format=$template_jurnal->kode_2;
	$format=str_replace('info',$template_jurnal->info,$format);
	$format=str_replace('th',$tahun,$format);
	$format=str_replace('bl',$bulan,$format);
	$format=str_replace('xxx',sprintf('%0'.$template_jurnal->kode_1.'d', $angka),$format);
	if($bank!=''){
		$kode_bank = $CI->db->query("SELECT * from ms_generate where tipe='kode_bank' and info='".$bank."'")->row();
		$format=str_replace('bank',$kode_bank->kode_1,$format);
	}
	return $format;
}

function DirectFinishGoodBef(){
    $PRODUCT = "('shop joint','joint nozzle','joint flange','joint saddle','joint end plate','joint taper plate','proses acs','frp branch joint','branch joint','joint plate')";
    return $PRODUCT;
}

function DirectFinishGood(){
    $PRODUCT = "('shop jointx')";
    return $PRODUCT;
}

function listSO_ByDeliveryMaterial_Add(){
    $CI =& get_instance();
    $SQL_listSO = "SELECT DISTINCT no_ipp FROM warehouse_adjustment WHERE no_ipp LIKE '%IPP%' AND kode_trans NOT IN ".getFiledJoint()." ORDER BY no_ipp";
    $rest_listSO = $CI->db->query($SQL_listSO)->result_array();

    $GET_SO = get_detail_ipp();
    $ArrListSO = [];
    foreach ($rest_listSO as $key => $value) {
        $no_ipp = str_replace('BQ-','',$value['no_ipp']);
        $ArrListSO[$key]['no_ipp'] = $no_ipp;
        $ArrListSO[$key]['no_so'] = (!empty($GET_SO[$no_ipp]['so_number']))?$GET_SO[$no_ipp]['so_number']:0;
    }

    return $ArrListSO;
}

function get_finance_manager() {
    $CI =& get_instance();
	$datagen='';
    $dt_gen = $CI->db->query("SELECT info FROM ms_generate WHERE tipe = 'finance_manager' ")->row();
    if (!empty($dt_gen)) {
		$datagen=$dt_gen->info;
	}
    return $datagen;
}

function getFiledJoint() {
    $CI =& get_instance();
    $getKodeTrans = $CI->db->select('kode_trans')->group_by('kode_trans')->get_where('outgoing_field_joint',array('deleted_date'=>NULL))->result_array();

    $ArrJoint = [];
    foreach ($getKodeTrans as $key => $value) {
        $ArrJoint[] = $value['kode_trans'];
    }
    
    $IMPLODE = "('".implode("','", $ArrJoint)."')";

    return $IMPLODE;
}

function get_CountMaterialDealSOEstimasi($id_bq,$revisi) {
    $CI =& get_instance();
    $no_ipp = str_replace('BQ-','',$id_bq);
    $listGetCategory = $CI->db
                            ->select('
                                a.est_material AS est_selling,
                                a.qty AS qty_selling,
                                a.revised_no AS revisi,
                                a.id_milik AS id_milik,
                                a.est_harga AS est_harga,
                                a.direct_labour AS direct_labour,
                                a.indirect_labour AS indirect_labour,
                                a.machine AS machine,
                                a.mould_mandrill AS mould_mandrill,
                                a.consumable AS consumable,
                                a.foh_consumable AS foh_consumable,
                                a.foh_depresiasi AS foh_depresiasi,
                                a.biaya_gaji_non_produksi AS biaya_gaji_non_produksi,
                                a.biaya_non_produksi AS biaya_non_produksi,
                                a.biaya_rutin_bulanan AS biaya_rutin_bulanan,
                                a.profit AS profit,
                                a.allowance AS allowance,
                                a.total_price_last AS total_last,
                                a.unit_price AS unit_price,
                                a.product_parent AS product,
                                a.id_milik AS id_milik,
                                b.qty AS qty_deal,
                                b.no_ipp AS no_ipp,
                                b.total_deal_usd AS total_deal
                            ')
                            ->from('billing_so_product b')
                            ->join('laporan_revised_detail a','a.id_milik=b.id_milik','left')
                            ->where('b.no_ipp',$no_ipp)
                            ->where('a.id_bq',$id_bq)
                            ->where('a.revised_no',$revisi)
                            ->get()
                            ->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ID_UNIQ    = $value['id_milik'].'-'.$value['revisi'];
        $EST_MAT    = (!empty($value['est_selling']))?$value['est_selling']:0;
        $EST_QTY    = (!empty($value['qty_selling']))?$value['qty_selling']:0;
        $DEAL_QTY   = (!empty($value['qty_selling']))?$value['qty_selling']:0;
        $DEAL_MAT   = ($EST_MAT/$EST_QTY) * $DEAL_QTY;

        $TOTAL_SELLING  = (!empty($value['total_last']))?$value['total_last']:0;
        $UNIT_SELLING   = $TOTAL_SELLING / $EST_QTY;

        $TOTAL_DEAL     = (!empty($value['total_last']))?$value['total_last']:0;
        $UNIT_DEAL      = $TOTAL_DEAL / $DEAL_QTY;

        $COMPARE    = 0;
        if($UNIT_DEAL != 0 AND $UNIT_SELLING != 0){
            $COMPARE    = $UNIT_DEAL / $UNIT_SELLING;
        }

        $EST_HARGA  = (($value['est_harga'] / $EST_QTY) * $COMPARE)* $DEAL_QTY;
        $PRICE_UNIT = $value['unit_price'] * $COMPARE;
        $PRICE_QTY  = $PRICE_UNIT * $DEAL_QTY;
        $PROFIT     = $PRICE_QTY + (($value['profit']/100)*$PRICE_QTY);
        $ALLOWANCE  = $PROFIT + (($value['allowance']/100)*$PROFIT);

        //FOH & PROCESS
        $direct_labour              = ($value['direct_labour'] / $EST_QTY) * $COMPARE;
        $indirect_labour            = ($value['indirect_labour'] / $EST_QTY) * $COMPARE;
        $machine                    = ($value['machine'] / $EST_QTY) * $COMPARE;
        $mould_mandrill             = ($value['mould_mandrill'] / $EST_QTY) * $COMPARE;
        $consumable                 = ($value['consumable'] / $EST_QTY) * $COMPARE;
        $foh_consumable             = ($value['foh_consumable'] / $EST_QTY) * $COMPARE;
        $foh_depresiasi             = ($value['foh_depresiasi'] / $EST_QTY) * $COMPARE;
        $biaya_gaji_non_produksi    = ($value['biaya_gaji_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_non_produksi         = ($value['biaya_non_produksi'] / $EST_QTY) * $COMPARE;
        $biaya_rutin_bulanan        = ($value['biaya_rutin_bulanan'] / $EST_QTY) * $COMPARE;

        $direct_labour_qty              = $direct_labour * $DEAL_QTY;
        $indirect_labour_qty            = $indirect_labour * $DEAL_QTY;
        $machine_qty                    = $machine * $DEAL_QTY;
        $mould_mandrill_qty             = $mould_mandrill * $DEAL_QTY;
        $consumable_qty                 = $consumable * $DEAL_QTY;
        $foh_consumable_qty             = $foh_consumable * $DEAL_QTY;
        $foh_depresiasi_qty             = $foh_depresiasi * $DEAL_QTY;
        $biaya_gaji_non_produksi_qty    = $biaya_gaji_non_produksi * $DEAL_QTY;
        $biaya_non_produksi_qty         = $biaya_non_produksi * $DEAL_QTY;
        $biaya_rutin_bulanan_qty        = $biaya_rutin_bulanan * $DEAL_QTY;

        $ArrGetCategory[$ID_UNIQ]['id_milik']       = $value['id_milik'];
        $ArrGetCategory[$ID_UNIQ]['product']        = $value['product'];
        $ArrGetCategory[$ID_UNIQ]['no_ipp']         = $value['no_ipp'];
        $ArrGetCategory[$ID_UNIQ]['revisi']         = $value['revisi'];
        $ArrGetCategory[$ID_UNIQ]['price_mat']      = $EST_HARGA;
        $ArrGetCategory[$ID_UNIQ]['est_selling']    = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['qty_selling']    = $EST_QTY;
        $ArrGetCategory[$ID_UNIQ]['qty_deal']       = $DEAL_QTY;
        $ArrGetCategory[$ID_UNIQ]['est_deal']       = $DEAL_MAT;
        $ArrGetCategory[$ID_UNIQ]['total_deal']     = $TOTAL_DEAL;
        $ArrGetCategory[$ID_UNIQ]['profit']         = $PROFIT - $PRICE_QTY;
        $ArrGetCategory[$ID_UNIQ]['allowance']      = $ALLOWANCE -  $PROFIT;
        $ArrGetCategory[$ID_UNIQ]['direct_labour']              = $direct_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['indirect_labour']            = $indirect_labour_qty;
        $ArrGetCategory[$ID_UNIQ]['machine']                    = $machine_qty;
        $ArrGetCategory[$ID_UNIQ]['mould_mandrill']             = $mould_mandrill_qty;
        $ArrGetCategory[$ID_UNIQ]['consumable']                 = $consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_consumable']             = $foh_consumable_qty;
        $ArrGetCategory[$ID_UNIQ]['foh_depresiasi']             = $foh_depresiasi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_gaji_non_produksi']    = $biaya_gaji_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_non_produksi']         = $biaya_non_produksi_qty;
        $ArrGetCategory[$ID_UNIQ]['biaya_rutin_bulanan']        = $biaya_rutin_bulanan_qty;
    }
    return $ArrGetCategory;
}

function get_cost_foh() {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get('cost_foh')->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $ArrGetCategory[$value['id']] = $value['std_rate'];
    }
    return $ArrGetCategory;
}

function get_estimasi_material_per_spk($id_milik){
    $CI =& get_instance();

    $Q1 = "SELECT id_material, nm_material, MAX(last_cost) AS berat from so_component_detail where id_milik = '".$id_milik."' and id_material != 'MTL-1903000'and id_category = 'TYP-0001' AND last_cost > 0 GROUP BY detail_name";
    $Q2 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' and id_category != 'TYP-0001' AND last_cost > 0 GROUP BY id_material";
    $Q3 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail_plus where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' AND last_cost > 0 GROUP BY id_material";
    $Q4 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail_add where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' AND last_cost > 0 GROUP BY id_material";

    $R1 = $CI->db->query($Q1)->result_array();
    $R2 = $CI->db->query($Q2)->result_array();
    $R3 = $CI->db->query($Q3)->result_array();
    $R4 = $CI->db->query($Q4)->result_array();

    $RESULT = array_merge($R1,$R2,$R3,$R4);

    $result = [];
    foreach ($RESULT as $key => $value) {
        $key_uniq = $value['id_material'];
        if(!array_key_exists($key_uniq, $result)) {
            $result[$key_uniq] = 0;
        }
        $result[$key_uniq] += $value['berat'];
    }

    return $result;
}

function get_estimasi_material_per_spk_detail($id_milik){
    $CI =& get_instance();

    $CHECK_ID = $CI->db->get_where('so_detail_header',array('id'=>$id_milik))->result();
    $product = (!empty($CHECK_ID[0]->id_category))?$CHECK_ID[0]->id_category:null;
    if($product == 'field joint' OR $product == 'shop joint' OR $product == 'branch joint'){
        $Q1 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail where id_milik = '".$id_milik."' and id_material != 'MTL-1903000'and id_category = 'TYP-0001' AND last_cost > 0 GROUP BY detail_name";
    }
    else{
        $Q1 = "SELECT id_material, nm_material, MAX(last_cost) AS berat from so_component_detail where id_milik = '".$id_milik."' and id_material != 'MTL-1903000'and id_category = 'TYP-0001' AND last_cost > 0 GROUP BY detail_name";
    }
    $Q2 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' and id_category != 'TYP-0001' AND last_cost > 0 GROUP BY id_material";
    $Q3 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail_plus where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' AND last_cost > 0 GROUP BY id_material";
    $Q4 = "SELECT id_material, nm_material, SUM(last_cost) AS berat from so_component_detail_add where id_milik = '".$id_milik."' and id_material != 'MTL-1903000' AND last_cost > 0 GROUP BY id_material";

    $R1 = $CI->db->query($Q1)->result_array();
    $R2 = $CI->db->query($Q2)->result_array();
    $R3 = $CI->db->query($Q3)->result_array();
    $R4 = $CI->db->query($Q4)->result_array();

    $RESULT = array_merge($R1,$R2,$R3,$R4);

    $result = [];
    foreach ($RESULT as $key => $value) {
        $key_uniq = $value['id_material'];
        if(!array_key_exists($key_uniq, $result)) {
            $result[$key_uniq]['berat'] = 0;
        }
        $result[$key_uniq]['id_material'] = $value['id_material'];
        $result[$key_uniq]['berat'] += $value['berat'];
    }

    return $result;
}

function getPriceBookByDate($dateFilter){
    $CI =& get_instance();

    $SQLPriceBook = "	SELECT
                            MAX( a.id ) AS id,
                            a.id_material 
                        FROM
                            price_book a 
                        WHERE
                            a.updated_date >= '2023-05-11 21:24:48' 
                            AND DATE( a.updated_date ) <= '".$dateFilter."'
                        GROUP BY
                            a.id_material";
    $resultPriceBook = $CI->db->query($SQLPriceBook)->result_array();

    $result = $CI->db->get('price_book')->result_array();
    $ArrResult = [];
    foreach ($result as $key => $value) {
        $ArrResult[$value['id']] = $value['price_book'];
    }
    
    $GET_PRICE_BOOK = $ArrResult;
    $ArrPriceBook = [];
    foreach ($resultPriceBook as $key => $value) {
        $priceBook = (!empty($GET_PRICE_BOOK[$value['id']]))?$GET_PRICE_BOOK[$value['id']]:0;
        $ArrPriceBook[$value['id_material']] = $priceBook;
    }

    return $ArrPriceBook;
}

function getPriceBookByDatesubgudang($dateFilter){ 
    $CI =& get_instance();

    $SQLPriceBook = "	SELECT
                            MAX( a.id ) AS id,
                            a.id_material 
                        FROM
                            price_book_subgudang a 
                        WHERE
                            a.updated_date >= '2023-05-11 21:24:48' 
                            AND DATE( a.updated_date ) <= '".$dateFilter."'
                        GROUP BY
                            a.id_material";
    $resultPriceBook = $CI->db->query($SQLPriceBook)->result_array();

    $result = $CI->db->get('price_book_subgudang')->result_array();
    $ArrResult = [];
    foreach ($result as $key => $value) {
        $ArrResult[$value['id']] = $value['price_book'];
    }
    
    $GET_PRICE_BOOK = $ArrResult;
    $ArrPriceBook = [];
    foreach ($resultPriceBook as $key => $value) {
        $priceBook = (!empty($GET_PRICE_BOOK[$value['id']]))?$GET_PRICE_BOOK[$value['id']]:0;
        $ArrPriceBook[$value['id_material']] = $priceBook;
    }

    return $ArrPriceBook;
}

function getPriceBookByDateproduksi($dateFilter){ 
    $CI =& get_instance();

    $SQLPriceBook = "	SELECT
                            MAX( a.id ) AS id,
                            a.id_material 
                        FROM
                            price_book_produksi a 
                        WHERE
                            a.updated_date >= '2023-05-11 21:24:48' 
                            AND DATE( a.updated_date ) <= '".$dateFilter."'
                        GROUP BY
                            a.id_material";
    $resultPriceBook = $CI->db->query($SQLPriceBook)->result_array();

    $result = $CI->db->get('price_book_produksi')->result_array();
    $ArrResult = [];
    foreach ($result as $key => $value) {
        $ArrResult[$value['id']] = $value['price_book'];
    }
    
    $GET_PRICE_BOOK = $ArrResult;
    $ArrPriceBook = [];
    foreach ($resultPriceBook as $key => $value) {
        $priceBook = (!empty($GET_PRICE_BOOK[$value['id']]))?$GET_PRICE_BOOK[$value['id']]:0;
        $ArrPriceBook[$value['id_material']] = $priceBook;
    }

    return $ArrPriceBook;
}

function get_warehouseStockAllMaterial() {
    $CI =& get_instance();
    $listGetCategory    = $CI->db->get('warehouse_stock')->result_array();
    $ArrGetCategory     = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['id_material'].'-'.$value['id_gudang'];
        $ArrGetCategory[$KEY]['stock']      = (!empty($value['qty_stock']))?$value['qty_stock']:0;
        $ArrGetCategory[$KEY]['booking']    = (!empty($value['qty_booking']))?$value['qty_booking']:0;
        $ArrGetCategory[$KEY]['rusak']      = (!empty($value['qty_rusak']))?$value['qty_rusak']:0;
        $ArrGetCategory[$KEY]['id']         = (!empty($value['id']))?$value['id']:NULL;
        $ArrGetCategory[$KEY]['idmaterial']    = (!empty($value['idmaterial']))?$value['idmaterial']:NULL;
        $ArrGetCategory[$KEY]['nm_material']   = (!empty($value['nm_material']))?$value['nm_material']:NULL;
        $ArrGetCategory[$KEY]['id_category']   = (!empty($value['id_category']))?$value['id_category']:NULL;
        $ArrGetCategory[$KEY]['nm_category']   = (!empty($value['nm_category']))?$value['nm_category']:NULL;
    }
    return $ArrGetCategory;
}

function get_CheckBooking($no_ipp) {
    $CI =& get_instance();
    $listGetCategory    = $CI->db->limit(1)->get_where('warehouse_history',array('kd_gudang_ke'=>'BOOKING','no_ipp'=>$no_ipp))->result_array();
    $CHECK = (!empty($listGetCategory))?TRUE:FALSE;
    return $CHECK;
}

function getEstimasiDeadstok($kode,$qty,$layer,$category,$resin=null,$tanda=null) {
    $CI =& get_instance();
    $ArrWhere = array(
                    'detail_name'=>$layer,
                    'category'=>$category,
                    'kode'=>$kode,
                    'id_material !='=>'MTL-1903000'
                );
    if($resin == 1 AND $tanda == 1){
        $ArrWhere = array(
            'detail_name'=>$layer,
            'category'=>$category,
            'kode'=>$kode,
            'id_material !='=>'MTL-1903000',
            'id_category' => 'TYP-0001'
        );
    }

    if($resin == 1 AND $tanda == 0){
        $ArrWhere = array(
            'detail_name'=>$layer,
            'category'=>$category,
            'kode'=>$kode,
            'id_material !='=>'MTL-1903000',
            'id_category !=' => 'TYP-0001'
        );
    }

    $listGetCategory    = $CI->db
                                ->select('category, id_material, nm_material, id_category, nm_category, SUM(last_cost) AS berat, detail_name')
                                ->group_by('id_material, detail_name, category')
                                ->get_where('deadstok_estimasi',$ArrWhere)
                                ->result_array();
    $ArrGetCategory     = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $key;
        $ArrGetCategory[$KEY]['category']       = $value['category'];
        $ArrGetCategory[$KEY]['detail_name']    = $value['detail_name'];
        $ArrGetCategory[$KEY]['id_material']    = $value['id_material'];
        $ArrGetCategory[$KEY]['nm_material']    = $value['nm_material'];
        $ArrGetCategory[$KEY]['id_category']    = $value['id_category'];
        $ArrGetCategory[$KEY]['nm_category']    = $value['nm_category'];
        $ArrGetCategory[$KEY]['berat']          = $value['berat'] * $qty;
        $ArrGetCategory[$KEY]['berat_all']      = $value['berat'] * $qty;
        $ArrGetCategory[$KEY]['berat_unit']     = $value['berat'];
    }
    return $ArrGetCategory;
}

function getEstimasiVsAktual($id_milik, $no_ipp, $qty, $id_production_detail) {
    $CI =& get_instance();

    $restHeader		= $CI->db->get_where('so_component_header',array('id_milik'=>$id_milik))->result_array();
    if ($restHeader[0]['parent_product']!='branch joint' && $restHeader[0]['parent_product']!='field joint' && $restHeader[0]['parent_product']!='shop joint'){
    $qDetail1		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name IN ('LINER THIKNESS / CB','GLASS','RESIN AND ADD') GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name IN ('LINER THIKNESS / CB','RESIN AND ADD') GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name IN ('LINER THIKNESS / CB','GLASS','RESIN AND ADD') ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name IN ('LINER THIKNESS / CB','RESIN AND ADD') ORDER BY a.id_detail DESC LIMIT 1)
        ";
    }
    else{
        $qDetail1		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name IN ('LINER THIKNESS / CB','GLASS','RESIN AND ADD') GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name IN ('LINER THIKNESS / CB','RESIN AND ADD') GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'LINER THIKNESS / CB' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name IN ('LINER THIKNESS / CB','GLASS','RESIN AND ADD') ORDER BY a.id_detail)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name IN ('LINER THIKNESS / CB','RESIN AND ADD') ORDER BY a.id_detail DESC LIMIT 1)
        ";
    }
    $qDetail2		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 1' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 1' ORDER BY a.id_detail DESC LIMIT 1)
        ";
    $qDetail3		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR NECK 2' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR NECK 2' ORDER BY a.id_detail DESC LIMIT 1)
        ";
    $qDetail4		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'STRUKTUR THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'STRUKTUR THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
        ";
    $qDetail5		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'EXTERNAL LAYER THICKNESS' GROUP BY a.id_material)
        UNION
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'EXTERNAL LAYER THICKNESS' ORDER BY a.id_detail DESC LIMIT 1)
        ";
    $qDetail6		= 	"
        (SELECT 'detail' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail a LEFT JOIN production_real_detail b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE a.id_bq='BQ-".$no_ipp."'  AND b.id_production_detail='".$id_production_detail."'AND a.id_milik='".$id_milik."' AND a.id_category NOT IN ( 'TYP-0001', 'TYP-0030' ) AND a.detail_name = 'TOPCOAT' GROUP BY a.id_material)
        UNION
        (SELECT 'plus' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga, b.material_terpakai AS real_material FROM so_component_detail_plus a LEFT JOIN production_real_detail_plus b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
        UNION
        (SELECT 'add' AS tipe, a.id_detail, a.id_milik, a.id_bq, a.detail_name, a.nm_category, a.nm_material, (a.last_cost * ".$qty.") AS est_material, a.price_mat AS est_harga,  b.material_terpakai AS real_material FROM so_component_detail_add a LEFT JOIN production_real_detail_add b ON a.id_detail = b.id_detail WHERE  a.id_bq='BQ-".$no_ipp."' AND b.id_production_detail='".$id_production_detail."' AND a.id_milik='".$id_milik."' AND a.id_category IN ( 'TYP-0001') AND a.id_category NOT IN ('TYP-0030') AND a.detail_name = 'TOPCOAT' ORDER BY a.id_detail DESC LIMIT 1)
        ";
    // echo $qDetail1; exit;
    $restDetail1	= $CI->db->query($qDetail1)->result_array();
    $restDetail2	= $CI->db->query($qDetail2)->result_array();
    $restDetail3	= $CI->db->query($qDetail3)->result_array();
    $restDetail4	= $CI->db->query($qDetail4)->result_array();
    $restDetail5	= $CI->db->query($qDetail5)->result_array();
    $restDetail6	= $CI->db->query($qDetail6)->result_array();
    $restDetail		= array_merge($restDetail1,$restDetail2,$restDetail3, $restDetail4, $restDetail5, $restDetail6);

    $ArrayResult = [];
    $SUM_MAT_EST = 0;
    $SUM_MAT_ACT = 0;
    $SUM_PRC_EST = 0;
    $SUM_PRC_ACT = 0;
    foreach($restDetail as $key => $row_Cek){
        $est_material	= (!empty($row_Cek['est_material']))?$row_Cek['est_material']:0;
        $est_harga	    = (!empty($row_Cek['est_harga']))?$row_Cek['est_harga']:0;
        $estHarga       = $est_material * $est_harga;
        $real_material	= (!empty($row_Cek['real_material']))?$row_Cek['real_material']:0;
        $estHargaReal   = $real_material * $est_harga;

        $SUM_MAT_EST += $est_material;
        $SUM_PRC_EST +=  $estHarga;
        $SUM_MAT_ACT += $real_material;
        $SUM_PRC_ACT += $estHargaReal;
    }

    $ArrayResult['est_mat']     = $SUM_MAT_EST;
    $ArrayResult['act_mat']     = $SUM_PRC_EST;
    $ArrayResult['est_price']   = $SUM_MAT_ACT;
    $ArrayResult['act_price']   = $SUM_PRC_ACT;

    return $ArrayResult;
}

function getGudangProject() {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('category'=>'project'))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:0;
    return $id_gudang;
}

function getGudangIndirect() {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('category'=>'indirect'))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:0;
    return $id_gudang;
}

function getGudangHouseHold() {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('category'=>'household'))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:0;
    return $id_gudang;
}

function getSubGudangProject() {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('category'=>'sub project'))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:0;
    return $id_gudang;
}

function getSubGudangCustomer($id_customer) {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('nm_gudang'=>$id_customer))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:NULL;
    return $id_gudang;
}

function getGudangFG() {
    $CI =& get_instance();
    $getGudang    = $CI->db->limit(1)->get_where('warehouse',array('category'=>'fg'))->result_array();
    $id_gudang = (!empty($getGudang[0]['id']))?$getGudang[0]['id']:NULL;
    return $id_gudang;
}

function get_warehouseStockProject($id_gudang) {
    $CI =& get_instance();
    $listGetCategory = $CI->db->get_where('warehouse_rutin_stock',array('gudang'=>$id_gudang))->result_array();
    $ArrGetCategory = [];
    foreach ($listGetCategory as $key => $value) {
        $KEY = $value['code_group'];
        $ArrGetCategory[$KEY] = (!empty($value['stock']))?$value['stock']:0;
    }
    return $ArrGetCategory;
}

function move_warehouse_barang_stok($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_trans=null){
    $CI 	=& get_instance();
    $dateTime		= date('Y-m-d H:i:s');
    $UserName 		= $CI->session->userdata['ORI_User']['username'];
    $kode_trans 	= $kode_trans;
    
    $kd_gudang_ke	= NULL;
    $kd_gudang_dari	= NULL;
    if($id_gudang_ke != null){
        $kd_gudang_ke 	    = get_name('warehouse', 'kode', 'id', $id_gudang_ke);
    }
    if($id_gudang_dari != null){
        $kd_gudang_dari 	= get_name('warehouse', 'kode', 'id', $id_gudang_dari);
    }
    //grouping sum
    $temp = [];
    foreach($ArrUpdateStock as $value) {
        if(!array_key_exists($value['id'], $temp)) {
            $temp[$value['id']] = 0;
        }
        $temp[$value['id']] += $value['qty'];
    }

    $ArrStock = array();
    $ArrHist = array();
    $ArrStockInsert = array();
    $ArrHistInsert = array();

    $ArrStock2 = array();
    $ArrHist2 = array();
    $ArrStockInsert2 = array();
    $ArrHistInsert2 = array();

    foreach ($temp as $key => $value) {
        //PENGURANGAN GUDANG
        if($id_gudang_dari != null){
            $rest_pusat = $CI->db->get_where('warehouse_rutin_stock',array('gudang'=>$id_gudang_dari, 'code_group'=>$key))->result();

            if(!empty($rest_pusat)){
                $ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
                $ArrStock[$key]['stock'] 	    = $rest_pusat[0]->stock - $value;
                $ArrStock[$key]['booking'] 	    = $rest_pusat[0]->booking - $value;
                $ArrStock[$key]['update_by'] 	= $UserName;
                $ArrStock[$key]['update_date'] 	= $dateTime;

                $ArrHist[$key]['code_group'] 	    = $key;
                $ArrHist[$key]['category_awal'] 	= $rest_pusat[0]->category_awal;
                $ArrHist[$key]['category_code'] 	= $rest_pusat[0]->category_code;
                $ArrHist[$key]['material_name'] 	= $rest_pusat[0]->material_name;
                $ArrHist[$key]['id_gudang'] 		= $id_gudang_dari;
                $ArrHist[$key]['gudang'] 		    = $kd_gudang_dari;
                $ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_dari;
                $ArrHist[$key]['gudang_dari'] 	    = $kd_gudang_dari;
                $ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_ke;
                $ArrHist[$key]['gudang_ke'] 		= $kd_gudang_ke;
                $ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->stock;
                $ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->stock - $value;
                $ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->rusak;
                $ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->rusak;
                $ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->booking;
                $ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->booking - $value;
                $ArrHist[$key]['no_trans'] 			= $kode_trans;
                $ArrHist[$key]['jumlah_qty'] 		= $value;
                $ArrHist[$key]['ket'] 				= 'pengurangan gudang';
                $ArrHist[$key]['update_by'] 		= $UserName;
                $ArrHist[$key]['update_date'] 		= $dateTime;
            }
            else{
                $restMat	= $CI->db->get_where('con_nonmat_new',array('code_group'=>$key))->result();

                $ArrStockInsert[$key]['code_group'] 	= $restMat[0]->code_group;
                $ArrStockInsert[$key]['category_awal'] 	= $restMat[0]->category_awal;
                $ArrStockInsert[$key]['material_name'] 	= $restMat[0]->material_name;
                $ArrStockInsert[$key]['gudang'] 		= $id_gudang_dari;
                $ArrStockInsert[$key]['stock'] 		    = 0 - $value;
                $ArrStockInsert[$key]['update_by'] 		= $UserName;
                $ArrStockInsert[$key]['update_date'] 	= $dateTime;

                $ArrHistInsert[$key]['code_group'] 	    = $key;
                $ArrHistInsert[$key]['category_awal'] 	= $restMat[0]->category_awal;
                $ArrHistInsert[$key]['material_name'] 	= $restMat[0]->material_name;
                $ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_dari;
                $ArrHistInsert[$key]['gudang'] 		    = $kd_gudang_dari;
                $ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_dari;
                $ArrHistInsert[$key]['gudang_dari'] 	= $kd_gudang_dari;
                $ArrHistInsert[$key]['id_gudang_ke']    = $id_gudang_ke;
                $ArrHistInsert[$key]['gudang_ke'] 		= $kd_gudang_ke;
                $ArrHistInsert[$key]['qty_stock_awal'] 	= 0;
                $ArrHistInsert[$key]['qty_stock_akhir'] = 0 - $value;
                $ArrHistInsert[$key]['qty_rusak_awal'] 	= 0;
                $ArrHistInsert[$key]['qty_rusak_akhir'] = 0;
                $ArrHistInsert[$key]['qty_booking_awal']    = 0;
                $ArrHistInsert[$key]['qty_booking_akhir']   = 0;
                $ArrHistInsert[$key]['no_trans'] 		= $kode_trans;
                $ArrHistInsert[$key]['jumlah_qty'] 		= $value;
                $ArrHistInsert[$key]['ket'] 			= 'pengurangan gudang (insert new)';
                $ArrHistInsert[$key]['update_by'] 		= $UserName;
                $ArrHistInsert[$key]['update_date'] 	= $dateTime;
            }
        }

        //PENAMBAHAN GUDANG
        if($id_gudang_ke != null){
            $rest_pusat = $CI->db->get_where('warehouse_rutin_stock',array('gudang'=>$id_gudang_ke, 'code_group'=>$key))->result();

            if(!empty($rest_pusat)){
                $ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
                $ArrStock2[$key]['stock'] 	    = $rest_pusat[0]->stock + $value;
                $ArrStock2[$key]['update_by'] 	= $UserName;
                $ArrStock2[$key]['update_date'] = $dateTime;

                $ArrHist2[$key]['code_group'] 	    = $key;
                $ArrHist2[$key]['category_awal'] 	= $rest_pusat[0]->category_awal;
                $ArrHist2[$key]['category_code'] 	= $rest_pusat[0]->category_code;
                $ArrHist2[$key]['material_name'] 	= $rest_pusat[0]->material_name;
                $ArrHist2[$key]['id_gudang'] 		= $id_gudang_ke;
                $ArrHist2[$key]['gudang'] 		    = $kd_gudang_ke;
                $ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
                $ArrHist2[$key]['gudang_dari'] 	    = $kd_gudang_dari;
                $ArrHist2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
                $ArrHist2[$key]['gudang_ke'] 		= $kd_gudang_ke;
                $ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->stock;
                $ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->stock - $value;
                $ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->rusak;
                $ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->rusak;
                $ArrHist2[$key]['qty_booking_awal'] 	= $rest_pusat[0]->booking;
                $ArrHist2[$key]['qty_booking_akhir'] 	= $rest_pusat[0]->booking;
                $ArrHist2[$key]['no_trans'] 		= $kode_trans;
                $ArrHist2[$key]['jumlah_qty'] 		= $value;
                $ArrHist2[$key]['ket'] 				= 'penambahan gudang';
                $ArrHist2[$key]['update_by'] 		= $UserName;
                $ArrHist2[$key]['update_date'] 		= $dateTime;
            }
            else{
                $restMat	= $CI->db->get_where('con_nonmat_new',array('code_group'=>$key))->result();

                $ArrStockInsert2[$key]['code_group'] 	= $restMat[0]->code_group;
                $ArrStockInsert2[$key]['category_awal'] = $restMat[0]->category_awal;
                $ArrStockInsert2[$key]['material_name'] = $restMat[0]->material_name;
                $ArrStockInsert2[$key]['gudang'] 		= $id_gudang_ke;
                $ArrStockInsert2[$key]['stock'] 		= $value;
                $ArrStockInsert2[$key]['update_by'] 	= $UserName;
                $ArrStockInsert2[$key]['update_date'] 	= $dateTime;

                $ArrHistInsert2[$key]['code_group'] 	= $key;
                $ArrHistInsert2[$key]['category_awal'] 	= $restMat[0]->category_awal;
                $ArrHistInsert2[$key]['material_name'] 	= $restMat[0]->material_name;
                $ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_ke;
                $ArrHistInsert2[$key]['gudang'] 		= $kd_gudang_ke;
                $ArrHistInsert2[$key]['id_gudang_dari'] = $id_gudang_dari;
                $ArrHistInsert2[$key]['gudang_dari'] 	= $kd_gudang_dari;
                $ArrHistInsert2[$key]['id_gudang_ke']   = $id_gudang_ke;
                $ArrHistInsert2[$key]['gudang_ke'] 		= $kd_gudang_ke;
                $ArrHistInsert2[$key]['qty_stock_awal']     = 0;
                $ArrHistInsert2[$key]['qty_stock_akhir']    = $value;
                $ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
                $ArrHistInsert2[$key]['qty_rusak_akhir']    = 0;
                $ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
                $ArrHistInsert2[$key]['qty_booking_akhir']    = 0;
                $ArrHistInsert2[$key]['no_trans'] 		= $kode_trans;
                $ArrHistInsert2[$key]['jumlah_qty'] 	= $value;
                $ArrHistInsert2[$key]['ket'] 			= 'penambahan gudang (insert new)';
                $ArrHistInsert2[$key]['update_by'] 		= $UserName;
                $ArrHistInsert2[$key]['update_date'] 	= $dateTime;
            }
        }
    }

    // print_r($ArrStock);
    // print_r($ArrStockInsert);
    // print_r($ArrStock2);
    // print_r($ArrStockInsert2);
    // exit;

    if(!empty($ArrStock)){
        $CI->db->update_batch('warehouse_rutin_stock', $ArrStock, 'id');
    }
    if(!empty($ArrHist)){
        $CI->db->insert_batch('warehouse_rutin_history', $ArrHist);
    }

    if(!empty($ArrStockInsert)){
        $CI->db->insert_batch('warehouse_rutin_stock', $ArrStockInsert);
    }
    if(!empty($ArrHistInsert)){
        $CI->db->insert_batch('warehouse_rutin_history', $ArrHistInsert);
    }

    if(!empty($ArrStock2)){
        $CI->db->update_batch('warehouse_rutin_stock', $ArrStock2, 'id');
    }
    if(!empty($ArrHist2)){
        $CI->db->insert_batch('warehouse_rutin_history', $ArrHist2);
    }

    if(!empty($ArrStockInsert2)){
        $CI->db->insert_batch('warehouse_rutin_stock', $ArrStockInsert2);
    }
    if(!empty($ArrHistInsert2)){
        $CI->db->insert_batch('warehouse_rutin_history', $ArrHistInsert2);
    }
}

function get_name_by_code_group($id){
    $CI =& get_instance();
    $get_detail = $CI->db->get_where('accessories', array('id_material'=>$id))->result();
    $radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
    $nama_acc = "Not found"; 
    if(!empty($get_detail)){
        if($get_detail[0]->category == '1'){
            $nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
        }
        if($get_detail[0]->category == '2'){
            $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
        }
        if($get_detail[0]->category == '3'){
            $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T x ".strtoupper($get_detail[0]->dimensi);
        }
        if($get_detail[0]->category == '4'){
            $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
        }
        if($get_detail[0]->category == '5'){
            $nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->standart);
        }
    }
    
    return $nama_acc;
    
}

function getGudangProduksi() {
    $CI =& get_instance();
    $getKodeTrans = $CI->db->select('id')->group_by('id')->get_where('warehouse',array('category'=>'produksi'))->result_array();

    $ArrJoint = [];
    foreach ($getKodeTrans as $key => $value) {
        $ArrJoint[] = $value['id'];
    }
    
    $IMPLODE = implode(",", $ArrJoint);

    return $IMPLODE;
}