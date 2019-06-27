<?php
/**
 * Importation des applications depuis les differents depots enregistres
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
 */
	// loading libs and init
	include("wpkg_libsql.php");

	$liste_depot=info_depot();

	truncate_depot_applications();
	foreach ($liste_depot as $ld)
	{
		$xml = new DOMDocument;
		$xml->formatOutput = true;
		$xml->preserveWhiteSpace = false;
		$xml->load($ld["url_depot"]);
		$element = $xml->documentElement;
		$branchs = $xml->documentElement->getElementsByTagName('branch');
		$length = $packages->length;
		foreach ($branchs as $branch)
		{
			$packages = $branch->getElementsByTagName('package');
			foreach ($packages as $package)
			{
				$tab=array();
				$tab = array("id_nom_app"=>(string) $package->getAttribute('id')
							,"nom_app"=>(string) $package->getAttribute('name')
							,"xml"=>(string) $package->getAttribute('xml')
							,"url_xml"=>(string) $package->getAttribute('url')
							,"sha_xml"=>(string) $package->getAttribute('hash')
							,"url_log"=>(string) $package->getAttribute('log')
							,"categorie"=>(string) $package->getAttribute('category')
							,"compatibilite"=>(string) $package->getAttribute('compatibilite')
							,"version"=>(string) $package->getAttribute('revision')
							,"branche"=>(string) $branch->getAttribute('id')
							,"date"=>(string) $package->getAttribute('date')
							,"id_depot"=>$ld["id_depot"]);
				insert_info_pkg_depot($tab);
			}
		}
	}
?>