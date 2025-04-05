<?php
include 'db_connect.php'; // Include the database connection file
if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Course Management</title>

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
         <input type="text" name="search_query" required placeholder="Search courses..." maxlength="100">
         <button type="submit" class="fas fa-search"></button>
      </form>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="search-btn" class="fas fa-search"></div>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="toggle-btn" class="fas fa-sun"></div>
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
      <p class="role">studen</p>
      <a href="profile.php" class="btn">view profile</a>
   </div>
   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>
</div>

<section class="courses">
   <h1 class="heading">Course Management</h1>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header bg-primary text-white">
               <h4>Course Management</h4>
            </div>
            <div class="card-body">
               <div class="row">
                  <?php
                  $query = "SELECT * FROM courses";
                  $result_courses = $conn->query($query);
                  if ($result_courses->num_rows > 0):
                     while ($course = $result_courses->fetch_assoc()):
                  ?>
                  <div class="col-md-4 mb-4">
                     <div class="card h-100">
                        <img src="images/<?php echo htmlspecialchars($course['thumbnail']); ?>" class="card-img-top" alt="Course Thumbnail" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                           <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                           <p class="card-text"><?php echo htmlspecialchars($course['description']); ?></p>
                        </div>
                        <div class="card-footer text-center">
                           <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                           <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                        </div>
                     </div>
                  </div>
                  <?php
                     endwhile;
                  else:
                  ?>
                  <tr>
                     <td colspan="4">No courses available.</td>
                  </tr>
                  <?php endif; ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<script src="js/script.js"></script>
</body>
</html>