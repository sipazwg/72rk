<?php
/*
	做入库的日志，按资源分布其内容
    by zwg
    2014.07.08
*/
class loghouse
{
	  //var
	  var $ppath;
	  var $logbase;
	  var $logpath;
	  var $logtext;
	  var $logtype;
	  
	  //construct
	  function loghouse($ppath,$logtext,$logtype)
	  {
		    $this->ppath = $ppath;			//图片路径
		    $this->logtext = $logtext;		//日志内容
		    $this->logtype = $this->getlogtype($logtype);	//日志类型
		    
			$sysname = $this->sourcename($ppath);	//根据目录名称，取得资源保存目录
		    $this->logbase = SIPA_LOGHOUSE_DIR.$sysname."/";
			//$this->logbase = "/data/www/newsave/loghouse/".$sysname."/";
			//echo $this->logbase."\n";

		    if (!is_dir($this->logbase))
		    {
				  mkdir($this->logbase, 0777);
			}
		    $filename = date("Ymd")."_".$this->logtype;
			
		    $this->logpath = $this->logbase.$filename.".txt";
		    //echo $this->logpath."\n";
		    //文件存在否？
		    if (!is_file($this->logpath))
		    {
				 touch($this->logpath);
			}
	  }
	  
	  function getlogtype($type)
	  {
		    $arr_type = array("ins","err","tra");
		    if (!in_array($type,$arr_type))
		    {
				  $type = "err";
			}
			else
			{
				  
			}
			return $type;
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
	  }
	  
	  function rec()
	  {
			$_fp = fopen($this->logpath,"a+");
		    $_action = fwrite($_fp,$this->logtext."\n");
		    fclose($_fp);
	  }
}

/*
//--------------------------------now,let's have a test
$path = "/data/ftpdir/editor/pic/iptc2009112719430216__1-781562.jpg";
$text = $path." test error";
$pi = new loghouse($path,$text,'ins');
$pi->rec();
*/

?>
