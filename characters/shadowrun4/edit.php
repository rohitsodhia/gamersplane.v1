<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'shadowrun4');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		includeSystemInfo('shadowrun4');
	} else $noChar = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/shadowrun4.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/shadowrun4/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr">
				<label for="name" class="textLabel">Name:</label>
				<input type="text" name="name" va 		lue="<?=$charInfo['name']?>" maxlength="50">
			</div>
			<div class="tr">
				<label for="metatype" class="textLabel">Metatype:</label>
				<input type="text" name="metatype" value="<?=$charInfo['metatype']?>" maxlength="20">
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	foreach ($stats as $stat => $statName) {
		if ($stat == 'body' || $stat == 'edge_total') echo "\t\t\t\t\t<div class=\"statCol\">\n";
?>
						<div class="tr">
							<label for="<?=$stat?>" class="textLabel"><?=$statName?>:</label>
							<input type="text" name="<?=$stat?>" value="<?=$charInfo[$stat]?>" maxlength="2">
						</div>
<?
		if ($stat == 'willpower' || $stat == 'astral_initiative') echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
				
				<div id="qualities">
					<h2 class="headerbar hbDark">Qualities</h2>
					<textarea name="qualities" class="hbdMargined"><?=$charInfo['qualities']?></textarea>
				</div>
				
				<div id="damage">
					<h2 class="headerbar hbDark">Damage Tracks</h2>
					<div class="hbdMargined">
						<div class="damageTrack">
							<label for="physical" class="textLabel">Physical Damage</label>
							<input type="text" name="physicalDamage" value="<?=$charInfo['physicalDamage']?>" maxlength="2">
						</div>
						<div class="damageTrack">
							<label for="stun" class="textLabel">Stun Damage</label>
							<input type="text" name="stunDamage" value="<?=$charInfo['stunDamage']?>" maxlength="2">
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="skills" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<textarea name="skills" class="hbdMargined"><?=$charInfo['skills']?></textarea>
				</div>
				<div id="spells" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Spells</h2>
					<textarea name="spells" class="hbdMargined"><?=$charInfo['spells']?></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<textarea name="weapons" class="hbdMargined"><?=$charInfo['weapons']?></textarea>
				</div>
				<div id="armor" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Armor</h2>
					<textarea name="armor" class="hbdMargined"><?=$charInfo['armor']?></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="augments" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Augments</h2>
					<textarea name="augments" class="hbdMargined"><?=$charInfo['augments']?></textarea>
				</div>
				<div id="contacts" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Contacts</h2>
					<textarea name="contacts" class="hbdMargined"><?=$charInfo['contacts']?></textarea>
				</div>
			</div>
			
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>