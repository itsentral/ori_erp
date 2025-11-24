<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_jurnal extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('M_laporan_jurnal_model', 'laporan');

        // Your own constructor code
		if(!$this->session->userdata('isORIlogin')){
			redirect('login');
		}
    }
 
    public function index()
    {
        $controller			= ucfirst(strtolower($this->uri->segment(1)));
		$Arr_Akses			= getAcccesmenu($controller);
		if($Arr_Akses['read'] !='1'){
			$this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page, Please Contact Your Administrator....</div>");
			redirect(site_url('dashboard'));
		}
		$data_session		= $this->session->userdata;
		$dateTime = date('Y-m-d H:i:s');
		$UserName = $data_session['ORI_User']['id_user'];

        $datas = [];

        // jika form filter dijalankan
        if ($this->input->get('dari') && $this->input->get('sampai')) {
 
            $dari   = $this->input->get('dari');
            $sampai = $this->input->get('sampai');

            $datas = $this->laporan->get_laporan($dari, $sampai);

           

        }

        $data = array(
			'title'			=> 'Laporan Jurnal VS COGS',
			'action'		=> 'index',
			'status'	    => $this->status,
			'results'	    => $datas,
			'akses_menu'	=> $Arr_Akses
		);

        $this->load->view('laporan_jurnal', $data);
    }

    private function export_excel($results, $dari, $sampai)
    {
    require FCPATH . 'vendor/autoload.php';

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', "Laporan Revenue & COGS");
    $sheet->setCellValue('A2', "Periode: $dari s/d $sampai");

    // Header tabel
    $header = [
        'No', 'Tanggal', 'Nomor Jurnal', 'Nomor Invoice', 'Nomor SO',
        'Customer', 'Revenue', 'COGS', 'Persentase (%)'
    ];

    $col = 'A';
    foreach ($header as $h) {
        $sheet->setCellValue($col . '4', $h);
        $col++;
    }

    // Isi data
    $row = 5;
    $no  = 1;

    foreach ($results as $r) {
        $sheet->setCellValue("A$row", $no++);
        $sheet->setCellValue("B$row", $r->tanggal);
        $sheet->setCellValue("C$row", $r->nomor_jurnal);
        $sheet->setCellValue("D$row", $r->no_invoice);
        $sheet->setCellValue("E$row", $r->no_so);
        $sheet->setCellValue("F$row", $r->customer);
        $sheet->setCellValue("G$row", $r->revenue);
        $sheet->setCellValue("H$row", $r->cogs);
        $sheet->setCellValue("I$row", number_format($r->persentase, 2));
        $row++;
    }

    // Nama file
    $filename = "Laporan_Rev_COGS_{$dari}_sd_{$sampai}.xlsx";

    // Output ke browser
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Cache-Control: max-age=0");

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    }

}
