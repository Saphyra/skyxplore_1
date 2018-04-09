<?php
	include("connection.php");
	$_SESSION["from"] = "shop.php";
	if(!isset($_SESSION["shop"]["page"])) $_SESSION["shop"]["page"] = "shop";
	if(!isset($_SESSION["shop"]["title"])) $_SESSION["shop"]["title"] = "Bolt - SkyXplore";
	
	if(!isset($_SESSION["shop"]["equiptype"])) $_SESSION["shop"]["equiptype"] = 0;
	if(isset($_GET["type"])) $_SESSION["shop"]["equiptype"] = $_GET["type"];
	
	if(!isset($_SESSION["shop"]["equiplevel"])) $_SESSION["shop"]["equiplevel"] = 0;
	if(isset($_GET["level"])) $_SESSION["shop"]["equiplevel"] = $_GET["level"];
	
	if(!isset($_SESSION["shop"]["equipcompany"])) $_SESSION["shop"]["equipcompany"] = 0;
	if(isset($_GET["equipcompany"])) $_SESSION["shop"]["equipcompany"] = $_GET["equipcompany"];
	
	if(!isset($_SESSION["shop"]["equipskill"])) $_SESSION["shop"]["equipskill"] = 0;
	if(isset($_GET["equipskill"])) $_SESSION["shop"]["equipskill"] = $_GET["equipskill"];
	
	if(!isset($_GET["available"])) $_SESSION["shop"]["available"] = 0;
	if(isset($_GET["available"])) $_SESSION["shop"]["available"] = 1;
	
	if(isset($_GET["pagechoose"]))
	{
		switch($_GET["pagechoose"])
		{
			
			case "shop":
				$_SESSION["shop"]["title"] = "Bolt - SkyXplore";
				$_SESSION["shop"]["page"] = $_GET["pagechoose"];
			break;
			case "skill":
				$_SESSION["shop"]["title"] = "Képességek - SkyXplore";
				$_SESSION["shop"]["page"] = $_GET["pagechoose"];
			break;
		}
	}
	
	if(!empty($_POST))
	{
		foreach($_POST as $name=>$ertek)
		{
			if($name == "amount")
			{
				$_SESSION["shop"]["action"]["$name"] = $ertek;
			}
			else
			{
				$er = decode($name);
				foreach($er as $ne=>$val) $_SESSION["shop"]["action"]["$ne"] = $val;
			}
		}
		if(1) header("location:shop_action.php");
		exit;
	}
	include("hundescription.php");
?>

<HTML>
	<HEAD>
		<TITLE><?php print $_SESSION["shop"]["title"]; ?></TITLE>
		<link rel='stylesheet' type='text/css' href='shell_style.css'>
		<link rel='stylesheet' type='text/css' href='shop.css'>
		<link rel='stylesheet' type='text/css' href='hundescription.css'>
	</HEAD>
</HTML>
<BODY>
	<DIV class='background'>
		<DIV class='choosecontainer'>
			<DIV class='shopchoose'><A class='choose' href='shop.php?pagechoose=shop' <?php if($_SESSION["shop"]["page"] == "shop") print "style='color: red;'"; ?>>Bolt</A></DIV>
			<DIV class='skillchoose'><A class='choose' href='shop.php?pagechoose=skill' <?php if($_SESSION["shop"]["page"] == "skill") print "style='color: red;'"; ?>>Képességek</A></DIV>
		</DIV>
		
		<?php
			switch($_SESSION["shop"]["page"])
			{
				case "shop";
					shop();
				break;
				case "skill":
					skill();
				break;
			}
		?>
		<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
		<?php
			$credit = $_SESSION["character"]["credit"];
			$diamond = $_SESSION["character"]["diamond"];
			print "<DIV class='money'>Kredit: $credit<BR>Gyémánt: $diamond</DIV>";
		?>
		<DIV class='back'><A href='hangar.php' class='logout'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>
