<div class="box box-primary">
    <div class="box-body">
        <br>
		<table class="table table-bordered table-striped" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Product</th>
					<th class="text-center" width='20%'>No SPK</th>
					<th class="text-center" width='10%'>QTY</th>
					<th class="text-center" width='10%'>DN1</th>
					<th class="text-center" width='10%'>DN2</th>
				</tr>
			</thead>
			<tbody>
                <?php
                foreach ($get_detail as $key => $value) { $key++;

                    $no_ipp 		= $value['no_ipp'];
                    $tandaTanki = substr($no_ipp,0,4);
                    if($tandaTanki != 'IPPT'){
                        $nm_product = $value['id_category'];
                    }
                    else{
                        $nm_product = $value['id_product'];
                    }
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='left'>".strtoupper($nm_product)."</td>";
                        echo "<td align='center'>".strtoupper($value['no_spk'])."</td>";
                        echo "<td align='center' class='text-bold text-red'>".number_format($value['total_qty'])."</td>";
                        echo "<td align='center'>".number_format($value['diameter_1'])."</td>";
                        echo "<td align='center'>".number_format($value['diameter_2'])."</td>";
                    echo "</tr>";
                }
                ?>
			</tbody>
		</table>
	</div>
</div>

<script>
    swal.close();
</script>