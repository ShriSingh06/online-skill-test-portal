<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$res = $conn->query("SELECT * FROM questions ORDER BY created_at DESC");
$pageTitle = "Questions";
include '../includes/header.php';
?>
<h2 class="page-title">Question Bank</h2>
<p><a href="add_question.php" class="btn primary">Add Question</a></p>

<?php if ($res->num_rows === 0): ?>
    <div class="alert">No questions yet.</div>
<?php else: ?>
<table class="glass-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Question</th>
        <th>Options</th>
        <th>Correct</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; while ($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($row['question_text']); ?></td>
            <td class="small">
                A) <?php echo htmlspecialchars($row['option_a']); ?><br>
                B) <?php echo htmlspecialchars($row['option_b']); ?><br>
                C) <?php echo htmlspecialchars($row['option_c']); ?><br>
                D) <?php echo htmlspecialchars($row['option_d']); ?>
            </td>
            <td><?php echo $row['correct_option']; ?></td>
            <td>
                <a href="edit_question.php?id=<?php echo $row['id']; ?>" class="btn ghost small">Edit</a>
                <a href="delete_question.php?id=<?php echo $row['id']; ?>" class="btn subtle small"
                   onclick="return confirm('Delete this question?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
