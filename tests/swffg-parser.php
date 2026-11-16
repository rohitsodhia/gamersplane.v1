<?php
// Standalone parser checks: php -d short_open_tag=1 tests/swffg-parser.php
if (PHP_SAPI !== 'cli') exit;
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require __DIR__.'/../javascript/markItUp/markitup.bbcode-parser.php';
function check($condition, $label) { if (!$condition) throw new RuntimeException($label); echo "PASS $label\n"; }
$source="[_agility=3]\n[table=swffg]\nSkill | Characteristic | Rank\nRanged | [_score$=agility] | [_rank=2]\n[/table]";
$html=BBCode2Html($source);
check(strpos($html,'bbTableSwffg')!==false,'swffg class');
check(strpos($html,'bbTableRolls')===false,'separate from generic rolls');
preg_match_all('/data-formfieldidx="(\d+)"/',$html,$indices);
check($indices[1]===['0','1','2'],'custom field indices preserved');
foreach (['rolls'=>'bbTableRolls','d20'=>'bbTableD20','d20 dnd5e'=>'bbTableDnd5e','swffg compact'=>'bbTableCompact'] as $modifier=>$class) check(strpos(BBCode2Html("[table=$modifier]Name | 3[/table]"),$class)!==false,$modifier);
check(strpos(BBCode2Html('[table=notswffg]Name | 3[/table]'),'bbTableSwffg')===false,'exact modifier');
check(strpos(BBCode2Html('[code][table=swffg]Name | 3[/table][/code]'),'bbTableSwffg')===false,'code example inert');
