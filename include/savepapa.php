<?php
/*
	@新结构入库父类
	@2015.08.31
	@by zwg

 */

//根目录
define("SIPA_CMD_DIR","/data/www/sipasave/");
//工具
define("SIPA_IMAGEMAGICKDIR", "/usr/bin/");
//入库日志保存路径
define("SIPA_LOG_DIR", SIPA_CMD_DIR."log/");
//综合日志
define("SIPA_LOGHOUSE_DIR", SIPA_CMD_DIR."loghouse/");


//图片路径中，第几位为资源名称
define("SIPA_PATH_NUM",4);

//数据库，大小图存储路径函数
require_once(SIPA_CMD_DIR."include/saveconf.data.php");

//iptc类
require_once(SIPA_CMD_DIR."include/IPTC.php");

require_once(SIPA_CMD_DIR."/include/SynPic.class.php");

//xmp信息
//require_once(SIPA_CMD_DIR."include/xmpiptc.php");

//ideniptc，使用identify取信息
//require_once(SIPA_CMD_DIR."include/ideniptc.php");

//图片裁边
require_once(SIPA_CMD_DIR."include/cutpic.php");

//图片是否入库判定
require_once(SIPA_CMD_DIR."include/pifenter.php");

//入库日志
require_once(SIPA_CMD_DIR."include/loghouse.php");

//是否有敏感词
require_once(SIPA_CMD_DIR."include/ifviolate.class.php");

//递归遍历目录，得到所有目录名称
require_once(SIPA_CMD_DIR."include/alldir.class.php");


//解析XML文件
//require_once(SIPA_CMD_DIR."include/parsexml.class.php");

//单张图片入库操作类
require_once(SIPA_CMD_DIR."include/saveonepic.php");




//-----------------------------------------------------------------------------------------------函数区
//功能函数，写信息到日志中
function loginf($log, $type) 
{
	//记录同步情况
    $ftime = date("Y-m-d H:i:s");
    $log = $ftime . "---" . $log . "\n";
    
	//下面写一个日志文件
    $writelog = SIPA_LOG_DIR . $type . "/" . date('Y') . date('m') . date('d') . $type . ".txt";
    echo "writelog:".$writelog . "----" . $log . "\n";
    
	if (is_file($writelog)) 
	{
		//如果已经存在此文件
        $_fp = fopen($writelog, "a+");
        $_action = fwrite($_fp, $log);
        fclose($_fp);
    } 
	else 
	{
        $_fp = fopen($writelog, "w+");
        chmod($writelog, 0777);
        $_action = fwrite($_fp, $log);
        fclose($_fp);
    }
}
//根据图片来源和图片名称，取得图片组ID及photoid
function getid_with_log($source,$findfile)
{
	$logpath = SIPA_LOGHOUSE_DIR.$source."/".date("Ymd")."_ins.txt";
	
	$fp = fopen($logpath,"r");
	while(!feof($fp))
	{
		$r = fgets($fp);
		if(stristr($r,$findfile) != '')
		{
			$arr_line = explode("---",$r);
			return array("group_id"=>$arr_line[0],"photo_id"=>$arr_line[1]);
		}
	}
	return 0;
}

//功能函数，得到一个目录下的所有文件，避免用php作遍历
function get_papa_ls($dir) 
{
    $cmdls = "/bin/ls '" . $dir . "'";
	echo $cmdls;
	$retls = shell_exec($cmdls . " 2>&1");
    $arrls = explode("\n", $retls);

    $arrlsf = array();
    foreach ($arrls as $kl => $vl) 
	{
        //为空，和. .. 忽略
        if (($vl == '') || ($vl == '.') || ($vl == '..')) {
            continue;
        }

        $vl = trim($vl);
        if (strpos($vl, "'") > 0) 
		{
            $newfindfile = str_replace("'", "", $vl);
            $newpathfile = $dir . "/" . $newfindfile;
            rename($dir . "/" . $vl, $newpathfile);
            $arrlsf[] = $newfindfile;
        }
		else 
		{
            $arrlsf[] = $vl;
        }
    }
	print_r($arrlsf);
    return $arrlsf;
}


//功能函数，检查程序是否在运行
/*
function ifrun($clsname) {
    $nn = exec("ps aux | grep " . $clsname . " | grep -v grep | wc -l");
    return $nn;
}
*/


//当前图片是否已经存在于ins文件当中
function read_ins_txt($pathfile) 
{
    $arr_dir = explode("/", $pathfile);
    $sname = $arr_dir[SIPA_PATH_NUM];
    if ($sname == '') {
        $sname = 'unknown';
    }
    $logbase = SIPA_LOGHOUSE_DIR. $sname . "/" . date("Ymd") . "_ins.txt";
    $arr_row = file($logbase);

    $arr_ha = array_reverse($arr_row);
    $ifins = 0;
    foreach ($arr_ha as $k => $row) 
	{
        if (strstr($row, $pathfile) != '') 
		{
            $ifins = 1;
            break;
        }
    }

    return $ifins;
}

