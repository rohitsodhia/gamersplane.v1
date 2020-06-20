			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_race" class="medText">Race</label>
				<label id="label_classes" class="longText">Class(es)</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><?=$this->getRace()?></div>
				<div class="longText"><? $this->displayClasses(); ?></div>
			</div>

			<div class="clearfix">
				<div id="stats">
<?
	$statLabels = '';
	$statRow = '';
	$modRow = '';
	$modPLRow = '';
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
		$statLabels .= "<label>".ucwords($short)."</label>\n";
		$statRow .= "<div>{$this->getStat($short)}</div>\n";
		$modRow .= "<div class=\"statBonus_{$short}\">".$this->getStatMod($short)."</div>\n";
		$modPLRow .= "<div class=\"statBonus_{$short} addHL\">".showSign($this->getStatMod($short) + $this->getLevel())."</div>\n";
	}
	echo "<div class=\"tr\">$statLabels</div>";
	echo "<div class=\"tr\">$statRow</div>";
	echo "<div class=\"tr\">$modRow</div>";
	echo "<div class=\"tr\">$modPLRow</div>";
?>
				</div>

				<div id="saves">
					<div class="labelTR tr">
						<div class="cell">&nbsp;</div>
						<label>Base</label>
						<span>&nbsp;</span>
						<label>Stat</label>
						<span>&nbsp;</span>
						<label>Misc</label>
					</div>
<?	foreach (array('ac', 'pd', 'md') as $save) { ?>
					<div id="<?=$save?>Row" class="saveSet tr">
						<label><?=strtoupper($save)?></label><div class="total"><?=$this->getSave($save, 'total')?></div>
						<span>=</span>
						<div class="shortNum"><?=$this->getSave($save, 'base')?></div>
						<span>+</span>
						<div id="<?=$save?>Stat" class="saveStat"><?=$this->getStatMod($this->getSaveStat($save), true)?></div>
						<span>+</span>
						<div class="shortNum"><?=$this->getSave($save, 'misc')?></div>
					</div>
<?	} ?>
				</div>

				<div id="hp">
					<div class="title" class="tr labelTR">HP</div>
					<div>
						<label for="hp_current">Current</label>
						<label for="hp_maximum">Max</label>
					</div>
					<div class="tr">
						<div class="shortNum"><?=$this->getHP('current')?></div>
						<div class="shortNum"><?=$this->getHP('maximum')?></div>
					</div>
				</div>
				<div id="recoveries">
					<div class="title" class="tr labelTR">Recoveries</div>
					<div>
						<label for="recoveries_current">Current</label>
						<label for="recoveries_maximum">Max</label>
						<label for="recoveries_roll">Roll</label>
					</div>
					<div class="tr">
						<div class="shortNum"><?=$this->getRecoveries('current')?></div>
						<div class="shortNum"><?=$this->getRecoveries('maximum')?></div>
						<div class="shortNum"><?=$this->getRecoveryRoll()?></div>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="uniqueThing" class="floatLeft">
					<h2 class="headerbar hbDark">One Unique Thing</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getUniqueThing()))?></div>
				</div>
				<div id="iconRelationships" class="floatRight">
					<h2 class="headerbar hbDark">Icon Relationships</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getIconRelationships()))?></div>
				</div>
			</div>
			<div class="clearfix">
				<div class="column first">
					<div id="backgrounds">
						<h2 class="headerbar hbDark">Backgrounds</h2>
						<div class="hbdMargined">
<?	$this->displayBackgrounds(); ?>
						</div>
					</div>
					<div id="feats">
						<h2 class="headerbar hbDark">Feats</h2>
						<div class="hbdMargined">
<?	$this->displayFeats(); ?>
						</div>
					</div>
				</div>
				<div class="column">
					<div id="abilitiesTalents">
						<h2 class="headerbar hbDark">Abilities/Talents</h2>
						<div class="hbdMargined">
<?	$this->displayAbilitiesTalents(); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<div class="column first">
					<div id="powers">
						<h2 class="headerbar hbDark">Powers</h2>
						<div class="hbdMargined">
<?	$this->displayPowers(); ?>
						</div>
					</div>
				</div>
				<div class="column">
					<div id="attacks">
						<h2 class="headerbar hbDark">Attacks</h2>
						<div id="basicAttacks" class="hbdMargined">
<?	foreach (array('melee', 'ranged') as $attack) { ?>
							<div id="ba_<?=$attack?>" class="tr" data-type="<?=$attack?>">
								<span class="label"><?=ucwords($attack)?></span>
								<span class="total"><?=showSign($this->getStatMod($this->getBasicAttacks($attack, 'stat'), false) + $this->getLevel() + $this->getBasicAttacks($attack, 'misc'))?></span>
								<span> = </span>
								<span class="stat"><?=ucwords($this->getBasicAttacks($attack, 'stat'))?></span>
								<span> + Lvl + </span>
								<span><?=$this->getBasicAttacks($attack, 'misc')?></span>
							</div>
							<div id="baDmg_<?=$attack?>" class="tr baDmg">
								<span class="hit">Hit: <?=$this->getBasicAttacks($attack, 'hit')?></span>
								<span class="miss">Miss: <?=$this->getBasicAttacks($attack, 'miss')?></span>
							</div>
<?	} ?>
						</div>
						<div class="hbdMargined">
<?	$this->displayAttacks(); ?>
						</div>
					</div>
				</div>
			</div>

			<div id="items" class="clearfix">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getItems()))?></div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
			</div>
