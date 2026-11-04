<?php
// Isolated integration fixture. No database, credentials, or forum writes.
if (PHP_SAPI !== 'cli-server') { http_response_code(404); exit; }
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
require __DIR__.'/../javascript/markItUp/markitup.bbcode-parser.php';
if (isset($_GET['roll'])) {
 require __DIR__.'/../includes/forums/_section.php';
 $_POST['count']=(int)($_POST['count'] ?? 0);
 require __DIR__.'/../forums/ajax/addRoll.php'; exit;
}
if (isset($_GET['sheet'])) {
 echo '<div><div id="charDetailsName">Fixture</div><input id="characterID" value="1">';
 if ($_GET['sheet']==='official') {
 echo '<div><div id="stats"><div class="stat_agi">3</div></div><div class="skill"><span class="skill_name">Official</span><span class="skill_stat">agi</span><span class="skill_rank">2</span></div></div>'; exit;
 }
 $bb="[_agility=3]\n[table=swffg]\nSkill | Characteristic | Rank\nCalculated | [_score$=agility] | [_rank=2]\nReverse | 2 | 3\nEqual | 3 | 3\nUntrained | 3 | 0\nZero | 0 | 0\nMaximum | 10 | 10\nBlank | | 2\nFraction | 2.5 | 2\nNegative | -1 | 2\nToo high | 11 | 2\nError | [_error$=missing] | 2\nCheckbox | [_checks=1/3] | 2\n[/table]\n[table=swffg]\nSkill | Characteristic | Rank\nSecond table | 4 | 1\n[/table]\n[table=rolls]\nAction | Roll\nBasic | 1d8+3\nFate | 4dF+2\n[/table]\n[table=d20 dnd5e]\nSkill | Modifier\nStealth | +3\n[/table]";
 if ($_GET['sheet']==='other') $bb="[table=swffg]\nSkill | Characteristic | Rank\nOther | 1 | 0\n[/table]";
 echo '<div><div id="charDetails"><div class="customChar ignoreSave">'.BBCode2Html($bb).'</div></div></div>';exit;
}
?><!doctype html><html lang="en"><meta charset="utf-8"><title>SWFFG composer integration checks</title>
<link rel="stylesheet" href="/styles/forums.css"><style>body{font:16px sans-serif;margin:24px}td{padding:6px}#rolls_decks{max-width:900px}.rollForChar{cursor:pointer;margin:12px;display:inline-block}#results{white-space:pre-wrap;background:#eef3ef;padding:12px}.customChar{border:1px solid #ccc;padding:10px}.formText{cursor:pointer}.rollWrapper{border:1px solid #ccc;margin:8px;padding:8px}</style>
<h1>SWFFG composer integration checks</h1><p>Actual parser, composer, custom-sheet calculations, and server roll-row template. Local fixture adapters replace account, sheet transport, and saves. No forum posts are created.</p><button id="run" type="button">Run checks</button><pre id="results">Not run</pre>
<div id="gameOptions">{"characterSheetIntegration":{"playerSheets":[{"Custom":"custom/custom"},{"Official":"starwarsffg/official"},{"Other":"custom/other"}]}}</div><div id="rolls_decks"><div id="rolls"></div><div id="newRolls"></div></div>
<script src="/javascript/jquery.min.js"></script><script>
var API_HOST='';
['prettySelect','prettyCheckbox','zoommap','darkModeColorize'].forEach(function(k){jQuery.fn[k]=function(){return this}});
var realGet=$.get,realPost=$.post;
$.get=function(url,callback){return realGet('swffg-composer.php?sheet='+url.split('/').pop(),callback)};
$.post=function(url,data,callback){return realPost('swffg-composer.php?roll=1',data,callback)};
</script><script src="/javascript/characters/custom/sheet.js"></script><script src="/javascript/forums/postingPage.js"></script>
<script>
$(function(){
 const out=$('#results');function check(ok,label){if(!ok)throw Error(label);out.append('PASS '+label+'\n')}
 function waitFor(test){return new Promise((resolve,reject)=>{let n=0;const t=setInterval(()=>{if(test()){clearInterval(t);resolve()}else if(++n>100){clearInterval(t);reject(Error('Timed out'))}},25)})}
 async function select(name){$('.rollForChar').filter(function(){return $(this).text()===name}).click();await waitFor(()=>$('#charSheetRoller .ffgSkill').length>0)}
 function row(name){return $('.ffgSkill').filter(function(){return $('.name',this).text()===name})}
 async function roll(control,expected,type='starwarsffg'){const count=$('#newRolls .newRoll').length;control.click();await waitFor(()=>$('#newRolls .newRoll').length===count+1);const last=$('#newRolls .newRoll').last();check(last.find('input[type=hidden]').val()===type,'roll type '+type);if(type==='starwarsffg'){check(last.find('.dicePool input').val()===expected,'serialized '+expected);check(last.find('.selectedDice .diceIcon').length===expected.split(',').length,'dice icons')}return last}
 $('#run').on('click',async function(){this.disabled=true;out.text('');try{
 await select('Custom');check($('.customFfgSkills').length===1,'single generated section');check($('.ffgSkill').length===7,'valid rows only, multiple tables');
 for(const [name,y,g] of [['Calculated',2,1],['Reverse',2,1],['Equal',3,0],['Untrained',0,3],['Zero',0,0],['Maximum',10,0],['Second table',1,3]]){check(row(name).find('i.y').length===y&&row(name).find('i.g').length===g,name+' pool')}
 check(row('Zero').find('td.swffgRoller').length===0,'zero base disabled');check(row('Zero').find('i.swffgRoller').length===5,'zero difficulty retained');
 await roll(row('Calculated').find('td.swffgRoller'),'p,p,a');
 for(let d=1;d<=5;d++)await roll(row('Calculated').find('i.p').eq(d-1),'p,p,a,'+Array(d).fill('d').join(','));
 await roll(row('Zero').find('i.p').eq(0),'d');
 let field=$('.customSheet [data-varname=agility]');field.click();field.find('input').val('4').trigger('blur');check(row('Calculated').find('i.g').length===2,'calculation refresh after field edit');check($('.customFfgSkills').length===1,'refresh has no duplicates');
 $('.customSheet').updateCalculations();check($('.customFfgSkills').length===1,'repeated calculations idempotent');await waitFor(()=>$.active===0);
 check($('.customSheet .rollDice').length>0&&$('.customSheet .rollFateDice').length===1,'basic and Fate shortcuts');
 await roll($('.customSheet .rollDice[roll="1d8+3"]'),'','basic');await roll($('.customSheet .rollFateDice'),'','fate');check($('.customSheet .rollDice[roll="2d20H1+3"]').length===1,'5e advantage');
 await select('Other');check($('.ffgSkill').length===1&&row('Other').length===1,'character switching replaces pools');
 await select('Official');check($('.ffgSkill').length===1,'official skill renderer');await roll(row('Official').find('i.p').eq(1),'p,p,a,d,d');
 await select('Custom');check($('.ffgSkill').length===7,'return to custom');out.append('COMPLETE: all integration checks passed\n');
 }catch(e){out.append('FAIL '+e.message+'\n');console.error(e)}finally{this.disabled=false}});
});
</script></html>
