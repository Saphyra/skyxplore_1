<?php
	include("connection.php");
	
	$_SESSION["from"] = "equipment.php";
	
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
	
	
	if(!isset($_SESSION["hangar"]["shipchoose"])) $_SESSION["hangar"]["shipchoose"] = "ship";
	if(isset($_GET["shipchoose"])) $_SESSION["hangar"]["shipchoose"] = $_GET["shipchoose"];
	
	foreach($_SESSION["character"]["squadrons"] as $firstsquadronid=>$ertek) break;
	if(!isset($firstsquadronid)) $firstsquadronid = 0;
	if(!isset($_SESSION["hangar"]["squadronidchoose"]) or !$_SESSION["hangar"]["squadronidchoose"]) $_SESSION["hangar"]["squadronidchoose"] = $firstsquadronid;
	
	$found = 0;
	foreach($_SESSION["character"]["squadrons"] as $index=>$squadron)
	{
		if($squadron->squadronid == $_SESSION["hangar"]["squadronidchoose"])
		{
			$found = 1;
			break;
		}
	}
	if(!$found) $_SESSION["hangar"]["squadronidchoose"] = $firstsquadronid;
	if(isset($_GET["squadronidchoose"])) $_SESSION["hangar"]["squadronidchoose"] = $_GET["squadronidchoose"];
	
	if(!isset($_SESSION["hangar"]["equiptype"])) $_SESSION["hangar"]["equiptype"] = 0;
	if(isset($_GET["type"])) $_SESSION["hangar"]["equiptype"] = $_GET["type"];
	
	if(!isset($_SESSION["hangar"]["equiplevel"])) $_SESSION["hangar"]["equiplevel"] = 0;
	if(isset($_GET["level"])) $_SESSION["hangar"]["equiplevel"] = $_GET["level"];
	
	include("hundescription.php");
	
	$equipmenttomb = equipmentload($_SESSION["character"]["equipment"]);
	$ammotomb = ammoload($_SESSION["character"]["ammo"]);
?>

<HTML>
	<HEAD>
		<TITLE>Felszerelés</TITLE>
		<link rel="stylesheet" type="text/css" href="shell_style.css">
		<link rel="stylesheet" type="text/css" href="equipment.css">
		<link rel="stylesheet" type="text/css" href="hundescription.css">

		<STYLE>
			div.hint
			{
				position: static;
				font-size: 20px;
				text-align: center;
				border-top-style: solid;
				border-top-width: 1px;
				margin-top: 20px;
			}
		</STYLE>
	</HEAD>
