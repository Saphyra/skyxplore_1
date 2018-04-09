<?php
	function playership($character)
	{
		print "<DIV class='bars'>";
			bars($character);
		print "</DIV>";
		effects($character);
		inputs($character);
		equipments($character);
		skills($character);
		squadrons($character);
	}
		function bars($character)
		{
			$corehull = $character["ship"]["ship"][0]->corehull;
			$actualcorehull = $character["ship"]["ship"][0]->actualcorehull;
			$corehullwidth = $actualcorehull / $corehull * 100;
			settype($corehullwidth, "integer");
			
			$hullenergy = 0;
			$actualhull = 0;
			$hullpenetration = 100;
			if(isset($character["ship"]["hull"]))
			{
				foreach($character["ship"]["hull"] as $hull)
				{
					$hullenergy += $hull->hullenergy;
					$actualhull += $hull->actualhull;
					$hullpenetrationtomb[] = $hull->actualhull / $hull->hullenergy;
				}
				
				$hullwidth = $actualhull / $hullenergy * 100;
				
				$maxpenetration = 0;
				$actualpenetration = 0;
				foreach($hullpenetrationtomb as $penetration)
				{
					$maxpenetration += 1;
					$actualpenetration += $penetration;
				}
				$hullpenetration = 100 - $actualpenetration / $maxpenetration * 100;
				settype($hullpenetration, "integer");
			}
			else $hullwidth = 0;
			settype($hullwidth, "integer");
			
			$shieldenergy = 0;
			$actualshield = 0;
			$recharge = 0;
			$shieldpenetration = 100;
			if(isset($character["ship"]["shield"]))
			{
				foreach($character["ship"]["shield"] as $shield)
				{
					$shieldenergy += $shield->shieldenergy;
					$actualshield += $shield->actualshield;
					$recharge += $shield->recharge;
					$shieldpenetrationtomb[] = $shield->actualshield / $shield->shieldenergy;
				}
				
				$shieldwidth = $actualshield / $shieldenergy * 100;
				
				$maxpenetration = 0;
				$actualpenetration = 0;
				foreach($shieldpenetrationtomb as $penetration)
				{
					$maxpenetration += 1;
					$actualpenetration += $penetration;
				}
				$shieldpenetration = 100 - $actualpenetration / $maxpenetration * 100;
				settype($shieldpenetration, "integer");
			}
			else $shieldwidth = 0;
			settype($shieldwidth, "integer");
			
			$capacity = 0;
			$actualcapacity = 0;
			$energystatus = 0;
			if(isset($character["ship"]["battery"]))
			{
					foreach($character["ship"]["battery"] as $battery)
				{
					$capacity += $battery->capacity;
					$actualcapacity += $battery->actualcapacity;
					$capacitytomb[] = $actualcapacity / $capacity;
				}
				
				$energywidth = $actualcapacity / $capacity * 100;
				
				$maxcapacity = 0;
				$capacitystatusitem = 0;
				foreach($capacitytomb as $capacitystatus)
				{
					$maxcapacity += 1;
					$capacitystatusitem += $capacitystatus;
				}
				$energystatus = $capacitystatusitem / $maxcapacity * 100;
				settype($energystatus, "integer");
			}
			else $energywidth = 0;
			settype($energywidth, "integer");
			
			
			
			$energyregen = 0;
			if(isset($character["ship"]["generator"]))
			{
				foreach($character["ship"]["generator"] as $generator)
				{
					$energyregen += $generator->energyregen;
				}
			}
			
			$basiccargo = $character["ship"]["ship"][0]->basiccargo;
			$actualcargo = $character["ship"]["ship"][0]->actualcargo;
			$cargowidth = $actualcargo / $basiccargo * 100;
			
			$actualammostorage = 0;
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammo) $actualammostorage += $ammo->amount;
			}
			$basicammostorage = $character["ship"]["ship"][0]->basicammostorage;
			$ammowidth = $actualammostorage / $basicammostorage * 100;
			settype($ammowidth, "integer");
			settype($cargowidth, "integer");
			
			print "
				<DIV class='bar'>
					<IMG class='bar' src='pixelred.jpg' width='$corehullwidth%'>
					<DIV class='bartext'>Magburkolat ($corehullwidth% - $actualcorehull / $corehull)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelgreen.jpg' width='$hullwidth%'>
					<DIV class='bartext'>Burkolat ($hullwidth% - $actualhull / $hullenergy - Penetráció: $hullpenetration%)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelblue.jpg' width='$shieldwidth%'>
					<DIV class='bartext'>Pajzs ($shieldwidth% - $actualshield / $shieldenergy - +$recharge / kör - Penetráció: $shieldpenetration%)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelyellow.jpg' width='$energywidth%'>
					<DIV class='bartext'>Energia ($energywidth% - $actualcapacity / $capacity - +$energyregen / kör - Állapot: $energystatus%)</DIV>
				</DIV>
				<DIV class='bar' style='border: none;'>
					<DIV class='bar' style='position: absolute; width: 49%;'>
						<IMG src='pixelpurple.jpg' class='bar' width='$cargowidth%'>
						<DIV class='bartext'>Raktér ($cargowidth% - $actualcargo / $basiccargo)</DIV>
					</DIV>
					<DIV class='bar' style='position: absolute; width: 49%; right: 0px;'>
						<IMG src='pixelpurple.jpg' class='bar' width='$ammowidth%'>
						<DIV class='bartext'>Lőszerraktár ($ammowidth% - $actualammostorage / $basicammostorage)</DIV>
					</DIV>
				</DIV>
			";
		}
		
		function effects($character)
		{
			foreach($character["ship"]["equipment"] as $itemid=>$equipment)
			{
				if($equipment->actualactive) $effect[] = "$itemid";
			}
			foreach($character["skill"] as $skillid=>$skill)
			{
				if(property_exists($skill, "actualactive"))
				{
					if($skill->actualactive)
					{
						$skilleffect[] = "$skillid";
					}
				}
			}
			
			if(isset($effect) or isset($skilleffect))
			{
				print "<DIV class='hov'>";
					print "<DIV class='hovtitle'>Hatások</DIV>";
					print "<DIV class='input'>";
						if(isset($effect))
						{
							foreach($effect as $equipment)
							{
								$equipmentname = $character["ship"]["equipment"]["$equipment"]->name;
								$equipmentactive = $character["ship"]["equipment"]["$equipment"]->actualactive;
								print "<DIV class='equipment'>";
									print "$equipmentname ($equipmentactive)";
								print "</DIV>";
							}
						}
						if(isset($skilleffect))
						{
							foreach($skilleffect as $skill)
							{
								$equipmentname = $_SESSION["data"]["items"]["$skill"]->name;
								$equipmentactive = $character["skill"]["$skill"]->actualactive;
								print "<DIV class='equipment'>";
									print "$equipmentname ($equipmentactive)";
								print "</DIV>";
							}
						}
						
					print "</DIV>";
				print "</DIV>";
			}
		}
		
		function inputs($character)
		{
			print "<DIV class='hov'>";
			print "<DIV class='hovtitle'>Vezérlés</DIV>";
			print "<DIV>Célpont: ";
			shiptarget($character["control"]["ship"]->target, $character["control"]["ship"]->targettry);
			
			if(isset($character["ship"]["battery"]) or isset($character["ship"]["generator"]))
			{
				if(isset($character["ship"]["shield"]))
				{
					$selected100 = "";
					$selected75 = "";
					$selected50 = "";
					$selected25 = "";
					$selected0 = "";
					switch($character["control"]["ship"]->shieldregen)
					{
						case 100:
							$selected100 = "selected";
						break;
						case 75:
							$selected75 = "selected";
						break;
						case 50:
							$selected50 = "selected";
						break;
						case 25:
							$selected25 = "selected";
						break;
						case 0:
							$selected0 = "selected";
						break;
					}
					print "
						Pajzsegeneráció: 
						<SELECT name='id=ship&control=shieldregen'>
							<OPTION value='100' $selected100>100%</OPTION>
							<OPTION value='75' $selected75>75%</OPTION>
							<OPTION value='50' $selected50>50%</OPTION>
							<OPTION value='25' $selected25>25%</OPTION>
							<OPTION value='0' $selected0>Nincs</OPTION>
						</SELECT>
					";
				}
			}
			
			print "</DIV>";
			print "</DIV>";
			weaponinput($character);
		}
		
			function shiptarget($targetid, $targettry)
			{
				print "<SELECT class='long' name='id=ship&control=targettry'>";
				$noselect = "";
				if($targettry == "no") $noselect = "selected";
				print "<OPTION value='no' $noselect>Nincs célpont </OPTION>";
				if(!isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"]))
				{
					print "</SELECT>";
					return;
				}
				else
				{
					foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $object)
					{
						$selected = "";
						$shotme = "";
						$locked = "";
						if($object->id == $targetid and $targetid != "no")
						{
							$locked = "(Jelölve)";
						}
						if($object->id == $targettry or $object->id == $targetid)
						{
							$selected = "selected";
						}
						if($_SESSION["game"]["$object->id"]["control"]["ship"]->target == "player") $shotme = " (!)";
						print "<OPTION value='$object->id' $selected>$object->name $shotme $locked</OPTION>";
					}
				}
				print "</SELECT>";
			}
			
			function weaponinput($character)
			{
				if(isset($character["ship"]["cannon"]))
				{
					print "<DIV class='inputcontainer'>";
						cannoninput($character);
					print "</DIV>";
				}
			
				if(isset($character["ship"]["rocketlauncher"]))
				{
					print "<DIV class='inputcontainer'>";
						rocketlauncherinput($character);
					print "</DIV>";
				}
			
				if(isset($character["ship"]["rifle"]))
				{
					print "<DIV class='inputcontainer'>";
						rifleinput($character);
						print "</DIV>";
				}
			}
				
				function cannoninput($character)
				{
					$cannonhulldamage = 0;
					$pulsehulldamage = 0;
					$cannonshielddamage = 0;
					$pulseshielddamage = 0;
					$cannonnum = 0;
					$pulsenum = 0;
					$cannonenergy = 0;
					$pulseenergy = 0;
					$cannonammo = 0;
					$pulseammo = 0;
					
					foreach($character["ship"]["cannon"] as $weapon)
					{
						switch($weapon->itemtype)
						{
							case "cannon":
								$cannonnum += 1;
								$cannonenergy += $weapon->energyusage;
								$cannonammo += $weapon->ammousage;
								$cannonhulldamage += $weapon->hulldamage;
								$cannonshielddamage += $weapon->shielddamage;
							break;
							case "pulse":
								$pulsenum += 1;
								$pulseenergy += $weapon->energyusage;
								$pulseammo += $weapon->ammousage;
								$pulsehulldamage += $weapon->hulldamage;
								$pulseshielddamage += $weapon->shielddamage;
							break;
						}
					}
					
					if(isset($character["ammo"]))
					{
						foreach($character["ammo"] as $ammo)
						{
							switch($ammo->itemtype)
							{
								case "cannonball":
									if($ammo->amount) $cannonballs["$ammo->itemid"] = $ammo;
								break;
								case "ioncell":
									if($ammo->amount) $ioncells["$ammo->itemid"] = $ammo;
								break;
							}
						}
					}
					
					
					if($cannonnum)
					{
						$cdi[] = "";
						$cannondamageinput = "";
						
						$cdi[] = "Sebzés: <SELECT name='id=ship&control=cannondamage'>";
						$select100 = "";
						$select75 = "";
						$select50 = "";
						$select25 = "";
						$select0 = "";
						
						switch($character["control"]["ship"]->cannondamage)
						{
							case 0:
								$select0 = "selected";
							break;
							case 25:
								$select25 = "selected";
							break;
							case 50:
								$select50 = "selected";
							break;
							case 75:
								$select75 = "selected";
							break;
							case 100:
								$select100 = "selected";
							break;
						}
						
						$cdi[] = "
							<OPTION value='100' $select100>100%</OPTION>
							<OPTION value='75' $select75>75%</OPTION>
							<OPTION value='50' $select50>50%</OPTION>
							<OPTION value='25' $select25>25%</OPTION>
							<OPTION value='0' $select0>Nem lő</OPTION>
						";
						$cdi[] = "</SELECT>";
						
						$cdi[] = "Lőszer: <SELECT name='id=ship&control=cannonammo'>";
						if(!isset($cannonballs)) $cdi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
						else
						{
							ksort($cannonballs);
							foreach($cannonballs as $ammo)
							{
								$selected = "";
								if($ammo->itemid == $character["control"]["ship"]->cannonammo) $selected = "selected";
								$cdi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
							}
						}
						
						
						$cdi[] = "</SELECT>";
						
						foreach($cdi as $text) $cannondamageinput .= $text;
						
						print "
							<DIV class='hov'>
								<DIV class='hovtitle'>Ágyúk</DIV>
								<DIV>Sebzés: $cannonhulldamage (Burkolat) / $cannonshielddamage (Pajzs)<BR>Energiahasználat: $cannonenergy, Lőszerhasználat: $cannonammo</DIV>
								<DIV class='input'>$cannondamageinput</DIV>
							</DIV>
						";
					}
					
					if($pulsenum)
					{
						$pdi[] = "";
						$pulsedamageinput = "";
						
						$pdi[] = "Sebzés: <SELECT name='id=ship&control=pulsedamage'>";
						$select100 = "";
						$select75 = "";
						$select50 = "";
						$select25 = "";
						$select0 = "";
						
						switch($character["control"]["ship"]->pulsedamage)
						{
							case 0:
								$select0 = "selected";
							break;
							case 25:
								$select25 = "selected";
							break;
							case 50:
								$select50 = "selected";
							break;
							case 75:
								$select75 = "selected";
							break;
							case 100:
								$select100 = "selected";
							break;
						}
						
						$pdi[] = "
							<OPTION value='100' $select100>100%</OPTION>
							<OPTION value='75' $select75>75%</OPTION>
							<OPTION value='50' $select50>50%</OPTION>
							<OPTION value='25' $select25>25%</OPTION>
							<OPTION value='0' $select0>Nem lő</OPTION>
						";
						$pdi[] = "</SELECT>";
						
						$pdi[] = "Lőszer: <SELECT name='id=ship&control=pulseammo'>";
						if(!isset($ioncells)) $pdi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
						else
						{
							ksort($ioncells);
							foreach($ioncells as $ammo)
							{
								$selected = "";
								if($ammo->itemid == $character["control"]["ship"]->pulseammo) $selected = "selected";
								$pdi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
							}
						}
						
						
						$pdi[] = "</SELECT>";
						
						foreach($pdi as $text) $pulsedamageinput .= $text;
						
						print "
							<DIV class='hov'>
								<DIV class='hovtitle'>Pulzuságyúk</DIV>
								<DIV>Sebzés: $pulsehulldamage (Burkolat) / $pulseshielddamage (Pajzs)<BR>Energiahasználat: $pulseenergy, Lőszerhasználat: $pulseammo</DIV>
								<DIV class='input'>$pulsedamageinput</DIV>
							</DIV>
						";
					}
				}
				
				function rocketlauncherinput($character)
				{
					$rocketlauncherhulldamage = 0;
					$sablauncherhulldamage = 0;
					$rocketlaunchershielddamage = 0;
					$sablaunchershielddamage = 0;
					$rocketlaunchernum = 0;
					$sablaunchernum = 0;
					$rocketlauncherenergy = 0;
					$sablauncherenergy = 0;
					$rocketlauncherammo = 0;
					$sablauncherammo = 0;
					
					foreach($character["ship"]["rocketlauncher"] as $weapon)
					{
						switch($weapon->itemtype)
						{
							case "rocketlauncher":
								$rocketlaunchernum += 1;
								$rocketlauncherenergy += $weapon->energyusage;
								$rocketlauncherammo += $weapon->ammousage;
								$rocketlauncherhulldamage += $weapon->hulldamage;
								$rocketlaunchershielddamage += $weapon->shielddamage;
							break;
							case "sablauncher":
								$sablaunchernum += 1;
								$sablauncherenergy += $weapon->energyusage;
								$sablauncherammo += $weapon->ammousage;
								$sablauncherhulldamage += $weapon->hulldamage;
								$sablaunchershielddamage += $weapon->shielddamage;
							break;
						}
					}
					
					if(isset($character["ammo"]))
					{
						foreach($character["ammo"] as $ammo)
						{
							switch($ammo->itemtype)
							{
								case "rocket":
									if($ammo->amount) $rockets["$ammo->itemid"] = $ammo;
								break;
								case "sabrocket":
									if($ammo->amount) $sabrockets["$ammo->itemid"] = $ammo;
								break;
							}
						}
					}
					
					if($rocketlaunchernum)
					{
						$rdi[] = "";
						$rocketlauncherdamageinput = "";
						
						$rdi[] = "Sebzés: <SELECT name='id=ship&control=rocketlauncherdamage'>";
						$select100 = "";
						$select75 = "";
						$select50 = "";
						$select25 = "";
						$select0 = "";
						
						switch($character["control"]["ship"]->rocketlauncherdamage)
						{
							case 0:
								$select0 = "selected";
							break;
							case 25:
								$select25 = "selected";
							break;
							case 50:
								$select50 = "selected";
							break;
							case 75:
								$select75 = "selected";
							break;
							case 100:
								$select100 = "selected";
							break;
						}
						
						$rdi[] = "
							<OPTION value='100' $select100>100%</OPTION>
							<OPTION value='75' $select75>75%</OPTION>
							<OPTION value='50' $select50>50%</OPTION>
							<OPTION value='25' $select25>25%</OPTION>
							<OPTION value='0' $select0>Nem lő</OPTION>
						";
						$rdi[] = "</SELECT>";
						
						$rdi[] = "Lőszer: <SELECT name='id=ship&control=rocketlauncherammo'>";
						if(!isset($rockets)) $rdi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
						else
						{
							ksort($rockets);
							foreach($rockets as $ammo)
							{
								$selected = "";
								if($ammo->itemid == $character["control"]["ship"]->rocketlauncherammo) $selected = "selected";
								$rdi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
							}
						}
						
						
						$rdi[] = "</SELECT>";
						
						foreach($rdi as $text) $rocketlauncherdamageinput .= $text;
						
						print "
							<DIV class='hov'>
								<DIV class='hovtitle'>Rakétakilövők</DIV>
								<DIV>Sebzés: $rocketlauncherhulldamage (Burkolat) / $rocketlaunchershielddamage (Pajzs)<BR>Energiahasználat: $rocketlauncherenergy, Lőszerhasználat: $rocketlauncherammo</DIV>
								<DIV class='input'>$rocketlauncherdamageinput</DIV>
							</DIV>
						";
					}
					
					if($sablaunchernum)
					{
						$sdi[] = "";
						$sablauncherdamageinput = "";
						
						$sdi[] = "Sebzés: <SELECT name='id=ship&control=sablauncherdamage'>";
						$select100 = "";
						$select75 = "";
						$select50 = "";
						$select25 = "";
						$select0 = "";
						
						switch($character["control"]["ship"]->sablauncherdamage)
						{
							case 0:
								$select0 = "selected";
							break;
							case 25:
								$select25 = "selected";
							break;
							case 50:
								$select50 = "selected";
							break;
							case 75:
								$select75 = "selected";
							break;
							case 100:
								$select100 = "selected";
							break;
						}
						
						$sdi[] = "
							<OPTION value='100' $select100>100%</OPTION>
							<OPTION value='75' $select75>75%</OPTION>
							<OPTION value='50' $select50>50%</OPTION>
							<OPTION value='25' $select25>25%</OPTION>
							<OPTION value='0' $select0>Nem lő</OPTION>
						";
						$sdi[] = "</SELECT>";
						
						$sdi[] = "Lőszer: <SELECT name='id=ship&control=sablauncherammo'>";
						if(!isset($sabrockets)) $sdi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
						else
						{
							ksort($sabrockets);
							foreach($sabrockets as $ammo)
							{
								$selected = "";
								if($ammo->itemid == $character["control"]["ship"]->sablauncherammo) $selected = "selected";
								$sdi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
							}
						}
						
						
						$sdi[] = "</SELECT>";
						
						foreach($sdi as $text) $sablauncherdamageinput .= $text;
						
						print "
							<DIV class='hov'>
								<DIV class='hovtitle'>SAB Rakétakilövők</DIV>
								<DIV>Sebzés: $sablauncherhulldamage (Burkolat) / $sablaunchershielddamage (Pajzs)<BR>Energiahasználat: $sablauncherenergy, Lőszerhasználat: $sablauncherammo</DIV>
								<DIV class='input'>$sablauncherdamageinput</DIV>
							</DIV>
						";
					}
				}
				
				function rifleinput($character)
				{
					$rifledamage = 0;
					$riflenum = 0;
					$rifleenergy = 0;
					$rifleammo = 0;
					
					foreach($character["ship"]["rifle"] as $weapon)
					{
						$rifledamage += $weapon->squadrondamage;
						$riflenum += 1;
						$rifleenergy += $weapon->energyusage;
						$rifleammo += $weapon->ammousage;
					}
					
					if(isset($character["ammo"]))
					{
						foreach($character["ammo"] as $ammo)
						{
							if($ammo->itemtype == "bullet" and $ammo->amount) $bullets["$ammo->itemid"] = $ammo;
						}
					}
					
					if($riflenum)
					{
						$rdi[] = "";
						$rifledamageinput = "";
						
						$rdi[] = "Sebzés: <SELECT name='id=ship&control=rifledamage'>";
						$select100 = "";
						$select75 = "";
						$select50 = "";
						$select25 = "";
						$select0 = "";
						
						switch($character["control"]["ship"]->rifledamage)
						{
							case 0:
								$select0 = "selected";
							break;
							case 25:
								$select25 = "selected";
							break;
							case 50:
								$select50 = "selected";
							break;
							case 75:
								$select75 = "selected";
							break;
							case 100:
								$select100 = "selected";
							break;
						}
						
						$rdi[] = "
							<OPTION value='100' $select100>100%</OPTION>
							<OPTION value='75' $select75>75%</OPTION>
							<OPTION value='50' $select50>50%</OPTION>
							<OPTION value='25' $select25>25%</OPTION>
							<OPTION value='0' $select0>Nem lő</OPTION>
						";
						$rdi[] = "</SELECT>";
						
						$rdi[] = "Lőszer: <SELECT name='id=ship&control=rifleammo'>";
						if(!isset($bullets)) $rdi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
						else
						{
							ksort($bullets);
							foreach($bullets as $ammo)
							{
								$selected = "";
								if($ammo->itemid == $character["control"]["ship"]->rifleammo) $selected = "selected";
								$rdi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
							}
						}
						
						
						$rdi[] = "</SELECT>";
						
						foreach($rdi as $text) $rifledamageinput .= $text;
						
						print "
							<DIV class='hov'>
								<DIV class='hovtitle'>Gépágyúk</DIV>
								<DIV>Sebzés: $rifledamage (Raj), Energiahasználat: $rifleenergy, Lőszerhasználat: $rifleammo</DIV>
								<DIV class='input'>$rifledamageinput</DIV>
							</DIV>
						";
					}
				}
		function equipments($character)
		{
			foreach($character["ship"]["equipment"] as $equipment) if($equipment->equipped) $equipped[] = $equipment;
			
			if(isset($equipped))
			{
				print "<DIV class='inputcontainer'>";
				print "<DIV class='hovtitle'>Felszerelések</DIV>";
				
				foreach($equipped as $equipment)
				{
					if(!$equipment->equipped)
					if(!isset($character["ship"]["battery"]) or !isset($character["ammo"])) break;
					$i[] = "";
					$input = "";
					
					$hundescription = $_SESSION["data"]["items"]["$equipment->itemid"]->hundescription;
					$i[] = "<DIV>$hundescription</DIV>";
					
					$ammoname = $_SESSION["data"]["items"]["$equipment->ammo"]->name;
					if(isset($character["ammo"]["$equipment->ammo"]))
					{
						$ammonum = $character["ammo"]["$equipment->ammo"]->amount;
					}
					else $ammonum = 0;
					$i[] = "
						<DIV>Lőszer: $ammoname ($equipment->ammousage/$ammonum), Energiahasználat: $equipment->energyusage</DIV>
					";
					
					if($equipment->actualreload)
					{
						$usable = "Töltődik ($equipment->actualreload)";
						$disabled = "disabled='disabled'";
						$cause = "Töltődés: $equipment->actualreload";
					}
					elseif($equipment->actualactive)
					{
						$usable = "Aktív ($equipment->actualactive)";
						$disabled = "disabled='disabled'";
						$cause = "Aktív ($equipment->actualactive)";
					}
					elseif(!ammoavailable($equipment, $character["ammo"]))
					{
						$usable = "Nincs elég lőszer";
						$disabled = "disabled='disabled'";
						$cause = "Nincs elég lőszer";
					}
					elseif(!energyavailable($equipment->energyusage, $character["ship"]["battery"]))
					{
						$usable = "Nincs elég energia";
						$disabled = "disabled='disabled'";
						$cause = "Nincs elég energia";
					}
					else
					{
						$usable = "Kész";
						$disabled = "";
						$cause = "";
					}
					
					if($disabled) $targets = "";
					else $targets = targetset($equipment->itemid, "equipment", "enemy");
					
					switch($equipment->itemid)
					{
						case "efi01":
						case "edi01":
						case "abs01":
						case "sre01":
						case "clo01":
						case "bol01":
						case "mac01":
						case "ser01":
						case "rep01":
						case "rep02":
						case "rep03":
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='control=use&itemid=$equipment->itemid' $disabled> $cause";
						break;
						case "pdu01":
						case "mdl01":
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='control=use&itemid=$equipment->itemid' $disabled>$targets $cause";
						break;
					}
					
					foreach($i as $text) $input .= $text;
					print "
						<DIV class='hov'>
							<DIV class='equipmenttitle'>$equipment->name ($usable)</DIV>
							<DIV class='input'>$input</DIV>
						</DIV>
					";
					unset($i);
				}
				
				
				print "</DIV>";
			}
			
		}
		
			function ammoavailable($equipment, $ammos)
			{
				if(!isset($ammos["$equipment->ammo"])) return 0;
				elseif($ammos["$equipment->ammo"]->amount < $equipment->ammousage) return 0;
				else return 1;
			}
			
			function energyavailable($energyusage, $batterys)
			{
				foreach($batterys as $battery)
				{
					if($battery->actualcapacity >= $energyusage) return 1;
				}
				return 0;
			}
			
			function targetset($itemid, $style, $targetstyle, $own = 1)
			{
				$result[] = "<SELECT name='itemid=$itemid&control=target&style=$style'>";
				$result[] = "<OPTION value='no'>Nincs célpont</OPTION>";
				
				$targetid = $_SESSION["game"]["player"]["control"]["ship"]->target;
				$targettry = $_SESSION["game"]["player"]["control"]["ship"]->targettry;
				foreach($_SESSION["gamedata"]["characters"]["$targetstyle"]["ships"] as $object)
				{
					if(!$own and $object->id == "player") continue;
					
					$selected = "";
					$shotme = "";
					$locked = "";
					if($object->id == $targetid and $targetid != "no")
					{
						$locked = "(Jelölve)";
					}
					if($object->id == $targettry or $object->id == $targetid)
					{
						$selected = "selected";
					}
					if($_SESSION["game"]["$object->id"]["control"]["ship"]->target == "player") $shotme = " (!)";
					$result[] = "<OPTION value='$object->id' $selected>$object->name $locked $shotme</OPTION>";
				}
				
				$result[] = "</SELECT>";
				$return = "";
				foreach($result as $text) $return .= $text;
				
				return $return;
			}
			
		function skills($character)
		{
			foreach($character["skill"] as $skill)
			{
				if($skill->owner == $character["ship"]["ship"][0]->company and $skill->level) $skills[] = $skill;
			}
			
			if(isset($skills))
			{
				print "<DIV class='inputcontainer'>";
				print "<DIV class='hovtitle'>Képességek</DIV>";
				
				foreach($skills as $skill)
				{
					$skilldata = $_SESSION["data"]["items"]["$skill->itemid"];
					
					$input = "";
					$i[] = "";
					$usable = "";
					$disabled = "";
					$cause = "";
					
					if($skilldata->itemtype != "passive")
					{
						
						$i[] = "
							<DIV>Energiahasználat: $skill->energyusage</DIV>
						";
						
						if($skill->actualreload)
						{
							$usable = "(Töltődik ($skill->actualreload))";
							$disabled = "disabled='disabled'";
							$cause = "Töltődés: $skill->actualreload";
						}
						elseif($skill->actualactive)
						{
							$usable = "(Aktív ($skill->actualactive))";
							$disabled = "disabled='disabled'";
							$cause = "Aktív ($skill->actualactive)";
						}
						elseif(!energyavailable($skill->energyusage, $character["ship"]["battery"]))
						{
							$usable = "(Nincs elég energia)";
							$disabled = "disabled='disabled'";
							$cause = "Nincs elég energia";
						}
						else
						{
							$usable = "(Kész)";
							$disabled = "";
							$cause = "";
						}
					}
					
					switch($skill->itemid)
					{
						case "emfa1":
						case "emfa2":
						case "idfa1":
						case "idfa2":
						case "mfaa1":
						case "mfaa2":
						case "gaaa1":
						case "cria1":
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='itemid=$skill->itemid&control=use' $disabled> $cause";
						break;
						case "pdma1":
							if($disabled) $targets = "";
							else $targets = targetset($skill->itemid, "skill", "friend");
						
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='itemid=$skill->itemid&control=use' $disabled>$targets $cause";
						break;
						case "pdma2":
							if($disabled) $targets = "";
							else $targets = targetset($skill->itemid, "skill", "friend", 0);
						
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='itemid=$skill->itemid&control=use' $disabled>$targets $cause";
						break;
						case "gaaa2":
						case "cria2":
							if($disabled) $targets = "";
							else $targets = targetset($skill->itemid, "skill", "enemy");
							
							$i[] = "<U>Használat:</U> <INPUT class='checkbox' type='checkbox' name='itemid=$skill->itemid&control=use' $disabled>$targets $cause";
						break;
					}
					
					foreach($i as $text) $input .= $text;
					unset($i);
					
					print "
						<DIV class='hov'>
							<DIV class='equipmenttitle'>$skilldata->name (Szint: $skill->level) $usable</DIV>
							<DIV class='input'>
								<DIV>$skilldata->hundescription</DIV>
								$input
							</DIV>
						</DIV>
					";
				}
				
				print "</DIV>";
			}
		}
		
		function squadrons($character)
		{
			if(!isset($character["squadrons"])) return;
			
			if(isset($character["ammo"]))
			{
				foreach($character["ammo"] as $ammo)
				{
					switch($ammo->itemtype)
					{
						case "cannonball":
							$cannonballs["$ammo->itemid"] = $ammo;
						break;
						case "ioncell":
							$ioncells["$ammo->itemid"] = $ammo;
						break;
						case "bullet":
							$bullets["$ammo->itemid"] = $ammo;
						break;
					}
				}
			}
			
								
			foreach($character["squadrons"] as $squadronid=>$squadron)
			{
				$group = $squadron["squadron"]->group;
				$groups["$group"]["$squadronid"] = $squadron;
			}
			
			print "<DIV class='inputcontainer'>";
			print "<DIV class='hovtitle'>Rajok</DIV>";
			
				foreach($groups as $groupid=>$members)
				{
					$groupdata = $character["groups"]["$groupid"];
					print "
						<DIV>
							<DIV class='groupname'>$groupdata->groupname</DIV>
					";
						
						$groupsquadroncannonnum = 0;
						$groupsquadronpulsenum = 0;
						$groupsquadronriflenum = 0;
						$groupsquadroncannonammousage = 0;
						$groupsquadroncannonenergyusage = 0;
						$groupsquadronpulseammousage = 0;
						$groupsquadronpulseenergyusage = 0;
						$groupsquadronrifleammousage = 0;
						$groupsquadronrifleenergyusage = 0;
						
						foreach($members as $squadronid=>$squadron)
						{
							$squadronname = $squadron["squadron"]->squadronname;
							$squadrongroup = $squadron["squadron"]->group;
							$s[] = "<DIV class='squadron'>";
							$squadronitemid = $squadron["squadron"]->itemid;
							$squadronitemname = $_SESSION["data"]["items"]["$squadronitemid"]->name;
							switch($character["control"]["$squadronid"]->place)
							{
								case "space":
									$placename = "Felszállt";
								break;
								case "hangar":
									$placename = "Hangárban";
								break;
								case "dead":
									$placename = "Megsemmisült";
								break;
							}
							if($character["control"]["$squadronid"]->callbackcount and $character["control"]["$squadronid"]->place != "dead") $cb = " - Visszatér";
							else $cb = "";
							
							$s[] = "<DIV class='squadrontitle'>$squadronname ($placename$cb)</DIV>";
							$s[] = "<DIV class='squadronitemname'>($squadronitemname)</DIV>";
							$s[] = squadronbars($squadron);
							
							$squadroncannonnum = 0;
							$squadroncannonhulldamage = 0;
							$squadroncannonshielddamage = 0;
							$squadroncannonammousage = 0;
							$squadroncannonenergyusage = 0;
							
							$squadronpulsenum = 0;
							$squadronpulsehulldamage = 0;
							$squadronpulseshielddamage = 0;
							$squadronpulseammousage = 0;
							$squadronpulseenergyusage = 0;
							
							$squadronriflenum = 0;
							$squadronrifledamage = 0;
							$squadronrifleammousage = 0;
							$squadronrifleenergyusage = 0;
							
							if(isset($squadron["squadroncannon"]))
							{
								foreach($squadron["squadroncannon"] as $weapon)
								{
									switch($weapon->itemtype)
									{
										case "squadroncannon":
											$groupsquadroncannonnum += 1;
											$groupsquadroncannonammousage += $weapon->ammousage;
											$groupsquadroncannonenergyusage += $weapon->energyusage;
											$squadroncannonnum += 1;
											$squadroncannonhulldamage += $weapon->hulldamage;
											$squadroncannonshielddamage += $weapon->shielddamage;
											$squadroncannonammousage += $weapon->ammousage;
											$squadroncannonenergyusage += $weapon->energyusage;
										break;
										case "squadronpulse":
											$groupsquadronpulsenum += 1;
											$groupsquadronpulseammousage += $weapon->ammousage;
											$groupsquadronpulseenergyusage += $weapon->energyusage;
											$squadronpulsenum += 1;
											$squadronpulsehulldamage += $weapon->hulldamage;
											$squadronpulseshielddamage += $weapon->shielddamage;
											$squadronpulseammousage += $weapon->ammousage;
											$squadronpulseenergyusage += $weapon->energyusage;
										break;
										case "squadronrifle":
											$groupsquadronriflenum += 1;
											$groupsquadronrifleammousage += $weapon->ammousage;
											$groupsquadronrifleenergyusage += $weapon->energyusage;
											$squadronriflenum += 1;
											$squadronrifledamage += $weapon->squadrondamage;
											$squadronrifleammousage += $weapon->ammousage;
											$squadronrifleenergyusage += $weapon->energyusage;
										break;
									}
								}
							}
							
							$squadroncannoninput = "";
							$squadronpulseinput = "";
							$squadronrifleinput = "";
								
								foreach($character["squadrons"] as $squadronid2=>$a) $squadrontomb[] = $squadronid2;
							
								if($squadrongroup == "no" and $character["control"]["$squadronid"]->place == "space" and isset($squadron["squadroncannon"])) $s[] = squadrontarget($squadron["squadroncannon"], $character["control"]["$squadronid"]->target, $squadronid, $squadrontomb);
								elseif($character["control"]["$squadronid"]->place == "space")
								{
									$targetid = $character["control"]["$squadronid"]->target;
									if($targetid == "no") $targetname = "Nincs célpont";
									else
									{
										foreach($_SESSION["gamedata"]["characters"]["enemy"] as $types)
										{
											foreach($types as $typemember)
											{
												if($typemember->id == $targetid) $targetname = $typemember->name;
											}
										}
									}
									$s[] = "<DIV class='target'>Célpont: $targetname</DIV>";
								}
								$s[] = "
									<DIV class='squadronhov'>
										<DIV class='squadronhovtitle'>Vezérlés</DIV>
								";
								$s[] = "<DIV class='input'>";
								
								$selectedauto = "";
								$selectedmanual = "";
								$selected100 = "";
								$selected75 = "";
								$selected50 = "";
								$selected25 = "";
								$selected0 = "";
								$selactauto = "";
								$selectmanual = "";
								$s75 = "";
								$s50 = "";
								$s25 = "";
								$h75 = "";
								$h50 = "";
								$h25 = "";
								$sh0 = "";
								
								switch($character["control"]["$squadronid"]->targetselect)
								{
									case "auto":
										$selectedauto = "selected";
									break;
									case "manual":
										$selectedmanual = "selected";
									break;
								}
								switch($character["control"]["$squadronid"]->squadronshieldregen)
								{
									case 100:
										$selected100 = "selected";
									break;
									case 75:
										$selected75 = "selected";
									break;
									case 50:
										$selected50 = "selected";
									break;
									case 25:
										$selected25 = "selected";
									break;
									case 0:
										$selected0 = "selected";
									break;
								}
								if($character["control"]["$squadronid"]->returnstyle == "shield" and $character["control"]["$squadronid"]->returnvalue == 75) $s75 = "selected";
								elseif($character["control"]["$squadronid"]->returnstyle == "shield" and $character["control"]["$squadronid"]->returnvalue == 50) $s50 = "selected";
								elseif($character["control"]["$squadronid"]->returnstyle == "shield" and $character["control"]["$squadronid"]->returnvalue == 25) $s25 = "selected";
								elseif($character["control"]["$squadronid"]->returnstyle == "hull" and $character["control"]["$squadronid"]->returnvalue == 75) $h75 = "selected";
								elseif($character["control"]["$squadronid"]->returnstyle == "hull" and $character["control"]["$squadronid"]->returnvalue == 50) $h50 = "selected";
								elseif($character["control"]["$squadronid"]->returnstyle == "hull" and $character["control"]["$squadronid"]->returnvalue == 25) $h25 = "selected";
								else $sh0 = "selected";
								
								switch($character["control"]["$squadronid"]->takeoff)
								{
									case "auto":
										$selectauto = "selected";
									break;
									case "manual":
										$selectmanual = "selected";
									break;
								}
								
								if($squadrongroup == "no")
								{
									$s[] = "
										<DIV class='squadroncontrol'>
											Célpontválasztás: 
											<SELECT name='id=$squadronid&control=targetselect'>
												<OPTION value='auto' $selectedauto>Automatikus</OPTION>
												<OPTION value='manual' $selectedmanual>Kézi</OPTION>
											</SELECT>
											</DIV>
										<DIV class='squadroncontrol'>
											Visszatérés: 
											<SELECT name='id=$squadronid&control=return'>
												<OPTION value='returnstyle=shield&returnvalue=75' $s75>75% pajzs alatt</OPTION>
												<OPTION value='returnstyle=shield&returnvalue=50' $s50>50% pajzs alatt</OPTION>
												<OPTION value='returnstyle=shield&returnvalue=25' $s25>25% pajzs alatt</OPTION>
												<OPTION value='returnstyle=hull&returnvalue=75' $h75>75% burkolat alatt</OPTION>
												<OPTION value='returnstyle=hull&returnvalue=50' $h50>50% burkolat alatt</OPTION>
												<OPTION value='returnstyle=hull&returnvalue=25' $h25>25% burkolat alatt</OPTION>
												<OPTION value='returnstyle=manual&returnvalue=0' $sh0>Kézi</OPTION>
											</SELECT>
											Felszállás: 
											<SELECT name='id=$squadronid&control=takeoff'>
												<OPTION value='auto' $selectauto>Automatikus</OPTION>
												<OPTION value='manual' $selectmanual>Kézi</OPTION>
											</SELECT>
										</DIV>
										
									";
									if($character["control"]["$squadronid"]->place == "space")
									{
										$calltitle = "Visszahívás:";
										$callname = "id=$squadronid&control=callback";
									}
									else
									{
										$calltitle = "Felszállás:";
										$callname = "id=$squadronid&control=go";
									}
									
									$s[] = "
										<DIV class='squadroncontrol'>
											$calltitle <INPUT class='checkbox' type='checkbox' name='$callname'>
										</DIV>
									";
								}
								$s[] = "
										Pajzsregeneráció: 
										<SELECT name='id=$squadronid&control=squadronshieldregen'>
											<OPTION value='100' $selected100>100%</OPTION>
											<OPTION value='75' $selected75>75%</OPTION>
											<OPTION value='50' $selected50>50%</OPTION>
											<OPTION value='25' $selected25>25%</OPTION>
											<OPTION value='0' $selected0>Nincs</OPTION>
										</SELECT>
									
									
								";
								
								$s[] = "</DIV>";
								$s[] = "</DIV>";
								
								if($squadroncannonnum)
								{
									$sqi[] = "<DIV class='input'>";
									
									$sqi[] = "Sebzés: <SELECT name='id=$squadronid&control=squadroncannondamage'>";
									$select100 = "";
									$select75 = "";
									$select50 = "";
									$select25 = "";
									$select0 = "";
									
									switch($character["control"]["$squadronid"]->squadroncannondamage)
									{
										case 0:
											$select0 = "selected";
										break;
										case 25:
											$select25 = "selected";
										break;
										case 50:
											$select50 = "selected";
										break;
										case 75:
											$select75 = "selected";
										break;
										case 100:
											$select100 = "selected";
										break;
									}
									
									$sqi[] = "
										<OPTION value='100' $select100>100%</OPTION>
										<OPTION value='75' $select75>75%</OPTION>
										<OPTION value='50' $select50>50%</OPTION>
										<OPTION value='25' $select25>25%</OPTION>
										<OPTION value='0' $select0>Nem lő</OPTION>
									";
									$sqi[] = "</SELECT>";
									
									$sqi[] = "Lőszer: <SELECT name='id=$squadronid&control=cannonammo'>";
									if(!isset($cannonballs)) $sqi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
									else
									{
										ksort($cannonballs);
										foreach($cannonballs as $ammo)
										{
											$selected = "";
											if($ammo->itemid == $character["control"]["$squadronid"]->squadroncannonammo) $selected = "selected";
											$sqi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
										}
									}
									$sqi[] = "</SELECT>";
									$sqi[] = "</DIV>";
						
						
									foreach($sqi as $text) $squadroncannoninput .= $text;
									unset($sqi);
								}
								
								if($squadronpulsenum)
								{
									$spi[] = "<DIV class='input'>";
									
									$spi[] = "Sebzés: <SELECT name='id=$squadronid&control=squadronpulsedamage'>";
									$select100 = "";
									$select75 = "";
									$select50 = "";
									$select25 = "";
									$select0 = "";
									
									switch($character["control"]["$squadronid"]->squadronpulsedamage)
									{
										case 0:
											$select0 = "selected";
										break;
										case 25:
											$select25 = "selected";
										break;
										case 50:
											$select50 = "selected";
										break;
										case 75:
											$select75 = "selected";
										break;
										case 100:
											$select100 = "selected";
										break;
									}
									
									$spi[] = "
										<OPTION value='100' $select100>100%</OPTION>
										<OPTION value='75' $select75>75%</OPTION>
										<OPTION value='50' $select50>50%</OPTION>
										<OPTION value='25' $select25>25%</OPTION>
										<OPTION value='0' $select0>Nem lő</OPTION>
									";
									$spi[] = "</SELECT>";
									
									$spi[] = "Lőszer: <SELECT name='id=$squadronid&control=squadronpulseammo'>";
									if(!isset($ioncells)) $spi[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
									else
									{
										ksort($ioncells);
										foreach($ioncells as $ammo)
										{
											$selected = "";
											if($ammo->itemid == $character["control"]["$squadronid"]->squadronpulseammo) $selected = "selected";
											$spi[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
										}
									}
									
									$spi[] = "</SELECT>";
									
									$spi[] = "</DIV>";
									foreach($spi as $text) $squadronpulseinput .= $text;
									unset($spi);
								}
								
								if($squadronriflenum)
								{
									$sri[] = "<DIV class='input'>";
									
									$sri[] = "Sebzés: <SELECT name='id=$squadronid&control=squadronrifledamage'>";
									$select100 = "";
									$select75 = "";
									$select50 = "";
									$select25 = "";
									$select0 = "";
									
									switch($character["control"]["$squadronid"]->squadronrifledamage)
									{
										case 0:
											$select0 = "selected";
										break;
										case 25:
											$select25 = "selected";
										break;
										case 50:
											$select50 = "selected";
										break;
										case 75:
											$select75 = "selected";
										break;
										case 100:
											$select100 = "selected";
										break;
									}
									
									$sri[] = "
										<OPTION value='100' $select100>100%</OPTION>
										<OPTION value='75' $select75>75%</OPTION>
										<OPTION value='50' $select50>50%</OPTION>
										<OPTION value='25' $select25>25%</OPTION>
										<OPTION value='0' $select0>Nem lő</OPTION>
									";
									$sri[] = "</SELECT>";
									
									$sri[] = "Lőszer: <SELECT name='id=$squadronid&control=squadronrifleammo'>";
									if(!isset($bullets)) $sri[] = "<OPTION value='no'>Nincs lőszer</OPTION>";
									else
									{
										ksort($bullets);
										foreach($bullets as $ammo)
										{
											$selected = "";
											if($ammo->itemid == $character["control"]["$squadronid"]->squadronrifleammo) $selected = "selected";
											$sri[] = "<OPTION value='$ammo->itemid' $selected>$ammo->name ($ammo->amount)</OPTION>";
										}
									}
									$sri[] = "</SELECT>";
									$sri[] = "</DIV>";
									
									foreach($sri as $text) $squadronrifleinput .= $text;
									unset($sri);
								}
							
							if($squadroncannonnum) $s[] = "
								<DIV class='squadronhov'>
									<DIV class='squadronhovtitle'>Rajágyúk</DIV>
									<DIV>Sebzés: $squadroncannonhulldamage (Burkolat) / $squadroncannonshielddamage (Pajzs)</DIV>
									<DIV>Energiahasználat: $squadroncannonenergyusage, Lőszerhasználat: $squadroncannonammousage</DIV>
									$squadroncannoninput
								</DIV>
							";
							if($squadronpulsenum) $s[] = "
								<DIV class='squadronhov'>
									<DIV class='squadronhovtitle'>Raj Pulzuságyúk</DIV>
									<DIV>Sebzés: $squadronpulsehulldamage (Burkolat) / $squadronpulseshielddamage (Pajzs)</DIV>
									<DIV>Energiahasználat: $squadronpulseenergyusage, Lőszerhasználat: $squadronpulseammousage</DIV>
									$squadronpulseinput
								</DIV>
							";
							if($squadronriflenum) $s[] = "
								<DIV class='squadronhov'>
									<DIV class='squadronhovtitle'>Raj Gépágyúk</DIV>
									<DIV>Sebzés: $squadronrifledamage (Raj), Energiahasználat: $squadronrifleenergyusage, Lőszerhasználat: $squadronrifleammousage</DIV>
									<DIV></DIV>
									$squadronrifleinput
								</DIV>
							";
							
							$s[] = "</DIV>";
							
							$st = "";
							foreach($s as $text) $st .= $text;
							$memberstext[] = $st;
							unset($s);
						}
						
						if($groupid != "no")
						{
							print grouptarget($groupid, $members, $character["control"]["$groupid"]->target);
							print "
								<DIV class='squadronhov'>
									<DIV class='squadronhovtitle'>Vezérlés</DIV>
							";
							
							print "<DIV class='input'>";
							
							$selectedauto = "";
							$selectedmanual = "";
							$selected100 = "";
							$selected75 = "";
							$selected50 = "";
							$selected25 = "";
							$selected0 = "";
							$selactauto = "";
							$selectmanual = "";
							$s75 = "";
							$s50 = "";
							$s25 = "";
							$h75 = "";
							$h50 = "";
							$h25 = "";
							$sh0 = "";
							
							switch($character["control"]["$groupid"]->targetselect)
							{
								case "auto":
									$selectedauto = "selected";
								break;
								case "manual":
									$selectedmanual = "selected";
								break;
							}
							switch($character["control"]["$groupid"]->squadronshieldregen)
							{
								case 100:
									$selected100 = "selected";
								break;
								case 75:
									$selected75 = "selected";
								break;
								case 50:
									$selected50 = "selected";
								break;
								case 25:
									$selected25 = "selected";
								break;
								case 0:
									$selected0 = "selected";
								break;
							}
							if($character["control"]["$groupid"]->returnstyle == "shield" and $character["control"]["$groupid"]->returnvalue == 75) $s75 = "selected";
							elseif($character["control"]["$groupid"]->returnstyle == "shield" and $character["control"]["$groupid"]->returnvalue == 50) $s50 = "selected";
							elseif($character["control"]["$groupid"]->returnstyle == "shield" and $character["control"]["$groupid"]->returnvalue == 25) $s25 = "selected";
							elseif($character["control"]["$groupid"]->returnstyle == "hull" and $character["control"]["$groupid"]->returnvalue == 75) $h75 = "selected";
							elseif($character["control"]["$groupid"]->returnstyle == "hull" and $character["control"]["$groupid"]->returnvalue == 50) $h50 = "selected";
							elseif($character["control"]["$groupid"]->returnstyle == "hull" and $character["control"]["$groupid"]->returnvalue == 25) $h25 = "selected";
							else $sh0 = "selected";
							
							switch($character["control"]["$groupid"]->takeoff)
							{
								case "auto":
									$selectauto = "selected";
								break;
								case "manual":
									$selectmanual = "selected";
								break;
							}
							
							print "
								<DIV class='squadroncontrol'>
									Célpontválasztás: 
									<SELECT name='id=$groupid&control=targetselect'>
										<OPTION value='auto' $selectedauto>Automatikus</OPTION>
										<OPTION value='manual' $selectedmanual>Kézi</OPTION>
									</SELECT>
								</DIV>
								<DIV class='squadroncontrol'>
									Visszatérés: 
									<SELECT name='id=$groupid&control=return'>
										<OPTION value='returnstyle=shield&returnvalue=75' $s75>75% pajzs alatt</OPTION>
										<OPTION value='returnstyle=shield&returnvalue=50' $s50>50% pajzs alatt</OPTION>
										<OPTION value='returnstyle=shield&returnvalue=25' $s25>25% pajzs alatt</OPTION>
										<OPTION value='returnstyle=hull&returnvalue=75' $h75>75% burkolat alatt</OPTION>
										<OPTION value='returnstyle=hull&returnvalue=50' $h50>50% burkolat alatt</OPTION>
										<OPTION value='returnstyle=hull&returnvalue=25' $h25>25% burkolat alatt</OPTION>
										<OPTION value='returnstyle=manual&returnvalue=0' $sh0>Kézi</OPTION>
									</SELECT>
									Felszállás: 
									<SELECT name='id=$groupid&control=takeoff'>
										<OPTION value='auto' $selectauto>Automatikus</OPTION>
										<OPTION value='manual' $selectmanual>Kézi</OPTION>
									</SELECT>
								</DIV>
							";
							if($character["control"]["$groupid"]->place == "space")
							{
								$calltitle = "Visszahívás:";
								$callname = "id=$groupid&control=callback";
							}
							else
							{
								$calltitle = "Felszállás:";
								$callname = "id=$groupid&control=takeoff";
							}
							
							print "
								<DIV class='squadroncontrol'>
									$calltitle <INPUT class='checkbox' type='checkbox' name='$callname'>
								</DIV>
							";
							print "</DIV>";
							print "</DIV>";
						}
						
						foreach($memberstext as $text) print $text;
						unset($memberstext);
					print "</DIV>";
				}
				
			print "</DIV>";
		}
		
			function squadronbars($squadron)
			{
				$r[] = "";
				
				$r[] = "<DIV class='bars'>";
				
				$corehull = $squadron["squadron"]->corehull;
				$actualcorehull = $squadron["squadron"]->actualcorehull;
				$corehullwidth = $actualcorehull / $corehull * 100;
				settype($corehullwidth, "integer");
				
				$hullenergy = 0;
				$actualhull = 0;
				$hullwidth = 0;
				if(isset($squadron["squadronhull"]))
				{
					foreach($squadron["squadronhull"] as $hull)
					{
						$hullenergy += $hull->hullenergy;
						$actualhull += $hull->actualhull;
					}
					$hullwidth = $actualhull / $hullenergy * 100;
					settype($hullwidth, "integer");
				}
				
				$shieldenergy = 0;
				$actualshield = 0;
				$shieldwidth = 0;
				$recharge = 0;
				if(isset($squadron["squadronshield"]))
				{
					foreach($squadron["squadronshield"] as $shield)
					{
						$shieldenergy += $shield->shieldenergy;
						$actualshield += $shield->actualshield;
						$recharge += $shield->recharge;
					}
					$shieldwidth = $actualshield / $shieldenergy * 100;
					settype($shieldwidth, "integer");
				}
				
				$capacity = 0;
				$actualcapacity = 0;
				$energywidth = 0;
				if(isset($squadron["battery"]))
				{
					foreach($squadron["battery"] as $battery)
					{
						$capacity += $battery->capacity;
						$actualcapacity += $battery->actualcapacity;
					}
					$energywidth = $actualcapacity / $capacity * 100;
					settype($energywidth, "integer");
				}
				
				$basicammostorage = $squadron["squadron"]->basicammostorage;
				$actualammostorage = $squadron["squadron"]->actualammostorage;
				$ammowidth = $actualammostorage / $basicammostorage * 100;
				
				
				$r[] = "
					<DIV class='bar'>
						<IMG class='bar' src='pixelred.jpg' width='$corehullwidth%'>
						<DIV class='bartext'>Magburkolat ($corehullwidth% - $actualcorehull / $corehull)</DIV>
					</DIV>
					<DIV class='bar'>
						<IMG class='bar' src='pixelgreen.jpg' width='$hullwidth%'>
						<DIV class='bartext'>Burkolat ($hullwidth% - $actualhull / $hullenergy)</DIV>
					</DIV>
					<DIV class='bar'>
						<IMG class='bar' src='pixelblue.jpg' width='$shieldwidth%'>
						<DIV class='bartext'>Pajzs ($shieldwidth% - $actualshield / $shieldenergy - +$recharge / kör)</DIV>
					</DIV>
					<DIV class='bar'>
						<IMG class='bar' src='pixelyellow.jpg' width='$energywidth%'>
						<DIV class='bartext'>Energia ($energywidth% - $actualcapacity / $capacity)</DIV>
					</DIV>
					<DIV class='bar'>
						<IMG class='bar' src='pixelpurple.jpg' width='$ammowidth%'>
						<DIV class='bartext'>Lőszerraktár ($ammowidth% - $actualammostorage / $basicammostorage)</DIV>
					</DIV>
				";
				
				
				$r[] = "</DIV>";
				
				$result = "";
				foreach($r as $text) $result .= $text;
				
				return $result;
			}
			
			function squadrontarget($weapons, $targetid, $squadronid, $squadrontomb)
			{
				$riflenum = 0;
				$cannonnum = 0;
				foreach($weapons as $weapon)
				{
					if($weapon->itemtype == "squadronrifle") $riflenum += 1;
					else $cannonnum += 1;
				}
				
				if($cannonnum and isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"]))
				{
					foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $target)
					{
						$target->nmetarget = $_SESSION["game"]["$target->id"]["control"]["ship"]->target;
						$targets[] = $target;
					}
				}
				if($riflenum and isset($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"]))
				{
					foreach($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"] as $target)
					{
						if($_SESSION["game"]["$target->owner"]["control"]["$target->id"]->place == "space")
						{
							$target->nmetarget = $_SESSION["game"]["$target->owner"]["control"]["$target->id"]->target;
							$targets[] = $target;
						}
					}
				}
				
				$noselected = "";
				if($targetid == "no") $noselected = "selected";
				$targetlist = "
					<OPTION value='no' $noselected>Nincs célpont</OPTION>
				";
				
				if(isset($targets))
				{
					foreach($targets as $target)
					{
						$selected = "";
						$shiptarget = "";
						$squadrontarget = "";
						if($targetid and $targetid == $target->id) $selected = "selected";
						if($target->nmetarget == "player") $shiptarget = " (!)";
						if(in_array($target->nmetarget, $squadrontomb)) $squadrontarget = "(S)";
						
						
						$targetlist .= "<OPTION value='$target->id' $selected>$target->name $shiptarget$squadrontarget</OPTION>";
					}
				}
				
				return "
					<DIV>
						Célpont: 
						<SELECT class='long' name='id=$squadronid&control=targettry'>
							$targetlist
						</SELECT>
					</DIV>
				";
			}
			
			function grouptarget($groupid, $members, $targetid)
			{
				$riflenum = 0;
				$cannonnum = 0;
				foreach($members as $member)
				{
					foreach($member["squadroncannon"] as $weapon)
					{
						if($weapon->itemtype == "squadronrifle") $riflenum += 1;
						else $cannonnum += 1;
					}
				}
				
				if($cannonnum and isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"]))
				{
					foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $target)
					{
						$target->nmetarget = $_SESSION["game"]["$target->id"]["control"]["ship"]->target;
						$targets[] = $target;
					}
				}
				if($riflenum and isset($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"]))
				{
					foreach($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"] as $target)
					{
						if($_SESSION["game"]["$target->owner"]["control"]["$target->id"]->place == "space")
						{
							$target->nmetarget = $_SESSION["game"]["$target->owner"]["control"]["$target->id"]->target;
							$targets[] = $target;
						}
					}
				}
				
				$noselected = "";
				if($targetid == "no") $noselected = "selected";
				$targetlist = "
					<OPTION value='no' $noselected>Nincs célpont</OPTION>
				";
				
				if(isset($targets))
				{
					foreach($targets as $target)
					{
						$selected = "";
						if($targetid and $targetid == $target->id) $selected = "selected";
						
						$targetlist .= "<OPTION value='$target->id' $selected>$target->name</OPTION>";
					}
				}
				
				return "
					<DIV>
						Célpont: 
						<SELECT class='long' name='id=$groupid&control=targettry'>
							$targetlist
						</SELECT>
					</DIV>
				";
			}
?>