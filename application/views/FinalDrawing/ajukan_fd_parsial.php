
<div class="box-body"> 
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<b>(NEW)</b> Melakukan check list untuk diajukan, kemudian klik tombol Save<br>
			<button type='button' class='btn btn-sm' style='color:white; background-color: #e09b12; margin-top:5px;'><i class='fa fa-frown-o'></i></button> Belum diajukan approve / menunggu pengajuan<br>
			<button type='button' class='btn btn-sm' style='color:white; background-color: #12b7e0; margin-top:5px;'><i class='fa fa-hourglass-start'></i></button> Menunggu persetujuan / sudah diajukan menunggu approval<br>
			<button type='button' class='btn btn-sm' style='color:white; background-color: #0bd652; margin-top:5px;'><i class='fa fa-check'></i></button> Sudah disetujui / sudah sampai produksi<br>
			<button type='button' class='btn btn-sm btn-info' style='color:white; margin-top:5px;'><i class='fa fa-check'></i></button> Untuk melakukan pengajuan<br>
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU BERAT SATUAN,<span> <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</b></span>
			<br><span style='color:red;'><b><u>PRODUCT KOSONG WAJIB DIAJUKAN !!!</u></b></span>
		</p>
	</div>
	<input type='hidden' name='id_bq' value='<?= $id_bq;?>'>
	<input type='hidden' name='pembeda' id='pembeda' value='<?= $this->uri->segment(4);?>'>
	<input type='hidden' name='no_ipp' value='<?= str_replace('BQ-','',$id_bq);?>'>  
	<!--
	<span style='color:green;'><b>* Tombol Edit berwarna <span style='color:red;'>Merah</span> dalam process Development, <span style='color:red;'><u>MOHON JANGAN DIGUNAKAN.</u></span> Kolom #</b></span>
	<br>
	<br><br>
	-->
	
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th style='background:none;' width='3%' class='no-sort'style='vertical-align:middle;'><font size='2'><B><center>
				<?php
				if($number != 0){
					echo "<input type='checkbox' name='chk_all' id='chk_all'></center></B></font>";
				}
				?>
				</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Series</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Spec</th>
				<th class="text-center" style='vertical-align:middle;'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Weight /Unit (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Weight (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Qty SO</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Qty Sisa</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Qty Release</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Status</th>
				<th class="text-center" style='vertical-align:middle;' width='3%'>Cut</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>#</th>
				
			</tr>
		</thead>
		<tbody id='detail_body'>
			<?php
				$no=0;
				foreach($qBQdetailRest AS $val => $valx){
					$no++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$nm_cty	= ucwords(strtolower($valx['id_category'])); 
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					$SumQty	= $valx['sum_mat'] * $valx['qty'];

					$get_qty = $this->db->group_by('id_bq_header')->select('COUNT(id) AS sisa')->get_where('so_detail_detail', array('id_bq_header'=>$valx['id_bq_header'],'approve'=>'N'))->result();
					$sisa_qty = (!empty($get_qty))?$get_qty[0]->sisa:0;
					if($valx['approve'] == 'P'){ 
						$sisa_qty = 0;
					}
					echo "<tr id='tr_".$no."'>"; 
						echo "<td><center>";
						if(!empty($valx['id_product'])){
							if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
								if($sisa_qty > 0){
									echo "<input type='checkbox' name='check[$no]' class='chk_personal' data-id_milik='".$valx['id']."' data-nomor='".$no."' value='".$valx['id']."-".$valx['id_milik_bq']."'>";
								}
							}
						}
						echo "</center></td>";
						echo "<td align='center'>".$spaces."".$valx['no_komponen']."</td>";
						echo "<td align='center'>".$spaces."".$valx['series']."</td>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='left'>".spec_fd($valx['id'],'so_detail_header')."</td>";
						
						$product = (!empty($valx['id_product']))?$valx['id_product']:'Not-Found';
						echo "<td align='left'>".$product."</td>";
						echo "<td align='right'>".number_format($valx['sum_mat'], 3)."
						<input type='hidden' id='berat_".$valx['id']."' name='berat_".$valx['id']."' value='".$valx['sum_mat']."'> 
						<input type='hidden' id='product_".$valx['id']."' value='".$valx['id_category']."'> 
						</span></td>";
						echo "<td align='right'>".number_format($SumQty, 3)."</span></td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='center'><span class='badge bg-red'>".$sisa_qty."</span></td>";
						
						if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
							if($sisa_qty > 0){
								$caption = 'Sebagian belum diajukan';
								$tanda = 'fa-frown-o';
								$color = '#e09b12';
								$input_release = "<input type='text' id='qtyrelease_".$valx['id']."' name='qtyrelease_".$valx['id']."' class='form-control input-sm text-center autoNumeric0' title='Qty Release' value='".$sisa_qty."'>";
							}
						}
						if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
							if($sisa_qty < 1){
								$caption = 'Semua sudah diajukan';
								$tanda = 'fa-hourglass-start';
								$color = '#12b7e0';
								$input_release = "";
							}
						}
						if($valx['approve'] == 'P'){ 
							$caption = 'Sudah Disetujui';
							$tanda = 'fa-check';
							$color = '#0bd652';
							$input_release = "";
						}
						echo "<td align='center'>".$input_release."</td>";
						echo "<td align='center'><button type='button' class='btn btn-sm' style='color:white; background-color: ".$color."' title='".$caption."'><i class='fa ".$tanda."'></i></button></td>";
						$colorX = "";
						if(!empty($valx['id_product'])){
							if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
								if($sisa_qty > 0){
									if($valx['id_category'] == 'pipe'){
										$colorX = "style='background-color:transparant;'";
									}
								}
							}
						}
						echo "<td ".$colorX." class='text-center'>";
						if(!empty($valx['id_product'])){
								if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
									echo "<i class='fa fa-check text-green'></i>";
									if($sisa_qty > 0){
										if($valx['id_category'] == 'pipe'){
											// echo "<input type='checkbox' name='cut_".$valx['id']."' class='chk_personal_cutting' data-nomor='".$no."' value='".$valx['id']."-".$valx['id_milik_bq']."' title='Proccess Cutting ?'>";
											echo "<select name='cut_".$valx['id']."'  id='cut_".$valx['id']."' class='form-control chosen-select ubah_cut' style='background-color:red;'>";
											echo "<option value='0'>Pilih</option>";
											echo "<option value='N'>Tidak</option>";
											echo "<option value='Y'>Ya</option>";
											echo "</select>";
										}
									}
									else{
										if($valx['cutting'] == 'Y'){
											echo "<i class='fa fa-check text-green'></i>";
										}
									}
								}
								else{
									if($valx['cutting'] == 'Y'){
										echo "<i class='fa fa-check text-green'></i>";
									}
								}
							}
						echo "</td>";
						echo "<td align='left'>";
							if(!empty($valx['id_product'])){
								echo "<button type='button' class='btn btn-sm btn-primary detail_comp' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
								
								if($valx['approve'] == 'N' OR $valx['approve'] == 'Y'){
									if($sisa_qty > 0){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-info ajukanSat' title='Ajukan Component' data-nomor='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."' data-panjang=".floatval($valx['length'])."><i class='fa fa-check'></i></button>";
									}
								}
							}
							
							
						echo "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
	<br>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Ajukan Semua",'id'=>'ajukanEst')).' ';
	
	if(!empty($detail2)){
		?>
		<br><br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th style='background:none;' width='4%' class='no-sort text-center'>
					<?php
					if($number2 != 0){
						echo "<input type='checkbox' name='chk_all2' id='chk_all2'></center></B></font>";
					}
					?></th>
					<th class="text-center" style='vertical-align:middle;' colspan='7'>Material Name</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Weight</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Status</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>#</th>
					
				</tr>
			</thead>
			<tbody id='detail_body'>
				<?php
					$no=0;
					foreach($detail2 AS $val => $valx){
						$no++;
						echo "<tr id='tr2_".$no."'>"; 
							echo "<td align='right' style='vertical-align:middle;'><center>";
							if($valx['approve'] == 'N'){
								echo "<input type='checkbox' name='check[$no]' class='chk_personal2' data-nomor='".$no."' value='".$valx['id']."'>";
							}
							echo "</center></td>";
							echo "<td align='left' colspan='7'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']))."</td>";
							echo "<td align='right' style='padding-right:20px;'>".number_format($valx['qty'], 2)." Kg</span></td>";
							if($valx['approve'] == 'N'){
								$caption = 'Belum diajukan';
								$tanda = 'fa-frown-o';
								$color = '#e09b12';
							}
							if($valx['approve'] == 'Y'){
								$caption = 'Sudah diajukan';
								$tanda = 'fa-hourglass-start';
								$color = '#12b7e0';
							}
							if($valx['approve'] == 'P'){ 
								$caption = 'Sudah Disetujui';
								$tanda = 'fa-check';
								$color = '#0bd652';
							}
							echo "<td align='center'><button type='button' class='btn btn-sm' style='color:white; background-color: ".$color."' title='".$caption."'><i class='fa ".$tanda."'></i></button></td>";
							echo "<td align='left'>";
								if($valx['approve'] == 'N'){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-info ajukanSatMat' title='Ajukan Component' data-nomor='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-check'></i></button>";
								}
							echo "</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Ajukan Semua",'id'=>'ajukan_mat')).' ';
		
	}
	
	if(!empty($detail3)){
		?>
		<br><br>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th style='background:none;' width='4%' class='no-sort text-center'>
					<?php
					if($number3 != 0){
						echo "<input type='checkbox' name='chk_all3' id='chk_all3'></center></B></font>";
					}
					?></th>
					<th class="text-center" style='vertical-align:middle;' colspan='7'>Material Name</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>Status</th>
					<th class="text-center" style='vertical-align:middle;' width='6%'>#</th>
					
				</tr>
			</thead>
			<tbody id='detail_body'>
				<?php
					$no=0;
					foreach($detail3 AS $val => $valx){
						$no++;
						echo "<tr id='tr2_".$no."'>"; 
							echo "<td align='right' style='vertical-align:middle;'><center>";
							if($valx['approve'] == 'N'){
								echo "<input type='checkbox' name='check2[$no]' class='chk_personal3' data-nomor='".$no."' value='".$valx['id']."'>";
							}
							echo "</center></td>";
							echo "<td align='left' colspan='7'>".strtoupper(get_name_acc($valx['id_material']))."</td>";
							echo "<td align='center' style='padding-right:20px;'>".number_format($valx['qty'])."</span></td>";
							if($valx['approve'] == 'N'){
								$caption = 'Belum diajukan';
								$tanda = 'fa-frown-o';
								$color = '#e09b12';
							}
							if($valx['approve'] == 'Y'){
								$caption = 'Sudah diajukan';
								$tanda = 'fa-hourglass-start';
								$color = '#12b7e0';
							}
							if($valx['approve'] == 'P'){ 
								$caption = 'Sudah Disetujui';
								$tanda = 'fa-check';
								$color = '#0bd652';
							}
							echo "<td align='center'><button type='button' class='btn btn-sm' style='color:white; background-color: ".$color."' title='".$caption."'><i class='fa ".$tanda."'></i></button></td>";
							echo "<td align='left'>";
								if($valx['approve'] == 'N'){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-info ajukanSatMat' title='Ajukan Component' data-nomor='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-check'></i></button>";
								}
							echo "</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; margin-right:10px; float:right;','value'=>"save",'content'=>"Ajukan Semua",'id'=>'ajukan_mat2')).' ';
		
	}
	?>
</div>
<style type="text/css">
	.modal-dialog{
		overflow: auto !important;
	}
	
	label{
		    font-size: small !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false});
		$(".chosen-select").chosen();
		
		$("#chk_all").click(function(){
			$('.chk_personal').not(this).prop('checked', this.checked);
		});

		$("#chk_all2").click(function(){
			$('.chk_personal2').not(this).prop('checked', this.checked);
		});
		
		$("#chk_all3").click(function(){
			$('.chk_personal3').not(this).prop('checked', this.checked);
		});

		$(".ubah_cut").change(function(){
			let nilai = $(this).val();
			if(nilai == 'Y'){
				$(this).parent().css({'background-color': '#0ffa00'});
			}
			if(nilai == 'N'){
				$(this).parent().css({'background-color': 'red'});
			}
			if(nilai == '0'){
				$(this).parent().css({'background-color': 'white'});
			}
		});
	});
</script>