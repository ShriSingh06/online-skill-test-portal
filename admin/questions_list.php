<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Manage Questions";
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']);

// Fetch total number of questions for pagination
$sql_count = "SELECT COUNT(id) FROM questions";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_row();
$total_questions = $row_count[0];
$total_pages = ceil($total_questions / $limit);

// Fetch questions with pagination
$questions = [];
$sql_questions = "SELECT id, question_text, correct_option, difficulty, category FROM questions ORDER BY id DESC LIMIT ?, ?";
if ($stmt = $conn->prepare($sql_questions)) {
    $stmt->bind_param("ii", $start, $limit);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>Manage Questions</h1>
    
    <p><a href="questions_add.php" class="btn btn-success">Add New Question</a></p>

    <?php if (!empty($success_message)) echo '<div class="alert alert-success">' . htmlspecialchars($success_message) . '</div>'; ?>
    <?php if (!empty($error_message)) echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>'; ?>
    
    <?php if (empty($questions)): ?>
        <div class="alert alert-warning">No questions found. Please add some.</div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 50%;">Question Text</th>
                    <th style="width: 10%;">Correct</th>
                    <th style="width: 10%;">Difficulty</th>
                    <th style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): ?>
                <tr>
                    <td><?php echo htmlspecialchars($q['id']); ?></td>
                    <td><?php echo htmlspecialchars(substr($q['question_text'], 0, 100)) . (strlen($q['question_text']) > 100 ? '...' : ''); ?></td>
                    <td><span style="font-weight: bold; color: var(--success-color);"><?php echo htmlspecialchars($q['correct_option']); ?></span></td>
                    <td><?php echo htmlspecialchars($q['difficulty'] ?? 'N/A'); ?></td>
                    <td class="table-actions">
                        <a href="questions_edit.php?id=<?php echo $q['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <form action="questions_edit.php?id=<?php echo $q['id']; ?>" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
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
                        <a href="questions_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>