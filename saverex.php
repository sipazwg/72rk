<?php
/*
  @rex入库
  @2015-09-07
  @by zwg
*/

require_once("/data/www/sipasave/include/savepapa.php");

//----------------------------使用saveonepic--------------------//
class saverex extends savepapa
{
	//字段，事前进行设置的各个字段

	var $picdir;		//要访问的目录
	var $bakdir;		//保存备份图片的目录
    var $groupobj;		//成组标识
	var $info;			//图片详细信息

	//-----------------------------------构造函数---------------//
	function saverex($catestr,$picdir)
	{
		//对相应的变量进行初始化
		$this->picdir		= $picdir;								//要访问的目录
		$this->bakdir		= "/data/www/ftpdir/rex.bak/".basename($this->picdir);	//备份的目录		
		$this->ifmod		= "1";									//是否审核
		$this->price		= "0";									//图片价格
		$this->ifedit		= "0";
		$this->ifstock		= "0";									//是否资料图
		$this->upuid		= "12166";								//摄影师id
		$this->upway		= "1";									//上传途径(0:历史数据1:ftp2前台3:后台)
		$this->upip			= "106.3.36.72";						//入库IP
		$this->penname		= "rex";								//笔名
		$this->plevel		= "2";									//图片的级别，sipausa与rex均为2级
		$this->zoneid		= "3";									//地区ID，1：国内，2：港奥台，3：国外
		$this->catestr		= $catestr;
		$this->tpid			= "0";
		$this->pwebsid		= '';
		//继承父类中建立到数据库连接的代码
		parent::savepapa();
	}


	//资源方网站的ID
	function setwebsid($filename)
	{
		$arr_ex = explode("_",$filename);
		$str_res = trim(str_replace(".jpg","",$arr_ex[count($arr_ex)-1]));
		$str_res = trim(str_replace(".JPG","",$str_res));


		return $str_res;
	}
   
	function cycle()
	{
		//父类中方法取得所有需入库的图片
		$arr_pic=get_papa_ls($this->picdir);
		
		$arr_group=array();
		foreach ($arr_pic as $kp => $findfile)
		{
			if (($findfile=='.') || ($findfile=='..')) continue;
			
			$pathfile=$this->picdir."/".$findfile;
			$bakfile=$this->bakdir."/".$findfile;
			
			//不对目录做操作。
			if (is_dir($pathfile))
			{
				continue;
			}
			
			//如果不是jpg则不处理，rex使用axel下载，有临时文件的扩展名为st
			if ($this->ifjpg($findfile)==false && (substr(strtolower($findfile), -2) != "st"))
			{
				$logtext = $pathfile."not jpg";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}
			
			//进行大小的检查
			if ($this->getsize($pathfile)==false)
			{
				continue;
			}
			
			//检查图片是否正在写入，安装lsof,yum install lsof
			if ($this->ifimgopen($pathfile,$this->picdir)==1)
			{
				continue;
			}
			
			
			//检查图片是否入过库，同一名称图片，当天入过一次，当天不再重复入库
			$pi = new pifenter($pathfile);
			if ($pi->if_rex_in() == 1)
			{
				$logtext = $pathfile."---".$this->upuid." have been enter";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}
		
			//是否是坏图
			if($this->if_good_pic($pathfile) == 1)
			{
				$logtext = $pathfile."---".$this->upuid." is bed";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}

			//得到图片基本信息
			$this->info        = $this->fetchImginfo($pathfile);
			
			//是否包含违规信息
			$wginfo = new ifviolate($pathfile,$this->penname,$this->info['title'],$this->info['keyword'],$this->info['title'],$this->info['gcontent']);
			//是否包含违规信息
			if ($wginfo->run() == "1")
			{
				rename($pathfile,$bakfile);
				continue;
			}

			//信息
			$this->info['ifmod']	= $this->ifmod;
			$this->info['price']	= $this->price;
			$this->info['ifedit']	= $this->ifedit;
			

			$this->info['ifstock']	= $this->ifstock;
			$this->info['upuid']	= $this->upuid;
			$this->info['upway']	= $this->upway;
			$this->info['upip']		= $this->upip;
			$this->info['penname']	= $this->penname;
			$this->info['plevel']	= $this->plevel;
			$this->info['zoneid']	= $this->zoneid;
			$this->info['tpid']		= $this->tpid;


			$this->info['pwebsid']	= $this->setwebsid($findfile);

			$this->info['catestr']	= $this->catestr;

			//groupobj 成组标识，
			$this->info['groupobj'] = date("Ymd")."rexs".$this->info['catestr']."-".$this->info['gtitle'];

			$this->info['title']		= iconv("utf8","gb2312",$this->info['title']);
			$this->info['gtitle']		= iconv("utf8","gb2312",$this->info['gtitle']);
			$this->info['gcontent']		= iconv("utf8","gb2312",$this->info['gcontent']);
			$this->info['keyword']		= iconv("utf8","gb2312",$this->info['keyword']);


	
			
			//单张入库
			
			
			$saveone = new saveonepic();
	

			//初始化入库值
			$saveone->link			=	$this->link;			//数据库连接
			$saveone->imgpath		=	$pathfile;			//资源图片路径
			$saveone->info			=	$this->info;		//图片信息
			
			//入库当前此一张图片
			$saveone->excute();

			//删除原图
			rename($pathfile,$bakfile);

			echo "photoid:".$saveone->photoid."\n";
			echo "groupid:".$saveone->groupid."\n";


			//入库一张同步一次
			dosync($saveone->groupid,$saveone->photoid,$this->link);

		}   //end for foreach ($arr_pic as $kp => $findfile)
	}      //end for cycle
  
}      //end for class 






//-------------------------------入库实体----------------------------------------------------//
$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 1)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}

$obj_dir = array(
		"SALL,XWYL"=>"/data/www/ftpdir/rex/Ent",
		"SALL,XWTY"=>"/data/www/ftpdir/rex/Sport",
		"SALL,GJXW"=>"/data/www/ftpdir/rex/News"
);


//遍历
foreach($obj_dir as $k=>$v)
{
	//对符合要求的文件夹进行入库实例
	$save = new saverex($k,$v);
	$save->cycle();
}
?>