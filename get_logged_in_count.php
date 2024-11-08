<?php
// Database connection
$host = "26.93.45.191";
$username = "sa";
$password = "CDPabina";
$dbname = "anviz";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to count employees with the latest record as Time-in (Checktype = 0)
$sql = "
    SELECT COUNT(DISTINCT emp.id) AS count
    FROM Checkinout emp
    JOIN (
        SELECT id, MAX(Checktime) as LatestCheck
        FROM Checkinout
        GROUP BY id
    ) latest ON emp.id = latest.id AND emp.Checktime = latest.LatestCheck
    WHERE emp.Checktype = 0;
";

$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['count' => 0]); // Fallback if query fails
}

$conn->close();
?>
