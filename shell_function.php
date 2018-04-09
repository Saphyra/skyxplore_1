<?php
	function decode($code)
	{
		$data = explode("&", $code);
		
		foreach($data as $elem)
		{
			$part = explode("=", $elem);
			
			$result["$part[0]"] = $part["1"];
		}
		
		return $result;
	}
	
	function equipmentupload($equipment, $charid)
	{
		if(!mysqli_query($_SESSION["conn"], "DELETE from equipment WHERE charid='$charid'")) die("Felszerelés törlése sikertelen");
		
		do
		{
			for($szam = 0; $szam < 250; $szam++)
			{
				if($equipment) $upload[] = array_shift($equipment);
				else break;
			}
			
			$supload = serialize($upload);
			if(!mysqli_query($_SESSION["conn"], "INSERT INTO equipment (charid, equipment) VALUES ('$charid', '$supload')")) die("Felszerelés feltöltése sikertelen");
			unset($upload);
		}
		while($equipment);
	}
	
	function equipmentdownload($charid)
	{
		if(!$equipmentleker = mysqli_query($_SESSION["conn"], "SELECT * FROM equipment WHERE charid='$charid'")) die("Felszerelés letöltése sikertelen");
		
		while($equipmenttomb = mysqli_fetch_assoc($equipmentleker))
		{
			$uns = unserialize($equipmenttomb["equipment"]);
			
			foreach($uns as $item)
			{
				$equipment[] = $item;
			}
			unset($uns);
			
		}
		
		return $equipment;
	}
	
	
	function squadronplacecount($equipment)
	{
		$place = 0;
		foreach($equipment as $item)
		{
			$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
			if($item->equipped and $itemdata->slot == "hangar") $place += $itemdata->squadronplace;
		}
		return $place;
	}

	function hullcount($shipid, $equipment, $in)
	{
		$hull = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "hullenergy"))
					{
						$hull += $_SESSION["data"]["items"]["$ertek->itemid"]->hullenergy;
					}
				}
			}
		}
		
		return $hull;
	}

	function shieldcount($shipid, $equipment, $in)
	{
		$shield = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "shieldenergy"))
					{
						$shield += $_SESSION["data"]["items"]["$ertek->itemid"]->shieldenergy;
					}
				}
			}
		}
		
		return $shield;
	}
	
	function shieldrechargecount($shipid, $equipment, $in)
	{
		$recharge = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "recharge"))
					{
						$recharge += $_SESSION["data"]["items"]["$ertek->itemid"]->recharge;
					}
				}
			}
		}
		
		return $recharge;
	}
	
	function energycount($shipid, $equipment, $in)
	{
		$energy = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "capacity"))
					{
						$energy += $_SESSION["data"]["items"]["$ertek->itemid"]->capacity;
					}
				}
			}
		}
		
		return $energy;
	}
	
	function batteryrechargecount($shipid, $equipment, $in)
	{
		$batteryrecharge = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "maxrecharge"))
					{
						$batteryrecharge += $_SESSION["data"]["items"]["$ertek->itemid"]->maxrecharge;
					}
				}
			}
		}
		
		return $batteryrecharge;
	}
	
	function energyregencount($shipid, $equipment, $in)
	{
		$energyregen = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "energyregen"))
					{
						$energyregen += $_SESSION["data"]["items"]["$ertek->itemid"]->energyregen;
					}
				}
			}
		}
		
		return $energyregen;
	}
	
	function energyusagecount($shipid, $equipment, $in)
	{
		$energyusage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "energyusage"))
					{
						$energyusage += $_SESSION["data"]["items"]["$ertek->itemid"]->energyusage;
					}
				}
			}
		}
		
		return $energyusage;
	}
	
	function hulldamagecount($shipid, $equipment, $in)
	{
		$hulldamage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "hulldamage"))
					{
						$hulldamage += $_SESSION["data"]["items"]["$ertek->itemid"]->hulldamage;
					}
				}
			}
		}
		
		return $hulldamage;
	}
	
	function shielddamagecount($shipid, $equipment, $in)
	{
		$shielddamage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "shielddamage"))
					{
						$shielddamage += $_SESSION["data"]["items"]["$ertek->itemid"]->shielddamage;
					}
				}
			}
		}
		
		return $shielddamage;
	}
	
	function squadrondamagecount($shipid, $equipment, $in)
	{
		$squadrondamage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "squadrondamage"))
					{
						$squadrondamage += $_SESSION["data"]["items"]["$ertek->itemid"]->squadrondamage;
					}
				}
			}
		}
		
		return $squadrondamage;
	}
	
	function rockethulldamagecount($shipid, $equipment, $in)
	{
		$rockethulldamage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "rockethulldamage"))
					{
						$rockethulldamage += $_SESSION["data"]["items"]["$ertek->itemid"]->rockethulldamage;
					}
				}
			}
		}
		
		return $rockethulldamage;
	}
	
	function rocketshielddamagecount($shipid, $equipment, $in)
	{
		$rocketshielddamage = 0;
		
		foreach($equipment as $ertek)
		{
			if(property_exists($ertek, "place"))
			{
				if($ertek->place == "$in")
				{
					if(property_exists($_SESSION["data"]["items"]["$ertek->itemid"], "rocketshielddamage"))
					{
						$rocketshielddamage += $_SESSION["data"]["items"]["$ertek->itemid"]->rocketshielddamage;
					}
				}
			}
		}
		return $rocketshielddamage;
	}
	
	function partscorecount($equipment, $id)
	{
		$score = 0;
		{
			foreach($equipment as $item)
			{
				if(property_exists($item, "place") and property_exists($item, "equipped"))
				{
					if($item->place == "$id")
					{
						$score += $_SESSION["data"]["items"]["$item->itemid"]->score;
					}
				}
			}
		}
		
		return $score;
	}
	
	function scorecount($equip)
	{
		$score = 0;
		foreach($equip as $name=>$ertek)
		{
			if(property_exists($ertek, "equipped"))
			{
				if($ertek->equipped)
				{
					$score += $_SESSION["data"]["items"]["$ertek->itemid"]->score;
				}
			}
			
		}
		return $score;
	}

	function kiir($tomb)
	{
		print "<OL>";
			write($tomb);
		print "</OL>";
	}
	function write($tomb)
	{
		print "<LI>" . gettype($tomb) . "</LI>";
		print "<OL>";
		if(gettype($tomb) == "array")
		{
			
			foreach($tomb as $name=>$ertek)
			{
				print $name;
				write($ertek);
			}
		}
		elseif(gettype($tomb) == "object")
		{
			print "<OL>";
			$vars = get_object_vars($tomb);
			foreach($vars as $name=>$ertek)
			{
				print "<LI>$name: $ertek<BR></LI>";
			}
			print "</OL>";
		}
		elseif(gettype($tomb) == "string" or gettype($tomb) == "integer" or gettype($tomb) == "double") print "<LI>$tomb</LI>";
		print "</OL>";
	}
	
	
	
	function objectsearch($kerestomb, $miben)
	{
		foreach($miben as $item=>$tul)
		{
			$matches = 0;
			foreach($kerestomb as $name=>$ertek)
			{
				if(!property_exists($miben["$item"], $name))
				{
					break;
				}
				elseif($miben["$item"]->$name != $ertek)
				{
					break;
				}
				else
				{
					$matches++;
				}
			}
			if($matches == count($kerestomb)) $result[] = $item;
		}
		if(isset($result))
		{
			if(count($result) == 1) $result = $result[0];
			return $result;
		}
		else return 0;
	}
	
	function idgenerate($type, $idtomb, $num)
	{
		do
		{
			$id = "$type";
			for($szam = 0; $szam < $num; $szam++)
			{
				$id = $id . rand(0, 9);
			}
		}
		while(in_array($id, $idtomb));
		
		return $id;
	}
	
	function objectkiir($object)
	{
		$props = get_object_vars($object);
		foreach($props as $name=>$ertek)
		{
			print $name . ": " . $ertek . "<BR>";
		}
	}
	
	function save()
	{
		$charid = $_SESSION["character"]["charid"];
		$credit = $_SESSION["character"]["credit"];
		$diamond = $_SESSION["character"]["diamond"];
		$company = $_SESSION["character"]["company"];
		$level = $_SESSION["character"]["level"];
		$ship = serialize($_SESSION["character"]["ship"]);
		$ammo = serialize($_SESSION["character"]["ammo"]);
		$squadrons = serialize($_SESSION["character"]["squadrons"]);
		$construction = serialize($_SESSION["character"]["construction"]);
		$skill = serialize($_SESSION["character"]["skill"]);
		$groups = serialize($_SESSION["character"]["groups"]);
		
		if(!mysqli_query($_SESSION["conn"], "UPDATE characters SET credit='$credit', diamond='$diamond', company='$company', level='$level', ship='$ship', ammo='$ammo', squadrons='$squadrons', construction='$construction', skill='$skill', groups='$groups' WHERE charid='$charid'")) die("Sikertelen mentés");
		
		equipmentupload($_SESSION["character"]["equipment"], $charid);
	}
?>