<?php
	function shop()
	{
		print "
			<DIV class='szuro'>
			<FORM method='GET'>
				<DIV class='type'>
					Típus:
					<SELECT name='type' class='type'>
						<OPTION value='0' class='type' selected='selected'>Összes</OPTION>
		";
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
								if(!$_SESSION["shop"]["equiptype"]) $selected = "";
								elseif($_SESSION["shop"]["equiptype"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='type'>$ertek</OPTION>";
							}
		print "
					</SELECT>
				</DIV>
				<DIV class='level'>
					Szint:
					<SELECT name='level' class='level'>
						<OPTION value='0' class='level' selected='selected'>Összes</OPTION>
		";
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
								if(!$_SESSION["shop"]["equiplevel"]) $selected = "";
								elseif($_SESSION["shop"]["equiplevel"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='level'>$ertek</OPTION>";
							}
							
			$checked = "";
			if($_SESSION["shop"]["available"]) $checked = "checked='checked'";
		print "
					</SELECT>
				</DIV>
				<DIV class='available'>Csak megvehető: <INPUT type='checkbox' $checked name='available' class='available' value='1'></DIV>
				<DIV class='search'><INPUT type='submit' name='submit' value='Szűrés' class='search'></DIV>
			</FORM>
			</DIV>
			<DIV class='container'>
		";
		
		$disp = 0;
		foreach($_SESSION["data"]["items"] as $item)
		{
			if($item->type != "ability")
			{
				if(!$_SESSION["shop"]["equiptype"] or $_SESSION["shop"]["equiptype"] == $item->slot)
				{
					if(!$_SESSION["shop"]["equiplevel"] or $_SESSION["shop"]["equiplevel"] == $item->level)
					{
						if($item->type != "ammo")
						{
							if($item->buyprice <= $_SESSION["character"]["credit"] and levelset($item))
							{
								$av = 1;
								$disabled = "";
							}
							else 
							{
								$disabled = "disabled='disabled'";
								$av = 0;
							}
							
							if(!$_SESSION["shop"]["available"]) $print = 1;
							elseif($_SESSION["shop"]["available"] and $av == 1) $print = 1;
							else $print = 0;
							
							$credit = $_SESSION["character"]["credit"];
							if($print) 
							{
								$disp = 1;
								
								$max = $credit / $item->buyprice;
								settype($max, "integer");
								
								$desc = hundescription($item->itemid);
								print "
								<DIV class='item'>
								<FORM method='POST'>
									<DIV class='itemname'>$item->name</DIV>
									<DIV class='buycontainer'>
										<DIV class='price'>$item->buyprice Kredit</DIV>
										<INPUT class='amount' type='number' name='amount' min='1' max='$max' required='required' value='1'>
										<INPUT class='submit' type='submit' name='activity=itembuy&itemid=$item->itemid' $disabled value='Vásárlás'>
									</DIV>
									<DIV class='itemdescription'>
										<DIV class='desctitle'>Leírás</DIV>
										$desc
									</DIV>
								</FORM>
								</DIV>
								";
							}
						}
						elseif($item->type == "ammo")
						{
							if($item->buyprice <= $_SESSION["character"]["credit"] and levelset($item))
							{
								$disabled = "";
								$av = 1;
							}
							else
							{
								$disabled = "disabled='disabled'";
								$av = 0;
							}
							
							$max = $_SESSION["character"]["credit"] / $item->buyprice;
							settype($max, "integer");
							
							if(!$_SESSION["shop"]["available"]) $print = 1;
							elseif($_SESSION["shop"]["available"] and $av == 1) $print = 1;
							else $print = 0;
							
							if($print)
							{
								$disp = 1;
								$desc = hundescription($item->itemid);
								print "
									<DIV class='item'>
										<DIV class='itemname'>$item->name</DIV>
										<FORM method='POST'>
										<DIV class='buycontainer'>
											<DIV class='price'>Ár: $item->buyprice Kredit</DIV>
											<INPUT class='amount' type='number' name='amount' required='required' value='1' min='1' max='$max'><INPUT class='submit' type='submit' name='activity=ammobuy&itemid=$item->itemid' value='Vásárlás' $disabled>
										</DIV>
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
				}
			}
			
		}
		if(!$disp)
		{
			print "
				<DIV class='item'>
					<DIV class='itemname'>Nincs a szűrésnek megfelelő eredmény.</DIV>
					<DIV class='itemdescription'><DIV class='desctitle'>Leírás</DIV></DIV>
				</DIV>
			";
		}
		
		print "</DIV>";
	}
	
	function levelset($item)
	{
		$level = $_SESSION["character"]["ship"]->level;
		$company = $_SESSION["character"]["ship"]->itemtype;
		$ship = $_SESSION["character"]["ship"];
		
		switch($item->type)
		{
			case "ship";
				return 1;
			break;
			case "squadron":
				if($item->level <= $ship->maxsquadronlevel - 1 or $item->level == 1) return 1;
				elseif($ship->level == 10) return 1;
				elseif($ship->itemtype == "emf" and $item->level == $ship->maxsquadronlevel) return 1;
				else return 0;
			break;
			case "weapon":
				switch($item->slot)
				{
					case "cannon":
						if($item->level == 1 or $item->level <= $ship->maxcannonlevel - 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "gaa" and $item->level == $ship->maxcannonlevel) return 1;
						else return 0;
					break;
					case "rifle":
						if($item->level == 1 or $item->level <= $ship->maxriflelevel - 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "mfa" and $item->level == $ship->maxriflelevel) return 1;
						else return 0;
					break;
					case "rocketlauncher":
						if($item->level <= $ship->maxrocketlevel - 1) return 1;
						elseif($item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "idf" and $item->level == $ship->maxrocketlevel) return 1;
						else return 0;
					break;
					case "squadroncannon":
						$rajkeres["level"] = $ship->maxsquadronlevel;
						$rajkeres["type"] = "squadron";
						
						$squadron = objectsearch($rajkeres, $_SESSION["data"]["items"]);
						$maxlevel = $_SESSION["data"]["items"]["$squadron"]->maxweaponlevel;
						
						if($item->level <= $maxlevel - 1 or $item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($item->itemtype == "squadronrifle" and $ship->itemtype  == "mfa" and $item->level == $maxlevel) return 1;
						elseif($item->itemtype == "squadroncannon" and $ship->itemtype  == "emf" and $item->level == $maxlevel) return 1;
						elseif($item->itemtype == "squadronpulse" and $ship->itemtype  == "idf" and $item->level == $maxlevel) return 1;
						else return 0;
					break;
				}
			break;
			case "shield":
				switch($item->slot)
				{
					case "shield":
						if($item->level <= $ship->maxshieldlevel - 1 or $item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "pdm" and $item->level == $ship->maxshieldlevel) return 1;
						else return 0;
					break;
					case "squadronshield":
						$rajkeres["level"] = $ship->maxsquadronlevel;
						$rajkeres["type"] = "squadron";
						
						$squadron = objectsearch($rajkeres, $_SESSION["data"]["items"]);
						$maxlevel = $_SESSION["data"]["items"]["$squadron"]->maxshieldlevel;
						
						if($item->level <= $maxlevel -1 or $item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "cri" and $item->level == $maxlevel) return 1;
						else return 0;
					break;
				}
			break;
			case "hull":
				switch($item->slot)
				{
					case "hull":
						if($item->level <= $ship->maxhulllevel - 1 or $item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "idf" and $item->level == $ship->maxhulllevel) return 1;
						else return 0;
					break;
					case "squadronhull":
						$rajkeres["level"] = $ship->maxsquadronlevel;
						$rajkeres["type"] = "squadron";
						
						$squadron = objectsearch($rajkeres, $_SESSION["data"]["items"]);
						$maxlevel = $_SESSION["data"]["items"]["$squadron"]->maxhulllevel;
						
						if($item->level <= $maxlevel -1 or $item->level == 1) return 1;
						elseif($ship->level == 10) return 1;
						elseif($ship->itemtype == "gaa" and $item->level == $maxlevel) return 1;
						else return 0;
					break;
				}
			break;
			case "hangar":
			{
				if($item->level <= $ship->maxhangarlevel - 1 or $item->level == 1) return 1;
				elseif($ship->level == 10) return 1;
				elseif($ship->itemtype == "emf" and $item->level == $ship->maxhangarlevel) return 1;
				else return 0;
			}
			break;
			case "equipment":
				return 1;
			break;
			case "generator":
				if($item->level <= $ship->maxgeneratorlevel - 1 or $item->level == 1) return 1;
				elseif($ship->level == 10) return 1;
				elseif($ship->itemtype == "cri" and $item->level == $ship->maxgeneratorlevel) return 1;
				else return 0;
			break;
			case "battery":
				if($item->level <= $ship->maxbatterylevel - 1 or $item->level == 1) return 1;
				elseif($ship->level == 10) return 1;
				elseif($ship->itemtype == "cri" and $item->level == $ship->maxbatterylevel) return 1;
				else return 0;
			break;
			case "extender":
				if($item->level <= $ship->maxextenderlevel - 1 or $item->level == 1) return 1;
				elseif($ship->level == 10) return 1;
				elseif($ship->itemtype == "pdm" and $item->level == $ship->maxextenderlevel) return 1;
				else return 0;
			break;
			case "ammo":
				return 1;
			break;
		}
	}
	
	function skill()
	{
		print "
			<DIV class='szuro'>
			<FORM method='GET'>
				<DIV class='company'>
					Társaság:
					<SELECT name='equipcompany' class='company'>
						<OPTION value='0' class='type' selected='selected'>Összes</OPTION>
		";
							$type["emf"] = "EMF - Earth Mothership Factory";
							$type["pdm"] = "PDM - Planet Defender Military";
							$type["idf"] = "IDF - Interplanetary Destroyer Forces";
							$type["mfa"] = "MFA - Mining and Forwarding Association";
							$type["gaa"] = "GAA - Galactic Assassin Alliance";
							$type["cri"] = "CRI - Central Research Institute";
							asort($type);
							
							foreach($type as $name=>$ertek)
							{
								$selected = "";
								if(!$_SESSION["shop"]["equipcompany"]) $selected = "";
								elseif($_SESSION["shop"]["equipcompany"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='type'>$ertek</OPTION>";
							}
		print "
					</SELECT>
				</DIV>
				<DIV class='skill'>
					Típus:
					<SELECT name='equipskill' class='skill'>
						<OPTION value='0' class='level' selected='selected'>Összes</OPTION>
		";
							$skill["passive"] = "Passzív";
							$skill["active1"] = "Aktív 1";
							$skill["active2"] = "Aktív 2";
							
							foreach($skill as $name=>$ertek)
							{
								$selected = "";
								if(!$_SESSION["shop"]["equipskill"]) $selected = "";
								elseif($_SESSION["shop"]["equipskill"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='level'>$ertek</OPTION>";
							}
		print "
					</SELECT>
				</DIV>
				<DIV class='search'><INPUT type='submit' name='submit' value='Szűrés' class='search'></DIV>
			</FORM>
			</DIV>
			<DIV class='container'>
		";
			
			foreach($_SESSION["character"]["skill"] as $itemid=>$skill)
			{
				$skilldata = $_SESSION["data"]["items"]["$itemid"];
				if(!$_SESSION["shop"]["equipcompany"] or $_SESSION["shop"]["equipcompany"] == $skilldata->owner)
				{
					if(!$_SESSION["shop"]["equipskill"] or $_SESSION["shop"]["equipskill"] == $skilldata->itemtype)
					{
						$partsneed = pow(($skill->level+1), 2) * $skilldata->upgradecost;
						$completed = $skill->parts / $partsneed * 100;
						settype($completed, "integer");
						
						$diamond = $_SESSION["character"]["diamond"];
						$upgradeprice = $partsneed * 10;
						
						
						if($upgradeprice <= $_SESSION["character"]["diamond"] and $skill->level < $skilldata->maxlevel) $disabled = "";
						else $disabled = "disabled='disabled'";
						
						$value = "Fejlesztés";
						if($skill->level >= $skilldata->maxlevel) $value = "Maximális szint elérve!";
						
						$desc = hundescription($itemid);
						print "
							<FORM method='POST'>
							<DIV class='item'>
								<DIV class='itemname'>$skill->name (Szint: $skill->level)</DIV>
								<DIV class='completebar'><IMG class='percent' width='$completed%' SRC='pixelblue.jpg'><DIV class='percenttext'>$completed%</DIV></DIV>
								<DIV class='buycontainer'>
						";
							if($disabled == "") print "<DIV class='price' style='margin: 5px;'>$upgradeprice Gyémánt</DIV>";
						print "
									<INPUT class='submit' type='submit' style='margin-bottom: 5px;' name='activity=skillfinish&itemid=$skilldata->itemid&upgradeprice=$upgradeprice&partsneed=$partsneed&parts=$skill->parts' value='$value' $disabled>
								</DIV>
								<DIV class='itemdescription'>
									<DIV class='desctitle'>Leírás</DIV>
									$desc
								</DIV>
							</DIV>
							</FORM>
						";
					}
				}
				
			}
			
		print "
			</DIV>
		";
	}
?>