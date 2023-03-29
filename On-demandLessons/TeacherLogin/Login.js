'use strict';

const submitbutton = document.querySelector('.submitbutton');
function formcheck(){
    const teachernumber = document.getElementById('form').teachernumber.value;
    const username = document.getElementById('form').username.value;
    const pw = document.getElementById('form').pw.value;
    if(teachernumber == "" || username == "" || pw.length < 4){
        $('.submitbutton').removeClass("onsubmit");
        submitbutton.disabled = true;
    }else{
        $('.submitbutton').addClass("onsubmit");
        submitbutton.disabled = false;
    }
}