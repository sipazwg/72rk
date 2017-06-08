<?php
//iptc类
include_once("/data/www/newsave/include/IPTC.php");
//连数据库
$link = mysql_connect("125.208.9.222","sipaphoto","123456");
mysql_select_db("sipaphoto",$link);
mysql_query("set names utf8");


//图片存放位置
$base_path = "/data/www/img1.sipaphoto.com/upload/";
$max_photo_id=1749548;


function getUid($byline,$link)
{
	$user_real_name  = str_replace('/sipa', '', strtolower($byline));
	$user_login_name = preg_replace('/\W/', '_', $byline);
	if (trim($user_login_name) == '') {
		$user_login_name = 'isopix';
	}
	$email    = $user_login_name . '@sipaphoto.com';
	$strQuery = "SELECT `user_id` FROM sipa_users WHERE `user_email`='".$email."'";
	echo "getuid:".$strQuery."\n";

	$rs       = mysql_query($strQuery,$link);

	$uid      = '';
	if ($rs)
		$row = mysql_fetch_assoc($rs);
	if (!empty($row['user_id'])) {
		$uid = $row['user_id'];
	} else {
		$email           = $user_login_name . '@sipaphoto.com';
		$user_login_code = random_string();
		$password        = md5('com.sipaphoto' . $user_login_code);
		$insert          = "INSERT INTO sipa_users (`user_login_name`,`user_login_passwd`,`user_login_code`,`user_real_name`,`user_identity_card`," . "`user_email`,`user_type`) VALUES(" . "'{$user_login_name}', '{$password}','{$user_login_code}','{$user_real_name}','1000000000','{$email}',2);";
		$irs             = mysql_query($insert,$link);
		if (!$irs) {
			$strQuery = "SELECT `user_id` FROM sipa_users WHERE `user_email`='{$email}'";
			$rs       = mysql_query($strQuery,$link);
			if ($rs) {
				$row = mysql_fetch_assoc($rs);
				$uid = $row['user_id'];
			}
		} else {
			$uid = mysql_insert_id($link);
		}
	}
	return $uid;
}

function random_string($type = 'alnum', $len = 8)
	{
		switch ($type) {
			case 'basic':
				return mt_rand();
				break;
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'alpha':
				
				switch ($type) {
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				
				$str = '';
				for ($i = 0; $i < $len; $i++) {
					$str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
				}
				return $str;
				break;
			case 'unique':
			case 'md5':
				
				return md5(uniqid(mt_rand()));
				break;
		}
	}

//建立循环
for($i = 1 ;$i<=$max_photo_id;$i++)
{

	$sql_sl = "select photo_image,photo_user_id,photo_from from sipa_photos where photo_id=".$i."";
	$res_sl = mysql_query($sql_sl,$link);
	echo $i."\n";
	//图片ID不存在或来源非指定来源，直接跳过
	if(mysql_num_rows($res_sl)<=0)
	{
		continue;
	}
	$row_sl = mysql_fetch_assoc($res_sl);
	//非admin图片
	if($row_sl['photo_user_id'] != 7)
	{
		continue;
	}
	//非sipa图片
	if($row_sl['photo_from'] != 0)
	{
		continue;
	}

	$photo_image = $row_sl['photo_image'];
	echo "photo_image:".$photo_image."\n";
	
	//取得图片大图路径
	$imagepre = str_replace(".jpg","",$photo_image);
	$arr_p = explode("_",$imagepre);

	$s_fullpath = $base_path.date("Y-m-d",$arr_p[1])."/".md5($imagepre."_s").".jpg";

	echo "s_fullpath:".$s_fullpath."\n";
	//取得图片分类字串
	$iptc	= new Image_IPTC($s_fullpath);
	$byline	= trim($iptc->getTag('byline'));
	if($byline == '')
	{
		continue;
	}
	$newuid		= getUid($byline,$link);

	//保存结果文件
	$fp = fopen("/data/www/newsave/pic_admin.txt","a+");
	fwrite($fp,$i."_".$newuid."\n");
	//关闭文本文档
	fclose($fp);

}


?>