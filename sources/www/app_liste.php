<?php
/**
 * Affichage de la liste des applications du serveur
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
 */
/**
 * @Repertoire: wpkg
 * file: app_liste.php
*/
	// loading libs and init
	include "entete.inc.php";
	//include "ldap.inc.php";
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

	echo "<form method='get' action=''>\n";
	$page_id=0;
	include ("app_top.php");

	echo "<input type='hidden' name='parc' value='".$get_parc."'>";
	echo "<input type='hidden' name='tous' value='".$get_tous."'>";
	echo "<input type='hidden' name='ok' value='".$get_ok."'>";
	echo "<input type='hidden' name='warning' value='".$get_warning."'>";
	echo "<input type='hidden' name='error' value='".$get_error."'>";
	echo "<input type='hidden' name='tri2' value='".$tri2."'>";
	echo "</form>\n";

	$svn_info=array(); // get_list_wpkg_svn_info($xml_forum);

	foreach ($liste_appli as $key => $row)
	{
		$liste_appli_postes=info_application_postes($row['id_nom_app']); // liste des postes devant avoir l'application donnee
		$liste_appli_status=info_application_rapport($row['id_nom_app']); // liste des informations rapports pour une application donnee
		$liste_appli[$key]["nb_postes"]=count($liste_appli_postes)+0;
		$liste_appli[$key]["NotOk"]=0; $liste_appli[$key]["Ok"]=0; $liste_appli[$key]["MaJ"]=0;
		foreach ($liste_hosts as $host)
		{
			if (array_key_exists($host["nom_poste"],$liste_appli_postes)) // application necessaire
			{
				if (!isset($liste_appli_status[$host["nom_poste"]]["statut_poste_app"])) // aucune info sur l'app
				{
					$liste_appli[$key]["NotOk"]++;
				}
				else if ($liste_appli_status[$host["nom_poste"]]["statut_poste_app"]=="Not Installed") // app non installee
				{
					$liste_appli[$key]["NotOk"]++;
				}
				else if ($liste_appli_status[$host["nom_poste"]]["revision_poste_app"]==$liste_appli[$key]["version_app"])// app installee et bonne version
				{
					$liste_appli[$key]["Ok"]++;
				}
				else // app installee mais mauvaise version
				{
					$liste_appli[$key]["MaJ"]++;
				}
			}
			else // application non necessaire
			{
				if (@$liste_appli_status[$host["nom_poste"]]["statut_poste_app"]=="Installed") // app installee
				{
					$liste_appli[$key]["NotOk"]++;
				}
				else
				{
					$liste_appli[$key]["Ok"]++;
				}
			}
		}
		$name[$key] = strtolower($row['nom_app']);
		$category[$key] = strtolower($row['categorie_app']);
		$compatibilite[$key] = $row['compatibilite_app']+0;
		$revision[$key] = $row['version_app'];
		$nb_postes[$key] = $liste_appli[$key]['nb_postes'];
		$date[$key] = $row['date_modif_app'];
		$NotOk[$key] = $liste_appli[$key]['NotOk'];
		$MaJ[$key] = $liste_appli[$key]['MaJ'];
	}
	switch ($tri)
	{
		case 0:
		array_multisort($name, SORT_ASC, $liste_appli);
		break;
		case 1:
		array_multisort($category, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		case 2:
		array_multisort($compatibilite, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 3:
		array_multisort($name, SORT_DESC, $liste_appli);
		break;
		case 4:
		array_multisort($category, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 5:
		array_multisort($compatibilite, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		case 6:
		array_multisort($date, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 7:
		array_multisort($date, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		case 8:
		array_multisort($nb_postes, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 9:
		array_multisort($nb_postes, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		case 10:
		array_multisort($NotOk, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 11:
		array_multisort($NotOk, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		case 12:
		array_multisort($MaJ, SORT_DESC, $name, SORT_ASC, $liste_appli);
		break;
		case 13:
		array_multisort($MaJ, SORT_ASC, $name, SORT_ASC, $liste_appli);
		break;
		default:
		array_multisort($name, SORT_ASC, $branche, SORT_ASC, $liste_appli);
		break;
	}
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	echo "<th width='300'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==0)
		echo "3";
	else
		echo "0";
	echo "' style='color:".$regular_lnk."'>Nom de l'application</a></th>";
	echo "<th width='120'>Version</th>";
	echo "<th width='120'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==2)
		echo "5";
	else
		echo "2";
	echo "' style='color:".$regular_lnk."'>Compatibilit&#233;</a></th>";
	echo "<th width='150'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==1)
		echo "4";
	else
		echo "1";
	echo "' style='color:".$regular_lnk."'>Cat&#233;gorie</a></th>";
	echo "<th width='70'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==8)
		echo "9";
	else
		echo "8";
	echo "' style='color:".$regular_lnk."'>Nombre de postes</a></th>";
	echo "<th width='70'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==10)
		echo "11";
	else
		echo "10";
	echo "' style='color:".$regular_lnk."'>Postes en erreur</a></th>";
	echo "<th width='70'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==12)
		echo "13";
	else
		echo "12";
	echo "' style='color:".$regular_lnk."'>Postes pas &#224; jour</a></th>";
	echo "<th width='120'><a href='?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tous=".$get_tous."&Appli=".$get_Appli."&tri2=".$tri2."&tri=";
	if ($tri==6)
		echo "7";
	else
		echo "6";
	echo "' style='color:".$regular_lnk."'>Date d'ajout</a></th>";
	echo "<th width='120'>Version SVN</th>";
	echo "</tr>";
	foreach ($liste_appli as $application)
	{
		echo "<tr bgcolor='white' height='30' valing='center'>";
		echo "<td><a href='app_parcs.php?Appli=".$application["id_nom_app"]."' style='color:".$regular_lnk."'>".$application["nom_app"]."</a></td>";
		echo "<td align='center'>".$application["version_app"]."</td>";
		echo "<td align='center' bgcolor='".$wintype_txt."'>";
		switch ($application["compatibilite_app"])
		{
			case 1:
			echo "<img src='images\winxp.png' witdh='20' height='20'>";
			break;
			case 2:
			echo "<img src='images\win7.png' witdh='20' height='20'>";
			break;
			case 3:
			echo "<img src='images\winxp.png' witdh='20' height='20'><img src='images\win7.png' witdh='20' height='20'>";
			break;
			case 4:
			echo "<img src='images\win10.png' witdh='20' height='20'>";
			break;
			case 5:
			echo "<img src='images\winxp.png' witdh='20' height='20'><img src='images\win10.png' witdh='20' height='20'>";
			break;
			case 6:
			echo "<img src='images\win7.png' witdh='20' height='20'><img src='images\win10.png' witdh='20' height='20'>";
			break;
			case 7:
			echo "<img src='images\winxp.png' witdh='20' height='20'><img src='images\win7.png' witdh='20' height='20'><img src='images\win10.png' witdh='20' height='20'>";
			break;
			case 0:
			echo "";
			break;
			default:
			echo "";
			break;
		}
		echo "</td>";
		echo "<td align='center'>".$application["categorie_app"]."</td>";
		echo "<td align='center'>".($application["nb_postes"]+0)."</td>";
		echo "<td align='center'";
		if ($application["NotOk"]>0)
			echo " bgcolor='".$warning_bg."' style='color: ".$warning_txt."'";
		echo ">".$application["NotOk"]."</td>";
		echo "<td align='center'";
		if ($application["MaJ"]>0)
			echo " bgcolor='".$error_bg."' style='color: ".$error_txt."'";
		echo ">".$application["MaJ"]."</td>";
		sscanf($application["date_modif_app"],"%4u-%2u-%2u %2u:%2u:%2uZ",$annee,$mois,$jour,$heure,$minute,$seconde);
		$newTstamp = mktime($heure,$minute,$seconde,$mois,$jour,$annee)+3600;
		$date2=date("d/m/Y à H:i:s", $newTstamp);
		echo "<td align='center'>".$date2."</td>";
		if (isset($svn_info[$application["id"]]))
		{
			$rev=array();
			if (isset ($svn_info[$application["id"]]["stable"]))
			{
				$rev["stable"]=$svn_info[$application["id"]]["stable"]["revision"];
			}
			if (isset ($svn_info[$application["id"]]["test"]))
			{
				$rev["test"]=$svn_info[$application["id"]]["test"]["revision"];
			}
			if (isset ($svn_info[$application["id"]]["XP"]) and get_wpkg_branche_XP()==1)
			{
				$rev["XP"]=$svn_info[$application["id"]]["XP"]["revision"];
			}
			if (in_array($application["revision"],$rev))
			{
				echo "<td align='center' bgcolor='".$ok_bg."' style='color: ".$ok_txt."'>";
			}
			else
			{
				echo "<td align='center' bgcolor='".$warning_bg."' style='color: ".$warning_txt."'>";
			}
			$i=0;
			foreach ($rev as $key=>$value)
			{
				if ($i>0)
					echo "<br>";
				echo $value." (".$key.")";
				$i++;
			}
			echo "</td>";
		}
		else
		{
			echo "<td align='center' bgcolor='".$error_bg."' style='color: ".$error_txt."'>-</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
include ("pdp.inc.php");
?>