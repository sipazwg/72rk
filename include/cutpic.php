<?php
/*
	裁边
    by zwg
	2014.07.25
*/
class cutpic
{
	//成员变量
	var $imgpath;           //图片所在路径
	var $course;            //需要裁边的方位（up，down,left,right)

	//构造函数，进行初始化
	function cutpic()
	{
		
	}
	  
    function cutcmd($width,$height,$startwid,$starthei)
	{
		$cmdcrop = SIPA_IMAGEMAGICKDIR."convert ".$this->imgpath." -crop ".$width."x".$height."+".$startwid."+".$starthei." ".$this->imgpath;
        echo "cmdcrop:".$cmdcrop."\n";
        shell_exec($cmdcrop);
	}
	  

    //裁边，$course为方位
    function cutlogo($imgpath,$course)
    {
		$this->imgpath=$imgpath;
		$this->course=$course;
		echo $this->imgpath."\n";
		//需要先做一次convert
	    $cmd = SIPA_IMAGEMAGICKDIR."convert ".$this->imgpath." ".$this->imgpath;
		echo $cmd."\n";
	    shell_exec($cmd);
        //开始得到信息
			  echo "imgpath:".$this->imgpath."\n";
        $im = imagecreatefromjpeg($this->imgpath);
		print_r($im);
        $size=getimagesize($this->imgpath);
		print_r($size);
		

		$colorDefult = '';
		$colorIndex  = '';
		  
		//如果是要切掉上边
		if("up" == $this->course)
		{
			$x = $size[0]-1;
			for($y=0;$y<$size[1];$y++)
			{
				$colorIndex = imagecolorat($im, $x, $y);
				if($colorDefult=="")
					$colorDefult = $colorIndex;
				if($colorIndex!=$colorDefult)
					break;
			}
			$bannerHeight = $y;

			//剩下高度大于总高度的3/4,并且，剩下高度要大于10
			if ((($size[1]-$bannerHeight) >= (3*$size[1]/4)) && ($size[1]-$bannerHeight) >10)
			{
				$newheight=$size[1]-$bannerHeight;
				$this->cutcmd($size[0],$newheight,0,$bannerHeight);
					 
			}
		}
		//如果要切下面的边儿
		else if("down" == $this->course)
		{
			$x = ceil($size[0]/2);
			for($y=$size[1]-10;$y>0;$y--)
			{
				$colorIndex = imagecolorat($im, $x, $y);
				if($colorDefult=="")
					$colorDefult = $colorIndex;
				if($colorIndex!=$colorDefult)
					break;
			}
			$bannerHeight = $y-10;
			//切掉的边要小于1/4，且剩下的高度要大于10
			if ((($size[1]-$bannerHeight) < ($size[1]/4)) && ($bannerHeight > 10))
			{
				$this->cutcmd($size[0],$bannerHeight,0,0);
			}
		}
		//如果要切左面的边儿
		else if("left" == $this->course)
		{
			$y = $size[1]-1;
			for($x=0;$x<$size[0];$x++)
			{
				$colorIndex = imagecolorat($im, $x, $y);
				if($colorDefult=="")
					$colorDefult = $colorIndex;
				if($colorIndex!=$colorDefult)
					break;						  
			}

			$bannerWidth = $x;
			//剩下部分要大于3/4，且要大于10
			if ((($size[0]-$bannerWidth) >= (3*$size[0]/4)) && (($size[0]-$bannerWidth) > 10))
			{
				$newwidth=$size[0]-$bannerWidth;
				$this->cutcmd($newwidth,$size[1],$bannerWidth,0);
			}
		}
		//如果要切右面的边儿
		else if("right" == $this->course)
		{
			$y = ceil($size[1]/2);
			for($x=$size[0]-10;$x>0;$x--)
			{
				
				$colorIndex = imagecolorat($im, $x, $y);
				if($colorDefult=="")
					 $colorDefult = $colorIndex;
				if($colorIndex!=$colorDefult)
					break;						  
			}
			
			$bannerWidth = $x-10;
			//切掉的部分要小于1/4,且剩下部分要大于10
			if ((($size[0]-$bannerWidth) <= ($size[0]/4)) && ($bannerWidth > 10))
			{
				$this->cutcmd($bannerWidth,$size[1],0,0);
			}
		}

	

	}       //end for function cutlogo()



}      //end for class cutpic

?>