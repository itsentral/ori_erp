<?php if (!defined('BASEPATH')) { exit('No direct script access allowed');}

class Serah_terima_asset extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('serah_terima_asset_model');
        $this->load->model('asset_model');
        $this->load->model('master_model');

        if(!$this->session->userdata('isORIlogin')){ 
            redirect('login');
        }
    }

    public function index(){ 
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);
        if($Arr_Akses['read'] !='1'){
            $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
            redirect(site_url('dashboard'));
        }

        $data = array(
            'title'         => 'Serah Terima Assets',
            'action'        => 'serah_terima_asset',
            'akses_menu'    => $Arr_Akses,
        );
        history("View index serah terima asset");
        $this->load->view('Serah_terima_asset/index', $data);
    }

    public function data_side(){
        $this->serah_terima_asset_model->getDataJSON();
    }

    public function add(){
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);

        if($this->input->post()){
            // Process save
            $post = $this->input->post();
            $result = $this->serah_terima_asset_model->saveSerahTerima($post);
            echo json_encode($result);
            return;
        }

        // Show form
        $data = array(
            'title'         => 'Add Form Serah Terima Asset',
            'action'        => 'serah_terima_asset',
            'akses_menu'    => $Arr_Akses,
            'list_dept'     => $this->asset_model->getList('department'),
            'list_asset'    => $this->serah_terima_asset_model->getActiveAssets(),
            'data'          => null,
            'detail'        => array(),
        );
        history("Add serah terima asset");
        $this->load->view('Serah_terima_asset/form', $data);
    }

    public function edit($id = null){
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);

        if($this->input->post()){
            $post = $this->input->post();
            $result = $this->serah_terima_asset_model->updateSerahTerima($post);
            echo json_encode($result);
            return;
        }

        $header = $this->serah_terima_asset_model->getById($id);
        if(!$header){
            redirect('serah_terima_asset');
        }
        $detail = $this->serah_terima_asset_model->getDetailByHeaderId($id);

        $data = array(
            'title'         => 'Edit Form Serah Terima Asset',
            'action'        => 'serah_terima_asset',
            'akses_menu'    => $Arr_Akses,
            'list_dept'     => $this->asset_model->getList('department'),
            'list_asset'    => $this->serah_terima_asset_model->getActiveAssets(),
            'data'          => $header,
            'detail'        => $detail,
        );
        history("Edit serah terima asset #".$id);
        $this->load->view('Serah_terima_asset/form', $data);
    }

    public function view($id = null){
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);

        $header = $this->serah_terima_asset_model->getById($id);
        if(!$header){
            redirect('serah_terima_asset');
        }
        $detail = $this->serah_terima_asset_model->getDetailByHeaderId($id);

        $data = array(
            'title'         => 'View Serah Terima Asset',
            'action'        => 'serah_terima_asset',
            'akses_menu'    => $Arr_Akses,
            'data'          => $header,
            'detail'        => $detail,
        );
        history("View serah terima asset #".$id);
        $this->load->view('Serah_terima_asset/view', $data);
    }

    public function print_pdf($id = null){
        $header = $this->serah_terima_asset_model->getById($id);
        if(!$header){
            redirect('serah_terima_asset');
        }
        $detail = $this->serah_terima_asset_model->getDetailByHeaderId($id);

        $data = array(
            'title'     => 'Form Serah Terima Asset',
            'data'      => $header,
            'detail'    => $detail,
        );
        history("Print PDF serah terima asset #".$id);
        $this->load->view('Serah_terima_asset/print_pdf', $data);
    }

    public function get_asset_info(){
        $id = $this->input->post('id');
        $asset = $this->db->get_where('asset', array('id' => $id))->row_array();
        if($asset){
            echo json_encode(array(
                'status' => 1,
                'code_ori' => $asset['code_ori'],
                'kd_asset' => $asset['kd_asset'],
                'nm_asset' => $asset['nm_asset'],
            ));
        } else {
            echo json_encode(array('status' => 0));
        }
    }

    public function delete($id = null){
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);
        
        if($Arr_Akses['delete'] != '1'){
            echo json_encode(array('status' => 0, 'pesan' => 'Anda tidak memiliki akses untuk menghapus data ini.'));
            return;
        }

        $result = $this->serah_terima_asset_model->deleteSerahTerima($id);
        echo json_encode($result);
    }
}
