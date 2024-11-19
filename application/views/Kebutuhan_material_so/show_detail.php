<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='5%'>#</th>
            <th class="text-center" >NO SO</th>
            <!-- <th class="text-center" >NO IPP</th> -->
            <th class="text-center" >NM PROJECT</th>
            <th class="text-center" >NO SPK</th>
            <th class="text-center" >PRODUCT</th>
            <th class="text-center" >SPEC</th>
            <th class="text-center" >QTY EST</th>
            <th class="text-center" width='25%'>KEBUTUHAN ESTIMASI</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $value){ $key++;
                $no_ipp = str_replace('PRO-','',$value['id_produksi']);

                $NO_SO = (!empty($GET_IPP_DET[$no_ipp]['so_number']))?$GET_IPP_DET[$no_ipp]['so_number']:'';
                $NM_PROJECT = (!empty($GET_IPP_DET[$no_ipp]['nm_project']))?$GET_IPP_DET[$no_ipp]['nm_project']:'';

                $detail_estimasi = get_estimasi_material_per_spk_detail($value['id_milik']);

                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$NO_SO."</td>";
                    // echo "<td align='center'>".$no_ipp."</td>";
                    echo "<td align='left'>".strtoupper($NM_PROJECT)."</td>";
                    echo "<td align='center'>".$value['no_spk']."</td>";
                    echo "<td align='left'>".strtoupper($value['id_category'])."</td>";
                    echo "<td align='left'>".spec_bq2($value['id_milik'])."</td>";
                    echo "<td align='center'>".number_format($value['qty'])."</td>";
                    echo "<td align='left'>";
                        echo "<table width='100%' border='1' class='table-striped'>";
                            if(!empty($detail_estimasi)){
                                foreach ($detail_estimasi as $key2 => $value2) {
                                    $NM_MATERIAL = (!empty($GET_MATERIAL[$value2['id_material']]['nm_material']))?$GET_MATERIAL[$value2['id_material']]['nm_material']:'';

                                    $berat = $value2['berat'] * $value['qty'];
                                    echo "<tr>";
                                        echo "<td>".$NM_MATERIAL."</td>";
                                        echo "<td width='30%' align='right'>".number_format($berat,4)."</td>";
                                    echo "</tr>";
                                }
                            }
                        echo "</table>";
                    echo "</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='8'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>