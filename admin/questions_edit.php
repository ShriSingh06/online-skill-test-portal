<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Edit Question";
$question = null;
$error = "";
$success = "";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("location: questions_list.php");
    exit;
}

// 1. Handle DELETE action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $sql_delete = "DELETE FROM questions WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Question deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting question: " . $stmt_delete->error;
        }
        $stmt_delete->close();
    } else {
        $_SESSION['error_message'] = "Database error during delete.";
    }
    header("location: questions_list.php");
    exit;
}

// 2. Handle GET request or initial form load (Fetch data)
$sql_fetch = "SELECT * FROM questions WHERE id = ?";
if ($stmt_fetch = $conn->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $id);
    if ($stmt_fetch->execute()) {
        $result = $stmt_fetch->get_result();
        if ($result->num_rows == 1) {
            $question = $result->fetch_assoc();
        } else {
            // No question found
            $_SESSION['error_message'] = "Question not found.";
            header("location: questions_list.php");
            exit;
        }
    } else {
        $error = "Error fetching question: " . $stmt_fetch->error;
    }
    $stmt_fetch->close();
} else {
    $error = "Database error: Unable to prepare statement.";
}

// 3. Handle UPDATE action
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    // Collect and sanitize input
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = trim($_POST['correct_option']);
    $difficulty = !empty(trim($_POST['difficulty'])) ? trim($_POST['difficulty']) : null;
    $category = !empty(trim($_POST['category'])) ? trim($_POST['category']) : null;

    // Server-side validation
    if (empty($question_text) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_option)) {
        $error = "All fields (Question and Options) are required.";
    } elseif (!in_array($correct_option, ['A', 'B', 'C', 'D'])) {
        $error = "Correct option must be A, B, C, or D.";
    } else {
        // Prepare an update statement
        $sql_update = "UPDATE questions SET question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_option=?, difficulty=?, category=? WHERE id=?";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("ssssssssi", 
                $question_text, 
                $option_a, 
                $option_b, 
                $option_c, 
                $option_d, 
                $correct_option, 
                $difficulty, 
                $category, 
                $id
            );
            
            if ($stmt_update->execute()) {
                $success = "Question updated successfully!";
                // Re-fetch the updated data
                $question = [
                    'id' => $id, 'question_text' => $question_text, 'option_a' => $option_a, 'option_b' => $option_b, 
                    'option_c' => $option_c, 'option_d' => $option_d, 'correct_option' => $correct_option, 
                    'difficulty' => $difficulty, 'category' => $category, 'created_at' => $question['created_at']
                ];
            } else {
                $error = "Error updating question: " . $stmt_update->error;
            }

            $stmt_update->close();
        } else {
            $error = "Database error: Unable to prepare update statement.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>Edit Question #<?php echo htmlspecialchars($id); ?></h1>
    
    <?php if (!empty($success)) echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>'; ?>
    <?php if (!empty($error)) echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; ?>

    <?php if ($question): ?>
    <form action="questions_edit.php?id=<?php echo $id; ?>" method="post">
        
        <div class="form-group">
            <label for="question_text">Question Text</label>
            <textarea name="question_text" id="question_text" class="form-control" rows="4" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
        </div>
        
        <h3>Options</h3>
        <div class="form-group">
            <label for="option_a">Option A</label>
            <input type="text" name="option_a" id="option_a" class="form-control" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_b">Option B</label>
            <input type="text" name="option_b" id="option_b" class="form-control" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_c">Option C</label>
            <input type="text" name="option_c" id="option_c" class="form-control" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_d">Option D</label>
            <input type="text" name="option_d" id="option_d" class="form-control" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
        </div>

        <div class="form-group">
            <label for="correct_option">Correct Option (A, B, C, or D)</label>
            <select name="correct_option" id="correct_option" class="form-control" required>
                <option value="">Select Correct Option</option>
                <?php $options = ['A', 'B', 'C', 'D']; ?>
                <?php foreach ($options as $opt): ?>
                    <option value="<?php echo $opt; ?>" <?php if ($question['correct_option'] == $opt) echo 'selected'; ?>><?php echo $opt; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Optional Fields</h3>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <input type="text" name="difficulty" id="difficulty" class="form-control" value="<?php echo htmlspecialchars($question['difficulty']); ?>" placeholder="e.g., Easy, Medium, Hard">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" class="form-control" value="<?php echo htmlspecialchars($question['category']); ?>" placeholder="e.g., PHP, SQL, JavaScript">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Question</button>
            <a href="questions_list.php" class="btn btn-secondary" style="background-color: var(--secondary-color); color: white;">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>