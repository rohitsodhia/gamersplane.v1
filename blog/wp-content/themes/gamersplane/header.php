<?
	global $mysql;
	global $loggedIn;
	$loggedIn = checkLogin(0);
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title>Gamers Plane Blog<?php wp_title(' | '); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link rel="shortcut icon" href="<?php echo SITEROOT; ?>/images/favicon.ico">
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<header id="pageHeader">
	<a href="<?=SITEROOT?>/"><img id="logo" src="<?=SITEROOT?>/images/logo.png" alt="Gamers Plane Logo"></a>
	
	<div id="userMenu">
		<img src="<?=SITEROOT?>/images/userMenuR.png">
		<div id="userMenuBody">
<? if (!$loggedIn) { ?>
			<a href="<?=SITEROOT?>/login" class="first">Login</a>
			<a href="<?=SITEROOT?>/register" class="last">Register</a>
<?
	} else {
		$mysql->query('SELECT COUNT(*) FROM pms WHERE recipientID = '.intval($_SESSION['userID']).' AND viewed = 0');
		list($numNewMessages) = $mysql->fetch();
?>
			<span id="menuMessage">Welcome, <a href="<?=SITEROOT?>/users/cp" class="username" class="first"><?=$_SESSION['username']?></a></span>
			<a href="<?=SITEROOT?>/ucp/pms"><img src="<?=SITEROOT?>/images/envelope.jpg" title="Private Messages" alt="Private Messages"> (<?=$numNewMessages?>)</a>
			<a href="<?=SITEROOT?>/logout" class="last">Logout</a>
<? } ?>
		</div>
		<img src="<?=SITEROOT?>/images/userMenuL.png">
	</div>
	
	<div id="followLinks">
		<a href="http://twitter.com/GamersPlane" target="_blank"><img src="<?=SITEROOT?>/images/twitter.png" height="20"></a>
		<a href="https://www.facebook.com/pages/Gamers-Plane/245904792107862" target="_blank"><img src="<?=SITEROOT?>/images/facebook.png" height="20"></a>
<!--		<script src="http://www.stumbleupon.com/hostedbadge.php?s=6"></script>-->
		<a href="http://www.stumbleupon.com/submit?url=http://gamersplane.com" target="_blank"><img src="<?=SITEROOT?>/images/stumble.png" height="20"></a>
	</div>
	
	<div id="mainMenu">
		<a href="<?=SITEROOT?>/tools" class="first">Tools</a>
<? if ($loggedIn) { ?>
		<a href="<?=SITEROOT?>/characters/my">My Characters</a>
		<a href="<?=SITEROOT?>/games/my">My Games</a>
<? } ?>
		<a href="<?=SITEROOT?>/forums">Forums</a>
		<a href="<?=SITEROOT?>/contact" class="last">Contact Us</a>
		
<? if ($loggedIn) { ?>
		<div id="menu_right">
			<a href="<?=SITEROOT?>/users/gamersList" class="first">The Gamers</a>
		</div>
<? } ?>
	</div>
</header>

<div id="page">
