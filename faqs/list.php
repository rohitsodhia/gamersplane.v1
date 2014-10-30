<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">FAQs</h1>
		<div class="sideWidget left"><ul>
<?	foreach ($faqsCategories as $category => $slug) { ?>
			<li><a href="#<?=$slug?>"><?=$category?></a></li>
<?	} ?>
		</ul></div>
		<div class="mainColumn right">
<?
	$faqRaws = $mongo->faqs->find()->sort(array('category' => 1, 'order' => 1));
	$faqs = array();
	foreach ($faqRaws as $faq) $faqs[$faq['category']][$faq['order']] = $faq;
	foreach ($faqsCategories as $category => $slug) {
		if (sizeof($faqs[$slug])) { ?>
			<a name="<?=$slug?>"></a>
			<h2 class="headerbar hbDark"><?=$category?></h2>
			<div class="faqs hbdMargined">
<?
			foreach ($faqs[$slug] as $faq) {
?>
				<div class="faq">
					<div class="question"><?=$faq['question']?></div>
					<div class="answer"><?=BBCode2Html(printReady($faq['answer']))?></div>
				</div>
<?			} ?>
			</div>
<?
		}
	}
?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>