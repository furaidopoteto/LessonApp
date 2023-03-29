'use strict';

        const AIvideo = document.getElementById('AIvideo');
        let AImodel = null;
        let personcheck = true;
        let timerID1 = null;
        let timerID2 = null;

        let stopdate = null;
        let stophantei = false;
        let DetectionCount = 0;

        // Load the model.
        cocoSsd.load().then(model => {
            AImodel = model;

            $('.loadview').remove();
            
            navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            }).then(stream => {
                $('.loadview').remove();
                AIvideo.srcObject = stream;
                AIvideo.play();
                stopdate = new Date();
                timerID1 = setInterval(AIstart, 10);
                timerID2 = setInterval(timer, 10);
                const mainvideo = document.getElementById('mainvideo');
                mainvideo.play();
            }).catch(e => {
                alert("Webカメラを検出できませんでした");
                location.href = "../Home/Home.php";
                console.log(e);
            })
            
        });


        async function AIstart(){
            AImodel.detect(AIvideo).then(predictions => {
                let count = 0;
                for(let i = 0;i<predictions.length;i++){
                    if(predictions[i]['class'] == 'person'){
                        count++;
                    }
                }
                for(let i = 0;i<predictions.length;i++){
                    if(predictions[i]['class'] == "cell phone"){
                        count = 0;
                    }
                }
                if(count >= 1){
                    personcheck = true;
                }else{
                    personcheck = false;
                }
            });
        }


        function timer(){
            if(personcheck){
                stopdate = new Date();
            }
            let nowtime = new Date();
            let interval = new Date(nowtime-stopdate);
            const hour = interval.getUTCHours();
            const min = interval.getMinutes();
            const sec = interval.getSeconds();
            console.log(`人が映っていない、またはスマホを操作している時間: ${hour}時間${min}分${sec}秒`);
            if(sec >= 10){
                DetectionCount++;
                stopdate = new Date();
            }
            if(DetectionCount >= 2){
                mainvideo.pause();
                clearInterval(timerID1)
                clearInterval(timerID2);
                alert("不正を検知したので受講は無効になります");
                location.href = "../Home/Home.php";
            }
        }