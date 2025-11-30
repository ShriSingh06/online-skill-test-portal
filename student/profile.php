<?php
require_once __DIR__ . '/../includes/auth_student.php';
require_once __DIR__ . '/../config/db.php';

$page_title = "My Profile";
$student_id = $_SESSION['student_id'];
$student = null;

// Fetch student data
$sql = "SELECT full_name, email, username, created_at FROM students WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $student = $result->fetch_assoc();
        }
    }
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar_student.php';
?>

<div class="main-content">
    <h1>My Profile</h1>
    
    <?php if ($student): ?>
    <div class="card" style="max-width: 600px; margin-top: 20px;">
        <h3 style="border-bottom: 2px solid var(--primary-color); padding-bottom: 10px;">Account Details</h3>
        
        <div style="margin-top: 15px;">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($student['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($student['created_at'])); ?></p>
        </div>
        
        <p style="margin-top: 20px; font-size: 0.9em; color: var(--secondary-color);">
            Note: For security reasons, profile details modification is only available through the admin.
        </p>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        Could not retrieve your profile information. Please contact support.
    </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>