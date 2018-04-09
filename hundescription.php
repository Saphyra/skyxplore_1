<?php
	function hundescription($itemid)
	{
		$itemdata = $_SESSION["data"]["items"]["$itemid"];
		
		$desc = "";
		
		if(property_exists($itemdata, "equipable"))
		{
			switch($itemdata->equipable)
			{
				case "ship": $equipable = "Hajó"; break;
				case "squadron": $equipable = "Raj"; break;
				case "both": $equipable = "Hajó, Raj"; break;
			}
		}
		
		switch($itemdata->type)
		{
			case "ship":
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Megbízó: $itemdata->companyname</DIV>
					<DIV class='property'>Típus: Hajó; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: $equipable</DIV>
					<DIV class='property'>Alap életerő: $itemdata->corehull</DIV>
					<DIV class='property'>Burkolat: $itemdata->hullslot ($itemdata->maxhulllevel); Pajzs: $itemdata->shieldslot ($itemdata->maxshieldlevel)</DIV>
					<DIV class='property'>Ágyú: $itemdata->cannonslot ($itemdata->maxcannonlevel); Rakéta: $itemdata->rocketslot ($itemdata->maxrocketlevel); Gépágyú: $itemdata->rifleslot ($itemdata->maxriflelevel)</DIV>
					<DIV class='property'>Generátor: $itemdata->generatorslot ($itemdata->maxgeneratorlevel); Akkumulátor: $itemdata->batteryslot ($itemdata->maxbatterylevel)</DIV>
					<DIV class='property'>Hangár: $itemdata->hangarslot ($itemdata->maxhangarlevel); Max raj szint: $itemdata->maxsquadronlevel</DIV>
					<DIV class='property'>Felszerelés: $itemdata->equipmentslot; Bővítő: $itemdata->extenderslot ( $itemdata->maxextenderlevel)</DIV>
					<DIV class='property'>Lőszerraktár: $itemdata->basicammostorage; Raktér: $itemdata->basiccargo</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
					<DIV class='hint'>A zárójelbe tett értékek a tárgy maximális szintjét jelzik.</DIV>
				";
			break;
			case "squadron":
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Raj; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: $equipable</DIV>
					<DIV class='property'>Alap életerő: $itemdata->corehull</DIV>
					<DIV class='property'>Burkolat: $itemdata->hullslot ($itemdata->maxhulllevel); Pajzs: $itemdata->shieldslot ($itemdata->maxshieldlevel)</DIV>
					<DIV class/'property'>Fegyver: $itemdata->weaponslot ($itemdata->maxweaponlevel); Akkumulátor: $itemdata->batteryslot ($itemdata->maxbatterylevel)</DIV>
					<DIV class='property'>Lőszerraktár: $itemdata->basicammostorage</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
					<DIV class='hint'>A zárójelbe tett értékek a tárgy maximális szintjét jelzik.</DIV>
				";
			break;
			case "weapon":
				switch($itemdata->itemtype)
				{
					case "rocketlauncher":
						$hulldamage = $itemdata->rockethulldamage;
						$shielddamage = $itemdata->rocketshielddamage;
						$squadrondamage = $itemdata->squadrondamage;
					break;
					case "sablauncher":
						$hulldamage = $itemdata->rockethulldamage;
						$shielddamage = $itemdata->rocketshielddamage;
						$squadrondamage = $itemdata->squadrondamage;
					break;
					default:
						$hulldamage = $itemdata->hulldamage;
						$shielddamage = $itemdata->shielddamage;
						$squadrondamage = $itemdata->squadrondamage;
					break;
				}
			
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Fegyver; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: $equipable</DIV>
					<DIV class='property'>Sebzés: $hulldamage (Burkolat); $shielddamage (Pajzs); $squadrondamage (Raj)</DIV>
					<DIV class='property'>Lőszerhasználat: $itemdata->ammousage; Lőszer: $itemdata->ammoname</DIV>
					<DIV class='property'>Energiafelhasználás: $itemdata->energyusage</DIV>
					<DIV class='property'>Pontosság: $itemdata->accuracy; Újratöltési idő: $itemdata->reloadtime</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "shield";
				switch($itemdata->equipable)
				{
					case "ship": $equipable = "Hajó"; break;
					case "squadron": $equipable = "Raj"; break;
				}
					$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Pajzs; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: $equipable</DIV>
					<DIV class='property'>Kapacitás: $itemdata->shieldenergy</DIV>
					<DIV class='property'>Regeneráció: $itemdata->recharge / kör; Energiafelhasználás: $itemdata->energyusage</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "hull":
				switch($itemdata->equipable)
				{
					case "ship": $equipable = "Hajó"; break;
					case "squadron": $equipable = "Raj"; break;
				}
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Burkolat; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: $equipable</DIV>
					<DIV class='property'>Burkolatnövelés: $itemdata->hullenergy</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "hangar":
				$rep = $itemdata->repair * 100;
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Hangár; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: Hajó</DIV>
					<DIV class='property'>Raj hely: $itemdata->squadronplace</DIV>
					<DIV class='property'>Raj javítás: $rep% / kör</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "equipment":
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Felszerelés; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: Hajó</DIV>
					<DIV class='property'>Lőszer: $itemdata->ammoname</DIV>
					<DIV class='property'>Újratöltési idő: $itemdata->reloadtime</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
					<DIV class='property_effect'>
						<DIV class='property_title'>Hatás:</DIV>
						$itemdata->hundescription
					</DIV>
				";
			break;
			case "generator":
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Generátor; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: Hajó</DIV>
					<DIV class='property'>Energiatermelés: $itemdata->energyregen</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "battery":
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Akkumulátor; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: Hajó, Raj</DIV>
					<DIV class='property'>Kapacitás: $itemdata->capacity; Tölthetőség: $itemdata->maxrecharge</DIV>
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "extender":
				$prop = "<DIV class='property'>Extra $itemdata->hundescription hely: $itemdata->slotextend</DIV>";
				if($itemdata->itemtype == "cargoextender" or $itemdata->itemtype == "ammoextender") $prop = "<DIV class='property'>$itemdata->hundescription</DIV>";
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Bővítő; Szint: $itemdata->level</DIV>
					<DIV class='property'>Felszerelhető: Hajó</DIV>
					$prop
					<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
					<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
				";
			break;
			case "ammo":
				switch($itemdata->itemtype)
				{
					case "specialammo":
						$desc = "
							<DIV class='property_name'>$itemdata->name</DIV>
							<DIV class='property'>Típus: Speciális lőszer; Szint: $itemdata->level</DIV>
							<DIV class='property'>Felszerelhető: Hajó</DIV>
							<DIV class='property'>Felhasználja: $itemdata->hundescription</DIV>
							<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
							<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
						";
					break;
					default:
						$desc = "
							<DIV class='property_name'>$itemdata->name</DIV>
							<DIV class='property'>Típus: Lőszer; Szint: $itemdata->level</DIV>
							<DIV class='property'>Felszerelhető: Hajó</DIV>
							<DIV class='property'>Sebzés: x$itemdata->dmgmultiplicator; Energiafelhasználás: x$itemdata->energymultiplicator</DIV>
							<DIV class='property'>Vásárlás: $itemdata->buyprice Kredit</DIV>
							<DIV class='property'>Eladás: $itemdata->sellprice Kredit</DIV>
						";
					break;
				}
			break;
			case "ability":
				switch($itemdata->itemtype)
				{
					case "passive":
						$skilltype = "Passzív";
						$descs = "
							
						";
					break;
					case "active1":
						$skilltype = "Aktív 1";
						$descs = "
							<DIV class='property'>Aktív: $itemdata->basicactive kör (+ $itemdata->activeinc / szint)</DIV>
							<DIV class='property'>Újratöltés: $itemdata->basicreload kör (- $itemdata->reloadinc / szint)</DIV>
							<DIV class='property'></DIV>
						";
					break;
					case "active2":
						$skilltype = "Aktív 2";
						$descs = "
							<DIV class='property'>Aktív: $itemdata->basicactive kör (+ $itemdata->activeinc / szint)</DIV>
							<DIV class='property'>Újratöltés: $itemdata->basicreload kör (- $itemdata->reloadinc / szint)</DIV>
						";
					break;
				}
				
				$desc = "
					<DIV class='property_name'>$itemdata->name</DIV>
					<DIV class='property'>Típus: Képesség ($skilltype)</DIV>
					<DIV class='property'>Max. szint: $itemdata->maxlevel</DIV>
					$descs
					<DIV class='property_effect'>
						<DIV class='property_title'>Hatás:</DIV>
						$itemdata->hundescription
					</DIV>
				";
			break;
			default:
				
				$desc = "
					<DIV class='property_name'>A tárgynak nincs leírása</DIV>
				";
			break;
		}
		
		return $desc;
	}
?>