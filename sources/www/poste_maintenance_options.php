<?php
/**
 * Maintenance d'un poste
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

	if (isset($_GET["tri"]))
		$tri=$purifier->purify($_GET["tri"])+0;
	else
		$tri=0;
	if (isset($_GET["tri2"]))
		$tri2=$purifier->purify($_GET["tri2"])+0;
	else
		$tri2=0;	
	if (isset($_GET['Appli']))
		$get_Appli=$purifier->purify($_GET['Appli']);
	else
		$get_Appli="";
	if (isset($_GET['id_host']))
		$id_host=$purifier->purify($_GET['id_host']);
	else
		$id_host="";
	if (isset($_GET["warning"]))
		$get_warning=$purifier->purify($_GET["warning"])+0;
	else
		$get_warning=1;
	if (isset($_GET["error"]))
		$get_error=$purifier->purify($_GET["error"])+0;
	else
		$get_error=1;
	if (isset($_GET["ok"]))
		$get_ok=$purifier->purify($_GET["ok"])+0;
	else
		$get_ok=1;
	if (isset($_GET["tous"]))
		$get_tous=$purifier->purify($_GET["tous"])+0;
	else
		$get_tous=0;

	echo "<form method='get' action=''>\n";
	$page_id=2;
	include ("poste_top.php");
	echo "</form>\n";



include ("pdp.inc.php");
?>