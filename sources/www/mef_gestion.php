<?php
/**
 * definition de la mise en forme personnalisee
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


	$page_id=1;
	include("mef_top.php");

	echo "<table align='center'>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a onclick=\"popuprecherche('mef_test.php','mef','scrollbars=no,width=800,height=500');\" style='color:".$regular_lnk.";'>Visualiser un apercu de la mise en forme personnalisée</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
		echo "<td align='center'>";
		echo "<a onclick=\"popuprecherche('mef_actuel.php','mef','scrollbars=no,width=800,height=500');\" style='color:".$regular_lnk.";'>Visualiser un apercu de la mise en forme actuelle</a>";
		echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

 	if (isset($_POST["action"]))
		$post_action=$purifier->purify($_POST["action"]);
	else
		$post_action="";

	if ($post_action=="Valider les modifications")
	{
		if (isset($_POST["new"]))
		{
			if (is_array($_POST["new"]))
			{
				$new=$_POST["new"];
			}
			else
			{
				$new=array();
			}
		}
		else
		{
			$new=array();
		}
		foreach ($new as $key=>$value)
		{
			if (ctype_xdigit($value)==1 and strlen($value)==6)
			{
				update_mef($key,1,$value);
			}
			else
			{
				echo "<b>".$key." est non modifiable : la variable doit comporter 6 chiffres hexadécimaux.</b><br>";
			}
		}
		echo "<br>";
	}

	$liste_mef=mise_en_forme_info();

	echo "<form method='post' action=''>\n";
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr style='color:white'>";
		echo "<th width='200'>Intitul&#233;</th>";
		echo "<th width='150'>Nom variable</th>";
		echo "<th width='150'>Couleur actuelle</th>";
		echo "<th width='150'>Couleur personnalisée</th>";
		echo "<th width='150'>Couleur par défaut</th>";
	echo "</tr>\n";
	// WARNING
	echo "<tr bgcolor='".$liste_mef["warning_bg"]["default"]."' style='color: ".$liste_mef["warning_txt"]["default"]."'>";
	echo "<th colspan='5'>Parc / poste / application en erreur</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="warning_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="warning_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="warning_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// ERROR
	echo "<tr bgcolor='".$liste_mef["error_bg"]["default"]."' style='color: ".$liste_mef["error_txt"]["default"]."'>";
	echo "<th colspan='5'>Parc / poste / application pas à jour</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="error_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="error_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="error_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// OK
	echo "<tr bgcolor='".$liste_mef["ok_bg"]["default"]."' style='color: ".$liste_mef["ok_txt"]["default"]."'>";
	echo "<th colspan='5'>Parc / poste / application à jour</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="ok_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="ok_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="ok_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "</tr>\n";
	// Unknown
	echo "<tr bgcolor='".$liste_mef["unknown_bg"]["default"]."' style='color: ".$liste_mef["unknown_txt"]["default"]."'>";
	echo "<th colspan='5'>Parc / poste / application avec un état inconnu</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="unknown_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="unknown_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="unknown_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// dep_entite
	echo "<tr bgcolor='".$liste_mef["dep_entite_bg"]["default"]."' style='color: ".$liste_mef["dep_entite_txt"]["default"]."'>";
	echo "<th colspan='5'>Le déploiement dépend de l'entité sélectionnée</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="dep_entite_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="dep_entite_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="dep_entite_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// dep_parc
	echo "<tr bgcolor='".$liste_mef["dep_parc_bg"]["default"]."' style='color: ".$liste_mef["dep_parc_txt"]["default"]."'>";
	echo "<th colspan='5'>Le déploiement dépend d'un autre parc</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="dep_parc_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="dep_parc_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="dep_parc_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// dep_depend
	echo "<tr bgcolor='".$liste_mef["dep_depend_bg"]["default"]."' style='color: ".$liste_mef["dep_depend_txt"]["default"]."'>";
	echo "<th colspan='5'>Le déploiement dépend d'une autre application</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="dep_depend_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="dep_depend_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="dep_depend_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	// dep_no
	echo "<tr bgcolor='".$liste_mef["dep_no_bg"]["default"]."' style='color: ".$liste_mef["dep_no_txt"]["default"]."'>";
	echo "<th colspan='5'>Le déploiement n'est pas demandé</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#CCCCCC' style='color: #000000'>";
		$variable_mef="dep_no_bg";
		echo "<td>Couleur de fond</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#BBBBBB' style='color: #000000'>";
		$variable_mef="dep_no_txt";
		echo "<td>Couleur des caractères</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='#AAAAAA' style='color: #000000'>";
		$variable_mef="dep_no_lnk";
		echo "<td>Couleur des liens</th>";
		echo "<td align='center'>".$variable_mef."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["value"]."</th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["test"]."<br><input name='new[".$variable_mef."]' value='".$liste_mef[$variable_mef]["test"]."' maxlength='6' size='6'></th>";
		echo "<td align='center'>".$liste_mef[$variable_mef]["default"]."</th>";
	echo "</tr>\n";
	echo "<tr style='color:white'>";
		echo "<th colspan='2'><input type='submit' name='action' value='Annuler les modifications'></th>";
		echo "<th></th>";
		echo "<th colspan='2'><input type='submit' name='action' value='Valider les modifications'></th>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";

include ("pdp.inc.php");
?>