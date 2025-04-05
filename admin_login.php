<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = md5($_POST['password']); // Hash the password using MD5

    // Debugging: Output email and hashed password
    echo "Email: $email<br>";
    echo "Password (hashed): $password<br>";

    $query = "SELECT * FROM users WHERE email = ? AND password = ? AND role = 'admin'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $admin['email'];
        header("Location: adminhomepage.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Form container styling */
        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container .btn:hover {
            background: #0056b3;
        }

        .form-container p {
            text-align: center;
            margin-top: 10px;
            color: red;
        }
    </style>
</head>
<body>
<section class="form-container">
    <form action="" method="post">
        <h3>Admin Login</h3>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <input type="email" name="email" placeholder="Enter your email" required class="box">
        <input type="password" name="password" placeholder="Enter your password" required class="box">
        <input type="submit" value="Login" class="btn">
    </form>
</section>
</body>
</html>