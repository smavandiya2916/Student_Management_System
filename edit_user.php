<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $role = htmlspecialchars($_POST['role']);
        $profile_picture = $_FILES['profile_picture']['name'];
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        $profile_picture_folder = 'images/' . $profile_picture;

        // Handle profile picture upload
        if (!empty($profile_picture)) {
            if (move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
                $query = "UPDATE users SET name = ?, email = ?, role = ?, profile_picture = ? WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $name, $email, $role, $profile_picture, $user_id);
            } else {
                $error = "Failed to upload profile picture.";
            }
        } else {
            $query = "UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?";
            $stmt->bind_param("sssi", $name, $email, $role, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit;
        } else {
            echo "Error updating user: " . $stmt->error;
        }
    }
} else {
    die("No user ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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
    </style>
</head>
<body>
<section class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Edit User</h3>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="box"><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="box"><br>
        <label>Role:</label>
        <select name="role" required class="box">
            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="teacher" <?php if ($user['role'] == 'teacher') echo 'selected'; ?>>Teacher</option>
            <option value="student" <?php if ($user['role'] == 'student') echo 'selected'; ?>>Student</option>
        </select><br>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture" accept="image/*" class="box"><br>
        <?php if (!empty($user['profile_picture'])): ?>
            <p>Current Profile Picture:</p>
            <img src="images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="100">
        <?php endif; ?>
        <button type="submit" class="btn">Update User</button>
    </form>
</section>
</body>
</html>