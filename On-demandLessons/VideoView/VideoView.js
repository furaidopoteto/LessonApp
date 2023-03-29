'use strict';

const mainvideo = document.getElementById('mainvideo');
const nowtime = document.querySelector('.nowtime');
let min = 0
let sec = 0
let nowmin = 0;
let nowsec = 0;
let jugyoid = null;
let studentnumber = null;

function dataset(id, sn){
    jugyoid  = id;
    studentnumber = sn;
}

// videoタグのイベント情報 参照: https://shanabrian.com/web/javascript/video-event.php
mainvideo.addEventListener("loadeddata", function(){
    min = String(Math.floor(mainvideo.duration/60)).padStart(2, '0');
    sec = String(Math.floor(mainvideo.duration%60)).padStart(2, '0');
});

function fullscreen(){// videoタグの全画面表示 参照: https://developer.mozilla.org/en-US/docs/Web/API/Fullscreen_API/Guide
    if(mainvideo.requestFullscreen){
        mainvideo.requestFullscreen();
    }
}

mainvideo.addEventListener('timeupdate', function(){
    nowmin = String(Math.floor(mainvideo.currentTime/60)).padStart(2, '0');
    nowsec = String(Math.floor(mainvideo.currentTime%60)).padStart(2, '0');
    nowtime.textContent = `${nowmin}:${nowsec}/${min}:${sec}`;
})

mainvideo.addEventListener('ended', function(){
    $.ajax({
        type: "post",
        url: "http://127.0.0.1:8080/On-demandLessons/VideoView/Completion.php",
        data: {"studentnumber": studentnumber, "jugyoid": jugyoid}
    }).done(function(kekka){
        alert(kekka);
        location.href = "../Anketo/Anketo.php?jugyoid="+jugyoid;
    }).fail(function(kekka){
        alert("通信に失敗しました");
        location.href = "../Home/Home.php";
    })
})