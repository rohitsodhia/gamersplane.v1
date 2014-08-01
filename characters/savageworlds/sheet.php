			<div id="nameDiv" class="tr clearfix">
				<label>Name:</label>
				<div><?=$this->getName?></div>
			</div>
			
			<div class="clearfix">
				<div class="triCol">
					<h2 class="headerbar hbDark">Mental</h2>
<?
	$first = TRUE;
	foreach (savageworlds_consts::getStats() as $abbrev => $label) {
		if ($abbrev == 'def') {
			$first = TRUE;
?>
				</div>
				<div class="triCol">
					<h2 class="headerbar hbDark">Corporeal</h2>
<?
		}
?>
					<div class="hbdMargined statDiv<?=$first?' firstStatDiv':''?>">
						<div class="statDice">
							<?=$this->getStats($abbrev, 'dice')." $label"?>
						</div>
						<div class="skillTitle"><?=$label?> Skills</div>
						<?=printReady($this->getStats($abbrev, 'skills'))?>
					</div>
<?
		if ($first) $first = FALSE;
	}
?>
				</div>
				<div class="triCol lastTriCol">
					<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
					<div class="hbdMargined"><?=printReady($this->getEdgesHindrances())?></div>
					
					<h2 class="headerbar hbDark">Worst Nightmare</h2>
					<div class="hbdMargined"><?=printReady($this->getNightmare())?></div>
					
					<h2 class="headerbar hbDark">Wounds</h2>
					<div id="woundsDiv" class="clearfix">
						<div class="indivWoundDiv">
							<div>Head</div>
							<?=$this->getWounds('head')?>
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Left Hand</div>
							<?=$this->getWounds('leftHand')?>
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Right Hand</div>
							<?=$this->getWounds('rightHand')?>
						</div>
						<div class="indivWoundDiv">
							<div>Guts</div>
							<?=$this->getWounds('guts')?>
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Left Leg</div>
							<?=$this->getWounds('leftLeg')?>
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Right Leg</div>
							<?=$this->getWounds('rightLeg')?>
						</div>
					</div>
					
					<div id="windDiv">
						<div>Wind</div><?=printReady($this->getWind())?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol">
					<h2 class="headerbar hbDark">Shootin Irons & Such</h2>
					<div class="hbdMargined"><?=printReady($this->getWeapons())?></div>
					
					<h2 class="headerbar hbDark">Arcane Abilities</h2>
					<div class="hbdMargined"><?=printReady($this->getArcane())?></div>
				</div>
				<div class="twoCol lastTwoCol">
					<h2 class="headerbar hbDark">Equipment</h2>
					<div class="hbdMargined"><?=printReady($this->getEquipment())?></div>
				</div>
			</div>
			
			<h2 class="headerbar hbDark">Background/Notes</h2>
			<div class="hbdMargined"><?=printReady($this->getNotes)?></div>
