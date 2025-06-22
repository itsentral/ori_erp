<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
            <a href="<?php echo site_url('deadstok') ?>" class="btn btn-sm btn-danger" style='float:right; margin-right:5px;'>Back</a>
        </div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">IPP</th>
						<th class="text-center th">SO</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">Deadstock</th>
						<th class="text-center th">No SPK</th>
						<th class="text-center th">Spec Product</th>
						<th class="text-center th">Qty Booking</th>
						<th class="text-center th">Booking By</th>
						<th class="text-center th">Booking Date</th>
						<th class="text-center th">Option</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    foreach ($result as $key => $value) { $key++;
						$TandaTanki = substr($value['no_ipp'],0,4);
						$SOTanki 	= substr($value['product_code'],0,9);

						$product 	= ($TandaTanki=='IPPT')?$value['id_product']:$value['product'];
						$so_number 	= ($TandaTanki=='IPPT')?$SOTanki:$value['so_number'];
						$spec 		= ($TandaTanki=='IPPT')?'':spec_bq2($value['id_milik']);

						$id_deadstok = $value['id_product_deadstok'];

                		$getDeadstok = $this->db->get_where('deadstok',array('id_product'=>$id_deadstok))->result_array();
						$product_deadstock = (!empty($getDeadstok[0]['product_name']))?$getDeadstok[0]['product_name']:'';

                        echo "<tr>";
                            echo "<td align='center'>".$key."</td>";
                            echo "<td align='left'>".$value['no_ipp']."</td>";
                            echo "<td align='center'>".$so_number."</td>";
                            echo "<td align='left'>".$product."</td>";
                            echo "<td align='left'>".$product_deadstock."</td>";
                            echo "<td align='left'>".$value['no_spk']."</td>";
                            echo "<td align='left'>".$spec."</td>";
                            echo "<td align='center'>".number_format($value['qty_booking'])."</td>";
                            echo "<td align='left'>".$value['booking_by']."</td>";
                            echo "<td align='center'>".date('d-M-Y H:i',strtotime($value['booking_date']))."</td>";
                            echo "<td align='center'>";
                            echo "<a href='".site_url('produksi/print_booking_deadstok/').$value['booking_code']."' target='_blank'>Print</a>";
                            echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
			</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#my-grid').DataTable()
	});
</script>
