<?php
function sanitizeString($string) {
	$options = func_get_args();
	array_shift($options);

	if (in_array('search_format', $options)) {
		$string = preg_replace('/[^A-za-z0-9]/', ' ', $string);
		$options = array('lower', 'rem_dup_spaces');
	}

	$string = trim($string);
	$string = strip_tags($string);
	// $string = utf8_decode($string);
	if (in_array('lower', $options)) $string = strtolower($string);
	if (in_array('like_clean', $options)) $string = str_replace(array('%', '_'), array('\%', '\_'), strip_tags($string));
	if (in_array('rem_dup_spaces', $options)) $string = preg_replace('/\s+/', ' ', $string);

	return $string;
}
?>
