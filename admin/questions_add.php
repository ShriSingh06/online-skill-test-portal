<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Add New Question";
$question_text = $option_a = $option_b = $option_c = $option_d = $correct_option = $difficulty = $category = "";
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        // Prepare an insert statement
        $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, difficulty, category) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssss", 
                $question_text, 
                $option_a, 
                $option_b, 
                $option_c, 
                $option_d, 
                $correct_option, 
                $difficulty, 
                $category
            );
            
            if ($stmt->execute()) {
                $success = "Question added successfully!";
                // Reset form fields after successful insertion
                $question_text = $option_a = $option_b = $option_c = $option_d = $correct_option = $difficulty = $category = "";
            } else {
                $error = "Error adding question: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare statement.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>Add New MCQ Question</h1>
    
    <?php if (!empty($success)) echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>'; ?>
    <?php if (!empty($error)) echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        
        <div class="form-group">
            <label for="question_text">Question Text</label>
            <textarea name="question_text" id="question_text" class="form-control" rows="4" required><?php echo htmlspecialchars($question_text); ?></textarea>
        </div>
        
        <h3>Options</h3>
        <div class="form-group">
            <label for="option_a">Option A</label>
            <input type="text" name="option_a" id="option_a" class="form-control" value="<?php echo htmlspecialchars($option_a); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_b">Option B</label>
            <input type="text" name="option_b" id="option_b" class="form-control" value="<?php echo htmlspecialchars($option_b); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_c">Option C</label>
            <input type="text" name="option_c" id="option_c" class="form-control" value="<?php echo htmlspecialchars($option_c); ?>" required>
        </div>
        <div class="form-group">
            <label for="option_d">Option D</label>
            <input type="text" name="option_d" id="option_d" class="form-control" value="<?php echo htmlspecialchars($option_d); ?>" required>
        </div>

        <div class="form-group">
            <label for="correct_option">Correct Option (A, B, C, or D)</label>
            <select name="correct_option" id="correct_option" class="form-control" required>
                <option value="">Select Correct Option</option>
                <option value="A" <?php if ($correct_option == 'A') echo 'selected'; ?>>A</option>
                <option value="B" <?php if ($correct_option == 'B') echo 'selected'; ?>>B</option>
                <option value="C" <?php if ($correct_option == 'C') echo 'selected'; ?>>C</option>
                <option value="D" <?php if ($correct_option == 'D') echo 'selected'; ?>>D</option>
            </select>
        </div>

        <h3>Optional Fields</h3>
        <div class="form-group">
            <label for="difficulty">Difficulty</label>
            <input type="text" name="difficulty" id="difficulty" class="form-control" value="<?php echo htmlspecialchars($difficulty); ?>" placeholder="e.g., Easy, Medium, Hard">
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>" placeholder="e.g., PHP, SQL, JavaScript">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">Add Question</button>
            <a href="questions_list.php" class="btn btn-secondary" style="background-color: var(--secondary-color); color: white;">Back to List</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>