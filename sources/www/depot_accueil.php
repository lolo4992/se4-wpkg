<?
/**
 * accueil de la section depot
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
 */

	// loading libs and init
	include "entete.inc.php";
	include "ihm.inc.php";
	include "wpkg_lib.php";
	include "wpkg_libsql.php";
	include "wpkg_lib_admin.php";

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

	if (isset($_POST["ignoreWawadebMD5"]))
		$ignoreWawadebMD5=$purifier->purify($_POST["ignoreWawadebMD5"])+0;
	else
		$ignoreWawadebMD5=0;
	if (isset($_POST["noDownload"]))
		$noDownload=$purifier->purify($_POST["noDownload"])+0;
	else
		$noDownload=0;

	$page_id=0;
	include("depot_top.php");

	include ("pdp.inc.php");
?>