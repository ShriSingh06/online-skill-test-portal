<?php
require_once __DIR__ . '/../includes/auth_student.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Test Submission";
$student_id = $_SESSION['student_id'];
$score = [
    'total_questions' => 0,
    'correct_answers' => 0,
    'wrong_answers' => 0,
    'percentage' => 0.00
];
$error = "";

// Check if a test session exists
if (!isset($_SESSION['test_active']) || $_SESSION['test_active'] !== true || !isset($_SESSION['test_correct_answers'])) {
    $error = "No active test found or session expired. Please start a new test.";
    // If no active test, redirect to dashboard
    if (basename($_SERVER['PHP_SELF']) == 'submit_test.php') {
        $_SESSION['test_error'] = $error;
        header("location: dashboard.php");
        exit;
    }
} else {
    // Get data from session
    $correct_answers_map = $_SESSION['test_correct_answers'];
    $score['total_questions'] = count($correct_answers_map);
    $submitted_answers = isset($_POST['answer']) ? $_POST['answer'] : [];

    // Evaluate answers
    foreach ($correct_answers_map as $q_id => $correct_option) {
        $student_answer = isset($submitted_answers[$q_id]) ? trim($submitted_answers[$q_id]) : null;

        // Ensure the answer is valid (A, B, C, D) to prevent simple manipulation
        if ($student_answer && in_array($student_answer, ['A', 'B', 'C', 'D'])) {
            if ($student_answer === $correct_option) {
                $score['correct_answers']++;
            } else {
                $score['wrong_answers']++;
            }
        } else {
            // Treat unanswered or invalid as wrong
            $score['wrong_answers']++;
        }
    }

    // Calculate percentage
    if ($score['total_questions'] > 0) {
        $score['percentage'] = ($score['correct_answers'] / $score['total_questions']) * 100;
    } else {
        $score['percentage'] = 0;
    }

    // Store the result in the database
    $sql = "INSERT INTO results (student_id, total_questions, correct_answers, wrong_answers, percentage) 
            VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiiid", 
            $student_id, 
            $score['total_questions'], 
            $score['correct_answers'], 
            $score['wrong_answers'], 
            $score['percentage']
        );
        
        if (!$stmt->execute()) {
            $error = "Error storing result: " . $stmt->error;
        } else {
            // Save the newly created result ID
            $new_result_id = $stmt->insert_id;
        }

        $stmt->close();
    } else {
        $error = "Database error: Unable to prepare statement for saving result.";
    }

    // Clear the active test session variables regardless of success/fail
    unset($_SESSION['test_active']);
    unset($_SESSION['test_questions']);
    unset($_SESSION['test_correct_answers']);
    unset($_SESSION['test_duration_minutes']);
    unset($_SESSION['test_start_time']);

    // Redirect to results page to show the specific result
    if (isset($new_result_id)) {
        header("location: results.php?last_id=" . $new_result_id);
        exit;
    }
}

// If something went wrong and we couldn't redirect to results.php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_student.php';
?>

<div class="main-content">
    <h1>Test Submission Status</h1>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
        <p>Please return to the <a href="dashboard.php">Dashboard</a>.</p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>