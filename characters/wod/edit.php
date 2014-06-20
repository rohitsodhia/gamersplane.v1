				<div id="name" class="tr">
					<label class="textLabel leftLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>" class="medText">
				</div>
				
				<div id="attributes">
					<h2 class="headerbar hbDark">Attributes</h2>
					<table>
<?	foreach (wod_consts::getAttrNames() as $category => $stats) { ?>
						<tr>
							<th><?=$category?></th>
<?		foreach ($stats as $short => $stat) { ?>
							<td class="statName"><?=$stat?></td>
							<td class="statVal">
<?			for ($count = 1; $count <= 5; $count++) { ?>
								<input type="radio" name="attributes[<?=$short?>]" value="<?=$count?>"<?=$this->getAttribute($short) == $count?' checked="checked"':''?>>
<?			} ?>
							</td>
<?		} ?>
						</tr>
<?	} ?>
					</table>
				</div>
				
				<div class="clearfix">
					<div id="skills">
						<h2 class="headerbar hbDark">Skills</h2>
						<div class="hbdMargined">
<?	foreach (wod_consts::getSkillNames() as $category => $skillSet) { ?>
							<div id="skills_<?=strtolower($category)?>" class="skillSet">
								<h3><?=$category?></h3>
								<p>(-<?=$category == 'Mental'?3:1?> unskilled)</p>
<?		foreach ($skillSet as $skill) { ?>
								<div class="tr">
									<label><?=$skill?></label>
									<div class="skillRank">
										<span>0</span>
<?			for ($count = 0; $count <= 5; $count++) { ?>
										<input type="radio" name="skills[<?=$skill?>]" value="<?=$count?>"<?=$this->getSkill($skill) == $count?' checked="checked"':''?>>
<?			} ?>
										<span>5</span>
									</div>
								</div>
<?		} ?>
							</div>
							
<?	} ?>
						</div>
					</div>
					
					<div id="otherTraits">
						<h2 class="headerbar hbDark">Other Traits</h2>
						<div class="hbdMargined clearfix">
							<div class="col floatLeft">
								<div id="merits">
									<h3>Merits</h3>
									<textarea name="merits"><?=$this->getMerits()?></textarea>
								</div>
								
								<div id="flaws" class="marginTop">
									<h3>Flaws</h3>
									<textarea name="flaws"><?=$this->getFlaws()?></textarea>
								</div>
							</div>
							<div class="col floatRight">
								<div class="clearfix">
									<div id="health" class="alignCenter">
										<h3>Health</h3>
										<input type="text" name="health" maxlength="2" value="<?=$this->getTrait('health')?>">
									</div>
									<div id="willpower" class="alignCenter">
										<h3>Willpower</h3>
										<input type="text" name="willpower" maxlength="2" value="<?=$this->getTrait('willpower')?>">
									</div>
									<div id="morality" class="alignCenter">
										<h3>Morality</h3>
										<input type="text" name="morality" maxlength="2" value="<?=$this->getTrait('morality')?>">
									</div>
								</div>
								
								<div class="tr marginTop">
									<label class="textLabel">Size</label>
									<input type="text" name="size" maxlength="2" value="<?=$this->getTrait('size')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Speed</label>
									<input type="text" name="speed" maxlength="2" value="<?=$this->getTrait('speed')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Initiative Mod</label>
									<input type="text" name="initiativeMod" maxlength="2" value="<?=$this->getTrait('initiativeMod')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Defense</label>
									<input type="text" name="defense" maxlength="2" value="<?=$this->getTrait('defense')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Armor</label>
									<input type="text" name="armor" maxlength="2" value="<?=$this->getTrait('armor')?>">
								</div>
							</div>
						</div>
					</div>
					
					<div id="itemsDiv">
						<div id="weapons">
							<h2 class="headerbar hbDark">Weapons</h2>
							<textarea name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
						</div>
						<div id="equipment">
							<h2 class="headerbar hbDark">Equipment</h2>
							<textarea name="equipment" class="hbdMargined"><?=$this->getEquipment()?></textarea>
						</div>
					</div>
				</div>
				
				<div id="notes" class="marginTop">
					<h2 id="notesTitle" class="headerbar hbDark">Notes</h3>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
