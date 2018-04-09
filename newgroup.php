<?php
	include("connection.php");
	
	if(isset($_SESSION["groups"]["action"]))
	{
		if($_SESSION["groups"]["action"]["action"] == "grouprename")
		{
			$title = "Csapat átnevezése";
			$value = "Átnevezés";
			$cannonselected = "";
			$rifleselected = "";
			if(isset($_SESSION["groups"]["action"]["groupid"]))
			{
				$groupid = $_SESSION["groups"]["action"]["groupid"];
				$group = $_SESSION["character"]["groups"]["$groupid"];
				
				switch($group->style)
				{
					case "cannon":
						$cannonselected = "selected";
					break;
					case "rifle":
						$rifleselected = "selected";
					break;
				}
			}
			$data = "
				<DIV class='input'>
				<FORM method='POST'>
					<DIV>
						Név: <INPUT type='text' class='text' name='groupname' required='required'>
						Feladat:
							<SELECT name='style'>
								<OPTION value='cannon' $cannonselected>Csatahajók támadása</OPTION>
								<OPTION value='rifle' $rifleselected>Rajok támadása</OPTION>
							</SELECT>
					</DIV>
					<DIV><INPUT type='submit' class='submit' name='rename' value='$value'></DIV>
				</FORM>
				</DIV>
			";
		}
		elseif($_SESSION["groups"]["action"]["action"] == "stylechange")
		{
			$title = "Csapat feladatának megváltoztatása";
			$value = "Megváltoztat";
			$cannonselected = "";
			$rifleselected = "";
			if(isset($_SESSION["groups"]["action"]["groupid"]))
			{
				$groupid = $_SESSION["groups"]["action"]["groupid"];
				$group = $_SESSION["character"]["groups"]["$groupid"];
				
				switch($group->style)
				{
					case "cannon":
						$cannonselected = "selected";
					break;
					case "rifle":
						$rifleselected = "selected";
					break;
				}
			}
			
			$data = "
				<DIV class='input'>
				<FORM method='POST'>
					<DIV>
						Feladat:
							<SELECT name='style'>
								<OPTION value='cannon' $cannonselected>Csatahajók támadása</OPTION>
								<OPTION value='rifle' $rifleselected>Rajok támadása</OPTION>
							</SELECT>
					</DIV>
					<DIV><INPUT type='submit' class='submit' name='stylechange' value='$value'></DIV>
				</FORM>
				</DIV>
			";
		}
	}
	else 
	{
		$title = "Új csapat létrehozása";
		$value = "Csapat létrehozása";
		$data = "
			<DIV class='input'>
			<FORM method='POST'>
				<DIV>
					Név: <INPUT type='text' class='text' name='groupname' required='required'>
					Feladat:
						<SELECT name='style'>
							<OPTION value='cannon'>Csatahajók támadása</OPTION>
							<OPTION value='rifle'>Rajok támadása</OPTION>
						</SELECT>
				</DIV>
				<DIV><INPUT type='submit' class='submit' name='rename' value='$value'></DIV>
			</FORM>
			</DIV>
		";
	}
	
	if(isset($_POST["rename"]))
	{
		$groupname = $_POST["groupname"];
		$egy = 0;
		foreach($_SESSION["character"]["groups"] as $group)
		{
			if(gettype($group) == "object" and $group->groupname == $groupname)
			{
				$egy = 1;
				$title = "Már van ilyen nevű csoport";
			}
		}
		
		if(!$egy)
		{
			if(isset($_SESSION["groups"]["action"]))
			{
				$groupid = $_SESSION["groups"]["action"]["groupid"];
				$_SESSION["character"]["groups"]["$groupid"]->groupname = $_POST["groupname"];
				$_SESSION["character"]["groups"]["$groupid"]->style = $_POST["style"];
				$title = "Csapat átnevezve";
				$data = "";
				unset($_SESSION["groups"]["action"]);
			}
			else
			{
				if(!$groupidsleker = mysqli_query($_SESSION["conn"], "SELECT value FROM systemdata WHERE name='groupids'")) die("Csapatidk lekérése siekrtelen");
				$groupidtomb = mysqli_fetch_assoc($groupidsleker);
				$groupids = unserialize($groupidtomb["value"]);
				$groupid = idgenerate("group", $groupids, 6);
				$groupids[] = $groupid;
				
				$groupidup = serialize($groupids);
				if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$groupidup' WHERE name='groupids'")) die("Csapatidk frissítése sikertelen");
				
				$_SESSION["character"]["groups"]["$groupid"] = new group($groupid, $groupname);
				$_SESSION["character"]["groups"]["$groupid"]->style = $_POST["style"];
				$title = "Csapat létrehozva";
				$data = "";
			}
			save();
		}
		
	}
	elseif(isset($_POST["stylechange"]))
	{
		$groupid = $_SESSION["groups"]["action"]["groupid"];
		$_SESSION["character"]["groups"]["$groupid"]->style = $_POST["style"];
		$title = "Feladat megváltoztatva";
		$data = "";
		save();
	}
?>

<HTML>
	<HEAD>
		<TITLE><?php print $title; ?></TITLE>
		<link rel='stylesheet' type='text/css' href='shell_style.css'>
		<link rel='stylesheet' type='text/css' href='newgroup.css'>
	</HEAD>
<BODY>
	<DIV class='container'>
		<DIV class='title'><?php print $title; ?></DIV>
		<?php print $data; ?>
		<DIV class='back'><A class='back' href='groups.php'>Vissza</A><DIV>
	</DIV>
</BODY>
</HTML>