<?php
include 'db_connect.php'; // Include the database connection file
session_start();

// Check if the playlist_id is provided in the URL
if (isset($_GET['playlist_id'])) {
    $playlist_id = intval($_GET['playlist_id']); // Sanitize the input

    // Fetch playlist details
    $query = "SELECT * FROM playlists WHERE playlist_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $playlist_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $playlist = $result->fetch_assoc();
    } else {
        die("Playlist not found.");
    }

    // Fetch videos in the playlist
    $query_videos = "SELECT * FROM videos WHERE playlist_id = ?";
    $stmt_videos = $conn->prepare($query_videos);
    $stmt_videos->bind_param("i", $playlist_id);
    $stmt_videos->execute();
    $result_videos = $stmt_videos->get_result();
} else {
    die("No playlist selected.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($playlist['title']); ?> - Playlist</title>

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

<section class="playlist-details">
   <h1 class="heading">Playlist Details</h1>

   <div class="row">
      <div class="column">
         <form action="save_playlist.php" method="post" class="save-playlist">
            <button type="submit" name="save_playlist" value="<?php echo $playlist_id; ?>"><i class="far fa-bookmark"></i> <span>Save Playlist</span></button>
         </form>
   
         <div class="thumb">
            <img src="images/<?php echo htmlspecialchars($playlist['thumbnail']); ?>" alt="">
            <span><?php echo htmlspecialchars($playlist['video_count']); ?> videos</span>
         </div>
      </div>
      <div class="column">
         <div class="tutor">
            <img src="images/<?php echo htmlspecialchars($playlist['tutor_image']); ?>" alt="">
            <div>
               <h3><?php echo htmlspecialchars($playlist['tutor_name']); ?></h3>
               <span><?php echo htmlspecialchars($playlist['date']); ?></span>
            </div>
         </div>
   
         <div class="details">
            <h3><?php echo htmlspecialchars($playlist['title']); ?></h3>
            <p><?php echo htmlspecialchars($playlist['description']); ?></p>
            <a href="teacher_profile.php?teacher_id=<?php echo $playlist['tutor_id']; ?>" class="inline-btn">View Profile</a>
         </div>
      </div>
   </div>
</section>

<section class="playlist-videos">
   <h1 class="heading">Playlist Videos</h1>

   <div class="box-container">
      <?php if ($result_videos->num_rows > 0): ?>
         <?php while ($video = $result_videos->fetch_assoc()): ?>
            <a class="box" href="watch-video.php?video_id=<?php echo $video['id']; ?>">
               <i class="fas fa-play"></i>
               <img src="images/<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="">
               <h3><?php echo htmlspecialchars($video['title']); ?></h3>
            </a>
         <?php endwhile; ?>
      <?php else: ?>
         <p>No videos found in this playlist.</p>
      <?php endif; ?>
   </div>
</section>

<section class="playlists">
   <h1 class="heading">Playlists</h1>
   <div class="box-container">
      <?php
      $query = "SELECT * FROM playlists";
      $result = $conn->query($query);
      if ($result->num_rows > 0):
         while ($playlist = $result->fetch_assoc()):
      ?>
         <div class="box">
            <h3><?php echo htmlspecialchars($playlist['title']); ?></h3>
            <p><?php echo htmlspecialchars($playlist['description']); ?></p>
         </div>
      <?php
         endwhile;
      else:
      ?>
         <p>No playlists available.</p>
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