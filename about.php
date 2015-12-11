<?	require_once(FILEROOT.'/header.php'); ?>
		<div class="clearfix">
			<div class="sidebar">
				<h2 class="headerbar hbDark" skew-element>Support GP</h2>
				<div id="support" hb-margined>
					<p>If you want to help support GPs costs (hosting, development, etc), there are a few ways you can do so!</p>
					<p>While you're shopping online, you can use these referral links:</p>
					<p><a href="http://amazon.gamersplane.com" target="_blank"><img src="/images/about/amazon.jpg" title="Amazon" alt="Amazon"></a></p>
					<p><a href="http://dtrpg.gamersplane.com" target="_blank"><img src="/images/about/drivethrurpg.jpg" title="DriveThru RPG" alt="DriveThru RPG"></a><br>(this link also works for any of DriveThru's other sites)</p>
					<p><a href="http://erd.gamersplane.com" target="_blank"><img src="/images/about/erd.jpg" title="Easy Roller Dice Co." alt="Easy Roller Dice Co."></a></p>
					<p>Or you can support GP directly by making a donation through PayPal:</p>
					<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="6VHQ2BP4AS7L6">
						<input type="image" src="/images/about/paypal.jpg" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form></p>
				</div>
			</div>
			<div class="mainColumn">
				<h1 class="headerbar" skew-element>About Gamers' Plane</h1>
				<div hb-margined>
					<p>I played my first RPG back in high school (2000 or 2001, not sure). I don't remember exactly how it happened, but basically, a friend was already setting up charater generation for a D&amp;D 3e game during lunch, and somehow I was invited in. I remember creating some sort of ranger, and had a blast.That first game led to many more over the next 3-4 years, starting with D&amp;D 3e and going to D&amp;D 3.5, Cthulhu, and a small variety of games. I looked forward to the games, to the characters, to the story.</p>
					<p>When I got to college, to game more, I decided to join a game though the RPG club on campus. Someone was running a Legend of the Five Rings game, and doing character generation at the campus center As I found the GM, he fell right within the geek stereotype: heavy-set, awash with body odor, generally unhygenic; I was surprised to find the stereotype true. Though I had made it clear I didn't know the game, the GM was annoyed at my lack of knowledge. I also didn't own dice at the time, and though the GM had a bucket of dice (enough that he could have been rolling one a minute and not been through them by the time the game ended), he was annoyed at having to lend me a set. The whole experience soured me, and 20 minutes in, I excused myself to the bathroom and left.</p>
					<p>I decided to see what options I had for online gaming. A friend recommended trying play-by-post gaming through a website he was on. I found the idea neat, but quickly got annoyed at the lack fo community on the site, and how bland it felt. I wasn't impressed and decided to do something about it.</p>
					<p>After many iterations and years and failures, I finally got Gamers' Plane off the ground at the end of 2013. Since then we've been going strong, gaining new players every day, and new games all the time. I'm excited to bring players together from around the world, and hope to keep making great tools to let people get into RPG gaming.</p>
					<p class="alignRight larger">Rohit</p>
					<p class="alignRight">Gamers' Plane's Developer</p>
				</div>
			</div>
		</div>
		<div class="clearfix">
			<h1 class="headerbar">Our Friends</h1>
			<h2 class="headerbar hbDark">Partners</h2>
			<ul id="partners" class="clearfix">
				<li ng-repeat="link in links.partners | orderBy: 'sortName'" equalize-heights="maxHeight.partners">
					<div class="image" ng-class="{ 'noImg': !link.image }">
						<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
							<p ng-if="!link.image">No Image</p>
							<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
						</a>
					</div>
					<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
				</li>
			</ul>

			<h2 class="headerbar hbDark">The RPG Academy Network</h2>
			<ul id="rpgan" class="clearfix network">
				<li ng-repeat="link in links.rpgan | orderBy: 'sortName'" equalize-heights="maxHeight.rpgan">
					<div class="image" ng-class="{ 'noImg': !link.image }">
						<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
							<p ng-if="!link.image">No Image</p>
							<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
						</a>
					</div>
					<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
				</li>
			</ul>

			<h2 class="headerbar hbDark">Affiliates</h2>
			<ul id="affiliates" class="clearfix">
				<li ng-repeat="link in links.affiliates | filter: { networks: '!rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.affiliates">
					<div class="image" ng-class="{ 'noImg': !link.image }">
						<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
							<p ng-if="!link.image">No Image</p>
							<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
						</a>
					</div>
					<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
				</li>
			</ul>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>