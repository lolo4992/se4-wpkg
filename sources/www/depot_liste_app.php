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
	include "wpkg_lib_admin.php";

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

	if (isset($_POST["action"]))
		$post_action=$purifier->purify($_POST["action"]);
	else
		$post_action="";
	if (isset($_POST["noDownload"]))
		$noDownload=$purifier->purify($_POST["noDownload"])+0;
	else
		$noDownload=0;

	if ($post_action=="Ajouter les applications") // mode ajout applications
	{
		$app_success=0;
		$app_error=0;
		if (isset($_POST["appli"]))
		{
			$id_app_upload=$_POST["appli"];
		}
		else
		{
			$id_app_upload=array();
		}
		$uploaddir = $wpkgroot."/tmp2/";
		if ($id_app_upload)
		{
			$i=1;
			foreach ($id_app_upload as $id_a_u)
			{
				$info_app=info_depot_id_appli($id_a_u);

				$appli = basename($info_app["url_xml"]);
				$name_import=pathinfo($appli,PATHINFO_FILENAME)."_".date("Ymd")."_".date("His").".".pathinfo($appli,PATHINFO_EXTENSION);
				$uploadfile = $uploaddir.$name_import;

				// Chargement du fichier avec curl
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $info_app["url_xml"]);
				/*
				// configuration du proxy necessaire pour https
				curl_setopt($ch, CURLOPT_PROXY, "ip_proxy");
				curl_setopt($ch, CURLOPT_PROXYPORT, "port_proxy");
				*/
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$data=curl_exec($ch);
				//$error = curl_error($ch);
				//echo "error : ".$error."<br>";
				curl_close ($ch);

				echo "<h1>Ajout de l'application : ".$info_app["nom_app"]."</h1>\n";
				// Ecriture des donnes dans le fichier xml sur le serveur
				if (pathinfo($appli,PATHINFO_EXTENSION)=="xml")
				{
					$file = fopen($uploadfile, "w+");
					fputs($file, $data);
					fclose($file);
					$hash_xml=hash_file('sha512',$uploadfile);
					$finfo=finfo_open(FILEINFO_MIME_TYPE);
				}
				else
				{
					echo "<h2>Transfert du fichier XML</h2>\n";
					echo "Mauvaise extenstion de fichier!<br>\n";
					$app_error++;
				}

				if ($hash_xml!=$info_app["sha_xml"]) //$ignoreWawadebMD5==0 impossible d'ignorer le controle hashage
				{
					echo "<h2>Transfert du fichier XML</h2>\n";
					echo "Le fichier '<b>".$appli."</b>' n'a pas &#233;t&#233; transf&#233;r&#233; car le contr&#244;le de hashage a &#233;chou&#233;.<br>\n";
					echo "Hashage du fichier transf&#233;r&#233; : ".$hash_xml."<br>\n";
					echo "Hashage du fichier du d&#233;p&#244;t : ".$hash_xml."<br>\n";
					flush();
					$app_error++;
				}
				elseif (strpos("xml",finfo_file($finfo, $uploadfile)) !== false)
				{
					echo "<h2>Transfert du fichier XML</h2>\n";
					echo "Le fichier '<b>".$appli."</b>' n'a pas &#233;t&#233; transf&#233;r&#233; car le type de fichier (".finfo_file($finfo, $uploadfile).") est incompatible.<br>\n";
					flush();
					$app_error++;
				}
				else
				{
					echo "<h2>Transfert du fichier XML</h2>\n";
					echo "Le fichier '<b>".$appli."</b>' a &#233;t&#233; transf&#233;r&#233; avec succ&#232;s dans le r&#233;pertoire <i><u><a onmouseover=\"this.innerHTML='".$uploaddir."';\" onmouseout=\"this.innerHTML='tmp2'; \">tmp2</a></i></u> sous le nom '<b>".$name_import."</b>'.<br>\n";
					flush();

					$xml = new DOMDocument;
					$xml->formatOutput = true;
					$xml->preserveWhiteSpace = false;
					$xml->load($uploadfile);

					echo "<h2>Téléchargement des fichiers d'installation</h2>\n";
					echo "<table width='80%' align='center'>\n";
					$ii=0; $success=0; $list_Appli=array();
					foreach ($xml->getElementsByTagName('package') as $package)
					{
						$list_Appli[] = (string) $package->getAttribute('id');
						if ($noDownload==0)
						{
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
								elseif ($info_return["etat"]==-1)
								{
									echo "<script language='JavaScript'> document.getElementById('".$i."').innerHTML = '".$info_return["msg"]."'; </script>";
								}
								else
								{
									echo $info_return["msg"]."<br>\n";
								}
								echo "</td></tr>\n";
								$i++;
								$ii++;
							}
						}
					}
					echo "<tr><td align='center'>\n";
					if ($noDownload==0)
					{
						echo $success." fichiers t&#233;l&#233;charg&#233;s avec succ&#232;s sur ".($ii)." fichiers n&#233;cessaires.<br>\n";
					}
					else
					{
						echo "Option NoDownload activ&#233;e. Aucun fichier t&#233;l&#233;charg&#233;.<br>\n";
					}
					echo "</td></tr>\n";
					echo "</table>\n";

					// si tout est telecharge... import du paquet dans packages.xml
					echo "<h2>Importation du xml dans packages.xml et mise &#224; jour de la liste des applications.</h2>\n";
					echo "<table width='80%' align='center'>\n";
					if ($ii==$success)
					{
						echo "<tr><td align='center'>\n";
						foreach ($list_Appli as $get_Appli)
						{
							remove_app($get_Appli,$url_packages);
							echo "Suppression de l'ancien paquet (".$get_Appli.").<br>\n";
						}
						echo "</td></tr>\n";
						echo "<tr><td align='center'>\n";
						add_app($liste_applications,$url_packages,$uploadfile,$login);
						echo "Ajout des nouveaux paquets achev&#233;.";
						echo "</td></tr>\n";
						echo "<tr><td align='center'>\n";
						echo "";
						echo "</td></tr>\n";
						$app_success++;
					}
					else
					{
						echo "<tr><td align='center'>\n";
						echo "Op&#233;ration annul&#233;e. Erreur sur le t&#233;l&#233;chargement des fichiers.";
						echo "</td></tr>\n";
						$app_error++;
					}
					echo "</table>\n";
				}
			}
		}
		echo "<h1>".$app_success." application";
		if ($app_success>1)
			echo "s";
		echo " ajoutée";
		if ($app_success>1)
			echo "s";
		echo " avec succès. ".$app_error." application";
		if ($app_error>1)
			echo "s";
		echo " en erreur.</h1>";
	}
	else // mode liste application
	{
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

		echo "<form method='post'>";
		echo "<table align='center'>\n";
		echo "<tr>\n";
		echo "<td>\n";
		echo "Si vous avez déjà placé les fichiers nécessaires à l'application, sur le serveur: <br>\n";
		echo "<input name='noDownload' value='1' type='checkbox'>Ne pas télécharger les fichiers d'installation de cette application.<br><br>\n";
		/* le depot doit certifier correctement les xml par l'usage de hashage
		echo "Pour ajouter une application qui n'est pas répertoriée sur le serveur de référence, cocher cette case : <br>\n";
		echo "<input name='ignoreWawadebMD5' value='1' onclick=\"if(this.checked) alert('Soyez sûr du contenu du fichier xml que vous allez installer sur le serveur!<\\nAucun contrôle ne sera effectué !\\n\\nLa sécurité de votre réseau est en jeu !!');\" type='checkbox'>Ignorer le contrôle de hashage.<br><br>\n";
		*/
		echo "<input type='submit' name='action' value='Ajouter les applications'>";
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
	}

	include ("pdp.inc.php");
?>