<?php
$this->load->view('include/side_menu'); 
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){
		?>
		  <a href="<?php echo site_url(strtolower($this->uri->segment(1)).'/add') ?>" class="btn btn-sm btn-success" id='btn-add'>
			<i class="fa fa-plus"></i> ADD BUSINESS FIELDS
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
					<th class="text-center">Business Field</th>
					<th class="text-center">Description</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						$int++;
						
						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='left'>".$datas->bidang_usaha."</td>";
							echo"<td align='left'>".strtoupper($datas->keterangan)."</td>";
							echo"<td align='center'>";
								// if($akses_menu['read']=='1'){
									// echo"<a href='".site_url(strtolower($this->uri->segment(1)).'/view/'.$datas->id_bidang_usaha)."' class='btn btn-sm btn-default' title='View Data' data-role='qtip' id='btn-view'><i class='fa fa-search'></i></a>";
								// }	
								if($akses_menu['update']=='1' && $datas->deleted !=1){
									echo"&nbsp;<a href='".site_url(strtolower($this->uri->segment(1)).'/edit/'.$datas->id_bidang_usaha)."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip' id='btn-edit'><i class='fa fa-edit'></i></a>";
								}									
								if($akses_menu['delete']=='1' && $datas->deleted !=1 ){
									echo"&nbsp;<a href='#' onClick='return del_bidang(".$datas->id_bidang_usaha.");' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></a>";
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
		$('#btn-add, #btn-edit, btn-view').click(function(){
			loading_spinner();
		});
		
	});
	function del_bidang(id){
		
		swal({
			  title: "Are you sure?",
			  text: "You will not be able to process again this data!",
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
					window.location.href = base_url +'index.php/'+ active_controller+'/delete_data/'+id;
					
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			  }
		});
       
	}
</script>
