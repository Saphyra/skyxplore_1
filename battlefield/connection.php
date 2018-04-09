﻿<?php
	include("../connection.php");
	
	function rankset($id)
	{
		$score = $_SESSION["gamedata"]["userdata"]["$id"]->score;
		$level = $_SESSION["gamedata"]["userdata"]["$id"]->level;
		
		if($score <= 1) $rank = "LvL1 (Közlegény)";
		elseif($score > 1 and $score <= 9) $rank = "LvL2 (Őrvezető)";
		elseif($score > 9 and $score <= 25) $rank = "LvL3 (Tizedes)";
		elseif($score > 25 and $score <= 49) $rank = "LvL4 (Szakaszvezető)";
		elseif($score > 49 and $score <= 81) $rank = "LvL5 (Őrmester)";
		elseif($score > 81 and $score <= 121) $rank = "LvL6 (Törzsőrmester)";
		elseif($score > 121 and $score <= 169) $rank = "LvL7 (Főtörzsőrmester)";
		elseif($score > 169 and $score <= 225) $rank = "LvL8 (Zászlós)";
		elseif($score > 225 and $score <= 289) $rank = "LvL9 (Törzszászlós)";
		elseif($score > 289 and $score <= 361) $rank = "LvL10 (Főtörzszászlós)";
		elseif($score > 361 and $score <= 441) $rank = "LvL11 (Hadnagy)";
		elseif($score > 441 and $score <= 529) $rank = "LvL12 (Főhadnagy)";
		elseif($score > 529 and $score <= 625) $rank = "LvL13 (Százados)";
		elseif($score > 625 and $score <= 729) $rank = "LvL14 (Őrnagy)";
		elseif($score > 729 and $score <= 841) $rank = "LvL15 (Alezredes)";
		elseif($score > 841 and $score <= 961) $rank = "LvL16 (Ezredes)";
		elseif($score > 961 and $score <= 1089) $rank = "LvL17 (Dandártábornok)";
		elseif($score > 1089 and $score <= 1225) $rank = "LvL18 (Vezérőrnagy)";
		elseif($score > 1225 and $score <= 1369) $rank = "LvL19 (Altábornagy)";
		elseif($score > 1369) $rank = "LvL20 (Vezérezredes)";
		
		settype($score, "integer");
		return $rank . " - $score pont";
	}
?>