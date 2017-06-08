<?php
//连数据库
$link = mysql_connect("125.208.9.222","sipaphoto","123456");
mysql_select_db("sipaphoto",$link);
mysql_query("set names utf8");


$max_photo_id=1720100;

//$logfile = "/data/www/newsave/photofrom.txt";
//保存结果文件
//$fp = fopen($logfile,"a+");


//建立循环
for($i = $max_photo_id ;$i>=1;$i--)
{
	
	$sql_sl = "select photo_from from sipa_photos where photo_id=".$i."";
	$res_sl = mysql_query($sql_sl,$link);
	echo $i."\n";
	//图片ID不存在或来源非指定来源，直接跳过
	if(mysql_num_rows($res_sl)<=0)
	{
		continue;
	}
	$row_sl = mysql_fetch_assoc($res_sl);
	$photo_from = $row_sl['photo_from'];
	if($photo_from == "69")
	{
		echo "res:".$i.":".$photo_from."\n";
		break;
	}
	/*
	$file_cont = file_get_contents($logfile);
	if(strstr($file_cont,$photo_from) != false)
	{
		continue;
	}
	fwrite($fp,$photo_from."\n");
	*/

}
//关闭文本文档
//fclose($fp);

?>