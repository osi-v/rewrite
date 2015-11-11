<?
$db_host='localhost';
$db_database='rewritesite';
$db_username='root';
$db_password='';
$connection = mysql_connect($db_host, $db_username, $db_password);
$db_select = mysql_select_db($db_database);
mysql_set_charset('utf8');
?>