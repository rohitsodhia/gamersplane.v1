<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cthulhu.*, characters.userID, gms.gameID IS NOT NULL isGM FROM cthulhu_characters cthulhu INNER JOIN characters ON cthulhu.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE cthulhu.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_total', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
			$textVals = array('name', 'race', 'class', 'dr', 'skills', 'feats', 'weapons', 'armor', 'items', 'spells', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/cthulhu.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/cthulhu/<?=$characterID?>/edit">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="col2 lrBuffer">Profession</label>
			<label id="label_alignment" class="medText lrBuffer">Alignment</label>
		</div>
		<div class="tr dataTR">
			<div class="col2"><?=$charInfo['class']?></div>
			<div class="col2"><?=$alignments[$charInfo['alignment']]?></div>
		</div>
		
		<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = showSign(floor(($charInfo[$short] - 10)/2));
		echo "				<div class=\"tr dataTR\">
					<label id=\"label_{$short}\" class=\"textLabel col3 lrBuffer leftLabel\">{$stat}</label>
					<div class=\"stat\">{$charInfo[$short]}</div>
					<span id=\"{$short}Modifier\">{$bonus}</span>
				</div>
	";
		$statBonus[$short] = $bonus;
	}
	
	$charInfo['size'] = showSign($charInfo['size']);
?>
		</div>
		
		<div id="savingThrows">
			<div class="tr labelTR">
				<label class="statCol col4 lrBuffer first">Total</label>
				<label class="statCol col4 lrBuffer">Base</label>
				<label class="statCol col4 lrBuffer">Ability</label>
				<label class="statCol col4 lrBuffer">Magic</label>
				<label class="statCol col4 lrBuffer">Race</label>
				<label class="statCol col4 lrBuffer">Misc</label>
			</div>
<?
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_magic'] + $charInfo['fort_race'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_magic'] + $charInfo['ref_race'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_magic'] + $charInfo['will_race'] + $charInfo['will_misc']);
?>
			<div id="fortRow" class="tr dataTR">
				<label class="leftLabel">Fortitude</label>
				<div id="fortTotal" class="col4 lrBuffer"><?=$fortBonus?></div>
				<div class="col4"><?=showSign($charInfo['fort_base'])?></div>
				<div class="col4 lrBuffer statBonus_con"><?=$statBonus['con']?></div>
				<div class="col4"><?=showSign($charInfo['fort_magic'])?></div>
				<div class="col4"><?=showSign($charInfo['fort_race'])?></div>
				<div class="col4"><?=showSign($charInfo['fort_misc'])?></div>
			</div>
			<div id="refRow" class="tr dataTR">
				<label class="leftLabel">Reflex</label>
				<div id="refTotal" class="col4 lrBuffer"><?=$refBonus?></div>
				<div class="col4"><?=showSign($charInfo['ref_base'])?></div>
				<div class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></div>
				<div class="col4"><?=showSign($charInfo['ref_magic'])?></div>
				<div class="col4"><?=showSign($charInfo['ref_race'])?></div>
				<div class="col4"><?=showSign($charInfo['ref_misc'])?></div>
			</div>
			<div id="willRow" class="tr dataTR">
				<label class="leftLabel">Will</label>
				<div id="willTotal" class="col4 lrBuffer"><?=$willBonus?></div>
				<div class="col4"><?=showSign($charInfo['will_base'])?></div>
				<div class="col4 lrBuffer statBonus_wis"><?=$statBonus['wis']?></div>
				<div class="col4"><?=showSign($charInfo['will_magic'])?></div>
				<div class="col4"><?=showSign($charInfo['will_race'])?></div>
				<div class="col4"><?=showSign($charInfo['will_misc'])?></div>
			</div>
		</div>
		
		<div id="hp" class="dataTR">
			<label class="leftLabel textLabel">Total HP</label>
			<div><?=$charInfo['hp']?></div>
			<label class="leftLabel textLabel">Damage Reduction</label>
			<div><?=$charInfo['dr']?></div>
		</div>
		
		<br class="clear">
		<div id="ac">
			<div class="tr labelTR">
				<label class="col5 lrBuffer first">Total AC</label>
				<label class="col5 lrBuffer">Armor</label>
				<label class="col5 lrBuffer">Shield</label>
				<label class="col5 lrBuffer">Dex</label>
				<label class="col5 lrBuffer">Class</label>
				<label class="col5 lrBuffer">Size</label>
				<label class="col5 lrBuffer">Natural</label>
				<label class="col5 lrBuffer">Deflection</label>
				<label class="col5 lrBuffer">Misc</label>
			</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_shield'] + $charInfo['ac_dex'] + $charInfo['ac_class'] + $charInfo['size'] + $charInfo['ac_natural'] + $charInfo['ac_deflection'] + $charInfo['ac_misc']; ?>
			<div class="tr dataTR">
				<div class="col5 first"><?=$acTotal?></div>
				<div class="col5"> = 10 + </div>
				<div class="col5"><?=showSign($charInfo['ac_armor'])?></div>
				<div class="col5"><?=showSign($charInfo['ac_shield'])?></div>
				<div class="col5"><?=$charInfo['ac_dex']?></div>
				<div class="col5"><?=showSign($charInfo['ac_class'])?></div>
				<div class="col5"><?=$charInfo['size']?></div>
				<div class="col5"><?=showSign($charInfo['ac_natural'])?></div>
				<div class="col5"><?=showSign($charInfo['ac_deflection'])?></div>
				<div class="col5"><?=showSign($charInfo['ac_misc'])?></div>
			</div>
		</div>
		
		<div id="combatBonuses" class="clearFix">
			<div class="tr labelTR">
				<label class="statCol col4 lrBuffer first">Total</label>
				<label class="statCol col4 lrBuffer">Base</label>
				<label class="statCol col4 lrBuffer">Ability</label>
				<label class="statCol col4 lrBuffer">Size</label>
				<label class="statCol col4 lrBuffer">Misc</label>
			</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['size'] + $charInfo['ranged_misc']);
