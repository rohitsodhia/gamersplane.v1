<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT shadowrun4.*, characters.userID, gms.gameID IS NOT NULL isGM FROM shadowrun4_characters shadowrun4 INNER JOIN characters ON shadowrun4.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE shadowrun4.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_total', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
			$textVals = array('name', 'race', 'class', 'dr', 'skills', 'feats', 'weapons', 'armor', 'items', 'spells', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp;';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/shadowrun4.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/shadowrun4/<?=$characterID?>/edit">Edit Character</a></div>
			
		<div class="tr">
			<label for="name">Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		<div class="tr">
			<label for="metatype">Metatype:</label>
			<div><?=$charInfo['metatype']?></div>
		</div>
		
		<div id="stats">
<?
	foreach (array('body' => 'Body', 'agility' => 'Agility', 'reaction' => 'Reaction', 'strength' => 'Strength', 'charisma' => 'Charisma', 'intuition' => 'Intuition', 'logic' => 'Logic', 'willpower' => 'Willpower', 'edge_total' => 'Total Edge', 'edge_current' => 'Current Edge', 'essence' => 'Essence', 'mag_res' => 'Magic or Resonance', 'initiative' => 'Initiative', 'initiative_passes' => 'Initiative Passes', 'matrix_initiative' => 'Matrix Initiative', 'astral_initiative' => 'Astral Initiative') as $stat => $statName) {
		if ($stat == 'body' || $stat == 'edge_total') echo "\t\t\t\t<div class=\"statCol\">\n";
		echo "\t\t\t\t\t<div class=\"tr\">\n";
		echo "\t\t\t\t\t\t<label for=\"{$stat}\">{$statName}:</label>\n";
		echo "\t\t\t\t\t\t<div>{$charInfo[$stat]}</div>\n";
		echo "\t\t\t\t\t</div>\n";
		if ($stat == 'willpower' || $stat == 'astral_initiative') echo "\t\t\t\t</div>\n";
	}
?>
		</div>
		
		<div id="qualities">
			<h2>Qualities</h2>
			<div><?=$charInfo['qualities']?></div>
		</div>
		
		<div id="damage">
			<h2>Damage Tracks</h2>
			<div>
				<label for="physical">Physical Damage</label>
				<div><?=$charInfo['physicalDamage']?></div>
			</div>
			<div>
				<label for="stun">Stun Damage</label>
				<div><?=$charInfo['stunDamage']?></div>
			</div>
		</div>
		
		<br class="clear">
		<div id="skills" class="twoCol floatLeft">
			<h2>Skills</h2>
			<div><?=$charInfo['skills']?></div>
		</div>
		<div id="spells" class="twoCol floatRight">
			<h2>Spells</h2>
			<div><?=$charInfo['spells']?></div>
		</div>
		
		<br class="clear">
		<div id="weapons" class="twoCol floatLeft">
			<h2>Weapons</h2>
			<div><?=$charInfo['weapons']?></div>
		</div>
		<div id="armor" class="twoCol floatRight">
			<h2>Armor</h2>
			<div><?=$charInfo['armor']?></div>
		</div>
		
		<br class="clear">
		<div id="augments" class="twoCol floatLeft">
			<h2>Augments</h2>
			<div><?=$charInfo['augments']?></div>
		</div>
		<div id="contacts" class="twoCol floatRight">
			<h2>Contacts</h2>
			<div><?=$charInfo['contacts']?></div>
		</div>
		
		<br class="clear">
		<div id="items">
			<h2>Items</h2>
			<div><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2>Notes</h2>
			<div><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>