<?php
	include("connection.php");
	$_SESSION["from"] = "equipment_all.php";
	
	if(!isset($_SESSION["equipment_all"]["equiptype"])) $_SESSION["equipment_all"]["equiptype"] = 0;
	if(isset($_GET["type"])) $_SESSION["equipment_all"]["equiptype"] = $_GET["type"];
	
	if(!isset($_SESSION["equipment_all"]["level"])) $_SESSION["equipment_all"]["level"] = 0;
	if(isset($_GET["level"])) $_SESSION["equipment_all"]["level"] = $_GET["level"];
	
	if(!isset($_SESSION["equipment_all"]["place"])) $_SESSION["equipment_all"]["place"] = 0;
	if(isset($_GET["place"])) $_SESSION["equipment_all"]["place"] = $_GET["place"];
	
	if(!empty($_POST))
	{
		foreach($_POST as $name=>$ertek)
		{
			if($name == "amount")
			{
				$_SESSION["equipment"]["action"]["$name"] = $ertek;
			}
			else
			{
				$er = decode($name);
				foreach($er as $ne=>$val) $_SESSION["equipment"]["action"]["$ne"] = $val;
			}
		}
		if(1) 
		{
			if($_SESSION["equipment"]["action"]["action"] == "squadronrename" or $_SESSION["equipment"]["action"]["action"] == "squadronequip") header("location:squadronequip.php");
			else header("location:equipment_action.php");
		}
		exit;
	}
	
	include("hundescription.php");
?>

<HTML>
	<HEAD>
		<link rel="stylesheet" type="text/css" href="shell_style.css">
		<link rel="stylesheet" type="text/css" href="equipment_all.css">
		<link rel="stylesheet" type="text/css" href="hundescription.css">
		<TITLE>Összes felszerelés</TITLE>
	</HEAD>
