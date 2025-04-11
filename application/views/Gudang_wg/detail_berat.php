
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
							<th class="text-center">ID Product</th>
							<th class="text-center">Material Name</th>
							<th class="text-center">Category</th>
							<th class="text-center">Berat</th>
						</tr>
					</thead>
					<tbody>
                        <?php
                        $nomor = 0;
                        $SUM = 0;
                        foreach ($ArrMerge as $key => $value) { $nomor++;
							$QTY_PRO = 1;
							if(!empty($qty_product)){
								$QTY_PRO = $qty_product;
							}
                            $berat = $value * $QTY_PRO;
                            if($status == 'cutting'){
                                $berat = ($value) / $length_awal * $length * $QTY_PRO;
                            }
                            $SUM += $berat;

                            $get_material = $this->db->get_where('raw_materials', array('id_material'=> $key))->result();
                            echo "<tr>";
                                echo "<td class='text-center'>".$nomor."</td>";
                                echo "<td>".$get_material[0]->idmaterial."</td>";
                                echo "<td>".$get_material[0]->nm_material."</td>";
                                echo "<td>".$get_material[0]->nm_category."</td>";
                                echo "<td class='text-right'>".number_format($berat,4)."</td>";
                            echo "</tr>";
                        }
                        ?>
					</tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <th colspan='3'>TOTAL MATERIAL</th>
                            <th class='text-right'><?=number_format($SUM,4);?></th>
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
		$('.autoNumeric').autoNumeric();
		$('.datetimepicker').datetimepicker();
	});
</script>