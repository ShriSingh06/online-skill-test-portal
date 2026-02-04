<?php
require_once 'config.php';
$pageTitle = "Student Registration";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password); // plain for basic project
            if ($stmt->execute()) {
                $message = "Registration successful. You can log in now.";
            } else {
                $message = "Error: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>
<div class="auth-wrapper glass-card">
    <h2>Student Registration</h2>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <label>Full Name
            <input type="text" name="name" required>
        </label>

        <label>Email
            <input type="email" name="email" required>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn primary full">Register</button>

        <p class="form-footer-text">
            Already have an account? <a href="student_login.php">Login</a>
        </p>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
