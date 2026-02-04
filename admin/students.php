<?php
require_once '../config.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ".$base_url."admin_login.php");
    exit;
}
$sql = "SELECT s.id, s.name, s.email, s.created_at, COUNT(r.id) tests 
        FROM students s 
        LEFT JOIN results r ON r.student_id = s.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";
$res = $conn->query($sql);

$pageTitle = "Students";
include '../includes/header.php';
?>
<h2 class="page-title">Students</h2>
<?php if ($res->num_rows===0): ?>
    <div class="alert">No students yet.</div>
<?php else: ?>
<table class="glass-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Registered</th>
        <th>Tests</th>
    </tr>
    </thead>
    <tbody>
    <?php $i=1; while($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td><?php echo $row['tests']; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>
