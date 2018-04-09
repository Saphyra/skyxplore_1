<?php
	include("connection.php");
	include("shipload.php");
	
	shipload("player", $_SESSION["character"], "friend");
	
	$_SESSION["gamedata"]["allianceability"]["friend"]["mfaa1"] = new emptyclass;
	$_SESSION["gamedata"]["allianceability"]["friend"]["mfaa1"]->actualactive = 0;
	$_SESSION["gamedata"]["allianceability"]["friend"]["mfaa1"]->effect = 0;
	$_SESSION["gamedata"]["allianceability"]["friend"]["mfaa1"]->owner = 0;
	
	$_SESSION["gamedata"]["allianceability"]["enemy"]["mfaa1"] = new emptyclass;
	$_SESSION["gamedata"]["allianceability"]["enemy"]["mfaa1"]->actualactive = 0;
	$_SESSION["gamedata"]["allianceability"]["enemy"]["mfaa1"]->effect = 0;
	$_SESSION["gamedata"]["allianceability"]["enemy"]["mfaa1"]->owner = 0;
	
	if(1) header("location:gameconfiguration.php");
?>