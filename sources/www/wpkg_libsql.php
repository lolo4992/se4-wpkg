<?php

$dbhost_wpkg='localhost';
$dbuser_wpkg='root';
$dbpass_wpkg='XXXX';
$dbname_wpkg='se3wpkg';

function connexion_db_wpkg()
{
	global $dbhost_wpkg,$dbuser_wpkg,$dbpass_wpkg, $dbname_wpkg;
	$link = mysqli_connect($dbhost_wpkg, $dbuser_wpkg, $dbpass_wpkg, $dbname_wpkg);
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
	deconnexion_db_wpkg($wpkg_link);
	return $tab;
}

function info_poste_parcs($nom_poste)
{
	$wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT pa.nom_parc, pa.nom_parc_wpkg, pa.id_parc, po.id_poste FROM parc pa, postes po, parc_profile pp WHERE po.nom_poste=? and pa.id_parc=pp.id_parc and pp.id_poste=po.id_poste");
	mysqli_stmt_bind_param($query,"s", $nom_poste);
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_nom_parc,$res_nom_parc_wpkg,$res_id_parc,$res_id_poste);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			$tab[$res_nom_parc] = array("id_parc"=>$res_id_parc
										,"id_poste"=>$res_id_poste
										,"nom_parc"=>$res_nom_parc
										,"nom_parc_wpkg"=>$res_nom_parc_wpkg);
		}

	}
	mysqli_stmt_close($query);
	deconnexion_db_wpkg($wpkg_link);
	return $tab;
}

function info_parcs()
{
    $wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT id_parc,nom_parc,nom_parc_wpkg FROM parc");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_id_parc,$res_nom_parc,$res_nom_parc_wpkg);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			$tab[$res_nom_parc]=array("id"=>$res_id_parc
									,"nom_parc"=>$res_nom_parc
									,"nom_parc_wpkg"=>$res_nom_parc_wpkg);
		}

	}
	mysqli_stmt_close($query);
	deconnexion_db_wpkg($wpkg_link);
	return $tab;
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
	deconnexion_db_wpkg($wpkg_link);
	return $tab;
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
			$tab[hash('md5',$res_id_nom_app)]= array("id_app"=>$res_id_app
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
	deconnexion_db_wpkg($wpkg_link);
	return $tab;
}

///////////////////////////////////

function insert_poste_info_wpkg($info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `postes` (`nom_poste`, `OS_poste`, `date_rapport_poste`, `ip_poste`, `mac_address_poste`, `sha_rapport_poste`, `file_log_poste`, `file_rapport_poste`, `date_modification_poste`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
	mysqli_stmt_bind_param($update_query,"ssssssss", $info["nom_poste"], $info["typewin"], $info["datetime"], $info["ip"], $info["mac_address"], $info["sha256"], $info["logfile"], $info["rapportfile"]);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}


function update_poste_info_wpkg($info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "UPDATE `postes` SET `OS_poste`=?, `date_rapport_poste`=?, `ip_poste`=?, `mac_address_poste`=?, `sha_rapport_poste`=?, `file_log_poste`=?, `file_rapport_poste`=?, `date_modification_poste`=NOW() WHERE `nom_poste`=?");
	mysqli_stmt_bind_param($update_query,"ssssssss", $info["typewin"], $info["datetime"], $info["ip"], $info["mac_address"], $info["sha256"], $info["logfile"], $info["rapportfile"], $info["nom_poste"]);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
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

function insert_applications($list_appli)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `applications` (`id_nom_app`, `nom_app`, `version_app`, `compatibilite_app`, `categorie_app`, `prorite_app`, `reboot_app`, `active_app`, `date_modif_app`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
	mysqli_stmt_bind_param($update_query,"sssisiii", $list_appli["id_nom_app"], $list_appli["nom_app"], $list_appli["version_app"], $list_appli["compatibilite_app"], $list_appli["categorie_app"], $list_appli["prorite_app"], $list_appli["reboot_app"], $list_appli["active_app"]);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function update_applications($id_app,$list_appli)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "UPDATE `applications` SET `id_nom_app`=?, `nom_app`=?, `version_app`=?, `compatibilite_app`=?, `categorie_app`=?, `prorite_app`=?, `reboot_app`=?, `active_app`=?, `date_modif_app`=NOW() WHERE id_app=?");
	mysqli_stmt_bind_param($update_query,"sssisiiii", $list_appli["id_nom_app"], $list_appli["nom_app"], $list_appli["version_app"], $list_appli["compatibilite_app"], $list_appli["categorie_app"], $list_appli["prorite_app"], $list_appli["reboot_app"], $list_appli["active_app"],$id_app);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function delete_dependances()
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "DELETE FROM `dependance`");
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function insert_dependance($id_appli,$id_required)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `dependance` (`id_app`, `id_app_requise`) VALUES (?, ?)");
	mysqli_stmt_bind_param($update_query,"ii", $id_appli, $id_required);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function insert_journal_app($id_appli,$info)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `journal_app` (`id_app`, `operation_journal_app`, `user_journal_app`, `date_journal_app`, `xml_journal_app`, `sha_journal_app`) VALUES (?, ?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($update_query,"isssss", $id_appli, $info["operation_journal_app"], $info["user_journal_app"], $info["date_journal_app"], $info["xml_journal_app"], $info["sha_journal_app"]);
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
}

