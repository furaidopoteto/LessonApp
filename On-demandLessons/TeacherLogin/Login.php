<?php
require('../DBconnect.php');
session_start();
if(isset($_COOKIE['teachernumber']) && isset($_COOKIE['pw']) && isset($_COOKIE['username'])){
    $_POST['teachernumber'] = $_COOKIE['teachernumber'];
    $_POST['pw'] = $_COOKIE['pw'];
    $_POST['username'] = $_COOKIE['username'];
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
    
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM teacheraccount WHERE teachernumber=? AND password=? AND username=?");
    $pw = $_POST['pw'];
    for($i = 0;$i<1000;$i++){
        $pw = sha1($pw);
    }
    $check->execute(array($_POST['teachernumber'], $pw, $_POST['username']));
    $check = $check->fetch();
    if($check['cnt'] == 1){
        if(!empty($_POST['checkbox'])){
            setcookie("teachernumber", $_POST['teachernumber'], time()+60*60*24*7);
            setcookie("pw", $_POST['pw'], time()+60*60*24*7);
            setcookie("username", $_POST['username'], time()+60*60*24*7);
            setcookie("grade", $_POST['grade'], time()+60*60*24*7);
        }
        $_SESSION['teachernumber'] = $_POST['teachernumber'];
        header('Location: ../TeacherHome/Home.php');
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
        <span class="title">教職員ログイン</span><br>
        <?php if(!empty($_POST) && $error['pw'] == "error"):?>
            <span class="error">* 入力された情報は登録されていません</span><br>
        <?php endif;?>
        <form action="" method="POST" id="form">
            <input class="inputform" type="text" name="teachernumber" placeholder="教員番号" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['teachernumber']);}?>" onkeyup="formcheck()" required><br>
            <input class="inputform" type="text" name="username" placeholder="氏名" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['username']);}?>" onkeyup="formcheck()" required><br>
            <input class="inputform" type="password" name="pw" placeholder="パスワード※4文字以上" onkeyup="formcheck()" required><br>
            <label class="label">次回以降も自動ログインする<input type="checkbox" name="checkbox" value="on"  class="checkbox"></label><br>
            <button type="submit" class="submitbutton" disabled><i class="fa-solid fa-arrow-right"></i></button><br>
        </form>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="./Login.js"></script>
</body>
</html>