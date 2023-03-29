<?php
require('../DBconnect.php');
session_start();

if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherLogin/Login.php');
    exit;
}else{
    $students = $db->query('SELECT * FROM account');
}
if(!empty($_GET)){
    if(empty($_GET['grade']) && empty($_GET['gakubu'])){
        $students = $db->query('SELECT * FROM account');
    }else if(!empty($_GET['grade']) && empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE grade=?");
        $check->execute(array($_GET['grade']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $students = $db->prepare("SELECT * FROM account WHERE grade=?");
            $students->execute(array($_GET['grade']));
        }else{
            $students = [];
        }
    }else if(empty($_GET['grade']) && !empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE gakubu=?");
        $check->execute(array($_GET['gakubu']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $students = $db->prepare("SELECT * FROM account WHERE gakubu=?");
            $students->execute(array($_GET['gakubu']));
        }else{
            $students = [];
        }
    }else if(!empty($_GET['grade']) && !empty($_GET['gakubu'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE grade=? AND gakubu=?");
        $check->execute(array($_GET['grade'], $_GET['gakubu']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $students = $db->prepare("SELECT * FROM account WHERE grade=? AND gakubu=?");
            $students->execute(array($_GET['grade'], $_GET['gakubu']));
        }else{
            $students = [];
        }
    }
    if(!empty($_GET['studentnumber'])){
        $check = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE studentnumber=?");
        $check->execute(array($_GET['studentnumber']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            $students = $db->prepare("SELECT * FROM account WHERE studentnumber=?");
            $students->execute(array($_GET['studentnumber']));
        }else{
            $students = [];
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
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="./ViewData.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>受講状況参照画面</title>
</head>
<body>
<div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">受講状況一覧</span>
        <span id="selectboxtitle">条件:</span>
        <select name="grade" id="gradeselect" onchange="dataselect()"></select>
        <select name="gakubu" id="gakubuselect" onchange="dataselect()"></select>
        <input id="inputstudentnumber" type="text" name="studentnumber" maxlength="200" placeholder="学籍番号で検索"
         value="<?php if(!empty($_GET)){echo $_GET['studentnumber'];}?>">
        <button onclick="searchstudentnumber()" id="searchbutton">検索</button>
        <?php include("../nav.php");?>
    </div>
    <div class="box">
        <div class="subbox">
            <span class="column">学籍番号</span>
            <span class="column">氏名</span>
            <span class="column">学年</span>
            <span class="column">学部</span>
            <span class="column">受講数</span>
            <span class="column">受講済みの授業ID</span>
            <span class="column">未受講の授業ID</span>
        </div>
        <?php foreach($students as $index):?>
            <?php
            $allcount = $db->prepare("SELECT COUNT(*) AS cnt FROM video v
            WHERE grade=? AND gakubu=?");
            $allcount->execute(array($index['grade'], $index['gakubu']));
            $allcount = $allcount->fetch();

            $count = $db->prepare('SELECT COUNT(*) AS cnt FROM video v, jukodata j 
            WHERE v.jugyoid=j.jugyoid AND j.studentnumber=?');
            $count->execute(array($index['studentnumber']));
            $count = $count->fetch();

            $jukodatacount = $db->query('SELECT COUNT(*) AS cnt FROM jukodata');
            $jukodatacount = $jukodatacount->fetch();
            $notcompleteID = [];
            if($jukodatacount['cnt'] <= 0){
                $notcompleteID = $db->prepare('SELECT v.* FROM video v WHERE v.grade=? AND v.gakubu=?');
                $notcompleteID->execute(array($index['grade'], $index['gakubu']));
            }else{# EXISTS文は副問い合わせの結果が1行でもあれば真を返す
                $videos = $db->prepare("SELECT * FROM video WHERE grade=? AND gakubu=?");
                $videos->execute(array($index['grade'], $index['gakubu']));
                foreach($videos as $index2){
                    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM jukodata WHERE jugyoid=? AND studentnumber=?");
                    $check->execute(array($index2['jugyoid'], $index['studentnumber']));
                    $check = $check->fetch();
                    if($check['cnt'] <= 0){
                        array_push($notcompleteID, $index2['jugyoid']);
                    }
                }
            }
            
            $complateID = $db->prepare("SELECT * FROM jukodata WHERE studentnumber=?");
            $complateID->execute(array($index['studentnumber']));
            ?>
            <div class="subbox">
                <span class="column"><?php echo h($index['studentnumber']);?></span>
                <span class="column"><?php echo h($index['username']);?></span>
                <span class="column"><?php echo h($index['grade']);?></span>
                <span class="column"><?php echo h($index['gakubu']);?></span>
                <span class="column"><?php echo $count['cnt']. "/". $allcount['cnt'];?></span>
                <span class="column">
                    <span>: </span>
                    <?php foreach($complateID as $jugyoid):?>
                        <a href="../TeacherHome/Home.php?jugyoid=<?php echo $jugyoid['jugyoid'];?>"><?php echo $jugyoid['jugyoid'];?></a>
                    <?php endforeach;?>
                </span>
                <span class="column">
                    <span>: </span>
                    <?php foreach($notcompleteID as $jugyoid):?>
                        <a href="../TeacherHome/Home.php?jugyoid=<?php echo $jugyoid['jugyoid'];?>"><?php echo $jugyoid['jugyoid'];?></a>
                    <?php endforeach;?>
                </span>
            </div>
        <?php endforeach;?>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script src="../grade&gakubu.js"></script>
    <script>
        'use strict';

        function dataselect(){
            let grade = document.getElementById('gradeselect').value;
            let gakubu = document.getElementById('gakubuselect').value;
            location.href = "./ViewData.php?grade="+grade+"&gakubu="+gakubu+"&studentnumber=";
        }

        function searchstudentnumber(){
            let grade = document.getElementById('gradeselect').value;
            let gakubu = document.getElementById('gakubuselect').value;
            const studentnumber = document.getElementById('inputstudentnumber').value;
            if(studentnumber != ""){
                grade = "";
                gakubu = "";
            }
            location.href = "./ViewData.php?grade=&gakubu=&studentnumber="+studentnumber;
        }
    </script>
</body>
</html>