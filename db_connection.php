<?php
// Use any of these or check the exact MSSQL ODBC driver name in "ODBC Data Source Administrator"
$mssqldriver = '{ODBC Driver 11 for SQL Server}';

$hostname = '172.20.72.124';
$dbname = 'anviz';
$username = 'sa';
$password = 'CDPabina';

try {
    $conn = new PDO("odbc:Driver=$mssqldriver;Server=$hostname;Database=$dbname", $username, $password);
    // Set the PDO error mode to exception  
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
