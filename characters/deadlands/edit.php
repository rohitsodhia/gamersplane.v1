				<div id="nameDiv" class="tr">
					<label class="textLabel leftLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>">
				</div>
				
				<div class="clearfix">
					<div class="triCol">
						<h2 class="headerbar hbDark">Mental</h2>
<?
	$first = TRUE;
	foreach (deadlands_consts::getStats() as $abbrev => $label) {
		if ($abbrev == 'def') {
			$first = TRUE;
?>
					</div>
					<div class="triCol">
						<h2 class="headerbar hbDark">Corporeal</h2>
<?
		}
		$dice = explode('d', $this->getStats($abbrev, 'dice'));
?>
						<div class="hbdMargined statDiv<?=$first?' firstStatDiv':''?>">
							<div class="statDice">
								<input type="text" name="stats[<?=$abbrev?>][numDice]" maxlength="2" class="numDice" value="<?=$dice[0]?>"> d <input type="text" name="stats[<?=$abbrev?>][typeDice]" class="typeDice" value="<?=$dice[1]?>"> <?=$label?>
							</div>
							<div class="skillTitle"><?=$label?> Skills</div>
							<textarea name="stats[<?=$abbrev?>][skills]"><?=$this->getStats($abbrev, 'skills')?></textarea>
						</div>
<?
		if ($first) $first = FALSE;
	}
?>
					</div>
					<div class="triCol lastTriCol">
						<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
						<textarea id="edge_hind" name="edge_hind" class="hbdMargined"><?=$this->getEdgesHindrances?></textarea>
						
						<h2 class="headerbar hbDark">Worst Nightmare</h2>
						<textarea id="nightmare" name="nightmare" class="hbdMargined"><?=$this->getNightmare()?></textarea>
						
						<h2 class="headerbar hbDark">Wounds</h2>
						<div id="woundsDiv" class="clearfix">
							<div class="indivWoundDiv">
								<div>Head</div>
								<input type="text" name="wounds[head]" maxlength="2" value="<?=$this->getWounds('head')?>">
							</div>
							<div class="indivWoundDiv subTwoCol">
								<div>Left Hand</div>
								<input type="text" name="wounds[leftHand]" maxlength="2" value="<?=$this->getWounds('leftHand')?>">
							</div>
							<div class="indivWoundDiv subTwoCol">
								<div>Right Hand</div>
								<input type="text" name="wounds[rightHand]" maxlength="2" value="<?=$this->getWounds('rightHand')?>">
							</div>
							<div class="indivWoundDiv">
								<div>Guts</div>
								<input type="text" name="wounds[guts]" maxlength="2" value="<?=$this->getWounds('guts')?>">
							</div>
							<div class="indivWoundDiv subTwoCol">
								<div>Left Leg</div>
								<input type="text" name="wounds[leftLeg]" maxlength="2" value="<?=$this->getWounds('leftLeg')?>">
							</div>
							<div class="indivWoundDiv subTwoCol">
								<div>Right Leg</div>
								<input type="text" name="wounds[rightLeg]" maxlength="2" value="<?=$this->getWounds('rightLeg')?>">
							</div>
						</div>
						
						<div id="windDiv">
							<div>Wind</div>
							<input type="text" name="wind" maxlength="2" value="<?=$this->getWind()?>">
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol">
						<h2 class="headerbar hbDark">Shootin Irons & Such</h2>
						<textarea id="weapons" name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
						
						<h2 class="headerbar hbDark">Arcane Abilities</h2>
						<textarea id="arcane" name="arcane" class="hbdMargined"><?=$this->getArcane()?></textarea>
					</div>
					<div class="twoCol lastTwoCol">
						<h2 class="headerbar hbDark">Equipment</h2>
						<textarea id="equipment" name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
					</div>
				</div>
				
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
