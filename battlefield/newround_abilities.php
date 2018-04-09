<?php
	function extras(&$character, &$energy, $id)
	{
		extracooldown($character["ship"]["equipment"]);
		
		if($character["ship"]["equipment"]["bol01"]->equipped) bol($character, $energy, $id);
		if($character["ship"]["equipment"]["edi01"]->equipped) edi($character, $energy, $id);
		if($character["ship"]["equipment"]["rep01"]->equipped or $character["ship"]["equipment"]["rep02"]->equipped or $character["ship"]["equipment"]["rep03"]->equipped) rep($character, $energy, $id);
		if($character["ship"]["equipment"]["efi01"]->equipped) efi($character, $energy, $id);
		if($character["ship"]["equipment"]["sre01"]->equipped) sre($character, $energy, $id);
		if($character["ship"]["equipment"]["mac01"]->equipped) mac($character, $energy, $id);
		if($character["ship"]["equipment"]["pdu01"]->equipped) pdu($character, $energy, $id);
		if($character["ship"]["equipment"]["mdl01"]->equipped) mdl($character, $energy, $id);
		if($character["ship"]["equipment"]["ser01"]->equipped) ser($character, $energy, $id);
		
	}
		
		function extracooldown(&$equipments)
		{
			foreach($equipments as $equipment)
			{
				if($equipment->actualreload > 0) $equipment->actualreload -= 1;
				if($equipment->actualactive > 0) $equipment->actualactive -= 1;
			}
		}
		
		function bol(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["bol01"];
			if(!isset($character["ammo"])) return;
			if(!isset($character["ship"]["battery"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["bol01"])) $use = 1;
			}
			else
			{
				if($energy["energystatus"] < 40 and $character["control"]["ship"]->dmgreceived < 2) $use = 1;
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
				}
			}
		}
		
		function rep(&$character, &$energy, $id)
		{
			if($character["ship"]["equipment"]["rep01"]->equipped) $equipmentdata = &$character["ship"]["equipment"]["rep01"];
			elseif($character["ship"]["equipment"]["rep02"]->equipped) $equipmentdata = &$character["ship"]["equipment"]["rep02"];
			elseif($character["ship"]["equipment"]["rep03"]->equipped) $equipmentdata = &$character["ship"]["equipment"]["rep03"];
			
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["rep01"])) $use = 1;
				elseif(isset($_SESSION["gamedata"]["playeruse"]["rep02"])) $use = 1;
				elseif(isset($_SESSION["gamedata"]["playeruse"]["rep03"])) $use = 1;
			}
			else
			{
				if(isset($character["ship"]["hull"]))
				{	
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 30) $use = 1;
				}
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					foreach($character["ship"]["hull"] as &$hull)
					{
						$hull->actualhull += $hull->hullenergy * $equipmentdata->effect / 100;
						if($hull->actualhull > $hull->hullenergy) $hull->actualhull = $hull->hullenergy;
					}
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Burkolatjavító Robotot használt.";
				}
			}
		}
			
			function attackers($id)
			{
				foreach($_SESSION["game"] as $chid=>$character)
				{
					foreach($character["control"] as $aid=>$control)
					{
						if(property_exists($control, "target"))
						{
							if($control->target == $id)
							{
								
								$attacker["id"] = ($aid == "ship") ? $chid : $aid;
								$attacker["style"] = ($aid == "ship") ? "ship" : "squadron";
								$attackers[] = $attacker;
							}
						}
					}
				}
				
				if(isset($attackers))
				{
					return $attackers;
				}
				else return 0;
			}
		
		function edi(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["edi01"];
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["edi01"])) $use = 1;
			}
			else
			{
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 40) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{	
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 60) $use = 1;
				}
				else
				{
					if($character["ship"]["ship"][0]->actualcorehull / $character["ship"]["ship"][0]->corehull * 100 < 80) $use = 1;
				}
			}
			
			if($use and attackers($id) and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Elektronikus Zavaróimpulzust (EDI01) használt.";
					targetidunset($id);
				}
			}
		}
		
		function efi(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["efi01"];
			
			if(!attackers($id)) return;
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["efi01"])) $use = 1;
			}
			else
			{
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{	
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 80) $use = 1;
				}
				else
				{
					if($character["ship"]["ship"][0]->actualcorehull / $character["ship"]["ship"][0]->corehull * 100 < 80) $use = 1;
				}
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Energiamezőt (EFI01) használt.";
				}
			}
		}
		
		function sre(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["sre01"];
			
			if(!attackers($id)) return;
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["sre01"])) $use = 1;
			}
			else
			{
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
				}
			}
		}
		
		function mac(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["mac01"];
			
			if(!attackers($id)) return;
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["mac01"])) $use = 1;
			}
			else
			{
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 60) $use = 1;
				}
				else
				{
					$actualcorehull = $character["ship"]["ship"][0]->actualcorehull;
					$corehull = $character["ship"]["ship"][0]->corehull;
					
					if($actualcorehull / $corehull * 100 < 80) $use = 1;
				}
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Mágneses Ködöt (MAC01) használt.";
				}
			}
		}
		
		function pdu(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["pdu01"];
			
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["pdu01"]) and $_SESSION["gamedata"]["playeruse"]["equipmenttarget"]["pdu01"] != "no")
				{
					$targetid = $_SESSION["gamedata"]["playeruse"]["equipmenttarget"]["pdu01"];
					$use = 1;
				}
			}
			else
			{
				if(!$attackers = attackers($id)) return;
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 60) $use = 1;
				}
				else
				{
					$actualcorehull = $character["ship"]["ship"][0]->actualcorehull;
					$corehull = $character["ship"]["ship"][0]->corehull;
					
					if($actualcorehull / $corehull * 100 < 80) $use = 1;
				}
				
				foreach($attackers as $attacker)
				{
					if($attacker["style"] == "ship") $shipattackers[] = $attacker["id"];
				}
				if(isset($shipattackers)) $targetid = $shipattackers[rand(0, count($shipattackers) - 1)];
				else return;
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					
					$_SESSION["game"]["$targetid"]["ship"]["equipment"]["pdu01"]->actualactive = $equipmentdata->active;
					
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					$targetname = $_SESSION["game"]["$targetid"]["charname"];
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Plazma Zavaró Egységet (PDU01) használt $targetname játékoson.";
				}
			}
		}
		
		function mdl(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["mdl01"];
			
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["mdl01"]) and $_SESSION["gamedata"]["playeruse"]["equipmenttarget"]["mdl01"] != "no")
				{
					$targetid = $_SESSION["gamedata"]["playeruse"]["equipmenttarget"]["mdl01"];
					$use = 1;
				}
			}
			else
			{
				if(!$attackers = attackers($id)) return;
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 60) $use = 1;
				}
				else
				{
					$actualcorehull = $character["ship"]["ship"][0]->actualcorehull;
					$corehull = $character["ship"]["ship"][0]->corehull;
					
					if($actualcorehull / $corehull * 100 < 80) $use = 1;
				}
				
				foreach($attackers as $attacker)
				{
					if($attacker["style"] == "ship") $shipattackers[] = $attacker["id"];
				}
				if(isset($shipattackers)) $targetid = $shipattackers[rand(0, count($shipattackers) - 1)];
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					
					$_SESSION["game"]["$targetid"]["ship"]["equipment"]["mdl01"]->actualactive = $equipmentdata->basicactive;
					
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					$targetname = $_SESSION["game"]["$targetid"]["charname"];
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Rakétaelhárító Lézert (MDL01) használt $targetname játékoson.";
				}
			}
		}
		
		function ser(&$character, &$energy, $id)
		{
			$equipmentdata = &$character["ship"]["equipment"]["ser01"];
			
			if(!isset($character["ammo"])) return;
			else
			{
				if(!isset($character["ammo"]["$equipmentdata->ammo"])) return;
				if($character["ammo"]["$equipmentdata->ammo"]->amount < $equipmentdata->ammousage) return;
			}
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["ser01"]))
				{
					$use = 1;
				}
			}
			else
			{
				if(!$attackers = attackers($id)) return;
				if(isset($character["ship"]["shield"]))
				{
					$shieldenergy = 0;
					$maxshieldenergy = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$shieldenergy += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
					if($shieldenergy / $maxshieldenergy * 100 < 60) $use = 1;
				}
				elseif(isset($character["ship"]["hull"]))
				{
					$hullenergy = 0;
					$maxhullenergy = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$hullenergy += $hull->actualhull;
						$maxhullenergy += $hull->hullenergy;
					}
					if($hullenergy / $maxhullenergy * 100 < 60) $use = 1;
				}
				else
				{
					$actualcorehull = $character["ship"]["ship"][0]->actualcorehull;
					$corehull = $character["ship"]["ship"][0]->corehull;
					
					if($actualcorehull / $corehull * 100 < 80) $use = 1;
				}
				
				foreach($attackers as $attacker)
				{
					if($attacker["style"] == "squadron") $shipattackers[] = $attacker["id"];
				}
				if(!isset($shipattackers)) return;
			}
			
			if($use and !$equipmentdata->actualreload)
			{
				if(($batteryindex = batteryindexchoose($energy["energy"], $equipmentdata->energyusage)) > -1 or $character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
				{
					if($character["control"]["ship"]->genenergyleft >= $equipmentdata->energyusage)
					{
						$character["control"]["ship"]->genenergyleft -= $equipmentdata->energyusage;
					}
					elseif($batteryindex > -1)
					{
						$character["ship"]["battery"][$batteryindex]->actualcapacity -= $equipmentdata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$equipmentdata->actualreload = $equipmentdata->reload;
					$equipmentdata->actualactive = $equipmentdata->active;
					
					
					$character["ammo"]["$equipmentdata->ammo"]->amount -= $equipmentdata->ammousage;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Rajzavaró Elektronsugarat (SER01) használt.";
				}
			}
		}
		
	function abilities(&$character, &$energy, $id)
	{
		abilitycooldown($character["skill"]);
		
		switch($character["ship"]["ship"][0]->company)
		{
			case "emf":
			
			break;
			case "pdm":
				if($character["skill"]["pdma1"]->level) pdma1($character, $energy, $id);
				if($character["skill"]["pdma2"]->level) pdma2($character, $energy, $id);
			break;
			case "idf":
				if($character["skill"]["idfa1"]->level) idfa1($character, $energy, $id);
			break;
			
			case "mfa":
				if($character["skill"]["mfaa1"]->level) mfaa1($character, $energy, $id);
			break;
			case "gaa":
			
			break;
			case "cri":
			
			break;
		}
	}
		
		function abilitycooldown(&$skills)
		{
			foreach($skills as $skill)
			{
				if(property_exists($skill, "actualactive"))
				{
					if($skill->actualactive) $skill->actualactive -= 1;
				}
				if(property_exists($skill, "actualreload"))
				{
					if($skill->actualreload) $skill->actualreload -= 1;
				}
			}
		}
		
		function pdma1(&$character, &$energy, $id)
		{
			$skilldata = $character["skill"]["pdma1"];
			
			$genenergy = &$character["control"]["ship"]->genenergyleft;
			$batteryindex = batteryindexchoose($energy["energy"], $skilldata->energyusage);
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["pdma1"]))
				{
					$use = 1;
					$target = $_SESSION["gamedata"]["playeruse"]["skilltarget"]["pdma1"];
				}
			}
			else
			{
				$company = $_SESSION["gamedata"]["userdata"]["$id"]->ally;
				
				foreach($_SESSION["gamedata"]["characters"]["$company"]["ships"] as $ship)
				{
					$targetid = $ship->id;
					
					if(isset($_SESSION["game"]["$targetid"]["ship"]["shield"]))
					{
						$maxshieldenergy = 0;
						$actualshield = 0;
						foreach($_SESSION["game"]["$targetid"]["ship"]["shield"] as $shield)
						{
							$maxshieldenergy += $shield->shieldenergy;
							$actualshield += $shield->actualshield;
						}
						
						if(60 > ($shieldpercent = $actualshield / $maxshieldenergy * 100) and $_SESSION["game"]["$targetid"]["control"]["ship"]->dmgreceived < 1 and !$_SESSION["game"]["$targetid"]["skill"]["pdma1"]->actualactive)
						{
							$shieldstatustomb["$targetid"] = $shieldpercent;
						}
					}
				}

				if(isset($shieldstatustomb))
				{
					asort($shieldstatustomb);
					foreach($shieldstatustomb as $target=>$shieldstatus) break;
					$use = 1;
				}
			}
			
			if(!isset($target)) return;
			
			if($use and !$skilldata->actualreload and $target != "no")
			{
				if($batteryindex > -1 or $genenergy >= $skilldata->energyusage)
				{
					if($genenergy >= $skilldata->energyusage)
					{
						$genenergy -= $skilldata->energyusage;
					}
					else
					{
						$character["ship"]["battery"]["$batteryindex"]->actualcapacity -= $skilldata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$skilldata->actualreload = $skilldata->reload;
					$_SESSION["game"]["$target"]["skill"]["pdma1"]->actualactive = $skilldata->active;
					$_SESSION["game"]["$target"]["skill"]["pdma1"]->actualeffect = $skilldata->effect;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Pajzsregeneráció (PDMA1) képességet használt " . $_SESSION["game"]["$target"]["charname"] . " játékoson.";
				}
			}
		}
		
		function pdma2(&$character, &$energy, $id)
		{
			$skilldata = $character["skill"]["pdma2"];
			
			$genenergy = &$character["control"]["ship"]->genenergyleft;
			$batteryindex = batteryindexchoose($energy["energy"], $skilldata->energyusage);
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["pdma2"]))
				{
					$use = 1;
					$target = $_SESSION["gamedata"]["playeruse"]["skilltarget"]["pdma2"];
				}
			}
			else
			{
				$company = $_SESSION["gamedata"]["userdata"]["$id"]->ally;
				
				foreach($_SESSION["gamedata"]["characters"]["$company"]["ships"] as $ship)
				{
					$targetid = $ship->id;
					
					if($_SESSION["game"]["$targetid"]["control"]["ship"]->dmgreceived < 1 and !$_SESSION["game"]["$targetid"]["skill"]["pdma2"]->actualactive)
					{
						if(isset($_SESSION["game"]["$targetid"]["ship"]["shield"]))
						{
							$maxshieldenergy = 0;
							$actualshield = 0;
							foreach($_SESSION["game"]["$targetid"]["ship"]["shield"] as $shield)
							{
								$maxshieldenergy += $shield->shieldenergy;
								$actualshield += $shield->actualshield;
							}
							
							if(40 > ($shieldpercent = $actualshield / $maxshieldenergy * 100))
							{
								$shieldstatustomb["$targetid"] = $shieldpercent;
							}
						}
						
						if(isset($_SESSION["game"]["$targetid"]["ship"]["hull"]))
						{
							$maxhullenergy = 0;
							$actualhull = 0;
							foreach($_SESSION["game"]["$targetid"]["ship"]["hull"] as $hull)
							{
								$maxhullenergy += $hull->hullenergy;
								$actualhull += $hull->actualhull;
							}
						}
						
						if(40 > ($hullpercent = $actualhull / $maxhullenergy * 100))
						{
							$hullstatustomb["$targetid"] = $hullpercent;
						}
						
						$corehull = $_SESSION["game"]["$targetid"]["ship"]["ship"][0]->corehull;
						$actualcorehull = $_SESSION["game"]["$targetid"]["ship"]["ship"][0]->actualcorehull;
						if(70 > ($corehullpercent = $actualcorehull / $corehull * 100))
						{
							$corehullstatustomb["$targetid"] = $corehullpercent;
						}
					}
				}
				
				$shieldstatus = 1;
				if(isset($character["ship"]["shield"]))
				{
					$maxshieldenergy = 0;
					$actualshield = 0;
					foreach($character["ship"]["shield"] as $shield)
					{
						$maxshieldenergy += $shield->shieldenergy;
						$actualshield += $shield->actualshield;
					}
					$shieldstatus = $actualshield / $maxshieldenergy;
				}
				
				$hullstatus = 1;
				if(isset($character["ship"]["hull"]))
				{
					$maxhullenergy = 0;
					$actualhull = 0;
					foreach($character["ship"]["hull"] as $hull)
					{
						$maxhullenergy += $hull->hullenergy;
						$actualhull += $hull->actualhull;
					}
					$hullstatus = $actualhull / $maxhullenergy;
				}
				
				$corehullstatus = $character["ship"]["ship"][0]->actualcorehull / $character["ship"]["ship"][0]->corehull;
			
				$shipstatus = 100 * $shieldstatus * $hullstatus * $corehullstatus;
				
				if($shipstatus > 12)
				{
					if(isset($shieldstatustomb))
					{
						asort($shieldstatustomb);
						foreach($shieldstatustomb as $target=>$shieldstatus) if($target != $id) break;
						$use = 1;
					}
					if(isset($hullstatustomb))
					{
						asort($hullstatustomb);
						foreach($hullstatustomb as $target=>$hullstatus) if($target != $id) break;
						$use = 1;
					}
					if(isset($corehullstatustomb))
					{
						asort($corehullstatustomb);
						foreach($corehullstatustomb as $target=>$corehullstatus) if($target != $id) break;
						$use = 1;
					}
				}
			}
			
			
			if(!isset($target)) return;
			
			if($use and !$skilldata->actualreload and $target != "no")
			{
				if($batteryindex > -1 or $genenergy >= $skilldata->energyusage)
				{
					if($genenergy >= $skilldata->energyusage)
					{
						$genenergy -= $skilldata->energyusage;
					}
					else
					{
						$character["ship"]["battery"]["$batteryindex"]->actualcapacity -= $skilldata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$skilldata->actualreload = $skilldata->reload;
					$_SESSION["game"]["$target"]["skill"]["pdma2"]->actualactive = $skilldata->basicactive;
					$_SESSION["game"]["$target"]["skill"]["pdma2"]->defender = $id;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Életmentő Manőver (PDMA2) képességet használt " . $_SESSION["game"]["$target"]["charname"] . " játékoson.";
				}
			}
		}
		
		function idfa1(&$character, &$energy, $id)
		{
			$skilldata = $character["skill"]["idfa1"];
			
			$genenergy = &$character["control"]["ship"]->genenergyleft;
			$batteryindex = batteryindexchoose($energy["energy"], $skilldata->energyusage);
			
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["idfa1"]))
				{
					$use = 1;
				}
			}
			else
			{
				$actualshield = 0;
				$maxshieldenergy = 1;
				if(isset($character["ship"]["shield"]))
				{
					foreach($character["ship"]["shield"] as $shield)
					{
						$actualshield += $shield->actualshield;
						$maxshieldenergy += $shield->shieldenergy;
					}
				}
				
				$att = 0;
				$attackers = attackers($id);
				if($attackers)
				{
					foreach($attackers as $attacker)
					{
						if($attacker["style"] == "ship") $att = 1;
					}
				}
				
				
				if(60 < ($actualshield / $maxshieldenergy * 100) and $att)
				{
					$use = 1;
				}
			}
			
			if($use and !$skilldata->actualreload)
			{
				if($batteryindex > -1 or $genenergy >= $skilldata->energyusage)
				{
					if($genenergy >= $skilldata->energyusage)
					{
						$genenergy -= $skilldata->energyusage;
					}
					else
					{
						$character["ship"]["battery"]["$batteryindex"]->actualcapacity -= $skilldata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$skilldata->actualreload = $skilldata->reload;
					$skilldata->actualactive = $skilldata->active;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Kinetikus Pajzs (IDFA1) képességet használt.";
				}
			}
		}
		
		function mfaa1(&$character, &$energy, $id)
		{
			$skilldata = $character["skill"]["mfaa1"];
			
			$genenergy = &$character["control"]["ship"]->genenergyleft;
			$batteryindex = batteryindexchoose($energy["energy"], $skilldata->energyusage);
			
			$ally = $_SESSION["gamedata"]["userdata"]["$id"]->ally;
			switch($ally)
			{
				case "friend":
					$alliance = "enemy";
				break;
				case "enemy":
					$alliance = "friend";
				break;
			}
				
			$use = 0;
			if($id == "player")
			{
				if(isset($_SESSION["gamedata"]["playeruse"]["mfaa1"]))
				{
					$use = 1;
				}
			}
			else
			{
				if(isset($_SESSION["gamedata"]["characters"]["$alliance"]["squadrons"]))
				{
					if(count($_SESSION["gamedata"]["characters"]["$alliance"]["squadrons"]) and !$_SESSION["gamedata"]["allianceability"]["$ally"]["mfaa1"]->actualactive)
					{
						$use = 1;
					}
				}
			}
			
			if($use and !$skilldata->actualreload)
			{
				if($batteryindex > -1 or $genenergy >= $skilldata->energyusage)
				{
					if($genenergy >= $skilldata->energyusage)
					{
						$genenergy -= $skilldata->energyusage;
					}
					else
					{
						$character["ship"]["battery"]["$batteryindex"]->actualcapacity -= $skilldata->energyusage;
						$energy = energyset($character["ship"]["battery"]);
					}
					
					$skilldata->actualreload = $skilldata->reload;
					$_SESSION["gamedata"]["allianceability"]["$ally"]["mfaa1"]->actualactive = $skilldata->basicactive;
					$_SESSION["gamedata"]["allianceability"]["$ally"]["mfaa1"]->effect = $skilldata->effect;
					$_SESSION["gamedata"]["allianceability"]["$ally"]["mfaa1"]->owner = $id;
					
					$_SESSION["gamedata"]["log"][] = $character["charname"] . " Rajbénítás (MFAA1) képességet használt.";
				}
			}
		}
?>