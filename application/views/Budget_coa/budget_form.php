<?php
$this->load->view('include/side_menu'); 
?>

<?= form_open($this->uri->uri_string(),array('id'=>'frm_data','name'=>'frm_data','role'=>'form','class'=>'form-horizontal')) ?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
        <div class="col-md-4">
            <input type="hidden" id="type" name="type" value="<?=$type?>">
            <div class="form-group ">
                <label class="col-sm-2 control-label">Tahun<font size="4" color="red"><B>*</B></font></label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" class="form-control" id="tahunform" name="tahun" value="" placeholder="tahun" required maxlength=4>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<div class="box-body">
        <div class="row table-responsive">
            <div class="col-md-12 tableFixHead">
            <table class="table table-bordered table-condensed">
					<thead>
					<tr>
						<th>COA</th>
						<th>Definisi</th>
						<th>Penanggung Jawab</th>
						<th>Kategori</th>
						<th>Formulasi Budget</th>
						<th>Budget</th>
						<th>Referensi Budget/Tahun</th>
					</tr>
					</thead>
					<tbody>
					<?php $i=0; $tahun=date("Y");
					foreach($data as $record) {
						$i++;
						if (isset($record->tahun)) $tahun=$record->tahun;?>
						<tr>
							<td>
								<input type="hidden" name="id[]" value="<?=(isset($record->id)?$record->id:''); ?>">
								<input type="hidden" name="coa[]" value="<?=$record->no_perkiraan; ?>">
								<b><?=$record->no_perkiraan.'</b><br />'.$record->nama_perkiraan; ?>
							</td>
                            <td>
								<input type="text" class="form-control" id="definisi_<?=$i?>" name="definisi[]" value="<?=(isset($record->definisi)?$record->definisi:''); ?>">
							</td>
							<td>
								<?php
								$datadept[0]='';
								echo form_dropdown('divisi[]',$datadept, set_value('divisi', isset($record->divisi) ? $record->divisi: '0'), array('id'=>'divisi'.$i,'class'=>'form-control chosen-select'));
								?>
							</td>
							<td>
								<?php
								echo form_dropdown('kategori[]',$datakategori, set_value('kategori', isset($record->kategori) ? $record->kategori: '0'), array('id'=>'kategori'.$i,'class'=>'form-control chosen-select'));
								?>
							</td>
							<td>
								<input type="text" class="form-control" id="info_<?=$i?>" name="info[]" value="<?=(isset($record->info)?$record->info:''); ?>">
							</td>
                            <td align=right>
								<?=(isset($record->total)?number_format($record->total):0); ?>
							</td>
							<td>
								<input type="hidden" id="finance_bulan<?=$i?>" name="finance_bulan[]" value="<?=(isset($record->finance_bulan)?$record->finance_bulan:0); ?>">
								<input type="text" class="form-control maskMoney" id="finance_tahun<?=$i?>" name="finance_tahun[]" value="<?=(isset($record->finance_tahun)?number_format($record->finance_tahun):0); ?>" size="5" data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
							</td>
					<?php } ?>
					</tbody>
					</table>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" name="save" class="btn btn-success" id="submit">Save</button>
            <button type='button' class="btn btn-danger" onclick="cancel()">Back</button>
        </div>
    </div>
</div>
<?= form_close() ?>

<?php $this->load->view('include/footer'); ?>

<style>
    .tableFixHead          { overflow: auto; height: 500px; }
    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; background-color:#dadada; }

    .chosen-container, .chosen-container-single{
        width: 100% !important;
    }
		
</style>
<script>
	$(document).ready(function() {
		$(".maskMoney").maskMoney();
        $(".chosen-select").chosen({
            width: '100%'
        });
		$("#tahunform").val('<?=$tahun?>');
    });

	function settahun(id){
		var perbulan=$("#finance_bulan"+id).val();
		$("#finance_tahun"+id).val(parseFloat(perbulan)*12);
	}

	function cektotal(){
		var sum = 0;
		$('.bulan').each(function() {
			sum += Number($(this).val());
		});
		$("#total").val(sum);
	}

    $('#frm_data').on('submit', function(e){
        e.preventDefault();
        var formdata = $("#frm_data").serialize();
        $.ajax({
            url: base_url+"budget_coa/save_data",
            dataType : "json",
            type: 'POST',
            data: formdata,
            success: function(msg){
                if(msg['save']=='1'){
                    swal({
                        title: "Sukses!",
                        text: "Data Berhasil Di Simpan",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
//                    cancel();
                    window.location.reload();
                } else {
                    swal({
                        title: "Gagal!",
                        text: "Data Gagal Di Simpan",
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
    });

    function cancel(){
        window.open(base_url+'budget_coa',"_self");
    }
</script>
