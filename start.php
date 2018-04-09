<?php
	include("connection.php");
	include("data.php");
	
	if(isset($_SESSION["hangar"])) unset($_SESSION["hangar"]);
	if(isset($_SESSION["shop"])) unset($_SESSION["shop"]);
	if(isset($_SESSION["equipment"])) unset($_SESSION["equipment"]);
	if(isset($_SESSION["construction"])) unset($_SESSION["construction"]);
	if(isset($_SESSION["equipment_all"])) unset($_SESSION["equipment_all"]);
	
	$id = $_SESSION["id"];
	if(!$characterleker = mysqli_query($_SESSION["conn"], "SELECT * FROM characters WHERE ownerid='$id'")) die("Karakterek lekérése sikertelen");
	
	if(!mysqli_num_rows($characterleker)) header("location:newcharacter.php");
	else
	{
		$_SESSION["from"] = "start.php";
		print "
			<HTML>
				<HEAD>
					<link rel='stylesheet' type='text/css' href='shell_style.css'>
					<link rel='stylesheet' type='text/css' href='start.css'>
					<TITLE>Dokk - SkyXplore</TITLE>
			<BODY>
				<DIV class='logout'><A class='logout' href='http://www.saphyra.pe.hu/logout.php'>Kijelentkezés</A></DIV>
				<DIV class='background'>
					<DIV class='head'>Karakterek:</DIV>
		";
		while($charactertomb = mysqli_fetch_assoc($characterleker))
		{
			$charactername = $charactertomb["charname"];
			$characterid = $charactertomb["charid"];
			$credit = $charactertomb["credit"];
			$diamond = $charactertomb["diamond"];
			
			$equipment = equipmentdownload($characterid);
			$ship = shipobject($charactertomb, $equipment);

			print "
				<DIV class='ship'>
					<DIV class='iden'>Név: $charactername<BR>ID: $characterid</DIV>
					<DIV class='shipdata'>Megbízó: $ship->company<BR>Hajó: $ship->shipname Szint: $ship->level Pontszám: $ship->score Rajszám: $ship->squadronnum</DIV>
					<DIV class='money'>Kredit: $credit<BR>Gyémánt: $diamond</DIV>
					<DIV class='activity'><A class='activity' href='activity.php?activity=rename&charid=$characterid'>Átnevezés</A><BR><A class='activity' href='activity.php?activity=delete&charid=$characterid'>Törlés</A></DIV>
					<DIV class='start'><A class='start' href='hangar.php?charid=$characterid'>Karakter kiválasztása</A></DIV>
				</DIV>
			";
			unset($equipment);
			unset($ship);
		}
	}
?>
		</DIV>
		<DIV class='new'>
			<A class='new' href='newcharacter.php'>Új karakter létrehozása</A>
		</DIV>
		
	
</BODY>
</HTML>

<?php
	function shipobject($chardata, $equipment)
	{
		$ship = new emptyclass;
		
		$ship->level = $chardata["level"];
		
		$shipdata = unserialize($chardata["ship"]);
		$squadrons = unserialize($chardata["squadrons"]);
		
		$shiptype = $shipdata->itemid;
		$ship->company = $shipdata->companyname;
		$ship->shipname = $shipdata->name;
		$ship->score = scorecount($equipment);
		$ship->squadronnum = count($squadrons);
		
		return $ship;
	}
?>