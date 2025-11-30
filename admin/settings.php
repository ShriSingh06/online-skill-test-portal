<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "Global Test Settings";
$settings = null;
$error = "";
$success = "";

// 1. Fetch current settings
$sql_fetch = "SELECT * FROM settings WHERE id = 1";
if ($result = $conn->query($sql_fetch)) {
    $settings = $result->fetch_assoc();
    $result->close();
}

// 2. Handle POST request (Update settings)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $duration = isset($_POST['test_duration_minutes']) ? (int)$_POST['test_duration_minutes'] : 30;
    $questions_per_test = isset($_POST['questions_per_test']) ? (int)$_POST['questions_per_test'] : 10;
    $shuffle_questions = isset($_POST['shuffle_questions']) ? 1 : 0;
    $shuffle_options = isset($_POST['shuffle_options']) ? 1 : 0;

    // Server-side validation
    if ($duration < 1 || $duration > 120) {
        $error = "Test duration must be between 1 and 120 minutes.";
    } elseif ($questions_per_test < 1 || $questions_per_test > 50) {
        $error = "Questions per test must be between 1 and 50.";
    } else {
        // Prepare an UPDATE statement
        $sql_update = "UPDATE settings SET test_duration_minutes=?, questions_per_test=?, shuffle_questions=?, shuffle_options=? WHERE id=1";
        
        if ($stmt = $conn->prepare($sql_update)) {
            $stmt->bind_param("iiii", 
                $duration, 
                $questions_per_test, 
                $shuffle_questions, 
                $shuffle_options
            );
            
            if ($stmt->execute()) {
                $success = "Settings updated successfully!";
                // Update $settings array to reflect the changes
                $settings = [
                    'id' => 1, 
                    'test_duration_minutes' => $duration, 
                    'questions_per_test' => $questions_per_test, 
                    'shuffle_questions' => $shuffle_questions, 
                    'shuffle_options' => $shuffle_options
                ];
            } else {
                $error = "Error updating settings: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare update statement.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="main-content">
    <h1>Global Test Settings</h1>
    
    <?php if (!empty($success)) echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>'; ?>
    <?php if (!empty($error)) echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>'; ?>

    <?php if ($settings): ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        
        <div class="form-group">
            <label for="test_duration_minutes">Global Test Duration (minutes)</label>
            <input type="number" name="test_duration_minutes" id="test_duration_minutes" class="form-control" 
                   value="<?php echo htmlspecialchars($settings['test_duration_minutes']); ?>" min="1" max="120" required>
            <span class="error-message">Must be between 1 and 120 minutes.</span>
        </div>
        
        <div class="form-group">
            <label for="questions_per_test">Number of Questions per Test</label>
            <input type="number" name="questions_per_test" id="questions_per_test" class="form-control" 
                   value="<?php echo htmlspecialchars($settings['questions_per_test']); ?>" min="1" max="50" required>
            <span class="error-message">Must be between 1 and 50.</span>
        </div>
        
        <div class="form-group" style="display: flex; align-items: center; margin-top: 20px;">
            <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1" 
                   <?php if ($settings['shuffle_questions']) echo 'checked'; ?> style="width: auto; margin-right: 10px;">
            <label for="shuffle_questions" style="margin-bottom: 0;">Shuffle Questions?</label>
        </div>

        <div class="form-group" style="display: flex; align-items: center;">
            <input type="checkbox" name="shuffle_options" id="shuffle_options" value="1" 
                   <?php if ($settings['shuffle_options']) echo 'checked'; ?> style="width: auto; margin-right: 10px;">
            <label for="shuffle_options" style="margin-bottom: 0;">Shuffle Options (A, B, C, D)?</label>
        </div>

        <div class="form-group" style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-danger">Could not load settings. Check database connection and 'settings' table.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>