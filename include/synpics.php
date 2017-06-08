<?
/*
	@功能：根据单张图片来进行同步
	@2015.09.01
	@by zwg 

*/

class SynPic
{
	function SynPic($link)
	{
		$this->link = $link;
	}
	
	function syn($groupid,$photoid) 
	{
		$groupid	= trim($groupid);
		$photoid	= trim($photoid);
		if($groupid <=0 || $photoid <=0)
		{
			echo "参数错误，请检查!";
			exit();
		}
		
		//组照内容有更新，审核时间也相应变化
		$nowtime = date("Y-m-d H:i:s");
		$sql_up = "update sipa_photo set moddate='".$nowtime."'  where groupid='".$groupid."'";
		mysql_query($sql_up,$this->link);


		//处理组图首张数量
		$sql_pindex_num = "select count(*) as pnum from sipa_photo where groupid='".$groupid."' and pindex=1";
		$res_num = mysql_query($sql_pindex_num,$this->link);
		$row_num = mysql_fetch_assoc($res_num);
		$pnum = $row_num['pnum'];
		
		//如果组图首张数量不是1，则将组图内最小图片ID的图片设为首张
		if($pnum  != 1 )
		{
			//先将所有图片均设置为非首张，此步是为了万一首张数量多的情况
			$sql_up_pindex = "update sipa_photo set pindex=0 where groupid='".$groupid."'";
			mysql_query($sql_up_index,$this->link);

			//设定组内第一张进来的图片为首张
			$sql_up_pindex = "update sipa_photo set pindex=1 where groupid='".$groupid."' order by photoid asc limit 0,1";
			mysql_query($sql_up_index,$this->link);

			//得到当前组的详细信息
			$gquery		= @mysql_query("select * from sipa_photo where groupid='".$groupid."' and pindex=1 ",$this->link);
			$grow		= @mysql_fetch_assoc($gquery);

			$this->updatepiccate($groupid,$grow['catestr'],$this->link);
		}


		//得到当前组的详细信息
		$gquery		= @mysql_query("select * from sipa_photo where groupid='".$groupid."' and pindex=1 ",$this->link);
		$grow		= @mysql_fetch_assoc($gquery);

		//只有图片是首张的时候，才会更新临时表
		if($grow['photoid'] == $photoid)
		{
			//写入临时表
			$this->updatepiccate($groupid,$grow['catestr'],$this->link);
			//更新专题组照首张
			$sql_up	= "update sipa_stg  set photoid='".$photoid."' where groupid='".$groupid."'";
			mysql_query($sql_up,$this->link);
		}

		//更新专题内容中，组照张数
		$sql_up	= "update sipa_stg  set gnum = (select count(*) from sipa_photo where groupid=".$groupid." and ifmod=1) where groupid='".$groupid."'";
		mysql_query($sql_up,$this->link);

		
		
	}//function syn end


	function updatepiccate($groupid,$catestr,$link)
	{
		//从临时表中清理数据
		$sql_del = "delete from sipa_pic_cate where groupid='".$groupid."'";
		mysql_query($sql_del,$this->link);

		//写入临时表
		$catestr = $grow['catestr'];
		//根据分类字串，得到此组图片所有分类ID
		$cidlist = $this->getcateidlist($catestr,$this->link);

		foreach($cidlist as $k=>$cid)
		{
			$sql_ins = "insert into sipa_pic_cate(moddate,photoid,cid,groupid,ifmod,zoneid,ifstock) values('".$grow['moddate']."','".$grow['photoid']."','".$cid."','".$groupid."','".$grow['ifmod']."','".$grow['zoneid']."','".$grow['ifstock']."')";
			mysql_query($sql_ins,$this->link);
		}
	}


	//取得图片分类ID列表
	function getcateidlist($catestr,$link)
	{
		$arr_res = array();
		$catestr = trim($catestr);
		if($catestr == '')
		{
			return $arr_res;
		}
		$list = explode(";",$catestr);
		$arr_list = array_unique($list);
		
		foreach($arr_list as $k=>$path)
		{
			$path = trim($path);
			if($path == '')
			{
				continue;
			}
			$arrdlist = explode(",",$path);
			foreach($arrdlist as $d=>$f)
			{
				if($f == "SALL")
				{
					continue;
				}
				$sql_s = "select cid from sipa_cate where fstr='".$f."'";
				$res_s = mysql_query($sql_s,$link);
				if(mysql_num_rows($res_s)<=0)
				{
					continue;
				}
				$row_s = mysql_fetch_assoc($res_s);
				$arr_res[]=$row_s['cid'];
			}
		}
		return $arr_res;
	}


}//class synpic end

?>
