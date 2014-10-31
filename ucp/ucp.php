<?
	$userCheck = $mysql->query('SELECT * FROM users WHERE userID = '.$currentUser->userID);
	if ($userCheck->rowCount() == 0) { header('Location: /404'); exit; }
	$userInfo = $userCheck->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">User Control Panel</h1>
		
<? if ($_GET['updated']) { ?>
		<div class="alertBox_success">
			Account successfully updated!
		</div>
		
<? } ?>
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
		<form id="changeOptions" method="post" action="/ucp/process/changeDetails" enctype="multipart/form-data" class="section_profile">
			<div class="tr">
				<label class="textLabel">Avatar</label>
<? if (file_exists(FILEROOT."/ucp/avatars/{$currentUser->userID}.{$userInfo['avatarExt']}")) { ?>
				<div id="avatar">
					<img src="/ucp/avatars/<?=$currentUser->userID.'.'.$userInfo['avatarExt']?>">
					<div><input type="checkbox" name="deleteAvatar"> Delete avatar</div>
				</div>
<? } ?>
				<div>
					<input type="file" name="avatar">
				</div>
				<div class="explanation">Only images at least 150px by 150px will be accepted, with a maximum file size of 1MB.<br>The images will be shrunk for GP use.</div>
			</div>
<!--			<div class="tr">
				<label>Show Avatars?</label>
				<div><input type="checkbox" name="showAvatars"<?=$userInfo['showAvatars'] == 1?' checked="checked"':''?>></div>
			</div>-->
			<div class="tr">
				<label class="textLabel">Timezone</label>
				<div>
					<select name="timezone">
<?
	$file = fopen('timezones.csv', 'r');
	while (($data = fgetcsv($file, 1000, ",")) !== FALSE) echo "\t\t\t\t\t\t<option value=\"{$data[0]}\"".(($userInfo['timezone'] == $data[0])?' selected="selected"':'').'>'.$data[1]/*.($data[2]?' (DST Applies)':'')*/."</option>\n";
	fclose($file);
?>
					</select>
				</div>
			</div>
			<div class="tr">
				<label>Show Timezone?</label>
				<div><input type="checkbox" name="showTZ"<?=$userInfo['showTZ'] == 1?' checked="checked"':''?>></div>
			</div>
			<div class="tr">
				<label>Gender</label>
				<div><input type="radio" name="gender" value="m"<?=$userInfo['gender'] == 'm'?' checked="checked"':''?>> Male <input type="radio" name="gender" value="f"<?=$userInfo['gender'] == 'f'?' checked="checked"':''?>> Female <input type="radio" name="gender" value="n"<?=$userInfo['gender'] == ''?' checked="checked"':''?>> Don't display</div>
			</div>
			<div class="tr">
				<label>Birthday</label>
				<div>
<?
	$bdayParts = explode('-', $userInfo['birthday']);
?>
					Month <select name="month">
<? for ($count = 1; $count <= 12; $count++) echo "						<option".(intval($bdayParts[1]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> Day <select name="day">
<? for ($count = 1; $count <= 31; $count++) echo "						<option".(intval($bdayParts[2]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select> Year <select name="year">
<? for ($count = date('Y') - 5; $count >= date('Y') - 100; $count--) echo "						<option".(intval($bdayParts[0]) == $count?' selected="selected"':'').">$count</option>\n"; ?>
					</select>
				</div>
			</div>
			<div class="tr">
				<label>Show Age?</label>
				<div><input type="checkbox" name="showAge"<?=$userInfo['showAge'] == 1?' checked="checked"':''?>></div>
				<div class="explanation">Only your age will be shown, not your full birthday.</div>
			</div>
			<div class="tr">
				<label class="textLabel">Location</label>
				<div><input type="text" name="location" value="<?=printReady($userInfo['location'])?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">AIM</label>
				<div><input type="text" name="aim" value="<?=printReady($userInfo['aim'])?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Gmail</label>
				<div><input type="text" name="gmail" value="<?=printReady($userInfo['gmail'])?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Twitter</label>
				<div><input type="text" name="twitter" value="<?=printReady($userInfo['twitter'])?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Game Stream (Twitch, etc.)</label>
				<div><input type="text" name="stream" value="<?=printReady($userInfo['stream'])?>"></div>
			</div>
			<div class="tr">
				<label class="textLabel">What games are you into?</label>
				<div><input id="games" type="text" name="games" value="<?=printReady($userInfo['games'])?>"></div>
			</div>
			<div class="tr">
				<label>Recieve new game emails?</label>
				<div><input type="radio" name="newGameMail" value="1"<?=$userInfo['newGameMail'] == 1?' checked="checked"':''?>> Yes <input type="radio" name="newGameMail" value="0"<?=$userInfo['newGameMail'] == 0?' checked="checked"':''?>> No</div>
			</div>
			<div class="tr">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>

		<form method="post" action="/ucp/process/changeInfo" class="section_security hideDiv">
			<div class="tr">
				<label>User Since</label>
				<div class="convertTZ"><?=date('F j, Y H:i a', strtotime($userInfo['joinDate']))?></div>
			</div>
			<div class="tr">
				<label>Email Address</label>
				<div><?=$userInfo['email']?></div>
			</div>
<!--			<div class="tr">
				<label class="textLabel">Change Email Address</label>
				<div><input type="text" name="email" maxlength="100"></div>
			</div>-->
			<div class="tr">
				<label class="textLabel">Old Password</label>
				<div><input type="password" name="oldPass" maxlength="16"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Change Password</label>
				<div><input type="password" name="password1" maxlength="16"></div>
			</div>
			<div class="tr">
				<label class="textLabel">Confirm Password</label>
				<div><input type="password" name="password2" maxlength="16"></div>
			</div>
			<div class="tr">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>
		
		<form id="changeOptions" method="post" action="/ucp/process/changeForumOptions" class="section_forumOptions hideDiv">
			<div id="postSide" class="tr">
				<label>Post Side</label>
				<div><input type="radio" name="postSide" value="r"<?=$userInfo['postSide'] == 'r'?' checked="checked"':''?>> Right</div>
				<div><input type="radio" name="postSide" value="l"<?=$userInfo['postSide'] == 'l'?' checked="checked"':''?>> Left</div>
				<div><input type="radio" name="postSide" value="c"<?=$userInfo['postSide'] == 'c'?' checked="checked"':''?>> Conversation</div>
			</div>
			<div class="tr">
				<button type="submit" name="submit" class="fancyButton">Save</button>
			</div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>