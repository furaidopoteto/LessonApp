<?php
require('../DBconnect.php');
if(!empty($_POST)){
    $error['pwcheck'] = "";
    $error['studentnumber'] = "";

    if($_POST['pw'] != $_POST['pwcheck']){
        $error['pwcheck'] = "error";
    }

    $searchname = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE studentnumber=?");
    $searchname->execute(array($_POST['studentnumber']));
    $searchname = $searchname->fetch();
    if($searchname['cnt'] > 0){
        $error['studentnumber'] = "error";
    }

    if(empty($error['pwcheck']) && empty($error['studentnumber'])){
        $pw = $_POST['pw'];
        for($i = 0;$i<1000;$i++){
            $pw = sha1($pw);
        }
        $insert = $db->prepare("INSERT INTO account (studentnumber, username, password, grade, gakubu, time) VALUES (?, ?, ?, ?, ?, NOW())");
        $insert->execute(array($_POST['studentnumber'], $_POST['username'], $pw, $_POST['grade'], $_POST['gakubu']));
        header('Location: ../Login/Login.php');
        exit;
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
    <link rel="stylesheet" href="./AccountStyle.css">
    <title>アカウント作成</title>
</head>
<body>
    <div class="box">
        <span class="title">オンデマンド授業用<br>アカウント登録</span>
        <form action="" method="POST" id="form">
            <span class="gradetitle">学年</span>
            <select name="grade" class="gradeselect" id="gradeselect"></select><br>
            <?php if(!empty($_POST) && $error['studentnumber'] == "error"):?>
                <span class="error">* 指定した学籍番号はすでに登録されています</span><br>
            <?php endif;?>
            <span class="gakubutitle">学部</span>
            <select name="gakubu" id="gakubuselect"></select><br>
            <input class="inputform" type="text" name="studentnumber" placeholder="学籍番号" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['studentnumber']);}?>" onkeyup="formcheck()" required><br>
            <input class="inputform" type="text" name="username" placeholder="氏名" maxlength="20" value="<?php if(!empty($_POST)){echo h($_POST['username']);}?>" onkeyup="formcheck()" required><br>
            <?php if(!empty($_POST) && $error['pwcheck'] == "error"):?>
                <span class="error">* パスワード確認の値が一致しません</span><br>
            <?php endif;?>
            <input class="inputform" type="password" name="pw" placeholder="パスワード※4文字以上" onkeyup="formcheck()" required>
            <input class="inputform" type="password" name="pwcheck" placeholder="パスワード確認" onkeyup="formcheck()" required>
            <button type="submit" class="submitbutton" disabled><i class="fa-solid fa-arrow-right"></i></button><br>
            <h3><a href="../Login/Login.php" class="footerbutton">ログイン画面へ</a></h3>
        </form>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="./Account.js"></script>
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