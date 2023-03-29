<?php
require("../DBconnect.php");
session_start();

if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Home/Home.php');
    exit;
}
if(isset($_GET['questionid'])){
    $check = $db->prepare('SELECT COUNT(*) AS cnt FROM question WHERE questionid=? AND studentnumber=?');
    $check->execute(array($_GET['questionid'], $_SESSION['studentnumber']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $jugyoid = $db->prepare('SELECT * FROM question WHERE questionid=? AND studentnumber=?');
        $jugyoid->execute(array($_GET['questionid'], $_SESSION['studentnumber']));
        $jugyoid = $jugyoid->fetch();
        $delete = $db->prepare('DELETE FROM question WHERE questionid=? AND studentnumber=?');
        $delete->execute(array($_GET['questionid'], $_SESSION['studentnumber']));
        header('Location: ./Question.php?jugyoid='. $jugyoid['jugyoid']);
        exit;
    }
}
?>