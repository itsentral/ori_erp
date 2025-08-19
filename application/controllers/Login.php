<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('master_model');			

		$this->site_key = '6LfRy6ErAAAAAIh8BomRhCz8Y4iOyR8OIm95qOwA';
        $this->secret_key = '6LfRy6ErAAAAALA6QN1Gwd8HtnyR0ljIOZuK023B';
		
		
	}

	public function index() {
		//echo $this->uri->segment(1);
		if ($this->input->post()) {			
			$UserName			= $this->input->post('username');
			$Password			= $this->input->post('password');
			$PassData			= cryptSHA1($Password);
			$token              = $this->security->xss_clean($this->input->post('recaptcha_token'));
			$WHERE				= array(
				'deleted'			=> 0,
				'st_aktif'			=> 1,
				'username'			=> $UserName,
				'password'			=> $PassData
			);
			$Cek_Data			= $this->master_model->getArray('users',$WHERE);
			//echo "<pre>";print_r($Cek_Data);exit;
              

            $urlVeryfy    = "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($this->secret_key) . "&response=" . urlencode($token);
            $resGoogle     = json_decode(file_get_contents($urlVeryfy));
            print_r($resGoogle);
            exit;

            if (!$resGoogle->success) {
                $pesan = 'Gagal validasi reCAPTCHA Google...!';
                $this->session->set_flashdata('error_captcha', $pesan);

                $Data_Identitas			= $this->master_model->getData('identitas');
				$data = array(
					'title'			=> 'Login',
					'idt'			=> $Data_Identitas[0],
					'site_key'      => $this->site_key
				);
				
				$this->load->view('login',$data);


            } else if ($resGoogle->score < 0.5 || $resGoogle->action !== 'login') {
                $pesan = 'Gagal, terdeteksi login mencurigakan. Silahkan coba lagi...!';
                $this->session->set_flashdata('error_captcha', $pesan);

                $Data_Identitas			= $this->master_model->getData('identitas');
				$data = array(
					'title'			=> 'Login',
					'idt'			=> $Data_Identitas[0],
					'site_key'      => $this->site_key
				);
				
				$this->load->view('login',$data);

            } else if ($resGoogle->success && $resGoogle->score >= 0.5) {
                if($Cek_Data){
				$Group_ID		= $Cek_Data[0]['group_id'];
				$Aktif			= $Cek_Data[0]['st_aktif'];
						if($Aktif==1){
							$Arr_Daftar	= array();
							$Arr_Daftar['isORIlogin']	= 1;
							$Arr_Daftar['ORI_User']		= $Cek_Data[0];
							unset($Arr_Daftar['ORI_User']['password']);
							unset($Arr_Daftar['ORI_User']['ip']);
							unset($Arr_Daftar['ORI_User']['login_terakhir']);
							unset($Arr_Daftar['ORI_User']['created_on']);
							unset($Arr_Daftar['ORI_User']['deleted']);
									if($Group_ID){
										$WHR_Group		= array(
											'id'			=> $Group_ID
										);
										$Cek_Group		= $this->master_model->getArray('groups',$WHR_Group);
										if($Cek_Group){
											$Arr_Daftar['ORI_Group']	= $Cek_Group[0];
											unset($Cek_Group);
										}						
									
									
										$this->session->set_userdata($Arr_Daftar);
										$_SESSION["ses_level3"] = 0;	
										$_SESSION["ses_level2"] = 0;	
										$_SESSION["ses_level1"] = 0;	
										$Arr_Return		= array(
											'status'		=> 1,
											'pesan'			=> 'Login Process Success. Thank You & Have A Nice Day..'
										);				
								
									}else{
										$Arr_Return		= array(
											'status'		=> 2,
											'pesan'			=> 'Inactive Account. Please Contact Your Administrator....'
										);
									}else{
										$Arr_Return		= array(
											'status'		=> 2,
											'pesan'			=> 'Incorrect Username Or Password. Please Try Again....'
										);
									}
									echo json_encode($Arr_Return);

								} else {
									$Data_Identitas			= $this->master_model->getData('identitas');
									$data = array(
										'title'			=> 'Login',
										'idt'			=> $Data_Identitas[0]
									);
									
									$this->load->view('login',$data);
									// $this->load->view('maintenance_page',$data); 
								}
				}
				
            } else {
                $pesan = 'Gagal login, silahkan coba lagi...!';
                $this->session->set_flashdata('error_captcha', $pesan);
				$Data_Identitas			= $this->master_model->getData('identitas');
				$data = array(
					'title'			=> 'Login',
					'idt'			=> $Data_Identitas[0],
					'site_key'      => $this->site_key
				);
				
				$this->load->view('login',$data);
            }		
			
		
	}

	/* 2FA Auth */
    // ==============
# Tambahkan library   phpgangsta/googleauthenticator
//composer require phpgangsta/googleauthenticator:dev-master


# Tambahkan di controller users

public function setup_2fa()
    {
        $ga = new PHPGangsta_GoogleAuthenticator();

        $secret = $ga->createSecret();

		$data_session	= $this->session->userdata;
		$id_user	= $this->session->userdata['ORI_User']['id_user'];

        // Simpan secret ke DB user
        $this->db->update('users', ['ga_secret' => $secret], ['id_user' => $id_user]);
        $user = $this->db->get_where('users', ['id_user' => $id_user])->row();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->nm_lengkap . '@ORI-ERP', $secret);

        

		$Data_Identitas			= $this->master_model->getData('identitas');
			$data = array(
				'title'			=> 'Login',
				'idt'			=> $Data_Identitas[0],
				'secret'        => $secret,
				'qrCodeUrl'        => $qrCodeUrl
			);
			
			$this->load->view('Users/setup_2fa',$data);

    }

