<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login Count</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 0 auto; padding: 20px; text-align: center; }
        h2 { color: #333; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Logged-In Employees</h2>
        <p>Total Employees Logged In: <span id="loggedInCount">0</span></p>
        
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody id="employeeTableBody">
                <!-- Employee data will be inserted here -->
            </tbody>
        </table>
    </div>

    <script>
        // Function to fetch the employee login count and details
        function updateCount() {
            fetch('get_logged_in_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error from PHP:', data.error);
                        alert('Error fetching employee data. Please try again later.');
                        return;
                    }

                    document.getElementById('loggedInCount').innerText = data.count;

                    const tableBody = document.getElementById('employeeTableBody');
                    tableBody.innerHTML = '';

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
                .catch(error => {
                    console.error('Error fetching employee data:', error);
                    alert('Error fetching employee data. Please try again later.');
                });
        }

        // Initial call to fetch data on page load
        updateCount();

        // Optionally, you can set an interval to refresh the data every few seconds
        setInterval(updateCount, 30000); // Refresh every 30 seconds
    </script>
</body>
</html>
