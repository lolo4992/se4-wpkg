<?php
/**
 * apercu de la mise en forme par defaut
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


$mise_en_forme_info=mise_en_forme_info();
foreach ($mise_en_forme_info as $key=>$value)
{
	${$key}=$value["default"];
}
echo "<h1>Aperçu de la mise en forme par défaut</h1>";

	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr style='color:white'>";
	echo "<th width='300'><a style='color:".$regular_lnk."'>Application</a></th>";
	echo "<th width='120'><a style='color:".$regular_lnk."'>Version</a></th>";
	echo "<th width='120'>Compatibilit&#233;</th>";
	echo "<th width='150'><a style='color:".$regular_lnk."'>Cat&#233;gorie</a></th>";
	echo "<th width='120'><a style='color:".$regular_lnk."'>Statut</a></th>";
	echo "<th width='300'>Demand&#233; par</th>";
	echo "</tr>\n";

	$list_app=array();
	$list_app[] = array("status_app"=>0
						,"id_nom_app"=>"7zip"
						,"nom_app"=>"7-zip 18"
						,"revision_poste_app"=>"18.01"
						,"compatibilite_app"=>7
						,"categorie_app"=>"Bureautique"
						,"statut_poste_app"=>"Installée"
						,"parc"=>array(array("nom_parc"=>"tous_les_postes")));
	$list_app[] = array("status_app"=>2
						,"id_nom_app"=>"7za"
						,"nom_app"=>"7-Zip autonome"
						,"revision_poste_app"=>"442"
						,"compatibilite_app"=>7
						,"categorie_app"=>"Système"
						,"statut_poste_app"=>"Non Installée"
						,"parc"=>array(array("nom_parc"=>"tous_les_postes")));
	$list_app[] = array("status_app"=>4
						,"id_nom_app"=>"adnarn"
						,"nom_app"=>"AdnArn"
						,"revision_poste_app"=>"1"
						,"compatibilite_app"=>7
						,"categorie_app"=>"SVT"
						,"statut_poste_app"=>"Non installée");
	$list_app[] = array("status_app"=>1
						,"id_nom_app"=>"AdobeAir"
						,"nom_app"=>"Adobe Air"
						,"revision_poste_app"=>"29.0.0.112"
						,"compatibilite_app"=>7
						,"categorie_app"=>"Système"
						,"statut_poste_app"=>"Installée"
						,"parc"=>array(array("nom_parc"=>"tous_les_postes"))
						,"poste"=>"amphi-01");
	$list_app[] = array("status_app"=>4
						,"id_nom_app"=>"airy"
						,"nom_app"=>"Airy"
						,"revision_poste_app"=>"-"
						,"compatibilite_app"=>7
						,"categorie_app"=>"SVT"
						,"statut_poste_app"=>"Inconnu");

	foreach ($list_app as $nom_poste=>$lp)
	{
		$affichage=0;
		switch ($lp["status_app"])
		{
			case 0:
				$bg=$ok_bg;
				$lnk=$ok_lnk;
				$txt=$ok_txt;
				$affichage=1;
				break;
			case 1:
				$bg=$error_bg;
				$lnk=$error_lnk;
				$txt=$error_txt;
				$affichage=1;
				break;
			case 2:
				$bg=$warning_bg;
				$lnk=$warning_lnk;
				$txt=$warning_txt;
				$affichage=1;
				break;
			case 4:
				$bg=$unknown_bg;
				$lnk=$unknown_lnk;
				$txt=$unknown_txt;
				$affichage=1;
				break;
		}
		if ($affichage==1)
		{
			echo "<tr bgcolor='".$bg."' style='color: ".$txt."'>";
			echo "<td align='center'><a style='color: ".$lnk."'>".$lp["nom_app"]."</a></td>";
			echo "<td align='center'>".$lp["revision_poste_app"]."</td>";
			echo "<td align='center' bgcolor='".$wintype_txt."'>";
			switch ($lp["compatibilite_app"])
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
			echo "<td align='center'>".$lp["categorie_app"]."</td>";
			echo "<td align='center'>".$lp["statut_poste_app"]."</td>";
			echo "<td align='center'>";
			$i=0;
			if (is_array($lp["parc"]))
			{
				foreach (@$lp["parc"] as $parc_app)
				{
					if ($i<>0)
						echo ", ";
					echo "<a style='color: ".$lnk."'>".$parc_app["nom_parc"]."</a>";
					$i++;
				}
			}
			if (is_array($lp["depend"]))
			{
				foreach ($lp["depend"] as $depend_app)
				{
					if ($i<>0)
						echo ", ";
					echo "<a style='color: ".$lnk."'>".$depend_app["nom_app"]."</a>";
					$i++;
				}
			}
			if (@$lp["poste"])
			{
				if ($i<>0)
					echo ", ";
				echo $lp["poste"];
				$i++;
			}
			echo "</td>";
			echo "</tr>\n";
		}
	}
	echo "</table><br>\n";
	echo "<br>\n";
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' align='justify'>\n";
		echo "<td bgcolor='".$dep_entite_bg."' style='color:".$dep_entite_txt."' width='250'>";
		echo "D&#233;ploiement demand&#233; pour ce poste";
		echo "</td>\n";
		echo "<td bgcolor='".$dep_parc_bg."' style='color:".$dep_parc_txt."' width='250'>";
		echo "D&#233;ploiement demand&#233; pour un parc du poste";
		echo "</td>\n";
		echo "<td bgcolor='".$dep_depend_bg."' style='color:".$dep_depend_txt."' width='250'>";
		echo "D&#233;ploiement demand&#233; par une d&#233;pendance";
		echo "</td>\n";
		echo "<td bgcolor='".$dep_no_bg."' style='color:".$dep_no_txt."' width='250'>";
		echo "D&#233;ploiement non demand&#233; pour ce poste";
		echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white'>\n";
		echo "<td align='left' width='200'>";
		echo "<table width='100%'><tr>";
		echo "<td align='left' width='30'>";
		echo "<input type='checkbox'/>";
		echo "</td>";
		echo "<td align='left' width='*' bgcolor='".$dep_entite_bg."' style='color:".$dep_entite_txt."'>&nbsp;poste-01</td>";
		echo "</tr></table>";
		echo "</td>\n";
		echo "<td align='left' width='200'>";
		echo "<table width='100%'><tr>";
		echo "<td align='left' width='30'>";
		echo "<input type='checkbox'/>";
		echo "</td>";
		echo "<td align='left' width='*' bgcolor='".$dep_parc_bg."' style='color:".$dep_parc_txt."'>&nbsp;poste-02</td>";
		echo "</tr></table>";
		echo "</td>\n";
		echo "<td align='left' width='200'>";
		echo "<table width='100%'><tr>";
		echo "<td align='left' width='30'>";
		echo "<input type='checkbox'/>";
		echo "</td>";
		echo "<td align='left' width='*' bgcolor='".$dep_depend_bg."' style='color:".$dep_depend_txt."'>&nbsp;poste-03</td>";
		echo "</tr></table>";
		echo "</td>\n";
		echo "<td align='left' width='200'>";
		echo "<table width='100%'><tr>";
		echo "<td align='left' width='30'>";
		echo "<input type='checkbox'/>";
		echo "</td>";
		echo "<td align='left' width='*' bgcolor='".$dep_no_bg."' style='color:".$dep_no_txt."'>&nbsp;poste-04</td>";
		echo "</tr></table>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

include ("pdp.inc.php");
?>