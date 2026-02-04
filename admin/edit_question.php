<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$id = (int)($_GET['id'] ?? 0);
$message = "";

$stmt = $conn->prepare("SELECT question_text, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$stmt->bind_result($q,$a,$b,$c,$d,$correct);
if (!$stmt->fetch()) {
    $stmt->close();
    $pageTitle = "Edit Question";
    include '../includes/header.php';
    echo "<div class='alert'>Question not found.</div>";
    include '../includes/footer.php';
    exit;
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $q = trim($_POST['question'] ?? '');
    $a = trim($_POST['option_a'] ?? '');
    $b = trim($_POST['option_b'] ?? '');
    $c = trim($_POST['option_c'] ?? '');
    $d = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? '';

    if ($q===""||$a===""||$b===""||$c===""||$d===""||$correct==="") {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE questions SET question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=? WHERE id=?");
        $stmt->bind_param("ssssssi", $q,$a,$b,$c,$d,$correct,$id);
        if ($stmt->execute()) $message = "Question updated.";
        else $message = "Error: ".$conn->error;
        $stmt->close();
    }
}

$pageTitle = "Edit Question";
include '../includes/header.php';
?>
<h2 class="page-title">Edit MCQ Question</h2>
<?php if ($message): ?><div class="alert"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

<form method="post" class="glass-card form-grid">
    <label>Question
        <textarea name="question" required><?php echo htmlspecialchars($q); ?></textarea>
    </label>
    <label>Option A
        <input type="text" name="option_a" value="<?php echo htmlspecialchars($a); ?>" required>
    </label>
    <label>Option B
        <input type="text" name="option_b" value="<?php echo htmlspecialchars($b); ?>" required>
    </label>
    <label>Option C
        <input type="text" name="option_c" value="<?php echo htmlspecialchars($c); ?>" required>
    </label>
    <label>Option D
        <input type="text" name="option_d" value="<?php echo htmlspecialchars($d); ?>" required>
    </label>
    <label>Correct Option
        <select name="correct_option" required>
            <?php foreach(['A','B','C','D'] as $opt): ?>
                <option value="<?php echo $opt; ?>" <?php if ($correct===$opt) echo "selected"; ?>><?php echo $opt; ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn primary full">Update</button>
</form>
<?php include '../includes/footer.php'; ?>
