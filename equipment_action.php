<?php
	include("connection.php");
	
	if(0)
	{
		kiir($_SESSION["equipment"]);
		exit;
	}
	
	if(isset($_SESSION["equipment"]["action"]))
	{
		
		action();
	}
	
	if(1)unset($_SESSION["equipment"]);
	$back = $_SESSION["from"];
	save();
	if(1) header("location:$back");
	
	function action()
	{
		switch($_SESSION["equipment"]["action"]["action"])
		{
			case "ammosell":
				ammosell();
			break;
			case "ammounequip":
				ammounequip();
			break;
			case "ammoequip":
				ammoequip();
			break;
			case "itemsell":
				itemsell();
			break;
			case "itemunequip":
				itemunequip();
			break;
			case "itemequip":
				amountcount();
				itemequip();
			break;
			case "specialequip":
				$_SESSION["equipment"]["action"]["amount"] = 1;
				itemequip();
			break;
			case "extendersell":
				extendersell();
			break;
			case "extenderunequip":
				extenderunequip();
			break;
			case "extenderequip":
				$_SESSION["equipment"]["action"]["amount"] = 1;
				extenderequip();
			break;
			case "squadronunequipall":
				squadronunequipall();
			break;
			case "unequipall":
				unequipall();
			break;
			case "squadronunequipallsquad":
				squadronunequipallsquad();
			break;
			case "hangarsell":
				hangarsell();
			break;
			case "hangarunequip":
				hangarunequip();
			break;
			case "squadronunequip":
				squadronunequip($_SESSION["equipment"]["action"]["squadronid"]);
			break;
			case "shipequip":
				$_SESSION["equipment"]["action"]["amount"] = 1;
				shipequip();
			break;
		}
	}
		
		function ammosell()
		{
			foreach($_SESSION["character"]["ammo"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == $_SESSION["equipment"]["action"]["place"])
				{
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					$_SESSION["character"]["credit"] += $_SESSION["equipment"]["action"]["amount"] * $itemdata->sellprice;
					$item->amount -= $_SESSION["equipment"]["action"]["amount"];
					break;
				}
			}
		}
		
		function ammounequip()
		{
			$success = 0;
			foreach($_SESSION["character"]["ammo"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$item->amount -= $_SESSION["equipment"]["action"]["amount"];
				}
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "hangar")
				{
					$item->amount += $_SESSION["equipment"]["action"]["amount"];
					$success = 1;
				}
			}
			if(!$success)
			{
				$_SESSION["character"]["ammo"][] = new ammo($_SESSION["equipment"]["action"]["itemid"], "hangar", 0, $_SESSION["equipment"]["action"]["amount"]);
			}
		}
		
		function ammoequip()
		{
			$success = 0;
			$ammoeq = 0;
			foreach($_SESSION["character"]["ammo"] as $ammo)
			{
				if($ammo->place == "ship") $ammoeq += $ammo->amount;
			}
			if($_SESSION["equipment"]["action"]["amount"] > $_SESSION["character"]["ship"]->basicammostorage - $ammoeq) $_SESSION["equipment"]["action"]["amount"] = $_SESSION["character"]["ship"]->basicammostorage - $ammoeq;
			foreach($_SESSION["character"]["ammo"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "hangar")
				{
					$item->amount -= $_SESSION["equipment"]["action"]["amount"];
				}
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$item->amount += $_SESSION["equipment"]["action"]["amount"];
					$success = 1;
				}
			}
			if(!$success)
			{
				$_SESSION["character"]["ammo"][] = new ammo($_SESSION["equipment"]["action"]["itemid"], "ship", 1, $_SESSION["equipment"]["action"]["amount"]);
			}
		}
		
		function itemsell()
		{
			$run = 0;
			foreach($_SESSION["character"]["equipment"] as $index=>$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == $_SESSION["equipment"]["action"]["place"])
				{
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					$_SESSION["character"]["credit"] += $itemdata->sellprice;
					unset($_SESSION["character"]["equipment"][$index]);
					
					$run++;
				}
				if($run >= $_SESSION["equipment"]["action"]["amount"]) return;
			}
		}
		
		function itemunequip()
		{
			$run = 0;
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == $_SESSION["equipment"]["action"]["place"])
				{
					$item->place = "hangar";
					$item->equipped = 0;
					
					$run++;
				}
				if($run >= $_SESSION["equipment"]["action"]["amount"]) return;
			}
		}
		
		function itemequip()
		{
			$run = 0;
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "hangar")
				{
					$item->place = $_SESSION["equipment"]["action"]["place"];
					$item->equipped = 1;
					$run++;
				}
				if($run >= $_SESSION["equipment"]["action"]["amount"]) return;
			}
		}
		
			function amountcount()
			{
				if($_SESSION["equipment"]["action"]["place"] == "ship")
				{
					$to = $_SESSION["character"]["ship"];
				}
				else
				{
					$id = $_SESSION["equipment"]["action"]["place"];
					$to = $_SESSION["character"]["squadrons"]["$id"];
				}
				
				$itemid = $_SESSION["equipment"]["action"]["itemid"];
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				switch($itemdata->slot)
				{
					case "cannon":
						$slotname = "cannonslot";
					break;
					case "rocketlauncher":
						$slotname = "rocletslot";
					break;
					case "rifle":
						$slotname = "rifleslot";
					break;
					case "hangar":
						$slotname = "hangarslot";
					break;
					case "shield":
						$slotname = "shieldslot";
					break;
					case "hull":
						$slotname = "hullslot";
					break;
					case "generator":
						$slotname = "generatorslot";
					break;
					case "battery":
						$slotname = "batteryslot";
					break;
					case "squadroncannon":
						$slotname = "weaponslot";
					case "squadronshield":
						$slotname = "shieldslot";
					case "squadronhull":
						$slotname = "hullslot";
					break;
					case "equipment":
						$slotname = "equipmentslot";
					break;
					case "extender":
						$slotname = "extenderslot";
					break;
				}
				
				$maxnum = $to->$slotname;
				$num = 0;
				foreach($_SESSION["character"]["equipment"] as $item)
				{
					if($item->place != $_SESSION["equipment"]["action"]["place"]) continue;
					$item2data = $_SESSION["data"]["items"]["$item->itemid"];
					if($item2data->slot != $itemdata->slot) continue;
					
					$num += 1;
				}
				print "$num, $maxnum, " . $_SESSION["equipment"]["action"]["amount"];
				if($_SESSION["equipment"]["action"]["amount"] > $maxnum - $num) $_SESSION["equipment"]["action"]["amount"] = $maxnum - $num;
				print $_SESSION["equipment"]["action"]["amount"];
			}
			
		function extendersell()
		{
			foreach($_SESSION["character"]["equipment"] as $index=>$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					$_SESSION["character"]["credit"] += $itemdata->sellprice;
					
					extenderset($itemdata);
					unset($_SESSION["character"]["equipment"][$index]);
					break;
				}
			}
		}
		
			function extenderset($itemdata)
			{
				$effect = $itemdata->effect;
				$ename = $itemdata->ename;
				$ship = $_SESSION["character"]["ship"];
				$shipdata = $_SESSION["data"]["items"]["$ship->itemid"];
				$_SESSION["character"]["ship"]->$effect = $shipdata->$effect;
				
				$count = 0;
				foreach($_SESSION["character"]["equipment"] as $item2)
				{
					$item2data = $_SESSION["data"]["items"]["$item2->itemid"];
					if($item2->place == "ship")
					{
						$itemdata2 = $_SESSION["data"]["items"]["$item2->itemid"];
						if($item2data->slot == $ename) $count += 1;
					}
				}
				for($count; $count > $_SESSION["character"]["ship"]->$effect; $count--)
				{
					foreach($_SESSION["character"]["equipment"] as &$item3)
					{
						$item3data = $_SESSION["data"]["items"]["$item3->itemid"];
						if($item3->place == "ship" and $item3data->slot == $itemdata->ename)
						{
							$item3->place = "hangar";
							$item3->equipped = 0;
							break;
						}
					}
				}
			}
		
		function extenderunequip()
		{
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$item->place = "hangar";
					$item->equipped = 0;
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					extenderset($itemdata);
					break;
				}
			}
		}
		
		function extenderequip()
		{
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "hangar")
				{
					$item->place = "ship";
					$item->equipped = 1;
					
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					$effect = $itemdata->effect;
					$_SESSION["character"]["ship"]->$effect += $itemdata->slotextend;
					break;
				}
			}
		}
		
		function squadronunequipall()
		{
			switch($_SESSION["equipment"]["action"]["slot"])
			{
				case "all":
					foreach($_SESSION["character"]["equipment"] as &$item)
					{
						if($item->place == $_SESSION["equipment"]["action"]["squadronid"])
						{
							$item->place = "hangar";
							$item->equipped = 0;
						}
					}
				break;
				default:
					foreach($_SESSION["character"]["equipment"] as &$item)
					{
						$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
						if($item->place == $_SESSION["equipment"]["action"]["squadronid"] and $itemdata->slot == $_SESSION["equipment"]["action"]["slot"])
						{
							$item->place = "hangar";
							$item->equipped = 0;
						}
					}
				break;
			}
		}
		
		function unequipall()
		{
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
				if($item->place == "ship" and $itemdata->slot != "ship")
				{
					if($_SESSION["equipment"]["action"]["slot"] == "all" or $itemdata->slot == $_SESSION["equipment"]["action"]["slot"])
					{
						if($itemdata->slot == "squadron") continue;
						$item->place = "hangar";
						$item->equipped = 0;
						
						if($itemdata->slot == "extender")
						{
							extenderset($itemdata);
						}
						elseif($itemdata->slot == "hangar")
						{
							squadroncount();
						}
					}
				}
			}
			if($_SESSION["equipment"]["action"]["slot"] == "all" or $_SESSION["equipment"]["action"]["slot"] == "ammo")
			foreach($_SESSION["character"]["ammo"] as &$item2)
			{
				if($item2->place == "ship")
				{
					$amount = $item2->amount;
					$item2->amount = 0;
					
					$success = 0;
					foreach($_SESSION["character"]["ammo"] as $item3)
					{
						if($item3->itemid == $item2->itemid and $item3->place == "hangar")
						{
							$item3->amount += $amount;
							$success = 1;
							break;
						}
					}
					if(!$success)
					{
						$_SESSION["character"]["ammo"][] = new ammo($item2->itemid, "hangar", 0, $amount);
					}
				}
			}
		}
		
			function squadroncount()
			{
				$squadronnum = count($_SESSION["character"]["squadrons"]);
				$squadronplace = 0;
				foreach($_SESSION["character"]["equipment"] as $item)
				{
					if($item->place == "ship" and $_SESSION["data"]["items"]["$item->itemid"]->type == "hangar")
					{
						$squadronplace += $_SESSION["data"]["items"]["$item->itemid"]->squadronplace;
					}
				}
				
				for($squadronnum; $squadronnum > $squadronplace; $squadronnum--)
				{
					foreach($_SESSION["character"]["squadrons"] as $squadronid=>$squadron)
					{
						squadronunequip($squadronid);
						break;
					}
				}
			}
		
				function squadronunequip($squadronid)
				{
					$squadron = $_SESSION["character"]["squadrons"]["$squadronid"];
					foreach($_SESSION["character"]["equipment"] as &$item)
					{
						if($item->place == $squadronid)
						{
							$item->place = "hangar";
							$item->equipped = 0;
						}
					}
					
					if($squadron->group)
					{
						$groupmembers = unserialize($_SESSION["character"]["groups"]["$squadron->group"]->members);
						foreach($groupmembers as $index=>$id)
						{
							if($squadron->squadronid == $id) unset($groupmembers[$index]);
						}
						
						if(count($groupmembers)) $_SESSION["character"]["groups"]["$squadron->group"]->members = serialize($groupmembers);
						else $_SESSION["character"]["groups"]["$squadron->group"]->members = 0;
						$_SESSION["character"]["groups"]["$squadron->group"]->membernum -= 1;
					}
					
					foreach($_SESSION["character"]["equipment"] as &$item2)
					{
						if($item2->itemid == $squadron->itemid and $item2->place == "ship")
						{
							$item2->place = "hangar";
							$item2->equipped = 0;
							break;
						}
					}
					
					if(!$squadronleker = mysqli_query($_SESSION["conn"], "SELECT * FROM systemdata WHERE name='squadronnames' OR name='squadronids'")) die("Squadronnevekidk lekérése sikertelen");
					while($squadrontomb = mysqli_fetch_assoc($squadronleker))
					{
						switch($squadrontomb["name"])
						{
							case "squadronids":
								$squadronids = unserialize($squadrontomb["value"]);
							break;
							case "squadronnames":
								$squadronnames = unserialize($squadrontomb["value"]);
							break;
						}
					}
					
					foreach($squadronids as $index=>$id)
					{
						if($id == $squadronid)
						{
							unset($squadronids[$index]);
							break;
						}
					}
					foreach($squadronnames as $index=>$name)
					{
						if($name == $squadron->squadronname)
						{
							unset($squadronnames[$index]);
							break;
						}
					}
					
					$squadronids = serialize($squadronids);
					$squadronnames = serialize($squadronnames);
					if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronnames' WHERE name='squadronnames'")) die("Squadronnames frissítése sikertelen");
					if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronids' WHERE name='squadronids'")) die("Squadronidk frissítése sikertelen");
					
					unset($_SESSION["character"]["squadrons"]["$squadronid"]);
				}
		
		function squadronunequipallsquad()
		{
			foreach($_SESSION["character"]["squadrons"] as $squadronid=>$squadron)
			{
				squadronunequip($squadronid);
			}
		}
		
		function hangarsell()
		{
			$run = 0;
			foreach($_SESSION["character"]["equipment"] as $index=>$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
					$_SESSION["character"]["credit"] += $itemdata->sellprice;
					unset($_SESSION["character"]["equipment"][$index]);
					
					squadroncount();
					
					$run++;
				}
				if($run >= $_SESSION["equipment"]["action"]["amount"]) return;
			}
		}
		
		function hangarunequip()
		{
			$run = 0;
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "ship")
				{
					$item->place = "hangar";
					$item->equipped = 0;
					
					squadroncount();
					
					$run++;
				}
			if($run >= $_SESSION["equipment"]["action"]["amount"]) return;
			}
		}
		
		function shipequip()
		{
			foreach($_SESSION["character"]["squadrons"] as $squadronid=>$squadron)
			{
				squadronunequip($squadronid);
			}
			foreach($_SESSION["character"]["equipment"] as &$item)
			{
				$item->place = "hangar";
				$item->equipped = 0;
			}
			foreach($_SESSION["character"]["ammo"] as &$ammo)
			{
				if($ammo->place == "ship")
				{
					foreach($_SESSION["character"]["ammo"] as &$ammo2)
					{
						$success = 0;
						if($ammo2->itemid == $ammo->itemid and $ammo2->place == "hangar")
						{
							$success = 1;
							$ammo2->amount += $ammo->amount;
						}
					}
					if(!$success)
					{
						$_SESSION["character"]["ammo"][] = new ammo($ammo->itemid, "hangar", 0, $ammo->amount);
					}
					$ammo->amount = 0;
				}
			}
			foreach($_SESSION["character"]["equipment"] as $item2)
			{
				if($item2->place == "hangar" and $item2->itemid == $_SESSION["equipment"]["action"]["itemid"])
				{
					$itemdata = $_SESSION["data"]["items"]["$item2->itemid"];
					
					$item2->place = "ship";
					$item2->equipped = 1;
					
					$_SESSION["character"]["level"] = $itemdata->level;
					$_SESSION["character"]["company"] = $itemdata->itemtype;
					$_SESSION["character"]["ship"] = $itemdata;
				}
			}
		}
?>

