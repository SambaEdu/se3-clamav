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

$directory=$purifier->purify($_POST[directory]);

if ( is_admin("se3_is_admin",$login)!="Y") die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction"
)."</BODY></HTML>");

echo "<h1> Solution antivirus serveur</h1>\n";
if (isset($directory)) {
    print "<h2> Log des scans du r&eacute;pertoire".$directory."</h2>\n";
    if (! isset($scan_start)) $scan_start=0;
    $query=" SELECT * FROM clamav_scan WHERE directory='".$directory."'";
    $query .=" ORDER BY id desc ";
    $query .="LIMIT $scan_start,1";
    
    $result = mysql_query($query);
    
    if (($result)) {
      echo "<TABLE width='100%'><TR><TD WIDTH='50%' align=\"left\">";
      if ($scan_start!=0) {
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
        $previous_scan_start=$scan_start-1;
        echo "<input type=\"hidden\" name=\"scan_start\" value=\"$previous_scan_start\"/>";
        print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\">\n";
        print "<input type=\"submit\" value=\"".gettext("Afficher les logs pr&eacute;c&eacute;dents.")."\">\n";
        print "</form>\n";
        }
      echo"</td>\n";
	  if (mysql_num_rows($result)==0) {
        echo "<td></td></tr></table>\n";
        echo gettext("fin des logs de scan");
	  } else {
        echo "<td width=\"50%\" align=\"right\">";
        echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
        $next_scan_start=$scan_start+1;
        echo "<input type=\"hidden\" name=\"scan_start\" value=\"$next_scan_start\"/>";
        print "<input type=\"hidden\" name=\"directory\" value=\"".$directory."\">\n";
        print "<input type=\"submit\" value=\"".gettext("Afficher les logs suivants.")."\">\n";
        print "</form>\n";
        echo "</td></tr></table>\n";
        // affichage de la table connexions
        echo "<TABLE  align='center' border='1'>\n";
        echo "<TR><TH> Log du scan par clamav</TH></TR>\n";
        $r=mysql_fetch_array($result);
       // SUMMARY
        echo "<TR><TD class=\"menuheader\">\n";
        echo "r&eacute;sum&eacute;";
        echo "</TD></TR>";
        echo "<TR><TD><pre>\n";        
        echo $r["summary"];
        echo "</pre></TD></TR>";
        //SCAN RESULT
        echo "<TR><TD class=\"menuheader\">\n";
        echo "R&eacute;sultat du scan";
        echo "</TD></TR>";
        echo "<TR><TD><pre>\n";        
        echo $r["result"];
        echo "</pre></TD></TR></TABLE>\n";
        }
      }
    } else {  
    $query="SELECT DISTINCT directory FROM clamav_scan"; 
    $result = mysql_query($query);

    print "<form action='".$_SERVER['PHP_SELF']."' method='post'>\n";
    while ($r=mysql_fetch_array($result)) {
      print "<input type=\"radio\" name=\"directory\" value=\"".$r["directory"]."\">\n"; 
      print gettext("Log des scans du r&eacute;pertoire ".$r["directory"]);
      print "<br/>\n";
    }
    print "<input type=\"submit\" value=\"".gettext("Afficher les logs.")."\">\n";
    print "</form>\n";    
}

require ("pdp.inc.php");
?>
