<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	date_default_timezone_set("Asia/Bangkok");
	
	function Enkripsi($sData, $sKey='200881173_HyunJoo'){
		$sResult = '';
		for($i=0;$i<strlen($sData);$i++){
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
			$sChar    = chr(ord($sChar) + ord($sKeyChar));
			$sResult .= $sChar;
		}
		return Enkripsi_base64($sResult);
	}

	function Dekripsi($sData, $sKey='200881173_HyunJoo'){
		$sResult = '';
		$sData   = Dekripsi_base64($sData);
		for($i=0;$i<strlen($sData);$i++){
			$sChar    = substr($sData, $i, 1);
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
			$sChar    = chr(ord($sChar) - ord($sKeyChar));
			$sResult .= $sChar;
		}
		return $sResult;
	}

	function Enkripsi_base64($sData){
		$sBase64 = base64_encode($sData);
		return strtr($sBase64, '+/', '-_');
	}

	function Dekripsi_base64($sData){
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64);
	}

	function history($desc=NULL){
		$CI 			=& get_instance();
		$path			= $CI->uri->segment(1);
		$data_session	= $CI->session->userdata;
		$userID			= $data_session['ORI_User']['username'];
		$Date			= date('Y-m-d H:i:s');
		$IP_Address		= $CI->input->ip_address();

		$DataHistory=array();
		$DataHistory['user_id']		= $userID;
		$DataHistory['path']		= $path;
		$DataHistory['description']	= $desc;
		$DataHistory['ip_address']	= $IP_Address;
		$DataHistory['created']		= $Date;
		$CI->db->insert('histories',$DataHistory);
	}

	function cryptSHA1($fields){
		$key			='-SonHyunJoo173';
		$Encrpt_Kata	= sha1($fields.$key);
		return $Encrpt_Kata;
	}

	function getRomawi($bulan){
		$month	= intval($bulan);
		switch($month){
			case "1":
				$romawi	='I';
				break;
			case "2":
				$romawi	='II';
				break;
			case "3":
				$romawi	='III';
				break;
			case "4":
				$romawi	='IV';
				break;
			case "5":
				$romawi	='V';
				break;
			case "6":
				$romawi	='VI';
				break;
			case "7":
				$romawi	='VII';
				break;
			case "8":
				$romawi	='VIII';
				break;
			case "9":
				$romawi	='IX';
				break;
			case "10":
				$romawi	='X';
				break;
			case "11":
				$romawi	='XI';
				break;
			case "12":
				$romawi	='XII';
				break;
		}
		return $romawi;
	}

	function getColsChar($colums){
		// Palleng by jester

		if($colums>26)
		{
			$modCols = floor($colums/26);
			$ExCols = $modCols*26;
			$totCols = $colums-$ExCols;

			if($totCols==0)
			{
				$modCols=$modCols-1;
				$totCols+=26;
			}

			$lets1 = getLetColsLetter($modCols);
			$lets2 = getLetColsLetter($totCols);
			return $letsi = $lets1.$lets2;
		}
		else
		{
			$lets = getLetColsLetter($colums);
			return $letsi = $lets;
		}
	}

	function getLetColsLetter($numbs){
	// Palleng by jester
		switch($numbs){
			case 1:
			$Chars = 'A';
			break;
			case 2:
			$Chars = 'B';
			break;
			case 3:
			$Chars = 'C';
			break;
			case 4:
			$Chars = 'D';
			break;
			case 5:
			$Chars = 'E';
			break;
			case 6:
			$Chars = 'F';
			break;
			case 7:
			$Chars = 'G';
			break;
			case 8:
			$Chars = 'H';
			break;
			case 9:
			$Chars = 'I';
			break;
			case 10:
			$Chars = 'J';
			break;
			case 11:
			$Chars = 'K';
			break;
			case 12:
			$Chars = 'L';
			break;
			case 13:
			$Chars = 'M';
			break;
			case 14:
			$Chars = 'N';
			break;
			case 15:
			$Chars = 'O';
			break;
			case 16:
			$Chars = 'P';
			break;
			case 17:
			$Chars = 'Q';
			break;
			case 18:
			$Chars = 'R';
			break;
			case 19:
			$Chars = 'S';
			break;
			case 20:
			$Chars = 'T';
			break;
			case 21:
			$Chars = 'U';
			break;
			case 22:
			$Chars = 'V';
			break;
			case 23:
			$Chars = 'W';
			break;
			case 24:
			$Chars = 'X';
			break;
			case 25:
			$Chars = 'Y';
			break;
			case 26:
			$Chars = 'Z';
			break;
		}

		return $Chars;
	}

	function getColsLetter($char){
	//	Palleng by jester
		$len = strlen($char);
		if($len==1)
		{
			$numb = getLetColsNumber($char);
		}
		elseif($len==2)
		{
			$i=1;
			$j=0;
			$jm=1;
			while($i<$len)
			{
				$let_fst = substr($char, $j,1);
				$dv = getLetColsNumber($let_fst);
				$jm = $dv * 26;

				$i++;
				$j++;
			}
			$let_last = substr($char, $j,1);
			$numb = $jm + getLetColsNumber($let_last);
		}

		return $numb;
	}

	function getLetColsNumber($char)
	{
		// Palleng by jester

		switch($char){
			case 'A':$numb = 1;break;
			case 'B':$numb = 2;break;
			case 'C':$numb = 3;break;
			case 'D':$numb = 4;break;
			case 'E':$numb = 5;break;
			case 'F':$numb = 6;break;
			case 'G':$numb = 7;break;
			case 'H':$numb = 8;break;
			case 'I':$numb = 9;break;
			case 'J':$numb = 10;break;
			case 'K':$numb = 11;break;
			case 'L':$numb = 12;break;
			case 'M':$numb = 13;break;
			case 'N':$numb = 14;break;
			case 'O':$numb = 15;break;
			case 'P':$numb = 16;break;
			case 'Q':$numb = 17;break;
			case 'R':$numb = 18;break;
			case 'S':$numb = 19;break;
			case 'T':$numb = 20;break;
			case 'U':$numb = 21;break;
			case 'V':$numb = 22;break;
			case 'W':$numb = 23;break;
			case 'X':$numb = 24;break;
			case 'Y':$numb = 25;break;
			case 'Z':$numb = 26;break;
		}

		return $numb;
	}

	function getAcccesmenu($controller=NULL){
		$CI 			=& get_instance();
		$data_session	= $CI->session->userdata;
		$group			= $data_session['ORI_User']['group_id'];
		$action=array();
		if($group=='1'){
			$action["read"]		= 1;
			$action["create"]	= 1;
			$action["update"]	= 1;
			$action["delete"]	= 1;
			$action["download"]	= 1;
			$action["approve"]	= 1;
		}else{
			$qMenu		= $CI->db->get_where('menus',array('LOWER(path)'=>strtolower($controller)));
			$dataMenu	= $qMenu->result();
			// echo $controller;
			// echo "<pre>"; print_r($dataMenu);exit;
			// echo $dataMenu[0]->id; exit;
			if($qMenu->num_rows() > 0){
				$qAccess	= $CI->db->get_where('group_menus',array('menu_id'=>$dataMenu[0]->id,'group_id'=>$group));
				$DataAccess	= $qAccess->result();
				if($DataAccess){
					$action["read"]=(isset($DataAccess[0]->read) && $DataAccess[0]->read)?$DataAccess[0]->read:0;
					$action["create"]=(isset($DataAccess[0]->create) && $DataAccess[0]->create)?$DataAccess[0]->create:0;
					$action["update"]=(isset($DataAccess[0]->update) && $DataAccess[0]->update)?$DataAccess[0]->update:0;
					$action["delete"]=(isset($DataAccess[0]->delete) && $DataAccess[0]->delete)?$DataAccess[0]->delete:0;
					$action["download"]=(isset($DataAccess[0]->download) && $DataAccess[0]->download)?$DataAccess[0]->download:0;
					$action["approve"]=(isset($DataAccess[0]->approve) && $DataAccess[0]->approve)?$DataAccess[0]->approve:0;
				}
			}

		}
		return $action;
	}

	function generate_tree($data=array(),$depth=0,$nilai=array()){
		$ArrDept=array(0=>10,1=>40,2=>70,3=>100);
		if(isset($data) && $data){
			foreach($data as $key=>$value){
				echo create_datas($value,$ArrDept[$depth],$nilai);
				if(array_key_exists('child',$value)){
					generate_tree($value['child'],($depth + 1),$nilai);
				}
			}
		}
	}

	function create_datas($value=array(),$padding=NULL,$data=array()){
			$template='<tr>';
			$state['read']		= (isset($data[$value['id']]['read']) && $data[$value['id']]['read'] == 1) ? ' checked="checked"' : '';
			$state['create']	= (isset($data[$value['id']]['create']) && $data[$value['id']]['create'] == 1) ? ' checked="checked"' : '';
			$state['update']	= (isset($data[$value['id']]['update']) && $data[$value['id']]['update'] == 1) ? ' checked="checked"' : '';
			$state['delete']	= (isset($data[$value['id']]['delete']) && $data[$value['id']]['delete'] == 1) ? ' checked="checked"' : '';
			$state['download']	= (isset($data[$value['id']]['download']) && $data[$value['id']]['download'] == 1) ? ' checked="checked"' : '';
			$state['approve']	= (isset($data[$value['id']]['approve']) && $data[$value['id']]['approve'] == 1) ? ' checked="checked"' : '';
			$template.=		'<td align="left" style="padding-left:'.$padding.'px;"><input type="hidden" name="tree['.$value['id'].'][menu_id]" value="'.$value['id'].'">  '.$value['name'].'</td>';
			$template.=		'<td align="center"><input type="checkbox" id="read'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][read]" value="1"'.$state['read'].'></td>';
			$template.=		'<td align="center"><input type="checkbox" id="create'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][create]" value="1"'.$state['create'].'></td>';
			$template.=		'<td align="center"><input type="checkbox" id="update'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][update]" value="1"'.$state['update'].'></td>';
			$template.=		'<td align="center"><input type="checkbox" id="delete'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][delete]" value="1"'.$state['delete'].'></td>';
			$template.=		'<td align="center"><input type="checkbox" id="approve'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][approve]" value="1"'.$state['approve'].'></td>';
			$template.=		'<td align="center"><input type="checkbox" id="download'.$value['id'].'" class="minimal" name="tree['.$value['id'].'][download]" value="1"'.$state['download'].'></td>';
			$template.='</tr>';
		//echo $template;
		return $template;
	}

	function group_menus_access(){
		$CI 			=& get_instance();
		$data_session	= $CI->session->userdata;
		$groupID		= $data_session['ORI_User']['group_id'];

		$ArrMenu	= array();
		if($groupID=='1'){
			$Query	= "SELECT * FROM menus WHERE flag_active='1' ORDER BY parent_id,weight,id ASC";
		}else{
			$Query	= "SELECT menus.* FROM menus INNER JOIN group_menus ON menus.id=group_menus.menu_id WHERE menus.flag_active='1' AND group_menus.group_id='$groupID' ORDER BY menus.parent_id,menus.weight,menus.id ASC";
		}

		$jumlah		= $CI->db->query($Query)->num_rows();

		if($jumlah > 0){
			$hasil		= $CI->db->query($Query)->result_array();

			foreach($hasil as $key=>$val){
				$ArrMenu[$key]['Menu']['id']		= $val['id'];
				$ArrMenu[$key]['Menu']['name']		= $val['name'];
				$ArrMenu[$key]['Menu']['path']		= $val['path'];
				$ArrMenu[$key]['Menu']['parent_id']	= $val['parent_id'];
				$ArrMenu[$key]['Menu']['weight']	= $val['weight'];
				$ArrMenu[$key]['Menu']['icon']		= $val['icon'];
			}
		}
		$Menus	= rebuild_structure($ArrMenu);
		return $Menus;
	}


	//echo"<pre>";print_r($Menus);	exit;


	function rebuild_structure($data){
		$childs = array();

		foreach($data as &$item){
			$childs[$item['Menu']['parent_id']][] = &$item['Menu'];
			unset($item);
		}

		foreach($data as &$item){
			if (isset($childs[$item['Menu']['id']])){
				$item['Menu']['child'] = $childs[$item['Menu']['id']];
				unset($childs[$item['Menu']['id']]);
			}
		}

	//	pr($childs);exit;
	//	menu that has no parent, append it as parent
		if(count($childs) > 0){
			foreach($childs as $key => $child){
				if($key != 0){
					$childs[0][] = $child[0];
					unset($childs[$key]);
				}
			}
		}

		return isset($childs[0]) ? $childs[0] : array();
	}

	function render_left_menus($fixed_structure=array(),$dept=0){
		//if first render echo wrapper
		if($dept==0){
			echo '<ul class="sidebar-menu" id="menu">';
			echo '<li class="header">MAIN NAVIGATION</li>';
		}

		$ses_level3 = (!empty($_SESSION["ses_level3"]))?$_SESSION["ses_level3"]:0;
		$ses_level2 = (!empty($_SESSION["ses_level2"]))?$_SESSION["ses_level2"]:0;
		$ses_level1 = (!empty($_SESSION["ses_level1"]))?$_SESSION["ses_level1"]:0;

		//loop children
		foreach($fixed_structure as $key=>$value){
			// $path=$value['path']==''?'#':base_url().'index.php/'.strtolower($value['path']);
			$path=$value['path']==''?'#':base_url().strtolower($value['path']);
			$icons=$value['icon'];

			if(array_key_exists('child',$value)){
				$MENUKLIK = ($value['path'] == '#')?'':'klikmenu';
				echo'<li id="menu_'.$value['id'].'" class="treeview '.$MENUKLIK.' menu'.$value['id'].'" data-idmenu='.$value['id'].'><a href="'.$path.'"><i class="fa '.$icons.'"></i>'.$value['name'].'<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
				echo('<ul class="treeview-menu ulmenu'.$value['id'].'">');
				render_left_menus($value['child'],$dept+1);
				echo('</ul>');
			}else{
				$MENUKLIK = ($value['path'] == '#')?'':'klikmenu';
				echo'<li id="menu_'.$value['id'].'" class="treeview '.$MENUKLIK.' menu'.$value['id'].'" data-idmenu='.$value['id'].'><a href="'.$path.'"><i class="fa '.$icons.'"></i>'.$value['name'].'</a>';
			}
			echo('</li>');
		}
		if($dept==0)echo('</ul>');
	}

	function group_access($groupID){
		$CI 			=& get_instance();
		$data_session	= $CI->session->userdata;
		// $groupID		= $data_session['ORI_User']['group_id'];
		$MenusAccess	= array();
		$Query	= "SELECT menus.*,group_menus.id AS kode_group,group_menus.read,group_menus.create,group_menus.update,group_menus.delete,group_menus.approve,group_menus.download FROM menus LEFT JOIN group_menus ON menus.id=group_menus.menu_id AND group_menus.group_id='$groupID' WHERE menus.flag_active='1' ORDER BY menus.parent_id,menus.weight,menus.id ASC";
		$jumlah		= $CI->db->query($Query);
		//echo"ono bro ".$jumlah;exit;
		if($jumlah->num_rows() > 0){
			$hasil		= $jumlah->result_array();

			foreach($hasil as $key=>$val){
				if($groupID=='1'){
					$MenusAccess[$val['id']]['read']=1;
					$MenusAccess[$val['id']]['create']=1;
					$MenusAccess[$val['id']]['update']=1;
					$MenusAccess[$val['id']]['delete']=1;
					$MenusAccess[$val['id']]['approve']=1;
					$MenusAccess[$val['id']]['download']=1;
				}else{
					if(isset($val['kode_group']) && $val['kode_group']){
						$MenusAccess[$val['id']]['read']=$val['read'];
						$MenusAccess[$val['id']]['create']=$val['create'];
						$MenusAccess[$val['id']]['update']=$val['update'];
						$MenusAccess[$val['id']]['delete']=$val['delete'];
						$MenusAccess[$val['id']]['approve']=$val['approve'];
						$MenusAccess[$val['id']]['download']=$val['download'];
					}
				}

			}
		}

		return $MenusAccess;
	}

	function reconstruction_tree($parent_id=0,$data=array()){
		$menus=array();
		foreach($data as $key=>$value){
			$index=count($menus);
			if($value['parent_id']==$parent_id){
				$menus[$index]=$value;
				if(count($value) >1){
					$menus[$index]['detail']=$value;
				}
				//unset print
				unset($data[$key]);
				if($child=reconstruction_tree($value['id'],$data)){
					$menus[$index]['child']=$child;
				}
			}
		}
		return $menus;
	}

	function implode_data($data=array(),$key='key'){
		if(strtolower($key)=='key'){
			$det_imp	="";
			foreach($data as $key=>$val){
				if(!empty($det_imp))$det_imp.="','";
				$det_imp	.=$key;
			}
		}else{
			$det_imp	=implode("','",$data);
		}
		return $det_imp;
	}

	function getExtension($str) {

		 $i = strrpos($str,".");
		 if (!$i) { return ""; }

		 $l = strlen($str) - $i;
		 $ext = substr($str,$i+1,$l);
		 return $ext;
	}

	function ImageResizes($data,$location,$NewName=NULL){
		 $CI 			=& get_instance();
		 $image 		= $data["name"];
		 $uploadedfile 	= $data['tmp_name'];
		 $Arr_Return	= array();
		 if ($image){
			$filename 	= stripslashes($data['name']);
			$extension 	= getExtension($filename);
			$extension 	= strtolower($extension);
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
					$Arr_Return	= array(
						'status'	=> 2,
						'pesan'		=> 'File ekstension tidak valid.....'
					);


			}else{
				$size	= filesize($data['tmp_name']);
				// cek image size
				if ($size > (3840*3840))	{
					$Arr_Return	= array(
						'status'	=> 2,
						'pesan'		=> 'Ukuran File terlalu besar......'
					);

				}else{

					if($extension=="jpg" || $extension=="jpeg" ){
						$uploadedfile = $data['tmp_name'];
						$src = imagecreatefromjpeg($uploadedfile);
					}else if($extension=="png"){
						$uploadedfile = $data['tmp_name'];
						$src = imagecreatefrompng($uploadedfile);
					}else {
						$src = imagecreatefromgif($uploadedfile);
					}

					list($width,$height)=getimagesize($uploadedfile);

					$newwidth	= 1024;
					$newheight	= ($height/$width)*$newwidth;
					$tmp		= imagecreatetruecolor($newwidth,$newheight);
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					$uploaddir 	= $inputFileName = './assets/images/'.$location.'/';
					if($NewName){
						$filename = $uploaddir.$NewName.'.'.$extension;
					}else{
						$filename = $uploaddir.$data['name'];
					}
					unlink($filename);
					imagejpeg($tmp,$filename,100);

					imagedestroy($src);
					imagedestroy($tmp);
					$Arr_Return	= array(
						'status'	=> 1,
						'pesan'		=> 'Upload Image Success....'
					);
				}

			}
		}

		return $Arr_Return;
	}

	function group_company(){
		$CI 			=& get_instance();
		$data_session	= $CI->session->userdata;

		$Query			= "SELECT * FROM identitas";

		$jumlah			= $CI->db->query($Query)->num_rows();
		$balik_data		= array();
		if($jumlah > 0){
			$hasil		= $CI->db->query($Query)->result();
			$balik_data	= $hasil[0];
		}

		return $balik_data;
	}

	function back_number($number){
		$dataNumber = number_format(($number * (-1)), 2);

		return $dataNumber;
	}

	function List_Realese(){
		$CI 			=& get_instance();
		$List_Realese	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' ORDER BY nm_material ASC")->result_array();
		return $List_Realese;
	}

	function List_PlasticFirm(){
		$CI 				=& get_instance();
		$List_PlasticFirm	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' AND nm_material NOT LIKE 'PE%' AND nm_material NOT LIKE 'POLYESTER%' ORDER BY nm_material ASC")->result_array();
		return $List_PlasticFirm;
	}

	function List_Veil(){
		$CI 				=& get_instance();
		$List_Veil			= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0003' ORDER BY nm_material ASC")->result_array();
		return $List_Veil;
	}

	function List_Resin(){
		$CI 			=& get_instance();
		$List_Resin		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0001' ORDER BY nm_material ASC")->result_array();
		return $List_Resin;
	}

	function List_MatCsm(){
		$CI 				=& get_instance();
		$List_MatCsm		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0004' ORDER BY nm_material ASC")->result_array();
		return $List_MatCsm;
	}

	function List_MatKatalis(){
		$CI 				=& get_instance();
		$List_MatKatalis	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0002' ORDER BY nm_material ASC")->result_array();
		return $List_MatKatalis;
	}

	function List_MatSm(){
		$CI 				=& get_instance();
		$List_MatSm			= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
		return $List_MatSm;
	}

	function List_MatCobalt(){
		$CI 				=& get_instance();
		$List_MatCobalt		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		return $List_MatCobalt;
	}

	function List_MatDma(){
		$CI 				=& get_instance();
		$List_MatDma		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0021' ORDER BY nm_material ASC")->result_array();
		return $List_MatDma;
	}

	function List_MatHydo(){
		$CI 				=& get_instance();
		$List_MatHydo		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		return $List_MatHydo;
	}

	function List_MatMethanol(){
		$CI 				=& get_instance();
		$List_MatMethanol	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0026' ORDER BY nm_material ASC")->result_array();
		return $List_MatMethanol;
	}

	function List_MatAdditive(){
		$CI 				=& get_instance();
		$List_MatAdditive	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		return $List_MatAdditive;
	}

	function List_MatWR(){
		$CI 			=& get_instance();
		$List_MatWR		= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0006' ORDER BY nm_material ASC")->result_array();
		return $List_MatWR;
	}

	function List_MatRooving(){
		$CI 			=& get_instance();
		$List_MatRooving= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0005' ORDER BY nm_material ASC")->result_array();
		return $List_MatRooving;
	}

	function List_MatColor(){
		$CI 			=& get_instance();
		$List_MatColor	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0007' ORDER BY nm_material ASC")->result_array();
		return $List_MatColor;
	}

	function List_MatTinuvin(){
		$CI 			=& get_instance();
		$List_MatTinuvin= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		return $List_MatTinuvin;
	}

	function List_MatChl(){
		$CI 			=& get_instance();
		$List_MatChl	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		return $List_MatChl;
	}

	function List_MatWax(){
		$CI 			=& get_instance();
		$List_MatWax	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0008' OR id_category='TYP-0019' ORDER BY nm_material ASC")->result_array();
		return $List_MatWax;
	}

	function List_MatMchl(){
		$CI 			=& get_instance();
		$List_MatMchl	= $CI->db->query("SELECT * FROM raw_materials WHERE flag_active='Y' AND id_category='TYP-0024' ORDER BY nm_material ASC")->result_array();
		return $List_MatMchl;
	}

	function Color_status($status){
		$CI 			=& get_instance();
		$sqlColor = "SELECT * FROM color_status WHERE status='".$status."' ";
		$restColor = $CI->db->query($sqlColor)->result_array();
		$hslColor = (!empty($restColor[0]['warna']))?$restColor[0]['warna']:'white';

		return $hslColor;
	}
	
	function Color_status_custom($status, $category){
		$CI 			=& get_instance();
		$sqlColor = "SELECT * FROM color_status_umum WHERE status='".$status."' AND kelompok = '".$category."' ";
		$restColor = $CI->db->query($sqlColor)->result_array();
		$hslColor = (!empty($restColor[0]['warna']))?$restColor[0]['warna']:'white';

		return $hslColor;
	}
	
	function Color_status_custom2($status, $category){
		$CI 			=& get_instance();
		$sqlColor = "SELECT * FROM color_status_umum WHERE id='".$status."' AND kelompok = '".$category."' ";
		$restColor = $CI->db->query($sqlColor)->result_array();
		$hslColor = (!empty($restColor[0]['warna']))?$restColor[0]['warna']:'white';

		return $hslColor;
	}
	
	function Status_status_custom2($status, $category){
		$CI 			=& get_instance();
		$sqlColor = "SELECT * FROM color_status_umum WHERE id='".$status."' AND kelompok = '".$category."' ";
		$restColor = $CI->db->query($sqlColor)->result_array();
		$hslColor = $restColor[0]['status'];

		return $hslColor;
	}
	
	function color_status_purchase($status){
		$CI 		=& get_instance();
		$sqlColor 	= "SELECT * FROM color_status_purchase WHERE status='".$status."' ";
		$restColor 	= $CI->db->query($sqlColor)->result_array();
		$color 		= (!empty($restColor[0]['warna']))?$restColor[0]['warna']:'#a99716';
		$status 	= (!empty($restColor[0]['keterangan']))?$restColor[0]['keterangan']:'WAITING RFQ';
		
		$data	= array(
			'color' 	=> $color,
			'status' 	=> $status,
		);
		
		return $data;
	}

	function SQL_Quo_EditBef($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.id_product,
							a.series,
							a.qty,
							b.parent_product AS id_category,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							a.parent_product,
							b.thickness,
							b.panjang AS length,
							b.angle AS sudut,
							b.type,
							b.pressure,
							b.liner,
							c.persen,
							c.extra,
							i.nego,
							(
								(
								SELECT
									d.direct_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS direct_labour,
							(
								(
								SELECT
									d.indirect_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS indirect_labour,
							(
								(
								SELECT
									d.machine
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS machine,
							(
								(
								SELECT
									d.mould_mandrill
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS mould_mandrill,
							(
								(
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
												IF
													(
														( h.type = 'joint' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
														IF
															(
																( h.type = 'field' ),
																( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
																0
															)
													)
											)
										) * a.sum_mat
									)
							) * a.qty AS consumable,
							(
								(
								SELECT
									d.total
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
									LIMIT 1
									) + (
								IF
									(
										( h.type = 'pipe' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
									IF
										(
											( h.type = 'fitting' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
											IF
												(
													( h.type = 'field' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
													0
												)
											)
										)
									) * a.sum_mat
								)
							) * a.qty AS cost_process,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
							LEFT JOIN product_parent h ON h.product_parent = b.parent_product
							LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_QuoBef($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							b.parent_product AS id_category,
							a.qty,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							b.panjang AS length,
							b.thickness,
							b.angle AS sudut,
							b.type,
							a.id_product,
							b.standart_code,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							b.pressure,
							b.liner,
							(
								(
								SELECT
									d.direct_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS direct_labour,
							(
								(
								SELECT
									d.indirect_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS indirect_labour,
							(
								(
								SELECT
									d.machine
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS machine,
							(
								(
								SELECT
									d.mould_mandrill
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS mould_mandrill,
							(
								(
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
												IF
													(
														( h.type = 'joint' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
														IF
															(
																( h.type = 'field' ),
																( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
																0
															)
													)
											)
										) * a.sum_mat
									)
							) * a.qty AS consumable,
							(
								(
								SELECT
									d.total
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
									LIMIT 1
									) + (
								IF
									(
										( h.type = 'pipe' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
									IF
										(
											( h.type = 'fitting' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
											IF
												(
													( h.type = 'field' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
													0
												)
											)
										)
									) * a.sum_mat
								)
							) * a.qty AS cost_process,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
							LEFT JOIN product_parent h ON h.product_parent = b.parent_product
						WHERE
							b.parent_product <> 'pipe slongsong'
							AND a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_SOBef($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.id_product,
							a.qty,

							a.id,
							a.id_bq_header,
							a.id_delivery,
							a.series,
							a.sub_delivery,
							a.sts_delivery,
							a.no_komponen,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.length,
							a.thickness,
							a.id_standard,
							a.type,
							a.sudut,

							b.parent_product AS id_category,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							a.parent_product,
							b.thickness,
							b.panjang AS length,
							b.angle AS sudut,
							b.type,
							b.pressure,
							b.liner,
							c.persen,
							c.extra,
							i.nego,
							(
								(
								SELECT
									d.direct_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS direct_labour,
							(
								(
								SELECT
									d.indirect_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS indirect_labour,
							(
								(
								SELECT
									d.machine
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS machine,
							(
								(
								SELECT
									d.mould_mandrill
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS mould_mandrill,
							(
								(
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
												IF
													(
														( h.type = 'joint' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
														IF
															(
																( h.type = 'field' ),
																( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
																0
															)
													)
											)
										) * a.sum_mat
									)
							) * a.qty AS consumable,
							(
								(
								SELECT
									d.total
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
									LIMIT 1
									) + (
								IF
									(
										( h.type = 'pipe' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
									IF
										(
											( h.type = 'fitting' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
											IF
												(
													( h.type = 'field' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
													0
												)
											)
										)
									) * a.sum_mat
								)
							) * a.qty AS cost_process,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_bf_estimasi_cost_and_mat a
							INNER JOIN so_product b ON a.id_milik = b.id
							LEFT JOIN product_parent h ON h.product_parent = b.parent_product
							LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_FDBef($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.id_product,
							a.qty,

							a.id,
							a.id_bq_header,
							a.id_delivery,
							a.series,
							a.sub_delivery,
							a.sts_delivery,
							a.no_komponen,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.length,
							a.thickness,
							a.id_standard,
							a.type,
							a.sudut,

							b.parent_product AS id_category,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							a.parent_product,
							b.thickness,
							b.panjang AS length,
							b.angle AS sudut,
							b.type,
							b.pressure,
							b.liner,
							c.persen,
							c.extra,
							i.nego,
							(
								(
								SELECT
									d.direct_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS direct_labour,
							(
								(
								SELECT
									d.indirect_labour
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS indirect_labour,
							(
								(
								SELECT
									d.machine
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS machine,
							(
								(
								SELECT
									d.mould_mandrill
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
								LIMIT 1)
							) * a.qty AS mould_mandrill,
							(
								(
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
												IF
													(
														( h.type = 'joint' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
														IF
															(
																( h.type = 'field' ),
																( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
																0
															)
													)
											)
										) * a.sum_mat
									)
							) * a.qty AS consumable,
							(
								(
								SELECT
									d.total
								FROM
									cost_process_auto d
								WHERE
									d.product_parent = b.parent_product
									AND d.diameter = b.diameter
									AND ( d.diameter2 = b.diameter2 )
									AND d.pn = b.pressure
									AND d.liner = b.liner
									LIMIT 1
									) + (
								IF
									(
										( h.type = 'pipe' ),
										( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
									IF
										(
											( h.type = 'fitting' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
										IF
											(
												( h.type = 'joint' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
											IF
												(
													( h.type = 'field' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
													0
												)
											)
										)
									) * a.sum_mat
								)
							) * a.qty AS cost_process,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									(
									SELECT
										d.total
									FROM
										cost_process_auto d
									WHERE
										d.product_parent = b.parent_product
										AND d.diameter = b.diameter
										AND d.diameter2 = b.diameter2
										AND d.pn = b.pressure
										AND d.liner = b.liner
										LIMIT 1
										) + (
									IF
										(
											( h.type = 'pipe' ),
											( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '3' ) ),
										IF
											(
												( h.type = 'fitting' ),
												( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '4' ) ),
											IF
												(
													( h.type = 'joint' ),
													( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '5' ) ),
												IF
													(
														( h.type = 'field' ),
														( SELECT `cost_process`.`std_rate` FROM `cost_process` WHERE ( `cost_process`.`id` = '8' ) ),
														0
													)
												)
											)
										) * a.sum_mat
									)
								) + a.est_harga
							) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_estimasi_cost_and_mat a
							INNER JOIN bq_product_fd b ON a.id_milik = b.id
							LEFT JOIN product_parent h ON h.product_parent = b.parent_product
							LEFT JOIN cost_project_detail c ON b.id2 = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id2 = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_Quo_Edit($id_bq){
			$SQL_data 		= "SELECT
								a.id_milik,
								a.id_bq,
								a.id_product,
								a.series,
								a.qty,
								b.parent_product AS id_category,
								b.diameter AS diameter_1,
								b.diameter2 AS diameter_2,
								( a.est_harga * a.qty ) AS est_harga2,
								( a.sum_mat * a.qty ) AS sum_mat2,
								a.parent_product,
								b.thickness,
								b.panjang AS length,
								b.angle AS sudut,
								b.type,
								b.pressure,
								b.liner,
								c.persen,
								c.extra,
								(a.direct_labour* a.qty) AS direct_labour,
								(a.indirect_labour* a.qty) AS indirect_labour,
								(a.machine* a.qty) AS machine,
								(a.mould_mandrill* a.qty) AS mould_mandrill,
								(a.consumable* a.qty) AS consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))
								) * a.qty AS cost_process,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
								) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
								) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
								) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
								) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
								(
									((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
								) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
							FROM
								estimasi_cost_and_mat a
								INNER JOIN bq_product b ON a.id_milik = b.id
								LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							WHERE
								a.id_bq = '".$id_bq."'
							ORDER BY
								a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_Quo($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							b.parent_product AS id_category,
							a.qty,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							b.panjang AS length,
							b.thickness,
							b.angle AS sudut,
							b.type,
							a.id_product,
							b.standart_code,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							b.pressure,
							b.liner,
							(a.direct_labour* a.qty) AS direct_labour,
							(a.indirect_labour* a.qty) AS indirect_labour,
							(a.machine* a.qty) AS machine,
							(a.mould_mandrill* a.qty) AS mould_mandrill,
							(a.consumable* a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))
							) * a.qty AS cost_process,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
						WHERE
							b.parent_product <> 'pipe slongsong'
							AND a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_SO($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.id_product,
							a.qty,

							a.id,
							a.id_bq_header,
							a.id_delivery,
							a.series,
							a.sub_delivery,
							a.sts_delivery,
							a.no_komponen,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.length,
							a.thickness,
							a.id_standard,
							a.type,
							a.sudut,

							b.parent_product AS id_category,
							b.diameter AS diameter_1,
							b.diameter2 AS diameter_2,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							a.parent_product,
							b.thickness,
							b.panjang AS length,
							b.angle AS sudut,
							b.type,
							b.pressure,
							b.liner,
							c.persen,
							c.extra,
							i.nego,
							(a.direct_labour* a.qty) AS direct_labour,
							(a.indirect_labour* a.qty) AS indirect_labour,
							(a.machine* a.qty) AS machine,
							(a.mould_mandrill* a.qty) AS mould_mandrill,
							(a.consumable* a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))
							) * a.qty AS cost_process,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_bf_estimasi_cost_and_mat a
							INNER JOIN so_product b ON a.id_milik = b.id
							LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'  AND a.qty <> 0
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SQL_FD($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.id_product,
							a.qty,
							a.id2,
							a.id,
							a.id_bq_header,
							a.id_delivery,
							a.series,
							a.sub_delivery,
							a.sts_delivery,
							a.no_komponen,
							a.id_product,
							a.diameter_1,
							a.diameter_2,
							a.length,
							a.thickness,
							a.id_standard,
							a.type,
							a.sudut,

							a.parent_product AS id_category,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							a.parent_product,
							SUBSTR(a.series, 4, 2) AS pressure,
							SUBSTR(a.series, 7, 4) AS liner,
							c.persen,
							c.extra,
							-- i.nego,
							(a.direct_labour* a.qty) AS direct_labour,
							(a.indirect_labour* a.qty) AS indirect_labour,
							(a.machine* a.qty) AS machine,
							(a.mould_mandrill* a.qty) AS mould_mandrill,
							(a.consumable* a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))
							) * a.qty AS cost_process,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_estimasi_cost_and_mat_fast a
							INNER JOIN bq_product_fd b ON a.id_milik = b.id
							LEFT JOIN cost_project_detail c ON b.id2 = c.caregory_sub
							-- LEFT JOIN cost_project_detail_sales i ON b.id2 = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."' AND a.qty <> 0
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function SUM_Quo_Material($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= SQL_Quo_Edit($id_bq);
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$NegoPersen = (!empty($valx['nego']))?'0':'0';
			$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
			$extra 		= (!empty($valx['extra']))?$valx['extra']:15;

			$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
			$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
			$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
			$nego		= $HrgTot * ($NegoPersen/100);
			$dataSum	= $HrgTot + $nego;

			$SUM 		+= $dataSum;
		}
		return $SUM;
	}

	function SUM_SO_Material($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= SQL_SO_FAST($id_bq);
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$NegoPersen = (!empty($valx['nego']))?'0':'0';
			$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
			$extra 		= (!empty($valx['extra']))?$valx['extra']:15;

			$est_harga = 0;
			$dataSum = 0;
			if($valx['qty'] <> 0){
				$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
				$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
				$nego		= $HrgTot * ($NegoPersen/100);
				$dataSum	= $HrgTot + $nego;
			}

			$SUM 		+= $dataSum;
		}
		return $SUM;
	}

	function SUM_SO_Material2($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= SQL_SO_FAST($id_bq);
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$SUM 		+= $valx['sum_mat2'];
		}
		return $SUM;
	}

	function SUM_FD_Material($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= SQL_FD_FAST($id_bq);
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$NegoPersen = (!empty($valx['nego']))?'0':'0';
			$persen 	= (!empty($valx['persen']))?$valx['persen']:30;
			$extra 		= (!empty($valx['extra']))?$valx['extra']:15;

			$est_harga = (($valx['est_harga2']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
			$HrgTot2   	= (($est_harga) + ($est_harga * ($persen/100))) * $valx['qty'];
			$HrgTot  	= (($HrgTot2) + ($HrgTot2 * ($extra/100)));
			$nego		= $HrgTot * ($NegoPersen/100);
			$dataSum	= $HrgTot + $nego;

			$SUM 		+= $dataSum;
		}
		return $SUM;
	}

	function SUM_EX_Material($id_bq){
		$CI 		=& get_instance();
		//get Enggenering Cost
		$eng 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y'";
		$getENg		= $CI->db->query($eng)->result();
		//get Packing Cost
		$pack 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'packing' AND b.id_bq='".$id_bq."'";
		$getPack	= $CI->db->query($pack)->result();
		//get Tructing Export
		$export 	= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y'";
		$getExport	= $CI->db->query($export)->result();
		//get Tracking Lokal
		$lokal 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'lokal' AND b.id_bq='".$id_bq."'";
		$getLokal	= $CI->db->query($lokal)->result();
		//get Material
		$mat 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.id_bq='".$id_bq."' AND (b.category='aksesoris' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')";
		$getMat		= $CI->db->query($mat)->result();

		$Sum_EX		= ($getENg[0]->price_total) + ($getPack[0]->price_total) + ($getExport[0]->price_total) + ($getLokal[0]->price_total) + ($getMat[0]->price_total);

		return $Sum_EX;
	}
	
	function SUM_EX_Material_SO($id_bq){
		$CI 		=& get_instance();
		//get Enggenering Cost
		$eng 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'engine' AND b.id_bq='".$id_bq."' AND b.option_type='Y'";
		$getENg		= $CI->db->query($eng)->result();
		//get Packing Cost
		$pack 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'packing' AND b.id_bq='".$id_bq."'";
		$getPack	= $CI->db->query($pack)->result();
		//get Tructing Export
		$export 	= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'export' AND b.id_bq='".$id_bq."' AND b.option_type='Y'";
		$getExport	= $CI->db->query($export)->result();
		//get Tracking Lokal
		$lokal 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b WHERE b.category = 'lokal' AND b.id_bq='".$id_bq."'";
		$getLokal	= $CI->db->query($lokal)->result();
		//get Material
		$mat 		= "SELECT SUM(b.price_total) AS price_total FROM cost_project_detail b LEFT JOIN so_bf_acc_and_mat c ON b.id_milik = c.id_milik WHERE b.id_bq='".$id_bq."' AND c.id_milik IS NOT NULL AND (b.category='aksesoris' OR b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya')";
		$getMat		= $CI->db->query($mat)->result();

		$Sum_EX		= ($getENg[0]->price_total) + ($getPack[0]->price_total) + ($getExport[0]->price_total) + ($getLokal[0]->price_total) + ($getMat[0]->price_total);

		return $Sum_EX;
	}

	function SUM_SO_ALL($id_bq){
		$SUM_ALL = SUM_SO_Material($id_bq) + SUM_EX_Material_SO($id_bq);
		return $SUM_ALL;
	}

	function SUM_SO_MATERIAL_WEIGHT($id_bq){
		$SUM_ALL = SUM_SO_Material2($id_bq);
		return $SUM_ALL;
	}

	function SUM_QUO_ALL($id_bq){
		$SUM_ALL = SUM_Quo_Material($id_bq) + SUM_EX_Material($id_bq);
		return $SUM_ALL;
	}

	function SUM_FD_ALL($id_bq){
		$SUM_ALL = SUM_FD_Material($id_bq) + SUM_EX_Material_SO($id_bq);
		return $SUM_ALL;
	}

	function SQL_SO_FAST($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.qty,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							c.persen,
							c.extra,
							(a.direct_labour* a.qty) AS direct_labour,
							(a.indirect_labour* a.qty) AS indirect_labour,
							(a.machine* a.qty) AS machine,
							(a.mould_mandrill* a.qty) AS mould_mandrill,
							(a.consumable* a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( a.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_bf_estimasi_cost_and_mat_fast a
							LEFT JOIN cost_project_detail c ON a.id_milik = c.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
								AND a.qty <> 0";
		return $SQL_data;
	}
	
	function SQL_FD_FAST($id_bq){
		$SQL_data 		= "SELECT
							a.id_milik,
							a.id_bq,
							a.qty,
							( a.est_harga * a.qty ) AS est_harga2,
							( a.sum_mat * a.qty ) AS sum_mat2,
							c.persen,
							c.extra,
							(a.direct_labour* a.qty) AS direct_labour,
							(a.indirect_labour* a.qty) AS indirect_labour,
							(a.machine* a.qty) AS machine,
							(a.mould_mandrill* a.qty) AS mould_mandrill,
							(a.consumable* a.qty) AS consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))
							) * a.qty AS cost_process,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan
						FROM
							so_estimasi_cost_and_mat a
							INNER JOIN bq_product_fd b ON a.id_milik = b.id
							LEFT JOIN cost_project_detail c ON b.id2 = c.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."' AND a.qty <> 0
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}

	function Allowance($id_bq){
		$CI 		=& get_instance();
		$qMatr 		= "SELECT
							(b.fumigasi * a.qty) AS cost_satuan,
							(b.price / c.qty) * a.qty AS cost_profit,
							(b.price_total / c.qty) * a.qty AS cost_allow
						FROM
							so_bf_detail_header a 
								LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
								LEFT JOIN bq_detail_header c ON a.id_milik=c.id
						WHERE
							a.id_bq = '".$id_bq."'";
		$getDetail	= $CI->db->query($qMatr)->result_array();

		$SUM_AWAL 	= 0;
		$PROFIT 	= 0;
		foreach($getDetail AS $val => $valx){
			
			$SUM_AWAL 	+= $valx['cost_allow'];
			$PROFIT	 	+= $valx['cost_profit'];
		}

		return $SUM_AWAL - $PROFIT;
	}

	function Profit($id_bq){
		$CI 		=& get_instance();
		$qMatr 		= "SELECT
							(b.fumigasi * a.qty) AS cost_satuan,
							(b.price / c.qty) * a.qty AS cost_profit,
							(b.price_total / c.qty) * a.qty AS cost_allow
						FROM
							so_bf_detail_header a 
								LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
								LEFT JOIN bq_detail_header c ON a.id_milik=c.id
						WHERE
							a.id_bq = '".$id_bq."'";
		$getDetail	= $CI->db->query($qMatr)->result_array();

		$SUM_AWAL 	= 0;
		$SATUAN 	= 0;
		foreach($getDetail AS $val => $valx){
			
			$SUM_AWAL 	+= $valx['cost_profit'];
			$SATUAN	 	+= $valx['cost_satuan'];
		}

		return $SUM_AWAL - $SATUAN;
	}

	function spec_master($id_product){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM component_header WHERE id_product='".$id_product."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['parent_product'];

		if($restHeader[0]['parent_product'] == 'pipe' OR $restHeader[0]['parent_product'] == 'pipe slongsong'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'elbow mitter' OR $restHeader[0]['parent_product'] == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']).", ".$restHeader[0]['type_elbow']." ".floatval($restHeader[0]['angle']);
		}
		elseif($restHeader[0]['parent_product'] == 'concentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee mould' OR $restHeader[0]['parent_product'] == 'eccentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee slongsong' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong' OR $restHeader[0]['parent_product'] == 'end cap' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'equal tee mould' OR $restHeader[0]['parent_product'] == 'blind flange' OR $restHeader[0]['parent_product'] == 'equal tee slongsong' OR $restHeader[0]['parent_product'] == 'spectacle blind' OR $restHeader[0]['parent_product'] == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'shop joint' || $restHeader[0]['parent_product'] == 'saddle' ){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang']);
		}
		elseif(	
					$parent_cat == 'inlet cone' || 
					$parent_cat == 'taper plate' ||
					$parent_cat == 'rib taper plate' ||
					$parent_cat == 'end plate' ||
					$parent_cat == 'rib end plate' ||
					$parent_cat == 'square flange' ||
					$parent_cat == 'joint saddle' ||
					$parent_cat == 'bellmouth' || 
					$restHeader[0]['parent_product'] == 'plate' || 
					$restHeader[0]['parent_product'] == 'puddle flange' || 
					$restHeader[0]['parent_product'] == 'rib' || 
					$restHeader[0]['parent_product'] == 'joint rib' || 
					$restHeader[0]['parent_product'] == 'support' || 
					$restHeader[0]['parent_product'] == 'spectacle blind' || 
					$restHeader[0]['parent_product'] == 'spacer' || 
					$restHeader[0]['parent_product'] == 'spacer ring' || 
					$restHeader[0]['parent_product'] == 'loose flange' || 
					$restHeader[0]['parent_product'] == 'blind spacer' || 
					$restHeader[0]['parent_product'] == 'joint puddle flange' || 
					$restHeader[0]['parent_product'] == 'blind flange with hole' || 
					$restHeader[0]['parent_product'] == 'laminate pad' || 
					$restHeader[0]['parent_product'] == 'handle' ||
					$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
					$parent_cat == 'nexus' || 
					$parent_cat == 'csm' || 
					$parent_cat == 'woven roving' || 
					$parent_cat == 'resin' || 
					$parent_cat == 'sic powder' || 
					$parent_cat == 'katalis' || 
					$parent_cat == 'accelator' || 
					$parent_cat == 'putty' || 
					$parent_cat == 'veil' || 
					$parent_cat == 'resin top coat' || 
					$parent_cat == 'build up penebalan' || 
					$parent_cat == 'penebalan mandril' || 
					$parent_cat == 'lining flange' || 
					$parent_cat == 'joint square flange depan 8 mm' || 
					$parent_cat == 'joint square flange belakang 6 mm' || 
					$parent_cat == 'oval flange' || 
					$parent_cat == 'joint oval flange belakang 6 mm' || 
					$parent_cat == 'joint oval flange depan 8 mm' || 
					$parent_cat == 'shimplate 2mm' || 
					$parent_cat == 'shimplate 3mm' || 
					$parent_cat == 'shimplate 5mm' || 
					$parent_cat == 'joint end plate' || 
					$parent_cat == 'joint taper plate' || 
					$parent_cat == 'joint flange' || 
					$parent_cat == 'flange fuji resin' ||
					$parent_cat == 'proses acs' ||
					$parent_cat == 'nozzle holder' ||
					$parent_cat == 'lining' ||
					$parent_cat == 'waterproof plate' ||
					$parent_cat == 'joint waterproof' ||
					$parent_cat == 'blind plate' ||
					$parent_cat == 'y tee' ||
					$parent_cat == 'sudden reducer' ||
					$parent_cat == 'joint sudden reducer' ||
					$parent_cat == 'manhole' ||
					$parent_cat == 'dummy support' ||
					$parent_cat == 'lining coupling' ||
					$parent_cat == 'plate assy' ||
					$parent_cat == 'abr end cover' ||
					$parent_cat == 'abr cover' ||
					$parent_cat == 'lining elbow' ||
					$parent_cat == 'damper' ||
					$parent_cat == 'additional accessories' ||
					$parent_cat == 'cross tee' ||
					$parent_cat == 'horn mouth' ||
					$parent_cat == 'joint plate' ||
					$parent_cat == 'lateral tee' ||
					$parent_cat == 'lining colar' ||
					$parent_cat == 'manhole cover' ||
					$parent_cat == 'mold cover' ||
					$parent_cat == 'orifice plate' ||
					$parent_cat == 'pipe support' ||
					$parent_cat == 'reinforce saddle' ||
					$parent_cat == 'rod' ||
					$parent_cat == 'stiffening ring' ||
					$parent_cat == 'tinuvin solution' ||
					$parent_cat == 'vortex breaker' ||
					$parent_cat == 'inlet cover' ||
					$parent_cat == 'joint spacer' ||
					$parent_cat == 'elbow 5d' ||
					$parent_cat == 'orifice' ||
					$parent_cat == 'lining concrete' ||
					$parent_cat == 'joint nozzle'
				){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}
		elseif($restHeader[0]['parent_product'] == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter2'])." x A ".floatval($restHeader[0]['diameter']);
		}
		elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}

		return $dim;
	}

	function spec_master_upload($id_product){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM upload_component_header WHERE id_product='".$id_product."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['parent_product'];

		if($restHeader[0]['parent_product'] == 'pipe' OR $restHeader[0]['parent_product'] == 'pipe slongsong'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'elbow mitter' OR $restHeader[0]['parent_product'] == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']).", ".$restHeader[0]['type_elbow']." ".floatval($restHeader[0]['angle']);
		}
		elseif($restHeader[0]['parent_product'] == 'concentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee mould' OR $restHeader[0]['parent_product'] == 'eccentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee slongsong' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong' OR $restHeader[0]['parent_product'] == 'end cap' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'equal tee mould' OR $restHeader[0]['parent_product'] == 'blind flange' OR $restHeader[0]['parent_product'] == 'equal tee slongsong' OR $restHeader[0]['parent_product'] == 'spectacle blind' OR $restHeader[0]['parent_product'] == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'shop joint' || $restHeader[0]['parent_product'] == 'saddle' ){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang']);
		}
		elseif(	
					$parent_cat == 'inlet cone' || 
					$parent_cat == 'taper plate' ||
					$parent_cat == 'rib taper plate' ||
					$parent_cat == 'end plate' ||
					$parent_cat == 'rib end plate' ||
					$parent_cat == 'square flange' ||
					$parent_cat == 'joint saddle' ||
					$parent_cat == 'bellmouth' || 
					$restHeader[0]['parent_product'] == 'plate' || 
					$restHeader[0]['parent_product'] == 'puddle flange' || 
					$restHeader[0]['parent_product'] == 'rib' || 
					$restHeader[0]['parent_product'] == 'joint rib' || 
					$restHeader[0]['parent_product'] == 'support' || 
					$restHeader[0]['parent_product'] == 'spectacle blind' || 
					$restHeader[0]['parent_product'] == 'spacer' || 
					$restHeader[0]['parent_product'] == 'spacer ring' || 
					$restHeader[0]['parent_product'] == 'loose flange' || 
					$restHeader[0]['parent_product'] == 'blind spacer' || 
					$restHeader[0]['parent_product'] == 'joint puddle flange' || 
					$restHeader[0]['parent_product'] == 'blind flange with hole' || 
					$restHeader[0]['parent_product'] == 'laminate pad' || 
					$restHeader[0]['parent_product'] == 'handle' ||
					$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
					$parent_cat == 'nexus' || 
					$parent_cat == 'csm' || 
					$parent_cat == 'woven roving' || 
					$parent_cat == 'resin' || 
					$parent_cat == 'sic powder' || 
					$parent_cat == 'katalis' || 
					$parent_cat == 'accelator' || 
					$parent_cat == 'putty' || 
					$parent_cat == 'veil' || 
					$parent_cat == 'resin top coat' || 
					$parent_cat == 'build up penebalan' || 
					$parent_cat == 'penebalan mandril' || 
					$parent_cat == 'lining flange' || 
					$parent_cat == 'joint square flange depan 8 mm' || 
					$parent_cat == 'joint square flange belakang 6 mm' || 
					$parent_cat == 'oval flange' || 
					$parent_cat == 'joint oval flange belakang 6 mm' || 
					$parent_cat == 'joint oval flange depan 8 mm' || 
					$parent_cat == 'shimplate 2mm' || 
					$parent_cat == 'shimplate 3mm' || 
					$parent_cat == 'shimplate 5mm' || 
					$parent_cat == 'joint end plate' || 
					$parent_cat == 'joint taper plate' || 
					$parent_cat == 'joint flange' || 
					$parent_cat == 'flange fuji resin' ||
					$parent_cat == 'proses acs' ||
					$parent_cat == 'nozzle holder' ||
					$parent_cat == 'lining' ||
					$parent_cat == 'waterproof plate' ||
					$parent_cat == 'joint waterproof' ||
					$parent_cat == 'blind plate' ||
					$parent_cat == 'y tee' ||
					$parent_cat == 'sudden reducer' ||
					$parent_cat == 'joint sudden reducer' ||
					$parent_cat == 'manhole' ||
					$parent_cat == 'dummy support' ||
					$parent_cat == 'lining coupling' ||
					$parent_cat == 'plate assy' ||
					$parent_cat == 'abr end cover' ||
					$parent_cat == 'abr cover' ||
					$parent_cat == 'lining elbow' ||
					$parent_cat == 'damper' ||
					$parent_cat == 'additional accessories' ||
					$parent_cat == 'cross tee' ||
					$parent_cat == 'horn mouth' ||
					$parent_cat == 'joint plate' ||
					$parent_cat == 'lateral tee' ||
					$parent_cat == 'lining colar' ||
					$parent_cat == 'manhole cover' ||
					$parent_cat == 'mold cover' ||
					$parent_cat == 'orifice plate' ||
					$parent_cat == 'pipe support' ||
					$parent_cat == 'reinforce saddle' ||
					$parent_cat == 'rod' ||
					$parent_cat == 'stiffening ring' ||
					$parent_cat == 'tinuvin solution' ||
					$parent_cat == 'vortex breaker' ||
					$parent_cat == 'inlet cover' ||
					$parent_cat == 'joint spacer' ||
					$parent_cat == 'elbow 5d' ||
					$parent_cat == 'orifice' ||
					$parent_cat == 'lining concrete' ||
					$parent_cat == 'joint nozzle'
				){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}
		elseif($restHeader[0]['parent_product'] == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter2'])." x A ".floatval($restHeader[0]['diameter']);
		}
		elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}

		return $dim;
	}

	function spec_bq($id){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM bq_detail_header WHERE id='".$id."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		if(!empty($restHeader)){
			$parent_cat		= $restHeader[0]['id_category'];

			$qPanjang		= "SELECT panjang FROM bq_component_header WHERE id_milik='".$id."' LIMIT 1";
			$restPanjang		= $CI->db->query($qPanjang)->result_array();

			$panjang = (!empty($restPanjang))?$restPanjang[0]['panjang']:$restHeader[0]['length'];

			if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
			}
			elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang)." x ".floatval($restHeader[0]['thickness']);
			}
			elseif(
						$parent_cat == 'inlet cone' || 
						$parent_cat == 'taper plate' ||
						$parent_cat == 'rib taper plate' ||
						$parent_cat == 'end plate' ||
						$parent_cat == 'rib end plate' ||
						$parent_cat == 'square flange' ||
						$parent_cat == 'joint saddle' ||
						$parent_cat == 'bellmouth' || 
						$parent_cat == 'plate' || 
						$parent_cat == 'puddle flange' || 
						$parent_cat == 'rib' || 
						$parent_cat == 'joint rib' || 
						$parent_cat == 'support' || 
						$parent_cat == 'spectacle blind' || 
						$parent_cat == 'spacer' || 
						$parent_cat == 'spacer ring' || 
						$parent_cat == 'loose flange' || 
						$parent_cat == 'blind spacer' || 
						$parent_cat == 'joint puddle flange' || 
						$parent_cat == 'blind flange with hole' || 
						$parent_cat == 'laminate pad' || 
						$parent_cat == 'handle' ||
						$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
						$parent_cat == 'nexus' || 
						$parent_cat == 'csm' || 
						$parent_cat == 'woven roving' || 
						$parent_cat == 'resin' || 
						$parent_cat == 'sic powder' || 
						$parent_cat == 'katalis' || 
						$parent_cat == 'accelator' || 
						$parent_cat == 'putty' || 
						$parent_cat == 'veil' || 
						$parent_cat == 'resin top coat' || 
						$parent_cat == 'build up penebalan' || 
						$parent_cat == 'penebalan mandril' || 
						$parent_cat == 'lining flange' || 
						$parent_cat == 'joint square flange depan 8 mm' || 
						$parent_cat == 'joint square flange belakang 6 mm' || 
						$parent_cat == 'oval flange' || 
						$parent_cat == 'joint oval flange belakang 6 mm' || 
						$parent_cat == 'joint oval flange depan 8 mm' || 
						$parent_cat == 'shimplate 2mm' || 
						$parent_cat == 'shimplate 3mm' || 
						$parent_cat == 'shimplate 5mm' || 
						$parent_cat == 'joint end plate' || 
						$parent_cat == 'joint taper plate' || 
						$parent_cat == 'joint flange' || 
						$parent_cat == 'flange fuji resin' ||
						$parent_cat == 'proses acs' ||
						$parent_cat == 'nozzle holder' ||
						$parent_cat == 'lining' ||
						$parent_cat == 'waterproof plate' ||
						$parent_cat == 'joint waterproof' ||
						$parent_cat == 'blind plate' ||
						$parent_cat == 'y tee' ||
						$parent_cat == 'sudden reducer' ||
						$parent_cat == 'joint sudden reducer' ||
						$parent_cat == 'manhole' ||
						$parent_cat == 'dummy support' ||
						$parent_cat == 'lining coupling' ||
						$parent_cat == 'plate assy' ||
						$parent_cat == 'abr end cover' ||
						$parent_cat == 'abr cover' ||
						$parent_cat == 'lining elbow' ||
						$parent_cat == 'damper' ||
						$parent_cat == 'additional accessories' ||
						$parent_cat == 'cross tee' ||
						$parent_cat == 'horn mouth' ||
						$parent_cat == 'joint plate' ||
						$parent_cat == 'lateral tee' ||
						$parent_cat == 'lining colar' ||
						$parent_cat == 'manhole cover' ||
						$parent_cat == 'mold cover' ||
						$parent_cat == 'orifice plate' ||
						$parent_cat == 'pipe support' ||
						$parent_cat == 'reinforce saddle' ||
						$parent_cat == 'rod' ||
						$parent_cat == 'stiffening ring' ||
						$parent_cat == 'tinuvin solution' ||
						$parent_cat == 'vortex breaker' ||
						$parent_cat == 'inlet cover' ||
						$parent_cat == 'joint spacer' ||
						$parent_cat == 'elbow 5d' ||
						$parent_cat == 'orifice' ||
						$parent_cat == 'lining concrete' ||
						$parent_cat == 'joint nozzle'
					){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'figure 8'){
				$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
			}
			elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			else{
				// $dim = "belum di set";
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}

			return $dim;
		}
	}
	
	function spec_bq2($id){
		$CI 		=& get_instance();
		$dim = 'not found (old ipp)';
		$restHeader		= $CI->db->get_where('so_detail_header',array('id'=>$id))->result_array();
		if(!empty($restHeader)){
			$parent_cat		= $restHeader[0]['id_category'];
			$restPanjang	= $CI->db->select('panjang')->limit(1)->get_where('so_component_header',array('id_milik'=>$id))->result_array();

			$panjang = (!empty($restPanjang))?$restPanjang[0]['panjang']:$restHeader[0]['length'];

			if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
			}
			elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang)." x ".floatval($restHeader[0]['thickness']);
			}
			elseif(
					$parent_cat == 'inlet cone' || 
					$parent_cat == 'taper plate' ||
					$parent_cat == 'rib taper plate' ||
					$parent_cat == 'end plate' ||
					$parent_cat == 'rib end plate' ||
					$parent_cat == 'square flange' ||
					$parent_cat == 'joint saddle' ||
					$parent_cat == 'bellmouth' || 
					$parent_cat == 'plate' || 
					$parent_cat == 'puddle flange' || 
					$parent_cat == 'rib' || 
					$parent_cat == 'joint rib' || 
					$parent_cat == 'support' || 
					$parent_cat == 'spectacle blind' || 
					$parent_cat == 'spacer' || 
					$parent_cat == 'spacer ring' || 
					$parent_cat == 'loose flange' || 
					$parent_cat == 'blind spacer' || 
					$parent_cat == 'joint puddle flange' || 
					$parent_cat == 'blind flange with hole' || 
					$parent_cat == 'laminate pad' || 
					$parent_cat == 'handle' ||
					$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
					$parent_cat == 'nexus' || 
					$parent_cat == 'csm' || 
					$parent_cat == 'woven roving' || 
					$parent_cat == 'resin' || 
					$parent_cat == 'sic powder' || 
					$parent_cat == 'katalis' || 
					$parent_cat == 'accelator' || 
					$parent_cat == 'putty' || 
					$parent_cat == 'veil' || 
					$parent_cat == 'resin top coat' || 
					$parent_cat == 'build up penebalan' || 
					$parent_cat == 'penebalan mandril' || 
					$parent_cat == 'lining flange' || 
					$parent_cat == 'joint square flange depan 8 mm' || 
					$parent_cat == 'joint square flange belakang 6 mm' || 
					$parent_cat == 'oval flange' || 
					$parent_cat == 'joint oval flange belakang 6 mm' || 
					$parent_cat == 'joint oval flange depan 8 mm' || 
					$parent_cat == 'shimplate 2mm' || 
					$parent_cat == 'shimplate 3mm' || 
					$parent_cat == 'shimplate 5mm' || 
					$parent_cat == 'joint end plate' || 
					$parent_cat == 'joint taper plate' || 
					$parent_cat == 'joint flange' || 
					$parent_cat == 'flange fuji resin' ||
					$parent_cat == 'proses acs' ||
					$parent_cat == 'nozzle holder' ||
					$parent_cat == 'lining' ||
					$parent_cat == 'waterproof plate' ||
					$parent_cat == 'joint waterproof' ||
					$parent_cat == 'blind plate' ||
					$parent_cat == 'y tee' ||
					$parent_cat == 'sudden reducer' ||
					$parent_cat == 'joint sudden reducer' ||
					$parent_cat == 'manhole' ||
					$parent_cat == 'dummy support' ||
					$parent_cat == 'lining coupling' ||
					$parent_cat == 'plate assy' ||
					$parent_cat == 'abr end cover' ||
					$parent_cat == 'abr cover' ||
					$parent_cat == 'lining elbow' ||
					$parent_cat == 'damper' ||
					$parent_cat == 'additional accessories' ||
					$parent_cat == 'cross tee' ||
					$parent_cat == 'horn mouth' ||
					$parent_cat == 'joint plate' ||
					$parent_cat == 'lateral tee' ||
					$parent_cat == 'lining colar' ||
					$parent_cat == 'manhole cover' ||
					$parent_cat == 'mold cover' ||
					$parent_cat == 'orifice plate' ||
					$parent_cat == 'pipe support' ||
					$parent_cat == 'reinforce saddle' ||
					$parent_cat == 'rod' ||
					$parent_cat == 'stiffening ring' ||
					$parent_cat == 'tinuvin solution' ||
					$parent_cat == 'vortex breaker' ||
					$parent_cat == 'inlet cover' ||
					$parent_cat == 'joint spacer' ||
					$parent_cat == 'elbow 5d' ||
					$parent_cat == 'orifice' ||
					$parent_cat == 'lining concrete' ||
					$parent_cat == 'joint nozzle'
			){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'figure 8'){
				$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
			}
			elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			else{
				// $dim = "belum di set";
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
		}
		return $dim;
	}

	function spec_bq3($id){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM so_detail_header WHERE id='".$id."'";
		$dim = 'not found (old ipp)';
		$restHeader		= $CI->db->query($qHeader)->result_array();
		if(!empty($restHeader)){
			$parent_cat		= $restHeader[0]['id_category'];

			$qPanjang		= "SELECT panjang FROM so_component_header WHERE id_milik='".$id."' LIMIT 1";
			$restPanjang		= $CI->db->query($qPanjang)->result_array();

			$panjang = (!empty($restPanjang))?$restPanjang[0]['panjang']:$restHeader[0]['length'];

			if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'saddle'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
			}
			elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang)." x ".floatval($restHeader[0]['thickness']);
			}
			elseif(
					$parent_cat == 'inlet cone' || 
					$parent_cat == 'taper plate' ||
					$parent_cat == 'rib taper plate' ||
					$parent_cat == 'end plate' ||
					$parent_cat == 'rib end plate' ||
					$parent_cat == 'square flange' ||
					$parent_cat == 'joint saddle' ||
					$parent_cat == 'bellmouth' || 
					$parent_cat == 'plate' || 
					$parent_cat == 'puddle flange' || 
					$parent_cat == 'rib' || 
					$parent_cat == 'joint rib' || 
					$parent_cat == 'support' || 
					$parent_cat == 'spectacle blind' || 
					$parent_cat == 'spacer' || 
					$parent_cat == 'spacer ring' || 
					$parent_cat == 'loose flange' || 
					$parent_cat == 'blind spacer' || 
					$parent_cat == 'joint puddle flange' || 
					$parent_cat == 'blind flange with hole' || 
					$parent_cat == 'laminate pad' || 
					$parent_cat == 'handle' ||
					$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
					$parent_cat == 'nexus' || 
					$parent_cat == 'csm' || 
					$parent_cat == 'woven roving' || 
					$parent_cat == 'resin' || 
					$parent_cat == 'sic powder' || 
					$parent_cat == 'katalis' || 
					$parent_cat == 'accelator' || 
					$parent_cat == 'putty' || 
					$parent_cat == 'veil' || 
					$parent_cat == 'resin top coat' || 
					$parent_cat == 'build up penebalan' || 
					$parent_cat == 'penebalan mandril' || 
					$parent_cat == 'lining flange' || 
					$parent_cat == 'joint square flange depan 8 mm' || 
					$parent_cat == 'joint square flange belakang 6 mm' || 
					$parent_cat == 'oval flange' || 
					$parent_cat == 'joint oval flange belakang 6 mm' || 
					$parent_cat == 'joint oval flange depan 8 mm' || 
					$parent_cat == 'shimplate 2mm' || 
					$parent_cat == 'shimplate 3mm' || 
					$parent_cat == 'shimplate 5mm' || 
					$parent_cat == 'joint end plate' || 
					$parent_cat == 'joint taper plate' || 
					$parent_cat == 'joint flange' || 
					$parent_cat == 'flange fuji resin' ||
					$parent_cat == 'proses acs' ||
					$parent_cat == 'nozzle holder' ||
					$parent_cat == 'lining' ||
					$parent_cat == 'waterproof plate' ||
					$parent_cat == 'joint waterproof' ||
					$parent_cat == 'blind plate' ||
					$parent_cat == 'y tee' ||
					$parent_cat == 'sudden reducer' ||
					$parent_cat == 'joint sudden reducer' ||
					$parent_cat == 'manhole' ||
					$parent_cat == 'dummy support' ||
					$parent_cat == 'lining coupling' ||
					$parent_cat == 'plate assy' ||
					$parent_cat == 'abr end cover' ||
					$parent_cat == 'abr cover' ||
					$parent_cat == 'lining elbow' ||
					$parent_cat == 'damper' ||
					$parent_cat == 'additional accessories' ||
					$parent_cat == 'cross tee' ||
					$parent_cat == 'horn mouth' ||
					$parent_cat == 'joint plate' ||
					$parent_cat == 'lateral tee' ||
					$parent_cat == 'lining colar' ||
					$parent_cat == 'manhole cover' ||
					$parent_cat == 'mold cover' ||
					$parent_cat == 'orifice plate' ||
					$parent_cat == 'pipe support' ||
					$parent_cat == 'reinforce saddle' ||
					$parent_cat == 'rod' ||
					$parent_cat == 'stiffening ring' ||
					$parent_cat == 'tinuvin solution' ||
					$parent_cat == 'vortex breaker' ||
					$parent_cat == 'inlet cover' ||
					$parent_cat == 'joint spacer' ||
					$parent_cat == 'elbow 5d' ||
					$parent_cat == 'orifice' ||
					$parent_cat == 'lining concrete' ||
					$parent_cat == 'joint nozzle'
			){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'figure 8'){
				$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
			}
			elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			else{
				// $dim = "belum di set";
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
		}
		return $dim;
	}

	function spec_fd($id, $table){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM ".$table." WHERE id='".$id."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['id_category'];

		$qPanjang		= "SELECT panjang, panjang_neck_1 FROM so_component_header WHERE id_milik='".$id."' LIMIT 1";
		$restPanjang		= $CI->db->query($qPanjang)->result_array();

		$panjang = (!empty($restPanjang))?$restPanjang[0]['panjang']:$restHeader[0]['length'];

		$panjang_n1 = (!empty($restPanjang))?$restPanjang[0]['panjang_neck_1']:$restHeader[0]['length'];

		if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
		}
		elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'end cap'  OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang)." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' ){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang_n1)." x ".floatval($restHeader[0]['thickness']);
		}
		elseif(
				$parent_cat == 'inlet cone' || 
				$parent_cat == 'taper plate' ||
				$parent_cat == 'rib taper plate' ||
				$parent_cat == 'end plate' ||
				$parent_cat == 'rib end plate' ||
				$parent_cat == 'square flange' ||
				$parent_cat == 'joint saddle' ||
				$parent_cat == 'bellmouth' || 
				$parent_cat == 'plate' || 
				$parent_cat == 'puddle flange' || 
				$parent_cat == 'rib' || 
				$parent_cat == 'joint rib' || 
				$parent_cat == 'support' || 
				$parent_cat == 'spectacle blind' || 
				$parent_cat == 'spacer' || 
				$parent_cat == 'spacer ring' || 
				$parent_cat == 'loose flange' || 
				$parent_cat == 'blind spacer' || 
				$parent_cat == 'joint puddle flange' || 
				$parent_cat == 'blind flange with hole' || 
				$parent_cat == 'laminate pad' || 
				$parent_cat == 'handle' ||
				$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
				$parent_cat == 'nexus' || 
				$parent_cat == 'csm' || 
				$parent_cat == 'woven roving' || 
				$parent_cat == 'resin' || 
				$parent_cat == 'sic powder' || 
				$parent_cat == 'katalis' || 
				$parent_cat == 'accelator' || 
				$parent_cat == 'putty' || 
				$parent_cat == 'veil' || 
				$parent_cat == 'resin top coat' || 
				$parent_cat == 'build up penebalan' || 
				$parent_cat == 'penebalan mandril' || 
				$parent_cat == 'lining flange' || 
				$parent_cat == 'joint square flange depan 8 mm' || 
				$parent_cat == 'joint square flange belakang 6 mm' || 
				$parent_cat == 'oval flange' || 
				$parent_cat == 'joint oval flange belakang 6 mm' || 
				$parent_cat == 'joint oval flange depan 8 mm' || 
				$parent_cat == 'shimplate 2mm' || 
				$parent_cat == 'shimplate 3mm' || 
				$parent_cat == 'shimplate 5mm' || 
				$parent_cat == 'joint end plate' || 
				$parent_cat == 'joint taper plate' || 
				$parent_cat == 'joint flange' || 
				$parent_cat == 'flange fuji resin' ||
				$parent_cat == 'proses acs' ||
				$parent_cat == 'nozzle holder' ||
				$parent_cat == 'lining' ||
				$parent_cat == 'waterproof plate' ||
				$parent_cat == 'joint waterproof' ||
				$parent_cat == 'blind plate' ||
				$parent_cat == 'y tee' ||
				$parent_cat == 'sudden reducer' ||
				$parent_cat == 'joint sudden reducer' ||
				$parent_cat == 'manhole' ||
				$parent_cat == 'dummy support' ||
				$parent_cat == 'lining coupling' ||
				$parent_cat == 'plate assy' ||
				$parent_cat == 'abr end cover' ||
				$parent_cat == 'abr cover' ||
				$parent_cat == 'lining elbow' ||
				$parent_cat == 'damper' ||
				$parent_cat == 'additional accessories' ||
				$parent_cat == 'cross tee' ||
				$parent_cat == 'horn mouth' ||
				$parent_cat == 'joint plate' ||
				$parent_cat == 'lateral tee' ||
				$parent_cat == 'lining colar' ||
				$parent_cat == 'manhole cover' ||
				$parent_cat == 'mold cover' ||
				$parent_cat == 'orifice plate' ||
				$parent_cat == 'pipe support' ||
				$parent_cat == 'reinforce saddle' ||
				$parent_cat == 'rod' ||
				$parent_cat == 'stiffening ring' ||
				$parent_cat == 'tinuvin solution' ||
				$parent_cat == 'vortex breaker' ||
				$parent_cat == 'inlet cover' ||
				$parent_cat == 'joint spacer' ||
				$parent_cat == 'elbow 5d' ||
				$parent_cat == 'orifice' ||
				$parent_cat == 'lining concrete' ||
				$parent_cat == 'joint nozzle'
		){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
		}
		elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}

		return $dim;
	}

	function spec_hasil($id, $table){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM ".$table." WHERE id_milik='".$id."'";
		// echo $qHeader;
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['parent_product'];
		// echo $parent_cat;
		if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang'] - 400)." x ".floatval($restHeader[0]['design']);
		}
		elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
		}
		elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['panjang'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif(
				$parent_cat == 'inlet cone' || 
				$parent_cat == 'taper plate' ||
				$parent_cat == 'rib taper plate' ||
				$parent_cat == 'end plate' ||
				$parent_cat == 'rib end plate' ||
				$parent_cat == 'square flange' ||
				$parent_cat == 'joint saddle' ||
				$parent_cat == 'bellmouth' || 
				$parent_cat == 'plate' || 
				$parent_cat == 'puddle flange' || 
				$parent_cat == 'rib' || 
				$parent_cat == 'joint rib' || 
				$parent_cat == 'support' || 
				$parent_cat == 'spectacle blind' || 
				$parent_cat == 'spacer' || 
				$parent_cat == 'spacer ring' || 
				$parent_cat == 'loose flange' || 
				$parent_cat == 'blind spacer' || 
				$parent_cat == 'joint puddle flange' || 
				$parent_cat == 'blind flange with hole' || 
				$parent_cat == 'laminate pad' || 
				$parent_cat == 'handle' ||
				$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
				$parent_cat == 'nexus' || 
				$parent_cat == 'csm' || 
				$parent_cat == 'woven roving' || 
				$parent_cat == 'resin' || 
				$parent_cat == 'sic powder' || 
				$parent_cat == 'katalis' || 
				$parent_cat == 'accelator' || 
				$parent_cat == 'putty' || 
				$parent_cat == 'veil' || 
				$parent_cat == 'resin top coat' || 
				$parent_cat == 'build up penebalan' || 
				$parent_cat == 'penebalan mandril' || 
				$parent_cat == 'lining flange' || 
				$parent_cat == 'joint square flange depan 8 mm' || 
				$parent_cat == 'joint square flange belakang 6 mm' || 
				$parent_cat == 'oval flange' || 
				$parent_cat == 'joint oval flange belakang 6 mm' || 
				$parent_cat == 'joint oval flange depan 8 mm' || 
				$parent_cat == 'shimplate 2mm' || 
				$parent_cat == 'shimplate 3mm' || 
				$parent_cat == 'shimplate 5mm' || 
				$parent_cat == 'joint end plate' || 
				$parent_cat == 'joint taper plate' || 
				$parent_cat == 'joint flange' || 
				$parent_cat == 'flange fuji resin' ||
				$parent_cat == 'proses acs' ||
				$parent_cat == 'nozzle holder' ||
				$parent_cat == 'lining' ||
				$parent_cat == 'waterproof plate' ||
				$parent_cat == 'joint waterproof' ||
				$parent_cat == 'blind plate' ||
				$parent_cat == 'y tee' ||
				$parent_cat == 'sudden reducer' ||
				$parent_cat == 'joint sudden reducer' ||
				$parent_cat == 'manhole' ||
				$parent_cat == 'dummy support' ||
				$parent_cat == 'lining coupling' ||
				$parent_cat == 'plate assy' ||
				$parent_cat == 'abr end cover' ||
				$parent_cat == 'abr cover' ||
				$parent_cat == 'lining elbow' ||
				$parent_cat == 'damper' ||
				$parent_cat == 'additional accessories' ||
				$parent_cat == 'cross tee' ||
				$parent_cat == 'horn mouth' ||
				$parent_cat == 'joint plate' ||
				$parent_cat == 'lateral tee' ||
				$parent_cat == 'lining colar' ||
				$parent_cat == 'manhole cover' ||
				$parent_cat == 'mold cover' ||
				$parent_cat == 'orifice plate' ||
				$parent_cat == 'pipe support' ||
				$parent_cat == 'reinforce saddle' ||
				$parent_cat == 'rod' ||
				$parent_cat == 'stiffening ring' ||
				$parent_cat == 'tinuvin solution' ||
				$parent_cat == 'vortex breaker' ||
				$parent_cat == 'inlet cover' ||
				$parent_cat == 'joint spacer' ||
				$parent_cat == 'elbow 5d' ||
				$parent_cat == 'orifice' ||
				$parent_cat == 'lining concrete' ||
				$parent_cat == 'joint nozzle'
		){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($parent_cat == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter2'])." x A ".floatval($restHeader[0]['diameter']);
		}
		elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang'])." x ".floatval($restHeader[0]['design']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}

		return $dim;
	}

	function spec_draf($id){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM draf_bq_detail_header WHERE id='".$id."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['id_category'];

		if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' OR $parent_cat == 'saddle' OR $parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
		}
		elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif(
				$parent_cat == 'inlet cone' || 
				$parent_cat == 'taper plate' ||
				$parent_cat == 'rib taper plate' ||
				$parent_cat == 'end plate' ||
				$parent_cat == 'rib end plate' ||
				$parent_cat == 'square flange' ||
				$parent_cat == 'joint saddle' ||
				$parent_cat == 'bellmouth' || 
				$parent_cat == 'plate' || 
				$parent_cat == 'puddle flange' || 
				$parent_cat == 'rib' || 
				$parent_cat == 'joint rib' || 
				$parent_cat == 'support' || 
				$parent_cat == 'spectacle blind' || 
				$parent_cat == 'spacer' || 
				$parent_cat == 'spacer ring' || 
				$parent_cat == 'loose flange' || 
				$parent_cat == 'blind spacer' || 
				$parent_cat == 'joint puddle flange' || 
				$parent_cat == 'blind flange with hole' || 
				$parent_cat == 'laminate pad' || 
				$parent_cat == 'handle' ||
				$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
				$parent_cat == 'nexus' || 
				$parent_cat == 'csm' || 
				$parent_cat == 'woven roving' || 
				$parent_cat == 'resin' || 
				$parent_cat == 'sic powder' || 
				$parent_cat == 'katalis' || 
				$parent_cat == 'accelator' || 
				$parent_cat == 'putty' || 
				$parent_cat == 'veil' || 
				$parent_cat == 'resin top coat' || 
				$parent_cat == 'build up penebalan' || 
				$parent_cat == 'penebalan mandril' || 
				$parent_cat == 'lining flange' || 
				$parent_cat == 'joint square flange depan 8 mm' || 
				$parent_cat == 'joint square flange belakang 6 mm' || 
				$parent_cat == 'oval flange' || 
				$parent_cat == 'joint oval flange belakang 6 mm' || 
				$parent_cat == 'joint oval flange depan 8 mm' || 
				$parent_cat == 'shimplate 2mm' || 
				$parent_cat == 'shimplate 3mm' || 
				$parent_cat == 'shimplate 5mm' || 
				$parent_cat == 'joint end plate' || 
				$parent_cat == 'joint taper plate' || 
				$parent_cat == 'joint flange' || 
				$parent_cat == 'flange fuji resin' ||
				$parent_cat == 'proses acs' ||
				$parent_cat == 'nozzle holder' ||
				$parent_cat == 'lining' ||
				$parent_cat == 'waterproof plate' ||
				$parent_cat == 'joint waterproof' ||
				$parent_cat == 'blind plate' ||
				$parent_cat == 'y tee' ||
				$parent_cat == 'sudden reducer' ||
				$parent_cat == 'joint sudden reducer' ||
				$parent_cat == 'manhole' ||
				$parent_cat == 'dummy support' ||
				$parent_cat == 'lining coupling' ||
				$parent_cat == 'plate assy' ||
				$parent_cat == 'abr end cover' ||
				$parent_cat == 'abr cover' ||
				$parent_cat == 'lining elbow' ||
				$parent_cat == 'damper' ||
				$parent_cat == 'additional accessories' ||
				$parent_cat == 'cross tee' ||
				$parent_cat == 'horn mouth' ||
				$parent_cat == 'joint plate' ||
				$parent_cat == 'lateral tee' ||
				$parent_cat == 'lining colar' ||
				$parent_cat == 'manhole cover' ||
				$parent_cat == 'mold cover' ||
				$parent_cat == 'orifice plate' ||
				$parent_cat == 'pipe support' ||
				$parent_cat == 'reinforce saddle' ||
				$parent_cat == 'rod' ||
				$parent_cat == 'stiffening ring' ||
				$parent_cat == 'tinuvin solution' ||
				$parent_cat == 'vortex breaker' ||
				$parent_cat == 'inlet cover' ||
				$parent_cat == 'joint spacer' ||
				$parent_cat == 'elbow 5d' ||
				$parent_cat == 'orifice' ||
				$parent_cat == 'lining concrete' ||
				$parent_cat == 'joint nozzle'
		){
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}
		elseif($parent_cat == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
		}

		return $dim;
	}
	
	function spec_base_on_component($id_milik, $table){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM ".$table." WHERE id_milik='".$id_milik."'";
		$restHeader		= $CI->db->query($qHeader)->result_array();
		$parent_cat		= $restHeader[0]['parent_product'];

		if($restHeader[0]['parent_product'] == 'pipe' OR $restHeader[0]['parent_product'] == 'pipe slongsong' OR $parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'elbow mitter' OR $restHeader[0]['parent_product'] == 'elbow mould'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']).", ".$restHeader[0]['type_elbow']." ".floatval($restHeader[0]['angle']);
		}
		elseif($restHeader[0]['parent_product'] == 'concentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee mould' OR $restHeader[0]['parent_product'] == 'eccentric reducer' OR $restHeader[0]['parent_product'] == 'reducer tee slongsong' OR $restHeader[0]['parent_product'] == 'branch joint'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'colar' OR $restHeader[0]['parent_product'] == 'colar slongsong' OR $restHeader[0]['parent_product'] == 'end cap' OR $restHeader[0]['parent_product'] == 'flange slongsong' OR $restHeader[0]['parent_product'] == 'flange mould' OR $restHeader[0]['parent_product'] == 'equal tee mould' OR $restHeader[0]['parent_product'] == 'blind flange' OR $restHeader[0]['parent_product'] == 'equal tee slongsong' OR $restHeader[0]['parent_product'] == 'spectacle blind' OR $restHeader[0]['parent_product'] == 'blank and spacer'){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['design']);
		}
		elseif($restHeader[0]['parent_product'] == 'field joint' OR $restHeader[0]['parent_product'] == 'shop joint' || $restHeader[0]['parent_product'] == 'saddle' ){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['panjang']);
		}
		elseif(
				$parent_cat == 'inlet cone' || 
				$parent_cat == 'taper plate' ||
				$parent_cat == 'rib taper plate' ||
				$parent_cat == 'end plate' ||
				$parent_cat == 'rib end plate' ||
				$parent_cat == 'square flange' ||
				$parent_cat == 'joint saddle' ||
				$parent_cat == 'bellmouth' || 
				$parent_cat == 'plate' || 
				$parent_cat == 'puddle flange' || 
				$parent_cat == 'rib' || 
				$parent_cat == 'joint rib' || 
				$parent_cat == 'support' || 
				$parent_cat == 'spectacle blind' || 
				$parent_cat == 'spacer' || 
				$parent_cat == 'spacer ring' || 
				$parent_cat == 'loose flange' || 
				$parent_cat == 'blind spacer' || 
				$parent_cat == 'joint puddle flange' || 
				$parent_cat == 'blind flange with hole' || 
				$parent_cat == 'laminate pad' || 
				$parent_cat == 'handle' ||
				$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
				$parent_cat == 'nexus' || 
				$parent_cat == 'csm' || 
				$parent_cat == 'woven roving' || 
				$parent_cat == 'resin' || 
				$parent_cat == 'sic powder' || 
				$parent_cat == 'katalis' || 
				$parent_cat == 'accelator' || 
				$parent_cat == 'putty' || 
				$parent_cat == 'veil' || 
				$parent_cat == 'resin top coat' || 
				$parent_cat == 'build up penebalan' || 
				$parent_cat == 'penebalan mandril' || 
				$parent_cat == 'lining flange' || 
				$parent_cat == 'joint square flange depan 8 mm' || 
				$parent_cat == 'joint square flange belakang 6 mm' || 
				$parent_cat == 'oval flange' || 
				$parent_cat == 'joint oval flange belakang 6 mm' || 
				$parent_cat == 'joint oval flange depan 8 mm' || 
				$parent_cat == 'shimplate 2mm' || 
				$parent_cat == 'shimplate 3mm' || 
				$parent_cat == 'shimplate 5mm' || 
				$parent_cat == 'joint end plate' || 
				$parent_cat == 'joint taper plate' || 
				$parent_cat == 'joint flange' || 
				$parent_cat == 'flange fuji resin' ||
				$parent_cat == 'proses acs' ||
				$parent_cat == 'nozzle holder' ||
				$parent_cat == 'lining' ||
				$parent_cat == 'waterproof plate' ||
				$parent_cat == 'joint waterproof' ||
				$parent_cat == 'blind plate' ||
				$parent_cat == 'y tee' ||
				$parent_cat == 'sudden reducer' ||
				$parent_cat == 'joint sudden reducer' ||
				$parent_cat == 'manhole' ||
				$parent_cat == 'dummy support' ||
				$parent_cat == 'lining coupling' ||
				$parent_cat == 'plate assy' ||
				$parent_cat == 'abr end cover' ||
				$parent_cat == 'abr cover' ||
				$parent_cat == 'lining elbow' ||
				$parent_cat == 'damper' ||
				$parent_cat == 'additional accessories' ||
				$parent_cat == 'cross tee' ||
				$parent_cat == 'horn mouth' ||
				$parent_cat == 'joint plate' ||
				$parent_cat == 'lateral tee' ||
				$parent_cat == 'lining colar' ||
				$parent_cat == 'manhole cover' ||
				$parent_cat == 'mold cover' ||
				$parent_cat == 'orifice plate' ||
				$parent_cat == 'pipe support' ||
				$parent_cat == 'reinforce saddle' ||
				$parent_cat == 'rod' ||
				$parent_cat == 'stiffening ring' ||
				$parent_cat == 'tinuvin solution' ||
				$parent_cat == 'vortex breaker' ||
				$parent_cat == 'inlet cover' ||
				$parent_cat == 'joint spacer' ||
				$parent_cat == 'elbow 5d' ||
				$parent_cat == 'orifice' ||
				$parent_cat == 'lining concrete' ||
				$parent_cat == 'joint nozzle'
		){
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}
		elseif($restHeader[0]['parent_product'] == 'figure 8'){
			$dim = floatval($restHeader[0]['diameter2'])." x A ".floatval($restHeader[0]['diameter']);
		}
		else{
			// $dim = "belum di set";
			$dim = floatval($restHeader[0]['diameter'])." x ".floatval($restHeader[0]['diameter2']);
		}

		return $dim;
	}

	function spec_deadstok($id){
		$CI 		=& get_instance();
		$qHeader		= "SELECT * FROM so_detail_header WHERE id='".$id."'";
		$dim = 'not found (old ipp)';
		$parent_cat		= 'not found';
		$restHeader		= $CI->db->query($qHeader)->result_array();
		if(!empty($restHeader)){
			$parent_cat		= $restHeader[0]['id_category'];

			$qPanjang		= "SELECT panjang FROM so_component_header WHERE id_milik='".$id."' LIMIT 1";
			$restPanjang		= $CI->db->query($qPanjang)->result_array();

			$panjang = (!empty($restPanjang))?$restPanjang[0]['panjang']:$restHeader[0]['length'];

			if($parent_cat == 'pipe' OR $parent_cat == 'pipe slongsong' || $parent_cat == 'saddle'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'elbow mitter' OR $parent_cat == 'elbow mould'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']).", ".$restHeader[0]['type']." ".floatval($restHeader[0]['sudut']);
			}
			elseif($parent_cat == 'concentric reducer' OR $parent_cat == 'reducer tee mould' OR $parent_cat == 'eccentric reducer' OR $parent_cat == 'reducer tee slongsong' OR $parent_cat == 'branch joint'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'colar' OR $parent_cat == 'colar slongsong' OR $parent_cat == 'end cap' OR $parent_cat == 'flange slongsong' OR $parent_cat == 'flange mould' OR $parent_cat == 'blind flange' OR $parent_cat == 'field joint' OR $parent_cat == 'shop joint' OR $parent_cat == 'spectacle blind' OR $parent_cat == 'blank and spacer'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'equal tee mould' OR $parent_cat == 'equal tee slongsong'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($panjang)." x ".floatval($restHeader[0]['thickness']);
			}
			elseif(
					$parent_cat == 'inlet cone' || 
					$parent_cat == 'taper plate' ||
					$parent_cat == 'rib taper plate' ||
					$parent_cat == 'end plate' ||
					$parent_cat == 'rib end plate' ||
					$parent_cat == 'square flange' ||
					$parent_cat == 'joint saddle' ||
					$parent_cat == 'bellmouth' || 
					$parent_cat == 'plate' || 
					$parent_cat == 'puddle flange' || 
					$parent_cat == 'rib' || 
					$parent_cat == 'joint rib' || 
					$parent_cat == 'support' || 
					$parent_cat == 'spectacle blind' || 
					$parent_cat == 'spacer' || 
					$parent_cat == 'spacer ring' || 
					$parent_cat == 'loose flange' || 
					$parent_cat == 'blind spacer' || 
					$parent_cat == 'joint puddle flange' || 
					$parent_cat == 'blind flange with hole' || 
					$parent_cat == 'laminate pad' || 
					$parent_cat == 'handle' ||
					$parent_cat == 'custom plate frp 1 x 1 m x 10t' || 
					$parent_cat == 'nexus' || 
					$parent_cat == 'csm' || 
					$parent_cat == 'woven roving' || 
					$parent_cat == 'resin' || 
					$parent_cat == 'sic powder' || 
					$parent_cat == 'katalis' || 
					$parent_cat == 'accelator' || 
					$parent_cat == 'putty' || 
					$parent_cat == 'veil' || 
					$parent_cat == 'resin top coat' || 
					$parent_cat == 'build up penebalan' || 
					$parent_cat == 'penebalan mandril' || 
					$parent_cat == 'lining flange' || 
					$parent_cat == 'joint square flange depan 8 mm' || 
					$parent_cat == 'joint square flange belakang 6 mm' || 
					$parent_cat == 'oval flange' || 
					$parent_cat == 'joint oval flange belakang 6 mm' || 
					$parent_cat == 'joint oval flange depan 8 mm' || 
					$parent_cat == 'shimplate 2mm' || 
					$parent_cat == 'shimplate 3mm' || 
					$parent_cat == 'shimplate 5mm' || 
					$parent_cat == 'joint end plate' || 
					$parent_cat == 'joint taper plate' || 
					$parent_cat == 'joint flange' || 
					$parent_cat == 'flange fuji resin' ||
					$parent_cat == 'proses acs' ||
					$parent_cat == 'nozzle holder' ||
					$parent_cat == 'lining' ||
					$parent_cat == 'waterproof plate' ||
					$parent_cat == 'joint waterproof' ||
					$parent_cat == 'blind plate' ||
					$parent_cat == 'y tee' ||
					$parent_cat == 'sudden reducer' ||
					$parent_cat == 'joint sudden reducer' ||
					$parent_cat == 'manhole' ||
					$parent_cat == 'dummy support' ||
					$parent_cat == 'lining coupling' ||
					$parent_cat == 'plate assy' ||
					$parent_cat == 'abr end cover' ||
					$parent_cat == 'abr cover' ||
					$parent_cat == 'lining elbow' ||
					$parent_cat == 'damper' ||
					$parent_cat == 'additional accessories' ||
					$parent_cat == 'cross tee' ||
					$parent_cat == 'horn mouth' ||
					$parent_cat == 'joint plate' ||
					$parent_cat == 'lateral tee' ||
					$parent_cat == 'lining colar' ||
					$parent_cat == 'manhole cover' ||
					$parent_cat == 'mold cover' ||
					$parent_cat == 'orifice plate' ||
					$parent_cat == 'pipe support' ||
					$parent_cat == 'reinforce saddle' ||
					$parent_cat == 'rod' ||
					$parent_cat == 'stiffening ring' ||
					$parent_cat == 'tinuvin solution' ||
					$parent_cat == 'vortex breaker' ||
					$parent_cat == 'inlet cover' ||
					$parent_cat == 'joint spacer' ||
					$parent_cat == 'elbow 5d' ||
					$parent_cat == 'orifice' ||
					$parent_cat == 'lining concrete' ||
					$parent_cat == 'joint nozzle'
			){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
			elseif($parent_cat == 'figure 8'){
				$dim = floatval($restHeader[0]['diameter_2'])." x A ".floatval($restHeader[0]['diameter_1']);
			}
			elseif($parent_cat == 'frp pipe' OR $parent_cat == 'lining pipe'){
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['length'])." x ".floatval($restHeader[0]['thickness']);
			}
			else{
				// $dim = "belum di set";
				$dim = floatval($restHeader[0]['diameter_1'])." x ".floatval($restHeader[0]['diameter_2'])." x ".floatval($restHeader[0]['thickness']);
			}
		}
		return strtoupper($parent_cat).', '.$dim;
	}

	function ck_replace($data){
		$data = (!empty($data)?str_replace(',','.',$data):0);
		return $data;
	}

	function pe_direct_labour(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_process WHERE id = '1' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_indirect_labour(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_process WHERE id = '2' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_machine($total_time, $id_mesin){
		$CI 		=& get_instance();
		$tm_mesin	= 0;
		if(!empty($id_mesin)){
			$qHeader	= "SELECT machine_cost_per_hour FROM machine WHERE no_mesin = '".$id_mesin."' LIMIT 1 ";
			$restHeader	= $CI->db->query($qHeader)->result_array();
			$tm_mesin	= $restHeader[0]['machine_cost_per_hour'];
		}
		$hasil		= $tm_mesin;
		if($hasil == NULL){
			$hasil	= 0;
		}
		return $hasil;
	}

	function pe_mould_mandrill($product_parent, $diameter_1, $diameter_2){
		$CI 		=& get_instance();
		$dim2		= ($diameter_2 == '0' OR $diameter_2 == '')?'0':$diameter_2;
		$qHeader	= "	SELECT
							biaya_per_pcs
						FROM mould_mandrill
						WHERE product_parent = '".$product_parent."'
							AND diameter = '".$diameter_1."'
							AND diameter2 = '".$dim2."'
						LIMIT 1  ";
		// echo $qHeader;
		$restHeader	= $CI->db->query($qHeader)->result_array();
		if(!empty($restHeader)){
			$hasil		= $restHeader[0]['biaya_per_pcs'];
		}

		if(empty($restHeader)){
			$hasil	= 0;
		}
		return $hasil;
	}

	function pe_consumable($product_parent){
		$CI 		=& get_instance();
		$qHeader	= "SELECT `type` FROM product_parent WHERE product_parent = '".$product_parent."' LIMIT 1 ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$ty_pe		= $restHeader[0]['type'];
		if($ty_pe == 'pipe'){
			$qType	= "SELECT std_rate FROM cost_process WHERE id = '3' ";
		}
		if($ty_pe == 'fitting'){
			$qType	= "SELECT std_rate FROM cost_process WHERE id = '4' ";
		}
		if($ty_pe == 'joint'){
			$qType	= "SELECT std_rate FROM cost_process WHERE id = '5' ";
		}
		if($ty_pe == 'field'){
			$qType	= "SELECT std_rate FROM cost_process WHERE id = '8' ";
		}
		$restType	= $CI->db->query($qType)->result_array();
		$hasil		= $restType[0]['std_rate'];
		if($hasil == NULL){
			$hasil	= 0;
		}
		return $hasil;
	}

	function pe_foh_consumable(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_foh WHERE id = '1' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_foh_depresiasi(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_foh WHERE id = '2' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_biaya_gaji_non_produksi(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_foh WHERE id = '3' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_biaya_non_produksi(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_foh WHERE id = '4' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function pe_biaya_rutin_bulanan(){
		$CI 		=& get_instance();
		$qHeader	= "SELECT std_rate FROM cost_foh WHERE id = '5' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['std_rate'];
		return $hasil;
	}

	function SQL_Revised($id_bq){
		$SQL_data 		= "SELECT
							a.id_bq,
							a.id_milik,
							a.parent_product,
							a.id_product,
							a.series,
							b.diameter AS diameter,
							b.diameter2 AS diameter2,
							b.pressure,
							b.liner,
							a.qty,
							( a.sum_mat * a.qty ) AS est_material,
							( a.est_harga * a.qty ) AS est_harga,
							( a.direct_labour * a.qty ) AS direct_labour,
							( a.indirect_labour * a.qty ) AS indirect_labour,
							( a.machine * a.qty ) AS machine,
							( a.mould_mandrill * a.qty ) AS mould_mandrill,
							( a.consumable * a.qty ) AS consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan,
							c.persen AS profit,
							c.extra AS allowance,
							b.man_power AS man_power,
							b.id_mesin AS id_mesin,
							b.total_time AS total_time,
							b.man_hours AS man_hours,
							b.pe_direct_labour AS pe_direct_labour,
							b.pe_indirect_labour AS pe_indirect_labour,
							b.pe_machine AS pe_machine,
							b.pe_mould_mandrill AS pe_mould_mandrill,
							b.pe_consumable AS pe_consumable,
							b.pe_foh_consumable AS pe_foh_consumable,
							b.pe_foh_depresiasi AS pe_foh_depresiasi,
							b.pe_biaya_gaji_non_produksi AS pe_biaya_gaji_non_produksi,
							b.pe_biaya_non_produksi AS pe_biaya_non_produksi,
							b.pe_biaya_rutin_bulanan AS pe_biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
							LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
	return $SQL_data;
	}
	
	function SQL_Revised_Costing($id_bq){
		$SQL_data 		= "SELECT
							a.id_bq,
							a.id_milik,
							a.parent_product,
							a.id_product,
							a.series,
							b.diameter AS diameter,
							b.diameter2 AS diameter2,
							b.pressure,
							b.liner,
							a.qty,
							( a.sum_mat * a.qty ) AS est_material,
							( a.est_harga * a.qty ) AS est_harga,
							( a.direct_labour * a.qty ) AS direct_labour,
							( a.indirect_labour * a.qty ) AS indirect_labour,
							( a.machine * a.qty ) AS machine,
							( a.mould_mandrill * a.qty ) AS mould_mandrill,
							( a.consumable * a.qty ) AS consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) * a.qty AS foh_consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) * a.qty AS foh_depresiasi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) * a.qty AS biaya_gaji_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) * a.qty AS biaya_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) * a.qty AS biaya_rutin_bulanan,
							b.man_power AS man_power,
							b.id_mesin AS id_mesin,
							b.total_time AS total_time,
							b.man_hours AS man_hours,
							b.pe_direct_labour AS pe_direct_labour,
							b.pe_indirect_labour AS pe_indirect_labour,
							b.pe_machine AS pe_machine,
							b.pe_mould_mandrill AS pe_mould_mandrill,
							b.pe_consumable AS pe_consumable,
							b.pe_foh_consumable AS pe_foh_consumable,
							b.pe_foh_depresiasi AS pe_foh_depresiasi,
							b.pe_biaya_gaji_non_produksi AS pe_biaya_gaji_non_produksi,
							b.pe_biaya_non_produksi AS pe_biaya_non_produksi,
							b.pe_biaya_rutin_bulanan AS pe_biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
		return $SQL_data;
	}
	
	function SQL_Revised_EXQTY($id_bq){
		$SQL_data 		= "SELECT
							a.id_bq,
							a.id_milik,
							a.parent_product,
							a.id_product,
							a.series,
							b.diameter AS diameter,
							b.diameter2 AS diameter2,
							b.pressure,
							b.liner,
							a.qty,
							( a.sum_mat) AS est_material,
							( a.est_harga) AS est_harga,
							( a.direct_labour) AS direct_labour,
							( a.indirect_labour) AS indirect_labour,
							( a.machine) AS machine,
							( a.mould_mandrill) AS mould_mandrill,
							( a.consumable) AS consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_consumable ) / 100 ) AS foh_consumable,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_foh_depresiasi ) / 100 ) AS foh_depresiasi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_gaji_non_produksi ) / 100 ) AS biaya_gaji_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_non_produksi ) / 100 ) AS biaya_non_produksi,
							(
								(
									( a.direct_labour ) + ( a.indirect_labour ) + ( a.machine ) + ( a.mould_mandrill ) + ( a.consumable )
								) + a.est_harga
							) * ( ( b.pe_biaya_rutin_bulanan ) / 100 ) AS biaya_rutin_bulanan,
							c.persen AS profit,
							c.extra AS allowance,
							b.man_power AS man_power,
							b.id_mesin AS id_mesin,
							b.total_time AS total_time,
							b.man_hours AS man_hours,
							b.pe_direct_labour AS pe_direct_labour,
							b.pe_indirect_labour AS pe_indirect_labour,
							b.pe_machine AS pe_machine,
							b.pe_mould_mandrill AS pe_mould_mandrill,
							b.pe_consumable AS pe_consumable,
							b.pe_foh_consumable AS pe_foh_consumable,
							b.pe_foh_depresiasi AS pe_foh_depresiasi,
							b.pe_biaya_gaji_non_produksi AS pe_biaya_gaji_non_produksi,
							b.pe_biaya_non_produksi AS pe_biaya_non_produksi,
							b.pe_biaya_rutin_bulanan AS pe_biaya_rutin_bulanan
						FROM
							estimasi_cost_and_mat a
							INNER JOIN bq_product b ON a.id_milik = b.id
							LEFT JOIN cost_project_detail c ON b.id = c.caregory_sub
							LEFT JOIN cost_project_detail_sales i ON b.id = i.caregory_sub
						WHERE
							a.id_bq = '".$id_bq."'
						ORDER BY
							a.id_milik ASC";
	return $SQL_data;
	}

	function eng_cost($id_bq, $rev){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM laporan_revised_etc WHERE id_bq='".$id_bq."' AND category ='engine' AND revised_no='".$rev."' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}

	function packing_cost($id_bq, $rev){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM laporan_revised_etc WHERE id_bq='".$id_bq."' AND category ='packing' AND revised_no='".$rev."' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}

	function truck_cost($id_bq, $rev){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM laporan_revised_etc WHERE id_bq='".$id_bq."' AND revised_no='".$rev."' AND (category ='export' OR category ='lokal') ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}
	
	function manual_eng_cost($id_bq){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category ='engine' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}
	
	function manual_packing_cost($id_bq){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category ='packing' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		//DEAL_SO
		$qHeader2	= "select sum(total_deal_usd) as price_total FROM billing_so_add WHERE no_ipp='".str_replace('BQ-','',$id_bq)."' AND category ='pack' ";
		$restHeader2	= $CI->db->query($qHeader2)->result_array();
		if(!empty($restHeader2[0]['price_total']) AND $restHeader2[0]['price_total'] > 0){
			$hasil		= $restHeader2[0]['price_total'];
		}

		return $hasil;
	}
	
	function manual_export_cost($id_bq){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category ='export' AND sts_so='Y' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}
	
	function manual_lokal_cost($id_bq){
		$CI 		=& get_instance();
		$qHeader	= "select sum(price_total) as price_total FROM cost_project_detail WHERE id_bq='".$id_bq."' AND category ='lokal' AND sts_so='Y' ";
		$restHeader	= $CI->db->query($qHeader)->result_array();
		$hasil		= $restHeader[0]['price_total'];
		return $hasil;
	}

	function get_price_ref($id_material){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlPrice 	= "SELECT price_ref_estimation FROM raw_materials WHERE id_material='".$id_material."' AND '".$date."' <= exp_price_ref_est LIMIT 1";
		$restPrice 	= $CI->db->query($sqlPrice)->result();

		$price		= (!empty($restPrice[0]->price_ref_estimation))?$restPrice[0]->price_ref_estimation:0;
		return $price;
	}
	
	function get_price_rutin($id_material, $id_unit){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlPrice 	= "SELECT rate FROM price_ref WHERE code_group='".$id_material."' AND id_unit = '".$id_unit."' LIMIT 1";
		$restPrice 	= $CI->db->query($sqlPrice)->result();

		$price		= (!empty($restPrice[0]->rate))?$restPrice[0]->rate:0;
		return $price;
	}

	function get_price_rutin2($id_material){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlPrice 	= "SELECT rate FROM price_ref WHERE code_group='".$id_material."' AND deleted='N' LIMIT 1";
		$restPrice 	= $CI->db->select('rate')->get_where('price_ref', array('code_group'=>$id_material,'deleted'=>'N'))->result();

		$price		= (!empty($restPrice[0]->rate))?$restPrice[0]->rate:0;
		return $price;
	}
	
	function get_price_acc($id_material){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlPrice 	= "SELECT harga FROM accessories WHERE id='".$id_material."' LIMIT 1";
		$restPrice 	= $CI->db->query($sqlPrice)->result();

		$price		= (!empty($restPrice[0]->harga))?$restPrice[0]->harga:0;
		return $price;
	}

	function get_name_accessories($id_material){
		$CI 		=& get_instance();
		$SQL 		= "SELECT id, id_material, nama, SUBSTRING(spesifikasi,1,30) as spesifikasi2, SUBSTRING(material,1,30) as material FROM accessories WHERE id='".$id_material."' LIMIT 1";
		$Result 	= $CI->db->query($SQL)->result();

		$name_acc	= (!empty($Result[0]->id_material))?$Result[0]->id_material.' - '.$Result[0]->nama." ".$Result[0]->spesifikasi2." ".$Result[0]->material:'';
		return $name_acc;
	}

	function check_approve_201111($id_bq){
		$CI 		=& get_instance();
		$data_session	= $CI->session->userdata;
		$no_ipp = str_replace('BQ-','',$id_bq);

		$sqlAll 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."'";
		$numAll 	= $CI->db->query($sqlAll)->num_rows();
		// echo $restAll; exit;
		$sqlCheck 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'Y' OR approve = 'P')";
		$restCheck 	= $CI->db->query($sqlCheck)->result_array();
		$numCheck 	= $CI->db->query($sqlCheck)->num_rows();

		if(!empty($restCheck)){
			$Arr_Edit2	= array(
				'status' => 'PARTIAL PROCESS'
			);

			$Arr_Edit	= array(
				'aju_approved_est' 		=> 'Y',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);

			$CI->db->where('id_bq', $id_bq);
			$CI->db->update('so_header', $Arr_Edit);

			$CI->db->where('no_ipp', $no_ipp);
			$CI->db->update('production', $Arr_Edit2);

			$sqlCheckY 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'Y')";
			$numCheckY 	= $CI->db->query($sqlCheckY)->num_rows();
			if($numCheckY == $numAll){
				$Arr_Edit2	= array(
					'status' => 'WAITING APPROVE FINAL DRAWING'
				);
				$CI->db->where('no_ipp', $no_ipp);
				$CI->db->update('production', $Arr_Edit2);
			}

			$sqlCheckP 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'P')";
			$numCheckP 	= $CI->db->query($sqlCheckP)->num_rows();
			if($numCheckP == $numAll){
				$Arr_Edit2	= array(
					'status' => 'PROCESS PRODUCTION',
					'quo_reason' => '',
					'quo_by' => $data_session['ORI_User']['username'],
					'quo_date' => date('Y-m-d H:i:s'),
					'mp' => 'Y',
					'mp_by' => $data_session['ORI_User']['username'],
					'mp_date' => date('Y-m-d H:i:s')
				);
				$Arr_Edit	= array(
					'approved_est' 		=> 'Y',
					'approved_est_by' 	=> $data_session['ORI_User']['username'],
					'approved_est_date' => date('Y-m-d H:i:s')
				);

				$CI->db->where('id_bq', $id_bq);
				$CI->db->update('so_header', $Arr_Edit);

				$CI->db->where('no_ipp', $no_ipp);
				$CI->db->update('production', $Arr_Edit2);
			}
		}

		if(empty($restCheck)){
			$Arr_Edit2	= array(
				'status' => 'WAITING FINAL DRAWING'
			);

			$Arr_Edit	= array(
				'aju_approved_est' 		=> 'N',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);

			$CI->db->where('id_bq', $id_bq);
			$CI->db->update('so_header', $Arr_Edit);

			$CI->db->where('no_ipp', $no_ipp);
			$CI->db->update('production', $Arr_Edit2);
		}

	}
	
	function check_approve($id_bq){
		$CI 		=& get_instance();
		$data_session	= $CI->session->userdata;
		$no_ipp = str_replace('BQ-','',$id_bq);

		$sql_all 		= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."'";
		$num_all 		= $CI->db->query($sql_all)->num_rows();

		$sql_all_mat 	= "SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."'";
		$num_all_mat 	= $CI->db->query($sql_all_mat)->num_rows();
		
		$sql_check 		= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'Y' OR approve = 'P')";
		$rest_check 	= $CI->db->query($sql_check)->result_array();

		$sql_check_acc 	= "SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND (approve = 'Y' OR approve = 'P')";
		$rest_check_acc = $CI->db->query($sql_check_acc)->result_array();

		if(!empty($rest_check) OR !empty($rest_check_acc)){
			$Arr_Edit2	= array(
				'status' => 'PARTIAL PROCESS'
			);

			$Arr_Edit	= array(
				'aju_approved_est' 		=> 'Y',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);

			$CI->db->where('id_bq', $id_bq);
			$CI->db->update('so_header', $Arr_Edit);

			$CI->db->where('no_ipp', $no_ipp);
			$CI->db->update('production', $Arr_Edit2);

			//cek status apa semua sudah diajuakn
			$sqlCheckY 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'Y')";
			$numCheckY 	= $CI->db->query($sqlCheckY)->num_rows();

			$sqlAccY 	= "SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND (approve = 'Y')";
			$numAccY 	= $CI->db->query($sqlAccY)->num_rows();
			if($numCheckY == $num_all AND $numAccY == $num_all_mat){
				$Arr_Edit2	= array(
					// 'status' => 'WAITING APPROVE FINAL DRAWING'
					'status' => 'PARTIAL PROCESS'
				);
				$CI->db->where('no_ipp', $no_ipp);
				$CI->db->update('production', $Arr_Edit2);
			}

			//cek status apa semua sudah sampai produksi
			$sqlCheckP 	= "SELECT * FROM so_detail_header WHERE id_bq='".$id_bq."' AND (approve = 'P')";
			$numCheckP 	= $CI->db->query($sqlCheckP)->num_rows();

			$sqlAccP 	= "SELECT * FROM so_acc_and_mat WHERE id_bq='".$id_bq."' AND (approve = 'P')";
			$numAccP 	= $CI->db->query($sqlAccP)->num_rows();
			if($numCheckP == $num_all AND $numAccP == $num_all_mat){
				$Arr_Edit2	= array(
					// 'status' => 'PROCESS PRODUCTION',
					'status' => 'PARTIAL PROCESS',
					'quo_reason' => '',
					'quo_by' => $data_session['ORI_User']['username'],
					'quo_date' => date('Y-m-d H:i:s'),
					'mp' => 'Y',
					'mp_by' => $data_session['ORI_User']['username'],
					'mp_date' => date('Y-m-d H:i:s')
				);
				$Arr_Edit	= array(
					// 'approved_est' 		=> 'Y',
					'approved_est' 		=> 'N',
					'approved_est_by' 	=> $data_session['ORI_User']['username'],
					'approved_est_date' => date('Y-m-d H:i:s')
				);

				$CI->db->where('id_bq', $id_bq);
				$CI->db->update('so_header', $Arr_Edit);

				$CI->db->where('no_ipp', $no_ipp);
				$CI->db->update('production', $Arr_Edit2);
			}
		}

		if(empty($rest_check) AND empty($rest_check_acc)){
			$Arr_edit_status	= array(
				'status' => 'WAITING FINAL DRAWING'
			);

			$Arr_edit_so	= array(
				'aju_approved_est' 		=> 'N',
				'aju_approved_est_by' 	=> $data_session['ORI_User']['username'],
				'aju_approved_est_date' => date('Y-m-d H:i:s')
			);

			$CI->db->where('id_bq', $id_bq);
			$CI->db->update('so_header', $Arr_edit_so);

			$CI->db->where('no_ipp', $no_ipp);
			$CI->db->update('production', $Arr_edit_status);
		}

	}

	function check_fd($id){
		$CI 		=& get_instance();
		$sqlAll 	= "SELECT approve FROM so_detail_header WHERE id='".$id."'";
		$rest 		= $CI->db->query($sqlAll)->result();

		$approve	= $rest[0]->approve;
		return $approve;
	}
	
	function check_fd_acc($id){
		$CI 		=& get_instance();
		$sqlAll 	= "SELECT approve FROM so_acc_and_mat WHERE id='".$id."'";
		$rest 		= $CI->db->query($sqlAll)->result();

		$approve	= $rest[0]->approve;
		return $approve;
	}

	function get_weight_compx($id){
		$CI 			=& get_instance();
		$eEx_resin 		= "SELECT SUM(last_cost) AS berat FROM component_detail WHERE id_product='".$id."' AND id_category <> 'TYP-0001' AND id_category <> 'TYP-0030'";
		$restEx_resin 	= $CI->db->query($eEx_resin)->result();
		$data	= array(
			'weight' => $restEx_resin[0]->berat
		);
		return $data;
	}

	function get_ph($table, $value){
		$CI 			=& get_instance();
		$eEx_resin 		= "SELECT std_rate FROM $table WHERE id='".$value."' ";
		$restEx_resin 	= $CI->db->query($eEx_resin)->result();
		$data			= (!empty($restEx_resin[0]->std_rate))?$restEx_resin[0]->std_rate:0;
		return $data;
	}

	function get_con($product){
		$CI 			=& get_instance();

		$value = '4';
		if($product == 'pipe'){
			$value = '3';
		}
		if($product == 'field joint'){
			$value = '8';
		}
		if($product == 'shop joint' OR $product == 'branch joint'){
			$value = '5';
		}

		$eEx_resin 		= "SELECT std_rate FROM cost_process WHERE id = '".$value."'";
		$restEx_resin 	= $CI->db->query($eEx_resin)->result();
		$data			= (!empty($restEx_resin[0]->std_rate))?$restEx_resin[0]->std_rate:0;
		return $data;
	}

	function get_profit($product, $dim1, $dim2){
		$CI 			=& get_instance();
		$eEx_resin 		= "SELECT profit FROM cost_profit WHERE product_parent='".$product."' AND diameter='".$dim1."' AND diameter2='".$dim2."' LIMIT 1";
		$restEx_resin 	= $CI->db->query($eEx_resin)->result();
		$data			= (!empty($restEx_resin[0]->profit))?$restEx_resin[0]->profit:0;
		return $data;
	}

	function get_weight_comp($id, $series, $product, $dim1, $dim2){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		//get machine
		$field_ = 'last_cost';
		if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
			$field_ = 'material_weight';
		}

		$wherePN = floatval(substr($series, 3,2));
		$whereLN = floatval(substr($series, 6,3));

		$wherePlus = " AND diameter='".$dim1."' ";
		if($product == 'concentric reducer' OR $product == 'eccentric reducer' OR $product == 'reducer tee mould' OR $product == 'reducer tee slongsong'){
			$wherePlus = " AND diameter='".$dim1."' AND diameter2 = '".$dim2."' ";
		}
		if($product == 'branch joint'){
			$wherePlus = " AND diameter2 = '".$dim2."' ";
		}
		$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$product."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
		$restSer = $CI->db->query($qSeries)->result();

		$total_time 	= (!empty($restSer[0]->total_time))?$restSer[0]->total_time:0;
		$id_mesin 		= (!empty($restSer[0]->id_mesin))?$restSer[0]->id_mesin:'';
		$man_hours 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:0;

		$eEx_resin 	= "	SELECT
							SUM(a.$field_) AS berat,
							SUM(a.$field_ * b.price_ref_estimation) AS price
						FROM component_detail a
							LEFT JOIN raw_materials b ON a.id_material=b.id_material
						WHERE 1=1
							AND a.id_product='".$id."'
							AND a.id_category <> 'TYP-0001'
							AND a.id_material <> 'MTL-1903000'
							AND a.id_category <> 'TYP-0030'";
		$rEx_resin 	= $CI->db->query($eEx_resin)->result();

		$direct 	= get_ph('cost_process', 1);
		$indirect 	= get_ph('cost_process', 2);
		$machine 	= pe_machine($total_time, $id_mesin) * $total_time;
		$mould 		= pe_mould_mandrill($product, $dim1, $dim2);
		$consumable = get_con($product);
		$foh_consumable 			= get_ph('cost_foh', 1) / 100;
		$foh_depresiasi 			= get_ph('cost_foh', 2) / 100;
		$biaya_gaji_non_produksi 	= get_ph('cost_foh', 3) / 100;
		$biaya_non_produksi 		= get_ph('cost_foh', 4) / 100;
		$biaya_rutin_bulanan 		= get_ph('cost_foh', 5) / 100;
		$p_profit 	= get_profit($product, $dim1, $dim2) / 100;

		$wExResin 	= (!empty($rEx_resin))?$rEx_resin[0]->berat:0;
		$wExPrice 	= (!empty($rEx_resin))?$rEx_resin[0]->price:0;

		$eEx_resin2 	= "	SELECT
								SUM(a.last_cost) AS berat,
								SUM(a.last_cost * b.price_ref_estimation) AS price
							FROM component_detail_plus a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_product='".$id."'
								AND a.id_category <> 'TYP-0001'
								AND a.id_material <> 'MTL-1903000'
								AND a.id_category <> 'TYP-0030'";

		$rEx_resin2 	= $CI->db->query($eEx_resin2)->result();
		$wExResin2 	= (!empty($rEx_resin2))?$rEx_resin2[0]->berat:0;
		$wExPrice2 	= (!empty($rEx_resin2))?$rEx_resin2[0]->price:0;

		$eEx_resin3 	= "	SELECT
								SUM(a.last_cost) AS berat,
								SUM(a.last_cost * b.price_ref_estimation) AS price
							FROM component_detail_add a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_material <> 'MTL-1903000'
								AND a.id_product='".$id."'
								AND a.id_category <> 'TYP-0001'";

		$rEx_resin3 	= $CI->db->query($eEx_resin3)->result();
		$wExResin3 	= (!empty($rEx_resin3))?$rEx_resin3[0]->berat:0;
		$wExPrice3 	= (!empty($rEx_resin3))?$rEx_resin3[0]->price:0;

		$e_resin 	= "	SELECT
							MAX(a.$field_) AS berat,
							(MAX(a.$field_) * b.price_ref_estimation) AS price
						FROM component_detail a
							LEFT JOIN raw_materials b ON a.id_material=b.id_material
						WHERE 1=1
							AND a.id_material <> 'MTL-1903000'
							AND a.id_product='".$id."'
							AND a.id_category = 'TYP-0001'
						GROUP BY
							a.detail_name";

		$r_resin 	= $CI->db->query($e_resin)->result_array();
		$SUM1 = 0;
		$SUMP1 = 0;
		foreach($r_resin AS $valx => $val){
			$SUM1 += $val['berat'];
			$SUMP1 += $val['price'];
		}
		$wResin 	= (!empty($r_resin))?$SUM1:0;
		$wPrice 	= (!empty($r_resin))?$SUMP1:0;

		if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
			$e_resinJN 	= "	SELECT
							SUM(a.$field_) AS berat,
							(SUM(a.$field_ * b.price_ref_estimation)) AS price
						FROM component_detail a
							LEFT JOIN raw_materials b ON a.id_material=b.id_material
						WHERE 1=1
							AND a.id_material <> 'MTL-1903000'
							AND a.id_product='".$id."'
							AND a.id_category = 'TYP-0001'";

			$r_resinJN 	= $CI->db->query($e_resinJN)->result();

			$wResin 	= (!empty($r_resinJN))?$r_resinJN[0]->berat:0;
			$wPrice 	= (!empty($r_resinJN))?$r_resinJN[0]->price:0;
		}


		$e_resin2 	= "	SELECT
							MAX(a.last_cost) AS berat,
							(MAX(a.last_cost) * b.price_ref_estimation) AS price
						FROM component_detail_plus a
							LEFT JOIN raw_materials b ON a.id_material=b.id_material
						WHERE 1=1
							AND a.id_material <> 'MTL-1903000'
							AND a.id_product='".$id."'
							AND a.id_category = 'TYP-0001'
						GROUP BY
							a.detail_name";

		$r_resin2 	= $CI->db->query($e_resin2)->result_array();
		$SUM2 = 0;
		$SUMP2 = 0;
		foreach($r_resin2 AS $valx => $val){
			$SUM2 += $val['berat'];
			$SUMP2 += $val['price'];
		}
		$wResin2 	= (!empty($r_resin2))?$SUM2:0;
		$wPrice2 	= (!empty($r_resin2))?$SUMP2:0;

		$weight 	= $wExResin + $wExResin2 + $wExResin3 + $wResin + $wResin2;
		// $weight 	= $wExResin3;
		$price 		= $wExPrice + $wExPrice2 + $wExPrice3 + $wPrice + $wPrice2;
		$process 	= ($man_hours * $direct) + ($man_hours * $indirect) + $machine + $mould + ($price * $consumable);
		$consumab 	= $price * $consumable;
		$foh 		= (($process + $price) * $foh_consumable) + (($process + $price) * $foh_depresiasi) + (($process + $price) * $biaya_gaji_non_produksi) + (($process + $price) * $biaya_non_produksi) + (($process + $price) * $biaya_rutin_bulanan);
		$profit 	= ($price + $process + $foh) * $p_profit;

		$data	= array(
			'sql' => $eEx_resin,
			'weight' => $weight,
			'price' => $price,
			'process' => $process,
			'foh' => $foh,
			'profit' => $profit
		);
		return $data;
	}

	function get_man_hours($id, $series, $product, $dim1, $dim2){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		//get machine
		$wherePN = floatval(substr($series, 3,2));
		$whereLN = floatval(substr($series, 6,3));

		$wherePlus = " AND diameter='".$dim1."' ";
		if($product == 'concentric reducer' OR $product == 'eccentric reducer' OR $product == 'reducer tee mould' OR $product == 'reducer tee slongsong'){
			$wherePlus = " AND diameter='".$dim1."' AND diameter2 = '".$dim2."' ";
		}
		if($product == 'branch joint'){
			$wherePlus = " AND diameter2 = '".$dim2."' ";
		}
		$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$product."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
		$restSer = $CI->db->query($qSeries)->result();

		$man_hours 		= (!empty($restSer[0]->man_hours))?$restSer[0]->man_hours:0;

		return $man_hours;
	}



	function get_name($table, $field, $field_whare, $value){
		$CI 		=& get_instance();
		$query	= $CI->db->query("SELECT $field FROM $table WHERE $field_whare='".$value."' LIMIT 1")->result();
		$data 	= (!empty($query))?$query[0]->$field:$value;
		return $data;
	}

	function get_menu($table, $field, $field_whare, $value){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT $field FROM $table WHERE $field_whare='".$value."' LIMIT 1")->result();
		$data 	= (!empty($query))?$query[0]->$field:0;
		return $data;
	}

	function get_name_report($table, $field, $field_whare, $value){
		$CI 		=& get_instance();
		$query	= $CI->db->query("SELECT $field FROM $table WHERE $field_whare='".$value."' LIMIT 1")->result();
		$data 	= (!empty($query))?$query[0]->$field:0;
		return $data;
	}

	function get_total_by_revised($id_bq){
		$CI 		=& get_instance();
		$query	= $CI->db->query("SELECT * FROM laporan_revised_header WHERE id_bq='".$id_bq."' ORDER BY revised_no DESC LIMIT 1")->result();
		$data	= array(
			'total_project' => (!empty($query))?$query[0]->price_project:0,
			'weight' => (!empty($query))?$query[0]->est_material:0,
		);
		return $data;
	}

	function get_list_liner(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT * FROM list_help WHERE group_by='liner' ORDER BY urut ASC")->result_array();
		return $query;
	}

	function get_jalur($id_produksi){
		$CI 		=& get_instance();
		$qSupplier	= "SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
		$row		= $CI->db->query($qSupplier)->result_array();

		$HelpDet 	= "bq_detail_header";
		$HelpDet2 	= "bq_component_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
			$HelpDet2 = "so_component_header";
		}
		$data	= array(
			'bq' => $HelpDet,
			'comp' => $HelpDet2,
		);
		return $data;
	}

	function get_max_field($table, $field, $field_whare, $value){
		$CI 		=& get_instance();
		$query	= $CI->db->query("SELECT MAX($field) AS $field FROM $table WHERE $field_whare='".$value."' LIMIT 1")->result();
		$data 	= (!empty($query))?$query[0]->$field:0;
		return $data;
	}
	
	function group_by($key, $data) {
		$result = array();

		foreach($data as $val) {
			if(array_key_exists($key, $val)){
				$result[$val[$key]][] = $val;
			}else{
				$result[""][] = $val;
			}
		}
		return $result;
	}
	
	function get_list_dept(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT * FROM department WHERE status='Y' AND deleted='N' ORDER BY nm_dept ASC")->result_array();
		return $query;
	}
	
	function get_list_costcenter(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT * FROM costcenter WHERE deleted='N' ORDER BY nm_costcenter ASC")->result_array();
		return $query;
	}

	function get_list_jenis_rutin(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT * FROM con_nonmat_category_awal WHERE `delete`='N' ORDER BY category ASC")->result_array();
		return $query;
	}
	
	function get_list_rutin(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT * FROM con_nonmat_new WHERE `deleted`='N' ORDER BY category_awal ASC, material_name ASC, spec ASC")->result_array();
		return $query;
	}
	
	function get_satuan_html(){
		$CI 	=& get_instance();
		$satuan	= $CI->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY kode_satuan ASC ")->result_array();
		$option = "";
		foreach($satuan AS $val2 => $valx2){
			$option .= "<option value='".$valx2['id_satuan']."'>".strtoupper($valx2['kode_satuan'])."</option>";
		}
		
		return $option;
	}
	
	function get_satuan(){
		$CI 	=& get_instance();
		$satuan	= $CI->db->query("SELECT * FROM raw_pieces WHERE `delete`='N' ORDER BY kode_satuan ASC ")->result_array();
		return $satuan;
	}
	
	function get_qty_pr($material){
		$CI 	=& get_instance();
		$qty	= $CI->db->query("SELECT * FROM cal_qty_pr WHERE id_material='".$material."' LIMIT 1")->result();
		$qty_pr 		= (!empty($qty[0]->qty_pr))?$qty[0]->qty_pr:0;
		$qty_belum_app 	= (!empty($qty[0]->qty_belum_app))?$qty[0]->qty_belum_app:0;
		$qty_in 		= (!empty($qty[0]->qty_in))?$qty[0]->qty_in:0;

		$qty_all= ($qty_pr+$qty_belum_app) - $qty_in;
		
		return $qty_all;
	}
	
	function get_qty_pr_rutin($material){
		$CI 	=& get_instance();
		$qty	= $CI->db->query("SELECT * FROM cal_qty_pr_rutin WHERE id_material='".$material."' LIMIT 1")->result();
		$qty_pr = (!empty($qty[0]->qty_pr))?$qty[0]->qty_pr:0;
		$qty_in = (!empty($qty[0]->qty_in))?$qty[0]->qty_in:0;
		$qty_all= number_format($qty_pr - $qty_in,2);
		
		return $qty_all;
	}
	
	function get_status_approve_pr($id){
		$CI 	=& get_instance();
		$detail	= $CI->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail WHERE id='".$id."'")->result();
		
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;
		$field 		= 'no_ipp';
		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
			$field 	= "no_ipp LIKE '".$tanda."%' AND DATE(created_date)";
		}
		$sql_cek	= "	(SELECT id FROM warehouse_planning_detail_acc WHERE ".$field."='".$no_ipp."' AND purchase > 0 AND sts_app = 'N' GROUP BY id_material)
						UNION
						(SELECT id FROM warehouse_planning_detail WHERE ".$field."='".$no_ipp."' AND purchase > 0 AND sts_app = 'N' GROUP BY id_material)";
		$cek		= $CI->db->query($sql_cek)->num_rows();
		
		return $cek;
	}
	
	function get_status_approve_pr_acc($id){
		$CI 	=& get_instance();
		$detail	= $CI->db->query("SELECT no_ipp, created_date FROM warehouse_planning_detail_acc WHERE id='".$id."'")->result();
		
		$tanda 		= substr($detail[0]->no_ipp,0,1);
		$no_ipp 	= $detail[0]->no_ipp;
		$field 		= 'no_ipp';
		if($tanda == 'P'){
			$no_ipp = date('Y-m-d', strtotime($detail[0]->created_date));
			$field 	= "no_ipp LIKE '".$tanda."%' AND DATE(created_date)";
		}
		$sql_cek	= "	(SELECT id FROM warehouse_planning_detail_acc WHERE ".$field."='".$no_ipp."' AND purchase > 0 AND sts_app = 'N' GROUP BY id_material)
						UNION
						(SELECT id FROM warehouse_planning_detail WHERE ".$field."='".$no_ipp."' AND purchase > 0 AND sts_app = 'N' GROUP BY id_material)";
		$cek		= $CI->db->query($sql_cek)->num_rows();
		
		return $cek;
	}
	
	function get_persen($id_bq, $category, $id_milik=''){
		$CI 	=& get_instance();
		$qty	= $CI->db->query("SELECT persen FROM cost_project_detail WHERE id_bq='".$id_bq."' AND caregory_sub='".$category."' AND (category = 'nonfrp' OR category = 'aksesoris' OR category = 'baut' OR category = 'plate' OR category = 'gasket' OR category = 'lainnya') ".($id_milik!=""?" and id_milik='".$id_milik."' ":"")." LIMIT 1")->result();
		$persen = (!empty($qty[0]->persen))?$qty[0]->persen:0;
		
		return $persen;
	}
	
	function get_extra($id_bq, $category, $id_milik=''){
		$CI 	=& get_instance();
		$qty	= $CI->db->query("SELECT extra FROM cost_project_detail WHERE id_bq='".$id_bq."' AND caregory_sub='".$category."' AND (category = 'nonfrp' OR category = 'aksesoris' OR category = 'baut' OR category = 'plate' OR category = 'gasket' OR category = 'lainnya') ".($id_milik!=""?" and id_milik='".$id_milik."' ":"")." LIMIT 1")->result();
		$extra = (!empty($qty[0]->extra))?$qty[0]->extra:0;
		
		return $extra;
	}
	
	function get_list_expired($material, $gudang){
		$CI 	=& get_instance();
		$sql	= "SELECT expired, qty_stock FROM warehouse_stock_expired WHERE id_material='".$material."' AND id_gudang='".$gudang."' AND qty_stock > 0 ORDER BY expired ASC";
		$result = $CI->db->query($sql)->result_array();
		
		$option = "";
		if(!empty($result)){
			$option .= "<option value='0'>Select An Expired</option>"; 
			foreach($result AS $val => $valx){
				if($valx['expired'] <> NULL AND $valx['expired'] <> '0000-00-00'){
					$option .= "<option value='".$valx['expired']."'>".date('d-M-Y', strtotime($valx['expired']))."</option>";
				}
				else{
					$option .= "<option value='0'>Expired not set</option>"; 
				}
			}
		}
		if(empty($result)){
			$option .= "<option value='0'>Stock empty</option>";
		}
		
		return $option;
	}
	
	function get_list_kurs(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT a.kode_dari, b.negara FROM kurs a LEFT JOIN currency b ON a.kode_dari=b.kode")->result_array();
		return $query;
	}

	function get_list_kurs2(){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT b.kode AS kode_dari, b.negara FROM currency b WHERE flag = '1'")->result_array();
		return $query;
	}
	
	function get_kurs($dari, $ke){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT a.kurs FROM kurs a WHERE a.kode_dari='".$dari."' AND a.kode_ke='".$ke."' ORDER BY update_date DESC LIMIT 1 ")->result();
		$kurs	= (!empty($query))?$query[0]->kurs:0;
		return $kurs;
	}
	
	function get_nomor_so($no_bq){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT so_number, no_ipp FROM so_bf_header WHERE no_ipp='".$no_bq."' LIMIT 1")->result();
		$data 	= (!empty($query[0]->so_number))?$query[0]->so_number:'';
		return $data;
	}
	function get_nomor_so_po($no_bq){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT so_number, no_ipp FROM so_bf_header WHERE no_ipp='".$no_bq."'")->result();
		$data 	= (!empty($query[0]->so_number))?$query[0]->so_number:'';
		return $data;
	}
	
	//FASTERST
	function SUM_Quo_Material_FAST($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= "SELECT a.price_total AS cost FROM cost_project_detail a WHERE a.id_bq = '".$id_bq."' AND a.category='material'";
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$dataSum	= $valx['cost'];
			$SUM 		+= $dataSum;
		}
		return $SUM;
	}
	
	function SUM_QUO_ALL_FAST($id_bq){
		$SUM_ALL = SUM_Quo_Material_FAST($id_bq) + SUM_EX_Material($id_bq);
		return $SUM_ALL;
	}

	function SUM_SO_Material_FAST($id_bq){
		$CI 		=& get_instance();
		$sqlQuo 	= "	SELECT
							a.qty AS qty_so,
							c.qty AS qty_bq,
							b.price_total AS cost
						FROM
							so_bf_detail_header a 
								LEFT JOIN cost_project_detail b ON a.id_milik=b.caregory_sub
								LEFT JOIN bq_detail_header c ON a.id_milik=c.id
						WHERE
							a.id_bq = '".$id_bq."' AND b.price_total > 0";
		$getSQL		= $CI->db->query($sqlQuo)->result_array();

		$SUM = 0;
		foreach($getSQL AS $val => $valx){
			$harga_satuan_so = $valx['cost'] / $valx['qty_bq'];

			$dataSum = 0;
			if($valx['qty_so'] <> 0){
				$dataSum = $harga_satuan_so * $valx['qty_so'];
			}
			$SUM += $dataSum;
		}
		return $SUM;
	}
	
	function SUM_SO_ALL_FAST($id_bq){
		$SUM_ALL = SUM_SO_Material_FAST($id_bq) + SUM_EX_Material_SO($id_bq);
		return $SUM_ALL;
	}
	
	function numberTowords($num){
		$ones = array(
			1 => "one",
			2 => "two",
			3 => "three",
			4 => "four",
			5 => "five",
			6 => "six",
			7 => "seven",
			8 => "eight",
			9 => "nine",
			10 => "ten",
			11 => "eleven",
			12 => "twelve",
			13 => "thirteen",
			14 => "fourteen",
			15 => "fifteen",
			16 => "sixteen",
			17 => "seventeen",
			18 => "eighteen",
			19 => "nineteen"
		);
		$tens = array(
			1 => "ten",
			2 => "twenty",
			3 => "thirty",
			4 => "forty",
			5 => "fifty",
			6 => "sixty",
			7 => "seventy",
			8 => "eighty",
			9 => "ninety"
		);
		$hundreds = array(
			"hundred",
			"thousand",
			"million",
			"billion",
			"trillion",
			"quadrillion"
		); //limit t quadrillion
		$num = number_format($num, 2, ".", ",");
		$num_arr = explode(".", $num);
		$wholenum = $num_arr[0];
		$decnum = $num_arr[1];
		$whole_arr = array_reverse(explode(",", $wholenum));
		krsort($whole_arr);
		$rettxt = "";
		foreach ($whole_arr as $key => $i)
		{
			if ($i < 20)
			{
				$rettxt .= $ones[$i];
			}
			elseif ($i < 100)
			{
				$rettxt .= $tens[substr($i, 0, 1) ];
				$rettxt .= " " . $ones[substr($i, 1, 1) ];
			}
			else
			{
				$rettxt .= $ones[substr($i, 0, 1) ] . " " . $hundreds[0];
				$rettxt .= " " . $tens[substr($i, 1, 1) ];
				$rettxt .= " " . $ones[substr($i, 2, 1) ];
			}
			if ($key > 0)
			{
				$rettxt .= " " . $hundreds[$key] . " ";
			}
		}
		if ($decnum > 0)
		{
			$rettxt .= " and ";
			if ($decnum < 20)
			{
				$rettxt .= $ones[$decnum];
			}
			elseif ($decnum < 100)
			{
				$rettxt .= $tens[substr($decnum, 0, 1) ];
				$rettxt .= " " . $ones[substr($decnum, 1, 1) ];
			}
		}
		return $rettxt;
	}
	
	function get_resin($id_bq){
		$CI 		=& get_instance();
		$sqlResin = "(SELECT id_material, nm_material  FROM bq_component_detail WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)
					 UNION
					(SELECT id_material, nm_material  FROM bq_component_detail_plus WHERE id_category='TYP-0001' AND id_bq = '".$id_bq."' GROUP BY id_material)";
		$ListBQipp		= $CI->db->query($sqlResin)->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['nm_material'];
		}
		$dtImplode	= "".implode(",  ", $dtListArray)."";
		
		return $dtImplode;
	}
	
	function get_name_acc($id){
		$CI =& get_instance();
		$get_detail = $CI->db->get_where('accessories', array('id'=>$id))->result();
		$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
		$nama_acc = "Not found"; 
		if(!empty($get_detail)){
			$nama_acc = $get_detail[0]->nama; 
			if($get_detail[0]->category == '1'){
				$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
			}
			if($get_detail[0]->category == '2'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($get_detail[0]->category == '3'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T x ".strtoupper($get_detail[0]->dimensi);
			}
			if($get_detail[0]->category == '4'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
			}
			if($get_detail[0]->category == '5'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->standart);
			}
		}
		
		return $nama_acc;
		
	}

	function get_name_acc_tanki($id){
		$CI =& get_instance();
		$get_detail = $CI->db->get_where('accessories', array('id_acc_tanki'=>$id))->result();
		$nama_acc = "Not found"; 
		if(!empty($get_detail)){
			if($get_detail[0]->category == '5'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->standart);
			}
		}
		
		return $nama_acc;
		
	}
	
	function gen_invoice($no_ipp){
		$CI =& get_instance();
		$LocInt	= substr($no_ipp, -1, 1);
		$m = date('m');
		$Y = date('Y');
//		$qIPP			= "SELECT MAX(no_invoice) as maxP FROM tr_invoice_header WHERE no_invoice LIKE 'PC_/__%/".$Y."' ";
		$qIPP			= "SELECT (no_invoice) as maxP FROM tr_invoice_header WHERE no_invoice LIKE 'PC_/__%/".$Y."' order by right(no_invoice,8) desc limit 1 ";
		$numrowIPP		= $CI->db->query($qIPP)->num_rows();
		$resultIPP		= $CI->db->query($qIPP)->result_array();
		$angkaUrut2		= $resultIPP[0]['maxP'];
		$urutan2		= (int)substr($angkaUrut2, 6, 3);
		$urutan2++;
		$urut2			= sprintf('%03s',$urutan2);
		$no_invoice		= "PC".$LocInt."/".$m.$urut2."/".$Y;
		
		return $no_invoice;
    }
	
	function persen_progress_produksi($id_produksi){
		$CI 		=& get_instance();
		$qDetail	= "SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."'";
		$row		= $CI->db->query($qDetail)->result_array();

		$HelpDet 	= "bq_detail_header";
		if($row[0]['jalur'] == 'FD'){
			$HelpDet = "so_detail_header";
		}

		$qDetail	= "	SELECT
							a.*,
							b.no_komponen,
							b.id_category AS comp,
							c.type AS typeProduct,
							b.id AS id_uniq
						FROM
							production_detail a
							LEFT JOIN product_parent c ON a.id_category = c.product_parent
							LEFT JOIN ".$HelpDet." b ON a.id_milik = b.id
						WHERE
							a.id_produksi = '".$id_produksi."'
						GROUP BY
							b.no_komponen,
							a.sts_delivery,
							a.id_product
						ORDER BY
							b.id_bq_header ASC";
		$rowD		= $CI->db->query($qDetail)->result_array();
		
		$GET_MATERIAL_FIELD = get_MaterialOutJoint();
		$GET_MATERIAL_FIELD_EST = get_MaterialEstJoint();

		$SUM_PROGRESS = 0;
		foreach($rowD AS $val => $valx){
			//check selain shop joint & type field
			if($valx['typeProduct'] != 'field'){
				$sqlCheck2 	= $CI->db
									->select('COUNT(*) as Numc')
									->group_start()
									->group_start()
									->where('daycode !=', NULL)
									->where('daycode !=', '')
									->group_end()
									->or_where('id_deadstok_dipakai !=', NULL)
									->group_end()
									->get_where('production_detail', 
										array(
											'id_milik'=>$valx['id_milik'],
											'id_produksi'=>$valx['id_produksi']
											)
										)
									->result();
				$QTY 		= $valx['qty'];
				$ACT 		= $sqlCheck2[0]->Numc;
				
				$progress = 0;
				if($ACT != 0 AND $QTY != 0){
					$progress 	= ($ACT/$QTY) *(100);
				}

				$SUM_PROGRESS += $progress;
			}
			//check type field
			if($valx['typeProduct'] == 'field'){
				$sqlCheck2 	= $CI->db->select('SUM(qty) as Numc')->get_where('outgoing_field_joint', array('id_milik'=>$valx['id_milik'],'no_ipp'=>str_replace('PRO-','',$valx['id_produksi']),'deleted_date'=>NULL))->result();
				$QTY 		= $valx['qty'];
				$ACT 		= $sqlCheck2[0]->Numc;
				
				$progress = 0;
				if($ACT != 0 AND $QTY != 0){
					$progress 	= ($ACT/$QTY) *(100);
				}

				$SUM_PROGRESS += $progress;
			}
			//check shop joint
			if (in_array($valx['comp'], NotInProductArray())) {
				$sqlCheck2 	= $CI->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi']))->result();
				$QTY_ 		= $valx['qty'];
				
				$checkActShopJoin 	= $CI->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'closing_produksi_date !='=>NULL))->result();
				$ACT_OUT_ 	= $checkActShopJoin[0]->Numc;

				$progress = 0;
				if($ACT_OUT_ > 0 AND $QTY_ > 0){
					$progress 	= ($ACT_OUT_/$QTY_) *(100);
				}

				$SUM_PROGRESS += $progress;
			}
		}

		$Progresss = 0;
		if($SUM_PROGRESS > 0 AND COUNT($rowD) > 0 ){
			$Progresss = $SUM_PROGRESS / COUNT($rowD);
		}
		
		return $Progresss;
	}
	
	function insert_est_bq_material($id_bq=NULL){
		$CI 			=& get_instance();
		$path			= $CI->uri->segment(1);
		$data_session	= $CI->session->userdata;
		$userID			= $data_session['ORI_User']['username'];
		
		$sql_material_down_down = "	SELECT
										a.id_detail AS id_detail,
										a.id_milik AS id_milik,
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.detail_name AS detail_name,
										a.id_category AS id_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										max( a.last_cost ) AS last_cost,
										a.price_mat AS price_mat 
									FROM
										bq_component_detail a
									WHERE
										(
											( a.id_category = 'TYP-0001' ) 
											AND ( a.id_material <> 'MTL-1903000' ) 
											AND ( NOT ( ( a.id_product LIKE '_J%' ) ) ) 
										)
										AND a.id_bq = '".$id_bq."'
									GROUP BY
										a.detail_name,
										a.id_milik,
										a.id_material 
									ORDER BY
										a.id_detail DESC";
		$result_material_down_down = $CI->db->query($sql_material_down_down)->result_array();
		$ArrBMD = [];
		foreach($result_material_down_down AS $val => $valx){
			// if($valx['id_milik'] == 59029){
			$ArrBMD[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMD[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMD[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMD[$val]['id_product'] = $valx['id_product'];
			$ArrBMD[$val]['id_category'] = $valx['id_category'];
			$ArrBMD[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMD[$val]['id_material'] = $valx['id_material'];
			$ArrBMD[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMD[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMD[$val]['last_cost'] = $valx['last_cost'];
			$ArrBMD[$val]['price_mat'] = $valx['price_mat'];
			$ArrBMD[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMD[$val]['hist_date'] = date('Y-m-d H:i:s');
			// }
		}
		if(!empty($ArrBMD)){
		$CI->db->delete('est_bq_material_down', array('hist_by'=>$data_session['ORI_User']['username']));
		$CI->db->insert_batch('est_bq_material_down', $ArrBMD);
		}

		// echo '<pre>';
		// print_r($ArrBMD);
		// exit;
		
		$sql_material_down_down = "	SELECT
										a.id_detail AS id_detail,
										a.id_milik AS id_milik,
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.detail_name AS detail_name,
										a.id_category AS id_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										max( a.last_cost ) AS last_cost,
										a.price_mat AS price_mat 
									FROM
										est_bq_material_down a
									WHERE
										(
											( a.id_category = 'TYP-0001' ) 
											AND ( a.id_material <> 'MTL-1903000' ) 
											AND ( NOT ( ( a.id_product LIKE '_J%' ) ) ) 
										)
										AND a.id_bq = '".$id_bq."'
										AND a.hist_by='".$data_session['ORI_User']['username']."'
									GROUP BY
										a.detail_name,
										a.id_milik,
										a.id_material 
									ORDER BY
										a.id_detail DESC";
		$result_material_down_down = $CI->db->query($sql_material_down_down)->result_array();
		$ArrBMD = [];
		foreach($result_material_down_down AS $val => $valx){
			// if($valx['id_milik'] == 59029){
			$ArrBMD[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMD[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMD[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMD[$val]['id_product'] = $valx['id_product'];
			$ArrBMD[$val]['id_category'] = $valx['id_category'];
			$ArrBMD[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMD[$val]['id_material'] = $valx['id_material'];
			$ArrBMD[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMD[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMD[$val]['last_cost'] = $valx['last_cost'];
			$ArrBMD[$val]['price_mat'] = $valx['price_mat'];
			$ArrBMD[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMD[$val]['hist_date'] = date('Y-m-d H:i:s');
			// }
		}
		if(!empty($ArrBMD)){
		$CI->db->delete('est_bq_material_down', array('hist_by'=>$data_session['ORI_User']['username']));
		$CI->db->insert_batch('est_bq_material_down', $ArrBMD);
		}
		// echo '<pre>';
		// print_r($ArrBMD);
		// exit;
		//akhir
		$sql_material_down_down = "	( SELECT
											est_bq_material_down.id_detail AS id_detail,
											est_bq_material_down.id_milik AS id_milik,
											est_bq_material_down.id_bq AS id_bq,
											est_bq_material_down.id_product AS id_product,
											est_bq_material_down.detail_name AS detail_name,
											est_bq_material_down.id_category AS id_category,
											est_bq_material_down.nm_category AS nm_category,
											est_bq_material_down.id_material AS id_material,
											est_bq_material_down.nm_material AS nm_material,
											max( est_bq_material_down.last_cost ) AS last_cost,
											est_bq_material_down.price_mat AS price_mat 
										FROM
											est_bq_material_down 
										WHERE
											est_bq_material_down.id_bq = '".$id_bq."'
											AND est_bq_material_down.hist_by='".$data_session['ORI_User']['username']."'
										GROUP BY
											est_bq_material_down.detail_name,
											est_bq_material_down.id_milik 
										) UNION
										(
										SELECT
											bq_component_detail_plus.id_detail AS id_detail,
											bq_component_detail_plus.id_milik AS id_milik,
											bq_component_detail_plus.id_bq AS id_bq,
											bq_component_detail_plus.id_product AS id_product,
											bq_component_detail_plus.detail_name AS detail_name,
											bq_component_detail_plus.id_category AS id_category,
											bq_component_detail_plus.nm_category AS nm_category,
											bq_component_detail_plus.id_material AS id_material,
											bq_component_detail_plus.nm_material AS nm_material,
											bq_component_detail_plus.last_cost AS last_cost,
											bq_component_detail_plus.price_mat AS price_mat 
										FROM
											bq_component_detail_plus 
										WHERE
											( ( bq_component_detail_plus.id_category = 'TYP-0001' ) AND ( bq_component_detail_plus.id_material <> 'MTL-1903000' ) ) AND bq_component_detail_plus.id_bq = '".$id_bq."'
										) UNION
										(
										SELECT
											bq_component_detail.id_detail AS id_detail,
											bq_component_detail.id_milik AS id_milik,
											bq_component_detail.id_bq AS id_bq,
											bq_component_detail.id_product AS id_product,
											bq_component_detail.detail_name AS detail_name,
											bq_component_detail.id_category AS id_category,
											bq_component_detail.nm_category AS nm_category,
											bq_component_detail.id_material AS id_material,
											bq_component_detail.nm_material AS nm_material,
											bq_component_detail.last_cost AS last_cost,
											bq_component_detail.price_mat AS price_mat 
										FROM
											bq_component_detail 
										WHERE
										( ( bq_component_detail.id_category = 'TYP-0001' ) AND ( bq_component_detail.id_material <> 'MTL-1903000' ) AND ( bq_component_detail.id_product LIKE '_J%' ) ) AND bq_component_detail.id_bq = '".$id_bq."'
										)";
		$result_material_down_down = $CI->db->query($sql_material_down_down)->result_array();
		$ArrBMD = [];
		foreach($result_material_down_down AS $val => $valx){
			$KEY_ID = $valx['id_detail'];
			// if($valx['id_milik'] == 59029){
			$ArrBMD[$KEY_ID]['id_detail'] = $valx['id_detail'];
			$ArrBMD[$KEY_ID]['id_milik'] = $valx['id_milik'];
			$ArrBMD[$KEY_ID]['id_bq'] = $valx['id_bq'];
			$ArrBMD[$KEY_ID]['id_product'] = $valx['id_product'];
			$ArrBMD[$KEY_ID]['id_category'] = $valx['id_category'];
			$ArrBMD[$KEY_ID]['nm_category'] = $valx['nm_category'];
			$ArrBMD[$KEY_ID]['id_material'] = $valx['id_material'];
			$ArrBMD[$KEY_ID]['nm_material'] = $valx['nm_material'];
			$ArrBMD[$KEY_ID]['detail_name'] = $valx['detail_name'];
			$ArrBMD[$KEY_ID]['last_cost'] = $valx['last_cost'];
			$ArrBMD[$KEY_ID]['price_mat'] = $valx['price_mat'];
			$ArrBMD[$KEY_ID]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMD[$KEY_ID]['hist_date'] = date('Y-m-d H:i:s');
			// }
		}
		// echo '<pre>';
		// print_r($ArrBMD);
		// exit;
		if(!empty($ArrBMD)){
		$CI->db->delete('est_bq_material_down', array('hist_by'=>$data_session['ORI_User']['username']));
		$CI->db->insert_batch('est_bq_material_down', $ArrBMD);
		}
		
		//awal
		$sql_material_down_down = "	SELECT
										a.id_detail AS id_detail,
										a.id_milik AS id_milik,
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.detail_name AS detail_name,
										a.id_category AS id_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										a.last_cost AS last_cost,
										a.price_mat AS price_mat 
									FROM
										bq_component_detail a 
									WHERE
										( ( a.id_category <> 'TYP-0030' ) AND ( a.id_category <> 'TYP-0001' ) AND ( a.id_material <> 'MTL-1903000' )  AND a.id_bq = '".$id_bq."' ) UNION
									SELECT
										b.id_detail AS id_detail,
										b.id_milik AS id_milik,
										b.id_bq AS id_bq,
										b.id_product AS id_product,
										b.detail_name AS detail_name,
										b.id_category AS id_category,
										b.nm_category AS nm_category,
										b.id_material AS id_material,
										b.nm_material AS nm_material,
										b.last_cost AS last_cost,
										b.price_mat AS price_mat 
									FROM
										bq_component_detail_plus b 
									WHERE
										( ( b.id_category <> 'TYP-0030' ) AND ( b.id_category <> 'TYP-0001' ) AND ( b.id_material <> 'MTL-1903000' ) AND b.id_bq = '".$id_bq."' ) UNION
									SELECT
										c.id_detail AS id_detail,
										c.id_milik AS id_milik,
										c.id_bq AS id_bq,
										c.id_product AS id_product,
										c.detail_name AS detail_name,
										c.id_category AS id_category,
										c.nm_category AS nm_category,
										c.id_material AS id_material,
										c.nm_material AS nm_material,
										c.last_cost AS last_cost,
										c.price_mat AS price_mat 
									FROM
										bq_component_detail_add c 
									WHERE
										( ( c.id_category <> 'TYP-0030' ) AND ( c.id_material <> 'MTL-1903000' ) ) AND c.id_bq = '".$id_bq."'";
		$result_material_down_down = $CI->db->query($sql_material_down_down)->result_array();
		$ArrBMD = [];
		foreach($result_material_down_down AS $val => $valx){
			$ArrBMD[$val]['id_detail'] = $valx['id_detail'];
			$ArrBMD[$val]['id_milik'] = $valx['id_milik'];
			$ArrBMD[$val]['id_bq'] = $valx['id_bq'];
			$ArrBMD[$val]['id_product'] = $valx['id_product'];
			$ArrBMD[$val]['id_category'] = $valx['id_category'];
			$ArrBMD[$val]['nm_category'] = $valx['nm_category'];
			$ArrBMD[$val]['id_material'] = $valx['id_material'];
			$ArrBMD[$val]['nm_material'] = $valx['nm_material'];
			$ArrBMD[$val]['detail_name'] = $valx['detail_name'];
			$ArrBMD[$val]['last_cost'] = $valx['last_cost'];
			$ArrBMD[$val]['price_mat'] = $valx['price_mat'];
			$ArrBMD[$val]['hist_by'] = $data_session['ORI_User']['username'];
			$ArrBMD[$val]['hist_date'] = date('Y-m-d H:i:s');
		}
		if(!empty($ArrBMD)){
		$CI->db->delete('est_bq_material_top', array('hist_by'=>$data_session['ORI_User']['username']));
		$CI->db->insert_batch('est_bq_material_top', $ArrBMD);
		}
		
		//material akhir
		$sql_material_down_down = "( SELECT
										a.id_detail AS id_detail,
										a.id_milik AS id_milik,
										a.id_bq AS id_bq,
										a.id_product AS id_product,
										a.detail_name AS detail_name,
										a.id_category AS id_category,
										a.nm_category AS nm_category,
										a.id_material AS id_material,
										a.nm_material AS nm_material,
										round( a.last_cost, 3 ) AS last_cost,
										a.price_mat AS price_mat
									FROM
										est_bq_material_top a WHERE a.id_bq = '".$id_bq."' AND a.hist_by='".$data_session['ORI_User']['username']."'
									) UNION
									(
									SELECT
										b.id_detail AS id_detail,
										b.id_milik AS id_milik,
										b.id_bq AS id_bq,
										b.id_product AS id_product,
										b.detail_name AS detail_name,
										b.id_category AS id_category,
										b.nm_category AS nm_category,
										b.id_material AS id_material,
										b.nm_material AS nm_material,
										round( b.last_cost, 3 ) AS last_cost,
										b.price_mat AS price_mat
									FROM
										est_bq_material_down b WHERE b.id_bq = '".$id_bq."' AND b.hist_by='".$data_session['ORI_User']['username']."'
									)";
		// echo $sql_material_down_down;
		// exit;
		$result_material_down_down = $CI->db->query($sql_material_down_down)->result_array();
		$ArrBMDNew = [];
		foreach($result_material_down_down AS $val => $valx){
			$KEY_ID = $valx['id_detail'];
			// if($valx['id_milik'] == '68725' AND $valx['detail_name'] == 'TOPCOAT'){
				$ArrBMDNew[$KEY_ID]['id_detail'] = $valx['id_detail'];
				$ArrBMDNew[$KEY_ID]['id_milik'] = $valx['id_milik'];
				$ArrBMDNew[$KEY_ID]['id_bq'] = $valx['id_bq'];
				$ArrBMDNew[$KEY_ID]['id_product'] = $valx['id_product'];
				$ArrBMDNew[$KEY_ID]['id_category'] = $valx['id_category'];
				$ArrBMDNew[$KEY_ID]['nm_category'] = $valx['nm_category'];
				$ArrBMDNew[$KEY_ID]['id_material'] = $valx['id_material'];
				$ArrBMDNew[$KEY_ID]['nm_material'] = $valx['nm_material'];
				$ArrBMDNew[$KEY_ID]['detail_name'] = $valx['detail_name'];
				$ArrBMDNew[$KEY_ID]['last_cost'] = $valx['last_cost'];
				$ArrBMDNew[$KEY_ID]['price_mat'] = $valx['price_mat'];
				$ArrBMDNew[$KEY_ID]['hist_by'] = $data_session['ORI_User']['username'];
				$ArrBMDNew[$KEY_ID]['hist_date'] = date('Y-m-d H:i:s');
			// }
		}
		// echo "<pre>";
		// print_r($ArrBMDNew);
		// exit;
		if(!empty($ArrBMDNew)){
		$CI->db->delete('est_bq_material', array('hist_by'=>$data_session['ORI_User']['username']));
		$CI->db->insert_batch('est_bq_material', $ArrBMDNew);
		}
		
	}

	function check_atatus_pr($tanda, $no_ipp2, $id_user){
		$CI 		=& get_instance();

		//check approved
		if($tanda == 'I'){
			$check_approved1 = $CI->db->get_where('warehouse_planning_detail',array('sts_app'=>'N','no_ipp'=>$no_ipp2,'purchase >'=>0))->num_rows();
			$check_approved2 = $CI->db->get_where('warehouse_planning_detail_acc',array('sts_app'=>'N','no_ipp'=>$no_ipp2,'purchase >'=>0))->num_rows();
		}
		if($tanda == 'P'){
			$check_approved1 = $CI->db->get_where('warehouse_planning_detail',array('sts_app'=>'N','DATE(created_date)'=>$no_ipp2,'purchase >'=>0))->num_rows();
			$check_approved2 = $CI->db->get_where('warehouse_planning_detail_acc',array('sts_app'=>'N','DATE(created_date)'=>$no_ipp2,'purchase >'=>0))->num_rows();
		}

		$check_num = $check_approved1 + $check_approved2;

		return $check_num;
	}

	function update_berat_est($id_product){
		$CI 	=& get_instance();
		$header = $CI->db->get_where('component_header', array('id_product'=>$id_product))->result();
		$berat1 = get_weight_comp($id_product, $header[0]->series, $header[0]->parent_product, $header[0]->diameter, $header[0]->diameter2)['weight'];
		$berat = (!empty($berat1))? $berat1 : 0;

		$CI->db->where('id_product', $id_product);
		$CI->db->update('component_header', array('berat'=>$berat));

	}

	function get_berat_est($id_product){
		$CI 	=& get_instance();
		$header = $CI->db->order_by('updated_date','desc')->get_where('table_product_list', array('id_product'=>$id_product))->result();
		$berat = (!empty($header))? $header[0]->weight : 0;

		return $berat;
	}

	function check_status_N($id_bq_header){
		$CI 	=& get_instance();
		$get_qty = $CI->db->group_by('id_bq_header')->select('COUNT(id) AS sisa')->get_where('so_detail_detail', array('id_bq_header'=>$id_bq_header,'approve'=>'N'))->result();
		$sisa_qty = (!empty($get_qty))?$get_qty[0]->sisa:0;
		return $sisa_qty;
	}

	function check_status_Y($id_bq_header){
		$CI 	=& get_instance();
		$get_qty = $CI->db->group_by('id_bq_header')->select('COUNT(id) AS sisa')->get_where('so_detail_detail', array('id_bq_header'=>$id_bq_header,'approve'=>'Y'))->result();
		$sisa_qty = (!empty($get_qty))?$get_qty[0]->sisa:0;
		return $sisa_qty;
	}

	function check_status_P($id_bq_header){
		$CI 	=& get_instance();
		$get_qty = $CI->db->group_by('id_bq_header')->select('COUNT(id) AS sisa')->get_where('so_detail_detail', array('id_bq_header'=>$id_bq_header,'approve'=>'P'))->result();
		$sisa_qty = (!empty($get_qty))?$get_qty[0]->sisa:0;
		return $sisa_qty;
	}

	function check_status($id_bq_header){
		$CI 	=& get_instance();

		$QTY_Q = $CI->db->limit(1)->select('qty')->get_where('so_detail_detail', array('id_bq_header'=>$id_bq_header))->result();
		$qty_so = (!empty($QTY_Q))?$QTY_Q[0]->qty:0;

		$stsN = check_status_N($id_bq_header);
		$stsY = check_status_Y($id_bq_header);
		$stsP = check_status_P($id_bq_header);

		$status = 'Y';
		if($qty_so == $stsN){
			$status = 'N';
		}
		if($qty_so == $stsY){
			$status = 'Y';
		}
		if($qty_so == $stsP){
			$status = 'P';
		}

		$ArrUpdate	= array(
			'approve' => $status
		);

		$CI->db->where('id_bq_header', $id_bq_header);
		$CI->db->update('so_detail_header', $ArrUpdate);
		
	}

	function check_status_all($id_bq=null,$wherein=null){
		$CI 	=& get_instance();

		$check_all = $CI->db->select('*')->from('so_detail_header')->where('id_bq',$id_bq)->where_in('id',$wherein)->get()->result_array();
		$ArrUpdate = [];
		foreach($check_all AS $val => $value){
			$QTY_Q = $CI->db->limit(1)->select('qty')->get_where('so_detail_detail', array('id_bq_header'=>$value['id_bq_header']))->result();
			$qty_so = (!empty($QTY_Q))?$QTY_Q[0]->qty:0;
			$id_bq_header = $value['id_bq_header'];
			$stsN = check_status_N($id_bq_header);
			$stsY = check_status_Y($id_bq_header);
			$stsP = check_status_P($id_bq_header);

			$status = 'Y';
			if($qty_so == $stsN){
				$status = 'N';
			}
			if($qty_so == $stsY){
				$status = 'Y';
			}
			if($qty_so == $stsP){
				$status = 'P';
			}

			// echo $qty_so.'<br>';
			// echo $stsN.'<br>';
			// echo $stsY.'<br>';
			// echo $stsP.'<br>';

			$ArrUpdate[$val]['id'] 		= $value['id'];
			$ArrUpdate[$val]['approve'] = $status;

		}

		// print_r($ArrUpdate);
		// exit;

		$CI->db->update_batch('so_detail_header', $ArrUpdate,'id');
		
	}

	function move_warehouse_fg($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_delivery=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];
		$kode_trans 	= $kode_delivery;
		$kd_gudang_dari = get_name('warehouse', 'kode', 'id', $id_gudang_dari);
		$kd_gudang_ke	= NULL;
		if($id_gudang_ke != null){
			$kd_gudang_ke 	= get_name('warehouse', 'kode', 'id', $id_gudang_ke);
		}
		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();

		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG
			$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key))->result();

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $UserName;
				$ArrStock[$key]['update_date'] 	= $dateTime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kd_gudang_ke;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_trans;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan gudang';
				$ArrHist[$key]['update_by'] 		= $UserName;
				$ArrHist[$key]['update_date'] 		= $dateTime;
			}
			else{
				$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value;
				$ArrStockInsert[$key]['update_by'] 		= $UserName;
				$ArrStockInsert[$key]['update_date'] 	= $dateTime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrHistInsert[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
				$ArrHistInsert[$key]['qty_stock_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_stock_akhir']     = 0 - $value;
				$ArrHistInsert[$key]['qty_booking_awal']    = 0;
				$ArrHistInsert[$key]['qty_booking_akhir']   = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_trans;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value;
				$ArrHistInsert[$key]['ket'] 			= 'pengurangan gudang (insert new)';
				$ArrHistInsert[$key]['update_by'] 		= $UserName;
				$ArrHistInsert[$key]['update_date'] 	= $dateTime;
			}

			//PENAMBAHAN GUDANG
			if($id_gudang_ke != null){
				$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$key))->result();

				if(!empty($rest_pusat)){
					$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
					$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrStock2[$key]['update_by'] 	=  $UserName;
					$ArrStock2[$key]['update_date'] 	= $dateTime;

					$ArrHist2[$key]['id_material'] 	= $key;
					$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
					$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
					$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
					$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
					$ArrHist2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHist2[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHist2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
					$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrHist2[$key]['qty_booking_awal'] = $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_booking_akhir']= $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHist2[$key]['jumlah_mat'] 		= $value;
					$ArrHist2[$key]['ket'] 				= 'penambahan gudang';
					$ArrHist2[$key]['update_by'] 		= $UserName;
					$ArrHist2[$key]['update_date'] 		= $dateTime;
				}
				else{
					$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

					$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrStockInsert2[$key]['id_gudang'] 	= $id_gudang_ke;
					$ArrStockInsert2[$key]['kd_gudang'] 	= $kd_gudang_ke;
					$ArrStockInsert2[$key]['qty_stock'] 	= $value;
					$ArrStockInsert2[$key]['update_by'] 	= $UserName;
					$ArrStockInsert2[$key]['update_date'] 	= $dateTime;

					$ArrHistInsert2[$key]['id_material'] 	= $key;
					$ArrHistInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHistInsert2[$key]['id_gudang_dari'] = $id_gudang_dari;
					$ArrHistInsert2[$key]['kd_gudang_dari'] = $kd_gudang_dari;
					$ArrHistInsert2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value;
					$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_booking_akhir']  = 0;
					$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
					$ArrHistInsert2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHistInsert2[$key]['jumlah_mat'] 		= $value;
					$ArrHistInsert2[$key]['ket'] 				= 'penambahan gudang (insert new)';
					$ArrHistInsert2[$key]['update_by'] 		    = $UserName;
					$ArrHistInsert2[$key]['update_date'] 		= $dateTime;
				}
			}
		}

		// print_r($ArrStock);
		// print_r($ArrStockInsert);
		// print_r($ArrStock2);
		// print_r($ArrStockInsert2);
		// exit;

		if(!empty($ArrStock)){
			$CI->db->update_batch('warehouse_stock', $ArrStock, 'id');
		}
		if(!empty($ArrHist)){
			$CI->db->insert_batch('warehouse_history', $ArrHist);
		}

		if(!empty($ArrStockInsert)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert);
		}
		if(!empty($ArrHistInsert)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert);
		}

		if(!empty($ArrStock2)){
			$CI->db->update_batch('warehouse_stock', $ArrStock2, 'id');
		}
		if(!empty($ArrHist2)){
			$CI->db->insert_batch('warehouse_history', $ArrHist2);
		}

		if(!empty($ArrStockInsert2)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert2);
		}
		if(!empty($ArrHistInsert2)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert2);
		}
		
	}

	function move_warehouse($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_delivery=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];
		$kode_trans 	= $kode_delivery;
		$kd_gudang_dari = get_name('warehouse', 'kode', 'id', $id_gudang_dari);
		$kd_gudang_ke	= NULL;
		if($id_gudang_ke != null){
			$kd_gudang_ke 	= get_name('warehouse', 'kode', 'id', $id_gudang_ke);
		}
		//grouping sum
		$temp = []; 
		$temp2 = [];
		// $value_qty = 0;
		foreach($ArrUpdateStock as $value) {		
         
			//grouping sum
				$temp = [];
				foreach($ArrUpdateStock as $value) {
					if(!array_key_exists($value['id'], $temp)) {
						$temp[$value['id']] = 0;
					}
					$temp[$value['id']] += $value['qty'];
				}

			//   $temp[$value['id']] = 
			//   [
			// 	'qty'          => += $value['qty'];
			// 	'harga_pusat'  => $value['harga_pusat'], 
			// 	'harga_tujuan' => $PRICE2,
			// 	'harga_baru'   => $PRICENEW,
			//   ];

			 
			
		}

		   

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();

		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG
			$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key))->result();
            
			//ambil saldo akhir 
				$key = $value['id_material'];
				$stokjurnalakhir=0;
				$nilaijurnalakhir=0;
				$PRICE=0;
				$bmunit = 0;
				$bm = 0;
          
                $qty_akhir = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				$costbook = $CI->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang, 'id_material'=>$key),1)->row();
				
				
				if(!empty($costbook)) $PRICE=$costbook->harga;
				if(!empty($qty_akhir)) $stokjurnalakhir=$qty_akhir->qty_stock;				
				if(!empty($qty_akhir)) $nilaijurnalakhir=$PRICE*$stokjurnalakhir;


				$qty_akhir2 = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$key),1)->row();
				$costbook2 = $CI->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_wip, 'id_material'=>$key),1)->row();
				
				
				if(!empty($costbook2)) $PRICE2=$costbook2->harga;
				if(!empty($qty_akhir2)) $stokjurnalakhir2=$qty_akhir2->qty_stock;				
				if(!empty($qty_akhir2)) $nilaijurnalakhir2=$PRICE2*$stokjurnalakhir2;
				
				

				$PRICENEW = (($PRICE*$value) + ($PRICE2*$stokjurnalakhir2))/($value+$stokjurnalakhir2);

				
			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['harga'] 	    = $value['harga_pusat'];//syam 28/11/2025
				$ArrStock[$key]['total_harga'] 	= ($rest_pusat[0]->qty_stock - $value) * $value['harga_pusat'];;//syam 28/11/2025
				$ArrStock[$key]['update_by'] 	= $UserName;
				$ArrStock[$key]['update_date'] 	= $dateTime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kd_gudang_ke;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_trans;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan gudang';
				$ArrHist[$key]['update_by'] 		= $UserName;
				$ArrHist[$key]['update_date'] 		= $dateTime;

				$ArrHist[$key]['harga'] 			= $PRICE;;
				$ArrHist[$key]['total_harga'] 		= $PRICE*$value;
				$ArrHist[$key]['saldo_awal']		= $rest_pusat[0]->qty_stock*$PRICE;
				$ArrHist[$key]['saldo_akhir']		= ($rest_pusat[0]->qty_stock - $value)*$PRICE;
				$ArrHist[$key]['harga_baru'] 		= $PRICE;
			}
			else{
				$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value;
				$ArrStockInsert[$key]['update_by'] 		= $UserName;
				$ArrStockInsert[$key]['update_date'] 	= $dateTime;
				$ArrStockInsert[$key]['harga'] 	         = $PRICE;//syam 28/11/2025
				$ArrStockInsert[$key]['total_harga'] 	= (0 - $value) * $PRICE;//syam 28/11/2025

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrHistInsert[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
				$ArrHistInsert[$key]['qty_stock_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_stock_akhir']     = 0 - $value;
				$ArrHistInsert[$key]['qty_booking_awal']    = 0;
				$ArrHistInsert[$key]['qty_booking_akhir']   = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_trans;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value;
				$ArrHistInsert[$key]['ket'] 			= 'pengurangan gudang (insert new)';
				$ArrHistInsert[$key]['update_by'] 		= $UserName;
				$ArrHistInsert[$key]['update_date'] 	= $dateTime;

				$ArrHistInsert[$key]['harga'] 			= $PRICE;//syam 28/11/2025
				$ArrHistInsert[$key]['total_harga'] 	= $PRICE*$value;
				$ArrHistInsert[$key]['saldo_awal']		= 0;
				$ArrHistInsert[$key]['saldo_akhir']		=  (0 - $value)*$PRICE;
				$ArrHistInsert[$key]['harga_baru'] 		= $PRICE; 
			}

			//PENAMBAHAN GUDANG
			if($id_gudang_ke != null){
				$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$key))->result();

				if(!empty($rest_pusat)){
					$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
					$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrStock2[$key]['update_by'] 	=  $UserName;
					$ArrStock2[$key]['update_date'] 	= $dateTime;
					$ArrStock2[$key]['harga'] 	        = $PRICENEW;//syam 28/11/2025
				    $ArrStock2[$key]['total_harga'] 	= ($rest_pusat[0]->qty_stock + $value) * $PRICENEW;//syam 28/11/2025

					$ArrHist2[$key]['id_material'] 	= $key;
					$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
					$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
					$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
					$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category; 
					$ArrHist2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHist2[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHist2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
					$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrHist2[$key]['qty_booking_awal'] = $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_booking_akhir']= $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHist2[$key]['jumlah_mat'] 		= $value;
					$ArrHist2[$key]['ket'] 				= 'penambahan gudang';
					$ArrHist2[$key]['update_by'] 		= $UserName;
					$ArrHist2[$key]['update_date'] 		= $dateTime;

					$ArrHist2[$key]['harga'] 			= $PRICE;//syam 28/11/2025
					$ArrHist2[$key]['total_harga'] 	    = $PRICE*$value;
					$ArrHist2[$key]['saldo_awal']		= $rest_pusat[0]->qty_stock*$PRICE2;
					$ArrHist2[$key]['saldo_akhir']		= ($rest_pusat[0]->qty_stock + $value)*$PRICENEW;
					$ArrHist2[$key]['harga_baru'] 		= $PRICENEW;
				}
				else{
					$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

					$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrStockInsert2[$key]['id_gudang'] 	= $id_gudang_ke;
					$ArrStockInsert2[$key]['kd_gudang'] 	= $kd_gudang_ke;
					$ArrStockInsert2[$key]['qty_stock'] 	= $value;
					$ArrStockInsert2[$key]['update_by'] 	= $UserName;
					$ArrStockInsert2[$key]['update_date'] 	= $dateTime;
					$ArrStockInsert2[$key]['harga'] 	    = $PRICENEW;//syam 28/11/2025
				    $ArrStockInsert2[$key]['total_harga'] 	= $value * $PRICENEW;//syam 28/11/2025


					$ArrHistInsert2[$key]['id_material'] 	= $key;
					$ArrHistInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHistInsert2[$key]['id_gudang_dari'] = $id_gudang_dari;
					$ArrHistInsert2[$key]['kd_gudang_dari'] = $kd_gudang_dari;
					$ArrHistInsert2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value;
					$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_booking_akhir']  = 0;
					$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
					$ArrHistInsert2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHistInsert2[$key]['jumlah_mat'] 		= $value;
					$ArrHistInsert2[$key]['ket'] 				= 'penambahan gudang (insert new)';
					$ArrHistInsert2[$key]['update_by'] 		    = $UserName;
					$ArrHistInsert2[$key]['update_date'] 		= $dateTime;
					
					$ArrHistInsert2[$key]['harga'] 			    = $PRICE2;//syam 28/11/2025
					$ArrHistInsert2[$key]['total_harga'] 	    = $PRICE*$value;
					$ArrHistInsert2[$key]['saldo_awal']		    = 0;
					$ArrHistInsert2[$key]['saldo_akhir']		= ($value)*$PRICENEW;
					$ArrHistInsert2[$key]['harga_baru'] 		= $PRICENEW;
				}
			}
		}

		// print_r($ArrStock);
		// print_r($ArrStockInsert);
		// print_r($ArrStock2);
		// print_r($ArrStockInsert2);
		// exit;

		if(!empty($ArrStock)){
			$CI->db->update_batch('warehouse_stock', $ArrStock, 'id');
		}
		if(!empty($ArrHist)){
			$CI->db->insert_batch('warehouse_history', $ArrHist);
		}

		if(!empty($ArrStockInsert)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert);
		}
		if(!empty($ArrHistInsert)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert);
		}

		if(!empty($ArrStock2)){
			$CI->db->update_batch('warehouse_stock', $ArrStock2, 'id');
		}
		if(!empty($ArrHist2)){
			$CI->db->insert_batch('warehouse_history', $ArrHist2);
		}

		if(!empty($ArrStockInsert2)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert2);
		}
		if(!empty($ArrHistInsert2)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert2);
		}
		
	}

	function move_warehouse2($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_delivery=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];
		$kode_trans 	= $kode_delivery;
		$kd_gudang_dari = get_name('warehouse', 'kode', 'id', $id_gudang_dari);
		$kd_gudang_ke	= NULL;
		if($id_gudang_ke != null){
			$kd_gudang_ke 	= get_name('warehouse', 'kode', 'id', $id_gudang_ke);
		}
		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$ArrStock = array();
		$ArrHist = array();
		$ArrStockInsert = array();
		$ArrHistInsert = array();

		$ArrStock2 = array();
		$ArrHist2 = array();
		$ArrStockInsert2 = array();
		$ArrHistInsert2 = array();

		foreach ($temp as $key => $value) {
			//PENGURANGAN GUDANG
			$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key))->result();

			if(!empty($rest_pusat)){
				$ArrStock[$key]['id'] 			= $rest_pusat[0]->id;
				$ArrStock[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrStock[$key]['update_by'] 	= $UserName;
				$ArrStock[$key]['update_date'] 	= $dateTime;

				$ArrHist[$key]['id_material'] 	= $key;
				$ArrHist[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
				$ArrHist[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
				$ArrHist[$key]['id_category'] 	= $rest_pusat[0]->id_category;
				$ArrHist[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
				$ArrHist[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHist[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHist[$key]['id_gudang_ke'] 		= $id_gudang_ke;
				$ArrHist[$key]['kd_gudang_ke'] 		= $kd_gudang_ke;
				$ArrHist[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
				$ArrHist[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock - $value;
				$ArrHist[$key]['qty_booking_awal'] 	= $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_booking_akhir'] = $rest_pusat[0]->qty_booking;
				$ArrHist[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
				$ArrHist[$key]['no_ipp'] 			= $kode_trans;
				$ArrHist[$key]['jumlah_mat'] 		= $value;
				$ArrHist[$key]['ket'] 				= 'pengurangan gudang';
				$ArrHist[$key]['update_by'] 		= $UserName;
				$ArrHist[$key]['update_date'] 		= $dateTime;
			}
			else{
				$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

				$ArrStockInsert[$key]['id_material'] 	= $restMat[0]->id_material;
				$ArrStockInsert[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
				$ArrStockInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrStockInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrStockInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrStockInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrStockInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrStockInsert[$key]['qty_stock'] 		= 0 - $value;
				$ArrStockInsert[$key]['update_by'] 		= $UserName;
				$ArrStockInsert[$key]['update_date'] 	= $dateTime;

				$ArrHistInsert[$key]['id_material'] 	= $key;
				$ArrHistInsert[$key]['idmaterial'] 		= $restMat[0]->idmaterial;
				$ArrHistInsert[$key]['nm_material'] 	= $restMat[0]->nm_material;
				$ArrHistInsert[$key]['id_category'] 	= $restMat[0]->id_category;
				$ArrHistInsert[$key]['nm_category'] 	= $restMat[0]->nm_category;
				$ArrHistInsert[$key]['id_gudang'] 		= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang'] 		= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_dari'] 	= $id_gudang_dari;
				$ArrHistInsert[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
				$ArrHistInsert[$key]['id_gudang_ke'] 	= $id_gudang_ke;
				$ArrHistInsert[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
				$ArrHistInsert[$key]['qty_stock_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_stock_akhir']     = 0 - $value;
				$ArrHistInsert[$key]['qty_booking_awal']    = 0;
				$ArrHistInsert[$key]['qty_booking_akhir']   = 0;
				$ArrHistInsert[$key]['qty_rusak_awal'] 	    = 0;
				$ArrHistInsert[$key]['qty_rusak_akhir'] 	= 0;
				$ArrHistInsert[$key]['no_ipp'] 			= $kode_trans;
				$ArrHistInsert[$key]['jumlah_mat'] 		= $value;
				$ArrHistInsert[$key]['ket'] 			= 'pengurangan gudang (insert new)';
				$ArrHistInsert[$key]['update_by'] 		= $UserName;
				$ArrHistInsert[$key]['update_date'] 	= $dateTime;
			}

			//PENAMBAHAN GUDANG
			if($id_gudang_ke != null){
				$rest_pusat = $CI->db->get_where('warehouse_stock',array('id_gudang'=>$id_gudang_ke, 'id_material'=>$key))->result();

				if(!empty($rest_pusat)){
					$ArrStock2[$key]['id'] 			= $rest_pusat[0]->id;
					$ArrStock2[$key]['qty_stock'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrStock2[$key]['update_by'] 	=  $UserName;
					$ArrStock2[$key]['update_date'] 	= $dateTime;

					$ArrHist2[$key]['id_material'] 	= $key;
					$ArrHist2[$key]['idmaterial'] 	= $rest_pusat[0]->idmaterial;
					$ArrHist2[$key]['nm_material'] 	= $rest_pusat[0]->nm_material;
					$ArrHist2[$key]['id_category'] 	= $rest_pusat[0]->id_category;
					$ArrHist2[$key]['nm_category'] 	= $rest_pusat[0]->nm_category;
					$ArrHist2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHist2[$key]['id_gudang_dari'] 	= $id_gudang_dari;
					$ArrHist2[$key]['kd_gudang_dari'] 	= $kd_gudang_dari;
					$ArrHist2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHist2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHist2[$key]['qty_stock_awal'] 	= $rest_pusat[0]->qty_stock;
					$ArrHist2[$key]['qty_stock_akhir'] 	= $rest_pusat[0]->qty_stock + $value;
					$ArrHist2[$key]['qty_booking_awal'] = $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_booking_akhir']= $rest_pusat[0]->qty_booking;
					$ArrHist2[$key]['qty_rusak_awal'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['qty_rusak_akhir'] 	= $rest_pusat[0]->qty_rusak;
					$ArrHist2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHist2[$key]['jumlah_mat'] 		= $value;
					$ArrHist2[$key]['ket'] 				= 'penambahan gudang';
					$ArrHist2[$key]['update_by'] 		= $UserName;
					$ArrHist2[$key]['update_date'] 		= $dateTime;
				}
				else{
					$restMat	= $CI->db->get_where('raw_materials',array('id_material'=>$key))->result();

					$ArrStockInsert2[$key]['id_material'] 	= $restMat[0]->id_material;
					$ArrStockInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrStockInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrStockInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrStockInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrStockInsert2[$key]['id_gudang'] 	= $id_gudang_ke;
					$ArrStockInsert2[$key]['kd_gudang'] 	= $kd_gudang_ke;
					$ArrStockInsert2[$key]['qty_stock'] 	= $value;
					$ArrStockInsert2[$key]['update_by'] 	= $UserName;
					$ArrStockInsert2[$key]['update_date'] 	= $dateTime;

					$ArrHistInsert2[$key]['id_material'] 	= $key;
					$ArrHistInsert2[$key]['idmaterial'] 	= $restMat[0]->idmaterial;
					$ArrHistInsert2[$key]['nm_material'] 	= $restMat[0]->nm_material;
					$ArrHistInsert2[$key]['id_category'] 	= $restMat[0]->id_category;
					$ArrHistInsert2[$key]['nm_category'] 	= $restMat[0]->nm_category;
					$ArrHistInsert2[$key]['id_gudang'] 		= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang'] 		= $kd_gudang_ke;
					$ArrHistInsert2[$key]['id_gudang_dari'] = $id_gudang_dari;
					$ArrHistInsert2[$key]['kd_gudang_dari'] = $kd_gudang_dari;
					$ArrHistInsert2[$key]['id_gudang_ke'] 	= $id_gudang_ke;
					$ArrHistInsert2[$key]['kd_gudang_ke'] 	= $kd_gudang_ke;
					$ArrHistInsert2[$key]['qty_stock_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_stock_akhir'] 	= $value;
					$ArrHistInsert2[$key]['qty_booking_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_booking_akhir']  = 0;
					$ArrHistInsert2[$key]['qty_rusak_awal'] 	= 0;
					$ArrHistInsert2[$key]['qty_rusak_akhir'] 	= 0;
					$ArrHistInsert2[$key]['no_ipp'] 			= $kode_trans;
					$ArrHistInsert2[$key]['jumlah_mat'] 		= $value;
					$ArrHistInsert2[$key]['ket'] 				= 'penambahan gudang (insert new)';
					$ArrHistInsert2[$key]['update_by'] 		    = $UserName;
					$ArrHistInsert2[$key]['update_date'] 		= $dateTime;
				}
			}
		}

		// print_r($ArrStock);
		// print_r($ArrStockInsert);
		// print_r($ArrStock2);
		// print_r($ArrStockInsert2);
		// exit;

		if(!empty($ArrStock)){
			$CI->db->update_batch('warehouse_stock', $ArrStock, 'id');
		}
		if(!empty($ArrHist)){
			$CI->db->insert_batch('warehouse_history', $ArrHist);
		}

		if(!empty($ArrStockInsert)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert);
		}
		if(!empty($ArrHistInsert)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert);
		}

		if(!empty($ArrStock2)){
			$CI->db->update_batch('warehouse_stock', $ArrStock2, 'id');
		}
		if(!empty($ArrHist2)){
			$CI->db->insert_batch('warehouse_history', $ArrHist2);
		}

		if(!empty($ArrStockInsert2)){
			$CI->db->insert_batch('warehouse_stock', $ArrStockInsert2);
		}
		if(!empty($ArrHistInsert2)){
			$CI->db->insert_batch('warehouse_history', $ArrHistInsert2);
		}
		
	}
	
	function get_filter_post($data){
		$filter = array("'", '"');
		$replace_all = str_replace($filter, "", $data);
		return strtolower(trim($replace_all));
	}

	function get_price_book($id_material=null){
		$CI 	=& get_instance();
		$get_price_book = $CI->db->order_by('id','desc')->get_where('price_book',array('id_material'=>$id_material))->result();
		$PRICE = (!empty($get_price_book[0]->price_book))?$get_price_book[0]->price_book:0;
		return $PRICE;
	}

	function get_price_aksesoris($id_material){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlPrice 	= "SELECT harga, exp_price_ref_est AS expired FROM accessories WHERE id='".$id_material."' AND '".$date."' <= exp_price_ref_est LIMIT 1";
		$restPrice 	= $CI->db->query($sqlPrice)->result();

		$sqlExp 	= "SELECT exp_price_ref_est AS expired FROM accessories WHERE id='".$id_material."' LIMIT 1";
		$restExp 	= $CI->db->query($sqlExp)->result();

		$price		= (!empty($restPrice[0]->harga))?$restPrice[0]->harga:0;
		$expired	= (!empty($restExp[0]->expired))?$restExp[0]->expired:NULL;

		$Array = [
			'price' => $price,
			'expired' => $expired
		];
		return $Array;
	}

	function get_delivery_date(){
		$CI 		=& get_instance();
		$sqlPrice 	= "	SELECT 
							a.no_ipp AS id_produksi,
							DATE(a.delivery_date) AS tanggal
						FROM 
							scheduling_master a
						WHERE a.delivery_date IS NOT NULL AND a.delivery_date NOT LIKE '%0000-00-00%' 
						ORDER BY a.no_ipp, a.delivery_date
						";
		$restPrice 	= $CI->db->query($sqlPrice)->result_array();

		$ArrDelivery = [];
		foreach($restPrice AS $val => $valx){
			$IPP = str_replace('PRO-','',$valx['id_produksi']);
			$IPP2 = str_replace('BQ-','',$IPP);
			$ArrDelivery[$IPP2][] = date('d-M-Y',strtotime($valx['tanggal']));
		}
		return $ArrDelivery;
	}

	function get_delivery_date_between($tgl_awal,$tgl_akhir){
		$CI 		=& get_instance();
		$sqlPrice 	= "	SELECT 
							a.no_ipp AS id_produksi,
							DATE(a.delivery_date) AS tanggal
						FROM 
							scheduling_master a
						WHERE 
							a.delivery_date IS NOT NULL 
							AND a.delivery_date NOT LIKE '%0000-00-00%' 
							AND DATE(a.delivery_date) BETWEEN '".date('Y-m-d',strtotime($tgl_awal))."' AND '".date('Y-m-d',strtotime($tgl_akhir))."'
						ORDER BY a.no_ipp, a.delivery_date
						";
		$restPrice 	= $CI->db->query($sqlPrice)->result_array();

		$ArrDelivery = [];
		foreach($restPrice AS $val => $valx){
			$UNIQ 	= $valx['tanggal'];
			$IPP 	= str_replace('PRO-','',$valx['id_produksi']);
			$IPP2 	= str_replace('BQ-','',$IPP);
			$ArrDelivery[] = $IPP2;
		}

		$ArrDeliveryUniq = array_unique($ArrDelivery);
		return $ArrDeliveryUniq;
	}

	function getAccessGroupMenu($id) {
		$CI =& get_instance();
		$listGetCategory    = $CI->db->get_where('group_menus',array('group_id'=>$id))->result_array();
		$ArrGetCategory     = [];
		foreach ($listGetCategory as $key => $value) {
			$KEY = $value['menu_id'];
			$ArrGetCategory[$KEY]['read']      = ($value['read'] == '1')?'Y':'';
			$ArrGetCategory[$KEY]['create']    = ($value['create'] == '1')?'Y':'';
			$ArrGetCategory[$KEY]['update']    = ($value['update'] == '1')?'Y':'';
			$ArrGetCategory[$KEY]['delete']    = ($value['delete'] == '1')?'Y':'';
			$ArrGetCategory[$KEY]['approve']   = ($value['approve'] == '1')?'Y':'';
			$ArrGetCategory[$KEY]['download']  = ($value['download'] == '1')?'Y':'';
		}
		return $ArrGetCategory;
	}

	function get_nomor_so_tanki($no_bq){
		$CI 	=& get_instance();
		$query	= $CI->db->query("SELECT no_so so_number, no_ipp FROM ".DBTANKI.".ipp_header WHERE no_ipp='".$no_bq."' LIMIT 1")->result();
		$data 	= (!empty($query[0]->so_number))?$query[0]->so_number:'';
		return $data;
	}

	function insertDataGroupReport($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_trans=null, $no_ipp=null, $no_spk=null, $product_name=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];
		$kd_gudang_dari = get_name('warehouse', 'kode', 'id', $id_gudang_dari);
		$kd_gudang_ke	= NULL;
		if($id_gudang_ke != null){
			$kd_gudang_ke 	= get_name('warehouse', 'kode', 'id', $id_gudang_ke);
		}
		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$GET_MAERIALS 			= get_detail_material();
		$GET_COSTBOOK_PUSAT 	= getPriceBookByDate(date('Y-m-d'));
		$GET_COSTBOOK_SUBGUDANG = getPriceBookByDatesubgudang(date('Y-m-d'));
		$GET_COSTBOOK_PRODUKSI 	= getPriceBookByDateproduksi(date('Y-m-d'));

		$tandaTanki = substr($no_spk,0,3);
		$getProduct = $CI->db->limit(1)->get_where('production_detail',array('no_spk'=>$no_spk))->result_array();
		$product 	= (!empty($getProduct[0]['id_category']))?$getProduct[0]['id_category']:null;
		if($tandaTanki == '90T'){
			$product 	= (!empty($getProduct[0]['id_product']))?$getProduct[0]['id_product']:null;
		}
		if($product_name == null){
			$product 	= null;
		}
		//DATA GROUP Gudang Out
		$tempMaterial = [];
		$ArrFinishGood = [];

		$GET_NO_SO = get_detail_ipp();
		$nomor_so = (!empty($GET_NO_SO[$no_ipp]['so_number']))?$GET_NO_SO[$no_ipp]['so_number']:$no_ipp;
		
		foreach ($temp as $key => $value) {
			if($value > 0 AND !empty($id_gudang_dari)){
				$nm_material = (!empty($GET_MAERIALS[$key]['nm_material']))?$GET_MAERIALS[$key]['nm_material']:'';
				
				$checkGudang = $CI->db->get_where('warehouse',array('id'=>$id_gudang_dari))->result_array();
				$categoryGudang = (!empty($checkGudang[0]['category']))?$checkGudang[0]['category']:0;

		

				if($categoryGudang == 'pusat' OR $categoryGudang == 'subgudang' OR $categoryGudang == 'produksi'){

				$pricebook = $CI->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				$costbook 	= $pricebook->harga;
					

					$tempMaterial[$key]['tanggal'] 		= $dateTime;
					$tempMaterial[$key]['keterangan'] 	= null;
					$tempMaterial[$key]['no_ipp'] 		= $nomor_so;
					$tempMaterial[$key]['no_spk'] 		= $no_spk;
					$tempMaterial[$key]['product'] 		= $product;
					$tempMaterial[$key]['kode_trans'] 		= $kode_trans;
					$tempMaterial[$key]['id_material'] 		= $key;
					$tempMaterial[$key]['nm_material'] 		= $nm_material;
					$tempMaterial[$key]['qty'] 				= $value;
					$tempMaterial[$key]['cost_book'] 		= $costbook;
					$tempMaterial[$key]['created_by'] 		= $UserName;
					$tempMaterial[$key]['created_date'] 	= $dateTime;
					$tempMaterial[$key]['tipe'] 			= 'out';
					$tempMaterial[$key]['gudang'] 			= $id_gudang_dari;
					$tempMaterial[$key]['gudang_dari'] 		= $id_gudang_dari;
					$tempMaterial[$key]['gudng_ke'] 		= $id_gudang_ke;

					if($id_gudang_ke == getGudangFG()){
						$ArrFinishGood[$key]['tanggal'] 		= $dateTime;
						$ArrFinishGood[$key]['keterangan'] 		= 'Subgudang to Finish Good';
						$ArrFinishGood[$key]['no_so'] 			= $nomor_so;
						$ArrFinishGood[$key]['product'] 		= $product;
						$ArrFinishGood[$key]['no_spk'] 			= $no_spk;
						$ArrFinishGood[$key]['kode_trans'] 		= $kode_trans;
						$ArrFinishGood[$key]['id_material'] 	= $key;
						$ArrFinishGood[$key]['nm_material'] 	= $nm_material;
						$ArrFinishGood[$key]['qty_mat'] 		= $value;
						$ArrFinishGood[$key]['cost_book'] 		= $costbook;
						$ArrFinishGood[$key]['nilai_unit'] 		= $costbook;
						$ArrFinishGood[$key]['created_by'] 		= $UserName;
						$ArrFinishGood[$key]['created_date'] 	= $dateTime;
						$ArrFinishGood[$key]['gudang'] 			= $id_gudang_ke;
						$ArrFinishGood[$key]['nilai_wip'] 		= $value * $costbook;
					}
				}
			}
		}

		$tempMaterialIn = [];
		foreach ($temp as $key => $value) {
			if($value > 0 AND !empty($id_gudang_ke)){
				$nm_material = (!empty($GET_MAERIALS[$key]['nm_material']))?$GET_MAERIALS[$key]['nm_material']:'';
				
				$gudangDariCostBook = (!empty($id_gudang_dari))?$id_gudang_dari:$id_gudang_ke;
				$checkGudang = $CI->db->get_where('warehouse',array('id'=>$gudangDariCostBook))->result_array();
				$categoryGudang = (!empty($checkGudang[0]['category']))?$checkGudang[0]['category']:0;

				if($categoryGudang == 'pusat' OR $categoryGudang == 'subgudang' OR $categoryGudang == 'produksi'){
					$pricebook = $CI->db->order_by('tgl_trans', 'desc')->get_where('tran_warehouse_jurnal_detail',array('id_gudang'=>$id_gudang_dari, 'id_material'=>$key),1)->row();
				    $costbook 	= $pricebook->harga;

					$tempMaterialIn[$key]['tanggal'] 		= $dateTime;
					$tempMaterialIn[$key]['keterangan'] 	= null;
					$tempMaterialIn[$key]['no_ipp'] 		= $nomor_so;
					$tempMaterialIn[$key]['no_spk'] 		= $no_spk;
					$tempMaterialIn[$key]['product'] 		= $product;
					$tempMaterialIn[$key]['kode_trans'] 		= $kode_trans;
					$tempMaterialIn[$key]['id_material'] 		= $key;
					$tempMaterialIn[$key]['nm_material'] 		= $nm_material;
					$tempMaterialIn[$key]['qty'] 				= $value;
					$tempMaterialIn[$key]['cost_book'] 		= $costbook;
					$tempMaterialIn[$key]['created_by'] 		= $UserName;
					$tempMaterialIn[$key]['created_date'] 	= $dateTime;
					$tempMaterialIn[$key]['tipe'] 			= 'in';
					$tempMaterialIn[$key]['gudang'] 			= $id_gudang_ke;
					$tempMaterialIn[$key]['gudang_dari'] 		= $id_gudang_dari;
					$tempMaterialIn[$key]['gudng_ke'] 		= $id_gudang_ke;
				}
			}
		}

		if(!empty($tempMaterial)){
			$CI->db->insert_batch('erp_data_subgudang', $tempMaterial);
		}
		if(!empty($tempMaterialIn)){
			$CI->db->insert_batch('erp_data_subgudang', $tempMaterialIn);
		}
		if(!empty($ArrFinishGood)){
			$CI->db->insert_batch('data_erp_fg', $ArrFinishGood);
		}
		
	}

	function insertDataGroupReport_Incoming($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_trans=null, $no_ipp=null, $no_spk=null, $product=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];

		//grouping sum
		$temp = [];
		$Harga = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
			$Harga[$value['id']] = $value['unit_price_idr'];
		}

		$GET_MAERIALS 			= get_detail_material();

		$tempMaterialIn = [];
		foreach ($temp as $key => $value) {
			if($value > 0 AND !empty($id_gudang_ke)){
				$nm_material = (!empty($GET_MAERIALS[$key]['nm_material']))?$GET_MAERIALS[$key]['nm_material']:'';
				
				$costbook 	= (!empty($Harga[$key]))?$Harga[$key]:0;

				$tempMaterialIn[$key]['tanggal'] 		= $dateTime;
				$tempMaterialIn[$key]['keterangan'] 	= 'incoming material';
				$tempMaterialIn[$key]['no_ipp'] 		= $no_ipp;
				$tempMaterialIn[$key]['no_spk'] 		= $no_spk;
				$tempMaterialIn[$key]['product'] 		= null;
				$tempMaterialIn[$key]['kode_trans'] 		= $kode_trans;
				$tempMaterialIn[$key]['id_material'] 		= $key;
				$tempMaterialIn[$key]['nm_material'] 		= $nm_material;
				$tempMaterialIn[$key]['qty'] 				= $value;
				$tempMaterialIn[$key]['cost_book'] 		= $costbook;
				$tempMaterialIn[$key]['created_by'] 		= $UserName;
				$tempMaterialIn[$key]['created_date'] 	= $dateTime;
				$tempMaterialIn[$key]['tipe'] 			= 'in';
				$tempMaterialIn[$key]['gudang'] 			= $id_gudang_ke;
				$tempMaterialIn[$key]['gudang_dari'] 		= $id_gudang_dari;
				$tempMaterialIn[$key]['gudng_ke'] 		= $id_gudang_ke;
				
			}
		}
		
		if(!empty($tempMaterialIn)){
			$CI->db->insert_batch('erp_data_subgudang', $tempMaterialIn);
		}
		
	}

	function insertDataGroupReport_GudangStok($ArrUpdateStock=null, $id_gudang_dari=null, $id_gudang_ke=null, $kode_trans=null, $no_ipp=null, $no_spk=null, $product_name=null){
		$CI 	=& get_instance();
		$dateTime		= date('Y-m-d H:i:s');
		$UserName 		= $CI->session->userdata['ORI_User']['username'];
		$kd_gudang_dari = get_name('warehouse', 'kode', 'id', $id_gudang_dari);
		$kd_gudang_ke	= NULL;
		if($id_gudang_ke != null){
			$kd_gudang_ke 	= get_name('warehouse', 'kode', 'id', $id_gudang_ke);
		}
		//grouping sum
		$temp = [];
		foreach($ArrUpdateStock as $value) {
			if(!array_key_exists($value['id'], $temp)) {
				$temp[$value['id']] = 0;
			}
			$temp[$value['id']] += $value['qty'];
		}

		$GET_MAERIALS 			= get_detail_consumable();
		$GET_COSTBOOK_PUSAT 	= getPriceBookByDate(date('Y-m-d'));

		$product 	= null;
		//DATA GROUP Gudang Out
		$tempMaterial = [];
		$ArrFinishGood = [];

		$GET_NO_SO = get_detail_ipp();
		$nomor_so = (!empty($GET_NO_SO[$no_ipp]['so_number']))?$GET_NO_SO[$no_ipp]['so_number']:$no_ipp;
		
		foreach ($temp as $key => $value) {
			if($value > 0 AND !empty($id_gudang_dari)){
				$nm_material = (!empty($GET_MAERIALS[$key]['nm_barang']))?$GET_MAERIALS[$key]['nm_barang']:'';
				
				$costbook 	= (!empty($GET_COSTBOOK_PUSAT[$key]))?$GET_COSTBOOK_PUSAT[$key]:0;
				
				$tempMaterial[$key]['tanggal'] 		= $dateTime;
				$tempMaterial[$key]['keterangan'] 	= null;
				$tempMaterial[$key]['no_ipp'] 		= $nomor_so;
				$tempMaterial[$key]['no_spk'] 		= $no_spk;
				$tempMaterial[$key]['product'] 		= $product;
				$tempMaterial[$key]['kode_trans'] 	= $kode_trans;
				$tempMaterial[$key]['id_material'] 	= $key;
				$tempMaterial[$key]['nm_material'] 	= $nm_material;
				$tempMaterial[$key]['qty'] 			= $value;
				$tempMaterial[$key]['cost_book'] 	= $costbook;
				$tempMaterial[$key]['created_by'] 	= $UserName;
				$tempMaterial[$key]['created_date'] = $dateTime;
				$tempMaterial[$key]['tipe'] 		= 'out';
				$tempMaterial[$key]['gudang'] 		= $id_gudang_dari;
				$tempMaterial[$key]['gudang_dari'] 	= $id_gudang_dari;
				$tempMaterial[$key]['gudng_ke'] 	= $id_gudang_ke;

				if($id_gudang_ke == getGudangFG()){
					$ArrFinishGood[$key]['tanggal'] 		= $dateTime;
					$ArrFinishGood[$key]['keterangan'] 		= 'Consumable to Finish Good';
					$ArrFinishGood[$key]['no_so'] 			= $nomor_so;
					$ArrFinishGood[$key]['product'] 		= $product;
					$ArrFinishGood[$key]['no_spk'] 			= $no_spk;
					$ArrFinishGood[$key]['kode_trans'] 		= $kode_trans;
					$ArrFinishGood[$key]['id_material'] 	= $key;
					$ArrFinishGood[$key]['nm_material'] 	= $nm_material;
					$ArrFinishGood[$key]['qty_mat'] 		= $value;
					$ArrFinishGood[$key]['cost_book'] 		= $costbook;
					$ArrFinishGood[$key]['nilai_unit'] 		= $costbook;
					$ArrFinishGood[$key]['created_by'] 		= $UserName;
					$ArrFinishGood[$key]['created_date'] 	= $dateTime;
					$ArrFinishGood[$key]['gudang'] 			= $id_gudang_ke;
					$ArrFinishGood[$key]['nilai_wip'] 		= $value * $costbook;
				}
			}
		}

		$tempMaterialIn = [];
		foreach ($temp as $key => $value) {
			if($value > 0 AND !empty($id_gudang_ke)){
				$nm_material = (!empty($GET_MAERIALS[$key]['nm_barang']))?$GET_MAERIALS[$key]['nm_barang']:'';

				$costbook 	= (!empty($GET_COSTBOOK_PUSAT[$key]))?$GET_COSTBOOK_PUSAT[$key]:0;

				$tempMaterialIn[$key]['tanggal'] 		= $dateTime;
				$tempMaterialIn[$key]['keterangan'] 	= null;
				$tempMaterialIn[$key]['no_ipp'] 		= $nomor_so;
				$tempMaterialIn[$key]['no_spk'] 		= $no_spk;
				$tempMaterialIn[$key]['product'] 		= $product;
				$tempMaterialIn[$key]['kode_trans'] 	= $kode_trans;
				$tempMaterialIn[$key]['id_material'] 	= $key;
				$tempMaterialIn[$key]['nm_material'] 	= $nm_material;
				$tempMaterialIn[$key]['qty'] 			= $value;
				$tempMaterialIn[$key]['cost_book'] 		= $costbook;
				$tempMaterialIn[$key]['created_by'] 	= $UserName;
				$tempMaterialIn[$key]['created_date'] 	= $dateTime;
				$tempMaterialIn[$key]['tipe'] 			= 'in';
				$tempMaterialIn[$key]['gudang'] 		= $id_gudang_ke;
				$tempMaterialIn[$key]['gudang_dari'] 	= $id_gudang_dari;
				$tempMaterialIn[$key]['gudng_ke'] 		= $id_gudang_ke;
				
			}
		}

		if(!empty($tempMaterial)){
			$CI->db->insert_batch('erp_data_subgudang', $tempMaterial);
		}
		if(!empty($tempMaterialIn)){
			$CI->db->insert_batch('erp_data_subgudang', $tempMaterialIn);
		}
		if(!empty($ArrFinishGood)){
			$CI->db->insert_batch('data_erp_fg', $ArrFinishGood);
		}
		
	}

	function persen_progress_produksi_tanki($id_produksi){
		$CI 		=& get_instance();
		
		$rowD		= $CI->db->select('id_milik,id_produksi,qty')->group_by('id_milik')->get_where('production_detail',['id_produksi'=>$id_produksi])->result_array();
		
		$SUM_PROGRESS = 0;
		foreach($rowD AS $val => $valx){
			$sqlCheck2 	= $CI->db
								->select('COUNT(*) as Numc')
								->group_start()
								->group_start()
								->where('daycode !=', NULL)
								->where('daycode !=', '')
								->group_end()
								->or_where('id_deadstok_dipakai !=', NULL)
								->group_end()
								->get_where('production_detail', 
									array(
										'id_milik'=>$valx['id_milik'],
										'id_produksi'=>$valx['id_produksi']
										)
									)
								->result();
			$QTY 		= $valx['qty'];
			$ACT 		= $sqlCheck2[0]->Numc;
			
			$progress = 0;
			if($ACT != 0 AND $QTY != 0){
				$progress 	= ($ACT/$QTY) *(100);
			}

			$SUM_PROGRESS += $progress;
		}

		$Progresss = 0;
		if($SUM_PROGRESS > 0 AND COUNT($rowD) > 0 ){
			$Progresss = $SUM_PROGRESS / COUNT($rowD);
		}
		
		return $Progresss;
	}

	function spec_deadstok_tanki($id_milik){
		$CI 			=& get_instance();
		$restHeader		= $CI->db->limit(1)->get_where('production_detail',array('id_milik'=>$id_milik))->result_array();
		$nm_product		= (!empty($restHeader[0]['id_product']))?$restHeader[0]['id_product']:'';
		$no_spk			= (!empty($restHeader[0]['no_spk']))?$restHeader[0]['no_spk']:'';
		$no_so			= (!empty($restHeader[0]['product_code']))?substr($restHeader[0]['product_code'],0,9):'';

		$Array = [
			'nm_product' => $nm_product,
			'no_spk' => $no_spk,
			'no_so' => $no_so
		];
		return $Array;
	}

	function getPriceAccessoriesMaster($id_material){
		$CI 		=& get_instance();
		$date		= date('Y-m-d');
		$sqlExp 	= "SELECT price_supplier AS price_from_supplier FROM price_ref WHERE code_group='".$id_material."' AND deleted_date is null LIMIT 1";
		$restExp 	= $CI->db->query($sqlExp)->result();
		$price_from_supplier	= (!empty($restExp[0]->price_from_supplier))?$restExp[0]->price_from_supplier:0;

		$Array = [
			'price_from_supplier' => $price_from_supplier
		];
		return $Array;
	}

?>
