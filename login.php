<?php
// Start session
session_start();
include 'config.php'; // Database connection

// Handle Login
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['logemail'], $_POST['logpass'])) {
    $email = $_POST['logemail'];
    $password = $_POST['logpass'];

    // Query to find the user
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: chat.php");
        }
        exit();
    } else {
        $login_error = "Invalid email or password!";
    }
}

// Handle Signup
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['logname'], $_POST['logemail'], $_POST['logpass'])) {
    $name = $_POST['logname'];
    $email = $_POST['logemail'];
    $password = password_hash($_POST['logpass'], PASSWORD_BCRYPT);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM tbl_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $signup_error = "Email already exists. Please log in.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO tbl_users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php?signup=success");
            exit();
        } else {
            $signup_error = "Error: Could not register user.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Log In </span><span>Sign Up</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
                        <label for="reg-log"></label>
                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">

                                <!-- Login Form -->
                                <div class="card-front" style=" margin-left: 108%;">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <form action="#" method="post">
                                                <h4 class="mb-4 pb-3">Log In</h4>
                                                <?php if (isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>
                                                <div class="form-group">
                                                    <input type="email" name="logemail" class="form-style" placeholder="Your Email" autocomplete="off" required>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="logpass" class="form-style" placeholder="Your Password" autocomplete="off" required>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn mt-4">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Signup Form -->
                                <div class="card-back" style="margin-left:-102%;">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <form action="#" method="post">
                                                <h4 class="mb-4 pb-3">Sign Up</h4>
                                                <?php if (isset($signup_error)) echo "<p class='error'>$signup_error</p>"; ?>
                                                <div class="form-group">
                                                    <input type="text" name="logname" class="form-style" placeholder="Your Full Name" autocomplete="off" required>
                                                    <i class="input-icon uil uil-user"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="email" name="logemail" class="form-style" placeholder="Your Email" autocomplete="off" required>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="logpass" class="form-style" placeholder="Your Password" autocomplete="off" required>
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" class="btn mt-4">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
