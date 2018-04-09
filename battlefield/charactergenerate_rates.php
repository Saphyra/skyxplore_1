<?php
	function botlevelset($level)
	{
		$rate = rand(1, 1000);
		switch($level)
		{
			case 1:
				$level = 1;
			break;
			case 2:
				if($rate < 700) $level = 2;
				elseif($rate >= 700 and $level > 850) $level = 1;
				else $level = 3;
			break;
			case 3:
				if($rate < 700) $level = 3;
				elseif($rate >= 700 and $level > 850) $level = 2;
				else $level = 4;
			break;
			case 4:
				if($rate < 700) $level = 4;
				elseif($rate >= 700 and $level > 850) $level = 3;
				else $level = 5;
			break;
			case 5:
				if($rate < 700) $level = 5;
				elseif($rate >= 700 and $level > 850) $level = 4;
				else $level = 6;
			break;
			case 6:
				if($rate < 700) $level = 6;
				elseif($rate >= 700 and $level > 850) $level = 5;
				else $level = 7;
			break;
			case 7:
				if($rate < 700) $level = 7;
				elseif($rate >= 700 and $level > 850) $level = 8;
				else $level = 6;
			break;
			case 8:
				if($rate < 700) $level = 8;
				elseif($rate >= 700 and $level > 850) $level = 9;
				else $level = 7;
			break;
			case 9:
				if($rate < 700) $level = 9;
				elseif($rate >= 700 and $level > 850) $level = 10;
				else $level = 8;
			break;
			case 10:
				if($rate < 700) $level = 10;
				else $level = 9;
			break;
		}
		return $level;
	}
	
	function equipmentrate3($level)
	{
		switch($level)
		{
			case 1:
				$max = 1;
			break;
			case 2:
				$max = 3;
			break;
			case 3:
				$max = 7;
			break;
			default:
				$max = 0;
			break;
		}
		
		$rate = rand(0, $max);
		
		if(!$rate) $level = 0;
		elseif($rate > 0 and $rate <= 1) $level = 1;
		elseif($rate > 1 and $rate <= 3) $level = 2;
		elseif($rate > 3 and $rate <= 7) $level = 3;
		return $level;
	}
	
	function equipmentrate10($level)
	{
		switch($level)
		{
			case 1:
				$max = 4;
			break;
			case 2:
				$max = 9;
			break;
			case 3:
				$max = 18;
			break;
			case 4:
				$max = 36;
			break;
			case 5:
				$max = 72;
			break;
			case 6:
				$max = 144;
			break;
			case 7:
				$max = 288;
			break;
			case 8:
				$max = 596;
			break;
			case 9:
				$max = 1192;
			break;
			case 10:
				$max = 2384;
			break;
		}
		
		$rate = rand(0, $max);
		
		if(!$rate) return 0;
		elseif($rate > 0 and $rate <= 4) return 1;
		elseif($rate > 4 and $rate <= 9) return 2;
		elseif($rate > 9 and $rate <= 18) return 3;
		elseif($rate > 18 and $rate <= 36) return 4;
		elseif($rate > 36 and $rate <= 72) return 5;
		elseif($rate > 72 and $rate <= 144) return 6;
		elseif($rate > 144 and $rate <= 288) return 7;
		elseif($rate > 288 and $rate <= 596) return 8;
		elseif($rate > 596 and $rate <= 1192) return 9;
		elseif($rate > 1192 and $rate <= 2384) return 10;
	}
	
	function repairbotrate($level)
	{
		$rate = rand(1, $level + 1);
		
		if($rate > 0 and $rate <= 1) return 1;
		elseif($rate > 2 and $rate <= 5) return 2;
		elseif($rate > 6 and $rate <= 11) return 3;
	}
	
	function skillrate10($level)
	{
		$rate = rand(1, 1000);
		switch($level)
		{
			case 1:
				if($rate <= 250) return 0;
				elseif($rate > 250 and $rate <= 550) return 1;
				elseif($rate > 550 and $rate <= 750) return 2;
				elseif($rate > 750 and $rate <= 850) return 3;
				elseif($rate > 850 and $rate <= 925) return 4;
				elseif($rate > 925 and $rate <= 950) return 5;
				elseif($rate > 950 and $rate <= 970) return 6;
				elseif($rate > 970 and $rate <= 985) return 7;
				elseif($rate > 985 and $rate <= 995) return 8;
				elseif($rate > 995 and $rate <= 999) return 9;
				elseif($rate > 999 and $rate <= 1000) return 10;
			break;
			case 2:
				if($rate <= 150) return 0;
				elseif($rate > 150 and $rate <= 350) return 1;
				elseif($rate > 350 and $rate <= 650) return 2;
				elseif($rate > 650 and $rate <= 800) return 3;
				elseif($rate > 800 and $rate <= 900) return 4;
				elseif($rate > 900 and $rate <= 945) return 5;
				elseif($rate > 945 and $rate <= 965) return 6;
				elseif($rate > 965 and $rate <= 980) return 7;
				elseif($rate > 980 and $rate <= 990) return 8;
				elseif($rate > 990 and $rate <= 995) return 9;
				elseif($rate > 995 and $rate <= 1000) return 10;
			break;
			case 3:
				if($rate <= 150) return 0;
				elseif($rate > 150 and $rate <= 250) return 1;
				elseif($rate > 250 and $rate <= 400) return 2;
				elseif($rate > 400 and $rate <= 700) return 3;
				elseif($rate > 700 and $rate <= 850) return 4;
				elseif($rate > 850 and $rate <= 900) return 5;
				elseif($rate > 900 and $rate <= 930) return 6;
				elseif($rate > 930 and $rate <= 955) return 7;
				elseif($rate > 955 and $rate <= 975) return 8;
				elseif($rate > 975 and $rate <= 990) return 9;
				elseif($rate > 990 and $rate <= 1000) return 10;
			break;
			case 4:
				if($rate <= 75) return 0;
				elseif($rate > 75 and $rate <= 175) return 1;
				elseif($rate > 175 and $rate <= 300) return 2;
				elseif($rate > 300 and $rate <= 450) return 3;
				elseif($rate > 450 and $rate <= 750) return 4;
				elseif($rate > 750 and $rate <= 850) return 5;
				elseif($rate > 850 and $rate <= 890) return 6;
				elseif($rate > 890 and $rate <= 925) return 7;
				elseif($rate > 925 and $rate <= 955) return 8;
				elseif($rate > 955 and $rate <= 980) return 9;
				elseif($rate > 980 and $rate <= 1000) return 10;
			break;
			case 5:
				if($rate <= 25) return 0;
				elseif($rate > 25 and $rate <= 75) return 1;
				elseif($rate > 75 and $rate <= 150) return 2;
				elseif($rate > 150 and $rate <= 250) return 3;
				elseif($rate > 250 and $rate <= 350) return 4;
				elseif($rate > 350 and $rate <= 650) return 5;
				elseif($rate > 650 and $rate <= 750) return 6;
				elseif($rate > 750 and $rate <= 850) return 7;
				elseif($rate > 850 and $rate <= 920) return 8;
				elseif($rate > 920 and $rate <= 960) return 9;
				elseif($rate > 960 and $rate <= 1000) return 10;
			break;
			case 6:
				if($rate <= 10) return 0;
				elseif($rate > 10 and $rate <= 25) return 1;
				elseif($rate > 25 and $rate <= 50) return 2;
				elseif($rate > 50 and $rate <= 100) return 3;
				elseif($rate > 100 and $rate <= 175) return 4;
				elseif($rate > 175 and $rate <= 275) return 5;
				elseif($rate > 275 and $rate <= 600) return 6;
				elseif($rate > 600 and $rate <= 800) return 7;
				elseif($rate > 800 and $rate <= 900) return 8;
				elseif($rate > 900 and $rate <= 950) return 9;
				elseif($rate > 950 and $rate <= 1000) return 10;
			break;
			case 7:
				if($rate <= 5) return 0;
				elseif($rate > 5 and $rate <= 10) return 1;
				elseif($rate > 10 and $rate <= 20) return 2;
				elseif($rate > 20 and $rate <= 35) return 3;
				elseif($rate > 35 and $rate <= 55) return 4;
				elseif($rate > 55 and $rate <= 120) return 5;
				elseif($rate > 120 and $rate <= 250) return 6;
				elseif($rate > 250 and $rate <= 550) return 7;
				elseif($rate > 550 and $rate <= 750) return 8;
				elseif($rate > 750 and $rate <= 900) return 9;
				elseif($rate > 900 and $rate <= 1000) return 10;
			break;
			case 8:
				if($rate <= 5) return 0;
				elseif($rate > 10 and $rate <= 15) return 1;
				elseif($rate > 25 and $rate <= 50) return 2;
				elseif($rate > 50 and $rate <= 85) return 3;
				elseif($rate > 85 and $rate <= 135) return 4;
				elseif($rate > 135 and $rate <= 170) return 5;
				elseif($rate > 170 and $rate <= 225) return 6;
				elseif($rate > 225 and $rate <= 400) return 7;
				elseif($rate > 400 and $rate <= 700) return 8;
				elseif($rate > 700 and $rate <= 850) return 9;
				elseif($rate > 850 and $rate <= 1000) return 10;
			break;
			case 9:
				if($rate <= 5) return 0;
				elseif($rate > 5 and $rate <= 15) return 1;
				elseif($rate > 15 and $rate <= 25) return 2;
				elseif($rate > 25 and $rate <= 45) return 3;
				elseif($rate > 45 and $rate <= 75) return 4;
				elseif($rate > 75 and $rate <= 135) return 5;
				elseif($rate > 135 and $rate <= 210) return 6;
				elseif($rate > 210 and $rate <= 300) return 7;
				elseif($rate > 300 and $rate <= 500) return 8;
				elseif($rate > 500 and $rate <= 800) return 9;
				elseif($rate > 800 and $rate <= 1000) return 10;
			break;
			case 10:
				if($rate <= 1) return 0;
				elseif($rate > 1 and $rate <= 5) return 1;
				elseif($rate > 5 and $rate <= 10) return 2;
				elseif($rate > 10 and $rate <= 20) return 3;
				elseif($rate > 20 and $rate <= 40) return 4;
				elseif($rate > 40 and $rate <= 80) return 5;
				elseif($rate > 80 and $rate <= 160) return 6;
				elseif($rate > 160 and $rate <= 320) return 7;
				elseif($rate > 320 and $rate <= 500) return 8;
				elseif($rate > 500 and $rate <= 700) return 9;
				elseif($rate > 700 and $rate <= 1000) return 10;
			break;
		}
	}
	
	function skillrate5($level)
	{
		$rate = rand(1, 1000);
		switch($level)
		{
			case 1:
				
				if($rate <= 400) return 0;
				elseif($rate > 400 and $rate <= 700) return 1;
				elseif($rate > 700 and $rate <= 850) return 2;
				elseif($rate > 850 and $rate <= 950) return 3;
				elseif($rate > 950 and $rate <= 999) return 4;
				elseif($rate > 999 and $rate <= 1000) return 5;
			break;
			case 2:
				if($rate <= 300) return 0;
				elseif($rate > 300 and $rate <= 600) return 1;
				elseif($rate > 600 and $rate <= 800) return 2;
				elseif($rate > 800 and $rate <= 950) return 3;
				elseif($rate > 950 and $rate <= 995) return 4;
				elseif($rate > 995 and $rate <= 1000) return 5;
			break;
			case 3:
				if($rate <= 200) return 0;
				elseif($rate > 200 and $rate <= 400) return 1;
				elseif($rate > 400 and $rate <= 700) return 2;
				elseif($rate > 700 and $rate <= 900) return 3;
				elseif($rate > 900 and $rate <= 990) return 4;
				elseif($rate > 990 and $rate <= 1000) return 5;
			break;
			case 4:
				if($rate <= 175) return 0;
				elseif($rate > 175 and $rate <= 350) return 1;
				elseif($rate > 350 and $rate <= 550) return 2;
				elseif($rate > 550 and $rate <= 850) return 3;
				elseif($rate > 850 and $rate <= 985) return 4;
				elseif($rate > 985 and $rate <= 1000) return 5;
			break;
			case 5:
				if($rate <= 150) return 0;
				elseif($rate > 150 and $rate <= 300) return 1;
				elseif($rate > 300 and $rate <= 600) return 2;
				elseif($rate > 600 and $rate <= 900) return 3;
				elseif($rate > 900 and $rate <= 975) return 4;
				elseif($rate > 975 and $rate <= 1000) return 5;
			break;
			case 6:
				if($rate <= 125) return 0;
				elseif($rate > 125 and $rate <= 250) return 1;
				elseif($rate > 250 and $rate <= 400) return 2;
				elseif($rate > 400 and $rate <= 800) return 3;
				elseif($rate > 800 and $rate <= 950) return 4;
				elseif($rate > 950 and $rate <= 1000) return 5;
			break;
			case 7:
				if($rate <= 100) return 0;
				elseif($rate > 100 and $rate <= 225) return 1;
				elseif($rate > 225 and $rate <= 400) return 2;
				elseif($rate > 400 and $rate <= 750) return 3;
				elseif($rate > 750 and $rate <= 900) return 4;
				elseif($rate > 900 and $rate <= 1000) return 5;
			break;
			case 8:
				if($rate <= 75) return 0;
				elseif($rate > 75 and $rate <= 125) return 1;
				elseif($rate > 125 and $rate <= 250) return 2;
				elseif($rate > 250 and $rate <= 500) return 3;
				elseif($rate > 500 and $rate <= 850) return 4;
				elseif($rate > 850 and $rate <= 1000) return 5;
			break;
			case 9:
				if($rate <= 50) return 0;
				elseif($rate > 50 and $rate <= 100) return 1;
				elseif($rate > 100 and $rate <= 200) return 2;
				elseif($rate > 200 and $rate <= 350) return 3;
				elseif($rate > 350 and $rate <= 750) return 4;
				elseif($rate > 750 and $rate <= 1000) return 5;
			break;
			case 10:
				if($rate <= 10) return 0;
				elseif($rate > 10 and $rate <= 50) return 1;
				elseif($rate > 50 and $rate <= 150) return 2;
				elseif($rate > 150 and $rate <= 300) return 3;
				elseif($rate > 300 and $rate <= 600) return 4;
				elseif($rate > 600 and $rate <= 1000) return 5;
			break;
		}
	}
	
	function ammorate()
	{
		$rate = rand(0, 1000);
		if(!$rate) return 0;
		elseif($rate > 1 and $rate <= 5) return 0.1;
		elseif($rate > 5 and $rate <= 10) return 0.2;
		elseif($rate > 10 and $rate <= 20) return 0.3;
		elseif($rate > 20 and $rate <= 40) return 0.4;
		elseif($rate > 40 and $rate <= 80) return 0.5;
		elseif($rate > 80 and $rate <= 160) return 0.6;
		elseif($rate > 160 and $rate <= 320) return 0.7;
		elseif($rate > 320 and $rate <= 500) return 0.8;
		elseif($rate > 500 and $rate <= 700) return 0.9;
		elseif($rate > 700 and $rate <= 1000) return 1;
	}
	
	function ammolevelrate()
	{
		$rate = rand(1, 20);
		
		if($rate <= 1) return 3;
		elseif($rate > 1 and $rate <= 5) return 2;
		elseif($rate > 5 and $rate <= 20) return 1;
	}
	
	function extrarate($level)
	{
		switch($level)
		{
			case 1:
				$rate = 50;
			break;
			case 2:
				$rate = 55;
			break;
			case 3:
				$rate = 60;
			break;
			case 4:
				$rate = 65;
			break;
			case 5:
				$rate = 70;
			break;
			case 6:
				$rate = 75;
			break;
			case 7:
				$rate = 80;
			break;
			case 8:
				$rate = 85;
			break;
			case 9:
				$rate = 90;
			break;
			case 10:
				$rate = 95;
			break;
		}
		
		return (rand(0, 100) < $rate) ? 1 : 0;
	}
?>