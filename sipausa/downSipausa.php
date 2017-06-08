<?php 
/*
 * 批量下载sipausa服务器上的图片
 * 
 * 
 */

$starttime = time();
$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 1)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}

//定义连接元素
$hostip			= "sipapull.mainstreamdata.com";
$hostuser		= "sipausanew";
$hostpass		= "JHUYI986HU";
$hostpath		= "/";
$name		= "sipausa";


// $hostip			= "106.3.36.73";
// $hostuser		= "gjf";
// $hostpass		= "123123";
//连接服务器
$conn_id = ftp_connect($hostip);
$login_result = ftp_login($conn_id, $hostuser, $hostpass);
ftp_pasv($conn_id,TRUE);


if ((!$conn_id) || (!$login_result)) {
	echo "FTP connection has failed! \n";
	exit;
}
else{
	echo "Connected to $hostip, for user $hostuser \n";
}


//得到数据
$res = ftp_nlist($conn_id,".");
$num = count($res);
ftp_close($conn_id);

$n = ceil($num/5);

if($n > 10)
{
	$n = 10;
}
 $n = 3;
for ($i = 1;$i <= $n;$i++)
{
	//ip-user-pwd-资源名字-分几组-第几组
	$info = $hostip." ".$hostuser." ".$hostpass." ".$name." ".$n." ".$i;
	$cmd = "/usr/bin/php /data/www/newsave/sipausa/doAxel.php ".$info."  >>/dev/null 2>&1 &";
	//$cmd = "/usr/bin/php /home/data/www/sipaphoto_cmd/doAxel2.php ".$info." >>/dev/null 2>&1 &";
	echo $cmd."\n";
	system($cmd);
// 	system($cmd1);
}


?>
