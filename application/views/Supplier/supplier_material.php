<?php
$this->load->view('include/side_menu'); 
// $tgl1 = new DateTime("2019-11-01");
// $tgl2 = new DateTime("2019-12-31");
// $d = $tgl2->diff($tgl1)->days + 1;
// echo $d." hari";
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Material ID</th>
					<th class="text-center">Material Name</th>
					<th class="text-center">Category Name</th>
					<th class="text-center">Nama Supplier</th>
					<th class="text-center">Status</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>  
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						$int++;
						$class	= 'bg-green';
						$status	= 'Active';
						if($datas->id_material == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						
						$qSupDipakai	= "SELECT nm_supplier FROM raw_material_supplier WHERE id_material = '".$datas->id_material."'";
						$dataSupDipakai	= $this->db->query($qSupDipakai)->result_array();
						$dtListArray 	= array();
						foreach($dataSupDipakai AS $val => $valx){
							$dtListArray[$val] = $valx['nm_supplier'];
						}
						$dtImplode	= implode("<br>", $dtListArray);

						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='left'>".$datas->idmaterial."</td>";
							echo"<td align='left'>".strtoupper($datas->nm_material)."</td>";
							echo"<td align='left'>".$datas->nm_category."</td>";
							echo"<td align='left'>".$dtImplode."</td>";
							echo"<td align='center'><span class='badge ".$class."'>".$status."</span></td>";
							echo"<td align='center'>";
								if($akses_menu['update']=='1'){
									echo"&nbsp;<a href='".site_url($this->uri->segment(1).'/supplier_material_edit/'.$datas->id_material)."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
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

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			$("#head_title").html("<b>DETAIL MATERIAL ["+$(this).data('nm_material')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_material'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#del_type', function(){
			var bF	= $(this).data('id_material');
			// alert(bF);
			// return false;
			swal({
			  title: "Are you sure?",
			  text: "Delete this data ?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/hapus/'+bF,
						type		: "POST",
						data		: "id="+bF,
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
								window.location.href = base_url + active_controller;
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
	});
	
</script>