<BODY>
	<DIV class='background'>
		<DIV class='title'>Összes felszerelés</DIV>
		<DIV class='szuro'>
		<FORM method='GET'>
			<DIV class='type'>
				Típus:
				<SELECT name='type' class='type'>
					<OPTION value='0' class='type' selected='selected'>Összes</OPTION>
					<?php
						$type["ship"] = "Hajó";
							$type["squadron"] = "Raj";
							$type["cannon"] = "Ágyú";
							$type["rocketlauncher"] = "Rakétakilövő";
							$type["rifle"] = "Gépágyú";
							$type["shield"] = "Pajzs";
							$type["hull"] = "Burkolat";
							$type["hangar"] = "Hangár";
							$type["equipment"] = "Felszerelés";
							$type["generator"] = "Generátor";
							$type["battery"] = "Akkumulátor";
							$type["extender"] = "Bővítő";
							$type["ammo"] = "Lőszer";
							$type["squadroncannon"] = "Raj fegyverzet";
							$type["squadronshield"] = "Raj pajzs";
							$type["squadronhull"] = "Raj burkolat";
						
							asort($type);
							
							foreach($type as $name=>$ertek)
							{
								$selected = "";
								if(!$_SESSION["equipment_all"]["equiptype"]) $selected = "";
								elseif($_SESSION["equipment_all"]["equiptype"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='type'>$ertek</OPTION>";
							}
					?>
				</SELECT>
			</DIV>
			<DIV class='level'>
				Szint:
				<SELECT name='level' class='level'>
					<OPTION value='0' class='level' selected='selected'>Összes</OPTION>
					<?php 
						$level[1] = 1;
						$level[2] = 2;
						$level[3] = 3;
						$level[4] = 4;
						$level[5] = 5;
						$level[6] = 6;
						$level[7] = 7;
						$level[8] = 8;
						$level[9] = 9;
						$level[10] = 10;
						
						foreach($level as $name=>$ertek)
						{
							$selected = "";
							if(!$_SESSION["equipment_all"]["level"]) $selected = "";
							elseif($_SESSION["equipment_all"]["level"] == $name) $selected = "selected='selected'";
							print "<OPTION value='$name' $selected class='level'>$ertek</OPTION>";
						}
					?>
				</SELECT>
			</DIV>
			<DIV class='place'>
				Hely:
				<SELECT class='place' name='place'>
					<OPTION value='0' selected='selected'>Összes</OPTION>
					<?php
						$plac["hangar"] = "Hangár";
						$plac["ship"] = "Hajó";
						foreach($_SESSION["character"]["squadrons"] as $squadronid=>$squadron)
						{
							$pla["$squadronid"] = $squadron->squadronname;
						}
						asort($pla);
						
						if(isset($pla)) $place = array_merge($plac, $pla);
						else $place = $plac;
						
						foreach($place as $name=>$ertek)
						{
							$selected = "";
							if(!$_SESSION["equipment_all"]["place"]) $selected = "";
							elseif($_SESSION["equipment_all"]["place"] == $name) $selected = "selected='selected'";
							print "<OPTION value='$name', $selected>$ertek</OPTION>";
						}
					?>
				</SELECT>
			</DIV>
			<DIV class='search'><INPUT type='submit' name='submit' value='Szűrés' class='search'></DIV>
		</FORM>
		</DIV>
		<DIV class='container'>
		<?php
			equipments();
		?>
		</DIV>
		<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
		<DIV class='back'><A href='equipment.php' class='back'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>

<?php
	function equipments()
	{
		$equipmenttomb = equipmentload();
		
		if(!$equipmenttomb)
		{
			print "
				<DIV class='item'>
					<DIV class='itemname'>Nincsenek tárgyak</DIV>

					<DIV class='itemdescription'>
						<DIV class='desctitle'>Leírás</DIV>

					</DIV>
				</DIV>
			";
			return;
		}
		
		$disp = 0;
		foreach($equipmenttomb as $place=>$items)
		{
			if($_SESSION["equipment_all"]["place"] and $_SESSION["equipment_all"]["place"] != $place) continue;
			
			foreach($items as $itemid=>$amount)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				if($_SESSION["equipment_all"]["equiptype"] and $_SESSION["equipment_all"]["equiptype"] != $itemdata->slot) continue;
				if($_SESSION["equipment_all"]["level"] and $_SESSION["equipment_all"]["level"] != $itemdata->level) continue;
				
				switch($place)
				{
					case "ship":
						$itemplace = "Hajó";
					break;
					case "hangar":
						$itemplace = "Hangár";
					break;
					default:
						$itemplace = $_SESSION["character"]["squadrons"]["$place"]->squadronname;
					break;
				}
				
				if($place == "ship")
				{
					switch($itemdata->type)
					{
						case "extender":
							$sellname = "action=extendersell&itemid=$itemid&place=$place";
						break;
						case "hangar":
							$sellname = "action=hangarsell&itemid=$itemid&place=$place";
						break;
						default:
							$sellname = "action=itemsell&itemid=$itemid&place=$place";
						break;
					}
				}
				else $sellname = "action=itemsell&itemid=$itemid&place=$place";
				
				$sell = "<INPUT type='submit' class='submit' name='$sellname' value='Eladás'>";
				
				
				if($place != "hangar")
				{
					switch($itemdata->type)
					{
						case "extender":
							$unequipname = "action=extenderunequip&itemid=$itemid&place=$place";
						break;
						case "hangar":
							$unequipname = "action=hangarunequip&itemid=$itemid&place=$place";
						break;
						default:
							$unequipname = "action=itemunequip&itemid=$itemid&place=$place";
						break;
					}
					
					$unequip = "<INPUT type='submit' class='submit' name='$unequipname', value='Leszerelés'>";
				}
				else $unequip = "";
				
				$numberset = "<INPUT type='number' class='amount' min='1' name='amount' max='$amount' required='required' value='$amount'>";
				
				if($place == "ship" and $itemdata->slot == "ship")
				{
					$sell = "";
					$unequip = "";
					$numberset = "";
				}
				if($place != "hangar" and $itemdata->slot == "squadron")
				{
					$sell = "";
					$unequip = "";
					$numberset = "";
				}
				
				$desc = hundescription($itemid);
				
				print "
					<DIV class='item'>
						<DIV class='itemname'>$itemdata->name (Mennyiség: $amount - Hely: $itemplace)</DIV>
						<FORM method='POST'>
							$numberset
							$sell
							$unequip
						</FORM>
						<DIV class='itemdescription'>
							<DIV class='desctitle'>Leírás</DIV>
							$desc
						</DIV>
					</DIV>
				";
			}
		}
	}
	
		function equipmentload()
		{
			foreach($_SESSION["character"]["equipment"] as $item)
			{
				if(!isset($equipmenttomb["$item->place"]["$item->itemid"])) $equipmenttomb["$item->place"]["$item->itemid"] = 1;
				else $equipmenttomb["$item->place"]["$item->itemid"] += 1;
			}
			foreach($_SESSION["character"]["ammo"] as $item)
			{
				$equipmenttomb["$item->place"]["$item->itemid"] = $item->amount;
			}
			if($equipmenttomb) return $equipmenttomb;
			else return 0;
		}
?>
