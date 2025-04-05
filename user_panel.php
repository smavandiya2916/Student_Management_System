<?php
// filepath: c:\xampp\htdocs\Educa\user_panel.php
include 'db_connect.php';

// Temporarily disable login check
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit;
// }

// For testing purposes, set a default user ID
$user_id = 1; // Replace with a valid user ID from your database

// Fetch user details
$query_user = "SELECT * FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Handle course purchase
if (isset($_GET['buy_course_id'])) {
    $course_id = $_GET['buy_course_id'];

    // Fetch course price
    $query = "SELECT price FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();

    if ($course) {
        $price = $course['price'];

        // Insert sale into sales table
        $query = "INSERT INTO sales (user_id, course_id, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iid", $user_id, $course_id, $price);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Course purchased successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error purchasing course: " . $stmt->error . "</p>";
        }
    }
}

// Fetch all courses
$query = "SELECT * FROM courses";
$result = $conn->query($query);

// Fetch purchased courses
$query_purchased = "SELECT courses.title, courses.description, courses.thumbnail, sales.sale_date 
                    FROM sales 
                    JOIN courses ON sales.course_id = courses.id 
                    WHERE sales.user_id = ?";
$stmt_purchased = $conn->prepare($query_purchased);
$stmt_purchased->bind_param("i", $user_id);
$stmt_purchased->execute();
$result_purchased = $stmt_purchased->get_result();

// Fetch progress
$query_progress = "SELECT courses.title, progress.progress_percentage 
                   FROM progress 
                   JOIN courses ON progress.course_id = courses.id 
                   WHERE progress.user_id = ?";
$stmt_progress = $conn->prepare($query_progress);
$stmt_progress->bind_param("i", $user_id);
$stmt_progress->execute();
$result_progress = $stmt_progress->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <!-- User Profile Section -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
            <img src="images/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default.jpg'); ?>" alt="Profile Picture" class="profile-img">
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
    </div>

    <!-- Purchased Courses Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Purchased Courses</h4>
                </div>
                <div class="card-body">
                    <?php if ($result_purchased->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Purchase Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($course = $result_purchased->fetch_assoc()): ?>
                                <tr>
                                    <td><img src="images/<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="Thumbnail" width="50"></td>
                                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                                    <td><?php echo htmlspecialchars($course['description']); ?></td>
                                    <td><?php echo htmlspecialchars($course['sale_date']); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No courses purchased yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Your Progress</h4>
                </div>
                <div class="card-body">
                    <?php if ($result_progress->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($progress = $result_progress->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($progress['title']); ?></td>
                                    <td><?php echo $progress['progress_percentage']; ?>%</td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No progress tracked yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <h1 class="text-center">Available Courses</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($course = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['description']); ?></td>
                <td>$<?php echo htmlspecialchars($course['price']); ?></td>
                <td>
                    <a href="?buy_course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-sm">Buy</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>