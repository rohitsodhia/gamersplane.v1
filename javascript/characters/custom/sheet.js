$(function () {

    //helpers for text area formatting
    jQuery.fn.surroundWithTags = function (openTag,closeTag){
        var pThis=$(this);
        var start = pThis[0].selectionStart;
        var end = pThis[0].selectionEnd;
        var curSel = pThis.val().substring(start, end);
        var block = openTag + curSel + closeTag;
        document.execCommand("insertText", false, block);
        return this;
    };

    $('body').on('keydown','.abilities textarea,.formBlock textarea',function(e){
        if (e.ctrlKey) {
            var handled=false;
            if (e.key === 'b'){$(this).surroundWithTags('[b]','[/b]');handled=true;} else
            if (e.key === 'i'){$(this).surroundWithTags('[i]','[/i]');handled=true;} else
            if (e.key === 'u'){$(this).surroundWithTags('[u]','[/u]');handled=true;} else
            if (e.key === 'm'){$(this).surroundWithTags('[_=',']');handled=true;} else
            if (e.key === 'k'){
                var link=prompt("URL");
                if(link){
                    $(this).surroundWithTags('[url='+link+']','[/url]');
                }
                handled=true;
            }
            if(handled){
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });

	$('body').on('paste','.abilities textarea,.formBlock textarea',function(ev){
        var pThis=$(this);
		ImgurHelper.uploadFromClipboard(ev, function(link){pThis.surroundWithTags('[img]'+link+'[/img]','');});
	});

    //Form field clicking on and away from
    $('body').on('click','.formText',function(){
        var pThis=$(this);
        if($('input,select',pThis).length==0) {
            var curText=pThis.text();
            var curWidth=pThis.width();
            if(parseInt(curText)!=curText){
                curWidth=Math.max(curWidth,100);
                curWidth=Math.min(curWidth,200);
            }
            pThis.text('');
            var input=$('<input type="text"/>').val(curText).css({"width":curWidth+20}).appendTo(pThis);
            input.focus();
        }
    });


    $('body').on('blur','.formText input',function(){
        var pThis=$(this);
        var val=pThis.val();
        var pFormValue=$(this).closest('.formVal');
        var charSheet=$(this).closest('.customChar');
        pThis.remove();
        var sendVal=val.replaceAll("[", "&#91;");
        sendVal=sendVal.replaceAll("]", "&#93;");
        pFormValue.text(val);
        updateField(pFormValue.data('formfieldidx'),sendVal,isDiceRoll(val)?function(){charSheet.trigger('gp.sheetUpdated');}:null,!charSheet.hasClass('ignoreSave'));
        charSheet.updateCalculations();
    });

    $('body').on('keyup keypress','.formText input',function(e){
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            //pressing return in field causes it to lose focus and save
            $(this).blur();
        }
    });
    //End form field

    //Form block clicking on and away from
    $('body').on('click','.formBlock h2 .ra-quill-ink',function(){
        var pThis=$(this);
        var block=pThis.closest('.formBlock');
        var rendered=$('.formBlockRendered',block);
        getBbCodeBlock(block.data('blockfieldidx'),'block',function(bbcode){
            var leadin=bbcode.match(/^[ \t]*\r?\n/);
            block.data('leadin',(leadin && leadin.length)?leadin[0]:'');
            bbcode=bbcode.replace(/^[ \t]*\r?\n/,'');

            $('<textarea></textarea>').width(rendered.width()).height(Math.max(rendered.height(),200)).val(bbcode).appendTo(block).focus();
        });

        rendered.hide();
    });

    $('body').on('blur','.formBlock textarea',function(){
        var pThis=$(this);
        var block=$(this).closest('.formBlock');
        var charSheet=$(this).closest('.customChar');
        var val=block.data('leadin')+pThis.val();
        pThis.remove();
        $('.formBlockRendered',block).show();

        updateBlock(block.data('blockfieldidx'),val,function(data){if(data){charSheet.html(data.notes);} charSheet.removeClass('calculationsInitialised').updateCalculations(); charSheet.trigger('gp.sheetUpdated');},!charSheet.hasClass('ignoreSave'));
    });
    //End Form block clicking on and away from

    //Abilities block clicking on and away from
    $('body').on('click','.abilities h2 .ra-quill-ink',function(){
        var pThis=$(this);
        var block=pThis.closest('.abilities');
        getBbCodeBlock(block.data('abilitiesfieldidx'),'abilities',function(bbcode){
            var leadin=bbcode.match(/^[ \t]*\r?\n/);
            block.data('leadin',(leadin && leadin.length)?leadin[0]:'');
            bbcode=bbcode.replace(/^[ \t]*\r?\n/,'');
            $('<textarea></textarea>').width(block.width()).height(Math.max(block.height(),200)).val(bbcode).appendTo(block).focus();
        });

        $('.ability',block).hide();
    });

    $('body').on('blur','.abilities textarea',function(){
        var pThis=$(this);
        var block=$(this).closest('.abilities');
        var charSheet=$(this).closest('.customChar');
        var val=block.data('leadin')+pThis.val();
        pThis.remove();
        $('.ability',block).show();

        updateAbilities(block.data('abilitiesfieldidx'),val,function(data){if(data){charSheet.html(data.notes);} charSheet.removeClass('calculationsInitialised').updateCalculations(); charSheet.trigger('gp.sheetUpdated');},!charSheet.hasClass('ignoreSave'));
    });
    //Abilities block clicking on and away from


    //Form check boxes
    $('body').on('click','.formCheck input',function(){
        var checkArea=$(this).closest('.formCheck');
        var val=$('input:checked',checkArea).length+'/'+$('input',checkArea).length;
        var charSheet=$(this).closest('.customChar');

        updateField(checkArea.data('formfieldidx'),val,function(){},!charSheet.hasClass('ignoreSave'));
        charSheet.updateCalculations();
    });
    //End form check boxes

    jQuery.fn.updateCalculations = function (){
        var variables={};

        //we don't need the flash-of-yellow during sheet initiatlisation
        var calcInit=$(this).hasClass('calculationsInitialised');
        $(this).addClass('calculationsInitialised');

        //if fields that look like dice rolls have changed
        var requiresRefresh=false;

        $('.formVar,.formCalc',this).each(function(){
            var pThis=$(this);
            //this is a calculation
            if(pThis.hasClass('formCalc')) {
                var formula=pThis.data('varcalc');
                var isModifier=formula.toString().startsWith('+'); //this is a modifier formula (e.g. +str)
                var newVal=formula;
                pThis.removeClass('formCalcError').attr('title','');
                try{
                    newVal=expressionParse(formula,variables);
                }catch(e){
                    pThis.addClass('formCalcError').attr('title',e.message);
                }
                var curText=pThis.text();
                var newText=((newVal>=0 && isModifier)?"+":"")+newVal; //append + to positive modifiers
                pThis.text(newText);

                if(calcInit && newText!=curText){

                    //flash of yellow
                    if(pThis.hasClass('updatedCalc1')){
                        pThis.removeClass('updatedCalc1').addClass('updatedCalc2')
                    }else{
                        pThis.removeClass('updatedCalc2').addClass('updatedCalc1')
                    }

                    if(isDiceRoll(newText)){
                        requiresRefresh=true;
                    }
                }
            }

            //set a variable
            if(pThis.hasClass('formVar')){
                if(pThis.hasClass('formCheck')){
                    variables[pThis.data('varname')]=$('input:checked',pThis).length;
                } else {
                    var val=$.trim(pThis.text());
                    if(val=='' || isNaN(val)){
                        variables[pThis.data('varname')]="'"+val.replace("'","\\'")+"'";
                    }else{
                        variables[pThis.data('varname')]=val;
                    }
                }
            }
        });

        //refresh required once the save queue is processed
        if(requiresRefresh){
            OnQueueEmpty(function(){$('div.customChar').trigger('gp.sheetUpdated');});
        }
    };

    //initialise all calculations
    $('#charDetails div.customChar').updateCalculations();

    ////helpers
    function expressionParse(expression,variables) {
        var vars='';
        var variableNames=Object.getOwnPropertyNames(variables).sort(function(a, b){return b.length - a.length;});
        for(var i=0;i<variableNames.length;i++){
            vars+='var '+variableNames[i]+'='+variables[variableNames[i]]+';';
        }

        return Function(`"use strict";
                        var d20bonus=function(val){return Math.floor((val-10)/2);};
                        var ceil=function(val){return Math.ceil(val);};
                        var floor=function(val){return Math.floor(val);};
                        var max=function(val1,val2){return Math.max(val1,val2);};
                        var min=function(val1,val2){return Math.min(val1,val2);};
                        var midVal=function(val1,val2,val3){return max(min(val1,val2), min(max(val1,val2),val3));};
                        var lookupBonus=function(val1,...val2){
                            var valchunked=[...Array(Math.ceil(val2.length / 3))].map(_ => val2.splice(0,3));
                            var validx=valchunked.findIndex(function(valtest) {return valtest[0]<=val1 && valtest[1]>=val1;});
                            return validx!=-1?valchunked[validx][2]:0;
                        };
                        var dwBonus=function(val){return lookupBonus(val, 1,3,-3, 4,5,-2, 6,8,-1, 9,12,0, 13,15,1, 16,17,2, 18,18,3);};`
                        +vars+
                        'return ('
                         + expression
                         + ');')();
    }

    //queue the ajax requests so we don't end up in an out-of-order mess
    var ajaxQueue=(function(){
        var queue=[];
        var blockCalls=false;

        var blockUI=function(){
            $('<div id="blockingCalls"></div>').appendTo('body');
        };

        var unblockUI=function(){
            $('#blockingCalls').remove();
        };

        var addToQueue=function(item){
            if(!blockCalls){
                queue.push(item);
                if(queue.length==1){
                    $('#headerBG').css({"backgroundColor":'#fdd'});
                    processQueue();
                    if(item.blocking){
                        blockCalls=true;
                        blockUI();
                    }
                }
            }
        };

        var processQueue=function(){
            var obj=queue[0].obj;
            $.ajax({type: 'post', url: API_HOST +queue[0].api, xhrFields: {withCredentials: true}, data:obj,
                complete:function (data)
                {
                    var fromQueue=queue.shift();
                    if(queue.length>0){
                        processQueue();
                    }else{
                        $('#headerBG').css({"backgroundColor":'#ddd'});
                        unblockUI();
                        blockCalls=false;
                        if(fromQueue.onComplete){
                            fromQueue.onComplete(data.responseJSON);
                        }
                        if(onQueueEmpty){
                            onQueueEmpty();
                            onQueueEmpty=null;
                        }
                    }
                }
            });
        };

        var onQueueEmpty=null;
        var addQueueComplete=function(onEmpty){
            if(queue.length==0 && onEmpty){
                onEmpty();
            } else {
                onQueueEmpty=onEmpty;
            }
        };

        return {addToQueue:addToQueue,addQueueComplete:addQueueComplete, blockUI:blockUI, unblockUI:unblockUI};
    })();

    function updateField(fieldIdx, value, onComplete, process){
        process?ajaxQueue.addToQueue({api: '/characters/bbformUpdateVal', obj:{ charID: $('#characterID').val(), fieldIdx:fieldIdx, fieldValue:value}, onComplete:onComplete}): (onComplete && onComplete(null));
    }

    function updateBlock(blockIdx, value, onComplete, process){
        process?ajaxQueue.addToQueue({api: '/characters/bbformUpdateBlock', obj:{ charID: $('#characterID').val(), blockIdx:blockIdx, fieldValue:value}, blocking:true, onComplete:onComplete}): (onComplete && onComplete(null));
    }

    function updateAbilities(blockIdx, value, onComplete, process){
        process?ajaxQueue.addToQueue({api: '/characters/bbformUpdateAbilities', obj:{ charID: $('#characterID').val(), blockIdx:blockIdx, fieldValue:value}, blocking:true, onComplete:onComplete}): (onComplete && onComplete(null));
    }

    function OnQueueEmpty(onComplete){
        ajaxQueue.addQueueComplete(onComplete);
    }

    function getBbCodeBlock(requestIdx, tagSelector, onComplete){
        ajaxQueue.blockUI();
        ajaxQueue.addQueueComplete(function(){
            $.ajax({type: 'post', url: API_HOST +'/characters/getBbcodeSection', xhrFields: {withCredentials: true}, data:{ charID: $('#characterID').val(), requestIdx:requestIdx, tagSelector:tagSelector },
                complete:function (data){
                    ajaxQueue.unblockUI();
                    onComplete(data.responseJSON.section);
                }
            });
        });
    }

    //val looks like a dice roll or modifier
    function isDiceRoll(val){
        return (/^[\+\-](\d)+$/.test(val))||/((\s*[\+\-]?)\s*(\d*)([dD])?(\d+)(([hlHL])\s*(\d+)\s*)?)+?/g.test(val);
    }

});