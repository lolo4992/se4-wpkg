<?php
/*
	echo header('Content-type: text/xml');
	$url_packages = "/var/se3/unattended/install/wpkg/packages.xml";
	echo file_get_contents($url_packages);
*/

	include("wpkg_lib.php");
	include("wpkg_libsql.php");

	$liste_applications=liste_applications();

	$xml = new DOMDocument;
	$xml->formatOutput = true;
	$xml->preserveWhiteSpace = false;
	$xml->load($url_packages);
	$element = $xml->documentElement;
	$packages = $xml->documentElement->getElementsByTagName('package');
	$length = $packages->length;

	$xml2 = new DOMDocument;
	$xml2->formatOutput = true;
	$xml2->preserveWhiteSpace = false;
	$root=$xml2->createElement("packages");
	$xml2->appendChild($root);
	$packages2 = $xml2->documentElement->getElementsByTagName('package');

	$i=0;
	foreach ($packages as $package)
	{
		if (array_key_exists(hash('md5',$package->getAttribute('id')),$liste_applications))
		{
			$node=$xml2->importNode($package, true);
			$xml2->documentElement->appendChild($node);
			$i++;
		}
	}

	$comment=$xml2->createComment(" Fichier genere par SambaEdu. Ne pas modifier. Il contient ".($i)." applications. ");
	$root->appendChild($comment);

	$xml2->encoding = 'UTF-8';
	echo header('Content-type: text/xml');
	echo $xml2->saveXML();;
?>