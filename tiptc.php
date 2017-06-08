<?php
include("/data/www/sipasave/include/IPTC.php");


$pathfile_arr[0] = "/data/www/ftpdir/Prvia_Da_Liga_Nacional_De_Futebol_Americano_Fluminense_Guerreiros_X_Nova_Friburgo_Tetis-404350.jpg";

foreach ( $pathfile_arr as $pathfile )
{
	// 显示转换前的IPTC
	$info=new Image_IPTC($pathfile);
	if ($info->isvalid())
	{
		print_r($info);
		echo "\n\n\n\n\n";
	}else{
		echo "11111";
	}
	
}
?>