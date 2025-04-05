<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['c_pass']);
    $role = 'student'; // Default role for registration

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Encrypt the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle profile picture upload
        $profile_picture = $_FILES['profile_picture']['name'];
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        $profile_picture_folder = 'images/' . $profile_picture;

        // Debugging: Check if the file is being uploaded
        if (empty($profile_picture)) {
            $error = "No file selected for upload.";
        } elseif (!is_uploaded_file($profile_picture_tmp)) {
            $error = "File upload failed. Temporary file not found.";
        } elseif (!move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
            $error = "Failed to upload file. Check folder permissions.";
        } else {
            echo "File uploaded successfully: $profile_picture_folder<br>";
        }

        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            // Insert user data into the database
            $query = "INSERT INTO users (name, email, password, role, profile_picture) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $profile_picture);

            if ($stmt->execute()) {
                // Move the uploaded profile picture to the images folder
                if (move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
                    $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                } else {
                    $error = "Failed to upload profile picture.";
                }
            } else {
                $error = "Error: " . $stmt->error;
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="home.html" class="logo">Educa.</a>

      <form action="search.html" method="post" class="search-form">
         <input type="text" name="search_box" required placeholder="search courses..." maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
      </div>

      <div class="profile">
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name">ashish</h3>
         <p class="role">student</p>
         <a href="profile.html" class="btn">view profile</a>
         <div class="flex-btn">
            <a href="login.html" class="option-btn">login</a>
            <a href="register.html" class="option-btn">register</a>
         </div>
      </div>

   </section>

</header>   

<div class="side-bar">

   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/pic-1.jpg" class="image" alt="">
      <h3 class="name">ashish</h3>
      <p class="role">student</p>
      <a href="profile.html" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.html"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.html"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.html"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.html"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.html"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
      <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
      <p>your name <span>*</span></p>
      <input type="text" name="name" placeholder="enter your name" required maxlength="50" class="box">
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" required maxlength="50" class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="password" placeholder="enter your password" required maxlength="20" class="box">
      <p>confirm password <span>*</span></p>
      <input type="password" name="c_pass" placeholder="confirm your password" required maxlength="20" class="box">
      <p>select profile <span>*</span></p>
      <input type="file" name="profile_picture" accept="image/*" required class="box">
      <input type="submit" value="register new" name="register" class="btn">
      <p>Already have an account? <a href="login.php">Login</a></p>
   </form>

</section>

<footer class="footer">

   &copy; ashish <span>ashish</span> | all rights reserved!

</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>