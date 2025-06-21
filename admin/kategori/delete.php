<?php
require_once '../auth.php';
require_once '../../config/database.php';

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list.php");
exit;
