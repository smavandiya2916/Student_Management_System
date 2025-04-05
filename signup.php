<?php
include 'db_connect.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists. Please use a different email.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Upload profile picture
            $profile_picture_path = "images/" . $profile_picture;
            move_uploaded_file($profile_picture_tmp, $profile_picture_path);

            // Insert user into the database
            $insert_query = "INSERT INTO users (name, email, password, profile_image) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $profile_picture);

            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login page after successful registration
                exit;
            } else {
                $error = "Failed to register. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to your CSS file -->
</head>
<body>

<header class="header">
    <h1>Register</h1>
</header>

<section class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <p>Your Name <span>*</span></p>
        <input type="text" name="name" placeholder="Enter your name" required maxlength="50" class="box">
        
        <p>Your Email <span>*</span></p>
        <input type="email" name="email" placeholder="Enter your email" required maxlength="50" class="box">
        
        <p>Your Password <span>*</span></p>
        <input type="password" name="password" placeholder="Enter your password" required maxlength="20" class="box">
        
        <p>Confirm Password <span>*</span></p>
        <input type="password" name="confirm_password" placeholder="Confirm your password" required maxlength="20" class="box">
        
        <p>Select Profile Picture <span>*</span></p>
        <input type="file" name="profile_picture" accept="image/*" required class="box">
        
        <input type="submit" value="Register" name="submit" class="btn">
    </form>
</section>

<footer class="footer">
    &copy; Educa | All rights reserved!
</footer>

</body>
</html>