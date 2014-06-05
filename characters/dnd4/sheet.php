			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_race" class="medText">Race</label>
				<label id="label_alignment" class="medText">Alignment</label>
			</div>
			<div class="tr dataTR">
				<div class="medText"><?=$this->getName();?></div>
				<div class="medText"><?=$this->getRace()?></div>
				<div class="medText"><?=$this->getAlignment()?></div>
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText">Class(es)</label>
				<label id="label_paragon" class="medText">Paragon Path</label>
				<label id="label_epic" class="medText">Epic Destiny</label>
			</div>
			<div class="tr dataTR">
				<div class="longText"><? $this->displayClasses(); ?></div>
				<div class="medText"><?=$this->getParagon()?></div>
				<div class="medText"><?=$this->getEpic()?></div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
					<div class="tr labelTR">
						<label class="shortText lrBuffer">Stat</label>
						<label class="shortNum alignCenter stat">Score</label>
						<label class="shortNum alignCenter">Mod</label>
						<label class="shortNum alignCenter">Mod + 1/2 Lvl</label>
					</div>
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
					<div class="tr dataTR">
						<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
						<div class="stat shortNum alignCenter"><?=$this->getStat($short)?></div>
						<div id="<?=$short?>Modifier" class="statMod shortNum alignCenter"><?=$this->getStatMod($short)?></div>
						<div id="<?=$short?>ModifierPL" class="shortNum alignCenter"><?=$this->getStatModPHL($short)?></div>
					</div>
<?	} ?>
				</div>
				
				<div id="saves">
					<div class="tr labelTR">
						<div class="fillerBlock cell">&nbsp;</div>
						<label class="statCol shortNum lrBuffer">Total</label>
						<label class="statCol shortNum lrBuffer">10 + 1/2 Lvl</label>
						<label class="statCol shortNum lrBuffer">Armor/ Ability</label>
						<label class="statCol shortNum lrBuffer">Class</label>
						<label class="statCol shortNum lrBuffer">Feat</label>
						<label class="statCol shortNum lrBuffer">Enh</label>
						<label class="statCol shortNum lrBuffer">Misc</label>
					</div>
					<div id="fortRow" class="tr dataTR">
						<label class="leftLabel">AC</label>
						<div id="acTotal" class="shortNum lrBuffer total"><?=showSign($this->getSave('ac', 'total'))?></div>
						<div class="shortNum lrBuffer"><?=showSign(10 + $this->getHL())?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave('ac', 'armor'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave('ac', 'class'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave('ac', 'feats'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave('ac', 'enh'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave('ac', 'misc'))?></div>
					</div>
<?
	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) {
		$abilities = $this->getSave($save, 'ability');
		$abilityMods = array($this->getStatMod($abilities[0], FALSE), $this->getStatMod($abilities[1], FALSE));
		$useAbility = 0;
		if ($abilityMods[1] > $abilityMods[0]) $useAbility = 1;
?>
					<div id="<?=$save?>Row" class="tr dataTR">
						<label class="leftLabel"><?=$saveFull?></label>
						<div id="fortTotal" class="shortNum lrBuffer total"><?=showSign($this->getSave($save, 'total'))?></div>
						<div class="shortNum lrBuffer"><?=showSign(10 + $this->getHL())?></div>
						<div class="shortNum lrBuffer"><?=showSign($abilityMods[$useAbility])?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'class'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'feats'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'enh'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'misc'))?></div>
					</div>
<?	} ?>
				</div>
				
				<div id="init">
					<div class="tr labelTR">
						<div class="fillerBlock cell shortText">&nbsp;</div>
						<label class="shortNum alignCenter lrBuffer first">Total</label>
						<label class="shortNum alignCenter lrBuffer">Dex</label>
						<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<label class="shortText alighRight leftLabel">Initiative</label>
						<div class="shortNum alignCenter lrBuffer total"><?=showSign($this->getInitiative('total'))?></div>
						<div class="shortNum alignCenter lrBuffer"><?=$this->getStatMod('dex')?></div>
						<div class="shortNum alignCenter lrBuffer">+<?=$this->getHL()?></div>
						<div class="shortNum alignCenter lrBuffer"><?=showSign($this->getInitiative('misc'))?></div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="hpCol">
					<div id="hp">
						<div class="tr labelTR">
							<label class="medNum alignCenter lrBuffer">Total HP</label>
							<label class="medNum alignCenter lrBuffer">Bloodied</label>
							<label class="medNum alignCenter lrBuffer">Surge Value</label>
							<label class="medNum alignCenter lrBuffer">Surges/ Day</label>
						</div>
						<div class="tr">
							<div class="medNum alignCenter lrBuffer cell"><?=$this->getHP('total')?></div>
							<div class="medNum alignCenter lrBuffer cell"><?=floor($this->getHP('total') / 2)?></div>
							<div class="medNum alignCenter lrBuffer cell"><?=floor($this->getHP('total') / 4)?></div>
							<div class="medNum alignCenter lrBuffer cell"><?=$this->getHP('surges')?></div>
						</div>
					</div>
					
					<div id="movement">
						<div class="tr labelTR">
							<div class="fillerBlock cell medNum">&nbsp;</div>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Base</label>
							<label class="shortNum alignCenter lrBuffer">Armor</label>
							<label class="shortNum alignCenter lrBuffer">Item</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
						<div class="tr">
							<label class="medNum leftLabel">Speed</label>
							<div class="shortNum alignCenter lrBuffer cell total"><?=$this->getSpeed('total')?></div>
							<div class="shortNum alignCenter lrBuffer cell"><?=$this->getSpeed('base')?></div>
							<div class="shortNum alignCenter lrBuffer cell"><?=$this->getSpeed('armor')?></div>
							<div class="shortNum alignCenter lrBuffer cell"><?=$this->getSpeed('item')?></div>
							<div class="shortNum alignCenter lrBuffer cell"><?=$this->getSpeed('misc')?></div>
						</div>
					</div>
					
					<div id="actionPoints">
						<label class="shortText leftLabel">Action Points</label>
						<div class="shortNum alignCenter lrBuffer cell"><?=$this->getActionPoints()?></div>
					</div>
					
					<div id="passiveSenses">
						<div class="tr labelTR">
							<div class="fillerBlock cell labelFiller">&nbsp;</div>
							<label class="shortNum alignCenter">Total</label>
							<div class="fillerBlock cell shortNum">&nbsp;</div>
							<label class="shortNum alignCenter">Skill</label>
						</div>
						<div class="tr">
							<label class="leftLabel">Passive Insight</label>
							<div class="shortNum alignCenter cell total"><?=$this->getPassiveSenses('insight') + 10?></div>
							<div class="shortNum alignCenter cell">10 + </div>
							<div class="shortNum alignCenter cell"><?=$this->getPassiveSenses('insight')?></div>
						</div>
						<div class="tr">
							<label class="leftLabel">Passive Perception</label>
							<div class="shortNum alignCenter cell total"><?=$this->getPassiveSenses('perception') + 10?></div>
							<div class="shortNum alignCenter cell">10 + </div>
							<div class="shortNum alignCenter cell"><?=$this->getPassiveSenses('perception')?></div>
						</div>
					</div>
				</div>
			
				<div id="attacks">
					<h2 class="headerbar hbDark">Attack Bonuses</h2>
