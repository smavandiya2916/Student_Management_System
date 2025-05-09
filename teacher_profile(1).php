<?php
include 'db_connect.php'; // Include the database connection file

// Fetch teacher details dynamically using the teacher ID from the URL
$teacher_id = isset($_GET['teacher_id']) ? intval($_GET['teacher_id']) : 1;

$query = "SELECT * FROM users WHERE id = ? AND role = 'teacher'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
} else {
    die("Teacher not found.");
}

// Fetch courses taught by the teacher
$courses_query = "SELECT * FROM courses WHERE tutor_name = ?";
$courses_stmt = $conn->prepare($courses_query);
$courses_stmt->bind_param("s", $teacher['name']);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($teacher['name']); ?>'s Profile</title>

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
         <img src="images/<?php echo htmlspecialchars($teacher['profile_image']); ?>" class="image" alt="">
         <h3 class="name"><?php echo htmlspecialchars($teacher['name']); ?></h3>
         <p class="role"><?php echo htmlspecialchars($teacher['role']); ?></p>
         <a href="profile.php" class="btn">View Profile</a>
      </div>
   </section>
</header>   

<div class="side-bar">
   <div id="close-btn">
      <i class="fas fa-times"></i>
   </div>

   <div class="profile">
      <img src="images/<?php echo htmlspecialchars($teacher['profile_image']); ?>" class="image" alt="">
      <h3 class="name"><?php echo htmlspecialchars($teacher['name']); ?></h3>
      <p class="role"><?php echo htmlspecialchars($teacher['role']); ?></p>
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

<section class="teacher-profile">
   <h1 class="heading">Profile Details</h1>

   <div class="details">
      <div class="tutor">
         <img src="images/<?php echo htmlspecialchars($teacher['profile_image']); ?>" alt="">
         <h3><?php echo htmlspecialchars($teacher['name']); ?></h3>
         <span><?php echo htmlspecialchars($teacher['role']); ?></span>
      </div>
      <div class="flex">
         <p>Total Playlists: <span>4</span></p>
         <p>Total Videos: <span>18</span></p>
         <p>Total Likes: <span>1208</span></p>
         <p>Total Comments: <span>52</span></p>
      </div>
   </div>
</section>

<section class="courses">
   <h1 class="heading">Courses by <?php echo htmlspecialchars($teacher['name']); ?></h1>

   <div class="box-container">
      <?php while ($course = $courses_result->fetch_assoc()): ?>
         <div class="box">
            <div class="thumb">
               <img src="images/<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="">
               <span><?php echo htmlspecialchars($course['video_count']); ?> videos</span>
            </div>
            <h3 class="title"><?php echo htmlspecialchars($course['title']); ?></h3>
            <a href="playlist.php?course_id=<?php echo $course['id']; ?>" class="inline-btn">View Playlist</a>
         </div>
      <?php endwhile; ?>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- Custom JS -->
<script src="js/script.js"></script>

</body>
</html>