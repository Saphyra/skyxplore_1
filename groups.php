<?php
	include("connection.php");
	$_SESSION["from"] = "groups.php";
	
	if(!empty($_POST))
	{
		foreach($_POST as $name=>$ertek)
		{
			$er = decode($name);
			foreach($er as $ne=>$val) $_SESSION["groups"]["action"]["$ne"] = $val;
		}
		if(isset($_POST["group"])) $_SESSION["groups"]["action"]["group"] = $_POST["group"];
		if($_SESSION["groups"]["action"]["action"] == "grouprename" or $_SESSION["groups"]["action"]["action"] == "stylechange")
		{
			header("location:newgroup.php");
			exit;
		}
		header("location:groups_action.php");
		exit;
	}
	unset($_SESSION["groups"]);
?>

<HTML>
	<HEAD>
		<TITLE>Csapatok</TITLE>
			<link rel='stylesheet' type='text/css' href='shell_style.css'>
			<link rel='stylesheet' type='text/css' href='groups.css'>
			<link rel='stylesheet' type='text/css' href='hundescription.css'>
	</HEAD>
<BODY>
	<DIV class='background'>
		<DIV class='title'>Csapatok</DIV>
		<DIV class='container'>
		<?php
			foreach($_SESSION["character"]["groups"] as $groupid=>$group)
			{
				$groupdelete = "";
				if($groupid != "no")
				{
					$groupdelete = "
						<FORM method='POST'>
						<DIV class='groupdelete'>
							<INPUT type='submit' class='submit' name='action=grouprename&groupid=$groupid' value='Csapat átnevezése'> <INPUT type='submit' class='submit' name='action=stylechange&groupid=$groupid' value='Csapat feladatának megváltoztatása'> <INPUT type='submit' class='submit' name='action=groupdelete&groupid=$groupid' value='Csapat törlése'>
						</DIV>
						</FORM>
					";
				}
				
				
				$duty = "";
				if(isset($group->style))
				{
					switch($group->style)
					{
						case "cannon":
							$duty = "<DIV class='duty'>(Feladat: Csatahajók támadása)</DIV>";
						break;
						case "rifle":
							$duty = "<DIV class='duty'>(Feladat: Rajok támadása)</DIV>";
						break;
					}
				}
				print "
					<TABLE class='container'>
						<TR>
							<TH colspan='3'>$group->groupname $duty $groupdelete</TH>
						</TR>
				";
				if(!$group->membernum)
				{
					print "
						<TR>
							<TD class='nomember'>A csoportnak nincsenek tagjai.</TD>
						</TR>
						</TABLE>
					";
					continue;
				}
				
				$members = unserialize($group->members);
				$num = 1;
				foreach($members as $squadronid)
				{
					if($num == 4)
					{	
						print "</TR><TR>";
						$num = 1;
					}
						$squadron = $_SESSION["character"]["squadrons"]["$squadronid"];
						$score = partscorecount($_SESSION["character"]["equipment"], $squadronid);
						$hull = hullcount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$shield = shieldcount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$shieldrecharge = shieldrechargecount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$energy = energycount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$energyusage = energyusagecount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$hulldamage = hulldamagecount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$shielddamage = shielddamagecount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						$squadrondamage = squadrondamagecount($squadron->itemid, $_SESSION["character"]["equipment"], $squadronid);
						
						$groupset = groupset($squadronid);
						
						
						print "
							<TD>
								<DIV class='squadron'>
									<DIV class='squadronname'>$squadron->squadronname</DIV>
									<DIV class='level'>Szint: $squadron->level Pont: $score</DIV>
									<DIV class='hull'><U>Burkolat:</U> $hull</DIV>
									<DIV class='shield'><U>Pajzs:</U> $shield (+$shieldrecharge / kör)</DIV>
									<DIV class='energy'><U>Energia:</U> $energy</DIV>
									<DIV class='usage'><U>Energiahasználat:</U> $energyusage / kör</DIV>
									<DIV class='damage'><U>Sebzés:</U> $hulldamage (burkolat) / $shielddamage (pajzs) / $squadrondamage (raj)</DIV>
									<DIV class='groupset'>$groupset</DIV>
								</DIV>
							</TD>
						";
						$num++;
				}
				for($num; $num < 4; $num++) print "<TD></TD>";
				print "</TR></TABLE>";
			}
		?>
		</DIV>
		
		<DIV class='logout'><A href='http://www.saphyra.pe.hu/logout.php' class='logout'>Kijelentkezés</A></DIV>
		<DIV class='newgroup'><A href='newgroup.php' class='newgroup'>Új csapat létrehozása</A></DIV>
		<DIV class='back'><A href='equipment.php' class='back'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>

<?php
	function groupset($squadronid)
	{
		$squadron = $_SESSION["character"]["squadrons"]["$squadronid"];
		$result[] = "
			<FORM method='POST'>
				<SELECT name='group'>
		";
		
		$maxmembernum = $_SESSION["character"]["skill"]["emfp"]->level + 1;
		
		foreach($_SESSION["character"]["groups"] as $groupid=>$group)
		{
			$selected = "";
			if($squadron->group == $groupid) $selected = "selected='selected'";
			
			$maxmembernum = $_SESSION["character"]["skill"]["emfp"]->level + 1;
			if($groupid == "no") $maxmembernum = 100;
			$disabled = "";
			if($group->membernum >= $maxmembernum) $disabled = "disabled='disabled'";
			
			$result[] = "
				<OPTION value='$groupid' $selected $disabled>$group->groupname</OPTION>
			";
		}
		
		$result[] = "
				</SELECT>
				<INPUT type='submit' name='action=othergroup&squadronid=$squadronid' value='Áthelyezés' class='submit'>
			</FORM>
		";
		
		$groupset = "";
		foreach($result as $ertek)
		{
			$groupset .= $ertek;
		}
		
		return $groupset;
	}
?>
