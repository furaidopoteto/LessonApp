<?php
require('../DBconnect.php');
session_start();
if(isset($_COOKIE['studentnumber']) && isset($_COOKIE['pw']) && isset($_COOKIE['username']) && isset($_COOKIE['grade']) && isset($_COOKIE['gakubu'])){
    $_POST['studentnumber'] = $_COOKIE['studentnumber'];
    $_POST['pw'] = $_COOKIE['pw'];
    $_POST['username'] = $_COOKIE['username'];
    $_POST['grade'] = $_COOKIE['grade'];
    $_POST['gakubu'] = $_COOKIE['gakubu'];
}
if(!isset($_COOKIE['misscount'])){
    setcookie('misscount', 0, time()+60*60*24);
}else{
    if($_COOKIE['misscount'] >= 5){
        header('Location: Lock.php');
        exit;
    }
}
if(!empty($_POST)){
    $error['pw'] = "";
    
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE studentnumber=? AND password=? AND username=? AND grade=? AND gakubu=?");
    $pw = $_POST['pw'];
    for($i = 0;$i<1000;$i++){
        $pw = sha1($pw);
    }
    $check->execute(array($_POST['studentnumber'], $pw, $_POST['username'], $_POST['grade'], $_POST['gakubu']));
    $check = $check->fetch();
    if($check['cnt'] == 1){
        if(!empty($_POST['checkbox'])){
            setcookie("studentnumber", $_POST['studentnumber'], time()+60*60*24*7);
            setcookie("pw", $_POST['pw'], time()+60*60*24*7);
            setcookie("username", $_POST['username'], time()+60*60*24*7);
            setcookie("grade", $_POST['grade'], time()+60*60*24*7);
            setcookie("gakubu", $_POST['gakubu'], time()+60*60*24*7);
        }
        $_SESSION['studentnumber'] = $_POST['studentnumber'];
        header('Location: ../Home/Home.php');
        exit;
    }else{
        $_COOKIE['misscount']++;
        setcookie('misscount', $_COOKIE['misscount'], time()+60*60);
        if($_COOKIE['misscount'] >= 5){
            header('Location: Lock.php');
            exit;
        }
        $error['pw'] = "error";
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
    <link rel="stylesheet" href="./LoginStyle.css">
    <title>ログイン</title>
</head>
<body>
    <div class="box">
        <span class="title">ログイン</span><br>
        <?php if(!empty($_POST) && $error['pw'] == "error"):?>
            <span class="error">* 入力された情報は登録されていません</span><br>
        <?php endif;?>
        <form action="" method="POST" id="form">
            <span class="gradetitle">学年</span>
            <select name="grade" class="gradeselect" id="gradeselect"></select><br>
            <span class="gakubutitle">学部</span>
            <select name="gakubu" id="gakubuselect"></select><br>
            <input class="inputform" type="text" name="studentnumber" placeholder="学籍番号" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['studentnumber']);}?>" onkeyup="formcheck()" required><br>
            <input class="inputform" type="text" name="username" placeholder="氏名" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['username']);}?>" onkeyup="formcheck()" required><br>
            <input class="inputform" type="password" name="pw" placeholder="パスワード※4文字以上" onkeyup="formcheck()" required><br>
            <label class="label">次回以降も自動ログインする<input type="checkbox" name="checkbox" value="on"  class="checkbox"></label><br>
            <button type="submit" class="submitbutton" disabled><i class="fa-solid fa-arrow-right"></i></button><br>
            <h3><a href="../CreateAccount/Account.php" class="footerbutton">未登録の方はこちら</a></h3>
        </form>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="./Login.js"></script>
    <script>
        'use strict';

        const gradeselect = document.getElementById('gradeselect');
        const gakubuselect = document.getElementById('gakubuselect');
        for(let i = 1;i<=4;i++){
            gradeselect.insertAdjacentHTML("beforeend", `<option value=${i}>${i}年</option>`);
        }

        const gakubu = ["情報学部", "工学部", "教育学部", "経済学部"];
        for(let i in gakubu){
            gakubuselect.insertAdjacentHTML("beforeend", `<option value="${gakubu[i]}">${gakubu[i]}</option>`);
        }
    </script>
</body>
</html>