$(function() {
	$('#messageTextArea').markItUp(mySettings);

	$('#optionControls a').click(function (e) {
		e.preventDefault();

		if (!$(this).hasClass('current')) {
			oldOpen = $('#optionControls .current').removeClass('current').attr('class');
			newOpen = $(this).attr('class');
			$(this).addClass('current');

			$('span.' + oldOpen + ', div.' + oldOpen).hide();
			$('span.' + newOpen + ', div.' + newOpen).show();

			$('#addRoll .fancyButton').each(function () { wingMargins($(this)[0]); });
		}
	});

	$newRolls = $('#newRolls');
	$addRoll_type = $('#addRoll select');
	rollCount = 0;
	$('div.newRoll').each(function() {
		name = $(this).find('input[type="hidden"]').attr('name');
		posCount = /rolls\[(\d+)\]/.exec(name);
		if (posCount[1] >= rollCount) rollCount = parseInt(posCount[1]) + 1;
	});
	$('#addRoll button').click(function (e) {
		e.preventDefault();

		$.post('/forums/ajax/addRoll/', { count: rollCount, type: $addRoll_type.val() }, function (data) {
			$newRow = $(data);
			$newRow.find('input[type="checkbox"]').prettyCheckbox();
			$newRow.find('select').prettySelect();
			$newRow.appendTo($newRolls);
			rollCount += 1;
		});
	});

	$('#newRolls').on('click', '.cross', function (e) {
		e.preventDefault();

		$(this).parent().remove();
	}).on('click', '.add', function (e) {
		e.stopPropagation();

		$(this).siblings('.diceOptions').toggle();
	}).on('click', '.diceOptions .diceIcon', function (e) {
		e.stopPropagation();

		$clickedDice = $(this);
		$input = $(this).closest('.dicePool').children('input');
		$clickedDice.closest('.dicePool').children('.selectedDice').append($clickedDice.clone());
		inputVal = $input.val().length?$input.val().split(','):[];
		inputVal[inputVal.length] = $clickedDice.attr('class').charAt(21);
		$input.val(inputVal.join());
	}).on('click', '.selectedDice .diceIcon', function (e) {
		e.stopPropagation();

		$selectedDice = $(this).parent();
		$(this).remove();
		var inputVal = [];
		$selectedDice.find('.diceIcon').each(function () {
			inputVal[inputVal.length] = $(this).attr('class').charAt(21);
		});
		$selectedDice.siblings('input').val(inputVal.join());
	});

	$('html').click(function () {
		$('div.diceOptions').hide();
	});


	//character sheet integration
	{
		var gameOptions = null;
		try {
			gameOptions = JSON.parse($('#gameOptions').html());
		} catch (e) { }
	
		//charactersheer integration enabled
		if (gameOptions && gameOptions.characterSheetIntegration) {

			//jquery helper selectors
			$.expr[':'].emptyContent =  $.expr[':'].emptyContent || $.expr.createPseudo(function() {
				return function( elem ) {
					return $.trim($(elem).text()).length==0;
				};
			}); 

			$.expr[':'].multipleDice =  $.expr[':'].multipleDice || $.expr.createPseudo(function() {
				return function( elem ) {
					return $('.rollDice',elem).length>1;
				};
			}); 
	
			var isGm = $('#fm_characters ul.submenu').hasClass('isGM');
	
			//setup the gui containers
			var charSection = $('<div><div id="charButtons"></div><div id="charSheetRoller" style="display:none;"></div></div>').insertAfter($('#rollExplination'));
			var charList = $('#charButtons', charSection);
			var charSheet = $('#charSheetRoller', charSection);
	
			//add the dice wizards for the characters
			var charactersToAdd;
			if (isGm) {
				charactersToAdd = $('#fm_characters .submenu>li:not(.thisUser) p.charName a');
			}
			else {
				charactersToAdd = $('#fm_characters .submenu>li.thisUser p.charName a');
			}
	
			charactersToAdd.each(function () {
				var addChar = $(this);
				var hrefParts = addChar.attr('href').split('/');
				$('<span class="rollForChar"></span>').text(addChar.text()).attr('charid', hrefParts[3]).attr('gamesys', hrefParts[2]).appendTo(charList);
			});
	
			//clicked on a character
			$('#rolls_decks').on('click', '.rollForChar', function () {
				var rollerForChar = $(this);
				if (rollerForChar.hasClass('sel')) {
					charSheet.hide();
				}
				else {
					$('#rolls_decks .rollForChar.sel').removeClass('sel');
					//load character bonuses
					charSheet.html('');
					var charId = $(this).attr('charid');
					var system = $(this).attr('gamesys');
					$.get('/characters/' + system + '/' + charId, function (data) {
						var charSheetContent = $(data);
	
						//features, spells and snippets
						var featDiv=$('<div class="roller feats"><select class="featSelect shortcutSelector addAsSpoiler"><option>--Feats/Abilities--</option></select></div>').appendTo(charSheet);
						$('.feat',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.feat_name',pThis).text());
							if(name.length>0){
								var notes=$.trim($('.notes',pThis).text());
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',featDiv));
							}
						});

						var spellDiv=$('<div class="roller feats"><select class="spellSelect shortcutSelector addAsSpoiler"><option>--Spells--</option></select></div>').appendTo(charSheet);
						$('.spell',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.spell_name',pThis).text());
							if(name.length>0){
								var notes=$.trim($('.spell_notes',pThis).text());
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',spellDiv));
							}

						});

						$('.abilities',charSheetContent).each(function(){
							var abilitySection=$(this);
							var title=$('h2',abilitySection).text();

							var abilityDiv=$('<div class="roller feats"><select class="abilitySelect shortcutSelector addAsSpoiler"><option></option></select></div>').appendTo(charSheet);
							$('option',abilityDiv).text('--'+title+'--');
							$('.ability',abilitySection).each(function(){
								var pThis=$(this);
								var name=$.trim($('.abilityName',pThis).text());
								if(name.length>0){
									var notes=$.trim($('.abilityNotes',pThis).text());
									$('<option></option>').text(name).data('notes',notes).appendTo($('select',abilityDiv));
								}
	
							});
	
						});

						var snippetDiv=$('<div class="roller snippets"><select class="snippetSelect shortcutSelector"><option>--Snippets--</option></select></div>').appendTo(charSheet);
						$('.spoiler.snippet',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.snippetName',pThis).text());
							var notes=$.trim($('.snippetBBCode',pThis).text());
							if(name.length>0 && notes.length>0){
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',snippetDiv));
							}

						});

						//remove unused roller dropdowns
						$('.roller select',charSheet).each(function(){
							var pThis=$(this);
							if($('option',pThis).length<=1){
								pThis.closest('.roller').remove();
							}
						});

						$('<hr class="clear"/>').appendTo(charSheet);

						if (system == 'dnd5') {
							addDnd5Rolls(charSheetContent);
						}

						//tables with rolls
						var rollsTable=$('table.bbTableRolls',charSheetContent);
						$('td',charSheetContent).each(function(){
							var td=$(this);
							var tdText=$.trim(td.text());
							if(/(\d*)[dD](\d+)([+-]\d+)?/g.test(tdText)) {
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var rollerSpan=$('<span class="rollDice"></span>').attr('roll',tdText).attr('rolltext',rollText).html(td.html());
								td.html(rollerSpan);
							}
						});

						//look for rows with multiple dice - they'll need the header too
						$('table.bbTableRolls tr:multipleDice td:has(.rollDice)',charSheetContent).each(function(){
							var td=$(this);
							var cellIndex=td.index();
							var tableHeadings=$('tr:first td',td.closest('table.bbTableRolls'));
							var rollDice=$('.rollDice',td);
							rollDice.attr('rolltext',rollDice.attr('rolltext')+' - '+$.trim(tableHeadings.eq(cellIndex).text()));
						});
						

						rollsTable.appendTo(charSheet);

						//multiple characters - prefix the rolls with the name
						if($('#charButtons .rollForChar').length>1){
							var charPrefix=rollerForChar.text()+': ';
							$('.rollDice',charSheet).each(function(){
								var rollDice=$(this);
								rollDice.attr('rolltext',charPrefix+rollDice.attr('rolltext'));
							});
						}

						
						
					});
	

					charSheet.show();
				}
				rollerForChar.toggleClass('sel');
			});
	
			var prefixSign=function(str) {
				var val=parseInt(str);
				if(isNaN(val)){
					return str;
				}
				else if(val>=0){
					return '+'+val;
				} else{
					return ''+val;
				}
			}
	
			//special code for dnd 5e
			var addDnd5Rolls = function (charSheetContent) {
				{
					var roller = $('<div class="roller rollerInit"><span></span><ul class="rollSel"><li><small></small></li><li class="adv">A</li><li class="dis">D</li></ul></roller>').appendTo(charSheet);
					var initiative = prefixSign($.trim($('div', $('#stats .tr label:contains(Initiative)', charSheetContent).closest('.tr')).text()));
	
					$('span', roller).text('Initiative');
					$('ul li', roller).addClass('rollDice').attr('roll','1d20'+initiative).attr('rolltext','Initiative');
					$('small', roller).text('Initiative ' + initiative);
				}
	
				$('<h3>Abilities</h3>').appendTo(charSheet);
				$('.abilityScore', charSheetContent).each(function () {
					var abilityScore = $(this);
					var label = $.trim($('.shortText', abilityScore).text());
					var check = $.trim($('.stat_mod', abilityScore).text());
					var save = $.trim($('.saveProficient', abilityScore).text());
					var roller = $('<div class="roller"><span></span><ul class="check rollSel"><li>Check <small></small></li><li class="adv">A</li><li class="dis">D</li></ul><ul class="save rollSel"><li>Save <small></small></li><li class="adv">A</li><li class="dis">D</li></ul></roller>').appendTo(charSheet);
					$('span', roller).text(label);
					$('ul.check li', roller).addClass('rollDice').attr('roll','1d20'+prefixSign(check)).attr('rolltext',label+' check');
					$('ul.save li', roller).addClass('rollDice').attr('roll', '1d20'+prefixSign(save)).attr('rolltext',label+' save');
					$('.check small', roller).text(check);
					$('.save small', roller).text(save);
				});
	
				$('<h3>Weapons</h3>').appendTo(charSheet);
				$('.weapon', charSheetContent).each(function () {
					var weapon = $(this);
					var label = $.trim($('.weapon_name', weapon).text());
					var toHit = $.trim($('.weapons_ab', weapon).text());
					var dmg = $.trim($('.weapon_damage', weapon).text());
					var roller = $('<div class="roller"><span></span><ul class="rollSel attack"><li>To hit: <small></small></li><li class="adv">A</li><li class="dis">D</li></ul></roller> <ul class="rollSel dmg"><li>Dmg: <small></small></li></ul>').appendTo(charSheet);
					$('span', roller).text(label);
					$('ul.attack li', roller).addClass('rollDice').attr('roll','1d20'+prefixSign(toHit)).attr('rolltext',label+' to hit');
					$('ul.dmg li', roller).addClass('rollDice').attr('roll', dmg).attr('rolltext', label+' damage');
					$('ul.attack small', roller).text(toHit);
					$('ul.dmg small', roller).text(dmg);
				});
	
				$('<h3>Skills</h3>').appendTo(charSheet);
				$('.skill', charSheetContent).each(function () {
					var skill = $(this);
					var label = $.trim($('.skill_name', skill).text());
					var bonus = $.trim($('.skill_stat', skill).text());
					//extract the number from +1 (wis)
					bonus = bonus.match(/[^\d\-\+]*([\-\+]\d+)[^\d\-\+]*/)[1];
	
					//if the number is hiding in the skill name (e.g. Medicine + 8) then use that
					var labelBonus = label.match(/[^\d\-\+]*([\-\+]\d+)[^\d\-\+]*/);
					if (labelBonus) {
						bonus = labelBonus[1];
					}
	
					{
						var roller = $('<div class="roller skill"><span></span><ul class="rollSel"><li><small></small></li><li class="adv">A</li><li class="dis">D</li></ul></roller>').appendTo(charSheet);
						$('span', roller).text(label);
						$('ul li', roller).addClass('rollDice').attr('roll','1d20'+prefixSign(bonus)).attr('rolltext',label);
						$('ul small', roller).text(bonus);
					}
				});

			};
	
	
			var addRollToList = function (reason, roll) {

				$.post('/forums/ajax/addRoll/', { count: rollCount, type: 'basic' }, function (data) {
					$newRow = $(data);
					$newRow.find('input[type="checkbox"]').prettyCheckbox();
					$newRow.find('select').prettySelect();
					$newRow.find('.reason input').val(reason);
					$newRow.find('.roll input').val(roll);
					$newRow.appendTo($newRolls);
					rollCount += 1;
				});				
			};
	
			//clicking a roll
			$('#rolls_decks').on('click', '.rollDice', function () {
				var thisRoll = $(this);
				var roll = thisRoll.attr('roll');
				var reason=thisRoll.attr('rolltext');
	
				if (thisRoll.hasClass('adv')) {
					reason += ' (advantage)';
					roll = roll + ',' + roll;
				}
				if (thisRoll.hasClass('dis')) {
					reason += ' (disadvantage)';
					roll = roll + ',' + roll;
				}

				addRollToList(reason, roll);
	
			});

			$('#rolls_decks').on('change', '.shortcutSelector', function (ev) {
				var pThis=$(this);
				var text=pThis.val();
				var selectedOption=pThis.find(":selected");
				var notes=selectedOption.data('notes');
				$('#messageTextArea').focus();
				if(pThis.hasClass('addAsSpoiler')){
					$.markItUp({ replaceWith: '[spoiler="'+text+'"]'+notes+'[/spoiler]' });
				}
				else{
					$.markItUp({ replaceWith: notes });
				}

				$('option:first',pThis).prop("selected", true);
	
			});
			
		}
	}	

});
