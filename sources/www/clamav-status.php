<?php

/**
   
   * Page clamv
   * @Version $Id$ 
   
   * @Projet LCS / SambaEdu 
   
   * @auteurs 

   * @Licence Distribue selon les termes de la licence GPL
   
   * @note 
   
   */

   /**

   * @Repertoire: /se3-clamav
   * file: clamav-status.php
   */



require("entete.inc.php");
require ("ihm.inc.php");

// HTMLpurifier
include("../se3/includes/library/HTMLPurifier.auto.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

if (isset($_POST['action']))
	$action=$purifier->purify($_POST['action']);
else
	$action="";
if (isset($_POST['mailing']))
	$mailing=$purifier->purify($_POST['mailing']);
if (isset($_POST['address']))
	$address=$purifier->purify($_POST['address']);

if ( is_admin("se3_is_admin",$login)!="Y")  if ( ($uid != $login) || (($uid == $login)&&((!preg_match("//home/$login/", $wrep))&&($consul!=1))))  die (gettext("Vous n'avez pas les droits suffisants pour acc�der � cette fonction")."</BODY></HTML>");

//
// Fichier de paquets apt - date de mise a jour.
//

$now = getdate();
$updatetime = filemtime("/var/cache/apt/pkgcache.bin");
$update_days = floor(($now[0]-$updatetime)/(3600*24));

//
// Base des signatures antivirales
//

$dailycvd= '/var/lib/clamav/daily.cvd';
$dailycld= '/var/lib/clamav/daily.cld';

if (file_exists($dailycvd))
{
	$update_virus_time = filemtime($dailycvd);
}
else
{
	$update_virus_time = filemtime($dailycld);
}

//
// Statut du paquet CLAMAV
//

$status = fopen("/var/lib/dpkg/status","r");
$parse_state = "begin";
while (!feof ($status))
{
	if ($parse_state == "begin")
	{
		$buffer = fgets($status,1024);
		if (preg_match ("/^Package\s*\:\s*clamav\s*/",$buffer))
		{
			$parse_state="version";
		}
		continue;
	}
	if ($parse_state == "version")
	{
		$buffer = fgets($status,1024);
		if (preg_match ("/^Version/",$buffer))
		{
			$line_pieces = explode(":",$buffer);
			$status_version = $line_pieces[1];
			$status_version = preg_replace("/^\s*/","",$status_version);
			$status_version = preg_replace("/\s*\b/","",$status_version);
			break;
		}
	}
}
fclose($status);

$avail = fopen("/var/lib/dpkg/available","r");

$parse_state = "begin";
while (!feof ($avail))
{
	if ($parse_state == "begin")
	{
		$buffer = fgets($avail,1024);
		if (preg_match ("/^Package\s*\:\s*clamav\s*/",$buffer))
		{
			$parse_state="version";
		}
	continue;
	}
	if ($parse_state == "version")
	{
		$buffer = fgets($avail,1024);
		if (preg_match ("/^Version/",$buffer))
		{
			$line_pieces = explode(":",$buffer);
			$avail_version = $line_pieces[1];
			$avail_version = preg_replace("/^\s*/","",$avail_version);
			$avail_version = preg_replace("/\s*\b/","",$avail_version);
			break;
		}
	}
}
fclose($avail);
?>
<h1> Solution antivirus serveur</h1>
<h2> Mise &agrave; jour des informations sur les paquets </h2>
La liste des paquets disponibles a &eacute;t&eacute; mise &agrave; jour le
<?php
setlocale (LC_TIME, "fr_FR");
echo strftime ("%A %d %B %Y",$updatetime);
?>
<br/>
<?php
if ($update_days > 0)
{
	echo "La mise a jour de la liste des paquets date de $update_days jours.\n";
	if ($update_days > 7)
	{
		echo "vous devriez <a href=\"../action.php\"> mettre &agrave jour </a>";
	}
}
else
{
	echo "Il est inutile de mettre &agrave; jour";
}
?>
<h2> Installation de Clamav </h2>
<table>
<tr>
<td class="menuheader">la version install&eacute;e de clamav est</td>
<td class="menuheader">la version disponible de clamav est </td>
</tr>
<tr>
<td class="menucell"><?php echo $status_version; ?> </td>
<td class="menucell"><?php echo $avail_version; ?></td>
</tr>
</table>
<?php
if ($status_version == $avail_version)
{
	echo "vous n'avez pas besoin de faire de mise &agrave; jour";
}
else
{
	echo "vous devriez <a href=\"../action.php\"> mettre &agrave; jour</a>";
}
?>
<h2> Base des signatures virales </h2>
La base des signatures virales a &eacute;t&eacute; mise &agrave; jour le :
<?php
setlocale (LC_TIME, "fr_FR");
echo strftime ("%A %d %B %Y",$update_virus_time);
?>
<?php
$link_clamav = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
mysqli_set_charset($link_clamav, "utf8");

if ($action == "mailing")
{
	//inscription des parametres dans la base SQL
	$mailing_boolean = "0";
	if (isset ($mailing))
	{
		$mailing_boolean = "1"; 
	}
	$mailing_address = $address;
	
	$query = mysqli_prepare($link_clamav, "INSERT INTO params (value, name, descr, cat) VALUES (?,'clamavmail','activation des mails',6) ON DUPLICATE KEY UPDATE value=?");
	mysqli_stmt_bind_param($query,"ii", $mailing_boolean, $mailing_boolean);
	mysqli_stmt_execute($query);
	mysqli_stmt_close($query);
	
	$query = mysqli_prepare($link_clamav, "INSERT INTO params (value, name, descr, cat) VALUES (?,'clamavadm','mail enregistre pour clamav',6) ON DUPLICATE KEY UPDATE value=?");
	mysqli_stmt_bind_param($query,"ss", $mailing_address, $mailing_address);
	mysqli_stmt_execute($query);
	mysqli_stmt_close($query);
}
else
{
	// Courriel 
	$query = mysqli_prepare($link_clamav, "SELECT value from params where name='clamavmail'");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$mailing_boolean=$value;
	mysqli_stmt_close($query);

	$query = mysqli_prepare($link_clamav, "SELECT value from params where name='clamavadm'");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$value);
	mysqli_stmt_fetch($query);
	$mailing_address=$value;
	mysqli_stmt_close($query);
}

mysqli_close($link_clamav);
?>
<h2> Rapport par courriel </h2>
Le syst&egrave;me antivirus peut vous envoyer un rapport du scan par courriel &agrave; votre demande.
<br/>
<form action="clamav-status.php" method="post">
<?php 
if ($mailing_boolean == 0 )
{
	$selected="";
}
else
{
	$selected ="checked";
}
echo "<input type=\"checkbox\" name=\"mailing\" value=\"mailing\" ".$selected." />\n"; 
?> Envoyer un courriel en cas de virus trouv&eacute;.
<br>
<input type="text" name="address" value="<?php echo htmlspecialchars($mailing_address, ENT_QUOTES, 'UTF-8'); ?>"/> Adresse mail d'envoi du courriel.
<br/>
<input type="hidden" name="action" value="mailing">
<input type="submit" value="Valider">
</form>
<?php
require ("pdp.inc.php");
?>