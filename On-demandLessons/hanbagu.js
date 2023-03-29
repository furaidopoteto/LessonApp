'use strict'

$('.hanbagubutton').on('click', function(){
    $('.warpper').toggleClass('navopen');
    $('.top').toggleClass('opentop');
    $('.center').toggleClass('opencenter');
    $('.bottom').toggleClass('openbottom');
});