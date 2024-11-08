<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "26.93.45.191"; // Update if local testing
$username = "sa";
$password = "CDPabina";
$dbname = "anviz";

try {
    $conn = new PDO("sqlsrv:Server=$host;Database=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo json_encode(['status' => 'Connection successful']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>
