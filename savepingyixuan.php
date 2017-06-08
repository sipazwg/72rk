<?php
/*
  @国内摄影师 平一轩 入库程序
  @2017-05-17
  @by yan
*/

/*
* 	是否是专题 tipid？图片级别？摄影师区域？caetstr？
*/

// error_reporting(0);
require_once("/data/www/sipasave/include/savepapa.php");
// die("End");
//----------------------------使用saveonepic--------------------//
class saveping extends savepapa
{
	//字段，事前进行设置的各个字段

	var $picdir;		//要访问的目录
	var $bakdir;		//保存备份图片的目录
    var $groupobj;		//成组标识
	var $info;			//图片详细信息

	//-----------------------------------构造函数---------------//
	function saveping($picdir)
	{
		//对相应的变量进行初始化
		$this->picdir		= $picdir;								//要访问的目录
		$this->bakdir		= "/data/www/ftpdir/pingyixuan.bak";		//备份的目录
		$this->ifmod		= "0";									//是否审核
		$this->price		= "0";									//图片价格
		$this->ifedit		= "0";
		$this->ifstock		= "0";									//是否资料图
		$this->upuid		= "64629";								//摄影师id
		$this->upway		= "1";									//上传途径(0:历史数据1:ftp2前台3:后台)
		$this->upip			= "106.3.36.72";						//入库IP
		$this->penname		= "GeForce";							//笔名
		$this->plevel		= "3";									//图片的级别，
		$this->zoneid		= "1";									//地区ID，1：国内，2：港奥台，3：国外
		$this->catestr		= 'SALL,XWYL';
		$this->tpid			= "0";
		//继承父类中建立到数据库连接的代码
		parent::savepapa();
	}


	//资源方网站的ID
	function setwebsid($filename)
	{
		return str_replace(".jpg","",strtolower($filename));
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

			//如果不是jpg则不处理
			if ($this->ifjpg($findfile)==false )
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
			if ($pi->ifin() == 1)
			{
				$logtext = $pathfile."--- have been enter";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}

			//是否是坏图
			if($this->if_good_pic($pathfile) == 1)
			{
				$logtext = $pathfile."---".$this->photoerid." is bed";
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
			$this->info['tpid']	= $this->tpid;


			$this->info['pwebsid']	= $this->setwebsid($findfile);

			$this->info['catestr']	= $this->catestr;

			//groupobj 成组标识，
			$this->info['groupobj'] = date("Ymd")."pyx".$this->info['catestr']."-".$this->picdir;

			//单张入库
			
			$saveone = new saveonepic();
	

			//初始化入库值
			$saveone->link			=	$this->link;			//数据库连接
			$saveone->imgpath		=	$pathfile;			//资源图片路径
			$saveone->info			=	$this->info;		//图片信息
			
			//入库当前此一张图片
			$saveone->excute();
			die("save");
			//删除原图
			rename($pathfile,$bakfile);

			//入库一张同步一次
			dosync($saveone->groupid,$saveone->photoid,$this->link);

			
		}   //end for foreach ($arr_pic as $kp => $findfile)
	}      //end for cycle
  
}      //end for class 






//-------------------------------入库实体----------------------------------------------------//
$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 2)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}

//需读取的文件夹
$filepath	= "/data/www/ftpdir/pingyixuan";

//通过alldir，得到文件夹下所有目录名称
$obj_dir	= new alldir($filepath);

//遍历
foreach($obj_dir->arrdir as $k=>$v)
{
	echo $v."\n";

	//取得创建时间
	$ctime = filectime($v);
	$nowtime = time();

	//如果文件夹是5天前创建的，直接接文件夹删除
	if(($nowtime - $ctime)>432000)
	{
		deletedir($v);
		continue;
	}
	//对符合要求的文件夹进行入库实例
	$save = new saveping($v);
	$save->cycle();
}
?>