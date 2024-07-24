<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/login.php");
    exit();
}

// Handle form submission for updating list name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_list'])) {
    $list_id = $_POST['list_id'];
    $list_name = $_POST['list_name'];

    // Update list name in the database
    $stmt = $conn->prepare("UPDATE lists SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $list_name, $list_id);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for adding item to list
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $list_id = $_POST['list_id'];
    $response_code = $_POST['response_code'];

    // Fetch image URL for the response code
    $item_image = "https://http.dog/{$response_code}.jpg";

    // Add item to the list_items table along with image URL
    if ($item_image !== null) {
        $stmt = $conn->prepare("INSERT INTO list_items (list_id, response_code, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $list_id, $response_code, $item_image);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Failed to fetch image for response code: " . htmlspecialchars($response_code);
    }
}

// Handle form submission for removing item from list
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $list_id = $_POST['list_id'];
    $item_id = $_POST['item_id'];

    // Remove item from the list_items table
    $stmt = $conn->prepare("DELETE FROM list_items WHERE list_id = ? AND id = ?");
    $stmt->bind_param("ii", $list_id, $item_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch list details based on list ID from query parameter
if (isset($_GET['id'])) {
    $list_id = $_GET['id'];
    
    // Fetch list details
    $stmt = $conn->prepare("SELECT name FROM lists WHERE id = ?");
    $stmt->bind_param("i", $list_id);
    $stmt->execute();
    $stmt->bind_result($list_name);
    $stmt->fetch();
    $stmt->close();

    // Fetch items in the list
    $stmt = $conn->prepare("SELECT id, response_code FROM list_items WHERE list_id = ?");
    $stmt->bind_param("i", $list_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Redirect if ID is not provided
    header("Location: ../home/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit List</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        
        <?php include '../nav.php'; ?>
        <div class="main-content">
            <div class="form-container">
                <h2>Edit List</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
                    <label for="list_name">List Name:</label>
                    <input type="text" id="list_name" name="list_name" value="<?php echo htmlspecialchars($list_name); ?>" required>
                    <br>
                    <button type="submit" name="edit_list">Save Changes</button>
                </form>
            </div>

            <div class="items-container">
                <h2>List Items</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
                    <label for="response_code">Response Code:</label>
                    <input type="text" id="response_code" name="response_code" required>
                    <button type="submit" name="add_item">Add Item</button>
                </form>

                <?php if (count($items) > 0): ?>
                    <ul class="item-list">
                        <?php foreach ($items as $item): ?>
                            <li>
                                <span><?php echo htmlspecialchars($item['response_code']); ?></span>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="remove_item">Remove</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No items in the list.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
