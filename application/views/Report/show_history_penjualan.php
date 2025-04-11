<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center">Induk Produk</th>
            <th class="text-center">Lokal / Eksport</th>
            <th class="text-center no-sort" width='8%'>Revenue</th>
            <th class="text-center no-sort" width='8%'>Material</th>
            <th class="text-center no-sort" width='8%'>Direct</th>
            <th class="text-center no-sort" width='8%'>Indirect</th>
            <th class="text-center no-sort" width='8%'>Consumable</th>
            <th class="text-center no-sort" width='8%'>Machine</th>
            <th class="text-center no-sort" width='8%'>MouldMandril</th>
            <th class="text-center no-sort" width='8%'>FOH</th>
            <th class="text-center no-sort" width='8%'>Profit</th>
            <th class="text-center no-sort" width='8%'>Allowance</th>
        </tr>
    </thead>
    <tbody>
        <?php
        //PIPE
        $SUM_PIPE_REV_LOCAL     = 0;
        $SUM_PIPE_REV_EXPORT    = 0;
        $SUM_PIPE_MAT_LOCAL     = 0;
        $SUM_PIPE_MAT_EXPORT    = 0;
        $SUM_PIPE_DIR_LOCAL     = 0;
        $SUM_PIPE_DIR_EXPORT    = 0;
        $SUM_PIPE_IND_LOCAL     = 0;
        $SUM_PIPE_IND_EXPORT    = 0;
        $SUM_PIPE_CON_LOCAL     = 0;
        $SUM_PIPE_CON_EXPORT    = 0;
        $SUM_PIPE_MAC_LOCAL     = 0;
        $SUM_PIPE_MAC_EXPORT    = 0;
        $SUM_PIPE_MM_LOCAL     = 0;
        $SUM_PIPE_MM_EXPORT    = 0;
        $SUM_PIPE_FOH_LOCAL     = 0;
        $SUM_PIPE_FOH_EXPORT    = 0;
        $SUM_PIPE_GP_LOCAL      = 0;
        $SUM_PIPE_GP_EXPORT     = 0;
        $SUM_PIPE_AL_LOCAL      = 0;
        $SUM_PIPE_AL_EXPORT     = 0;
        //FITTING
        $SUM_FIT_REV_LOCAL     = 0;
        $SUM_FIT_REV_EXPORT    = 0;
        $SUM_FIT_MAT_LOCAL     = 0;
        $SUM_FIT_MAT_EXPORT    = 0;
        $SUM_FIT_DIR_LOCAL     = 0;
        $SUM_FIT_DIR_EXPORT    = 0;
        $SUM_FIT_IND_LOCAL     = 0;
        $SUM_FIT_IND_EXPORT    = 0;
        $SUM_FIT_CON_LOCAL     = 0;
        $SUM_FIT_CON_EXPORT    = 0;
        $SUM_FIT_MAC_LOCAL     = 0;
        $SUM_FIT_MAC_EXPORT    = 0;
        $SUM_FIT_MM_LOCAL     = 0;
        $SUM_FIT_MM_EXPORT    = 0;
        $SUM_FIT_FOH_LOCAL     = 0;
        $SUM_FIT_FOH_EXPORT    = 0;
        $SUM_FIT_GP_LOCAL      = 0;
        $SUM_FIT_GP_EXPORT     = 0;
        $SUM_FIT_AL_LOCAL      = 0;
        $SUM_FIT_AL_EXPORT     = 0;
        
        foreach ($result as $key => $value) {
            $Tipe = substr($value['id_bq'], -1, 1);
            $dataDetail = $this->db->get_where('laporan_revised_detail',array('id_bq'=>$value['id_bq'],'revised_no'=>$value['no_revisi']))->result_array();
            foreach ($dataDetail as $key2 => $value2) {
                if($Tipe == 'L' AND $value2['product_parent'] == 'pipe'){
                    $SUM_PIPE_REV_LOCAL     += $value2['total_price_last'];
                    $SUM_PIPE_MAT_LOCAL     += $value2['est_harga'];
                    $SUM_PIPE_DIR_LOCAL     += $value2['direct_labour'];
                    $SUM_PIPE_IND_LOCAL     += $value2['indirect_labour'];
                    $SUM_PIPE_CON_LOCAL     += $value2['consumable'];
                    $SUM_PIPE_MAC_LOCAL     += $value2['machine'];
                    $SUM_PIPE_MM_LOCAL     += $value2['mould_mandrill'];
                    $SUM_PIPE_FOH_LOCAL     += $value2['foh_consumable'] + $value2['foh_depresiasi'] + $value2['biaya_gaji_non_produksi'] + $value2['biaya_non_produksi'] + $value2['biaya_rutin_bulanan'];
                    $SUM_PIPE_GP_LOCAL      += ($value2['unit_price'] * $value2['qty']) * $value2['profit'] / 100;
                    $SUM_PIPE_AL_LOCAL      += ($value2['total_price']) * $value2['allowance'] / 100;
                }
                if($Tipe == 'E' AND $value2['product_parent'] == 'pipe'){
                    $SUM_PIPE_REV_EXPORT    += $value2['total_price_last'];
                    $SUM_PIPE_MAT_EXPORT    += $value2['est_harga'];
                    $SUM_PIPE_DIR_EXPORT    += $value2['direct_labour'];
                    $SUM_PIPE_IND_EXPORT    += $value2['indirect_labour'];
                    $SUM_PIPE_CON_EXPORT    += $value2['consumable'];
                    $SUM_PIPE_MAC_EXPORT     += $value2['machine'];
                    $SUM_PIPE_MM_EXPORT     += $value2['mould_mandrill'];
                    $SUM_PIPE_FOH_EXPORT    += $value2['foh_consumable'] + $value2['foh_depresiasi'] + $value2['biaya_gaji_non_produksi'] + $value2['biaya_non_produksi'] + $value2['biaya_rutin_bulanan'];
                    $SUM_PIPE_GP_EXPORT     += ($value2['unit_price'] * $value2['qty']) * $value2['profit'] / 100;
                    $SUM_PIPE_AL_EXPORT     += ($value2['total_price']) * $value2['allowance'] / 100;
                }

                if($Tipe == 'L' AND $value2['product_parent'] != 'pipe'){
                    $SUM_FIT_REV_LOCAL     += $value2['total_price_last'];
                    $SUM_FIT_MAT_LOCAL     += $value2['est_harga'];
                    $SUM_FIT_DIR_LOCAL     += $value2['direct_labour'];
                    $SUM_FIT_IND_LOCAL     += $value2['indirect_labour'];
                    $SUM_FIT_CON_LOCAL     += $value2['consumable'];
                    $SUM_FIT_MAC_LOCAL     += $value2['machine'];
                    $SUM_FIT_MM_LOCAL     += $value2['mould_mandrill'];
                    $SUM_FIT_FOH_LOCAL     += $value2['foh_consumable'] + $value2['foh_depresiasi'] + $value2['biaya_gaji_non_produksi'] + $value2['biaya_non_produksi'] + $value2['biaya_rutin_bulanan'];
                    $SUM_FIT_GP_LOCAL      += ($value2['unit_price'] * $value2['qty']) * $value2['profit'] / 100;
                    $SUM_FIT_AL_LOCAL      += ($value2['total_price']) * $value2['allowance'] / 100;
                }
                if($Tipe == 'E' AND $value2['product_parent'] != 'pipe'){
                    $SUM_FIT_REV_EXPORT    += $value2['total_price_last'];
                    $SUM_FIT_MAT_EXPORT    += $value2['est_harga'];
                    $SUM_FIT_DIR_EXPORT    += $value2['direct_labour'];
                    $SUM_FIT_IND_EXPORT    += $value2['indirect_labour'];
                    $SUM_FIT_CON_EXPORT    += $value2['consumable'];
                    $SUM_FIT_MAC_EXPORT     += $value2['machine'];
                    $SUM_FIT_MM_EXPORT     += $value2['mould_mandrill'];
                    $SUM_FIT_FOH_EXPORT    += $value2['foh_consumable'] + $value2['foh_depresiasi'] + $value2['biaya_gaji_non_produksi'] + $value2['biaya_non_produksi'] + $value2['biaya_rutin_bulanan'];
                    $SUM_FIT_GP_EXPORT     += ($value2['unit_price'] * $value2['qty']) * $value2['profit'] / 100;
                    $SUM_FIT_AL_EXPORT     += ($value2['total_price']) * $value2['allowance'] / 100;
                }
            }
        }
            echo "<tr>";
                echo "<td>PIPE</td>";
                echo "<td>LOCAL</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_REV_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MAT_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_DIR_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_IND_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_CON_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MAC_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MM_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_FOH_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_GP_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_AL_LOCAL,2)."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>PIPE</td>";
                echo "<td>EXPORT</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_REV_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MAT_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_DIR_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_IND_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_CON_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MAC_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_MM_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_FOH_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_GP_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_PIPE_AL_EXPORT,2)."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>FITTING</td>";
                echo "<td>LOCAL</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_REV_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MAT_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_DIR_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_IND_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_CON_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MAC_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MM_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_FOH_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_GP_LOCAL,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_AL_LOCAL,2)."</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>FITTING</td>";
                echo "<td>EXPORT</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_REV_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MAT_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_DIR_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_IND_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_CON_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MAC_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_MM_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_FOH_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_GP_EXPORT,2)."</td>";
                echo "<td class='text-right'>".number_format($SUM_FIT_AL_EXPORT,2)."</td>";
            echo "</tr>";
        ?>
    </tbody>
</table>