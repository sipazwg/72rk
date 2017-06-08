<?php
/*
	利用identify得到图片说明信息
    by zwg
	2014.08.09
*/

//注意,此类identify执行时费时较多.
class ideniptc
{
    var $pathfile;

	//成员变量
	var $category;
	var $scategory;
	var $headline;
	var $country;
	var $city;
	var $keyword;
	var $createdate;
	var $caption;
	
	//add by ryq
	var $gcontent;
	var $picinfo;
	var $title;
	var $photoerid;
	var $groupobj;
	var $byline;
	var $ImageName;
	var $src;
	//add end

    //构造函数
	function ideniptc($pathfile)
	{
		  $this->pathfile=$pathfile;
	}
	
    function getinfo($picstr,$info)
    {
	      $str = strstr($picstr,$info);
	      $tok = strtok($str,"\n");
		  $strinfo = "";
	      while ($tok !== false) 
	      {
		        $str1 = strstr($tok,$info);
		        if($str1)
		        {
			         $pos=strpos($str1,":");
			         $strinfo.=substr($str1,$pos+1);
		        }
		        $tok = strtok("\n");
	      }
	      return substr($strinfo,1);
    }	
	
    function getOneinfo($picstr,$info)
    {
	      $str = strstr($picstr,$info);
	      $tok = strtok($str,"\n");
		  $strinfo = "";
	      if ($tok !== false) 
	      {
		        $str1 = strstr($tok,$info);
		        if($str1)
		        {
			         $pos=strpos($str1,":");
			         $strinfo.=substr($str1,$pos+1);
		        }
		        $tok = strtok("\n");
	      }
	      return substr($strinfo,1);
    }
	
	function getpicinfoAll()
	{	
		$this->picinfo = shell_exec(SIPA_IMAGEMAGICKDIR."identify -verbose '".addslashes($this->pathfile)."'");	
		//--------------------------------------------------------
		$this->gcontent= $this->getinfo($this->picinfo,"Special Instructions");  //40
		$this->city    = $this->getinfo($this->picinfo,"City");
		$this->country = $this->getinfo($this->picinfo,"Country");
		$this->title   = $this->getOneinfo($this->picinfo,"Caption");
		if(trim($this->title == ''))
		{
			$this->title   = $this->getOneinfo($this->picinfo,"Image Description");
			
		}
		$this->createdate= $this->getinfo($this->picinfo,"Created Date");
		$this->keyword   = $this->getinfo($this->picinfo,"Keyword");
		$this->headline  = $this->getinfo($this->picinfo,"Headline");
		$this->photoerid = $this->getOneinfo($this->picinfo,"Created Time");
		$this->groupobj = $this->getinfo($this->picinfo,"Original Transmission Reference");
		$this->byline = $this->getinfo($this->picinfo,"Byline");
		$this->ImageName = $this->getinfo($this->picinfo,"Image Name");
		$this->category = $this->getOneinfo($this->picinfo,"Category");
		$this->scategory = $this->getOneinfo($this->picinfo,"Supplemental Category");
		$this->src = $this->getinfo($this->picinfo,"Src");
		//======================================		
	}

    function getpicinfo()
    {
	       $picarray=array();
	       $picinfo = shell_exec(SIPA_IMAGEMAGICKDIR."identify -verbose '".$this->pathfile."'");

	       $category=$this->getinfo($picinfo,"Category");//得到category
	       $headline=$this->getinfo($picinfo,"Headline");//得到headline
	       $city=$this->getinfo($picinfo,"City");//得到city
	       $country=$this->getinfo($picinfo,"Country");//得到country
	       $keyword=$this->getinfo($picinfo,"Keyword");//得到keyword
	       $scategory=$this->getinfo($picinfo,"Supplemental Category");//得到Supplemental Category
	       $createdate=$this->getinfo($picinfo," Created Date");//得到createdate
	       $caption=$this->getinfo($picinfo,"Caption");//得到caption
	       $position=strpos($category,$scategory);

	       if($position)
	       {
		          $category=substr($category,0,$position);
	       }

           $this->category   = $category;//
		   $this->scategory  = $scategory;//
		   $this->headline   = $headline;//
		   $this->country    = $country;//
		   $this->keyword    = $keyword;//
		   $this->createdate = $createdate;//
		   $this->caption    = $caption;//

		   //add by zwg
		   $this->city		 = $city;//
    }

}
?>