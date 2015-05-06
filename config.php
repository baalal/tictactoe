<?php
if(!defined('APP_PATH')) define('APP_PATH', str_replace('\\', '/', __DIR__).'/');
if(!defined('APP_URL')){
	$file = str_replace('\\', '/', __FILE__);
	$filename = end(explode('/', $file));
	$app_url = str_replace(array($_SERVER['DOCUMENT_ROOT'], $filename), array('',''), $file);
	define('APP_URL', $app_url);
}
if(!defined('CLASS_PATH')) define('CLASS_PATH', APP_PATH.'classes/');

$db_user = '';
$db_password = '';
$db_host = '';
$dbname = '';
?>