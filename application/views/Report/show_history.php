<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='4%'>#</th>
            <th class="text-center" width='9%'>NO DOKUMEN</th>
            <!-- <th class="text-center" width='9%'>ID PROGRAM</th> -->
            <th class="text-center" >NM MATERIAL</th>
            <!-- <th class="text-center no-sort" width='8%'>GUDANG</th> -->
            <th class="text-center no-sort" width='12%'>GUDANG DARI</th>
            <th class="text-center no-sort" width='12%'>GUDANG KE</th>
            <th class="text-center no-sort" width='8%'>QTY</th>
            <th class="text-center no-sort" width='8%'>STOK AWAL</th>
            <th class="text-center no-sort" width='8%'>STOK AKHIR</th>
            <th class="text-center no-sort" width='15%'>KETERANGAN</th>
            <th class="text-center no-sort" width='7%'>TANGGAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $row_Cek){ $key++;
                $NO_TRANSX = $row_Cek['no_ipp'];
				$EXPLODE = explode('/',$row_Cek['no_ipp']);
				if(!empty($EXPLODE[1])){
				$GET_KODE = $this->db->get_where('warehouse_adjustment',array('kode_spk'=>$EXPLODE[0],'created_date'=>$EXPLODE[1]))->result();
				$NO_TRANSX = (!empty($GET_KODE[0]->kode_trans))?$GET_KODE[0]->kode_trans:$row_Cek['no_ipp'];
				}

                $gudang		        = $GET_GUDANG[$row_Cek['id_gudang']];
                $gudang_dari        = (!empty($row_Cek['id_gudang_dari']))?$GET_GUDANG[$row_Cek['id_gudang_dari']]:$row_Cek['kd_gudang_dari'];
                $gudang_ke		    = (!empty($row_Cek['id_gudang_ke']))?$GET_GUDANG[$row_Cek['id_gudang_ke']]:$row_Cek['kd_gudang_ke'];
                $jumlah_mat		    = $row_Cek['jumlah_mat'];
                $qty_stock_awal		= $row_Cek['qty_stock_awal'];
                $qty_stock_akhir	= $row_Cek['qty_stock_akhir'];
                $ket		        = $row_Cek['ket'];
                $update_date		= $row_Cek['update_date']; 
                
                $bold_1 = ($row_Cek['id_gudang'] == $row_Cek['id_gudang_dari'])?'text-bold':'';
                $bold_2 = ($row_Cek['id_gudang'] == $row_Cek['id_gudang_ke'])?'text-bold':'';
                echo "<tr>";
                    echo "<td>".$key."</td>";
                    echo "<td>".$NO_TRANSX."</td>";
                    // echo "<td>".strtoupper($row_Cek['idmaterial'])."</td>";
                    echo "<td>".strtoupper($row_Cek['nm_material'])."</td>";
                    // echo "<td>".$gudang."</td>";
                    echo "<td class='".$bold_1."'>".$gudang_dari."</td>";
                    echo "<td class='".$bold_2."'>".$gudang_ke."</td>";
                    echo "<td align='right'>".number_format($jumlah_mat,4)."</td>";
                    echo "<td align='right'>".number_format($qty_stock_awal,4)."</td>";
                    echo "<td align='right'>".number_format($qty_stock_akhir,4)."</td>";
                    echo "<td>".$ket."</td>";
                    echo "<td align='right'>".$update_date."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>