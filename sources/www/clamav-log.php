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

if (isset($_POST[directory]))
	$directory=$purifier->purify($_POST[directory]);

if ( is_admin("se3_is_admin",$login)!="Y") die (gettext("Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette fonction"
)."</BODY></HTML>");

echo "<h1> Solution antivirus serveur</h1>\n";
if (isset($directory)) {
    print "<h2> Log des scans du r&eacute;pertoire".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."</h2>\n";
    if (! isset($_POST["scan_start"])) $scan_start=0; else $scan_start=$_POST["scan_start"]+0;
    $query=" SELECT * FROM clamav_scan WHERE directory='".mysql_real_escape_string($directory)."'";
    $query .=" ORDER BY id desc ";
    $query .="LIMIT '".mysql_real_escape_string($scan_start)."',1";
    
    $result = mysql_query($query);
    
    if (($result)) {
      echo "<TABLE width='100%'><TR><TD WIDTH='50%' align=\"left\">";
      if ($scan_start!=0) {
        echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
        $previous_scan_start=$scan_start-1;
        echo "<input type=\"hidden\" name=\"scan_start\" value=\"".htmlspecialchars($previous_scan_start, ENT_QUOTES, 'UTF-8')."\"/>";
        print "<input type=\"hidden\" name=\"directory\" value=\"".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."\">\n";
        print "<input type=\"submit\" value=\"".gettext("Afficher les logs pr&eacute;c&eacute;dents.")."\">\n";
        print "</form>\n";
        }
      echo"</td>\n";
	  if (mysql_num_rows($result)==0) {
        echo "<td></td></tr></table>\n";
        echo gettext("fin des logs de scan");
	  } else {
        echo "<td width=\"50%\" align=\"right\">";
        echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
        $next_scan_start=$scan_start+1;
        echo "<input type=\"hidden\" name=\"scan_start\" value=\"".htmlspecialchars($next_scan_start, ENT_QUOTES, 'UTF-8')."\"/>";
        print "<input type=\"hidden\" name=\"directory\" value=\"".htmlspecialchars($directory, ENT_QUOTES, 'UTF-8')."\">\n";
        print "<input type=\"submit\" value=\"".gettext("Afficher les logs suivants.")."\">\n";
        print "</form>\n";
        echo "</td></tr></table>\n";
        // affichage de la table connexions
        echo "<TABLE  align='center' border='1'>\n";
        echo "<TR><TH> Log du scan par clamav</TH></TR>\n";
        $r=mysql_fetch_array($result);
		// DATE
        echo "<TR><TD class=\"menuheader\">\n";
        echo "Date du scan";
        echo "</TD></TR>";
        echo "<TR><TD><pre>";        
        echo htmlspecialchars($r["date"], ENT_QUOTES, 'UTF-8');
        echo "</pre></TD></TR>";
       // SUMMARY
        echo "<TR><TD class=\"menuheader\">\n";
        echo "r&eacute;sum&eacute;";
        echo "</TD></TR>";
        echo "<TR><TD><pre>\n";        
        echo htmlspecialchars($r["summary"], ENT_QUOTES, 'UTF-8');
        echo "</pre></TD></TR>";
        //SCAN RESULT
        echo "<TR><TD class=\"menuheader\">\n";
        echo "R&eacute;sultat du scan";
        echo "</TD></TR>";
        echo "<TR><TD><pre>\n";        
        echo htmlspecialchars($r["result"], ENT_QUOTES, 'UTF-8');
        echo "</pre></TD></TR></TABLE>\n";
        }
      }
    } else {  
    $query="SELECT DISTINCT directory FROM clamav_scan"; 
    $result = mysql_query($query);

    print "<form action='".htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8')."' method='post'>\n";
    while ($r=mysql_fetch_array($result)) {
      print "<input type=\"radio\" name=\"directory\" value=\"".htmlspecialchars($r["directory"], ENT_QUOTES, 'UTF-8')."\">\n"; 
      print gettext("Log des scans du r&eacute;pertoire ".htmlspecialchars($r["directory"], ENT_QUOTES, 'UTF-8'));
      print "<br/>\n";
    }
    print "<input type=\"submit\" value=\"".gettext("Afficher les logs.")."\">\n";
    print "</form>\n";    
}

require ("pdp.inc.php");
?>
