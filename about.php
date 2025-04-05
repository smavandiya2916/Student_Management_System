<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about us</title>

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
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>
      <div class="content">
         <h3>why choose us?</h3>
         <p>We provide the best learning experience with expert tutors, a wide range of courses, and guaranteed job placement.</p>
         <a href="courses.php" class="inline-btn">our courses</a>
      </div>
   </div>

   <div class="box-container">
      <?php
      $query = "SELECT * FROM about_us";
      $result = $conn->query($query);
      while ($row = $result->fetch_assoc()):
      ?>
         <div class="box">
            <i class="<?php echo htmlspecialchars($row['icon']); ?>"></i>
            <div>
               <h3><?php echo htmlspecialchars($row['title']); ?></h3>
               <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
         </div>
      <?php endwhile; ?>
   </div>
</section>

<section class="reviews">
   <h1 class="heading">student's reviews</h1>
   <div class="box-container">
      <?php
      $query = "SELECT * FROM reviews";
      $result = $conn->query($query);
      while ($row = $result->fetch_assoc()):
      ?>
         <div class="box">
            <p><?php echo htmlspecialchars($row['review']); ?></p>
            <div class="student">
               <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="">
               <div>
                  <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                  <div class="stars">
                     <?php for ($i = 0; $i < $row['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                     <?php endfor; ?>
                     <?php if ($row['rating'] < 5): ?>
                        <i class="fas fa-star-half-alt"></i>
                     <?php endif; ?>
                  </div>
               </div>
            </div>
         </div>
      <?php endwhile; ?>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>

   
</body>
</html>