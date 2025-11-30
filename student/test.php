<?php
require_once __DIR__ . '/../includes/auth_student.php';
// Note: We don't include db.php here to minimize DB connection time and rely on session data

$page_title = "Online Skill Test";

// Check if a test is active
if (!isset($_SESSION['test_active']) || $_SESSION['test_active'] !== true) {
    header("location: dashboard.php");
    exit;
}

$test_questions = $_SESSION['test_questions'];
$duration_minutes = $_SESSION['test_duration_minutes'];
$start_time = $_SESSION['test_start_time'];

// Calculate remaining time
$elapsed_time = time() - $start_time;
$total_duration_seconds = $duration_minutes * 60;
$remaining_time = $total_duration_seconds - $elapsed_time;

// If time has elapsed, auto-submit (should also be handled by JS)
if ($remaining_time <= 0) {
    // Redirect to the submission script which will handle the result calculation
    header("location: submit_test.php?auto=true");
    exit;
}

// Convert remaining time to minutes for JS timer initialization
$initial_minutes = ceil($remaining_time / 60);

include __DIR__ . '/../includes/header.php';
// Note: No navbar for a distraction-free test environment
?>
<div class="test-container">
    <div class="test-header">
        <h1>Skill Test</h1>
        <div style="font-size: 1.5rem; font-weight: bold; padding: 10px 15px; border: 2px solid var(--danger-color); border-radius: 4px;">
            Time Left: <span id="timer" data-duration-minutes="<?php echo $duration_minutes; ?>"><?php echo date('i:s', $remaining_time); ?></span>
        </div>
    </div>

    <form action="submit_test.php" method="post" id="testForm" data-duration-minutes="<?php echo $duration_minutes; ?>">
        <input type="hidden" name="total_questions" value="<?php echo count($test_questions); ?>">
        
        <?php $q_number = 1; ?>
        <?php foreach ($test_questions as $question): ?>
            <div class="question-item">
                <h4><?php echo $q_number; ?>. <?php echo htmlspecialchars($question['text']); ?></h4>
                <ul class="options-list">
                    <?php foreach ($question['options'] as $key => $option_text): ?>
                        <li>
                            <label>
                                <input type="radio" 
                                       name="answer[<?php echo $question['id']; ?>]" 
                                       value="<?php echo htmlspecialchars($key); ?>"
                                       required>
                                <span><?php echo htmlspecialchars($key); ?>: <?php echo htmlspecialchars($option_text); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php $q_number++; ?>
        <?php endforeach; ?>

        <div class="form-group" style="margin-top: 40px; text-align: center;">
            <button type="submit" class="btn btn-success btn-lg">Submit Test and View Result</button>
        </div>
    </form>
</div>

<script src="/online-skill-test-portal/assets/js/timer.js"></script>

<?php 
// Instead of the standard footer, just output the closing tags for a focused test view
if (isset($conn)) {
    close_db_connection($conn);
}
?>
</div> </body>
</html>