<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch latest saved list for the user from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, creation_date FROM lists WHERE user_id = ? ORDER BY creation_date DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$saved_lists = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <?php include '../nav.php'; ?>

        <div class="main-content">
            <div class="form-container">
                <h2>Search for HTTP Response Codes</h2>
                <form action="../search/search_results.php" method="get">
                    <label for="filter">Filter:</label>
                    <input type="text" id="filter" name="filter" required>
                    <br>
                    <button type="submit" class="button">Search</button>
                </form>
            </div>

            <div class="saved-lists-container">
                <h2>Your Saved Lists</h2>
                <?php if (count($saved_lists) > 0): ?>
                    <ul class="saved-lists">
                        <?php foreach ($saved_lists as $list): ?>
                            <li>
                                <a href="../list/view_list.php?id=<?php echo $list['id']; ?>">
                                    <?php echo htmlspecialchars($list['name']); ?>
                                </a>
                                <span><?php echo htmlspecialchars($list['creation_date']); ?></span>
                                <a href="../list/delete_list.php?id=<?php echo $list['id']; ?>" class="delete-link">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="../list/lists.php" class="button">View All</a>
                <?php else: ?>
                    <p>You have no saved lists.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
