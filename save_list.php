<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_signup.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filter = $_POST['filter'];
    $list_name = $_POST['list_name'];
    $user_id = $_SESSION['user_id'];
    $creation_date = date('Y-m-d H:i:s');

    // Generate response codes based on the filter
    $response_codes = generate_response_codes($filter);

    // Prepare images array
    $images = [];
    foreach ($response_codes as $code) {
        $images[$code] = "https://http.dog/{$code}.jpg";
    }

    // Insert list details into the database
    $sql = "INSERT INTO lists (user_id, name, creation_date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $list_name, $creation_date);

    if ($stmt->execute()) {
        $list_id = $stmt->insert_id;

        // Insert each response code and its image into list_items table
        $sql = "INSERT INTO list_items (list_id, response_code, image_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($images as $code => $url) {
            $stmt->bind_param("iis", $list_id, $code, $url);
            $stmt->execute();
        }

        header("Location: lists.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

function generate_response_codes($filter) {
    $all_codes = [
        100, 101, 102, 103, 
        200, 201, 202, 203, 204, 205, 206, 207, 208, 226,
        300, 301, 302, 303, 304, 305, 306, 307, 308,
        400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451,
        500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511
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
