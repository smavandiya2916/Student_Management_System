<?php
include 'db_connect.php';
session_start();

// Check if the user is already logged in
if (!isset($_SESSION['user_id'])) {
    // Check if the user_id is provided in the URL
    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']); // Sanitize input

        // Fetch user details from the database
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Set session variables to log in the user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
        } else {
            die("Invalid link. User not found.");
        }
    } else {
        die("Access denied. Please log in.");
    }
}

// Fetch the logged-in user's details
$logged_in_user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Fetch the user's enrolled courses
$query_courses = "SELECT courses.title, courses.description, progress.progress_percentage 
                  FROM progress 
                  JOIN courses ON progress.course_id = courses.id 
                  WHERE progress.user_id = ?";
$stmt_courses = $conn->prepare($query_courses);
$stmt_courses->bind_param("i", $logged_in_user_id);
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

// Fetch the user's assigned homework
$query_homework = "SELECT courses.title AS course_title, homework.homework_description, homework.due_date, homework.status 
                   FROM homework 
                   JOIN courses ON homework.course_id = courses.id 
                   WHERE homework.user_id = ?";
$stmt_homework = $conn->prepare($query_homework);
$stmt_homework->bind_param("i", $logged_in_user_id);
$stmt_homework->execute();
$result_homework = $stmt_homework->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .dashboard-header {
            background-color: #35424a;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px auto;
        }
        .card {
            margin: 20px 0;
        }
        .card-header {
            background-color: #35424a;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #35424a;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2c3e50;
        }
        .todo-list {
            list-style: none;
            padding: 0;
        }
        .todo-list li {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .todo-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <img src="images/<?php echo htmlspecialchars($user['profile_picture'] ?: 'default.jpg'); ?>" alt="Profile Picture" class="profile-img">
        <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
    </div>

    <div class="container">
        <!-- Enrolled Courses Section -->
        <div class="card">
            <div class="card-header">
                <h4>Your Enrolled Courses</h4>
            </div>
            <div class="card-body">
                <?php if ($result_courses->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Course Title</th>
                                <th>Description</th>
                                <th>Progress (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($course = $result_courses->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td><?php echo htmlspecialchars($course['description']); ?></td>
                                <td><?php echo $course['progress_percentage']; ?>%</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You are not enrolled in any courses yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Homework Section -->
        <div class="card">
            <div class="card-header">
                <h4>Your Homework</h4>
            </div>
            <div class="card-body">
                <?php if ($result_homework->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Description</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($homework = $result_homework->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($homework['course_title']); ?></td>
                                <td><?php echo htmlspecialchars($homework['homework_description']); ?></td>
                                <td><?php echo htmlspecialchars($homework['due_date']); ?></td>
                                <td><?php echo htmlspecialchars($homework['status']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No homework assigned yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- To-Do List Section -->
        <div class="card">
            <div class="card-header">
                <h4>To-Do List</h4>
            </div>
            <div class="card-body">
                <ul class="todo-list">
                    <li>Complete the "Introduction to Programming" course.</li>
                    <li>Submit the assignment for "Web Development Bootcamp".</li>
                    <li>Watch the "Data Science with R" videos.</li>
                    <li>Prepare for the "Machine Learning Basics" quiz.</li>
                </ul>
            </div>
        </div>

        <!-- Pending Work Section -->
        <div class="card">
            <div class="card-header">
                <h4>Pending Work</h4>
            </div>
            <div class="card-body">
                <p>You have 3 pending assignments and 2 quizzes to complete.</p>
                <a href="assignments.php" class="btn btn-primary">View Assignments</a>
            </div>
        </div>
    </div>
</body>
</html>