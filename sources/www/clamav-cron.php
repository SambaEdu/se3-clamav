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
   * file: clamav-cron.php
   */

require("entete.inc.php");
require ("ihm.inc.php");

// HTMLpurifier
include("../se3/includes/library/HTMLPurifier.auto.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$id=$purifier->purify($_GET[id]);

if ( isset($_POST['action']))  $action = $purifier->purify($_POST['action']);
elseif ( isset($_GET['action'])) $action = $purifier->purify($_GET['action']);

// Authorization
if ( is_admin("se3_is_admin",$login)!="Y")  if ( ($uid != $login) || (($uid == $login)&&((!preg_match("//home/$login/", $wrep))&&($consul!=1))))  die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");

$link_clamav = mysqli_connect($_SESSION["SERVEUR_SQL"], $_SESSION["COMPTE_BASE"], $_SESSION["PSWD_BASE"], 'ocsweb');
mysqli_set_charset($link_clamav, "utf8");

if ($action == "trash")
{
	$query = mysqli_prepare($link_clamav, "DELETE from clamav_dirs WHERE id= ?");
	mysqli_stmt_bind_param($query,"i", $id);
	mysqli_stmt_execute($query);
	mysqli_stmt_close($query);
}

if ($action == "croncreate")
{
	// Recuperation des donnees dans la base SQL
	$query = "SELECT * from clamav_dirs ";
	$result = mysqli_query($link_clamav, $query);
	//
	while ($r=mysqli_fetch_array($result))
	{
		$id2 = $r["id"];
		$frequency=$purifier->purify($_POST["frequency".$id2]);
		$remove=$purifier->purify($_POST["remove".$id2]);
		if ($remove=="remove".$id2)
		{
			$remove2 = "1"; // changer le nom des variables... risques de confusion
		}
		else
		{
			$remove2 = "0";
		}
		$query = mysqli_prepare($link_clamav, "UPDATE clamav_dirs SET frequency='?',remove=? WHERE id=?");
		mysqli_stmt_bind_param($query,"sii", $frequency, $remove2, $id2);
		mysqli_stmt_execute($query);
		mysqli_stmt_close($query);
	}
} 

if ($action == "diradd")
{
	$directory=$purifier->purify($_POST["directory"]);
	$query = mysqli_prepare($link_clamav, "INSERT into clamav_dirs (directory,frequency,remove) VALUES ('?','weekly','0')");
	mysqli_stmt_bind_param($query,"s", $directory);
	mysqli_stmt_execute($query);
	mysqli_stmt_close($query);
}

//the form
echo '<h1> Solution antivirus serveur</h1>
<h2> Programmation de l\'antivirus </h2>
';
$query = "SELECT * from clamav_dirs ";
$result = mysqli_query($link_clamav, $query);

$form = "<form action=\"clamav-cron.php\" method=\"post\">\n";

$form .="<table align='center' border='1'>\n";
$form .="<TR><TH class=\"menuheader\"> Programmation de l'antivirus </TH></TR>\n";
$form .="<TR><td><table align='center' border='1'>\n";
$form .="<TR><th class=\"menuheader\"> R&eacute;pertoire </th><th class=\"menuheader\"> p&eacute;riodicit&eacute; du scan </th>";
$form .="<th class=\"menuheader\"> retirer les fichiers (dangeureux)</th></TR>\n";
if (mysqli_num_rows($result)!=0)
{
	while ($r=mysqli_fetch_array($result))
	{
		$none_selected = "";
		$lundi_selected ="";
		$mardi_selected ="";
		$mercredi_selected ="";
		$jeudi_selected ="";
		$vendredi_selected ="";
		$samedi_selected ="";
		$dimanche_selected ="";
		$daily_selected="";
		$weekly_selected="";
		switch ($r["frequency"])
		{
			case "none":$none_selected = "selected";
				break;
			case "lundi":$lundi_selected ="selected";
				break;
			case "mardi":$mardi_selected ="selected";
				break;
			case "mercredi":$mercredi_selected ="selected";
				break;
			case "jeudi":$jeudi_selected ="selected";
				break;
			case "vendredi":$vendredi_selected ="selected";
				break;
			case "samedi":$samedi_selected ="selected";
				break;
			case "dimanche":$dimanche_selected ="selected";
				break;
			case "daily":$daily_selected ="selected";
				break;
			case "weekly":$weekly_selected="selected";
				break;
		}
		$form .="<tr><td align=\"left\">";
		$form .="<a href='clamav-cron.php?action=trash&amp;id=".$r['id']."'>\n";
		$form .="<img src='/elements/images/edittrash.png' border='0' alt='Supprimer' title='Supprimer'>\n";
		$form .="</a>\n";
		$form .= htmlspecialchars($r['directory'], ENT_QUOTES, 'UTF-8')."</td>\n";
		$form .="<td  align=\"center\" ><select name=\"frequency".$r['id']."\"> \n";
		$form .="<option value=\"none\" ".$none_selected."> Pas de scan </option> \n";
		$form .="<option value=\"lundi\" ".$lundi_selected."> Scan lundi soir </option> \n";
		$form .="<option value=\"mardi\" ".$mardi_selected."> Scan mardi soir </option> \n";
		$form .="<option value=\"mercredi\" ".$mercredi_selected."> Scan mercredi soir </option> \n";
		$form .="<option value=\"jeudi\" ".$jeudi_selected."> Scan jeudi soir </option> \n";
		$form .="<option value=\"vendredi\" ".$vendredi_selected."> Scan vendredi soir </option> \n";
		$form .="<option value=\"samedi\" ".$samedi_selected."> Scan samedi soir </option> \n";
		$form .="<option value=\"dimanche\" ".$dimanche_selected."> Scan dimanche soir </option> \n";
		$form .="<option value=\"daily\" ".$daily_selected."> Scan quotidien </option> \n";
		$form .="<option value=\"weekly\" ".$weekly_selected."> Scan hebdomadaire </option> \n";
		$form .="</select></td>\n";
		if ($r['remove'] == 0 )
		{
			$remove_selected="";
		}
		else
		{
			$remove_selected ="checked";
		}
		$form .="<td class=\"menucell\"  align=\"center\" > Suppression des virus (dangereux) <input type=\"checkbox\" name=\"remove".$r['id']."\" value=\"remove".$r['id']."\"".$remove_selected."  /><br/> \n";
	}
	$form .= "</table></td></tr>\n";

	$form .="<tr><td align='right'><input type='hidden' name='action' value='croncreate'>\n";
	$form.="<input type=\"submit\" value=\"Valider\"></td></tr>\n";
	$form .= "</table>\n";
	$form.="</form>\n";

}

echo $form;

$form = "<form action=\"clamav-cron.php\" method=\"post\">\n";
$form .="<table align='center' border='1'>\n";
$form .="<TR><TH> Ajout de r&eacute;pertoire </TH></TR>\n";
$form .="<TR><TD>R&eacute;pertoire &agrave; ajouter : <input type=\"text\" name=\"directory\" value=\"\"> 
</TD></TR>";
$form .= "</table>\n";
$form .= "<input type=\"hidden\" name=\"action\" value=\"diradd\">";
$form .= "<input type=\"submit\" value=\"Ajouter\">";
$form.="</form>\n";

echo $form;

mysqli_close($link_clamav);

require ("pdp.inc.php");
?>