<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'sweote';
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
				<a id="editCharacter" href="<?=SITEROOT?>/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
			<label id="label_species" class="medText">Species</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['species']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_career" class="medText">Career</label>
			<label id="label_specialization" class="medText">Specialization</label>
			<label id="label_totalXP" class="shortText">Total XP</label>
			<label id="label_spentXP" class="shortText">Spent XP</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['career']?></div>
			<div class="medText"><?=$charInfo['specialization']?></div>
			<div class="shortText"><?=$charInfo['totalXP']?></div>
			<div class="shortText"><?=$charInfo['spentXP']?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats">
<?
	$count = 0;
	foreach ($stats as $short => $stat) {
		if ($count % 3 == 0) echo "				<div class=\"col\">\n";
?>
					<div class="tr">
						<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
						<div class="stat"><?=$charInfo[strtolower($stat)]?></div>
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
						<label class="leftLabel lrBuffer">Defense (Melee)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['defense_melee']?></div>
					</div>
					<div class="tr">
						<label class="leftLabel lrBuffer">Defense (Ranged)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['defense_ranged']?></div>
					</div>
					<div class="tr">
						<label class="leftLabel lrBuffer">Soak</label>
						<div class="shortNum lrBuffer"><?=$charInfo['soak']?></div>
					</div>
				</div>
				<div class="col">
					<div class="tr">
						<label class="leftLabel lrBuffer">Strain (Max)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['strain_max']?></div>
					</div>
					<div class="tr">
						<label class="leftLabel lrBuffer">Strain (Current)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['strain_current']?></div>
					</div>
					<div class="tr">
						<label class="leftLabel lrBuffer">Wounds (Max)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['wounds_max']?></div>
					</div>
					<div class="tr">
						<label class="leftLabel lrBuffer">Wounds (Current)</label>
						<div class="shortNum lrBuffer"><?=$charInfo['wounds_current']?></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="skills" class="floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined">
					<div class="tr labelTR">
						<label class="medText">Skill</label>
						<label class="skill_stat alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Rank</label>
						<label class="shortNum alignCenter lrBuffer">Career</label>
					</div>
<?
	$skills = $mysql->query('SELECT ss.skillID, sl.name, ss.stat, ss.rank, ss.career FROM sweote_skills ss INNER JOIN skillsList sl USING (skillID) WHERE ss.characterID = '.$characterID.' ORDER BY sl.name');
	if ($skills->rowCount()) { foreach ($skills as $skill) { ?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($stats[$skill['stat']])?></span>
						<span class="skill_rank alignCenter shortNum lrBuffer"><?=$skill['rank']?></span>
						<span class="skill_career alignCenter shortNum lrBuffer"><?=$skill['career']?'<img src="'.SITEROOT.'/images/check.png">':''?></span>
					</div>
<?	} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n"; ?>
				</div>
			</div>
			<div id="talents" class="floatRight">
				<h2 class="headerbar hbDark">Talents</h2>
				<div class="hbdMargined">
<?
	$talents = $mysql->query("SELECT ct.talentID, tl.name FROM sweote_talents ct INNER JOIN sweote_talentsList tl USING (talentID) WHERE ct.characterID = $characterID ORDER BY tl.name");
	if ($talents->rowCount()) { foreach ($talents as $talent) { ?>
					<div id="talent_<?=$talent['talentID']?>" class="talent tr clearfix">
						<span class="talent_name"><?=mb_convert_case($talent['name'], MB_CASE_TITLE)?></span>
						<a href="<?=SITEROOT?>/characters/sweote/<?=$characterID?>/talentNotes/<?=$talent['talentID']?>" class="talent_notesLink">Notes</a>
					</div>
<?	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no talents.</p>\n"; ?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbdMargined">
<?
	$weapons = $mysql->query('SELECT * FROM sweote_weapons WHERE characterID = '.$characterID);
	foreach ($weapons as $weapon) {
	?>
					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer">Name</label>
							<label class="weapons_skill alignCenter lrBuffer">Skill</label>
						</div>
						<div class="tr">
							<span class="weapon_name medText lrBuffer"><?=$weapon['name']?></span>
							<span class="weapons_skill lrBuffer alignCenter"><?=$weapon['skill']?></span>
						</div>
						<div class="tr labelTR weapon_secondRow">
							<label class="shortText alignCenter lrBuffer">Damage</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Critical</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weapon['damage']?></span>
							<span class="weapon_range shortText lrBuffer alignCenter"><?=$weapon['range']?></span>
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['critical']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="weapon_notes lrBuffer"><?=$weapon['notes']?></span>
						</div>
					</div>
<?
	}
?>
				</div>
			</div>
			<div id="items" class="floatRight">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=$charInfo['items']?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="motivations" class="floatLeft">
				<h2 class="headerbar hbDark">Motivations</h2>
				<div class="hbdMargined"><?=$charInfo['motivations']?></div>
			</div>
			
			<div id="obligations" class="floatRight">
				<h2 class="headerbar hbDark">Obligations</h2>
				<div class="hbdMargined"><?=$charInfo['obligations']?></div>
			</div>
		</div>

		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>