<!DOCTYPE html>
<html>
<head>
  <title>Установка</title>
  <meta charset="utf-8">
</head>
<body>
<form action="" method="POST">
  <table>
    <tr><td>Имя пользователя:</td><td><input name="db_user" value="root"></td></tr>
    <tr><td>Пароль:</td><td><input name="db_password" type="password" value=""></td></tr>
    <tr><td>Хост:</td><td><input name="host" value="localhost"></td></tr>
    <tr><td>Имя базы данных:</td><td><input name="db_name" value=""></td></tr>
    <tr><td></td><td><input type="submit" name="db" value="Установить"></td></tr>
  </table>
</form>
</body>
</html>
<?php
if(!isset($_POST['db'])) exit();

$a = array(
"\$db_user = '';",
"\$db_password = '';",
"\$db_host = '';",
"\$dbname = '';"
);
$b = array(
"\$db_user = '{$_POST['db_user']}';",
"\$db_password = '{$_POST['db_password']}';",
"\$db_host = '{$_POST['host']}';",
"\$dbname = '{$_POST['db_name']}';"
);

$config_path = str_replace('\\', '/', dirname(__DIR__)).'/config.php';
$config = file_get_contents($config_path);
$config = str_replace($a, $b, $config);
file_put_contents($config_path, $config);

include $config_path;
include CLASS_PATH.'db.php';
$tables = array(
'CREATE TABLE IF NOT EXISTS game (
  s_id int unsigned NOT NULL,
  x tinytext NOT NULL,
  o tinytext NOT NULL,
  field text,
  difficulty tinyint DEFAULT NULL,
  PRIMARY KEY (s_id)
) DEFAULT CHARSET=utf8',

'CREATE TABLE IF NOT EXISTS session (
  s_id int unsigned NOT NULL AUTO_INCREMENT,
  player_1 char(40) DEFAULT NULL,
  player_2 char(40) DEFAULT NULL,
  status tinytext NOT NULL,
  time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  type tinytext,
  PRIMARY KEY (s_id)
) DEFAULT CHARSET=utf8'
  );

$db = new DB();

try{
  foreach ($tables as $table) {
    $db->query($table);
  }
  $sth = $db->query('show tables');
  foreach ($sth as $table) {
    if ($table[0] == 'session' || $table[0] == 'game') {
      echo str_pad($table[0], 30, '.'), 'OK<br>';
    }
  }
  echo 'Delete this folder after install.';
} catch(PDOException $e){
  echo $e->getMessage(), ': ', $e->getCode();
}
?>