//删除文件夹
function deletedir($dir)
{
	//如果删除目录失败，并且它确认是个文件夹的话
	if (rmdir($dir)==false && is_dir($dir)) 
	{
		//打开文件夹
		if ($dp = opendir($dir)) 
		{
			//读取文件夹下内容
			while (($file=readdir($dp)) != false) 
			{
				echo "dirname:".$file."\n";
				//文件夹下有下一级文件夹的情况，直接递归
				if (is_dir($file) && $file!='.' && $file!='..') 
				{
					deletedir($file);
				} 
				else 
				{
					//如果碰到的是文件，直接删除
					unlink($file);
				}
			}
			//关闭文件夹
			closedir($dp);
		} 
		else 
		{
			echo $dir." Not permission";
		}
	}
}

//同步
function dosync($groupid,$photoid,$link) 
{
	if ($photoid >0)
	{
		$syn = new SynPic($link);
		$syn->syn($groupid,$photoid);
	}
}

//-----------------------------------------------------------------------------------------------SAVEPAPA类
class savepapa 
{

    var $link;	//到数据库的连接

    //构造函数，建立到数据库的连接

    function savepapa() 
	{
		$this->link = mysql_connect(SIPA_DATABASE_HOST, SIPA_DATABASE_USERNAME, SIPA_DATABASE_PASSWORD);
        mysql_select_db(SIPA_DATABASE_NAME, $this->link);
		mysql_query("SET NAMES utf8");
    }

    //检测是否坏图
    function if_good_pic($picpath) 
	{
        //创建
		$im		= imagecreatefromjpeg($picpath);
		//尺寸
		$size	= getimagesize($picpath);
		//坏图颜色值8421504
		
		
		/*X轴取数开始位置
		jpg图片传输数据是从上至下，从左至右，所以选择图片右下角部分做比对
		*/
		$x = $size[0]-1;
		$ifwrong = 0;
		for($y=$size[1]-1;$y>$size[1]-3;$y--)
		{
			$colorIndex = imagecolorat($im, $x, $y);
			if($colorIndex == 8421504 || $colorIndex === '')
			{
				echo "yes\n";
				$ifwrong = 1;
				break;
			}
		}
		return $ifwrong;
    }

    //功能函数,检查是否jpg图片
    function ifjpg($findfile) 
	{
        $fileext = substr(strtolower($findfile), -3);
        if ($fileext == "jpg")
		{
            return true;
        } 
		else 
		{
            return false;
        }
    }

    //功能函数,检查文件大小
    function getsize($pathfile, $limsize = 4000) 
	{
        if (!is_file($pathfile)) {
            return false;
        }
        $thissize = filesize($pathfile);
        if (($thissize < $limsize) || ($thissize == false)) { //如得到大小出错或图片小于2k,不作处理
            return false;
        } else {
            return true;
        }
    }

  

    //功能函数，检查文件是否正在被打开
    function ifimgopen($imgpath, $imgdir) {
        $cmdlsof = "/usr/sbin/lsof \"" . $imgpath . "\" 2>&1";
		echo $cmdlsof;
        $retlsof = shell_exec($cmdlsof);
        if ($retlsof == '') {
            return 0;
        } else {

            $logtext = $imgpath . " img is open \n" . $retlsof;
            $lh = new loghouse($imgpath, $logtext, 'err');
            $lh->rec();
            return 1;
        }
    }







	

	//使用identify取得图片宽和高
	function getImgSize($file)
	{
		$cmd = SIPA_IMAGEMAGICKDIR."identify ".$file;
		$res = shell_exec($cmd);
		$arr_res = explode(" ",$res);
		$arr_wh = explode("x",$arr_res[2]);

		return $arr_wh;
	}

	//sipa取图片信息函数
	function fetchImginfo($img)
	{
		$sizes = ($_s = getimagesize($img, $info)) ? $_s : $this->getImgSize($img);
		if (isset($info["APP13"])) 
		{
			$iptc = iptcparse($info["APP13"]);
			if (is_array($iptc)) 
			{
				$data['caption']		= str_replace(array("'",",","\\",","), " ", addslashes($iptc["2#120"][0]));
				$data['headline']		= str_replace(array("'",",","\\",","), " ", addslashes($iptc["2#105"][0]));
				$data['gtitle']			= $data['headline'];
				$data['pdate']			= $iptc["2#055"][0];
				$data['country']		= $iptc["2#101"][0];
				$data['pwebsid']		= $iptc["2#103"][0];
				$data['source']			= $iptc["2#110"][0];
				$data['photo_source']	= $iptc["2#115"][0];
				$data['object_name']	= $iptc["2#005"][0];
				$data['category']		= $iptc["2#015"][0];
				$data['city']			= $iptc["2#090"][0];
				$data['state']			= $iptc["2#095"][0];
				$data['keyword']		= implode(",",$iptc["2#025"]);
				$data['gcontent']		= $data['caption'];
				$data['title']			= $data['caption'];
			}
		}


		$data['w']    = $sizes[0];
		$data['h']    = $sizes[1];
		$data['mime'] = $sizes['mime'];
		return $data;
	}


	
}


// end for class 
?>
