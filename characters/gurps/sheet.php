		<div id="nameDiv" class="tr clearfix">
			<label>Name:</label>
			<div><?=$this->getName()?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats" class="twoCol floatLeft">
				<div class="statCol">
					<div class="tr">
						<label>ST</label>
						<div><?=$this->getStat('st')?></div>
					</div>
					<div class="tr">
						<label>DX</label>
						<div><?=$this->getStat('dx')?></div>
					</div>
					<div class="tr">
						<label>IQ</label>
						<div><?=$this->getStat('iq')?></div>
					</div>
					<div class="tr">
						<label>HT</label>
						<div><?=$this->getStat('ht')?></div>
					</div>
				</div>
				<div class="statCol">
					<div class="tr">
						<label>HP</label>
						<div><?=$this->getStat('hp')?></div>
					</div>
					<div class="tr">
						<label>Will</label>
						<div><?=$this->getStat('will')?></div>
					</div>
					<div class="tr">
						<label>Per</label>
						<div><?=$this->getStat('per')?></div>
					</div>
					<div class="tr">
						<label>FP</label>
						<div><?=$this->getStat('fp')?></div>
					</div>
				</div>
<!--				<div class="statCol">
					<div><?=$charInfo['hp_current']?></div>
					<div class="statRow blank">&nbsp;</div>
					<div class="statRow blank">&nbsp;</div>
					<div><?=$charInfo['fp_current']?></div>
				</div>-->
				<div class="statCol largeCol">
					<div class="tr">
						<label>Damage (Thrown)</label>
						<div><?=$this->getDamage('thrown')?></div>
					</div>
					<div class="tr">
						<label>Damage (Swing)</label>
						<div><?=$this->getDamage('swing')?></div>
					</div>
					<div class="tr">
						<label>Speed</label>
						<div><?=$this->getSpeed('speed')?></div>
					</div>
					<div class="tr">
						<label>Move</label>
						<div><?=$this->getSpeed('move')?></div>
					</div>
				</div>
			</div>
			
			<div id="langDiv" class="twoCol floatRight">
				<h2 class="headerbar hbDark">Languages</h2>
				<div class="hbdMargined"><?=printReady($this->getLanguages())?></div>
			</div>
		</div>

		<div class="clearfix">
			<div class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Advantages</h2>
				<div class="hbdMargined"><?=printReady($this->getAdvantages())?></div>
			</div>
			
			<div class="twoCol floatRight">
				<h2 class="headerbar hbDark">Disadvantages</h2>
				<div class="hbdMargined"><?=printReady($this->getDisadvantages())?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=printReady($this->getSkills())?></div>
			</div>
			
			<div class="twoCol floatRight">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=printReady($this->getItems())?></div>
			</div>
		</div>
		
		<div id="notesDiv">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
		</div>
