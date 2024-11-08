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


// Query to get the count and details of employees currently logged in a
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

$result = $conn->query($sql);

// Prepare the response
$response = [
    'count' => 0,
    'employees' => []
];

if ($result) {
    $response['count'] = $result->num_rows; // Count the number of logged-in employees
    while ($row = $result->fetch_assoc()) {
        $response['employees'][] = [
            'name' => $row['employee_name'],
            'department' => $row['department_name']
        ];
    }
}

// Return the result as JSON
echo json_encode($response);

$conn->close();
?>
