<?php
// filepath: c:\xampp\htdocs\Educa\edit_course.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db_connect.php';

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Debug: Check if course ID is being passed
    echo "Course ID: " . $course_id . "<br>";

    // Fetch course details
    $query = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error fetching course: " . $conn->error);
    }

    $course = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Debug: Check form submission data
        print_r($_POST);
        print_r($_FILES);

        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $thumbnail = $_FILES['thumbnail']['name'];
        $thumbnail_tmp = $_FILES['thumbnail']['tmp_name'];
        $thumbnail_folder = 'images/' . $thumbnail;

        // Handle thumbnail upload
        if (!empty($thumbnail)) {
            if (move_uploaded_file($thumbnail_tmp, $thumbnail_folder)) {
                $query = "UPDATE courses SET title = ?, description = ?, thumbnail = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $title, $description, $thumbnail, $course_id);
            } else {
                $error = "Failed to upload thumbnail.";
            }
        } else {
            $query = "UPDATE courses SET title = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $title, $description, $course_id);
        }

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit;
        } else {
            echo "Error updating course: " . $stmt->error;
        }
    }
} else {
    die("No course ID provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container .box {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container .btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<section class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Edit Course</h3>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($course['title']); ?>" required class="box"><br>
        <label>Description:</label>
        <textarea name="description" required class="box"><?php echo htmlspecialchars($course['description']); ?></textarea><br>
        <label>Thumbnail:</label>
        <input type="file" name="thumbnail" accept="image/*" class="box"><br>
        <button type="submit" class="btn">Update Course</button>
    </form>
</section>
</body>
</html>