<?php
class Tracking_so_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Get tracking data for a specific SO (id_bq) and PO
	 * Tracks: SPK -> Produksi -> FG -> In Transit -> Customer -> Invoice
	 */
	public function get_tracking_data($id_bq, $no_po){
		$result = array();

		// Get SO detail items for the selected SO
		$so_details = $this->db->query("
			SELECT 
				a.id,
				a.id_bq,
				a.id_milik,
				a.no_spk,
				a.id_category AS product,
				a.qty AS qty_so,
				b.no_po,
				c.so_number
			FROM so_detail_header a
			LEFT JOIN billing_so b ON REPLACE(a.id_bq,'BQ-','') = b.no_ipp
			LEFT JOIN so_number c ON a.id_bq = c.id_bq
			WHERE a.id_bq = '".$this->db->escape_str($id_bq)."'
			ORDER BY a.id ASC
		")->result_array();

		if(!empty($so_details)){
			foreach($so_details as $detail){
				$qty_so = (int)$detail['qty_so'];

				// SPK data - get total qty assigned to SPK
				$spk_data = $this->db->query("
					SELECT COALESCE(SUM(f.qty),0) AS total_spk
					FROM production_spk f
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$spk_total = (!empty($spk_data)) ? (int)$spk_data->total_spk : 0;
				$spk_r = $spk_total; // Released to SPK
				$spk_o = $qty_so - $spk_total; // Outstanding (belum di-SPK)
				if($spk_o < 0) $spk_o = 0;

				// PRODUKSI - get qty already produced (from production_spk_parsial with spk=1)
				$prod_data = $this->db->query("
					SELECT COALESCE(SUM(g.qty),0) AS total_prod
					FROM production_spk f
					LEFT JOIN production_spk_parsial g ON f.id = g.id_spk AND g.spk = '1'
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$prod_total = (!empty($prod_data)) ? (int)$prod_data->total_prod : 0;
				$prod_r = $prod_total; // Released (sudah diproduksi)
				$prod_o = $spk_r - $prod_total; // Outstanding (belum diproduksi)
				if($prod_o < 0) $prod_o = 0;

				// Defect count
				$defect_data = $this->db->query("
					SELECT COALESCE(SUM(g.qty),0) AS total_defect
					FROM production_spk f
					LEFT JOIN production_spk_parsial g ON f.id = g.id_spk AND g.spk = '2'
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$prod_d = (!empty($defect_data)) ? (int)$defect_data->total_defect : 0;

				// FG (Finish Good) - items that have been moved to FG
				$fg_data = $this->db->query("
					SELECT COUNT(*) AS total_fg
					FROM production_spk f
					JOIN production_spk_parsial g ON f.id = g.id_spk AND g.spk = '1'
					JOIN data_erp_wip_group h ON CONCAT(f.kode_spk,'/',g.created_date) = h.kode_trans AND h.jenis = 'in'
					JOIN data_erp_fg j ON h.id_trans = j.id_trans AND j.jenis = 'in'
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$fg_total = (!empty($fg_data)) ? (int)$fg_data->total_fg : 0;
				$fg_r = $fg_total; // Released to FG
				$fg_o = $prod_r - $fg_total; // Outstanding (belum FG)
				if($fg_o < 0) $fg_o = 0;

				// IN TRANSIT - items that have been sent/delivered
				$transit_data = $this->db->query("
					SELECT COUNT(*) AS total_transit, GROUP_CONCAT(DISTINCT k.kode_delivery SEPARATOR ', ') AS delivery_codes
					FROM production_spk f
					JOIN production_spk_parsial g ON f.id = g.id_spk AND g.spk = '1'
					JOIN data_erp_wip_group h ON CONCAT(f.kode_spk,'/',g.created_date) = h.kode_trans AND h.jenis = 'in'
					JOIN data_erp_fg j ON h.id_trans = j.id_trans AND j.jenis = 'in'
					JOIN data_erp_in_transit k ON j.id_trans = k.id_trans AND j.jenis = k.jenis AND j.id_pro = k.id_pro AND k.jenis = 'in'
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$transit_total = (!empty($transit_data)) ? (int)$transit_data->total_transit : 0;
				$no_delivery = (!empty($transit_data->delivery_codes)) ? $transit_data->delivery_codes : '-';
				$transit_r = $transit_total; // Released (sudah dikirim)
				$transit_o = $fg_r - $transit_total; // Outstanding (belum dikirim)
				if($transit_o < 0) $transit_o = 0;

				// IN CUSTOMER - items received by customer
				$cust_data = $this->db->query("
					SELECT COUNT(*) AS total_cust
					FROM production_spk f
					JOIN production_spk_parsial g ON f.id = g.id_spk AND g.spk = '1'
					JOIN data_erp_wip_group h ON CONCAT(f.kode_spk,'/',g.created_date) = h.kode_trans AND h.jenis = 'in'
					JOIN data_erp_fg j ON h.id_trans = j.id_trans AND j.jenis = 'in'
					JOIN data_erp_in_transit k ON j.id_trans = k.id_trans AND j.jenis = k.jenis AND j.id_pro = k.id_pro AND k.jenis = 'in'
					JOIN data_erp_in_customer l ON k.id_trans = l.id_trans AND k.jenis = l.jenis AND k.id_pro = l.id_pro AND l.jenis = 'in'
					WHERE f.id_milik = '".$this->db->escape_str($detail['id'])."'
				")->row();
				$cust_total = (!empty($cust_data)) ? (int)$cust_data->total_cust : 0;
				$cust_r = $cust_total; // Released (sudah di customer)
				$cust_o = $transit_r - $cust_total; // Outstanding
				if($cust_o < 0) $cust_o = 0;

				// INVOICE - get detail per material from tr_invoice_detail
				$inv_data = $this->db->query("
					SELECT 
						GROUP_CONCAT(DISTINCT ih.no_invoice SEPARATOR ', ') AS invoice_numbers,
						COALESCE(SUM(id.harga_total_idr),0) AS total_nilai,
						COALESCE(SUM(id.qty),0) AS total_inv_qty
					FROM tr_invoice_detail id
					LEFT JOIN tr_invoice_header ih ON id.no_invoice = ih.no_invoice
					WHERE ih.no_ipp = '".$this->db->escape_str(str_replace('BQ-','',$id_bq))."'
					AND ih.no_po = '".$this->db->escape_str($no_po)."'
					AND id.nm_material LIKE '%".$this->db->escape_like_str($detail['product'])."%'
				")->row();

				$no_invoice = (!empty($inv_data->invoice_numbers)) ? $inv_data->invoice_numbers : '-';
				$inv_nilai = (!empty($inv_data->total_nilai) && $inv_data->total_nilai > 0) ? number_format($inv_data->total_nilai, 2) : '-';
				$inv_qty = (!empty($inv_data)) ? (int)$inv_data->total_inv_qty : 0;

				$inv_r = $inv_qty; // Items invoiced
				$inv_o = $cust_r - $inv_qty; // Items not yet invoiced
				if($inv_o < 0) $inv_o = 0;

				// Get spesifikasi using helper function spec_bq3
				$spesifikasi = spec_bq3($detail['id']);

				$result[] = array(
					'no_po'			=> $no_po,
					'so_number'		=> (!empty($detail['so_number'])) ? $detail['so_number'] : '-',
					'no_spk'		=> (!empty($detail['no_spk'])) ? $detail['no_spk'] : '-',
					'product'		=> strtoupper($detail['product']),
					'spesifikasi'	=> (!empty($spesifikasi)) ? $spesifikasi : '-',
					'qty_so'		=> $qty_so,
					'spk_r'			=> $spk_r,
					'spk_o'			=> $spk_o,
					'prod_r'		=> $prod_r,
					'prod_o'		=> $prod_o,
					'prod_d'		=> $prod_d,
					'fg_r'			=> $fg_r,
					'fg_o'			=> $fg_o,
					'transit_r'		=> $transit_r,
					'transit_o'		=> $transit_o,
					'no_delivery'	=> $no_delivery,
					'cust_r'		=> $cust_r,
					'cust_o'		=> $cust_o,
					'inv_r'			=> $inv_r,
					'inv_o'			=> $inv_o,
					'no_invoice'	=> $no_invoice,
					'inv_nilai'		=> $inv_nilai
				);
			}
		}

		return $result;
	}
}
?>
