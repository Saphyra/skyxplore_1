<?php
	include("connection.php");
	
	switch($_SESSION["gamedata"]["gameover"])
	{
		case "win":
			$title = "Győzelem!";
			$multiplicator = 1.5;
		break;
		case "loose":
			$title = "Vereség";
			$multiplicator = 0.5;
		break;
		case "draw":
			$title = "Döntetlen!";
			$multiplicator = 1;
		break;
	}
?>

<HTML>
	<HEAD>
		<TITLE><?php print $title; ?> - Játék vége</TITLE>
		<link rel="stylesheet" type="text/css" href="battlefield_style.css">
		<link rel="stylesheet" type="text/css" href="gameover.css">
	</HEAD>
<BODY>
	<DIV class ='container'>
		<DIV class='containertitle'><?php print $title; ?></DIV>
		<?php
			$rank = rankset("player");
			
			foreach($_SESSION["gamedata"]["userdata"] as $user=>$userdata)
			{
				$scores["$user"] = $userdata->score;
			}
			arsort($scores);
			
			foreach($scores as $id=>$score)
			{
				$ids[] = $id;
			}
			
			$place = array_search("player", $ids) + 1;
			
			print "
				<DIV class='lootcontainer'>
					<DIV class='loottitle'>Eredmény: $rank (Helyezés: $place)</DIV>
				</DIV>
			";
			
			loot();
		?>
	</DIV>
	
	<DIV class='back'>
		<A class='back' href='http://localhost/workdir/online/skyxplore/hangar.php'>Vissza</A>
	</DIV>
</BODY>
</HTML>

<?php
	function loot()
	{
		print "<DIV class='lootcontainer'>";
			print "<DIV class='loottitle'>Zsákmány:</DIV>";
			
		print "</DIV>";
	}
?>