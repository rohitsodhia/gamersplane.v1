<?
	if (isset($pathOptions[0]) && intval($pathOptions[0]) && $currentUser->checkACP('users')) {
		$user = new User(intval($pathOptions[0]));
		if (!$user->userID) { header('Location: /ucp/'); exit; }
	} else $user = $currentUser;
	$user->getAllUsermeta();
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">User Control Panel<?=$user->userID != $currentUser->userID?' - '.$user->username:''?></h1>
		
<?	if ($formErrors->getErrors('addFAQ')) { ?>
			<div class="alertBox_error"><ul>
<?
		if ($formErrors->checkError('noCategory')) echo "				<li>No category selected.</li>\n";
		if ($formErrors->checkError('noQuestion')) echo "				<li>No question asked.</li>\n";
		if ($formErrors->checkError('noAnswer')) echo "				<li>No answer given.</li>\n";
?>
			</ul></div>
<?	} ?>
<?	if ($_GET['updated']) { ?>
		<div class="alertBox_success">
			Account successfully updated!
		</div>
		
<?	} ?>
<!--		<div class="clearfix"><div id="controls" class="wingDiv hbDark floatLeft" data-ratio=".8">
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
		</h2>-->
		<form method="post" action="/ucp/process/ucp/" enctype="multipart/form-data">
			<div id="profile">
				<h2 class="headerbar hbDark">Profile</h2>
				<div class="tr">
					<label>User Since</label>
					<div class="convertTZ"><?=date('F j, Y H:i a', strtotime($user->joinDate))?></div>
				</div>
<?	if ($user->userID != $currentUser->userID) { ?>
				<input type="hidden" name="userID" value="<?=$user->userID?>">
<?	} ?>
				<div id="avatar" class="tr">
					<label>Avatar</label>
					<div>
						<div id="avatarDisp">
							<img src="<?=$user->getAvatar()?>">
<?	if ($user->getAvatar(true)) { ?>
							<div><input type="checkbox" name="deleteAvatar"> Delete avatar</div>
<?	} ?>
						</div>
						<input type="file" name="avatar">
						<div class="explanation">Only images at least 150px by 150px will be accepted, with a maximum file size of 1MB.<br>The images will be shrunk for GP use.</div>
					</div>
				</div>
<!--				<div class="tr">
					<label>Show Avatars?</label>
					<div><input type="checkbox" name="showAvatars"<?=$user->showAvatars == 1?' checked="checked"':''?>></div>
				</div>-->
				<div id="gender" class="tr">
					<label>Gender</label>
					<div><input id="male" type="radio" name="gender" value="m"<?=$user->gender == 'm'?' checked="checked"':''?>> <label for="male">Male</label> <input id="female" type="radio" name="gender" value="f"<?=$user->gender == 'f'?' checked="checked"':''?>> <label for="female">Female</label> <input id="gendre_dd" type="radio" name="gender" value="n"<?=$user->gender == ''?' checked="checked"':''?>> <label for="gendre_dd">Don't display</label></div>
				</div>
				<div class="tr">
					<label>Birthday</label>
					<div>
<?	$bdayParts = explode('-', $user->birthday); ?>
						<span>Month</span> <select name="month">
<?	for ($count = 1; $count <= 12; $count++) echo "							<option".(intval($bdayParts[1]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> <span>Day</span> <select name="day">
<?	for ($count = 1; $count <= 31; $count++) echo "							<option".(intval($bdayParts[2]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> <span>Year</span> <select name="year">
<?	for ($count = date('Y') - 5; $count >= date('Y') - 100; $count--) echo "							<option".(intval($bdayParts[0]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
						</select>
					</div>
				</div>
				<div class="tr">
					<label>Show Age?</label>
					<div><input type="checkbox" name="showAge"<?=$user->showAge == 1?' checked="checked"':''?>></div>
					<div class="explanation">Only your age will be shown, not your full birthday.</div>
				</div>
				<div class="tr">
					<label>Location</label>
					<div><input type="text" name="location" value="<?=printReady($user->location)?>"></div>
				</div>
				<div class="tr">
					<label>Twitter</label>
					<div><input type="text" name="twitter" value="<?=printReady($user->twitter)?>"></div>
				</div>
				<div class="tr">
					<label>Game Stream (Twitch, etc.)</label>
					<div><input type="text" name="stream" value="<?=printReady($user->stream)?>"></div>
				</div>
				<div class="tr">
					<label>What games are you into?</label>
					<div><input id="games" type="text" name="games" value="<?=printReady($user->games)?>"></div>
				</div>
				<div class="tr">
					<label>Recieve new game emails?</label>
					<div><input type="radio" name="newGameMail" value="1"<?=$user->newGameMail == 1?' checked="checked"':''?>> Yes <input type="radio" name="newGameMail" value="0"<?=$user->newGameMail == 0?' checked="checked"':''?>> No</div>
				</div>
				<div class="tr submitDiv">
					<button type="submit" name="submit" class="fancyButton">Save</button>
				</div>
			</div>

			<div id="security">
				<h2 class="headerbar hbDark">Security</h2>
<?	if ($user->userID != $currentUser->userID) { ?>
				<div class="tr">
					<label>Username</label>
					<div><input type="text" name="username" value="<?=$user->username?>"></div>
				</div>
<?	} ?>
				<div class="tr">
					<label>Email Address</label>
					<div><input type="text" name="email" value="<?=$user->email?>"></div>
				</div>
<?	if ($user->userID == $currentUser->userID) { ?>
				<div class="tr">
					<label>Old Password</label>
					<div><input type="password" name="oldPass" maxlength="16"></div>
				</div>
<?	} ?>
				<div class="tr">
					<label for="password1">Change Password</label>
					<div><input id="password1" type="password" name="password1" maxlength="32"></div>
				</div>
				<div class="explanation">Password must be between 6-32 characters</div>
				<div id="passShort" class="<?=$formErrors->checkError('passShort')?'':'hideDiv'?> error">Password too short</div>
				<div id="passLong" class="hideDiv error">Password too long</div>
				<div class="tr">
					<label for="password2">Confirm Password</label>
					<div><input id="password2" type="password" name="password2" maxlength="32"></div>
				</div>
				<div id="passMismatch" class="hideDiv error">Passwords don't match</div>
				<div class="tr submitDiv">
					<button type="submit" name="submit" class="fancyButton">Save</button>
				</div>
			</div>

			<div id="forumOptions">
				<h2 class="headerbar hbDark">Forum Options</h2>
				<div id="postSide" class="tr">
					<label>Post Side</label>
					<div>
						<input id="ps_right" type="radio" name="postSide" value="r"<?=$user->postSide == 'r'?' checked="checked"':''?>> <label for="ps_right">Right</label>
						<input id="ps_left" type="radio" name="postSide" value="l"<?=$user->postSide == 'l'?' checked="checked"':''?>> <label for="ps_left">Left</label>
						<input id="ps_conversation" type="radio" name="postSide" value="c"<?=$user->postSide == 'c'?' checked="checked"':''?>> <label for="ps_conversation">Conversation</label>
					</div>
				</div>
				<div class="tr submitDiv">
					<button type="submit" name="submit" class="fancyButton">Save</button>
				</div>
			</div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>