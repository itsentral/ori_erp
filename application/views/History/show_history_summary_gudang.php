<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='8%'>#</th>
            <th class="text-center" >NM PRODUCT</th>
            <th class="text-center no-sort" width='20%'>Total IN</th>
            <th class="text-center no-sort" width='20%'>Total OUT</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            $GET_IN_MATERIAL = $get_in_material;
            $GET_OUT_MATERIAL = $get_out_material;
            foreach($result as $key => $value){ $key++;
                $IN_MATERIAL    = (!empty($GET_IN_MATERIAL[$value['product']]))?number_format($GET_IN_MATERIAL[$value['product']]):'-';
                $OUT_MATERIAL   = (!empty($GET_OUT_MATERIAL[$value['product']]))?number_format($GET_OUT_MATERIAL[$value['product']]):'-';
                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td>".strtoupper($value['product'])."</td>";
                    echo "<td class='text-center text-bold text-green'><span class='text-green text-bold detail_material' style='cursor:pointer;' data-type='in' data-id_material='".$value['product']."'>".$IN_MATERIAL."</span></td>";
                    echo "<td class='text-center text-bold text-red'><span class='text-red text-bold detail_material' style='cursor:pointer;' data-type='out' data-id_material='".$value['product']."'>".$OUT_MATERIAL."</span></td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='4'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>