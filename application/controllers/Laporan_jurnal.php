<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_jurnal extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('M_laporan_jurnal_model', 'laporan');
    }
 
    public function index()
    {
        $data['results'] = [];

        // jika form filter dijalankan
        if ($this->input->get('dari') && $this->input->get('sampai')) {

            $dari   = $this->input->get('dari');
            $sampai = $this->input->get('sampai');

            $data['results'] = $this->laporan->get_laporan($dari, $sampai);

            print_r($data['results']);
            exit;

        }

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
