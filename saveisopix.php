<?php
/*
  @isopix���
  @2015-09-06
  @by zwg
*/

require_once("/data/www/sipasave/include/savepapa.php");

//----------------------------ʹ��saveonepic--------------------//
class saveisopix extends savepapa
{
	//�ֶΣ���ǰ�������õĸ����ֶ�

	var $picdir;		//Ҫ���ʵ�Ŀ¼
	var $bakdir;		//���汸��ͼƬ��Ŀ¼
    var $groupobj;		//�����ʶ
	var $info;			//ͼƬ��ϸ��Ϣ

	//-----------------------------------���캯��---------------//
	function saveisopix()
	{
		//����Ӧ�ı������г�ʼ��
		$this->picdir		= "/data/www/ftpdir/isopix";			//Ҫ���ʵ�Ŀ¼
		$this->bakdir		= $this->picdir.'.bak';		//���ݵ�Ŀ¼
		$this->ifmod		= "1";									//�Ƿ����
		$this->price		= "0";									//ͼƬ�۸�
		$this->ifedit		= "0";
		$this->ifstock		= "0";									//�Ƿ�����ͼ
		$this->upuid		= "29621";								//��Ӱʦid
		$this->upway		= "1";									//�ϴ�;��(0:��ʷ����1:ftp2ǰ̨3:��̨)
		$this->upip			= "106.3.36.72";						//���IP
		$this->penname		= "isopix";							//����
		$this->plevel		= "2";									//ͼƬ�ļ���sipausa��rex��Ϊ2��
		$this->zoneid		= "3";									//����ID��1�����ڣ�2���۰�̨��3������
		$this->catestr		= 'SALL,GJXW';
		$this->tpid			= "0";
		//�̳и����н��������ݿ����ӵĴ���
		parent::savepapa();
	}

	//��Դ����վ��ID
	function setwebsid($filename)
	{
		return str_replace(".jpg","",strtolower($filename));
	}
   
	function cycle()
	{
		//�����з���ȡ������������ͼƬ
		$arr_pic=get_papa_ls($this->picdir);
		
		$arr_group=array();
		foreach ($arr_pic as $kp => $findfile)
		{
			if (($findfile=='.') || ($findfile=='..')) continue;
			
			$pathfile=$this->picdir."/".$findfile;
			$bakfile=$this->bakdir."/".$findfile;
			
			//����Ŀ¼��������
			if (is_dir($pathfile))
			{
				continue;
			}
			
			//�������jpg�򲻴���
			if ($this->ifjpg($findfile)==false )
			{
				$logtext = $pathfile."not jpg";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}
			
			//���д�С�ļ��
			if ($this->getsize($pathfile)==false)
			{
				continue;
			}
			
			//���ͼƬ�Ƿ�����д�룬��װlsof,yum install lsof
			if ($this->ifimgopen($pathfile,$this->picdir)==1)
			{
				continue;
			}
			
			//���ͼƬ�Ƿ�����⣬ͬһ����ͼƬ���������һ�Σ����첻���ظ����
			$pi = new pifenter($pathfile);
			if ($pi->ifin() == 1)
			{
				$logtext = $pathfile."--- have been enter";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}

			//�Ƿ��ǻ�ͼ
			if($this->if_good_pic($pathfile) == 1)
			{
				$logtext = $pathfile."---".$this->photoerid." is bed";
				$lh = new loghouse($pathfile,$logtext,'err');
				$lh->rec();
				rename($pathfile,$bakfile);
				continue;
			}

			//�õ�ͼƬ������Ϣ
			$this->info        = $this->fetchImginfo($pathfile);

			$str_f_obj = substr($findfile, 0, strpos($findfile, '-'));

			
			//�Ƿ����Υ����Ϣ
			$wginfo = new ifviolate($pathfile,$this->penname,$this->info['title'],$this->info['keyword'],$this->info['title'],$this->info['gcontent']);
			//�Ƿ����Υ����Ϣ
			if ($wginfo->run() == "1")
			{
				rename($pathfile,$bakfile);
				continue;
			}


				$str_category = trim(strtolower($this->info['category']));

				if(strstr($str_category,"spo") != false)
				{
					$catestr = "SALL,XWTY";//����
				}
				elseif(strstr($str_category,"ent") != false)
				{
					$catestr = "SALL,XWYL";//����
				}
				elseif(strstr($str_category,"fin") != false)
				{
					$catestr = "SALL,XWCJ";//�ƾ�
				}
				elseif(strstr($str_category,"sci") != false)
				{
					$catestr = "SALL,XWKJ";//�Ƽ�
				}
				else
				{
					$catestr = "SALL,GJXW";//��������74
				}


			//��Ϣ
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
			$this->info['topicid']	= $this->topicid;


			$this->info['pwebsid']	= $this->setwebsid($findfile);

			$this->info['catestr']	= $catestr;
		

			//groupobj �����ʶ��

			$this->info['groupobj'] = date("Ymd")."isopix".$this->info['catestr']."-".$str_f_obj;
			
			//�������
			
			
			$saveone = new saveonepic();
	

			//��ʼ�����ֵ
			$saveone->link			=	$this->link;			//���ݿ�����
			$saveone->imgpath		=	$pathfile;			//��ԴͼƬ·��
			$saveone->info			=	$this->info;		//ͼƬ��Ϣ
			
			//��⵱ǰ��һ��ͼƬ
			$saveone->excute();

			//ɾ��ԭͼ
			rename($pathfile,$bakfile);

			//���һ��ͬ��һ��
			dosync($saveone->groupid,$saveone->photoid,$this->link);

			
		}   //end for foreach ($arr_pic as $kp => $findfile)
	}      //end for cycle
  
}      //end for class 




//-------------------------------���ʵ��----------------------------------------------------//

$n = exec("ps aux |grep '" . __FILE__ . "' | grep -v grep | wc -l");
if($n > 1)
{
	die(date("Y-m-d H:i:s") . __FILE__ . " is running");
}
$save = new saveisopix();
$save->cycle();
?>