<?php
require_once __DIR__ . '/../includes/auth_admin.php'; // Session guard
require_once __DIR__ . '/../config/db.php'; // DB connection

$page_title = "Admin Dashboard";

// Fetch Dashboard Stats
$total_students = 0;
$total_questions = 0;
$total_tests_taken = 0;
$latest_results = [];

// 1. Total Students
$sql_students = "SELECT COUNT(id) AS total_students FROM students";
if ($result = $conn->query($sql_students)) {
    $row = $result->fetch_assoc();
    $total_students = $row['total_students'];
    $result->close();
}

// 2. Total Questions
$sql_questions = "SELECT COUNT(id) AS total_questions FROM questions";
if ($result = $conn->query($sql_questions)) {
    $row = $result->fetch_assoc();
    $total_questions = $row['total_questions'];
    $result->close();
}

// 3. Total Tests Taken (Results)
$sql_results_count = "SELECT COUNT(id) AS total_tests_taken FROM results";
if ($result = $conn->query($sql_results_count)) {
    $row = $result->fetch_assoc();
    $total_tests_taken = $row['total_tests_taken'];
    $result->close();
}

// 4. Latest Results Summary (Top 5)
$sql_latest_results = "
    SELECT 
        r.correct_answers, 
        r.total_questions, 
        r.percentage, 
        r.taken_at, 
        s.full_name
    FROM results r
    JOIN students s ON r.student_id = s.id
    ORDER BY r.taken_at DESC
    LIMIT 5
";
if ($result = $conn->query($sql_latest_results)) {
    while ($row = $result->fetch_assoc()) {
        $latest_results[] = $row;
    }
    $result->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1><span style="color: var(--primary-color);">Hello, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span></h1>
    <h2>Dashboard Overview</h2>
    
    <div class="card-grid">
        <div class="card">
            <h4>Total Students</h4>
            <div class="stat"><?php echo number_format($total_students); ?></div>
        </div>
        <div class="card warning">
            <h4>Total Questions</h4>
            <div class="stat"><?php echo number_format($total_questions); ?></div>
        </div>
        <div class="card success">
            <h4>Total Tests Taken</h4>
            <div class="stat"><?php echo number_format($total_tests_taken); ?></div>
        </div>
        <div class="card danger">
            <h4>Avg. Percentage (Not Implemented)</h4>
            <div class="stat">N/A</div>
        </div>
    </div>
    
    <hr>

    <h2>Latest Test Results</h2>
    <?php if (empty($latest_results)): ?>
        <div class="alert alert-warning">No test results found yet.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latest_results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['full_name']); ?></td>
                    <td><?php echo $result['correct_answers'] . '/' . $result['total_questions']; ?></td>
                    <td><?php echo number_format($result['percentage'], 2) . '%'; ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($result['taken_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top: 15px;"><a href="results_list.php">View all results &rarr;</a></p>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>