<?php
require("../DBconnect.php");
session_start();

if(!isset($_SESSION['teachernumber'])){
    header('Location: ../Home/Home.php');
    exit;
}
if(isset($_GET['questionid'])){
    $check = $db->prepare('SELECT COUNT(*) AS cnt FROM question WHERE questionid=? AND teachernumber=?');
    $check->execute(array($_GET['questionid'], $_SESSION['teachernumber']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $jugyoid = $db->prepare('SELECT * FROM question WHERE questionid=? AND teachernumber=?');
        $jugyoid->execute(array($_GET['questionid'], $_SESSION['teachernumber']));
        $jugyoid = $jugyoid->fetch();
        $updata = $db->prepare('UPDATE question SET teachernumber=NULL, answer=NULL, answertime=NULL');
        $updata->execute(array());
        header('Location: ./QuestionAnswer.php?jugyoid='. $jugyoid['jugyoid']);
        exit;
    }
}
?>