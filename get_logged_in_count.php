<?php
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection using PDO for SQL Server
// Rest of the code follows...

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
    
    // Check if there are results
    if ($stmt === false) {
        // Query failed
        echo json_encode(['error' => 'Query failed to execute.']);
        exit();
    }

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

// Close the connection
$conn = null;
?>
