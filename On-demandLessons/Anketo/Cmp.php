<?php
session_start();
if(!isset($_SESSION['studentnumber'])){
    header('Location: ../Home/Home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アンケート完了</title>
    <link rel="stylesheet" href="./Anketo.css">
</head>
<body>
    <div class="subbox">
        <span class="title">ご回答ありがとうございました!<br><a href="../Home/Home.php">授業一覧に戻る</a></span>
    </div>
</body>
</html>