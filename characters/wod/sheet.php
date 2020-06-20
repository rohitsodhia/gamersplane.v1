			<div id="nameDiv" class="tr">
				<label>Name:</label>
				<div><?=$this->getName()?></div>
			</div>

			<div id="attributes">
				<h2 class="headerbar hbDark">Attributes</h2>
				<table>
<?	foreach (wod_consts::getAttrNames() as $category => $stats) { ?>
					<tr>
						<th><?=$category?></th>
<?		foreach ($stats as $short => $stat) { ?>
						<td class="statName"><?=$stat?></td>
						<td class="statVal"><div class="dots"><div class="dotCount_<?=$this->getAttribute($short)?>"></div></div></td>
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
						<div id="skills_mental" class="skillSet">
							<h3><?=$category?></h3>
							<p>(-<?=$category == 'Mental'?3:1?> unskilled)</p>
<?		foreach ($skillSet as $skill) { ?>
							<div class="tr">
								<label><?=$skill?></label>
								<div class="skillRank"><div class="dots"><div class="dotCount_<?=$this->getSkill($skill)?>"></div></div></div>
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
								<div><?=printReady(BBCode2Html($this->getMerits()))?></div>
							</div>

							<div id="flaws" class="marginTop">
								<h3>Flaws</h3>
								<div><?=printReady(BBCode2Html($this->getFlaws()))?></div>
							</div>
						</div>
						<div class="col floatRight">
							<div class="clearfix">
								<div id="health" class="alignCenter">
									<h3>Health</h3>
									<div><?=$this->getTrait('health')?></div>
								</div>
								<div id="willpower" class="alignCenter">
									<h3>Willpower</h3>
									<div><?=$this->getTrait('willpower')?></div>
								</div>
								<div id="morality" class="alignCenter">
									<h3>Morality</h3>
									<div><?=$this->getTrait('morality')?></div>
								</div>
							</div>

							<div class="tr marginTop">
								<label class="textLabel">Size</label>
								<div><?=$this->getTrait('size')?></div>
							</div>
							<div class="tr">
								<label class="textLabel">Speed</label>
								<div><?=$this->getTrait('speed')?></div>
							</div>
							<div class="tr">
								<label class="textLabel">Initiative Mod</label>
								<div><?=$this->getTrait('initiativeMod')?></div>
							</div>
							<div class="tr">
								<label class="textLabel">Defense</label>
								<div><?=$this->getTrait('defense')?></div>
							</div>
							<div class="tr">
								<label class="textLabel">Armor</label>
								<div><?=$this->getTrait('armor')?></div>
							</div>
						</div>
					</div>
				</div>

				<div id="itemsDiv">
					<div id="weapons">
						<h2 class="headerbar hbDark">Weapons</h2>
						<div class="hbdMargined"><?=printReady(BBCode2Html($this->getWeapons()))?></div>
					</div>
					<div id="equipment">
						<h2 class="headerbar hbDark">Equipment</h2>
						<div class="hbdMargined"><?=printReady(BBCode2Html($this->getEquipment()))?></div>
					</div>
				</div>
			</div>

			<div id="notes" class="marginTop">
				<h2 id="notesTitle" class="headerbar hbDark">Notes</h3>
				<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
			</div>
