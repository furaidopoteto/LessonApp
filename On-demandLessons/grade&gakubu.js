'use strict';

let queryString = window.location.search;
let parameterlist = [];
let forselected = "";
if(queryString){
  queryString = queryString.substring(1);
  let parameters = queryString.split('&');

  for (let i = 0; i < parameters.length; i++) {
    let element = parameters[i].split('=');

    let paramName = decodeURIComponent(element[0]);
    let paramValue = decodeURIComponent(element[1]);

    parameterlist.push(paramValue);
  }
}
const gradeselect = document.getElementById('gradeselect');
const gakubuselect = document.getElementById('gakubuselect');
gradeselect.insertAdjacentHTML("beforeend", `<option value="">学年を選択</option>`);
gakubuselect.insertAdjacentHTML("beforeend", `<option value="">学部を選択</option>`);
for(let i = 1;i<=4;i++){
    if(parameterlist.indexOf(String(i)) != -1){
        forselected = "selected";
    }
    gradeselect.insertAdjacentHTML("beforeend", `<option value=${i} ${forselected}>${i}年</option>`);
    forselected = "";
}

const gakubu = ["情報学部", "工学部", "教育学部", "経済学部"];
for(let i in gakubu){
    if(parameterlist.indexOf(gakubu[i]) != -1){
        forselected = "selected";
    }
    gakubuselect.insertAdjacentHTML("beforeend", `<option value="${gakubu[i]}" ${forselected}>${gakubu[i]}</option>`);
    forselected = "";
}