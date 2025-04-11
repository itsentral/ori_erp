<?php
$Arr_Coa		= array();
if ($data_perkiraan) {
	foreach ($data_perkiraan as $key => $vals) {
		$kode_Coa			= $vals->no_perkiraan . '^' . $vals->nama;
		$Arr_Coa[$kode_Coa]	= $vals->no_perkiraan . '  ' . $vals->nama;
	}
}
?>
<form id="form-detail-jurnal" method="post">

   <input type="hidden" id="jenis" name="jenis" value="<?= $jenis ?>" class="form-control"  readonly />
   <input type="hidden" id="akses" name="akses" value="<?= $akses ?>" class="form-control"  readonly />
   <input type="hidden" id="jenis_jurnal" name="jenis_jurnal" value="<?= $jenis_jurnal ?>" class="form-control"  readonly />
   <input type="hidden" id="po_no" name="po_no" value="<?= $po_no ?>" class="form-control"  readonly />
   <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $id_vendor ?>" class="form-control"  readonly />
   <input type="hidden" id="vendor_nm" name="vendor_nm" value="<?= $nama_vendor ?>" class="form-control"  readonly />

	<div class="row">
		<div class="col-xs-12">
			<div class="box">

				<div class="box-body bodimodal">
					<div class="box-body table-responsive no-padding">
						<table class="table table-bordered table-hover">
							<thead>
								<tr bgcolor='#9acfea'>
									<th>
										<center>Tanggal</center>
									</th>
									<th>
										<center>Tipe</center>
									</th>
									<th>
										<center>No. COA</center>
									</th>
									<th colspan="2">
										<center>Keterangan</center>
									</th>
									<th>
										<center>No. Reff</center>
									</th>
									<th>
										<center>Debit</center>
									</th>
									<th>
										<center>Kredit</center>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 0;
								$sum_debet = 0;
								$sum_kredit = 0;
								if ($list_data > 0) {
									$no = 0;
									foreach ($list_data as $row) {
										$no++;

										$debet  = $row->debet;
										$kredit = $row->kredit;
										$sum_debet	+= $row->debet;
										$sum_kredit	+= $row->kredit;

										$format_debet = number_format($row->debet, 0, ',', '.');
										$format_kredit = number_format($row->kredit, 0, ',', '.');

										$format_sumdebet = number_format($sum_debet, 0, ',', '.');
										$format_sumkredit = number_format($sum_kredit, 0, ',', '.');

									if($debet ==0 AND $kredit ==0){
								?>

								<?php
										}
										else
										{ ?>

										<tr bgcolor='#DCDCDC'>
											<td><input type="text" id="tgl_jurnal'.$no.'" name="tgl_jurnal[]" value="<?=$row->tanggal?>" class="form-control" readonly /></td>
											<td><input type="text" id="type'.$no.'" name="type[]" value="<?= $row->tipe ?>" class="form-control" readonly /></td>
											<td>
											<select id="no_coa'.$no.'" name="no_coa[]" class="form-control input-sm" readonly style="width: 100%;" readonly='readonly'>
											<option value="">Select</option>
												<?php
												$No_Coa=$row->no_perkiraan;
												foreach ($Arr_Coa as $key => $row2) {
													$coa_pisah	= explode('^', $key);
													$nokir		= $coa_pisah[0];

													if ($nokir == $No_Coa) {
														echo "<option value='" . $key . "' selected>" . $row2 . "</option>";
													} else {
														echo "<option value='" . $key . "'>" . $row2 . "</option>";
													}
												}
												?>
											</select>
											</td>
											<td colspan="2"><textarea class="form-control" id="keterangan'.$no.'" name="keterangan[]" placeholder="Keterangan" readonly ><?= $row->keterangan ?></textarea></td>
											<td><input type="text" id="reff'.$no.'" name="reff[]" value="<?= $row->no_reff ?>" class="form-control" readonly /></td>
											<td><input type="hidden" id="debet'.$no.'" name="debet[]" value="<?= $row->debet ?>" class="form-control" readonly />
											<input type="text" id="debet2'.$no.'" name="debet2[]" value="<?= $format_debet ?>" class="form-control" readonly /></td>
											<td><input type="hidden" id="kredit'.$no.'" name="kredit[]" value="<?= $row->kredit ?>" class="form-control" readonly />
											<input type="text" id="kredit2'.$no.'" name="kredit2[]" value="<?= $format_kredit ?>" class="form-control" readonly /></td>
											<input type="hidden" id="jenisjurnal'.$no.'" name="jenisjurnal[]" value="<?= $row->jenis_jurnal ?>" class="form-control" readonly /></td>
											<input type="hidden" id="no_request'.$no.'" name="no_request[]" value="<?= $row->no_request ?>" class="form-control" readonly /></td>



										</tr>

								<?php		}
									}
								} else {
									$format_sumdebet = 0;
									$format_sumkredit = 0;
								}
								?>
								<tr bgcolor='#DCDCDC'>
									<td colspan="6" align="right"><b>TOTAL</b></td>
									<td align="right" ><input type="hidden" id="total" name="total" value="<?= $sum_debet ?>" class="form-control"  readonly />
									<input type="text" id="total3'.$no.'" name="total3" value="<?= $format_sumdebet  ?>" class="form-control"  readonly /></td>
									<td align="right" ><input type="hidden" id="total2" name="total2" value="<?= $sum_kredit ?>" class="form-control" readonly />
									<input type="text" id="total4'.$no.'" name="total4" value="<?= $format_sumkredit ?>" class="form-control" readonly /></td>
								</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
   <input type="hidden" id="total_po" name="total_po" value="<?= $sum_debet ?>" class="form-control"  readonly />
</form>
<style>
.bodimodal {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}
</style>
