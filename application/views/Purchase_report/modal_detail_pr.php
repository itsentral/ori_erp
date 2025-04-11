<div class="box-body">
    <table class="table table-sm table-bordered table-striped" width='100%'>
        <thead>
            <tr class='bg-blue'>
                <th class="text-center">#</th>
                <th class="text-center">Tanggal PR</th>
                <th class="text-center">Jenis Pembelian</th>
                <th class="text-center">No RFQ</th>
                <th class="text-center">Nama Barang</th>
                <th class="text-center">Qty PR</th>
                <th class="text-center">Qty RFQ</th>
                <th class="text-center">Supplier</th>
                <!-- <th class="text-center">Approval 1</th>
                <th class="text-center">Approval 2</th> -->
                <th class="text-center">No PO</th>
                <th class="text-center">Qty PO</th>
                <th class="text-center">Price Unit</th>
                <th class="text-center">Total Price</th>
                <th class="text-center">Status PR</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($getData as $key => $value) { $key++;
                // $color1 = ($value['status'] == 'SETUJU')?'bg-green':'bg-red';
                // $color2 = ($value['status_apv'] == 'SETUJU')?'bg-green':'bg-red';
                $HARGA_UNIT = (!empty($value['harga_idr']))?$value['harga_idr']:0;
                $TOTAL_HARGA = $HARGA_UNIT * $value['qty_po'];
                echo "<tr>";
                    echo "<td align='center'>$key</td>";
                    echo "<td align='center'>".date('d-M-Y',strtotime($value['tgl_pr']))."</td>";
                    echo "<td align='center'>".strtoupper($value['jenis_pembelian'])."</td>";
                    echo "<td align='center'>".$value['no_rfq']."</td>";
                    echo "<td align='left'>".strtoupper($value['nm_barang'])."</td>";
                    echo "<td align='center'>".number_format($value['qty_pr'],2)."</td>";
                    echo "<td align='center'>".number_format($value['qty_rfq'],2)."</td>";
                    echo "<td align='left'>".strtoupper($value['nm_supplier'])."</td>";
                    // echo "<td align='center'><span class='badge ".$color1."'>".$value['status']."</span></td>";
                    // echo "<td align='center'><span class='badge ".$color2."'>".$value['status_apv']."</span></td>";
                    echo "<td align='center'>".strtoupper($value['no_po'])."</td>";
                    echo "<td align='center'>".number_format($value['qty_po'])."</td>";
                    echo "<td align='right'>".number_format($HARGA_UNIT)."</td>";
                    echo "<td align='right'>".number_format($TOTAL_HARGA)."</td>";
                    echo "<td align='center'><span class='badge' style='background-color: ".color_status_purchase($value['sts_ajuan'])['color']."'>".color_status_purchase($value['sts_ajuan'])['status']."</span></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    swal.close()
</script>