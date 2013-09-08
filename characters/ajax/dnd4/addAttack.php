				<div class="attackBonusSet">
					<div class="tr">
						<label class="medNum leftLabel">Ability</label>
						<input type="text" name="ab_<?=$_POST['count']?>_ability" value="" class="ability">
					</div>
					<div class="tr labelTR">
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Class</label>
						<label class="shortNum alignCenter lrBuffer">Prof</label>
						<label class="shortNum alignCenter lrBuffer">Feat</label>
						<label class="shortNum alignCenter lrBuffer">Enh</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<span class="shortNum lrBuffer addHL total"><?=$total?></span>
						<span class="shortNum lrBuffer addHL">+<?=floor($charInfo['level'] / 2)?></span>
						<input type="text" name="ab_<?=$_POST['count']?>_stat" class="statInput lrBuffer">
						<input type="text" name="ab_<?=$_POST['count']?>_class" class="statInput lrBuffer">
						<input type="text" name="ab_<?=$_POST['count']?>_prof" class="statInput lrBuffer">
						<input type="text" name="ab_<?=$_POST['count']?>_feat" class="statInput lrBuffer">
						<input type="text" name="ab_<?=$_POST['count']?>_enh" class="statInput lrBuffer">
						<input type="text" name="ab_<?=$_POST['count']?>_misc" class="statInput lrBuffer">
					</div>
				</div>
