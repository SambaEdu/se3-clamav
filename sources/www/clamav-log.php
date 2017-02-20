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
   * file: clamav-log.php
   */


require("entete.inc.php");
require ("ihm.inc.php");

// HTMLpurifier
include("../se3/includes/library/HTMLPurifier.auto.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

if (isset($_POST['directory']))
	$directory=$purifier->purify($_POST['directory']);

if ( is_admin("se3_is_admin",$login)!="Y") die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction"
)."</BODY></HTML>");

$link_clamav = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
mysqli_set_charset($link_clamav, "utf8");

echo "<h1> Solution antivirus serveur</h1>\n";
if (isset($directory))
{
	print "<h2> Log des scans du r&eacute;pertoire".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."</h2>\n";
	if (! isset($_POST["scan_start"]))
		$scan_start=0;
	else
		$scan_start=$_POST["scan_start"]+0;
	
	$query = mysqli_prepare($link_clamav, "SELECT id,date,directory,summary,result FROM clamav_scan WHERE directory= ? ORDER BY id desc LIMIT ?,1");
	mysqli_stmt_bind_param($query,"si", $directory, $scan_start);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_id,$res_date,$res_directory,$res_summary,$res_result);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	mysqli_stmt_fetch($query);
	mysqli_stmt_close($query);

	if (1==1)
	{
		echo "<TABLE width='100%'><TR><TD WIDTH='50%' align=\"left\">";
		if ($scan_start!=0)
		{
			echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
			$previous_scan_start=$scan_start-1;
			echo "<input type=\"hidden\" name=\"scan_start\" value=\"".htmlspecialchars($previous_scan_start, ENT_QUOTES, 'UTF-8')."\"/>";
			print "<input type=\"hidden\" name=\"directory\" value=\"".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."\">\n";
			print "<input type=\"submit\" value=\"".gettext("Afficher les logs pr&eacute;c&eacute;dents.")."\">\n";
			print "</form>\n";
		}
		echo"</td>\n";
		if ($num_rows==0)
		{
			echo "<td></td></tr></table>\n";
			echo gettext("fin des logs de scan");
		}
		else
		{
			echo "<td width=\"50%\" align=\"right\">";
			echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
			$next_scan_start=$scan_start+1;
			echo "<input type=\"hidden\" name=\"scan_start\" value=\"".$next_scan_start."\"/>";
			print "<input type=\"hidden\" name=\"directory\" value=\"".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."\">\n";
			print "<input type=\"submit\" value=\"".gettext("Afficher les logs suivants.")."\">\n";
			print "</form>\n";
			echo "</td></tr></table>\n";
			// affichage de la table connexions
			echo "<TABLE  align='center' border='1'>\n";
			echo "<TR><TH> Log du scan par clamav</TH></TR>\n";
			// DATE
			echo "<TR><TD class=\"menuheader\">\n";
			echo "Date du scan";
			echo "</TD></TR>";
			echo "<TR><TD><pre>";
			echo $res_date;
			echo "</pre></TD></TR>";
			// SUMMARY
			echo "<TR><TD class=\"menuheader\">\n";
			echo "r&eacute;sum&eacute;";
			echo "</TD></TR>";
			echo "<TR><TD><pre>\n";
			echo $res_summary;
			echo "</pre></TD></TR>";
			//SCAN RESULT
			echo "<TR><TD class=\"menuheader\">\n";
			echo "R&eacute;sultat du scan";
			echo "</TD></TR>";
			echo "<TR><TD><pre>\n";
			echo $res_result;
			echo "</pre></TD></TR></TABLE>\n";
		}
	}
}
else
{
	$query = mysqli_prepare($link_clamav, "SELECT DISTINCT directory FROM clamav_scan");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_directory);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	if ($num_rows!=0)
	{
		echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
		while (mysqli_stmt_fetch($query))
		{
			echo "<input type=\"radio\" name=\"directory\" value=\"".htmlspecialchars($res_directory, ENT_QUOTES, 'UTF-8')."\">\n"; 
			echo gettext("Log des scans du r&eacute;pertoire ".htmlspecialchars($res_directory, ENT_QUOTES, 'UTF-8'));
			echo "<br/>\n";
		}
		echo "<input type=\"submit\" value=\"".gettext("Afficher les logs.")."\">\n";
		echo "</form>\n";
	}
	else
		echo "Aucun r&#233;pertoire trouv&#233;";
	mysqli_stmt_close($query);
}

mysqli_close($link_clamav);

require ("pdp.inc.php");
?>
