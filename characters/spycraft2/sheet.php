			<div class="tr labelTR tr-noPadding">
				<label id="label_name" class="medText">Name</label>
				<label id="label_codename" class="medText">Codename</label>
			</div>
			<div class="tr dataTR gapBelow">
				<div class="medText"><?=$this->getName()?></div>
				<div class="medText"><?=$this->getCodename()?></div>
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText">Class(es)</label>
				<label id="label_talent" class="medText">Talent</label>
				<label id="label_specialty" class="medText">Specialty</label>
			</div>
			<div class="tr dataTR gapBelow">
				<div class="longText"><? $this->displayClasses(); ?></div>
				<div class="medText"><?=$this->getTalent()?></div>
				<div class="medText"><?=$this->getSpecialty()?></div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
					<div class="tr">
						<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
						<div class="stat"><?=$this->getStat($short)?></div>
						<span id="<?=$short?>Modifier"><?=$this->getStatMod($short)?></span>
					</div>
<?	} ?>
				</div>
				
				<div id="savingThrows">
					<div class="tr labelTR">
						<div class="">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="statSelect lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
<?	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) { ?>
					<div id="<?=$save?>Row" class="tr dataTR">
						<label class="leftLabel"><?=$saveFull?></label>
						<div id="fortTotal" class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'total'))?></div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'base'))?></div>
						<div class="statSelect lrBuffer">
							<div class="statShort"><?=ucwords($this->getSave($save, 'stat'))?></div>
							<div class="shortNum"><?=$this->getStatMod($this->getSave($save, 'stat'))?></div>
						</div>
						<div class="shortNum lrBuffer"><?=showSign($this->getSave($save, 'misc'))?></div>
					</div>
