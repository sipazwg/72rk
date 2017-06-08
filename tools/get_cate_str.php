<?php
//iptc类
include_once("/data/www/newsave/include/IPTC.php");
//连数据库
$link = mysql_connect("125.208.9.222","sipaphoto","123456");
mysql_select_db("sipaphoto",$link);
mysql_query("set names utf8");


//图片存放位置
$base_path = "/data/www/img1.sipaphoto.com/upload/";
$max_photo_id=1720100;
//$max_photo_id=2000;

//结果数组
//$arr_res = array("newscom"=>array(),"tass"=>array(),"sipa"=>array());

//保存结果文件
$fp_news = fopen("/data/www/newsave/newscom.txt","a+");
$fp_tass = fopen("/data/www/newsave/tass.txt","a+");
$fp_sp   = fopen("/data/www/newsave/sipa.txt","a+");


//建立循环
for($i = $max_photo_id ;$i>=381957;$i--)
{
	$sql_sl = "select photo_image,photo_from from sipa_photos where photo_id=".$i." and photo_image!= '' and photo_from in (0,8,12)";
	$res_sl = mysql_query($sql_sl,$link);
	echo $i."\n";
	//图片ID不存在或来源非指定来源，直接跳过
	if(mysql_num_rows($res_sl)<=0)
	{
		continue;
	}
	$row_sl = mysql_fetch_assoc($res_sl);
	$photo_image = $row_sl['photo_image'];
	$photo_from = $row_sl['photo_from'];
	echo "photo_image:".$photo_image."\n";
	
	//取得图片大图路径
	$imagepre = str_replace(".jpg","",$photo_image);
	$arr_p = explode("_",$imagepre);

	$s_fullpath = $base_path.date("Y-m-d",$arr_p[1])."/".md5($imagepre."_s").".jpg";

	echo "s_fullpath:".$s_fullpath."\n";
	//取得图片分类字串
	$iptc	= new Image_IPTC($s_fullpath);
	$cate	= trim($iptc->getTag('category'));
	if($cate == '')
	{
		continue;
	}
	echo "cate:".$cate."\n";
	//根据不同来源
	if($photo_from == 8)
	{
		$file_cont = file_get_contents("/data/www/newsave/newscom.txt");
		if(strstr($file_cont,$cate) != false)
		{
			continue;
		}
		fwrite($fp_news,$cate."\n");
	}
	elseif($photo_from == 12)
	{
		$file_cont = file_get_contents("/data/www/newsave/tass.txt");
		if(strstr($file_cont,$cate) != false)
		{
			continue;
		}
		fwrite($fp_tass,$cate."\n");
	}
	else
	{
		$file_cont = file_get_contents("/data/www/newsave/sipa.txt");
		if(strstr($file_cont,$cate) != false)
		{
			continue;
		}
		fwrite($fp_sp,$cate."\n");
	}
}
//关闭文本文档
fclose($fp_news);
fclose($fp_tass);
fclose($fp_sp);
?>