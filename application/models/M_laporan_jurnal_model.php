<?php
class M_laporan_jurnal_model extends CI_Model {

    public function get_laporan($dari, $sampai)
    {
        // ambil data jurnal sesuai periode
      $sql = "SELECT
                a.tanggal,
                a.nomor AS nomor_jurnal,
                a.no_reff,
                a.keterangan,
                b.no_invoice,
                b.so_number,
                b.nm_customer AS customer,
                x.revenue,
                x.cogs
            FROM (
                SELECT DISTINCT nomor, tanggal, no_reff, keterangan
                FROM view_gl_jurnal
                WHERE tanggal >= $dari AND tanggal <=$sampai
                AND nomor LIKE '%GJ%'
            ) a
            LEFT JOIN tr_invoice_header b 
                ON b.no_invoice = a.no_reff
            LEFT JOIN (
                SELECT 
                    nomor,
                    SUM(CASE WHEN no_perkiraan = '4101-01-02' THEN kredit ELSE 0 END) AS revenue,
                    SUM(CASE WHEN no_perkiraan = '5104-01-01' THEN debet ELSE 0 END) AS cogs
                FROM view_gl_jurnal
                GROUP BY nomor
            ) x ON x.nomor = a.nomor
            ORDER BY a.tanggal ASC";

            return $this->db->query($sql)->result();

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
