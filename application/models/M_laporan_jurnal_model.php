<?php
class M_laporan_jurnal_model extends CI_Model {

    public function get_laporan($dari, $sampai)
    {
        // ambil data jurnal sesuai periode
        $this->db->select("
            a.tanggal,
            a.nomor AS nomor_jurnal,
            a.no_reff,
            b.no_invoice,
            b.so_number,
            b.nm_customer AS customer,

            -- revenue
            (SELECT SUM(debet - kredit) 
             FROM view_gl_jurnal 
             WHERE nomor = a.nomor 
             AND no_perkiraan = '4101-01-02') AS revenue,

            -- cogs
            (SELECT SUM(debet - kredit)
             FROM view_gl_jurnal 
             WHERE nomor = a.nomor
             AND no_perkiraan = '5104-01-01') AS cogs
        ");

        $this->db->from("view_gl_jurnalx a");
        $this->db->join("tr_invoice_header b", "b.no_invoice = a.no_reff", "left");
        $this->db->where("a.tanggal >=", $dari);
        $this->db->where("a.tanggal <=", $sampai);
        $this->db->group_by("a.nomor");

        $query = $this->db->get();
        $rows  = $query->result();

        // hitung persentase di PHP
        foreach ($rows as $r) { 
            if ($r->revenue > 0) {
                $r->persentase = ($r->cogs / $r->revenue) * 100;
            } else {
                $r->persentase = 0;
            }
        }

        return $rows;
    }
}
