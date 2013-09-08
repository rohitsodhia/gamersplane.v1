<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT dnd4.*, characters.userID, gms.gameID IS NOT NULL isGM FROM dnd4_characters dnd4 INNER JOIN characters ON dnd4.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE dnd4.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('str', 'con', 'dex', 'int', 'wis', 'cha', 'ac_armor', 'ac_class', 'ac_feats', 'ac_enh', 'ac_misc', 'fort_class', 'fort_feats', 'fort_enh', 'fort_misc', 'ref_class', 'ref_feats', 'ref_enh', 'ref_misc', 'will_class', 'will_feats', 'will_enh', 'will_misc', 'init_misc', 'hp', 'surges', 'speed_base', 'speed_armor', 'speed_item', 'speed_misc', 'ap', 'piSkill', 'ppSkill', 'ab1_stat', 'ab1_class', 'ab1_prof', 'ab1_feat', 'ab1_enh', 'ab1_misc', 'ab2_stat', 'ab2_class', 'ab2_prof', 'ab2_feat', 'ab2_enh', 'ab2_misc', 'ab3_stat', 'ab3_class', 'ab3_prof', 'ab3_feat', 'ab3_enh', 'ab3_misc');
			$textVals = array('name', 'race', 'alignment', 'class', 'paragon', 'epic', 'ab1_ability', 'ab2_ability', 'ab3_ability', 'skills', 'feats', 'powers', 'weapons', 'armor', 'items', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
			$noChar = FALSE;
		}
	}
	
	$alignments = array('g' => 'Good', 'lg' => 'Lawful Good', 'e' => 'Evil', 'ce' => 'Chaotic Evil', 'u' => 'Unaligned'); 
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/dnd4.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/dnd4/<?=$characterID?>/edit">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
			<label id="label_race" class="medText">Race</label>
			<label id="label_alignment" class="medText">Alignment</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['race']?></div>
			<div class="medText"><?=$alignments[$charInfo['alignment']]?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="longText">Class(es)/Level(s)</label>
			<label id="label_paragon" class="medText">Paragon Path</label>
			<label id="label_epic" class="medText">Epic Destiny</label>
		</div>
		<div class="tr dataTR">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="longText"><?=$charInfo['paragon']?></div>
			<div class="longText"><?=$charInfo['epic']?></div>
		</div>
		
		<div id="stats">
			<div class="tr labelTR">
				<label class="shortText lrBuffer">Stat</label>
				<label class="shortNum alignCenter stat">Score</label>
				<label class="shortNum alignCenter">Mod</label>
				<label class="shortNum alignCenter">Mod + 1/2 Lvl</label>
			</div>
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = floor(($charInfo[$short] - 10) / 2);
		echo "				<div class=\"tr dataTR\">
					<label id=\"label_{$short}\" class=\"textLabel shortText lrBuffer leftLabel\">{$stat}</label>
					<div class=\"stat shortNum alignCenter\">{$charInfo[$short]}</div>
					<div id=\"{$short}Modifier\" class=\"statMod shortNum alignCenter\">".showSign($bonus)."</div>
					<div id=\"{$short}ModifierPL\" class=\"shortNum alignCenter\">".showSign($bonus + floor($charInfo['level'] / 2))."</div>
				</div>
	";
		$statBonus[$short] = $bonus;
	}
	
	$charInfo['size'] = showSign($charInfo['size']);
?>
		</div>
		
		<div id="saves">
			<div class="tr labelTR">
				<label class="statCol shortNum lrBuffer first">Total</label>
				<label class="statCol shortNum lrBuffer">10 + 1/2 Lvl</label>
				<label class="statCol shortNum lrBuffer">Armor/ Ability</label>
				<label class="statCol shortNum lrBuffer">Class</label>
				<label class="statCol shortNum lrBuffer">Feat</label>
				<label class="statCol shortNum lrBuffer">Enh</label>
				<label class="statCol shortNum lrBuffer">Misc</label>
			</div>
<?
	$ac = 10 + floor($charInfo['level'] / 2) + $charInfo['ac_armor'] + $charInfo['ac_class'] + $charInfo['ac_feats'] + $charInfo['ac_enh'] + $charInfo['ac_misc'];
	$fortBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['fort_armor'] + $charInfo['fort_class'] + $charInfo['fort_feats'] + $charInfo['fort_enh'] + $charInfo['fort_misc'] + ($statBonus['con'] > $statBonus['str']?$statBonus['con']:$statBonus['str']);
	$refBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['ref_armor'] + $charInfo['ref_class'] + $charInfo['ref_feats'] + $charInfo['ref_enh'] + $charInfo['ref_misc'] + ($statBonus['dex'] > $statBonus['int']?$statBonus['dex']:$statBonus['int']);
	$willBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['will_armor'] + $charInfo['will_class'] + $charInfo['will_feats'] + $charInfo['will_enh'] + $charInfo['will_misc'] + ($statBonus['wis'] > $statBonus['cha']?$statBonus['wis']:$statBonus['cha']);
