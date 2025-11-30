<?php
require_once __DIR__ . '/../includes/auth_student.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "My Test Results";
$student_id = $_SESSION['student_id'];
$last_result = null;
$all_results = [];
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : null;

// Define a simple pass threshold
$PASS_THRESHOLD = 40.0;

// 1. Fetch the last/specific result (if provided via GET)
$sql_last = "SELECT * FROM results WHERE student_id = ? ORDER BY taken_at DESC LIMIT 1";

if ($last_id) {
    // If a specific ID is requested, fetch that one first
    $sql_last_specific = "SELECT * FROM results WHERE id = ? AND student_id = ?";
    if ($stmt_last = $conn->prepare($sql_last_specific)) {
        $stmt_last->bind_param("ii", $last_id, $student_id);
        if ($stmt_last->execute()) {
            $result = $stmt_last->get_result();
            if ($result->num_rows == 1) {
                $last_result = $result->fetch_assoc();
            }
        }
        $stmt_last->close();
    }
}

// If no specific or initial fetch failed, get the latest result
if (!$last_result) {
    if ($stmt_last = $conn->prepare($sql_last)) {
        $stmt_last->bind_param("i", $student_id);
        if ($stmt_last->execute()) {
            $result = $stmt_last->get_result();
            if ($result->num_rows == 1) {
                $last_result = $result->fetch_assoc();
            }
        }
        $stmt_last->close();
    }
}

// 2. Fetch list of previous test attempts
$sql_all = "SELECT id, correct_answers, total_questions, percentage, taken_at FROM results WHERE student_id = ? ORDER BY taken_at DESC";
if ($stmt_all = $conn->prepare($sql_all)) {
    $stmt_all->bind_param("i", $student_id);
    if ($stmt_all->execute()) {
        $result = $stmt_all->get_result();
        while ($row = $result->fetch_assoc()) {
            $all_results[] = $row;
        }
    }
    $stmt_all->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_student.php';
?>

<div class="main-content">
    <h1>My Test Results</h1>
    
    <?php if (isset($_SESSION['test_error'])): ?>
        <div class="alert alert-danger">
            <strong>Error:</strong> <?php echo htmlspecialchars($_SESSION['test_error']); ?>
        </div>
        <?php unset($_SESSION['test_error']); ?>
    <?php endif; ?>

    <h2><?php echo $last_id ? 'Last Submitted Test' : 'Latest Test Result'; ?></h2>
    <?php if ($last_result): ?>
        <?php 
            $is_passed = $last_result['percentage'] >= $PASS_THRESHOLD;
            $status_class = $is_passed ? 'passed' : 'failed';
        ?>
        <div class="card <?php echo $status_class; ?>" style="border-left-width: 10px;">
            <h3 style="margin-top: 0; color: var(--text-color);">
                Test Taken on: <?php echo date('F j, Y, h:i A', strtotime($last_result['taken_at'])); ?>
            </h3>

            <div class="result-summary">
                <div class="result-stat">
                    <h4>Score</h4>
                    <div class="value"><?php echo $last_result['correct_answers'] . '/' . $last_result['total_questions']; ?></div>
                </div>
                <div class="result-stat">
                    <h4>Percentage</h4>
                    <div class="value <?php echo $status_class; ?>"><?php echo number_format($last_result['percentage'], 2) . '%'; ?></div>
                </div>
                <div class="result-stat">
                    <h4>Result</h4>
                    <div class="value <?php echo $status_class; ?>"><?php echo $is_passed ? 'PASS' : 'FAIL'; ?></div>
                </div>
            </div>
            
            <p style="text-align: right;">
                <a href="#" onclick="window.print(); return false;" class="btn btn-secondary btn-sm" style="background-color: var(--secondary-color); color: white;">Print Result (Minimal)</a>
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">You have not taken any tests yet.</div>
    <?php endif; ?>
    
    <hr>

    <h2>Previous Test Attempts (<?php echo count($all_results); ?>)</h2>
    <?php if (!empty($all_results)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_results as $r): ?>
                    <?php $is_passed = $r['percentage'] >= $PASS_THRESHOLD; ?>
                    <tr <?php if ($last_result && $r['id'] == $last_result['id']) echo 'style="background-color: #e6f7ff; font-weight: bold;"'; ?>>
                        <td><?php echo date('Y-m-d H:i', strtotime($r['taken_at'])); ?></td>
                        <td><?php echo $r['correct_answers'] . '/' . $r['total_questions']; ?></td>
                        <td><?php echo number_format($r['percentage'], 2) . '%'; ?></td>
                        <td>
                            <span style="font-weight: bold; color: <?php echo $is_passed ? 'var(--success-color)' : 'var(--danger-color)'; ?>">
                                <?php echo $is_passed ? 'PASS' : 'FAIL'; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>