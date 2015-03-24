				<div class="tr labelTR">
					<label id="label_name" class="medText lrBuffer shiftRight borderBox">Name</label>
					<label id="label_race" class="medText lrBuffer shiftRight borderBox">Race</label>
					<label id="label_alignment" class="shortText lrBuffer shiftRight borderBox">Alignment</label>
				</div>
				<div class="tr">
					<input type="text" name="name" value="<?=$this->getName()?>" class="medText lrBuffer">
					<input type="text" name="race" value="<?=$this->getRace()?>" class="medText lrBuffer">
					<select name="alignment" class="lrBuffer">
<?	foreach (dnd4_consts::getAlignments() as $alignShort => $alignment) { ?>
						<option value="<?=$alignShort?>"<?=$this->getAlignment() == $alignShort?' selected="selected"':''?>><?=$alignment?></option>
<?	} ?>
					</select>
				</div>
				
				<div class="tr labelTR">
					<label id="label_classes" class="medText lrBuffer shiftRight borderBox">Class(es)</label>
					<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
					<label id="label_paragon" class="medText lrBuffer shiftRight borderBox">Paragon Path</label>
					<label id="label_epic" class="medText lrBuffer shiftRight borderBox">Epic Destinies</label>
				</div>
				<div class="tr">
					<div id="classWrapper">
						<a href="">[ Add Class ]</a>
<?
	$hasClasses = false;
	foreach ($this->getClasses() as $class => $level) {
		$hasClasses = true;
?>
						<div class="classSet">
							<input type="text" name="class[]" value="<?=$class?>" class="medText lrBuffer classInput">
							<input type="text" name="level[]" value="<?=$level?>" class="shortNum lrBuffer levelInput">
						</div>
<?
	}
	if (!$hasClasses) {
?>
						<div class="classSet">
							<input type="text" name="class[]" class="medText lrBuffer classInput">
							<input type="text" name="level[]" class="shortNum lrBuffer levelInput">
						</div>
<?	} ?>
					</div>
					<input id="paragon" type="text" name="paragon" value="<?=$this->getParagon()?>" class="medText lrBuffer">
					<input id="epic" type="text" name="epic" value="<?=$this->getEpic()?>" class="medText lrBuffer">
				</div>
				
				<div class="clearfix">
					<div id="stats">
						<div class="tr labelTR">
							<label class="shortText lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Score</label>
							<label class="shortNum alignCenter">Mod</label>
							<label class="shortNum alignCenter">Mod + 1/2 Lvl</label>
						</div>
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
?>
						<div class="tr">
							<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
							<input type="text" id="<?=$short?>" name="stats[<?=$short?>]" value="<?=$this->getStat($short)?>" maxlength="2" class="stat lrBuffer">
							<div id="<?=$short?>Modifier" class="shortNum cell alignCenter statBonus_<?=$short?>"><?=$this->getStatMod($short)?></div>
							<div id="<?=$short?>ModifierPL" class="shortNum cell alignCenter addStat_<?=$short?> addHL"><?=$this->getStatModPHL($short)?></div>
						</div>
<?	} ?>
					</div>
					
					<div id="saves">
						<div class="tr labelTR">
							<div class="fillerBlock cell">&nbsp;</div>
							<label class="shortNum lrBuffer">Total</label>
							<label class="shortNum lrBuffer">10 + 1/2 Lvl</label>
							<label class="shortNum lrBuffer">Armor/ Ability</label>
							<label class="shortNum lrBuffer">Class</label>
							<label class="shortNum lrBuffer">Feats</label>
							<label class="shortNum lrBuffer">Enh</label>
							<label class="shortNum lrBuffer">Misc</label>
						</div>
						<div id="acRow" class="tr sumRow">
							<label class="leftLabel">AC</label>
							<span id="acTotal" class="shortNum lrBuffer total addInt_10 addHL"><?=showSign($this->getSave('ac', 'total'))?></span>
							<span class="shortNum lrBuffer addHL"><?=showSign(10 + $this->getHL())?></span>
							<input type="text" name="saves[ac][armor]"  value="<?=$this->getSave('ac', 'armor')?>" class="lrBuffer">
							<input type="text" name="saves[ac][class]"  value="<?=$this->getSave('ac', 'class')?>" class="lrBuffer">
							<input type="text" name="saves[ac][feats]"  value="<?=$this->getSave('ac', 'feats')?>" class="lrBuffer">
							<input type="text" name="saves[ac][enh]"  value="<?=$this->getSave('ac', 'enh')?>" class="lrBuffer">
							<input type="text" name="saves[ac][misc]"  value="<?=$this->getSave('ac', 'misc')?>" class="lrBuffer">
						</div>
