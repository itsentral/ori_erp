 <div class="box box-primary">
    <div class="box-body">
		<form id="data-form" method="post">
		<div class="form-group row table-responsive" >
			 <table class="table table-bordered" width="100%" id="list_item_stok">
              <thead>
                  <tr>
				      <th width="30%">Tgl</th>
				      <th width="30%">No IPP</th>
				      <th width="30%">No Invoice</th>
                      <th width="30%">Mata Uang</th>                         
                      <th width="30%">Nama Customer</th>
					   <th width="30%">Total Invoice USD</th>
                      <th width="30%">Total Invoice</th>
					   <th width="30%">Sisa Invoice IDR</th>
					    <th width="30%">Sisa Invoice USD</th>
						  <th width="30%">Sisa Retensi IDR</th>
					    <th width="30%">Sisa Retensi USD</th>
                      <th width="2%" class="text-center">Aksi</th>  
                  </tr>
              </thead>
              <tbody>
                  <?php	
				 
				  $cust = $results;
				  // $inv = $this->db->query("SELECT a.*, b.nm_customer as nm_customer FROM tr_invoice_header a
				                      // INNER JOIN customer b ON a.id_customer=b.id_customer WHERE a.id_customer ='$cust' AND (a.sisa_invoice_idr >='1' OR a.sisa_invoice >'0') ")->result();
				  
				  // foreach($inv as $iv=>$in){
				  
						  // if($in->base_cur=='IDR') {
						  
						  // $invoice = $this->db->query("SELECT a.*, b.nm_customer as nm_customer FROM tr_invoice_header a
											  // INNER JOIN customer b ON a.id_customer=b.id_customer WHERE a.id_customer ='$cust' AND (a.sisa_invoice_idr >='1' AND a.sisa_invoice >'0') ")->result();
						  // } else {
						  $invoice = $this->db->query("SELECT a.*, b.nm_customer as nm_customer FROM tr_invoice_header a
											  INNER JOIN customer b ON a.id_customer=b.id_customer WHERE a.id_customer ='$cust' AND ((a.base_cur='IDR' AND a.sisa_invoice_idr >='1')  OR (a.base_cur='USD' AND a.sisa_invoice >'0') OR (a.base_cur='USD' AND a.sisa_invoice_retensi2 >'0') OR (a.base_cur='IDR' AND a.sisa_invoice_retensi2_idr >='0')) ")->result();
						  //}
				  
				  //}

				 if($invoice){
					foreach($invoice as $ks=>$vs){
                  ?>
						  <tr>
						      <td><?php echo $vs->tgl_invoice ?></td>
							  <td><?php echo $vs->no_ipp ?></td>
							  <td><?php echo $vs->no_invoice ?></td>
							   <td><?php echo $vs->base_cur ?></td>
							  <td><center><?php echo $vs->nm_customer ?></center></td>
							    <td><center><?php echo number_format($vs->total_invoice,2) ?></center></td>
							  <td><center><?php echo number_format($vs->total_invoice_idr) ?></center></td>
							  <td><center><?php echo number_format($vs->sisa_invoice_idr) ?></center></td> 
							  <td><center><?php echo number_format($vs->sisa_invoice,2) ?></center></td>
							  <td><center><?php echo number_format($vs->sisa_invoice_retensi2_idr) ?></center></td> 
							  <td><center><?php echo number_format($vs->sisa_invoice_retensi2,2) ?></center></td>
							  <td>
							  <?php 
							  if($vs->proses_print == 0){
								  echo 'Belum cetak invoice';
							  }else{
							  ?>
								<center>
									<button id="btn-<?php echo $vs->id_invoice?>" class="btn btn-warning btn-sm" type="button" onclick="startmutasi('<?php echo $vs->id_invoice?>', '<?php echo $vs->no_invoice?>', '<?php echo $vs->no_ipp?>','<?php echo addslashes($vs->nm_customer) ?>','<?php echo $vs->total_invoice_idr?>','<?php echo $vs->sisa_invoice_idr?>','<?php echo $vs->sisa_invoice_retensi2_idr?>','<?php echo $vs->kurs_jual?>')">
										Pilih
									</button>
								</center>
							  <?php 
							  }
							  ?>
							  </td>
						  </tr>
                  <?php 
						}
					  }				  
				  ?>
              </tbody>
          </table>
		</div>
			</div>
				 </div>
			</div>
		</form>		  
	</div>
</div>	
	
				  
				  

	
	
	
</script>