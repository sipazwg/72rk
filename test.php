<?php
/*
  @tpg入库
  @2015-09-07
  @by zwg
*/

require_once("/data/www/sipasave/include/savepapa.php");

//----------------------------使用saveonepic--------------------//
class savetpg extends savepapa
{
	//字段，事前进行设置的各个字段

	var $picdir;		//要访问的目录
	var $bakdir;		//保存备份图片的目录
    var $groupobj;		//成组标识
	var $info;			//图片详细信息

	//-----------------------------------构造函数---------------//
	function savetpg($picdir)
	{
		//对相应的变量进行初始化
		$this->picdir		= $picdir;								//要访问的目录
		$this->bakdir		= "/data/www/ftpdir/tpg.bak";		//备份的目录
		$this->ifmod		= "0";									//是否审核
		$this->price		= "0";									//图片价格
		$this->ifedit		= "0";
		$this->ifstock		= "0";									//是否资料图
		$this->upuid		= "102630";								//摄影师id
		$this->upway		= "1";									//上传途径(0:历史数据1:ftp2前台3:后台)
		$this->upip			= "106.3.36.72";						//入库IP
		$this->penname		= "zda";								//笔名
		$this->plevel		= "2";									//图片的级别，sipausa与rex均为2级
		$this->zoneid		= "2";									//地区ID，1：国内，2：港奥台，3：国外
		$this->catestr		= 'SALL,XWYL';
		$this->tpid			= "0";
		$this->pwebsid		= '';
		//继承父类中建立到数据库连接的代码
		parent::savepapa();
	}


   
	function cycle()
	{
		//当传入的目录名称中没有txt文件时，不入库，此时没有图说
		
		//若有txt文档，则使用txt文档内容******start******
		
		//$tem_arr = glob($this->picdir."/*.txt");
		//if(!$tem_arr[0])
		//{
		//	$tem_arr = glob($this->picdir."/*.txt.old");
		//}
		//如果txt文件不存在
		//if(!$tem_arr)
		//{
		//	die("no txt file!!");
		//}


		//父类中方法取得所有需入库的图片
		$arr_pic=get_papa_ls($this->picdir);
	        var_dump($arr_pic);	
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
				//rename($pathfile,$bakfile);
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
				// rename($pathfile,$bakfile);
				// continue;
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
			$this->info['tpid']		= $this->tpid;


			$this->info['pwebsid']	= $this->pwebsid;

			$this->info['catestr']	= $this->catestr;

			//groupobj 成组标识，
			$this->info['groupobj'] = date("Ymd")."-".$this->picdir;
			//$this->info['groupobj'] = "20151229-".$this->picdir;
			$this->info['gtitle'] = preg_replace('/^.+[\\\\\\/]/', '', $this->picdir);


			$encode = mb_detect_encoding($this->info['gtitle'], array("BIG5","ASCII","UTF-8","GB2312","GBK"));
			if($encode == "CP936")
			{
				$this->info['gtitle']	= iconv("CP936","utf8",$this->info['gtitle']);
				if(!$this->info['title'])
				{
					$this->info['title'] = $this->info['gtitle'];
				}
				
			}

			
			//若有txt文档，则使用txt文档内容******start******
			$tem_arr = glob($this->picdir."/*.txt");
			if(!$tem_arr[0])
			{
				$tem_arr = glob($this->picdir."/*.txt.old");
			}
			if($tem_arr)
			{
				//若有txt文档，则通过
				$this->info['ifmod']	= '1';
				$txt_path	= $tem_arr[0];
				$tem_info	= file_get_contents($txt_path);
				$encode		= mb_detect_encoding($tem_info, array("BIG5","ASCII","UTF-8","GB2312","GBK"));
				
				if($encode == "BIG5" || $encode == "BIG-5")
				{
					$tem_info= iconv("BIG5","utf8",$tem_info);
				}
				if($tem_info)
				{
					$this->info['gcontent'] = $tem_info;
					$this->info['title'] = $tem_info;

					//查询此组是否有图片入库，判断其图说是否为空。用于防止txt文档由于晚到出现无图说的问题。
					$sql_g = "select * from sipa_group where groupobj='".md5($this->info['groupobj'])."'";

					$res_g = mysql_query($sql_g,$this->link);

					//已存在组照，则取其信息
					if (mysql_num_rows($res_g)>0)
					{
						$row_g = mysql_fetch_assoc($res_g);
						$sql_ginfo = "select * from sipa_sphoto where groupid = ".$row_g['groupid'];
						$res_ginfo = mysql_query($sql_ginfo,$this->link);
						if(mysql_num_rows($res_ginfo)>0)
						{
							while ($row_ginfo = mysql_fetch_assoc($res_ginfo)) 
							{
								if(!$row_ginfo['title'])
								{
									//只同步原始表sipa_sphoto中的图说字段
									//mysql_query("update sipa_photo set title = '".addslashes($tem_info)."' where photoid = ".$row_ginfo['photoid'],$this->link);
									mysql_query("update sipa_sphoto set title = '".addslashes($tem_info)."' where syspid = ".$row_ginfo['syspid'],$this->link);
									//将当前组入过库的图片审核状态改为通过
									mysql_query("update sipa_photo set ifmod = '1' where groupid = ".$row_g['groupid']." ");
								}
							}
						}
						
					}

				}
			}
			//若有txt文档，则使用txt文档内容******end******
			
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
$filepath	= "/data/www/ftpdir/tpg";

//通过alldir，得到文件夹下所有目录名称
$obj_dir	= new alldir($filepath);
var_dump($obj_dir);
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
	$save = new savetpg($v);
	$save->cycle();
}
?>
