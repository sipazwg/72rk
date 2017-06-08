<?php 
//邮件发送
error_reporting(E_ALL ^ E_NOTICE);
require_once("/data/www/newsave/remind/smtp.php");
require_once("/data/www/newsave/remind/coon.php");

//获取表信息
$sql = "SELECT path,id,type FROM sipa_remind WHERE status = 0 ";

$rs       = mysql_query($sql,$con);

$str      = '';
$ids      = '';
if ($rs){
	while($row=mysql_fetch_array($rs)){
			$str .= '服务器-'.$row['type'].'路径  '.$row['path']."\r\n";
			$ids .= $row['id'].",";
	}

}
if(!empty($str)){
	$smtpserver = "smtp.exmail.qq.com";
	$smtpserverport = 25;
	$smtpusermail = "system@sipaphoto.com";
	$smtpemailto = "gaojianfeng@sipaphoto.com,172983386@qq.com";
	$smtpuser = "system@sipaphoto.com";
	$smtppass = "sipa2016";
	$mailsubject = "图片积压";
	$mailbody = $str;
	$mailtype = "TXT";
	$smtp = new \smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
	$smtp->debug = false;
	$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);

	//更新表数据

	$ids = rtrim($ids,',');
	$up = "UPDATE sipa_remind SET status = 1 WHERE id in ($ids)";
	 mysql_query($up,$con);
}

//删除已发送
$del = "DELETE FROM sipa_remind WHERE status = 1  ";
 mysql_query($del,$con);





