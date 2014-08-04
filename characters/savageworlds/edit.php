				<div id="nameDiv" class="tr">
					<label class="textLabel">Name:</label>
					<div><input type="text" name="name" maxlength="50" value="<?=$this->getName()?>"></div>
				</div>
				
				<div class="clearfix">
					<div class="sidebar left">
						<h2 class="headerbar hbDark">Traits &amp; Skills</h2>
<?
	foreach (savageworlds_consts::getStats() as $abbrev => $label) {
		$dice = $this->getStats($abbrev);
?>
						<div class="hbdMargined statDiv">
							<div class="trait clearfix">
								<div class="traitName"><?=$label?></div>
								<div class="diceSelect"><span>d</span> <select name="stats[<?=$abbrev?>]" class="typeDice">
<?		foreach (array(4, 6, 8, 10, 12) as $dCount) { ?>
									<option><?=$dCount?></option>
<?		} ?>
								</select></div>
							</div>
							<div class="skillHeader"><?=$label?> Skills</div>
							<div class="skills">
							</div>
						</div>
<?	} ?>
					</div>
					<div class="mainColumn right">
						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
								<textarea id="edge_hind" name="edge_hind" class="hbdMargined"><?=$this->getEdgesHindrances?></textarea>
							</div>
							<div class="twoCol lastTwoCol">
								<h2 class="headerbar hbDark">Wounds</h2>
								<div id="fatigueDiv">
									<div>Fatigue</div>
									<input type="text" name="wind" maxlength="2" value="<?=$this->getFatigue()?>">
								</div>
							</div>
						</div>
							
						<div class="clearfix">
							<div class="twoCol">
								<h2 class="headerbar hbDark">Shootin Irons & Such</h2>
								<textarea id="weapons" name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
							</div>
							<div class="twoCol lastTwoCol">
								<h2 class="headerbar hbDark">Equipment</h2>
								<textarea id="equipment" name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
							</div>
						</div>
						
						<h2 class="headerbar hbDark">Background/Notes</h2>
						<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
					</div>
				</div>
