<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	$currentUser->checkACP('faqs');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage FAQs</h1>
<?	if ($formErrors->getErrors('addFAQ')) { ?>
			<div class="alertBox_error"><ul>
<?
		if ($formErrors->checkError('noCategory')) echo "				<li>No category selected.</li>\n";
		if ($formErrors->checkError('noQuestion')) echo "				<li>No question asked.</li>\n";
		if ($formErrors->checkError('noAnswer')) echo "				<li>No answer given.</li>\n";
?>
			</ul></div>
<?	} ?>
			<form method="post" action="/acp/process/addFAQ/">
				<div class="pRow">
					<label>Category</label>
					<select name="category">
						<option value="">Select One</option>
<?	foreach ($faqsCategories as $category => $slug) { ?>
						<option value="<?=$slug?>"><?=$category?></option>
<?	} ?>
					</select>
				</div>
				<div class="pRow">
					<label for="question">Question:</label>
					<input id="question" type="text" name="question">
				</div>
				<textarea name="answer"></textarea>
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<hr>
<?
	$faqRaws = $mongo->faqs->find()->sort(array('category' => 1, 'order' => 1));
	$faqs = array();
	foreach ($faqRaws as $faq) $faqs[$faq['category']][$faq['order']] = $faq;
	foreach ($faqsCategories as $category => $slug) {
		if (sizeof($faqs[$slug])) { ?>
			<h2 class="headerbar hbDark"><?=$category?></h2>
			<div class="faqs hbdMargined">
<?
			foreach ($faqs[$slug] as $faq) {
?>
				<div class="faq" data-question-id="<?=(string) $faq['_id']?>">
					<div class="controls">
						<a href="" class="sprite upArrow"></a>
						<a href="" class="sprite downArrow"></a>
					</div>
					<div class="display">
						<div class="question"><?=$faq['question']?></div>
						<div class="answer"><?=BBCode2Html(printReady($faq['answer']))?></div>
						<div><a href="" class="edit">[ Edit ]</a> <a href="" class="delete">[ Delete ]</a></div>
					</div>
					<div class="inputs">
						<div class="question"><input type="text" value="<?=htmlspecialchars($faq['question'])?>"></div>
						<div class="answer"><textarea><?=$faq['answer']?></textarea></div>
						<div class="tr"><a href="" class="save">[ Save ]</a> <a href="" class="cancel">[ Cancel ]</a></div>
					</div>
				</div>
<?			} ?>
			</div>
<?
		}
	}
?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>