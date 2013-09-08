					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer shiftRight">Name</label>
							<label class="shortText alignCenter lrBuffer"><?=$pathOptions[1] == 'spycraft'?'Def':'AC'?> Bonus</label>
<? if ($pathOptions[1] == 'spycraft') { ?>
							<label class="shortText alignCenter lrBuffer">Dam Resist</label>
<? } else { ?>
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
<? } ?>
						</div>
						<div class="tr armor_firstRow">
							<input type="text" name="armor[armorNum][name]" class="armor_name medText lrBuffer">
							<input type="text" name="armor[armorNum][<?=$pathOptions[1] == 'spycraft'?'def':'ac'?>]" class="armors_<?=$pathOptions[1] == 'spycraft'?'def':'ac'?> shortText lrBuffer">
<? if ($pathOptions[1] == 'spycraft') { ?>
							<input type="text" name="armor[armorNum][resist]" class="armors_resist shortText lrBuffer">
<? } else { ?>
							<input type="text" name="armor[armorNum][maxDex]" class="armor_maxDex shortText lrBuffer">
<? } ?>
						</div>
						<div class="tr labelTR armor_secondRow">
<? if ($pathOptions[1] == 'spycraft') { ?>
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
<? } ?>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
<? if ($pathOptions[1] != 'spycraft') { ?>
							<label class="shortText alignCenter lrBuffer">Spell Failure</label>
<? } ?>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
<? if ($pathOptions[1] == 'spycraft') { ?>
							<input type="text" name="armor[armorNum][maxDex]" class="armor_maxDex shortText lrBuffer">
<? } ?>
							<input type="text" name="armor[armorNum][type]" class="armor_type shortText lrBuffer">
							<input type="text" name="armor[armorNum][check]" class="armor_check shortText lrBuffer">
<? if ($pathOptions[1] != 'spycraft') { ?>
							<input type="text" name="armor[armorNum][spellFailure]" class="armor_spellFailure shortText lrBuffer">
<? } ?>
							<input type="text" name="armor[armorNum][speed]" class="armor_speed shortNum lrBuffer">
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer shiftRight">Notes</label>
						</div>
						<div class="tr">
							<input type="text" name="armor[armorNum][notes]" class="armor_notes lrBuffer">
						</div>
					</div>
