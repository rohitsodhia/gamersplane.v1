<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT wod.*, characters.userID, gms.gameID IS NOT NULL isGM FROM wod_characters wod INNER JOIN characters ON wod.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE wod.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/wod.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<a href="<?=SITEROOT?>/characters/wod/<?=$characterID?>/edit">Edit Character</a>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=strlen($charInfo['name'])?printReady($charInfo['name']):'&nbsp;'?></div>
		</div>
		
		<div id="attributes">
			<h2>Attributes</h2>
			<table>
				<tr>
					<th>Power</th>
					<td class="statName">Intelligence</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['int']}.jpg"?>"></td>
					<td class="statName">Strength</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['str']}.jpg"?>"></td>
					<td class="statName">Presence</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['pre']}.jpg"?>"></td>
				</tr>
				<tr>
					<th>Finesse</th>
					<td class="statName">Wits</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['wit']}.jpg"?>"></td>
					<td class="statName">Dexterity</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['dex']}.jpg"?>"></td>
					<td class="statName">Manipulation</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['man']}.jpg"?>"></td>
				</tr>
				<tr>
					<th>Resistance</th>
					<td class="statName">Resolve</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['res']}.jpg"?>"></td>
					<td class="statName">Stamina</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['sta']}.jpg"?>"></td>
					<td class="statName">Composure</td>
					<td class="statVal"><img src="<?=SITEROOT."/images/wod_dots_{$charInfo['com']}.jpg"?>"></td>
				</tr>
			</table>
		</div>
		
		<div id="leftCol" class="triCol">
			<h2 id="leftColHeader">Skills</h2>
			<h3>Mental</h3>
			<p>(-3 unskilled)</p>
<?
	foreach (array('Academics', 'Computer', 'Crafts', 'Investigation', 'Medicine', 'Occult', 'Politics', 'Science') as $skill) {
		echo "				
			<div class=\"tr\">
				{$skill}
				<div class=\"skillRank\"><img src=\"".SITEROOT.'/images/wod_dots_'.$charInfo[strtolower($skill)].".jpg\"></div>
			</div>
";
	}
?>
			
			<h3>Physical</h3>
			<p>(-1 unskilled)</p>
<?
	foreach (array('Athletics', 'Brawl', 'Drive', 'Firearms', 'Larceny', 'Stealth', 'Survival', 'Weaponry') as $skill) {
		echo "				
			<div class=\"tr\">
				{$skill}
				<div class=\"skillRank\"><img src=\"".SITEROOT.'/images/wod_dots_'.$charInfo[strtolower($skill)].".jpg\"></div>
			</div>
";
	}
?>
			
			<h3>Social</h3>
			<p>(-1 unskilled)</p>
<?
	foreach (array('Animal Ken', 'Empathy', 'Expression', 'Intimidation', 'Persuasion', 'Socialize', 'Streetwise', 'Subterfuge') as $skill) {
		echo "				
			<div class=\"tr\">
				{$skill}
				<div class=\"skillRank\"><img src=\"".SITEROOT.'/images/wod_dots_'.$charInfo[camelcase($skill)].".jpg\"></div>
			</div>
";
	}
?>
		</div>
		
		<h2 id="rightColHeader">Other Traits</h2>
		<div class="triCol rightCol">
			<h3>Merits</h3>
			<p><?=strlen($charInfo['merits'])?printReady($charInfo['merits']):'&nbsp;'?></p>
			
			<h3 class="marginTop">Flaws</h3>
			<p><?=strlen($charInfo['flaws'])?printReady($charInfo['flaws']):'&nbsp;'?></p>
		</div>
		<div class="triCol rightCol lastCol">
			<h3>Health</h3>
			<div class="alignCenter"><?=strlen($charInfo['health'])?printReady($charInfo['health']):'&nbsp;'?></div>
			
			<h3 class="marginTop">Willpower</h3>
			<div class="alignCenter"><?=strlen($charInfo['willpower'])?printReady($charInfo['willpower']):'&nbsp;'?></div>
			
			<h3 class="marginTop">Morality</h3>
			<div class="alignCenter"><?=strlen($charInfo['morality'])?printReady($charInfo['morality']):'&nbsp;'?></div>
			
			<div class="tr marginTop">
				<label>Size</label>
				<div class="value"><?=strlen($charInfo['size'])?printReady($charInfo['size']):'&nbsp;'?></div>
			</div>
			<div class="tr">
				<label>Speed</label>
				<div class="value"><?=strlen($charInfo['speed'])?printReady($charInfo['speed']):'&nbsp;'?></div>
			</div>
			<div class="tr">
				<label>Initiative Mod</label>
				<div class="value"><?=strlen($charInfo['initiativeMod'])?printReady($charInfo['initiativeMod']):'&nbsp;'?></div>
			</div>
			<div class="tr">
				<label>Defense</label>
				<div class="value"><?=strlen($charInfo['defense'])?printReady($charInfo['defense']):'&nbsp;'?></div>
			</div>
			<div class="tr">
				<label>Armor</label>
				<div class="value"><?=strlen($charInfo['armor'])?printReady($charInfo['armor']):'&nbsp;'?></div>
			</div>
		</div>
		
		<div id="itemsDiv">
			<h3 class="marginTop">Weapons</h3>
			<?=strlen($charInfo['weapons'])?printReady($charInfo['weapons']):'&nbsp;'?>
			
			<h3 class="marginTop">Equipment</h3>
			<?=strlen($charInfo['equipment'])?printReady($charInfo['equipment']):'&nbsp;'?>
		</div>
		
		<br class="clear">
		<h2 id="notesTitle" class="marginTop">Notes</h3>
		<?=strlen($charInfo['notes'])?printReady($charInfo['notes']):'&nbsp;'?>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>