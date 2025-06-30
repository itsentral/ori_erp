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
		  <a href="<?php echo site_url('users/register_user') ?>" class="btn btn-sm btn-success" id='btn-add'>
			<i class="fa fa-user"></i> Add User
		  </a>
		  <?php
			}
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="example1"> 
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">Username</th>
					<th class="text-center">Name</th>
					<th class="text-center">Group</th>
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
						if($datas->st_aktif != '1'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						echo"<tr>";							
							echo"<td align='left'>".$datas->username."</td>";
							echo"<td align='left'>".$datas->nm_lengkap."</td>";
							echo"<td align='left'>".$datas->nm_group."</td>";
							echo"<td align='center'><span class='badge $class'>$status</span></td>";

							$view	='<button type="button" class="btn btn-sm btn-default" title="View Data" data-role="qtip" onClick="viewUser('.$datas->id_user.');"><i class="fa fa-search"></i></button>';
							$edit = '';
							$change = '';
							$delete = '';
							
							if($akses_menu['update']=='1'){
								$edit	='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-primary" title="Edit Data" data-role="qtip" onClick="editUser('.$datas->id_user.');"><i class="fa fa-pencil"></i></button>';
								$change	='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-success" title="Change Password" data-role="qtip" onClick="changeUser('.$datas->id_user.');"><i class="fa fa-key"></i></button>';
							}
							if($akses_menu['delete']=='1'){
								$delete	='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-danger" title="Delete Data" data-role="qtip" onClick="deleteUser('.$datas->id_user.');"><i class="fa fa-trash-o"></i></button>';
							}

							echo"<td align='center'>".$view.$edit.$change.$delete."</td>";
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
	var akses_menu		= <?php echo json_encode($akses_menu);?>;
	var akses_group		= <?php echo json_encode($row_group);?>;
	$(document).ready(function(){
		
		$('#listData').dataTable( {
			"processing": true,
			"serverSide": true,
			"ajax": base_url + active_controller+'/display_data',
			"columns": [
				{"data":"username"},
				{"data":"nm_lengkap"},
				{"data":"group_name","searchable":"false"},
				{"data":"status","sClass":"text-center","searchable":"false"},
				{"data":"action","sClass":"text-center","searchable":"false"}
			],
			"rowCallback": function(row,data,index,iDisplayIndexFull){
				//console.log(data.tool_id);
				var group	= data.group_id;
				var sts		= data.st_aktif;
				var group_name	= akses_group[group];
				if(sts=='1'){
					var status	='<span class="badge bg-green">ACTIVE</span>';
				}else{
					var status	='<span class="badge bg-red">INACTIVE</span>';
				}
				$('td:eq(2)',row).append(group_name);
				$('td:eq(3)',row).append(status);
				var Template	='<button type="button" class="btn btn-sm btn-default" title="View Data" data-role="qtip" onClick="viewUser('+"'"+data.id_user+"'"+');"><i class="fa fa-search"></i></button>';
				if(akses_menu.update =='1'){
					Template	+='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-primary" title="Edit Data" data-role="qtip" onClick="editUser('+"'"+data.id_user+"'"+');"><i class="fa fa-pencil"></i></button>';
					Template	+='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-success" title="Change Password" data-role="qtip" onClick="changeUser('+"'"+data.id_user+"'"+');"><i class="fa fa-key"></i></button>';
				}
				if(akses_menu.delete =='1'){
					Template	+='&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-danger" title="Delete Data" data-role="qtip" onClick="deleteUser('+"'"+data.id_user+"'"+');"><i class="fa fa-trash-o"></i></button>';
				}
				$('td:eq(4)',row).append(Template);
				
			},
			"order": [[1,"asc"]]
		});
		
		// DataTable
		//var table	= $("#listData").DataTable();
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
	});
	function deleteUser(id){
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
					window.location.href = base_url +'index.php/'+ active_controller+'/delete_user/'+id;
					
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			  }
		});
       
	} 
	
	function viewUser(id){
		loading_spinner();
		window.location.href = base_url +'index.php/'+ active_controller+'/view_user/'+id;
		    
	}
	
	function editUser(id){
		loading_spinner();
		window.location.href = base_url +'index.php/'+ active_controller+'/edit_user/'+id;
		    
	}
	function changeUser(id){
		loading_spinner();
		window.location.href = base_url +'index.php/'+ active_controller+'/change_password/'+id;
		    
	}
</script>
