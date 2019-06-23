<?php
/**
 * applicaiton de la mise en forme
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
 */
/**
 * @Repertoire: dhcp
 * file: reservations.php
*/
	// loading libs and init
	include "entete.inc.php";
	include "ihm.inc.php";
	include "wpkg_lib.php";
	include "wpkg_libsql.php";

	$login = isauth();
	if (! $login )
	{
		echo "<script language=\"JavaScript\" type=\"text/javascript\">\n<!--\n";
		$request = '/wpkg/index.php';
		echo "top.location.href = '/auth.php?request=" . rawurlencode($request) . "';\n";
		echo "//-->\n</script>\n";
		exit;
	}

	if (is_admin("computers_is_admin",$login)!="Y")
		die (gettext("Vous n'avez pas les droits suffisants pour acc&#233;der &#224; cette fonction")."</BODY></HTML>");

	// HTMLpurifier
	include("../se3/includes/library/HTMLPurifier.auto.php");
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);

	 if (isset($_GET["action"]))
		$get_action=$_GET["action"]+0;
	else
		$get_action=0;
	
	$return="";
	switch ($get_action)
	{
		case 0: break;
		case 1: update_mef_defaut(); $return="<b>Mise en forme par défaut appliquée.</b><br>"; break;
		case 2: update_mef_test(); $return="<b>Mise en forme personnalisée appliquée.</b><br>"; break;
	}

	$page_id=2;
	include("mef_top.php");
	
	echo $return;

	echo "<table align='center'>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a onclick=\"popuprecherche('mef_default.php','mef','scrollbars=no,width=800,height=500');\" style='color:".$regular_lnk.";'>Visualiser un apercu de la mise en forme par défaut</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a href='?action=1'>Remise de la mise en forme par défaut</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a onclick=\"popuprecherche('mef_test.php','mef','scrollbars=no,width=800,height=500');\" style='color:".$regular_lnk.";'>Visualiser un apercu de la mise en forme personnalisée</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a href='?action=2'>Mise en place de la mise en forme personnalisée</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
		echo "<table align='center'>\n";
	echo "</table>\n";

include ("pdp.inc.php");
?>