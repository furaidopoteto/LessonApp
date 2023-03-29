<?php
require('../DBconnect.php');
session_start();

if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Home/Home.php');
    exit;
}
if(!empty($_POST)){
    if($_SESSION['studentnumber'] == $_POST['studentnumber']){
        $check = $db->prepare('SELECT COUNT(*) AS cnt FROM jukodata WHERE studentnumber=? AND jugyoid=?');
        $check->execute(array($_SESSION['studentnumber'], $_POST['jugyoid']));
        $check = $check->fetch();
        if($check['cnt'] >= 1){
            echo "受講データはすでに登録されています";
        }else{
            $userdata = $db->prepare('SELECT * FROM account WHERE studentnumber=?');
            $userdata->execute(array($_SESSION['studentnumber']));
            $userdata = $userdata->fetch();
    
            $insert = $db->prepare("INSERT INTO jukodata (studentnumber, grade, gakubu, jugyoid, jukotime)
            VALUES (?, ?, ?, ?, NOW())");
            $insert->execute(array($_SESSION['studentnumber'], $userdata['grade'], $userdata['gakubu'], $_POST['jugyoid']));
            echo "正常に登録されました";
        }
    }
}
?>