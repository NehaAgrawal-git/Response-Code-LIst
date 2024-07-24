<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_signup.html");
    exit();
}

$filter = $_GET['filter'];
$response_codes = generate_response_codes($filter);

$images = [];
foreach ($response_codes as $code) {
    $images[$code] = "https://http.dog/{$code}.jpg";
}

function generate_response_codes($filter) {
    $all_codes = [
        100, 101, 102, 103, 
        200, 201, 202, 203, 204, 205, 206, 207, 208, 218, 226,
        300, 301, 302, 303, 304, 305, 306, 307, 308,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 440, 444, 449, 450, 451, 460, 463, 464, 494, 495,496,497,498,499,
        500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 520, 521,522,523,524,525,526,527,529,530,561,598,599,
        999
    ];

    if (preg_match('/^\d{3}$/', $filter)) {
        return in_array((int)$filter, $all_codes) ? [(int)$filter] : [];
    }

    $filtered_codes = [];
    $pattern = str_replace('x', '\d', $filter);
    foreach ($all_codes as $code) {
        if (preg_match("/^$pattern$/", (string)$code)) {
            $filtered_codes[] = $code;
        }
    }
    return $filtered_codes;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <?php include '../nav.php'; ?>
        <h2>Search Results</h2>
        <?php if (empty($images)): ?>
            <p>No matching response codes found.</p>
        <?php else: ?>
            <div class="grid-container">
                <?php foreach ($images as $code => $url): ?>
                    <div class="grid-item">
                        <h3><?php echo $code; ?></h3>
                        <img src="<?php echo $url; ?>" alt="Response code <?php echo $code; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-container">
                <form action="../list/save_list.php" method="post">
                    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                    <input type="text" name="list_name" placeholder="List Name" required>
                    <button type="submit">Save List</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
