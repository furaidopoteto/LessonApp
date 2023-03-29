<?php
if(isset($_GET)){
    session_start();
    setcookie('username', null, time()-3600);//期限をマイナスにすればすぐに削除される
    setcookie('pw', null, time()-3600);
    setcookie('studentnumber', null, time()-3600);
    setcookie('teachernumber', null, time()-3600);
    setcookie('grade', null, time()-3600);
    $_SESSION = array();
    session_destroy();
    if($_GET['user'] == 'teacher'){
        header('Location: ../TeacherHome/Home.php');
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