<?	$this->displayAttacks(); ?>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Ranks</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
<?	$this->displaySkills(); ?>
					</div>
				</div>
				<div id="feats" class="floatRight">
					<h2 class="headerbar hbDark">Feats/Features</h2>
					<div class="hbdMargined">
<?	$this->displayFeats(); ?>
					</div>
				</div>
			</div>
			
			<div id="powers" class="clearfix">
				<h2 class="headerbar hbDark">Powers</h2>
				<div class="hbdMargined">
<?	$powers = $this->getPowers(); ?>
					<div id="powers_atwill" class="powerCol first">
						<h3>At-Will</h3>
<?	foreach ($powers['a'] as $power) $this->powerSheetFormat($power); ?>
				</div>
				<div id="powers_encounter" class="powerCol">
					<h3>Encounter</h3>
<?	foreach ($powers['e'] as $power) $this->powerSheetFormat($power); ?>
				</div>
				<div id="powers_daily" class="powerCol">
					<h3>Daily</h3>
<?	foreach ($powers['d'] as $power) $this->powerSheetFormat($power); ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="textDiv floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div class="hbdMargined"><?=print_ready($this->getWeapons())?></div>
				</div>
				<div id="armor" class="textDiv floatRight">
					<h2 class="headerbar hbDark">Armor</h2>
					<div class="hbdMargined"><?=print_ready($this->getArmor())?></div>
				</div>
			</div>
			
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=print_ready($this->getItems())?></div>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=print_ready($this->getNotes())?></div>
			</div>
