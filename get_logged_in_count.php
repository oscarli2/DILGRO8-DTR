<?php
header('Content-Type: application/json');

$host = "172.20.72.124";
$username = "sa";
$password = "CDPabina";
$dbname = "anviz";
$mssqldriver = '{ODBC Driver 11 for SQL Server}';

try {
    $conn = new PDO("odbc:Driver=$mssqldriver;Server=$host;Database=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$deptId = isset($_GET['department']) ? $_GET['department'] : '';

$sql = "
    WITH LatestCheckins AS (
        SELECT 
            emp.Userid,
            emp.Checktime,
            emp.Checktype,
            ROW_NUMBER() OVER (PARTITION BY emp.Userid ORDER BY emp.Checktime DESC) AS rn
        FROM Checkinout emp
        WHERE CAST(emp.Checktime AS DATE) = CAST(GETDATE() AS DATE) -- Only include records for today
    )
    SELECT 
        u.Name AS employee_name,
        d.DeptName AS department_name
    FROM LatestCheckins lc
    JOIN Userinfo u ON lc.Userid = u.Userid
    LEFT JOIN Dept d ON u.Deptid = d.Deptid
    WHERE lc.Checktype = 0 AND lc.rn = 1

";

if ($deptId != '') {
    $sql .= " AND u.Deptid = :deptId";
}

try {
    $stmt = $conn->prepare($sql);
   
    if ($deptId != '') {
        $stmt->bindParam(':deptId', $deptId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalCount = count($employees);

    $tableHtml = "<table style='width: 100%;'><tr><th>Employee Name</th><th>Department Name</th></tr>";
    if ($totalCount == 0) {
        $tableHtml .= "<tr><td colspan='2'>No logged-in employees found for this department.</td></tr>";
    } else {
        foreach ($employees as $employee) {
            $tableHtml .= "<tr>";
            $tableHtml .= "<td style='width: 50%;'>" . htmlspecialchars($employee['employee_name']) . "</td>";
            $tableHtml .= "<td style='width: 50%;'>" . htmlspecialchars($employee['department_name'] ?: 'N/A') . "</td>";
            $tableHtml .= "</tr>";
        }
    }
    $tableHtml .= "</table>";

    echo json_encode(["tableHtml" => $tableHtml, "totalCount" => $totalCount]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
    exit();
}

$conn = null;
?>
