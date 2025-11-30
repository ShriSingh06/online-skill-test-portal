<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "All Test Results";
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter setup
$filter_student_id = isset($_GET['student_id']) && is_numeric($_GET['student_id']) ? (int)$_GET['student_id'] : null;
$filter_date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
$filter_date_end = isset($_GET['date_end']) ? $_GET['date_end'] : '';

$where_clauses = [];
$param_types = '';
$params = [];

if ($filter_student_id) {
    $where_clauses[] = "r.student_id = ?";
    $param_types .= 'i';
    $params[] = $filter_student_id;
}

if (!empty($filter_date_start)) {
    $where_clauses[] = "r.taken_at >= ?";
    $param_types .= 's';
    $params[] = $filter_date_start . ' 00:00:00';
}

if (!empty($filter_date_end)) {
    $where_clauses[] = "r.taken_at <= ?";
    $param_types .= 's';
    $params[] = $filter_date_end . ' 23:59:59';
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// 1. Fetch total count
$sql_count = "SELECT COUNT(r.id) FROM results r {$where_sql}";
if ($stmt_count = $conn->prepare($sql_count)) {
    if (!empty($params)) {
        $stmt_count->bind_param($param_types, ...$params);
    }
    $stmt_count->execute();
    $row_count = $stmt_count->get_result()->fetch_row();
    $total_results = $row_count[0];
    $stmt_count->close();
} else {
    $total_results = 0;
}
$total_pages = ceil($total_results / $limit);

// 2. Fetch results
$results = [];
$sql_results = "
    SELECT 
        r.id, r.total_questions, r.correct_answers, r.percentage, r.taken_at,
        s.full_name, s.id AS student_id
    FROM results r
    JOIN students s ON r.student_id = s.id
    {$where_sql}
    ORDER BY r.taken_at DESC
    LIMIT ?, ?
";

// Add LIMIT parameters to the list
$param_types .= 'ii';
$params[] = $start;
$params[] = $limit;

if ($stmt = $conn->prepare($sql_results)) {
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
    $stmt->close();
}

// Fetch students list for filter dropdown
$students_list = [];
$sql_students = "SELECT id, full_name, username FROM students ORDER BY full_name ASC";
if ($result_s = $conn->query($sql_students)) {
    while ($row = $result_s->fetch_assoc()) {
        $students_list[] = $row;
    }
    $result_s->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>All Test Results</h1>
    
    <div class="card" style="margin-bottom: 20px;">
        <h3>Filter Results</h3>
        <form method="get" action="results_list.php">
            <div style="display: flex; gap: 20px; align-items: flex-end;">
                <div class="form-group" style="flex: 1;">
                    <label for="student_id">Filter by Student:</label>
                    <select name="student_id" id="student_id" class="form-control">
                        <option value="">-- All Students --</option>
                        <?php foreach ($students_list as $student): ?>
                            <option 
                                value="<?php echo htmlspecialchars($student['id']); ?>" 
                                <?php echo ($filter_student_id == $student['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($student['full_name']) . ' (' . htmlspecialchars($student['username']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="date_start">Date Range Start:</label>
                    <input type="date" name="date_start" id="date_start" class="form-control" value="<?php echo htmlspecialchars($filter_date_start); ?>">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="date_end">Date Range End:</label>
                    <input type="date" name="date_end" id="date_end" class="form-control" value="<?php echo htmlspecialchars($filter_date_end); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="results_list.php" class="btn" style="background-color: var(--secondary-color); color: white;">Clear</a>
                </div>
            </div>
        </form>
    </div>
    <?php if (empty($results)): ?>
        <div class="alert alert-warning">No results found matching the criteria.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Pass/Fail</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                <?php $is_passed = $r['percentage'] >= 40; ?>
                <tr>
                    <td><a href="results_list.php?student_id=<?php echo $r['student_id']; ?>"><?php echo htmlspecialchars($r['full_name']); ?></a></td>
                    <td><?php echo $r['correct_answers'] . '/' . $r['total_questions']; ?></td>
                    <td><?php echo number_format($r['percentage'], 2) . '%'; ?></td>
                    <td>
                        <span style="font-weight: bold; color: <?php echo $is_passed ? 'var(--success-color)' : 'var(--danger-color)'; ?>">
                            <?php echo $is_passed ? 'PASS' : 'FAIL'; ?>
                        </span>
                    </td>
                    <td><?php echo date('Y-m-d H:i', strtotime($r['taken_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): 
                $query_params = http_build_query(array_filter(['student_id' => $filter_student_id, 'date_start' => $filter_date_start, 'date_end' => $filter_date_end, 'page' => $i]));
            ?>
                <li class="<?php echo ($i == $page) ? 'current-page' : ''; ?>">
                    <?php if ($i == $page): ?>
                        <span><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="results_list.php?<?php echo $query_params; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>