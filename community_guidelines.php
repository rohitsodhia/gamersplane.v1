<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Community Guidelines</h1>
		<p>Welcome to Gamers' Plane. By accessing or using our site or our Discord, you agree to abide by the following terms. These rules are designed to keep our community welcoming, safe, and focused on what brought us here: gaming.</p>

		<h2>Community Conduct</h2>
		<p>Gamers' Plane is a site dedicated to gaming and the community around the games. As such, Gamers' Plane welcomes diverse viewpoints on gaming, and that will by definition include disagreement. However, we explicitly stand against hate and discrimination. All members are entitled to respect. This community is and will remain a safe and welcoming space for minorities and marginalized groups. Any discussion or debate should remain respectful and focused on the topic rather than on the individuals involved. Harassment, hate speech, and personal attacks are not permitted. This includes, but is not limited to: name-calling, belittling, bullying, or the use of slurs or derogatory language based on race, ethnicity, national origin, religion, disability, gender, gender identity, sexual orientation, or age.</p>
		<p>We appreciate your cooperation in keeping Gamers' Plane a great place to game.</p>

		<h2>Content Standards</h2>
		<p>While Gamers&apos; Plane provides a platform for sharing, users are responsible for the content and tone of their own posts. Posts on public forums, public games, and the Gamers&apos; Plane Discord should comply with the following:</p>
		<ol>
			<li><strong>Divisive Topics:</strong> Subjects such as politics and religion should be avoided. Topics that are likely to lead to political or religious arguments may be halted by the moderators if the need arises.</li>
			<li><strong>Images:</strong> We strive to maintain an environment appropriate for a general audience. Do not post pornography, sexually explicit content, or realistic depictions of gratuitous violence. Images that impact the site’s performance may also be removed.</li>
			<li><strong>Explicit Content:</strong> Do not post descriptions of pornography, descriptions of sexually explicit actions, or realistic descriptions of gratuitous violence. Also, please refrain from using profanity. If it would get you an adult rating in a theater, it’s too much for the public spaces on Gamers' Plane. </li>
			<li><strong>Language:</strong> Posts in public forums must be in English. Games Tavern posts for Non-English games must be accompanied by an English translation. If a private game is going to be run in a language other than English, that should be stated clearly, in English, on the game's front page.</li>
			<li><strong>Channel Purpose &amp; Etiquette:</strong> Any thread or channel that is named for a specific purpose should be used only for that purpose. In the forums, for instance, game discussions belong in the appropriate Game Discussions forum, site ideas or problems belong under Site Discussions, and game requests belong under Games Tavern. In Discord, there are multiple channels for discussions of various systems. When possible, move a prolonged chat to the channel designated for the subject of that chat.</li>
			<li><strong>Spoilers:</strong> Everyone should have the chance to experience new movies, books, games, and other media. At the same time, those who have already done so may wish to discuss them. To balance these interests, we ask that you follow these guidelines: Any thread about a recent release (less than six months) that includes information not easily available to the public should be marked with [Spoilers] at the beginning of the thread title. In Discord, please use the built-in spoiler tags. If the thread merely mentions a release or discusses what is easy to find, the tag is not necessary. When in doubt, add the tag. After six months, tags are generally not required. However, some media releases content periodically (such as games with monthly updates), so please remain mindful of others who may not be current. Again, when in doubt, add the tag.</li>
			<li><strong>Spam:</strong> Inappropriate, unsolicited or irrelevant posts may be removed.</li>
			<li><strong>Legally Prohibited Content:</strong> You may not post or share anything that is libelous, defamatory, or threatening, or that violates any other laws of the United States, including copyright or other intellectual property rights. This includes sharing or linking to copyrighted material without permission from the rights holder.</li>
			<li><strong>Site Security:</strong> You may not take any action intended to harm Gamers' Plane, its servers, or its content, nor may you attempt to circumvent its security measures. Using multiple accounts simultaneously to circumvent bans, or to manipulate or deceive other users is prohibited.</li>
			<li><strong>Commercial Services/Products:</strong> Promotion of commercial services or products is permitted only in the Advertising forum or the Advertisements discord channel. You must be an existing member of the community in order to make posts advertising any product. All advertising must follow all other rules. Advertisements that follow a known pattern of scammers may be removed.</li>
			<li><strong>Off-Site Promotion:</strong> You may not use Gamers' Plane to advertise or promote other play-by-post sites, nor may you advertise games that take place elsewhere. You are welcome to use third-party tools to assist with games on Gamers' Plane, but the game itself should take place here.</li>
		</ol>

		<h2>Private Content Standards</h2>
		<p>For private games, section 1 (Community Conduct) still applies, as does section 2 (Public Content Standards), subsections 8-11. Beyond that, the mods are not going to police your private games for minor infractions. We understand that some games will include explicit material, profanity, and divisive topics.</p>
		<p>GMs running games (or planning to run games) that contain any exceptions to Section 2 (or any other adult content) must:</p>
		<ul>
			<li>Add the [Adult] tag to the title of any game advertisement.</li>
			<li>Include specific content warnings for language, violence, horror, sex, substance abuse or suicide, etc. in both the game advertisement and the Game Details page.</li>
		</ul>
		<p>The GM is the final arbiter on what is or is not acceptable in a game, provided the game adheres to the rules above. The moderation staff will not intervene on in-game issues outside of these stated rules.</p>

		<h2>Moderation &amp; Enforcement</h2>
		<p>Gamers' Plane reserves the right to moderate content and enforce these rules at its sole discretion. Violations may result in content removal, warnings, temporary suspension, or permanent bans, depending on the nature and severity of the offense.</p>
		<ol>
			<li>Disputes: If you have concerns over a moderation decision, you may appeal it to any moderator, keeping the discussion respectful and calm. Any issues with a moderator may be raised to the site owner, Keleth.</li>
			<li>
				<p>Copyright Compliance & DMCA Agent: Gamers' Plane respects the intellectual property rights of others and expects its users to do the same. In accordance with the Digital Millennium Copyright Act (DMCA), 17 U.S.C. § 512, we have designated a DMCA agent to receive notices of alleged copyright infringement. Our designated agent can be reached at:</p>
				<p>Email: contact@gamersplane.com</p>
				<p>If you believe that your copyrighted work has been copied in a way that constitutes infringement and is accessible on Gamers' Plane or our Discord, please provide our DMCA agent with a written notice containing the information required under 17 U.S.C. § 512(c)(3) (including identification of the copyrighted work, the infringing material, your contact information, a good-faith statement, and your signature).</p>
				<p><strong><em>Repeat Infringer Policy:</em></strong> Gamers' Plane will, in appropriate circumstances, terminate the accounts of users who are determined to be repeat infringers of copyright. A “repeat infringer” includes any user who has received more than two (2) valid DMCA takedown notices that were not successfully counter-noticed, or who has been judicially determined to have infringed copyright on our platform. We reserve the right to terminate any account at any time for a single egregious violation, such as posting a pre-release movie or software.</p>
			</li>
		</ol>


<?php
$mods = $mysql->query('SELECT u.userID, u.username FROM users u INNER JOIN forumAdmins fa ON u.userID = fa.userID AND forumID = 1 WHERE u.userID != 1 ORDER BY username ASC');
?>
		<h1 id="mods">Mod Team</h1>
		<dl class="modTeam">
			<dt><a href="/user/1">Keleth</a> - Site Owner</dt>
<?php
foreach ($mods->fetchAll() as $mod) {
?>
			<dt><a href="/user/<?=$mod['userID']?>"><?=$mod['username']?></a></dt>
<?php
}
?>
		</dl>
<?php require_once(FILEROOT.'/footer.php'); ?>
