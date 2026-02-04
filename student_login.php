<?php
require_once 'config.php';
$pageTitle = "Student Login";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === "" || $password === "") {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $name, $dbPass);
        if ($stmt->fetch()) {
            if ($password === $dbPass) {
                $_SESSION['student_id'] = $id;
                $_SESSION['student_name'] = $name;
                header("Location: student/dashboard.php");
                exit;
            } else {
                $message = "Invalid credentials.";
            }
        } else {
            $message = "Student not found.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>
<div class="auth-wrapper glass-card">
    <h2>Student Login</h2>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <label>Email
            <input type="email" name="email" required>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn primary full">Login</button>

        <p class="form-footer-text">
            New user? <a href="student_register.php">Register</a>
        </p>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
