#!/usr/bin/php
<?php
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
	    $dir = $this->dirtree($this->filepath);
             var_dump($dir);
	}

	//递归遍历目录
	function dirtree($path)
	{
		$pathfile = $path;
		$d = dir($pathfile);//列出某一文件夹下的所有文件和文件夹，不含子目录文件夹下的文件
		while(($v=$d->read()) != null) //循环读取文件夹的下的文件
		{
			if($v == "." || $v == "..")
			continue;
			$file = $d->path."/".$v;

			if(is_dir($file))
			{
				//echo $file."\n";
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
	   $d->close();
	}
}

$dir = "/data/www/ftpdir/username";
$obg = new alldir($dir);
print_r($obg);
