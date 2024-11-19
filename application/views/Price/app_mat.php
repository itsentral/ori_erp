<?php
$this->load->view('include/side_menu');
?>   
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<!-- /.box-header -->
	<input type='hidden' id='url_help' name='url_help' value='<?= $this->uri->segment(3); ?>'>
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Product ID</th>
					<th class="text-center">Product Name</th>
					<th class="text-center">Standard By</th>
					<th class="text-center">Application</th>
					<th class="text-center">By</th>
					<th class="text-center">Rev</th>
					<th class="text-center">Status Price</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						$int++;
						$detail = "";
						if(strtolower($datas->parent_product) == 'pipe'){
							$detail = "(".$datas->diameter." x ".$datas->panjang." x ".$datas->design.")";
						}
						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='left'>".$datas->id_product."</td>";
							echo"<td align='left'>".$datas->nm_product." ".$detail."</td>";
							
							echo"<td align='left'>".strtoupper($datas->standart_toleransi)."</td>";
							echo"<td align='left'>".$datas->aplikasi_product."</td>";
							echo"<td align='left'>".ucfirst(strtolower($datas->created_by))."</td>";
							echo"<td align='center'><span class='badge bg-blue'>".$datas->rev."</span></td>";
							if($datas->sts_price == 'REGISTERED'){
								$warna = 'bg-green';
							}
							elseif($datas->sts_price == 'UNREGISTERED'){
								$warna = 'bg-red';
							}
							else{
								$warna = 'bg-blue';
							}
							echo"<td align='center'><span class='badge ".$warna."'>".$datas->sts_price."</span></td>";
							echo"<td align='center'>";
								echo"<button type='button' id='MatDetail' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";									
								if($akses_menu['update']=='1'){
									echo "&nbsp;<button type='button' id='MatPrice' data-id_product='".$datas->id_product."' data-nm_product='".$datas->nm_product."' class='btn btn-sm btn-primary' title='Approve Now' data-role='qtip'><i class='fa fa-check'></i></button>";										
								}
							echo"</td>";
						echo"</tr>";
					}
			  }
			  ?> 
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$('#printSPK').click(function(e){
			e.preventDefault();
			var id_product	= $(this).data('id_product');
			
			var Links		= base_url +'index.php/'+ active_controller+'/printSPK/'+id_product;
			window.open(Links,'_blank');
		});
		
		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			$("#head_title").html("<b>DETAIL ESTIMATION</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#MatPrice', function(e){
			e.preventDefault();
			$("#head_title").html("<b>APPROVE ESTIMATION</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalAppMat/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
	});
	
</script>
