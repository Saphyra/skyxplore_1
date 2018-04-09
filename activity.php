<?php
	include("connection.php");

	$charid = $_GET["charid"];
	
	switch($_GET["activity"])
	{
		case "rename": $title = "Karakter átnevezése"; break;
		case "delete": $title = "Karakter törlése"; break;
		default: die("URL érvénytelen"); break;
	}
	
	if(isset($_POST["submit"]))
	{
		if(isset($_POST["newcharname"]))
		{
			if(!$charnamelekeres = mysqli_query($_SESSION["conn"], "SELECT charname FROM characters")) die("karakternevek lekérése sikertelen");
			while($nametomb = mysqli_fetch_assoc($charnamelekeres))
			{
				$names[] = $nametomb["charname"];
			}
			if(in_array($_POST["newcharname"], $names))
			{
				$title = "A karakternév foglalt.";
			}
			else
			{
				$charname = $_POST["newcharname"];
				$charid = $_GET["charid"];
				if(1) if(!$update = mysqli_query($_SESSION["conn"], "UPDATE characters SET charname='$charname' WHERE charid='$charid'")) die("karakter átnevezése sikertelen");
				print "
					<HTML>
						<HEAD>
							<TITLE>Karakter átnevezve</TITLE>
							<link rel='stylesheet' type='text/css' href='shell_style.css'>
							<link rel='stylesheet' type='text/css' href='activity.css'>
						</HEAD>
					<BODY>
					
						<DIV class='background'>
							<DIV class='text'>Karakter átnevezve.</DIV>
							<DIV class='back'><A href='start.php'>Vissza</A></DIV>
						</DIV>
					</BODY>
					<HTML>
				";
				exit;
			}
			
			
		}
		elseif(isset($_POST["password"]))
		{
			$id = $_SESSION["id"];
			$password = $_POST["password"];
			if(!$passwordleker = mysqli_query($_SESSION["conn"], "SELECT * FROM users WHERE id='$id' AND password='$password'")) die("jelszó lekérése sikertelen");
			
			if(!mysqli_num_rows($passwordleker))
			{
				$title = "Hibás jelszó.";
			}
			else
			{
				$charid = $_GET["charid"];
				$ownerid = $_SESSION["id"];
				
				if(!$dataleker = mysqli_query($_SESSION["conn"], "SELECT squadrons, groups FROM characters WHERE charid='$charid'")) die("Karakteradatok lekérése sikertelen");
				$datatomb = mysqli_fetch_assoc($dataleker);
				$squadrons = unserialize($datatomb["squadrons"]);
				$groups = unserialize($datatomb["groups"]);
				foreach($squadrons as $ertek)
				{
					$squadronidscharacter[] = $ertek->squadronid;
					$squadronnamescharacter[] = $ertek->squadronname;
				}
				
				if(!$squadronleker = mysqli_query($_SESSION["conn"], "SELECT name, value FROM systemdata WHERE name='squadronids' OR name='squadronnames'")) die("squadronok lekérése sikertelen");
					$squadronidtomb = mysqli_fetch_assoc($squadronleker);
						$squadronids = unserialize($squadronidtomb["value"]);
						foreach($squadronidscharacter as $squadronid)
						{
							foreach($squadronids as $index=>$id)
							{
								if($id == $squadronid) unset($squadronids[$index]);
							}
						}
						$sersquadronids = serialize($squadronids);
						if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$sersquadronids' WHERE name='squadronids'")) die("squadronidk frissítése sikertelen");
					$squadronnametomb = mysqli_fetch_assoc($squadronleker);
						$squadronnames = unserialize($squadronnametomb["value"]);
						foreach($squadronnamescharacter as $squadronname)
						{
							foreach($squadronnames as $index=>$name)
							{
								if($name == $squadronname) unset($squadronnames[$index]);
							}
						}
						$sersquadronnames = serialize($squadronnames);
						if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$sersquadronnames' WHERE name='squadronnames'")) die("squadronnamek frissítése sikertelen");
				
				
				if(!$groupidsleker = mysqli_query($_SESSION["conn"], "SELECT value FROM systemdata WHERE name='groupids'")) die("Csapatidk lekérése siekrtelen");
					$groupidtomb = mysqli_fetch_assoc($groupidsleker);
						$groupids = unserialize($groupidtomb["value"]);
						foreach($groups as $groupid=>$a)
						{
							foreach($groupids as $index=>$gid)
							{
								if($groupid and $groupid == $gid) 
								{
									unset($groupids[$index]);
								}
							}
						}
						$sergroupids = serialize($groupids);
						if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$sergroupids' WHERE name='groupids'")) die("Csapatidk frissítése sikertelen");
						
					
				if(!mysqli_query($_SESSION["conn"], "DELETE FROM characters WHERE charid='$charid' AND ownerid='$ownerid'")) die("Karakter törlése sikertelen");
				if(!mysqli_query($_SESSION["conn"], "DELETE FROM equipment WHERE charid='$charid'")) die("felszerelés törlése sikertelen");
				print "
					<HTML>
						<HEAD>
							<TITLE>Karakter törölve</TITLE>
							<link rel='stylesheet' type='text/css' href='shell_style.css'>
							<link rel='stylesheet' type='text/css' href='activity.css'>
						</HEAD>
					<BODY>
					
						<DIV class='background'>
							<DIV class='text'>Karakter törölve.</DIV>
							<DIV class='back'><A href='start.php'>Vissza</A></DIV>
						</DIV>
				";
				unset($_SESSION["character"]);
				exit;
			}
			
			
			
		}
	}
?>

<HTML>
	<HEAD>
		<TITLE><?php print $title; ?></TITLE>
		<link rel="stylesheet" type="text/css" href="shell_style.css">
		<link rel="stylesheet" type="text/css" href="activity.css">
	</HEAD>
<BODY>
	<FORM method='POST'>
	<?php	
		if($_GET["activity"] == "rename")
		{
			print "
				<DIV class='background'>
					<DIV class='title'>$title</DIV>
					<DIV class='inputcontainer'>
						<DIV class='name'>Új karakternév:</DIV>
						<DIV class='input'><INPUT type='text' name='newcharname' required='required'></DIV>
					</DIV>
					<DIV class='submit'><INPUT type='submit' name='submit' value='Átnevezés'></DIV>
				</DIV>
			";
		}
		elseif($_GET["activity"] == "delete")
		{
			print "
				<DIV class='background'>
					<DIV class='title'>$title</DIV>
					<DIV class='inputcontainer'>
						<DIV class='name'>Jelszó:</DIV>
						<DIV class='input'><INPUT type='password' name='password' required='required' value='371321'></DIV>
					</DIV>
					<DIV class='submit'><INPUT type='submit' name='submit' value='Karakter törlése'></DIV>
				</DIV>
			";
		}
	?>
	</FORM>
	<DIV class='a'><A href='start.php'>Vissza</A></DIV>
</HTML>