<?php
	include("connection.php");
	
	score();
	print "<HR>";
	equippedfail();
	print "<HR>";
	equipments();
	print "<HR>";
	skill();
	
	
	function score()
	{
		foreach($_SESSION["gamedata"]["userdata"] as $id=>$userdata)
		{
			$scores["$id"] = $userdata->score;
		}
		arsort($scores);
		
		foreach($scores as $name=>$score)
		{
			$score /= pow($_SESSION["gamedata"]["userdata"]["$name"]->level, 0.5);
			$it = $_SESSION["gamedata"]["userdata"]["$name"]->company;
			$charname = $_SESSION["gamedata"]["userdata"]["$name"]->charname;
			$rank = rankset($name);
			print "($it) $name - $charname: $score - $rank<BR>";
		}
	}
	
	function equipments()
	{
		foreach($_SESSION["game"] as $character)
		{
			foreach($character["ship"]["equipment"] as $index=>$equipment)
			{
				if($equipment->equipped)
				{
					if(isset($equipped["$index"]))
					{
						$equipped["$index"]->num += 1;
					}
					else
					{
						$equipped["$index"] = new emptyclass;
						$equipped["$index"]->name = $_SESSION["data"]["items"]["$index"]->name;
						$equipped["$index"]->num = 1;
					}
				}
			}
		}
		
		if(isset($equipped)) kiir($equipped);
		else print "<DIV>Nincs felszerelés</DIV>";
	}
	
	function skill()
	{
		foreach($_SESSION["game"] as $character)
		{
			foreach($character["skill"] as $skillindex=>$skill)
			{
				if($skill->level and $skill->owner == $character["ship"]["ship"][0]->company)
				{
					if(isset($equipped["$skillindex"]["$skill->level"]))
					{
						$equipped["$skillindex"]["$skill->level"]->num += 1;
					}
					else
					{
						$equipped["$skillindex"]["$skill->level"] = new emptyclass;
						$equipped["$skillindex"]["$skill->level"]->name = $_SESSION["data"]["items"]["$skillindex"]->name;
						$equipped["$skillindex"]["$skill->level"]->num = 1;
					}
				}
			}
		}
		
		if(isset($equipped)) kiir($equipped);
		else print "<DIV>Nincs képesség</DIV>";
	}
	
	function equippedfail()
	{
		foreach($_SESSION["game"] as $id=>$player)
		{
			foreach($player["ship"]["equipment"] as $eid=>$equipment)
			{
				if(!property_exists($equipment, "equipped")) print "$id, $eid<BR>";
			}
		}
	}
	
?>