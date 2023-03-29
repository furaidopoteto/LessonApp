<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherLogin/Login.php');
    exit;
}
if(!empty($_POST)){
    $error['videofile'] = "";
    $error['samunefile'] = "";
    $error['simekiri'] = "";

    $videofile = $_FILES['videofile'];
    $samunefile = $_FILES['samunefile'];
    $str = substr($videofile['name'], -4);
    $str2 = substr($samunefile['name'], -4);
    if($str == '.mp4' || $str == 'mpeg' || $str == '.MOV' || $str == '.mov' || $str == '.wmv' ||$str == '.avi' || $str == '.mpg'){
        $filepath = "../Videos&Samune/". time(). $videofile['name'];
    }else{
        $error['videofile'] = 'error';
    }
    if($str2 == "jpeg" || $str2 == ".jpg" || $str2 == ".png" || $str2 == "JPEG" || $str2 == ".gif" || $str2 == ".bmp" || $str2 == "HEIC"){
        $filepath2 = "../Videos&Samune/". time(). $samunefile['name'];
    }else{
        $error['samunefile'] = 'error';
    }

    if($_POST['kadai'] == "ari" && empty($_POST['simekiri'])){
        $error['simekiri'] = "error";
    }
    
    if(empty($error['videofile']) && empty($error['samunefile']) && empty($error['simekiri'])){
        if(empty($_POST['simekiri'])){
            $_POST['simekiri'] = null;
        }
        move_uploaded_file($videofile['tmp_name'], $filepath);
        move_uploaded_file($samunefile['tmp_name'], $filepath2);
        $insert = $db->prepare("INSERT INTO video (title, grade, gakubu, teachernumber, videopass, samune, kadai, kadaitext, simekiri, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, DATE_FORMAT(?, '%Y-%m-%d %H:%i'),  NOW())");
        if($_POST['simekiri'] == null){
            $insert->execute(array($_POST['title'], $_POST['grade'], $_POST['gakubu'], $_SESSION['teachernumber'], $filepath, $filepath2,
            $_POST['kadai'], $_POST['kadaitext'], $_POST['simekiri']));
        }else{
            $insert->execute(array($_POST['title'], $_POST['grade'], $_POST['gakubu'], $_SESSION['teachernumber'], $filepath, $filepath2,
            $_POST['kadai'], $_POST['kadaitext'], $_POST['simekiri']));
        }
        
        header('Location: ../TeacherHome/Home.php');
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
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="./UploadVideo.css">
    <title>授業動画投稿画面</title>
</head>
<body>
<div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">授業動画の投稿</span>
        <?php include("../nav.php");?>
    </div>
    <!-- POST送信できるファイルサイズの初期値が40MBなのでそれより大きい動画や画像をアップロードする場合は
         php.iniファイルを編集する必要がある 参照: https://cravelweb.com/webdesign/wp-customize/wordpress-warning-post-content-length-exceeds-the-limit-of-8388608-bytes -->
    <form action="" method="POST" enctype="multipart/form-data" id="form" onsubmit="return submitcheck()">
        <span class="selecttitle">学年</span>
        <select name="grade" id="gradeselect" required></select><br>
        <span class="selecttitle">学部</span>
        <select name="gakubu" id="gakubuselect" required></select><br>
        <input id="inputtitle" type="text" name="title" placeholder="タイトル" maxlength="200" value="<?php if(!empty($_POST)){echo h($_POST['title']);}?>" required><br>
        <?php if(!empty($_POST) && $error['videofile'] == "error"):?>
            <span class="error">* 動画ファイルを選択してください</span><br>
        <?php endif;?>
        <span class="subtitle">動画を選択</span>
        <input class="inputfile" type="file" name="videofile" accept=".mp4, .mpeg, .MOV, .avi, .mov, .MPEG" required><br>
        <?php if(!empty($_POST) && $error['samunefile'] == "error"):?>
            <span class="error">* 画像ファイルを選択してください</span><br>
        <?php endif;?>
        <span class="subtitle">サムネイルを選択</span>
        <input class="inputfile" type="file" name="samunefile" accept=".jpeg, .jpg, .JPEG, .gif, .png, .bmp, .HEIC" required><br>
        <span class="subtitle">課題の提出</span><br>
        <label class="radiotext">あり<input <?php if(!empty($_POST) && $error['simekiri'] == "error"):?> checked <?php endif;?> class="radiobutton" type="radio" name="kadai" value="ari" required></label>
        <label class="radiotext">なし<input id="nasibutton" class="radiobutton" type="radio" name="kadai" value="nasi" required><br></label>
        <div id="kadaitextbox">
            <span class="subtitle">課題についての説明</span>
            <textarea id="kadaitext" name="kadaitext"><?php if(!empty($_POST)):?><?php echo h($_POST['kadaitext']);?><?php endif;?></textarea><br>
            <?php if(!empty($_POST) && $error['simekiri'] == "error"):?>
                <span class="error">* 締め切り日時が入力されていません</span><br>
            <?php endif;?>
            <span class="subtitle">締め切り:</span>
            <!-- 締め切り日時にrequiredは付けられないのでPHP側でチェックする必要がある -->
            <input type="datetime-local" name="simekiri" id="inputdatetime">
        </div>
        <input id="submitbutton" type="submit" value="投稿">
    </form>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script src="../grade&gakubu.js"></script>
    <script>
        'use strict';

        const form = document.getElementById('form');
        const nasibutton = document.getElementById('nasibutton');
        const kadaitextbox = document.getElementById('kadaitextbox');
        window.onload = function(){
            if(form.kadai.value == "ari"){
                kadaitextbox.style.display = "block";
            }else{
                kadaitextbox.style.display = "none";
            }
        }
        form.addEventListener("change", function(){
            if(form.kadai.value == "ari"){
                kadaitextbox.style.display = "block";
            }else{
                kadaitextbox.style.display = "none";
            }
        });
        function submitcheck(){
            return confirm("動画を投稿しますか？");
        }
    </script>
</body>
</html>