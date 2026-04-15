<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gl_interface extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('master_model');
        if (!$this->session->userdata('isORIlogin')) {
            redirect('login');
        }
    }

    // ─────────────────────────────────────────────
    // INDEX — tampilkan daftar gl_interface
    // ─────────────────────────────────────────────
    public function index() {
        $controller  = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses   = getAcccesmenu($controller);
        if ($Arr_Akses['read'] != '1') {
            $this->session->set_flashdata("alert_data", "<div class=\"alert alert-warning\" id=\"flash-message\">You Don't Have Right To Access This Page.</div>");
            redirect(site_url('dashboard'));
        }

        $jenis_list = $this->db->query("SELECT DISTINCT jenis_transaksi FROM gl_interface ORDER BY jenis_transaksi ASC")->result_array();

        $data = array(
            'title'       => 'GL Interface Monitor',
            'action'      => 'index',
            'akses_menu'  => $Arr_Akses,
            'jenis_list'  => $jenis_list,
        );
        $this->load->view('Gl_interface/index', $data);
    }

    // ─────────────────────────────────────────────
    // SERVER SIDE — datatable ajax
    // ─────────────────────────────────────────────
    public function server_side() {
        $requestData  = $_REQUEST;
        $status       = isset($requestData['status'])       ? $requestData['status']       : '';
        $jenis        = isset($requestData['jenis'])         ? $requestData['jenis']         : '';
        $tgl_from     = isset($requestData['tgl_from'])      ? $requestData['tgl_from']      : '';
        $tgl_to       = isset($requestData['tgl_to'])        ? $requestData['tgl_to']        : '';
        $search       = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        $where  = "WHERE 1=1 ";
        if (!empty($status))   $where .= " AND a.status = '".$this->db->escape_str($status)."'";
        if (!empty($jenis))    $where .= " AND a.jenis_transaksi = '".$this->db->escape_str($jenis)."'";
        if (!empty($tgl_from)) $where .= " AND a.tgl >= '".$this->db->escape_str($tgl_from)."'";
        if (!empty($tgl_to))   $where .= " AND a.tgl <= '".$this->db->escape_str($tgl_to)."'";
        if (!empty($search)) {
            $s = $this->db->escape_str($search);
            $where .= " AND (a.nomor LIKE '%$s%' OR a.keterangan LIKE '%$s%' OR a.memo LIKE '%$s%' OR a.jenis_transaksi LIKE '%$s%')";
        }

        $sql_count = "SELECT COUNT(*) as total FROM gl_interface a $where";
        $total     = $this->db->query($sql_count)->row()->total;

        $order_col = isset($requestData['order'][0]['column']) ? $requestData['order'][0]['column'] : 0;
        $order_dir = isset($requestData['order'][0]['dir'])    ? $requestData['order'][0]['dir']    : 'desc';
        $cols      = array('a.id','a.tgl','a.nomor','a.jenis_transaksi','a.keterangan','a.jml','a.status','a.posted_at');
        $order_by  = isset($cols[$order_col]) ? $cols[$order_col] : 'a.id';

        $limit  = (int)(isset($requestData['length']) ? $requestData['length'] : 10);
        $offset = (int)(isset($requestData['start'])  ? $requestData['start']  : 0);

        $sql = "SELECT a.*, 
                    (SELECT COUNT(*) FROM gl_interface_detail d WHERE d.id_gl_interface=a.id) as jml_detail
                FROM gl_interface a 
                $where 
                ORDER BY $order_by $order_dir 
                LIMIT $offset, $limit";

        $rows  = $this->db->query($sql)->result_array();
        $data  = [];
        $no    = $offset + 1;

        foreach ($rows as $row) {
            if ($row['status'] == 'posted') {
                $badge_status = "<span class='label label-success'>POSTED</span>";
            } elseif ($row['status'] == 'error') {
                $badge_status = "<span class='label label-danger'>ERROR</span>";
            } else {
                $badge_status = "<span class='label label-warning'>PENDING</span>";
            }

            $btn_retry = '';
            if ($row['status'] == 'error' || $row['status'] == 'pending') {
                $btn_retry = "<button class='btn btn-xs btn-warning btn-retry' 
                                data-id='".$row['id']."' title='Retry Posting'>
                                <i class='fa fa-refresh'></i> Retry
                              </button> ";
            }

            $btn_detail = "<button class='btn btn-xs btn-info btn-detail' 
                            data-id='".$row['id']."' title='Lihat Detail'>
                            <i class='fa fa-list'></i>
                          </button>";

            $error_info = '';
            if (!empty($row['error_msg'])) {
                $error_info = "<br><small class='text-danger'>".htmlspecialchars(substr($row['error_msg'],0,80))."...</small>";
            }

            $data[] = [
                "<div align='center'>".$no."</div>",
                "<div align='center'>".date('d-M-Y', strtotime($row['tgl']))."</div>",
                "<div align='left'><b>".$row['nomor']."</b></div>",
                "<div align='left'><span class='label label-default'>".$row['jenis_transaksi']."</span></div>",
                "<div align='left'>".strtoupper($row['keterangan'])."</div>",
                "<div align='right'>".number_format($row['jml'],2)."</div>",
                "<div align='center'>".$row['jml_detail']."</div>",
                "<div align='center'>".$badge_status.$error_info."</div>",
                "<div align='center'>".(!empty($row['posted_at']) ? date('d-M-Y H:i', strtotime($row['posted_at'])) : '-')."</div>",
                "<div align='center'>".$btn_retry.$btn_detail."</div>",
            ];
            $no++;
        }

        echo json_encode(array(
            'draw'            => intval(isset($requestData['draw']) ? $requestData['draw'] : 1),
            'recordsTotal'    => intval($total),
            'recordsFiltered' => intval($total),
            'data'            => $data,
        ));
    }

    // ─────────────────────────────────────────────
    // MODAL DETAIL — tampilkan line items
    // ─────────────────────────────────────────────
    public function modal_detail() {
        $id      = $this->uri->segment(3);
        $header  = $this->db->get_where('gl_interface', ['id' => $id])->row();
        $details = $this->db->get_where('gl_interface_detail', ['id_gl_interface' => $id])->result_array();

        $total_debet  = array_sum(array_column($details, 'debet'));
        $total_kredit = array_sum(array_column($details, 'kredit'));
        $is_balance   = abs($total_debet - $total_kredit) < 0.01;

        $data = [
            'header'       => $header,
            'details'      => $details,
            'total_debet'  => $total_debet,
            'total_kredit' => $total_kredit,
            'is_balance'   => $is_balance,
        ];
        $this->load->view('Gl_interface/modal_detail', $data);
    }

    // ─────────────────────────────────────────────
    // RETRY — posting ulang yang gagal/pending
    // ─────────────────────────────────────────────
    public function retry() {
        $id     = $this->uri->segment(3);
        $header = $this->db->get_where('gl_interface', ['id' => $id])->row();

        if (empty($header)) {
            echo json_encode(['status' => 2, 'pesan' => 'Data tidak ditemukan.']);
            return;
        }

        if ($header->status == 'posted') {
            echo json_encode(['status' => 2, 'pesan' => 'Transaksi sudah berhasil diposting sebelumnya.']);
            return;
        }

        $details = $this->db->get_where('gl_interface_detail', ['id_gl_interface' => $id])->result_array();

        if (empty($details)) {
            echo json_encode(['status' => 2, 'pesan' => 'Detail jurnal tidak ditemukan di gl_interface_detail.']);
            return;
        }

        // Validasi balance debet = kredit
        $total_debet  = array_sum(array_column($details, 'debet'));
        $total_kredit = array_sum(array_column($details, 'kredit'));
        if (abs($total_debet - $total_kredit) > 0.01) {
            $this->db->where('id', $id);
            $this->db->update('gl_interface', [
                'status'    => 'error',
                'error_msg' => "Debet ($total_debet) tidak balance dengan Kredit ($total_kredit)",
            ]);
            echo json_encode(['status' => 2, 'pesan' => 'Jurnal tidak balance. Debet: '.number_format($total_debet,2).' | Kredit: '.number_format($total_kredit,2)]);
            return;
        }

        // Cek apakah nomor JV sudah ada di DBACC — hindari double posting
        $db2        = $this->load->database('accounting', TRUE);
        $cek_posted = $db2->get_where('javh', ['nomor' => $header->nomor])->row();
        if (!empty($cek_posted)) {
            // Sudah ada di accounting, update status saja
            $this->db->where('id', $id);
            $this->db->update('gl_interface', [
                'status'    => 'posted',
                'posted_at' => date('Y-m-d H:i:s'),
                'error_msg' => NULL,
            ]);
            echo json_encode(['status' => 1, 'pesan' => 'Nomor JV sudah ada di accounting. Status diupdate ke posted.']);
            return;
        }

        // Posting ke DBACC
        $db2->trans_begin();
        try {
            $dataJVhead = [
                'nomor'         => $header->nomor,
                'tgl'           => $header->tgl,
                'jml'           => $header->jml,
                'koreksi_no'    => $header->koreksi_no,
                'kdcab'         => $header->kdcab,
                'jenis'         => $header->jenis,
                'keterangan'    => $header->keterangan,
                'bulan'         => $header->bulan,
                'tahun'         => $header->tahun,
                'user_id'       => $header->user_id,
                'memo'          => $header->memo,
                'tgl_jvkoreksi' => $header->tgl_jvkoreksi,
                'ho_valid'      => $header->ho_valid,
            ];
            $db2->insert('javh', $dataJVhead);

            foreach ($details as $vals) {
                $db2->insert('jurnal', [
                    'tipe'         => $vals['tipe'],
                    'nomor'        => $header->nomor,
                    'tanggal'      => $vals['tanggal'],
                    'no_perkiraan' => $vals['no_perkiraan'],
                    'keterangan'   => $vals['keterangan'],
                    'no_reff'      => $vals['no_reff'],
                    'debet'        => $vals['debet'],
                    'kredit'       => $vals['kredit'],
                ]);
            }

            if ($db2->trans_status() === FALSE) {
                $db2->trans_rollback();
                $this->db->where('id', $id);
                $this->db->update('gl_interface', [
                    'status'    => 'error',
                    'error_msg' => 'Trans rollback saat retry posting',
                ]);
                echo json_encode(['status' => 2, 'pesan' => 'Gagal posting ke accounting. Silakan coba lagi.']);
            } else {
                $db2->trans_commit();
                $this->db->where('id', $id);
                $this->db->update('gl_interface', [
                    'status'    => 'posted',
                    'posted_at' => date('Y-m-d H:i:s'),
                    'error_msg' => NULL,
                ]);
                echo json_encode(['status' => 1, 'pesan' => 'Berhasil diposting ke accounting.']);
            }
        } catch (Exception $e) {
            $db2->trans_rollback();
            $this->db->where('id', $id);
            $this->db->update('gl_interface', [
                'status'    => 'error',
                'error_msg' => $e->getMessage(),
            ]);
            echo json_encode(['status' => 2, 'pesan' => 'Error: '.$e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────
    // RETRY BULK — retry semua yang error/pending
    // ─────────────────────────────────────────────
    public function retry_bulk() {
        $jenis  = $this->input->post('jenis');
        $jenis  = $jenis ? $jenis : '';
        $where  = ['status' => 'error'];
        if (!empty($jenis)) $where['jenis_transaksi'] = $jenis;

        $list = $this->db->get_where('gl_interface', $where)->result_array();

        $sukses = 0;
        $gagal  = 0;
        foreach ($list as $row) {
            // panggil retry per id
            $result = json_decode($this->_do_retry($row['id']), true);
            if ($result['status'] == 1) $sukses++;
            else $gagal++;
        }

        echo json_encode([
            'status' => 1,
            'pesan'  => "Retry selesai. Sukses: $sukses | Gagal: $gagal",
        ]);
    }

    // ─────────────────────────────────────────────
    // PRIVATE helper retry by id (reuse logic)
    // ─────────────────────────────────────────────
    private function _do_retry($id) {
        // Redirect ke retry() dengan segment override tidak bisa,
        // jadi duplikasi logic minimal di sini
        $header  = $this->db->get_where('gl_interface', ['id' => $id])->row();
        if (empty($header) || $header->status == 'posted') {
            return json_encode(['status' => 2, 'pesan' => 'Skip id '.$id]);
        }

        $details = $this->db->get_where('gl_interface_detail', ['id_gl_interface' => $id])->result_array();
        if (empty($details)) return json_encode(['status' => 2, 'pesan' => 'No detail']);

        $db2        = $this->load->database('accounting', TRUE);
        $cek_posted = $db2->get_where('javh', ['nomor' => $header->nomor])->row();
        if (!empty($cek_posted)) {
            $this->db->where('id', $id)->update('gl_interface', ['status' => 'posted', 'posted_at' => date('Y-m-d H:i:s'), 'error_msg' => NULL]);
            return json_encode(['status' => 1, 'pesan' => 'Already posted']);
        }

        $db2->trans_begin();
        $db2->insert('javh', [
            'nomor' => $header->nomor, 'tgl' => $header->tgl, 'jml' => $header->jml,
            'koreksi_no' => $header->koreksi_no, 'kdcab' => $header->kdcab, 'jenis' => $header->jenis,
            'keterangan' => $header->keterangan, 'bulan' => $header->bulan, 'tahun' => $header->tahun,
            'user_id' => $header->user_id, 'memo' => $header->memo,
            'tgl_jvkoreksi' => $header->tgl_jvkoreksi, 'ho_valid' => $header->ho_valid,
        ]);
        foreach ($details as $v) {
            $db2->insert('jurnal', [
                'tipe' => $v['tipe'], 'nomor' => $header->nomor, 'tanggal' => $v['tanggal'],
                'no_perkiraan' => $v['no_perkiraan'], 'keterangan' => $v['keterangan'],
                'no_reff' => $v['no_reff'], 'debet' => $v['debet'], 'kredit' => $v['kredit'],
            ]);
        }
        if ($db2->trans_status() === FALSE) {
            $db2->trans_rollback();
            $this->db->where('id', $id)->update('gl_interface', ['status' => 'error', 'error_msg' => 'Bulk retry rollback']);
            return json_encode(['status' => 2, 'pesan' => 'Rollback']);
        }
        $db2->trans_commit();
        $this->db->where('id', $id)->update('gl_interface', ['status' => 'posted', 'posted_at' => date('Y-m-d H:i:s'), 'error_msg' => NULL]);
        return json_encode(['status' => 1, 'pesan' => 'OK']);
    }
}
