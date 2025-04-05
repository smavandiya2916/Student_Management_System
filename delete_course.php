<?php
// filepath: c:\xampp\htdocs\Educa\delete_course.php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    $query = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>