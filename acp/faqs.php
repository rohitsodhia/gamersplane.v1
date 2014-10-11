<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('faqs', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage FAQs</h1>
<?
	$faqRaws = $mongo->faqs->find();
	$faqs = array();
	$categories = array('Getting Started' => 'getting-started', 'Tools' => 'tools', 'Games' => 'games');
	foreach ($faqRaws as $faq) $faqs[$faqs->category] = $faq;
	foreach ($categories as $type => $slug) {
?>
<?	} ?>
			<hr>
			<form method="post" action="/acp/process/addFAQ/">
			</form>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>