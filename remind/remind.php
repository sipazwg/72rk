<?php 
//����
error_reporting(E_ALL ^ E_NOTICE);
require_once("/data/www/newsave/remind/coon.php");
//���ȡ���ļ���
$filepath	= "/data/www/ftpdir";


$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 1)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}

//���ܺ������õ�һ��Ŀ¼�µ������ļ���������php������
function get_papa_ls($dir) 
{
	$cmdls = "/bin/ls '" . $dir . "'";
	echo $cmdls;
	$retls = shell_exec($cmdls . " 2>&1");
	$arrls = explode("\n", $retls);

	$arrlsf = array();
	foreach ($arrls as $kl => $vl) 
	{
		//Ϊ�գ���. .. ����
		if (($vl == '') || ($vl == '.') || ($vl == '..')) {
			continue;
		}

		$vl = trim($vl);
		if (strpos($vl, "'") > 0) 
		{
			$newfindfile = str_replace("'", "", $vl);
			$newpathfile = $dir . "/" . $newfindfile;
			rename($dir . "/" . $vl, $newpathfile);
			$arrlsf[] = $newfindfile;
		}
		else 
		{
			$arrlsf[] = $vl;
		}
	}
	//print_r($arrlsf);
	return $arrlsf;
}



//ͨ��alldir���õ��ļ���������Ŀ¼����
$obj_dir	= new alldir($filepath);
//var_dump($obj_dir);die;

foreach($obj_dir->arrdir as $k=>$v){
	$i=0;
	foreach(get_papa_ls($v) as $val){
		if(strstr(strtolower($val), '.jpg')){
			$i++;
		}
	}
	echo $i.'---';
	//echo $v.'<br/>';
	if($i >= '50' ){
		$insert          = "INSERT INTO sipa_remind (`path`,`status`,`type`) VALUES('$v','0','73');";
		$irs             = mysql_query($insert,$con);
	}
}
