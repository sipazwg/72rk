<?php
/*
  @agif入库
  @2017-06-02
  @by yxq
*/

require_once("/data/www/sipasave/include/savepapa.php");

//----------------------------使用saveonepic--------------------//
class saveagif extends savepapa
{
	//字段，事前进行设置的各个字段

	var $picdir;		//要访问的目录
	var $bakdir;		//保存备份图片的目录
    var $groupobj;		//成组标识
	var $info;			//图片详细信息

	//--------------------构造函数---------------//
	function saveagif()
	{
		//对相应的变量进行初始化
		$this->picdir		= "/data/www/ftpdir/agif";								//要访问的目录    /data/www/ftpdir/sports
		$this->bakdir		= "/data/www/ftpdir/agif.bak";	        //备份的目录
		$this->ifmod		= "1";									//是否审核
		$this->price		= "0";									//图片价格
		$this->ifedit		= "0";                                  //是否编辑
		$this->ifstock		= "0";							  		//是否资料图
		$this->upuid		= "109413";						    	//摄影师id
		$this->upway		= "1";									//上传途径(0:历史数据1:ftp2前台3:后台)
		$this->upip			= "106.3.36.72";						//入库IP
		$this->penname		= "AGIF传图专用账号";				    //笔名
		$this->plevel		= "3";									//图片的级别，sipausa与rex均为2级
		$this->zoneid		= "3";					                //地区ID，1：国内，2：港奥台，3：国外
		$this->catestr		= "SALL,XWTY";                          //指定字符串的完整路径--体育类--娱乐类等等
		$this->tpid			= "0";
		$this->pwebsid		= '';
		//继承父类中建立到数据库连接的代码
		parent::savepapa();
	}


	function cycle()
	{
		//父类中方法取得所有需入库的图片，并返回一个一维数组
		$arr_pic=get_papa_ls($this->picdir);

		$arr_group=array();
		foreach ($arr_pic as $kp => $findfile)
		{
			if (($findfile=='.') || ($findfile=='..')) continue;

			$pathfile=$this->picdir."/".$findfile;//获得图片资源全路径
			$bakfile=$this->bakdir."/".$findfile;//获得图片资源备份全路径

			//不对目录做操作。
			if (is_dir($pathfile))
			{
				continue;
			}
            
           

			//如果不是jpg则不处理
			if ($this->ifjpg($findfile)==false)
			{
				$logtext = $pathfile."not jpg";
				$lh = new loghouse($pathfile,$logtext,'err');//日志入库
				$lh->rec();//已追加的形式添加日志信息
				rename($pathfile,$bakfile);//对文件重命名   rename(oldname,newname)
				continue;
			}

			//对文件大小进行判断，大于4000不做处理
			if ($this->getsize($pathfile)==false)
			{
				continue;
			}

			//判断图片是否正在写入
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
			//得到图片基本信息----全部信息
			$this->info  = $this->fetchImginfo($pathfile);
            
			//是否包含违规信息-----敏感词汇过滤
			$wginfo = new ifviolate($pathfile,$this->penname,$this->info['title'],$this->info['keyword'],$this->info['title'],$this->info['gcontent']);
			//是否包含违规信息
			if ($wginfo->run() == "1")
			{
				rename($pathfile,$bakfile);
				continue;
			}
			$this->info['title'] 	= str_replace("/AGIF","",$this->info['title']);
			$this->info['gtitle'] 	= str_replace("/AGIF","",$this->info['gtitle']);
			$this->info['gcontent'] = str_replace("/AGIF","",$this->info['gcontent']);
			//信息
			$this->info['ifmod']	= $this->ifmod;
			$this->info['price']	= $this->price;
			$this->info['ifedit']	= $this->ifedit;
			$this->info['ifstock']	= $this->ifstock;//是否是资料图
			$this->info['upuid']	= $this->upuid;//摄影师ID
			$this->info['upway']	= $this->upway;//上传途径
			$this->info['upip']		= $this->upip;//上传ID
			$this->info['penname']	= $this->penname;//笔名
			$this->info['plevel']	= $this->plevel;//图片级别
			$this->info['zoneid']	= $this->zoneid;//地区
			$this->info['tpid']		= $this->tpid;//
		    $this->info['pwebsid']	= $this->pwebsid;
            $this->info['catestr']	= $this->catestr;
			
			$arr_path = explode("-",$findfile);
			//groupobj 成组标识
            $this->info['groupobj'] = date("Ymd")."agif"."-".$arr_path[0];
            
			//单张入库
			$saveone = new saveonepic();
			//初始化入库值
			$saveone->link			=	$this->link;		//数据库连接
			$saveone->imgpath		=	$pathfile;			//资源图片路径
			$saveone->info			=	$this->info;		//图片信息

			//入库当前此一张图片
			$saveone->excute();

			//删除原图
			rename($pathfile,$bakfile);
			//根据图片的组ID和照片ID，入库一张同步一次
			dosync($saveone->groupid,$saveone->photoid,$this->link);

		}   
	}      
}      

//-------------------------------入库实体----------------------------------------------------//
$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 2)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}



//对符合要求的文件夹进行入库实例
$save = new saveagif();
$save->cycle();
?>
