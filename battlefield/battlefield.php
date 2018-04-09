<?php
	include("connection.php");
	include("playership.php");
	include("botship.php");
	
	if(!empty($_POST))
	{
		foreach($_POST as $name=>$ertek)
		{
			$_SESSION["gamedata"]["input"]["$name"] = $ertek;
			print "$name: $ertek<BR>";
		}
		header("location:newround.php");
	}
	if(isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"])) $enemynum = count($_SESSION["gamedata"]["characters"]["enemy"]["ships"]);
	else $enemynum = 0;
	if(isset($_SESSION["gamedata"]["characters"]["friend"]["ships"])) $friendnum = count($_SESSION["gamedata"]["characters"]["friend"]["ships"]);
	else $friendnum = 0;
?>


<HTML>
	<HEAD>
		<TITLE>Űr - SkyXplore</TITLE>
		<link rel="stylesheet" type="text/css" href="battlefield_style.css">
		<link rel="stylesheet" type="text/css" href="battlefield.css">
	</HEAD>
<BODY>
	<FORM method='POST'>
	<TABLE class='container'>
		<TR>
			<TD class='shipcontainer'>
			
				<DIV class='container'>
					<DIV class='containertitle'>
						<?php
							print $_SESSION["game"]["player"]["charname"];
							print "<BR>";
							$rank = rankset("player");
							print "<DIV class='rank'>$rank</DIV>";
						?>
					</DIV>
					<?php playership($_SESSION["game"]["player"]); ?>
				</DIV>
			</TD>
			<TD class='container'>
				<DIV class='container'>
					<DIV class='containertitle'>Szövetségesek <?php print "($friendnum)"; ?></DIV>
					<?php
						if(isset($_SESSION["gamedata"]["characters"]["friend"]["ships"]))
						{
							foreach($_SESSION["gamedata"]["characters"]["friend"]["ships"] as $character)
							{
								if($character->id != "player") botship($_SESSION["game"]["$character->id"]);
							}
						}
						
					?>
				</DIV>
			</TD>
			<TD class='container'>
				<DIV class='container'>
					<DIV class='containertitle'>Ellenségek <?php print "($enemynum)"; ?></DIV>
					<?php
						if(isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"]))
						{
							foreach($_SESSION["gamedata"]["characters"]["enemy"]["ships"] as $character)
							{
								botship($_SESSION["game"]["$character->id"]);
							}
						}
						
					?>
				</DIV>
			</TD>
	</TABLE>
	
	<DIV class='functionbar'>
		<DIV class='nextround'><INPUT class='nextround' type='submit' name='nextround=attack' value='Támadás'></DIV>
		<?php logload(); ?>
		<DIV class='back'><A class='back' href='http://localhost/workdir/online/skyxplore/hangar.php'>Vissza</A></DIV>
	</DIV>
	</FORM>
</BODY>
</HTML>

<?php
	function logload()
	{
		if(isset($_SESSION["gamedata"]["log"]))
		{
			krsort($_SESSION["gamedata"]["log"]);
			print "<DIV class='log'>";
			
			foreach($_SESSION["gamedata"]["log"] as $index=>$log)
			{
				$index++;
				print "<DIV>$index: $log</DIV>";
			}
			
			print "</DIV>";
		}
	}
?>