# halaman verification OTP

public function verify_2fa()
    {
        $identitas = $this->master_model->getData('identitas');

		$data_session	= $this->session->userdata;
		$id_user	= $this->session->userdata['ORI_User']['id_user'];

        $user = $this->db->get_where('users', ['id_user' => $id_user])->row();
        $secret = $user->ga_secret;

        if (!$secret) {
            $this->session->set_flashdata('error', '2FA belum diaktifkan. Silakan aktifkan terlebih dahulu.');
            redirect('login/setup_2fa');
        }

		$Data_Identitas			= $this->master_model->getData('identitas');
			$data = array(
				'title'			=> 'Login',
				'idt'			=> $Data_Identitas[0]
			);
			
			$this->load->view('Users/verify_2fa',$data);
    }

# verification OTP

public function check_otp()
    {
        $ga = new PHPGangsta_GoogleAuthenticator();

		$data_session	= $this->session->userdata;
		$id_user	= $this->session->userdata['ORI_User']['id_user'];


        $otp = $this->input->post('otp');
        $user = $this->db->get_where('users', ['id_user' => $id_user])->row();
        $secret = $user->ga_secret;

        if (!$secret) {
            $this->session->set_flashdata('error', '2FA belum diaktifkan. Silakan aktifkan terlebih dahulu.');
            redirect('login/setup_2fa');
        }

        if (!$otp) {
            $this->session->set_flashdata('error', 'Kode OTP tidak boleh kosong.');
            redirect('login/verify_2fa');
        }

        // Verifikasi kode OTP
        $checkResult = $ga->verifyCode($secret, $otp, 2); // toleransi waktu 2x30 detik
    
        if ($checkResult) {
            $this->session->set_userdata('2fa_verified', true);
            redirect('dashboard');
        } else {
            // Jika verifikasi gagal, tampilkan pesan error
            $this->session->set_userdata('2fa_verified', false);
            $this->session->set_flashdata('error', 'Kode OTP salah');
            redirect('login/verify_2fa');
        }
    }






	
}