<?	} ?>
				</div>
				
				<div id="hp">
					<div class="tr">
						<label class="leftLabel">Vitality</label>
						<div><?=$this->getHP('vitality')?></div>
					</div>
					<div class="tr">
						<label class="leftLabel">Wounds</label>
						<div><?=$this->getHP('wounds')?></div>
					</div>
					<div class="tr">
						<label class="leftLabel">Subdual</label>
						<div><?=$this->getHP('subdual')?></div>
					</div>
					<div class="tr">
						<label class="leftLabel">Stress</label>
						<div><?=$this->getHP('stress')?></div>
					</div>
				</div>
				
				<div id="ac">
					<div class="tr labelTR">
						<label class="first">Total AC</label>
						<label class="shortNum">Class</label>
						<label class="shortNum">Armor</label>
						<label class="shortNum">Dex</label>
						<label class="shortNum">Misc</label>
					</div>
					<div class="tr dataTR">
						<div class="first"><?=$this->getAC('total')?></div>
						<div> = 10 + </div>
						<div class="shortNum"><?=showSign($this->getAC('class'))?></div>
						<div class="shortNum"><?=showSign($this->getAC('armor'))?></div>
						<div class="shortNum"><?=showSign($this->getAC('dex'))?></div>
						<div class="shortNum"><?=showSign($this->getAC('misc'))?></div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="combatBonuses">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="statSelect lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
					<div id="init" class="tr dataTR">
						<label class="leftLabel shortText">Initiative</label>
						<span id="initTotal" class="shortNum lrBuffer"><?=showSign($this->getInitiative('total'))?></span>
						<span class="shortNum lrBuffer">&nbsp;</span>
						<span class="statSelect lrBuffer">
							<span class="statShort"><?=ucwords($this->getInitiative('stat'))?></span>
							<span class="shortNum"><?=$this->getStatMod($this->getInitiative('stat'))?></span>
						</span>
						<div class="shortNum lrBuffer"><?=showSign($this->getInitiative('misc'))?></div>
					</div>
					<div id="melee" class="tr dataTR">
						<label class="leftLabel shortText">Melee</label>
						<span id="meleeTotal" class="shortNum lrBuffer"><?=showSign($this->getAttackBonus('total', 'melee'))?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getAttackBonus('base'))?></span>
						<span class="statSelect lrBuffer">
							<span class="statShort"><?=ucwords($this->getAttackBonus('stat', 'melee'))?></span>
							<span class="shortNum"><?=$this->getStatMod($this->getAttackBonus('stat', 'melee'))?></span>
						</span>
						<div class="shortNum lrBuffer"><?=showSign($this->getAttackBonus('misc', 'melee'))?></div>
					</div>
					<div id="ranged" class="tr dataTR">
						<label class="leftLabel shortText">Ranged</label>
						<span id="rangedTotal" class="shortNum lrBuffer"><?=showSign($this->getAttackBonus('total', 'ranged'))?></span>
						<span class="shortNum bab lrBuffer"><?=showSign($this->getAttackBonus('base'))?></span>
						<span class="statSelect lrBuffer">
							<span class="statShort"><?=ucwords($this->getAttackBonus('stat', 'ranged'))?></span>
							<span class="shortNum"><?=$this->getStatMod($this->getAttackBonus('stat', 'ranged'))?></span>
						</span>
						<div class="shortNum lrBuffer"><?=showSign($this->getAttackBonus('misc', 'ranged'))?></div>
					</div>
				</div>
				
				<div id="actionDie">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="medNum lrBuffer">Dice Type</label>
					</div>
					<div class="tr">
						<label class="leftLabel shortText">Action Die</label>
						<span class="shortNum lrBuffer"><?=$this->getActionDie('number')?></span>
						<span class="medNum lrBuffer"><?=$this->getActionDie('type')?></span>
					</div>
				</div>
				
				<div id="extraStats">
					<div class="tr labelTR">
						<label class="shortText">Check Bonus</label>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Level</label>
						<label class="shortNum lrBuffer">Stat</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<label class="leftLabel shortText">Knowledge</label>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('knowledge') + $this->getLevel() + $this->getStatMod('int', falseh))?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getLevel())?></span>
						<span class="shortNum lrBuffer"><?=$this->getStatMod('int')?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('knowledge'))?></span>
					</div>
					<div class="tr">
						<label class="leftLabel shortText">Request</label>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('request') + $this->getLevel() + $this->getStatMod('cha', falseh))?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getLevel())?></span>
						<span class="shortNum lrBuffer"><?=$this->getStatMod('cha')?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('request'))?></span>
					</div>
					<div class="tr">
						<label class="leftLabel shortText">Gear</label>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('gear') + $this->getLevel() + $this->getStatMod('wis', falseh))?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getLevel())?></span>
						<span class="shortNum lrBuffer"><?=$this->getStatMod('wis')?></span>
						<span class="shortNum lrBuffer"><?=showSign($this->getExtraStats('gear'))?></span>
					</div>
				</div>
			</div>
			
			<div id="skills">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined">
					<div class="tr labelTR">
						<label class="medText skill_name">Skill</label>
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortText alignCenter lrBuffer">Stat(s)</label>
						<label class="shortNum alignCenter lrBuffer">Ranks</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
						<label class="medNum alignCenter lrBuffer">Error</label>
						<label class="medNum alignCenter lrBuffer">Threat</label>
					</div>
<?	$this->displaySkills(); ?>
				</div>
			</div>

			<div class="clearfix">
				<div id="focuses">
					<h2 class="headerbar hbDark">Focuses/Fortes</h2>
					<div class="hbdMargined">
<?	$this->displayFocuses(); ?>
					</div>
				</div>
				<div id="feats">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
<?	$this->displayFeats(); ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div>
<?	$this->displayWeapons(); ?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2 class="headerbar hbDark">Armor</h2>
					<div>
<?	$this->displayArmor(); ?>
					</div>
				</div>
			</div>
			
			<br class="clear">
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=$this->getItems()?></div>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=$this->getNotes()?></div>
			</div>
