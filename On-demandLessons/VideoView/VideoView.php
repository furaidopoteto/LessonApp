<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Login/Login.php');
    exit;
}
if(isset($_GET['jugyoid'])){
    $userdata = $db->prepare('SELECT * FROM account WHERE studentnumber=?');
    $userdata->execute(array($_SESSION['studentnumber']));
    $userdata = $userdata->fetch();

    $check = $db->prepare('SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=? AND grade=? AND gakubu=?');
    $check->execute(array($_GET['jugyoid'], $userdata['grade'], $userdata['gakubu']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $videodata = $db->prepare('SELECT * FROM video WHERE jugyoid=?');
        $videodata->execute(array($_GET['jugyoid']));
    }else{
        header('Location: ../Home/Home.php');
        exit;
    }

    $check2 = $db->prepare('SELECT COUNT(*) AS cnt FROM jukodata WHERE jugyoid=? AND studentnumber=?');
    $check2->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
    $check2 = $check2->fetch();
    if($check2['cnt'] >= 1){
        header('Location: ../Home/Home.php');
        exit;
    }

    $jukodata = $db->prepare("SELECT * FROM jukodata WHERE studentnumber=?");
    $jukodata->execute(array($_SESSION['studentnumber']));
    foreach($jukodata as $index){
        $check3 = $db->prepare("SELECT COUNT(*) AS cnt FROM jukodata j WHERE j.studentnumber=? AND j.jugyoid=? 
        AND NOT EXISTS (SELECT * FROM anketo a WHERE a.studentnumber=j.studentnumber AND a.jugyoid=j.jugyoid)");
        $check3->execute(array($_SESSION['studentnumber'], $index['jugyoid']));
        $check3 = $check3->fetch();
        if($check3['cnt'] >= 1){
            header('Location: ../Home/Home.php?error=anketo');
            exit;
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
    <link rel="stylesheet" href="./VideoView.css">
    <!-- Load TensorFlow.js. This is required to use coco-ssd model. -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"> </script>
    <!-- Load the coco-ssd model. -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd"> </script>
    <title>オンデマンド授業システム</title>
</head>
<body onload="dataset(<?php echo h($_GET['jugyoid']);?>, <?php echo h($_SESSION['studentnumber']);?>)">
    <div class="loadview">
        <span class="loadtitle">動画を読み込み中</span>
        <img src="./loading.gif" class="loadingimg">
    </div>
    <div class="box">
        <?php foreach($videodata as $index):?>
            <!-- oncontextmenuで右クリックでスクロールバーを表示させないようにする 参照: https://unity-yuji.xyz/html-css-forbid-control-video/ -->
            <video id="mainvideo" src="<?php echo h($index['videopass']);?>" oncontextmenu="return false;"></video>
        <?php endforeach;?>
        <span class="nowtime"></span><br>
        <a id="fullscreenbutton" href="javascript: fullscreen()">全画面表示</a>
    </div>
    <video id="AIvideo"></video>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="./VideoView.js"></script>
    <script src="./AIvideo.js"></script>
</body>
</html>