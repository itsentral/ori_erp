<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='4%'>#</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Accurate Code</th>
            <th class="text-center">Material Name</th>
            <th class="text-center no-sort" width='10%'>PR</th>
            <th class="text-center no-sort" width='10%'>PO</th>
            <th class="text-center no-sort" width='10%'>Incoming</th>
            <th class="text-center no-sort" width='10%'>Dated</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $row_Cek){ $key++;
                if($kategory == 'MATERIAL'){
                    $KD_ACCURATE = (!empty($GET_MATERIAL[$row_Cek['id_barang']]['id_accurate']))?$GET_MATERIAL[$row_Cek['id_barang']]['id_accurate']:'';
                }
                else{
                    $KD_ACCURATE = (!empty($GET_STOK[$row_Cek['id_barang']]['id_accurate']))?$GET_STOK[$row_Cek['id_barang']]['id_accurate']:'';
                }
                echo "<tr>";
                    echo "<td class='text-center'>".$key."</td>";
                    echo "<td>".$kategory."</td>";
                    echo "<td>".$row_Cek['id_barang']."</td>";
                    echo "<td>".$KD_ACCURATE."</td>";
                    echo "<td class='text-left'>".strtoupper($row_Cek['nm_barang'])."</td>";
                    echo "<td class='text-center'>".$row_Cek['no_pr']."</td>";
                    echo "<td class='text-center'>".$row_Cek['no_po']."</td>";
                    echo "<td class='text-center'>".$row_Cek['kode_trans']."</td>";
                    echo "<td class='text-center'>".date('d-M-Y H:i:s',strtotime($row_Cek['created_date']))."</td>";
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