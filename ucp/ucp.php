<?
	$currentUser->getAllUsermeta();
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">User Control Panel</h1>
		
<?	if ($_GET['updated']) { ?>
		<div class="alertBox_success">
			Account successfully updated!
		</div>
		
<?	} ?>
		<div class="clearfix"><div id="controls" class="wingDiv hbDark floatLeft" data-ratio=".8">
			<div>
				<a href="" class="section_profile current">Profile</a>
				<a href="" class="section_security">Security</a>
				<a href="" class="section_forumOptions">Forum Options</a>
			</div>
			<div class="wing dlWing"></div>
			<div class="wing drWing"></div>
		</div></div>
		<h2 class="headerbar hbDark">
			<span class="section_profile">Profile</span>
			<span class="section_security hideDiv">Security</span>
			<span class="section_forumOptions hideDiv">Forum Options</span>
		</h2>
		<form id="changeOptions" method="post" action="/ucp/process/changeDetails/" enctype="multipart/form-data" class="section_profile">
			<div id="avatar" class="tr">
				<label>Avatar</label>
				<div>
					<div id="avatarDisp">
						<img src="<?=$currentUser->getAvatar()?>">
<?	if ($currentUser->getAvatar(true)) { ?>
						<div><input type="checkbox" name="deleteAvatar"> Delete avatar</div>
<?	} ?>
					</div>
					<input type="file" name="avatar">
					<div class="explanation">Only images at least 150px by 150px will be accepted, with a maximum file size of 1MB.<br>The images will be shrunk for GP use.</div>
				</div>
			</div>
<!--			<div class="tr">
				<label>Show Avatars?</label>
				<div><input type="checkbox" name="showAvatars"<?=$currentUser->showAvatars == 1?' checked="checked"':''?>></div>
			</div>-->
			<div class="tr">
				<label>Gender</label>
				<div><input type="radio" name="gender" value="m"<?=$currentUser->gender == 'm'?' checked="checked"':''?>> Male <input type="radio" name="gender" value="f"<?=$currentUser->gender == 'f'?' checked="checked"':''?>> Female <input type="radio" name="gender" value="n"<?=$currentUser->gender == ''?' checked="checked"':''?>> Don't display</div>
			</div>
			<div class="tr">
				<label>Birthday</label>
				<div>
<?
	$bdayParts = explode('-', $currentUser->birthday);
?>
					<span>Month</span> <select name="month">
<?	for ($count = 1; $count <= 12; $count++) echo "						<option".(intval($bdayParts[1]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> <span>Day</span> <select name="day">
<?	for ($count = 1; $count <= 31; $count++) echo "						<option".(intval($bdayParts[2]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> <span>Year</span> <select name="year">
<?	for ($count = date('Y') - 5; $count >= date('Y') - 100; $count--) echo "						<option".(intval($bdayParts[0]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select>
				</div>
			</div>
			<div class="tr">
				<label>Show Age?</label>
				<div><input type="checkbox" name="showAge"<?=$currentUser->showAge == 1?' checked="checked"':''?>></div>
				<div class="explanation">Only your age will be shown, not your full birthday.</div>
			</div>
			<div class="tr">
				<label>Location</label>
				<div><input type="text" name="location" value="<?=printReady($currentUser->location)?>"></div>
			</div>
			<div class="tr">
				<label>Twitter</label>
				<div><input type="text" name="twitter" value="<?=printReady($currentUser->twitter)?>"></div>
			</div>
			<div class="tr">
				<label>Game Stream (Twitch, etc.)</label>
				<div><input type="text" name="stream" value="<?=printReady($currentUser->stream)?>"></div>
			</div>
			<div class="tr">
				<label>What games are you into?</label>
				<div><input id="games" type="text" name="games" value="<?=printReady($currentUser->games)?>"></div>
			</div>
			<div class="tr">
				<label>Recieve new game emails?</label>
				<div><input type="radio" name="newGameMail" value="1"<?=$currentUser->newGameMail == 1?' checked="checked"':''?>> Yes <input type="radio" name="newGameMail" value="0"<?=$currentUser->newGameMail == 0?' checked="checked"':''?>> No</div>
			</div>
			<div class="tr submitDiv">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>

		<form method="post" action="/ucp/process/changeInfo" class="section_security hideDiv">
			<div class="tr">
				<label>User Since</label>
				<div class="convertTZ"><?=date('F j, Y H:i a', strtotime($currentUser->joinDate))?></div>
			</div>
			<div class="tr">
				<label>Email Address</label>
				<div><input type="text" name="email" value="<?=$currentUser->email?>"></div>
			</div>
<!--			<div class="tr">
				<label>Change Email Address</label>
				<div><input type="text" name="email" maxlength="100"></div>
			</div>-->
			<div class="tr">
				<label>Old Password</label>
				<div><input type="password" name="oldPass" maxlength="16"></div>
			</div>
			<div class="tr">
				<label>Change Password</label>
				<div><input type="password" name="password1" maxlength="16"></div>
			</div>
			<div class="tr">
				<label>Confirm Password</label>
				<div><input type="password" name="password2" maxlength="16"></div>
			</div>
			<div class="tr submitDiv">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>
		
		<form id="changeOptions" method="post" action="/ucp/process/changeForumOptions" class="section_forumOptions hideDiv">
			<div id="postSide" class="tr">
				<label>Post Side</label>
				<div><input type="radio" name="postSide" value="r"<?=$currentUser->postSide == 'r'?' checked="checked"':''?>> Right</div>
				<div><input type="radio" name="postSide" value="l"<?=$currentUser->postSide == 'l'?' checked="checked"':''?>> Left</div>
				<div><input type="radio" name="postSide" value="c"<?=$currentUser->postSide == 'c'?' checked="checked"':''?>> Conversation</div>
			</div>
			<div class="tr submitDiv">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>