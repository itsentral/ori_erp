<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='4%'>#</th>
            <th class="text-center">SALES ORDER</th>
            <th class="text-center">PRODUK</th>
            <th class="text-center">SPEC</th>
            <th class="text-center">QTY</th>
            <th class="text-center">HARGA PER PCS (IDR)</th>
            <th class="text-center">TOTAL NILAI PENJUALAN (IDR)</th>
            <th class="text-center">QTY PRODUKSI</th>
            <th class="text-center">NILAI PER PCS PRODUKSI (IDR)</th>
            <th class="text-center">TOTAL NILAI PRODUK (IDR)</th>
            <th class="text-center">PROFIT PER PRODUK (IDR)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $row_Cek){ $key++;
                $QTY_FG = (!empty($ArrData[$row_Cek['id_milik_so']]['qty']))?$ArrData[$row_Cek['id_milik_so']]['qty']:0;
                $price_idr = (!empty($ArrData[$row_Cek['id_milik_so']]['price_idr']))?$ArrData[$row_Cek['id_milik_so']]['price_idr']:0;

                $color = '';
                if($row_Cek['qty'] != $QTY_FG){
                    $color = 'text-red text-bold';
                }
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".strtoupper($no_so)."</td>";
                    echo "<td>".strtoupper($row_Cek['product'])."</td>";
                    echo "<td>".strtoupper($row_Cek['spec'])."</td>";
                    echo "<td align='center' class='".$color."'>".number_format($row_Cek['qty'])."</td>";
                    $HRG_PER_PCS = 0;
                    if($row_Cek['qty'] > 0 AND $row_Cek['total_deal_idr'] > 0){
                        $HRG_PER_PCS = $row_Cek['total_deal_idr'] / $row_Cek['qty'];
                    }
                    echo "<td align='right'>".number_format($HRG_PER_PCS)."</td>";
                    echo "<td align='right'>".number_format($row_Cek['total_deal_idr'])."</td>";

                    
                    $HRG_PER_PCS_FG = 0;
                    if($QTY_FG > 0 AND $price_idr > 0){
                        $HRG_PER_PCS_FG = $price_idr / $QTY_FG;
                    }
                    echo "<td align='center' class='".$color."'>".number_format($QTY_FG)."</td>";
                    echo "<td align='right'>".number_format($HRG_PER_PCS_FG)."</td>";
                    echo "<td align='right'>".number_format($price_idr)."</td>";

                    $PROFIT = $HRG_PER_PCS-$HRG_PER_PCS_FG;
                    $color = '';
                    if($PROFIT <= 0){
                        $color = 'text-red text-bold';
                    }

                    echo "<td align='right' class='".$color."'>".number_format($PROFIT)."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='11'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>