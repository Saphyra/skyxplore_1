<?php
	include("connection.php");
	$character = $_SESSION["game"]["player"];
	
	$_SESSION["gamedata"]["playerdead"] = 0;
	
	if(isset($_POST["submit"]))
	{
		if(0) foreach($_POST as $name=>$ertek) print "$name: $ertek<BR>";
		$_SESSION["game"]["player"]["control"]["ship"] = new emptyclass;
		$_SESSION["game"]["player"]["control"]["ship"]->target = "no";
		$_SESSION["game"]["player"]["control"]["ship"]->targettry = "no";
		$_SESSION["game"]["player"]["control"]["ship"]->dmgreceived = 0;
		$_SESSION["game"]["player"]["control"]["ship"]->genenergyleft = 0;
		$_SESSION["game"]["player"]["control"]["ship"]->lastattack = 0;
		foreach($_POST as $name=>$ertek)
		{
			switch($name)
			{
				case "shieldregen":
					$_SESSION["game"]["player"]["control"]["ship"]->shieldregen = $ertek;
				break;
				case "batteryrechargestyle":
					$_SESSION["game"]["player"]["control"]["ship"]->batteryrechargestyle = $ertek;
				break;
				case "cannondamage":
					$_SESSION["game"]["player"]["control"]["ship"]->cannondamage = $ertek;
				break;
				case "cannonammo":
					$_SESSION["game"]["player"]["control"]["ship"]->cannonammo = $ertek;
				break;
				case "pulsedamage":
					$_SESSION["game"]["player"]["control"]["ship"]->pulsedamage = $ertek;
				break;
				case "pulseammo":
					$_SESSION["game"]["player"]["control"]["ship"]->pulseammo = $ertek;
				break;
				case "rocketlauncherdamage":
					$_SESSION["game"]["player"]["control"]["ship"]->rocketlauncherdamage = $ertek;
				break;
				case "rocketlauncherammo":
					$_SESSION["game"]["player"]["control"]["ship"]->rocketlauncherammo = $ertek;
				break;
				case "sablauncherdamage":
					$_SESSION["game"]["player"]["control"]["ship"]->sablauncherdamage = $ertek;
				break;
				case "sablauncherammo":
					$_SESSION["game"]["player"]["control"]["ship"]->sablauncherammo = $ertek;
				break;
				case "rifledamage":
					$_SESSION["game"]["player"]["control"]["ship"]->rifledamage = $ertek;
				break;
				case "rifleammo":
					$_SESSION["game"]["player"]["control"]["ship"]->rifleammo = $ertek;
				break;
				case "submit":
				break;
				default:
					$data = explode("&", $name);
					foreach($data as $datapart)
					{
						$part = explode("=", $datapart);
						$result["$part[0]"] = $part[1];
					}
					$group = $result["group"];
					$control = $result["control"];
					if(!isset($groupcontrol["$group"]))
					{
						$groupcontrol["$group"] = new groupcontrol;
						$groupcontrol["$group"]->target = "no";
						$groupcontrol["$group"]->targettry = "no";
						$groupcontrol["$group"]->place = "space";
						$groupcontrol["$group"]->callback = 0;
						$groupcontrol["$group"]->callbackcount = 0;
					}
					if($result["control"] == "return" and 1)
					{
						$ertekex = explode("&", $ertek);
						foreach($ertekex as $ert)
						{
							$er = explode("=", $ert);
							$valueresult["$er[0]"] = $er[1];
						}
						
						foreach($valueresult as $control=>$value)
						{
							$groupcontrol["$group"]->$control = $value;
						}
						
					}
					else $groupcontrol["$group"]->$control = $ertek;
				break;
			}
		}
		
		foreach($groupcontrol as $groupid=>$control)
		{
			$groupdata = $_SESSION["character"]["groups"]["$groupid"];
			$members = unserialize($groupdata->members);

			$nocontrol = get_object_vars($control);
			foreach($members as $squadronid)
			{
				$_SESSION["game"]["player"]["control"]["$squadronid"] = new emptyclass;
				foreach($nocontrol as $controlstyle=>$controlvalue)
				{
					$_SESSION["game"]["player"]["control"]["$squadronid"]->$controlstyle = $controlvalue;
				}
				$_SESSION["game"]["player"]["control"]["$squadronid"]->place == "space";
			}
			
			if($groupid != "no") $_SESSION["game"]["player"]["control"]["$groupid"] = $control;
		}
		include("charactergenerate.php");
		$num = rand(1, 20);
		$num = 15;
		for($szam = 0; $szam < $num; $szam++) charactergenerate($_SESSION["game"]["player"]["ship"]["ship"][0]->level, "enemy");
		if(1) for($szam = 0; $szam < $num - 1; $szam++) charactergenerate($_SESSION["game"]["player"]["ship"]["ship"][0]->level, "friend");
		header("location:battlefield.php");
	}
