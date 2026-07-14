<?php

class Serah_terima_asset_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate Form No: STA001, STA002, etc.
     */
    public function generateFormNo(){
        $query = $this->db->query("SELECT MAX(CAST(SUBSTRING(form_no, 4) AS UNSIGNED)) AS max_no FROM serah_terima_asset WHERE deleted_date IS NULL");
        $result = $query->row_array();
        $next = (!empty($result['max_no'])) ? intval($result['max_no']) + 1 : 1;
        return 'STA' . sprintf('%03d', $next);
    }

    /**
     * Get active assets for dropdown
     */
    public function getActiveAssets(){
        $query = $this->db->query("SELECT id, kd_asset, nm_asset, code_ori FROM asset WHERE deleted_date IS NULL ORDER BY nm_asset ASC");
        return $query->result_array();
    }

    /**
     * Save Serah Terima (Header + Detail)
     */
    public function saveSerahTerima($post){
        $this->db->trans_start();

        $data_session = $this->session->userdata;
        $user_id = $data_session['ORI_User']['username'];

        $form_no = $this->generateFormNo();

        // Insert Header
        $header = array(
            'form_no'       => $form_no,
            'sender'        => $post['sender'],
            'receiver'      => $post['receiver'],
            'lokasi'        => $post['lokasi'],
            'departemen'    => $post['departemen'],
            'created_by'    => $user_id,
            'created_date'  => date('Y-m-d H:i:s'),
        );
        $this->db->insert('serah_terima_asset', $header);
        $header_id = $this->db->insert_id();

        // Insert Details
        if(!empty($post['asset_id'])){
            foreach($post['asset_id'] as $key => $asset_id){
                $detail = array(
                    'header_id'         => $header_id,
                    'asset_id'          => $asset_id,
                    'assets_code'       => $post['assets_code'][$key],
                    'new_assets_code'   => $post['new_assets_code'][$key],
                    'model'             => $post['model'][$key],
                    'merk'              => $post['merk'][$key],
                );
                $this->db->insert('serah_terima_asset_detail', $detail);

                // Update asset master: code_ori dan nama_user
                $update_asset = array(
                    'code_ori'      => $post['new_assets_code'][$key],
                    'nama_user'     => $post['receiver'],
                    'lokasi'        => $post['lokasi'],
                );
                $this->db->where('id', $asset_id);
                $this->db->update('asset', $update_asset);
            }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return array('status' => 0, 'pesan' => 'Gagal menyimpan data serah terima asset.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'pesan' => 'Data serah terima asset berhasil disimpan.');
        }
    }

    /**
     * Update Serah Terima (Header + Detail)
     */
    public function updateSerahTerima($post){
        $this->db->trans_start();

        $data_session = $this->session->userdata;
        $user_id = $data_session['ORI_User']['username'];
        $header_id = $post['id'];

        // Update Header
        $header = array(
            'sender'        => $post['sender'],
            'receiver'      => $post['receiver'],
            'lokasi'        => $post['lokasi'],
            'departemen'    => $post['departemen'],
            'modified_by'   => $user_id,
            'modified_date' => date('Y-m-d H:i:s'),
        );
        $this->db->where('id', $header_id);
        $this->db->update('serah_terima_asset', $header);

        // Delete old details
        $this->db->where('header_id', $header_id);
        $this->db->delete('serah_terima_asset_detail');

        // Insert new Details
        if(!empty($post['asset_id'])){
            foreach($post['asset_id'] as $key => $asset_id){
                $detail = array(
                    'header_id'         => $header_id,
                    'asset_id'          => $asset_id,
                    'assets_code'       => $post['assets_code'][$key],
                    'new_assets_code'   => $post['new_assets_code'][$key],
                    'model'             => $post['model'][$key],
                    'merk'              => $post['merk'][$key],
                );
                $this->db->insert('serah_terima_asset_detail', $detail);

                // Update asset master
                $update_asset = array(
                    'code_ori'      => $post['new_assets_code'][$key],
                    'nama_user'     => $post['receiver'],
                    'lokasi'        => $post['lokasi'],
                );
                $this->db->where('id', $asset_id);
                $this->db->update('asset', $update_asset);
            }
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return array('status' => 0, 'pesan' => 'Gagal mengupdate data serah terima asset.');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'pesan' => 'Data serah terima asset berhasil diupdate.');
        }
    }

    /**
     * Get Header by ID
     */
    public function getById($id){
        $query = $this->db->query("
            SELECT sta.*, d.nm_dept 
            FROM serah_terima_asset sta 
            LEFT JOIN department d ON sta.departemen = d.id
            WHERE sta.id = '".$this->db->escape_str($id)."' 
            AND sta.deleted_date IS NULL
        ");
        return $query->row_array();
    }

    /**
     * Get Detail by Header ID
     */
    public function getDetailByHeaderId($header_id){
        $query = $this->db->query("
            SELECT std.*, a.nm_asset, a.kd_asset
            FROM serah_terima_asset_detail std
            LEFT JOIN asset a ON std.asset_id = a.id
            WHERE std.header_id = '".$this->db->escape_str($header_id)."'
        ");
        return $query->result_array();
    }

    /**
     * Soft Delete
     */
    public function deleteSerahTerima($id){
        $data_session = $this->session->userdata;
        $user_id = $data_session['ORI_User']['username'];

        $this->db->where('id', $id);
        $this->db->update('serah_terima_asset', array(
            'deleted_by'    => $user_id,
            'deleted_date'  => date('Y-m-d H:i:s'),
        ));

        if($this->db->affected_rows() > 0){
            return array('status' => 1, 'pesan' => 'Data berhasil dihapus.');
        } else {
            return array('status' => 0, 'pesan' => 'Gagal menghapus data.');
        }
    }

    /**
     * DataTables JSON
     */
    public function getDataJSON(){
        $controller         = ucfirst(strtolower($this->uri->segment(1)));
        $Arr_Akses          = getAcccesmenu($controller);
        $requestData        = $_REQUEST;

        $like_value     = $requestData['search']['value'];
        $column_order   = $requestData['order'][0]['column'];
        $column_dir     = $requestData['order'][0]['dir'];
        $limit_start    = $requestData['start'];
        $limit_length   = $requestData['length'];

        $sql = "
            SELECT 
                sta.id,
                sta.form_no,
                sta.sender,
                sta.receiver,
                sta.lokasi,
                sta.departemen,
                d.nm_dept,
                sta.created_date,
                GROUP_CONCAT(a.nm_asset SEPARATOR ', ') AS assets_name
            FROM serah_terima_asset sta
            LEFT JOIN department d ON sta.departemen = d.id
            LEFT JOIN serah_terima_asset_detail std ON sta.id = std.header_id
            LEFT JOIN asset a ON std.asset_id = a.id
            WHERE sta.deleted_date IS NULL
            AND (
                sta.form_no LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR sta.receiver LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR sta.lokasi LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR sta.sender LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR d.nm_dept LIKE '%".$this->db->escape_like_str($like_value)."%'
                OR a.nm_asset LIKE '%".$this->db->escape_like_str($like_value)."%'
            )
            GROUP BY sta.id
        ";

        $columns_order_by = array(
            0 => 'sta.id',
            1 => 'sta.form_no',
            2 => 'sta.receiver',
            3 => 'sta.lokasi',
            4 => 'd.nm_dept',
            5 => 'assets_name',
            6 => 'sta.created_date',
        );

        $totalData      = $this->db->query($sql)->num_rows();
        $totalFiltered  = $totalData;

        $order_col = isset($columns_order_by[$column_order]) ? $columns_order_by[$column_order] : 'sta.id';
        $sql .= " ORDER BY ".$order_col." ".$column_dir;
        $sql .= " LIMIT ".$limit_start.", ".$limit_length;

        $query = $this->db->query($sql);

        $data = array();
        $urut = $limit_start + 1;

        foreach($query->result_array() as $row){
            $nestedData     = array();
            $nestedData[]   = "<div align='center'>".$urut."</div>";
            $nestedData[]   = "<div align='left'>".$row['form_no']."</div>";
            $nestedData[]   = "<div align='left'>".$row['receiver']."</div>";
            $nestedData[]   = "<div align='left'>".$row['lokasi']."</div>";
            $nestedData[]   = "<div align='left'>".$row['nm_dept']."</div>";
            $nestedData[]   = "<div align='left'>".$row['assets_name']."</div>";
            $nestedData[]   = "<div align='center'>".date('d-M-Y', strtotime($row['created_date']))."</div>";

            // Action buttons
            $btn_print = "<a href='".site_url('serah_terima_asset/print_pdf/'.$row['id'])."' target='_blank' class='btn btn-sm btn-default' title='Print PDF' data-role='qtip'><i class='fa fa-print'></i></a>";
            $btn_view = "<a href='".site_url('serah_terima_asset/view/'.$row['id'])."' class='btn btn-sm btn-info' title='View' data-role='qtip'><i class='fa fa-eye'></i></a>";
            $btn_edit = "";
            $btn_delete = "";

            if($Arr_Akses['update'] == '1'){
                $btn_edit = "<a href='".site_url('serah_terima_asset/edit/'.$row['id'])."' class='btn btn-sm btn-primary' title='Edit' data-role='qtip'><i class='fa fa-pencil'></i></a>";
            }
            if($Arr_Akses['delete'] == '1'){
                $btn_delete = "<button type='button' class='btn btn-sm btn-danger btn-delete' title='Delete' data-id='".$row['id']."' data-role='qtip'><i class='fa fa-trash'></i></button>";
            }

            $nestedData[] = "<div align='center'>".$btn_print." ".$btn_view." ".$btn_edit." ".$btn_delete."</div>";

            $data[] = $nestedData;
            $urut++;
        }

        $json_data = array(
            "draw"              => intval($requestData['draw']),
            "recordsTotal"      => intval($totalData),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data,
        );

        echo json_encode($json_data);
    }
}
