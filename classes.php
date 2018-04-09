<?php
	class common
	{
		function common($id)
		{
			$this->itemid = $id;
		}
	}
	
		class ships extends common
		{
			public $type = "ship";
			public $slot = "ship";
			public $equipable = "ship";
		}
		
			class emf extends ships
			{
				public $itemtype = "emf";
				public $companyname = "EMF - Earth Mothership Factory";
			}
			
			class pdm extends ships
			{
				public $itemtype = "pdm";
				public $companyname = "PDM - Planet Defender Military";
			}
			
			class idf extends ships
			{
				public $itemtype = "idf";
				public $companyname = "IDF - Interplanetary Destroyer Forces";
			}
			
			class mfa extends ships
			{
				public $itemtype = "mfa";
				public $companyname = "MFA - Mining and Forwarding Association";
			}
			
			class gaa extends ships
			{
				public $itemtype = "gaa";
				public $companyname = "GAA - Galactic Assassin Alliance";
			}
			
			class cri extends ships
			{
				public $itemtype = "cri";
				public $companyname = "CRI - Central Research Institute";
			}
			
		class sqa extends common
		{
			public $type = "squadron";
			public $slot = "squadron";
			public $equipable = "ship";
		}
			
		class weapons extends common
		{
			public $type = "weapon";
		}
		
			class cannon extends weapons
			{
				public $itemtype = "cannon";
				public $ammotype = "cannonball";
				public $reloadtime = 1;
				public $squadrondamage = 0;
				public $accuracy = 700;
				public $slot = "cannon";
				public $equipable = "ship";
				public $ammoname = "Ágyúgolyó";
			}
			
			class pulse extends weapons
			{
				public $itemtype = "pulse";
				public $ammotype = "ioncell";
				public $reloadtime = 1;
				public $accuracy = 700;
				public $slot = "cannon";
				public $equipable = "ship";
				public $squadrondamage = 0;
				public $ammoname = "Ioncella";
			}
			
			class rifle extends weapons
			{
				public $itemtype = "rifle";
				public $ammotype = "bullet";
				public $reloadtime = 1;
				public $accuracy = 700;
				public $slot = "rifle";
				public $equipable = "ship";
				public $hulldamage = 0;
				public $shielddamage = 0;
				public $ammoname = "Töltény";
			}
			
			class rocketlauncher extends weapons
			{
				public $itemtype = "rocketlauncher";
				public $ammotype = "rocket";
				public $reloadtime = 6;
				public $accuracy = 500;
				public $slot = "rocketlauncher";
				public $equipable = "ship";
				public $squadrondamage = 0;
				public $ammoname = "Rakéta";
			}
			
			class sablauncher extends weapons
			{
				public $type = "weapon";
				public $itemtype = "sablauncher";
				public $ammotype = "sabrocket";
				public $reloadtime = 6;
				public $accuracy = 500;
				public $slot = "rocketlauncher";
				public $equipable = "ship";
				public $squadrondamage = 0;
				public $ammoname = "SAB rakéta";
			}
			
			class squadronrifle extends weapons
			{
				public $itemtype = "squadronrifle";
				public $ammotype = "bullet";
				public $reloadtime = 1;
				public $accuracy = 700;
				public $slot = "squadroncannon";
				public $equipable = "squadron";
				public $hulldamage = 0;
				public $shielddamage = 0;
				public $ammoname = "Töltány";
			}
			
			class squadroncannon extends weapons
			{
				public $itemtype = "squadroncannon";
				public $ammotype = "cannonball";
				public $reloadtime = 1;
				public $accuracy = 700;
				public $slot = "squadroncannon";
				public $equipable = "squadron";
				public $squadrondamage = 0;
				public $ammoname = "Ágyúgolyó";
			}
			
			class squadronpulse extends weapons
			{
				public $itemtype = "squadronpulse";
				public $ammotype = "ioncell";
				public $reloadtime = 1;
				public $accuracy = 700;
				public $slot = "squadroncannon";
				public $equipable = "squadron";
				public $squadrondamage = 0;
				public $ammoname = "Ioncella";
			}
			
		class shields extends common				
		{
			public $type = "shield";
		}
						
			class highcapacityshields extends shields
			{
				public $itemtype = "highcapacityshield";
				public $slot = "shield";
				public $equipable = "ship";
			}
			
			class quickrechargeshields extends shields
			{
				public $itemtype = "quickrechargeshield";
				public $slot = "shield";
				public $equipable = "ship";
			}
			
			class squadronshields extends shields
			{
				public $itemtype = "squadronshield";
				public $slot = "squadronshield";
				public $equipable = "squadron";
			}
			
			class squadronquickrechargeshields extends shields
			{
				public $itemtype = "squadronquickrechargeshield";
				public $slot = "squadronshield";
				public $equipable = "squadron";
			}
			
		class hulls extends common
		{
			public $type = "hull";
		}

			class battleshiphulls extends hulls
			{
				public $itemtype = "battleshiphull";
				public $slot = "hull";
				public $equipable = "ship";
			}
			
			class squadronhulls extends hulls
			{
				public $itemtype = "squadronhull";
				public $slot = "squadronhull";
				public $equipable = "squadron";
			}
			
		class hangars extends common
		{
			public $type = "hangar";
			public $slot = "hangar";
			public $equipable = "ship";
		}
			
		class extras extends common
		{
			public $type = "equipment";
			public $level = 1;
			public $construction = 5000;
			public $slot = "equipment";
			public $craftprice = 5000;
			public $sellprice = 15000;
			public $buyprice = 50000;
			public $equipable = "ship";
			public $score = 0;
		}	
			
			class repairbots extends extras
			{
				public $itemtype = "repairbot";
				public $reloadtime = 10;
				public $ammotype = "spp01";
				public $ammoname = "Pótalkatrész";
			}
			
		class generators extends common
		{
			public $type = "generator";
			public $slot = "generator";
			public $equipable = "ship";
		}
			
		class batterys extends common
		{
			public $type = "battery";
			public $slot = "battery";
			public $equipable = "both";
		}
			
		class extenders extends common
		{
			public $type = "extender";
			public $slot = "extender";
			public $equipable = "ship";
		}
			
			class weaponextenders extends extenders
			{
				public $itemtype = "weaponextender";
				public $effect = "cannonslot";
				public $ename = "cannon";
				public $hundescription = "ágyú";
			}
			
			class rifleextenders extends extenders
			{
				public $itemtype = "rifleextender";
				public $effect = "rifleslot";
				public $ename = "rifle";
				public $hundescription = "gépágyú";
			}
			
			class rocketextenders extends extenders
			{
				public $itemtype = "rocketextender";
				public $effect = "rocketslot";
				public $ename = "rocketlauncher";
				public $hundescription = "rakétakilövő";
			}
			
			class hangarextenders extends extenders
			{
				public $itemtype = "hangarextender";
				public $effect = "hangarslot";
				public $ename = "hangar";
				public $hundescription = "hangár";
			}
			
			class shieldextenders extends extenders
			{
				public $itemtype = "shieldextender";
				public $effect = "shieldslot";
				public $ename = "shield";
				public $hundescription = "pajzs";
			}
			
			class hullextenders extends extenders
			{
				public $itemtype = "hullextender";
				public $effect = "hullslot";
				public $ename = "hull";
				public $hundescription = "burkolat";
			}
			
			class equipextenders extends extenders
			{
				public $itemtype = "equipextender";
				public $effect = "equipmentslot";
				public $ename = "equipment";
				public $hundescription = "felszerelés";
			}
			
			class generatorextenders extends extenders
			{
				public $itemtype = "generatorextender";
				public $effect = "generatorslot";
				public $ename = "generator";
				public $hundescription = "generátor";
			}
			
			class batteryextenders extends extenders
			{
				public $itemtype = "batteryextender";
				public $effect = "batteryslot";
				public $ename = "battery";
				public $hundescription = "akkumulátor";
			}
			
			class extenderextenders extends extenders
			{
				public $itemtype = "extenderextender";
				public $effect = "extenderslot";
				public $ename = "extender";
				public $hundescription = "bővítő";
			}
			
			class cargoextenders extends extenders
			{
				public $itemtype = "cargoextender";
				public $effect = "basiccargo";
			}
			
			class ammoextenders extends extenders
			{
				public $itemtype = "ammoextender";
				public $effect = "basicammostorage";
			}
			
		class ammos extends common
		{
			public $type = "ammo";
			public $slot = "ammo";
			public $equipable = "ship";
		}
				
			class cannonballs extends ammos
			{
				public $itemtype = "cannonball";
			}
			
			class ioncells extends ammos
			{
				public $itemtype = "ioncell";
			}
			
			class bullets extends ammos
			{
				public $itemtype = "bullet";
			}
			
			class rockets extends ammos
			{
				public $itemtype = "rocket";
			}
			
			class sabrockets extends ammos
			{
				public $itemtype = "sabrocket";
			}
			
			class specialammos extends ammos
			{
				public $itemtype = "specialammo";
				public $level = 1;
			}
			
		class abilities extends common
		{
			public $type = "ability";
			public $slot = "skill";
		}		
				
			class passive extends abilities
			{
				public $itemtype = "passive";
			}
			
			class active1 extends abilities
			{
				public $itemtype = "active1";
			}
			
			class active2 extends abilities
			{
				public $itemtype = "active2";
			}
			
	class company
	{
		public $id;
		public $name;
		public $hundescription;
		public $hunname;
		
		function company($id, $name, $hundescription, $hunname)
		{
			$this->id = $id;
			$this->name = $name;
			$this->hundescription = $hundescription;
			$this->hunname= $hunname;
		}
	}
			
	class emptyclass
	{
		
	}
			
	class squadron
	{
		public $group = "no";
		function squadron($squadronid, $squadronname)
		{
			$this->squadronid = $squadronid;
			$this->squadronname = $squadronname;
		}
	}
			
	class equipment
	{
		function equipment($itemid, $equipped, $place)
		{
			$this->itemid = $itemid;
			$this->equipped = $equipped;
			$this->place = $place;
		}
	}
			
	class ammo
	{
		function ammo($itemid, $place, $equipped, $amount)
		{
			$this->itemid = $itemid;
			$this->place = $place;
			$this->equipped = $equipped;
			$this->amount = $amount;
		}
	}
			
	class ship
	{
		
	}
	
	class group
	{
		public $membernum = 0;
		public $members = 0;
		function group($groupid, $groupname)
		{
			$this->groupid = $groupid;
			$this->groupname = $groupname;
		}
	}
	
	class groupcontrol
	{
		public $target = 0;
		public $targettry = 0;
		public $targetselect = 0;
		public $targetstyle = "auto";
		public $takeoff = 0;
		public $returnstyle = 0;
		public $returnvalue = 0;
		public $place = 0;
		public $dmgreceived = 0;
	}
?>