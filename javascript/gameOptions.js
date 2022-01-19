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
                    if(rule.highlight || rule.content || rule.hideTotal){

                        var highlightClass=rule.highlight?highlightClass=rule.highlight.split(" ").map(function(item) {return 'rollVal-'+item.trim().toLowerCase();}).join(' '):'';

                        var selectorSuffix='';

                        //last die
                        if(rule.lastDie) {
                            selectorSuffix+=':last';
                        }

                        //roll values
                        if(rule.natural) {
                            selectorSuffix+=':equals('+rule.natural+')';
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
                        if(rule.totalge){
                            totalSuffix+=':ge('+rule.totalge+')';
                        }
                        if(rule.totalle){
                            totalSuffix+=':le('+rule.totalle+')';
                        }

                        if(selectorSuffix){
                            var matchedDice=$('i'+selectorSuffix,parsedRolls);

                            //reason
                            if(rule.reason){
                                var match=rule.reason.toLowerCase();
                                matchedDice=matchedDice.filter(function(){
                                    var thisVal=$('.rollString',$(this).closest('.roll')).text().toLowerCase();
                                    return thisVal.indexOf(match)!=-1;
                                });
                            }

                            if(rule.highlight){
                                matchedDice.addClass(highlightClass);
                            }
                            if(rule.content){
                                matchedDice.each(function(){$(this).attr('title',$(this).text()).text(rule.content);});
                            }

                            if(matchedDice.length>0 && rule.hideTotal){
                                $('.rollResultTotal',matchedDice.closest('.roll')).hide();
                            }

                        } else if(totalSuffix){
                            var matchTotal=$('.rollTotal'+totalSuffix,parsedRolls.closest('.roll'));

                            if(rule.reason){
                                var match=rule.reason.toLowerCase();
                                matchTotal=matchTotal.filter(function(){
                                    var thisVal=$('.rollString',$(this).closest('.roll')).text().toLowerCase();
                                    return thisVal.indexOf(match)!=-1;
                                });
                            }

                            if(rule.highlight){
                                matchTotal.addClass(highlightClass);
                            }
                            if(rule.content){
                                matchTotal.each(function(){$(this).attr('title',$(this).text()).text(rule.content);});
                            }
                            if(matchTotal.length>0 && rule.hideTotal){
                                matchTotal.closest('.rollResultTotal').hide();
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
    }
});