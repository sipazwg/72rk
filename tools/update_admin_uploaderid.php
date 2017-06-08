<?php
//iptc类
include_once("/data/www/newsave/include/IPTC.php");
//连数据库
$link = mysql_connect("125.208.9.222","sipaphoto","123456");
mysql_select_db("sipaphoto",$link);
mysql_query("set names utf8");


$txtfile = "/data/www/newsave/pic_admin.txt";
$arr_row = file($txtfile);

foreach($arr_row as $k=>$v)
{
	echo $k.":".$v."\n";
	$v = trim($v);
	if($v == '')
	{
		continue;
	}
	$arr_h = explode("_",$v);
	$photoid = $arr_h[0];
	$newuserid = $arr_h[1];
	$sql_se = "select * from sipa_user_downloads where userdownload_photo_id=".$photoid."";
	$res_se = mysql_query($sql_se,$link);

	if(mysql_num_rows($res_se)<=0)
	{
		continue;
	}
	$sql_up = "update sipa_user_downloads set userdownload_uploaderid=".$newuserid." where userdownload_photo_id=".$photoid."";
	echo $sql_up."\n";
	mysql_query($sql_up,$link);
	//exit;
	/*
	$sql_up = "update sipa_photos set photo_user_id=".$newuserid." where photo_id=".$photoid."";
	echo $sql_up."\n";
	mysql_query($sql_up,$link);

	$page_url = "http://125.208.9.222/import_index.php?photo_id=".$photoid."";
	$contents = file_get_contents($page_url);



	$sql_se = "select group_id from sipa_group_photos where photo_id=1167481";
	$res_se = mysql_query($sql_se,$link);
	$num = mysql_num_rows($res_se);
	if($num == 0  || $num >1 )
	{
		continue;
	}
	else
	{
		$row = mysql_fetch_assoc($res_se);
		$sql_up = "update sipa_groups set group_user_id=".$newuserid." where group_id=".$row['group_id']."";
		mysql_query($sql_up,$link);
		echo $sql_up."\n";
		$page_url = "http://125.208.9.222/import_index.php?group_id=".$row['group_id']."";
		$contents = file_get_contents($page_url);
	}
	*/
	
	
}
?>