<?php 
//ÿ��12��Сʱ�����һ�α�
error_reporting(E_ALL ^ E_NOTICE);
require_once("/data/www/newsave/remind/coon.php");

$del = "DELETE FROM sipa_remind  ";

$rs       = mysql_query($del,$con);
