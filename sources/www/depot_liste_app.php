<?php
/**
 * liste des applications disponibles sur le depot
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
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

	if (isset($_GET["id_depot"]))
		$id_depot=$purifier->purify($_GET["id_depot"])+0;
	else
		$id_depot=0;
	if (isset($_GET["tri"]))
		$tri=$purifier->purify($_GET["tri"])+0;
	else
		$tri=0;

	$info_depot=info_depot();
	$liste_applications=liste_applications();
	if (!array_key_exists($id_depot,$info_depot))
	{
		$depot_principal=info_depot_principal();
		$id_depot=$depot_principal[0]["id_depot"];
	}

	$page_id=1;
	include("depot_top.php");

	$list_app=info_depot_appli($id_depot);	
	$statut_depot=array("Ok"=>0,"MaJ"=>0,"Total"=>0);
	if ($list_app)
	{
		$i=0;
		
		foreach ($list_app as $key=>$la)
		{
			$md5=hash('md5',$la["id_nom_app"]);
			if (array_key_exists($md5,$liste_applications))
			{
				$info_app=$liste_applications[$md5];
				if ($info_app["sha_app"]==$la["sha_xml"])
				{
					$list_app[$key]["etat_wpkg"]="A jour";
					$statut_depot["Ok"]++;
				}
				else
				{
					$list_app[$key]["etat_wpkg"]="Xml différent<br>".$info_app["version_app"];
					$statut_depot["MaJ"]++;
				}
				$list_app[$key]["user_modif_app"]=$info_app["user_modif_app"];
				$list_app[$key]["date_modif_app"]=$info_app["date_modif_app"];
			}
			else
			{
				$list_app[$key]["user_modif_app"]="";
				$list_app[$key]["date_modif_app"]="";
				$list_app[$key]["etat_wpkg"]="Non installé";
			}
			$statut_depot["Total"]++;

			$tri_nom_app[$i]=$list_app[$i]["nom_app"];
			$tri_categorie[$i]=$list_app[$i]["categorie"];
			$tri_version[$i]=$list_app[$i]["version"];
			$tri_compatibilite[$i]=$list_app[$i]["compatibilite"];
			$tri_date[$i]=$list_app[$i]["date"];
			if ($list_app[$i]["etat_wpkg"]=="A jour")
				$tri_etat_wpkg[$i]=2;
			elseif ($list_app[$i]["etat_wpkg"]=="Non installé")
				$tri_etat_wpkg[$i]=0;
			else
				$tri_etat_wpkg[$i]=1;
			$tri_date_modif_app[$i]=$list_app[$i]["date_modif_app"];
			$tri_user_modif_app[$i]=$list_app[$i]["user_modif_app"];
			$i++;
		}
		switch ($tri)
		{
			case 0:
			array_multisort($tri_nom_app, SORT_ASC, $list_app);
			break;
			case 1:
			array_multisort($tri_nom_app, SORT_DESC, $list_app);
			break;
			case 2:
			array_multisort($tri_categorie, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 3:
			array_multisort($tri_categorie, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 4:
			array_multisort($tri_version, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 5:
			array_multisort($tri_version, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 6:
			array_multisort($tri_compatibilite, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 7:
			array_multisort($tri_compatibilite, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 8:
			array_multisort($tri_date, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 9:
			array_multisort($tri_date, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 10:
			array_multisort($tri_etat_wpkg, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 11:
			array_multisort($tri_etat_wpkg, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 12:
			array_multisort($tri_date_modif_app, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 13:
			array_multisort($tri_date_modif_app, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 14:
			array_multisort($tri_user_modif_app, SORT_ASC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			case 15:
			array_multisort($tri_user_modif_app, SORT_DESC, $tri_nom_app, SORT_ASC, $list_app);
			break;
			default:
			array_multisort($tri_nom_app, SORT_ASC, $list_app);
			break;
		}
	}

	echo "<form method='get' action='?tri=".$tri."'>";
	echo "<table align='center' bgcolor='black'>\n";
	echo "<tr style='color:white'>\n";
	echo "<th>Dépôt</th>";
	echo "<th>Nombre<br>d'applications</th>";
	echo "<th>Applications<br>à jour</th>";
	echo "<th>Applications<br>pas à jour</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='white'>\n";
	echo "<td>";
	echo "<select name='id_depot'>";
	foreach ($info_depot as $i_d)
	{
		echo "<option value='".$i_d["id_depot"]."'";
		if ($id_depot==$i_d["id_depot"])
			echo " selected";
		echo ">".$i_d["nom_depot"];
		if ($i_d["depot_principal"]==1)
		{
			echo " (principal)";
		}
		echo "</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "<td align='center'>".$statut_depot["Total"]."</td>";
	echo "<td align='center' bgcolor='".$ok_bg."' style='color:".$ok_txt."'>".$statut_depot["Ok"]."</td>";
	echo "<td align='center' bgcolor='".$error_bg."' style='color:".$error_txt."'>".$statut_depot["MaJ"]."</td>";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td colspan='4' align='center'>";
	echo "<input type='submit' name='action' value='Valider'>";
	echo "</td>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";

	echo "<form>";
	echo "<table align='center'>\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "Si vous avez déjà placé les fichiers nécessaires à l'application, sur le serveur: <br>\n";
	echo "<input name='noDownload' value='1' type='checkbox'>Ne pas télécharger les fichiers d'installation de cette application.<br><br>\n";
	echo "Pour ajouter une application qui n'est pas répertoriée sur le serveur de référence, cocher cette case : <br>\n";
	echo "<input name='ignoreWawadebMD5' value='1' onclick=\"if(this.checked) alert('Soyez sûr du contenu du fichier xml que vous allez installer sur le serveur!<\\nAucun contrôle ne sera effectué !\\n\\nLa sécurité de votre réseau est en jeu !!');\" type='checkbox'>Ignorer le contrôle de hashage.<br><br>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr style='color: white' height='30' valing='center'>";
	echo "<th colspan='10'>Nom du dépôt : ".$info_depot[$id_depot]["nom_depot"]."</th>";
	echo "</tr>\n";
	echo "<tr style='color: white' height='30' valing='center'>";
	echo "<th width='20'></th>";
	echo "<th width='300'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==0)
		echo "1";
	else
		echo "0";
	echo "'>Application</a></th>";
	echo "<th width='200'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==2)
		echo "3";
	else
		echo "2";
	echo "'>Catégorie</a></th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==4)
		echo "5";
	else
		echo "4";
	echo "'>Version</a></th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==6)
		echo "7";
	else
		echo "6";
	echo "'>Compatibilité</a></th>";
	echo "<th width='100'>Infos</th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==8)
		echo "9";
	else
		echo "8";
	echo "'>Date du fichier</a></th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==10)
		echo "11";
	else
		echo "10";
	echo "'>Etat sur le serveur</a></th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==12)
		echo "13";
	else
		echo "12";
	echo "'>Installé le</a></th>";
	echo "<th width='100'><a href='?id_depot=".$id_depot."&tri=";
	if ($tri==14)
		echo "15";
	else
		echo "14";
	echo "'>Installé par</a></th>";
	echo "</tr>\n";

	foreach ($list_app as $la)
	{
		if ($la["etat_wpkg"]=="A jour")
		{
			$bg=$ok_bg;
			$txt=$ok_txt;
			$lnk=$ok_lnk;
		}
		elseif ($la["etat_wpkg"]=="Non installé")
		{
			$bg=$unknown_bg;
			$txt=$unknown_txt;
			$lnk=$unknown_lnk;
		}
		else
		{
			$bg=$error_bg;
			$txt=$error_txt;
			$lnk=$error_lnk;
		}
		echo "<tr bgcolor='".$bg."' height='30' valing='center' style='color:".$txt."'>";
		echo "<td align='center' valign='center'>";
		echo "<input type='checkbox' id='appli[]' name='appli[]' value='".$la["id_depot_applications"]."'>"; 
		echo "</td>";
		echo "<td align='center' valign='center'><a href='".$la["url_xml"]."' target='xml' style='color:".$lnk."'>".$la["nom_app"]."</a></td>";
		echo "<td align='center' valign='center'>".$la["categorie"]."</td>";
		echo "<td align='center' valign='center'>".$la["version"]."</td>";
		echo "<td align='center' valign='center' bgcolor='".$wintype_txt."'>";
		switch ($la["compatibilite"])
		{
			case 1:
			echo "<img src='images/winxp.png' witdh='20' height='20'>";
			break;
			case 2:
			echo "<img src='images/win7.png' witdh='20' height='20'>";
			break;
			case 3:
			echo "<img src='images/winxp.png' witdh='20' height='20'><img src='images/win7.png' witdh='20' height='20'>";
			break;
			case 4:
			echo "<img src='images/win10.png' witdh='20' height='20'>";
			break;
			case 5:
			echo "<img src='images/winxp.png' witdh='20' height='20'><img src='images/win10.png' witdh='20' height='20'>";
			break;
			case 6:
			echo "<img src='images/win7.png' witdh='20' height='20'><img src='images/win10.png' witdh='20' height='20'>";
			break;
			case 7:
			echo "<img src='images/winxp.png' witdh='20' height='20'><img src='images/win7.png' witdh='20' height='20'><img src='images/win10.png' witdh='20' height='20'>";
			break;
			case 0:
			echo "";
			break;
			default:
			echo "";
			break;
		}
		echo "</td>";
		echo "<td align='center' valign='center'><a href='".$la["url_log"]."' target='log' style='color:".$lnk."'>".$la["branche"]."</a></td>";
		echo "<td align='center' valign='center'>".$la["date"]."</td>";
		echo "<td align='center' valign='center'>".$la["etat_wpkg"]."</td>";
		echo "<td align='center' valign='center'>".$la["date_modif_app"]."</td>";
		echo "<td align='center' valign='center'>".$la["user_modif_app"]."</td>";
		echo "</tr>\n";
	}
	echo "</table></form>\n";

	include ("pdp.inc.php");
?>