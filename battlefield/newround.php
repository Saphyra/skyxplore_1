<?php
	include("connection.php");
	include("decode.php");
	include("charactergenerate.php");
	include("newround_function.php");
	include("newround_abilities.php");
	
	if(isset($_SESSION["gamedata"]["log"])) unset($_SESSION["gamedata"]["log"]);
	if(isset($_SESSION["gamedata"]["input"])) inputdecode($_SESSION["gamedata"]["input"]);
	
	nround();
	
	function nround()
	{
		
		allianceability();
		
		attackersreset();
		if(isset($_SESSION["gamedata"]["characters"])) $attackvalues = setattack($_SESSION["gamedata"]["characters"]);
		if(isset($attackvalues))
		{
			arsort($attackvalues);
			foreach($attackvalues as $id=>$value)
			{
				nextround($id);
			}
		}
		endgame();
	}
	
		function allianceability()
		{
			foreach($_SESSION["gamedata"]["allianceability"] as $alliance=>$abilities)
			{
				foreach($abilities as $abilityindex=>$ability)
				{
					if($ability->actualactive)
					{
						switch($alliance)
						{
							case "friend":
								$all = "Szövetséges csapat ";
							break;
							case "enemy":
								$all = "Ellenséges csapat ";
							break;
						}
						switch($abilityindex)
						{
							case "mfaa1":
								$ab = "Rajbénítás (MFAA1) képessége aktív. ";
							break;
						}
						$left = 
						
						$_SESSION["gamedata"]["log"][] = $all . $ab . "($ability->actualactive)";
						$ability->actualactive -= 1;
					}
				}
			}
		}
	
	if($_SESSION["gamedata"]["playerdead"])
	{
		for($szam = 0; $szam < 150; $szam++)
		{
			nround();
		}
		
		$_SESSION["gamedata"]["gameover"] = "draw";
		header("location:gameover.php");
		exit;
	}
	
		function attackersreset()
		{
			if(isset($_SESSION["gamedata"]["attackers"]))
			{
				foreach($_SESSION["gamedata"]["attackers"] as &$attackers)
				{
					foreach($attackers as &$value) if($value) $value -= 1;
				}
			}
		}
		function setattack($characters)
		{
			foreach($characters as $alliancename=>$alliance)
			{
				foreach($alliance as $typename=>$type)
				{
					foreach($type as $object)
					{
						$attackvalue = rand(1, 1000);
						if($type == "ships" and $_SESSION["game"]["$object->userid"]["ship"]["equipment"]["clo01"]->actualactive < 0) $attackvalue += rand(1, 500);
						$attackvalues["$object->id"] = $attackvalue;
					}
				}
			}
			arsort($attackvalues);
			return $attackvalues;
		}
		
		function nextround($id)
		{
			foreach($_SESSION["gamedata"]["characters"] as $alliance=>$ally)
			{
				foreach($ally as $typename=>$members)
				{
					foreach($members as $object)
					{
						if($object->id == $id)
						{
							switch($typename)
							{
								case "ships":
									shipround($id, $alliance);
								break;
								case "squadrons":
									squadronround($id, $alliance);
								break;
							}
						}
					}
				}
			}
		}
			
			function shipround($id, $alliance)
			{
				$character = &$_SESSION["game"]["$id"];
				$name = $character["charname"];
				
				$character["control"]["ship"]->genenergyleft = 0;
				
				$energy = energy($character);
				
				extras($character, $energy, $id);
				abilities($character, $energy, $id);
				target($character, $id, $alliance);
				attack($character, $energy, $id);
				
				if($id != "player") $character["control"]["ship"]->shieldregen = percentset($energy["energystatus"]);
				if($character["control"]["ship"]->shieldregen) shieldrecharge($character, $energy);
				
				if($character["control"]["ship"]->genenergyleft and isset($character["ship"]["battery"])) batteryrecharge($character, $energy);

				$character["control"]["ship"]->dmgreceived += 1;
			}
	
			function squadronround($id, $alliance)
			{
				
				foreach($_SESSION["gamedata"]["characters"]["$alliance"]["squadrons"] as $squadrons)
				{
					if($squadrons->id == $id)
					{
						$owner = $squadrons->owner;
						break;
					}
				}
				if(!isset($owner)) return;
				
				$character = &$_SESSION["game"]["$owner"];
				$squadron = &$character["squadrons"]["$id"];
				$squadroncontrol = &$character["control"]["$id"];
				$name = $squadron["squadron"]->squadronname;
				
				if($squadroncontrol->place == "dead") return;
				
				if(isset($squadron["battery"])) $energy = energyset($squadron["battery"]);
				else
				{
					$energy["energy"] = 0;
					$energy["energystatus"] = 0;
				}
				
				if(isset($squadron["squadronshield"])) squadronshieldrecharge($character, $squadron, $squadroncontrol, $energy, $owner);
				
				if($squadroncontrol->place == "hangar")
				{
					if(takeoff($character, $squadron, $squadroncontrol, $energy)) return;
					repair($character, $squadron, $squadroncontrol);
				}
				elseif($squadroncontrol->place == "space")
				{
					recallset($squadron, $squadroncontrol, $energy, $owner, $alliance);
					if(recall($character, $squadroncontrol, $id)) return;
					
					if(!$squadroncontrol->callbackcount)
					{
						squadrontarget($squadron, $squadroncontrol, $owner, $alliance);
						squadronattack($character, $squadron, $squadroncontrol, $energy, $owner, $id);
					}
				}
				
				$squadroncontrol->dmgreceived += 1;
			}

	function endgame()
	{
		if(!isset($_SESSION["gamedata"]["characters"]["friend"]["ships"]) or !count($_SESSION["gamedata"]["characters"]["friend"]["ships"]))
		{
			$_SESSION["gamedata"]["gameover"] = "loose";
			header("location:gameover.php");
			exit;
		}
		if(!isset($_SESSION["gamedata"]["characters"]["enemy"]["ships"]) or !count($_SESSION["gamedata"]["characters"]["enemy"]["ships"]))
		{
			$_SESSION["gamedata"]["gameover"] = "win";
			header("location:gameover.php");
			exit;
		}
	}
	
	if(0)exit;
	if(1)
	{
		unset($_SESSION["input"]);
		if(1) unset($_SESSION["gamedata"]["playeruse"]);
		if(1) header("location:battlefield.php");
	}
?>