<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
<title>dynDNS 1.0 - dynamic DNS 1.0</title></head><body><?php
echo "Bitte warten, Sie werden weitergeleitet...<p>";

echo "Ihre SubDomain: " . htmlspecialchars($_POST['subdomain_name']) . htmlspecialchars($_POST['domain_list']);
echo "<br>";
echo "Ihre E-Mail: "  . $_POST['email'] . "<br>";
echo "Ihr Buddy: "    . $_POST['user']  . "<br>";
echo "Ihr Kennwort: " . md5($_POST['pass']) . "<br>";

if (!isset($_POST['email']) || empty($_POST['email']) || (strlen($_POST['email']) < 1)) { echo "wrong data."; die(1); }
if (!isset($_POST['user' ]) || empty($_POST['user' ]) || (strlen($_POST['user' ]) < 1)) { echo "wrong data."; die(1); }
if (!isset($_POST['pass' ]) || empty($_POST['pass' ]) || (strlen($_POST['pass' ]) < 1)) { echo "wrong data."; die(1); }

if (!isset($_POST['domain_list' ])
|| empty  ($_POST['domain_list' ])
|| (strlen($_POST['domain_list' ]) < 1)) { echo "wrong data."; die(1); }

if (!isset($_POST['subdomain_name' ])
|| empty  ($_POST['subdomain_name' ])
|| (strlen($_POST['subdomain_name' ]) < 1)) { echo "wrong data."; die(1); }


$user = $_POST['user' ];
$mail = $_POST['email'];
$pass = md5($_POST['pass']);
$udom = $_POST['subdomain_name'] . $_POST['domain_list'];
echo "--=> " . $udom . "<br>";
$usid = 0;
$flag = 1;

$curdat = date("Y-m-d");
$demdat = date("Y-m-d") + 14;

$last = $curdat;
$demo = $demdat;

try {
  $dbh = new PDO('sqlite:/var/www/data/hd_data.db');
  $res = $dbh->prepare("SELECT user,pass FROM hd_users WHERE user=:user AND pass=:pass;");
  $res->execute([$user,$pass]);
  $arr = $res->fetchAll();
  if(count($arr) < 1)
  { echo "Heute: " . $curdat . "<br>";
    echo "Bis: "   . $demdat . "<br>";
    echo "user noch nicht bekannt<br>";
    echo "versuche user check...<br>";

    $res = $dbh->prepare(htmlspecialchars(
           "INSERT INTO hd_users(user,pass,udom,demo,mail,last,flag)" .
           "VALUES(:user,:pass,:udom,:demo,:mail,:last,:flag);"));
    $res->execute([$user,$pass,$udom,$demo,$mail,$last,$flag]);

    $res = $dbh->prepare(htmlspecialchars("SELECT id,user FROM hd_users WHERE user=:user;"));
    $res->execute([$user]);
    $arr = $res->fetchAll();

    echo "User... added<br>";
    $usid = $arr[0]['id'];
    $usid = $usid + 1;
  }

  if ($usid < 1) {
    $res = $dbh->prepare(htmlspecialchars("SELECT id,user FROM hd_users WHERE user=:user;"));
    $res->bindParam(":user",$user,PDO::PARAM_STR,30);
    $res->execute([$user]);
    $arr = $res->fetchAll();

    if ($arr) {
      $usid = $arr[0]['id'];
      echo "user... ok: $usid<br>";
    }
  }

  echo "suche nach SubDomain-Eintrag...<br>";

  $res = $dbh->prepare(htmlspecialchars("SELECT udom FROM hd_domains WHERE udom=:udom;"));
  $res->execute([$udom]);
  $arr = $res->fetchAll();

  $res = $dbh->prepare(htmlspecialchars("SELECT udom FROM hd_domains WHERE udom=:udom;"));
  $res->execute([$udom]);
  $arr = $res->fetchAll();
  if (!$arr) {
    $res = $dbh->prepare(htmlspecialchars("INSERT INTO hd_domains".
           "(id,udom,demo,flag)".
           "VALUES(:id,:udom,:demo,:flag);"));
    $res->execute([$usid,$udom,$demo,$flag]);
    echo "SubDomain ist verf&uuml;gbar!<br>";
  }
  else {
    echo "SubDomain ist vergeben ! $udom<br>";
    return;
  }
} catch (PDOException $e) {
  echo "error: " . $e;
  die(1);
}
?></body></html>
