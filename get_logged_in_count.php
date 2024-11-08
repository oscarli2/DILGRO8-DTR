<?php
// Database connection parameters for SQL Server
$host = "sqlsrv:Server=26.93.45.191;Database=anviz";
$username = "sa";
$password = "CDPabina";

try {
    // Create PDO instance for SQL Server connection
    $conn = new PDO($host, $username, $password);
    
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL query to get the count and details of employees currently logged in
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
        GROUP BY emp.Userid;
    ";

    // Prepare the query
    $stmt = $conn->prepare($sql);

    // Execute the query
    $stmt->execute();

    // Initialize the response array
    $response = [
        'count' => 0,
        'employees' => []
    ];

    // Fetch results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response['count'] = $stmt->rowCount(); // Count the number of logged-in employees
        $response['employees'][] = [
            'name' => $row['employee_name'],
            'department' => $row['department_name']
        ];
    }

    // Return the result as JSON
    echo json_encode($response);

} catch (PDOException $e) {
    // Catch any errors and return them as JSON
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}

// Close the connection
$conn = null;
?>
