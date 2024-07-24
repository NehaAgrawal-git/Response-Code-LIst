<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../home/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Search for HTTP Response Codes</h2>
            <form action="search_results.php" method="get">
                <label for="filter">Filter:</label>
                <input type="text" id="filter" name="filter" required>
                <br>
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
</body>
</html>
