<?php
include 'db_connect.php'; // Include the database connection file
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the current user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $c_pass = $_POST['c_pass'];
    $profile_image = $_FILES['profile_image']['name'];
    $profile_image_tmp = $_FILES['profile_image']['tmp_name'];

    // Update name and email
    if (!empty($name) && !empty($email)) {
        $update_query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $name, $email, $user_id);
        $stmt->execute();
        $_SESSION['user_name'] = $name; // Update session name
    }

    // Update password
    if (!empty($old_pass) && !empty($new_pass) && !empty($c_pass)) {
        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $c_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $update_pass_query = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($update_pass_query);
                $stmt->bind_param("si", $hashed_pass, $user_id);
                $stmt->execute();
            } else {
                $error = "New password and confirm password do not match.";
            }
        } else {
            $error = "Old password is incorrect.";
        }
    }

    // Update profile picture
    if (!empty($profile_image)) {
        $image_path = "images/" . $profile_image;
        move_uploaded_file($profile_image_tmp, $image_path);
        $update_image_query = "UPDATE users SET profile_image = ? WHERE id = ?";
        $stmt = $conn->prepare($update_image_query);
        $stmt->bind_param("si", $profile_image, $user_id);
        $stmt->execute();
    }

    header("Location: profile.php"); // Redirect to profile page after update
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">Educa.</a>

      <form action="search.php" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/<?php echo htmlspecialchars($user['profile_image']); ?>" class="image" alt="">
         <h3 class="name"><?php echo htmlspecialchars($user['name']); ?></h3>
         <p class="role"><?php echo htmlspecialchars($user['role']); ?></p>
         <a href="profile.php" class="btn">View Profile</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>
   </section>
</header>   

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/<?php echo htmlspecialchars($user['profile_image']); ?>" class="image" alt="">
      <h3 class="name"><?php echo htmlspecialchars($user['name']); ?></h3>
      <p class="role"><?php echo htmlspecialchars($user['role']); ?></p>
      <a href="profile.php" class="btn">View Profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Update Profile</h3>
      <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
      <p>Update Name</p>
      <input type="text" name="name" placeholder="<?php echo htmlspecialchars($user['name']); ?>" maxlength="50" class="box">
      <p>Update Email</p>
      <input type="email" name="email" placeholder="<?php echo htmlspecialchars($user['email']); ?>" maxlength="50" class="box">
      <p>Previous Password</p>
      <input type="password" name="old_pass" placeholder="Enter your old password" maxlength="20" class="box">
      <p>New Password</p>
      <input type="password" name="new_pass" placeholder="Enter your new password" maxlength="20" class="box">
      <p>Confirm Password</p>
      <input type="password" name="c_pass" placeholder="Confirm your new password" maxlength="20" class="box">
      <p>Update Profile Picture</p>
      <input type="file" name="profile_image" accept="image/*" class="box">
      <input type="submit" value="Update Profile" name="submit" class="btn">
   </form>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>