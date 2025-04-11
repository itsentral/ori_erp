<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center">#</th>
            <th class="text-center">No SO</th>
            <th class="text-center no-sort">Product Name</th>
            <th class="text-center no-sort">Spec</th>
            <th class="text-center no-sort">No SPK</th>
            <th class="text-center no-sort">Tipe</th>
            <th class="text-center no-sort">Keterangan</th>
            <th class="text-center no-sort">Kode</th>
            <th class="text-center no-sort">By</th>
            <th class="text-center no-sort">Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($get_in_trans[$material])){
            $nomor = 0;
            foreach($get_in_trans[$material] as $key => $value){ 
                $nomor++;

                $NO_SO = (!empty($GET_DETAIL[$value['no_ipp']]['so_number']))?$GET_DETAIL[$value['no_ipp']]['so_number']:'';
                $NO_SPK = (!empty($GET_DETAIL_FD[$value['id_milik']]['no_spk']))?$GET_DETAIL_FD[$value['id_milik']]['no_spk']:'';
                $LENGTH_CUT = (!empty($value['length_split']))?', cut: '.number_format($value['length_split']):'';
                 echo "<tr>";
                    echo "<td align='center'>".$nomor."</td>";
                    echo "<td align='center'>".$NO_SO."</td>";
                    echo "<td>".strtoupper($value['id_category'])."</td>";
                    echo "<td>".spec_bq2($value['id_milik']).$LENGTH_CUT."</td>";
                    echo "<td align='center'>".$NO_SPK."</td>";
                    echo "<td align='center'>".strtoupper($value['tipe'])."</td>";
                    echo "<td>".strtolower($value['keterangan'])."</td>";
                    echo "<td>".$value['daycode']."</td>";
                    echo "<td align='center'>".strtolower($value['hist_by'])."</td>";
                    echo "<td align='center'>".date('d-M-Y H:i:s', strtotime($value['hist_date']))."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
<script>
    swal.close();
</script>