<?php
require_once __DIR__ . '/../includes/auth_student.php'; // Session guard
require_once __DIR__ . '/../config/db.php'; // DB connection

$page_title = "Student Dashboard";
$student_id = $_SESSION['student_id'];
$stats = [
    'total_tests_taken' => 0,
    'best_score_percentage' => 0,
    'last_score' => null
];

// 1. Fetch Stats
$sql_stats = "
    SELECT 
        COUNT(id) AS total_tests_taken,
        MAX(percentage) AS best_score_percentage
    FROM results
    WHERE student_id = ?
";
if ($stmt_stats = $conn->prepare($sql_stats)) {
    $stmt_stats->bind_param("i", $student_id);
    if ($stmt_stats->execute()) {
        $result = $stmt_stats->get_result();
        $row = $result->fetch_assoc();
        $stats['total_tests_taken'] = (int)$row['total_tests_taken'];
        $stats['best_score_percentage'] = (float)$row['best_score_percentage'];
    }
    $stmt_stats->close();
}

// 2. Fetch Last Test Score
$sql_last_score = "
    SELECT 
        correct_answers, 
        total_questions
    FROM results
    WHERE student_id = ?
    ORDER BY taken_at DESC
    LIMIT 1
";
if ($stmt_last = $conn->prepare($sql_last_score)) {
    $stmt_last->bind_param("i", $student_id);
    if ($stmt_last->execute()) {
        $result = $stmt_last->get_result();
        if ($result->num_rows > 0) {
            $stats['last_score'] = $result->fetch_assoc();
        }
    }
    $stmt_last->close();
}

// 3. Check Question Count for Test Button
$required_questions = 0;
$actual_questions = 0;

// Fetch settings
$sql_settings = "SELECT questions_per_test FROM settings WHERE id = 1";
if ($result_s = $conn->query($sql_settings)) {
    $settings = $result_s->fetch_assoc();
    $required_questions = $settings['questions_per_test'];
    $result_s->close();
}

// Fetch total number of questions
$sql_count = "SELECT COUNT(id) AS total FROM questions";
if ($result_q = $conn->query($sql_count)) {
    $row = $result_q->fetch_assoc();
    $actual_questions = $row['total'];
    $result_q->close();
}

$can_start_test = $actual_questions >= $required_questions;
$test_button_text = $can_start_test ? 'Start Test Now' : "Insufficient Questions ({$actual_questions}/{$required_questions})";


include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_student.php';
?>

<div class="main-content">
    <h1><span style="color: var(--primary-color);">Welcome back, <?php echo htmlspecialchars($_SESSION['student_full_name']); ?>!</span></h1>
    <p>This is your student dashboard where you can start a test and review your performance.</p>
    
    <hr>

    <h2>Quick Stats</h2>
    <div class="card-grid">
        <div class="card">
            <h4>Total Tests Taken</h4>
            <div class="stat"><?php echo number_format($stats['total_tests_taken']); ?></div>
        </div>
        <div class="card success">
            <h4>Best Score Percentage</h4>
            <div class="stat"><?php echo number_format($stats['best_score_percentage'], 2) . '%'; ?></div>
        </div>
        <div class="card warning">
            <h4>Last Test Score</h4>
            <div class="stat">
                <?php 
                if ($stats['last_score']) {
                    echo $stats['last_score']['correct_answers'] . '/' . $stats['last_score']['total_questions'];
                } else {
                    echo 'N/A';
                }
                ?>
            </div>
        </div>
    </div>
    
    <hr>

    <h2>Ready to Test Your Skills?</h2>
    <div class="card">
        <p style="font-size: 1.1em; margin-bottom: 20px;">
            A new test will contain **<?php echo $required_questions; ?>** questions. Good luck!
        </p>
        <a 
            href="start_test.php" 
            class="btn btn-primary" 
            id="startTestBtn"
            data-question-count="<?php echo $actual_questions; ?>"
            data-required-questions="<?php echo $required_questions; ?>"
            <?php if (!$can_start_test) echo 'disabled style="pointer-events: none; opacity: 0.6;"'; ?>
        >
            <?php echo $test_button_text; ?>
        </a>
    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>