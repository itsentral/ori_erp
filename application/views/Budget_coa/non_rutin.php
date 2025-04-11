<?php
$this->load->view('include/side_menu'); 
?>

<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-left">
                <a class="btn btn-success" href="javascript:void(0)" title="Add" onclick="showmodal()">New</a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box-body table-responsive">
            <table id="mytabledata" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <th>Tahun</th>
                    <th>Penanggung Jawab</th>
                    <th>Kategori</th>
                    <th>Revisi</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $edit='';
                if(empty($results)){
                    $edit='';
                }else{
                    $numb=0;
                    foreach($results AS $record) {
                        $numb++; 
                        $kategori = $record->kategori;
                        if($record->kategori == 'NON RUTIN'){
                            $kategori = 'DEPARTEMEN';
                        }
                        ?>
                <tr>
                    <td><?= $record->created_on_dept ?></td>
                    <td><?= $record->tahun ?></td>
                    <td><?= $record->nm_dept ?></td>
                    <td><?= $kategori?></td>
                    <td><?= $record->revisi?></td>
                    <td>
                    <?php
                    if($record->status=='3'){ ?>
                        <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Revisi" onclick="revisi_data('<?=$record->tahun?>','<?=$record->kategori?>','<?=$record->divisi?>','<?=$record->revisi?>')"> <i class="fa fa-share-square"></i></a> 
                    <?php }
                    if($record->status=='2'){ ?>
                        <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Approve" onclick="approve_data('<?=$record->tahun?>','<?=$record->kategori?>','<?=$record->divisi?>')"> <i class="fa fa-check-square-o"></i></a> 
                        <a class="btn btn-sm btn-warning" href="javascript:void(0)" title="Edit" onclick="edit_data('<?=$record->tahun?>','<?=$record->kategori?>','<?=$record->divisi?>')"> <i class="fa fa-pencil"></i></a> 
                    <?php }
                    ?>
                        <a class="btn btn-sm btn-info" href="javascript:void(0)" title="Print" onclick="print_data('<?=$record->tahun?>','<?=$record->kategori?>','<?=$record->divisi?>')"> <i class="fa fa-print"></i></a> 
                    		
                    </td>
                </tr>
                <?php
                    }
                }  ?>
                </tbody>
            </table>
            </div>
        </div>
    
           
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="mymodal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pilih Budget</h4>
      </div>
      <div class="modal-body">
		<form id="frmbudget" name="frmbudget" method="post" target="_blank" action="<?=base_url('budget_coa/print_budget_category')?>">
		<input type="hidden" id="fkategori" name="fkategori" value="<?=$tipek?>">
	    <div class="row">
		<?php
        $datadept[0] = 'Select An Department';
		echo '<div class="col-md-8"><label> Penanggung Jawab</label><br />'.form_dropdown('fdivisi',$datadept, '0',array('id'=>'fdivisi','class'=>'form-control chosen-select')).'</div>';
		echo '<div class="col-md-4"><label> Tahun</label><br /><input type="text" id="ftahun" name="ftahun" class="form-control" maxlength="4"></div>';
		?>
        </div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="add_data()">Lanjut</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="form-data"></div>

<?php $this->load->view('include/footer'); ?>
<style>
    #fdivisi_chosen{
        width: 100% !important;
    }
</style>
<script type="text/javascript">
  	$(function() {
    	$("#mytabledata").DataTable({
			"paging":   true,
		});
    	$("#form-data").hide();
        
  	});
	function revisi_data(tahun,kategori,divisi,revisi){
        $.ajax({
            url: base_url+"budget_coa/revisi_data_category",
            dataType : "json",
            type: 'POST',
            data: {tahun : tahun, kategori : kategori, divisi : divisi, revisi : revisi},
            success: function(msg){
                if(msg['save']=='1'){
                    swal({
                        title: "Sukses!",
                        text: "Data Berhasil Di Approve",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.reload();
                } else {
                    swal({
                        title: "Gagal!",
                        text: "Data Gagal Di Approve",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                };
				console.log(msg);
            },
            error: function(msg){
                swal({
                    title: "Gagal!",
                    text: "Ajax Data Gagal Di Proses",
                    type: "error",
                    timer: 1500,
                    showConfirmButton: false
                });
				console.log(msg);
            }
        });
	}
	function approve_data(tahun,kategori,divisi){
        $.ajax({
            url: base_url+"budget_coa/approve_data_category",
            dataType : "json",
            type: 'POST',
            data: {tahun : tahun,kategori : kategori,divisi : divisi},
            success: function(msg){
                if(msg['save']=='1'){
                    swal({
                        title: "Sukses!",
                        text: "Data Berhasil Di Approve",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.reload();
                } else {
                    swal({
                        title: "Gagal!",
                        text: "Data Gagal Di Approve",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                };
				console.log(msg);
            },
            error: function(msg){
                swal({
                    title: "Gagal!",
                    text: "Ajax Data Gagal Di Proses",
                    type: "error",
                    timer: 1500,
                    showConfirmButton: false
                });
				console.log(msg);
            }
        });
	}
	function showmodal(){
		$("#ftahun").val(" <?=date("Y")?>");
		$("#fdivisi").val("");
		$('#mymodal').modal('show');
		//add_data
	}
  	function add_data(){
		var url = 'budget_coa/create_budget_category';
		$(".box").hide();
		$('#mymodal').modal('hide');
		$("#form-data").show();
		$("#form-data").load(base_url+url, {
           kategori: $("#fkategori").val(), 
           tahun: $("#ftahun").val(), 
           divisi: $("#fdivisi").val()
       });
	}

  	function edit_data(tahun,kategori,divisi){
		var url = 'budget_coa/create_budget_category/';
		$(".box").hide();
		$("#form-data").show();
		$("#form-data").load(base_url+url,{
           kategori: kategori, 
           tahun: tahun, 
           divisi: divisi
		});
        $('.chosen-select').chosen({
            width: '100%'
        });
	}

  	function print_data(tahun,kategori,divisi){
		$("#fkategori").val(kategori);
		$("#ftahun").val(tahun);
		$("#fdivisi").val(divisi);
		$("#frmbudget").submit();
	}
</script>
