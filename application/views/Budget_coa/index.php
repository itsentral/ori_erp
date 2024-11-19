<?php
$this->load->view('include/side_menu'); 
?>

<div class="box box-primary">
    <form method="get" action="">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-left">
                <select name="tahun" id="tahun" class="form-control" required="required" onchange="cek_button()">
                    <?php
                    foreach($listtahun as $val){
                        echo '<option value="'.$val.'" '.($val==$tahun?' selected ':'').'>'.$val.'</option>';
                    }
                    ?>
                </select>
                <button type="button" id="btn_view" class="btn btn-info" onclick="lihat_data()">Lihat</button>
                <button type="button" id="btn_detail" class="btn bg-purple hidden" onclick="detail_data()">Detail</button>
                <button type="button" id="btn_edit" class="btn btn-warning hidden" onclick="edit_data()">Edit</button>
                <button type="button" id="btn_delete" class="btn btn-danger hidden" onclick="delete_data()">Delete</button>
                <button type="button" id="btn_new" class="btn btn-success hidden" onclick="add_data()">New</button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box-body table-responsive">
                <table id="mytabledata" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tahun</th>
                            <th>COA</th>
                            <th>Definisi</th>
                            <th>Penganggung Jawab</th>
                            <th>Kategori</th>
                            <th>Formulasi Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $edit='';
                        if(empty($results)){
                            $edit='';
                        }
                        else{
                            $numb=0;
                            foreach($results AS $record) {
                                $numb++; 
								$kategori = $record->kategori;
								if(strtolower($kategori) == 'rutin'){
									$kategori = 'STOK';
								}
								if(strtolower($kategori) == 'non rutin'){
									$kategori = 'DEPARTMENT';
								}
								?>
                                <tr>
                                    <td><?= $numb ?></td>
                                    <td><?= $record->tahun ?></td>
                                    <td><?= $record->no_perkiraan?> | <?= $record->nama_perkiraan ?></td>
                                    <td><?= $record->definisi ?></td>
                                    <td><?= $record->nm_dept ?></td>
                                    <td><?= $kategori ?></td>
                                    <td><?= $record->info ?></td>
                                </tr>
                            <?php
                                if($record->status==0) {
                                    $edit=$record->tahun;
                                }
                            }
                        }  
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <form method="post" action="<?=base_url('budget_coa/detail_bulan');?>">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Laporan Budget</h3><br><br>
                <div class="box-tool pull-left">
                    <select name="bulan" id="bulan" class="form-control" required="required">
                        <?php
                        for($i=1; $i<=12; $i++) {
                            echo '<option value="'.$i.'">'.date('M', strtotime('2020-'.$i.'-01')).'</option>';  
                        }
                        ?>
                    </select>

                    <select name="tahun" id="tahun" class="form-control" required="required">
                        <?php
                        foreach($listtahun as $val){
                            echo '<option value="'.$val.'" '.($val==$tahun?' selected ':'').'>'.$val.'</option>';
                        }
                        ?>
                    </select>

                    <button type="submit" class="btn btn-info">Lihat</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
        
            </div>
        </div>
    </form>
</div>
<?php $this->load->view('include/footer'); ?>
<style>
    button{
        width: 70px;
    }
</style>
<script>
	 $(document).ready(function() {
		<?php
        if($edit!='') {
			?>
			$("#btn_edit").removeClass("hidden");
			$("#btn_approve").removeClass("hidden");
			$("#btn_detail").removeClass("hidden");
			$("#btn_delete").removeClass("hidden");
			$("#btn_new").addClass("hidden");
		    <?php
		}else{
				echo '$("#btn_new").removeClass("hidden");';
			}
        ?>
    });
	function cek_button(){
			$("#btn_edit").addClass("hidden");
			$("#btn_approve").addClass("hidden");
			$("#btn_detail").addClass("hidden");
			$("#btn_delete").addClass("hidden");
			$("#btn_new").addClass("hidden");
	}
  	$(function() {
    	// $("#mytabledata").DataTable();
		$("#mytabledata").DataTable({
			"paging":   true,
			dom: 'lBfrtip',
			"pageLength": 50,
			buttons: [{
                extend: 'excel',
                exportOptions: {
                    columns: [ 1,2,3,4,5 ]
                }
            }]
		});
    	$("#form-data").hide();
  	});

    function lihat_data(){
        window.open(base_url+'budget_coa?tahun='+$('#tahun').val(),"_self");
    }

  	function detail_data(){
        window.open(base_url+'budget_coa/detail/<?=$edit?>',"_self");
	}

	function edit_data(){
        window.open(base_url+'budget_coa/edit/<?=$edit?>',"_self");
	}

	function add_data(){
        window.open(base_url+'budget_coa/create',"_self");
	}

	//Delete
	function delete_data(){
		swal({
		  title: "Anda Yakin?",
		  text: "Data Akan Terhapus secara Permanen!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Ya!",
		  cancelButtonText: "Tidak!",
		  closeOnConfirm: false,
		  closeOnCancel: true
		},
		function(isConfirm){
		  if (isConfirm) {
		  	$.ajax({
		            url: base_url+'budget_coa/hapus_data/<?=$edit?>',
		            dataType : "json",
		            type: 'POST',
		            success: function(msg){
		                if(msg['delete']=='1'){
		                    swal({
		                      title: "Terhapus!",
		                      text: "Data berhasil dihapus",
		                      type: "success",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                    window.location.reload();
		                } else {
		                    swal({
		                      title: "Gagal!",
		                      text: "Data gagal dihapus",
		                      type: "error",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                };
						console.log(msg)
		            },
		            error: function(msg){
		                swal({
	                      title: "Gagal!",
	                      text: "Gagal Eksekusi Ajax",
	                      type: "error",
	                      timer: 1500,
	                      showConfirmButton: false
	                    });
						console.log(msg)
		            }
		        });
		  } else {
		    //cancel();
		  }
		});
	}
	//Approve
	function approve_data(){
		swal({
		  title: "Anda Yakin?",
		  text: "Data Akan Di Approve!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Ya, Approve!",
		  cancelButtonText: "Tidak!",
		  closeOnConfirm: false,
		  closeOnCancel: true
		},
		function(isConfirm){
		  if (isConfirm) {
		  	$.ajax({
		            url: base_url+'budget_coa/approve_data/<?=$edit?>',
		            dataType : "json",
		            type: 'POST',
		            success: function(msg){
		                if(msg['delete']=='1'){
		                    swal({
		                      title: "Diapprove!",
		                      text: "Data berhasil Approve",
		                      type: "success",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                    window.location.reload();
		                } else {
		                    swal({
		                      title: "Gagal!",
		                      text: "Data gagal di approve",
		                      type: "error",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                };
						console.log(msg)
		            },
		            error: function(msg){
		                swal({
	                      title: "Gagal!",
	                      text: "Gagal Eksekusi Ajax",
	                      type: "error",
	                      timer: 1500,
	                      showConfirmButton: false
	                    });
						console.log(msg)
		            }
		        });
		  } else {
		    //cancel();
		  }
		});
	}	
</script>
