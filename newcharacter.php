<?php
	include("connection.php");

	$id = "EMF";
	$name = "Earth Mothership Factory";
	$hundescription = "Az EMF gyártja az emberi faj legjobb hordozóit. Bevált stratégiájuk, hogy az anyahajóról indított kisebb vadászok legyengítik az ellenséget, míg a vezérhajó hatótávon kívülről irányítja őket. A vadászpilótákat a galaxis legelitebbjei közül válogatják össze, így mindig győztesként kerülnek ki a harcokból. A vadászok csapatokba rendeződve páratlan hatékonysággal pusztítják el kijelölt célpontjukat, míg az anyahajó biztos távolságból osztja a parancsokat és vezeti győzelemre a csapatot.";
	$hunname = "Földi Anyahajó Gyár";
	$emf = new company($id, $name, $hundescription, $hunname);
	
	$id = "PDM";
	$name = "Planet Defender Military";
	$hundescription = "Bolygónk legerősebb védelemmel elátott hajói mind a PDM üzemeiből kerülnek ki. Nincs az a fegyver, ami áthatolhatna a pajzsukon, ezért nem csoda, hogy mindig a frontvonal első soraiban repülnek megvédve sérülékenyebb társaikat az ellenséges tűztől. Az egységek különleges pajzsokkal és rakétaelhárító rendszerekkel vannak szerelve, így bátran vethetik magukat a legkeményebb tűzharcba is.";
	$hunname = "Bolygóvédő Hadsereg";
	$pdm = new company($id, $name, $hundescription, $hunname);
	
	$id = "IDF";
	$name = "Interplanetary Destroyer Forces";
	$hundescription = "Aki szereti a robbanásokat, annak mindenképp a IDF kötelékében a helye! Masszív rakétahordozó hajóik pusztító erejével utat törnek maguknak, még a csillagok sem állíthatják meg rohamukat. Fejlett rakétakilövőiknél csak a robbanófejeik erősebbek, megspékelve szoftveres irányítással, így az ellenség számára kellemetlen tűzijáték mindig ott okozza a legnagyobb kárt, ahol a legjobban fáj.";
	$hunname = "Bolygóközi Pusztító Erők";
	$idf = new company($id, $name, $hundescription, $hunname);
	
	$id = "MFA";
	$name = "Mining and Forwarding Association";
	$hundescription = "Az univerzum áruforgalmának nagy részét MFA jelzéssel ellátott teherhajók szállítják. Bejárják a galaxis bolgyóit, így nem csoda, hogy mindig a legkorszerűbb felszerelésekkel rendelkeznek. Habár a robosztus szállítóknak nem a harctér a kedvenc tartózkodási helyük, fejlett rajok elleni védelemmel vannak ellátva, ezzel okozva fejtörést a támadóknak.";
	$hunname = "Bányász és Kereskedelmi Egyesület";
	$mfa = new company($id, $name, $hundescription, $hunname);
	
	$id = "GAA";
	$name = "Galactic Assassin Alliance";
	$hundescription = "Aki az űr mélyeibe repül, mindig számolnia kell a váratlanul felbukkanó, hatalmas tűzerővel rendelkező Assassinok megjelenésével. Nem sokat tudni ezekről az elvetemült gazfickókról, csak azt, hogy támadásuk mindig célba talál, és megbénítja a célpontját, miközben a saját pajzsát erősíti.";
	$hunname = "Galaktikus Orgyilkos Szövetség";
	$gaa = new company($id, $name, $hundescription, $hunname);
	
	$id = "CRI";
	$name = "Central Researcher Institute";
	$hundescription = "Ha az új technika érdekel, a CRI hajókban megtalálod. Energiafelhasználás csökkentésében kiváló eredményeket értek el, így a gyorsan újratöltődő speciális fegyvereik szünet nélkül szólhatnak. Szofverfejlesztés terén is a világranglista élén állnak, és bármikor fel tudják törni az ellenség központi számítógépét, hogy blokkolva funkcióit tehetetlenné tegyék a szövetséges tűzzel szemben.";
	$hunname = "Központi Kutató Társaság";
	$cri = new company($id, $name, $hundescription, $hunname);
?>

<HTML>
	<HEAD>
			<link rel="stylesheet" type="text/css" href="shell_style.css">
			<link rel="stylesheet" type="text/css" href="newcharacter.css">
		<TITLE>Karakter létrehozása</TITLE>
	</HEAD>
<BODY>

<?php

	print "
		<TABLE>
			<TR>
				<TD>
					<DIV class='company'>
						<H1>$emf->id - $emf->name</H1>
						<H2>($emf->hunname)</H2>
						<P>$emf->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=emf'>EMF pilóta leszek!</A></P>
					</DIV>
				</TD>
				<TD>
					<DIV class='company'>
						<H1>$pdm->id - $pdm->name</H1>
						<H2>($pdm->hunname)</H2>
						<P>$pdm->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=pdm'>PDM pilóta leszek!</A></P>
					</DIV>
				</TD>
				<TD>
					<DIV class='company'>
						<H1>$idf->id - $idf->name</H1>
						<H2>($idf->hunname)</H2>
						<P>$idf->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=idf'>IDF pilóta leszek!</A></P>
					</DIV>
				</TD>
			</TR>
			<TR>
				<TD>
					<DIV class='company'>
						<H1>$mfa->id - $mfa->name</H1>
						<H2>($mfa->hunname)</H2>
						<P>$mfa->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=mfa'>MFA pilóta leszek!</A></P>
					</DIV>
				</TD>
				<TD>
					<DIV class='company'>
						<H1>$gaa->id - $gaa->name</H1>
						<H2>($gaa->hunname)</H2>
						<P>$gaa->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=gaa'>GAA pilóta leszek!</A></P>
					</DIV>
				</TD>
				<TD>
					<DIV class='company'>
						<H1>$cri->id - $cri->name</H1>
						<H2>($cri->hunname)</H2>
						<P>$cri->hundescription</P>
						<P class='choose'><A href='newcharactergenerate.php?company=cri'>CRI pilóta leszek!</A></P>
					</DIV>
				</TD>
			</TR>
		</TABLE>		
	";
?>
	<DIV class='back'><A href='<?php print $_SESSION["from"]; ?>'>Vissza</A></DIV>

</BODY>
</HTML>