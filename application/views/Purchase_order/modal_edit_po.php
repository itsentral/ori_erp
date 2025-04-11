
<div class="box-body"> 
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Supplier Name</b></label>
		<div class='col-sm-4'>              
			<select id='id_supplier' name='id_supplier[]' class='form-control input-sm chosen-select' multiple>
				<?php
					foreach($supList AS $val => $valx){
						$sel3 = '';
						if(!empty($supplierx)){
							$sel3 = (isset($supplierx) && in_array($valx['id_supplier'], $supplierx))?'selected':'';
						}
						echo "<option value='".$valx['id_supplier']."' ".$sel3.">".strtoupper($valx['nm_supplier'])."</option>";
					}
				?>
			</select>
		</div>
	</div><br>
	<input type="hidden" name='no_rfq' value='<?=$no_rfq;?>'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center no-sort" width='5%'>#</th>
				<th class="text-center" width='10%'>No PR</th> 
				<th class="text-center" width='10%'>Tanggal PR</th>
				<th class="text-center">Material Name</th>
				<th class="text-center" width='10%'>MOQ</th>
				<th class="text-center" width='10%'>Qty</th>
				<th class="text-center" width='5%'>Unit</th>
				<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
				<th class="text-center no-sort" width='5%'>#</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++; 
				$nm_material = $valx['nm_material'];
				$satuan = 'KG';
				if($valx['category'] == 'acc'){
					$nm_material = get_name_acc($valx['id_material']);
					$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$valx['idmaterial']);
					if(empty($valx['idmaterial'])){
						$nm_material = $valx['nm_material'];
					}
				}
				echo "<tr>";
                    echo "<td align='center'>".$No."</td>";
                    echo "<td align='center'>".$valx['no_pr']."</td>";
					echo "<td align='center'>".date('d-m-Y', strtotime($valx['tgl_pr']))."</td>";
					echo "<td align='left'>".$nm_material."</td>";
					echo "<td align='right'>".number_format($valx['moq'],2)."</td>";
					echo "<td align='right'>".number_format($valx['qty'])."</td>";
					echo "<td align='left'>".strtoupper($satuan)."</td>";
					echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."
							<input type='hidden' name='check[".$No."]' value='".$valx['no_pr']."'>
						  </td>";
					echo "<td align='center'><button type='button' class='btn btn-sm btn-danger delMat' title='Total Material Purchase' data-no_pr='".$valx['no_pr']."' data-no_rfq='".$valx['no_rfq']."' data-id_material='".$valx['id_material']."'>Delete</button></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php
        echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Update','content'=>'Update','id'=>'updatePur')).' ';
    ?>
</div>
<style>
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen-select').chosen();
	});

</script>