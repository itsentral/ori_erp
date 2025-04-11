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
                <table class="table table-bordered table-condensed">
                <tr>
                    <td>COA</td>
                    <td>Jenis</td>
                    <td>Nilai</td>
                    <td></td>
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
                            <?php
                            echo form_dropdown('jenis[]',$datajenis, set_value('jenis', isset($record->jenis) ? $record->jenis: '0'), array('id'=>'jenis'.$i,'class'=>'form-control select2','onchange'=>'cekvariabel('.$i.')'));
                            ?>
                        </td>
                        <td nowrap>
                            <div class="input-group <?=($record->jenis=='FIX COST BULANAN'?'hidden':'')?>" id="divilai<?=$i?>">
                            <input type="text" id="nilai<?=$i?>" name="nilai[]" value="<?=(isset($record->nilai)?$record->nilai:0); ?>" class="form-control input-sm maskMoney" data-decimal="." data-thousand="" data-precision="0" data-allow-zero="">
                            <span class="input-group-addon"><span class="<?=($record->variabel_coa=='' || $record->variabel_coa=='0'?'hidden':'')?>" id='persen<?=$i?>'>%</span> </span>
                            </div>
                            <div id="tdbulan<?=$i?>" class="<?=($record->jenis=='FIX COST BULANAN'?'':'hidden')?>" nowrap>
                            <?php
                            for($bln=1;$bln<=12;$bln++){
                                echo '<span>'.sprintf('%02d',$bln).'<input type="text" size="12" class="form-fixer maskMoney input-sm" id="bulan_'.$bln.'_'.$i.'" name="bulan_'.$bln.'[]" value="'.(isset($record->{"bulan_".$bln})?$record->{"bulan_".$bln}:0).'" data-decimal="." data-thousand="" data-precision="0" data-allow-zero=""></span> ';
                                if($bln%4==0) echo '<br />';
                            }
                            ?>
                            </div>
                        </td>
                        <td id="row<?=$i?>" class="<?=($record->jenis=='VARIABLE'?'':'hidden')?>">
                            <?php
                            echo form_dropdown('variabel_coa[]',$datacoa, set_value('variabel_coa', isset($record->variabel_coa) ? $record->variabel_coa: '0'), array('id'=>'variabel_coa'.$i,'class'=>'form-control select2'));
                            ?>
                        </td>
                <?php } ?>
                </table>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="save" class="btn btn-success" id="submit"><i class="fa fa-save">&nbsp;</i>Save</button>
                    <a class="btn btn-danger" data-toggle="modal" onclick="cancel()"><i class="fa fa-minus-circle">&nbsp;</i>Cancel</a>
                    </div>
                </div>
            </div>
        <?= form_close() ?>
    </div>     
</div>

<style>
    input.form-fixer {
        padding: 1px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
		$(".maskMoney").maskMoney();
		  $(".select2").chosen({
			width: '100%'
		  });
    });
	function cekvariabel(idrow){
		var variabel=$("#jenis"+idrow).val();
		if(variabel=='VARIABLE'){
			$("#row"+idrow).removeClass("hidden");
			$("#persen"+idrow).removeClass("hidden");
		}else{
			$("#row"+idrow).addClass("hidden");
			$("#persen"+idrow).addClass("hidden");
			$("#variabel_coa"+idrow).val('').trigger('change');
		}
		$("#tdbulan"+idrow).addClass("hidden");
		$("#divilai"+idrow).removeClass("hidden");		
		if(variabel=='FIX COST BULANAN'){
			$("#tdbulan"+idrow).removeClass("hidden");
			$("#divilai"+idrow).addClass("hidden");
			$("#nilai"+idrow).val(0);
		}
	}
    $('#frm_data').on('submit', function(e){
        e.preventDefault();
        var formdata = $("#frm_data").serialize();
        $.ajax({
            url: base_url+"budget_coa/save_data_umum",
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
