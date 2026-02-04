<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}

// Settings
$duration = 600;
$numQuestions = 10;
$res = $conn->query("SELECT test_duration, num_questions FROM admin LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    $duration = (int)$row['test_duration'];
    $numQuestions = (int)$row['num_questions'];
}

// Questions
$questions = [];
$qRes = $conn->query("SELECT * FROM questions ORDER BY RAND() LIMIT ".(int)$numQuestions);
while ($row = $qRes->fetch_assoc()) {
    $questions[] = $row;
}

$_SESSION['test_questions'] = [];
foreach ($questions as $q) {
    $_SESSION['test_questions'][$q['id']] = $q['correct_option'];
}

$pageTitle = "Online Test";
include '../includes/header.php';
?>
<h2 class="page-title">Online Test</h2>

<?php if (empty($questions)): ?>
    <div class="alert">No questions available. Contact admin.</div>
<?php else: ?>
    <div class="test-header glass-card">
        <div><strong>Time Left:</strong> <span id="timer"></span></div>
        <div><strong>Total Questions:</strong> <?php echo count($questions); ?></div>
    </div>

    <form id="testForm" method="post" action="submit_test.php" class="glass-card">
        <?php foreach ($questions as $i => $q): ?>
            <div class="question-block">
                <p class="question-text">
                    Q<?php echo $i+1; ?>. <?php echo htmlspecialchars($q['question_text']); ?>
                </p>
                <div class="options">
                    <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A"> <?php echo htmlspecialchars($q['option_a']); ?></label>
                    <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> <?php echo htmlspecialchars($q['option_b']); ?></label>
                    <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> <?php echo htmlspecialchars($q['option_c']); ?></label>
                    <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> <?php echo htmlspecialchars($q['option_d']); ?></label>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn primary">Submit Test</button>
    </form>

    <script>
        let duration = <?php echo (int)$duration; ?>; // seconds
        function tick() {
            let m = Math.floor(duration / 60);
            let s = duration % 60;
            document.getElementById('timer').textContent =
                String(m).padStart(2,'0') + ":" + String(s).padStart(2,'0');

            if (duration <= 0) {
                document.getElementById('testForm').submit();
            } else {
                duration--;
                setTimeout(tick, 1000);
            }
        }
        tick();
    </script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
