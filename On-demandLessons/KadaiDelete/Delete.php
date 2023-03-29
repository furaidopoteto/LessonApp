<?php
require("../DBconnect.php");
session_start();
if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Login/Login.php');
    exit;
}

if(isset($_GET['jugyoid']) && isset($_GET['studentnumber'])){
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai k, video v WHERE k.jugyoid=? AND k.studentnumber=? AND k.jugyoid=v.jugyoid AND NOW()<=v.simekiri");
    $check->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $select = $db->prepare("SELECT * FROM kadai WHERE jugyoid=? AND studentnumber=?");
        $select->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
        $select = $select->fetch();
        $kadaipass = unlink($select['kadaipass']);
        $delete = $db->prepare("DELETE FROM kadai WHERE jugyoid=? AND studentnumber=?");
        $delete->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
        header('Location: ../KadaiUpLoad/Home.php?jugyoid='. $_GET['jugyoid']);
        exit;
    }else{
        header('Location: ../Home/Home.php');
        exit;
    }
}else{
    header('Location: ../Home/Home.php');
    exit;
}
?>