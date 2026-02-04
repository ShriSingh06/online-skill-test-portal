<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}
$studentId = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT name, email, created_at FROM students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stmt->bind_result($name, $email, $created);
$stmt->fetch();
$stmt->close();

$pageTitle = "Profile";
include '../includes/header.php';
?>
<h2 class="page-title">Profile</h2>
<div class="glass-card">
    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Registered on:</strong> <?php echo htmlspecialchars($created); ?></p>
</div>
<?php include '../includes/footer.php'; ?>
