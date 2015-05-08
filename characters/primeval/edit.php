				<div id="nameDiv" class="tr">
					<label class="textLabel">Name:</label>
					<div><input type="text" name="name" maxlength="50" value="<?=$this->getName()?>"></div>
				</div>
				
				<div class="clearfix">
					<div id="primaryStatsCol">
						<h2 class="headerbar hbDark">Primary Attributes</h2>
						<div class="hbdMargined clearfix">
							<div class="twoCol leftCol">
								<div class="tr">
									<label class="textLabel">Strength</label>
									<input type="text" name="stat[str]" maxlength="2" value="<?=$this->getStat('str')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Dexterity</label>
									<input type="text" name="stat[dex]" maxlength="2" value="<?=$this->getStat('dex')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Constitution</label>
									<input type="text" name="stat[con]" maxlength="2" value="<?=$this->getStat('con')?>">
								</div>
							</div>
							<div class="twoCol">
								<div class="tr">
									<label class="textLabel">Intelligence</label>
									<input type="text" name="stat[int]" maxlength="2" value="<?=$this->getStat('int')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Perception</label>
									<input type="text" name="stat[per]" maxlength="2" value="<?=$this->getStat('per')?>">
								</div>
								<div class="tr">
									<label class="textLabel">Willpower</label>
									<input type="text" name="stat[wil]" maxlength="2" value="<?=$this->getStat('wil')?>">
								</div>
							</div>
						</div>
					</div>
					
					<div id="secondaryStatsCol">
						<h2 class="headerbar hbDark">Secondary Attributes</h2>
						<div class="hbdMargined clearfix">
							<div class="tr">
								<label class="textLabel">Life Points</label>
								<input type="text" name="stat[lp]" maxlength="2" value="<?=$this->getStat('lp')?>">
							</div>
							<div class="tr">
								<label class="textLabel">Endurance Points</label>
								<input type="text" name="stat[end]" maxlength="2" value="<?=$this->getStat('end')?>">
							</div>
							<div class="tr">
								<label class="textLabel">Speed</label>
								<input type="text" name="stat[spd]" maxlength="2" value="<?=$this->getStat('spd')?>">
							</div>
							<div class="tr">
								<label class="textLabel">Essence Pool</label>
								<input type="text" name="stat[ess]" maxlength="2" value="<?=$this->getStat('ess')?>">
							</div>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol leftCol">
						<h2 class="headerbar hbDark">Qualities</h2>
						<textarea name="qualities" class="hbdMargined"><?=$this->getQualities()?></textarea>
					</div>
					
					<div class="twoCol">
						<h2 class="headerbar hbDark">Drawbacks</h2>
						<textarea name="drawbacks" class="hbdMargined"><?=$this->getDrawbacks()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol leftCol">
						<h2 class="headerbar hbDark">Skills</h2>
						<textarea name="skills" class="hbdMargined"><?=$this->getSkills()?></textarea>
					</div>
					
					<div class="twoCol">
						<h2 class="headerbar hbDark">Powers</h2>
						<textarea name="powers" class="hbdMargined"><?=$this->getPowers()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div class="twoCol leftCol">
						<h2 class="headerbar hbDark">Weapons</h2>
						<textarea name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
					</div>
					
					<div class="twoCol">
						<h2 class="headerbar hbDark">Posessions</h2>
						<textarea name="items" class="hbdMargined"><?=$this->getPosessions()?></textarea>
					</div>
				</div>
				
				<div id="charInfoDiv">
					<h2 class="headerbar hbDark">Notes</h2>
					<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
				</div>
