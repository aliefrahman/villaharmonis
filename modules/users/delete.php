<?php
session_start();
require '../../config/db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

if(isset($_GET['id']) && $_GET['id'] != $_SESSION['user_id']) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: index.php?msg=" . urlencode("Pengguna berhasil dihapus."));
exit;
?>
