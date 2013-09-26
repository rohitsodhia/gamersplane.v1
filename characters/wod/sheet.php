<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM wod_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/wod.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/wod/<?=$characterID?>/edit" class="fancyButton">Edit Character</a></div>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=strlen($charInfo['name'])?printReady($charInfo['name']):'&nbsp;'?></div>
		</div>
		
		<div id="attributes">
			<h2 class="headerbar hbDark">Attributes</h2>
			<table>
				<tr>
					<th>Power</th>
					<td class="statName">Intelligence</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['int']?>"></div></div></td>
					<td class="statName">Strength</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['str']?>"></div></div></td>
					<td class="statName">Presence</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['pre']?>"></div></div></td>
				</tr>
				<tr>
					<th>Finesse</th>
					<td class="statName">Wits</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['wit']?>"></div></div></td>
					<td class="statName">Dexterity</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['dex']?>"></div></div></td>
					<td class="statName">Manipulation</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['man']?>"></div></div></td>
				</tr>
				<tr>
					<th>Resistance</th>
					<td class="statName">Resolve</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['res']?>"></div></div></td>
					<td class="statName">Stamina</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['sta']?>"></div></div></td>
					<td class="statName">Composure</td>
					<td class="statVal"><div class="dots"><div class="dotCount_<?=$charInfo['com']?>"></div></div></td>
				</tr>
			</table>
		</div>
		
		<div class="clearfix">
			<div id="skills">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined">
					<div id="skills_mental" class="skillSet">
						<h3>Mental</h3>
						<p>(-3 unskilled)</p>
<? foreach (array('Academics', 'Computer', 'Crafts', 'Investigation', 'Medicine', 'Occult', 'Politics', 'Science') as $skill) { ?>
						<div class="tr">
							<label><?=$skill?></label>
							<div class="skillRank"><div class="dots"><div class="dotCount_<?=$charInfo[strtolower($skill)]?>"></div></div></div>
						</div>
<? } ?>
					</div>
					
					<div id="skills_physical" class="skillSet">
						<h3>Physical</h3>
						<p>(-1 unskilled)</p>
<? foreach (array('Athletics', 'Brawl', 'Drive', 'Firearms', 'Larceny', 'Stealth', 'Survival', 'Weaponry') as $skill) { ?>
						<div class="tr">
							<label><?=$skill?></label>
							<div class="skillRank"><div class="dots"><div class="dotCount_<?=$charInfo[strtolower($skill)]?>"></div></div></div>
						</div>
<? } ?>
					</div>
					
					<div id="skills_social" class="skillSet">
						<h3>Social</h3>
						<p>(-1 unskilled)</p>
<? foreach (array('Animal Ken', 'Empathy', 'Expression', 'Intimidation', 'Persuasion', 'Socialize', 'Streetwise', 'Subterfuge') as $skill) { ?>
						<div class="tr">
							<label><?=$skill?></label>
							<div class="skillRank"><div class="dots"><div class="dotCount_<?=$charInfo[strtolower($skill)]?>"></div></div></div>
						</div>
<? } ?>
					</div>
				</div>
			</div>
			
			<div id="otherTraits">
				<h2 class="headerbar hbDark">Other Traits</h2>
				<div class="hbdMargined clearfix">
					<div class="col floatLeft">
						<div id="merits">
							<h3>Merits</h3>
							<div><?=$charInfo['merits']?></div>
						</div>
						
						<div id="flaws" class="marginTop">
							<h3>Flaws</h3>
							<div><?=$charInfo['flaws']?></div>
						</div>
					</div>
					<div class="col floatRight">
						<div class="clearfix">
							<div id="health" class="alignCenter">
								<h3>Health</h3>
								<div><?=$charInfo['health']?></div>
							</div>
							<div id="willpower" class="alignCenter">
								<h3>Willpower</h3>
								<div><?=$charInfo['willpower']?></div>
							</div>
							<div id="morality" class="alignCenter">
								<h3>Morality</h3>
								<div><?=$charInfo['morality']?></div>
							</div>
						</div>
						
						<div class="tr marginTop">
							<label class="textLabel">Size</label>
							<div><?=$charInfo['size']?></div>
						</div>
						<div class="tr">
							<label class="textLabel">Speed</label>
							<div><?=$charInfo['speed']?></div>
						</div>
						<div class="tr">
							<label class="textLabel">Initiative Mod</label>
							<div><?=$charInfo['initiativeMod']?></div>
						</div>
						<div class="tr">
							<label class="textLabel">Defense</label>
							<div><?=$charInfo['defense']?></div>
						</div>
						<div class="tr">
							<label class="textLabel">Armor</label>
							<div><?=$charInfo['armor']?></div>
						</div>
					</div>
				</div>
			</div>
			
			<div id="itemsDiv">
				<div id="weapons">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div class="hbdMargined"><?=$charInfo['weapons']?></div>
				</div>
				<div id="equipment">
					<h2 class="headerbar hbDark">Equipment</h2>
					<div class="hbdMargined"><?=$charInfo['equipment']?></div>
				</div>
			</div>
		</div>
		
		<div id="notes" class="marginTop">
			<h2 id="notesTitle" class="headerbar hbDark">Notes</h3>
			<div class="hbdMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>