<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}

$studentsCount  = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'] ?? 0;
$questionsCount = $conn->query("SELECT COUNT(*) c FROM questions")->fetch_assoc()['c'] ?? 0;
$testsCount     = $conn->query("SELECT COUNT(*) c FROM results")->fetch_assoc()['c'] ?? 0;
$settings = $conn->query("SELECT test_duration, num_questions FROM admin WHERE id=".(int)$_SESSION['admin_id'])->fetch_assoc();

$pageTitle = "Admin Dashboard";
include '../includes/header.php';
?>
<h2 class="page-title">Admin Dashboard</h2>

<div class="grid">
    <div class="glass-card stat">
        <span class="stat-label">Students</span>
        <span class="stat-value"><?php echo $studentsCount; ?></span>
    </div>
    <div class="glass-card stat">
        <span class="stat-label">Questions</span>
        <span class="stat-value"><?php echo $questionsCount; ?></span>
    </div>
    <div class="glass-card stat">
        <span class="stat-label">Tests Taken</span>
        <span class="stat-value"><?php echo $testsCount; ?></span>
    </div>
</div>

<div class="grid">
    <div class="glass-card">
        <h3>Question Bank</h3>
        <p>Add, edit and delete MCQ questions.</p>
        <a href="add_question.php" class="btn primary">Add Question</a>
        <a href="questions.php" class="btn ghost">View All</a>
    </div>

    <div class="glass-card">
        <h3>Students & Results</h3>
        <p>Monitor students and their performance.</p>
        <a href="students.php" class="btn ghost">Students</a>
        <a href="results.php" class="btn subtle">Results</a>
    </div>

    <div class="glass-card">
        <h3>Test Settings</h3>
        <p>Duration: <?php echo (int)$settings['test_duration']; ?>s<br>
           Questions per test: <?php echo (int)$settings['num_questions']; ?></p>
        <a href="settings.php" class="btn ghost">Edit Settings</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
