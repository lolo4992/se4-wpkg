<?php
	echo "<h1>Mise à jour des applications</h1>";

	// tableau 0
	echo "<table cellspadding='2' cellspacing='1' border='0' align='center' bgcolor='black'>\n";
	echo "<tr bgcolor='white' height='30' valing='center'>";
	if ($page_id==0)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Accueil</th>";
	}
	else
	{
		echo "<th width='220'><a href='depot_accueil.php'>Accueil</a></th>";
	}
	if ($page_id==1)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Ajout semi-automatique depuis un dépôt</th>";
	}
	else
	{
		echo "<th width='220'><a href='depot_liste_app.php'>Ajout semi-automatique depuis un dépôt</a></th>";
	}
	if ($page_id==2)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Ajout manuel</th>";
	}
	else
	{
		echo "<th width='220'><a href='depot_upload_app.php'>Ajout manuel</a></th>";
	}
	if ($page_id==3)
	{
		echo "<th width='220' bgcolor='black' style='color:white'>Mise à jour automatique</th>";
	}
	else
	{
		echo "<th width='220'><a href='depot_maj_auto.php'>Mise à jour automatique</a></th>";
	}
	echo "</tr>\n";
	echo "</table>\n";
	echo "<br>\n";

?>