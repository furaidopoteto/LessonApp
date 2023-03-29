<?php
require("../DBconnect.php");
session_start();
if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Login/Login.php');
    exit;
}
if(isset($_GET['jugyoid'])){
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=? AND kadai=?");
    $check->execute(array($_GET['jugyoid'], "ari"));
    $check = $check->fetch();
    $check2 = $db->prepare("SELECT COUNT(*) AS cnt FROM kadai WHERE jugyoid=? AND studentnumber=?");
    $check2->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
    $check2 = $check2->fetch();
    if(!($check['cnt'] > 0)){
        header('Location: ../Home/Home.php');
        exit;
    }

    if(!($check2['cnt'] <= 0)){
        $kadai = $db->prepare("SELECT * FROM kadai WHERE jugyoid=? AND studentnumber=?");
        $kadai->execute(array($_GET['jugyoid'], $_SESSION['studentnumber']));
        $kadai = $kadai->fetch();
    }

    $select = $db->prepare("SELECT *, DATE_FORMAT(simekiri, '%Y-%m-%d %H:%i') as simekirifm FROM video WHERE jugyoid=?");
    $select->execute(array($_GET['jugyoid']));

    $simekiricheck = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=? AND simekiri>=NOW()");
    $simekiricheck->execute(array($_GET['jugyoid']));
    $simekiricheck = $simekiricheck->fetch();
}
if(!empty($_FILES)){
    $file = $_FILES['file'];
    $filepath = "../KadaiFiles/". date("YmdHis") . $file['name'];
    move_uploaded_file($file['tmp_name'], $filepath);
    $insert = $db->prepare("INSERT INTO kadai (jugyoid, studentnumber, kadaipass, filename, time) VALUES (?, ?, ?, ?, NOW())");
    $insert->execute(array($_POST['jugyoid'], $_SESSION['studentnumber'], $filepath, $file['name']));
    header('Location: ./Home.php?jugyoid='. $_GET['jugyoid']);
    exit;
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
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <link rel="stylesheet" href="./Home.css">
    <title>課題提出画面</title>
</head>
<body>
    <div class="header">
        <a href="../Home/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">提出画面</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <div class="warpper">
            <div class="hanbagubutton">
                <span class="top"></span>
                <span class="center"></span>
                <span class="bottom"></span>
            </div>
            <ul class="nav">
                <li class="navlist"><?php echo h($_SESSION['studentnumber']);?>でログイン中</li>
                <li class="navlist"><a class="navlist" href="../Logout/Logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <div class="box">
        <?php foreach($select as $index):?>
            <span class="boxitem">授業名: <?php echo h($index['title']);?></span><br>
            <span class="boxitem">課題についての説明<br></span><br>
            <span class="boxitem"><?php echo h($index['kadaitext']);?></span><br>
            <span class="boxitem">締め切り: <?php echo h($index['simekirifm']);?>まで</span>
            <?php if($simekiricheck['cnt'] <= 0):?>
                <span class="boxitem error">締め切り期限を過ぎたので提出することはできません</span><br>
            <?php endif;?>
            <?php if(!($check2['cnt'] <= 0)):?>
                <span class="boxitem">提出ファイル: <a target="_blank" href="<?php echo h($kadai['kadaipass']);?>"><?php echo h($kadai['filename']);?></a></span><br>
            <?php endif;?>
        <?php endforeach;?>
    </div>
    <?php if($simekiricheck['cnt'] >= 1):?>
        <?php if(!($check2['cnt'] <= 0)):?>
            <input id="resubmitbutton" type="submit" onclick="kadaidelete(<?php echo h($kadai['studentnumber']. ','. $kadai['jugyoid']);?>)" value="再提出する">
        <?php else:?>
            <form id="form" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" value="<?php echo $_GET['jugyoid'];?>" name="jugyoid">
                <p class="fileerror">* ファイルが選択されていません</p>
                <label id="inputfilelabel" for="inputfile"><input id="inputfile" type="file" name="file" required>ファイルを選択</label>
                <p id="filetitle">選択されていません</p><br>
                <input id="submitbutton" type="submit" value="提出">
            </form>
        <?php endif;?>
    <?php endif;?>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        $('#inputfile').on('change', function () {
            var file = $(this).prop('files')[0];
            if(file == undefined){
                $('#filetitle').text("選択されていません");
                $('.fileerror').css("display", "block");
            }else{
                $('#filetitle').text(file.name);
                $('.fileerror').css("display", "none");
            }
        });
        $('#submitbutton').on('click', function(){
            let text = $('#filetitle').text();
            if(text == "選択されていません"){
                $('.fileerror').css("display", "block");
            }else{
                $('.fileerror').css("display", "none");
            }
        });

        function kadaidelete(studentnumber, jugyoid){
            if(confirm("提出済みのファイルが削除され、再度提出できるようになります。\n本当によろしいですか?")){
                location.href = "../KadaiDelete/Delete.php?jugyoid="+jugyoid+"&studentnumber="+studentnumber;
            }
        }

        function load(){
            location.reload();
        }
    </script>
</body>
</html>