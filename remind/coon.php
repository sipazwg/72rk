<?php 
//配置文件
require_once("/data/www/newsave/remind/alldir.class.php");
require_once("/data/www/newsave/include/saveconf.data.php");
$con = mysql_connect(SIPA_DATABASE_HOST,SIPA_DATABASE_USERNAME,SIPA_DATABASE_PASSWORD);
mysql_select_db(SIPA_DATABASE_NAME,$con);
mysql_query('set names utf8');