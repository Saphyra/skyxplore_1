<?php
	include("connection.php");
?>

<?php
	if(isset($_SESSION["shop"]["action"]))
	{
		switch($_SESSION["shop"]["action"]["activity"])
		{
			case "itembuy":
				for($szam = 0; $szam < $_SESSION["shop"]["action"]["amount"]; $szam++)
				{
					$itemid = $_SESSION["shop"]["action"]["itemid"];
					$_SESSION["character"]["equipment"][] = new equipment($itemid, 0, "hangar");
					$_SESSION["character"]["credit"] -= $_SESSION["data"]["items"]["$itemid"]->buyprice;
				}
				
			break;
			case "ammobuy":
				$amount = $_SESSION["shop"]["action"]["amount"];
				$itemid = $_SESSION["shop"]["action"]["itemid"];
				$itemdata = $_SESSION["data"]["items"]["$itemid"];
				
				$cost = $amount * $itemdata->buyprice;
				
				$_SESSION["character"]["credit"] -= $cost;
				
				$comp = 0;
				foreach($_SESSION["character"]["ammo"] as &$ammo)
				{
					if($ammo->itemid == $itemid and $ammo->place == "hangar")
					{
						$ammo->amount += $amount;
						$comp = 1;
						break;
					}
				}
				if(!$comp)
				{
					$_SESSION["character"]["ammo"][] = new ammo($itemid, "hangar", 0, $amount);
				}
			break;
			case "skillfinish":
				$itemid = $_SESSION["shop"]["action"]["itemid"];
				$cost = $_SESSION["shop"]["action"]["upgradeprice"];
				$partsneed = $_SESSION["shop"]["action"]["partsneed"];
				$parts = $_SESSION["shop"]["action"]["parts"];
				
				$skill = &$_SESSION["character"]["skill"]["$itemid"];
				
				$_SESSION["character"]["diamond"] -= $cost;
				$skill->level += 1;
				$skill->parts -= $partsneed;
				if($skill->parts < 0) $skill->parts = 0;
			break;
		}
		
		if(1) save();
		unset($_SESSION["shop"]["action"]);
		if(1) header("location:shop.php");
	}
?>