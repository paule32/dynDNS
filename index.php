<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>dynDNS 1.0 - dynamic DNS</title>
<script type="text/javascript" src="./js/jquery-3.0.0.min.js"></script>
<script type="text/javascript" src="./onload.js"></script>
<link   type="text/css"        rel="stylesheet" href="./mytest.css">
</head>
<body>
<div id="page_container">
  <div id="page_header">
    <b>dynDNS - dynamic DNS 1.0</b><br>
    <i>(c) 2016 by Jens Kallup - Alle Rechte vorbehalten!</i>
  </div>
  <div id="page_menu">
    <div id="menu_home"  class="menu_item">Home</div>
    <div id="menu_hilfe" class="menu_item">Hilfe</div>
    <div id="menu_login" class="menu_item">Anmelden</div>
  </div>
  <div id="workplace">
    <div>
      <div id="left_menupanel">
        <br>HOME
        <hr>
        <a href=""> &nbsp; <b>.:. NEUIGKEITEN .:.&nbsp;</b></a>
      </div>
      <div id="right_menupanel">
        <br>TEAM<hr>
        .: Beitretten :.<br>
        .: Gr&uuml;nden :.
      </div>
      <div id="middle_menupanel">
        <div id="middle_menupanel_inner">
	<?php
if (!isset($_GET['pay']))
{
  if (isset($_GET['init']) && $_GET['init'] === "ok")
  {
  echo
  "Noch ein letzter Schritt, um die Registrierung abzuschließen:<p>".
  "<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">".
  "<input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">".
  "<input type=\"hidden\" name=\"hosted_button_id\" value=\"E2R4ZHJBRXH6L\">".
  "<input type=\"hidden\" name=\"item_name\" value=\"dnyDNS - non-profit\">".
  "<input type=\"hidden\" name=\"return\" value=\"http://www.dns-ip.org/success\">".
  "<input type=\"hidden\" name=\"on0\" value=\"" . $_GET['em'] . "\">".
  "<input type=\"hidden\" name=\"on1\" value=\"" . $_GET['pd'] . "\">".
  "<input type=\"image\" src=\"https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif\" ".
  " border=\"0\" name=\"submit\" alt=\"Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.\">".
  "<img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/de_DE/i/scr/pixel.gif\" width=\"1\" ".
  "height=\"1\">".
  "</form>";
  }
  else if (isset($_GET['init']) && $_GET['init'] === "success") {
  echo "Ihre Registrierung war erfolgreich!<br>",
  "Sie können nun den dynDNS 1.0 services nutzen.<p>".
  "Mit freundlichen Grüßen<p>".
  "Jens Kallup";
  }
  else if (isset($_GET['init']) && $_GET['init'] === "cancel") {
  echo "Ihe Registrierung ist leider fehlgeschlagen.<br>".
  "Sie können nach 30 Sekunden erneut einen Versuch starten.".
  header('Refresh: 5; URL="http://kallup.net/dns/index.php"');
  }
  else {
  echo
  "<b>Willkommen bei dynDNS 1.0</b><p><p>" .
  "Bitte w&auml;hlen Sie eine Domain aus, auf der Sie erreichbar sein m&ouml;chten." .
  "<form action=\"payload.php?tld=list\" method=\"post\">" .
  "Subdomain: <input type=\"text\" id=\"subdomain_name\" name=\"subdomain_name\"><br>".
  "TLD-Domain: <select id=\"list\" name=\"domain_list\">".
  "  <option value=\".dns-ip.org\">.dns-ip.org</option>".
  "  <option value=\".ipc-home.net\">.ipc-home.net</option>".
  "  <option value=\".bildung\">.bildung</option>".
  "  <option value=\".katzen\">.katzen</option>".
  "  <option value=\".lernen\">.lernen</option>".
  "  <option value=\".programmieren\">.programmieren</option>".
  "  <option value=\".schule\">.schule</option>".
  "  <option value=\".spiele\">.spiele</option>".
  "</select><p>".
  "E-Mail:<br><input type=\"text\" name=\"email\" id=\"email\" maxlength=\"30\"><br>".
  "Passwort:<br><input type=\"password\" id=\"pass\" name=\"pass\" maxlength=\"60\"><br>".
  "Buddy:<br><input type=\"text\" name=\"user\" maxlength=\"64\"><br><br>".
  "<input type=\"submit\" name=\"action\" value=\"Registrieren\"><form><p>";
}}
?>
        </div>
      </div>
      <div id="login_page">
        <div id="login_page_text"><b>
          Bitte geben Sie Ihren Benutzername <br>
          sowie das Kennwort ein:</b><p>
          Benutzername: <input type="text" id="login_username" name="username"><br>
          Kennung: &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;<input type="password" id="login_password" name="password">
        </div>
        <div id="login_page_button">Anmelden</div>
      </div>
    </div>
  </div>

  <div id="menu_inhalt" class="menu_box menu_help">Inhalt</div>
  <div id="menu_ueber"  class="menu_box menu_help">Über...</div>

  <div id="noDisplayArea" style="display:none"></div>
</div>
</body>
</html>
