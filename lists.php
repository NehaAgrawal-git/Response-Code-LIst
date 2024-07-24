<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/login_signup.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT id, name, creation_date FROM lists WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lists = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Lists</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            width: 100%;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        nav {
            background-color: #007bff;
            padding: 10px;
            width: 100%;
            margin-bottom: 20px;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around;
        }
        nav ul li {
            display: inline;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
        }
        nav ul li a:hover {
            background-color: #0056b3;
            border-radius: 5px;
        }
        .main-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .lists-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .list {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            width: calc(50% - 20px);
        }
        .list h2 {
            margin-bottom: 10px;
        }
        .list-actions {
            margin-top: 10px;
        }
        .list-actions a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }
        .list-actions form {
            display: inline;
        }
        .list-actions button {
            padding: 5px 10px;
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .list-actions button:hover {
            background-color: #cc0000;
        }
        button {
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <div class="container">
        <?php include '../nav.php'; ?>

        <div class="main-content">
            <h1>My Lists</h1>
            <div class="lists-container">
                <?php if (count($lists) > 0): ?>
                    <div class="lists">
                        <?php foreach ($lists as $list): ?>
                            <div class="list">
                                <h2><?php echo htmlspecialchars($list['name']); ?></h2>
                                <p>Created on: <?php echo date('F j, Y', strtotime($list['creation_date'])); ?></p>
                                <div class="list-actions">
                                    <a class="button" href="view_list.php?id=<?php echo $list['id']; ?>">View</a>
                                    <a class="button" href="edit_list.php?id=<?php echo $list['id']; ?>">Edit</a>
                                    <form action="delete_list.php" method="get" onsubmit="return confirm('Are you sure you want to delete this list?');">
                                        <input type="hidden" name="id" class="button" value="<?php echo $list['id']; ?>">
                                        <button type="submit" name="delete_list">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No lists found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
