<?php
require("../DBconnect.php");
session_start();
if(!isset($_SESSION['studentnumber']) || !isset($_GET['jugyoid'])){
    header('Location: ../Home/Home.php');
    exit;
}else{
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM jukodata j WHERE j.studentnumber=? AND j.jugyoid=? 
    AND NOT EXISTS (SELECT * FROM anketo a WHERE a.studentnumber=j.studentnumber AND a.jugyoid=j.jugyoid)");
    $check->execute(array($_SESSION['studentnumber'], $_GET['jugyoid']));
    $check = $check->fetch();
    if($check['cnt'] <= 0){
        header("Location: ../Home/Home.php");
        exit;
    }
    $count = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=?");
    $count->execute(array($_GET['jugyoid']));
    $count = $count->fetch();
    if($count['cnt'] >= 1){
        $title = $db->prepare("SELECT * FROM video WHERE jugyoid=?");
        $title->execute(array($_GET['jugyoid']));
        $title = $title->fetch();
    }else{
        header('Location: ../Home/Home.php');
        exit;
    }
}
if(!empty($_POST)){
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM jukodata j WHERE j.studentnumber=? AND j.jugyoid=? 
    AND NOT EXISTS (SELECT * FROM anketo a WHERE a.studentnumber=j.studentnumber AND a.jugyoid=j.jugyoid)");
    $check->execute(array($_SESSION['studentnumber'], $_POST['jugyoid']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $insert = $db->prepare("INSERT INTO anketo (jugyoid, studentnumber, item1, item2, item3, item4, item5, question, time)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $insert->execute(array($_POST['jugyoid'], $_SESSION['studentnumber'],
         $_POST['item1'], $_POST['item2'], $_POST['item3'], $_POST['item4'], $_POST['item5'], $_POST['question']));
        header("Location: ./Cmp.php");
        exit;
    }else{
        header("Location: ../Home/Home.php");
        exit;
    }
}
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>授業アンケート</title>
    <link rel="stylesheet" href="./Anketo.css">
</head>
<body>
    <div class="headerbox">
        <span class="title">「<?php echo h($title['title']);?>」の授業アンケート</span>
    </div>
    <div class="mainbox">
        <form action="" method="POST" id="form">
            <input type="hidden" name="jugyoid" value="<?php echo $_GET['jugyoid'];?>">
        </form>  
    </div>
    <script>
        'use strict';

        const form = document.getElementById('form');
        const question = ["授業の内容はどのくらい理解できましたか?",
         "先生の教え方は分かりやすかったですか?",
          "今回の授業は面白かったですか?",
          "聞き取りやすかったですか?",
          "今後も同じようなやり方で実施してほしいと思いますか?"];
        const items = [["まったく理解できなかった", "理解できない部分もあった", "なんとなく理解できた", "理解できた", "非常に理解できた"],
         ["非常に分かりにくかった", "分かりにくかった", "どちらでもない", "分かりやすかった", "非常に分かりやすかった"],
         ["非常につまらなかった", "つまらなかった", "普通", "面白かった", "非常に面白かった"],
         ["非常に聞きづらかった", "聞き取りづらかった", "普通", "聞き取りやすかった", "非常に聞き取りやすかった"],
         ["まったく思わない", "思わない", "どちらとも言えない", "そう思う", "非常にそう思う"]];
        

        for(let i = 0;i<question.length;i++){
            form.insertAdjacentHTML("beforeend", `<div class="subbox" id="subbox${i}"><span class="subboxtitle">質問${i+1}: ${question[i]}<span class="hissu">※必須</span></span><br></div>`);
            const subbox = document.getElementById(`subbox${i}`);
            for(let j = 0;j<5;j++){
                subbox.insertAdjacentHTML("beforeend", 
                `<label><input class="radiobutton" type="radio" name="item${i+1}" value=${j+1} required>
                <span class="itemtitle">${items[i][j]}</span></label><br>
                </div>`)
            }
        }
        form.insertAdjacentHTML("beforeend", `
        <div class="subbox">
            <span class="subboxtitle">その他、授業に対する要望や質問等などがありましたらご記入ください。</span><br>
            <textarea class="questionarea" name="question" maxlength=500></textarea>
        </div>`);

        form.insertAdjacentHTML("beforeend", `<div class="submitbox"><input id="submitbutton" type="submit" value="送信する"></div>`)
        
    </script>
</body>
</html>