<?php 
//测试
error_reporting(E_ALL ^ E_NOTICE);
require_once("/data/www/newsave/remind/coon.php");
require_once("/data/www/newsave/remind/smtp.php");


//获取表中数据
$sql = "SELECT * FROM sipa_remind_file WHERE type = '73'";
$res = mysql_query($sql,$con);
$paths = '';
while($row=mysql_fetch_array($res)){
			//获得文件最新修改时间
			$newtime = 	filemtime ($row['path']);
			$time = time();
			//获取时间差
			$margin = $time - $newtime;
			
			//判断时间差是否大于两天
			
			if($margin >= 60*60*24*2){
				if($row['path'] != '/data/www/ftpdir/isopix' && $row['path'] != '/data/www/ftpdir/SIPA-DHK' ){
					$paths .= $row['path']."\r\n";
				}
			}
			
			//判断时间差是否大于五天
			
			if($margin >= 60*60*24*5){
				if($row['path'] == '/data/www/ftpdir/isopix' || $row['path'] == '/data/www/ftpdir/SIPA-DHK' ){
					$paths .= $row['path']."\r\n";
				}
			}
			
			//更新时间
			$up = "UPDATE sipa_remind_file SET lastime = $time WHERE path = '$row[path]' AND type = '73'";
			mysql_query($up,$con);
			
	}
	if(!empty($paths)){
		$smtpserver = "smtp.exmail.qq.com";
		$smtpserverport = 25;
		$smtpusermail = "system@sipaphoto.com";
		$smtpemailto = "gaojianfeng@sipaphoto.com,172983386@qq.com";
		$smtpuser = "system@sipaphoto.com";
		$smtppass = "sipa2016";
		$mailsubject = "73服务器超过两天未进图";
		$mailbody = "PATH : ".$paths;
		$mailtype = "TXT";
		$smtp = new \smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
		$smtp->debug = false;
		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);	
	
	
	}
	
	