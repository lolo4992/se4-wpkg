<?php

$dbhost='localhost';
$dbuser='root';
$dbpass='XXXX';
$dbname='se3wpkg';

function connexion_db_wpkg()
{
	global $dbhost,$dbuser,$dbpass, $dbname;
	$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
	mysqli_set_charset($link, "utf8");
	return $link;
}

function deconnexion_db_wpkg($link)
{
	mysqli_close($link);
}

function info_postes()
{
    $wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT id_poste,nom_poste,OS_poste,date_rapport_poste,ip_poste,mac_address_poste,sha_rapport_poste,file_log_poste,file_rapport_poste,date_modification_poste FROM postes");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_id_poste,$res_nom_poste,$res_OS_poste,$res_date_rapport_poste,$res_ip_poste,$res_mac_address_poste,$res_sha_rapport_poste,$res_file_log_poste,$res_file_rapport_poste,$res_date_modification_poste);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			$tab[$res_nom_poste] = array("id"=>$res_id_poste
										,"nom_poste"=>$res_nom_poste
										,"OS_poste"=>$res_OS_poste
										,"date_rapport_poste"=>$res_date_rapport_poste
										,"IP_poste"=>$res_ip_poste
										,"mac_address_poste"=>$res_mac_address_poste
										,"sha_rapport_poste"=>$res_sha_rapport_poste
										,"file_log_poste"=>$res_file_log_poste
										,"file_rapport_poste"=>$res_file_rapport_poste
										,"date_modification_poste"=>$res_date_modification_poste);
		}

	}
	mysqli_stmt_close($query);
	return $tab;
	deconnexion_db_wpkg($wpkg_link);
}


function info_sha_postes()
{
    $wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT sha_rapport_poste,file_rapport_poste FROM postes");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_sha_rapport_poste,$res_file_rapport_poste);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			$tab[$res_file_rapport_poste] = $res_sha_rapport_poste;
		}

	}
	mysqli_stmt_close($query);
	return $tab;
	deconnexion_db_wpkg($wpkg_link);
}

function liste_applications()
{
	$wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT id_app, id_nom_app, nom_app, version_app, compatibilite_app, categorie_app, prorite_app, reboot_app, sha_app, date_modif_app, user_modif_app, active_app FROM applications");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_id_app, $res_id_nom_app, $res_nom_app, $res_version_app, $res_compatibilite_app, $res_categorie_app, $res_prorite_app, $res_reboot_app, $res_sha_app, $res_date_modif_app, $res_user_modif_app, $res_active_app);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			$tab[hash('sha512',$res_id_nom_app)] = array("id_app"=>$res_id_app
														,"id_nom_app"=>$res_id_nom_app
														,"nom_app"=>$res_nom_app
														,"version_app"=>$res_version_app
														,"compatibilite_app"=>$res_compatibilite_app
														,"categorie_app"=>$res_categorie_app
														,"prorite_app"=>$res_prorite_app
														,"reboot_app"=>$res_reboot_app
														,"sha_app"=>$res_sha_app
														,"date_modif_app"=>$res_date_modif_app
														,"user_modif_app"=>$res_user_modif_app
														,"active_app"=>$res_active_app);
		}

	}
	mysqli_stmt_close($query);
	return $tab;
	deconnexion_db_wpkg($wpkg_link);
}
	
///////////////////////////////////
	
function insert_poste_info_wpkg($info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `postes` (`nom_poste`, `OS_poste`, `date_rapport_poste`, `ip_poste`, `mac_address_poste`, `sha_rapport_poste`, `file_log_poste`, `file_rapport_poste`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($update_query,"ssssssss", $info["nom_poste"], $info["typewin"], $info["datetime"], $info["ip"], $info["mac_address"], $info["sha512"], $info["logfile"], $info["rapportfile"]);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id();
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}


function update_poste_info_wpkg($info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "UPDATE `postes` SET `OS_poste`=?, `date_rapport_poste`=?, `ip_poste`=?, `mac_address_poste`=?, `sha_rapport_poste`=?, `file_log_poste`=?, `file_rapport_poste`=? WHERE `nom_poste`=?");
	mysqli_stmt_bind_param($update_query,"ssssssss", $info["typewin"], $info["datetime"], $info["ip"], $info["mac_address"], $info["sha512"], $info["logfile"], $info["rapportfile"], $info["nom_poste"]);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id();
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

function insert_info_app_poste($id_poste,$id_app,$info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `poste_app` (`id_poste`, `id_app`, `id_nom_app`, `revision_poste_app`, `statut_poste_app`, `reboot_poste_app`) VALUES (?, ?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($update_query,"iisssi", $id_poste, $id_app, $info["id_nom_app"], $info["Revision"], $info["Status"], $info["Reboot"]);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function delete_info_app_poste($id_poste)
{
	$wpkg_link=connexion_db_wpkg();  
	$update_query = mysqli_prepare($wpkg_link, "DELETE FROM `poste_app` WHERE `id_poste`=?");
	mysqli_stmt_bind_param($update_query,"i", $id_poste);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

?>