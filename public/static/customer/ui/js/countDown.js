function monitor(obj,text){var LocalDelay=getLocalDelay();if(LocalDelay.time!=null){var timeLine=parseInt((new Date().getTime()-LocalDelay.time)/1000);if(timeLine>LocalDelay.delay){}else{var _delay=LocalDelay.delay-timeLine;obj.text(_delay+"s");obj.attr("disabled",true);obj.css("cursor","no-drop");var timer=setInterval(function(){if(_delay>1){_delay--;obj.text(_delay+"s");setLocalDelay(_delay)}else{clearInterval(timer);obj.text(text);obj.attr("disabled",false);obj.css("cursor","pointer")}},1000)}}}function setLocalDelay(delay){sessionStorage.setItem("delay_"+location.href,delay);sessionStorage.setItem("time_"+location.href,new Date().getTime())}function getLocalDelay(){var LocalDelay={};LocalDelay.delay=sessionStorage.getItem("delay_"+location.href);LocalDelay.time=sessionStorage.getItem("time_"+location.href);return LocalDelay}function countDown(obj,initDelay,text){if(obj.text()===text){var _delay=initDelay;obj.text(initDelay+"s");obj.attr("disabled",true);obj.css("cursor","no-drop");var timer=setInterval(function(){if(_delay>1){_delay--;obj.html(_delay+"s");setLocalDelay(_delay)}else{clearInterval(timer);obj.text(text);obj.attr("disabled",false);obj.css("cursor","pointer")}},1000)}else{return false}};