<?
	foreach (d20Character_consts::getSaveNames() as $save => $saveFull) {
		$abilities = $this->saves[$save]['ability'];
		$abilityMods = array($this->getStatMod($abilities[0], false), $this->getStatMod($abilities[1], false));
		$useAbility = 0;
		if ($abilityMods[1] > $abilityMods[0]) $useAbility = 1;
?>
						<div id="<?=$save?>Row" class="tr sumRow">
							<label class="leftLabel"><?=$saveFull?></label>
							<span id="<?=$save?>Total" class="shortNum lrBuffer total addInt_10 addStat_<?=$abilities[$useAbility]?> addHL"><?=showSign($this->getSave($save, 'total'))?></span>
							<span class="shortNum lrBuffer addHL"><?=showSign(10 + $this->getHL())?></span>
							<span id="<?=$save?>StatBonus" class="shortNum lrBuffer"><?=showSign($abilityMods[$useAbility])?></span>
							<input type="text" name="saves[<?=$save?>][class]"  value="<?=$this->getSave($save, 'class')?>" class="lrBuffer">
							<input type="text" name="saves[<?=$save?>][feats]"  value="<?=$this->getSave($save, 'feats')?>" class="lrBuffer">
							<input type="text" name="saves[<?=$save?>][enh]"  value="<?=$this->getSave($save, 'enh')?>" class="lrBuffer">
							<input type="text" name="saves[<?=$save?>][misc]"  value="<?=$this->getSave($save, 'misc')?>" class="lrBuffer">
						</div>
<?	} ?>
					</div>
					
					<div id="init">
						<div class="tr labelTR">
							<div class="fillerBlock cell shortText">&nbsp;</div>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Dex</label>
							<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
						<div class="tr sumRow">
							<label class="shortText alighRight leftLabel">Initiative</label>
							<div id="init_total" class="shortNum alignCenter lrBuffer total addStat_dex addHL"><?=showSign($this->getInitiative('total'))?></div>
							<div class="shortNum alignCenter statBonus_dex lrBuffer"><?=$this->getStatMod('dex')?></div>
							<div class="shortNum alignCenter addHL lrBuffer">+<?=$this->getHL()?></div>
							<input id="init_misc" type="text" name="initiative[misc]"  value="<?=$this->getInitiative('misc')?>" class="lrBuffer">
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
								<input id="hpInput" type="text" name="hp" value="<?=$this->getHP('total')?>" class="medNum lrBuffer">
								<div id="bloodiedVal" class="medNum alignCenter lrBuffer cell"><?=floor($this->getHP('total') / 2)?></div>
								<div id="surgeVal" class="medNum alignCenter lrBuffer cell"><?=floor($this->getHP('total') / 4)?></div>
								<input type="text" name="surges" value="<?=$this->getHP('surges')?>" class="medNum lrBuffer">
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
							<div class="tr sumRow">
								<label class="medNum leftLabel">Speed</label>
								<div class="shortNum alignCenter lrBuffer cell total noSign"><?=$this->getSpeed('total')?></div>
								<input type="text" name="speed[base]"  value="<?=$this->getSpeed('base')?>" class="lrBuffer">
								<input type="text" name="speed[armor]"  value="<?=$this->getSpeed('armor')?>" class="lrBuffer">
								<input type="text" name="speed[item]"  value="<?=$this->getSpeed('item')?>" class="lrBuffer">
								<input type="text" name="speed[misc]"  value="<?=$this->getSpeed('misc')?>" class="lrBuffer">
							</div>
						</div>
						
						<div id="actionPoints">
							<label class="shortText leftLabel">Action Points</label>
							<input type="text" name="ap" value="<?=$this->getActionPoints()?>">
						</div>
						
						<div id="passiveSenses">
							<div class="tr labelTR">
								<div class="fillerBlock cell labelFiller">&nbsp;</div>
								<label class="medNum alignCenter">Total</label>
								<div class="fillerBlock cell shortNum">&nbsp;</div>
								<label class="shortNum alignCenter">Skill</label>
							</div>
							<div class="tr sumRow">
								<label class="leftLabel">Passive Insight</label>
								<div class="medNum alignCenter cell total noSign addInt_10"><?=$this->getPassiveSenses('insight') + 10?></div>
								<div class="shortNum alignCenter cell">10 + </div>
								<input type="text" name="passiveSenses[insight]" value="<?=$this->getPassiveSenses('insight')?>">
							</div>
							<div class="tr sumRow">
								<label class="leftLabel">Passive Perception</label>
								<div class="medNum alignCenter cell total noSign addInt_10"><?=$this->getPassiveSenses('perception') + 10?></div>
								<div class="shortNum alignCenter cell">10 + </div>
								<input type="text" name="passiveSenses[perception]" value="<?=$this->getPassiveSenses('perception')?>">
							</div>
						</div>
					</div>
					
					<div id="attacks">
						<h2 class="headerbar hbDark">Attack Bonuses <a id="addAttack" href="">[ Add Attack ]</a></h2>
						<div class="hbdMargined">
