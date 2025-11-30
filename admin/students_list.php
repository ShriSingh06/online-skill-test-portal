<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Manage Students";
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch total number of students for pagination
$sql_count = "SELECT COUNT(id) FROM students";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_row();
$total_students = $row_count[0];
$total_pages = ceil($total_students / $limit);

// Fetch students list with pagination
$students = [];
$sql_students = "SELECT id, full_name, email, username, created_at FROM students ORDER BY id DESC LIMIT ?, ?";
if ($stmt = $conn->prepare($sql_students)) {
    $stmt->bind_param("ii", $start, $limit);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>Registered Students</h1>
    
    <?php if (empty($students)): ?>
        <div class="alert alert-warning">No students registered yet.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 25%;">Full Name</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 20%;">Username</th>
                    <th style="width: 15%;">Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['id']); ?></td>
                    <td><?php echo htmlspecialchars($s['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['username']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($s['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="<?php echo ($i == $page) ? 'current-page' : ''; ?>">
                    <?php if ($i == $page): ?>
                        <span><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="students_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>