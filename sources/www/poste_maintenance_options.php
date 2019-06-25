<?php
/**
 * Maintenance d'un poste gestion des options wpkg
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

	if (isset($_POST["action"]))
		$post_action=$purifier->purify($_POST["action"]);
	else
		$post_action="";
	if (isset($_POST["parametre"]))
		$post_parametre=$_POST["parametre"];
	else
		$post_parametre=array();
	
	function delete_ini_poste($id_host)
	{
		$filepath = "/var/se3/unattended/install/wpkg/ini/".$id_host.".ini";
		unlink($filepath);
	}
	function create_ini_poste($id_host)
	{
		$filepath = "/var/se3/unattended/install/wpkg/ini/".$id_host.".ini";
		$liste_options=array();
		$liste_options[]=array("name"=>"debug","etat"=>"false","description"=>"Permet d'avoir des logs plus détaillés.");
		$liste_options[]=array("name"=>"logdebug","etat"=>"false","description"=>"Pour avoir des logs en temps réel sur le serveur.");
		$liste_options[]=array("name"=>"force","etat"=>"false","description"=>"Pour tester la présence ou l'absence effective de chaque appli sur le poste.");
		$liste_options[]=array("name"=>"forceinstall","etat"=>"false","description"=>"Pour installer ou désinstaller les applications même si les tests 'check' sont vérifiés.");
		$liste_options[]=array("name"=>"nonotify","etat"=>"false","description"=>"Pour ne pas avertir l'utilisateur logué des opérations de wpkg.");
		$liste_options[]=array("name"=>"dryrun","etat"=>"false","description"=>"Pour que wpkg simule une exécution mais n'installe ou ne désinstalle rien.");
		$liste_options[]=array("name"=>"nowpkg","etat"=>"false","description"=>"Pour ne pas exécuter wpkg sur le poste.");
		$liste_options[]=array("name"=>"noforcedremove","etat"=>"false","description"=>"Pour ne pas retirer les applis zombies de la base de données du poste si les commandes de remove échouent.");
		$new_file = fopen($filepath, "w");
		foreach ($liste_options as $tmp_option)
		{
			fwrite($new_file, $tmp_option["name"]."=".$tmp_option["etat"]." ' ".$tmp_option["description"]."\n");
		}
		fclose($new_file);
	}
	function update_ini_poste($id_host,$data)
	{
		$liste_options[]=array("name"=>"debug","description"=>"Permet d'avoir des logs plus détaillés.");
		$liste_options[]=array("name"=>"logdebug","description"=>"Pour avoir des logs en temps réel sur le serveur.");
		$liste_options[]=array("name"=>"force","description"=>"Pour tester la présence ou l'absence effective de chaque appli sur le poste.");
		$liste_options[]=array("name"=>"forceinstall","description"=>"Pour installer ou désinstaller les applications même si les tests 'check' sont vérifiés.");
		$liste_options[]=array("name"=>"nonotify","description"=>"Pour ne pas avertir l'utilisateur logué des opérations de wpkg.");
		$liste_options[]=array("name"=>"dryrun","description"=>"Pour que wpkg simule une exécution mais n'installe ou ne désinstalle rien.");
		$liste_options[]=array("name"=>"nowpkg","description"=>"Pour ne pas exécuter wpkg sur le poste.");
		$liste_options[]=array("name"=>"noforcedremove","description"=>"Pour ne pas retirer les applis zombies de la base de données du poste si les commandes de remove échouent.");
		$filepath = "/var/se3/unattended/install/wpkg/ini/".$id_host.".ini";
		$new_file = fopen($filepath, "w");
		foreach ($liste_options as $tmp_option)
		{
			if (in_array($tmp_option["name"],$data))
				$tmp_etat="true";
			else
				$tmp_etat="false";
			fwrite($new_file, $tmp_option["name"]."=".$tmp_etat." ' ".$tmp_option["description"]."\n");
		}
		fclose($new_file);
	}

	if ($post_action=="Retour sans modification")
	{
		header("Location: poste_maintenance.php?tri2=".$tri2."&id_host=".$id_host."&ok=".$get_ok."&warning=".$get_warning."&error=".$get_error);
		exit;
	}
	elseif ($post_action=="Valider les modifications")
	{
		if (is_array($post_parametre))
			update_ini_poste($id_host,$post_parametre);
	}
	elseif ($post_action=="Supprimer le fichier ini")
	{
		delete_ini_poste($id_host);
	}
	elseif ($post_action=="Générer le fichier ini")
	{
		create_ini_poste($id_host);
	}

	echo "<form method='get' action=''>\n";
	$page_id=2;
	include ("poste_top.php");
	echo "</form>\n";

	$filepath = "/var/se3/unattended/install/wpkg/ini/".$id_host.".ini";
	$liste_options=array();
	if (file_exists($filepath))
	{
		$file_open = fopen($filepath,"r");
		while($row = fgets($file_open))
		{
			list( $tmp_name, $temp_other) = explode( "=", $row, 2);
			list( $tmp_etat, $tmp_description) = explode( "'", $temp_other, 2);
			$liste_options[]=array("name"=>trim($tmp_name),"etat"=>trim($tmp_etat),"description"=>trim($tmp_description));
		}
		fclose($file_open);
	}

	echo "<form method='post' action='?tri2=".$tri2."&id_host=".$id_host."&ok=".$get_ok."&warning=".$get_warning."&error=".$get_error."'>";
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='black'>";
		echo "<td align='center' colspan='3' width='260'>";
		echo "<input type='submit' name='action' value='Valider les modifications'>";
		echo "</td>";
		echo "<th align='center' width='260'>";
		echo "<input type='submit' name='action' value='";
		if ($liste_options)
			echo "Supprimer le fichier ini";
		else
			echo "Générer le fichier ini";
		echo "'>";
		echo "</th>";
		echo "<td align='center' width='260'>";
		echo "<input type='submit' name='action' value='Retour sans modification'>";
		echo "</td>";
	echo "</tr>\n";
	echo "<tr bgcolor='black' style='color:white'>";
	echo "<td align='center' width='20'>";
	echo "</td>";
	echo "<td align='center' width='120'>";
	echo "Paramètre";
	echo "</td>";
	echo "<td align='center' width='120'>";
	echo "Etat actuel";
	echo "</td>";
	echo "<td align='center' width='520' colspan='2'>";
	echo "Description";
	echo "</td>";
	echo "</tr>\n";

	if ($liste_options)
	{
		foreach ($liste_options as $option)
		{
			echo "<tr bgcolor='#FFFFFF' style='color:#000000'>";
			echo "<td align='center'>";
			echo "<input type='checkbox' id='parametre[]' name='parametre[]' value='".$option["name"]."'";
			if ($option["etat"]=="true")
				echo " checked";
			echo " />";
			echo "</td>";
			echo "<td align='center'>".$option["name"]."</td>";
			echo "<td align='center'";
			if ($option["etat"]=="true")
				echo " style='color: ".$ok_txt."' bgcolor='".$ok_bg."'";
			else
				echo " style='color: ".$warning_txt."' bgcolor='".$warning_bg."'";
			echo ">".$option["etat"]."</td>";
			echo "<td colspan='2'>".$option["description"]."</td>";
			echo "</tr>\n";
		}
	}
	else
	{
		echo "<tr bgcolor='#FFFFFF' style='color:#000000'>";
		echo "<td align='center' colspan='5'>";
		echo "Aucune option définie";
		echo "</td>";
		echo "</tr>\n";
	}

	echo "</table>\n";
	echo "<br>\n";
	echo "</form>";

	include ("pdp.inc.php");
?>