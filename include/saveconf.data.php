<?php

define ("SIPA_DATABASE_HOST", "106.3.36.74" );
define ("SIPA_DATABASE_NAME", "sipapic" );
define ("SIPA_DATABASE_USERNAME", "sipaphoto" );
define ("SIPA_DATABASE_PASSWORD", "123456" );


//取得图片所在存储
function getbasepath($photoid)
{
	$basepath = '';
	if($photoid >1 && $photoid<6499999)
	{
		$basepath = "/base/basen1";
	}
	elseif($photoid >=6499999 && $photoid<11926000)
	{
		$basepath = "/base/basen2";
	}
	else
	{
		$basepath = "/base/basen3";
	}
	return $basepath;
}

//取得大图存储路径
function getbigpath($photoid)
{
	$path1 = $photoid - $photoid%100000;
	$path2 = $photoid - $photoid%1000;
	return getbasepath($photoid)."/sipa_big_picture/".$path1."/".$path2;
}

//取得小图存储路径
function getsmallpath($photoid)
{
	$path1 = $photoid - $photoid%100000;
	$path2 = $photoid - $photoid%1000;
	return getbasepath($photoid)."/small/".$path1."/".$path2;
}
?>