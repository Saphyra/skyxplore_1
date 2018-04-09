<?php
	include("connection.php");

	$_SESSION["from"] = "construction.php";
	
	if(!empty($_POST))
	{
		foreach($_POST as $name=>$ertek)
		{
			$er = decode($name);
			foreach($er as $ne=>$val) $_SESSION["construction"]["action"]["$ne"] = $val;
		}
		if(1) header("location:constructionfinish.php");
		exit;
	}
	include("hundescription.php");
	
	if(!isset($_SESSION["construction"]["equiptype"])) $_SESSION["construction"]["equiptype"] = 0;
	if(isset($_GET["type"])) $_SESSION["construction"]["equiptype"] = $_GET["type"];
	
	if(!isset($_SESSION["construction"]["equiplevel"])) $_SESSION["construction"]["equiplevel"] = 0;
	if(isset($_GET["level"])) $_SESSION["construction"]["equiplevel"] = $_GET["level"];
?>

<HTML>
	<HEAD>
		<TITLE>Tárgyak</TITLE>
		<link rel="stylesheet" type="text/css" href="shell_style.css">
		<link rel="stylesheet" type="text/css" href="hundescription.css">
		<link rel="stylesheet" type="text/css" href="construction.css">
	</HEAD>
<BODY>
	<DIV class='background'>
		<DIV class='title'>Tárgyak</DIV>
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
								if(!$_SESSION["construction"]["equiptype"]) $selected = "";
								elseif($_SESSION["construction"]["equiptype"] == $name) $selected = "selected='selected'";
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
								if($_SESSION["construction"]["equiplevel"] == $name) $selected = "selected='selected'";
								print "<OPTION value='$name' $selected class='level'>$ertek</OPTION>";
							}
					?>
				</SELECT>
			</DIV>
			<DIV class='search'><INPUT type='submit' name='submit' value='Szűrés' class='search'></DIV>
		</FORM>
		</DIV>
		<DIV class='container'>
		
<?php
	foreach($_SESSION["character"]["construction"] as $itemid=>$partsnum)
	{
		
		$itemdata = $_SESSION["data"]["items"]["$itemid"];
		if(property_exists($itemdata, "construction"))
		{
			$completed = $partsnum / $itemdata->construction * 100;
			if($completed > 100) $completed = 100;
			
			$constszazalek["$itemid"] = $completed;
		}
	}
	arsort($constszazalek);
	
	foreach($constszazalek as $itemid=>$ertek)
	{
		$itemdata = $_SESSION["data"]["items"]["$itemid"];
		
		if($_SESSION["construction"]["equiptype"] and $_SESSION["construction"]["equiptype"] != $itemdata->slot)
		{
			unset($constszazalek["$itemid"]);
			continue;
		}
		if($_SESSION["construction"]["equiplevel"] and $_SESSION["construction"]["equiplevel"] != $itemdata->level)
		{
			unset($constszazalek["$itemid"]);
		}
	}
	
	if(!$constszazalek)
	{
		print "
			<DIV class='item'>
				<DIV class='itemname'>Nincs a szűrésnek megfelelő eredmény.</DIV>
				<DIV class='itemdescription'><DIV class='desctitle'>Leírás</DIV></DIV>
			</DIV>
		";
	}
	foreach($constszazalek as $itemid=>$completed)
	{
		$itemdata = $_SESSION["data"]["items"]["$itemid"];
		
		$partsnum = $_SESSION["character"]["construction"]["$itemid"];
		$left = 100-$completed;
		
		if($left <= 0) $flow = 0;
		else
		{
			$flow = $left/2.5;
		}
		
		$creditcost = $itemdata->craftprice * $flow + $itemdata->craftprice;
		settype($creditcost, "integer");
		$credit = $_SESSION["character"]["credit"];
		$disabled = "";
		if($credit < $creditcost) $disabled = "disabled='disabled'";
		settype($completed, "integer");
		
		$description = hundescription($itemid);
		
		print "
			<DIV class='item'>
			<FORM method='POST'>
				<DIV class='itemname'>$itemdata->name</DIV>
				<DIV class='itemdescription'>
					<DIV class='desctitle'>Leírás</DIV>
					$description
				</DIV>
				<DIV class='completebar'><IMG class='percent' width='$completed%' SRC='pixelgreen.jpg'><DIV class='percenttext'>$completed% ($partsnum / $itemdata->construction rész)</DIV></DIV>
				<DIV class='finish'>
					<P class='finish'>$creditcost Kredit</P>
					<INPUT class='finish' type='submit' name='itemid=$itemdata->itemid&cost=$creditcost' $disabled value='Befejezés '>
				</DIV>
			</FORM>
			</DIV>
		";
	}

?>
		</DIV>
			<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
			<?php
				$credit = $_SESSION["character"]["credit"];
				$diamond = $_SESSION["character"]["diamond"];
				print "<DIV class='money'>Kredit: $credit<BR>Gyémánt: $diamond</DIV>";
			?>
			<DIV class='back'><A href='hangar.php' class='back'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>