<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherHome/Home.php');
    exit;
}
if(!empty($_GET)){
    $check = $db->prepare('SELECT *,  COUNT(*) AS cnt FROM video WHERE jugyoid=?');
    $check->execute(array($_GET['id']));
    $check = $check->fetch();
    if($check['cnt'] >= 1 && $_SESSION['teachernumber'] == $check['teachernumber']){
        $deletevideo = unlink($check['videopass']);
        $deletesamune = unlink($check['samune']);
        $delete = $db->prepare('DELETE FROM video WHERE jugyoid=?');
        $delete->execute(array($_GET['id']));
        header('Location: ../TeacherHome/Home.php');
        exit;
    }
}
?>