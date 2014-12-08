		<div id="acpMenu" class="sideWidget left"><ul>
<?	if ($currentUser->checkACP('music', false)) { ?>
			<li><a href="/acp/music/">Manage Music</a></li>
<?
	}
	if ($currentUser->checkACP('autocomplete', false)) {
?>
			<li><a href="/acp/autocomplete/">Manage Autocomplete</a></li>
<?
	}
	if ($currentUser->checkACP('faqs', false)) {
?>
			<li><a href="/acp/faqs/">Manage FAQs</a></li>
<?	} ?>
		</ul></div>
