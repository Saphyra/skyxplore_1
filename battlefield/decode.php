<?php
	function inputdecode($input)
	{
		if(0)
		{
			kiir($input);
			exit;
		}
		foreach($input as $name=>$value)
		{
			$data = explode("&", $name);
			foreach($data as $datapart)
			{
				$part = explode("=", $datapart);
				$result["$part[0]"] = $part[1];
			}
			if(isset($result["id"]))
			{
				$set = new emptyclass;
				foreach($result as $type=>$typevalue) $set->$type = $typevalue;
				$set->value = $value;
				if($set->control == "return")
				{
					$valuedata = explode("&", $value);
					$vpart = explode("=", $valuedata[0]);
					$_SESSION["game"]["player"]["control"]["$set->id"]->$vpart[0] = $vpart[1];
					$vpart = explode("=", $valuedata[1]);
					$_SESSION["game"]["player"]["control"]["$set->id"]->$vpart[0] = $vpart[1];
				}
				else
				{
					$control = $set->control;
					$_SESSION["game"]["player"]["control"]["$set->id"]->$control = $set->value;
				}
			}
			if(isset($result["control"]))
			{
				if($result["control"] == "use")
				{
					$itemid = $result["itemid"];
					$_SESSION["gamedata"]["playeruse"]["$itemid"] = 1;
				}
				elseif($result["control"] == "target")
				{
					$itemid = $result["itemid"];
					
					$style = ($result["style"] == "skill") ? "skilltarget" : "equipmenttarget";
					
					$_SESSION["gamedata"]["playeruse"]["$style"]["$itemid"] = $value;
				}
			}
			unset($result);
			if(1) unset($_SESSION["gamedata"]["input"]["$name"]);
		}
		
		if(0)
		{
			kiir($_SESSION["gamedata"]["playeruse"]);
			exit;
		}
	}
?>