<?php
	function energy(&$character)
	{
		if(isset($character["ship"]["generator"]))
		{
			$genenergy = genenergyset($character);
		}
		else $genenergy = 0;
		
		if(isset($character["ship"]["battery"]))
		{
			$energyresult = energyset($character["ship"]["battery"]);
			$energy = $energyresult["energy"];
			$energystatus = $energyresult["energystatus"];
			if($character["ship"]["equipment"]["bol01"]->actualactive)
			{
				foreach($character["ship"]["battery"] as $battery)
				{
					$character["control"]["ship"]->genenergyleft += $battery->maxrecharge;
				}
			}
		}
		else
		{
			$energy = 0;
			$energystatus = 0;
		}
		
		$result["energy"] = $energy;
		$result["energystatus"] = $energystatus;
		
		return $result;
	}
		
		function genenergyset(&$character)
		{
			$genenergy = 0;
			foreach($character["ship"]["generator"] as $generator)
			{
				$genenergy += $generator->energyregen;
			}
			
			if($active = $character["ship"]["equipment"]["bol01"]->actualactive)
			{
				$_SESSION["gamedata"]["log"][] = $character["charname"] . " Akkumulátor túltöltést (BOL01) használ. ($active)";
				foreach($character["ship"]["battery"] as $battery)
				{
					$genenergy += $battery->maxrecharge;
				}
			}
			
			$character["control"]["ship"]->genenergyleft = $genenergy;
		}
		
		function energyset($batterys)
		{
			$actualcapacity = 0;
			$capacity = 0;
			foreach($batterys as $index=>$battery)
			{
				$energy[$index] = $battery->actualcapacity;
				$actualcapacity += $battery->actualcapacity;
				$capacity += $battery->capacity;
			}
			asort($energy);
			
			$result["energystatus"] = $actualcapacity / $capacity * 100;
			$result["energy"] = $energy;
			return $result;
		}
	
	function target(&$character, $id, $alliance)
	{
		$name = $character["charname"];
		$shipcontrol = &$character["control"]["ship"];
		
		if($shipcontrol->target == "no" and $shipcontrol->targettry == "no" and $id != "player")
		{
			$shipcontrol->targettry = targetchoose($alliance, $id);
		}
		
		if($shipcontrol->targettry == $shipcontrol->target and $shipcontrol->targettry != "no")
		{
			$shipcontrol->targettry = "no";
		}

		if($shipcontrol->targettry != "no")
		{
			$shipcontrol->target = targetset($shipcontrol->targettry);
		}
	}
	
		function targetchoose($alliance, $id)
		{
			if($alliance == "friend") $alliancesearch = "enemy";
			elseif($alliance == "enemy") $alliancesearch = "friend";
			
			if(!isset($_SESSION["gamedata"]["characters"]["$alliancesearch"]["ships"])) return "no";
			if(!count($_SESSION["gamedata"]["characters"]["$alliancesearch"]["ships"])) return "no";
			
			foreach($_SESSION["gamedata"]["characters"]["$alliancesearch"]["ships"] as $target)
			{
				if($_SESSION["game"]["$target->id"]["control"]["ship"]->target == $id)
				{
					$locktarget[] = $target;
				}
				$targettomb[] = $target;
			}
			
			if(isset($locktarget))
			{
				return $locktarget[rand(0, count($locktarget) - 1 )]->id;
			}
			elseif(isset($targettomb))
			{
				return $targettomb[rand(0, count($targettomb) - 1 )]->id;
			}
			else
			{
				return "no";
			}
		}
		
		function targetset($targetid)
		{
			if(!isset($_SESSION["game"]["$targetid"])) return "no";
			if($_SESSION["game"]["$targetid"]["ship"]["equipment"]["edi01"]->actualactive)
			{
				$name = $_SESSION["game"]["$targetid"]["charname"];
				$_SESSION["gamedata"]["log"][] = "$name Elektronikus Zavaróimpulzus (EDI01) védelme alatt áll.";
				return "no";
			}
			
			if(rand(0, 2000) > 800)
			{
				return $targetid;
			}
			else
			{
				return "no";
			}
		}
		
	function attack(&$character, &$energy, $id)
	{
		$shipcontrol = &$character["control"]["ship"];
		
		if($shipcontrol->target == "no" or !attackset($character, $energy, $id)) return;
		$character["control"]["ship"]->lastattack = 0;
		if($id != "player")
		{
			damageset($shipcontrol, $energy["energystatus"]);
		}

		if(isset($character["ammo"]))
		{
			weaponattack($character, $id, $energy);
		}
	}
		
		function attackset($character, $energy, $id)
		{
			if($id == "player") return 1;
			
			if(isset($character["ship"]["generator"]) and isset($character["ship"]["battery"]) and $energy["energystatus"] < 70 and $character["control"]["ship"]->dmgreceived > 1 and !$character["control"]["ship"]->lastattack and $energy["energy"])
			{
				return 0;
			}
			else return 1 ;
		}
		
		function damageset(&$shipcontrol, $energystatus)
		{
			$shipcontrol->cannondamage = percentset($energystatus);
			$shipcontrol->pulsedamage = percentset($energystatus);
			$shipcontrol->rocketlauncherdamage = percentset($energystatus);
			$shipcontrol->sablauncherdamage = percentset($energystatus);
		}
			
			function percentset($energystatus)
			{
				if($energystatus >= 50) $percent =  100;
				elseif($energystatus < 50 and $energystatus >= 25) $percent =  75;
				elseif($energystatus < 25 and $energystatus >= 10) $percent =  50;
				else $percent =  25;
				return $percent;
			}
			
		function weaponattack(&$character, $id, &$energy)
		{
			$shipcontrol = &$character["control"]["ship"];
			if(isset($character["ship"]["cannon"]))
			{
				foreach($character["ship"]["cannon"] as $weapon)
				{
					shot($character, $weapon, $id, $energy);
				}
			}
			
			if(isset($character["ship"]["rocketlauncher"]))
			{
				foreach($character["ship"]["rocketlauncher"] as $weapon)
				{
					shot($character, $weapon, $id, $energy);
				}
			}
		}
			
			function shot(&$character, $weapon, $id, &$energy)
			{
				$shipcontrol = &$character["control"]["ship"];
				if(!isset($_SESSION["game"]["$shipcontrol->target"])) return;
				switch($weapon->itemtype)
				{
					case "cannon":
						$type = "cannon";
						$ammotype = "cannonball";
						$ammocontrol = "cannonammo";
						$damagecontrol = "cannondamage";
					break;
					case "pulse":
						$type = "cannon";
						$ammotype = "ioncell";
						$ammocontrol = "pulseammo";
						$damagecontrol = "pulsedamage";
					break;
					case "rocketlauncher":
						$type = "rocketlauncher";
						$ammotype = "rocket";
						$ammocontrol = "rocketlauncherammo";
						$damagecontrol = "rocketlauncherdamage";
					break;
					case "sablauncher":
						$type = "rocketlauncher";
						$ammotype = "sabrocket";
						$ammocontrol = "sablauncherammo";
						$damagecontrol = "sablauncherdamage";
					break;
				}
				if($id != "player")
				{
					$shipcontrol->$ammocontrol = ammoset($character["ammo"], $energy["energystatus"], $weapon, $ammotype);
				}
				if($shipcontrol->$ammocontrol != "no")
				{
					$ammoid = $shipcontrol->$ammocontrol;
					if($character["ammo"]["$ammoid"]->amount < $weapon->ammousage) $shipcontrol->$ammocontrol = ammoset($character["ammo"], $energy["energystatus"], $weapon, $ammotype);
				}
				
				$ammoid = $shipcontrol->$ammocontrol;
				
				$shot = rand(1, $weapon->reload) - 1;
				if($ammoid != "no" and !$shot)
				{
					$ammodata = &$character["ammo"]["$ammoid"];
					
					$energyusage = $weapon->energyusage * $ammodata->energymultiplicator * $shipcontrol->$damagecontrol / 100;
					$batteryindex = batteryindexchoose($energy["energy"], $energyusage);
					if($batteryindex > -1 or $character["control"]["ship"]->genenergyleft >= $energyusage)
					{
						if($character["control"]["ship"]->genenergyleft >= $energyusage)
						{
							$character["control"]["ship"]->genenergyleft -= $energyusage;
						}
						elseif($batteryindex > -1)
						{
							$character["ship"]["battery"][$batteryindex]->actualcapacity -= $energyusage;
							$energyresult = energyset($character["ship"]["battery"]);
							$energy["energy"] = $energyresult["energy"];
							$energy["energystatus"] = $energyresult["energystatus"];
						}
						
						$ammodata->amount -= $weapon->ammousage;
						
						$accuracynegative = 0;
						switch($type)
						{
							case "cannon":
								if($character["ship"]["equipment"]["pdu01"]->actualactive)
								{
									$accuracynegative = $character["ship"]["equipment"]["pdu01"]->effect;
								}
							break;
							case "rocketlauncher":
								if($character["ship"]["equipment"]["mdl01"]->actualactive)
								{
									$accuracynegative = $character["ship"]["equipment"]["mdl01"]->effect;
								}
							break;
						}
						
						if(hitset($weapon->accuracy - $accuracynegative))
						{
							damage($shipcontrol->target, $weapon->hulldamage * $ammodata->dmgmultiplicator * rand(800, 1200) / 1000 * $shipcontrol->$damagecontrol / 100, $weapon->shielddamage * $ammodata->dmgmultiplicator * rand(800, 1200) / 1000 * $shipcontrol->$damagecontrol / 100, $id, "ship");
						}
						
						$character["control"]["ship"]->lastattack = 1;
					}
				}
			}
			
				function ammoset($ammos, $energystatus, $weapon, $ammotype)
				{
					switch($ammotype)
					{
						case "cannonball":
							if($energystatus > 75)
							{
								if(isset($ammos["cab03"]) and $ammos["cab03"]->amount >= $weapon->ammousage) return "cab03";
								elseif(isset($ammos["cab02"]) and $ammos["cab02"]->amount >= $weapon->ammousage) return "cab02";
								elseif(isset($ammos["cab01"]) and $ammos["cab01"]->amount >= $weapon->ammousage) return "cab01";
								else return "no";
							}
							elseif($energystatus > 50)
							{
								if(isset($ammos["cab02"]) and $ammos["cab02"]->amount >= $weapon->ammousage) return "cab02";
								elseif(isset($ammos["cab01"]) and $ammos["cab01"]->amount >= $weapon->ammousage) return "cab01";
								elseif(isset($ammos["cab03"]) and $ammos["cab03"]->amount >= $weapon->ammousage) return "cab03";
								else return "no";
							}
							else
							{
								if(isset($ammos["cab01"]) and $ammos["cab01"]->amount >= $weapon->ammousage) return "cab01";
								elseif(isset($ammos["cab02"]) and $ammos["cab02"]->amount >= $weapon->ammousage) return "cab02";
								elseif(isset($ammos["cab03"]) and $ammos["cab03"]->amount >= $weapon->ammousage) return "cab03";
								else return "no";
							}
						break;
						case "ioncell";
							if($energystatus > 75)
							{
								if(isset($ammos["ioc03"]) and $ammos["ioc03"]->amount >= $weapon->ammousage) return "ioc03";
								elseif(isset($ammos["ioc02"]) and $ammos["ioc02"]->amount >= $weapon->ammousage) return "ioc02";
								elseif(isset($ammos["ioc01"]) and $ammos["ioc01"]->amount >= $weapon->ammousage) return "ioc01";
								else return "no";
							}
							elseif($energystatus > 50)
							{
								if(isset($ammos["ioc02"]) and $ammos["ioc02"]->amount >= $weapon->ammousage) return "ioc02";
								elseif(isset($ammos["ioc01"]) and $ammos["ioc01"]->amount >= $weapon->ammousage) return "ioc01";
								elseif(isset($ammos["ioc03"]) and $ammos["ioc03"]->amount >= $weapon->ammousage) return "ioc03";
								else return "no";
							}
							else
							{
								if(isset($ammos["ioc01"]) and $ammos["ioc01"]->amount >= $weapon->ammousage) return "ioc01";
								elseif(isset($ammos["ioc02"]) and $ammos["ioc02"]->amount >= $weapon->ammousage) return "ioc02";
								elseif(isset($ammos["ioc03"]) and $ammos["ioc03"]->amount >= $weapon->ammousage) return "ioc03";
								else return "no";
							}
						break;
						case "rocket";
							if($energystatus > 75)
							{
								if(isset($ammos["roc03"]) and $ammos["roc03"]->amount >= $weapon->ammousage) return "roc03";
								elseif(isset($ammos["roc02"]) and $ammos["roc02"]->amount >= $weapon->ammousage) return "roc02";
								elseif(isset($ammos["roc01"]) and $ammos["roc01"]->amount >= $weapon->ammousage) return "roc01";
								else return "no";
							}
							elseif($energystatus > 50)
							{
								if(isset($ammos["roc02"]) and $ammos["roc02"]->amount >= $weapon->ammousage) return "roc02";
								elseif(isset($ammos["roc01"]) and $ammos["roc01"]->amount >= $weapon->ammousage) return "roc01";
								elseif(isset($ammos["roc03"]) and $ammos["roc03"]->amount >= $weapon->ammousage) return "roc03";
								else return "no";
							}
							else
							{
								if(isset($ammos["roc01"]) and $ammos["roc01"]->amount >= $weapon->ammousage) return "roc01";
								elseif(isset($ammos["roc02"]) and $ammos["roc02"]->amount >= $weapon->ammousage) return "roc02";
								elseif(isset($ammos["roc03"]) and $ammos["roc03"]->amount >= $weapon->ammousage) return "roc03";
								else return "no";
							}
						break;
						case "sabrocket";
							if($energystatus > 75)
							{
								if(isset($ammos["sro03"]) and $ammos["sro03"]->amount >= $weapon->ammousage) return "sro03";
								elseif(isset($ammos["sro02"]) and $ammos["sro02"]->amount >= $weapon->ammousage) return "sro02";
								elseif(isset($ammos["sro01"]) and $ammos["sro01"]->amount >= $weapon->ammousage) return "sro01";
								else return "no";
							}
							elseif($energystatus > 50)
							{
								if(isset($ammos["sro02"]) and $ammos["sro02"]->amount >= $weapon->ammousage) return "sro02";
								elseif(isset($ammos["sro01"]) and $ammos["sro01"]->amount >= $weapon->ammousage) return "sro01";
								elseif(isset($ammos["sro03"]) and $ammos["sro03"]->amount >= $weapon->ammousage) return "sro03";
								else return "no";
							}
							else
							{
								if(isset($ammos["sro01"]) and $ammos["sro01"]->amount >= $weapon->ammousage) return "sro01";
								elseif(isset($ammos["sro02"]) and $ammos["sro02"]->amount >= $weapon->ammousage) return "sro02";
								elseif(isset($ammos["sro03"]) and $ammos["sro03"]->amount >= $weapon->ammousage) return "sro03";
								else return "no";
							}
						break;
						case "bullet":
							if($energystatus > 75)
							{
								if(isset($ammos["bul03"]) and $ammos["bul03"]->amount >= $weapon->ammousage) return "bul03";
								elseif(isset($ammos["bul02"]) and $ammos["bul02"]->amount >= $weapon->ammousage) return "bul02";
								elseif(isset($ammos["bul01"]) and $ammos["bul01"]->amount >= $weapon->ammousage) return "bul01";
								else return "no";
							}
							elseif($energystatus > 50)
							{
								if(isset($ammos["bul02"]) and $ammos["bul02"]->amount >= $weapon->ammousage) return "bul02";
								elseif(isset($ammos["bul01"]) and $ammos["bul01"]->amount >= $weapon->ammousage) return "bul01";
								elseif(isset($ammos["bul03"]) and $ammos["bul03"]->amount >= $weapon->ammousage) return "bul03";
								else return "no";
							}
							else
							{
								if(isset($ammos["bul01"]) and $ammos["bul01"]->amount >= $weapon->ammousage) return "bul01";
								elseif(isset($ammos["bul02"]) and $ammos["bul02"]->amount >= $weapon->ammousage) return "bul02";
								elseif(isset($ammos["bul03"]) and $ammos["bul03"]->amount >= $weapon->ammousage) return "bul03";
								else return "no";
							}
						break;
					}
				}
				
				function batteryindexchoose($energytomb, $energyusage)
				{
					if(gettype($energytomb) != "array") return -1;
					foreach($energytomb as $index=>$capacity)
					{
						if($capacity >= $energyusage)
						{
							return $index;
						}
					}
					
					return -1;
				}
				
				function hitset($accuracy)
				{
					if(rand(0, 1000) < $accuracy) return 1;
					else return 0;
				}
				
				function damage($targetid, $hulldamage, $shielddamage, $attacker, $style)
				{
					if(!isset($_SESSION["game"]["$targetid"])) return;
					$_SESSION["gamedata"]["attackers"]["$targetid"]["$attacker"] = 5;
					
					$targetcharacter = $_SESSION["game"]["$targetid"];
					
					if($targetcharacter["skill"]["pdma2"]->actualactive and $targetcharacter["skill"]["pdma2"]-> defender != "no")
					{
						if(isset($_SESSION["gamedata"]["damagestatus"])) $_SESSION["gamedata"]["damagestatus"] += 1;
						else $_SESSION["gamedata"]["damagestatus"] = 1;
						
						if($_SESSION["gamedata"]["damagestatus"] < 5)
						{
							damage($targetcharacter["skill"]["pdma2"]->defender, $hulldamage, $shielddamage, $attacker, $style);
							
							unset($_SESSION["gamedata"]["damagestatus"]);
							return;
						}
					}
					
					if($targetcharacter["ship"]["equipment"]["efi01"]->actualactive)
					{
						return;
					}
					
					if($targetcharacter["ship"]["equipment"]["mac01"]->actualactive)
					{
						if(rand(0, 1000) < $targetcharacter["ship"]["equipment"]["mac01"]->effect)
						{
							return;
						}
					}
					
					if($targetcharacter["skill"]["idfa1"]->actualactive and $style == "ship")
					{
						$hulldamage *= (100 - $targetcharacter["skill"]["idfa1"]->effect) / 100;
						$shielddamage *= (100 - $targetcharacter["skill"]["idfa1"]->effect) / 100;
					}
					
					$ally = $_SESSION["gamedata"]["userdata"]["$targetid"]->ally;
					switch($ally)
					{
						case "friend":
							$alliance = "enemy";
						break;
						case "enemy":
							$alliance = "friend";
						break;
					}
				
					if($_SESSION["gamedata"]["allianceability"]["$alliance"]["mfaa1"]->actualactive and $style == "squadron")
					{
						$hulldamage *= (100 - $_SESSION["gamedata"]["allianceability"]["$alliance"]["mfaa1"]->effect) / 100;
						$shielddamage *= (100 - $_SESSION["gamedata"]["allianceability"]["$alliance"]["mfaa1"]->effect) / 100;
					}
					
					$targetcharacter["control"]["ship"]->dmgreceived = 0;
					
					if(isset($targetcharacter["ship"]["shield"]))
					{
						$shieldindex = rand(0, count($targetcharacter["ship"]["shield"]) - 1);
						$shieldenergy = $targetcharacter["ship"]["shield"][$shieldindex]->actualshield;
						if($shieldenergy)
						{
							if($shieldenergy >= $shielddamage)
							{
								$targetcharacter["ship"]["shield"][$shieldindex]->actualshield -= $shielddamage;
								settype($targetcharacter["ship"]["shield"][$shieldindex]->actualshield, "integer");
								return;
							}
							else
							{
								$targetcharacter["ship"]["shield"][$shieldindex]->actualshield =0;
								$hulldamage *= 1 - $shieldenergy / $shielddamage;
							}
						}
					}
					if($hulldamage and isset($targetcharacter["ship"]["hull"]))
					{
						$hullindex = rand(0, count($targetcharacter["ship"]["hull"]) - 1);
						$hullenergy = $targetcharacter["ship"]["hull"][$hullindex]->actualhull;
						if($hullenergy >= $hulldamage)
						{
							$targetcharacter["ship"]["hull"][$hullindex]->actualhull -= $hulldamage;
							settype($targetcharacter["ship"]["hull"][$hullindex]->actualhull, "integer");
							return;
						}
						else
						{
							$targetcharacter["ship"]["hull"][$hullindex]->actualhull = 0;
							$hulldamage -= $hullenergy;
						}
					}
					if($hulldamage)
					{
						$targetcharacter["ship"]["ship"][0]->actualcorehull -= $hulldamage;
						settype($targetcharacter["ship"]["ship"][0]->actualcorehull, "integer");
						if($targetcharacter["ship"]["ship"][0]->actualcorehull <= 0)
						{
							death($targetid, $attacker);
						}
					}
				}
				
					function death($targetid, $attacker)
					{
						$_SESSION["gamedata"]["log"][] = $_SESSION["game"]["$targetid"]["charname"] . " meghalt.";
						
						$_SESSION["gamedata"]["userdata"]["$attacker"]->score += 180;
						foreach($_SESSION["gamedata"]["attackers"]["$targetid"] as $attacker=>$value)
						{
							if($value) $_SESSION["gamedata"]["userdata"]["$attacker"]->score += 20;
						}
						
						foreach($_SESSION["gamedata"]["characters"] as $alliancename=>$alliance)
						{
							foreach($alliance as $typename=>$type)
							{
								foreach($type as $index=>$member)
								{
									if($member->id == $targetid)
									{
										unset($_SESSION["gamedata"]["characters"]["$alliancename"]["$typename"][$index]);
										foreach($_SESSION["gamedata"]["characters"]["$alliancename"]["squadrons"] as $index=>$member)
										{
											if($member->owner == $targetid)
											{
												unset($_SESSION["gamedata"]["characters"]["$alliancename"]["squadrons"][$index]);
											}
										}
									}
								}
							}
						}
						targetidunset($targetid);
						unset($_SESSION["game"]["$targetid"]);
						
						if($targetid == "player")
						{
							if(1)
							{
								$_SESSION["gamedata"]["playerdead"] = 1;
							}
						}
					}
					
						function targetidunset($targetid)
						{
							foreach($_SESSION["game"] as &$character)
							{
								foreach($character["control"] as &$control)
								{
									if(property_exists($control, "target"))
									{
										if($control->target == $targetid) $control->target = "no";
									}
									if(property_exists($control, "targettry"))
									{
										if($control->targettry == $targetid) $control->targettry = "no";
									}
								}
								
								if($character["skill"]["pdma2"]->defender == $targetid)
								{
									$character["skill"]["pdma2"]->defender = "no";
									$character["skill"]["pdma2"]->actualactive = 0;
								}
							}
							
							foreach($_SESSION["gamedata"]["allianceability"] as $alliance)
							{
								foreach($alliance as $ability)
								{
									if($ability->owner == $targetid) $ability->actualactive = 0;
								}
							}
						}
						
	function shieldrecharge(&$character, &$energy)
	{
		if(!isset($character["ship"]["shield"])) return;
		if(!$energy["energy"] and !$character["control"]["ship"]->genenergyleft) return;
		
		$shipcontrol = &$character["control"]["ship"];
		
		foreach($character["ship"]["shield"] as $index=>$shield)
		{
			$status = $shield->actualshield / $shield->shieldenergy;
			if($status != 1) $shieldstatus[$index] = $status;
		}
		if(isset($shieldstatus))
		{
			
			$srebonus = 1;
			if($sreactive = $character["ship"]["equipment"]["sre01"]->actualactive)
			{
				$srebonus = 3;
				$_SESSION["gamedata"]["log"][] = $character["charname"] . " Pajzsregeneráció Növelés (SRE01) hatása alatt áll. ($sreactive)";
			}
			
			$pdma1bonus = 1;
			if($pdma1active = $character["skill"]["pdma1"]->actualactive)
			{
				$pdma1bonus = $character["skill"]["pdma1"]->actualeffect;
				$_SESSION["gamedata"]["log"][] = $character["charname"] . " Pajzsregeneráció (PDMA1) hatása alatt áll. ($pdma1active)";
			}
			
			asort($shieldstatus);
			foreach($shieldstatus as $index=>$status)
			{
				$shielddata = &$character["ship"]["shield"][$index];
				
				$energyusage = $shielddata->energyusage * $shipcontrol->shieldregen / 100;
				$batteryindex = batteryindexchoose($energy["energy"], $energyusage);
					
				if($batteryindex > - 1 or $character["control"]["ship"]->genenergyleft >= $energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $energyusage;
						$energyresult = energyset($character["ship"]["battery"]);
						$energy["energystatus"] = $energyresult["energystatus"];
						$energy["energy"] = $energyresult["energy"];
					}
				}
				
				$dmgreceivedbonus = 1;
				if($shipcontrol->dmgreceived > 2) $dmgreceivedbonus = 5;
				
				$shielddata->actualshield += $shielddata->recharge * $shipcontrol->shieldregen / 100 * $dmgreceivedbonus * $srebonus * $pdma1bonus;
				if($shielddata->actualshield > $shielddata->shieldenergy) $shielddata->actualshield = $shielddata->shieldenergy;
			}
		}
	}
	
	function batteryrecharge(&$character, &$energy)
	{
		foreach($character["ship"]["battery"] as $index=>$battery)
		{
			$status = $battery->actualcapacity / $battery->capacity;
			if($status < 1)
			{
				$batteryset[$index] = $status;
			}
		}
		
		if(isset($batteryset))
		{
			arsort($batteryset);
			
			foreach($batteryset as $index=>$status)
			{
				$batterydata = &$character["ship"]["battery"]["$index"];
				
				$maxrecharge = $batterydata->maxrecharge;
				if($batterydata->capacity - $batterydata->actualcapacity < $maxrecharge) $maxrecharge = $batterydata->capacity - $batterydata->actualcapacity;
				
				if($maxrecharge <= $character["control"]["ship"]->genenergyleft)
				{
					$batterydata->actualcapacity += $maxrecharge;
					$character["control"]["ship"]->genenergyleft -= $maxrecharge;
				}
				else
				{
					$batterydata->actualcapacity += $character["control"]["ship"]->genenergyleft;
					$character["control"]["ship"]->genenergyleft = 0;
				}
			}
		}
		
		$energyresult = energyset($character["ship"]["battery"]);
		$energy["energystatus"] = $energyresult["energystatus"];
		$energy["energy"] = $energyresult["energy"];
	}
	
	function squadronshieldrecharge(&$character, &$squadron, &$squadroncontrol, &$energy, $owner)
	{
		foreach($squadron["squadronshield"] as $index=>$shield)
		{
			$status = $shield->actualshield / $shield->shieldenergy;
			if($status != 1) $shieldstatus[$index] = $status;
		}
		
		if(isset($shieldstatus)) asort($shieldstatus);
		else return;
		
		switch($squadroncontrol->place)
		{
			case "space":
				if($owner != "player") $squadroncontrol->squadronshieldregen = percentset($energy["energystatus"]);
				
				foreach($shieldstatus as $index=>$status)
				{
					$shielddata = &$squadron["squadronshield"][$index];
					
					$energyusage = $shielddata->energyusage * $squadroncontrol->squadronshieldregen / 100;
					$batteryindex = batteryindexchoose($energy["energy"], $energyusage);
					
					if($batteryindex > -1)
					{
						$squadron["battery"][$batteryindex]->actualcapacity -= $energyusage;
						$energy = energyset($squadron["battery"]);
						
						$dmgreceivedbonus = 1;
						if($squadroncontrol->dmgreceived > 2) $dmgreceivedbonus = 5;
						
						$shielddata->actualshield += $shielddata->recharge * $squadroncontrol->squadronshieldregen / 100 * $dmgreceivedbonus;
						if($shielddata->actualshield > $shielddata->shieldenergy) $shielddata->actualshield = $shielddata->shieldenergy;
					}
				}
			break;
			case "hangar":
				if(isset($character["ship"]["battery"]))
				{
					$shipenergy = energyset($character["ship"]["battery"]);
				}
				else
				{
					$shipenergy["energy"] = 0;
					$shipenergy["energystatus"] = 0;
				}
				
				if($owner != "player") $squadroncontrol->squadronshieldregen = percentset($shipenergy["energystatus"]);
				
				foreach($shieldstatus as $index=>$status)
				{
					$shielddata = &$squadron["squadronshield"][$index];
					$energyusage = $shielddata->energyusage * $squadroncontrol->squadronshieldregen / 100;
					
					$batteryindex = batteryindexchoose($energy["energy"], $energyusage);
					$shipbatteryindex = batteryindexchoose($shipenergy["energy"], $energyusage);
					$genenergy = &$character["control"]["ship"]->genenergyleft;
					
					if($genenergy >= $energyusage or $shipbatteryindex > -1 or $batteryindex > -1)
					{
						if($genenergy >= $energyusage)
						{
							$genenergy -= $energyusage;
						}
						elseif($shipbatteryindex > -1)
						{
							$character["ship"]["battery"][$shipbatteryindex]->actualcapacity -=  $energyusage;
						}
						elseif($batteryindex > -1)
						{
							$squadron["battery"][$batteryindex]->actualcapacity -= $energyusage;
						}
						
						$dmgreceivedbonus = 1;
						if($squadroncontrol->dmgreceived > 2) $dmgreceivedbonus = 5;
						
						$shielddata->actualshield += $shielddata->recharge * $squadroncontrol->squadronshieldregen / 100 * $dmgreceivedbonus;
						if($shielddata->actualshield > $shielddata->shieldenergy) $shielddata->actualshield = $shielddata->shieldenergy;
					}
				}
			break;
		}
	}
	
	function takeoff(&$character, &$squadron, &$squadroncontrol, &$energy)
	{
		if($squadroncontrol->takeoff == "auto")
		{
			$ammotakeoff = 0;
			$energytakeoff = 0;
			$hulltakeoff = 0;
			$shieldtakeoff = 0;
			$corehulltakeoff = 0;
			
			$ammopercent = $squadron["squadron"]->actualammostorage / $squadron["squadron"]->basicammostorage * 100;
			if($ammopercent > 90) $ammotakeoff = 1;
			
			if(isset($character["ship"]["battery"]))
			{
				$shipenergy = energyset($character["ship"]["battery"]);
			}
			else
			{
				$shipenergy["energy"] = 0;
				$shipenergy["energystatus"] = 0;
			}
			if($shipenergy["energystatus"] > 50) $energystatusset = 90;
			else $energystatusset = 50;
			if($energy["energystatus"] > $energystatusset or !$energy["energystatus"]) $energytakeoff = 1;
			
			$hullpercent = 0;
			$maxhullpercent = 0;
			if(isset($squadron["squadronhull"]))
			{
				foreach($squadron["squadronhull"] as $hull)
				{
					$hullpercent += $hull->actualhull / $hull->hullenergy * 100;
					$maxhullpercent += 100;
				}
				
				if($hullpercent / $maxhullpercent * 100 > 90) $hulltakeoff = 1;
			}
			else $hulltakeoff = 1;
			
			$shieldpercent = 0;
			$maxshieldpercent = 0;
			if(isset($squadron["squadronshield"]))
			{
				foreach($squadron["squadronshield"] as $shield)
				{
					$shieldpercent += $shield->actualshield / $shield->shieldenergy * 100;
					$maxshieldpercent += 100;
				}
				if($shieldpercent / $maxshieldpercent * 100 > 90) $shieldtakeoff = 1;
			}
			else $shieldtakeoff = 1;
			
			if($squadron["squadron"]->actualcorehull / $squadron["squadron"]->corehull * 100 > 90) $corehulltakeoff = 1;
			
			if($ammotakeoff and $energytakeoff and $shieldtakeoff and $hulltakeoff and $corehulltakeoff)
			{
				$character["ship"]["hangar"][$squadroncontrol->hangarindex]->actualsquadronplace -= 1;
				$squadroncontrol->place = "space";
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}
	
	function repair(&$character, &$squadron, &$squadroncontrol)
	{
		$hangardata = $character["ship"]["hangar"][$squadroncontrol->hangarindex];
		
		$squadron["squadron"]->actualcorehull += $squadron["squadron"]->corehull * $hangardata->repair;
		if($squadron["squadron"]->actualcorehull > $squadron["squadron"]->corehull) $squadron["squadron"]->actualcorehull = $squadron["squadron"]->corehull;
			
		$squadron["squadron"]->actualammostorage += $squadron["squadron"]->basicammostorage * $hangardata->repair;
		if($squadron["squadron"]->actualammostorage > $squadron["squadron"]->basicammostorage) $squadron["squadron"]->actualammostorage = $squadron["squadron"]->basicammostorage;

		if(isset($squadron["squadronhull"]))
		{
			foreach($squadron["squadronhull"] as &$squadronhull)
			{
				$squadronhull->actualhull += $squadronhull->hullenergy * $hangardata->repair;
				if($squadronhull->actualhull > $squadronhull->hullenergy) $squadronhull->actualhull = $squadronhull->hullenergy;
			}
		}

		if(isset($squadron["battery"]))
		{
			if(isset($character["ship"]["battery"]))
			{
				$shipenergy = energyset($character["ship"]["battery"]);
				arsort($shipenergy["energy"]);
			}
			else
			{
				$shipenergy["energy"] = 0;
				$shipenergy["energystatus"] = 0;
			}
			$genenergy = &$character["control"]["ship"]->genenergyleft;
			
			if($shipenergy["energy"] or $genenergy)
			{
				foreach($squadron["battery"] as $index=>$battery)
				{
					$status = $battery->actualcapacity / $battery->capacity * 100;
					if($status < 100)
					{
						$batteryset[$index] = $status;
					}
				}
				
				if(isset($batteryset))
				{
					arsort($batteryset);
					foreach($batteryset as $index=>$status)
					{
						$batterydata = &$squadron["battery"][$index];
						
						
						$maxrecharge = $batterydata->capacity * $hangardata->repair;
						if($batterydata->actualcapacity + $maxrecharge > $batterydata->capacity) $maxrecharge = $batterydata->capacity - $batterydata->actualcapacity;
						
						if($maxrecharge / 2 <= $genenergy)
						{
							$batterydata->actualcapacity += $maxrecharge;
							$genenergy -= $maxrecharge;
							$maxrecharge = 0;
						}
						else
						{
							$batterydata->actualcapacity += $genenergy;
							$maxrecharge -= $genenergy;
							$genenergy = 0;
						}
						if($maxrecharge and $shipenergy["energy"])
						{
							foreach($shipenergy["energy"] as $shipbatteryindex=>$status)
							{
								$shipbatterydata = &$character["ship"]["battery"][$shipbatteryindex];
								
								if($shipbatterydata->actualcapacity <= $maxrecharge / 2 and $shipbatterydata->actualcapacity)
								{
									$batterydata->actualcapacity += $shipbatterydata->actualcapacity;
									$maxrecharge -= $shipbatterydata->actualcapacity;
									$shipbatterydata->actualcapacity = 0;
								}
								elseif($shipbatterydata->actualcapacity > $maxrecharge / 2)
								{
									$shipbatterydata->actualcapacity -= $maxrecharge / 2;
									$batterydata->actualcapacity += $maxrecharge;
									$maxrecharge = 0;
								}
								
								$shipenergy = energyset($character["ship"]["battery"]);
								if(!$maxrecharge) break;
							}
						}
					}
				}
			}	
		}
	}
	
	function recallset(&$squadron, &$squadroncontrol, &$energy, $owner, $alliance)
	{
		if(recallvalueset($squadron, $squadroncontrol, $energy, $owner, $alliance) or $squadroncontrol->callbackcount)
		{
			$squadroncontrol->callbackcount += 1;
			if($squadroncontrol->dmgreceived > 2) $squadroncontrol->callbackcount += 2;
			
			if($squadroncontrol->callbackcount > 2)
			{
				$squadroncontrol->callback = 1;
				$squadroncontrol->callbackcount = 0;
			}
		}
	}
	
		function recallvalueset(&$squadron, &$squadroncontrol, &$energy, $owner, $alliance)
		{
			if($squadroncontrol->takeoff == "auto")
			{
				$ammopercent = $squadron["squadron"]->actualammostorage / $squadron["squadron"]->basicammostorage * 100;
				if($ammopercent < 10) return 1;
				
				if(isset($squadron["battery"]) and $energy["energystatus"] < 10) return 1;
				
				
				switch($squadroncontrol->returnstyle)
				{
					case "shield":
						$percent = 0;
						$maxpercent = 0;
						if(isset($squadron["squadronshield"]))
						{
							foreach($squadron["squadronshield"] as $shield)
							{
								$percent += $shield->actualshield / $shield->shieldenergy * 100;
								$maxpercent += 100;
							}
						}
						else
						{
							$percent = 1;
							$maxpercent = 1;
						}
						if($percent / $maxpercent * 100 < $squadroncontrol->returnvalue) return 1;
					case "hull":
						$percent = 0;
						$maxpercent = 0;
						if(isset($squadron["squadronhull"]))
						{
							foreach($squadron["squadronhull"] as $hull)
							{
								$percent += $hull->actualhull / $hull->hullenergy * 100;
								$maxpercent += 100;
							}
						}
						else
						{
							$percent = 1;
							$maxpercent = 1;
						}
						if($percent / $maxpercent * 100 < $squadroncontrol->returnvalue) return 1;
						
						if($squadron["squadron"]->actualcorehull / $squadron["squadron"]->corehull * 100 < $squadroncontrol->returnvalue) return 1;
					break;
					
				}
				if($percent / $maxpercent * 100 < $squadroncontrol->returnvalue) return 1;
				
				$tg = squadrontargetchoose($alliance, $squadron, $squadroncontrol, $owner);
				if($tg == "no")
				{
					$ammopercent = $squadron["squadron"]->actualammostorage / $squadron["squadron"]->basicammostorage * 100;
					if($ammopercent < 90) return 1;
					
					if(isset($squadron["battery"]) and $energy["energystatus"] < 90) return 1;
					
					if(isset($squadron["squadronhull"]))
					{
						$percent = 0;
						$maxpercent = 0;
						foreach($squadron["squadronhull"] as $hull)
						{
							$percent += $hull->actualhull / $hull->hullenergy * 100;
							$maxpercent += 100;
						}
					}
					else
					{
						$percent = 1;
						$maxpercent = 1;
					}
					if($percent / $maxpercent * 100 < 90) return 1;
					
					if(isset($squadron["squadronshield"]))
					{
						$percent = 0;
						$maxpercent = 0;
						foreach($squadron["squadronshield"] as $shield)
						{
							$percent += $shield->actualshield / $shield->shieldenergy * 100;
							$maxpercent += 100;
						}
					}
					else
					{
						$percent = 1;
						$maxpercent = 1;
					}
					if($percent / $maxpercent * 100 < 90) return 1;
					
					if($energy["energystatus"] < 90 and $energy["energystatus"]) return 1;
					
					if($squadron["squadron"]->actualammostorage / $squadron["squadron"]->basicammostorage * 100 < 90) return 1;
				}
			}
			return 0;
		}
		
	function recall(&$character, &$squadroncontrol, $id)
	{
		if($squadroncontrol->callback)
		{
			$squadroncontrol->callback = 0;
			
			foreach($character["ship"]["hangar"] as $index=>$hangar)
			{
				if($hangar->actualsquadronplace < $hangar->squadronplace)
				{
					$hangars[$index] = $hangar->repair;
				}
			}
			arsort($hangars);
			
			foreach($hangars as $index=>$repair)
			{
				$character["ship"]["hangar"][$index]->actualsquadronplace += 1;
				$squadroncontrol->place = "hangar";
				$squadroncontrol->hangarindex = $index;
				$squadroncontrol->target = "no";
				$squadroncontrol->targettry = "no";
				break;
			}
			
			targetidunset($id);
			unset($_SESSION["gamedata"]["attackers"]["$id"]);
			foreach($_SESSION["gamedata"]["attackers"] as $target=>$attackers)
			{
				foreach($attackers as $index=>$a)
				{
					if($index == $id) unset($_SESSION["gamedata"]["attackers"]["$target"]["$index"]);
				}
			}
			
			return 1;
		}
		else return 0;
	}
	
	function squadrontarget(&$squadron, &$squadroncontrol, $owner, $alliance)
	{
		if($squadroncontrol->target == "no" and $squadroncontrol->targettry == "no" and $squadroncontrol->targetselect == "auto")
		{
			$squadroncontrol->targettry = squadrontargetchoose($alliance, $squadron, $squadroncontrol, $owner);
		}
		
		if($squadroncontrol->targettry == $squadroncontrol->target and $squadroncontrol->target != "no")
		{
			$squadroncontrol->targettry = "no";
		}
		
		if($squadroncontrol->targettry != "no" and $squadroncontrol->targettry != $squadroncontrol->target)
		{
			switch($targettype = targettypeset($squadroncontrol->targettry))
			{
				case "ships":
					$squadroncontrol->target = targetset($squadroncontrol->targettry);
				break;
				case "squadrons":
					$squadroncontrol->target = squadrontargetset($squadroncontrol->targettry, $alliance);
				break;
			}
			$squadroncontrol->targettry = "no";
		}
	}
		
		function targettypeset($targetid)
		{
			foreach($_SESSION["gamedata"]["characters"] as $alliance=>$type)
			{
				foreach($type as $typename=>$members)
				{
					foreach($members as $member)
					{
						if($member->id == $targetid) return $typename;
					}
				}
			}
		}
		
		function squadrontargetchoose($alliance, $squadron, &$squadroncontrol, $owner)
		{
			if($alliance == "friend") $alliancesearch = "enemy";
			elseif($alliance == "enemy") $alliancesearch = "friend";
			
			$cannonnum = 0;
			$riflenum = 0;
			
			if(isset($squadron["squadroncannon"]))
			{
				foreach($squadron["squadroncannon"] as $weapon)
				{
					if($weapon->itemtype == "squadronrifle") $riflenum += 1;
					else $cannonnum += 1;
				}
			}
			
			
			if(!$cannonnum and !$riflenum) return "no";
			
			
			if($riflenum > $cannonnum)
			{
				
				/*
					Gépágyús
					1 - Hajót lövi raj
					2 - Rajt lövi raj
					3 - Random raj
					4 - Random csatahajó ha van benne ágyú
				*/
				if(isset($_SESSION["gamedata"]["characters"]["$alliancesearch"]["squadrons"]))
				{
					foreach($_SESSION["game"]["$owner"]["squadrons"] as $squadronid=>$squadron)
					{
						$ownsquadrons[] = $squadronid;
					}
					
					foreach($_SESSION["gamedata"]["characters"]["$alliancesearch"]["squadrons"] as $target)
					{
						if($_SESSION["game"]["$target->owner"]["control"]["$target->id"]->place != "space") continue;
						if($_SESSION["game"]["$target->owner"]["control"]["$target->id"]->target == $owner)
						{
							$targets[] = $target->id;
						}
						elseif(in_array($_SESSION["game"]["$target->owner"]["control"]["$target->id"]->target, $ownsquadrons))
						{
							$squadrontarget[] = $target->id;
						}
						$allsquadrons[] = $target->id;
					}
					
					if(isset($targets)) return $targets[rand(0, count($targets) - 1 )];
					if(isset($squadrontargets)) return $squadrontargets[rand(0, count($squadrontargets) - 1 )];
					if(isset($allsquadrons)) return $allsquadrons[rand(0, count($allsquadrons) - 1)];
				}
			}
			
			if($cannonnum)
			{
				$shiptarget = $_SESSION["game"]["$owner"]["control"]["ship"]->target;
				if($shiptarget != "no")
				{
					return $shiptarget;
				}
				
				if(isset($_SESSION["gamedata"]["characters"]["$alliancesearch"]["ships"]))
				{
					foreach($_SESSION["gamedata"]["characters"]["$alliancesearch"]["ships"] as $target)
					{
						if($_SESSION["game"]["$target->id"]["control"]["ship"]->target == $owner)
						{
							$targets[] = $target->id;
						}
						$alltargets[] = $target->id;
					}
					if(isset($targets)) return $targets[rand(0, count($targets) - 1 )];
					return $alltargets[rand(0, count($alltargets) - 1)];
				}
			}
			
			return "no";
		}
		
		function squadrontargetset($targetid, $alliance)
		{
			if($alliance == "friend") $alliancesearch = "enemy";
			elseif($alliance == "enemy") $alliancesearch = "friend";
			
			if(isset($_SESSION["gamedata"]["characters"]["$alliancesearch"]["squadrons"]))
			{
				foreach($_SESSION["gamedata"]["characters"]["$alliancesearch"]["squadrons"] as $target)
				{
					if($target->id == $targetid and rand(0, 1000) > 400)
					{
						return $targetid;
					}
				}
			}
			return "no";
		}
		
	function squadronattack(&$character, &$squadron, &$squadroncontrol, &$energy, $owner, $id)
	{
		if($squadroncontrol->target == "no") return;
		
		$targettype = targettypeset($squadroncontrol->target);
		if($owner != "player")
		{
			$squadroncontrol->squadroncannondamage = percentset($energy["energystatus"]);
			$squadroncontrol->squadronpulsedamage = percentset($energy["energystatus"]);
			$squadroncontrol->squadronrifledamage = percentset($energy["energystatus"]);
		}
		
		if(isset($squadron["squadroncannon"]))
		{
			foreach($squadron["squadroncannon"] as $weapon)
			{
				switch($weapon->itemtype)
				{
					case "squadronrifle":
						$type = "rifle";
						$ammocontrol = "squadronrifleammo";
						$ammoname = "bullet";
						$damagecontrol = "squadronrifledamage";
						$tgtype = "squadrons";
					break;
					case "squadroncannon":
						$type = "cannon";
						$ammocontrol = "squadroncannonammo";
						$ammoname = "cannonball";
						$damagecontrol = "squadroncannondamage";
						$tgtype = "ships";
					break;
					case "squadronpulse":
						$type = "cannon";
						$ammocontrol = "squadronpulseammo";
						$ammoname = "ioncell";
						$damagecontrol = "squadronpulsedamage";
						$tgtype = "ships";
					break;
				}
				
				if($owner != "player")
				{
					$squadroncontrol->$ammocontrol = ammoset($character["ammo"], $energy["energystatus"], $weapon, $ammoname);
				}
				if($squadroncontrol->$ammocontrol != "no")
				{
					$ammoid = $squadroncontrol->$ammocontrol;
					if($character["ammo"]["$ammoid"]->amount < $weapon->ammousage) $squadroncontrol->$ammocontrol = ammoset($character["ammo"], $energy["energystatus"], $weapon, $ammoname);
				}
				
				$strikebackcount[] = 0;
				$ammoid = $squadroncontrol->$ammocontrol;
				if($ammoid != "no" and $targettype == $tgtype)
				{
					$ammodata = &$character["ammo"]["$ammoid"];
					
					$strikebackcount[] = squadronshot($character, $squadron, $squadroncontrol, $energy, $weapon, $ammodata, $damagecontrol, $type, $owner);
				}
				
			}
			if(in_array(1, $strikebackcount)) strikeback($id, $squadroncontrol->target);
		}
	}
		
		function squadronshot(&$character, &$squadron, &$squadroncontrol, &$energy, $weapon, &$ammodata, $damagecontrol, $type, $owner)
		{
			$energyusage = $weapon->energyusage * $ammodata->energymultiplicator * $squadroncontrol->$damagecontrol / 100;
			$batteryindex = batteryindexchoose($energy["energy"], $energyusage);
			
			if($batteryindex > -1 and $ammodata->amount >= $weapon->ammousage and $squadron["squadron"]->actualammostorage >= $weapon->ammousage)
			{
				$squadron["battery"][$batteryindex]->actualcapacity -= $energyusage;
				$energy = energyset($squadron["battery"]);
				
				$ammodata->amount -= $weapon->ammousage;
				$squadron["squadron"]->actualammostorage -= $weapon->ammousage;
				
				if(hitset($weapon->accuracy))
				{
					switch($type)
					{
						case "rifle":
							squadrondamage($squadroncontrol->target, $weapon->squadrondamage * $ammodata->dmgmultiplicator * rand(800, 1200) / 1000 * $squadroncontrol->$damagecontrol / 100, $owner);
						break;
						default:
							if($_SESSION["game"]["$squadroncontrol->target"]["ship"]["equipment"]["ser01"]->actualactive)
							{
								break;
							}
							damage($squadroncontrol->target, $weapon->hulldamage * $ammodata->dmgmultiplicator * rand(800, 1200) / 1000 * $squadroncontrol->$damagecontrol / 100, $weapon->shielddamage * $ammodata->dmgmultiplicator * rand(800, 1200) / 1000 * $squadroncontrol->$damagecontrol / 100, $owner, "squadron");
						break;
					}
				}
				return 1;
			}
			return 0;
		}
		
			function squadrondamage($targetid, $damage, $attackerowner)
			{
				$_SESSION["gamedata"]["attackers"]["$targetid"]["$attackerowner"] = 5;
				foreach($_SESSION["gamedata"]["characters"] as $aname=>$alliance)
				{
					if(isset($alliance["squadrons"]))
					{
						foreach($alliance["squadrons"] as $member)
						{
							if($member->id == $targetid) $owner = $member->owner;
						}
					}
				}
				if(!isset($owner)) return;

				switch($aname)
				{
					case "friend":
						$alliance = "enemy";
					break;
					case "enemy":
						$alliance = "friend";
					break;
				}
			
				if($_SESSION["gamedata"]["allianceability"]["$alliance"]["mfaa1"]->actualactive)
				{
					$damage *= (100 - $_SESSION["gamedata"]["allianceability"]["$alliance"]["mfaa1"]->effect) / 100;
				}
					
				$squadron = $_SESSION["game"]["$owner"]["squadrons"]["$targetid"];
				$_SESSION["game"]["$owner"]["control"]["$targetid"]->dmgreceived = 0;
			
				if(isset($squadron["squadronshield"]))
				{
					$shieldindex = rand(0, count($squadron["squadronshield"]) - 1);
					$shieldenergy = $squadron["squadronshield"][$shieldindex]->actualshield;
					
					if($shieldenergy)
					{
						
						if($shieldenergy >= $damage)
						{
							$squadron["squadronshield"][$shieldindex]->actualshield -= $damage;
							settype($squadron["squadronshield"][$shieldindex]->actualshield, "integer");
							return;
						}
						else
						{
							$damage -= $squadron["squadronshield"][$shieldindex]->actualshield;
							$squadron["squadronshield"][$shieldindex]->actualshield = 0;
						}
					}
				}
				
				if(isset($squadron["squadronhull"]))
				{
					$hullindex = rand(0, count($squadron["squadronhull"]) - 1);
					$hullenergy = $squadron["squadronhull"][$hullindex]->actualhull;
					
					if($hullenergy)
					{
						if($hullenergy >= $damage)
						{
							$squadron["squadronhull"][$hullindex]->actualhull -= $damage;
							settype($squadron["squadronhull"][$hullindex]->actualhull, "integer");
							return;
						}
						else
						{
							$damage -= $squadron["squadronhull"][$hullindex]->actualhull;
							$squadron["squadronhull"][$hullindex]->actualhull = 0;
						}
					}
				}
				if($damage)
				{
					$squadron["squadron"]->actualcorehull -= $damage;
					settype($squadron["squadron"]->actualcorehull, "integer");
					if($squadron["squadron"]->actualcorehull <= 0)
					{
						squadrondeath($targetid, $owner, $attackerowner);
						$squadron["squadron"]->actualcorehull = 0;
					}
					
				}
			}
			
				function squadrondeath($targetid, $owner, $attackerowner)
				{
					$_SESSION["gamedata"]["userdata"]["$attackerowner"]->score += 9.5;
					foreach($_SESSION["gamedata"]["attackers"]["$targetid"] as $attacker=>$value)
					{
						if($value) $_SESSION["gamedata"]["userdata"]["$attackerowner"]->score += 0.5;
					}
					
					$_SESSION["game"]["$owner"]["control"]["$targetid"]->place = "dead";
					
					targetidunset($targetid);
				}
				
		function strikeback($id, $targetid)
		{
			foreach($_SESSION["gamedata"]["characters"] as $alliance)
			{
				foreach($alliance as $typename=>$members)
				{
					foreach($members as $member)
					{
						if($member->id == $targetid)
						{
							$targetdata = $member;
							$targetdata->type = $typename;
						}
						if($member->id == $id)
						{
							$attackerdata = $member;
							$attackerdata->type = $typename;
						}
					}
				}
			}
			
			if(!isset($targetdata)) return;
			switch($targetdata->type)
			{
				case "ships":
					$targetcharacter = $_SESSION["game"]["$targetdata->id"];
					if(!isset($targetcharacter["ammo"])) return;
					
					if(isset($targetcharacter["ship"]["battery"]))
					{
						$energyresult = energyset($targetcharacter["ship"]["battery"]);
						$targetenergy = $energyresult["energy"];
						$targetenergystatus = $energyresult["energystatus"];
					}
					else
					{
						$targetenergy = 0;
						$targetenergystatus = 0;
					}
					$genenergy = $targetcharacter["control"]["ship"]->genenergyleft;
					
					if($targetdata->id != "player")
					{
						$targetcharacter["control"]["ship"]->rifledamage = percentset($targetenergystatus);
					}
					
					if(isset($targetcharacter["ship"]["rifle"]))
					{
						foreach($targetcharacter["ship"]["rifle"] as $weapon)
						{
							$count = 0;
							$success = 0;
							
							do
							{
								$count++;
								
								if($targetdata->id != "player") $targetcharacter["control"]["ship"]->rifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
								
								if($targetcharacter["control"]["ship"]->rifleammo == "no") $targetcharacter["control"]["ship"]->rifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
								$ammoid = $targetcharacter["control"]["ship"]->rifleammo;
								if(!isset($targetcharacter["ammo"]["$ammoid"]) or $targetcharacter["ammo"]["$ammoid"]->amount < $weapon->ammousage) $targetcharacter["control"]["ship"]->rifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
								
								if(($ammoid = $targetcharacter["control"]["ship"]->rifleammo) != "no")
								{
									$energyusage = $weapon->energyusage * $targetcharacter["ammo"]["$ammoid"]->energymultiplicator * $targetcharacter["control"]["ship"]->rifledamage / 100;
									$batteryindex = batteryindexchoose($targetenergy, $energyusage);
									
									if($batteryindex > -1 or $genenergy >= $energyusage and $targetcharacter["ammo"]["$ammoid"]->amount >= $weapon->ammousage)
									{
										if($genenergy >= $energyusage)
										{
											$genenergy -= $energyusage;
										}
										elseif($batteryindex > -1)
										{
											$targetcharacter["ship"]["battery"][$batteryindex]->actualcapacity -= $energyusage;
											$targetenergyresult = energyset($targetcharacter["ship"]["battery"]);
											$targetenergy = $targetenergyresult["energy"];
											$targetenergystatus = $targetenergyresult["energystatus"];
										}
										
										$targetcharacter["ammo"]["$ammoid"]->amount -= $weapon->ammousage;
										
										if(squadronhitset($weapon->accuracy))
										{
											$damage = $weapon->squadrondamage * $targetcharacter["ammo"]["$ammoid"]->dmgmultiplicator * rand(800, 1200) / 1000 * $targetcharacter["control"]["ship"]->rifledamage / 100;
											squadrondamage($attackerdata->id, $damage, $targetid);
										}
										
										$success = 1;
									}
								}
								
								if($count > 5) $success = 1;
							}
							while(!$success);
						}
					}
					$targetcharacter["control"]["ship"]->genenergyleft = $genenergy;

					$_SESSION["game"]["$targetdata->id"] = $targetcharacter;
				break;
				case "squadrons":
					$targetcharacter = $_SESSION["game"]["$targetdata->owner"];
					$targetsquadron = $targetcharacter["squadrons"]["$targetdata->id"];
					$targetsquadroncontrol = $targetcharacter["control"]["$targetdata->id"];
					
					if(isset($targetsquadron["battery"]))
					{
						$energyresult = energyset($targetsquadron["battery"]);
						$targetenergy = $energyresult["energy"];
						$targetenergystatus = $energyresult["energystatus"];
					}
					else
					{
						$targetenergy = 0;
						$targetenergystatus = 0;
					}
					
					if($targetdata->owner != "player")
					{
						$targetsquadroncontrol->squadronrifledamage = percentset($targetenergystatus);
					}
					
					if(isset($targetsquadron["squadroncannon"]))
					{
						foreach($targetsquadron["squadroncannon"] as $weapon)
						{
							$count = 0;
							$success = 0;
							if($weapon->itemtype == "squadronrifle")
							{
								do
								{
									$count++;
									if($targetdata->owner != "player") $targetsquadroncontrol->squadronrifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
									
									if($targetsquadroncontrol->squadronrifleammo == "no") $targetsquadroncontrol->squadronrifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
									$ammoid = $targetsquadroncontrol->squadronrifleammo;
									if(!isset($targetcharacter["ammo"]["$ammoid"]) or $targetcharacter["ammo"]["$ammoid"]->amount < $weapon->ammousage) $targetsquadroncontrol->squadronrifleammo = ammoset($targetcharacter["ammo"], $targetenergystatus, $weapon, "bullet");
									
									if(($ammoid = $targetsquadroncontrol->squadronrifleammo) != "no")
									{
										$energyusage = $weapon->energyusage * $targetcharacter["ammo"]["$ammoid"]->energymultiplicator * $targetsquadroncontrol->squadronrifledamage / 100;
										$batteryindex = batteryindexchoose($targetenergy, $energyusage);
										if($batteryindex > -1)
										{
											if($targetcharacter["ammo"]["$ammoid"]->amount >= $weapon->ammousage and $targetsquadron["squadron"]->actualammostorage >= $weapon->ammousage)
											{
												$targetsquadron["battery"][$batteryindex]->actualcapacity -= $energyusage;
												$targetenergyresult = energyset($targetsquadron["battery"]);
												$targetenergy = $targetenergyresult["energy"];
												$targetenergystatus = $targetenergyresult["energystatus"];
												
												$targetcharacter["ammo"]["$ammoid"]->amount -= $weapon->ammousage;
												$targetsquadron["squadron"]->actualammostorage -= $weapon->ammousage;
												if(squadronhitset($weapon->accuracy))
												{
													$damage = $weapon->squadrondamage * $targetcharacter["ammo"]["$ammoid"]->dmgmultiplicator * rand(800, 1200) / 1000 * $targetsquadroncontrol->squadronrifledamage / 100;
													squadrondamage($attackerdata->id, $damage, $targetdata->owner);
												}
												$success = 1;
											}
										}
									}
									if($count > 5) $success = 1;
								}
								while(!$success);
							}
						}
					}
				break;
			}
		}
			
			function squadronhitset($accuracy)
			{
				if(rand(0, 1000) < $accuracy) return 1;
				else return 0;
			}
?>