<?php if($hist_produksi == '0'){ ?>
<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='5%'>#</th>
            <th class="text-center" width='10%'>No SO</th>
            <th class="text-center" width='10%'>No SPK</th>
            <th class="text-center">Product</th>
            <th class="text-center" width='15%'>Spec</th>
            <th class="text-center" width='10%'>Qty</th>
            <th class="text-center" width='10%'>Sudah Input</th>
            <th class="text-center" width='10%'>Qty Balance</th>
            <th class="text-center" width='10%'>Qty Produksi</th>
            <!-- <th class="text-center" width='10%'>Sts SPK</th>
            <th class="text-center" width='10%'>Sts SPK Mixing</th> -->
        </tr>
    </thead>
    <tbody>
        <?php
            if(!empty($get_detail_spk)){
                foreach($get_detail_spk AS $key => $value){
                    $key++;
                    $EXPLODE = explode('-',$value['product_code']);
                    $NO_SPK = get_name('so_detail_header','no_spk','id',$value['id_milik']);
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".$EXPLODE[0]."</td>";
                        echo "<td align='center'>".$NO_SPK."</td>";
                        echo "<td>".strtoupper($value['product'])."</td>";
                        echo "<td>".spec_bq2($value['id_milik'])."</td>";
                        echo "<td class='text-center text-blue text-bold'>".number_format($value['qty'])."</td>";
                        echo "<td class='text-center text-green text-bold'>".number_format($value['qty_input'])."</td>";
                        echo "<td class='text-center text-red text-bold sisa_spk'>".number_format($value['qty'] - $value['qty_input'])."</td>";
                        echo "<td align='center'>
                                <input type='hidden' name='detail_input[$key][id]' class='form-control text-center' value='".$value['id']."'>
                                <input type='hidden' name='detail_input[$key][id_milik]' class='form-control text-center' value='".$value['id_milik']."'>
                                <input type='hidden' name='detail_input[$key][qty_all]' class='form-control text-center' value='".$value['qty']."'>
                                <input type='text' name='detail_input[$key][qty]' class='form-control text-center autoNumeric0 qty_spk'>
                                </td>";
                        // echo "<td align='center'>".strtoupper($value['spk1'])."</td>";
                        // echo "<td align='center'>".strtoupper($value['spk2'])."</td>";
                    echo "</tr>";
                }
            }
            else{
                echo "<tr>";
                    echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
                echo "</tr>";
            }
        ?>
    </tbody>
</table>
<?php } ?>

<?php if($hist_produksi != '0'){ ?>
<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" width='5%'>#</th>
            <th class="text-center" width='10%'>No SO</th>
            <th class="text-center" width='10%'>No SPK</th>
            <th class="text-center">Product</th>
            <th class="text-center" width='15%'>Spec</th>
            <th class="text-center" width='10%'>Qty</th>
            <th class="text-center" width='10%'>Qty Produksi</th>
            <!-- <th class="text-center" width='10%'>Sts SPK</th>
            <th class="text-center" width='10%'>Sts SPK Mixing</th> -->
        </tr>
    </thead>
    <tbody>
        <?php
            if(!empty($get_detail_spk2)){
                foreach($get_detail_spk2 AS $key => $value){
                    $key++;
                    $EXPLODE = explode('-',$value['product_code']);

                    $NO_SO = $EXPLODE[0];
                    $NO_SPK = $value['no_spk'];
                    $SPEC = spec_bq2($value['id_milik']);
                    if($value['typeTanki'] == 'tanki'){
                        $NO_SPK = $value['no_spk'];
                        $NO_SO = $value['no_so'];
                        $SPEC = (!empty($tanki_model->get_spec($value['id_milik'])))?$tanki_model->get_spec($value['id_milik']):'';
                    }
                    if($value['typeTanki'] == 'deadstok'){
                        $SPEC = '';
                    }
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".$NO_SO."</td>";
                        echo "<td align='center'>".$NO_SPK."</td>";
                        echo "<td>".strtoupper($value['product'])."</td>";
                        echo "<td>".$SPEC."</td>";
                        echo "<td class='text-center text-blue text-bold'>".number_format($value['qty'])."</td>";
                        echo "<td align='center'>
                                <input type='hidden' name='detail_input[$key][id]' class='form-control text-center' value='".$value['id']."'>
                                <input type='hidden' name='detail_input[$key][id_milik]' class='form-control text-center' value='".$value['id_milik']."'>
                                <input type='hidden' name='detail_input[$key][qty_all]' class='form-control text-center' value='".$value['qty']."'>
                                <input type='text' name='detail_input[$key][qty]' class='form-control text-center autoNumeric0 qty_spk' value='".$value['qty_parsial']."' readonly>
                                </td>";
                        // echo "<td align='center'>".strtoupper($value['spk1'])."</td>";
                        // echo "<td align='center'>".strtoupper($value['spk2'])."</td>";
                    echo "</tr>";
                }
            }
            else{
                echo "<tr>";
                    echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
                echo "</tr>";
            }
        ?>
    </tbody>
</table>
<?php } ?>