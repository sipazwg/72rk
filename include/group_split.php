<?php
/*
    对图片的组进行分拆，超过100张，另外设置一个组
    * by hd
    * 2011.01.29
*/
class group_split
{
	  var $link;
	  var $ifnew;
	  var $g_max;
	  //分组
	  function group_split($link)
	  {
		    $this->link = $link;
		    $this->ifnew = -1;
		    $this->g_max = 100;
	  }
	  
	  //根据传入的组标识，得到最新的组id
	  function get_group_id($source_name,$group_obj)
	  {
            $gid = $this->get_last_gid_by_gobj($source_name,$group_obj);
            if ($gid == 0)
            {
				  //得到gid
				  $n_i = new new_info($this->link);
				  $gid = $n_i->new_groupid($group_obj);
				  //记录此gid
				  //$this->ins_file($log,$gobj,$gid);
				  $this->write_source_gobj_gid_log($source_name,$group_obj,$gid);
				  $this->ifnew = 1;
				  
				  $md5_str = md5($group_obj);
				  $this->rec_groupid_2_md5($gid,$source_name,$md5_str);
				  
				  return $gid;
			}
			else
			{
				  //$this->ifnew = 0;
				  //return $gid;
				  //if this gid is 
				  $g_sum = $this->get_one_gid_outid_sum($source_name,$gid);
				  if ($g_sum < $this->g_max)
				  {
					    $this->ifnew = 0;
					    return $gid;
				  }
				  else
				  {
				       $n_i = new new_info($this->link);
				       $gid = $n_i->new_groupid($group_obj);

 				       $this->write_source_gobj_gid_log($source_name,$group_obj,$gid);
				       $this->ifnew = 1;
				       
				       $md5_str = md5($group_obj);
				       $this->rec_groupid_2_md5($gid,$source_name,$md5_str);
				       
				       return $gid;
				  }
			}
	  }
	  
	  function rec_groupid_2_md5($groupid,$source_name,$md5_str)
	  {
		    $log_file = $this->get_groupid_2_md5_log_file($source_name,$groupid);
            $content = $md5_str."\n";
            error_log($content,3,$log_file);
	  }
	  
	  function get_first_outid_by_groupid($source_name,$groupid)
	  {
		    $log_file = $this->get_gid_outid_log($source_name,$gid);
		    $arr_file = file($log_file);
		    $arr_file2 = array();
		    foreach ($arr_file as $kf => $vf)
		    {
				  $vf2 = trim($vf);
				  if ($vf2 == '')
				  {
					    continue;
				  }
				  $arr_file2[]=$vf2;
			}
			if (sizeof($arr_file2) > 0)
			{
				  $first_outid = $arr_file2[0];
				  return $first_outid;
			}
			else
			{
				  return 0;
			}
	  }
	  
	  function get_first_gid_by_md5($source_name,$md5_str)
	  {
		    $log_file = $this->get_md5_log_file($source_name,$md5_str);
		    $arr_file = file($log_file);
		    $arr_file2 = array();
		    foreach ($arr_file as $kf => $vf)
		    {
				  $vf2 = trim($vf);
				  if ($vf2 == '')
				  {
					    continue;
				  }
				  $arr_file2[]=$vf2;
			}
			if (sizeof($arr_file2) > 0)
			{
				  $first_gid = $arr_file2[0];
				  return $first_gid;
			}
			else
			{
				  return 0;
			}
	  }
	  
	  function get_md5_by_groupid($source_name,$groupid)
	  {
		    $log_file = $this->get_groupid_2_md5_log_file($source_name,$groupid);
		    $arr_file = file($log_file);
		    $arr_file2 = array();
		    foreach ($arr_file as $kf => $vf)
		    {
				  $vf2 = trim($vf);
				  if ($vf2 == '')
				  {
					    continue;
				  }
				  $arr_file2[]=$vf2;
			}
			if (sizeof($arr_file2) > 0)
			{
				  $md5str = $arr_file2[0];
				  return $md5str;
			}
			else
			{
				  return 0;
			}
      }
	  
	  
	  function get_md5_log_file($source_name,$md5_str)
	  {
		    $cur_date = date("Y_m_d");
		    $dir = "/data/www/loghouse/".$source_name."/".$cur_date;
		    if (!is_dir($dir))
		    {
				//mkdir($dir,0777);
				system("mkdir -p ".$dir." -m 777");
			}
			$g_log = $dir."/".$md5_str.".txt";
			if (!is_file($g_log))
			{
				  touch($g_log);
			}
			return $g_log;
	  }
	  
	  
	  function get_groupid_2_md5_log_file($source_name,$groupid)
	  {
		    $cur_date = date("Y_m_d");
		    $dir = "/data/www/loghouse/".$source_name."/".$cur_date;
		    if (!is_dir($dir))
		    {
				//  mkdir($dir,0777);
				system("mkdir -p ".$dir." -m 777");
			}
			$g_log = $dir."/".$groupid."_md5.txt";
			if (!is_file($g_log))
			{
				  touch($g_log);
			}
			return $g_log;
	  }
	  
