<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php'; // Include the database connection file

// Handle form submissions for adding users
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt the password
        $role = htmlspecialchars($_POST['role']);
        $profile_picture = $_FILES['profile_picture']['name'];
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        $profile_picture_folder = 'images/' . $profile_picture;

        // Upload profile picture
        if (!empty($profile_picture)) {
            if (!move_uploaded_file($profile_picture_tmp, $profile_picture_folder)) {
                $error = "Failed to upload profile picture.";
                $profile_picture = 'default.jpg'; // Use default image if upload fails
            }
        } else {
            $profile_picture = 'default.jpg'; // Use default image if no file is uploaded
        }

        // Insert user data into the database
        $query = "INSERT INTO users (name, email, password, role, profile_picture) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $name, $email, $password, $role, $profile_picture);

        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

// Fetch all users for display
$query = "SELECT * FROM users";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching users: " . $conn->error);
}

// Fetch all courses for display
$query_courses = "SELECT * FROM courses";
$result_courses = $conn->query($query_courses);
if (!$result_courses) {
    die("Error fetching courses: " . $conn->error);
}

// Fetch student progress
$query_progress = "SELECT 
                    users.name AS student_name, 
                    courses.title AS course_title, 
                    progress.progress_percentage 
                  FROM progress 
                  JOIN users ON progress.user_id = users.user_id 
                  JOIN courses ON progress.course_id = courses.id";
$result_progress = $conn->query($query_progress);

if (!$result_progress) {
    die("Error fetching student progress: " . $conn->error);
}

// Fetch all students
$query_students = "SELECT * FROM users WHERE role = 'student'";
$result_students = $conn->query($query_students);

// Handle form submission for assigning homework
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_homework'])) {
    $user_id = intval($_POST['user_id']);
    $course_id = intval($_POST['course_id']);
    $homework_description = $_POST['homework_description'];
    $due_date = $_POST['due_date'];

    $query = "INSERT INTO homework (user_id, course_id, homework_description, due_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $user_id, $course_id, $homework_description, $due_date);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Homework assigned successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error assigning homework: " . $stmt->error . "</p>";
    }
}

// Handle form submission for marking attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $user_id = intval($_POST['user_id_attendance']);
    $course_id = intval($_POST['course_id_attendance']);
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    $query = "INSERT INTO attendance (user_id, course_id, attendance_date, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $user_id, $course_id, $attendance_date, $status);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Attendance marked successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error marking attendance: " . $stmt->error . "</p>";
    }
}

// Handle form submission for uploading video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_video'])) {
    $video_title = htmlspecialchars($_POST['video_title']);
    $playlist_id = intval($_POST['playlist_id']);

    // Handle video file upload
    $video_file = $_FILES['video_file']['name'];
    $video_tmp = $_FILES['video_file']['tmp_name'];
    $video_folder = 'videos/' . $video_file;

    if (!move_uploaded_file($video_tmp, $video_folder)) {
        echo "<p style='color: red;'>Failed to upload video file.</p>";
        exit;
    }

    // Handle thumbnail upload
    $thumbnail = $_FILES['thumbnail']['name'];
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
    $thumbnail_folder = 'thumbnails/' . $thumbnail;

    if (!empty($thumbnail)) {
        if (!move_uploaded_file($thumbnail_tmp, $thumbnail_folder)) {
            echo "<p style='color: red;'>Failed to upload thumbnail.</p>";
            $thumbnail = 'default.jpg'; // Use default thumbnail if upload fails
        }
    } else {
        $thumbnail = 'default.jpg'; // Use default thumbnail if no file is uploaded
    }

    // Insert video details into the database
    $query = "INSERT INTO videos (title, video_file, thumbnail, playlist_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $video_title, $video_file, $thumbnail, $playlist_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Video uploaded successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error uploading video: " . $stmt->error . "</p>";
    }
}

