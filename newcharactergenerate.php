<?php
	include("connection.php");
	include("data.php");
	
	if(!isset($_POST["charactername"]))
	{
		$_SESSION["charactercompany"] = $_GET["company"];
	}
	else
	{
		$charactername = $_POST["charactername"];
		if(!$usernameleker = mysqli_query($_SESSION["conn"], "SELECT * FROM characters WHERE charname='$charactername'")) die("karakternevek lekérése sikertelen");
			
		if(mysqli_num_rows($usernameleker))
		{
			$errorn = "A karakternév foglalt.";
		}
		else
		{
			startgenerate($_GET["company"], $_POST["charactername"]);
			
			if(1)
			{
				unset($_SESSION["charactercompany"]);
				header("location:start.php");
				
			}
			exit;
		}
		
	}
?>

<HTML>
	<HEAD>
		<TITLE>Karakternév</TITLE>
		<link rel="stylesheet" type="text/css" href="shell_style.css">
		<link rel="stylesheet" type="text/css" href="newcharactergenerate.css">
	</HEAD>
<BODY>

<DIV class='base'>
	<H1>Karakternév megadása</H1>
	<?php if(isset($errorn)) print "<H1>$errorn<H1>"; ?>
	<FORM method='POST'>

		<TABLE>
			<TR>
				<TD><H2>Karakternév:</H2></TD>
				<TD><INPUT type='text' name='charactername' required='required'></TD>
			</TR>
			<TR>
				<TD class='submit' align='center' colspan='2'><INPUT type='submit' name='submit' value='Karakter létrehozása'></TD>
			</TR>
		</TABLE>

	</FORM>
</DIV>
</BODY>
</HTML>

