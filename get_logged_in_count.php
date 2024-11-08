<?php
// Database connection using PDO for SQL Server
$host = "26.93.45.191";
$username = "sa";
$password = "CDPabina";
$dbname = "anviz";

try {
    // Create PDO connection for SQL Server
    $conn = new PDO("sqlsrv:Server=$host;Database=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, return a JSON error message
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Query to get the count and details of employees currently logged in
$sql = "
    SELECT COUNT(DISTINCT emp.Userid) AS count, 
           u.Name AS employee_name,
           d.DeptName AS department_name
    FROM Checkinout emp
    JOIN (
        SELECT Userid, MAX(Checktime) AS LatestCheck
        FROM Checkinout
        GROUP BY Userid
    ) latest ON emp.Userid = latest.Userid AND emp.Checktime = latest.LatestCheck
    JOIN Userinfo u ON emp.Userid = u.Userid
    LEFT JOIN Dept d ON u.Deptid = d.Deptid
    WHERE emp.Checktype = 0
    GROUP BY emp.Userid, u.Name, d.DeptName;
";

try {
    // Execute the query
    $stmt = $conn->query($sql);
    
    // Fetch all results
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare the response
    $response = [
        'count' => count($employees), // Total logged-in employees
        'employees' => $employees
    ];

    // Return the result as JSON
    echo json_encode($response);
} catch (PDOException $e) {
    // If query fails, return an error message
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    exit();
}

$conn = null;
?>
