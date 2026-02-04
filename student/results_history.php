<?php
require_once '../config.php';
if (!isset($_SESSION['student_id'])) {
    header("Location: ".$base_url."student_login.php");
    exit;
}
$studentId = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT id, score, total_questions, correct_answers, test_date 
                        FROM results WHERE student_id=? ORDER BY test_date DESC");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$res = $stmt->get_result();

$pageTitle = "Results History";
include '../includes/header.php';
?>
<h2 class="page-title">Results History</h2>

<?php if ($res->num_rows === 0): ?>
    <div class="alert">No results yet.</div>
<?php else: ?>
<table class="glass-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Score (%)</th>
        <th>Correct / Total</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; while ($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($row['test_date']); ?></td>
            <td><?php echo $row['score']; ?></td>
            <td><?php echo $row['correct_answers']."/".$row['total_questions']; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>

<?php
$stmt->close();
include '../includes/footer.php';
?>
