<?php
	include("connection.php");
	
	if(isset($_SESSION["construction"]["action"]))
	{
		$_SESSION["character"]["credit"] -= $_SESSION["construction"]["action"]["cost"];
		$itemid = $_SESSION["construction"]["action"]["itemid"];
		$itemdata = $_SESSION["data"]["items"]["$itemid"];
		$_SESSION["character"]["construction"]["$itemid"] -= $itemdata->construction;
		if($_SESSION["character"]["construction"]["$itemid"] < 0) $_SESSION["character"]["construction"]["$itemid"] = 0;
		
		$_SESSION["character"]["equipment"][] = new equipment($itemid, 0, "hangar");
		
		save();
		unset($_SESSION["construction"]["action"]);
		header("location:construction.php");
	}
?>