<?php
include 'db_connect.php'; // Include your database connection

$search_query = $_GET['search_query'] ?? '';
$search_query = $conn->real_escape_string($search_query);

$query = "SELECT * FROM courses WHERE title LIKE '%$search_query%'";
$result = $conn->query($query);

while ($course = $result->fetch_assoc()) {
    echo "<h3>" . htmlspecialchars($course['title']) . "</h3>";
}
?>