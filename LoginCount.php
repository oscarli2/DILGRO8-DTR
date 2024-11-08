<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login Count</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
    <script>
        function updateCount() {
            fetch('get_logged_in_count.php')
                .then(response => response.json())
                .then(data => {
                    // Update the count
                    document.getElementById('loggedInCount').innerText = data.count;

                    // Clear existing table rows
                    const tableBody = document.getElementById('employeeTableBody');
                    tableBody.innerHTML = '';

                    // Populate the table with the employee data
                    data.employees.forEach(employee => {
                        const row = document.createElement('tr');

                        const nameCell = document.createElement('td');
                        nameCell.textContent = employee.name;
                        row.appendChild(nameCell);

                        const deptCell = document.createElement('td');
                        deptCell.textContent = employee.department;
                        row.appendChild(deptCell);

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => console.error('Error fetching employee data:', error));
        }

        // Refresh count and employee data every 5 seconds asdasdas
        setInterval(updateCount, 5000);
        window.onload = updateCount;
    </script>
</head>
<body>
    <h1>Current Logged-In Employees</h1>
    <p>Total Logged-In Employees: <strong id="loggedInCount">0</strong></p>

    <h2>Logged-In Employee List</h2>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            <!-- Employee data will be populated here -->
        </tbody>
    </table>
</body>
</html>
