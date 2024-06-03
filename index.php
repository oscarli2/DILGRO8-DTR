<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        <h1>View Attendance Records</h1>
        <div class="container">
            <div class="form-container">
                <form method="POST" action="index.php">
                    <label for="user_id">User ID:</label>
                    <input type="number" id="user_id" name="user_id" required>
                    
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                    
                    <input type="submit" value="View Records">
                </form>
            </div>
            <div class="table-container">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    include 'db_connection.php';

                    $user_id = $_POST['user_id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    try {
                        // Convert dates to strings in 'YYYY-MM-DD' format test
                        $start_date = date('Y-m-d', strtotime($start_date));
                        $end_date = date('Y-m-d', strtotime($end_date));

                        $sql = "SET NOCOUNT ON;

                        -- Disable foreign key checks
                        EXEC sp_msforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL';
                
                        -- Your original CTE and main query
                        WITH date_ranges AS (
                            SELECT CAST(? AS DATE) AS dt
                            UNION ALL
                            SELECT DATEADD(DAY, 1, dt) 
                            FROM date_ranges 
                            WHERE DATEADD(DAY, 1, dt) <= ?
                        )
                        SELECT 
                            DAY(date_ranges.dt) as 'days',
                            MAX(CASE WHEN T.Checktype = 0 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '03:00:00' AND '11:59:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS ArrivalAM,
                            MIN(CASE WHEN T.Checktype = 1 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '12:00:00' AND '14:00:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS DepartAM,
                            MAX(CASE WHEN T.Checktype = 0 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '12:00:00' AND '14:00:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS ArrivalPM,
                            MAX(CASE WHEN T.Checktype = 1 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '15:00:01' AND '23:59:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS DepartPM,
                            DATEPART(WEEKDAY, date_ranges.dt) AS Weekend
                        FROM 
                            date_ranges
                        LEFT JOIN 
                            Checkinout T ON date_ranges.dt = CAST(T.Checktime AS DATE)
                        GROUP BY 
                            date_ranges.dt;
                
                        -- Enable foreign key checks
                        EXEC sp_msforeachtable 'ALTER TABLE ? CHECK CONSTRAINT ALL'";

                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(1, $start_date, PDO::PARAM_STR);
                        $stmt->bindParam(2, $end_date, PDO::PARAM_STR);
                        $stmt->bindParam(3, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(4, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(5, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(6, $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($results) {
                            echo "<table>";
                            echo "<tr><th>Date</th><th>Arrival AM</th><th>Departure AM</th><th>Arrival PM</th><th>Departure PM</th></tr>";
                            foreach ($results as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['days']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['ArrivalAM']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['DepartAM']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['ArrivalPM']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['DepartPM']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "<p>No records found for the given dates.</p>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
