<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}
$studentId = $_SESSION['student_id'];
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT score, total_questions, correct_answers, test_date 
                        FROM results WHERE id=? AND student_id=?");
$stmt->bind_param("ii", $id, $studentId);
$stmt->execute();
$stmt->bind_result($score, $total, $correct, $date);
if (!$stmt->fetch()) {
    $stmt->close();
    $pageTitle = "Result";
    include '../includes/header.php';
    echo "<div class='alert'>Result not found.</div>";
    include '../includes/footer.php';
    exit;
}
$stmt->close();

$pageTitle = "Result";
include '../includes/header.php';
?>
<h2 class="page-title">Test Result</h2>
<div class="glass-card">
    <p><strong>Date:</strong> <?php echo htmlspecialchars($date); ?></p>
    <p><strong>Score:</strong> <?php echo $score; ?>%</p>
    <p><strong>Correct:</strong> <?php echo $correct."/".$total; ?></p>
</div>

<p>
    <a href="results_history.php" class="btn ghost">All Results</a>
    <a href="dashboard.php" class="btn subtle">Back to Dashboard</a>
</p>
<?php include '../includes/footer.php'; ?>
