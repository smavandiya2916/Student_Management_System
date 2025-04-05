<?php
include 'db_connect.php'; // Include the database connection file

// Fetch video details dynamically using the video ID from the URL
$video_id = isset($_GET['video_id']) ? intval($_GET['video_id']) : 1;

$query = "SELECT * FROM videos WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $video_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $video = $result->fetch_assoc();
} else {
    die("Video not found.");
}

// Fetch comments for the video
$comments_query = "SELECT * FROM comments WHERE video_id = ?";
$comments_stmt = $conn->prepare($comments_query);
$comments_stmt->bind_param("i", $video_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($video['title']); ?></title>

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

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
   </nav>
</div>

<section class="watch-video">
   <div class="video-container">
      <div class="video">
         <video src="videos/<?php echo htmlspecialchars($video['video_file']); ?>" controls poster="images/<?php echo htmlspecialchars($video['thumbnail']); ?>" id="video"></video>
      </div>
      <h3 class="title"><?php echo htmlspecialchars($video['title']); ?></h3>
      <div class="info">
         <p class="date"><i class="fas fa-calendar"></i><span><?php echo htmlspecialchars($video['upload_date']); ?></span></p>
         <p class="date"><i class="fas fa-heart"></i><span><?php echo htmlspecialchars($video['likes']); ?> likes</span></p>
      </div>
      <div class="tutor">
         <img src="images/<?php echo htmlspecialchars($video['tutor_image']); ?>" alt="">
         <div>
            <h3><?php echo htmlspecialchars($video['tutor_name']); ?></h3>
            <span><?php echo htmlspecialchars($video['tutor_role']); ?></span>
         </div>
      </div>
      <form action="like_video.php" method="post" class="flex">
         <a href="playlist.php?playlist_id=<?php echo htmlspecialchars($video['playlist_id']); ?>" class="inline-btn">View Playlist</a>
         <button type="submit" name="like_video" value="<?php echo $video_id; ?>"><i class="far fa-heart"></i><span>Like</span></button>
      </form>
      <p class="description"><?php echo htmlspecialchars($video['description']); ?></p>
   </div>
</section>

<section class="comments">
   <h1 class="heading"><?php echo $comments_result->num_rows; ?> Comments</h1>

   <form action="add_comment.php" method="post" class="add-comment">
      <h3>Add Comment</h3>
      <textarea name="comment_box" placeholder="Enter your comment" required maxlength="1000" cols="30" rows="10"></textarea>
      <input type="submit" value="Add Comment" class="inline-btn" name="add_comment">
   </form>

   <h1 class="heading">User Comments</h1>

   <div class="box-container">
      <?php while ($comment = $comments_result->fetch_assoc()) { ?>
      <div class="box">
         <div class="user">
            <img src="images/<?php echo htmlspecialchars($comment['user_image']); ?>" alt="">
            <div>
               <h3><?php echo htmlspecialchars($comment['user_name']); ?></h3>
               <span><?php echo htmlspecialchars($comment['comment_date']); ?></span>
            </div>
         </div>
         <div class="comment-box"><?php echo htmlspecialchars($comment['comment_text']); ?></div>
         <form action="edit_comment.php" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
            <input type="submit" value="Edit Comment" name="edit_comment" class="inline-option-btn">
            <input type="submit" value="Delete Comment" name="delete_comment" class="inline-delete-btn">
         </form>
      </div>
      <?php } ?>
   </div>
</section>

<footer class="footer">
   &copy; <?php echo date('Y'); ?> <span>Educa</span> | All rights reserved!
</footer>

<!-- Custom JS -->
<script src="js/script.js"></script>
   
</body>
</html>