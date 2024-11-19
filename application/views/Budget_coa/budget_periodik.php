<?php
$this->load->view('include/side_menu'); 
?>

<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-left">
				<div class="row">
					<?php
					foreach (get_list_dept() as $key => $val){
						echo "<div class='col-md-2'><a href=".site_url('budget_coa/create_periodik/'.$val['id'])." class='btn btn-default btn-block'> ".$val['nm_dept']."</a></div>";
					}
					?>
                </div>
            </div>
        </div>
</div>

<?php $this->load->view('include/footer'); ?>
<script type="text/javascript">
	var url_add = "";
	var url_add_def = base_url+'budget_coa/create_periodik/';
	var url_edit = base_url+'budget_coa/edit_periodik/';
	var url_delete = base_url+'budget_coa/hapus_data_periodik/';
	var url_view = base_url+'budget_coa/view/';

	function new_data(key){
		url_add = url_add_def+key;
		data_add();
	}

    function data_edit2(key){
        window.open(base_url+'budget_coa/edit_periodik/'+key,"_self");
	}

    function lihat_data(key){
        window.open(base_url+'budget_coa/edit_periodik/'+key+'/lihat',"_self");
	}
</script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>