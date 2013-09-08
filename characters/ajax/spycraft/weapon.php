					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer shiftRight">Name</label>
							<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
							<label class="shortText alignCenter lrBuffer">Damage</label>
						</div>
						<div class="tr">
							<input type="text" name="weapon[weaponNum][name]" class="weapon_name medText lrBuffer">
							<input type="text" name="weapon[weaponNum][ab]" class="weapons_ab shortText lrBuffer">
							<input type="text" name="weapon[weaponNum][damage]" class="weapon_damage shortText lrBuffer">
						</div>
						<div class="tr labelTR weapon_secondRow">
<? if ($pathOptions[1] == 'spycraft') { ?>
							<label class="shortText alignCenter lrBuffer">Error</label>
<? } ?>
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
<? if ($pathOptions[1] == 'spycraft') { ?>
							<input type="text" name="weapon[weaponNum][error]" class="weapon_error shortText lrBuffer">
<? } ?>
							<input type="text" name="weapon[weaponNum][crit]" class="weapon_crit shortText lrBuffer">
							<input type="text" name="weapon[weaponNum][range]" class="weapon_range shortText lrBuffer">
							<input type="text" name="weapon[weaponNum][type]" class="weapon_type shortText lrBuffer">
							<input type="text" name="weapon[weaponNum][size]" class="weapon_size shortNum lrBuffer">
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer shiftRight">Notes</label>
						</div>
						<div class="tr">
							<input type="text" name="weapon[weaponNum][notes]" class="weapon_notes lrBuffer">
						</div>
					</div>
