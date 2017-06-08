<?php
/*
	解析xml文件，并取得相关标签
*/

class parsexml
{
	var $xmlfile; //XML文件的路径
	var $title; //图片说明
	var $gcontent;//组照说明
	var $categorycode;//分类的ID
	var $penname;//图片作者
	var $city;//城市
	var $country;//国家

	//构造函数，接收xml文件路径，初始化相应变量
	function parsexml($xmlfile)
	{
		 //xml文件的路径
		 $this->xmlfile = $xmlfile;

		 //所有变量初始化
		 $this->title='';
		 $this->gcontent='';
		 $this->categorycode='';
		 $this->penname='';
		 $this->city='';
		 $this->country='';
		 $this->source = '';
		 //执行解析函数
		 $this->get_tag_value();

	}
	//解析xml文件的函数
	function get_tag_value()
	{
		//三大分类ID数组
		$arr_cateid = array("S","ENT","F","SOC","WSOC");
		
		$data=implode("",file($this->xmlfile)); 
		//建立一个 XML 解析器
		$xml=xml_parser_create();
		//将 XML 数据解析到数组中
		xml_parse_into_struct($xml,$data,$vals,$index);
		//释放一个 XML 解析器
		xml_parser_free($xml);
		//对得到的数组进行分析
		//arr_fir数组当中存放三大分类的ID，arr_sec当中存放其它分类的ID
		$arr_fir = array();
		$arr_sec = array();
	//	print_r($vals);
	
		foreach ($vals as $k=>$v)
		{
			//得到组说明
			if($v["tag"]=="TITLE")
			{
				$this->gcontent=$v["value"];
			}
			//得到分说明
			elseif($v["tag"]=="P")
			{
				$this->title = $v["value"];
			}
			//得到分类的ID
			elseif($v["tag"] == "APCM:SUBJECTCLASSIFICATION")
			{
				$cateid = $v["attributes"]["ID"];
				/*
				//如果已经有APCM:SUBJECTCLASSIFICATION标签的内容时，
				if(is_array($arr_qc["APCM:SUBJECTCLASSIFICATION"]))
				{
					//如果分类ID为"ent",代表是娱乐
					if($v["attributes"]["ID"] == "ENT")
					{
						$this->categorycode = $v["attributes"]["ID"];
					}
					
				}
				else
				{
					//记录首个分类的ID
					$arr_qc["APCM:SUBJECTCLASSIFICATION"][]=$v["attributes"]["ID"];
					$this->categorycode = $v["attributes"]["ID"];
				}
				*/
				//为最后取得分类ID做准备，如果属于三大分类，记在arr_fir数组当中
				if(in_array($cateid,$arr_cateid))
				{
					$arr_fir[]=$cateid;
				}
				//否则记入arr_sec
				else
				{
					$arr_sec[]=$cateid;
				}
				
			}
			//得到作者信息
			elseif($v["tag"] == "APCM:BYLINE")
			{
				if($v["attributes"]["TITLE"] == "Caption Writer")
				{
					$this->penname = $v["value"];
				}
			}
			//得到国家与城市的名称
			elseif($v["tag"]=="APCM:DATELINELOCATION")
			{
				$this->city = $v["attributes"]["CITY"];
				$this->country = $v["attributes"]["COUNTRYNAME"];
			}
			elseif($v["tag"]=="APCM:FRIENDLYKEY")
			{
				$this->provider = $v["value"];
			}
			elseif($v["tag"] == "APCM:SOURCE")
			{
				$this->source = $v["value"];
			}
		}

				//如果图片属于三大分类，就取其中一个
		if(count($arr_fir) >0)
		{
			$this->categorycode = $arr_fir[0];
		}
		//否则取第一个
		else
		{
			$this->categorycode = $arr_sec[0];
		}
		
		
	}
	

}
?>