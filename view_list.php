<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_signup.html");
    exit();
}

$list_id = $_GET['id'];

$sql = "SELECT name FROM lists WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $list_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($list_name);
$stmt->fetch();
$stmt->close();

$sql = "SELECT response_code, image_url FROM list_items WHERE list_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $list_id);
$stmt->execute();
$result = $stmt->get_result();
$list_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View List - <?php echo htmlspecialchars($list_name); ?></title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <?php include '../nav.php'; ?>
        <h3>List Name : <?php echo htmlspecialchars($list_name); ?></h3>
        <div class="grid-container">
            <?php foreach ($list_items as $item): ?>
                <div class="grid-item">
                    <h3><?php echo $item['response_code']; ?></h3>
                    <img src="<?php echo $item['image_url']; ?>" alt="Response code <?php echo $item['response_code']; ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <a href="lists.php" class="button">Back to Lists</a>
    </div>
</body>
</html>
