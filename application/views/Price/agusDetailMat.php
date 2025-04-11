<?php
$i=0;
foreach ($result as $keys=>$val){
	$i++;
	echo "<input type=hidden class='data' value='".$val->id_product."#".$val->id."#".$val->qty."#".$val->length."#".$val->id_bq."' id='data_".$i."' size=100>";
//<!--".$i." . ".$val->id_product." # ".$val->id." # ".$val->id_bq."-->
//	$dataview[]=$this->agus_modalDetailMat($val->id_product,$val->id,$val->id_bq);
}
?>
<div class="progress active">
	<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
	  <span class="dataval"></span>
	</div>
</div>
<div id="shows"></div>
<script>
	var xhr=null;
	function showme(id) {
		var data=$("#data_"+id).val();
		var myarr = data.split("#");
		xhr = $.ajax({
			url			: base_url + active_controller+'/agus_modalDetailMat/'+myarr[0]+"/"+myarr[1]+"/"+myarr[2]+"/"+myarr[3]+"/"+myarr[4],
			type		: "POST",
			cache		: false,
			processData	: false,
			contentType	: false,
			success		: function(data){
				 $("#shows").append(id+'<br />'+data);
				 id++;
				 if(id<=<?=$i?>) {
					 showme(id);
					 valeur=parseFloat(id/<?=$i?>*100);
					 $('.progress-bar').css('width', valeur+'%').attr('aria-valuenow', valeur);
					 $(".dataval").html(id+" / <?=$i?>");
				 }else{
					 swal.close();
					 $(".dataval").html("FINISH");
					 $(".progress").addClass("hidden");
				 }
			},
			error: function(msg) {
				console.log(msg);
			}
		});
	}
	showme(1);
	$("#ModalView2").on('hide.bs.modal', function(){
		if (xhr!=null) xhr.abort();
	});	
</script>

