<div class="warpper">
    <div class="hanbagubutton">
        <span class="top"></span>
        <span class="center"></span>
        <span class="bottom"></span>
    </div>
    <ul class="nav">
        <li class="navlist"><?php echo h($_SESSION['teachernumber']);?>でログイン中</li>
        <li class="navlist"><a class="navlist" href="../TeacherHome/Home.php">投稿済み授業一覧</a></li>
        <li class="navlist"><a class="navlist" href="../UploadVideo/UploadVideo.php">授業動画を投稿する</a></li>
        <li class="navlist"><a class="navlist" href="../ViewData/ViewData.php">生徒の受講状況を参照</a></li>
        <li class="navlist"><a class="navlist" href="../ViewGraph/ViewGraph.php">アンケートグラフ</a></li>
        <li class="navlist"><a class="navlist" href="../Logout/Logout.php?user=teacher">ログアウト</a></li>
    </ul>
</div>