<?php

//连数据库
$link = mysql_connect("125.208.9.222","sipaphoto","123456");
mysql_select_db("test",$link);
mysql_query("set names utf8");

$fp = fopen("/data/www/newsave/sys_info.txt","a+");

$sql_a = "SELECT `SYSUSERID`,`NAME`,`ADDRESS`,`TEL`,`EMAIL`,`COMPANY`,`COMMENT`,`ZIWU`,`MOBILE`,`MSN`,`OICQ`,`PENNAME`,`COUNTRY`,`PROVINCE`,`CITY`,`PHOTOTYPE`,`PHOTOINFO`,`AWARDS` FROM `usersystem` WHERE `SYSUSERID` in(select sysuserid from crm_provider)";
$res_a = mysql_query($sql_a,$link);

while($row = mysql_fetch_assoc($res_a))
{
	echo $row['SYSUSERID']."\n";
	$sql_c = "select upinfo from usersystem_supp where sysuserid=".$row['SYSUSERID']."";
	$res_c = mysql_query($sql_c,$link);
	$row_c = mysql_fetch_assoc($res_c);

	//保存结果文件
	$str_ins = '';
	$str_ins = "姓名：".$row['NAME']."\n地址：".$row['ADDRESS']."\n电话：".$row['TEL']."\nEMAIL:".$row['EMAIL']."\n备注：".$row['COMMENT']."\n职务：".$row['ZIWU']."\n手机：".$row['MOBILE']."\nMSN:".$row['MSN']."\nQQ:".$row['OICQ']."\n笔名：".$row['PENNAME']."\n国家：".$row['COUNTRY']."\n省：".$row['PROVINCE']."\n城市：".$row['CITY']."\n图片类型：".$row['PHOTOTYPE']."\n图片信息".$row['PHOTOINFO']."\n拍摄方向：".$row['AWARDS']."\n最后一次更新：".$row_c['upinfo']."\n";
	
	fwrite($fp,$str_ins."\n\n\n");
}

//关闭文本文档
fclose($fp);



?>