<?php
// Run: php -d short_open_tag=1 tests/starwarsffg-empty-editor.php
// No database, session, or production data is used.
define('FILEROOT', dirname(__DIR__));
require_once FILEROOT . '/includes/characters/Character.class.php';
require_once FILEROOT . '/includes/characters/starwarsffg/starwarsffgCharacter.class.php';
require_once FILEROOT . '/includes/characters/starwarsffg/starwarsffg_consts.class.php';

set_error_handler(function ($severity, $message, $file, $line) {
	throw new ErrorException($message, 0, $severity, $file, $line);
});

class EmptyEditorFixture extends starwarsffgCharacter {
	public function __construct(array $storedData = []) {
		foreach ($storedData as $key => $value) {
			$this->$key = $value;
		}
	}
}

function check($condition, $message) {
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

$skill = ['name' => 'Piloting (Space)', 'stat' => 'agi', 'rank' => 2, 'career' => true];
$talent = ['name' => 'Grit', 'notes' => 'One extra strain'];
$cases = [
	'missing lists' => [],
	'null lists' => ['skills' => null, 'talents' => null],
	'empty lists' => ['skills' => [], 'talents' => []],
	'missing skills with populated talents' => ['talents' => [$talent]],
	'missing talents with populated skills' => ['skills' => [$skill]],
	'populated lists' => ['skills' => [$skill], 'talents' => [$talent]]
];

foreach ($cases as $name => $storedData) {
	$sheet = new EmptyEditorFixture($storedData);
	ob_start();
	try {
		$sheet->showSkillsEdit();
		$sheet->showTalentsEdit();
		$html = ob_get_contents();
	} finally {
		ob_end_clean();
	}
	check(substr_count($html, 'class="skill_name medText"') === 1, "$name: expected one skill row");
	check(substr_count($html, 'class="talent_name placeholder"') === 1, "$name: expected one talent row");
	if (!empty($storedData['skills'])) {
		check(strpos($html, 'value="Piloting (Space)"') !== false, "$name: skill name lost");
		check(strpos($html, 'value="agi" selected="selected"') !== false, "$name: characteristic lost");
		check(strpos($html, 'value="2" class="skill_rank') !== false, "$name: rank lost");
		check(strpos($html, 'checked="checked"') !== false, "$name: career flag lost");
	}
	if (!empty($storedData['talents'])) {
		check(strpos($html, 'value="Grit"') !== false, "$name: talent name lost");
		check(strpos($html, '>One extra strain</textarea>') !== false, "$name: talent notes lost");
	}
	echo "PASS: $name\n";
}

ob_start();
try {
	starwarsffgCharacter::skillEditFormat('n1');
	starwarsffgCharacter::talentEditFormat('n1');
	$html = ob_get_contents();
} finally {
	ob_end_clean();
}
check(strpos($html, 'skills[n1][name]') !== false, 'Dynamic blank skill row missing');
check(strpos($html, 'talents[n1][name]') !== false, 'Dynamic blank talent row missing');
echo "PASS: dynamic blank rows without PHP warnings\n";
