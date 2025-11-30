<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include config file
require_once __DIR__ . '/../config/db.php';

$full_name = $email = $username = $password = $confirm_password = "";
$full_name_err = $email_err = $username_err = $password_err = $confirm_password_err = $register_err = "";
$success_message = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // 1. Validate Full Name
    if(empty(trim($_POST["full_name"]))){
        $full_name_err = "Please enter your full name.";
    } else {
        $full_name = trim($_POST["full_name"]);
    }

    // 2. Validate Email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else{
        $email = trim($_POST["email"]);
        // Check if email already exists
        $sql = "SELECT id FROM students WHERE email = ?";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_email);
            $param_email = $email;
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows >= 1){
                    $email_err = "This email is already registered.";
                }
            } else{
                $register_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }

    // 3. Validate Username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(strlen(trim($_POST["username"])) < 3){
        $username_err = "Username must have at least 3 characters.";
    } else{
        $username = trim($_POST["username"]);
        // Check if username already exists
        $sql = "SELECT id FROM students WHERE username = ?";
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            $param_username = $username;
            if($stmt->execute()){
                $stmt->store_result();
                if($stmt->num_rows >= 1){
                    $username_err = "This username is already taken.";
                }
            }
            $stmt->close();
        }
    }

    // 4. Validate Password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // 5. Validate Confirm Password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if(empty($full_name_err) && empty($email_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($register_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO students (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)";
         
        if($stmt = $conn->prepare($sql)){
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Bind parameters
            $stmt->bind_param("ssss", $param_full_name, $param_email, $param_username, $param_password_hash);
            
            // Set parameters
            $param_full_name = $full_name;
            $param_email = $email;
            $param_username = $username;
            $param_password_hash = $password_hash; // Hashed password

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $success_message = "Registration successful! You can now log in.";
                // Clear inputs for display after success
                $full_name = $email = $username = $password = $confirm_password = "";
            } else{
                $register_err = "Something went wrong. Please try again later. Error: " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        } else {
            $register_err = "Database error: Unable to prepare statement.";
        }
    }
}

$page_title = "Student Registration";
include __DIR__ . '/../includes/header.php';
// Note: No navbar included as this is an auth page
?>

<div class="auth-container">
    <div class="registration-box">
        <h2>Student Registration</h2>
        <p>Please fill this form to create an account.</p>

        <?php 
        if(!empty($register_err)){
            echo '<div class="alert alert-danger">' . $register_err . '</div>';
        }
        if(!empty($success_message)){
            echo '<div class="alert alert-success">' . $success_message . '</div>';
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registrationForm" novalidate>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
                <span class="error-message"><?php echo $full_name_err; ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                <span class="error-message"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                <span class="error-message"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" value="" required>
                <span class="error-message"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="" required>
                <span class="error-message"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>