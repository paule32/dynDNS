<?php
if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $client_ip = $_SERVER['REMOTE_ADDR']; } else {
  $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (isset($_POST['user']) && isset($_POST['pass']))
{
  $user = $_POST['user'];
  $pass = md5($_POST['pass']);

echo $user . " : " . $pass;


  try {
    $dbh = new PDO('sqlite:/var/www/kallup.net/users/jkallup/data/hd_data.db');
    $res = $dbh->prepare("SELECT user,pass FROM hd_users WHERE user=:user AND pass=:pass;");
    $res->execute([$user,$pass]);
    $arr = $res->fetchAll();

    if(count($arr) < 1) {
      echo "Daten passen nicht zusammen!";
      header("Refresh: 10; URL='http://kallup.net/dns/update.php'");
    }
    else {
	$data =
	"\$ORIGIN        dns-ip.org.\n".
	"@   IN  NS  ns.dns-ip.org.\n".
	"\n".
	$user . "  IN A " . $client_ip . "\n";
	$update = array( "$user" => "/var/www/dns-ip.org/users/" . $user . "/dns-ip.data");
	$h = fopen($update["$user"],"w");
	if ($h == null) {
	  echo "Update: fail.";
	  die(1);
	} else {
	  fprintf($h,"%s",$data);
	  fclose ($h);
	}
        echo "Update: ok.";
	die(0);
    }
  } catch (PDOException $e) { echo "Error: " . $e; die(1); }
}
else {
echo '<form action="update.php" method="post">Bitte Buddy eingeben:<br>'.
'<input type="text" name="user" value=""><br>Passwort:<br>'.
'<input type="password" name="pass" maxlength="100"><br>'.
'<input type="submit" name="action" value="IP-Update">'.
'</form>';
}
?>
