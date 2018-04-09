<?php
	function shipload($user, $character, $ally)
	{
		if(isset($character["charid"])) $_SESSION["game"]["$user"]["charid"] = $character["charid"];
		if(isset($character["charname"])) $_SESSION["game"]["$user"]["charname"] = $character["charname"];
		if(isset($character["equipment"])) equipmentobject($user, $character);
		if(isset($character["equipment"])) extraobject($user, $character);
		if(isset($character["squadrons"])) squadronobject($user, $character, $ally);
		if(isset($character["ammo"])) ammoobject($user, $character);
		if(isset($character["skill"])) skillobject($user, $character);
		if(isset($character["groups"])) groupobject($user, $character);
		
		$_SESSION["gamedata"]["userdata"]["$user"] = new emptyclass;
		$_SESSION["gamedata"]["userdata"]["$user"]->ally = $ally;
		$_SESSION["gamedata"]["userdata"]["$user"]->score = 0;
		$_SESSION["gamedata"]["userdata"]["$user"]->level = $_SESSION["game"]["$user"]["ship"]["ship"][0]->level;
		$_SESSION["gamedata"]["userdata"]["$user"]->company = $_SESSION["game"]["$user"]["ship"]["ship"][0]->company;
		$_SESSION["gamedata"]["userdata"]["$user"]->charname = $character["charname"];
		
		$userset = new emptyclass;
		$userset->id = $user;
		$userset->name = $character["charname"];
		
		$_SESSION["gamedata"]["characters"]["$ally"]["ships"][] = $userset;
		
		if(isset($character["skill"]) and isset($character["equipment"]))
		{
			if($_SESSION["game"]["$user"]["skill"]["crip"]->level and $_SESSION["game"]["$user"]["ship"]["ship"][0]->company == "cri")
			{
				$effect = $_SESSION["game"]["$user"]["skill"]["crip"]->effect;
				foreach($_SESSION["game"]["$user"]["ship"]["equipment"] as &$equipment) $equipment->reload -= $effect;
				foreach($_SESSION["game"]["$user"]["skill"] as &$skill)
				{
					if(property_exists($skill, "reload")) $skill->reload -= $effect;
				}
			}
		}
	}
	
		function equipmentobject($user, $character)
		{
			foreach($character["equipment"] as $item)
			{
				$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
				if($item->place == "hangar" or $itemdata->slot == "extender" or $itemdata->slot == "squadron" or $itemdata->slot == "equipment") continue;
				else
				{
					$itemobject = new emptyclass;
					switch($itemdata->slot)
					{
						case "ship":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->level = $itemdata->level;
							$itemobject->name = $character["charname"];
							$itemobject->company = $itemdata->itemtype;
							$itemobject->corehull = $itemdata->corehull;
							$itemobject->actualcorehull = $itemdata->corehull;
							$itemobject->basiccargo = $itemdata->basiccargo;
							$itemobject->actualcargo = 0;
							$itemobject->basicammostorage = $itemdata->basicammostorage;
							$itemobject->actualammostorage = 0;
							if(isset($character["ammo"])) $itemobject->actualammostorage = ammocount($character["ammo"]);
						break;
						case "cannon":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->itemtype = $itemdata->itemtype;
							$itemobject->ammotype = $itemdata->ammotype;
							$itemobject->reload = $itemdata->reloadtime;
							$itemobject->accuracy = $itemdata->accuracy;
							$itemobject->hulldamage = $itemdata->hulldamage;
							$itemobject->shielddamage = $itemdata->shielddamage;
							$itemobject->energyusage = $itemdata->energyusage;
							$itemobject->ammousage = $itemdata->ammousage;
						break;
						case "rifle":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->ammotype = $itemdata->ammotype;
							$itemobject->reload = $itemdata->reloadtime;
							$itemobject->accuracy = $itemdata->accuracy;
							$itemobject->squadrondamage = $itemdata->squadrondamage;
							$itemobject->energyusage = $itemdata->energyusage;
							$itemobject->ammousage = $itemdata->ammousage;
						break;
						case "rocketlauncher":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->itemtype = $itemdata->itemtype;
							$itemobject->ammotype = $itemdata->ammotype;
							$itemobject->reload = $itemdata->reloadtime;
							$itemobject->accuracy = $itemdata->accuracy;
							$itemobject->hulldamage = $itemdata->rockethulldamage;
							$itemobject->shielddamage = $itemdata->rocketshielddamage;
							$itemobject->energyusage = $itemdata->energyusage;
							$itemobject->ammousage = $itemdata->ammousage;
							$itemobject->target = "ship";
						break;
						case "shield":
						case "squadronshield":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->shieldenergy = $itemdata->shieldenergy;
							$itemobject->recharge = $itemdata->recharge;
							$itemobject->energyusage = $itemdata->energyusage;
							$itemobject->actualshield = $itemdata->shieldenergy;
						break;
						case "squadronhull":
						case "hull":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->hullenergy = $itemdata->hullenergy;
							$itemobject->actualhull = $itemdata->hullenergy;
						break;
						case "generator":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->energyregen = $itemdata->energyregen;
						break;
						case "battery":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->capacity = $itemdata->capacity;
							$itemobject->maxrecharge = $itemdata->maxrecharge;
							$itemobject->actualcapacity = $itemdata->capacity;
						break;
						case "hangar":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->squadronplace = $itemdata->squadronplace;
							$itemobject->repair = $itemdata->repair;
							$itemobject->actualsquadronplace = 0;
						break;
						case "squadroncannon":
							$itemobject->itemid = $itemdata->itemid;
							$itemobject->itemtype = $itemdata->itemtype;
							$itemobject->ammotype = $itemdata->ammotype;
							$itemobject->reload = $itemdata->reloadtime;
							$itemobject->accuracy = $itemdata->accuracy;
							if($itemdata->squadrondamage)
							{
								$itemobject->target = "squadron";
								$itemobject->squadrondamage = $itemdata->squadrondamage;
							}
							else
							{
								$itemobject->target = "ship";
								$itemobject->hulldamage = $itemdata->hulldamage;
								$itemobject->shielddamage = $itemdata->shielddamage;
							}
							$itemobject->energyusage = $itemdata->energyusage;
							$itemobject->ammousage = $itemdata->ammousage;
						break;
					}
					if($item->place == "ship") $_SESSION["game"]["$user"]["$item->place"]["$itemdata->slot"][] = $itemobject;
					else $_SESSION["game"]["$user"]["squadrons"]["$item->place"]["$itemdata->slot"][] = $itemobject;
					
				}
			}
		}
		
			function ammocount($ammo)
			{
				$result = 0;
				foreach($ammo as $item)
				{
					if($item->place == "ship") $result += $item->amount;
				}
				return $result;
			}
		
		function extraobject($user, $character)
		{
			$level = $character["level"];
			foreach($character["equipment"] as $equipment)
			{
				$itemdata = $_SESSION["data"]["items"]["$equipment->itemid"];
				if($itemdata->type == "equipment" and $equipment->place == "ship") $equipped[] = "$equipment->itemid";
			}
			
			foreach($_SESSION["data"]["items"] as $itemid=>$item)
			{
				if($item->slot == "equipment") $equipmenttomb["$itemid"] = $item;
			}
			
			foreach($equipmenttomb as $itemid=>$item)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				$extraobject = new emptyclass;
				$extraobject->itemid = $itemdata->itemid;
				$extraobject->name = $itemdata->name;
				$extraobject->actualreload = 0;
				$extraobject->reload = $itemdata->reloadtime;
				$extraobject->ammo = $itemdata->ammotype;
				$extraobject->ammousage = $level;
				$extraobject->actualactive = 0;
				if(isset($equipped))
				{
					if(in_array($itemid, $equipped)) $extraobject->equipped = 1;
					else $extraobject->equipped = 0;
				}
				else $extraobject->equipped = 0;
				
				switch($item->itemid)
				{
					case "efi01":
						$extraobject->energyusage = 200 * $level;
						$extraobject->active = 2;
					break;
					case "pdu01":
						$extraobject->energyusage = 300 * $level;
						$extraobject->active = 5;
						$extraobject->effect = 300;
					break;
					case "edi01":
						$extraobject->energyusage = $level * 500;
						$extraobject->active = 1;
					break;
					case "abs01":
						$extraobject->energyusage = $level * 500;
						$extraobject->active = 1;
					break;
					case "sre01":
						$extraobject->active = 5;
						$extraobject->energyusage = $level * 500;
					break;
					case "clo01":
						$extraobject->active = -1;
						$extraobject->energyusage = $level * 100;
					break;
					case "bol01":
						$extraobject->active = 5;
						$extraobject->energyusage = $level * 200;
					break;
					case "mac01":
						$extraobject->active = 5;
						$extraobject->effect = 350;
						$extraobject->energyusage = $level * 300;
					break;
					case "ser01":
						$extraobject->active = 3;
						$extraobject->energyusage = $level * 150;
					break;
					case "mdl01":
						$extraobject->basicactive = 5;
						$extraobject->effect = 200;
						$extraobject->energyusage = $level * 300;
					break;
					case "rep01":
					case "rep02":
					case "rep03":
						$extraobject->effect = $itemdata->heal;
						$extraobject->energyusage = $level * 250;
						$extraobject->active = 1;
					break;
				}
				
				$_SESSION["game"]["$user"]["ship"]["equipment"]["$itemid"] = $extraobject;
			}
		}
	
	function squadronobject($user, $character, $ally)
	{
		foreach($character["squadrons"] as $squadronid=>$squadron)
		{
			$allyset = new emptyclass;
			$allyset->owner = $user;
			$allyset->id = $squadron->squadronid;
			$allyset->name = $squadron->squadronname;
			$_SESSION["gamedata"]["characters"]["$ally"]["squadrons"][] = $allyset;
			
			$squadronobject = new emptyclass;
			$squadronobject->itemid = $squadron->itemid;
			$squadronobject->squadronid = $squadron->squadronid;
			$squadronobject->squadronname = $squadron->squadronname;
			$squadronobject->corehull = $squadron->corehull;
			$squadronobject->actualcorehull = $squadron->corehull;
			$squadronobject->basicammostorage = $squadron->basicammostorage;
			$squadronobject->actualammostorage = $squadron->basicammostorage;
			$squadronobject->group = $squadron->group;
			$squadronobject->place = "space";
			$squadronobject->mfa1active = 0;
			if(isset($squadron->style)) $squadronobject->style = $squadron->style;
			
			$_SESSION["game"]["$user"]["squadrons"]["$squadronid"]["squadron"] = $squadronobject;
		}
	}
	
	function ammoobject($user, $character)
	{
		foreach($character["ammo"] as $ammo)
		{
			if($ammo->place == "ship" and $ammo->amount)
			{
				$ammodata = $_SESSION["data"]["items"]["$ammo->itemid"];
				$ammoobject = new emptyclass;
				$ammoobject->itemid = $ammo->itemid;
				$ammoobject->itemtype = $ammodata->itemtype;
				$ammoobject->level = $ammodata->level;
				$ammoobject->name = $ammodata->name;
				$ammoobject->amount = $ammo->amount;
				if($ammodata->itemtype != "specialammo")
				{
					$ammoobject->dmgmultiplicator = $ammodata->dmgmultiplicator;
					$ammoobject->energymultiplicator = $ammodata->energymultiplicator;
				}
				
				
				$_SESSION["game"]["$user"]["ammo"]["$ammo->itemid"] = $ammoobject;
			}
		}
	}
	
	function skillobject($user, $character)
	{
		foreach($character["skill"] as $skillid=>$skill)
		{
			$skilldata = $_SESSION["data"]["items"]["$skillid"];
			$skillobject = new emptyclass;
			$skillobject->itemid = $skillid;
			$skillobject->level = $skill->level;
			$skillobject->owner = $skilldata->owner;
			switch($skillid)
			{
				case "emfp":
					
				break;
				case "emfa1":
					$skillobject->reload = $skilldata->basicreload;
					$skillobject->actualreload = 0;
					$skillobject->effect = 20 + $skillobject->level * 0.3;
					$skillobject->active = $skilldata->basicactive;
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "emfa2":
					$skillobject->reload = $skilldata->basicreload - $skillobject->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive + $skillobject->level * $skilldata->activeinc;
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "pdmp":
					$skillobject->effect = 100 + $skillobject->level * 5;
					if($skillobject->level and $_SESSION["game"]["$user"]["ship"]["ship"][0]->company == "pdm") pdmp($user, $skillobject->effect);
				break;
				case "pdma1":
					$skillobject->reload = $skilldata->basicreload - $skillobject->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive;
					$skillobject->actualactive = 0;
					$skillobject->basiceffect = 1 + $skillobject->level * 0.5;
					$skillobject->actualeffect = 0;
					$skillobject->energyusage = $skill->level * 250;
					$skillobject->effect = 0;
				break;
				case "pdma2":
					$skillobject->reload = $skilldata->basicreload - $skillobject->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->basicactive = $skilldata->basicactive + $skillobject->level * $skilldata->activeinc;
					$skillobject->actualactive = 0;
					$skillobject->effect = 0;
					$skillobject->energyusage = $skill->level * 500;
					$skillobject->defender = "no";
				break;
				case "idfp":
					$skillobject->effect = 50 * $skill->level;
					if($skillobject->level and $_SESSION["game"]["$user"]["ship"]["ship"][0]->company == "idf") idfp($user, $skillobject->effect);
				break;
				case "idfa1":
					$skillobject->effect = 20 + $skill->level * 3;
					$skillobject->reload = $skilldata->basicreload;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive + $skill->level * $skilldata->activeinc;
					settype($skillobject->active, "integer");
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "idfa2":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive;
					$skillobject->actualactive = 0;
					$skillobject->effect = $skill->level;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "mfap":
					$skillobject->effect = $skill->level * 2;
				break;
				case "mfaa1":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					settype($skillobject->reload, "integer");
					$skillobject->actualreload = 0;
					$skillobject->basicactive = $skilldata->basicactive + $skill->level * $skilldata->activeinc;
					$skillobject->actualactive = 0;
					settype($skillobject->basicactive, "integer");
					$skillobject->effect = 20 + $skill->level * 3;
					$skillobject->energyusage = $skill->level * 300;
				break;
				case "mfaa2":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive + $skill->level * $skilldata->activeinc;
					$skillobject->actualactive = 0;
					$skillobject->effect = 100 + $skill->level * 20;
					$skillobject->energyusage = $skill->level * 200;
				break;
				case "gaap":
					$skillobject->effect = 100 * $skill->level;
				break;
				case "gaaa1":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive + $skill->level * $skilldata->activeinc;
					settype($skillobject->active, "integer");
					$skillobject->actualactive = 0;
					$skillobject->effect = 20 + $skill->level * 3;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "gaaa2":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->basicactive = $skill->level;
					$skillobject->active = 0;
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 500;
				break;
				case "crip":
					$skillobject->effect = $skill->level;
				break;
				case "cria1":
					$skillobject->effect = 20 + $skill->level * 3;
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->active = $skilldata->basicactive + $skill->level * $skilldata->activeinc;
					settype($skillobject->active, "integer");
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 250;
				break;
				case "cria2":
					$skillobject->reload = $skilldata->basicreload - $skill->level * $skilldata->reloadinc;
					$skillobject->actualreload = 0;
					$skillobject->basicactive = $skill->level;
					$skillobject->active = 0;
					$skillobject->actualactive = 0;
					$skillobject->energyusage = $skill->level * 500;
				break;
			}
			
			
			$_SESSION["game"]["$user"]["skill"]["$skillid"] = $skillobject;
		}
	}
	
		function pdmp($user, $effect)
		{
			foreach($_SESSION["game"]["$user"]["ship"]["shield"] as &$shield)
			{
				$shield->shieldenergy *= $effect/100;
				$shield->actualshield *= $effect/100;
			}
		}
		
		function idfp($user, $effect)
		{
			foreach($_SESSION["game"]["$user"]["ship"]["rocketlauncher"] as &$item) $item->accuracy += $effect;
		}
		
	function groupobject($user, $character)
	{
		foreach($character["groups"] as $groupid=>$group)
		{
			$groupobject = new emptyclass;
			$groupobject->groupid = $groupid;
			$groupobject->groupname = $group->groupname;
			if(property_exists($group, "style")) $groupobject->targetstyle = $group->style;
			else $groupobject->targetstyle = "auto";
			$_SESSION["game"]["$user"]["groups"]["$groupid"] = $groupobject;
		}
	}
?>