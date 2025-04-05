<?php
include 'db_connect.php'; // Include the database connection file

// Fetch teachers dynamically from the database
$search_query = "";
if (isset($_POST['search_tutor'])) {
    $search_query = htmlspecialchars($_POST['search_box']);
    $query = "SELECT * FROM users WHERE role = 'teacher' AND name LIKE ?";
    $stmt = $conn->prepare($query);
    $search_term = "%$search_query%";
    $stmt->bind_param("s", $search_term);
} else {
    $query = "SELECT * FROM users WHERE role = 'teacher'";
    $stmt = $conn->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Teachers</title>

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
         <img src="images/pic-1.jpg" class="image" alt="">
         <h3 class="name">Ashish</h3>
         <p class="role">Student</p>
         <a href="profile.php" class="btn">View Profile</a>
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
      <img src="images/pic-1.jpg" class="image" alt="">
      <h3 class="name">Ashish</h3>
      <p class="role">Student</p>
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

<section class="teachers">
   <h1 class="heading">Expert Teachers</h1>

   <form action="" method="post" class="search-tutor">
      <input type="text" name="search_box" placeholder="Search tutors..." required maxlength="100" value="<?php echo htmlspecialchars($search_query); ?>">
      <button type="submit" class="fas fa-search" name="search_tutor"></button>
   </form>

   <div class="box-container">
      <div class="box offer">
         <h3>Become a Tutor</h3>
         <p>Join our platform and share your knowledge with students worldwide. Start your journey as a tutor today!</p>
         <a href="register.php" class="inline-btn">Get Started</a>
      </div>

      <?php if ($result->num_rows > 0): ?>
         <?php while ($teacher = $result->fetch_assoc()): ?>
            <div class="box">
               <div class="tutor">
                  <img src="images/<?php echo htmlspecialchars($teacher['profile_image']); ?>" alt="">
                  <div>
                     <h3><?php echo htmlspecialchars($teacher['name']); ?></h3>
                     <span><?php echo htmlspecialchars($teacher['role']); ?></span>
                  </div>
               </div>
               <p>Total Playlists: <span>4</span></p>
               <p>Total Videos: <span>18</span></p>
               <p>Total Likes: <span>1208</span></p>
               <a href="teacher_profile.php?teacher_id=<?php echo $teacher['id']; ?>" class="inline-btn">View Profile</a>
            </div>
         <?php endwhile; ?>
      <?php else: ?>
         <p class="empty">No teachers found.</p>
      <?php endif; ?>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>