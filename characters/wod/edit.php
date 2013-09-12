<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM wod_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/wod.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/wod/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>"></div>
			</div>
			
			<div id="attributes">
				<h2 class="headerbar hbDark">Attributes</h2>
				<table>
					<tr>
						<th>Power</th>
						<td class="statName">Intelligence</td>
						<td class="statVal">
							<input type="radio" name="int" value="1"<?=$charInfo['int'] == 1?' checked="checked"':''?>>
							<input type="radio" name="int" value="2"<?=$charInfo['int'] == 2?' checked="checked"':''?>>
							<input type="radio" name="int" value="3"<?=$charInfo['int'] == 3?' checked="checked"':''?>>
							<input type="radio" name="int" value="4"<?=$charInfo['int'] == 4?' checked="checked"':''?>>
							<input type="radio" name="int" value="5"<?=$charInfo['int'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Strength</td>
						<td class="statVal">
							<input type="radio" name="str" value="1"<?=$charInfo['str'] == 1?' checked="checked"':''?>>
							<input type="radio" name="str" value="2"<?=$charInfo['str'] == 2?' checked="checked"':''?>>
							<input type="radio" name="str" value="3"<?=$charInfo['str'] == 3?' checked="checked"':''?>>
							<input type="radio" name="str" value="4"<?=$charInfo['str'] == 4?' checked="checked"':''?>>
							<input type="radio" name="str" value="5"<?=$charInfo['str'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Presence</td>
						<td class="statVal">
							<input type="radio" name="pre" value="1"<?=$charInfo['pre'] == 1?' checked="checked"':''?>>
							<input type="radio" name="pre" value="2"<?=$charInfo['pre'] == 2?' checked="checked"':''?>>
							<input type="radio" name="pre" value="3"<?=$charInfo['pre'] == 3?' checked="checked"':''?>>
							<input type="radio" name="pre" value="4"<?=$charInfo['pre'] == 4?' checked="checked"':''?>>
							<input type="radio" name="pre" value="5"<?=$charInfo['pre'] == 5?' checked="checked"':''?>>
						</td>
					</tr>
					<tr>
						<th>Finesse</th>
						<td class="statName">Wits</td>
						<td class="statVal">
							<input type="radio" name="wit" value="1"<?=$charInfo['wit'] == 1?' checked="checked"':''?>>
							<input type="radio" name="wit" value="2"<?=$charInfo['wit'] == 2?' checked="checked"':''?>>
							<input type="radio" name="wit" value="3"<?=$charInfo['wit'] == 3?' checked="checked"':''?>>
							<input type="radio" name="wit" value="4"<?=$charInfo['wit'] == 4?' checked="checked"':''?>>
							<input type="radio" name="wit" value="5"<?=$charInfo['wit'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Dexterity</td>
						<td class="statVal">
							<input type="radio" name="dex" value="1"<?=$charInfo['dex'] == 1?' checked="checked"':''?>>
							<input type="radio" name="dex" value="2"<?=$charInfo['dex'] == 2?' checked="checked"':''?>>
							<input type="radio" name="dex" value="3"<?=$charInfo['dex'] == 3?' checked="checked"':''?>>
							<input type="radio" name="dex" value="4"<?=$charInfo['dex'] == 4?' checked="checked"':''?>>
							<input type="radio" name="dex" value="5"<?=$charInfo['dex'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Manipulation</td>
						<td class="statVal">
							<input type="radio" name="man" value="1"<?=$charInfo['man'] == 1?' checked="checked"':''?>>
							<input type="radio" name="man" value="2"<?=$charInfo['man'] == 2?' checked="checked"':''?>>
							<input type="radio" name="man" value="3"<?=$charInfo['man'] == 3?' checked="checked"':''?>>
							<input type="radio" name="man" value="4"<?=$charInfo['man'] == 4?' checked="checked"':''?>>
							<input type="radio" name="man" value="5"<?=$charInfo['man'] == 5?' checked="checked"':''?>>
						</td>
					</tr>
					<tr>
						<th>Resistance</th>
						<td class="statName">Resolve</td>
						<td class="statVal">
							<input type="radio" name="res" value="1"<?=$charInfo['res'] == 1?' checked="checked"':''?>>
							<input type="radio" name="res" value="2"<?=$charInfo['res'] == 2?' checked="checked"':''?>>
							<input type="radio" name="res" value="3"<?=$charInfo['res'] == 3?' checked="checked"':''?>>
							<input type="radio" name="res" value="4"<?=$charInfo['res'] == 4?' checked="checked"':''?>>
							<input type="radio" name="res" value="5"<?=$charInfo['res'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Stamina</td>
						<td class="statVal">
							<input type="radio" name="sta" value="1"<?=$charInfo['sta'] == 1?' checked="checked"':''?>>
							<input type="radio" name="sta" value="2"<?=$charInfo['sta'] == 2?' checked="checked"':''?>>
							<input type="radio" name="sta" value="3"<?=$charInfo['sta'] == 3?' checked="checked"':''?>>
							<input type="radio" name="sta" value="4"<?=$charInfo['sta'] == 4?' checked="checked"':''?>>
							<input type="radio" name="sta" value="5"<?=$charInfo['sta'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Composure</td>
						<td class="statVal">
							<input type="radio" name="com" value="1"<?=$charInfo['com'] == 1?' checked="checked"':''?>>
							<input type="radio" name="com" value="2"<?=$charInfo['com'] == 2?' checked="checked"':''?>>
							<input type="radio" name="com" value="3"<?=$charInfo['com'] == 3?' checked="checked"':''?>>
							<input type="radio" name="com" value="4"<?=$charInfo['com'] == 4?' checked="checked"':''?>>
							<input type="radio" name="com" value="5"<?=$charInfo['com'] == 5?' checked="checked"':''?>>
						</td>
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
								<div class="skillRank">
									<span>0</span>
									<input type="radio" name="<?=strtolower($skill)?>" value="0"<?=($charInfo[strtolower($skill)] == 0?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="1"<?=($charInfo[strtolower($skill)] == 1?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="2"<?=($charInfo[strtolower($skill)] == 2?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="3"<?=($charInfo[strtolower($skill)] == 3?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="4"<?=($charInfo[strtolower($skill)] == 4?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="5"<?=($charInfo[strtolower($skill)] == 5?' checked="checked"':'')?>>
									<span>5</span>
								</div>
							</div>
<? } ?>
						</div>
						
						<div id="skills_physical" class="skillSet">
							<h3>Physical</h3>
							<p>(-1 unskilled)</p>
<? foreach (array('Athletics', 'Brawl', 'Drive', 'Firearms', 'Larceny', 'Stealth', 'Survival', 'Weaponry') as $skill) { ?>
							<div class="tr">
								<label><?=$skill?></label>
								<div class="skillRank">
									<span>0</span>
									<input type="radio" name="<?=strtolower($skill)?>" value="0"<?=($charInfo[strtolower($skill)] == 0?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="1"<?=($charInfo[strtolower($skill)] == 1?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="2"<?=($charInfo[strtolower($skill)] == 2?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="3"<?=($charInfo[strtolower($skill)] == 3?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="4"<?=($charInfo[strtolower($skill)] == 4?' checked="checked"':'')?>>
									<input type="radio" name="<?=strtolower($skill)?>" value="5"<?=($charInfo[strtolower($skill)] == 5?' checked="checked"':'')?>>
									<span>5</span>
								</div>
							</div>
<? } ?>
						</div>
						
						<div id="skills_social" class="skillSet">
							<h3>Social</h3>
							<p>(-1 unskilled)</p>
<? foreach (array('Animal Ken', 'Empathy', 'Expression', 'Intimidation', 'Persuasion', 'Socialize', 'Streetwise', 'Subterfuge') as $skill) { ?>
							<div class="tr">
								<label><?=$skill?></label>
								<div class="skillRank">
									<span>0</span>
									<input type="radio" name="<?=camelcase($skill)?>" value="0"<?=($charInfo[camelcase($skill)] == 0?' checked="checked"':'')?>>
									<input type="radio" name="<?=camelcase($skill)?>" value="1"<?=($charInfo[camelcase($skill)] == 1?' checked="checked"':'')?>>
									<input type="radio" name="<?=camelcase($skill)?>" value="2"<?=($charInfo[camelcase($skill)] == 2?' checked="checked"':'')?>>
									<input type="radio" name="<?=camelcase($skill)?>" value="3"<?=($charInfo[camelcase($skill)] == 3?' checked="checked"':'')?>>
									<input type="radio" name="<?=camelcase($skill)?>" value="4"<?=($charInfo[camelcase($skill)] == 4?' checked="checked"':'')?>>
									<input type="radio" name="<?=camelcase($skill)?>" value="5"<?=($charInfo[camelcase($skill)] == 5?' checked="checked"':'')?>>
									<span>5</span>
								</div>
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
								<textarea name="merits"><?=$charInfo['merits']?></textarea>
							</div>
							
							<div id="flaws" class="marginTop">
								<h3>Flaws</h3>
								<textarea name="flaws"><?=$charInfo['flaws']?></textarea>
							</div>
						</div>
						<div class="col floatRight">
							<div class="clearfix">
								<div id="health" class="alignCenter">
									<h3>Health</h3>
									<input type="text" name="health" maxlength="2" value="<?=$charInfo['health']?>">
								</div>
								<div id="willpower" class="alignCenter">
									<h3>Willpower</h3>
									<input type="text" name="willpower" maxlength="2" value="<?=$charInfo['willpower']?>">
								</div>
								<div id="morality" class="alignCenter">
									<h3>Morality</h3>
									<input type="text" name="morality" maxlength="2" value="<?=$charInfo['morality']?>">
								</div>
							</div>
							
							<div class="tr marginTop">
								<label class="textLabel">Size</label>
								<input type="text" name="size" maxlength="2" value="<?=$charInfo['size']?>">
							</div>
							<div class="tr">
								<label class="textLabel">Speed</label>
								<input type="text" name="speed" maxlength="2" value="<?=$charInfo['speed']?>">
							</div>
							<div class="tr">
								<label class="textLabel">Initiative Mod</label>
								<input type="text" name="initiativeMod" maxlength="2" value="<?=$charInfo['initiativeMod']?>">
							</div>
							<div class="tr">
								<label class="textLabel">Defense</label>
								<input type="text" name="defense" maxlength="2" value="<?=$charInfo['defense']?>">
							</div>
							<div class="tr">
								<label class="textLabel">Armor</label>
								<input type="text" name="armor" maxlength="2" value="<?=$charInfo['armor']?>">
							</div>
						</div>
					</div>
				</div>
				
				<div id="itemsDiv">
					<div id="weapons">
						<h2 class="headerbar hbDark">Weapons</h2>
						<textarea name="weapons" class="hbdMargined"><?=$charInfo['weapons']?></textarea>
					</div>
					<div id="equipment">
						<h2 class="headerbar hbDark">Equipment</h2>
						<textarea name="equipment" class="hbdMargined"><?=$charInfo['equipment']?></textarea>
					</div>
				</div>
			</div>
			
			<div id="notes" class="marginTop">
				<h2 id="notesTitle" class="headerbar hbDark">Notes</h3>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>