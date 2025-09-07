<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center">#</th>
            <th class="text-left">NO PO</th>
            <th class="text-center">NO SO</th>
            <th class="text-center">NO SPK</th>
            <th class="text-left" >PRODUCT NAME</th>
            <th class="text-center">QTY<br>SO</th>
            <th class="text-right">NILAI SO<br>PER QTY SO</th>
            <th class="text-center">KODE SPK</th>
            <th class="text-center">QTY<br>SPK</th>
            <th class="text-center">QTY<br>MIX</th>
            <th class="text-right">EST. WEIGHT</th>
            <th class="text-right">ACT. WEIGHT</th>
            <th class="text-right">MATERIAL</th>
            <th class="text-right">DIRECT</th>
            <th class="text-right">INDIRECT</th>
            <th class="text-right">CONSUMABLE</th>
            <th class="text-right">FOH</th>
            <th class="text-center">QTY<br>WIP</th>
            <th class="text-right">WIP TOTAL</th>
            <th class="text-center">QTY<br>FG</th>
            <th class="text-right">FG</th>
            <th class="text-right">IN<br>TRANSIT</th>
            <th class="text-right">DELIVERY<br>CODE</th>
            <th class="text-right">IN<br>CUSTOMER</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $row_Cek){ $key++;
                $wip_material = (!empty($row_Cek['wip_material']))?number_format($row_Cek['wip_material']):'';
                $wip_direct = (!empty($row_Cek['wip_direct']))?number_format($row_Cek['wip_direct']):'';
                $wip_indirect = (!empty($row_Cek['wip_indirect']))?number_format($row_Cek['wip_indirect']):'';
                $wip_consumable = (!empty($row_Cek['wip_consumable']))?number_format($row_Cek['wip_consumable']):'';
                $wip_foh = (!empty($row_Cek['wip_foh']))?number_format($row_Cek['wip_foh']):'';
                $wip_total = (!empty($row_Cek['wip_total']))?number_format($row_Cek['wip_total']):'';

                $est_material = (!empty($row_Cek['est_material']))?number_format($row_Cek['est_material'],3):'';
                $real_material = (!empty($row_Cek['real_material']))?number_format($row_Cek['real_material'],3):'';
                $nilai_fg = (!empty($row_Cek['nilai_fg']))?number_format($row_Cek['nilai_fg']):'';
                $nilai_intransit = (!empty($row_Cek['nilai_intransit']))?number_format($row_Cek['nilai_intransit']):'';
                $nilai_incustomer = (!empty($row_Cek['nilai_incustomer']))?number_format($row_Cek['nilai_incustomer']):'';
                $kode_delivery = (!empty($row_Cek['kode_delivery']))?$row_Cek['kode_delivery']:'';

                $qty_mix = (!empty($row_Cek['qty_mix']))?number_format($row_Cek['qty_mix']):'';
                $qty_parsial = (!empty($row_Cek['qty_parsial']))?number_format($row_Cek['qty_parsial']):'';
                $qty_fg = (!empty($nilai_fg))?1:'';
                $qty_wip = (!empty($wip_total))?$qty_parsial:'';

                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='left'>".strtoupper($row_Cek['no_po'])."</td>";
                    echo "<td align='center'>".str_replace('BQ-','',$row_Cek['no_so'])."</td>";
                    echo "<td align='center'>".strtoupper($row_Cek['no_spk'])."</td>";
                    echo "<td>".strtoupper($row_Cek['id_category'])."</td>";
                    echo "<td align='center'>".number_format($row_Cek['qty'])."</td>";
                    echo "<td align='right'>".number_format($row_Cek['deal_idr'],2)."</td>";
                    echo "<td>".$row_Cek['kode_spk'].'/'.$row_Cek['mixing_uniq']."</td>";
                    echo "<td align='center'>".$qty_mix."</td>";
                    echo "<td align='center'>".$qty_parsial."</td>";
                    echo "<td align='right'>".$est_material."</td>";
                    echo "<td align='right'>".$real_material."</td>";
                    echo "<td align='right'>".$wip_material."</td>";
                    echo "<td align='right'>".$wip_direct."</td>";
                    echo "<td align='right'>".$wip_indirect."</td>";
                    echo "<td align='right'>".$wip_consumable."</td>";
                    echo "<td align='right'>".$wip_foh."</td>";
                    echo "<td align='center'>".$qty_wip."</td>";
                    echo "<td align='right'>".$wip_total."</td>";
                    echo "<td align='center'>".$qty_fg."</td>";
                    echo "<td align='right'>".$nilai_fg."</td>";
                    echo "<td align='right'>".$nilai_intransit."</td>";
                    echo "<td align='center'>".$kode_delivery."</td>";
                    echo "<td align='right'>".$nilai_incustomer."</td>";
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