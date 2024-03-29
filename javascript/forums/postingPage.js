$(function() {

	var gameOptions = null;
	try {
		gameOptions = JSON.parse($('#gameOptions').html());
	} catch (e) { }

	$('#backfill, #forwardfill').on('click',function(){
		var pThis=$(this);
		var loadLocation=pThis.data('loadpage');
		var isForwardFill=pThis.hasClass('forwardfill');

		var startScroll = $(window).scrollTop();
		var startHeight=$(document ).height()

		$.get(loadLocation, function (data) {
			var lastPageData=$(data);
			var block=$('.postBlock:not(.postPreview)', lastPageData).clone();
			var addedBlock=isForwardFill?(block.insertBefore(pThis)):(block.insertAfter(pThis));
			addedBlock.addClass('postBlockFound').darkModeColorize().zoommap().convertTimeZones();
			if($.isFunction($.fn.applyDiceRules)){
				addedBlock.applyDiceRules();
			}

			if(!isForwardFill) {
				$("img",addedBlock).on("load", function() {
					$(this).removeClass('imageNotLoaded');
					$(window).scrollTop(startScroll+($(document).height()-startHeight));
				});
				$(window).scrollTop(startScroll+($(document).height()-startHeight));
			}

			var lastPageBackFill=isForwardFill?$('#forwardfill',lastPageData):$('#backfill',lastPageData);
			var beforeIndex=loadLocation.indexOf('?b');
			if(beforeIndex==-1){
				if(lastPageBackFill.length){
					pThis.data('loadpage',lastPageBackFill.data('loadpage'));
				} else {
					pThis.remove();
				}
			} else {
				if(block.length==0){
					pThis.remove();
				} else {
					pThis.data('loadpage',loadLocation.substring(0,beforeIndex)+'?b='+block.data('postid'));
				}
			}

			if(isForwardFill){
				$('.paginateDiv').html($('.paginateDiv:first',lastPageData).html());
			}
		});
	});

	$('body').on('click','.quotePost',function(){

		var pThis=$(this);
		var postId=pThis.data('postid');
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/getPostQuote',
			xhrFields: {
				withCredentials: true
			},
			data:{ postID: postId},
			success:function (data) {
				$('#messageTextArea').focus();
				$.markItUp({ replaceWith: data });
				$("#messageTextArea")[0].scrollIntoView();
			}
		});
	});

	$('body').on('click','div.note > div:first-child',function(){
		var pThis=$(this);
		var commaSepList=$.trim($('span',pThis).text());
		var sender=$('.posterDetails .username',pThis.closest('.postBlock')).text().toLowerCase();
		var toArray = commaSepList.split(',').map(function(element) {return element.trim().toLowerCase();});
		if(sender && !toArray.includes(sender)){
			if(commaSepList.length){
				commaSepList+=',';
			}
			commaSepList+=sender;
		}
		$('textarea.markItUpEditor').focus();
		$.markItUp({ openWith:'[note="'+commaSepList+'"]', closeWith:'[/note]'});
	});

	$('body').on('click','.createSheetButton',function(){
		var snippetIdx=$(this).closest('.charsheet').data('charsheet');
		var postId=$(this).closest('.postBlock').data('postid');
		var name=prompt("What is your character's name?");
		if(postId && name){
			$(this).remove();
			$.ajax( { type: 'post', url: API_HOST +'/characters/createFromSnippet', xhrFields: { withCredentials: true},
				data:{ postID: postId, snippetIdx:snippetIdx, name:name },
				success:function (data) {
					if(data && data.success){
						window.location.href='/characters/custom/'+data.characterID+'/';
					}
				}
			});
		}
	});

	var characterSheetIntegration={gmExcludePcs:false,gmExcludeNpcs:false};
	if (gameOptions && gameOptions.characterSheetIntegration){
		$.extend(characterSheetIntegration,gameOptions.characterSheetIntegration);
	}

    var updateCharLink=function(postAs){
        var charPost=$('#fm_characters .charid-'+postAs.val());
        var charSheetLink=$('#charSheetLink').html('');
        if(charPost.length>0){
            charPost=charPost.eq(0);
            $('<a target="_blank"></a>').text(charPost.text()).attr('href',charPost.attr('href')).appendTo(charSheetLink);
        }
    };

    $('select[name="postAs"]').on('change',function(){
        updateCharLink($(this));
    });

    updateCharLink($('select[name="postAs"]'));

	$('#previewPost').click(function(){
		$('.postPreview .post').html('<div class="previewing">Getting preview</div>');
		$('.postPreview').show();
		var selOpt=$('select[name="postAs"] option:selected');
		$.ajax({
			type: 'post',
			url: API_HOST +'/forums/getPostPreview',
			xhrFields: {
				withCredentials: true
			},
			data:{ postText: $('#messageTextArea').val(), postAsId: selOpt.val(), postAsName: selOpt.text()},
			success:function (data) {
				$('.postPreview .post').html(data.post).darkModeColorize().zoommap();

				var title=$('input#title');
				if(title.length){
					$('.postPreview .postHeader .subject').text(title.val());
				}

				if(data.avatar){
					$('img',$('.postPreview .posterDetails .avatar').show()).attr('src',data.avatar);
				}
				else{
					$('.postPreview .posterDetails .avatar').hide();
				}

				if(data.npcPoster){
					$('.postPreview').addClass('withSingleNpc');
				}else{
					$('.postPreview').removeClass('withSingleNpc');
				}

				$('.postPreview .charName').text(data.name);
				$(".postPreview")[0].scrollIntoView();
				var startScroll = $(window).scrollTop()-100;
				if(startScroll>0){
					$(window).scrollTop(startScroll);
				}
			}
		});
	});

    $('#fm_characters .ra-quill-ink').css({visibility:'visible'}).on('click',function(){
        var text=$('a',$(this).closest('p')).text();
        $('#messageTextArea').focus();
        $.markItUp({ replaceWith: text });
    });

	$('#optionControls a').click(function (e) {
		e.preventDefault();

		if (!$(this).hasClass('current')) {
			oldOpen = $('#optionControls .current').removeClass('current').attr('class');
			newOpen = $(this).attr('class');
			$(this).addClass('current');

			$('span.' + oldOpen + ', div.' + oldOpen).hide();
			$('span.' + newOpen + ', div.' + newOpen).show();

			$('#addRoll .fancyButton').each(adjustSkewMargins);
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

		rollCount += 1;
		$.post('/forums/ajax/addRoll/', { count: rollCount, type: $addRoll_type.val() }, function (data) {
			$newRow = $(data);

			if(gameOptions && gameOptions.diceDefaults && gameOptions.diceDefaults.rerollAces){
				$newRow.find('.reroll input[type="checkbox"]').prop('checked',true)	;
			}

			$newRow.find('input[type="checkbox"]').prettyCheckbox();
			$newRow.find('select').prettySelect();
			$newRow.appendTo($newRolls);
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
		if($(this).hasClass('starwarsffg_difficulty')){
			$(this).removeClass('starwarsffg_difficulty').addClass('starwarsffg_challenge').attr('title','Challenge');
		}
		else if($(this).hasClass('starwarsffg_ability')){
			$(this).removeClass('starwarsffg_ability').addClass('starwarsffg_proficiency').attr('title','Proficiency');
		}
		else{
			$(this).remove();
		}

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
		//charactersheer integration enabled
		if (characterSheetIntegration) {

			//jquery helper selectors
			$.expr[':'].emptyContent =  $.expr[':'].emptyContent || $.expr.createPseudo(function() {
				return function( elem ) {
					return $.trim($(elem).text()).length==0;
				};
			});

			$.expr[':'].multipleDice =  $.expr[':'].multipleDice || $.expr.createPseudo(function() {
				return function( elem ) {
					return $('td:has(.rollDice)',elem).length>1;
				};
			});

			var isGm = $('#fm_characters ul.submenu').hasClass('isGM');

			//setup the gui containers
			var charSection = $('<div><div id="charButtons"></div><div id="charSheetRoller" style="display:none;"></div></div>').insertAfter($('#rolls'));
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

			if(!isGm || !characterSheetIntegration.gmExcludePcs){
				charactersToAdd.each(function () {
					var addChar = $(this);
					var hrefParts = addChar.attr('href').split('/');
					$('<span class="rollForChar"></span>').text(addChar.text()).attr('charid', hrefParts[3]).attr('gamesys', hrefParts[2]).appendTo(charList);
				});
			}

			if(isGm && !characterSheetIntegration.gmExcludeNpcs){
				$('#fm_characters .submenu>li.thisUser p.charName a').each(function () {
					var addChar = $(this);
					var hrefParts = addChar.attr('href').split('/');
					$('<span class="rollForChar gmSheet"></span>').text(addChar.text()).attr('charid', hrefParts[3]).attr('gamesys', hrefParts[2]).appendTo(charList);
				});
			}

			if(isGm && characterSheetIntegration.gmSheets && Array.isArray(characterSheetIntegration.gmSheets)){

				for(var i=0;i<characterSheetIntegration.gmSheets.length;i++){
					var char=characterSheetIntegration.gmSheets[i];
					var keys=Object.keys(char);
					if(keys.length>0){
						var hrefParts = char[keys[0]].split('/');
						$('<span class="rollForChar gmSheet"></span>').text(keys[0]).attr('charid', hrefParts[1]).attr('gamesys', hrefParts[0]).appendTo(charList);
					}
				}
			}

			if(!isGm && characterSheetIntegration.playerSheets && Array.isArray(characterSheetIntegration.playerSheets)){

				for(var i=0;i<characterSheetIntegration.playerSheets.length;i++){
					var char=characterSheetIntegration.playerSheets[i];
					var keys=Object.keys(char);
					if(keys.length>0){
						var hrefParts = char[keys[0]].split('/');
						$('<span class="rollForChar"></span>').text(keys[0]).attr('charid', hrefParts[1]).attr('gamesys', hrefParts[0]).appendTo(charList);
					}
				}
			}

			var replaceForumulae=function(text,htmlEle){
				var valElements=$('.formVal',htmlEle);

				var i=0;
				text = text.replace(/\[\_(([\w\_\$]*)\=)?([^\]]*)\]/g, function(match, contents){
					return valElements.eq(i++).text();
				});

				return text;
			};

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
					var charUrl='/characters/' + system + '/' + charId;
					$.get(charUrl, function (data) {
						var charSheetContent = $(data);

						var charName=$('#charDetailsName',charSheetContent).text();
						$('<a target="_blank"><i class="ra ra-scroll-unfurled"></i></a>').attr('href',charUrl).prependTo(($('<h3 class="charName"></h3>').text(charName).appendTo(charSheet)));

						var topRollers=$('<div class="topRollers"></div>').appendTo(charSheet);

						if (system == 'dnd5') {
							addDnd5Rolls(charSheetContent);
						} else if(system=='savageworlds'){
							addSavageWorldRolls(charSheetContent);
						} else if (system == 'starwarsffg') {
							addStarwarsFFGRolls(charSheetContent);
						} else if (system == 'custom') {
							addCustomSheet(charSheetContent);
							$('#charDetails',charSheetContent).updateCalculations();
						}

						//features, spells and snippets
						var featDiv=$('<div class="roller feats"><select class="featSelect shortcutSelector addAsSpoiler"><option>--Feats/Abilities--</option></select></div>').appendTo(topRollers);
						$('.feat',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.feat_name',pThis).text());
							var notes=$('.notes',pThis);
							if(name.length>0 && notes.length>0){
								var notes=$.trim(notes.html().replace(/(?:\r\n|\r|\n)/g, '').replace(/<br\s*[\/]?>/gi, '\n'));
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',featDiv));
							}
						});

						var spellDiv=$('<div class="roller feats"><select class="spellSelect shortcutSelector addAsSpoiler"><option>--Spells--</option></select></div>').appendTo(topRollers);
						$('.spell',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.spell_name',pThis).text());
							var notes=$('.spell_notes',pThis);
							if(name.length>0 && notes.length>0){
								var notes=$.trim(notes.html().replace(/(?:\r\n|\r|\n)/g, '').replace(/<br\s*[\/]?>/gi, '\n'));
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',spellDiv));
							}
						});

						$('.abilities',charSheetContent).each(function(){
							var abilitySection=$(this);
							var title=$('h2',abilitySection).text();

							var abilityDiv=$('<div class="roller feats"><select class="abilitySelect shortcutSelector addAsSpoiler"><option></option></select></div>').appendTo(topRollers);
							$('option',abilityDiv).text('--'+title+'--');
							$('.ability',abilitySection).each(function(){
								var pThis=$(this);
								var name=$.trim($('.abilityName',pThis).text());
								if(name.length>0){
									var notes=$.trim($('.abilityBBCode',pThis).text());
									notes=replaceForumulae(notes,$('.notes',pThis));
									$('<option></option>').text(name).data('notes',notes).appendTo($('select',abilityDiv));
								}
							});
						});

						$('.snippets',charSheetContent).each(function(){
							var abilitySection=$(this);
							var title=$('h2',abilitySection).text();

							var abilityDiv=$('<div class="roller feats"><select class="abilitySelect shortcutSelector"><option></option></select></div>').appendTo(topRollers);
							$('option',abilityDiv).text('--'+title+'--');
							$('.ability',abilitySection).each(function(){
								var pThis=$(this);
								var name=$.trim($('.abilityName',pThis).text());
								if(name.length>0){
									var notes=$.trim($('.abilityBBCode',pThis).text());
									notes=replaceForumulae(notes,$('.notes',pThis));
									$('<option></option>').text(name).data('notes',notes).appendTo($('select',abilityDiv));
								}
							});
						});

						$('.npcs',charSheetContent).each(function(){
							var npcSection=$(this);
							var title=$('h3',npcSection).text();

							var npcDiv=$('<div class="roller npcs"><select class="abilitySelect shortcutSelector shortcutSelector"><option></option></select></div>').appendTo(topRollers);
							$('option',npcDiv).text('--'+title+'--');
							$('.npcList_item',npcSection).each(function(){
								var pThis=$(this);
								var name=$.trim($('.npcList_itemName',pThis).text());
								if(name.length>0){
									var notes=$.trim($('.npcList_itemAvatar',pThis).data('avatar'));
									var bbCode='[npc="'+name+'"]'+notes+'[/npc]';
									$('<option></option>').text(name).data('notes',bbCode).appendTo($('select',npcDiv));
								}
							});
						});

						var snippetDiv=$('<div class="roller snippets"><select class="snippetSelect shortcutSelector"><option>--Snippets--</option></select></div>').appendTo(topRollers);
						$('.spoiler.snippet',charSheetContent).each(function(){
							var pThis=$(this);
							var name=$.trim($('.snippetName',pThis).text());
							var notes=$.trim($('.snippetBBCode',pThis).text());
							if(name.length>0 && notes.length>0){
								notes=replaceForumulae(notes,$('.hidden',pThis));
								$('<option></option>').text(name).data('notes',notes).appendTo($('select',snippetDiv));
							}
						});

						//remove unused roller dropdowns
						$('.roller select',topRollers).each(function(){
							var pThis=$(this);
							if($('option',pThis).length<=1){
								pThis.closest('.roller').remove();
							}
						});

						if($('.roller select',topRollers).length>0){
							$('<hr class="clear"/>').appendTo(topRollers);
						}

						if (system != 'custom'){
							//tables with rolls
							var rollsTable=$('table.bbTableRolls',charSheetContent);
							rollsTable.appendTo(charSheet);
						}

						$('table.bbTableRolls td',charSheet).each(function(){
							var td=$(this);
							var tdText=$.trim(td.text());
							if(/(\d*)[dD](\d+)([+-]\d+)?/g.test(tdText)) {
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var rollerSpan=$('<span class="rollDice"></span>').attr('roll',tdText).attr('rolltext',rollText).html(td.html());
								td.html(rollerSpan);
							}
							if(/([\d]+)[dD][fF]([+-]\d+)?/.test(tdText)) {
								var matches=tdText.match(/([\d]+)[dD][fF]([+-]\d+)?/);
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var rollerSpan=$('<span class="rollFateDice"></span>').attr('rolldice',matches[1]).attr('rollmodifier',matches[2]?matches[2]:0).attr('rolltext',rollText).html(td.html());
								td.html(rollerSpan);
							}
						});

						/*
						$('table.bbTableRolls.bbSwRolls td',charSheet).each(function(){
							var td=$(this);
							var tdText=$.trim(td.text());
							if(/(^|\s+)((\d*)([apbdcsfAPBDCSF]))+(\s+|$)/gm.test(tdText)) {
								var matches=tdText.match(/([\d]+)[dD][fF]([+-]\d+)?/);
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var rollerSpan=$('<span class="rollFateDice"></span>').attr('rolldice',matches[1]).attr('rollmodifier',matches[2]?matches[2]:0).attr('rolltext',rollText).html(td.html());
								td.html(rollerSpan);
							}
						});*/

						$('table.bbTableD20:not(.bbTableDnd5e) td',charSheet).each(function(){
							var td=$(this);
							var tdText=$.trim(td.text());
							if(/^[\+\-](\d)+$/.test(tdText)) {
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var rollerSpan=$('<span class="rollDice rollDiceD20"></span>').attr('rollmod',tdText).attr('roll','1d20'+tdText).attr('rolltext',rollText).html(td.html());
								td.html(rollerSpan);
							}
						});

						$('table.bbTableD20.bbTableDnd5e td',charSheet).each(function(){
							var td=$(this);
							var tdText=$.trim(td.text());
							if(/^[\+\-](\d)+$/.test(tdText)) {
								var rollText=$.trim($('td:not(:emptyContent):first',td.closest('tr')).text());
								var advDis=$('<span class="rolld20-5e"></span>');
								$('<span class="rollDice rollDiceD20"></span>').attr('roll','1d20'+tdText).attr('rolltext',rollText).html(td.html()).appendTo(advDis);
								$('<span class="rollDice rollDiceD20">A</span>').attr('roll','2d20H1'+tdText).attr('rolltext',rollText+' (advantage)').appendTo(advDis);
								$('<span class="rollDice rollDiceD20">D</span>').attr('roll','2d20L1'+tdText).attr('rolltext',rollText+' (disadvantage)').appendTo(advDis);
								advDis.appendTo(td.html(''));
							}

						});

						//look for rows with multiple dice - they'll need the header too
						$('table.bbTableRolls tr:multipleDice td:has(.rollDice,.rollFateDice)',charSheet).each(function(){
							var td=$(this);
							var cellIndex=td.index();
							var tableHeadings=$('tr:first td',td.closest('table.bbTableRolls'));
							var rollDice=$('.rollDice,.rollFateDice',td);
							rollDice.each(function(){
								$(this).attr('rolltext',$(this).attr('rolltext')+' - '+$.trim(tableHeadings.eq(cellIndex).text()));
							});
						});

						//multiple characters - prefix the rolls with the name
						if($('#charButtons .rollForChar').length>1){
							var charPrefix=$('.rollForChar.sel').hasClass('gmSheet')?'':(charName+': ');

							$('.rollDice,.rollFateDice',charSheet).each(function(){
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

			var addCustomSheet=function(charSheetContent){
				var customSheet=$('<div class="customSheet customChar"></div>').appendTo(charSheet);
				customSheet.html($('#charDetails div.customChar',charSheetContent).html()).zoommap().darkModeColorize();
				$('<input id="characterID" type="hidden" value=""></input>').val($('#characterID',charSheetContent).val()).appendTo(customSheet);
				customSheet.updateCalculations();
				customSheet.on('gp.sheetUpdated',function(){$('#rolls_decks .rollForChar.sel').removeClass('sel').click();});
			};

			var liAdvDis=function(lis,bonus,text) {
				lis.each(function(){
					var pLi=$(this);
					pLi.addClass('rollDice').attr('rolltext',text);
					if(pLi.hasClass('adv')){
						pLi.attr('roll','2d20H1'+prefixSign(bonus));
					}
					else if(pLi.hasClass('dis')){
						pLi.attr('roll','2d20L1'+prefixSign(bonus));
					}
					else {
						pLi.attr('roll','1d20'+prefixSign(bonus));
					}
				});
			};

			var addSavageWorldRolls=function(charSheetContent){
				var swRoller=$('<div class="savageWorldsRoller"></div>').appendTo(charSheet);
				$('.traitDiv', charSheetContent).each(function(){
					var trait=$(this);
					var traitDiv=$('<div class="savageWorldsTrait"><table></table></div>').appendTo(swRoller);
					var traitTable=$('table',traitDiv);
					var traitName=$('.traitName',trait).text();
					var dieSelect='1'+$('.diceSelect',trait).text()+',1d6';
					var traitRoller=$('<tr class="traitRow"></tr>').addClass('rollDice').attr('roll',dieSelect).attr('rolltext',traitName).attr('rerollAces','true').appendTo(traitTable);
					$('<td></td>').text(traitName).appendTo(traitRoller);
					$('<td></td>').text(dieSelect).appendTo(traitRoller);
					$('.skill',trait).each(function(){
						var skill=$(this);
						var skillName=$('.skillName',skill).text();
						var skillDieSelect='1'+$('.diceType',skill).text()+',1d6';
						var traitSkillRoller=$('<tr></tr>').addClass('rollDice').attr('roll',skillDieSelect).attr('rolltext',skillName).attr('rerollAces','true').appendTo(traitTable);
						$('<td></td>').text(skillName).appendTo(traitSkillRoller);
						$('<td></td>').text(skillDieSelect).appendTo(traitSkillRoller);
					});
				});

				var unskilledDiv=$('<div class="savageWorldsTrait"><table></table></div>').appendTo(swRoller);
				var unskilledRoller=$('<tr class="traitRow"></tr>').addClass('rollDice').attr('roll','1d4-2,1d6-2').attr('rolltext','Unskilled').attr('rerollAces','true').appendTo($('table',unskilledDiv));
				$('<td>Unskilled</td>').appendTo(unskilledRoller);
				$('<td>1d4-2,1d6-2</td>').appendTo(unskilledRoller);
			};

			//special code for Star Wars FFG
			var addStarwarsFFGRolls=function(charSheetContent){
				var talenDiv=$('<div class="roller"><select class="shortcutSelector addAsSpoiler"><option>--Talents--</option></select></div>').appendTo(charSheet);
				$('.talent',charSheetContent).each(function(){
					var pThis=$(this);
					var name=$.trim($('.talent_name',pThis).text());
					var notes=$('.talent_notes',pThis);
					if(name.length>0 && notes.length>0){
						var notes=$.trim(notes.html().replace(/(?:\r\n|\r|\n)/g, '').replace(/<br\s*[\/]?>/gi, '\n'));
						$('<option></option>').text(name).data('notes',notes).appendTo($('select',talenDiv));
					}
				});

				$('<h3>Skills</h3>').appendTo(charSheet);
				var skills=$('<table class="ffgSkills"></table>').appendTo(charSheet);
				$('.skill', charSheetContent).each(function () {
					var skill = $(this);
					var label = $.trim($('.skill_name', skill).text());
					var ability = $.trim($('.skill_stat', skill).text());
					var rank = $.trim($('.skill_rank', skill).text());
					var abilityScore=$.trim($('#stats .stat_'+ability.toLowerCase(), charSheetContent).text());
					var yellow=Math.min(rank,abilityScore);
					var green=Math.max(rank,abilityScore)-yellow;

					{
						var roller = $('<tr class="ffgSkill"><td><span class="name"></span></td><td class="swffgRoller"><span class="ability"></span></td><td><i class="p">1</i><i class="p">2</i><i class="p">3</i><i class="p">4</i><i class="p">5</i></td></tr>').appendTo(skills);
						$('span.name', roller).text(label);
						$('.swffgRoller,i.p',roller).data('y',yellow);
						$('.swffgRoller,i.p',roller).data('g',green);
						$('.swffgRoller',roller).data('p',0);
						$('i.p',roller).each(function(){$(this).addClass('swffgRoller').data('p',$(this).text());});
						$('span.ability', roller).html('<i class="y">y</i>'.repeat(yellow)+'<i class="g">g</i>'.repeat(green));
					}
				});
			};

			//special code for dnd 5e
			var addDnd5Rolls = function (charSheetContent) {
				{
					var roller = $('<div class="roller rollerInit"><span></span><ul class="rollSel"><li><small></small></li><li class="adv">A</li><li class="dis">D</li></ul></roller>').appendTo(charSheet);
					var initiative = prefixSign($.trim($('div', $('#stats .tr label:contains(Initiative)', charSheetContent).closest('.tr')).text()));

					$('span', roller).text('Initiative');
					liAdvDis($('ul li', roller),initiative,'Initiative');
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
					liAdvDis($('ul.check li', roller),check,label+' check');
					liAdvDis($('ul.save li', roller),save,label+' save');
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
					liAdvDis($('ul.attack li', roller),toHit,label+' to hit');
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
						liAdvDis($('ul li', roller),bonus,label);
						$('ul small', roller).text(bonus);
					}
				});

			};


			var addRollToList = function (reason, roll, rerollAces) {
				rollCount += 1;
				$.post('/forums/ajax/addRoll/', { count: rollCount, type: 'basic' }, function (data) {
					$newRow = $(data);
					if( rerollAces || (gameOptions && gameOptions.diceDefaults && gameOptions.diceDefaults.rerollAces)){
						$newRow.find('.reroll input[type="checkbox"]').prop('checked',true)	;
					}
					$newRow.find('input[type="checkbox"]').prettyCheckbox();
					$newRow.find('select').prettySelect();
					$newRow.find('.reason input').val(reason);
					$newRow.find('.roll input').val(roll);
					$newRow.appendTo($newRolls);
				});
			};

			var addFateRollToList = function (reason, rolldice, rollmodifier){
				rollCount += 1;
				$.post('/forums/ajax/addRoll/', { count: rollCount, type: 'fate' }, function (data) {
					$newRow = $(data);
					$newRow.find('select').prettySelect();
					$newRow.find('.reason input').val(reason);
					$newRow.find('.roll input').val(rolldice);
					$newRow.find('.modifier input').val(rollmodifier);
					$newRow.appendTo($newRolls);
				});
			};

			var addStarwarsFFGRollToList = function (reason, y, g, p) {

				rollCount += 1;
				$.post('/forums/ajax/addRoll/', { count: rollCount, type: 'starwarsffg' }, function (data) {
					$newRow = $(data);
					$newRow.find('select').prettySelect();
					var ytext='<div class="diceIcon starwarsffg_proficiency" title="Proficiency"></div>'.repeat(y);
					var gtext='<div class="diceIcon starwarsffg_ability" title="Ability"></div>'.repeat(g);
					var ptext='<div class="diceIcon starwarsffg_difficulty" title="Difficulty"></div>'.repeat(p);
					var items=[];
					items=items.concat(new Array(y).fill('p'));
					items=items.concat(new Array(g).fill('a'));
					items=items.concat(new Array(p).fill('d'));
					$newRow.find('.selectedDice').html(ytext+gtext+ptext);
					$newRow.find('.reason input').val(reason);
					$newRow.find('.dicePool input').val(items.join(','));
					$newRow.appendTo($newRolls);
				});
			};

			//clicking a roll
			$('#rolls_decks').on('click', '.rollDice', function () {
				var thisRoll = $(this);
				var roll = thisRoll.attr('roll');
				var reason=thisRoll.attr('rolltext');
				var rerollAces=thisRoll.attr('rerollAces')=='true';

				if (thisRoll.hasClass('adv')) {
					reason += ' (advantage)';
				}
				if (thisRoll.hasClass('dis')) {
					reason += ' (disadvantage)';
				}

				//add to dice pool
				var addedRollToPool=false;
				var tablePool=thisRoll.closest('.bbTablePool');
				if(tablePool.length>0){
					var lastRoll=$('#newRolls .basicRoll').last();
					if(lastRoll.length>0){
						var reasonInput=$('.reason input',lastRoll);
						var rollInput=$('.roll input',lastRoll);
						var suffix=", ";
						if(tablePool.hasClass('bbTablePoolAdd')){
							suffix=' + ';
						}
						var curReasonVal=reasonInput.val();
						var curRollVal=rollInput.val();
						//check it fits
						if((curReasonVal.length+reason.length) < 96 && (curRollVal.length+reason.length) < 46){
							reasonInput.val(curReasonVal+(curReasonVal.length?suffix:'')+reason);
							rollInput.val(curRollVal+(curRollVal.length?suffix:'')+roll);
							addedRollToPool=true;
						}
					}
				}

				if(!addedRollToPool){
					addRollToList(reason, roll, rerollAces);
				}
			});

			$('#rolls_decks').on('click', '.rollFateDice', function () {
				var thisRoll = $(this);
				var reason=thisRoll.attr('rolltext');
				var rolldice = thisRoll.attr('rolldice');
				var rollmodifier = thisRoll.attr('rollmodifier');

				addFateRollToList(reason, rolldice, rollmodifier);
			});


			$('#rolls_decks').on('click', '.swffgRoller', function () {
				var thisRoll = $(this);
				var reason=$('.name',thisRoll.closest('tr')).text();
				var y=parseInt(thisRoll.data('y'));
				var g=parseInt(thisRoll.data('g'));
				var p=parseInt(thisRoll.data('p'));

				addStarwarsFFGRollToList(reason, y, g, p);
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

			$('#charSheetRoller').on('click','.npcList_item',function(){
				$('#messageTextArea').focus();
				$.markItUp({ replaceWith: '[npc='+$.trim($('.npcList_itemName',this).text())+']'+$.trim($('img',this).data('avatar'))+"[/npc]" });
			});
		}
	}
});
