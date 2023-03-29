<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Login/Login.php');
    exit;
}else{
    $userdata = $db->prepare('SELECT * FROM account WHERE studentnumber=?');
    $userdata->execute(array($_SESSION['studentnumber']));
    $userdata = $userdata->fetch();
    
    $videos = $db->prepare('SELECT * FROM video WHERE grade=? AND gakubu=?');
    $videos->execute(array($userdata['grade'], $userdata['gakubu']));

    $studentdata = $db->prepare("SELECT * FROM account WHERE studentnumber=?");
    $studentdata->execute(array($_SESSION['studentnumber']));
    $studentdata = $studentdata->fetch();

    $allcount = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE grade=? AND gakubu=?");
    $allcount->execute(array($studentdata['grade'], $studentdata['gakubu']));
    $allcount = $allcount->fetch();

    $cmp = $db->prepare("SELECT COUNT(*) AS cnt FROM video v, jukodata j WHERE v.jugyoid=j.jugyoid AND j.studentnumber=?");
    $cmp->execute(array($_SESSION['studentnumber']));
    $cmp = $cmp->fetch();

    $kadaisum = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE grade=? AND gakubu=? AND kadai='ari'");
    $kadaisum->execute(array($userdata['grade'], $userdata['gakubu']));
    $kadaisum = $kadaisum->fetch();

    $kadainow = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE studentnumber=?");
    $kadainow->execute(array($_SESSION['studentnumber']));
    $kadainow = $kadainow->fetch();
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
    <title>オンデマンド授業システム</title>
</head>
<body>
    <div class="header">
        <span class="headeritem1">授業一覧</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <span class="jukodata">受講数: <?php echo $cmp['cnt'];?>/<?php echo $allcount['cnt'];?></span>
        <span class="jukodata">課題提出数: <?php echo $kadainow['cnt'];?>/<?php echo $kadaisum['cnt'];?></span>
        <div class="warpper">
            <div class="hanbagubutton">
                <span class="top"></span>
                <span class="center"></span>
                <span class="bottom"></span>
            </div>
            <ul class="nav">
                <li class="navlist"><?php echo h($_SESSION['studentnumber']);?>でログイン中</li>
                <li class="navlist"><a class="navlist" href="../Logout/Logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <div class="box">
        <?php foreach($videos as $index):?>
            <?php
            $kekka = "";
            $classname = "";
            $aclassname = "";
            $check = $db->prepare('SELECT COUNT(*) AS cnt FROM jukodata WHERE studentnumber=? AND jugyoid=?');
            $check->execute(array($_SESSION['studentnumber'], $index['jugyoid']));
            $check = $check->fetch();
            if($check['cnt'] >= 1){
                $kekka = "受講済み";
                $classname = "ok";
                $aclassname = "aok";
            }else{
                $kekka = "未受講";
                $classname = "ng";
            }

            $anketo = "";
            $anketourl = "";
            $check = $db->prepare("SELECT COUNT(*) AS cnt FROM jukodata j WHERE j.studentnumber=? AND j.jugyoid=? 
            AND NOT EXISTS (SELECT * FROM anketo a WHERE a.studentnumber=j.studentnumber AND a.jugyoid=j.jugyoid)");
            $check->execute(array($_SESSION['studentnumber'], $index['jugyoid']));
            $check = $check->fetch();
            if($check['cnt'] >= 1){
                $anketo = "アンケート未回答";
                $anketourl = "../Anketo/Anketo.php?jugyoid=". $index['jugyoid'];
            }

            $kadai = "";
            if($index['kadai'] == "nasi"){
                $kadai = "なし";
            }else{
                $kadai = "あり";
            }

            $kadaicheck = false;
            $kadaitext = "";
            $kadaiclassname = "";
            $uploadkadai = "";
            if($kadai == "あり"){
                $select = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE jugyoid=? AND studentnumber=?");
                $select->execute(array($index['jugyoid'], $_SESSION['studentnumber']));
                $select = $select->fetch();
                if($select['cnt'] > 0){
                    $kadaicheck = true;
                    $kadaitext = "提出済み";
                    $kadaiclassname = "ok";
                    $uploadkadai = $db->prepare("SELECT * FROM kadai WHERE jugyoid=? AND studentnumber=?");
                    $uploadkadai->execute(array($index['jugyoid'], $_SESSION['studentnumber']));
                    $uploadkadai = $uploadkadai->fetch();
                }else{
                    $kadaitext = "未提出";
                    $kadaiclassname = "ng";
                }
            }
            ?>
            <div class="subbox">
                <a href="../VideoView/VideoView.php?jugyoid=<?php echo h($index['jugyoid']);?>" class="<?php echo $aclassname;?>">
                <i onmouseover="mouseover(<?php echo h($index['jugyoid']);?>)" onmouseout="mouseleave(<?php echo h($index['jugyoid']);?>)" id="itag<?php echo h($index['jugyoid']);?>" class="fa-solid fa-play itag"></i>
                <img onmouseover="mouseover(<?php echo h($index['jugyoid']);?>)" onmouseout="mouseleave(<?php echo h($index['jugyoid']);?>)" id="videoimg" src="<?php echo h($index['samune']);?>" class="samuneimg"></a>
                <span class="subboxitem checktitle <?php echo $classname;?>"><?php echo $kekka;?></span><br>
                <span class="subboxitem"><?php echo h($index['title']);?></span><br>
                <span class="subboxitem"><?php echo h($index['grade']);?>年</span>
                <span class="subboxitem"><?php echo h($index['gakubu']);?></span><br>
                <span class="time subboxitem">投稿日時:<?php echo h($index['time']);?></span>
                <span class="subboxitem"><a href="../Question/Question.php?jugyoid=<?php echo $index['jugyoid'];?>">質問する</a></span><br>
                <span class="subboxitem"><a class="anketo" href="<?php echo $anketourl;?>"><?php echo $anketo;?></a></span><br>
                <?php if($kadai == "あり"):?>
                    <span class="subboxitem <?php echo  $kadaiclassname;?>"><?php echo $kadaitext;?></span><br>
                    <?php if($kadaitext == "提出済み"):?>
                        <a href="../KadaiUpLoad/Home.php?jugyoid=<?php echo $index['jugyoid'];?>">提出物の確認</a><br>
                    <?php endif;?>
                <?php endif;?>
                <span class="subboxitem">課題提出: <?php echo $kadai;?></span>
                <?php if($kadai == "あり" && !$kadaicheck):?>
                    <a href="../KadaiUpLoad/Home.php?jugyoid=<?php echo $index['jugyoid'];?>">提出する</a>
                <?php endif;?>
            </div>
        <?php endforeach;?>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        <?php if(isset($_GET['error']) && $_GET['error'] == "anketo"):?>
            alert("未回答のアンケートを回答してから受講してください");
            location.href = "./Home.php";
        <?php endif;?>

        const videoimg = document.getElementById('videoimg');
        function mouseover(jugyoid){
            $(`#itag${jugyoid}`).addClass('imghover');
        }
        function mouseleave(jugyoid){
            $(`#itag${jugyoid}`).removeClass('imghover');
        }

        function load(){
            location.reload();
        }
    </script>
</body>
</html>