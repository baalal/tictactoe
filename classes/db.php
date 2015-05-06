<?php
/**
* 
*/
class DB extends PDO
{
	private $user;
	private $password;
	private $host;
	private $dbname;

	function __construct()
	{
		if(!defined('APP_PATH')) die();

		include APP_PATH.'config.php';

		$this->user = $db_user;
		$this->password = $db_password;
		$this->host = $db_host;
		$this->dbname = $dbname;

		parent::__construct("mysql:dbname={$this->dbname};host={$this->host}", $this->user, $this->password);
	}
}
?>