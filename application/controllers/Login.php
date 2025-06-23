<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	function __construct() {
		parent::__construct();
		
		$this->load->model('master_model');			
		
		
	}

	public function index() {
		//echo $this->uri->segment(1);
		if ($this->input->post()) {			
			$UserName			= $this->input->post('username');
			$Password			= $this->input->post('password');
			$PassData			= cryptSHA1($Password);
			$WHERE				= array(
				'deleted'			=> 0,
				'st_aktif'			=> 1,
				'username'			=> $UserName,
				'password'			=> $PassData
			);
			$Cek_Data			= $this->master_model->getArray('users',$WHERE);
			//echo "<pre>";print_r($Cek_Data);exit;
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
				}
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

	/* 2FA Auth */
    // ==============
# Tambahkan library   phpgangsta/googleauthenticator
//composer require phpgangsta/googleauthenticator:dev-master


# Tambahkan di controller users

public function setup_2fa()
    {
        $ga = new PHPGangsta_GoogleAuthenticator();

        $secret = $ga->createSecret();

        // Simpan secret ke DB user
        $this->db->update('users', ['ga_secret' => $secret], ['id_user' => $this->auth->user_id()]);
        $user = $this->db->get_where('users', ['id_user' => $this->auth->user_id()])->row();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->full_name . '@ORI-ERP', $secret);

        $data['secret'] = $secret;
        $data['qrCodeUrl'] = $qrCodeUrl;

		$Data_Identitas			= $this->master_model->getData('identitas');
			$data = array(
				'title'			=> 'Login',
				'idt'			=> $Data_Identitas[0]
			);
			
			$this->load->view('setup_2fa',$data);

    }

# halaman verification OTP

public function verify_2fa()
    {
        $identitas = $this->identitas_model->find_by(array('ididentitas' => 1)); // By Muhaemin => Di Form Login
        $user = $this->db->get_where('users', ['id_user' => $this->auth->user_id()])->row();
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
			
			$this->load->view('verify_2fa',$data);
    }

# verification OTP

public function check_otp()
    {
        $ga = new PHPGangsta_GoogleAuthenticator();

        $otp = $this->input->post('otp');
        $user = $this->db->get_where('users', ['id_user' => $this->auth->user_id()])->row();
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
