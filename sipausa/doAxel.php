<?php 

/*
 * 批量下载sipausa服务器上的图片
 * $argv可以得到所有参数
 * $argc 可以得到所有参数的个数
 */

if($argc < 6)
{
	die("参数错误！\n");
}
$starttime = time();
//定义连接元素
$hostip			= $argv[1];
$hostuser		= $argv[2];
$hostpass		= $argv[3];
//资源名称
$name = $argv[4];
//将数组分成组数
$n = $argv[5];
$n = 20;
//数组的第几部分
$i = $argv[6];
//图片保存路径
$localpath		= "/data/www/ftpdir/".$argv[4]."/";
//日志路径
$logpath		= "/data/www/newsave/log/axelpic/";

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
$res_tem = ftp_nlist($conn_id,".");
$res_tem = array_reverse($res_tem);
$num = count($res_tem);
$tem = array_chunk($res_tem,$n);
$res = $tem[$i-1];


// echo "\n";
// print_r($res_tem);
// echo "\n";
// echo $num;
// echo "\n";
// print_r($res);
// echo "\n";
// die;


//下载图片日志
$curdate = date("ymd");
$filepath=$logpath.$name."_".$curdate.".txt";

//重复图片日志
$uniquepath = $logpath.$name."_unique_".$curdate.".txt";

//所有图片
$allpic = $logpath.$name."_all_".$curdate.".txt";

//遍历目录
foreach($res as $k=>$p)
{
	if($p == "." || $p == ".." || !ftp_size($conn_id,$p))
	{
		continue;
	}
	
	//取得图片名称
	$picname = substr($p,strrpos($p,"/")+1);

	//如果图片被下载过，则记录到文档中
	if(strstr(file_get_contents($filepath),$p) !== false)
	{
		//取得图片的分类ID
		$cateid = getcate($p);
		file_put_contents($uniquepath,$cateid."---".$p."---".date("Y-m-d H:i:s")."\n",FILE_APPEND);
		ftp_delete($conn_id,$p);
		continue;
	}
	
	
	$destpath = $localpath.$p;
	
	//记录所有图片
	file_put_contents($allpic,$destpath."---".date("Y-m-d H:i:s")."-->>",FILE_APPEND);
	
	if(is_dir($destpath))
	{
		echo "$destpath is dir \n ";
		continue;
	}
	
	$picurl = "";
	$stime = time();
	//$axelcmd = "/usr/bin/axel -a  -n 5 -o " .$destpath. " ftp://".$hostuser.":".$hostpass."@".$hostip."/".$p;
	$axelcmd="wget -P ".$localpath." ftp://".$hostuser.":".$hostpass."@".$hostip."/".$p."\n";
	echo $k."--".$num."--".$axelcmd."---->";
	$cmd_info = shell_exec($axelcmd);
	
	//记录所有图片
	file_put_contents($allpic,$axelcmd."\n",FILE_APPEND);
	
	if(!file_exists($destpath))
	{
		file_put_contents("/tmp/gjf0122.txt",$destpath."----".$cmd_info."----".$axelcmd."\n\n",FILE_APPEND);
		echo "\n\n\n no file\n\n\n\n";
		continue;
	}
	
	
	$etime= time();
	$usetime = $etime - $stime;
	echo $usetime."\n\n";
	file_put_contents($filepath,"wget---".$i."---".$usetime."---".$p."---".date("Y-m-d H:i:s")."\n",FILE_APPEND);
	ftp_delete($conn_id,$p);
	if($k>30)
	{
		break;
	}
}

ftp_close($conn_id);

$endtime = time();
echo "\r\n"."nowtime:"."\r\n".date("Y-m-d")."runsec:".($endtime-$starttime)."\r\n";


function getcate($picdir)
{
	$arr_cate = array("News"=>74,"Ent"=>71,"Sport"=>69);
	$dirname = substr($picdir,1,strrpos($picdir,"/")-1);

	if(isset($arr_cate[$dirname]))
	{
		return $arr_cate[$dirname];
	}
	else
	{
		return 74;
	}
}



?>