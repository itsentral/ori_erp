<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='5%'>#</th>
            <th class="text-center" >NO TRANSAKSI</th>
            <th class="text-center no-sort" width='14%'>GUDANG DARI</th>
            <th class="text-center no-sort" width='14%'>GUDANG KE</th>
            <th class="text-center no-sort" width='12%'>DATE TRANSAKSI</th>
            <th class="text-center no-sort" width='12%'>DATE REQUEST</th>
            <th class="text-center no-sort" width='12%'>DATE AKTUAL</th>
            <th class="text-center no-sort" width='8%'>DETAIL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $value){ $key++;
                $GUDANG_DARI = (!empty($GET_GUDANG[$value['id_gudang_dari']]))?strtoupper($GET_GUDANG[$value['id_gudang_dari']]):'-';
                $GUDANG_KE   = (!empty($GET_GUDANG[$value['id_gudang_ke']]))?strtoupper($GET_GUDANG[$value['id_gudang_ke']]):'-';
                
                $tgl_trans = (!empty($value['tanggal']))?date('d-M-Y',strtotime($value['tanggal'])):'-';
                $tgl_request = (!empty($value['created_date']))?date('d-M-Y',strtotime($value['created_date'])):'-';
                $tgl_aktual = (!empty($value['checked_date']))?date('d-M-Y',strtotime($value['checked_date'])):'-';

                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['kode_trans']."</td>";
                    echo "<td align='center'>".$GUDANG_DARI."</td>";
                    echo "<td align='center'>".$GUDANG_KE."</td>";
                    echo "<td align='center'>".$tgl_trans."</td>";
                    echo "<td align='center'>".$tgl_request."</td>";
                    echo "<td align='center'>".$tgl_aktual."</td>";
                    echo "<td align='center'><span class='text-green text-bold detail_material' style='cursor:pointer;' data-kode_trans='".$value['kode_trans']."'>DETAIL</span></td>";
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