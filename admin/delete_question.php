<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM questions WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$stmt->close();
header("Location: questions.php");
exit;
