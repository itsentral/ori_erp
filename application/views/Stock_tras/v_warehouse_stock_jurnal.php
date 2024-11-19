<style>
	.table-bordered tbody tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.table-bordered thead tr th,thead tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.table-bordered tfoot tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.text-up{
		text-transform: uppercase;
	}
</style>
<div class="box box-warning box-solid">
	<div class="box-header">
		<h5 class="box-title text-center"> <?= $title;?></h5>
	</div>
	
	<?php
	if(empty($rows_header)){
		echo"<div class='box-body'>
			<div class='row'>";
				echo"<div class='col-sm-12'>";
					echo"<h4 class='text-red'><b>NO RECORD WAS FOUND.....</b></h4>";
				echo"</div>";
			echo"</div>
		</div>";
	}else{
	
	
	?>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>DETAIL JURNAL</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Nomor Jurnal</b></label>
					<?php
						echo form_input(array('id'=>'nomor_jurnal','name'=>'nomor_jurnal','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->nomor));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Tanggal</b></label>
					<?php
						echo form_input(array('id'=>'tgl_jurnal','name'=>'tgl_jurnal','class'=>'form-control input-sm','readOnly'=>true),date('d-m-Y',strtotime($rows_header->tgl)));
						
					?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label">
						<b>
						<?php
						$Label_Field	= 'Periode';
						$Val_Field		= '-';
						if(strtolower($tipe_jurnal) == 'jv'){
							$Label_Field	= 'Periode';
							$Val_Field		= $arr_month[$rows_header->bulan].' '.$rows_header->tahun;
						}else if(strtolower($tipe_jurnal) == 'bum'){
							$Label_Field	= 'Terima Dari';
							$Val_Field		= $rows_header->terima_dari;
						}else if(strtolower($tipe_jurnal) == 'buk'){
							$Label_Field	= 'Bayar Kepada';
							$Val_Field		= $rows_header->bayar_kepada;
						}
						echo $Label_Field;
						?>
						</b>
					</label>
					<?php
						echo form_input(array('id'=>'var_label','name'=>'var_label','class'=>'form-control input-sm','readOnly'=>true),strtoupper($Val_Field));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Total</b></label>
					<?php
						echo form_input(array('id'=>'total_trans','name'=>'total_trans','class'=>'form-control input-sm','readOnly'=>true),number_format($rows_header->jml));
						
					?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label">
						<b>
						<?php
						$Label_Koreksi	= 'Koreksi No';
						$Val_Koreksi		= '-';
						$Val_Notes			= '-';
						if(strtolower($tipe_jurnal) == 'jv'){
							$Val_Koreksi	= $rows_header->koreksi_no;
							$Val_Notes		= $rows_header->keterangan;
						}else if(strtolower($tipe_jurnal) == 'bum'){
							$Val_Notes		= $rows_header->note;
						}else if(strtolower($tipe_jurnal) == 'buk'){
							$Val_Notes		= $rows_header->note;
						}
						echo $Label_Koreksi;
						?>
						</b>
					</label>
					<?php
						echo form_input(array('id'=>'koreksi_no','name'=>'koreksi_no','class'=>'form-control input-sm','readOnly'=>true),strtoupper($Val_Koreksi));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Keterangan</b></label>
					<?php
						echo form_textarea(array('id'=>'notes','name'=>'notes','rows'=>2,'cols'=>100,'class'=>'form-control input-sm','readOnly'=>true),strtoupper($Val_Notes));
						
					?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>DETAIL ITEM JURNAL</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		
		<div class="table-responsive">
			<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
				<thead>
					<tr class="bg-navy-active text-white">
						<th class="text-center">No.</th>
						<th class="text-center">COA No.</th>
						<th class="text-center">Description</th>
						<th class="text-center">No. Reff</th>
						<th class="text-center">Debit</th>
						<th class="text-center">Kredit</th>
						<th class="text-center">Debit USD</th>
						<th class="text-center">Kredit USD</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($rows_detail){
							$totDebet = $totKredit = $no = $totDebetUSD = $totKreditUSD =0;
							foreach($rows_detail as $row){
								$no++;
								$no_perkiraan 	= $row['no_perkiraan'];
								$keterangan 	= $row['keterangan'];
								$debet 			= $row['debet'];
								$kredit 		= $row['kredit'];
								$no_reff 		= $row['no_reff'];
								$debetUSD 		= $row['nilai_valas_debet'];
								$kreditUSD 		= $row['nilai_valas_kredit'];
								$Query_COA		= "SELECT nama FROM COA WHERE no_perkiraan = '".$no_perkiraan."' ORDER BY id DESC LIMIT 1";
								$rows_COA		= $this->accounting->query($Query_COA)->row();
								if($rows_COA){
									$no_perkiraan	.='<br><span class="text-red">'.$rows_COA->nama.'</span>';
								}
								
								echo '<tr>
										<td class="text-center">'.$no.'</td>
										<td class="text-center">'.$no_perkiraan.'</td>
										<td class="text-left">'.$keterangan.'</td>
										<td class="text-center">'.$no_reff.'</td>
										<td class="text-right">'.number_format($debet).'</td>
										<td class="text-right">'.number_format($kredit).'</td>
										<td class="text-right">'.number_format($debetUSD).'</td>
										<td class="text-right">'.number_format($kreditUSD).'</td>
									</tr>';
								
								$totDebet += $debet;
								$totKredit += $kredit;
								$totDebetUSD += $debetUSD;
								$totKreditUSD += $kreditUSD;
							}
							echo '<tr class="text-bold bg-gray">
									<td class="text-center" colspan="4"> TOTAL</td>
									<td class="text-right">'.number_format($totDebet).'</td>
									<td class="text-right">'.number_format($totKredit).'</td>
									<td class="text-right">'.number_format($totDebetUSD).'</td>
									<td class="text-right">'.number_format($totKreditUSD).'</td>
								</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	}
	?>
	
</div>

