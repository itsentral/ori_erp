<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='4%'>#</th>
            <th class="text-center" width='9%'>NO DOKUMEN</th>
            <th class="text-center" width='9%'>CODE PROGRAM</th>
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

                $jumlah_mat		    = $row_Cek['jumlah_qty'];
                $qty_stock_awal		= $row_Cek['qty_stock_awal'];
                $qty_stock_akhir	= $row_Cek['qty_stock_akhir'];
                $update_date		= $row_Cek['update_date']; 

                $get_ket = $this->db->select('keterangan')->get_where('warehouse_adjustment_detail', array('kode_trans'=>$row_Cek['no_trans'],'id_material'=>$row_Cek['code_group']))->result();
				$ket = (!empty($get_ket[0]->keterangan))?$get_ket[0]->keterangan:'';
                
                $bold_1 = ($row_Cek['id_gudang'] == $row_Cek['id_gudang_dari'])?'text-bold':'';
                $bold_2 = ($row_Cek['id_gudang'] == $row_Cek['id_gudang_ke'])?'text-bold':'';
                echo "<tr>";
                    echo "<td>".$key."</td>";
                    echo "<td>".strtoupper($row_Cek['no_trans'])."</td>";
                    echo "<td>".strtoupper($row_Cek['code_group'])."</td>";
                    echo "<td>".strtoupper($row_Cek['material_name_new'])."</td>";
                    // echo "<td>".$gudang."</td>";
                    echo "<td class='".$bold_1."'>".strtoupper($row_Cek['gudang_dari'])."</td>";
                    echo "<td class='".$bold_2."'>".strtoupper($row_Cek['gudang_ke'])."</td>";
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