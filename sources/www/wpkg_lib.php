<?php

function extract_app($get_Appli,$url_packages,$url_extract)
{
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
	//$comment=$xml2->createComment(" Fichier genere par SambaEdu. Ne pas modifier. Il contient ".($length-1)." applications. ");
	//$root->appendChild($comment);
	$packages2 = $xml2->documentElement->getElementsByTagName('package');

	$return=0;

	foreach ($packages as $package)
	{
		if ($package->getAttribute('id')==$get_Appli)
		{
			$node=$xml2->importNode($package, true);
			$xml2->documentElement->appendChild($node);
			$return=1;
		}
	}

	$xml2->save($url_extract);

	return $return;
}

?>