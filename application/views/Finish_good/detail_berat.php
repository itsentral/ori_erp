
<div class="box box-primary">
	<div class="box-header">
	
	<div>
	<div class="box-body">
		<div class='form-group row'>
			<div class='col-sm-12'>             
				<table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
					<thead>
						<tr class='bg-blue'>
							<th class="text-center" width='5%'>#</th>
							<th class="text-center">Material Name</th>
							<th class="text-center">Category</th>
							<th class="text-center">Berat</th>
						</tr>
					</thead>
					<tbody>
                        <?php
                        $nomor = 0;
                        $SUM = 0;
                        foreach ($detail as $key => $value) { $nomor++;
                            $SUM += $value['qty_oke'];
                            echo "<tr>";
                                echo "<td class='text-center'>".$nomor."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td class='text-right'>".number_format($value['qty_oke'],2)." kg</td>";
                            echo "</tr>";
                        }
                        ?>
					</tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <th colspan='2'>TOTAL MATERIAL</th>
                            <th class='text-right'><?=number_format($SUM,2);?> kg</th>
                        </tr>
                    </tfoot>
				</table>
			</div>
		</div>	
	<div>
</div>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen_select').chosen({
			width : '100%'
		});
	});
</script>