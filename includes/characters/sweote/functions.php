<?
	function weaponFormFormat($weaponNum = 1, $weaponInfo = array()) {
		if (!is_numeric($weaponNum)) $weaponNum = 1;
		if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) $weaponInfo = array();
?>
						<div class="weapon<?=$weaponNum == 1?' first':''?>">
							<div class="tr labelTR">
								<label class="medText lrBuffer borderBox shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
								<label class="shortText alignCenter lrBuffer">Damage</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][ab]" value="<?=$weaponInfo['ab']?>" class="weapons_ab shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
							</div>
							<div class="tr labelTR weapon_secondRow">
								<label class="shortText alignCenter lrBuffer">Critical</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][critical]" value="<?=$weaponInfo['critical']?>" class="weapon_crit shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][size]" value="<?=$weaponInfo['size']?>" class="weapon_size shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][notes]" value="<?=$weaponInfo['notes']?>" class="weapon_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
	}

	function skillFormFormat($skillInfo) {
		global $stats;
		if (is_array($skillInfo)) {
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
							<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
							<span class="skill_stat textLabel lrBuffer alignCenter"><?=ucwords($stats[$skillInfo['stat']])?></span>
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][rank]" value="<?=$skillInfo['rank']?>" class="skill_rank shortNum lrBuffer">
							<span class="skill_career shortNum lrBuffer alignCenter"><input type="checkbox" name="skills[<?=$skillInfo['skillID']?>][career]" value="<?=$skillInfo['career']?>"></span>
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="<?=SITEROOT?>/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}
	}

	function talentFormFormat($characterID, $talentInfo) {
		if (is_array($talentInfo)) {
?>
						<div id="talent_<?=$talentInfo['talentID']?>" class="talent tr clearfix">
							<span class="talent_name textLabel"><?=mb_convert_case($talentInfo['name'], MB_CASE_TITLE)?></span>
							<a href="<?=SITEROOT?>/characters/sweote/<?=$characterID?>/editTalentNotes/<?=$talentInfo['talentID']?>" id="talentNotesLink_<?=$talentInfo['talentID']?>" class="talent_notesLink">Notes</a>
							<input type="image" name="talentRemove_<?=$talentInfo['talentID']?>" src="<?=SITEROOT?>/images/cross.png" value="<?=$talentInfo['talentID']?>" class="talent_remove lrBuffer">
						</div>
<?
		}
	}
?>