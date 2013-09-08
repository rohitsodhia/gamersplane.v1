<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

global $mysql;
global $loggedIn;

if ( 'content' != $current_layout ) :
?>
	<div id="sidebar">
		<div id="latestGames" class="widget">
			<h3>Latest Games</h3>
			<div class="widgetBody">
<?
	$mysql->query('SELECT games.gameID, games.title, systems.fullName system, games.gmID, users.username, games.created started, games.numPlayers, numPlayers.playersInGame FROM games INNER JOIN systems ON games.systemID = systems.systemID LEFT JOIN users ON games.gmID = users.userID LEFT JOIN (SELECT gameID, COUNT(*) playersInGame FROM characters WHERE gameID IS NOT NULL AND approved = 1 GROUP BY gameID) numPlayers ON games.gameID = numPlayers.gameID ORDER BY gameID DESC LIMIT 5');
	while ($gameInfo = $mysql->fetch()) {
		$gameInfo['started'] = switchTimezone($_SESSION['timezone'], $gameInfo['started']);
		$gameInfo['numPlayers'] = intval($gameInfo['numPlayers']);
		$gameInfo['playersInGame'] = intval($gameInfo['playersInGame']);
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		echo "\t\t\t\t<div class=\"gameInfo\">\n";
		echo "\t\t\t\t\t<p class=\"title\"><a href=\"".SITEROOT."/games/{$gameInfo['gameID']}\">{$gameInfo['title']}</a> (".($slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}").")</p>\n";
		echo "\t\t\t\t\t<p class=\"details\"><u>{$gameInfo['system']}</u> run by <a href=\"".SITEROOT."/users/{$gameInfo['gmID']}\" class=\"username\">{$gameInfo['username']}</a></p>\n";
//		echo "\t\t\t\t\t<p class=\"details\">Started on ".date('M j, Y g:i a', $gameInfo['started'])."</p>\n";
		echo "\t\t\t\t</div>\n";
	}
?>
			</div>
		</div>
		
		<div id="availSystems" class="widget">
			<h3>Available Systems</h3>
			<div class="widgetBody">
				<p>Gamers Plane has a number of systems built into our site, including:</p>
				<ul>
<?
	$mysql->query('SELECT fullName FROM (SELECT fullName FROM systems WHERE systemID != 0 ORDER BY RAND() LIMIT 4) s ORDER BY fullName');
	while ($info = $mysql->fetch()) echo "\t\t\t\t\t<li>{$info['fullName']}</li>\n";
?>
				</ul>
				<p>And many more available and coming!</p>
<?=$loggedIn?"				<p>If you have a system you want added, <a href=\"".SITEROOT."/forums/thread/2\">let us know</a>!</p>\n":''?>
			</div>
		</div>
		
<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
		
		<aside id="archives" class="widget">
			<h3 class="widget-title">Archives</h3>
			<div class="widgetBody">
				<ul>
				<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</div>
		</aside>
		
<?php endif; // end sidebar widget area ?>
	</div><!-- #secondary .widget-area -->
<?php endif; ?>