<?php

	echo "<h1>Mise en forme de l'interface</h1>";
	// tableau 0
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	if ($page_id==0)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Accueil</th>";
	}
	else
	{
		echo "<th width='220'><a href='mef_accueil.php'>Accueil</a></th>";
	}
	if ($page_id==1)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Définition du thème</th>";
	}
	else
	{
		echo "<th width='220'><a href='mef_gestion.php'>Définition du thème</a></th>";
	}
	if ($page_id==2)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>choix du thème</th>";
	}
	else
	{
		echo "<th width='220'><a href='mef_modification.php'>Choix du thème</a></th>";
	}
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";

?>