	  function get_last_gid_by_gobj($source_name,$group_obj)
	  {
		    $gobjfile = $this->get_source_gobj_gid_log($source_name,$group_obj);
		    $arr_gid = file($gobjfile);
		    $arr_gid2 = array_reverse($arr_gid);
            $arr_final_g = array();
            foreach ($arr_gid2 as $kg => $vg)
            {
				  $vg2 = trim($vg);
				  if (is_numeric($vg2) && ($vg2>0))
				  {
					  $arr_final_g[] = $vg2;
				  }
			}
			if (sizeof($arr_final_g)>0)
			{
				  return $arr_final_g[0];
			}
			else
			{
				  return 0;
			}
	  }
	  
	  function get_first_gid_by_gobj($source_name,$group_obj)
	  {
		    $gobjfile = $this->get_source_gobj_gid_log($source_name,$group_obj);
		    $arr_gid = file($gobjfile);
		    //$arr_gid2 = array_reverse($arr_gid);
            $arr_final_g = array();
            foreach ($arr_gid as $kg => $vg)
            {
				  $vg2 = trim($vg);
				  if (is_numeric($vg2) && ($vg2>0))
				  {
					  $arr_final_g[] = $vg2;
				  }
			}
			if (sizeof($arr_final_g)>0)
			{
				  return $arr_final_g[0];
			}
			else
			{
				  return 0;
			}
	  }
	  
	  //function get_
	  
	  function write_source_gobj_gid_log($source_name,$group_obj,$gid)
	  {
            $gobjfile = $this->get_source_gobj_gid_log($source_name,$group_obj);
            $content = $gid."\n";
            error_log($content,3,$gobjfile);
	  }
	  
	  function get_source_gobj_gid_log($source_name,$group_obj)
	  {
		    $cur_date = date("Y_m_d");
		    $dir = "/data/www/loghouse/".$source_name."/".$cur_date;
		    if (!is_dir($dir))
		    {
				// mkdir($dir,0777);
				system("mkdir -p ".$dir." -m 777");
			}
			$md5=md5($group_obj);
			$g_log = $dir."/".$md5.".txt";
			if (!is_file($g_log))
			{
				  touch($g_log);
			}
			return $g_log;
	  }
	  
	  function get_gid_outid_log($source_name,$gid)
	  {
		    $cur_date = date("Y_m_d");
		    $dir = "/data/www/loghouse/".$source_name."/".$cur_date;
		    if (!is_dir($dir))
		    {
				 // mkdir($dir,0777);
				 system("mkdir -p ".$dir." -m 777");
			}
			//$md5=md5($group_obj);
			$g_log = $dir."/".$gid.".txt";
			if (!is_file($g_log))
			{
				  touch($g_log);
			}
			return $g_log;
	  }
	  
	  function write_gid_outid_log($source_name,$gid,$outid)
	  {
		   $gid_log = $this->get_gid_outid_log($source_name,$gid);
		   $content = $outid."\n";
		   error_log($content,3,$gid_log);
	  }
	  
	  function get_one_gid_outid_sum($source_name,$gid)
	  {
		    $gid_log = $this->get_gid_outid_log($source_name,$gid);
		    $arr_gid = file($gid_log);
		    return sizeof($arr_gid);
	  }
	
	  function get_all_gid_by_gobj($source_name,$group_obj)
	  {
		    $gobjfile = $this->get_source_gobj_gid_log($source_name,$group_obj);
		    $arr_gid = file($gobjfile);
		    //$arr_gid2 = array_reverse($arr_gid);
            $arr_final_g = array();
            foreach ($arr_gid as $kg => $vg)
            {
				  $vg2 = trim($vg);
				  if (is_numeric($vg2) && ($vg2>0))
				  {
					  $arr_final_g[] = $vg2;
				  }
			}
			return $arr_final_g;
	  }
	  	  
}
?>





















