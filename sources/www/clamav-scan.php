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
   * file: clamav-scan.php
   */


require("entete.inc.php");
require ("ihm.inc.php");

// HTMLpurifier
include("../se3/includes/library/HTMLPurifier.auto.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$upload=$purifier->purify($_POST[upload]);

//aide
$_SESSION["pageaide"]="L\'interface_%C3%A9l%C3%A8ve#La_solution_antivirus";
	
if( isset($upload) ) // si formulaire soumis
{
  $file = $_FILES['fichier']['tmp_name'];
  if ($file=="") {
    exit ("le t&eacute;l&eacute;chargement n'a pas eu lieu. La taille est peut &ecirc;tre sup&eacute;rieure &agrave; 2 Mo?");
  }
  if( preg_match('#[\x00-\x1F\x7F-\x9F]#', $file))
  {
    exit("Nom de fichier non valide");
  }
  else {
    exec('/usr/bin/clamscan'." ".$file,$scan_output);
    $ligne=array_shift($scan_output);
    print "<div  style=\"padding:10px; border: solid #9e9784 2px; background-color:#6699CC; -moz-border-radius: 20px 20px 0 0;\">
     Scan antivirus sur le  fichier ".$_FILES['fichier']['name'].". 
     </div>\n";     
    if ( preg_match('/:\ OK/',$ligne)) { 
      print "<div style=\"padding:10px; border: solid #9e9784 2px; -moz-border-radius: 0 0 20px 20px;\"
	pas de virus pour le fichier ".$_FILES['fichier']['name']."<br>\n"; 
      }
    else {
      print "<div style=\"padding:10px; background-color:#FFAAAA; border: solid #9e9784 2px; -moz-border-radius: 0 0 20px 20px;\">
	Probl&egrave;me avec le fichier ".$_FILES['fichier']['name']."\n"; 
      $temp = explode(":",$ligne,2);
      print " :".$temp[1]."<br>";
      }
    foreach($scan_output as $ligne) {
      print "$ligne <br>";
    }
    print "</div>";
  }
}
else 
{
print "<p>
<div  style=\"padding:10px; border: solid #9e9784 2px; background-color:#6699CC; -moz-border-radius: 20px 20px 0 0;\">
Scan antivirus sur un fichier.
</div>
<div style=\"padding:10px; border: solid #9e9784 2px;\">
Cette page vous permet de soumettre un fichier pour effectuer un scan antivirus dessus.<BR>
Vous pouvez soumettre des fichiers d'une taille maximum de 2 Mo.<BR>
</div>
<div style=\"padding:3px; border: solid #9e9784 2px; background-color:#EEEEEE; -moz-border-radius: 0 0 20px 20px;\">
<form method=\"post\" enctype=\"multipart/form-data\" action=\"clamav-scan.php\">
<p>
<input type=\"file\" maxlength=\"2000000\" name=\"fichier\" size=\"30\" style=\"margin:2px\"><br>
<input type=\"submit\" name=\"upload\" value=\"Scanner ce fichier\" style=\"margin:2px\">
</p>
</form>
</div>";
}
require ("pdp.inc.php");
?>
