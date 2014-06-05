				<div id="nameDiv" class="tr clearfix">
					<label class="textLabel leftLabel">Name:</label>
					<input type="text" name="name" maxlength="50" value="<?=$this->getName()?>">
				</div>
				
				<div class="clearfix">
					<div id="stats" class="twoCol floatLeft">
						<div class="statCol">
							<div class="tr">
								<label for="st" class="leftLabel">ST</label>
								<input type="text" id="st" name="stats[st]" maxlength="2" value="<?=$this->getStat('st')?>">
							</div>
							<div class="tr">
								<label for="dx" class="leftLabel">DX</label>
								<input type="text" id="dx" name="stats[dx]" maxlength="2" value="<?=$this->getStat('dx')?>">
							</div>
							<div class="tr">
								<label for="iq" class="leftLabel">IQ</label>
								<input type="text" id="iq" name="stats[iq]" maxlength="2" value="<?=$this->getStat('iq')?>">
							</div>
							<div class="tr">
								<label for="ht" class="leftLabel">HT</label>
								<input type="text" id="ht" name="stats[ht]" maxlength="2" value="<?=$this->getStat('ht')?>">
							</div>
						</div>
						<div class="statCol">
							<div class="tr">
								<label for="hp" class="leftLabel">HP</label>
								<input type="text" id="hp" name="stats[hp]" maxlength="2" value="<?=$this->getStat('hp')?>">
							</div>
							<div class="tr">
								<label for="will" class="leftLabel">Will</label>
								<input type="text" id="will" name="stats[will]" maxlength="2" value="<?=$this->getStat('will')?>">
							</div>
							<div class="tr">
								<label for="per" class="leftLabel">Per</label>
								<input type="text" id="per" name="stats[per]" maxlength="2" value="<?=$this->getStat('per')?>">
							</div>
							<div class="tr">
								<label for="fp" class="leftLabel">FP</label>
								<input type="text" id="fp" name="stats[fp]" maxlength="2" value="<?=$this->getStat('fp')?>">
							</div>
						</div>
<!--						<div class="statCol">
							<div class="statRow"><input type="text" id="hp_current" name="hp_current" maxlength="2" value="<?=$charInfo['hp_current']?>"></div>
							<div class="statRow blank">&nbsp;</div>
							<div class="statRow blank">&nbsp;</div>
							<div class="statRow"><input type="text" id="fp_current" name="fp_current" maxlength="2" value="<?=$charInfo['fp_current']?>"></div>
						</div>-->
						<div class="statCol largeCol">
							<div class="tr">
								<label for="dmg_thr" class="leftLabel">Damage (Thrown)</label>
								<input type="text" id="dmg_thr" name="damage[thrown]" maxlength="2" value="<?=$this->getDamage('thrown')?>">
							</div>
							<div class="tr">
								<label for="dmg_sw" class="leftLabel">Damage (Swing)</label>
								<input type="text" id="dmg_sw" name="damage[swing]" maxlength="2" value="<?=$this->getDamage('swing')?>">
							</div>
							<div class="tr">
								<label for="speed" class="leftLabel">Speed</label>
								<input type="text" id="speed" name="speed[speed]" maxlength="5" value="<?=$this->getSpeed('speed')?>">
							</div>
							<div class="tr">
								<label for="move" class="leftLabel">Move</label>
								<input type="text" id="move" name="speed[move]" maxlength="5" value="<?=$this->getSpeed('move')?>">
							</div>
						</div>
					</div>
					
					<div id="langDiv" class="twoCol floatRight">
						<h2 class="headerbar hbDark">Languages</h2>
						<textarea name="languages" class="hbdMargined"><?=$this->getLanguages()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol floatLeft">
						<h2 class="headerbar hbDark">Advantages</h2>
						<textarea name="advantages" class="hbdMargined"><?=$this->getAdvantages()?></textarea>
					</div>
					
					<div class="twoCol floatRight">
						<h2 class="headerbar hbDark">Disadvantages</h2>
						<textarea name="disadvantages" class="hbdMargined"><?=$this->getDisadvantages()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol floatLeft">
						<h2 class="headerbar hbDark">Skills</h2>
						<textarea name="skills" class="hbdMargined"><?=$this->getSkills()?></textarea>
					</div>
					
					<div class="twoCol floatRight">
						<h2 class="headerbar hbDark">Items</h2>
						<textarea name="items" class="hbdMargined"><?=$this->getItems()?></textarea>
					</div>
				</div>
				
				<div id="notesDiv">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
