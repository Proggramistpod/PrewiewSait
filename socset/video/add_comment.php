<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../auth.php");
    exit;
}

include_once "../application/db.php";

$video_id = (int)$_POST['video_id'];
$content = trim($_POST['comment_content']);
$author = $_SESSION['login'];

if ($content !== '') {
    $sql = "INSERT INTO comments_for_video (video_id, author_name, content) VALUES ($video_id, '$author', '$content')";
    $conn->query($sql);
}
header("Location: watch_video.php?id=$video_id");
exit;
?>