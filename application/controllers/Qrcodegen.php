<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Qrcodegen extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		// if (!$this->session->userdata('isORIlogin')) {
		// 	redirect('login');
		// }
	}

	function generate($code, $content)
	{
		//		$code=$this->input->post('code');
		//		$content=$this->input->post('content');
		$this->load->library('ciqrcode'); //pemanggilan library QR CODE
		$config['cacheable']	= true; //boolean, the default is true
		$config['cachedir']		= './assets/qrcode/cache/'; //string, the default is application/cache/
		$config['errorlog']		= './assets/qrcode/cache/'; //string, the default is application/logs/
		$config['imagedir']		= './assets/qrcode/images/'; //direktori penyimpanan qr code
		$config['quality']		= true; //boolean, the default is true
		$config['size']			= '1024'; //interger, the default is 1024
		$config['black']		= array(224, 255, 255); // array, default is array(255,255,255)
		$config['white']		= array(70, 130, 180); // array, default is array(0,0,0)
		$this->ciqrcode->initialize($config);
		$image_name 			= $code . '.png'; //buat name dari qr code sesuai dengan nim
		$params['data'] 		= $content; //data yang akan di jadikan QR CODE
		$params['level'] 		= 'H'; //H=High
		$params['size'] 		= 10;
		$params['savename'] 	= FCPATH . $config['imagedir'] . $image_name; //simpan image QR CODE ke folder assets/images/
		$this->ciqrcode->generate($params); // fungsi untuk generate QR CODE
		echo "<img src='" . base_url($config['imagedir'] . $image_name) . "'>";
	}
}
