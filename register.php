<?php
// Database connection
$host = 'localhost';
$db = 'rtc';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if username or email already exists
    $sql = "SELECT id FROM tbl_users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username or email already exists. Please choose a different one.";
    } else {
        // Hash the password before saving it to the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $insert_sql = "INSERT INTO tbl_users (username, email, password) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param('sss', $username, $email, $hashed_password);

        if ($insert_stmt->execute()) {
            echo "User registered successfully. <a href='login.php'>Login here</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Close statement
    $stmt->close();
    $insert_stmt->close();
}
?>

<!-- Registration Form -->
<form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
