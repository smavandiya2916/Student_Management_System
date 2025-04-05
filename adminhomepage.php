<?php
// filepath: c:\xampp\htdocs\Educa\adminhomepage.php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .header {
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header .btn {
            background: #fff;
            color: #007bff;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .header .btn:hover {
            background: #0056b3;
            color: #fff;
        }

        .dashboard {
            padding: 20px;
        }

        .dashboard h1 {
            margin-bottom: 20px;
        }

        .dashboard .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .dashboard .card h3 {
            margin: 0 0 10px;
        }

        .dashboard .card p {
            margin: 0 0 15px;
        }

        .dashboard .card .btn {
            background: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .dashboard .card .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="logo">Admin Dashboard</div>
    <a href="logout.php" class="btn">Logout</a>
</header>

<section class="dashboard">
    <h1>Welcome, Admin!</h1>
    <div class="card">
        <h3>Manage Users</h3>
        <p>View, edit, or delete user accounts.</p>
        <a href="admin.php" class="btn">Go to User Management</a>
    </div>
    <div class="card">
        <h3>Manage Courses</h3>
        <p>View, edit, or delete courses.</p>
        <a href="courses.php" class="btn">Go to Course Management</a>
    </div>
</section>
</body>
</html>