function update_sha_xml_journal($url_xml_tmp)
{
	$wpkg_link=connexion_db_wpkg();
	$query = mysqli_prepare($wpkg_link, "SELECT ja1.id_app, ja1.xml_journal_app, ja1.user_journal_app, ja1.id_journal_app FROM (journal_app ja1)
										LEFT JOIN (journal_app ja2)
										ON (ja1.id_app=ja2.id_app and ja1.id_journal_app<ja2.id_journal_app)
										where ja2.id_journal_app is NULL and ja1.id_app!=0 and ja1.operation_journal_app='add'
										ORDER BY ja1.`id_app` ASC");
	mysqli_stmt_execute($query);
	mysqli_stmt_bind_result($query,$res_id_app, $res_xml_journal_app, $res_user_journal_app, $res_id_journal_app);
	mysqli_stmt_store_result($query);
	$num_rows=mysqli_stmt_num_rows($query);
	$tab=array();
	if ($num_rows!=0)
	{
		while (mysqli_stmt_fetch($query))
		{
			if (file_exists($url_xml_tmp.$res_xml_journal_app))
			{
				$sha512_file=hash_file('sha512',$url_xml_tmp.$res_xml_journal_app);
				$update_query = mysqli_prepare($wpkg_link, "UPDATE `journal_app` SET `sha_journal_app`=? WHERE `id_journal_app`=?");
				mysqli_stmt_bind_param($update_query,"si", $sha512_file, $res_id_journal_app);
				mysqli_stmt_execute($update_query);
				mysqli_stmt_close($update_query);
				$update_query = mysqli_prepare($wpkg_link, "UPDATE `applications` SET `sha_app`=?, user_modif_app=? WHERE `id_app`=?");
				mysqli_stmt_bind_param($update_query,"ssi", $sha512_file, $res_user_journal_app, $res_id_app);
				mysqli_stmt_execute($update_query);
				mysqli_stmt_close($update_query);
			}
		}

	}
	mysqli_stmt_close($query);
	deconnexion_db_wpkg($wpkg_link);
}

function truncate_table_profiles()
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "TRUNCATE TABLE `applications_profile`");
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	$update_query = mysqli_prepare($wpkg_link, "TRUNCATE TABLE `parc_profile`");
	mysqli_stmt_execute($update_query);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

function insert_application_profile($type_entite,$id_entite,$id_appli)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `applications_profile` (`type_entite`, `id_entite`, `id_appli`) VALUES (?, ?, ?)");
	mysqli_stmt_bind_param($update_query,"sii", $type_entite, $id_entite, $id_appli);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

function insert_parc_profile($id_poste,$id_parc)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `parc_profile` (`id_poste`, `id_parc`) VALUES (?, ?)");
	mysqli_stmt_bind_param($update_query,"ii", $id_poste, $id_parc);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

function delete_parc_profile($id_poste,$id_parc)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "DELETE FROM `parc_profile` WHERE `id_poste`=? AND `id_parc`=?");
	mysqli_stmt_bind_param($update_query,"ii", $id_poste, $id_parc);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

function insert_parc($nom_parc)
{
	$wpkg_link=connexion_db_wpkg();
	$update_query = mysqli_prepare($wpkg_link, "INSERT INTO `parc` (`nom_parc`) VALUES (?)");
	mysqli_stmt_bind_param($update_query,"s", $nom_parc);
	mysqli_stmt_execute($update_query);
	$id=mysqli_insert_id($wpkg_link);
	mysqli_stmt_close($update_query);
	deconnexion_db_wpkg($wpkg_link);
	return $id;
}

?>