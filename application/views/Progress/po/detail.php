<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" rowspan='2'>#</th>
            <th class="text-center" rowspan='2'>NO PO</th>
            <th class="text-center" rowspan='2'>SUPPLIER</th>
            <th class="text-center" rowspan='2'>PRODUCT</th>
            <th class="text-center" rowspan='2'>QTY PR</th>
            <th class="text-center" rowspan='2'>QTY RFQ</th>
            <th class="text-center" colspan='2'>PO</th>
            <th class="text-center" colspan='2'>INCOMING</th>
        </tr>
        <tr class='bg-blue'>
            <th class="text-right">PO</th>
            <th class="text-right">O</th>
            <th class="text-right">IN</th>
            <th class="text-right">O</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $value){ 
                $key++;

                $QTY_PR    = $value['qty_pr'];
                $QTY_RFQ   = $value['qty_rfq'];

                $qty_po                 = ($value['qty_po'] > 0)?number_format($value['qty_po'],2):'-';
                $outstanding_po         = ($value['outstanding_po'] > 0)?number_format($value['outstanding_po'],2):'-';
                $qty_incoming           = ($value['qty_incoming'] > 0)?number_format($value['qty_incoming'],2):'-';
                $outstanding_incoming   = ($value['outstanding_incoming'] > 0)?number_format($value['outstanding_incoming'],2):'-';


                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['no_po']."</td>";
                    echo "<td align='left'>".strtoupper($value['nm_supplier'])."</td>";
                    echo "<td align='left'>".strtoupper($value['nm_material'])."</td>";
                    echo "<td align='center'>".$QTY_PR."</td>";
                    echo "<td align='center'>".$QTY_RFQ."</td>";
                    echo "<td class='text-right text-bold text-primary'>".number_format($value['qty_po'],2)."</td>";
                    echo "<td class='text-right text-bold text-primary'>".number_format($value['outstanding_po'],2)."</td>";
                    echo "<td class='text-right text-bold text-success'>".number_format($value['qty_incoming'],2)."</td>";
                    echo "<td class='text-right text-bold text-success'>".number_format($value['outstanding_incoming'],2)."</td>";
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