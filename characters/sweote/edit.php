<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM sweote_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		includeSystemInfo('sweote');
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/sweote.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/sweote/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
				<label id="label_species" class="medText lrBuffer borderBox shiftRight">Species</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="species" value="<?=$charInfo['species']?>" class="medText lrBuffer">
			</div>
			<div class="tr labelTR">
				<label id="label_career" class="medText lrBuffer borderBox shiftRight">Career</label>
				<label id="label_specialization" class="medText lrBuffer borderBox shiftRight">Specialization</label>
				<label id="label_totalXP" class="shortText lrBuffer borderBox shiftRight">Total XP</label>
				<label id="label_spentXP" class="shortText lrBuffer borderBox shiftRight">Spent XP</label>
			</div>
			<div class="tr">
				<input type="text" name="career" value="<?=$charInfo['career']?>" class="medText lrBuffer">
				<input type="text" name="specialization" value="<?=$charInfo['specialization']?>" class="medText lrBuffer">
				<input type="text" name="totalXP" value="<?=$charInfo['totalXP']?>" class="shortText lrBuffer">
				<input type="text" name="spentXP" value="<?=$charInfo['spentXP']?>" class="shortText lrBuffer">
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$count = 0;
	foreach ($stats as $short => $stat) {
		if ($count % 3 == 0) echo "					<div class=\"col\">\n";
?>
						<div class="tr">
							<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
							<input type="text" id="<?=strtolower($stat)?>" name="<?=strtolower($stat)?>" value="<?=$charInfo[strtolower($stat)]?>" maxlength="2" class="stat lrBuffer">
						</div>
<?
		if ($count % 3 == 2) echo "					</div>\n";
		$count++;
	}
?>
				</div>
				<div id="defense">
					<div class="col">
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Defense (Melee)</label>
							<input type="text" name="defense_melee" value="<?=$charInfo['defense_melee']?>" maxlength="2" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Defense (Ranged)</label>
							<input type="text" name="defense_ranged" value="<?=$charInfo['defense_ranged']?>" maxlength="2" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Soak</label>
							<input type="text" name="soak" value="<?=$charInfo['soak']?>" maxlength="2" class="lrBuffer">
						</div>
					</div>
					<div class="col">
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Strain (Max)</label>
							<input type="text" name="strain_max" value="<?=$charInfo['strain_max']?>" maxlength="2" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Strain (Current)</label>
							<input type="text" name="strain_current" value="<?=$charInfo['strain_current']?>" maxlength="2" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Wounds (Max)</label>
							<input type="text" name="wounds_max" value="<?=$charInfo['wounds_max']?>" maxlength="2" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="textLabel leftLabel lrBuffer">Wounds (Current)</label>
							<input type="text" name="wounds_current" value="<?=$charInfo['wounds_current']?>" maxlength="2" class="lrBuffer">
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div id="addSkillWrapper">
							<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
							<select id="skillStat" name="newSkill[stat]">
<?	foreach ($stats as $short => $stat) { ?>
								<option value="<?=$short?>"><?=$stat?></option>
<?	} ?>
							</select>
							<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
						</div>
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="skill_stat alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Rank</label>
							<label class="shortNum alignCenter lrBuffer">Career</label>
						</div>
<?
	$skills = $mysql->query('SELECT ss.skillID, sl.name, ss.stat, ss.rank, ss.career FROM sweote_skills ss INNER JOIN skillsList sl USING (skillID) WHERE ss.characterID = '.$characterID.' ORDER BY sl.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		skillFormFormat($skillInfo, $statBonus[$skillInfo['stat']]);
	} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?	} ?>
					</div>
				</div>
				<div id="talents" class="floatRight">
					<h2 class="headerbar hbDark">Talents</h2>
					<div class="hbdMargined">
						<div id="addTalentWrapper">
							<input id="talentName" type="text" name="newTalent_name" class="medText placeholder" autocomplete="off" data-placeholder="Talent Name">
							<button id="addTalent" type="submit" name="newTalent_add" class="fancyButton">Add</button>
						</div>
<?
	$talents = $mysql->query("SELECT ct.talentID, tl.name FROM sweote_talents ct INNER JOIN sweote_talentsList tl USING (talentID) WHERE ct.characterID = $characterID ORDER BY tl.name");
	if ($talents->rowCount()) { foreach ($talents as $talentInfo) {
		talentFormFormat($characterID, $talentInfo);
	} } else { ?>
					<p id="noTalents">This character currently has no talents.</p>
<?	} ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
					<div class="hbMargined">
<?
	$weapons = $mysql->query('SELECT * FROM dnd3_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 1) weaponFormFormat($weaponNum++, $weaponInfo);
?>
					</div>
				</div>
			
				<div id="items" class="floatRight">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
				</div>
			</div>

			<div class="clearfix">
				<div id="motivations" class="floatLeft">
					<h2 class="headerbar hbDark">Motivations</h2>
					<textarea name="motivations" class="hbdMargined"><?=$charInfo['motivations']?></textarea>
				</div>
				<div id="obligations" class="floatRight">
					<h2 class="headerbar hbDark">Obligations</h2>
					<textarea name="obligations" class="hbdMargined"><?=$charInfo['obligations']?></textarea>
				</div>
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