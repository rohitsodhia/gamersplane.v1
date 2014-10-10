		<div id="acpMenu" class="sideWidget left"><ul>
<?	if (in_array('autocomplete', $acpPermissions) || in_array('all', $acpPermissions)) { ?>
			<li><a href="/acp/autocomplete/">Manage Autocomplete</a></li>
<?
	}
	if (in_array('faqs', $acpPermissions) || in_array('all', $acpPermissions)) {
?>
			<li><a href="/acp/faqs/">Manage FAQs</a></li>
<?	} ?>
		</ul></div>
