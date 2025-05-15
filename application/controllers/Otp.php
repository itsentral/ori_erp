<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Otp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'string']);
        $this->load->library('session');
        $this->load->model('master_model');
    }

    public function index(){
		$controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}

		$data_Group			= $this->master_model->getArray('groups',array(),'id','name');
        $Data_Identitas			= $this->master_model->getData('identitas');
		$data = array(
			'title'			=> 'Indeks Of OTP',
			'action'		=> 'index',
			'row_group'		=> $data_Group,
			'akses_menu'	=> $Arr_Akses,
            'title'			=> 'Login',
			'idt'			=> $Data_Identitas[0]
		);
		history('View OTP');
		$this->load->view('Users/otp',$data);
	}

    public function send_otp() {
        $phone = $this->input->post('phone'); // Nomor hp dengan kode negara, contoh: 6281234567890

        // Generate OTP
        $otp = random_string('numeric', 6);
        $this->session->set_userdata('otp', $otp);
        $this->session->set_userdata('otp_expired', time() + 300); // 5 menit expired

        // Pesan yang dikirim
        $message = "Kode OTP kamu adalah: *$otp*. Jangan berikan kepada siapa pun.";

        // Kirim lewat WhatsApp via API Wablas
        $response = $this->send_whatsapp($phone, $message);

        echo $response;
    }

    private function send_whatsapp($phone, $message) {
        $curl = curl_init();

        $token = 'YOUR_WABLAS_API_KEY';
        $payload = [
            "phone" => $phone,
            "message" => $message,
            "secret" => false
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://kirim.pesan.my.id/api/v2/send-message",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "Authorization: $token",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function verify_otp() {
        $input_otp = $this->input->post('otp');
        $saved_otp = $this->session->userdata('otp');
        $expired = $this->session->userdata('otp_expired');

        if (time() > $expired) {
            echo "OTP expired";
        } elseif ($input_otp === $saved_otp) {
            echo "OTP valid";
        } else {
            echo "OTP salah";
        }
    }
}
