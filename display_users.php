<?php
include 'db_connect.php'; // Include your database connection file

// Retrieve user data from database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Display user data
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. " - Email: " . $row["email"]. "<br>";
    }
} else {
    echo "0 results";
}

$conn->close(); // Close the database connection
?>