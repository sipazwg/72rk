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
$filepath=$logpath.$name."_".$curdate.".txt";
//重复图片日志
$uniquepath = $logpath.$name."_unique_".$curdate.".txt";

//连接服务器
$conn_id = ftp_connect($hostip);
$login_result = ftp_login($conn_id, $hostuser, $hostpass);
ftp_pasv($conn_id,TRUE);
ftp_set_option($conn_id,FTP_TIMEOUT_SEC,1200000);

if ((!$conn_id) || (!$login_result)) {
	echo "FTP connection has failed!";
	exit;
}
else{
	echo "Connected to $hostip, for user $hostuser";
}

//当天的日期
$curdate = date("ymd");

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
 	echo $fdir."\n";
	if(in_array($fdir,$res))
	{
		
		if($fdir == "." || $fdir == ".."){continue;}
		
		$fdir_tem = $fdir;
		$fdir = @str_replace($replace_arr,"_",$fdir);
		ftp_rename($conn_id,$fdir_tem,$fdir);
		
		//图片下载后需删除
		if(@ftp_chdir($conn_id,$fdir))
		{
			echo $axelcmd="wget -r -nH -m -P".$localpath." ftp://".$hostuser.":".$hostpass."@".$hostip."/".$fdir."\n";
			die;
			if(strstr(file_get_contents($filepath),$fdir) !== false)
			{
				echo "\n------>\n"; 
				$pic = ftp_nlist($conn_id,".");
				foreach ($pic as $k)
				{
					if($k == "." || $k == ".."){continue;}
					
					if(strstr(file_get_contents($filepath),$k) !== false)
					{
						file_put_contents($filepath,"downFile ---timeoutDel---".$fdir."/".$k."---".date("Y-m-d H:i:s")."\n",FILE_APPEND);
						ftp_delete($conn_id,$k);
						echo "del succ  timeoutDel------".$fdir."/".$k."--**************************************\n\n";
					}
				}
				ftp_chdir($conn_id,"..");
				//continue;
			}
			
			
			$stime = time();
			echo $fdir."--is file \n";
			$fdir = addslashes($fdir);
			$axelcmd="wget -r -nH -m -P".$localpath." ftp://".$hostuser.":".$hostpass."@".$hostip."/".$fdir."\n";
			echo $axelcmd."----> \n\n";
			
			$cmd_info = shell_exec($axelcmd);
			//ftp_set_option($conn_id,FTP_TIMEOUT_SEC,500);
			$etime= time();
			$usetime = $etime - $stime;
			$pic = ftp_nlist($conn_id,".");
			
			print_R($pic);
			echo "\n";
			echo count($pic) ;
			echo "\n";
			if(count($pic)<3)
			{
				ftp_chdir($conn_id,"..");
				ftp_rmdir($conn_id,$fdir);
				file_put_contents($filepath,"Del  File ---".$usetime."---".$fdir."---".date("Y-m-d H:i:s")."\n",FILE_APPEND);
				continue;
			}
			foreach ($pic as $k)
			{
				if($k == "." || $k == ".."){continue;}
// 				echo $fdir."/".$k."\n";die;
				file_put_contents($filepath,"downFile ---".$usetime."---".$fdir."/".$k."---".date("Y-m-d H:i:s")."\n",FILE_APPEND);
				ftp_delete($conn_id,$k);
				echo "del succ ------".$fdir."/".$k."--**************************************\n\n";
			}
			break;
		}
	}else{
		echo " no fdir ! \n";
	}
}
ftp_close($conn_id);

$endtime = time();
echo "\r\n"."nowtime:"."\r\n".date("Y-m-d")."runsec:".($endtime-$starttime)."\r\n";

?>
