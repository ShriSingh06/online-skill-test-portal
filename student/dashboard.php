<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}

$studentId = $_SESSION['student_id'];

// Latest result
$latest = null;
$stmt = $conn->prepare("SELECT score, total_questions, correct_answers, test_date 
                        FROM results WHERE student_id = ? 
                        ORDER BY test_date DESC LIMIT 1");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stmt->bind_result($score, $total, $correct, $date);
if ($stmt->fetch()) {
    $latest = compact('score','total','correct','date');
}
$stmt->close();

$pageTitle = "Student Dashboard";
include '../includes/header.php';
?>
<h2 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></h2>

<div class="grid">
    <div class="glass-card">
        <h3>Attempt Online Test</h3>
        <p>Timed MCQ test with auto-submit when the timer ends.</p>
        <a class="btn primary" href="test.php">Start Test</a>
    </div>

    <div class="glass-card">
        <h3>Your Profile</h3>
        <p>Basic profile and account info.</p>
        <a class="btn ghost" href="profile.php">View Profile</a>
    </div>

    <div class="glass-card">
        <h3>Results History</h3>
        <p>Check your previous performance and scores.</p>
        <a class="btn ghost" href="results_history.php">View Results</a>
    </div>

    <div class="glass-card">
        <h3>Latest Result</h3>
        <?php if ($latest): ?>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($latest['date']); ?></p>
            <p><strong>Score:</strong> <?php echo $latest['score']; ?>%</p>
            <p><strong>Correct:</strong> <?php echo $latest['correct']."/".$latest['total']; ?></p>
        <?php else: ?>
            <p>No tests taken yet.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
