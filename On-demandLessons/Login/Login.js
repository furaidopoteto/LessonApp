'use strict';

const submitbutton = document.querySelector('.submitbutton');
function formcheck(){
    const studentnumber = document.getElementById('form').studentnumber.value;
    const username = document.getElementById('form').username.value;
    const pw = document.getElementById('form').pw.value;
    if(studentnumber == "" || username == "" || pw.length < 4){
        $('.submitbutton').removeClass("onsubmit");
        submitbutton.disabled = true;
    }else{
        $('.submitbutton').addClass("onsubmit");
        submitbutton.disabled = false;
    }
}