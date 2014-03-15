<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'gurps';
	$charInfo = getCharInfo($characterID, $system);
	if ($charInfo) {
		if ($viewerStatus = allowCharView($characterID, $userID)) {
			$noChar = FALSE;

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

		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=printReady($charInfo['name'])?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats">
				<div class="statCol">
					<label class="statRow" for="st">ST</label>
					<label class="statRow" for="dx">DX</label>
					<label class="statRow" for="iq">IQ</label>
					<label class="statRow" for="ht">HT</label>
				</div>
				<div class="statCol">
					<div class="statRow"><?=$charInfo['st']?></div>
					<div class="statRow"><?=$charInfo['dx']?></div>
					<div class="statRow"><?=$charInfo['iq']?></div>
					<div class="statRow"><?=$charInfo['ht']?></div>
				</div>
				<div class="statCol">
					<label class="statRow" for="hp">HP</label>
					<label class="statRow" for="will">Will</label>
					<label class="statRow" for="per">Per</label>
					<label class="statRow" for="fp">FP</label>
				</div>
				<div class="statCol">
					<div class="statRow"><?=$charInfo['hp']?></div>
					<div class="statRow"><?=$charInfo['will']?></div>
					<div class="statRow"><?=$charInfo['per']?></div>
					<div class="statRow"><?=$charInfo['fp']?></div>
				</div>
				<div class="statCol">
					<div class="statRow"><?=$charInfo['hp_current']?></div>
					<div class="statRow blank">&nbsp;</div>
					<div class="statRow blank">&nbsp;</div>
					<div class="statRow"><?=$charInfo['fp_current']?></div>
				</div>
				<div class="statCol largeCol">
					<label class="statRow" for="dmg_thr">Damage (Thrown)</label>
					<label class="statRow" for="dmg_sw">Damage (Swing)</label>
					<label class="statRow" for="speed">Speed</label>
					<label class="statRow" for="move">Move</label>
				</div>
				<div class="statCol">
					<div class="statRow"><?=$charInfo['dmg_thr']?></div>
					<div class="statRow"><?=$charInfo['dmg_sw']?></div>
					<div class="statRow"><?=$charInfo['speed']?></div>
					<div class="statRow"><?=$charInfo['move']?></div>
				</div>
			</div>
			
			<div id="langDiv" class="floatRight">
				<h2 class="headerbar hbDark">Languages</h2>
				<div class="hbdMargined"><?=printReady($charInfo['languages'])?></div>
			</div>
		</div>

		<div class="clearfix">
			<div class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Advantages</h2>
				<div class="hbdMargined"><?=printReady($charInfo['advantages'])?></div>
			</div>
			
			<div class="twoCol floatRight">
				<h2 class="headerbar hbDark">Disadvantages</h2>
				<div class="hbdMargined"><?=printReady($charInfo['disadvantages'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=printReady($charInfo['skills'])?></div>
			</div>
			
			<div class="twoCol floatRight">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=printReady($charInfo['items'])?></div>
			</div>
		</div>
		
		<div id="notesDiv">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=printReady($charInfo['notes'])?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>