<?php
	function startgenerate($company, $charactername)
	{
		
		if(!$idleker = mysqli_query($_SESSION["conn"], "SELECT charid FROM characters")) die("Karakteridk lekérése sikertelen");
		$ids[] = "";
		while($idtomb = mysqli_fetch_assoc($idleker))
		{
			$ids[] = $idtomb["charid"];
		}
		
		$character = new emptyclass;
		
		$keres["level"] = 1;
		$keres["type"] = "ship";
		$keres["itemtype"] = "$company";
		
		$itemindex = objectsearch($keres, $_SESSION["data"]["items"]);
		$shipdata = $_SESSION["data"]["items"]["$itemindex"];
		$charactertomb["ship"] = $shipdata;
		
		$startequipment = startequipment($charactertomb["ship"]);
		$charactertomb["equipment"] = $startequipment["equipment"];
		$charactertomb["ammo"] = $startequipment["ammo"];
		
		$squadronresult = squadrongenerate($charactertomb["ship"], $charactertomb["equipment"]);
		$charactertomb["equipment"] = $squadronresult["equipment"];
		$charactertomb["squadrons"] = $squadronresult["squadrons"];
		
		foreach($_SESSION["data"]["items"] as $name=>$ertek)
		{
			if(property_exists($ertek, "construction")) $construction["$name"] = 0;
		}
		
		$charactertomb["construction"] = $construction;
		
		$skillids = array("emfp", "emfa1", "emfa2", "pdmp", "pdma1", "pdma2", "idfp", "idfa1", "idfa2", "mfap", "mfaa1", "mfaa2", "gaap", "gaaa1", "gaaa2", "crip", "cria1", "cria2");
		
		foreach($skillids as $skillid)
		{
			$skilldata = new emptyclass;
			
			$skilldata->level = 0;
			$skilldata->parts = 0;
			$skilldata->itemid = $_SESSION["data"]["items"]["$skillid"]->itemid;
			$skilldata->name = $_SESSION["data"]["items"]["$skillid"]->name;
			$skill["$skillid"] = $skilldata;
		}
		
		$charactertomb["skill"] = $skill;
		
		$group["no"] = new group("no", "Csapatba nem sorolt");
		foreach($charactertomb["squadrons"] as $squadronid=>$squadron)
		{
			$member[] = $squadronid;
		}
		$group["no"]->members = serialize($member);
		$group["no"]->membernum = count($charactertomb["squadrons"]);
		$group = serialize($group);
		
		$charid = idgenerate("char", $ids, 8);
		$ownerid = $_SESSION["id"];
		$credit = 100000000;
		$diamond = 100000000;
		$level = $shipdata->level;
		$ship = serialize($charactertomb["ship"]);
		$ammo = serialize($charactertomb["ammo"]);
		$squadrons = serialize($charactertomb["squadrons"]);
		$construction = serialize($charactertomb["construction"]);
		$skill = serialize($charactertomb["skill"]);
		
		
		equipmentupload($charactertomb["equipment"], $charid);
		if(1) if(!mysqli_query($_SESSION["conn"], "INSERT INTO characters (ownerid, charid, charname, company, credit, diamond, level, ship, ammo, squadrons, construction, skill, groups) VALUES('$ownerid', '$charid', '$charactername', '$company', '$credit', '$diamond', '$level', '$ship', '$ammo', '$squadrons', '$construction', '$skill', '$group')")) die("karakter létrehozása sikertelen");
	}
	
	function startequipment($ship)
	{
		$equipment[] = new equipment($ship->itemid, 1, "ship");
		
		$cannonkeres["level"] = $ship->maxcannonlevel;
		$cannonkeres["itemtype"] = "cannon";
		$cannonid = objectsearch($cannonkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->cannonslot; $szam++)
		{
			$equipment[] = new equipment($cannonid, 1, "ship");
		}
		
		$rocketkeres["level"] = $ship->maxrocketlevel;
		$rocketkeres["itemtype"] = "rocketlauncher";
		$rocketid = objectsearch($rocketkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->rocketslot; $szam++)
		{
			$equipment[] = new equipment($rocketid, 1, "ship");
		}
		
		$riflekeres["level"] = $ship->maxriflelevel;
		$riflekeres["itemtype"] = "rifle";
		$rifleid = objectsearch($riflekeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->rifleslot; $szam++)
		{
			$equipment[] = new equipment($rifleid, 1, "ship");
		}
		
		$hangarkeres["level"] = $ship->maxhangarlevel;
		$hangarkeres["type"] = "hangar";
		$hangarid = objectsearch($hangarkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->hangarslot; $szam++)
		{
			$equipment[] = new equipment($hangarid, 1, "ship");
		}
		
		$shieldkeres["level"] = $ship->maxshieldlevel;
		$shieldkeres["itemtype"] = "highcapacityshield";
		$shieldid = objectsearch($shieldkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->shieldslot; $szam++)
		{
			$equipment[] = new equipment($shieldid, 1, "ship");
		}
		
		$hullkeres["level"] = $ship->maxhulllevel;
		$hullkeres["itemtype"] = "battleshiphull";
		$hullid = objectsearch($hullkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->hullslot; $szam++)
		{
			$equipment[] = new equipment($hullid, 1, "ship");
		}
		
		$generatorkeres["level"] = $ship->maxgeneratorlevel;
		$generatorkeres["type"] = "generator";
		$generatorid = objectsearch($generatorkeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->generatorslot; $szam++)
		{
			$equipment[] = new equipment($generatorid, 1, "ship");
		}
		
		$batterykeres["level"] = $ship->maxbatterylevel;
		$batterykeres["type"] = "battery";
		$batteryid = objectsearch($batterykeres, $_SESSION["data"]["items"]);
		
		for($szam = 0; $szam < $ship->batteryslot; $szam++)
		{
			$equipment[] = new equipment($batteryid, 1, "ship");
		}
		
		$ammonum = $ship->cannonslot + $ship->rifleslot + $ship->rocketslot;
		
		$cannonballkeres["level"] = 1;
		$cannonballkeres["itemtype"] = "cannonball";
		$cannonballid = objectsearch($cannonballkeres, $_SESSION["data"]["items"]);
		
		$cannonballnum = $ship->basicammostorage/$ammonum*$ship->cannonslot;
		settype($cannonballnum, "integer");
		if($cannonballnum) $ammo[] = new ammo($cannonballid, "ship", 1, $cannonballnum);
		
		
		$bulletkeres["level"] = 1;
		$bulletkeres["itemtype"] = "bullet";
		$bulletid = objectsearch($bulletkeres, $_SESSION["data"]["items"]);
		
		$bulletnum = $ship->basicammostorage/$ammonum*$ship->rifleslot;
		settype($bulletnum, "integer");
		if($bulletnum) $ammo[] = new ammo($bulletid, "ship", 1, $bulletnum);
		
		
		$rocketkeres["level"] = 1;
		$rocketkeres["itemtype"] = "rocket";
		$rocketid = objectsearch($rocketkeres, $_SESSION["data"]["items"]);
		
		$rocketnum = $ship->basicammostorage/$ammonum*$ship->rocketslot/6;
		settype($rocketnum, "integer");
		if($rocketnum) $ammo[] = new ammo($rocketid, "ship", 1, $rocketnum);
		
		$result["ammo"] = $ammo;
		$result["equipment"] = $equipment;
		return $result;
	}
	
	function squadrongenerate($ship, $equipment)
	{
		if(!$squadronidlekeres = mysqli_query($_SESSION["conn"], "SELECT name, value FROM systemdata WHERE name='squadronids' OR name='squadronnames'")) die("Squadronidk és nevek lekérése sikerelen");
		while($squadronidtomb = mysqli_fetch_assoc($squadronidlekeres))
		{
			$$squadronidtomb["name"] = unserialize($squadronidtomb["value"]);
		}
		
		$hangarkeres["level"] = $ship->maxhangarlevel;
		$hangarkeres["type"] = "hangar";
		$hangarid = objectsearch($hangarkeres, $_SESSION["data"]["items"]);
		
		$squadronnum = $ship->hangarslot*$_SESSION["data"]["items"]["$hangarid"]->squadronplace;

		for($szaml = 0; $szaml < $squadronnum; $szaml++)
		{
			$equipment[] = new equipment("sqa01", 1, "ship");
			$squadronid = idgenerate("squad", $squadronids, 10);
			$squadronname = idgenerate("Squad", $squadronnames, 5);
			$newsquad = new squadron($squadronid, $squadronname);
			$squadronids[] = $squadronid;
			$squadronnames[] = $squadronname;
			
			$squadidkeres["level"] = $ship->maxsquadronlevel;
			$squadidkeres["type"] = "squadron";
			$itemid = objectsearch($squadidkeres, $_SESSION["data"]["items"]);
			$squadrondata = $_SESSION["data"]["items"]["$itemid"];
			
			$newsquad->itemid = $itemid;
			$newsquad->level = $squadrondata->level;
			$newsquad->corehull = $squadrondata->corehull;
			$newsquad->weaponslot = $squadrondata->weaponslot;
			$newsquad->maxweaponlevel = $squadrondata->maxweaponlevel;
			$newsquad->shieldslot = $squadrondata->shieldslot;
			$newsquad->maxshieldlevel = $squadrondata->maxshieldlevel;
			$newsquad->hullslot = $squadrondata->hullslot;
			$newsquad->maxhulllevel = $squadrondata->maxhulllevel;
			$newsquad->batteryslot = $squadrondata->batteryslot;
			$newsquad->maxbatterylevel = $squadrondata->maxbatterylevel;
			$newsquad->basicammostorage = $squadrondata->basicammostorage;
			
			for($szam = 0; $szam < $newsquad->weaponslot; $szam++)
			{
				$weaponkeres["level"] = $newsquad->maxweaponlevel;
				switch(rand(0, 1))
				{
					case 0: $itemtype = "squadronrifle"; break;
					case 1: $itemtype = "squadroncannon"; break;
				}
				$weaponkeres["itemtype"] = "$itemtype";
				$weaponid = objectsearch($weaponkeres, $_SESSION["data"]["items"]);
				
				$equipment[] = new equipment($weaponid, 1, $newsquad->squadronid);
			}
			
			$shieldkeres["level"] = $newsquad->maxshieldlevel;
			$shieldkeres["itemtype"] = "squadronshield";
			$shieldid = objectsearch($shieldkeres, $_SESSION["data"]["items"]);
			for($szam = 0; $szam < $newsquad->shieldslot; $szam++)
			{
				$equipment[] = new equipment($shieldid, 1, $newsquad->squadronid);
			}
			
			$hullkeres["level"] = $newsquad->maxhulllevel;
			$hullkeres["itemtype"] = "squadronhull";
			$hullid = objectsearch($hullkeres, $_SESSION["data"]["items"]);
			for($szam = 0; $szam < $newsquad->hullslot; $szam++)
			{
				$equipment[] = new equipment($hullid, 1, $newsquad->squadronid);
			}
			
			$batterykeres["level"] = $newsquad->maxbatterylevel;
			$batterykeres["type"] = "battery";
			$batteryid = objectsearch($batterykeres, $_SESSION["data"]["items"]);
			for($szam = 0; $szam < $newsquad->batteryslot; $szam++)
			{
				$equipment[] = new equipment($batteryid, 1, $newsquad->squadronid);
			}
			
			$riflekeres["level"] = $newsquad->maxweaponlevel;
			$riflekeres["itemtype"] = "squadronrifle";
			$rifleid = objectsearch($riflekeres, $_SESSION["data"]["items"]);
			
			$cannonkeres["level"] = $newsquad->maxweaponlevel;
			$cannonkeres["itemtype"] = "squadroncannon";
			$cannonid = objectsearch($cannonkeres, $_SESSION["data"]["items"]);
			
			$scannonkeres["itemid"] = $cannonid;
			$scannonkeres["place"] = $newsquad->squadronid;
			$cannons = objectsearch($scannonkeres, $equipment);
			if(!$cannons) $cannonnum = 0;
			else $cannonnum = count($cannons);
			
			$sriflekeres["itemid"] = $rifleid;
			$sriflekeres["place"] = $newsquad->squadronid;
			$rifles = objectsearch($sriflekeres, $equipment);
			if(!$rifles) $riflenum = 0;
			else $riflenum = count($rifles);
			
			$squadrons["$newsquad->squadronid"] = $newsquad;
		}
		
		$squadronids = serialize($squadronids);
		$squadronnames = serialize($squadronnames);
		if(1)
		{
			if(!$squadronupdate = mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronids' WHERE name='squadronids'")) die("systemdata frissítése sikertelen");
			if(!$squadronupdate = mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronnames' WHERE name='squadronnames'")) die("systemdata frissítése sikertelen1");
		}
		
		$result["equipment"] = $equipment;
		$result["squadrons"] = $squadrons;
		return $result;
	}
	
?>