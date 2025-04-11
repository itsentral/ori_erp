<?php
	$sql 	= "SELECT a.* FROM tran_po_header a WHERE a.no_po='".$no_po."'";
	$datapo	= $this->db->query($sql)->row();
	$kurs=1;
	if($datapo->total_bayar_rupiah >0 || $datapo->nilai_dp >0) die('<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Perhatian!</h4>'.$no_po.' sudah ada pembayaran</div><script>swal.close();</script>');
	if($datapo->mata_uang!='IDR'){
		$dt_kurs=$this->db->query("SELECT * FROM ms_kurs where mata_uang='".$datapo->mata_uang."' order by tanggal desc limit 1")->row();
		if(!empty($dt_kurs)) $kurs=$dt_kurs->kurs;
	}
	?>
<form action="#" method="POST" id="form_adjustment" autocomplete='off'> 
<div class="box-body"> 
	<div class="row">
		<div class="col-md-4">No PO : <?= $no_po;?><br />Supplier : <?=$datapo->nm_supplier?></div>
		<div class="col-md-6">Keterangan : <input type="text" name='note' id='note' size=25></div>
	</div><br/>
    <input type="hidden" name='id_supplier' id='id_supplier' value='<?= $datapo->id_supplier;?>'>
    <input type="hidden" name='nm_supplier' id='nm_supplier' value='<?= $datapo->nm_supplier;?>'>
    <input type="hidden" name='kurs' id='kurs' value='<?= $kurs;?>'>
    <input type="hidden" name='no_po' id='no_po' value='<?= $no_po;?>'>
    <input type="hidden" name='mata_uang' id='mata_uang' value='<?= $datapo->mata_uang;?>'>
    <input type="hidden" name='adjustment' id='adjustment' value='OUT'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
                <th class="text-center" style='vertical-align:middle;'>Material ID</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>UoM Order</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Incoming</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty R e t u r</th> 
				<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total1 = 0;
			$Total2 = 0;
            $No=0; $gudang='';
			$gd_consumable=array('1','6','7','8');
			$gd_household=array('2','10');
			foreach($result AS $val => $valx){
                $No++;
                $Total1 += $valx['qty_in'];
				$Total2 += $valx['qty_purchase'];
				echo "<tr>";
                    echo "<td align='center'>".$No."
                        <input type='hidden' name='addOutMat[$No][id]' value='".$valx['id']."'>
                        <input type='hidden' name='addOutMat[$No][qty_order]' value='".$valx['qty_purchase']."'>
                        <input type='hidden' name='addOutMat[$No][qty_in]' value='".$valx['qty_in']."'>
						<input type='hidden' name='addOutMat[$No][harga]' value='".$valx['net_price']."'>
						<input type='hidden' name='addOutMat[$No][id_material]' value='".$valx['id_barang']."'>
                    </td>";
                    echo "<td>".$valx['id_barang']."</td>";
                    echo "<td>".$valx['nm_barang']."</td>";
					echo "<td align='right'>".number_format($valx['qty_purchase'],4)."</td>";
					echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
                    echo "<td align='right' class='Diterima'>".number_format($valx['qty_in'],4)."</td>";
                    echo "<td align='center'><input type='text' name='addOutMat[$No][qty_out]' data-no='$No' data-qtymax='".$valx['qty_in']."' class='form-control input-sm text-right divide qtyOut' value='".$valx['qty_in']."'></td>";
                    echo "<td align='center'><input type='text' name='addOutMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left'>".$valx['category_awal']."</td>";
                echo "</tr>";
				if($gudang=""){
					if(in_array($valx['category_awal'],$gd_consumable)) {
						$dt_gudang=$this->db->query("SELECT * FROM warehouse WHERE category='indirect' limit 1")->row();
						if(!empty($dt_gudang)){
							$gudang=$dt_gudang->id;
						}
					}else{
						if(in_array($valx['category_awal'],$gd_household)) {
							$dt_gudang=$this->db->query("SELECT * FROM warehouse WHERE category='consumable' limit 1")->row();
							if(!empty($dt_gudang)){
								$gudang=$dt_gudang->id;
							}
						}
					};
				}
			}
			?>
			<tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2, 4);?></b></td> 
				<td><b></b></td>
				<td align='right'><b><?= number_format($Total1, 4);?></b></td> 
                <td colspan='2'><b></b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='8'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
	if($gudang!=''){
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'saveINMaterial')).' ';
	}else{
		echo '<h1>Gudang tidak ditemukan</h1>';
	}
	?>
</div>
<input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
</form>
<script>
	$(document).ready(function(){
        swal.close();
		$('.divide').divide();
    });
	$(document).on('keyup','.qtyOut',function(){
		var qtymax=$(this).data("qtymax");
		var qtyout=$(this).val();
		if(parseFloat(qtyout) > parseFloat(qtymax)){
			$(this).val(qtymax)
		}
	});

	$(document).on('click', '#saveINMaterial', function(){
		swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_retur_stok',
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false,
					contentType	: false,
					success		: function(data){
						if(data.status == 1){
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/stok';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',
							type				: "warning",
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

</script>