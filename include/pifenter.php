<?php
/*
	检查图片是否已入库
    by zwg
    2014.07.08
*/

class pifenter
{
	//var
	var $ppath;
	var $logbase;
	var $logpath;
	
	//construct
	function pifenter($ppath)
	{
		$this->ppath = $ppath;
		$sysname = $this->sourcename($ppath);
		if($sysname == 'YRLJW')
		{
			$sysname == "YRLJW_tem";
		}
		if($sysname == 'Abbott')
		{
			$sysname == "Abbott_tem";
		}
		if($sysname == 'photo_kr3')
		{
			$sysname == "photo_kr3_tem";
		}
		
		$this->logbase = SIPA_LOGHOUSE_DIR.$sysname."/";
		//$this->logbase = "/data/www/newsave/loghouse/".$sysname."/";
		if (!is_dir($this->logbase))
		{
			  mkdir($this->logbase, 0777);
		}
		$filename = date("Ymd")."_ifin";
		$this->logpath = $this->logbase.$filename.".txt";
		
		//文件存在否？
		if (!is_file($this->logpath))
		{
			 touch($this->logpath);
		}
			
	}
	  
	function sourcename($ppath)
	{
		$arr_dir = explode("/",$ppath);
		$sname = $arr_dir[SIPA_PATH_NUM];
		if ($sname == '')
		{
			  $sname = 'unknown';
		}
		return $sname;
	}
	  
	//write file
	function ifin()
	{
		$content = file_get_contents($this->logpath);
		if (strpos($content,$this->ppath) === false)
		{
			  return 0;
		}
		else
		{
			  return 1;
		}
		return 0;
	}
	//rex图片，会在两个目录中同时保存同一图片，在此只判断图片名称
	function if_rex_in()
	{
		$content = file_get_contents($this->logpath);
		if(strpos($content,substr($this->ppath,strrpos($this->ppath,"/")+1)) === false)
		{
			return 0;
		}
		else
		{
			return 1;
		}
	}
	  
	function rec()
	{
		if ($this->ifin()==1)
		{
			  //do nothing
		}
		else
		{
			$_fp = fopen($this->logpath,"a+");
			$_action = fwrite($_fp,$this->ppath."\n");
			fclose($_fp);  
		}
	}
}

/*
//--------------------------------now,let's have a test
$path = "/data/ftpdir/editor/pic/iptc2009112719430216__1-781562.jpg";
$pi = new pifenter($path);
 $pi->ifin();
$pi->rec();
*/

?>
