<?php

	// recuperation des donnees
	$liste_appli=liste_applications(); // liste des applications
	$liste_parcs=info_parcs(); // liste des parcs
	$liste_postes_parc=info_parc_postes($get_parc); // liste des postes du parc donne
	asort($liste_parcs);
	if (!array_key_exists($get_parc,$liste_parcs))
		header("Location: parc_statuts.php");

	$info_parc=array("MaJ"=>0,"Not_Ok"=>0,"Ok"=>0,"Total"=>0);
	$info_poste=array();

	foreach ($liste_postes_parc as $poste)
	{
		$tmp_rapport=info_poste_rapport($poste["nom_poste"]);
		$tmp_appli=info_poste_applications($poste["nom_poste"]);
		$info_poste[$poste["nom_poste"]]["info"]=array("typewin"=>$poste["OS_poste"],"status"=>0,"nb_app"=>count($tmp_appli),"logfile"=>$poste["file_log_poste"],"ip"=>$poste["ip_poste"],"mac"=>$poste["mac_address_poste"],"datetime"=>$poste["date_rapport_poste"]);
		$info_poste[$poste["nom_poste"]]["status"]=array("MaJ"=>0,"Not_Ok-"=>0,"Ok"=>0,"Not_Ok+"=>0);
		$info_poste[$poste["nom_poste"]]["info"]["date"]=date('d/m/Y',strtotime($info_poste[$poste["nom_poste"]]["info"]["datetime"]));
		$info_poste[$poste["nom_poste"]]["info"]["time"]=date('H:i:s',strtotime($info_poste[$poste["nom_poste"]]["info"]["datetime"]));
		foreach ($liste_appli as $key_app=>$tmp_app)
		{
			if (isset($tmp_appli[$tmp_app["id_app"]]))
			{
				if (!isset($tmp_rapport[$key_app]))
				{
					$info_poste[$poste["nom_poste"]]["status"]["Not_Ok-"]++;
				}
				else if ($tmp_rapport[$key_app]["statut_poste_app"]=="Not Installed")
				{
					$info_poste[$poste["nom_poste"]]["status"]["Not_Ok-"]++;
				}
				else if ($tmp_rapport[$key_app]["revision_poste_app"]==$tmp_appli[$tmp_app["id_app"]]["info_app"]["version_app"])
				{
					$info_poste[$poste["nom_poste"]]["status"]["Ok"]++;
				}
				else
				{
					$info_poste[$poste["nom_poste"]]["status"]["MaJ"]++;
				}
			}
			elseif (@$tmp_rapport[$key_app]["statut_poste_app"]=="Installed")
			{
				$info_poste[$poste["nom_poste"]]["status"]["Not_Ok+"]++;
			}
		}
		if ($info_poste[$poste["nom_poste"]]["status"]["Not_Ok+"]+$info_poste[$poste["nom_poste"]]["status"]["Not_Ok-"]>0)
		{
			$info_poste[$poste["nom_poste"]]["info"]["status"]=2;
			$info_parc["Not_Ok"]++;
		}
		else if ($info_poste[$poste["nom_poste"]]["status"]["MaJ"]>0)
		{
			$info_poste[$poste["nom_poste"]]["info"]["status"]=1;
			$info_parc["MaJ"]++;
		}
		else
		{
			$info_poste[$poste["nom_poste"]]["info"]["status"]=0;
			$info_parc["Ok"]++;
		}
		$info_parc["Total"]++;
	}

	echo "<h1>Gestion des parcs</h1>\n";

	echo "<input type='hidden' name='tri2' value='".$tri2."'>";

// tableau 0
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	if ($page_id==1)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Etat du parc</th>";
	}
	else
	{
		echo "<th width='220'><a href='parc_statuts.php?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tri2=".$tri2."' style='color:".$regular_lnk."'>Etat du parc</a></th>";
	}
	if ($page_id==2)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Applications du parc</th>";
	}
	else
	{
		echo "<th width='220'><a href='parc_appli.php?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tri2=".$tri2."' style='color:".$regular_lnk."'>Applications du parc</a></th>";
	}
	if ($page_id==3)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Gestion</th>";
	}
	else
	{
		echo "<th width='220'><a href='parc_maintenance.php?parc=".$get_parc."&warning=".$get_warning."&error=".$get_error."&ok=".$get_ok."&tri2=".$tri2."' style='color:".$regular_lnk."'>Gestion</a></th>";
	}
	echo "</tr>\n";
	echo "<tr bgcolor='black' height='30' valing='center'>";
	echo "<th>";
	echo "<select name='parc'>";
	foreach ($liste_parcs as $l_parc)
	{
		echo "<option value='".$l_parc["nom_parc"]."'";
		if ($l_parc["nom_parc"]==$get_parc)
			echo " selected";
		echo ">".$l_parc["nom_parc"]."</option>";
	}
	echo "</select>";
	echo "</td>\n";
	echo "<th></th>";
	echo "<th><input type='submit' value='Valider' name='Valider'></th>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";

	// tableau 1
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	echo "<th width='150'>Nombre de postes</th>";
	echo "<th width='150'>Postes &#224; jour</th>";
	echo "<th width='150'>Postes en erreur</th>";
	echo "<th width='150'>Postes pas &#224; jour</th>";
	echo "</tr>\n";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	echo "<td align='center'>".$info_parc["Total"]."</td>";
	echo "<td align='center'";
		echo " bgcolor='".$ok_bg."' style='color:".$ok_txt."'";
	echo ">".$info_parc["Ok"]."<br>";
		echo "<select name='ok'>";
		echo "<option value='1'";
		if ($get_ok==1)
			echo " selected";
		echo ">Afficher</option>";
		echo "<option value='0'";
		if ($get_ok==0)
			echo " selected";
		echo ">Masquer</option>";
		echo "</select>";
	echo "</td>";
	echo "<td align='center'";
		echo " bgcolor='".$warning_bg."' style='color:".$warning_txt."'";
	echo ">".@$info_parc["Not_Ok"]."<br>";
		echo "<select name='warning'>";
		echo "<option value='1'";
		if ($get_warning==1)
			echo " selected";
		echo ">Afficher</option>";
		echo "<option value='0'";
		if ($get_warning==0)
			echo " selected";
		echo ">Masquer</option>";
		echo "</select>";
	echo "</td>";
	echo "<td align='center'";
		echo " bgcolor='".$error_bg."' style='color:".$error_txt."'";
	echo ">".$info_parc["MaJ"]."<br>";
		echo "<select name='error'>";
		echo "<option value='1'";
		if ($get_error==1)
			echo " selected";
		echo ">Afficher</option>";
		echo "<option value='0'";
		if ($get_error==0)
			echo " selected";
		echo ">Masquer</option>";
		echo "</select>";
	echo "</td>";
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";
?>