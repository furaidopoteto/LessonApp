<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherLogin/Login.php');
    exit;
}else{
    $videos = $db->prepare('SELECT * FROM video WHERE teachernumber=?');
    $videos->execute(array($_SESSION['teachernumber']));
}
if(!empty($_GET)){
    if(empty($_GET['grade']) && empty($_GET['gakubu'])){
        $videos = $db->prepare('SELECT * FROM video WHERE teachernumber=?');
        $videos->execute(array($_SESSION['teachernumber']));
    }else if(!empty($_GET['grade']) && empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE teachernumber=? AND grade=?");
        $check->execute(array($_SESSION['teachernumber'], $_GET['grade']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $videos = $db->prepare("SELECT * FROM video WHERE teachernumber=? AND grade=?");
            $videos->execute(array($_SESSION['teachernumber'], $_GET['grade']));
        }else{
            $videos = [];
        }
    }else if(empty($_GET['grade']) && !empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE teachernumber=? AND gakubu=?");
        $check->execute(array($_SESSION['teachernumber'], $_GET['gakubu']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $videos = $db->prepare("SELECT * FROM video WHERE teachernumber=? AND gakubu=?");
            $videos->execute(array($_SESSION['teachernumber'], $_GET['gakubu']));
        }else{
            $videos = [];
        }
    }else if(!empty($_GET['grade']) && !empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE teachernumber=? AND grade=? AND gakubu=?");
        $check->execute(array($_SESSION['teachernumber'], $_GET['grade'], $_GET['gakubu']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $videos = $db->prepare("SELECT * FROM video WHERE teachernumber=? AND grade=? AND gakubu=?");
            $videos->execute(array($_SESSION['teachernumber'], $_GET['grade'], $_GET['gakubu']));
        }else{
            $videos = [];
        }
    }

    if(!empty($_GET['jugyoid'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=?");
        $check->execute(array($_GET['jugyoid']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $videos = $db->prepare("SELECT * FROM video WHERE jugyoid=?");
            $videos->execute(array($_GET['jugyoid']));
        }else{
            $videos = [];
        }
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
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./HomeStyle.css">
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <title>教職員用画面</title>
</head>
<body>
    <div class="header">
        <span class="headeritem1">投稿済み授業一覧</span>
        <span id="selectboxtitle">条件:</span>
        <select name="grade" id="gradeselect" onchange="dataselect()"></select>
        <select name="gakubu" id="gakubuselect" onchange="dataselect()"></select>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <?php include("../nav.php");?>
    </div>
    <div class="box">
        <?php foreach($videos as $index):?>
            <?php
            $questioncount = $db->prepare("SELECT COUNT(*) AS cnt FROM question WHERE jugyoid=?");
            $questioncount->execute(array($index['jugyoid']));
            $questioncount = $questioncount->fetch();
        
            $nullcount = $db->prepare("SELECT COUNT(*) AS cnt FROM question WHERE answer IS NULL AND jugyoid=?");
            $nullcount->execute(array($index['jugyoid']));
            $nullcount = $nullcount->fetch();
            $unanswer = "";
            if($nullcount['cnt'] >= 1){
                $unanswer = "unanswer";
            }

            $allusercount = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE grade=? AND gakubu=?");
            $allusercount->execute(array($index['grade'], $index['gakubu']));
            $allusercount = $allusercount->fetch();

            $anketocount = $db->prepare("SELECT COUNT(*) AS cnt FROM anketo WHERE jugyoid=?");
            $anketocount->execute(array($index['jugyoid']));
            $anketocount = $anketocount->fetch();

            $kadai = "";
            $zumicount = "";
            $kadaiflag = "";
            if($index['kadai'] == "nasi"){
                $kadai = "なし";
            }else{
                $kadai = "あり";
                $zumicount = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE jugyoid=?");
                $zumicount->execute(array($index['jugyoid']));
                $zumicount = $zumicount->fetch();
                if($zumicount['cnt'] != $allusercount['cnt']){
                    $kadaiflag = "redview";
                }
            }
            ?>
            <div class="subbox">
                <video src="<?php echo h($index['videopass']);?>" poster="<?php echo h($index["samune"]);?>" class="samuneimg" controls></video>
                <span class="subboxitem"><?php echo h($index['title']);?></span><br>
                <span class="subboxitem"><?php echo h($index['grade']);?>年</span>
                <span class="subboxitem"><?php echo h($index['gakubu']);?>(授業ID:<?php echo $index['jugyoid'];?>)</span><br>
                <span class="time subboxitem">投稿日時:<?php echo h($index['time']);?></span>
                <a href="../DeleteVideo/Delete.php?id=<?php echo h($index['jugyoid']);?>" onclick="return deletecheck()">削除</a><br>
                <a href="../QuestionAnswer/QuestionAnswer.php?jugyoid=<?php echo h($index['jugyoid']);?>">質問に回答する</a><br>
                <span class="subboxitem">質問数: <?php echo $questioncount['cnt'];?>件</span><br>
                <span class="subboxitem <?php echo $unanswer;?>">未回答数: <?php echo $nullcount['cnt'];?>件</span><br>
                <span class="subboxitem"><a href="./ViewAnketo.php?jugyoid=<?php echo $index['jugyoid'];?>">アンケート結果を見る</a></span><br>
                <span class="subboxitem">アンケート回答数: <?php echo $anketocount['cnt'];?>/<?php echo $allusercount['cnt'];?></span><br>
                <span class="subboxitem">課題提出: <?php echo $kadai;?></span><br>
                <?php if($kadai == "あり"):?>
                    <span class="subboxitem <?php echo $kadaiflag;?>">提出状況: <?php echo $zumicount['cnt'];?>/<?php echo $allusercount['cnt'];?><a href="../KadaiView/Home.php?jugyoid=<?php echo $index['jugyoid'];?>">詳細</a></span><br>
                <?php endif;?>
            </div>
        <?php endforeach;?>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script src="../grade&gakubu.js"></script>
    <script>
        'use strict';

        function deletecheck(){
            return confirm("本当に動画を削除しますか？");
        }

        function dataselect(){
            const grade = document.getElementById('gradeselect').value;
            const gakubu = document.getElementById('gakubuselect').value;
            location.href = "./Home.php?grade="+grade+"&gakubu="+gakubu;
        }

        function load(){
            location.reload();
        }
    </script>
</body>
</html>