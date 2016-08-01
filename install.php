<?php
try {
  $dbh = new PDO('sqlite:/var/www/kallup.net/users/jkallup/data/hd_data.db');
  $res = $dbh->query("CREATE TABLE IF NOT EXISTS hd_users ("
  . "id INTEGER PRIMARY KEY AUTOINCREMENT,"
  . "user VARCHAR(30),"
  . "pass VARCHAR(90),"
  . "udom VARCHAR(256),"
  . "demo VARCHAR(10),"
  . "mail VARCHAR(60),"
  . "last VARCHAR(10),"
  . "flag CHAR(1))"  );

  $res = $dbh->query("CREATE TABLE IF NOT EXISTS hd_domains ("
  . "id INTEGER,"
  . "udom VARCHAR(256),"
  . "demo VARCHAR(10),"
  . "flag VARCHAR(1)"
  . ");");

/*
  $res = $dbh->query("CREATE TABLE IF NOT EXISTS hd_update ("
  . "id INTEGER,"
  . "pubID VARCHAR(20),"
  . ");");
*/
} catch (PDOException $e) {
  echo "error: " . $e;
  die(1);
}
echo "installation seems OK.";
?>

