<?php
	include("connection.php");

	$_SESSION["from"] = "hangar.php";
	if(isset($_SESSION["game"])) unset($_SESSION["game"]);
	if(isset($_SESSION["gamedata"])) unset($_SESSION["gamedata"]);
	
	if(isset($_GET["charid"])) $_SESSION["character"]["charid"] = $_GET["charid"];
	$charid = $_SESSION["character"]["charid"];
	$ownerid = $_SESSION["id"];
	if(!$characterleker = mysqli_query($_SESSION["conn"], "SELECT * FROM characters WHERE charid='$charid' AND ownerid='$ownerid'")) die("karakter betöltése sikertelen");
	if(!mysqli_num_rows($characterleker)) die("Karakterid nem azonosítható");
	$charactertomb = mysqli_fetch_assoc($characterleker);
	
	$_SESSION["character"]["charname"] = $charactertomb["charname"];
	$_SESSION["character"]["credit"] = $charactertomb["credit"];
	$_SESSION["character"]["diamond"] = $charactertomb["diamond"];
	$_SESSION["character"]["level"] = $charactertomb["level"];
	$_SESSION["character"]["company"] = $charactertomb["company"];
	$_SESSION["character"]["ship"] = unserialize($charactertomb["ship"]);
	$_SESSION["character"]["squadrons"] = unserialize($charactertomb["squadrons"]);
	$_SESSION["character"]["equipment"] = equipmentdownload($charid);
	$_SESSION["character"]["construction"] = unserialize($charactertomb["construction"]);
	$_SESSION["character"]["skill"] = unserialize($charactertomb["skill"]);
	$_SESSION["character"]["ammo"] = unserialize($charactertomb["ammo"]);
	$_SESSION["character"]["groups"] = unserialize($charactertomb["groups"]);
	
	$ship = new emptyclass;
	
	$shipid = $_SESSION["character"]["ship"]->itemid;
	$ship->name = $_SESSION["character"]["ship"]->name;
	$ship->level = $_SESSION["character"]["ship"]->level;
	$ship->corehull = $_SESSION["character"]["ship"]->corehull;
	$ship->score = partscorecount($_SESSION["character"]["equipment"], "ship");
	$ship->hull = hullcount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->shield = shieldcount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->shieldrecharge = shieldrechargecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->energy = energycount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->batteryrecharge = batteryrechargecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->energyregen = energyregencount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->energyusage = energyusagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->hulldamage = hulldamagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->shielddamage = shielddamagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->squadrondamage = squadrondamagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->rockethulldamage = rockethulldamagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	$ship->rocketshielddamage = rocketshielddamagecount($shipid, $_SESSION["character"]["equipment"], "ship");
	settype($ship->rocketshielddamage, "integer");
	settype($ship->rockethulldamage, "integer");
?>

<HTML>
	<HEAD>
		<TITLE>Hangár - SkyXplore</TITLE>
		<link rel='stylesheet' type='text/css' href='shell_style.css'>
		<link rel='stylesheet' type='text/css' href='hangar.css'>
	</HEAD>
<BODY>

<?php
	$charname = $_SESSION["character"]["charname"];
	$charid = $_SESSION["character"]["charid"];
	$credit = $_SESSION["character"]["credit"];
	$diamond = $_SESSION["character"]["diamond"];
	
	print "
		<DIV class='background'>
			<DIV class='container1'>
				<DIV class='iden'>Név: $charname<BR>ID: $charid</DIV>
				
				<DIV class='equipment'><A href='equipment.php' class='equipment'>Felszerelés</A></DIV>
				<DIV class='construction'><A href='construction.php' class='construction'>Tárgyak</A></DIV>
				<DIV class='shop'><A href='shop.php' class='shop'>Bolt</A></DIV>
				<DIV class='start'><A href='battlefield/load.php' class='start'>Felszállás</A></DIV>
			</DIV>
			<DIV class='ship'>
				<DIV class='level'>Szint: $ship->level Pont: $ship->score</DIV>
				<DIV class='name'>$ship->name</DIV>
				<DIV class='data'>
					<DIV class='corehull'><U>Magburkolat:</U> $ship->corehull</DIV>
					<DIV class='shiphull'><U>Burkolat:</U> $ship->hull</DIV>
					<DIV class='shipshield'><U>Pajzserő:</U> $ship->shield (+$ship->shieldrecharge/kör)</DIV>
					<DIV class='shipenergy'><U>Energia:</U> $ship->energy (max. +$ship->batteryrecharge/kör)</DIV>
					<DIV class='shipenergyregen'><U>Energitaermelés:</U> $ship->energyregen/kör</DIV>
					<DIV class='shipenergyusage'><U>Energiafelhasználás:</U> $ship->energyusage/kör</DIV>
					<DIV class='shipdamage'><U>Sebzés:</U> $ship->hulldamage (burkolat) / $ship->shielddamage (pajzs) / $ship->squadrondamage (raj)</DIV>
					<DIV class='shiprocketdamage'><U>Rakétasebzés:</U> $ship->rockethulldamage (burkolat) / $ship->rocketshielddamage (pajzs)</DIV>
				</DIV>
				<DIV class='sq'>Rajok:</DIV>
	";
			$szam = 1;
			foreach($_SESSION["character"]["squadrons"] as $name=>$ertek)
			{
				if($szam > 2)
				{
					print "<BR>";
					$szam = 1;
				}
				$class = "squad$szam";
				$szam++;
				
				
				$squadron = $ertek;
				$squadid = $squadron->squadronid;
				$squadron->score = partscorecount($_SESSION["character"]["equipment"], $squadid);
				$squadron->corehull = $_SESSION["character"]["squadrons"]["$squadid"]->corehull;
				$squadron->hull = hullcount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->shield = shieldcount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->shieldrecharge = shieldrechargecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->energy = energycount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->energyusage = energyusagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->hulldamage = hulldamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->shielddamage = shielddamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				$squadron->squadrondamage = squadrondamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadid");
				if(!$squadron->group) $group = "Nincs csapatba sorolva";
				else $group = $_SESSION["character"]["groups"]["$squadron->group"]->groupname;
				
				print "
					<DIV class='$class'>
						<DIV class='squadname'>$squadron->squadronname</DIV>
						<DIV class='squadlevel'>Szint: $squadron->level Pont: $squadron->score</DIV>
						<DIV class='squadcorehull'><U>Magburkolat:</U> $squadron->corehull</DIV>
						<DIV class='squadhull'><U>Burkolat:</U> $squadron->hull</DIV>
						<DIV class='squadshield'><U>Pajzs:</U> $squadron->shield (+$squadron->shieldrecharge/kör)</DIV>
						<DIV class='squadenergy'><U>Energia:</U> $squadron->energy</DIV>
						<DIV class='squadenergyusage'><U>Energiafelhasználás:</U> $squadron->energyusage</DIV>
						<DIV class='squaddamage'><U>Sebzés:</U> $squadron->hulldamage (burkolat) / $squadron->shielddamage (pajzs) / $squadron->squadrondamage (raj)</DIV>
						<DIV class='group'><U>Csapat:</U> $group</DIV>
					</DIV>
				";
			}
	print "
			</DIV>
			<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
			<DIV class='money'>Kredit: $credit<BR>Gyémánt: $diamond</DIV>
			<DIV class='back'><A href='start.php' class='back'>Vissza</A></DIV>
		</DIV>
	";
?>


</BODY>
</HTML>