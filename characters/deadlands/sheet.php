			<div id="nameDiv" class="tr clearfix">
				<label>Name:</label>
				<div><?=$this->getName?></div>
			</div>

			<div class="clearfix">
				<div class="triCol">
					<h2 class="headerbar hbDark">Mental</h2>
<?
	$first = true;
	foreach (deadlands_consts::getStats() as $abbrev => $label) {
		if ($abbrev == 'def') {
			$first = true;
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
						<?=printReady(BBCode2Html($this->getStats($abbrev, 'skills')))?>
					</div>
<?
		if ($first) $first = false;
	}
?>
				</div>
				<div class="triCol lastTriCol">
					<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getEdgesHindrances()))?></div>

					<h2 class="headerbar hbDark">Worst Nightmare</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNightmare()))?></div>

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
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getWeapons()))?></div>

					<h2 class="headerbar hbDark">Arcane Abilities</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getArcane()))?></div>
				</div>
				<div class="twoCol lastTwoCol">
					<h2 class="headerbar hbDark">Equipment</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getEquipment()))?></div>
				</div>
			</div>

			<h2 class="headerbar hbDark">Background/Notes</h2>
			<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
