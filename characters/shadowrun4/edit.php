				<div class="tr">
					<label for="name" class="textLabel">Name:</label>
					<input type="text" name="name" value="<?=$this->getName()?>" maxlength="50" class="medText">
				</div>
				<div class="tr">
					<label for="metatype" class="textLabel">Metatype:</label>
					<input type="text" name="metatype" value="<?=$this->getMetatype()?>" maxlength="20" class="medText">
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
								<label for="<?=$short?>" class="textLabel"><?=$stat?>:</label>
								<input type="text" name="stats[<?=$short?>]" value="<?=$this->getStat($short)?>" maxlength="3">
							</div>
<?	} ?>
						</div>
					</div>
					
					<div id="qualities">
						<h2 class="headerbar hbDark">Qualities</h2>
						<textarea name="qualities" class="hbdMargined"><?=$this->getQualities()?></textarea>
					</div>
					
					<div id="damage">
						<h2 class="headerbar hbDark">Damage Tracks</h2>
						<div class="hbdMargined">
							<div class="damageTrack">
								<input type="text" name="damage[physical]" value="<?=$this->getDamage('physical')?>" maxlength="2">
								<label for="physical" class="textLabel">Physical Damage</label>
							</div>
							<div class="damageTrack">
								<input type="text" name="damage[stun]" value="<?=$this->getDamage('stun')?>" maxlength="2">
								<label for="stun" class="textLabel">Stun Damage</label>
							</div>
						</div>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="skills" class="twoCol floatLeft">
						<h2 class="headerbar hbDark">Skills</h2>
						<textarea name="skills" class="hbdMargined"><?=$this->getSkills()?></textarea>
					</div>
					<div id="spells" class="twoCol floatRight">
						<h2 class="headerbar hbDark">Spells</h2>
						<textarea name="spells" class="hbdMargined"><?=$this->getSpells()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="weapons" class="twoCol floatLeft">
						<h2 class="headerbar hbDark">Weapons</h2>
						<textarea name="weapons" class="hbdMargined"><?=$this->getWeapons()?></textarea>
					</div>
					<div id="armor" class="twoCol floatRight">
						<h2 class="headerbar hbDark">Armor</h2>
						<textarea name="armor" class="hbdMargined"><?=$this->getArmor()?></textarea>
					</div>
				</div>
				
				<div class="clearfix">
					<div id="augments" class="twoCol floatLeft">
						<h2 class="headerbar hbDark">Augments</h2>
						<textarea name="augments" class="hbdMargined"><?=$this->getAugments()?></textarea>
					</div>
					<div id="contacts" class="twoCol floatRight">
						<h2 class="headerbar hbDark">Contacts</h2>
						<textarea name="contacts" class="hbdMargined"><?=$this->getContacts()?></textarea>
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