<?	$this->showAttacksEdit(3); ?>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="skills" class="floatLeft">
						<h2 class="headerbar hbDark">Skills <a id="addSkill" href="">[ Add Skill ]</a></h2>
						<div class="hbdMargined">
							<div class="tr labelTR">
								<label class="medText">Skill</label>
								<label class="shortNum alignCenter lrBuffer">Total</label>
								<label class="skill_stat alignCenter">Stat</label>
								<label class="shortNum alignCenter lrBuffer">Ranks</label>
								<label class="shortNum alignCenter lrBuffer">Misc</label>
							</div>
							<div id="skillList">
<?	$this->showSkillsEdit(); ?>
							</div>
						</div>
					</div>
					<div id="feats" class="floatRight">
						<h2 class="headerbar hbDark">Feats/Abilities <a id="addFeat" href="">[ Add Feat/Ability ]</a></h2>
						<div id="featList" class="hbdMargined">
<?	$this->showFeatsEdit(); ?>
						</div>
					</div>
				</div>
				
				<div id="powers" class="clearfix">
					<h2 class="headerbar hbDark">Powers</h2>
					<div class="hbdMargined clearfix">
						<div id="powers_atwill" class="powerCol first">
							<h3>At-Will <a href="" data-type="atwill">+</a></h3>
<?	$this->showPowersEdit('atwill'); ?>
						</div>
						<div id="powers_encounter" class="powerCol">
							<h3>Encounter <a href="" data-type="encounter">+</a></h3>
<?	$this->showPowersEdit('encounter'); ?>
						</div>
						<div id="powers_daily" class="powerCol">
							<h3>Daily <a href="" data-type="daily">+</a></h3>
<?	$this->showPowersEdit('daily'); ?>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="weapons" class="textareaDiv floatLeft">
						<h2 class="headerbar hbDark">Weapons</h2>
						<textarea name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
					</div>
					<div id="armor" class="textareaDiv floatRight">
						<h2 class="headerbar hbDark">Armor</h2>
						<textarea name="armor" class="hbdMargined"><?=$this->getArmor()?></textarea>
					</div>
				</div>

				<div id="items">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
				</div>
				
				<div id="notes">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
