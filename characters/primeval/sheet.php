		<div id="nameDiv" class="tr clearfix">
			<label>Name:</label>
			<div><?=$this->getName()?></div>
		</div>
		
		<div class="clearfix">
			<div id="primaryStatsCol">
				<h2 class="headerbar hbDark">Primary Attributes</h2>
				<div class="hbdMargined clearfix">
					<div class="twoCol leftCol">
						<div class="tr clearfix">
							<label>Strength</label>
							<?=$this->getStat('str')?>
						</div>
						<div class="tr clearfix">
							<label>Dexterity</label>
							<?=$this->getStat('dex')?>
						</div>
						<div class="tr clearfix">
							<label>Constitution</label>
							<?=$this->getStat('con')?>
						</div>
					</div>
					<div class="twoCol">
						<div class="tr clearfix">
							<label>Intelligence</label>
							<?=$this->getStat('int')?>
						</div>
						<div class="tr clearfix">
							<label>Perception</label>
							<?=$this->getStat('per')?>
						</div>
						<div class="tr clearfix">
							<label>Willpower</label>
							<?=$this->getStat('wil')?>
						</div>
					</div>
				</div>
			</div>
			
			<div id="secondaryStatsCol">
				<h2 class="headerbar hbDark">Secondary Attributes</h2>
				<div class="hbdMargined clearfix">
					<div class="tr clearfix">
						<label>Life Points</label>
						<?=$this->getStat('lp')?>
					</div>
					<div class="tr clearfix">
						<label>Endurance Points</label>
						<?=$this->getStat('end')?>
					</div>
					<div class="tr clearfix">
						<label>Speed</label>
						<?=$this->getStat('spd')?>
					</div>
					<div class="tr clearfix">
						<label>Essence Pool</label>
						<?=$this->getStat('ess')?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Qualities</h2>
				<div class="hbdMargined"><?=printReady($this->getQualities())?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Drawbacks</h2>
				<div class="hbdMargined"><?=printReady($this->getDrawbacks())?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=printReady($this->getSkills())?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Powers</h2>
				<div class="hbdMargined"><?=printReady($this->getPowers())?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbdMargined"><?=printReady($this->getWeapons())?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Posessions</h2>
				<div class="hbdMargined"><?=printReady($this->getPosessions())?></div>
			</div>
		</div>
		
		<div id="charInfoDiv" class="clearfix">
			<h2 class="headerbar hbDark">Character Notes</h2>
			<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
		</div>
