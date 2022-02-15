$(function () {

    var gameOptions={diceRules:[]};
    try{
        gameOptions=JSON.parse($('#gameOptions').html());
    }catch(e){}

    if(gameOptions){
        //jquery helper pseudoselectors
        $.expr[':'].equals =  $.expr[':'].equals || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text()==arg;
            };
        });

        $.expr[':'].reasonEquals = $.expr[':'].reasonEquals || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                var thisVal=escape($('.rollString',$(elem).closest('.roll')).text().toLowerCase());
                return thisVal.indexOf(arg)!=-1;
            };
        });

        $.expr[':'].paired =  $.expr[':'].paired || $.expr.createPseudo(function() {
            return function( elem ) {
                var pThis=$(elem);
                var thisVal=pThis.text();
                return $('i:equals("'+thisVal+'")',pThis.closest('.parsedRolls')).length>1;
            };
        });

        $.expr[':'].d100double =  $.expr[':'].d100double || $.expr.createPseudo(function() {
            return function( elem ) {
                var thisVal=parseInt($(elem).text());
                return ((thisVal%11)==0)||(thisVal==100);
            };
        });

        $.expr[':'].ge =  $.expr[':'].ge || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return parseInt($(elem).text())>=parseInt(arg);
            };
        });

        $.expr[':'].le =  $.expr[':'].le || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return parseInt($(elem).text())<=parseInt(arg);
            };
        });

        $.expr[':'].meval =  $.expr[':'].meval || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                try{
                    if(arg.includes('x')){
                        return diceExpression(arg.replace(/[x]/gi,$(elem).text()));
                    }
                    else{
                        return diceExpression($(elem).text()+arg);
                    }
                }catch{}
                return false;
            };
        });

        //strips bad characters from maths eval expression, prepends with = if nothing else present
        var valToMeval = function (val){
            val=String(val).toLowerCase();
            val.replace(/[^0-9\<\=\>\!x\&\|]/gi, '');
            if(!val.startsWith('<') && !val.startsWith('>') && !val.startsWith('=') && !val.startsWith('!') && !val.includes('x')){
                val="="+val;
            }

            //replace single = with == if not a <= >= etc
            val=val.replace(/(^|[^\<\>\!\&\|\=])(\=)([^\<\>\!\&\|\=])/g,"$1==$3");

            //replace 7<x<9 with 7<x && x<9
            val=val.replace(/([=<>])(x)([=><])/g,"$1x && x$3");

            return val;
        }

        //toggle ordering between original and numeric
        var orderRolls=function(parsedRolls){
            if(parsedRolls.hasClass('rollsOrderedByVal')){
                $('i', parsedRolls).sort(function (a, b) { return parseInt($(a).data('ro'))-parseInt($(b).data('ro')); }).appendTo(parsedRolls);
            }else{
                $('i', parsedRolls).sort(function (a, b) { return parseInt($(a).data('rv'))-parseInt($(b).data('rv')); }).appendTo(parsedRolls);
            }
            parsedRolls.toggleClass('rollsOrderedByVal');
        };

        //apply dice rules
        var applyDiceRules=function(parsedRolls){
            var rollstring=parsedRolls.data('rollstring');

            for (var count = 0; count < gameOptions.diceRules.length; count++) {
                var rule = gameOptions.diceRules[count];
                if(rollstring && rule.rolled &&  rollstring.toLowerCase().indexOf(rule.rolled.toLowerCase())!=-1){
                    //rules highlighting
                    if(rule.highlight || rule.content || rule.contentAppend || rule.hideTotal){

                        var dieSelector=false;
                        var matchedDice=null;

                        //hashed reason e.g. DC##
                        if(rule.reason){
                            var foundHashes=rule.reason.indexOf('##');
                            if(foundHashes!=-1){
                                var reasonRegExpression=rule.reason.toLowerCase().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                                reasonRegExpression='.*'+reasonRegExpression.replace(/##/g,'\\s*(\\d+)\\s*')+'.*';
                                var matchReason=new RegExp(reasonRegExpression);

                                var rollSection=parsedRolls.closest('.rollResults');
                                matchedDice=$('.rollTotal',rollSection).filter(function(index){
                                    var pThis=$(this);
                                    var rollString=$('.rollString',pThis.closest('.roll'));
                                    var matched=matchReason.exec(rollString.text().toLowerCase());
                                    if(matched && matched.length==2){
                                        var totalHashedSelector=':meval('+valToMeval(rule.total.replace(/##/g,matched[1]))+')';
                                        if($('.rollTotal'+totalHashedSelector,pThis.parent()).length>0){
                                            return true;
                                        }
                                    }
                                    return false;
                                });
                            }
                        }

                        if(matchedDice==null)
                        {

                            var reasonSelector=''
                            var selectorSuffix=''

                            //reason
                            if(rule.reason){
                                reasonSelector+=":reasonEquals('"+escape(rule.reason.toLowerCase())+"')";
                            }

                            //last die
                            if(rule.lastDie) {
                                selectorSuffix+=':last';
                            }

                            //roll values
                            if(rule.natural) {
                                selectorSuffix+=':meval('+valToMeval(rule.natural)+')';
                            }

                            if(rule.ge) {
                                selectorSuffix+=':ge('+rule.ge+')';
                            }

                            if(rule.le) {
                                selectorSuffix+=':le('+rule.le+')';
                            }

                            //check for paired dice
                            if(rule.paired) {
                                selectorSuffix+=':paired';
                            }

                            //d100 doubles (11,22,..,99,100)
                            if(rule.d100double) {
                                selectorSuffix+=':d100double';
                            }

                            var totalSuffix='';
                            if(rule.total){
                                totalSuffix+=':meval('+valToMeval(rule.total)+')';
                            }

                            if(selectorSuffix){
                                dieSelector=true; //selecting individal dice
                                matchedDice=$('i'+selectorSuffix+reasonSelector,parsedRolls);
                            } else if(totalSuffix){
                                matchedDice=$('.rollTotal'+totalSuffix+reasonSelector,parsedRolls.closest('.roll'));
                            }
                        }

                        //apply highlighting
                        if(matchedDice && matchedDice.length){
                            if(rule.highlight) {
                                var highlightClass=rule.highlight.split(" ").map(function(item) {return 'rollVal-'+item.trim().toLowerCase();}).join(' ');

                                matchedDice.addClass(highlightClass);
                            }

                            if(rule.content) {
                                matchedDice.each(function(){$(this).attr('title',$(this).text()).text(rule.content);});
                            }

                            if(rule.contentAppend){
                                if(dieSelector) {
                                    matchedDice.each(function(){$(this).text($(this).text()+' '+rule.contentAppend);});
                                } else {
                                    matchedDice.each(function(){$('<span></span>').text(' '+rule.contentAppend).insertAfter($(this));});
                                }
                            }

                            if(matchedDice.length>0 && rule.hideTotal){
                                if(dieSelector) {
                                    $('.rollResultTotal',matchedDice.closest('.roll')).hide();
                                } else {
                                    matchedDice.closest('.rollResultTotal').hide();
                                }
                            }
                        }
                    }

                    //other rule options
                    if(rule.autoSort){
                        orderRolls(parsedRolls);
                    }
                }
            }
        };

        //convert the text into spans and apply the rules
        jQuery.fn.applyDiceRules = function (){
            $('.rollValues',this).each(function(){
                var pThis=$(this);

                pThis.on('click',function(){orderRolls($(this));});

                if(gameOptions.diceRules){
                    applyDiceRules(pThis);
                }
            });

            return this;
        };

        $('body').applyDiceRules();

        function diceExpression(expression) {
            return Function('"use strict"; return (' + expression + ');')();
        }
    }

});