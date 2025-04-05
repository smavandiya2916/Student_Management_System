<?php
include 'db_connect.php';

if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // Sanitize input

    $query = "SELECT * FROM users WHERE unique_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "Welcome, " . htmlspecialchars($user['name']);
    } else {
        echo "User not found.";
    }
} else {
    echo "Invalid link.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">Educa.</a>

      <form action="search.php" method="get" class="search-form">
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
         <img src="images/<?php echo isset($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default.jpg'; ?>" class="image" alt="Profile Picture">
         <h3 class="name"><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Guest'; ?></h3>
         <p class="role"><?php echo isset($user['role']) ? htmlspecialchars($user['role']) : 'Visitor'; ?></p>
         <a href="profile.php" class="btn">View Profile</a>
         <a href="dashboard.php?token=<?php echo $user['unique_token']; ?>" class="btn">Go to Dashboard</a>
         <div class="flex-btn">
            <a href="login.php" class="option-btn">Login</a>
            <a href="register.php" class="option-btn">Register</a>
         </div>
      </div>
   </section>
</header>

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/<?php echo isset($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'default.jpg'; ?>" class="image" alt="Profile Picture">
      <h3 class="name"><?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'Guest'; ?></h3>
      <p class="role"><?php echo isset($user['role']) ? htmlspecialchars($user['role']) : 'Visitor'; ?></p>
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

<section class="courses">
   <h1 class="heading">Our Courses</h1>
   <div class="box-container">
      <?php
      $query = "SELECT * FROM courses LIMIT 5"; // Fetch the first 5 courses
      $result = $conn->query($query);
      if ($result->num_rows > 0):
         while ($course = $result->fetch_assoc()):
      ?>
         <div class="box">
            <div class="tutor">
               <img src="images/<?php echo isset($course['tutor_image']) ? htmlspecialchars($course['tutor_image']) : 'default.jpg'; ?>" alt="">
               <div class="info">
                  <h3><?php echo isset($course['tutor_name']) ? htmlspecialchars($course['tutor_name']) : 'Unknown Tutor'; ?></h3>
                  <span><?php echo isset($course['date']) ? htmlspecialchars($course['date']) : 'Unknown Date'; ?></span>
               </div>
            </div>
            <div class="thumb">
               <img src="images/<?php echo isset($course['thumbnail']) ? htmlspecialchars($course['thumbnail']) : 'default.jpg'; ?>" alt="">
               <span><?php echo isset($course['video_count']) ? htmlspecialchars($course['video_count']) : '0'; ?> videos</span>
            </div>
            <h3 class="title"><?php echo isset($course['title']) ? htmlspecialchars($course['title']) : 'Untitled Course'; ?></h3>
            <a href="playlist.php?course_id=<?php echo $course['id']; ?>" class="inline-btn">View Playlist</a>
         </div>
      <?php
         endwhile;
      else:
      ?>
         <p>No courses available.</p>
      <?php endif; ?>
   </div>
   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">View All Courses</a>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- Custom JavaScript -->
<script src="js/script.js"></script>

</body>
</html>