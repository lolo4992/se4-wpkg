<?php
/**
 * Maintenance des parcs d'une application
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
//	include "ldap.inc.php";
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
	if (isset($_GET['parc']))
		$get_parc=$purifier->purify($_GET['parc']);
	else
		$get_parc="";
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
		$get_ok=0;
	if (isset($_GET["tous"]))
		$get_tous=$purifier->purify($_GET["tous"])+0;
	else
		$get_tous=0;

	$liste_parcs=info_parcs();
	asort($liste_parcs);

	if (isset($_POST["action"]))
		$post_action=$purifier->purify($_POST["action"]);
	else
		$post_action="";

	$result_xml="";
	if ($post_action=="Annuler les modifications")
	{
		header("Location: app_maintenance.php?tri2=".$tri2."&Appli=".$get_Appli."&parc=".$get_parc."&tous=".$get_tous."&ok=".$get_ok."&warning=".$get_warning."&error=".$get_error);
		exit;
	}
	elseif ($post_action=="Valider les modifications")
	{
		if (isset($_POST["parc"]))
			$post_parc=$_POST["parc"];
		else
			$post_parc=array();
		
		$tmp_result_xml=set_appli_entites($post_parc,"parc",$get_Appli);
		$result_xml="<center>Modification effectu&#233;e. Application ajout&#233;e sur ".$tmp_result_xml["in"]." parc";
		if ($tmp_result_xml["in"]>1)
			$result_xml.="s";
		$result_xml.=" et retir&#233;e sur ".$tmp_result_xml["out"]." parc";
		if ($tmp_result_xml["out"]>1)
			$result_xml.="s";
		$result_xml.=".</center><br>";
	}

	echo "<script>\n";
	echo "function checkAll()\n";
	echo "{\n";
	echo "     var checkboxes = document.getElementsByTagName('input'), val = null; \n";   
	echo "     for (var i = 0; i < checkboxes.length; i++)\n";
	echo "     {\n";
 	echo "        if (checkboxes[i].type == 'checkbox')\n";
 	echo "        {\n";
 	echo "            if (val === null) val = checkboxes[i].checked;\n";
 	echo "            checkboxes[i].checked = val;\n";
 	echo "        }\n";
 	echo "    }\n";
	echo " }\n";
	echo "</script>\n";

	echo "<form method='get' action=''>\n";
	$page_id=2;
	include ("app_top.php");

	echo "<input type='hidden' name='parc' value='".$get_parc."'>";
	echo "<input type='hidden' name='tous' value='".$get_tous."'>";
	echo "<input type='hidden' name='ok' value='".$get_ok."'>";
	echo "<input type='hidden' name='warning' value='".$get_warning."'>";
	echo "<input type='hidden' name='error' value='".$get_error."'>";
	echo "</form>\n";

	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' align='justify'>\n";
		echo "<td bgcolor='".$dep_entite_bg."' style='color:".$dep_entite_txt."' width='250'>";
		echo "D&#233;ploiement demand&#233; pour ce parc";
		echo "</td>\n";
		echo "<td bgcolor='".$dep_depend_bg."' style='color:".$dep_depend_txt."' width='250'>";
		echo "D&#233;ploiement demand&#233; par une d&#233;pendance";
		echo "</td>\n";
		echo "<td bgcolor='".$dep_no_bg."' style='color:".$dep_no_txt."' width='250'>";
		echo "D&#233;ploiement non demand&#233; pour ce parc";
		echo "</td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor='white'>\n";
		echo "<th colspan='3'>";
		echo "<a href='app_maintenance.php?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."' style='color:".$regular_lnk."'>Retour</a>";
		echo "</th>\n";
	echo "</tr>\n";
	echo "</table><br>\n";

	echo $result_xml;

	echo "<form method='post' action='?tri2=".$tri2."&Appli=".$get_Appli."&parc=".$get_parc."&tous=".$get_tous."&ok=".$get_ok."&warning=".$get_warning."&error=".$get_error."'>";
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='black'>";
		echo "<td align='center' colspan='2' width='400'>";
		echo "<input type='submit' name='action' value='Valider les modifications'>";
		echo "</td>";
		echo "<th align='center' width='200' style='color:white'>";
		echo "<input type='checkbox' onchange='checkAll()' name='chk[]' /> Tous/Aucun";
		echo "</th>";
		echo "<td align='center' colspan='2' width='400'>";
		echo "<input type='submit' name='action' value='Annuler les modifications'>";
		echo "</td>";
	echo "</tr>\n";

	$liste_parc_app=info_application_parcs($get_Appli); // liste des parcs ou l'app est demandee
	$liste_dependance=info_application_requiered_parc($application["id_app"]); // liste des parcs ou l'app est necessaire par dependance

	$i=0;
	echo "<tr bgcolor='white'>\n";
	foreach($liste_parcs as $parc)
	{
		if ($i==5)
		{
			$i=0;
			echo "</tr>\n";
			echo "<tr bgcolor='white'>\n";
		}
		if (!is_array($liste_parc_app))
		{
			$liste_parc_app=array();
		}
		echo "<td align='left' width='200'>";
		echo "<table width='100%'><tr>";
		echo "<td align='left' width='30'>";
		echo "<input type='checkbox' id='parc[]' name='parc[]' value='".$parc["id"]."'";
		if (in_array($parc["nom_parc"],$liste_parc_app))
			echo " checked";
		echo " />";
		echo "</td>";
		echo "<td align='left' width='*'";
		if (in_array($parc["nom_parc"],$liste_parc_app))
			echo " bgcolor='".$dep_entite_bg."' style='color:".$dep_entite_txt."'";
		elseif (in_array($parc["nom_parc"], $liste_dependance))
		{
			echo " bgcolor='".$dep_depend_bg."' style='color:".$dep_depend_txt."'";
		}
		else
		{
			echo " bgcolor='".$dep_no_bg."' style='color:".$dep_no_txt."'";
		}
		echo">&nbsp;".$parc["nom_parc"]."</td>";
		echo "</tr></table>";
		echo "</td>\n";
		$i++;
	}
	if ($i!=5)
		echo "<td align='left' colspan='".(5-$i)."'></td>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>";

	include ("pdp.inc.php");
?>