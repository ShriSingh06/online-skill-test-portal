<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$pageTitle = "Add Question";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q = trim($_POST['question'] ?? '');
    $a = trim($_POST['option_a'] ?? '');
    $b = trim($_POST['option_b'] ?? '');
    $c = trim($_POST['option_c'] ?? '');
    $d = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';

    if ($q===""||$a===""||$b===""||$c===""||$d===""||$correct==="") {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option)
                                VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $q, $a, $b, $c, $d, $correct);
        if ($stmt->execute()) $message = "Question added.";
        else $message = "Error: ".$conn->error;
        $stmt->close();
    }
}

include '../includes/header.php';
?>
<h2 class="page-title">Add MCQ Question</h2>
<?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<form method="post" class="glass-card form-grid">
    <label>Question
        <textarea name="question" required></textarea>
    </label>
    <label>Option A
        <input type="text" name="option_a" required>
    </label>
    <label>Option B
        <input type="text" name="option_b" required>
    </label>
    <label>Option C
        <input type="text" name="option_c" required>
    </label>
    <label>Option D
        <input type="text" name="option_d" required>
    </label>
    <label>Correct Option
        <select name="correct_option" required>
            <option value="">Select</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
    </label>

    <button type="submit" class="btn primary full">Save</button>
</form>
<?php include '../includes/footer.php'; ?>