<BODY>
	<DIV class='background'>
		<DIV class='title'>
			Felszerelés
			<?php
				if($_SESSION["character"]["company"] == "emf" and $_SESSION["character"]["skill"]["emfp"]->level) print "
					<DIV class='groupset'>
						<A class='groupset' href='groups.php'>Csapatok</A>
					</DIV>
				";
			?>
		</DIV>
		
		<DIV class='ship'>
			<DIV class='classchoose'>
				<DIV class='shipchoose'><A class='shipchoose' href='equipment.php?shipchoose=ship' <?php if($_SESSION["hangar"]["shipchoose"] == "ship") print "style='color: red;'"; ?>>Hajó</A></DIV>
				<DIV class='squadronchoose'><A class='squadronchoose' href='equipment.php?shipchoose=squadron' <?php if($_SESSION["hangar"]["shipchoose"] == "squadron") print "style='color: red;'"; ?>>Raj</A></DIV>
			</DIV>
			<?php
				squadronchoose();
				shipequipment($equipmenttomb, $_SESSION["character"]["ship"], $ammotomb["ship"], $_SESSION["character"]["squadrons"]);
				
				$squadronid = $_SESSION["hangar"]["squadronidchoose"];
				if($squadronid)
				{
					if(isset($equipmenttomb["$squadronid"]))
					{
						squadronequipment($_SESSION["hangar"]["squadronidchoose"], $_SESSION["character"]["squadrons"], $equipmenttomb["$squadronid"]);
					}
					else squadronequipment($_SESSION["hangar"]["squadronidchoose"], $_SESSION["character"]["squadrons"]);
				}
				
			?>
			
		</DIV>
		<DIV class='equipment'>
			<P class='equipment'>Felszerelések</P>
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
								if(!$_SESSION["hangar"]["equiptype"]) $selected = "";
								elseif($_SESSION["hangar"]["equiptype"] == $name) $selected = "selected='selected'";
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
								if($_SESSION["hangar"]["equiplevel"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='level'>$ertek</OPTION>";
							}
						?>
					</SELECT>
				</DIV>
				<DIV class='search'><INPUT type='submit' name='submit' value='Szűrés' class='search'></DIV>
			</FORM>
			</DIV>
			<?php
				if(isset($equipmenttomb["hangar"]) or isset($ammotomb["hangar"]))
				{
					if(isset($ammotomb["hangar"]))
					{
						hangarammo($ammotomb, $_SESSION["character"]["ship"]->basicammostorage);
					}
					if(isset($equipmenttomb["hangar"]))
					{
						hangarequipment($equipmenttomb, $_SESSION["character"]["ship"], $_SESSION["character"]["squadrons"]);
					}
				}
				else print "<H2>Nincs tárgy a hangárban.</H2>";
			?>
		</DIV>
		<DIV class='description'>
			<DIV class='desctitle'>Leírás:</DIV>
		</DIV>
		<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
		<DIV class='equall'><A href='equipment_all.php' class='equall'>Összes tárgy</A></DIV>
		<DIV class='back'><A href='hangar.php' class='back'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>

<?php
	unset($_SESSION["hangar"]["shipequip"]);
	unset($_SESSION["hangar"]["equipment"]);
	unset($_SESSION["hangar"]["equipable"]);
?>

<?php
	function equipmentload($equipmenttomb)
	{
		foreach($equipmenttomb as $item)
		{
			$itemdata = $_SESSION["data"]["items"]["$item->itemid"];
			if(!isset($equipmentsorted["$item->place"]["$itemdata->slot"]["$item->itemid"])) $equipmentsorted["$item->place"]["$itemdata->slot"]["$item->itemid"] = 1;
			else
			{
				$equipmentsorted["$item->place"]["$itemdata->slot"]["$item->itemid"] += 1;
			}
		}
		
		return $equipmentsorted;
	}
	
	function ammoload($ammotomb)
	{
		foreach($ammotomb as $ammo)
		{
			$ammodata = $_SESSION["data"]["items"]["$ammo->itemid"];
			
			$ammosorted["$ammo->place"]["$ammo->itemid"] = $ammo->amount;
		}
		
		return $ammosorted;
	}
	
	function squadronchoose()
	{
		if($_SESSION["hangar"]["shipchoose"] != "squadron") return;
		print "
			<DIV class='squadronidchoose'>
				<FORM method='GET'>
					<SELECT name='squadronidchoose' class='squadronidchoose'>
		";
		
		foreach($_SESSION["character"]["squadrons"] as $squadron)
			{
				$squadronid = $squadron->squadronid;
				$squadronname = $squadron->squadronname;
				
				$selected = "";
				if(isset($_SESSION["hangar"]["squadronidchoose"]))
				{
					if($_SESSION["hangar"]["squadronidchoose"] == $squadronid) $selected = "selected='selected'";
				}
				print "<OPTION value='$squadronid' $selected>$squadronname</OPTION>";
			}
		print "
					</SELECT>
						<INPUT class='squadronidchoose' type='submit' name='submit' value='Kiválaszt'>
					</FORM>
				</DIV>
		";
	}
	
	function shipequipment($equipmenttomb, $ship, $ammo, $squadrons)
	{
		if($_SESSION["hangar"]["shipchoose"] != "ship") return;
		
		print "
			<DIV style='margin-top: 10px;'>
			<FORM method='POST'>
				<INPUT type='submit' class='submit' name='action=unequipall&slot=all' value='Hajó kiürítése' style='font-size: 20px;'>
			</FORM>
			</DIV>
		";
		
		shipammo($ship, $ammo);
		
		$slots = array("cannon", "rocketlauncher", "rifle", "hull", "shield", "generator", "battery", "equipment", "extender", "hangar");
		
		foreach($slots as $slot)
		{
			$type = 1;
			$sell = "itemsell";
			$unequip = "itemunequip";
			switch($slot)
			{
				case "cannon":
					$name = "Ágyú";
					$num = $ship->cannonslot;
					$maxlevel = $ship->maxcannonlevel;
				break;
				case "rocketlauncher":
					$name = "Rakétakilövő";
					$num = $ship->rocketslot;
					$maxlevel = $ship->maxrocketlevel;
				break;
				case "rifle":
					$name = "Gépágyú";
					$num = $ship->rifleslot;
					$maxlevel = $ship->maxriflelevel;
				break;
				case "shield":
					$name = "Pajzs";
					$num = $ship->shieldslot;
					$maxlevel = $ship->maxshieldlevel;
				break;
				case "hull":
					$name = "Burkolat";
					$num = $ship->hullslot;
					$maxlevel = $ship->maxhulllevel;
				break;
				case "generator":
					$name = "Generátor";
					$num = $ship->generatorslot;
					$maxlevel = $ship->maxgeneratorlevel;
				break;
				case "battery":
					$name = "Akkumulátor";
					$num = $ship->batteryslot;
					$maxlevel = $ship->maxbatterylevel;
				break;
				case "equipment":
					$name = "Felszerelés";
					$num = $ship->equipmentslot;
					$maxlevel = 0;
				break;
				case "extender":
					$name = "Bővítő";
					$num = $ship->extenderslot;
					$maxlevel = $ship->maxextenderlevel;
					$sell = "extendersell";
					$unequip = "extenderunequip";
				break;
				case "hangar":
					$name = "Hangár";
					$num = $ship->hangarslot;
					$maxlevel = $ship->maxhangarlevel;
					$sell = "hangarsell";
					$unequip = "hangarunequip";
				break;
				default:
					$type = 0;
					print "$slot<BR>";
				break;
			}
			if(!$type) continue;
			if(!$num) continue;
			if($maxlevel) $maxlevel = " - Szint: " . $maxlevel;
			else $maxlevel = "";
			
			print "
				<DIV class='eq'>
					<P class='eq'>$name (Hely: $num$maxlevel)</P>
					<FORM method='POST'>
						<INPUT type='submit' class='submit' name='action=unequipall&slot=$slot' value='Mindet leszerel'>
					</FORM>
			";
			
			if(isset($equipmenttomb["ship"]["$slot"]))
			{
				$slotnum = $num;
				foreach($equipmenttomb["ship"]["$slot"] as $itemid=>$amount)
				{
					$slotnum -= $amount;
					$itemdata = $_SESSION["data"]["items"]["$itemid"];
					$desc = hundescription($itemid);
					
					print"
						<DIV class='item'>
								<DIV class='itemname'>$itemdata->name ($amount)</DIV>
								<DIV class='ammoaction'>
									<FORM method='POST'>
										<INPUT class='ammo' type='number' name='amount' min='1' max='$amount' required='required' value='$amount'>
										<INPUT class='ammosubmit' type='submit' name='action=$sell&itemid=$itemid&place=ship' value='Eladás'>
										<INPUT class='ammosubmit' type='submit' name='action=$unequip&itemid=$itemid&place=ship' value='Leszerelés'>
									</FORM>
								</DIV>
								<DIV class='itemdescription'>
									$desc
								</DIV>
						</DIV>
					";
				}
				if($slotnum > 0)
				{
					print "
						<DIV class='item'>
									<DIV class='itemname'>Üres hely ($slotnum)</DIV>
							</DIV>
					";
				}
			}
			else
			{
				print "
					<DIV class='item'>
								<DIV class='itemname'>Üres hely ($num)</DIV>
						</DIV>
				";
			}
			
			print "</DIV>";
		}
		squadronequip($equipmenttomb, $ship, $squadrons);
	}
		function shipammo($ship, $ammotomb)
		{
			$ammonum = 0;
			foreach($ammotomb as $ammoid=>$amount)
			{
				$ammonum += $amount;
			}
			print "
				<DIV class='eq'>
					<P class='eq'>Lőszer ($ammonum/$ship->basicammostorage)</P>
					<FORM method='POST'>
						<INPUT type='submit' class='submit' name='action=unequipall&slot=ammo' value='Mindet leszerel'>
					</FORM>
			";
			
			foreach($ammotomb as $ammoid=>$amount)
			{
				if(!$amount) continue;
				$ammodata = $_SESSION["data"]["items"]["$ammoid"];
				$desc = hundescription($ammoid);
				
				print"
					<DIV class='item'>
							<DIV class='itemname'>$ammodata->name ($amount)</DIV>
							<DIV class='ammoaction'>
								<FORM method='POST'>
									<INPUT class='ammo' type='number' name='amount' min='1' max='$amount' required='required' value='$amount'>
									<INPUT class='ammosubmit' type='submit' name='action=ammosell&itemid=$ammoid&place=ship' value='Eladás'>
									<INPUT class='ammosubmit' type='submit' name='action=ammounequip&itemid=$ammoid' value='Leszerelés'>
								</FORM>
							</DIV>
							<DIV class='itemdescription'>
								$desc
							</DIV>
					</DIV>
				";
			}
			
			print "</DIV>";
		}
		
		function squadronequip($equipmenttomb, $ship, $squadrons)
		{
			if(!count($squadrons)) return;
			
			$squadronslot = 0;
			foreach($equipmenttomb["ship"]["hangar"] as $itemid=>$amount)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				$squadronslot += $itemdata->squadronplace * $amount;
			}
			
			print "
				<DIV class='eq'>
					<P class='eq'>Raj (Hely: $squadronslot - Szint: $ship->maxsquadronlevel)</P>
					<FORM method='POST'>
						<INPUT type='submit' class='submit' name='action=squadronunequipallsquad&slot=squadron' value='Mindet leszerel'>
					</FORM>
			";
			
			foreach($squadrons as $squadronid=>$squadron)
			{
				$itemdata = $_SESSION["data"]["items"]["$squadron->itemid"];
				
				$score = partscorecount($_SESSION["character"]["equipment"], $squadronid);
				$desc = hundescription($squadron->itemid);
				
				$squadronhull = hullcount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronshield = shieldcount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronshieldrecharge = shieldrechargecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronenergy = energycount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronenergyusage = energyusagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronhulldamage = hulldamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronshielddamage = shielddamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				$squadronsquadrondamage = squadrondamagecount($squadron->itemid, $_SESSION["character"]["equipment"], "$squadronid");
				
				if($squadron->group == "no") $groupname = "Csapatba nem sorolt";
				else $groupname = $_SESSION["character"]["groups"]["$squadron->group"]->groupname;
				
				print "
					<DIV class='item'>
						<DIV class='itemdescription'>
									$desc
								</DIV>
						<DIV class='shipsquadronname'>
							<P class='shipsquadronname'>$squadron->squadronname</P>
							<DIV class='itemname'>($itemdata->name)</DIV>
						</DIV>
						<DIV class='squadronaction'>
						<FORM method='POST'>
							<INPUT type='submit' class='submit' name='action=squadronrename&squadronid=$squadron->squadronid' value='Átnevezés'>
							<INPUT type='submit' class='submit' value='Eladás' name='activity=squadronsell&squadronid=$squadron->squadronid'>
							<INPUT type='submit' class='submit' value='Leszerelés' name='action=squadronunequip&squadronid=$squadron->squadronid'>
						</FORM>
						</DIV>
						<DIV class='squadronproperty'>
							Pont: $score
						</DIV>
						<DIV class='squadronproperty'>
							Burkolat: $squadronhull
						</DIV>
						<DIV class='squadronproperty'>
							Pajzs: $squadronshield (+$squadronshieldrecharge/kör)
						</DIV>
						<DIV class='squadronproperty'>
							Energia: $squadronenergy (-$squadronenergyusage/kör)
						</DIV>
						<DIV class='squadronproperty'>
							Sebzés: $squadronhulldamage (burkolat) / $squadronshielddamage (pajzs) / $squadronsquadrondamage (raj)
						</DIV>
						<DIV class='squadronproperty'>
							Csapat: $groupname
						</DIV>
					</DIV>
				";
			}
			
			$freeplace = $squadronslot - count($squadrons);
			
			if($freeplace)
			{
				print "
					<DIV class='item'>
						<DIV class='itemname'>Üres hely ($freeplace)</DIV>
					</DIV>
				";
			}
			
			print "</DIV>";
		}
		
	function squadronequipment($squadronid, $squadrons, $equipmenttomb = 0)
	{
		if($_SESSION["hangar"]["shipchoose"] != "squadron") return;
		
		$squadron = $squadrons["$squadronid"];
		
		if(!$groupid = $_SESSION ["character"]["squadrons"]["$squadronid"]->group) $group = "Nincs csapatba sorolva";
		else $group = $_SESSION["character"]["groups"]["$groupid"]->groupname;
		print "
			<DIV style='margin-top: 15px;'>
			<FORM method='POST'>
				<INPUT type='submit' class='submit' name='action=squadronunequipall&slot=all&squadronid=$squadronid' value='Raj kiürítése' style='font-size: 20px;'>
			</FORM>
			</DIV>
			<DIV class='group'>
				$group
			</DIV>
		";
		
		$slots = array("squadroncannon", "squadronhull", "squadronshield", "battery");
		
		foreach($slots as $slot)
		{
			$sell = "itemsell";
			$unequip = "itemunequip";
			
			switch($slot)
			{
				case "squadroncannon":
					$name = "Raj ágyú";
					$num = $squadron->weaponslot;
					$maxlevel = $squadron->maxweaponlevel;
				break;
				case "squadronhull":
					$name = "Raj burkolat";
					$num = $squadron->hullslot;
					$maxlevel = $squadron->maxhulllevel;
				break;
				case "squadronshield":
					$name = "Raj pajzs";
					$num = $squadron->shieldslot;
					$maxlevel = $squadron->maxshieldlevel;
				break;
				case "battery":
					$name = "Akkumulátor";
					$num = $squadron->batteryslot;
					$maxlevel = $squadron->maxbatterylevel;
				break;
			}
			
			if(!$num) continue;
			$maxlevel = " - Szint: " . $maxlevel;
			
			print "
				<DIV class='eq'>
					<P class='eq'>$name (Hely: $num$maxlevel)</P>
					<FORM method='POST'>
						<INPUT type='submit' class='submit' name='action=unequipall&slot=$slot' value='Mindet leszerel'>
					</FORM>
			";
			
			if(isset($equipmenttomb["$slot"]))
			{
				foreach($equipmenttomb["$slot"] as $itemid=>$amount)
				{
					$itemdata = $_SESSION["data"]["items"]["$itemid"];
					$desc = hundescription($itemid);
					
					print"
						<DIV class='item'>
								<DIV class='itemname'>$itemdata->name ($amount)</DIV>
								<DIV class='ammoaction'>
									<FORM method='POST'>
										<INPUT class='ammo' type='number' name='amount' min='1' max='$amount' required='required' value='$amount'>
										<INPUT class='ammosubmit' type='submit' name='action=$sell&itemid=$itemid&place=ship' value='Eladás'>
										<INPUT class='ammosubmit' type='submit' name='action=$unequip&itemid=$itemid&place=$squadronid' value='Leszerelés'>
									</FORM>
								</DIV>
								<DIV class='itemdescription'>
									$desc
								</DIV>
						</DIV>
					";
				}
			}
			else
			{
				print "
					<DIV class='item'>
								<DIV class='itemname'>Üres hely ($num)</DIV>
						</DIV>
				";
			}
			
			print "</DIV>";
		}
	}
	
	function hangarammo($ammotomb, $basicammostorage)
	{
		if($_SESSION["hangar"]["equiptype"] != "ammo" and $_SESSION["hangar"]["equiptype"]) return;
		
		$equippedamount = 0;
		$equipdisabled = "";
		foreach($ammotomb["ship"] as $amount)
		{
			$equippedamount += $amount;
		}
		if($equippedamount >= $basicammostorage) $equipdisabled = "disabled";
		
		foreach($ammotomb["hangar"] as $ammoid=>$amount)
		{
			if(!$amount) continue;
			$ammodata = $_SESSION["data"]["items"]["$ammoid"];
			$desc = hundescription($ammoid);
			
			if($_SESSION["hangar"]["equiplevel"] and $_SESSION["hangar"]["equiplevel"] != $ammodata->level) continue;
			
			print "
				<DIV class='equipitem'>
					<DIV class='itemname'>$ammodata->name ($amount)</DIV>
					<DIV class='itemaction'>
						<FORM method='POST'>
							<INPUT class='ammo' type='number' name='amount' min='1' max='$amount' required='required' value='$amount'>
							<INPUT class='ammosubmit' type='submit' name='action=ammosell&itemid=$ammoid&place=hangar' value='Eladás'>
							<INPUT class='ammosubmit' type='submit' name='action=ammoequip&itemid=$ammoid' value='Felszerelés' $equipdisabled>
						</FORM>
					</DIV>
					<DIV class='itemdescription'>
						$desc
					</DIV>
				</DIV>
			";
		}
	}
	
	function hangarequipment($equipmenttomb, $ship, $squadrons)
	{
		$places = emptyplaces($equipmenttomb, $ship, $squadrons);
		
		if(isset($equipmenttomb["ship"]["equipment"]))
		{
			foreach($equipmenttomb["ship"]["equipment"] as $itemid=>$amount)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				$extras["$itemdata->effect"] = 1;
			}
		}
		
		if(isset($equipmenttomb["ship"]["extender"]))
		{
			foreach($equipmenttomb["ship"]["extender"] as $itemid=>$amount)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				$exteders["$itemdata->effect"] = 1;
			}
		}
		
		if($_SESSION["hangar"]["shipchoose"] == "ship") $to = "ship";
		else $to = $_SESSION["hangar"]["squadronidchoose"];
		
		foreach($equipmenttomb["hangar"] as $slot=>$items)
		{
			foreach($items as $itemid=>$amount)
			{
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				if($_SESSION["hangar"]["equiptype"] and $_SESSION["hangar"]["equiptype"] != $slot) continue;
				
				$equipable = 0;
				switch($slot)
				{
					case "ship":
						$style = "shipequip";
						if($to == "ship") $equipable = 1;
					break;
					case "cannon":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxcannonlevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "rocketlauncher":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxrocketlevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "rifle":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxriflelevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "hangar":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxhangarlevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "shield":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxshieldlevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "hull":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxhulllevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "generator":
						if($to == "ship" and $places["ship"]["$slot"] and $itemdata->level <= $ship->maxgeneratorlevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "battery":
						$level = 0;
						if($to == "ship")
						{
							if($itemdata->level <= $ship->maxbatterylevel) $level = 1;
						}
						else
						{
							if($itemdata->level <= $squadrons["$to"]->maxbatterylevel) $level = 1;
						}
						if($places["$to"]["$slot"] and $level) $equipable = 1;
						$style = "itemequip";
					break;
					case "squadroncannon":
						if($to != "ship" and $places["$to"]["$slot"] and $itemdata->level <= $squadrons["$to"]->maxweaponlevel) $equipable = 1;
						$style = "itemequip";
					case "squadronshield":
						if($to != "ship" and $places["$to"]["$slot"] and $itemdata->level <= $squadrons["$to"]->maxshieldlevel) $equipable = 1;
						$style = "itemequip";
					case "squadronhull":
						if($to != "ship" and $places["$to"]["$slot"] and $itemdata->level <= $squadrons["$to"]->maxhulllevel) $equipable = 1;
						$style = "itemequip";
					break;
					case "squadron":
						if($to == "ship")
						{
							$maxsquadronnum = 0;
							foreach($equipmenttomb["ship"]["hangar"] as $hitemid=>$hamount)
							{
								$hitemdata = $_SESSION["data"]["items"]["$hitemid"];
								$maxsquadronnum += $hamount * $hitemdata->squadronplace;
							}
							
							if($maxsquadronnum > $a = count($squadrons)) $equipable = 1;
						}
						$style = "squadronequip";
					break;
					case "equipment":
						if(isset($extras["$itemdata->effect"])) print 1;
						if($to == "ship" and $places["$to"]["$slot"] and !isset($extras["$itemdata->effect"])) $equipable = 1;
						$style = "specialequip";
					break;
					case "extender":
						if(isset($extenders["$itemdata->effect"])) print 1;
						if($to == "ship" and $places["$to"]["$slot"] and !isset($extenders["$itemdata->effect"])) $equipable = 1;
						$style = "extenderequip";
					break;
					default:
						print "$slot<BR>";
					break;
				}
				
				itemprint($itemid, $amount, $style, $equipable, $to);
			}
		}
	}
	
		function emptyplaces($equipmenttomb, $ship, $squadrons)
		{
			foreach($equipmenttomb as $place=>$equip)
			{
				foreach($equip as $slot=>$items)
				{
					$slotamount = 0;
					foreach($items as $itemid=>$amount)
					{
						$slotamount += $amount;
					}
					$equipments["$place"]["$slot"] = $slotamount;
				}
			}
			
			$tos["ship"] = "ship";
			foreach($squadrons as $squadronid=>$squadron) $tos["$squadronid"] = "squadron";
			
			$slots["ship"] = "";
			$slots["cannon"] = "cannonslot";
			$slots["rocketlauncher"] = "rocketslot";
			$slots["rifle"] = "rifleslot";
			$slots["hangar"] = "hangarslot";
			$slots["squadron"] = "";
			$slots["shield"] = "shieldslot";
			$slots["hull"] = "hullslot";
			$slots["equipment"] = "equipmentslot";
			$slots["generator"] = "generatorslot";
			$slots["battery"] = "batteryslot";
			$slots["extender"] = "extenderslot";
			$slots["squadroncannon"] = "weaponslot";
			$slots["squadronhull"] = "hullslot";
			$slots["squadronshield"] = "shieldslot";
			
			foreach($tos as $to=>$style)
			{
				switch($style)
				{
					case "ship":
						$in = $ship;
						$place = "ship";
					break;
					case "squadron":
						$place = $_SESSION["hangar"]["squadronidchoose"];
						$in = $squadrons["$place"];
					break;
				}
				
				foreach($slots as $slot=>$slotnum)
				{
					switch($slot)
					{
						case "ship":
							$slotequipments["$place"]["$slot"] = 1;
						break;
						case "squadron":
							if(isset($equipmenttomb["ship"]["hangar"]))
							{
								$maxsquadronnum = 0;
								foreach($equipmenttomb["ship"]["hangar"] as $itemid=>$amount)
								{
									$itemdata = $_SESSION["data"]["items"]["$itemid"];
									$maxsquadronnum += $itemdata->squadronplace * $amount;
								}
								$slotequipments["$place"]["$slot"] = $maxsquadronnum - count($squadrons);
							}
							else $slotequipments["$place"]["$slot"] = 0;
						break;
						default:
							if(property_exists($in, "$slotnum"))
							{
								if(!isset($equipments["$place"]["$slot"])) $slotequipments["$place"]["$slot"] = $in->$slotnum;
								else $slotequipments["$place"]["$slot"] = $equipments["$place"]["$slot"] - $in->$slotnum;
							}
							else $slotequipments["$place"]["$slot"] = 0;
							
						break;
					}
				}
			}
			
			return $slotequipments;
		}
		
		function itemprint($itemid, $amount, $style, $equipable, $to)
		{
			$itemdata = $_SESSION["data"]["items"]["$itemid"];
			$desc = hundescription($itemid);
			
			$equipdisabled = ($equipable) ? "" : "disabled";
			
			print "
				<DIV class='equipitem'>
					<DIV class='itemname'>$itemdata->name ($amount)</DIV>
					<DIV class='itemaction'>
						<FORM method='POST'>
							<INPUT class='ammo' type='number' name='amount' min='1' max='$amount' required='required' value='$amount'>
							<INPUT class='ammosubmit' type='submit' name='action=itemsell&itemid=$itemid&place=hangar' value='Eladás'>
							<INPUT class='ammosubmit' type='submit' name='action=$style&itemid=$itemid&place=$to' value='Felszerelés' $equipdisabled>
						</FORM>
					</DIV>
					<DIV class='itemdescription'>
						$desc
					</DIV>
				</DIV>
			";
		}
?>