<?php
	include("classes.php");
	include("shell_function.php");
	session_start();
	$_SESSION["conn"] = mysqli_connect("localhost", "root", "", "skyxplore");
	if(1)
	{
		$_SESSION["status"] = "active";
		$_SESSION["id"] = "idu59649566";
		$_SESSION["password"] = "371321";
	}
	if($_SESSION["status"] != "active")
	{
		$_SESSION["from"] = "http://www.saphyra.pe.hu/skyxplore/index.php";
		header("location:http://www.saphyra.pe.hu/login.php");
		exit;
	}
?>