?>
			<div id="fortRow" class="tr dataTR">
				<label class="leftLabel">AC</label>
				<div id="acTotal" class="shortNum lrBuffer total"><?=showSign($ac)?></div>
				<div class="shortNum lrBuffer"><?=showSign(10 + floor($charInfo['level'] / 2))?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ac_armor'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ac_class'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ac_feats'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ac_enh'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ac_misc'])?></div>
			</div>
			<div id="fortRow" class="tr dataTR">
				<label class="leftLabel">Fortitude</label>
				<div id="fortTotal" class="shortNum lrBuffer total"><?=showSign($fortBonus)?></div>
				<div class="shortNum lrBuffer"><?=showSign(10 + floor($charInfo['level'] / 2))?></div>
				<div class="shortNum lrBuffer"><?=showSign($statBonus['con'] > $statBonus['str']?$statBonus['con']:$statBonus['str'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_class'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_feats'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_enh'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_misc'])?></div>
			</div>
			<div id="refRow" class="tr dataTR">
				<label class="leftLabel">Reflex</label>
				<div id="refTotal" class="shortNum lrBuffer total"><?=showSign($refBonus)?></div>
				<div class="shortNum lrBuffer"><?=showSign(10 + floor($charInfo['level'] / 2))?></div>
				<div class="shortNum lrBuffer"><?=showSign($statBonus['dex'] > $statBonus['int']?$statBonus['dex']:$statBonus['int'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_class'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_feats'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_enh'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_misc'])?></div>
			</div>
			<div id="willRow" class="tr dataTR">
				<label class="leftLabel">Will</label>
				<div id="willTotal" class="shortNum lrBuffer total"><?=showSign($willBonus)?></div>
				<div class="shortNum lrBuffer"><?=showSign(10 + floor($charInfo['level'] / 2))?></div>
				<div class="shortNum lrBuffer"><?=showSign($statBonus['wis'] > $statBonus['cha']?$statBonus['wis']:$statBonus['cha'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['will_class'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['will_feats'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['will_enh'])?></div>
				<div class="shortNum lrBuffer"><?=showSign($charInfo['will_misc'])?></div>
			</div>
		</div>
		
		<div id="init">
			<div class="tr labelTR">
				<label class="shortNum alignCenter lrBuffer first">Total</label>
				<label class="shortNum alignCenter lrBuffer">Dex</label>
				<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
				<label class="shortNum alignCenter lrBuffer">Misc</label>
			</div>
			<div class="tr">
				<label class="shortText alighRight leftLabel">Initiative</label>
				<div class="shortNum alignCenter lrBuffer total"><?=showSign($statBonus['dex'] + floor($charInfo['level'] / 2) + $charInfo['init_misc'])?></div>
				<div class="shortNum alignCenter lrBuffer"><?=showSign($statBonus['dex'])?></div>
				<div class="shortNum alignCenter lrBuffer">+<?=floor($charInfo['level'] / 2)?></div>
				<div class="shortNum alignCenter lrBuffer"><?=showSign($charInfo['init_misc'])?></div>
			</div>
		</div>
		
		<br class="clear">
		<div id="hpCol">
			<div id="hp">
				<div class="tr labelTR">
					<label class="medNum alignCenter lrBuffer">Total HP</label>
					<label class="medNum alignCenter lrBuffer">Bloodied</label>
					<label class="medNum alignCenter lrBuffer">Surge Value</label>
					<label class="medNum alignCenter lrBuffer">Surges/ Day</label>
				</div>
				<div class="tr">
					<div class="medNum alignCenter lrBuffer cell"><?=$charInfo['hp']?></div>
					<div class="medNum alignCenter lrBuffer cell"><?=floor($charInfo['hp'] / 2)?></div>
					<div class="medNum alignCenter lrBuffer cell"><?=floor($charInfo['hp'] / 4)?></div>
					<div class="medNum alignCenter lrBuffer cell"><?=$charInfo['surges']?></div>
				</div>
			</div>
			
			<div id="movement">
				<div class="tr labelTR">
					<label class="shortNum alignCenter lrBuffer first">Total</label>
					<label class="shortNum alignCenter lrBuffer">Base</label>
					<label class="shortNum alignCenter lrBuffer">Armor</label>
					<label class="shortNum alignCenter lrBuffer">Item</label>
					<label class="shortNum alignCenter lrBuffer">Misc</label>
				</div>
				<div class="tr">
					<label class="medNum leftLabel">Speed</label>
					<div class="shortNum alignCenter lrBuffer cell total"><?=$charInfo['speed_base'] + $charInfo['speed_armor'] + $charInfo['speed_item'] + $charInfo['speed_misc']?></div>
					<div class="shortNum alignCenter lrBuffer cell"><?=$charInfo['speed_base']?></div>
					<div class="shortNum alignCenter lrBuffer cell"><?=$charInfo['speed_armor']?></div>
					<div class="shortNum alignCenter lrBuffer cell"><?=$charInfo['speed_item']?></div>
					<div class="shortNum alignCenter lrBuffer cell"><?=$charInfo['speed_misc']?></div>
				</div>
			</div>
			
			<div id="actionPoints">
				<label class="shortText leftLabel">Action Points</label>
				<div class="shortNum alignCenter lrBuffer cell"><?=$charInfo['ap']?></div>
			</div>
			
			<div id="passiveSenses">
				<div class="tr labelTR">
					<label class="shortNum alignCenter">Total</label>
					<label class="shortNum alignCenter">Skill</label>
				</div>
				<div class="tr">
					<label class="leftLabel">Passive Insight</label>
					<div class="shortNum alignCenter cell total"><?=$charInfo['piSkill'] + 10?></div>
					<div class="shortNum alignCenter cell">10 + </div>
					<div class="shortNum alignCenter cell"><?=$charInfo['piSkill']?></div>
				</div>
				<div class="tr">
					<label class="leftLabel">Passive Perception</label>
					<div class="shortNum alignCenter cell total"><?=$charInfo['ppSkill'] + 10?></div>
					<div class="shortNum alignCenter cell">10 + </div>
					<div class="shortNum alignCenter cell"><?=$charInfo['ppSkill']?></div>
				</div>
			</div>
		</div>
	
		<div id="combatBonuses">
			<h3>Attack Bonuses</h3>
<?
	$count = 0;
	$attacks = $mysql->query('SELECT attackID, ability, stat, class, prof, feat, enh, misc FROM dnd4_attacks WHERE characterID = '.$characterID);
	foreach ($attacks as $attackInfo) {
		$total = showSign(floor($charInfo['level'] / 2) + $attackInfo['stat'] + $attackInfo['class'] + $attackInfo['prof'] + $attackInfo['feat'] + $attackInfo['enh'] + $attackInfo['misc']);
?>
				<div class="attackBonusSet">
					<div class="tr">
						<label class="medNum leftLabel">Ability:</label>
						<div class="lrBuffer ability"><?=$attackInfo['ability']?></div>
					</div>
					<div class="tr labelTR">
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Class</label>
						<label class="shortNum alignCenter lrBuffer">Prof</label>
						<label class="shortNum alignCenter lrBuffer">Feat</label>
						<label class="shortNum alignCenter lrBuffer">Enh</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<div class="shortNum alignCenter lrBuffer"><?=$total?></div>
						<div class="shortNum alignCenter lrBuffer">+<?=floor($charInfo['level'] / 2)?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['stat'])?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['class'])?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['prof'])?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['feat'])?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['enh'])?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($attackInfo['misc'])?></div>
					</div>
				</div>
