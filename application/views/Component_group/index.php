<?php
$this->load->view('include/side_menu'); 
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){		?>
			  <a href="<?php echo site_url('component_group/add') ?>" class="btn btn-sm btn-success" id='btn-add'>
				<i class="fa fa-plus"></i> &nbsp;Add Series
			  </a>
		  <?php
			}
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No.</th>
					<th class="text-center">Group ID</th>
					<th class="text-center">Resin System</th>
					<th class="text-center">Pressure</th>
					<th class="text-center">Liner</th>
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
						if($datas->status == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						$KdPressure		= sprintf('%02s',$datas->pressure);
						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='center'>".$datas->kode_group."</td>";
							echo"<td align='center'>".$datas->resin_system."</td>";
							echo"<td align='center'>PN ".$KdPressure."</td>";
							echo"<td align='center'>".ucwords(strtolower($datas->liner))." mm</td>";
							echo"<td align='center'><span class='badge $class'>$status</span></td>";
							echo"<td align='center'>";
								// if($akses_menu['update']=='1'){
									// echo"<a href='".site_url($this->uri->segment(1).'/edit/'.$datas->id)."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
								// }									
								if($akses_menu['delete']=='1'){
									echo"&nbsp;<button id='del_satuan' data-id='".$datas->id."' class='btn btn-sm btn-danger' title='Delete Series ".$datas->kode_group."' data-role='qtip'><i class='fa fa-trash'></i></button>";
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

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#del_satuan', function(){
			var bF	= $(this).data('id');
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
