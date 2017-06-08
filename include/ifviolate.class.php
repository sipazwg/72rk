<?php
/*
	检查图片是否有违规之处，GFW

	2009/12/02

	add by zwg
*/

class ifviolate
{
	var $pathfile;		//文件路径
	var $penname;		//笔名
	var $title;			//标题
	var $keyword;		//关键字
	var $gtitle;		//组标题
	var $gcontent;		//组说明
	var $res;			//成员变量，保存是否有违规的地方
	var $strlog;		//log的内容

	//构造函数，接收参数	
	function ifviolate($pathfile,$penname,$title,$keyword,$gtitle="",$gcontent="")
	{
		$this->pathfile  = $pathfile;
		$this->penname   = strtolower($penname);
		$this->title     = strtolower($title);
		$this->keyword   = $keyword;
		$this->gtitle    = strtolower($gtitle);
		$this->gcontent  = $gcontent;
	}

	

	function run()
	{
		//结果值
		$this->res = 0;

		$this->strlog = '';
		//避免敏感字串
		 
		  if (strstr($this->title,'china out') || strstr($this->gtitle,'china out') || strstr($this->keyword,'china out')) 
		  {
			  $this->strlog = $this->pathfile." title CHINA OUT";
			  $this->res = 1;
		  }
		  if (strstr($this->penname,'xinhua') || strstr($this->keyword,'xinhua') || strstr($this->title,'xinhua')) 
		  {
			  $this->strlog = $this->pathfile." penname or keyword or title  Xinhua";
			  $this->res = 1;
		  }
		 
		  if (strstr($this->title,'tibet') || strstr($this->gtitle,'tibet') ||  strstr($this->keyword,'tibet'))
		  {
				$this->strlog = $this->pathfile." title tibet";
				$this->res = 1;
		  }
		  if (strstr($this->title,'tibetan') || strstr($this->gtitle,'tibetan') || strstr($this->keyword,'tibetan') )
		  {
				$this->strlog = $this->pathfile." title tibetan";
				$this->res = 1;
		  }
		   
		  if (strstr($this->title,'lhasa') || strstr($this->gtitle,'lhasa') || strstr($this->keyword,'lhasa'))
		  {
				$this->strlog = $this->pathfile." title lhasa";
				$this->res = 1;
		  }
		  if (strstr($this->title,'dharamsala') || strstr($this->title,'dharamsala') || strstr($this->title,'dharamsala'))
		  {
				$this->strlog = $this->pathfile." dharamsala";
				$this->res = 1;
		  }
		  if (strstr($this->title,'dalai lama') || strstr($this->title,'dalai lama') || strstr($this->title,'dalai'))
		  {
				$this->strlog = $this->pathfile." title dalai lama";
				$this->res = 1;
		  }
		   if (strstr($this->title,'china human rights') || strstr($this->title,'china human rights') || strstr($this->title,'china human rights'))
		  {
				$this->strlog = $this->pathfile." china human rights";
				$this->res = 1;
		  }
		
		  if (strstr($this->title,'falun gong') || strstr($this->gtitle,'falun gong') || strstr($this->keyword,'falun gong'))
		  {
				$this->strlog = $this->pathfile." title falun gong";
				$this->res = 1;
		  }
		  if (strstr($this->title,'falun dafa') || strstr($this->gtitle,'falun dafa') || strstr($this->keyword,'falun dafa'))
		  {
				$this->strlog = $this->pathfile." title falun dafa";
				$this->res = 1;
		  }
		  if (strstr($this->title,'east turkistan') || strstr($this->gtitle,'east turkistan') || strstr($this->keyword,'east turkistan'))
		  {
				$this->strlog = $this->pathfile." title east turkistan";
				$this->res = 1;
		  }
		  if (strstr($this->title,'anwar ysuef') || strstr($this->gtitle,'anwar ysuef') || strstr($this->keyword,'anwar ysuef'))
		  {
				$this->strlog = $this->pathfile." title anwar ysuef";
				$this->res = 1;
		  }
		  if (strstr($this->title,'mahmut kashgari') || strstr($this->gtitle,'mahmut kashgari') || strstr($this->keyword,'mahmut kashgari'))
		  {
				$this->strlog = $this->pathfile." title mahmut kashgari";
				$this->res = 1;
		  }
		 
		  /**热比娅过滤**/
		  if (strstr($this->title,'rabiye qadir') || strstr($this->gtitle,'rabiye qadir') || strstr($this->keyword,'rabiye qadir'))
		  {
				$this->strlog = $this->pathfile." title or gtitle  Rabiye Qadir";
				$this->res = 1;
		  }

		  if (strstr($this->title,'rebiya kadeer') || strstr($this->gtitle,'rebiya kadeer')|| strstr($this->keyword,'rebiya kadeer'))
		  {
				$this->strlog = $this->pathfile." title Rebiya Kadeer";
				$this->res = 1;
		  }
		  if (strstr($this->title,'热比娅') || strstr($this->gtitle,'热比娅')  || strstr($this->keyword,'热比娅'))
		  {
				$this->strlog = $this->pathfile." title or gtitle 热比娅";
				$this->res = 1;
		  }

		  if (strstr($this->title,'world uighur congress') || strstr($this->gtitle,'world uighur congress') ||  strstr($this->keyword,'world uighur congress'))
		  {
				$this->strlog = $this->pathfile." title or gtitle  World Uighur Congress";
				$this->res = 1;
		  }

		  if (strstr($this->title,'世界维吾尔族大会') || strstr($this->gtitle,'世界维吾尔族大会') || strstr($this->keyword,'世界维吾尔族大会'))
		  {
				$this->strlog = $this->pathfile." title or gtitle 世界维吾尔族大会";
				$this->res = 1;
		  }
		  
		  //茉莉花革命，Jasmine Revolution
		  if (strstr($this->title,'茉莉花革命') || strstr($this->gtitle,'茉莉花革命') || strstr($this->keyword,'茉莉花革命'))
		  {
				$this->strlog = $this->pathfile." title or gtitle 茉莉花革命";
				$this->res = 1;
		  }

		   if (strstr($this->title,'jasmine revolution') || strstr($this->gtitle,'jasmine revolution') || strstr($this->keyword,'jasmine revolution'))
		  {
				$this->strlog = $this->pathfile." title or gtitle  Jasmine Revolution";
				$this->res = 1;
		  }



		   if (strstr($this->title,'刘小波') || strstr($this->gtitle,'刘小波') || strstr($this->keyword,'刘小波'))
		  {
				$this->strlog = $this->pathfile." 刘小波";
				$this->res = 1;
		  }
		   if (strstr($this->title,'刘晓波') || strstr($this->gtitle,'刘晓波') || strstr($this->keyword,'刘晓波'))
		  {
				$this->strlog = $this->pathfile." 刘晓波";
				$this->res = 1;
		  }
		   if (strstr($this->title,'liuxiaobo') || strstr($this->gtitle,'liuxiaobo') || strstr($this->keyword,'liuxiaobo'))
		  {
				$this->strlog = $this->pathfile." liuxiaobo";
				$this->res = 1;
		  }
		  if (strstr($this->title,'liu xiaobo') || strstr($this->gtitle,'liu xiaobo') || strstr($this->keyword,'liu xiaobo'))
		  {
				$this->strlog = $this->pathfile." liuxiaobo";
				$this->res = 1;
		  }

		  if (strstr($this->title,'chen guangcheng') || strstr($this->gtitle,'chen guangcheng') || strstr($this->keyword,'chen guangcheng'))
		  {
				$this->strlog = $this->pathfile." chen guangcheng";
				$this->res = 1;
		  }
		  if (strstr($this->title,'陈光诚') || strstr($this->gtitle,'陈光诚') || strstr($this->keyword,'陈光诚'))
		  {
				$this->strlog = $this->pathfile." 陈光诚";
				$this->res = 1;
		  }
		  /*
		  if (strstr($this->title,'xijinping') || strstr($this->gtitle,'xijinping') || strstr($this->keyword,'xijinping'))
		  {
				$this->strlog = $this->pathfile." xijinping";
				$this->res = 1;
		  }
		  if (strstr($this->title,'xi jinping') || strstr($this->gtitle,'xi jinping') || strstr($this->keyword,'xi jinping'))
		  {
				$this->strlog = $this->pathfile." xi jinping";
				$this->res = 1;
		  }
		  */
		  if (strstr($this->title,'1989年6月4日') || strstr($this->gtitle,'1989年6月4日') || strstr($this->keyword,'1989年6月4日'))
		  {
				$this->strlog = $this->pathfile." 1989年6月4日";
				$this->res = 1;
		  }
		  if (strstr($this->title,'“六四”事件') || strstr($this->gtitle,'“六四”事件') || strstr($this->keyword,'“六四”事件'))
		  {
				$this->strlog = $this->pathfile." “六四”事件";
				$this->res = 1;
		  }
		  if (strstr($this->title,'六四事件') || strstr($this->gtitle,'六四事件') || strstr($this->keyword,'六四事件'))
		  {
				$this->strlog = $this->pathfile." 六四事件";
				$this->res = 1;
		  }
		  if (strstr($this->title,'friends of tibet') || strstr($this->gtitle,'friends of tibet') || strstr($this->keyword,'friends of tibet'))
		  {
				$this->strlog = $this->pathfile." friends of tibet";
				$this->res = 1;
		  }
		  
		  echo "res:".$this->res."\n";
		  echo "strlog:".$this->strlog."\n";

		//如果有需要过滤的字串，就做日志
		if($this->res == 1)
		{
			 $lh = new loghouse($this->pathfile,$this->strlog,'err');
             $lh->rec();
		}
		  //专线图片特殊过滤结束
		
	  return $this->res;
		
	}
}
?>