<?
	}
?>
		</div>
		
		<br class="clear">
		<div class="clearfix">
			<div id="skills" class="floatLeft">
				<h2>Skills</h2>
				<div class="tr labelTR">
					<label class="medText">Skill</label>
					<label class="shortNum alignCenter lrBuffer">Total</label>
					<label class="shortNum alignCenter lrBuffer">Stat</label>
					<label class="shortNum alignCenter lrBuffer">Ranks</label>
					<label class="shortNum alignCenter lrBuffer">Misc</label>
				</div>
<?
	$skills = $mysql->query('SELECT dnd4_skills.skillID, skillsList.name, dnd4_skills.stat, dnd4_skills.ranks, dnd4_skills.misc FROM dnd4_skills INNER JOIN skillsList USING (skillID) ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		echo "\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"skill_name medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_total addStat_{$skillInfo['stat']} shortNum lrBuffer\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_stat alignCenter shortNum lrBuffer\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['ranks'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
			</div>
			<div id="feats" class="floatRight">
				<h2>Feats/Features</h2>
<?
	$feats = $mysql->query('SELECT dnd4_feats.featID, featsList.name FROM dnd4_feats INNER JOIN featsList USING (featID) ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<a href=\"".SITEROOT."/characters/dnd4/featNotes/$characterID/{$featInfo['featID']}?modal=1\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noFeats\">This character currently has no feats/features.</p>\n";
?>
			</div>
		</div>
		
		<div id="powers" class="clearfix">
			<h2>Powers</h2>
			<div id="powers_atwill" class="powerCol first">
				<h3>At-Will</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "a" AND characterID = '.$characterID);
	foreach ($powers as $power) echo "\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</div>\n";
?>
			</div>
			<div id="powers_encounter" class="powerCol">
				<h3>Encounter</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "e" AND characterID = '.$characterID);
	foreach ($powers as $power) echo "\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</div>\n";
?>
			</div>
			<div id="powers_daily" class="powerCol">
				<h3>Daily</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "d" AND characterID = '.$characterID);
	foreach ($powers as $power) echo "\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</div>\n";
?>
			</div>
		</div>
		
		<div id="weapons" class="textDiv floatLeft">
			<h2>Weapons</h2>
			<div><?=$charInfo['weapons']?></div>
		</div>
		<div id="armor" class="textDiv floatRight">
			<h2>Armor</h2>
			<div><?=$charInfo['armor']?></div>
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