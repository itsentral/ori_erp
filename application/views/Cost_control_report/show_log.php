<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='5%'>#</th>
            <th class="text-center no-sort" width='12%'>No SO</th>
            <th class="text-center no-sort" width='12%'>No SPK</th>
            <th class="text-center"  width='15%'>No Tansaksi</th>
            <th class="text-center no-sort" width='10%'>Date</th>
            <th class="text-center no-sort" width='35%'>Material</th>
            <th class="text-center no-sort" width='10%'>Qty</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $GET_NOMOR_SO = get_ipp_release();
        if(!empty($result)){
            foreach($result as $key => $value){ $key++;

                $no_ipp = str_replace('BQ-','',$value['no_ipp']);
                if($value['no_ipp'] == 'resin mixing'){
                    $no_ipp = $value['no_ipp_mixing'];
                }

                $NOMOR_SO = (!empty($GET_NOMOR_SO[$no_ipp]['so_number']))?$GET_NOMOR_SO[$no_ipp]['so_number']:$no_ipp;

                $no_spk = $value['no_spk'];
                if($value['no_ipp'] == 'resin mixing'){
                    $no_spk = $value['no_spk_mixing'];
                }
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td>".$NOMOR_SO."</td>";
                    echo "<td>".$no_spk."</td>";
                    echo "<td>".$value['kode_trans']."</td>";
                    echo "<td>".date('d-M-Y',strtotime($value['checked_date']))."</td>";
                    echo "<td>".$value['id_material']." - ".$value['nm_material']."</td>";
                    echo "<td align='right'>".number_format($value['check_qty_oke'],4)."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>