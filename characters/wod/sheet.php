<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'wod';
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
				<a id="editCharacter" href="/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

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