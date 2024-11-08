<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login Count</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Employee Login Count</h2>
    <p>Total logged-in employees: <span id="loggedInCount">0</span></p>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody id="employeeTableBody">
            <!-- Employee rows will be dynamically inserted here -->
        </tbody>
    </table>
</div>

<script>
function updateCount() {
    fetch('get_logged_in_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                // Log error message from PHP to console and show alert
                console.error('Error from PHP:', data.error);
                alert('Error: ' + data.error);
                return;
            }

            // Update the count of logged-in employees
            document.getElementById('loggedInCount').innerText = data.count;

            // Clear any existing rows in the employee table
            const tableBody = document.getElementById('employeeTableBody');
            tableBody.innerHTML = '';

            // Add each logged-in employee's name and department to the table
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
            // Handle fetch or network errors
            console.error('Error fetching employee data:', error);
            alert('Error fetching employee data. Please try again later.');
        });
}

// Refresh data every 5 seconds
setInterval(updateCount, 5000);

// Initial call to populate the data immediately on page load
updateCount();
</script>

</body>
</html>
