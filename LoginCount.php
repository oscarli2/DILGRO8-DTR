<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .total-count {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .filter-container {
            margin-bottom: 20px;
        }
        .table-container {
            width: 100%;
            max-width: 800px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #fff;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Employee Login Tracker</h1>
    <div class="total-count" id="total-count">Total Employees Logged In: 0</div>

    <div class="filter-container">
        <label for="department-select">Filter by Department:</label>
        <select id="department-select" onchange="filterByDepartment()">
            <option value="">--All Departments--</option>
            <?php
            try {
                $host = "172.20.72.124";
                $username = "sa";
                $password = "CDPabina";
                $dbname = "anviz";
                $mssqldriver = '{ODBC Driver 11 for SQL Server}';

                $conn = new PDO("odbc:Driver=$mssqldriver;Server=$host;Database=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Get departments
                $deptSql = "SELECT Deptid, DeptName FROM Dept";
                $deptStmt = $conn->query($deptSql);
                $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($departments as $dept) {
                    echo "<option value='" . htmlspecialchars($dept['Deptid']) . "'>" . htmlspecialchars($dept['DeptName']) . "</option>";
                }

            } catch (PDOException $e) {
                echo "<p>Error: Could not fetch departments. " . $e->getMessage() . "</p>";
                exit();
            }
            ?>
        </select>
    </div>

    <div class="table-container" id="employeeData">
        <!-- The employee data table will be loaded here -->
    </div>

    <script>
        // Refreshes the content every 5 seconds
        let autoRefresh = setInterval(fetchEmployees, 5000);

        function fetchEmployees(department = '') {
            fetch(`get_logged_in_count.php?department=${encodeURIComponent(department)}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('employeeData').innerHTML = data.tableHtml;
                    document.getElementById('total-count').textContent = `Total Employees Logged In: ${data.totalCount}`;
                })
                .catch(error => console.error('Error fetching employee data:', error));
        }

        // Filter employees by department selection
        function filterByDepartment() {
            const department = document.getElementById('department-select').value;

            if (department) {
                clearInterval(autoRefresh);
                fetchEmployees(department);
            } else {
                autoRefresh = setInterval(fetchEmployees, 5000);
                fetchEmployees();
            }
        }

        // Initial fetch to populate the table
        fetchEmployees();
    </script>

</body>
</html>
