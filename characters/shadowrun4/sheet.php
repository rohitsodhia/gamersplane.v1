<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'shadowrun4';
	$charInfo = getCharInfo($characterID, $system);
	if ($charInfo) {
		if ($viewerStatus = allowCharView($characterID, $userID)) {
			$noChar = FALSE;
			includeSystemInfo($system);

			if ($viewerStatus == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<? if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="wingDiv hbMargined floatRight">
			<div>
<?		if ($viewerStatus == 'edit') { ?>
				<a id="editCharacter" href="/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

		<div class="tr">
			<label for="name">Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		<div class="tr">
			<label for="metatype">Metatype:</label>
			<div><?=$charInfo['metatype']?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats">
<?
	foreach ($stats as $stat => $statName) {
		if ($stat == 'body' || $stat == 'edge_total') echo "\t\t\t\t<div class=\"statCol\">\n";
?>
							<div class="tr">
								<label for="<?=$stat?>"><?=$statName?>:</label>
								<div><?=$charInfo[$stat]?></div>
							</div>
<?
		if ($stat == 'willpower' || $stat == 'astral_initiative') echo "\t\t\t\t</div>\n";
	}
?>
			</div>
			
			<div id="qualities">
				<h2 class="headerbar hbDark">Qualities</h2>
				<div class="hbdMargined"><?=$charInfo['qualities']?></div>
			</div>
			
			<div id="damage">
				<h2 class="headerbar hbDark">Damage Tracks</h2>
				<div class="hbdMargined">
					<div class="damageTrack">
						<label for="physical">Physical Damage</label>
						<div><?=$charInfo['physicalDamage']?></div>
					</div>
					<div class="damageTrack">
						<label for="stun">Stun Damage</label>
						<div><?=$charInfo['stunDamage']?></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="skills" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=$charInfo['skills']?></div>
			</div>
			<div id="spells" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Spells</h2>
				<div class="hbdMargined"><?=$charInfo['spells']?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbdMargined"><?=$charInfo['weapons']?></div>
			</div>
			<div id="armor" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Armor</h2>
				<div class="hbdMargined"><?=$charInfo['armor']?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="augments" class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Augments</h2>
				<div class="hbdMargined"><?=$charInfo['augments']?></div>
			</div>
			<div id="contacts" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Contacts</h2>
				<div class="hbdMargined"><?=$charInfo['contacts']?></div>
			</div>
		</div>
		
		<div id="items">
			<h2 class="headerbar hbDark">Items</h2>
			<div class="hbdMargined"><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>