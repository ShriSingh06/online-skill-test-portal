<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$sql = "SELECT r.score,r.total_questions,r.correct_answers,r.test_date,
               s.name,s.email
        FROM results r
        JOIN students s ON s.id = r.student_id
        ORDER BY r.test_date DESC";
$res = $conn->query($sql);

$pageTitle = "Results";
include '../includes/header.php';
?>
<h2 class="page-title">All Results</h2>
<?php if ($res->num_rows===0): ?>
    <div class="alert">No results yet.</div>
<?php else: ?>
<table class="glass-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Student</th>
        <th>Email</th>
        <th>Date</th>
        <th>Score (%)</th>
        <th>Correct / Total</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; while($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['test_date']); ?></td>
            <td><?php echo $row['score']; ?></td>
            <td><?php echo $row['correct_answers']."/".$row['total_questions']; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
