<?php
	include("connection.php");
	
	if(isset($_SESSION["groups"]["action"]))
	{
		switch($_SESSION["groups"]["action"]["action"])
		{
			case "othergroup":
				$squadronid = $_SESSION["groups"]["action"]["squadronid"];
				$squadron = &$_SESSION["character"]["squadrons"]["$squadronid"];
				$oldgroup = &$_SESSION["character"]["groups"]["$squadron->group"];
				$newgroupid = $_SESSION["groups"]["action"]["group"];
				$newgroup = &$_SESSION["character"]["groups"]["$newgroupid"];
				
				$oldgroupmembers = unserialize($oldgroup->members);
				foreach($oldgroupmembers as $index=>$member)
				{
					if($member == $squadronid) unset($oldgroupmembers[$index]);
				}
				if(count($oldgroupmembers)) $oldgroup->members = serialize($oldgroupmembers);
				else $oldgroup->members = 0;
				
				$oldgroup->membernum -= 1;
				
				$squadron->group = $newgroupid;
				
				if($newgroup->members) $newgroupmembers = unserialize($newgroup->members);
				$newgroupmembers[] = $squadronid;
				$newgroup->members = serialize($newgroupmembers);
				$newgroup->membernum += 1;
			break;
			case "groupdelete":
				$groupid = $_SESSION["groups"]["action"]["groupid"];
				$group = $_SESSION["character"]["groups"]["$groupid"];
				if($group->members)
				{
					$groupmembers = unserialize($group->members);
					$newgroup = &$_SESSION["character"]["groups"]["no"];
					if($newgroup->members) $newgroupmembers = unserialize($newgroup->members);
					foreach($groupmembers as $squadronid)
					{
						$_SESSION["character"]["squadrons"]["$squadronid"]->group = "no";
						$newgroupmembers[] = $squadronid;
						$newgroup->membernum +=1;
					}
					$newgroup->members = serialize($newgroupmembers);
				}
				
				if(!$groupidsleker = mysqli_query($_SESSION["conn"], "SELECT value FROM systemdata WHERE name='groupids'")) die("Csapatidk lekérése sikertelen");
				$groupidstomb = mysqli_fetch_assoc($groupidsleker);
				$groupids = unserialize($groupidstomb["value"]);
				foreach($groupids as $index=>$id)
				{
					if($id == $groupid)
					{
						unset($groupids[$index]);
						break;
					}
				}
				$upgroupids = serialize($groupids);
				if(!mysqli_query($_SESSION["conn"], "UPDATE systemdata SET value='$upgroupids' WHERE name='groupids'")) die("Csapatidk feltöltése sikeretlen");
				
				unset($_SESSION["character"]["groups"]["$groupid"]);
			break;
		}
		save();
		unset($_SESSION["groups"]["action"]);
		$from = $_SESSION["from"];
		if(1) header("location:$from");
	}
?>