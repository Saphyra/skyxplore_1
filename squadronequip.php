<?php
	include("connection.php");
	
	if(isset($_POST["submit"]))
	{
		if(!$squadrondataleker = mysqli_query($_SESSION["conn"], "SELECT * FROM systemdata WHERE name='squadronnames' OR name='squadronids'")) die("squadronadatok lelkérése sikertelen");
		$squadronids = mysqli_fetch_assoc($squadrondataleker);
		$squadronnames = mysqli_fetch_assoc($squadrondataleker);
		$squadronids = unserialize($squadronids["value"]);
		$squadronnames = unserialize($squadronnames["value"]);
	}
	
	if(isset($_SESSION["equipment"]["action"]))
	{
		switch($_SESSION["equipment"]["action"]["action"])
		{
			case "squadronrename":
				if(!isset($_POST["submit"]))
				{
					$title = "Raj átnevezése";
					$data = "
						<FORM method='POST'>
							<DIV class='input'>
								Új név:
								<INPUT type='text' name='squadronname' required='required'>
								<INPUT type='submit' name='submit' value='Átnevezés'>
							</DIV>
						</FORM>
					";
				}
				elseif(isset($_POST["submit"]))
				{
					if(in_array($_POST["squadronname"], $squadronnames))
					{
						$title = "Rajnév foglalt!";
						$data = "
							<FORM method='POST'>
								<DIV class='input'>
									Új név:
									<INPUT type='text' name='squadronname' required='required'>
									<INPUT type='submit' name='submit' value='Átnevezés'>
								</DIV>
							</FORM>
						";
					}
					else
					{
						$squadronid = $_SESSION["equipment"]["action"]["squadronid"];
						foreach($squadronnames as $index=>$name)
						{
							if($name == $_SESSION["character"]["squadrons"]["$squadronid"]->squadronname) unset($squadronnames[$index]);
						}
						$squadronnames[] = $_POST["squadronname"];
						
						$_SESSION["character"]["squadrons"]["$squadronid"]->squadronname = $_POST["squadronname"];
						$title = "Raj sikeresen átnevezve!";
						$data = "";
					}
				}
			break;
			case "squadronequip":
				if(!isset($_POST["submit"]))
				{
					$title = "Raj elnevezése";
					$data = "
						<FORM method='POST'>
							<DIV class='input'>
								Név:
								<INPUT type='text' name='squadronname' required='required'>
								<INPUT type='submit' name='submit' value='Felszerelés'>
							</DIV>
						</FORM>
					";
				}
				else
				{
					$match = 0;
					foreach($squadronnames as $name)
					{
						if($name == $_POST["squadronname"]) $match = 1;
					}
					
					if($match)
					{
						$title = "Rajnév foglalt!";
						$data = "
							<FORM method='POST'>
								<DIV class='input'>
									Név:
									<INPUT type='text' name='squadronname' required='required'>
									<INPUT type='submit' name='submit' value='Felszerelés'>
								</DIV>
							</FORM>
						";
					}
					elseif(!$match)
					{
						$squadronid = idgenerate("squad", $squadronids, 10);
						$squadronids[] = $squadronid;
						$squadronnames[] = $_POST["squadronname"];
						
						foreach($_SESSION["character"]["equipment"] as &$item)
						{
							if($item->itemid == $_SESSION["equipment"]["action"]["itemid"] and $item->place == "hangar")
							{
								$item->equipped = 1;
								$item->place = $squadronid;
								break;
							}
						}
						
						$_SESSION["character"]["squadrons"]["$squadronid"] = new squadron($squadronid, $_POST["squadronname"]);
						
						$itemid = $_SESSION["equipment"]["action"]["itemid"];
						$itemdata  = $_SESSION["data"]["items"]["$itemid"];
						
						$properties = get_object_vars($itemdata);
						
						foreach($properties as $name=>$ertek)
						{
							$_SESSION["character"]["squadrons"]["$squadronid"]->$name = $ertek;
						}
						
						$group = &$_SESSION["character"]["groups"]["no"];
						$group->membernum += 1;
						if($group->members) $members = unserialize($group->members);
						$members[] = $squadronid;
						$group->members = serialize($members);
						
						$title = "Raj felszerelve";
						$data = "";
						unset($_SESSION["equipment"]["action"]);
					}
				}
			break;
			default:
				$title = "Hiba az utasításban.";
				$data = "";
			break;
		}
	}
	else
	{
		$title = "Hiba az utasításban.";
		$data = "";
	}
	
	if(isset($_POST["submit"]))
	{
		$squadronids = serialize($squadronids);
		$squadronnames = serialize($squadronnames);
		if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronids' WHERE name='squadronids'")) die("rajnevek frissítése siekrtelen");
		if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$squadronnames' WHERE name='squadronnames'")) die("rajidk frissítése siekrtelen");
	}
	save();
?>

<HTML>
	<HEAD>
		<link rel='stylesheet' type='text/css' href='shell_style.css'>
		<link rel='stylesheet' type='text/css' href='squadronequip.css'>
		<TITLE><?php print $title; ?></TITLE>
	</HEAD>
<BODY>
	<DIV class='container'>
		<DIV class='title'><?php print $title; ?></DIV>
		<?php print $data; ?>
		<DIV class='back'><A class='back' href=' <?php print $_SESSION["from"]; ?>'>Vissza</A></DIV>
	</DIV>
</BODY>
</HTML>


<?php

?>