?>

<HTML>
	<HEAD>
		<TITLE>Konfiguráció</TITLE>
		<link rel="stylesheet" type="text/css" href="battlefield_style.css">
		<link rel="stylesheet" type="text/css" href="gameconfiguration.css">
	</HEAD>
<BODY>
	<DIV class='background'>
		<DIV class='title'>Konfiguráció</DIV>
		<FORM method='POST'>
		<DIV class='shipcontainer'>
			 <DIV class='typecontainer'>
				<DIV class='typetitle'>Csatahajó</DIV>
				<?php
					corebar($character["ship"]["ship"][0]);
					hullbar($character["ship"]);
					shieldbar($character["ship"]);
					energybar($character["ship"]);
					cargobar($character["ship"]["ship"][0]);
				?>
			 </DIV>
			 <DIV class='typecontainer'>
				<DIV class='typetitle'>Fegyverek</DIV>
				<TABLE class='weapon'>
					<TR>
						<TH class='weapon'>Ágyúk</TH>
						<TH class='weapon'>Rakétakilövők</TH>
						<TH class='weapon'>Gépágyúk</TH>
					</TR>
					<TR>
						<TD class='weapon'>
							<?php
								if(isset($character["ship"]["cannon"])) cannon($character);
								else print "<DIV class='weapontitle'>Nincs felszerelt ágyú</DIV>";
							?>
						</TD>
						<TD class='weapon'>
							<?php
								if(isset($character["ship"]["rocketlauncher"])) rocketlauncher($character);
								else print "<DIV class='weapontitle'>Nincs felszerelt rakétakilövő</DIV>";
							?>
						</TD>
						<TD class='weapon'>
							<?php
								if(isset($character["ship"]["rifle"])) rifle($character);
								else print "<DIV class='weapontitle'>Nincs felszerelt gépágyú</DIV>";
							?>
						</TD>
					</TR>
				</TABLE>
			</DIV>
			<?php
				squadrons($character);
			?>
		</DIV>
		<DIV class='functionbar'>
			<DIV class='start'><INPUT class='submit 'type='submit' name='submit' value='Kidokkolás'></DIV>
			<DIV class='back'><A class='back' href='http://localhost/php/online/skyxplore/hangar.php'>Vissza</A></DIV>
		</DIV>
		</FORM>
	</DIV>
	
</BODY>
</HTML>

