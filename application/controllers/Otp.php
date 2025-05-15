<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Otp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'string']);
        $this->load->library('session');
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
