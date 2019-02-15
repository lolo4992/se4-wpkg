<?
/**
 * Upload d'un xml
 * @Version $Id$
 * @Projet LCS / SambaEdu
 * @auteurs  Laurent Joly
 * @note
 * @Licence Distribue sous la licence GPL
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

	if (isset($_GET["ignoreWawadebMD5"]))
		$ignoreWawadebMD5=$purifier->purify($_GET["ignoreWawadebMD5"])+0;
	else
		$ignoreWawadebMD5=0;
	if (isset($_GET["noDownload"]))
		$noDownload=$purifier->purify($_GET["noDownload"])+0;
	else
		$noDownload=0;

	if (isset($_FILES['appliXml']))
	{
		$uploaddir = $wpkgroot."/tmp2/";
		$appli = basename($_FILES['appliXml']['name']);
		$name_import=date("Ymd")."_".date("His")."_".$appli;
		$uploadfile = $uploaddir.$name_import;
		$hash_xml=hash_file('sha512',$_FILES['appliXml']['tmp_name']);
		if ($ignoreWawadebMD5==0 and $hash_xml!=$hash_xml)
		{
			echo "<h1>Ajout d'une application</h1>\n";
			echo "<h2>Transfert du fichier XML</h2>\n";
			echo "Le fichier '<b>".$appli."</b>' n'a pas &#233;t&#233; transf&#233;r&#233; car le contr&#244;le de hashage a &#233;chou&#233;.<br>\n";
			echo "Hashage du fichier transf&#233;r&#233; : ".$hash_xml."<br>\n";
			echo "Hashage du fichier du d&#233;p&#244;t : ".$hash_xml."<br>\n";
			flush();
		}
		elseif ($_FILES['appliXml']['type']!="text/xml")
		{
			echo "<h1>Ajout d'une application</h1>\n";
			echo "<h2>Transfert du fichier XML</h2>\n";
			echo "Le fichier '<b>".$appli."</b>' n'a pas &#233;t&#233; transf&#233;r&#233; car le type de fichier (".$_FILES['appliXml']['type'].") est incompatible.<br>\n";
			flush();
		}
		elseif (move_uploaded_file($_FILES['appliXml']['tmp_name'], $uploadfile))
		{
			echo "<h1>Ajout d'une application</h1>\n";
			echo "<h2>Transfert du fichier XML</h2>\n";
			echo "Le fichier '<b>".$appli."</b>' a &#233;t&#233; transf&#233;r&#233; avec succ&#232;s dans le r&#233;pertoire <i><u><a onmouseover=\"this.innerHTML='".$uploaddir."';\" onmouseout=\"this.innerHTML='tmp2'; \">tmp2</a></i></u> sous le nom '<b>".$name_import."</b>'.<br>\n";
			flush();


	/* Functions */
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function download_file($fileUrl,$fileTarget,$hashage_md5,$hashage_sha256)
	{
		global $wpkgroot,$wpkgroot2;
		$fileName = basename($fileTarget);
		$direName = dirname($fileTarget);
		$handle = popen("/usr/bin/wget --progress=dot -O '".$wpkgroot."/tmp2/".$fileName."' ".$fileUrl." 2>&1", 'r');
		$download=0;
		$etat=0;
		if (file_exists($wpkgroot2."/".$fileTarget))
		{
			if ($hashage_sha256!="")
			{
				if (hash_file('sha256', $wpkgroot2."/".$fileTarget)!=$hashage_sha256)
				{
					$download=1;
				}
				else
				{
					$return="Le fichier <b>".$fileName."</b> est d&#233;j&#224; pr&#233;sent avec le bon hashage sha256.";
					$etat=1;
					$download=0;
				}
			}
			elseif ($hashage_md5!="")
			{
				if (hash_file('md5', $wpkgroot2."/".$fileTarget)!=$hashage_md5)
				{
					$download=1;
				}
				else
				{
					$return="Le fichier <b>".$fileName."</b> est d&#233;j&#224; pr&#233;sent avec le bon hashage md5.";
					$etat=1;
					$download=0;
				}
			}
			else
			{
				$download=1;
			}
		}
		else
		{
			$download=1;
		}
		if ($download==1)
		{
			if (is_resource($handle))
			{
				$timestamp = microtime_float();
				$ch = "";
				sleep(1);
				while ( !feof($handle) )
				{
					# Pour eviter : Fatal error:  Maximum execution time of 30 seconds exceeded
					set_time_limit(300);
					$car = fread($handle, 1);
					if ( strlen($car) == 0 )
					{
						sleep(1);
					}
					else
					{
						$ch = "$ch$car";
					}
					if ( (microtime_float() - $timestamp) > 1 )
					{
						echo nl2br("$ch");
						$ch = "";
						$timestamp = microtime_float();
						flush();
					}
				}
				echo "$ch";
				flush();
			}
			if (file_exists($wpkgroot."/tmp2/".$fileName))
			{
				if ($hashage_sha256!="")
				{
					if (hash_file('sha256', $wpkgroot."/tmp2/".$fileName)==$hashage_sha256)
					{
						exec("mkdir -p '".$wpkgroot2."/".$direName."'");
						exec("mv '".$wpkgroot."/tmp2/".$fileName."' '".$wpkgroot2."/".$fileTarget."'");
						$return="Le fichier <b>".$fileName."</b> a &#233;t&#233; t&#233;l&#233;charg&#233; avec succ&#232;s et poss&#232;de le bon hashage sha256.";
					}
					else
					{
						$return="Erreur : Le fichier <b>".$fileName."</b> a &#233;t&#233; t&#233;l&#233;charg&#233; avec succ&#232;s et ne poss&#232;de  pas le bon hashage sha256 (".hash_file('sha256', $wpkgroot."/tmp2/".$fileName).").";
					}
				}
				elseif ($hashage_md5!="")
				{
					if (hash_file('md5', $wpkgroot."/tmp2/".$fileName)==$hashage_md5)
					{
						exec("mkdir -p '".$wpkgroot2."/".$direName."'");
						exec("mv '".$wpkgroot."/tmp2/".$fileName."' '".$wpkgroot2."/".$fileTarget."'");
						$return="Le fichier <b>".$fileName."</b> a &#233;t&#233; t&#233;l&#233;charg&#233; avec succ&#232;s et poss&#232;de le bon hashage md5.";
					}
					else
					{
						$return="Le fichier <b>".$fileName."</b> a &#233;t&#233; t&#233;l&#233;charg&#233; avec succ&#232;s et ne poss&#232;de pas le bon hashage md5 (".hash_file('md5', $wpkgroot."/tmp2/".$fileName).").";
					}
				}
				else
				{
					$return="Le fichier <b>".$fileName."</b> a &#233;t&#233; t&#233;l&#233;charg&#233; avec succès et sans v&#233;rification du hashage.";
				}
			}
			$etat=1;
		}
		if ($etat==0)
		{
			$return="Le fichier <b>".$fileName."</b> n'a pas &#233;t&#233; t&#233;l&#233;charg&#233;!";
		}
		return array("etat"=>$etat,"msg"=>$return);
	}
	/* End Functions */


			$xml = new DOMDocument;
			$xml->formatOutput = true;
			$xml->preserveWhiteSpace = false;
			$xml->load($uploadfile);
			/*$element = $xml->documentElement;
			$packages = $xml->documentElement->getElementsByTagName('package');
			$length = $packages->length;*/

			echo "<h2>Téléchargement des fichiers d'installation</h2>\n";
			echo "<table width='80%' align='center'>\n";
			$i=1; $success=0;
			foreach ($xml->getElementsByTagName('package') as $package)
			{
				//$download = $package->getElementsByTagName('download');
				foreach ($package->getElementsByTagName('download') as $dwn)
				{
					$fileUrl = (string) $dwn->getAttribute('url');
					$fileTarget = (string) $dwn->getAttribute('saveto');
					$hashage_md5 = (string) $dwn->getAttribute('md5sum');
					$hashage_sha256 = (string) $dwn->getAttribute('sha256sum');
					echo "<tr><td align='center'>\n";
					echo "<div id='".$i."'>";
					$info_return=download_file($fileUrl,$fileTarget,$hashage_md5,$hashage_sha256);
					echo "</div>";
					if ($info_return["etat"]==1)
					{
						echo "<script language='JavaScript'> document.getElementById('".$i."').innerHTML = '".$info_return["msg"]."'; </script>";
						$success++;
					}
					else
					{
						echo $info_return["msg"]."<br>\n";
					}
					echo "</td></tr>\n";
					$i++;
				}
			}
			echo "<tr><td align='center'>\n";
			echo $success." fichiers t&#233;l&#233;charg&#233;s avec succ&#232;s sur ".($i-1)." fichiers n&#233;cessaires.<br>\n";
			echo "</td></tr>\n";
			echo "</table>\n";

			//configAppli($appli);
		}
		else
		{
			echo "<h1>Ajout d'une application</h1>\n";
			echo "<h2>Transfert du fichier XML</h2>\n";
			echo "Erreur de transfert du fichier '" . $_FILES['appliXml']['tmp_name'] . "' dans $uploadfile.<br>\n";
			echo '<pre>';
			print_r($_FILES);
			echo '</pre>';
		}
	}
	else
	{
?>
<h1>Ajout d'une application</h1>
<form name="formulaire" method="post" action="" enctype="multipart/form-data">
			<table align="center">
				<tr>
					<td>
						Si vous avez déjà placé les fichiers nécessaires à l'application, sur le serveur: <br>
						<input name="noDownload" value="1" type="checkbox"></input>Ne pas télécharger les fichiers d'installation de cette application.<br><br>
						Pour ajouter une application qui n'est pas répertoriée sur le serveur de référence, cocher cette case : <br>
						<input name="ignoreWawadebMD5" value="1" onclick="if(this.checked) alert('Soyez sûr du contenu du fichier xml que vous allez installer sur le serveur!\nAucun contrôle ne sera effectué !\n\nLa sécurité de votre réseau est en jeu !!');" type="checkbox"></input>Ignorer le contrôle de hashage.<br><br>
					</td>
				</tr>
				<tr>
					<td>
						Fichier xml de définition de l'application :<br>
						<input title="chemin du fichier xml" size="70" name="appliXml" type="file"></input><input value="Ajouter cette application !" type="submit"></input>
					</td>
				</tr>
			</table>
		</form>
<?php
	}
	include ("pdp.inc.php");
?>