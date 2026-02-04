<?php
require_once 'config.php';
$pageTitle = "Admin Login";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $dbPass);
        if ($stmt->fetch()) {
            if ($password === $dbPass) {
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_username'] = $username;
                header("Location: admin/dashboard.php");
                exit;
            } else {
                $message = "Invalid credentials.";
            }
        } else {
            $message = "Admin not found.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>
<div class="auth-wrapper glass-card">
    <h2>Admin Login</h2>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <label>Username
            <input type="text" name="username" required>
        </label>

        <label>Password
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn primary full">Login</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
