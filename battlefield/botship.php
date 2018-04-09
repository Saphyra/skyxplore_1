<?php
	function botship($character)
	{
		print "<DIV class='botship'>";
		
		$botname = $character["charname"];
		print "<DIV class='botname'>$botname</DIV>";
		
		$rank = rankset($character["charid"]);
		print "<DIV class='rank'>$rank</DIV>";
		
		$shipid = $character["ship"]["ship"][0]->itemid;
		$botshipname = $_SESSION["data"]["items"]["$shipid"]->name;
		print "<DIV class='botshipname'>$botshipname</DIV>";
		
		print "<DIV class='bars'>";
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
			
			if(isset($character["ship"]["battery"]))
			{
					foreach($character["ship"]["battery"] as $battery)
				{
					$capacity += $battery->capacity;
					$actualcapacity += $battery->actualcapacity;
					$capacitytomb[] = $actualcapacity / $capacity;
				}
				
				$energywidth = $actualcapacity / $capacity * 100;
			}
			else $energywidth = 0;
			settype($energywidth, "integer");
			
			$maxcapacity = 0;
			$capacitystatusitem = 0;
			$energystatus = 0;
			if(isset($capacitytomb))
			{
				foreach($capacitytomb as $capacitystatus)
				{
					$maxcapacity += 1;
					$capacitystatusitem += $capacitystatus;
				}
				$energystatus = $capacitystatusitem / $maxcapacity * 100;
			}
			
			
			settype($energystatus, "integer");
			
			print "
				<DIV class='bar'>
					<IMG class='bar' src='pixelred.jpg' width='$corehullwidth%'>
					<DIV class='bartext'>Magburkolat ($corehullwidth%)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelgreen.jpg' width='$hullwidth%'>
					<DIV class='bartext'>Burkolat ($hullwidth% - Penetráció: $hullpenetration%)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelblue.jpg' width='$shieldwidth%'>
					<DIV class='bartext'>Pajzs ($shieldwidth% - Penetráció: $shieldpenetration%)</DIV>
				</DIV>
				<DIV class='bar'>
					<IMG class='bar' src='pixelyellow.jpg' width='$energywidth%'>
					<DIV class='bartext'>Energia ($energywidth% - Állapot: $energystatus%)</DIV>
				</DIV>
			";
		print "</DIV>";
		
		boteffects($character);
		
		$targetname = "";
		$targetid = $character["control"]["ship"]->target;
		if($targetid == "no")
		{
			$targetname = "Nincs célpont";
		}
		else $targetname = targetnamesearch($targetid);
		
		print "<DIV class='target'>Célpont: $targetname</DIV>";
		
		$cannonhulldamage = 0;
		$cannonshielddamage = 0;
		$pulsehulldamage = 0;
		$pulseshielddamage = 0;
		$rocketlauncherhulldamage = 0;
		$rocketlaunchershielddamage = 0;
		$sablauncherhulldamage = 0;
		$sablaunchershielddamage = 0;
		$rifledamage = 0;
		$damage = 0;
		
		if(isset($character["ship"]["cannon"]))
		{
			foreach($character["ship"]["cannon"] as $cannon)
			{
				switch($cannon->itemtype)
				{
					case "cannon":
						$cannonhulldamage += $cannon->hulldamage;
						$cannonshielddamage += $cannon->shielddamage;
						$cannonnum = 1;
						$damage = 1;
					break;
					case "pulse":
						$pulsehulldamage += $cannon->hulldamage;
						$pulseshielddamage += $cannon->shielddamage;
						$pulsenum = 1;
						$damage = 1;
					break;
				}
			}
		}
		
		if(isset($character["ship"]["rocketlauncher"]))
		{
			foreach($character["ship"]["rocketlauncher"] as $rocketlauncher)
			{
				switch($rocketlauncher->itemtype)
				{
					case "rocketlauncher":
						$rocketlauncherhulldamage += $rocketlauncher->hulldamage;
						$rocketlaunchershielddamage += $rocketlauncher->shielddamage;
						$rocketlaunchernum = 1;
						$damage = 1;
					break;
					case "sablauncher":
						$sablauncherhulldamage += $rocketlauncher->hulldamage;
						$sablaunchershielddamage += $rocketlauncher->shielddamage;
						$sablaunchernum = 1;
						$damage = 1;
					break;
				}
			}
		}
		
		if(isset($character["ship"]["rifle"]))
		{
			foreach($character["ship"]["rifle"] as $rifle)
			{
				$rifledamage += $rifle->squadrondamage;
				$riflenum = 1;
				$damage = 1;
			}
		}
		
		if(isset($damage))
		{
			print "<DIV class='hov'>";
			print "<DIV class='damagetitle'>Sebzés</DIV>";
			
			if(isset($cannonnum))
			{
				$ammoid = $character["control"]["ship"]->cannonammo;
				$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
				
				print "
					<DIV class='hovtitle'>Ágyú</DIV>
					<DIV>$cannonhulldamage (Burkolat) / $cannonshielddamage (Pajzs)</DIV>
					<DIV>Lőszer: $ammoname</DIV>
				";
			}
			
			if(isset($pulsenum))
			{
				$ammoid = $character["control"]["ship"]->pulseammo;
				$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
				
				print "
					<DIV class='hovtitle'>Pulzuságyú</DIV>
					<DIV>$pulsehulldamage (Burkolat) / $pulseshielddamage (Pajzs)</DIV>
					<DIV>Lőszer: $ammoname</DIV>
				";
			}
			
			if(isset($rocketlaunchernum))
			{
				$ammoid = $character["control"]["ship"]->rocketlauncherammo;
				$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
				
				print "
					<DIV class='hovtitle'>Rakétakilövő</DIV>
					<DIV>$rocketlauncherhulldamage (Burkolat) / $rocketlaunchershielddamage (Pajzs)</DIV>
					<DIV>Lőszer: $ammoname</DIV>
				";
			}
			
			if(isset($sablaunchernum))
			{
				$ammoid = $character["control"]["ship"]->sablauncherammo;
				$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
				
				print "
					<DIV class='hovtitle'> SAB Rakétakilövő</DIV>
					<DIV>$sablauncherhulldamage (Burkolat) / $sablaunchershielddamage (Pajzs)</DIV>
					<DIV>Lőszer: $ammoname</DIV>
				";
			}
			
			if(isset($riflenum))
			{
				$ammoid = $character["control"]["ship"]->rifleammo;
				$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
				
				print "
					<DIV class='hovtitle'>Gépágyú</DIV>
					<DIV>$rifledamage (Raj), Lőszer: $ammoname</DIV>
				";
			}
			print "</DIV>";
			
			if(isset($character["squadrons"]))
			{
				print "<DIV class='inputcontainer'>";
				print "<DIV class='hovtitle'>Rajok</DIV>";
				
				foreach($character["squadrons"] as $squadronid=>$squadron)
				{
					$group = $squadron["squadron"]->group;
					$groups["$group"]["$squadronid"] = $squadron;
				}
				
				foreach($groups as $groupid=>$members)
				{
					$groupdata = $character["groups"]["$groupid"];
					print "
						<DIV>
							<DIV class='groupname'>$groupdata->groupname</DIV>
					";
						
					foreach($members as $squadronid=>$squadron)
					{
						if($character["control"]["$squadronid"]->place == "space")
						{
							$squadronname = $squadron["squadron"]->squadronname;
							$squadrongroup = $squadron["squadron"]->group;
							$squadronitemid = $squadron["squadron"]->itemid;
							$squadronitemname = $_SESSION["data"]["items"]["$squadronitemid"]->name;
							
							if($character["control"]["$squadronid"]->callbackcount and $character["control"]["$squadronid"]->place != "dead") $cb = " (Visszatér)";
							else $cb = "";
							
							print "<DIV class='squadron' id='squad'>";
							print "<DIV class='squadrontitle'>$squadronname$cb</DIV>";
							print "<DIV class='squadronitemname'>($squadronitemname)</DIV>";
					
							
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
							if(isset($squadron["squadronshield"]))
							{
								foreach($squadron["squadronshield"] as $shield)
								{
									$shieldenergy += $shield->shieldenergy;
									$actualshield += $shield->actualshield;
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
							
							print "
								<DIV class='bars'>
									<DIV class='bar'>
										<IMG class='bar' src='pixelred.jpg' width='$corehullwidth%'>
										<DIV class='bartext'>Magburkolat ($corehullwidth%)</DIV>
									</DIV>
									<DIV class='bar'>
										<IMG class='bar' src='pixelgreen.jpg' width='$hullwidth%'>
										<DIV class='bartext'>Burkolat ($hullwidth%)</DIV>
									</DIV>
									<DIV class='bar'>
										<IMG class='bar' src='pixelblue.jpg' width='$shieldwidth%'>
										<DIV class='bartext'>Pajzs ($shieldwidth%)</DIV>
									</DIV>
									<DIV class='bar'>
										<IMG class='bar' src='pixelyellow.jpg' width='$energywidth%'>
										<DIV class='bartext'>Energia ($energywidth%)</DIV>
									</DIV>
								</DIV>
							";

							$targetname = "";
							$targetid = $character["control"]["$squadronid"]->target;
							if($targetid == "no")
							{
								$targetname = "Nincs célpont";
							}
							else
							{
								$targetname = targetnamesearch($targetid);
							}
							print "<DIV class='target'>Célpont: $targetname</DIV>";
							
							$squadroncannonnum = 0;
							$squadroncannonhulldamage = 0;
							$squadroncannonshielddamage = 0;
							
							$squadronpulsenum = 0;
							$squadronpulsehulldamage = 0;
							$squadronpulseshielddamage = 0;
							
							$squadronriflenum = 0;
							$squadronrifledamage = 0;
							
							if(isset($squadron["squadroncannon"]))
							{
								foreach($squadron["squadroncannon"] as $weapon)
								{
									switch($weapon->itemtype)
									{
										case "squadroncannon":
											$squadroncannonnum += 1;
											$squadroncannonhulldamage += $weapon->hulldamage;
											$squadroncannonshielddamage += $weapon->shielddamage;
										break;
										case "squadronpulse":
											$squadronpulsenum += 1;
											$squadronpulsehulldamage += $weapon->hulldamage;
											$squadronpulseshielddamage += $weapon->shielddamage;
										break;
										case "squadronrifle":
											$squadronriflenum += 1;
											$squadronrifledamage += $weapon->squadrondamage;
										break;
									}
								}
								print "<DIV class='hov'>";
								print "<DIV class='damagetitle'>Sebzés</DIV>";
									if($squadroncannonnum)
									{
										$ammoid = $character["control"]["$squadronid"]->squadroncannonammo;
										$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
										
										print "
											<DIV class='hovtitle'>Rajágyú</DIV>
											<DIV>$squadroncannonhulldamage (Burkolat) / $squadroncannonshielddamage (Pajzs)</DIV>
											<DIV>Lőszer: $ammoname</DIV>
										";
									}
									if($squadronpulsenum)
									{
										$ammoid = $character["control"]["$squadronid"]->squadronpulseammo;
										$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
										
										print "
											<DIV class='hovtitle'>Raj Pulzuságyú</DIV>
											<DIV>$squadronpulsehulldamage (Burkolat) / $squadronpulseshielddamage (Pajzs)</DIV>
											<DIV>Lőszer: $ammoname</DIV>
										";
									}
									if($squadronriflenum)
									{
										$ammoid = $character["control"]["$squadronid"]->squadronrifleammo;
										$ammoname = ($ammoid != "no") ? $character["ammo"]["$ammoid"]->name : "Nincs lőszer";
										
										print "
											<DIV class='hovtitle'>Raj Gépágyú</DIV>
											<DIV>$squadronrifledamage (Raj), Lőszer: $ammoname</DIV>
										";
									}
								print "</DIV>";
							}
							print "</DIV>";
						}
						
					}
					print "</DIV>";
				}
				print "</DIV>";
			}
		}
		print "</DIV>";
	}
	
		function targetnamesearch($targetid)
		{
			foreach($_SESSION["gamedata"]["characters"] as $alliance)
			{
				foreach($alliance as $type)
				{
					foreach($type as $object)
					{
						if($object->id == $targetid) return $object->name;
					}
				}
			}
		}
		
		function boteffects($character)
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
?>