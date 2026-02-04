<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$message = "";

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $duration = (int)($_POST['test_duration'] ?? 600);
    $num = (int)($_POST['num_questions'] ?? 10);

    if ($duration <= 0 || $num <= 0) {
        $message = "Values must be positive.";
    } else {
        $id = (int)$_SESSION['admin_id'];
        $stmt = $conn->prepare("UPDATE admin SET test_duration=?, num_questions=? WHERE id=?");
        $stmt->bind_param("iii", $duration, $num, $id);
        if ($stmt->execute()) $message = "Settings updated.";
        else $message = "Error: ".$conn->error;
        $stmt->close();
    }
}

$settings = $conn->query("SELECT test_duration, num_questions FROM admin WHERE id=".(int)$_SESSION['admin_id'])->fetch_assoc();

$pageTitle = "Test Settings";
include '../includes/header.php';
?>
<h2 class="page-title">Test Settings</h2>
<?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<form method="post" class="glass-card form-grid">
    <label>Test Duration (seconds)
        <input type="number" name="test_duration" min="30" value="<?php echo (int)$settings['test_duration']; ?>" required>
    </label>
    <label>Number of Questions
        <input type="number" name="num_questions" min="1" value="<?php echo (int)$settings['num_questions']; ?>" required>
    </label>
    <button type="submit" class="btn primary full">Save Settings</button>
</form>
<?php include '../includes/footer.php'; ?>
