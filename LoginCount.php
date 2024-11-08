<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login Count</title>
    <script>
        function updateCount() {
            fetch('get_logged_in_count.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loggedInCount').innerText = data.count;
                })
                .catch(error => console.error('Error fetching count:', error));
        }

        // Refresh count every 5 seconds
        setInterval(updateCount, 5000);
        window.onload = updateCount;
    </script>
</head>
<body>
    <h1>Current Logged-In Employees</h1>
    <p id="loggedInCount">0</p>
</body>
</html>