<?php
	function corebar($ship)
	{
		$corehull = $ship->corehull;
		$actualcorehull = $ship->actualcorehull;
		
		$width = $actualcorehull / $corehull * 100;
		print "
			<DIV class='bar'>
				<IMG src='pixelred.jpg' class='bar' width='$width%'>
				<DIV class='text'>Magburkolat ($width% - $actualcorehull / $corehull)</DIV>
			</DIV>
		";
	}
	
	function hullbar($ship)
	{
		$hullenergy = 0;
		$actualhull = 0;
		if(isset($ship["hull"]))
		{
			foreach($ship["hull"] as $hull)
			{
				$hullenergy += $hull->hullenergy;
				$actualhull += $hull->actualhull;
			}
			
			$width = $actualhull / $hullenergy * 100;
		}
		else $width = 0;
		settype($width, "integer");
		
		print "
			<DIV class='bar'>
				<IMG src='pixelgreen.jpg' class='bar' width='$width%'>
				<DIV class='text'>Burkolat ($width% - $actualhull / $hullenergy)</DIV>
			</DIV>
		";
	}
	
	function shieldbar($ship)
	{
		$shieldenergy = 0;
		$actualshield = 0;
		$recharge = 0;
		
		if(isset($ship["shield"]))
		{
			foreach($ship["shield"] as $shield)
			{
				$shieldenergy += $shield->shieldenergy;
				$actualshield += $shield->actualshield;
				$recharge += $shield->recharge;
			}
			
			$width = $actualshield / $shieldenergy * 100;
		}
		else $width = 0;
		settype($width, "integer");
		
		$input = "";
		if(isset($ship["battery"]) or isset($ship["generator"]))
		{
			if(isset($ship["shield"]))
			{
				$input = "
					<DIV class='select'>
						Regeneráció:
						<SELECT name='shieldregen'>
							<OPTION value='100'>100%</OPTION>
							<OPTION value='75'>75%</OPTION>
							<OPTION value='50'>50%</OPTION>
							<OPTION value='25'>25%</OPTION>
							<OPTION value='0'>Nincs</OPTION>
						</SELECT>
					</DIV>
				";
			}
		}
		
		
		print "
			<DIV class='bar'>
				<IMG src='pixelblue.jpg' class='bar' width='$width%'>
				<DIV class='text'>Pajzs ($width% - $actualshield / $shieldenergy - + $recharge / kör)</DIV>
				$input
			</DIV>
		";
	}
		
	function energybar($ship)
	{
		$capacity = 0;
		$actualcapacity = 0;
		
		if(isset($ship["battery"]))
		{
				foreach($ship["battery"] as $battery)
			{
				$capacity += $battery->capacity;
				$actualcapacity += $battery->actualcapacity;
			}
			
			$width = $actualcapacity / $capacity * 100;
		}
		else $width = 0;
		settype($width, "integer");
		
		$energyregen = 0;
		if(isset($ship["generator"]))
		{
			foreach($ship["generator"] as $generator)
			{
				$energyregen += $generator->energyregen;
			}
		}
		
		print "
			<DIV class='bar'>
				<IMG src='pixelyellow.jpg' class='bar' width='$width%'>
				<DIV class='text'>Energia ($width% - $actualcapacity / $capacity - + $energyregen / kör)</DIV>
			</DIV>
		";
	}
	
	function cargobar($ship)
	{
		
		$cargowidth = $ship->actualcargo / $ship->basiccargo * 100;
		$ammowidth = $ship->actualammostorage / $ship->basicammostorage * 100;
		settype($ammowidth, "integer");
		settype($cargowidth, "integer");
		print "
			<DIV class='bar' style='border: none;'>
				<DIV class='bar' style='position: absolute; width: 49%;'>
					<IMG src='pixelpurple.jpg' class='bar' width='$cargowidth%'>
					<DIV class='text'>Raktér ($cargowidth% - $ship->actualcargo / $ship->basiccargo)</DIV>
				</DIV>
				<DIV class='bar' style='position: absolute; width: 49%; right: 0px;'>
					<IMG src='pixelpurple.jpg' class='bar' width='$ammowidth%'>
					<DIV class='text'>Lőszerraktár ($ammowidth% - $ship->actualammostorage / $ship->basicammostorage)</DIV>
				</DIV>
			</DIV>
		";
	}
	
	function cannon($character)
	{
		$cannons = $character["ship"]["cannon"];
		$shielddamage = 0;
		$hulldamage = 0;
		$cannonnum = 0;
		$pulsenum = 0;
		$cannonset = "";
		$pulseset = "";
		
		foreach($cannons as $cannon)
		{
			if($cannon->itemtype == "cannon") $cannonnum += 1;
			elseif($cannon->itemtype == "pulse") $pulsenum += 1;
			$shielddamage += $cannon->shielddamage;
			$hulldamage += $cannon->hulldamage;
		}
		
		if($cannonnum)
		{
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammoid=>$ammo)
				{
					if($ammo->itemtype == "cannonball") $cannonballtomb["$ammoid"] = $ammo;
				}
			}
			
			if(isset($cannonballtomb)) ksort($cannonballtomb);
			else $cannonballtomb = 0;
			
			$cannonsettomb[] = "<DIV class='weaponitem'>";
			$cannonsettomb[] = "
				<DIV class='weapontitle'>Ágyú</DIV>
				Sebzés:
				<SELECT name='cannondamage'>
					<OPTION value='0'>Nem lő</OPTION>
					<OPTION value='25'>25%</OPTION>
					<OPTION value='50'>50%</OPTION>
					<OPTION value='75'>75%</OPTION>
					<OPTION value='100' selected='selected'>100%</OPTION>
				</SELECT>
			";
			$cannonsettomb[] = "
				Lőszer:
				<SELECT name='cannonammo'>
			";
			if($cannonballtomb)
			{
				foreach($cannonballtomb as $ammoid=>$ammo)
				{
					$cannonsettomb[] = "<OPTION value='$ammoid'>$ammo->name</OPTION>";
				}
			}
			else $cannonsettomb[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
			
			$cannonsettomb[] = "</SELECT></DIV>";
			foreach($cannonsettomb as $ertek) $cannonset .= $ertek;
		}
		
		if($pulsenum)
		{
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammoid=>$ammo)
				{
					if($ammo->itemtype == "ioncell") $ioncelltomb["$ammoid"] = $ammo;
				}
			}
			
			if(isset($ioncelltomb)) ksort($ioncelltomb);
			else $ioncelltomb = 0;
			
			$pulsesettomb[] = "<DIV class='weaponitem'>";
			$pulsesettomb[] = "
				<DIV class='weapontitle'>Pulzuságyú</DIV>
				Sebzés:
				<SELECT name='pulsedamage'>
					<OPTION value='0'>Nem lő</OPTION>
					<OPTION value='25'>25%</OPTION>
					<OPTION value='50'>50%</OPTION>
					<OPTION value='75'>75%</OPTION>
					<OPTION value='100' selected='selected'>100%</OPTION>
				</SELECT>
			";
			$pulsesettomb[] = "
				Lőszer:
				<SELECT name='pulseammo'>
			";
			if($ioncelltomb)
			{
				
				foreach($ioncelltomb as $ammoid=>$ammo)
				{
					$pulsesettomb[] = "<OPTION value='$ammoid'>$ammo->name</OPTION>";
				}
			}
			else $pulsesettomb[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
			
			$pulsesettomb[] = "</SELECT></DIV>";
			foreach($pulsesettomb as $ertek) $pulseset .= $ertek;
		}
		
		print "
			<DIV class='weapon'>
				<DIV class='weapondamage'>Sebzés: $hulldamage (Burkolat) / $shielddamage (Pajzs)</DIV>
				$cannonset
				$pulseset
			</DIV>
		";
	}
	
	function rocketlauncher($character)
	{
		$rocketlaunchers = $character["ship"]["rocketlauncher"];
		$shielddamage = 0;
		$hulldamage = 0;
		$rocketlaunchernum = 0;
		$sablaunchernum = 0;
		$rocketlauncherset = "";
		$sablauncherset = "";
		
		foreach($rocketlaunchers as $rocketlauncher)
		{
			if($rocketlauncher->itemtype == "rocketlauncher") $rocketlaunchernum += 1;
			elseif($rocketlauncher->itemtype == "sablauncher") $sablaunchernum += 1;
			$shielddamage += $rocketlauncher->shielddamage;
			$hulldamage += $rocketlauncher->hulldamage;
		}
		
		if($rocketlaunchernum)
		{
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammoid=>$ammo)
				{
					if($ammo->itemtype == "rocket") $rockettomb["$ammoid"] = $ammo;
				}
			}
			
			if(isset($rockettomb)) ksort($rockettomb);
			else $rockettomb = 0;
			
			$rocketsettomb[] = "<DIV class='weaponitem'>";
			$rocketsettomb[] = "
				<DIV class='weapontitle'>Rakétakilövő</DIV>
				Sebzés:
				<SELECT name='rocketlauncherdamage'>
					<OPTION value='0'>Nem lő</OPTION>
					<OPTION value='25'>25%</OPTION>
					<OPTION value='50'>50%</OPTION>
					<OPTION value='75'>75%</OPTION>
					<OPTION value='100' selected='selected'>100%</OPTION>
				</SELECT>
			";
			$rocketsettomb[] = "
				Lőszer:
				<SELECT name='rocketlauncherammo'>
			";
			if($rockettomb)
			{
				foreach($rockettomb as $ammoid=>$ammo)
				{
					$rocketsettomb[] = "<OPTION value='$ammoid'>$ammo->name</OPTION>";
				}
			}
			else $rocketsettomb[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
			
			$rocketsettomb[] = "</SELECT></DIV>";
			foreach($rocketsettomb as $ertek) $rocketlauncherset .= $ertek;
		}
		
		if($sablaunchernum)
		{
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammoid=>$ammo)
				{
					if($ammo->itemtype == "sabrocket") $sabrockettomb["$ammoid"] = $ammo;
				}
			}
			
			if(isset($sabrockettomb)) ksort($sabrockettomb);
			else $sabrockettomb = 0;
			
			$sablaunchertomb[] = "<DIV class='weaponitem'>";
			$sablaunchertomb[] = "
				<DIV class='weapontitle'>SAB Rakétakilövő</DIV>
				Sebzés:
				<SELECT name='sablauncherdamage'>
					<OPTION value='0'>Nem lő</OPTION>
					<OPTION value='25'>25%</OPTION>
					<OPTION value='50'>50%</OPTION>
					<OPTION value='75'>75%</OPTION>
					<OPTION value='100' selected='selected'>100%</OPTION>
				</SELECT>
			";
			$sablaunchertomb[] = "
				Lőszer:
				<SELECT name='sablauncherammo'>
			";
			if($sabrockettomb)
			{
				
				foreach($sabrockettomb as $ammoid=>$ammo)
				{
					$sablaunchertomb[] = "<OPTION value='$ammoid'>$ammo->name</OPTION>";
				}
			}
			else $sablaunchertomb[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
			
			$sablaunchertomb[] = "</SELECT></DIV>";
			foreach($sablaunchertomb as $ertek) $sablauncherset .= $ertek;
		}
		
		print "
			<DIV class='weapon'>
				<DIV class='weapondamage'>Sebzés: $hulldamage (Burkolat) / $shielddamage (Pajzs)</DIV>
				$rocketlauncherset
				$sablauncherset
			</DIV>
		";
	}
	
	function rifle($character)
	{
		$rifles = $character["ship"]["rifle"];
		$squadrondamage = 0;
		$riflenum = 0;
		$rifleset = "";
		
		foreach($rifles as $rifle)
		{
			$riflenum += 1;
			$squadrondamage += $rifle->squadrondamage;
		}
		
		if($riflenum)
		{
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammoid=>$ammo)
				{
					if($ammo->itemtype == "bullet") $bullettomb["$ammoid"] = $ammo;
				}
			}
			
			if(isset($bullettomb)) ksort($bullettomb);
			else $bullettomb = 0;
			
			$riflesettomb[] = "<DIV class='weaponitem'>";
			$riflesettomb[] = "
				<DIV class='weapontitle'>Gépágyú</DIV>
				Sebzés:
				<SELECT name='rifledamage'>
					<OPTION value='0'>Nem lő</OPTION>
					<OPTION value='25'>25%</OPTION>
					<OPTION value='50'>50%</OPTION>
					<OPTION value='75'>75%</OPTION>
					<OPTION value='100' selected='selected'>100%</OPTION>
				</SELECT>
			";
			$riflesettomb[] = "
				Lőszer:
				<SELECT name='rifleammo'>
			";
			if($bullettomb)
			{
				foreach($bullettomb as $ammoid=>$ammo)
				{
					$riflesettomb[] = "<OPTION value='$ammoid'>$ammo->name</OPTION>";
				}
			}
			else $riflesettomb[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
			
			$riflesettomb[] = "</SELECT></DIV>";
			foreach($riflesettomb as $ertek) $rifleset .= $ertek;
		}
		
		print "
			<DIV class='weapon'>
				<DIV class='weapondamage'>Sebzés: $squadrondamage (Raj)</DIV>
				$rifleset
			</DIV>
		";
	}
	
	function squadrons($character)
	{
		if(isset($character["squadrons"])) $squadrons = $character["squadrons"];
		else return;
		
		print "<DIV class='typecontainer'><DIV class='typetitle'>Rajok</DIV>";
		$groups = $character["groups"];
		if(isset($character["ammo"])) $ammos = $character["ammo"];
		else $ammos = 0;
		
		foreach($squadrons as $squadronid=>$squadron)
		{
			$group = $squadron["squadron"]->group;
			$groupset["$group"]["$squadronid"] = $squadron;
		}
		
		foreach($groupset as $groupid=>$members)
		{
			$groupname = $groups["$groupid"]->groupname;
			
			print "
				<TABLE class='squadron'>
					<TR>
						<TH colspan='3' class='squadron'>$groupname</TH>
					</TR>
			";
			inputcommon($members, $ammos, $groupid);
			
			$num = 1;
			print "<TR>";
			foreach($members as $squadronid=>$squadron)
			{
				if($num == 4)
				{	
					print "</TR><TR>";
					$num = 1;
				}
				
				$squadronname = $squadron["squadron"]->squadronname;
				
				$corehull = $squadron["squadron"]->corehull;
				$actualcorehull = $squadron["squadron"]->actualcorehull;
				$corehullwidth = $actualcorehull / $corehull * 100;
				settype($corehullwidth, "integer");
				
				$hull = 0;
				$actualhull = 0;
				if(isset($squadron["squadronhull"]))
				{
					foreach($squadron["squadronhull"] as $hullitem)
					{
						$hull += $hullitem->hullenergy;
						$actualhull += $hullitem->actualhull;
					}
					$hullwidth = $actualhull / $hull * 100;
				}
				else $hullwidth = 0;
				settype($hullwidth, "integer");
				
				$shield = 0;
				$actualshield = 0;
				if(isset($squadron["squadronshield"]))
				{
					foreach($squadron["squadronshield"] as $shielditem)
					{
						$shield += $shielditem->shieldenergy;
						$actualshield += $shielditem->actualshield;
					}
					$shieldwidth = $actualshield / $shield * 100;
				}
				else $shieldwidth = 0;
				settype($shieldwidth, "integer");
				
				$energy = 0;
				$actualenergy = 0;
				if(isset($squadron["battery"]))
				{
					foreach($squadron["battery"] as $battery)
					{
						$energy += $battery->capacity;
						$actualenergy += $battery->actualcapacity;
					}
					$energywidth = $actualenergy / $energy * 100;
				}
				else $energywidth = 0;
				settype($energywidth, "integer");
				
				$ammo = $squadron["squadron"]->basicammostorage;
				$actualammo = $squadron["squadron"]->actualammostorage;
				$ammowidth = $actualammo / $ammo * 100;
				settype($ammowidth, "integer");
				
				$hulldamage = 0;
				$shielddamage = 0;
				$squadrondamage = 0;
				if(isset($squadron["squadroncannon"]))
				{
					foreach($squadron["squadroncannon"] as $weapon)
					{
						if(property_exists($weapon, "hulldamage")) $hulldamage += $weapon->hulldamage;
						if(property_exists($weapon, "shielddamage")) $shielddamage += $weapon->shielddamage;
						if(property_exists($weapon, "squadrondamage")) $squadrondamage += $weapon->squadrondamage;
					}
				}
				
				print "
					<TD class='squadron'>
						<DIV class='squadron'>
							<DIV class='squadronname'>$squadronname</DIV>
							<DIV class='squadronbars'>
								<DIV class='squadronbar'>
									<IMG class='bar' src='pixelred.jpg' width='$corehullwidth%'>
									<DIV class='squadrontext'>Magburkolat ($corehullwidth% - $actualcorehull / $corehull)</DIV>
								</DIV>
								<DIV class='squadronbar'>
									<IMG class='bar' src='pixelgreen.jpg' width='$hullwidth%'>
									<DIV class='squadrontext'>Burkolat ($hullwidth% - $actualhull / $hull)</DIV>
								</DIV>
								<DIV class='squadronbar'>
									<IMG class='bar' src='pixelblue.jpg' width='$shieldwidth%'>
									<DIV class='squadrontext'>Pajzs ($shieldwidth% - $actualshield / $shield)</DIV>
								</DIV>
								<DIV class='squadronbar'>
									<IMG class='bar' src='pixelyellow.jpg' width='$energywidth%'>
									<DIV class='squadrontext'>Energia ($energywidth% - $actualenergy / $energy)</DIV>
								</DIV>
								<DIV class='squadronbar'>
									<IMG class='bar' src='pixelpurple.jpg' width='$ammowidth%'>
									<DIV class='squadrontext'>Lőszer ($ammowidth% - $actualammo / $ammo)</DIV>
								</DIV>
							</DIV>
							<DIV class='squadrondamage'>
								Sebzés: $hulldamage (Burkolat) / $shielddamage (Pajzs) / $squadrondamage (Raj)
							</DIV>
						</DIV>
					</TD>
				";
				
				$num++;
			}
			for($num; $num < 4; $num++) print "<TD class='squadron'></TD>";
			
			print "</TR></TABLE></DIV>";
		}
	}
		
		function inputcommon($members, $ammos, $groupid)
		{
			print "<TR><TD colspan='3' style='line-height: 2;'>";
				
				$duty = "";
				if($groupid != "no")
				{
					$cannonselected = "";
					$rifleselected = "";
					
					switch($_SESSION["character"]["groups"]["$groupid"]->style)
					{
						case "cannon":
							$cannonselected = "selected";
						break;
						case "rifle":
							$rifleselected = "selected";
						break;
					}
					
					$duty = "
						Feladat:
						<SELECT name='group=$groupid&control=targetstyle'>
							<OPTION value='cannon' $cannonselected>Csatahajók támadása</OPTION>
							<OPTION value='rifle' $rifleselected>Rajok támadása</OPTION>
						</SELECT>
					";
				}
				
				print "
					Célpontválasztás: 
					<SELECT name='group=$groupid&control=targetselect'>
						<OPTION value='auto'>Automatikus</OPTION>
						<OPTION value='manual'>Kézi</OPTION>
					</SELECT>
					$duty
					Visszatérés: 
					<SELECT name='group=$groupid&control=return'>
						<OPTION value='returnstyle=shield&returnvalue=75'>75% pajzs alatt</OPTION>
						<OPTION value='returnstyle=shield&returnvalue=50'>50% pajzs alatt</OPTION>
						<OPTION value='returnstyle=shield&returnvalue=25'>25% pajzs alatt</OPTION>
						<OPTION value='returnstyle=hull&returnvalue=75'>75% burkolat alatt</OPTION>
						<OPTION value='returnstyle=hull&returnvalue=50' selected>50% burkolat alatt</OPTION>
						<OPTION value='returnstyle=hull&returnvalue=25'>25% burkolat alatt</OPTION>
						<OPTION value='returnstyle=manual&returnvalue=0'>Kézi</OPTION>
					</SELECT>
					Pajzsregeneráció: 
					<SELECT name='group=$groupid&control=squadronshieldregen'>
						<OPTION value='100'>100%</OPTION>
						<OPTION value='75'>75%</OPTION>
						<OPTION value='50'>50%</OPTION>
						<OPTION value='25'>25%</OPTION>
						<OPTION value='0'>Nincs</OPTION>
					</SELECT>
					Felszállás: 
					<SELECT name='group=$groupid&control=takeoff'>
						<OPTION value='auto'>Automatikus</OPTION>
						<OPTION value='manual'>Kézi</OPTION>
					</SELECT>
					<BR>
				";
				
				$squadroncannon = 0;
				$squadronrifle = 0;
				$squadronpulse = 0;
				foreach($members as $member)
				{
					if(isset($member["squadroncannon"]))
					{
						foreach($member["squadroncannon"] as $weapon)
						{
							if($weapon->itemtype == "squadroncannon") $squadroncannon = 1;
							if($weapon->itemtype == "squadronrifle") $squadronrifle = 1;
							if($weapon->itemtype == "squadronpulse") $squadronpulse = 1;
						}
					}
				}
				
				if($ammos)
				{
					foreach($ammos as $ammo)
					{
						$ammoset["$ammo->itemtype"]["$ammo->itemid"] = $ammo->name;
					}
				}
				if($squadroncannon)
				{
					print "Ágyú lőszer: ";
					print "<SELECT name='group=$groupid&control=squadroncannonammo'>";
					
					if(isset($ammoset["cannonball"]))
					{
						foreach($ammoset["cannonball"] as $ammoid=>$ammo)
						{
							print "<OPTION value='$ammoid'>$ammo</OPTION>";
						}
					}
					else print "<OPTION value='no'>Nincs lőszer</OPTION>";
					print "</SELECT>";
					print "
						Sebzés: 
						<SELECT name='group=$groupid&control=squadroncannondamage'>
							<OPTION value='100'>100%</OPTION>
							<OPTION value='75'>75%</OPTION>
							<OPTION value='50'>50%</OPTION>
							<OPTION value='25'>25%</OPTION>
							<OPTION value='0'>Nem lő</OPTION>
						</SELECT>
					";
					print "<BR>";
				}
				if($squadronpulse)
				{
					print "Pulzuságyú lőszer: ";
					print "<SELECT name='group=$groupid&control=squadronpulseammo'>";
					if(isset($ammoset["ioncell"]))
					{
						foreach($ammoset["ioncell"] as $ammoid=>$ammo)
						{
							print "<OPTION value='$ammoid'>$ammo</OPTION>";
						}
					}
					else print "<OPTION value='no'>Nincs lőszer</OPTION>";
					print "</SELECT>";
					print "
						Sebzés: 
						<SELECT name='group=$groupid&control=squadronpulsedamage'>
							<OPTION value='100'>100%</OPTION>
							<OPTION value='75'>75%</OPTION>
							<OPTION value='50'>50%</OPTION>
							<OPTION value='25'>25%</OPTION>
							<OPTION value='0'>Nem lő</OPTION>
						</SELECT>
					";
					print "<BR>";
				}
				if($squadronrifle)
				{
					print "Gépágyú lőszer: ";
					print "<SELECT name='group=$groupid&control=squadronrifleammo'>";
					if(isset($ammoset["bullet"]))
					{
						foreach($ammoset["bullet"] as $ammoid=>$ammo)
						{
							print "<OPTION value='$ammoid'>$ammo</OPTION>";
						}
					}
					else print "<OPTION value='no'>Nincs lőszer</OPTION>";
					print "</SELECT>";
					print "
						Sebzés: 
						<SELECT name='group=$groupid&control=squadronrifledamage'>
							<OPTION value='100'>100%</OPTION>
							<OPTION value='75'>75%</OPTION>
							<OPTION value='50'>50%</OPTION>
							<OPTION value='25'>25%</OPTION>
							<OPTION value='0'>Nem lő</OPTION>
						</SELECT>
					";
				}
				
				
			print "</TD></TR>";
		}
?>
