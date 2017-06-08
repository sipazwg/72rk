<?php
/*
	取得目录下所有目录的名称
	主要用于不定层级目录
	by zwg
	2014.07.08
*/

class alldir
{
	var $filepath;//文件路径 
	var $arrdir;//需要返回的路径数组

	//构造函数
	function alldir($filepath)
	{
		//接收变量
		$this->filepath=$filepath;
		$this->arrdir=array();

		//执行遍历
		$this->dirtree($this->filepath);
	}

	//递归遍历目录
	function dirtree($path)
	{
		$pathfile = $path;
		$d = dir($pathfile);
		while(($v=$d->read()) != null) 
		{
			if($v == "." || $v == "..")
			continue;
			$file = $d->path."/".$v;
			
			$domain = strstr($file, 'docment');
			$bak = strstr($file, '.bak');
			$rex = strstr($file, 'rex');
			$photo = strstr($file, 'photo');
			if($domain || $bak || $rex || $photo ){
				
			}else{
				if(is_dir($file))
				{
					//实现递归
					$this->dirtree($file);
				}
				else
				{
					$strpos = strrpos($file,"/");
					$nojpgpath = substr($file,0,$strpos); 
					//对于数组中不存在的目录进行记录
					if(!in_array($nojpgpath,$this->arrdir))
					{
						$this->arrdir[]=$path;
					}
				}
			}
		}
	   $d->close();
	}
	
	
}
?>
