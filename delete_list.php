<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login_signup.html");
    exit();
}

$list_id = $_GET['id'];

$sql = "DELETE FROM list_items WHERE list_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $list_id);
$stmt->execute();
$stmt->close();

$sql = "DELETE FROM lists WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $list_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

header("Location: lists.php");
exit();
