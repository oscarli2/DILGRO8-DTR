<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use any of these or check the exact MSSQL ODBC driver name in "ODBC Data Source Administrator"
try {
    $mssqldriver = '{ODBC Driver 11 for SQL Server}';
    // Database connection code as before
    $host = "26.93.45.191";
    $username = "sa";
    $password = "CDPabina";
    $dbname = "anviz";
    $conn = new PDO("odbc:Driver=$mssqldriver;Server=$host;Database=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Simplified query to check data retrieval
    $sql = "SELECT TOP 1 * FROM Checkinout";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return result as JSON
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}

$conn = null;

?>
