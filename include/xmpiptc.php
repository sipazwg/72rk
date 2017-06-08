<?php
/*
	xmpiptc类,用来得到图片中所写入的adobe所用的xmp格式的iptc信息
	by zwg
	2014-08-09
	
*/
class xmpiptc
{
	 //---------------------------------------------------------------------------下面定义成员变量------------------------------------------------------------------------------

     var $category;   //类别
	 var $country;    //国家
	 var $city;       //城市

	 var $headline;       //标题
	 var $title;          //说明
	 var $keyword;        //关键词

     var $photodate;      //拍摄日期
	 var $supple;         //补充分类

	 var $ifxmp;          //是否存在xmp信息

	 var $xmpdata;        //图片信息
	 
	 var $creator;	//图片作者 
	 var $gettysort;// 
	 var $TransmissionReference;

	 //---------------------------------------------------------------------------下面定义构造函数---------------------------------------------------------------------------
     function xmpiptc($filepath)
	 {
		     //取得相应的数据块
             ob_start();
             readfile($filepath);
             $source = ob_get_contents();
             ob_end_clean();

			 $xmpdata_start = strpos($source,"<x:xmpmeta");
	         //echo "start:".$xmpdata_start."\r\n";
             $xmpdata_end = strpos($source,"</x:xmpmeta>");
	         //echo "end:".$xmpdata_end."\r\n";
             $xmplenght = $xmpdata_end-$xmpdata_start;
             $this->xmpdata = substr($source,$xmpdata_start,$xmplenght+12);

			 //检查是否存在相应的信息
             if (($xmpdata_start===false) || ($xmpdata_end===false))
		     {
				   $this->ifxmp=='noxmp';
			 }
			 else
		     {
				   $this->ifxmp=='yesxmp';
			 }
	 }

	 //---------------------------------------------------------------------------下面定义方法---------------------------------------------------------------------------
     function getpicxmp()
	 {
	         //下面从xmpdata中取得相关信息
             $this->category  = addslashes($this->getxmpfield($this->xmpdata,"<photoshop:Category>","</photoshop:Category>"));
             $this->country   = addslashes($this->getxmpfield($this->xmpdata,"<photoshop:Country>","</photoshop:Country>"));
             $this->city      = addslashes($this->getxmpfield($this->xmpdata,"<photoshop:City>","</photoshop:City>"));
	         $this->headline  = addslashes($this->getxmpfield($this->xmpdata,"<photoshop:Headline>","</photoshop:Headline>"));
	         $this->title     = addslashes($this->getxmpfieldrdfli($this->xmpdata,"<dc:description>","</dc:description>"));
	         $this->keyword   = addslashes($this->getxmpfieldrdfli($this->xmpdata,"<dc:subject>","</dc:subject>"));
	         $this->photodate = addslashes($this->getxmpfield($this->xmpdata," <photoshop:DateCreated>","</photoshop:DateCreated>"));
	         $this->supple    = addslashes($this->getxmpfieldrdfli($this->xmpdata,"<dc:rights>","</dc:rights>"));
			 $this->creator   = addslashes($this->getxmpfieldrdfli2($this->xmpdata,"<dc:creator>","</dc:creator>"));
			 $this->gettysort = addslashes($this->getxmpfieldrdfli2($this->xmpdata,"<photoshop:SupplementalCategories>","</photoshop:SupplementalCategories>"));
			 $this->TransmissionReference = addslashes($this->getxmpfieldrdfli2($this->xmpdata,"<photoshop:TransmissionReference>","</photoshop:TransmissionReference>"));
	 }

     function getxmpfield($xmpdata,$startstr,$endstr)
     {
	         $xmpdata_start1 = strpos($xmpdata,$startstr);
             $xmpdata_end1 = strpos($xmpdata,$endstr);
             $xmplenght1 = $xmpdata_end1-$xmpdata_start1;
	         $endstrlen=strlen($endstr);
	         $xmpdata1 = substr($xmpdata,$xmpdata_start1,$xmplenght1+$endstrlen);
    
             $xmpdata2=str_replace($startstr,"",$xmpdata1);
             $xmpdata2=str_replace($endstr,"",$xmpdata2);

             return $xmpdata2;
      }

     function getxmpfieldrdfli($xmpdata,$startstr,$endstr)
     {
	         $xmpdata_start1 = strpos($xmpdata,$startstr);
	         $xmpdata_end1 = strpos($xmpdata,$endstr);
             $xmplenght1 = $xmpdata_end1-$xmpdata_start1;
             $xmpdata1 = substr($xmpdata,$xmpdata_start1,$xmplenght1+17);
			 
             $xmpdata2=str_replace($startstr,"",$xmpdata1);
             $xmpdata2=str_replace($endstr,"",$xmpdata2);

			 
             $xmpdata_start3 = strpos($xmpdata2,"<rdf:li xml:lang='x-default'>");
			  
	         if ($xmpdata_start3=='')
	         {
		          $xmpdata_start3 = strpos($xmpdata2,"<rdf:li>");
	         }
			
             $xmpdata_end3 = strpos($xmpdata2,"</rdf:li>");
			 $xmplenght3 = $xmpdata_end3-$xmpdata_start3;
				
			 if(($xmpdata_start3 == 0 || $xmpdata_start3=='') && ($xmplenght3 == 0 || $xmplenght3 == ''))
		     {
				 $xmpdata3 = $xmpdata2;
			 }
			 else
		     {
				$xmpdata3 = substr($xmpdata2,$xmpdata_start3,$xmplenght3+12);
		     }	
             $xmpdata3=str_replace("<rdf:li xml:lang='x-default'>","",$xmpdata3);
	         $xmpdata3=str_replace("<rdf:li>","",$xmpdata3);
             $xmpdata3=str_replace("</rdf:li>","",$xmpdata3);

             return $xmpdata3;
     }
	 
	 //ADD BY RYQ
	function getxmpfieldrdfli2($tmp,$startflag,$endflag)
	{
		 $start1 = strpos($tmp,$startflag);
		 if($start1 === false)
		 {
			return "";
		 }
		 $end1 = strpos($tmp,$endflag);
		 if($end1 === false || $end1 <= $start1)
		 {
			return "";
		 }
		 $newstr = substr($tmp,$start1,$end1-$start1);
		 $start1 = strpos($newstr,"<rdf:li");
		 if($start1 === false)
		 {
			return "";
		 }
		 $start1 = strpos($newstr,">",$start1);
		 if($start1 === false)
		 {
			return "";
		 }
		 $end1 = strpos($newstr,"</rdf:li>");
		 if($end1 === false || $end1 <= $start1)
		 {
			return "";
		 }
		 
		 $rtn = substr($newstr,$start1 + 1,$end1 - $start1 - 1);
		 
		 return $rtn;
	}

}  //end for class xmpiptc


/*
$arrfile = array("/usr/www/tools/sipaphotos915660.JPG","/usr/www/tools/sipaphotos915621.JPG","/usr/www/tools/krtphotoslive285918.JPG");
foreach($arrfile as $vkey=>$val)
{
	   $iptc=new xmpiptc($val);
       if ($iptc->ifxmp=='noxmp')
	   {
		     echo "noxmp";
	   }
	   else
	   {
		     $iptc->getpicxmp();
			 echo $iptc->category."\r\n";
			 echo $iptc->headline."\r\n";
			 echo $iptc->title."\r\n";
	   }
}
*/
?>