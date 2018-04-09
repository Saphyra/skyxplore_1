<?php
	include("charactergenerate_rates.php");
	include("shipload.php");
	
	function charactergenerate($level, $alliance)
	{
		$ids[] = "";
		if(isset($_SESSION["gamedata"]["characters"]["friend"]["ships"])) foreach($_SESSION["gamedata"]["characters"]["friend"]["ships"] as $id) $ids[] = $id->id;
		if(isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"])) foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $id) $ids[] = $id->id;
		$character["charid"] = idgenerate("bot", $ids, 5);
		
		$names[] = "";
		if(isset($_SESSION["gamedata"]["characters"]["friend"]["ships"])) foreach($_SESSION["gamedata"]["characters"]["friend"]["ships"] as $name) $names[] = $name->name;
		if(isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"])) foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $name) $names[] = $name->name;
		$character["charname"] = namegenerate($names);
		
		switch(rand(1, 6))
		{
			case 1:
				$company = "emf";
			break;
			case 2:
				$company = "pdm";
			break;
			case 3:
				$company = "idf";
			break;
			case 4:
				$company = "mfa";
			break;
			case 5:
				$company = "gaa";
			break;
			case 6:
				$company = "cri";
			break;
		}
		
		$character["company"] = $company;
		
		$character["skill"] = skillset($level);
		
		$result = equipmentset($level, $company);
		$character["ship"] = $result["ship"];
		$character["level"] = $result["level"];
		if(isset($result["equipment"])) $character["equipment"] = $result["equipment"];
		if(isset($result["squadrons"])) $character["squadrons"] = $result["squadrons"];
		if(isset($result["ammo"])) $character["ammo"] = $result["ammo"];
		
		if(isset($character["squadrons"]) and isset($character["skill"])) $character = groupset($character, $character["skill"], $character["ship"]->itemtype);
		elseif(isset($character["squadrons"])) $character = groupset($character);
		
		shipload($character["charid"], $character, $alliance);
		controlset($character["charid"]);
	}
	
		function namegenerate($names)
		{
			include("charactergenerate_names.php");
			do
			{
				$veznev = $veznevs[rand(0, count($veznevs) - 1)];
				$kernev = $kernevs[rand(0, count($kernevs) - 1)];
				
				$name = "$veznev $kernev";
			}
			while(in_array($name, $names));
			
			return $name;
		}
		
		function skillset($level)
		{
			$skillsearch["slot"] = "skill";
			$skills = objectsearch($skillsearch, $_SESSION["data"]["items"]);
			
			foreach($skills as $skillid)
			{
				$skilldata = $_SESSION["data"]["items"]["$skillid"];
				
				$skillset = new emptyclass;
				switch($skilldata->maxlevel)
				{
					case 10:
						$skillevel = skillrate10($level);
					break;
					case 5:
						$skillevel = skillrate5($level);
					break;
				}
				$skillset->level = $skillevel;
				$skillset->itemid = $skillid;
				
				$result["$skillid"] = $skillset;
			}
			
			return $result;
		}
		
		function equipmentset($level, $company)
		{
			$botlevel = botlevelset($level);
			$shipproperty["itemtype"] = $company;
			$shipproperty["level"] = $botlevel;
			$shipid = objectsearch($shipproperty, $_SESSION["data"]["items"]);
			
			$result["equipment"][] = new equipment($shipid, 1, "ship");
			$shipdata1 = $_SESSION["data"]["items"]["$shipid"];
			
			$shipproperties = get_object_vars($shipdata1);
			$shipdata = new emptyclass;
			foreach($shipproperties as $property=>$ertek)
			{
				$shipdata->$property = $ertek;
			}
			
			$result["ship"] = $shipdata;
			$result["level"] = $botlevel;
			
			foreach($_SESSION["data"]["items"] as $itemid=>$item)
			{
				$items["$item->slot"]["$itemid"] = $item;
			}
			
			foreach($items["extender"] as $itemdata)
			{
				if(isset($extendertypes))
				{
					if(!in_array($itemdata->effect, $extendertypes)) $extendertypes[] = $itemdata->effect;
				}
				else $extendertypes[] = $itemdata->effect;
			}
			
			$already_extender[] = "";
			for($szam = 0; $szam < $shipdata->extenderslot; $szam++)
			{
				if($extenderlevel = equipmentrate3($shipdata->maxextenderlevel))
				{
					do
					{
						$extendertype = $extendertypes[rand(0, count($extendertypes) - 1)];
					}
					while(in_array($extendertype, $already_extender));
					$already_extender[] = $extendertype;
					
					$extendersearch["effect"] = $extendertype;
					$extendersearch["level"] = $extenderlevel;
					$extenderid = objectsearch($extendersearch, $_SESSION["data"]["items"]);
					$extenderdata = $_SESSION["data"]["items"]["$extenderid"];
					$effect = $extenderdata->effect;
				
					if($effect == "basicammostorage" or $effect == "basiccargo") $shipdata->$effect *= $extenderdata->slotextend;
					else $shipdata->$effect += $extenderdata->slotextend;
				}
			}
			
			for($szam = 0; $szam < $shipdata->cannonslot; $szam++)
			{
				if($cannonlevel = equipmentrate10($shipdata->maxcannonlevel))
				{
					$type = rand(0, 2);
					if(!$type) $itemtype = "pulse";
					else $itemtype = "cannon";
					$cannonkeres["itemtype"] = $itemtype;
					$cannonkeres["level"] = $cannonlevel;
					$cannonid = objectsearch($cannonkeres, $items["cannon"]);
					$result["equipment"][] = new equipment($cannonid, 1, "ship");
					$ammoneed[] = $cannonid;
				}
			}
			
			for($szam = 0; $szam < $shipdata->rocketslot; $szam++)
			{
				if($rocketlauncherlevel = equipmentrate10($shipdata->maxrocketlevel))
				{
					$type = rand(0, 2);
					if(!$type) $itemtype = "rocketlauncher";
					else $itemtype = "sablauncher";
					$rocketlauncherkeres["itemtype"] = $itemtype;
					$rocketlauncherkeres["level"] = $rocketlauncherlevel;
					$rocketlauncherid = objectsearch($rocketlauncherkeres, $items["rocketlauncher"]);
					$result["equipment"][] = new equipment($rocketlauncherid, 1, "ship");
					$ammoneed[] = $rocketlauncherid;
				}
			}
			
			for($szam = 0; $szam < $shipdata->rifleslot; $szam++)
			{
				if($riflelevel = equipmentrate10($shipdata->maxriflelevel))
				{
					$riflekeres["level"] = $riflelevel;
					$rifleid = objectsearch($riflekeres, $items["rifle"]);
					$result["equipment"][] = new equipment($rifleid, 1, "ship");
					$ammoneed[] = $rifleid;
				}
			}
			
			for($szam = 0; $szam < $shipdata->shieldslot; $szam++)
			{
				if($shieldlevel = equipmentrate10($shipdata->maxshieldlevel))
				{
					$type = rand(0, 2);
					if(!$type) $itemtype = "highcapacityshield";
					else $itemtype = "quickrechargeshield";
					$shieldkeres["itemtype"] = $itemtype;
					$shieldkeres["level"] = $shieldlevel;
					$itemid = objectsearch($shieldkeres, $items["shield"]);
					$result["equipment"][] = new equipment($itemid, 1, "ship");
				}
			}
			
			for($szam = 0; $szam < $shipdata->hullslot; $szam++)
			{
				if($hulllevel = equipmentrate10($shipdata->maxhulllevel))
				{
					$hullkeres["level"] = $hulllevel;
					$itemid = objectsearch($hullkeres, $items["hull"]);
					$result["equipment"][] = new equipment($itemid, 1, "ship");
				}
			}
			
			for($szam = 0; $szam < $shipdata->generatorslot; $szam++)
			{
				if($generatorlevel = equipmentrate10($shipdata->maxgeneratorlevel))
				{
					$generatorkeres["level"] = $generatorlevel;
					$itemid = objectsearch($generatorkeres, $items["generator"]);
					$result["equipment"][] = new equipment($itemid, 1, "ship");
				}
			}
			
			for($szam = 0; $szam < $shipdata->batteryslot; $szam++)
			{
				if($batterylevel = equipmentrate10($shipdata->maxbatterylevel))
				{
					$batterykeres["level"] = $batterylevel;
					$itemid = objectsearch($batterykeres, $items["battery"]);
					$result["equipment"][] = new equipment($itemid, 1, "ship");
				}
			}
			
			$equipments = $items["equipment"];
			sort($equipments);
			
			for($szam = 0; $szam < $shipdata->equipmentslot; $szam++)
			{
				if(extrarate($shipdata->level))
				{
					$success = 0;
					do
					{
						$equipment = $equipments[rand(0, count($equipments) - 1)];
						if(!isset($eqs) or !in_array($equipment->itemid, $eqs))
						{
							$eqs[] = $equipment->itemid;
							$ammoneed[] = $equipment->itemid;
							$result["equipment"][] = new equipment($equipment->itemid, 1, "ship");
							$success = 20;
						}
						$success++;
					}
					while($success < 20);
				}
			}
			
			$squadronnum = 0;
			for($szam = 0; $szam < $shipdata->hangarslot; $szam++)
			{
				if($hangarlevel = equipmentrate3($shipdata->maxhangarlevel))
				{
					$hangarkeres["level"] = $hangarlevel;
					$itemid = objectsearch($hangarkeres, $items["hangar"]);
					$result["equipment"][] = new equipment($itemid, 1, "ship");
					$squadronnum += $_SESSION["data"]["items"]["$itemid"]->squadronplace;
				}
			}
			
			$squadronids[] = "";
			if(isset($_SESSION["gamedata"]["characters"]["friend"]["squadrons"])) foreach($_SESSION["gamedata"]["characters"]["friend"]["squadrons"] as $id) $squadronids[] = $id->id;
			if(isset($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"])) foreach($_SESSION["gamedata"]["characters"]["enemy"]["squadrons"] as $id) $squadronids[] = $id->id;
			
			for($szam = 0; $szam < $squadronnum; $szam++)
			{
				if($squadronlevel = equipmentrate10($result["ship"]->maxsquadronlevel))
				{
					$squadronkeres["level"] = $squadronlevel;
					$squadronitemid = objectsearch($squadronkeres, $items["squadron"]);
					$squadrondata = $items["squadron"]["$squadronitemid"];
					
					$squadronid = idgenerate("botsquad", $squadronids, 5);
					$squadronids[] = $squadronid;
					
					$squadronname = "Botraj" . substr($squadronid, 8, strlen($squadronid) - 1);
					
					$squadron = new squadron($squadronid, $squadronname);
					foreach(get_object_vars($squadrondata) as $name=>$ertek)
					{
						$squadron->$name = $ertek;
					}
					
					$result["equipment"][] = new equipment($squadron->itemid, 1, $squadronid);
					$style = (rand(0, 1)) ? "cannon" : "rifle";
					$squadron->style = $style;
					$result["squadrons"]["$squadronid"] = $squadron;
					
					
					for($weaponnum = 0; $weaponnum < $squadron->weaponslot; $weaponnum++)
					{
						if($weaponlevel = equipmentrate10($squadron->maxweaponlevel))
						{
							$weaponrate = rand(1, 10);
							switch($style)
							{
								case "cannon":
									if($weaponrate <= 5) $weaponkeres["itemtype"] = "squadroncannon";
									elseif($weaponrate > 5 and $weaponrate <= 8) $weaponkeres["itemtype"] = "squadronpulse";
									elseif($weaponrate > 8 and $weaponrate <= 10) $weaponkeres["itemtype"] = "squadronrifle";
								break;
								case "rifle":
									if($weaponrate <= 7) $weaponkeres["itemtype"] = "squadronrifle";
									elseif($weaponrate > 7 and $weaponrate <= 9) $weaponkeres["itemtype"] = "squadroncannon";
									elseif($weaponrate > 9 and $weaponrate <= 10) $weaponkeres["itemtype"] = "squadronpulse";
								break;
							}
							$weaponkeres["level"] = $weaponlevel;
							$weaponid = objectsearch($weaponkeres, $items["squadroncannon"]);
							
							unset($weaponkeres);
							$result["equipment"][] = new equipment($weaponid, 1, $squadronid);
							$ammoneed[] = $weaponid;
						}
					}
					
					for($squadronshieldnum = 0; $squadronshieldnum < $squadron->shieldslot; $squadronshieldnum++)
					{
						if($squadronshieldlevel = equipmentrate10($squadron->maxshieldlevel))
						{
							$squadronshieldsearch["itemtype"] = (rand(0, 1)) ? "squadronshield" : "squadronquickrechargeshield";
							$squadronshieldsearch["level"] = $squadronshieldlevel;
							$squadronshieldid = objectsearch($squadronshieldsearch, $items["squadronshield"]);
							
							$result["equipment"][] = new equipment($squadronshieldid, 1, $squadronid);
						}
					}
					
					for($squadronhullnum = 0; $squadronhullnum < $squadron->hullslot; $squadronhullnum++)
					{
						if($squadronhulllevel = equipmentrate10($squadron->maxhulllevel))
						{
							$squadronhullsearch["level"] = $squadronhulllevel;
							$squadronhullid = objectsearch($squadronhullsearch, $items["squadronhull"]);
							
							$result["equipment"][] = new equipment($squadronhullid, 1, $squadronid);
						}
					}
					
					for($squadronbatterynum = 0; $squadronbatterynum < $squadron->batteryslot; $squadronbatterynum++)
					{
						if($squadronbatterylevel = equipmentrate10($squadron->maxbatterylevel))
						{
							$squadronbatterysearch["level"] = $squadronbatterylevel;
							$squadronbatteryid = objectsearch($squadronbatterysearch, $items["battery"]);
							
							$result["equipment"][] = new equipment($squadronbatteryid, 1, $squadronid);
						}
					}
				}
					
			}

			if(isset($ammoneed))
			{
				$ammorate = 0;
				foreach($ammoneed as $itemid)
				{
					$itemdata = $_SESSION["data"]["items"]["$itemid"];
					
					if($itemdata->type == "equipment") $ammonum = $result["ship"]->level / 20;
					else $ammonum = $itemdata->ammousage;
					
					if(isset($ammoset["$itemdata->ammotype"])) $ammoset["$itemdata->ammotype"] += $ammonum;
					else $ammoset["$itemdata->ammotype"] = $ammonum;
					$ammorate += $ammonum;
				}
				
				foreach($ammoset as $ammotype=>$ammonum)
				{
					$ammorateset["$ammotype"] = $ammonum / $ammorate * $result["ship"]->basicammostorage;
				}
				
				foreach($ammorateset as $ammotype=>$maxammonum)
				{
					$ammosearch["itemtype"] = $ammotype;

					if(!$ammos = objectsearch($ammosearch, $items["ammo"]))
					{
						$specialammosearch["itemid"] = $ammotype;
						$ammoid = objectsearch($specialammosearch, $items["ammo"]);
						
						$amount = $maxammonum * ammorate();
						settype($amount, "integer");
						$result["ammo"][] = new ammo($ammoid, "ship", 1, $amount);
					}
					else
					{
						foreach($ammos as $ammoid)
						{
							$ammodata["$ammoid"] = $items["ammo"]["$ammoid"];
						}
						
						
						for($ammopart = 0; $ammopart < 10; $ammopart++)
						{
							$normalammosearch["level"] = ammolevelrate();
							$ammoid = objectsearch($normalammosearch, $ammodata);
							$amount = $maxammonum * ammorate() * 0.1;
							settype($amount, "integer");
							
							if(isset($result["ammo"]["$ammoid"])) $result["ammo"]["$ammoid"]->amount += $amount;
							else $result["ammo"]["$ammoid"] = new ammo($ammoid, "ship", 1, $amount);
						}
						
						unset($ammodata);
					}
				}
			}
			return $result;
		}
		
		function groupset($character, $skill = 0, $company = 0)
		{
			$groupids[] = "";
			if($skill and $company == "emf")
			{
				if($skill["emfp"]->level)
				{
					$maxgroupmembernum = $skill["emfp"]->level + 1;
					foreach($character["squadrons"] as $squadronid=>$squadron)
					{
						$success = 0;
						if(isset($groups["$squadron->style"]))
						{
							foreach($groups["$squadron->style"] as $groupid=>$group)
							{
								if($group->membernum < $maxgroupmembernum)
								{
									$members = unserialize($group->members);
									$members[] = $squadronid;
									$group->membernum += 1;
									$group->members = serialize($members);
									unset($members);
									$character["squadrons"]["$squadronid"]->group = $groupid;
									$success = 1;
								}
							}
						}
						if(!$success)
						{
							$groupid = idgenerate("botgroup", $groupids, 5);
							if($squadron->style == "cannon") $gname = "Ágyúscsapat";
							elseif($squadron->style == "rifle") $gname = "Gépágyúscsapat";
							$groupname = "$gname" . substr($groupid, 8, strlen($groupid) - 1);
							$groupids[] = $groupid;
							
							$character["squadrons"]["$squadronid"]->group = $groupid;
							$newgroup = new group($groupid, $groupname);
							$members[] = $squadronid;
							$newgroup->membernum = 1;
							$newgroup->members = serialize($members);
							unset($members);
							$newgroup->style = $squadron->style;
							$groups["$squadron->style"]["$groupid"] = $newgroup;
						}
						
					}
				}
				
				if(isset($groups["cannon"]))
				{
					foreach($groups["cannon"] as $groupid=>$group)
					{
						$character["groups"]["$groupid"] = $group;
					}
				}
				if(isset($groups["rifle"]))
				{
					foreach($groups["rifle"] as $groupid=>$group)
					{
						$character["groups"]["$groupid"] = $group;
					}
				}
			}
			
			$nogroup = new group("no", "Csapatba nem sorolt");
			$nogroup->membernum = 0;
			$nogroup->members = 0;
			
			foreach($character["squadrons"] as $squadronid=>$squadron)
			{
				if($squadron->group == "no")
				{
					$nogroup->membernum += 1;
					if(!$nogroup->members)
					{
						$members[] = $squadronid;
						$nogroup->members = serialize($members);
						unset($members);
					}
					else
					{
						$members = unserialize($nogroup->members);
						$members[] = $squadronid;
						$nogroup->members = serialize($members);
						unset($members);
					}
				}
			}
			
			$character["groups"]["no"] = $nogroup;
			return $character;
		}
		
		function controlset($charid)
		{
			$character = &$_SESSION["game"]["$charid"];
			$shipcontrol = new emptyclass;
			
			$shipcontrol->target = "no";
			$shipcontrol->targettry = "no";
			$shipcontrol->shieldregen = 100;
			$shipcontrol->dmgreceived = 0;
			$shipcontrol->genenergyleft = 0;
			$shipcontrol->lastattack = 0;
			
			if(isset($character["ship"]["cannon"]))
			{
				foreach($character["ship"]["cannon"] as $cannon)
				{
					$cannons["$cannon->itemtype"] = 1;
				}
				
				if(isset($cannons["cannon"]))
				{
					$shipcontrol->cannonammo = "no";
					$shipcontrol->cannondamage = 100;
				}
				
				if(isset($cannons["pulse"]))
				{
					$shipcontrol->pulseammo = "no";
					$shipcontrol->pulsedamage = 100;;
				}
			}
			
			if(isset($character["ship"]["rocketlauncher"]))
			{
				foreach($character["ship"]["rocketlauncher"] as $rocketlauncher)
				{
					$rocketlaunchers["$rocketlauncher->itemtype"] = 1;
				}
				
				if(isset($rocketlaunchers["rocketlauncher"]))
				{
					$shipcontrol->rocketlauncherammo = "no";
					$shipcontrol->rocketlauncherdamage = 100;
				}
				
				if(isset($rocketlaunchers["sablauncher"]))
				{
					$shipcontrol->sablauncherammo = "no";
					$shipcontrol->sablauncherdamage = 100;
				}
			}
			
			if(isset($character["ship"]["rifle"]))
			{
				$shipcontrol->rifledamage = 100;
				$shipcontrol->rifleammo = "no";
			}
			$character["control"]["ship"] = $shipcontrol;
			
			if(isset($character["squadrons"]))
			{
				foreach($character["squadrons"] as $squadronid=>$squadron)
				{
					$squadroncontrol = new emptyclass;
					$squadroncontrol->target = "no";
					$squadroncontrol->targettry = "no";
					$squadroncontrol->targetselect = "auto";
					$squadroncontrol->targetstyle = "auto";
					$squadroncontrol->takeoff = "auto";						
					$squadroncontrol->place = "space";
					$squadroncontrol->dmgreceived = 0;
					$squadroncontrol->callback = 0;
					$squadroncontrol->callbackcount = 0;
					
					if(isset($squadron["squadroncannon"]))
					{
						foreach($squadron["squadroncannon"] as $squadroncannon)
						{
							$squadroncannons["$squadroncannon->itemtype"] = 1;
						}
						
						if(isset($squadroncannons["squadroncannon"]))
						{
							$squadroncontrol->squadroncannondamage = 100;
							$squadroncontrol->squadroncannonammo = "no";
						}
						
						if(isset($squadroncannons["squadronpulse"]))
						{
							$squadroncontrol->squadronpulsedamage = 100;
							$squadroncontrol->squadronpulseammo = "no";
						}
						
						if(isset($squadroncannons["squadronrifle"]))
						{
							$squadroncontrol->squadronrifledamage = 100;
							$squadroncontrol->squadronrifleammo = "no";
						}
					}
					
					if(isset($squadron["squadronhull"]))
					{
						$squadroncontrol->returnstyle = "hull";
						$squadroncontrol->returnvalue = 50;
					}
					elseif(isset($squadron["squadronshield"]))
					{
						$squadroncontrol->returnstyle = "shield";
						$squadroncontrol->returnvalue = 50;
					}
					else
					{
						$squadroncontrol->returnstyle = "manual";
						$squadroncontrol->returnvalue = 0;
					}
					
					if(isset($squadron["squadronshield"]) and isset($squadron["battery"])) $squadroncontrol->squadronshieldregen = 100;
					$character["control"]["$squadronid"] = $squadroncontrol;
				}
			}
			if(isset($character["groups"]))
			{
				foreach($character["groups"] as $groupid=>$group)
				{
					$groupcontrol = new emptyclass;
				
					$groupcontrol->target = "no";
					$groupcontrol->targettry = "no";
					$groupcontrol->targetselect = "auto";
					$groupcontrol->targetstyle = $group->targetstyle;

					$character["control"]["$groupid"] = $groupcontrol;
				}
			}
		}
?>