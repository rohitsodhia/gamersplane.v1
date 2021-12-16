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

        $.expr[':'].reason =  $.expr[':'].reason || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                var thisVal=('.rollString',$(elem).closest('roll')).text().toLowerCase();
                return thisVal.indexOf(arg)!=-1;
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
                    if(rule.highlight || rule.content){

                        var highlightClass=rule.highlight?highlightClass=rule.highlight.split(" ").map(function(item) {return 'rollVal-'+item.trim().toLowerCase();}).join(' '):'';

                        var selectorSuffix='';

                        //last die
                        if(rule.lastDie) {
                            selectorSuffix+=':last';
                        }

                        //natural values
                        if(rule.natural) {
                            selectorSuffix+=':equals('+rule.natural+')';
                        }

                        //check for paired dice
                        if(rule.paired) {
                            selectorSuffix+=':paired';
                        }

                        //d100 doubles (11,22,..,99,100)
                        if(rule.d100double) {
                            selectorSuffix+=':d100double';
                        }

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
                            matchedDice.text(rule.content);
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
        $('.rollValues').each(function(){
            var pThis=$(this);

            pThis.on('click',function(){orderRolls($(this));});

            if(gameOptions.diceRules){
                applyDiceRules(pThis);
            }
        });
    }
});