<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherLogin/Login.php');
    exit;
}
if(isset($_GET['jugyoid'])){
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=? AND kadai='ari'");
    $check->execute(array($_GET['jugyoid']));
    $check = $check->fetch();
    if($check['cnt'] <= 0){
        header('Location: ../TeacherHome/Home.php');
        exit;
    }
    
    $users = $db->prepare("SELECT * FROM account a WHERE EXISTS 
    (SELECT * FROM video v WHERE v.jugyoid=? AND a.grade=v.grade AND a.gakubu=v.gakubu) ORDER BY studentnumber");
    $users->execute(array($_GET['jugyoid']));

    $title = $db->prepare("SELECT *, DATE_FORMAT(simekiri, '%Y-%m-%d %H:%i') as simekirifm FROM video WHERE jugyoid=?");
    $title->execute(array($_GET['jugyoid']));
    $title = $title->fetch();

    $allusercount = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE grade=? AND gakubu=?");
    $allusercount->execute(array($title['grade'], $title['gakubu']));
    $allusercount = $allusercount->fetch();

    $zumicount = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE jugyoid=?");
    $zumicount->execute(array($_GET['jugyoid']));
    $zumicount = $zumicount->fetch();
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
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <link rel="stylesheet" href="./Home.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>提出状況確認画面</title>
</head>
<body>
    <div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">
            <?php echo h($title['title']);?>の課題提出状況: <?php echo $zumicount['cnt']. "/". $allusercount['cnt'];?>
        </span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <div class="warpper">
            <div class="hanbagubutton">
                <span class="top"></span>
                <span class="center"></span>
                <span class="bottom"></span>
            </div>
            <ul class="nav">
                <li class="navlist"><?php echo h($_SESSION['teachernumber']);?>でログイン中</li>
                <li class="navlist"><a class="navlist" href="../Logout/Logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <div class="box">
        締め切り: <?php echo h($title['simekirifm']);?>まで
        <div class="subbox">
            <span class="column">学籍番号</span>
            <span class="column">氏名</span>
            <span class="column">提出状況</span>
            <span class="column">提出されたファイル</span>
        </div>
        <?php foreach($users as $index):?>
            <?php
            $flag = false;
            $check = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE jugyoid=? AND studentnumber=?");
            $check->execute(array($_GET['jugyoid'], $index['studentnumber']));
            $check = $check->fetch();
            if($check['cnt'] >= 1){
                $flag = true;
            }else{
                $flag = false;
            }
            
            $kadai = null;
            if($flag){
                $kadai = $db->prepare("SELECT * FROM kadai WHERE jugyoid=? AND studentnumber=?");
                $kadai->execute(array($_GET['jugyoid'], $index['studentnumber']));
                $kadai = $kadai->fetch();
            }
            ?>
            <div class="subbox">
                <span class="column"><?php echo h($index['studentnumber']);?></span>
                <span class="column"><?php echo h($index['username']);?></span>
                <span class="column">
                    <?php if($flag):?>
                        <span class="kadaiok">提出済み</span>
                    <?php else:?>
                        <span class="error">未提出</span>
                    <?php endif;?>
                </span>
                <span class="column">
                    <?php if($flag):?>
                        <a target="_blank" href="<?php echo h($kadai['kadaipass']);?>"><?php echo h($kadai['filename']);?></a>
                    <?php else:?>
                        <span class="error">未提出</span>
                    <?php endif;?>
                </span>
            </div>
        <?php endforeach;?>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        function load(){
            location.reload();
        }
    </script>
</body>
</html>