// Handle form submission for uploading course image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    $course_id = intval($_POST['course_id']);

    // Handle image upload
    $thumbnail = $_FILES['thumbnail']['name'];
    $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
    $thumbnail_folder = 'images/' . $thumbnail;

    if (!move_uploaded_file($thumbnail_tmp, $thumbnail_folder)) {
        echo "<p style='color: red;'>Failed to upload image.</p>";
        exit;
    }

    // Update the course with the uploaded image
    $query = "UPDATE courses SET thumbnail = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $thumbnail, $course_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Image uploaded successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating course: " . $stmt->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }

        .navbar a {
            color: #fff !important;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            border-bottom: 1px solid #ddd;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .card-text {
            font-size: 0.9rem;
            color: #666;
        }

        .card-footer .btn {
            margin: 0 5px;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #007bff;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Educa Admin</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Dashboard Header -->
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center mb-4">Admin Dashboard</h1>
        </div>
    </div>

    <!-- User Management Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>User Management</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profile Picture</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>User URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td>
                                    <?php if (!empty($user['profile_picture'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img">
                                    <?php else: ?>
                                        <img src="images/default.jpg" alt="Default Profile Picture" class="profile-img">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td>
                                    <a href="dashboard.php?user_id=<?php echo $user['user_id']; ?>" target="_blank">
                                        http://localhost/Educa/dashboard.php?user_id=<?php echo $user['user_id']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Management Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Course Management</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php while ($course = $result_courses->fetch_assoc()): ?>
                            <div class="col-md-3 mb-4">
                                <div class="card h-100">
                                    <img src="images/<?php echo htmlspecialchars($course['thumbnail']); ?>" class="card-img-top" alt="Course Thumbnail" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($course['title']); ?></h5>
                                        <p class="card-text text-truncate" style="max-height: 50px; overflow: hidden;"><?php echo htmlspecialchars($course['description']); ?></p>
                                    </div>
                                    <div class="card-footer text-center">
                                        <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Progress Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Student Progress</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch student progress
                    $query = "SELECT 
                                users.name AS student_name, 
                                courses.title AS course_title, 
                                progress.progress_percentage 
                              FROM progress 
                              JOIN users ON progress.user_id = users.user_id 
                              JOIN courses ON progress.course_id = courses.id";
                    $result = $conn->query($query);
                    ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                                <td><?php echo $row['progress_percentage']; ?>%</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Financial Overview</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch financial data
                    $query = "SELECT 
                                COUNT(*) AS total_sales, 
                                SUM(price) AS total_revenue, 
                                DATE(sale_date) AS sale_date 
                              FROM sales 
                              GROUP BY DATE(sale_date)";
                    $result = $conn->query($query);

                    $total_sales = 0;
                    $total_revenue = 0;
                    while ($row = $result->fetch_assoc()) {
                        $total_sales += $row['total_sales'];
                        $total_revenue += $row['total_revenue'];
                    }
                    ?>
                    <p><strong>Total Sales:</strong> <?php echo $total_sales; ?></p>
                    <p><strong>Total Revenue:</strong> $<?php echo number_format($total_revenue, 2); ?></p>
                    <h5>Day-Wise Sales</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sales</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result->data_seek(0); // Reset result pointer
                            while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['sale_date']; ?></td>
                                <td><?php echo $row['total_sales']; ?></td>
                                <td>$<?php echo number_format($row['total_revenue'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Homework Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Assign Homework</h4>
                </div>
                <div class="card-body">
                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Student</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Select Student --</option>
                                <?php while ($student = $result_students->fetch_assoc()): ?>
                                    <option value="<?php echo $student['user_id']; ?>">
                                        <?php echo htmlspecialchars($student['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Select Course</label>
                            <select name="course_id" id="course_id" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                <?php while ($course = $result_courses->fetch_assoc()): ?>
                                    <option value="<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="homework_description" class="form-label">Homework Description</label>
                            <textarea name="homework_description" id="homework_description" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" required>
                        </div>

                        <button type="submit" name="assign_homework" class="btn btn-primary">Assign Homework</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Attendance Section -->
    <div class="container mt-4">
        <h2>Mark Attendance</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="user_id_attendance" class="form-label">Select Student</label>
                <select name="user_id_attendance" id="user_id_attendance" class="form-select" required>
                    <option value="">-- Select Student --</option>
                    <?php
                    $result_students->data_seek(0); // Reset the result pointer
                    while ($student = $result_students->fetch_assoc()): ?>
                        <option value="<?php echo $student['user_id']; ?>">
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="course_id_attendance" class="form-label">Select Course</label>
                <select name="course_id_attendance" id="course_id_attendance" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php
                    $result_courses->data_seek(0); // Reset the result pointer
                    while ($course = $result_courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['id']; ?>">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="attendance_date" class="form-label">Date</label>
                <input type="date" name="attendance_date" id="attendance_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                </select>
            </div>

            <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
        </form>
    </div>

    <!-- Uploaded Videos Section -->
    <div class="container mt-4">
        <h2>Uploaded Videos</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Video File</th>
                    <th>Thumbnail</th>
                    <th>Playlist</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_videos = "SELECT videos.video_id, videos.title, videos.video_file, videos.thumbnail, playlists.title AS playlist_title 
                                 FROM videos 
                                 JOIN playlists ON videos.playlist_id = playlists.playlist_id";
                $result_videos = $conn->query($query_videos);
                while ($video = $result_videos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $video['video_id']; ?></td>
                        <td><?php echo htmlspecialchars($video['title']); ?></td>
                        <td><a href="videos/<?php echo htmlspecialchars($video['video_file']); ?>" target="_blank">View Video</a></td>
                        <td><img src="thumbnails/<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="Thumbnail" width="50"></td>
                        <td><?php echo htmlspecialchars($video['playlist_title']); ?></td>
                        <td>
                            <a href="edit_video.php?id=<?php echo $video['video_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_video.php?id=<?php echo $video['video_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this video?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Upload Video Section -->
    <div class="container mt-4">
        <h2>Upload Video</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="video_title" class="form-label">Video Title</label>
                <input type="text" name="video_title" id="video_title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="video_file" class="form-label">Upload Video</label>
                <input type="file" name="video_file" id="video_file" class="form-control" accept="video/*" required>
            </div>

            <div class="mb-3">
                <label for="thumbnail" class="form-label">Upload Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="playlist_id" class="form-label">Assign to Playlist</label>
                <select name="playlist_id" id="playlist_id" class="form-select" required>
                    <option value="">-- Select Playlist --</option>
                    <?php
                    $query_playlists = "SELECT * FROM playlists";
                    $result_playlists = $conn->query($query_playlists);
                    while ($playlist = $result_playlists->fetch_assoc()): ?>
                        <option value="<?php echo $playlist['playlist_id']; ?>">
                            <?php echo htmlspecialchars($playlist['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" name="upload_video" class="btn btn-primary">Upload Video</button>
        </form>
    </div>

    <!-- Upload Course Image Section -->
    <div class="container mt-4">
        <h2>Upload Course Image</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="course_id" class="form-label">Select Course</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php
                    $query_courses = "SELECT * FROM courses";
                    $result_courses = $conn->query($query_courses);
                    while ($course = $result_courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['id']; ?>">
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="thumbnail" class="form-label">Upload Image</label>
                <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" name="upload_image" class="btn btn-primary">Upload Image</button>
        </form>
    </div>
</div>

<!-- Footer -->
<footer>
    &copy; <?php echo date('Y'); ?> Educa | All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>