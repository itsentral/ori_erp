<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='4%'>#</th>
            <th class="text-center" width='33%'>Material Request</th>
            <th class="text-center" width='33%'>Material Aktual</th>
            <th class="text-center no-sort" width='15%'>ESTIMASI</th>
            <th class="text-center no-sort" width='15%'>ACTUAL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($transaksi)){
            $nomor = 0;
            $SUM_EST = 0;
            $SUM_ACT = 0;
            foreach($transaksi as $key => $value){ 
                // if($value['qty_oke'] > 0){
                $nomor++;
                $SUM_EST += $value['qty_oke'];
                $SUM_ACT += $value['check_qty_oke'];
                
                $BOLD = '';
                if($value['id_material_req'] != $value['id_material']){
                    $BOLD = 'text-bold';
                }
                echo "<tr>";
                    echo "<td align='center'>".$nomor."</td>";
                    echo "<td class='$BOLD'>".strtoupper($GET_MATERIAL[$value['id_material_req']]['nm_material'])."</td>";
                    echo "<td class='$BOLD'>".strtoupper($GET_MATERIAL[$value['id_material']]['nm_material'])."</td>";
                    echo "<td class='text-right text-bold text-blue'>".number_format($value['qty_oke'],4)."</td>";
                    echo "<td class='text-right text-bold text-green'>".number_format($value['check_qty_oke'],4)."</td>";
                echo "</tr>";
                // }
            }
            echo "<tr>";
                echo "<th class='text-center' colspan='3'>TOTAL MATERIAL</th>";
                echo "<th class='text-right text-bold text-blue'>".number_format($SUM_EST,4)."</th>";
                echo "<th class='text-right text-bold text-green'>".number_format($SUM_ACT,4)."</th>";
            echo "</tr>";
        }
        else{
            echo "<tr>";
                echo "<td colspan='5'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
<script>
    swal.close();
</script>