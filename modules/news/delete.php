<?php
session_start();
require '../../config/db.php';
require '../../functions/upload.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$role = $_SESSION['role'];
if($role === 'user') {
    header("Location: ../dashboard/index.php");
    exit;
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get image and check ownership if kontributor
    if($role === 'kontributor') {
        $stmt = $conn->prepare("SELECT image, author_id FROM news WHERE id=?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();
        if($news && $news['author_id'] == $_SESSION['user_id']) {
            deleteNewsImage($news['image']);
            $stmt = $conn->prepare("DELETE FROM news WHERE id=?");
            $stmt->execute([$id]);
        }
    } else {
        $stmt = $conn->prepare("SELECT image FROM news WHERE id=?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();
        if($news) {
            deleteNewsImage($news['image']);
            $stmt = $conn->prepare("DELETE FROM news WHERE id=?");
            $stmt->execute([$id]);
        }
    }
}
header("Location: index.php?msg=" . urlencode("Berita berhasil dihapus."));
exit;
?>