?>
			<div id="init" class="tr dataTR">
				<label class="leftLabel col3">Initiative</label>
				<span id="initTotal" class="col4 lrBuffer"><?=$initTotal?></span>
				<span class="lrBuffer">&nbsp;</span>
				<span class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
				<span class="lrBuffer">&nbsp;</span>
				<div class="col4"><?=showSign($charInfo['initiative_misc'])?></div>
			</div>
			<div id="melee" class="tr dataTR">
				<label class="leftLabel col3">Melee</label>
				<span id="meleeTotal" class="col4 lrBuffer"><?=$meleeTotal?></span>
				<div class="col4"><?=showSign($charInfo['bab'])?></div>
				<span class="col4 lrBuffer statBonus_str"><?=$statBonus['str']?></span>
				<span class="col4 lrBuffer sizeVal"><?=$charInfo['size']?></span>
				<div class="col4"><?=showSign($charInfo['melee_misc'])?></div>
			</div>
			<div id="ranged" class="tr dataTR">
				<label class="leftLabel col3">Ranged</label>
				<span id="rangedTotal" class="col4 lrBuffer"><?=$rangedTotal?></span>
				<span class="col4 lrBuffer bab"><?=showSign($charInfo['bab'])?></span>
				<span class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
				<span class="col4 lrBuffer sizeVal"><?=$charInfo['size']?></span>
				<div class="col4"><?=showSign($charInfo['ranged_misc'])?></div>
			</div>
		</div>
		
<?
	$cmb = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size']);
	$cmd = showSign($charInfo['bab'] + $statBonus['str'] + $statBonus['dex'] + $charInfo['size'] + 10);
?>
		<div id="combatManuvers">
			<div id="cmb">
				<div class="tr labelTR">
					<label class="statCol col4 first">Total</label>
					<label class="statCol col4">Base</label>
					<label class="statCol col4">Str</label>
					<label class="statCol col4">Size</label>
				</div>
				<div class="tr">
					<label class="leftLabel col5">CMB</label>
					<div class="col4 cell addStat_str subSize addBAB"><?=$cmb?></div>
					<div class="col4 cell bab"><?=$charInfo['bab']?></div>
					<div class="col4 cell statBonus_str"><?=$statBonus['str']?></div>
					<div class="col4 cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
				</div>
			</div>
			
			<div id="cmd">
				<div class="tr labelTR">
					<label class="statCol col4 first">Total</label>
					<label class="statCol col4">Base</label>
					<label class="statCol col4">Str</label>
					<label class="statCol col4">Dex</label>
					<label class="statCol col4">Size</label>
				</div>
				<div class="tr">
					<label class="leftLabel col5">CMD</label>
					<div class="col4 cell addStat_str addStat_dex subSize addBAB"><?=$cmd?></div>
					<div class="col4 cell bab"><?=$charInfo['bab']?></div>
					<div class="col4 cell statBonus_str"><?=$statBonus['str']?></div>
					<div class="col4 cell statBonus_dex"><?=$statBonus['dex']?></div>
					<div class="col4 cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
					<div class="col4 cell">+ 10</div>
				</div>
			</div>
		</div>
		
		<br class="clear">
		<div id="skills" class="textDiv floatLeft">
			<h2>Skills</h2>
			<div><?=$charInfo['skills']?></div>
		</div>
		<div id="feats" class="textDiv floatRight">
			<h2>Feats/Abilities</h2>
			<div><?=$charInfo['feats']?></div>
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
		
		<div id="spells">
			<h2>Spells</h2>
			<div><?=$charInfo['spells']?></div>
		</div>
		
		<div id="notes">
			<h2>Notes</h2>
			<div><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>