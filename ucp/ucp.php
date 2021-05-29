<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar" skew-element>User Control Panel{{CurrentUser.userID != user.userID?' - ' + user.username:''}}</h1>
		<div ng-if="admin" id="acpLink" class="alignRight hbMargined"><a href="/acp/users/">Users ACP</a></div>

		<form enctype="multipart/form-data">
			<div id="profile">
				<h2 class="headerbar hbDark" skew-element>Profile</h2>
				<div class="tr">
					<label>User Since</label>
					<div>{{user.joinDate | convertTZ:'YYYY-MM-DD HH:mm:ss':'MMMM D, YYYY h:mm a'}}</div>
				</div>
				<div id="avatar" class="tr">
					<label>Avatar</label>
					<div>
						<div id="avatarDisp">
							<img ng-src="{{user.avatar.url}}?{{avatarTime}}">
							<div ng-if="user.avatar.avatarExt"><pretty-checkbox checkbox="user.avatar.delete"></pretty-checkbox> Delete avatar</div>
						</div>
						<input type="file" ngf-select ng-model="newAvatar">
						<div class="explanation">Only images at least 150px by 150px will be accepted, with a maximum file size of 1MB.<br>The images may be shrunk for GP use.</div>
					</div>
				</div>
				<div id="gender" class="tr">
					<label>Gender</label>
					<div><label for><pretty-radio radio="user.gender" r-value="'m'"></pretty-radio> Male</label> <label for><pretty-radio radio="user.gender" r-value="'f'"></pretty-radio> Female</label> <label for><pretty-radio radio="user.gender" r-value="'n'"></pretty-radio> Don't display</label></div>
				</div>
				<div class="tr">
					<label>Birthday</label>
					<div id="birthday">
						<input type="text" ng-model="user.birthday.date.month" placeholder="MM"> / <input type="text" ng-model="user.birthday.date.day" placeholder="DD"> / <input type="text" ng-model="user.birthday.date.year" placeholder="YYYY">
					</div>
				</div>
				<div class="tr">
					<label>Show Age?</label>
					<div>
						<pretty-checkbox checkbox="user.birthday.showAge" value="category"></pretty-checkbox>
						<span class="explanation">Only your age will be shown, not your full birthday.</span>
					</div>
				</div>
				<div class="tr">
					<label>Location</label>
					<div><input type="text" ng-model="user.location"></div>
				</div>
				<div class="tr">
					<label>Twitter</label>
					<div><input type="text" ng-model="user.twitter"></div>
				</div>
				<div class="tr">
					<label>Game Stream (Twitch, etc.)</label>
					<div><input type="text" ng-model="user.stream"></div>
				</div>
				<div class="tr">
					<label>What games are you into?</label>
					<div><input type="text" ng-model="user.games"></div>
				</div>
				<div class="tr">
					<label>Receive PM emails?</label>
					<div>
						<label for>
							<pretty-radio radio="user.pmMail" r-value="true"></pretty-radio> Yes
						</label>
						<label for>
							<pretty-radio radio="user.pmMail" r-value="false"></pretty-radio> No
						</label>
					</div>
				</div>
				<div class="tr">
					<label>Receive new game emails?</label>
					<div>
						<label for>
							<pretty-radio radio="user.newGameMail" r-value="true"></pretty-radio> Yes
						</label>
						<label for>
							<pretty-radio radio="user.newGameMail" r-value="false"></pretty-radio> No
						</label>
					</div>
				</div>
				<div class="tr">
					<label>Receive GM emails? (Approvals)</label>
					<div>
						<label for>
							<pretty-radio radio="user.gmMail" r-value="true"></pretty-radio> Yes
						</label>
						<label for>
							<pretty-radio radio="user.gmMail" r-value="false"></pretty-radio> No
						</label>
					</div>
				</div>
				
				<div class="tr submitDiv">
					<button type="submit" ng-click="save()" class="fancyButton" skew-element>Save</button>
				</div>
			</div>

			<div id="security">
				<h2 class="headerbar hbDark" skew-element>Security</h2>
				<!-- <div ng-if="admin" class="tr">
					<label>Username</label>
					<div><input type="text" ng-model="user.username"></div>
				</div> -->
				<!-- <div class="tr">
					<label>Email Address</label>
					<div><input type="text" ng-model="user.email"></div>
				</div> -->
				<div class="tr">If you're looking to change your username or email, please email contact@gamersplane.com; I've had to temporarily disable the automatic functionality.</div>
				<div ng-if="user.userID == CurrentUser.userID" class="tr">
					<label>Old Password</label>
					<div><input type="password" ng-model="newPass.oldPassword" maxlength="32"></div>
				</div>
				<div ng-show="" class="error">Your old password is wrong</div>
				<div class="tr">
					<label for="password1">Change Password</label>
					<div><input id="password1" type="password" ng-model="newPass.password1" maxlength="32"></div>
				</div>
				<div class="explanation">Password must be between 6-32 characters</div>
				<div ng-show="newPass.password1.length && newPass.password1.length < 6" class="error">Password too short</div>
				<div ng-show="newPass.password1.length > 32" class="error">Password too long</div>
				<div class="tr">
					<label for="password2">Confirm Password</label>
					<div><input id="password2" type="password" ng-model="newPass.password2" ng-focus="passMissmatch = false" ng-blur="samePass()" maxlength="32"></div>
				</div>
				<div ng-show="passMismatch" class="error">Passwords don't match</div>
				<div class="tr submitDiv">
					<button type="submit" ng-click="save()" class="fancyButton" skew-element>Save</button>
				</div>
			</div>

			<div id="forumOptions">
				<h2 class="headerbar hbDark" skew-element>Forum Options</h2>
				<div id="postSide" class="tr">
					<label>Post Side</label>
					<div>
						<label for>
							<pretty-radio radio="user.postSide" r-value="'r'"></pretty-radio> Right
						</label>
						<label for>
							<pretty-radio radio="user.postSide" r-value="'l'"></pretty-radio> Left
						</label>
						<label for>
							<pretty-radio radio="user.postSide" r-value="'c'"></pretty-radio> Conversation
						</label>
					</div>
				</div>
				<div id="theme" class="tr">
					<label>Theme</label>
					<div><label for><pretty-radio radio="user.theme" r-value="''"></pretty-radio> Default</label> <label for><pretty-radio radio="user.theme" r-value="'dark'"></pretty-radio> Dark</label></div>
				</div>
				<div id="warn" class="tr">
					<label>Warn on leaving a post without saving</label>
					<div><label for><pretty-radio radio="user.warnUnsaved" r-value="''"></pretty-radio> Yes</label> <label for><pretty-radio radio="user.warnUnsaved" r-value="'no'"></pretty-radio> No</label></div>
				</div>
				<div class="tr submitDiv">
					<button type="submit" ng-click="save()" class="fancyButton" skew-element>Save</button>
				</div>
			</div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>
