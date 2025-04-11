<?php
// Require composer autoload
$sroot 		= $_SERVER['DOCUMENT_ROOT'];
include $sroot."/application/libraries/MPDF57/mpdf.php";
// $mpdf=new mPDF('utf-8','A4');
$mpdf=new mPDF('utf-8','A4-L');


set_time_limit(0);
ini_set('memory_limit','1024M');
ob_start();

foreach($detail AS $keys => $valx){
	$ids 	= $valx['id_material'];
	$images	= "	<figure class='fig-header'>
					<span>
						<img src='https://chart.googleapis.com/chart?chs=150x150&amp;cht=qr&amp;chl=$ids&amp;choe=UTF-8' alt='QR code'>
					</span>
					<figcaption  class='fig-caption'>".$ids."#".$valx['id']."#".$valx['nm_material']."</figcaption>
				</figure>";
	echo $images;
	// QRcode::png($link);
}

?>
<style>
	.qr{
		border: 1px solid black;
	}

	.fig-header{
		border: 2px solid black;
		padding: 5px;
		margin-bottom: 5px;
		margin-right: 5px;
		margin-left: 5px;
		text-align: center;
		width : 200px;
		display: inline-block !important;
		float:left;
	}

	.fig-caption{
		font-family: verdana,arial,sans-serif;
		font-size:9px;
	}
</style>
<?php
// exit;
$html = ob_get_contents();
ob_end_clean();

$mpdf->WriteHTML($html);
$mpdf->SetTitle('QRCODE ');
$mpdf->Output('QRCODE '.date('dmyhis').'.pdf' ,'I');
