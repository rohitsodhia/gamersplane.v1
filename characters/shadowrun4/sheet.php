			<div class="tr clearfix">
				<label for="name">Name:</label>
				<div><?=printReady($this->getName())?></div>
			</div>
			<div class="tr clearfix">
				<label for="metatype">Metatype:</label>
				<div><?=printReady($this->getMetatype())?></div>
			</div>

			<div class="clearfix">
				<div id="stats">
					<div class="statCol">
<?
	foreach (shadowrun4_consts::getStatNames() as $short => $stat) {
		if ($short == 'edge_total') {
?>
					</div>
					<div class="statCol">
<?		} ?>
						<div class="tr">
							<label for="<?=$short?>"><?=$stat?>:</label>
							<div><?=$this->getStat($short)?></div>
						</div>
<?	} ?>
					</div>
				</div>

				<div id="qualities">
					<h2 class="headerbar hbDark">Qualities</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getQualities()))?></div>
				</div>

				<div id="damage">
					<h2 class="headerbar hbDark">Damage Tracks</h2>
					<div class="hbdMargined">
						<div class="damageTrack">
							<div><?=$this->getDamage('physical')?></div>
							<label for="physical">Physical Damage</label>
						</div>
						<div class="damageTrack">
							<div><?=$this->getDamage('stun')?></div>
							<label for="stun">Stun Damage</label>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="skills" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getSkills()))?></div>
				</div>
				<div id="spells" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Spells</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getSpells()))?></div>
				</div>
			</div>

			<div class="clearfix">
				<div id="weapons" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Weapons</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getWeapons()))?></div>
				</div>
				<div id="armor" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Armor</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getArmor()))?></div>
				</div>
			</div>

			<div class="clearfix">
				<div id="augments" class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Augments</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getAugments()))?></div>
				</div>
				<div id="contacts" class="twoCol floatRight">
					<h2 class="headerbar hbDark">Contacts</h2>
					<div class="hbdMargined"><?=printReady(BBCode2Html($this->getContacts()))?></div>
				</div>
			</div>

			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getItems()))?></div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<div class="hbdMargined"><?=printReady(BBCode2Html($this->getNotes()))?></div>
			</div>
