<?php
/*
 * 下载sipausa服务器上的图片到本地  
 *	使用axel多线程下载，以查看速度
 *
 *	下载文件夹内图片
 *	
 */

$starttime = time();
$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");

if($n >1)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}

//定义连接元素
$hostip			= "sipapull.mainstreamdata.com";
$hostuser		= "sipausanew";
$hostpass		= "JHUYI986HU";
$hostpath		= "/";

//保存路径
$localpath		= "/data/www/ftpdir/sipausa/";
//日志路径 
$logpath		= "/data/www/newsave/log/axelpic/";
$name		= "sipausa";
//下载图片日志
$curdate = date("ymd");
$dowlogfile=$logpath.$name."_".$curdate."_downlist.txt";
//重复图片日志
$uniquepath = $logpath.$name."_unique_".$curdate.".txt";

//连接服务器
$conn_id = ftp_connect($hostip);
$login_result = ftp_login($conn_id, $hostuser, $hostpass);
ftp_pasv($conn_id,TRUE);

if ((!$conn_id) || (!$login_result)) {
	echo "FTP connection has failed!";
	exit;
}
else{
	echo "Connected to $hostip, for user $hostuser";
}

$res = ftp_nlist($conn_id,".");
$res = array_reverse($res);
//arsort($res);

//需要替换的字符串
$replace_arr = array(" ","'","\"","(",")","&",":","#","<",">","?");

echo "\n";
print_r($res);
echo count($res);
echo "\n";

//$res = array("Auto_Distribution_-_Admedia__Entertainment_");

foreach ($res as $fdir)
{
 		echo $fdir.":start====>\n";
 		
		if($fdir == "." || $fdir == ".."){continue;}
		if(substr($fdir,-4) == '.jpg'){echo $fdir."---->is jpg \n";}
		
		//替换特殊字符
		$fdir_tem = $fdir;
		$fdir = @str_replace($replace_arr,"_",$fdir);
		@ftp_rename($conn_id,$fdir_tem,$fdir);
		
		//图片下载后需删除
		if(@ftp_chdir($conn_id,$fdir))
		{
			$pic_arr = ftp_nlist($conn_id,".");
			print_R($pic_arr);
			foreach ($pic_arr as $pic)
			{
				if(substr($pic,-4) == '.jpg' || substr($pic,-4) == '.JPG')
				{
					//得到ftp图片路径
					$ftp_pic_path = $fdir."/".$pic;
					//判断是否下载过
					if(strstr(file_get_contents($dowlogfile),$ftp_pic_path) !== false)
					{
						//删除图片
						ftp_delete($conn_id,$pic);
						continue;
					}
					//开始时间
					$stime = time();
					//执行下载
					$cmd = "wget -r -nH -m -P".$localpath." ftp://".$hostuser.":".$hostpass."@".$hostip."/".$ftp_pic_path;
					$cmd_info = shell_exec($cmd);
					//结束时间
					$etime = time();
					//下载所用时间
					$downtime = $etime - $stime;
					//做下载纪录
					file_put_contents($dowlogfile,"downFile-->downtime:".$downtime."|".$ftp_pic_path."|date:".date("Y-m-d H:i:s")."\n",FILE_APPEND);
					//删除图片
					ftp_delete($conn_id,$pic);
				}
			}
			ftp_chdir($conn_id,"..");
		}
}
ftp_close($conn_id);

$endtime = time();
echo "\r\n"."nowtime:"."\r\n".date("Y-m-d")."runsec:".($endtime-$starttime)."\r\n";

?>
