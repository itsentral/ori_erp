<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $title;?></h3>
    </div>
    <div class="box-body">
        <?= form_open($this->uri->uri_string(),array('id'=>'frm_data','name'=>'frm_data','role'=>'form','class'=>'form-horizontal')) ?>
            <input type="hidden" id="type" name="type" value="edit">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group ">
                            <label class="col-sm-4 control-label">Tahun</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="tahun" name="tahun" value="<?=$dataset['tahun']?>" placeholder="tahun" readonly required maxlength=4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group ">
                            <label class="col-sm-6 control-label">Penanggung Jawab</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                <?php
                                echo form_dropdown('divisi',$datadept, $dataset['divisi'], array('id'=>'divisi','class'=>'form-control','readonly'=>'readonly'));
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group ">
                            <label class="col-sm-2 control-label">Kategori</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                <?php
                                echo form_dropdown('kategori',$datakategori, $dataset['kategori'], array('id'=>'kategori','class'=>'form-control','readonly'=>'readonly'));
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-condensed">
                    <tr>
                        <td>COA</td>
                        <td>Total Budget</td>
                        <?php
                        for($bln=1;$bln<=12;$bln++){
                            echo '<td nowrap>'.date('F', mktime(0, 0, 0, $bln, 10)).'</td>';
                        }
                        ?>
                    </tr>
                    <?php $i=0;
                    foreach($data as $record) {
                        $i++; ?>
                        <tr>
                            <td width="200px">
                                <input type="hidden" name="id[]" value="<?=(isset($record->id)?$record->id:''); ?>">
                                <input type="hidden" name="coa[]" value="<?=$record->no_perkiraan; ?>">
                                <?=$record->no_perkiraan.'<br />'.$record->nama_perkiraan; ?>
                            </td>
                            <td>
                                <input type="text" id="total<?=$i?>" name="total[]" value="<?=(isset($record->total)?$record->total:0); ?>" class="input-sm maskMoney" size="8" readonly tabindex="-1" style="background-color:#eee"  data-decimal="." data-thousand="" data-precision="0">
                            </td>
                            <?php
                            for($bln=1;$bln<=12;$bln++){
                                echo '<td><input type="text" size=8 onkeyup="cektotalrow('.$bln.','.$i.')" class="maskMoney input-sm" id="bulan_'.$bln.'_'.$i.'" name="bulan_'.$bln.'[]" value="'.(isset($record->{"bulan_".$bln})?$record->{"bulan_".$bln}:0).'" data-decimal="." data-thousand="" data-precision="0" ></td>';
                            }
                            ?>
                    <?php } ?>
                    </table>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="save" class="btn btn-success" id="submit"><i class="fa fa-save">&nbsp;</i>Save</button>
                    <a class="btn btn-danger" data-toggle="modal" onclick="cancel()">Cancel</a>
                    </div>
                </div>
            </div>
        <?= form_close() ?>
    </div>     
</div>


<script type="text/javascript">
    $(document).ready(function() {
		$(".maskMoney").maskMoney();
    });
	function cektotalrow(bln,row){
		var sum = 0;
		var finance = $("#finance_tahun"+row).val();
		for(i=1;i<=12;i++){
			sum += Number($("#bulan_"+i+"_"+row).val().split(",").join(""));
		};
		if (Number(sum)>Number(finance)) {
			alert("Total alokasi budget melebihi budget yang telah ditentukan");
			$("#bulan_"+bln+"_"+row).focus();
			$("#bulan_"+bln+"_"+row).val(0);
		}else{
			$("#total"+row).val(sum);
		}
	}
    $('#frm_data').on('submit', function(e){
        e.preventDefault();
        var formdata = $("#frm_data").serialize();
        $.ajax({
            url: base_url+"budget_coa/save_data_category",
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
        $(".box").show();
        $("#form-data").hide();
    }
</script>