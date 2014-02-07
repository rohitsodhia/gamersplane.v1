<?
	function attackFormFormat($attackNum = 1, $attackInfo = array()) {
		if (!is_int($attackNum)) $attackNum = 1;
		if (!is_array($attackInfo) || sizeof($attackInfo) == 0) $attackInfo = array();
		$defaults = array('total' => 0, 'stat' => 0, 'class' => 0, 'prof' => 0, 'feat' => 0, 'enh' => 0, 'misc' => 0);
		foreach ($defaults as $key => $value) if (!isset($attackInfo[$key])) $attackInfo[$key] = $value;
?>
					<div class="attackBonusSet">
						<div class="tr">
							<label class="medNum leftLabel">Ability</label>
							<input type="text" name="attacks[<?=$attackNum?>][ability]" value="<?=$attackInfo['ability']?>" class="ability">
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
							<span class="shortNum lrBuffer addHL total"><?=showSign($attackInfo['total'])?></span>
							<span class="shortNum lrBuffer addHL">+<?=floor($attackInfo['charLevel'] / 2)?></span>
							<input type="text" name="attacks[<?=$attackNum?>][stat]" value="<?=$attackInfo['stat']?>" class="statInput lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][class]" value="<?=$attackInfo['class']?>" class="statInput lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][prof]" value="<?=$attackInfo['prof']?>" class="statInput lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][feat]" value="<?=$attackInfo['feat']?>" class="statInput lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][enh]" value="<?=$attackInfo['enh']?>" class="statInput lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][misc]" value="<?=$attackInfo['misc']?>" class="statInput lrBuffer">
						</div>
					</div>
<?
	}

	function skillFormFormat($skillInfo, $statBonus = 0) {
		if (is_array($skillInfo)) {
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
							<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
							<span class="skill_total textLabel lrBuffer addStat_<?=$skillInfo['stat']?> addHL shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat textLabel lrBuffer alignCenter shortNum"><?=ucwords($skillInfo['stat'])?></span>
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="<?=SITEROOT?>/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}
	}

	function featFormFormat($characterID, $featInfo) {
		if (is_array($featInfo)) {
?>
						<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
							<span class="feat_name textLabel"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
							<a href="<?=SITEROOT?>/characters/dnd4/<?=$characterID?>/editFeatNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
							<input type="image" name="featRemove_<?=$featInfo['featID']?>" src="<?=SITEROOT?>/images/cross.png" value="<?=$featInfo['featID']?>" class="feat_remove lrBuffer">
						</div>
<?
		}
	}

	function powerFormFormat($power) {
		if (is_array($power)) {
?>
						<div class="power">
							<span id="power_<?=$power['powerID']?>" class="power_name"><?=mb_convert_case($power['name'], MB_CASE_TITLE)?></span>
							<input type="image" name="removePower_<?=$power['powerID']?>" src="<?=SITEROOT?>/images/cross.png" value="<?=$power['powerID']?>" class="power_remove lrBuffer">
						</div>
<?
		}
	}
?>